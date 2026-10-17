<?php
namespace App\Core;

use PDO;

class Wishlist {
    public static function toggle(int $productId): array {
        if (!Auth::isLoggedIn()) {
            // Store in session for guests
            $w = $_SESSION['guest_wishlist'] ?? [];
            if (in_array($productId, $w)) {
                $w = array_values(array_diff($w, [$productId]));
                $added = false;
            } else {
                $w[] = $productId;
                $added = true;
            }
            $_SESSION['guest_wishlist'] = $w;
            return ['success' => true, 'added' => $added, 'count' => count($w)];
        }

        $userId = Auth::id();
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $del = $db->prepare("DELETE FROM wishlists WHERE id = ?");
            $del->execute([$existing['id']]);
            $added = false;
        } else {
            $ins = $db->prepare("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)");
            $ins->execute([$userId, $productId]);
            $added = true;
        }

        $cntStmt = $db->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ?");
        $cntStmt->execute([$userId]);
        $total = (int)$cntStmt->fetchColumn();

        return ['success' => true, 'added' => $added, 'count' => $total];
    }

    public static function getCount(): int {
        if (!Auth::isLoggedIn()) {
            return count($_SESSION['guest_wishlist'] ?? []);
        }
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ?");
        $stmt->execute([Auth::id()]);
        return (int)$stmt->fetchColumn();
    }

    public static function has(int $productId): bool {
        if (!Auth::isLoggedIn()) {
            return in_array($productId, $_SESSION['guest_wishlist'] ?? []);
        }
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT 1 FROM wishlists WHERE user_id = ? AND product_id = ? LIMIT 1");
        $stmt->execute([Auth::id(), $productId]);
        return (bool)$stmt->fetch();
    }
}
