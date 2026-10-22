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

if (!function_exists('slugify')) {
    function slugify(string $text): string {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text) ?: $text;
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'item-' . time() : $text;
    }
}

if (!function_exists('upload_image_file')) {
    function upload_image_file(array $file, string $subfolder = 'products'): ?string {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            return null;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            return null;
        }

        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif'
        ];
        $ext = $extMap[$mime] ?? 'jpg';
        $filename = uniqid('img_', true) . '.' . $ext;
        $targetDir = dirname(__DIR__, 2) . '/assets/images/' . trim($subfolder, '/');
        
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'assets/images/' . trim($subfolder, '/') . '/' . $filename;
        }

        return null;
    }
}

if (!function_exists('upload_multiple_images')) {
    function upload_multiple_images(string $inputKey, string $subfolder = 'products'): array {
        $uploaded = [];
        if (!isset($_FILES[$inputKey]) || !isset($_FILES[$inputKey]['name']) || !is_array($_FILES[$inputKey]['name'])) {
            return $uploaded;
        }
        $count = count($_FILES[$inputKey]['name']);
        for ($i = 0; $i < $count; $i++) {
            if (isset($_FILES[$inputKey]['error'][$i]) && $_FILES[$inputKey]['error'][$i] === UPLOAD_ERR_OK) {
                $singleFile = [
                    'name'     => $_FILES[$inputKey]['name'][$i],
                    'type'     => $_FILES[$inputKey]['type'][$i],
                    'tmp_name' => $_FILES[$inputKey]['tmp_name'][$i],
                    'error'    => $_FILES[$inputKey]['error'][$i],
                    'size'     => $_FILES[$inputKey]['size'][$i],
                ];
                $path = upload_image_file($singleFile, $subfolder);
                if ($path) {
                    $uploaded[$i] = $path;
                }
            }
        }
        return $uploaded;
    }
}

