<?php
namespace App\Core;

use PDO;

class Mailer {
    /**
     * Send email using localized country configuration (FastAPI, Custom SMTP, or Global Fallback)
     */
    public static function send(string $toEmail, string $subject, string $htmlBody, ?int $countryId = null, ?string $toName = null): array {
        $db = Database::getConnection();

        // 1. Resolve country context
        $country = null;
        if ($countryId) {
            $stmt = $db->prepare("SELECT * FROM countries WHERE id = ?");
            $stmt->execute([$countryId]);
            $country = $stmt->fetch();
        }

        if (!$country) {
            $country = MarketContext::getCountry();
        }

        $serviceType = $country['email_service_type'] ?? 'global_default';
        $countryName = $country['name'] ?? '';
        $senderName = !empty($country['email_sender_name']) ? $country['email_sender_name'] : (APP_NAME . ($countryName ? ' ' . $countryName : ''));
        $senderEmail = !empty($country['contact_email']) ? $country['contact_email'] : (!empty($country['email_sender_address']) ? $country['email_sender_address'] : 'concierge@bullerleather.com');

        // 2. Dispatch via FastAPI if configured for this country
        if ($serviceType === 'fastapi' && !empty($country['email_api_endpoint'])) {
            $payload = [
                'to_email'     => $toEmail,
                'to_name'      => $toName ?: $toEmail,
                'subject'      => $subject,
                'html_content' => $htmlBody,
                'country_code' => $country['code'] ?? 'GLOBAL',
                'country_name' => $country['name'] ?? 'International',
                'sender_name'  => $senderName,
                'sender_email' => $senderEmail
            ];

            $res = self::dispatchFastAPI($country['email_api_endpoint'], $payload, $country['email_api_key'] ?? '');
            if ($res['success']) {
                return ['success' => true, 'provider' => 'fastapi', 'country' => $country['name']];
            }
            // Fallback gracefully if FastAPI request failed
            error_log("FastAPI mail dispatch failed for country {$country['code']}: " . ($res['error'] ?? 'Unknown'));
        }

        // 3. Fallback to Standard PHP Mail / Server SMTP
        return self::dispatchStandardMail($toEmail, $toName, $subject, $htmlBody, $senderName, $senderEmail);
    }

    /**
     * Dispatch JSON payload to a FastAPI microservice endpoint
     */
    private static function dispatchFastAPI(string $endpoint, array $payload, ?string $apiKey = null): array {
        $ch = curl_init($endpoint);
        $json = json_encode($payload);

        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ];

