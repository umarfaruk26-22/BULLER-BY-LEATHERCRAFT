<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

$db = Database::getConnection();

$sql = "CREATE TABLE IF NOT EXISTS contact_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_code VARCHAR(10) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NULL,
    inquiry_type VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'in_progress', 'resolved') DEFAULT 'new',
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$db->exec($sql);
echo "contact_inquiries table created or already exists.\n";
