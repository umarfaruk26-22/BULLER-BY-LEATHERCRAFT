<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // In production, log error and display user-friendly message
                error_log("Database Connection Failed: " . $e->getMessage());
                die("A database connection error occurred. Please ensure MySQL is active and configured.");
            }
        }

        return self::$instance;
    }
}
