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

if (!function_exists('get_swatch_color')) {
    function get_swatch_color(string $c): string {
        $lc = strtolower(trim($c));
        if (preg_match('/#([a-f0-9]{3}|[a-f0-9]{6})\b/i', $lc, $m)) {
            return '#' . $m[1];
        }
        if (str_contains($lc, 'black') || str_contains($lc, 'raven') || str_contains($lc, 'obsidian') || str_contains($lc, 'noir') || str_contains($lc, 'dark')) return '#181818';
        if (str_contains($lc, 'cognac') || str_contains($lc, 'tan') || str_contains($lc, 'caramel') || str_contains($lc, 'camel')) return '#9a532e';
        if (str_contains($lc, 'espresso') || str_contains($lc, 'chocolate')) return '#2e1c14';
        if (str_contains($lc, 'chestnut') || str_contains($lc, 'cedar') || str_contains($lc, 'wood') || str_contains($lc, 'brown')) return '#5c3a21';
        if (str_contains($lc, 'oxblood') || str_contains($lc, 'burgundy') || str_contains($lc, 'wine') || str_contains($lc, 'maroon') || str_contains($lc, 'bordeaux') || str_contains($lc, 'mahogany')) return '#4c1e1a';
        if (str_contains($lc, 'navy') || str_contains($lc, 'blue') || str_contains($lc, 'indigo') || str_contains($lc, 'sapphire')) return '#1c2833';
        if (str_contains($lc, 'olive') || str_contains($lc, 'green') || str_contains($lc, 'forest') || str_contains($lc, 'sage') || str_contains($lc, 'moss') || str_contains($lc, 'army')) return '#3d493a';
        if (str_contains($lc, 'grey') || str_contains($lc, 'gray') || str_contains($lc, 'slate') || str_contains($lc, 'charcoal') || str_contains($lc, 'graphite') || str_contains($lc, 'ash') || str_contains($lc, 'smoke')) return '#4f4f4f';
        if (str_contains($lc, 'sand') || str_contains($lc, 'beige') || str_contains($lc, 'cream') || str_contains($lc, 'ivory') || str_contains($lc, 'nude') || str_contains($lc, 'taupe') || str_contains($lc, 'khaki') || str_contains($lc, 'desert')) return '#d4b895';
        if (str_contains($lc, 'red') || str_contains($lc, 'ruby') || str_contains($lc, 'crimson') || str_contains($lc, 'scarlet')) return '#8b1e1a';
        if (str_contains($lc, 'gold') || str_contains($lc, 'yellow') || str_contains($lc, 'mustard') || str_contains($lc, 'honey')) return '#c5a059';
        if (str_contains($lc, 'orange') || str_contains($lc, 'rust') || str_contains($lc, 'copper') || str_contains($lc, 'amber')) return '#b85d19';
        if (str_contains($lc, 'white') || str_contains($lc, 'pearl') || str_contains($lc, 'snow') || str_contains($lc, 'chalk')) return '#f5f5f5';
        if (str_contains($lc, 'purple') || str_contains($lc, 'violet') || str_contains($lc, 'plum')) return '#4a235a';
        if (str_contains($lc, 'pink') || str_contains($lc, 'rose') || str_contains($lc, 'blush')) return '#c0392b';
        if (str_contains($lc, 'teal') || str_contains($lc, 'cyan') || str_contains($lc, 'turquoise')) return '#117864';
        return '#5c3a21';
    }
}
