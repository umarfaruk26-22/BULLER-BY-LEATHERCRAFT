<?php
namespace App\Core;

use PDO;

class Auth {

    protected static ?string $currentRoute = null;

    /**
     * Explicitly set the active route context
     */
    public static function setRoute(?string $route): void {
        self::$currentRoute = $route !== null ? trim($route, '/') : null;
    }

    /**
     * Determine active portal context based on current route or request URI
     * Returns: 'super_admin', 'country_admin', or 'customer'
     */
    public static function getContext(): string {
        $r = self::$currentRoute;
        if ($r === null) {
            $qRoute = $_GET['route'] ?? '';
            if (!empty($qRoute)) {
                $r = trim($qRoute, '/');
            } else {
                $uri = $_SERVER['REQUEST_URI'] ?? '';
                $path = trim((string)parse_url($uri, PHP_URL_PATH), '/');
                $r = $path;
            }
        }

        if ($r === 'superadmin' || str_starts_with($r, 'superadmin/')) {
            return 'super_admin';
        }

        if ($r === 'admin' || str_starts_with($r, 'admin/')) {
            return 'country_admin';
        }

        return 'customer';
    }

    /**
     * Initialize and retrieve the isolated session slot based on context
     */
    public static function getActiveSlot(): ?array {
        if (!isset($_SESSION['auth_slots']) || !is_array($_SESSION['auth_slots'])) {
            $_SESSION['auth_slots'] = [];

            // Migrate legacy flat session if present
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_role'])) {
                $role = $_SESSION['user_role'];
                $_SESSION['auth_slots'][$role] = [
                    'id'                 => (int)$_SESSION['user_id'],
                    'name'               => $_SESSION['user_name'] ?? 'Administrator',
                    'email'              => $_SESSION['user_email'] ?? '',
                    'role'               => $role,
                    'default_country_id' => $_SESSION['user_default_country_id'] ?? null,
                    'assigned_countries' => $_SESSION['assigned_countries'] ?? [],
                ];
            }
        }

        $context = self::getContext();
        $slot = null;

        if ($context === 'super_admin') {
            $slot = $_SESSION['auth_slots']['super_admin'] ?? null;
        } elseif ($context === 'country_admin') {
            // In Regional Admin portal: prefer regional admin slot
            if (!empty($_SESSION['auth_slots']['country_admin'])) {
                $slot = $_SESSION['auth_slots']['country_admin'];
            } elseif (!empty($_SESSION['auth_slots']['super_admin'])) {
                // Super Admin can also view regional pages if no country admin is logged in
                $slot = $_SESSION['auth_slots']['super_admin'];
            }
        } else {
            // Customer / Storefront context
            if (!empty($_SESSION['auth_slots']['customer'])) {
                $slot = $_SESSION['auth_slots']['customer'];
            } elseif (!empty($_SESSION['auth_slots']['super_admin'])) {
                $slot = $_SESSION['auth_slots']['super_admin'];
            } elseif (!empty($_SESSION['auth_slots']['country_admin'])) {
                $slot = $_SESSION['auth_slots']['country_admin'];
            }
        }

        // Synchronize legacy $_SESSION variables so third-party or legacy code stays 100% in sync
        if ($slot) {
            $_SESSION['user_id'] = $slot['id'];
            $_SESSION['user_name'] = $slot['name'];
            $_SESSION['user_email'] = $slot['email'];
            $_SESSION['user_role'] = $slot['role'];
            $_SESSION['user_default_country_id'] = $slot['default_country_id'];
            $_SESSION['assigned_countries'] = $slot['assigned_countries'] ?? [];
        }

