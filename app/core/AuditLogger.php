<?php
namespace App\Core;

use PDO;
use Exception;

class AuditLogger {
    public static function log(?int $userId, string $action, string $targetType, ?int $targetId = null, array $details = []): void {
        try {
            $db = Database::getConnection();
            $userEmail = $_SESSION['user_email'] ?? 'guest';
            $countryId = $_SESSION['buller_market_id'] ?? null;
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 250);
            $detailsJson = json_encode($details);

            $stmt = $db->prepare("INSERT INTO audit_logs (user_id, user_email, country_id, action, target_type, target_id, ip_address, user_agent, details_json) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $userEmail, $countryId, $action, $targetType, $targetId, $ip, $ua, $detailsJson]);
        } catch (Exception $e) {
            error_log("AuditLogger Failed: " . $e->getMessage());
        }
    }
}
