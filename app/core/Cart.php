<?php
namespace App\Core;

use PDO;

class Cart {

    public static function getItems(): array {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            return [];
        }

        $country = MarketContext::getCountry();
        $countryId = (int)$country['id'];
        $db = Database::getConnection();

        $productIds = array_keys($cart);
        $inPlaceholders = implode(',', array_fill(0, count($productIds), '?'));

        $sql = "SELECT p.*, c.name as category_name, 
                       pcp.original_price, pcp.discount_price, pcp.discount_start_date, pcp.discount_end_date, 
                       pcp.stock_quantity, pcp.is_available
                FROM products p
                JOIN categories c ON p.category_id = c.id
                JOIN product_country_pricing pcp ON p.id = pcp.product_id
                WHERE pcp.country_id = ? AND p.id IN ($inPlaceholders) AND p.status = 'published'";

        $stmt = $db->prepare($sql);
        $params = array_merge([$countryId], $productIds);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        $items = [];
        foreach ($products as $p) {
            $pid = (int)$p['id'];
            $qty = (int)$cart[$pid];
            $pricing = MarketContext::evaluatePricing(
                (float)$p['original_price'],
                $p['discount_price'] ? (float)$p['discount_price'] : null,
                $p['discount_start_date'],
                $p['discount_end_date']
            );

            // Cap quantity to available stock
            $maxQty = max(0, (int)$p['stock_quantity']);
            if ($qty > $maxQty) {
                $qty = $maxQty;
                $_SESSION['cart'][$pid] = $qty;
            }

            if ($p['is_available'] && $qty > 0) {
                $itemTotal = $pricing['final_price'] * $qty;
                $items[] = [
                    'product_id' => $pid,
                    'sku' => $p['sku'],
                    'title' => $p['title'],
                    'slug' => $p['slug'],
                    'leather_type' => $p['leather_type'],
                    'color' => $p['color'],
                    'primary_image' => $p['primary_image'],
                    'unit_price' => $pricing['final_price'],
                    'original_price' => $pricing['original_price'],
                    'is_discounted' => $pricing['is_discounted'],
                    'quantity' => $qty,
                    'max_stock' => $maxQty,
                    'total_price' => $itemTotal,
                    'formatted_unit' => MarketContext::formatPrice($pricing['final_price']),
                    'formatted_total' => MarketContext::formatPrice($itemTotal),
                ];
            }
        }

        return $items;
    }

    public static function add(int $productId, int $quantity = 1): array {
        if ($quantity < 1) $quantity = 1;
        $country = MarketContext::getCountry();
        $countryId = (int)$country['id'];
        $db = Database::getConnection();

        // Check availability and stock in active market
        $stmt = $db->prepare("SELECT p.id, pcp.stock_quantity, pcp.is_available 
                              FROM products p 
                              JOIN product_country_pricing pcp ON p.id = pcp.product_id 
                              WHERE p.id = ? AND pcp.country_id = ? AND p.status = 'published'");
        $stmt->execute([$productId, $countryId]);
        $row = $stmt->fetch();

        if (!$row || !$row['is_available']) {
            return ['success' => false, 'message' => 'This product is not currently available for delivery in ' . $country['name'] . '.'];
        }

        $stock = (int)$row['stock_quantity'];
        $currentInCart = $_SESSION['cart'][$productId] ?? 0;
        $newQty = $currentInCart + $quantity;

        if ($newQty > $stock) {
            return ['success' => false, 'message' => "Only $stock units in stock for your region."];
        }

        $_SESSION['cart'][$productId] = $newQty;

        return [
            'success' => true, 
            'message' => 'Item added to your shopping bag.',
            'cart_count' => self::getCount()
        ];
    }

    public static function update(int $productId, int $quantity): array {
        if ($quantity <= 0) {
            self::remove($productId);
            return ['success' => true, 'cart_count' => self::getCount()];
        }

        $country = MarketContext::getCountry();
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT pcp.stock_quantity FROM product_country_pricing pcp WHERE pcp.product_id = ? AND pcp.country_id = ?");
        $stmt->execute([$productId, $country['id']]);
        $stock = (int)$stmt->fetchColumn();

        if ($quantity > $stock) {
            $quantity = $stock;
        }

        $_SESSION['cart'][$productId] = $quantity;
        return ['success' => true, 'cart_count' => self::getCount()];
    }

    public static function remove(int $productId): void {
        unset($_SESSION['cart'][$productId]);
    }

    public static function clear(): void {
        unset($_SESSION['cart'], $_SESSION['applied_coupon']);
    }

    public static function getCount(): int {
        return array_sum($_SESSION['cart'] ?? []);
    }

    public static function getSummary(): array {
        $country = MarketContext::getCountry();
        $items = self::getItems();
        $subtotal = 0.0;

        foreach ($items as $item) {
            $subtotal += $item['total_price'];
        }

        // Coupon discount logic
        $discountAmount = 0.0;
        $appliedCoupon = $_SESSION['applied_coupon'] ?? null;
        if ($appliedCoupon && $subtotal >= (float)$appliedCoupon['min_spend']) {
            if ($appliedCoupon['discount_type'] === 'percentage') {
                $discountAmount = ($subtotal * (float)$appliedCoupon['discount_value']) / 100;
            } else {
                $discountAmount = (float)$appliedCoupon['discount_value'];
            }
            if ($discountAmount > $subtotal) {
                $discountAmount = $subtotal;
            }
        }

        $taxableAmount = max(0.0, $subtotal - $discountAmount);
        $taxRate = (float)$country['tax_rate_percent'];
        $taxAmount = ($taxableAmount * $taxRate) / 100;

        // Shipping calculation
        $flatRate = (float)$country['flat_shipping_rate'];
        $freeThreshold = (float)$country['free_shipping_threshold'];
        $isFreeShipping = ($subtotal >= $freeThreshold && $freeThreshold > 0);
        $shippingAmount = $isFreeShipping ? 0.0 : $flatRate;

        $grandTotal = $taxableAmount + $taxAmount + $shippingAmount;

        return [
            'country' => $country,
            'items_count' => self::getCount(),
            'items' => $items,
            'subtotal' => $subtotal,
            'formatted_subtotal' => MarketContext::formatPrice($subtotal),
            'discount_amount' => $discountAmount,
            'formatted_discount' => MarketContext::formatPrice($discountAmount),
            'coupon' => $appliedCoupon,
            'tax_name' => $country['tax_name'],
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'formatted_tax' => MarketContext::formatPrice($taxAmount),
            'shipping_amount' => $shippingAmount,
            'formatted_shipping' => $isFreeShipping ? 'Complimentary' : MarketContext::formatPrice($shippingAmount),
            'is_free_shipping' => $isFreeShipping,
            'grand_total' => $grandTotal,
            'formatted_grand_total' => MarketContext::formatPrice($grandTotal),
        ];
    }
}
