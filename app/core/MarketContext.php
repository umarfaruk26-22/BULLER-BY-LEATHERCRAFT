<?php
namespace App\Core;

use PDO;

class MarketContext {
    private static ?array $activeCountry = null;
    private static ?array $allActiveCountries = null;

    /**
     * Initialize Market Context
     */
    public static function init(): array {
        if (self::$activeCountry !== null) {
            return self::$activeCountry;
        }

        $db = Database::getConnection();

        // 0. Enforce registered country lock for logged-in customers
        if (Auth::isCountryLocked()) {
            $userCountryId = Auth::userCountryId();
            if (!$userCountryId && Auth::id()) {
                $uStmt = $db->prepare("SELECT default_country_id FROM users WHERE id = ?");
                $uStmt->execute([Auth::id()]);
                $userCountryId = $uStmt->fetchColumn();
                $_SESSION['user_default_country_id'] = $userCountryId ? (int)$userCountryId : null;
            }

            if ($userCountryId) {
                $cStmt = $db->prepare("SELECT * FROM countries WHERE id = ? AND is_active = 1 LIMIT 1");
                $cStmt->execute([(int)$userCountryId]);
                $country = $cStmt->fetch();
                if ($country) {
                    self::$activeCountry = $country;
                    $_SESSION['buller_market_code'] = $country['code'];
                    $_SESSION['buller_market_id'] = (int)$country['id'];
                    setcookie('buller_market_code', $country['code'], time() + (86400 * 30), "/");
                    return self::$activeCountry;
                }
            }
        }

        // 1. Check Session
        $selectedCode = $_SESSION['buller_market_code'] ?? null;

        // 2. Check Cookie
        if (!$selectedCode && isset($_COOKIE['buller_market_code'])) {
            $selectedCode = $_COOKIE['buller_market_code'];
        }

        if ($selectedCode) {
            $stmt = $db->prepare("SELECT * FROM countries WHERE code = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([strtoupper(trim($selectedCode))]);
            $country = $stmt->fetch();
            if ($country) {
                self::$activeCountry = $country;
                $_SESSION['buller_market_code'] = $country['code'];
                $_SESSION['buller_market_id'] = (int)$country['id'];
                return self::$activeCountry;
            }
        }

        // 3. Fallback to default (US or first active)
        $stmt = $db->prepare("SELECT * FROM countries WHERE is_active = 1 ORDER BY (code = 'US') DESC, id ASC LIMIT 1");
        $stmt->execute();
        $country = $stmt->fetch();

        if (!$country) {
            // Absolute emergency fallback
            self::$activeCountry = [
                'id' => 1,
                'code' => 'US',
                'name' => 'United States',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'currency_name' => 'US Dollar',
                'exchange_rate_to_usd' => 1.0,
                'tax_rate_percent' => 7.50,
                'tax_name' => 'Sales Tax',
                'flat_shipping_rate' => 15.00,
                'free_shipping_threshold' => 150.00,
                'return_window_days' => 30,
                'phone_code' => '+1',
                'flag_icon' => 'us',
                'is_active' => 1
            ];
        } else {
            self::$activeCountry = $country;
        }

        $_SESSION['buller_market_code'] = self::$activeCountry['code'];
        $_SESSION['buller_market_id'] = (int)self::$activeCountry['id'];

        return self::$activeCountry;
    }

    /**
     * Get the active country record
     */
    public static function getCountry(): array {
        if (self::$activeCountry === null) {
            return self::init();
        }
        return self::$activeCountry;
    }

    /**
     * Switch user to a new country market
     */
    public static function switchCountry(string $code): bool {
        // Disallow country switching if user is locked to their registered profile
        if (Auth::isCountryLocked()) {
            return false;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM countries WHERE code = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([strtoupper(trim($code))]);
        $country = $stmt->fetch();

        if ($country) {
            self::$activeCountry = $country;
            $_SESSION['buller_market_code'] = $country['code'];
            $_SESSION['buller_market_id'] = (int)$country['id'];
            setcookie('buller_market_code', $country['code'], time() + (86400 * 30), "/"); // 30 days
            return true;
        }

        return false;
    }

    /**
     * Fetch all active countries for navbar / modal switcher
     */
    public static function getAllActiveCountries(): array {
        if (self::$allActiveCountries !== null) {
            return self::$allActiveCountries;
        }

        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM countries WHERE is_active = 1 ORDER BY name ASC");
        self::$allActiveCountries = $stmt->fetchAll();
        return self::$allActiveCountries;
    }

    /**
     * Format a price according to current market currency
     */
    public static function formatPrice(float $amount, bool $includeSymbol = true): string {
        $country = self::getCountry();
        $symbol = $country['currency_symbol'];
        $code = $country['currency_code'];

        // Formatting logic: INR and AED often use standard formatting or comma grouping
        $formattedNum = number_format($amount, 2);

        if (!$includeSymbol) {
            return $formattedNum;
        }

        if ($code === 'AED') {
            return $formattedNum . ' ' . $symbol;
        }

        return $symbol . $formattedNum;
    }

    /**
     * Calculate effective price considering active discount window
     */
    public static function evaluatePricing(float $originalPrice, ?float $discountPrice, ?string $startDate, ?string $endDate): array {
        $now = date('Y-m-d');
        $isDiscounted = false;

        if ($discountPrice !== null && $discountPrice > 0 && $discountPrice < $originalPrice) {
            $isStartValid = empty($startDate) || ($startDate <= $now);
            $isEndValid = empty($endDate) || ($endDate >= $now);

            if ($isStartValid && $isEndValid) {
                $isDiscounted = true;
            }
        }

        $finalPrice = $isDiscounted ? (float)$discountPrice : (float)$originalPrice;
        $savings = $isDiscounted ? ($originalPrice - $discountPrice) : 0.0;
        $discountPct = ($isDiscounted && $originalPrice > 0) ? round(($savings / $originalPrice) * 100) : 0;

        return [
            'final_price' => $finalPrice,
            'original_price' => $originalPrice,
            'is_discounted' => $isDiscounted,
            'discount_price' => $discountPrice,
            'discount_percent' => $discountPct,
            'formatted_final' => self::formatPrice($finalPrice),
            'formatted_original' => self::formatPrice($originalPrice)
        ];
    }
}