        if (!empty($apiKey)) {
            $headers[] = 'Authorization: Bearer ' . trim($apiKey);
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Fast 5s timeout to never hang user checkout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'error' => $err];
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'response' => $response];
        }

        return ['success' => false, 'error' => "HTTP status $httpCode: $response"];
    }

    /**
     * Dispatch using PHP mail with proper MIME and HTML headers
     */
    private static function dispatchStandardMail(string $toEmail, ?string $toName, string $subject, string $htmlBody, string $fromName, string $fromEmail): array {
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = 'From: ' . sprintf('=?UTF-8?B?%s?= <%s>', base64_encode($fromName), $fromEmail);
        $headers[] = 'Reply-To: ' . $fromEmail;
        $headers[] = 'X-Mailer: BullerLeather/1.0';

        $to = $toName ? sprintf('=?UTF-8?B?%s?= <%s>', base64_encode($toName), $toEmail) : $toEmail;

        // Try standard mail()
        $sent = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $htmlBody, implode("\r\n", $headers));
        
        return ['success' => (bool)$sent, 'provider' => 'standard_mail'];
    }

    /**
     * Send Luxury HTML Order Confirmation
     */
    public static function sendOrderConfirmation(int $orderId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT o.*, c.name as country_name, c.contact_email, c.email_sender_name, c.email_sender_address, c.admin_notify_email, c.email_service_type, c.email_api_endpoint, c.email_api_key 
                              FROM orders o 
                              JOIN countries c ON o.country_id = c.id 
                              WHERE o.id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if (!$order) return false;

        $itemsStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $itemsStmt->execute([$orderId]);
        $items = $itemsStmt->fetchAll();

        $sym = $order['currency_symbol'];
        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= sprintf(
                '<tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                        <strong>%s</strong><br>
                        <span style="font-size: 12px; color: #64748b;">SKU: %s | Qty: %d</span>
                    </td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; text-align: right; font-weight: bold;">
                        %s%s
                    </td>
                </tr>',
                htmlspecialchars($item['product_title'] ?? $item['product_name'] ?? 'Bespoke Item'),
                htmlspecialchars($item['sku']),
                (int)$item['quantity'],
                $sym,
                number_format((float)$item['total_price'], 2)
            );
        }

        $emailBody = "
        <div style=\"font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;\">
            <div style=\"background: #0d0d0d; color: #ffffff; padding: 28px 24px; text-align: center;\">
                <h1 style=\"margin: 0; font-family: Georgia, serif; font-size: 24px; letter-spacing: 0.1em;\">BULLER</h1>
                <p style=\"margin: 4px 0 0 0; font-size: 11px; letter-spacing: 0.25em; color: #d4af37;\">BY LEATHERCRAFT</p>
            </div>
            <div style=\"padding: 30px 24px;\">
                <h2 style=\"margin-top: 0; font-size: 20px; color: #1a1a1a; font-family: Georgia, serif;\">Order Confirmation</h2>
                <p style=\"color: #475569; font-size: 14px; line-height: 1.6;\">
                    Dear <strong>" . htmlspecialchars($order['customer_name']) . "</strong>,<br>
                    Thank you for patronizing Buller Leather. Your artisanal piece has been reserved and entered our tailoring queue.
                </p>
                <div style=\"background: #f8fafc; border: 1px solid #e2e8f0; padding: 14px 18px; border-radius: 4px; margin: 20px 0;\">
                    <p style=\"margin: 0; font-size: 13px; color: #334155;\">
                        <strong>Order Number:</strong> <span style=\"font-family: monospace; font-size: 14px;\">" . htmlspecialchars($order['order_number']) . "</span><br>
                        <strong>Market Jurisdiction:</strong> " . htmlspecialchars($order['country_name']) . "<br>
                        <strong>Payment Status:</strong> " . ucfirst($order['payment_status']) . "
                    </p>
                </div>
                <table style=\"width: 100%; border-collapse: collapse; font-size: 14px; margin: 20px 0;\">
                    <thead>
                        <tr style=\"border-bottom: 2px solid #e2e8f0; color: #64748b; font-size: 12px; text-transform: uppercase;\">
                            <th style=\"text-align: left; padding-bottom: 8px;\">Item Details</th>
                            <th style=\"text-align: right; padding-bottom: 8px;\">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style=\"padding-top: 12px; font-weight: bold; font-size: 16px;\">Grand Total:</td>
                            <td style=\"padding-top: 12px; font-weight: bold; font-size: 16px; text-align: right; color: #0f382a;\">{$sym}" . number_format((float)$order['grand_total'], 2) . "</td>
                        </tr>
                    </tfoot>
                </table>
                <p style=\"color: #64748b; font-size: 12px; margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 16px; text-align: center;\">
                    For inquiries or custom accommodations, contact our regional desk at " . htmlspecialchars($order['contact_email'] ?: ($order['email_sender_address'] ?: 'concierge@bullerleather.com')) . "
                </p>
            </div>
        </div>
        ";

        // Dispatch to Customer
        self::send(
            $order['customer_email'],
            "Order Confirmation #{$order['order_number']} — Buller Leather",
            $emailBody,
            (int)$order['country_id'],
            $order['customer_name']
        );

        // Dispatch alert to Regional Store Desk (Single Common Regional Email)
        $regionalAlertEmail = !empty($order['contact_email']) ? $order['contact_email'] : (!empty($order['admin_notify_email']) ? $order['admin_notify_email'] : null);
        if (!empty($regionalAlertEmail)) {
            $adminAlertSubject = "[New Order Alert - {$order['country_name']}] #{$order['order_number']} by {$order['customer_name']}";
            self::send(
                $regionalAlertEmail,
                $adminAlertSubject,
                $emailBody,
                (int)$order['country_id'],
                "Regional Store Manager"
            );
        }

        return true;
    }
}
