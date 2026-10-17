<?php
/**
 * Global helper functions for Buller Leather
 */

if (!function_exists('asset')) {
    function asset(string $path): string {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('e')) {
    function e(?string $string): string {
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void {
        header("Location: " . url($path));
        exit;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $message = null) {
        if ($message !== null) {
            $_SESSION['flash'][$key] = $message;
            return null;
        }

        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }

        return null;
    }
}

if (!function_exists('has_flash')) {
    function has_flash(string $key): bool {
        return isset($_SESSION['flash'][$key]);
    }
}

if (!function_exists('json_response')) {
    function json_response(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}

if (!function_exists('country_flag')) {
    function country_flag(?string $code, string $name = '', int $width = 24, string $class = ''): string {
        $cleanCode = strtolower(trim((string)$code));
        if (empty($cleanCode)) {
            return '';
        }
        
        $localPath = 'assets/images/flags/' . $cleanCode . '.png';
        $fullFsPath = dirname(__DIR__, 2) . '/' . $localPath;
        
        if (file_exists($fullFsPath)) {
            $src = asset($localPath);
        } else {
            $src = "https://flagcdn.com/w40/{$cleanCode}.png";
        }
        
        $height = round($width * 0.75);
        $escapedName = htmlspecialchars($name ?: strtoupper($cleanCode), ENT_QUOTES, 'UTF-8');
        return '<img src="' . $src . '" alt="' . $escapedName . '" width="' . $width . '" height="' . $height . '" class="rounded-1 align-middle ' . $class . '" style="object-fit: cover; border: 1px solid rgba(0,0,0,0.18); display: inline-block; vertical-align: middle;">';
    }
}

