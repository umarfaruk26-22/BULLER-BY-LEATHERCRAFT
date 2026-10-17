<?php
namespace App\Core;

use PDO;

class Auth {

    public static function user(): ?array {
        if (!self::isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
        ];
    }

    public static function id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function isSuperAdmin(): bool {
        return self::isLoggedIn() && ($_SESSION['user_role'] === 'super_admin');
    }

    public static function isCountryAdmin(): bool {
        return self::isLoggedIn() && in_array($_SESSION['user_role'], ['super_admin', 'country_admin']);
    }

    public static function isCustomer(): bool {
        return self::isLoggedIn() && ($_SESSION['user_role'] === 'customer');
    }

    /**
     * Authenticate user credentials
     */
    public static function attempt(string $email, string $password): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1");
        $stmt->execute([trim($email)]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Update last login
            $update = $db->prepare("UPDATE users SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?");
            $update->execute([$_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $user['id']]);

            // Set session variables
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_default_country_id'] = $user['default_country_id'] ? (int)$user['default_country_id'] : null;

            // Automatically lock active market to customer's registered country
            if ($user['default_country_id'] && $user['role'] !== 'super_admin') {
                $cStmt = $db->prepare("SELECT * FROM countries WHERE id = ? AND is_active = 1 LIMIT 1");
                $cStmt->execute([(int)$user['default_country_id']]);
                $cCountry = $cStmt->fetch();
                if ($cCountry) {
                    $_SESSION['buller_market_code'] = $cCountry['code'];
                    $_SESSION['buller_market_id'] = (int)$cCountry['id'];
                    setcookie('buller_market_code', $cCountry['code'], time() + (86400 * 30), "/");
                }
            }

            // Cache assigned countries for country admins
            if ($user['role'] === 'country_admin') {
                $accStmt = $db->prepare("SELECT country_id FROM admin_country_access WHERE admin_id = ?");
                $accStmt->execute([$user['id']]);
                $_SESSION['assigned_countries'] = $accStmt->fetchAll(PDO::FETCH_COLUMN);
            }

            return true;
        }

        return false;
    }

    /**
     * Check if user is locked to their registered country
     */
    public static function isCountryLocked(): bool {
        return self::isLoggedIn() && !self::isSuperAdmin();
    }

    /**
     * Get user's registered country ID
     */
    public static function userCountryId(): ?int {
        return $_SESSION['user_default_country_id'] ?? null;
    }

    /**
     * Log user out
     */
    public static function logout(): void {
        unset(
            $_SESSION['user_id'],
            $_SESSION['user_name'],
            $_SESSION['user_email'],
            $_SESSION['user_role'],
            $_SESSION['user_default_country_id'],
            $_SESSION['assigned_countries']
        );
        session_regenerate_id(true);
    }

    /**
     * Check if the current user has access to a specific country
     */
    public static function hasCountryAccess(int $countryId): bool {
        if (!self::isLoggedIn()) {
            return false;
        }

        if (self::isSuperAdmin()) {
            return true;
        }

        if ($_SESSION['user_role'] === 'country_admin') {
            $assigned = $_SESSION['assigned_countries'] ?? [];
            return in_array($countryId, $assigned);
        }

        return false;
    }

    /**
     * Enforce strict country scoping on administrative pages.
     * Halts execution with 403 Forbidden if unauthorized.
     */
    public static function enforceCountryAccess(int $countryId): void {
        if (!self::hasCountryAccess($countryId)) {
            AuditLogger::log(
                self::id(),
                'security_violation',
                'countries',
                $countryId,
                ['message' => 'Unauthorized attempt to access country data by country admin.']
            );

            http_response_code(403);
            require __DIR__ . '/../../app/views/errors/403.php';
            exit;
        }
    }

    /**
     * Get list of country IDs permitted for logged-in admin
     */
    public static function getPermittedCountryIds(): array {
        if (self::isSuperAdmin()) {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT id FROM countries WHERE is_active = 1");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $_SESSION['assigned_countries'] ?? [];
    }

    /**
     * CSRF Protection
     */
    public static function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Strict RBAC Gatekeeper for Super Admin routes
     */
    public static function requireSuperAdmin(): void {
        if (!self::isSuperAdmin()) {
            AuditLogger::log(
                self::id(),
                'security_violation',
                'superadmin_portal',
                null,
                ['message' => 'Unauthorized attempt to access Super Admin portal.']
            );
            if (!self::isLoggedIn()) {
                redirect('login');
            }
            http_response_code(403);
            require __DIR__ . '/../../app/views/errors/403.php';
            exit;
        }
    }

    /**
     * Strict RBAC Gatekeeper for Regional Admin routes
     */
    public static function requireCountryAdmin(): void {
        if (!self::isCountryAdmin()) {
            AuditLogger::log(
                self::id(),
                'security_violation',
                'admin_portal',
                null,
                ['message' => 'Unauthorized attempt to access Admin portal.']
            );
            redirect('login');
        }
    }
}
