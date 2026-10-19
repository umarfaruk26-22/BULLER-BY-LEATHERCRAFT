<?php
/**
 * BULLER LEATHER — GLOBAL CONFIGURATION
 */

// Error Reporting
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Base Paths & URLs
define('APP_NAME', 'Buller Leather');
define('APP_TAGLINE', 'Generational Leather Craftsmanship & Heritage Luxury');
define('APP_VERSION', '1.0.0');

// Database Credentials (XAMPP / MySQL & Live Hostinger)
$isLive = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'nexalogictechno.com') !== false);

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', $isLive ? 'u121474497_buller' : 'buller_leather_db');
define('DB_USER', $isLive ? 'u121474497_buller' : 'root');
define('DB_PASS', $isLive ? 'Umarfaruk#26' : '');
define('DB_CHARSET', 'utf8mb4');

// Base URL calculation (Supports Hostinger SSL & Cloudflare)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? 80) == 443)
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (!empty($_SERVER['HTTP_CF_VISITOR']) && str_contains($_SERVER['HTTP_CF_VISITOR'], 'https'));
$protocol = $isHttps ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($protocol . $host . $scriptName, '/');
define('BASE_URL', $baseUrl);

// Sessions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('UTC');