        return $slot;
    }

    public static function hasSuperAdminSlot(): bool {
        return !empty($_SESSION['auth_slots']['super_admin']);
    }

    public static function hasRegionalAdminSlot(): bool {
        return !empty($_SESSION['auth_slots']['country_admin']);
    }

    public static function hasCustomerSlot(): bool {
        return !empty($_SESSION['auth_slots']['customer']);
    }

    public static function user(): ?array {
        $slot = self::getActiveSlot();
        if (!$slot) {
            return null;
        }

        return [
            'id'    => $slot['id'],
            'name'  => $slot['name'],
            'email' => $slot['email'],
            'role'  => $slot['role'],
        ];
    }

    public static function id(): ?int {
        $slot = self::getActiveSlot();
        return $slot['id'] ?? null;
    }

    public static function isLoggedIn(): bool {
        return self::getActiveSlot() !== null;
    }

    public static function isSuperAdmin(): bool {
        $context = self::getContext();
        // If viewing /admin as Regional Admin, don't treat as Super Admin so the regional views render properly
        if ($context === 'country_admin' && self::hasRegionalAdminSlot()) {
            return false;
        }

        if (self::hasSuperAdminSlot()) {
            return true;
        }

        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'super_admin';
    }

    public static function isCountryAdmin(): bool {
        if (self::hasRegionalAdminSlot()) {
            return true;
        }

        if (self::hasSuperAdminSlot() && self::getContext() !== 'customer') {
            return true;
        }

        return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['super_admin', 'country_admin']);
    }

    public static function isCustomer(): bool {
        $slot = self::getActiveSlot();
        return $slot && $slot['role'] === 'customer';
    }

    /**
     * Authenticate user credentials and save into isolated auth slot
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

            $assignedCountries = [];
            if ($user['role'] === 'country_admin') {
                $accStmt = $db->prepare("SELECT country_id FROM admin_country_access WHERE admin_id = ?");
                $accStmt->execute([$user['id']]);
                $assignedCountries = $accStmt->fetchAll(PDO::FETCH_COLUMN);
            }

            $slotData = [
                'id'                 => (int)$user['id'],
                'name'               => $user['full_name'],
                'email'              => $user['email'],
                'role'               => $user['role'],
                'default_country_id' => $user['default_country_id'] ? (int)$user['default_country_id'] : null,
                'assigned_countries' => $assignedCountries,
            ];

            // Initialize slots
            if (!isset($_SESSION['auth_slots']) || !is_array($_SESSION['auth_slots'])) {
                $_SESSION['auth_slots'] = [];
            }

            // Save directly into the user's role slot without wiping other active roles
            $_SESSION['auth_slots'][$user['role']] = $slotData;

            // Sync legacy session values
            $_SESSION['user_id'] = $slotData['id'];
            $_SESSION['user_name'] = $slotData['name'];
            $_SESSION['user_email'] = $slotData['email'];
            $_SESSION['user_role'] = $slotData['role'];
            $_SESSION['user_default_country_id'] = $slotData['default_country_id'];
            $_SESSION['assigned_countries'] = $assignedCountries;

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

            return true;
        }

        return false;
    }

    /**
     * Check if user is locked to their registered country
     */
    public static function isCountryLocked(): bool {
        return false;
    }

    /**
     * Get user's registered country ID
     */
    public static function userCountryId(): ?int {
        $slot = self::getActiveSlot();
        return $slot['default_country_id'] ?? null;
    }

    /**
     * Log user out from the active or specified role slot
     */
    public static function logout(?string $role = null): void {
        if (!isset($_SESSION['auth_slots']) || !is_array($_SESSION['auth_slots'])) {
            $_SESSION['auth_slots'] = [];
        }

        if ($role !== null) {
            unset($_SESSION['auth_slots'][$role]);
        } else {
            $context = self::getContext();
            unset($_SESSION['auth_slots'][$context]);
        }

        // If any remaining slot is active, sync legacy variables to it
        $remaining = null;
        if (!empty($_SESSION['auth_slots']['super_admin'])) {
            $remaining = $_SESSION['auth_slots']['super_admin'];
        } elseif (!empty($_SESSION['auth_slots']['country_admin'])) {
            $remaining = $_SESSION['auth_slots']['country_admin'];
        } elseif (!empty($_SESSION['auth_slots']['customer'])) {
            $remaining = $_SESSION['auth_slots']['customer'];
        }

        if ($remaining) {
            $_SESSION['user_id'] = $remaining['id'];
            $_SESSION['user_name'] = $remaining['name'];
            $_SESSION['user_email'] = $remaining['email'];
            $_SESSION['user_role'] = $remaining['role'];
            $_SESSION['user_default_country_id'] = $remaining['default_country_id'];
            $_SESSION['assigned_countries'] = $remaining['assigned_countries'] ?? [];
        } else {
            unset(
                $_SESSION['user_id'],
                $_SESSION['user_name'],
                $_SESSION['user_email'],
                $_SESSION['user_role'],
                $_SESSION['user_default_country_id'],
                $_SESSION['assigned_countries']
            );
        }

        session_regenerate_id(true);
    }

    /**
     * Check if the current user has access to a specific country
     */
    public static function hasCountryAccess(int $countryId): bool {
        $slot = self::getActiveSlot();
        if (!$slot) {
            return false;
        }

        if ($slot['role'] === 'super_admin') {
            return true;
        }

        if ($slot['role'] === 'country_admin') {
            $assigned = $slot['assigned_countries'] ?? [];
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
        $slot = self::getActiveSlot();
        if (!$slot) {
            return [];
        }

        if ($slot['role'] === 'super_admin') {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT id FROM countries WHERE is_active = 1");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $slot['assigned_countries'] ?? [];
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
        if (!self::hasSuperAdminSlot()) {
            AuditLogger::log(
                self::id(),
                'security_violation',
                'superadmin_portal',
                null,
                ['message' => 'Unauthorized attempt to access Super Admin portal.']
            );
            redirect('admin');
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
            redirect('admin');
        }
    }
}
