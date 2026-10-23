-- =====================================================================
-- BULLER LEATHER INTERNATIONAL E-COMMERCE DATABASE SCHEMA
-- Compatible with MySQL 8.0+ / MariaDB / XAMPP
-- Character Set: utf8mb4, Collation: utf8mb4_unicode_ci
-- =====================================================================

-- CREATE DATABASE IF NOT EXISTS `buller_leather_db` 
-- CHARACTER SET utf8mb4 
-- COLLATE utf8mb4_unicode_ci;

-- USE `buller_leather_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables to allow clean re-import
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `system_settings`;
DROP TABLE IF EXISTS `product_reviews`;
DROP TABLE IF EXISTS `wishlists`;
DROP TABLE IF EXISTS `coupons`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `customer_addresses`;
DROP TABLE IF EXISTS `product_country_pricing`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `admin_country_access`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `countries`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- 1. COUNTRIES & REGIONAL MARKETS
-- ---------------------------------------------------------------------
CREATE TABLE `countries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(5) NOT NULL UNIQUE,          -- ISO 3166-1 alpha-2 (e.g. IN, US, GB, AE)
    `name` VARCHAR(100) NOT NULL,
    `currency_code` VARCHAR(10) NOT NULL,       -- ISO 4217 (INR, USD, GBP, AED)
    `currency_symbol` VARCHAR(10) NOT NULL,     -- ₹, $, £, AED
    `currency_name` VARCHAR(50) NOT NULL,       -- Indian Rupee, US Dollar, British Pound, UAE Dirham
    `exchange_rate_to_usd` DECIMAL(12, 6) NOT NULL DEFAULT 1.000000,
    `tax_rate_percent` DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
    `tax_name` VARCHAR(50) NOT NULL DEFAULT 'VAT / Sales Tax',
    `flat_shipping_rate` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `free_shipping_threshold` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `return_window_days` INT NOT NULL DEFAULT 30,
    `phone_code` VARCHAR(10) NOT NULL DEFAULT '+1',
    `flag_icon` VARCHAR(10) NOT NULL DEFAULT '🌐',
    `address` TEXT NULL,
    `contact_email` VARCHAR(150) NULL,
    `contact_phone` VARCHAR(50) NULL,
    `contact_whatsapp` VARCHAR(50) NULL,
    `city` VARCHAR(100) NULL,
    `atelier_title` VARCHAR(150) NULL,
    `support_hours` VARCHAR(150) NULL,
    `shipping_partner` VARCHAR(150) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_country_code` (`code`),
    INDEX `idx_country_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. USERS (Super Admin, Country Admins, Customers)
-- ---------------------------------------------------------------------
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) NULL,
    `role` ENUM('super_admin', 'country_admin', 'customer') NOT NULL DEFAULT 'customer',
    `default_country_id` INT NULL,
    `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    `last_login_at` DATETIME NULL,
    `last_login_ip` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_users_country` FOREIGN KEY (`default_country_id`) REFERENCES `countries`(`id`) ON DELETE SET NULL,
    INDEX `idx_user_email` (`email`),
    INDEX `idx_user_role` (`role`),
    INDEX `idx_user_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. COUNTRY ADMIN SCOPED ACCESS MATRIX (Strict RBAC)
-- ---------------------------------------------------------------------
CREATE TABLE `admin_country_access` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT NOT NULL,
    `country_id` INT NOT NULL,
    `can_manage_products` TINYINT(1) NOT NULL DEFAULT 1,
    `can_manage_orders` TINYINT(1) NOT NULL DEFAULT 1,
    `can_manage_discounts` TINYINT(1) NOT NULL DEFAULT 1,
    `can_manage_inventory` TINYINT(1) NOT NULL DEFAULT 1,
    `can_moderate_reviews` TINYINT(1) NOT NULL DEFAULT 1,
    `can_process_refunds` TINYINT(1) NOT NULL DEFAULT 0,
    `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_admin_country` (`admin_id`, `country_id`),
    CONSTRAINT `fk_aca_admin` FOREIGN KEY (`admin_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_aca_country` FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. CATEGORIES
-- ---------------------------------------------------------------------
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `image_url` VARCHAR(255) NULL,
    `parent_id` INT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_cat_slug` (`slug`),
    INDEX `idx_cat_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. PRODUCTS (Global Master Specification)
-- ---------------------------------------------------------------------
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sku` VARCHAR(50) NOT NULL UNIQUE,
    `title` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(220) NOT NULL UNIQUE,
    `category_id` INT NOT NULL,
    `leather_type` VARCHAR(100) NULL,          -- e.g. Full-Grain Italian Vachetta, Top-Grain Buffalo
    `hardware_finish` VARCHAR(100) NULL,      -- e.g. Hand-Burnished Solid Brass
    `color` VARCHAR(255) NULL,                -- e.g. Espresso Brown, Cognac Tan, Raven Black
    `dimensions` VARCHAR(100) NULL,           -- e.g. 42cm x 30cm x 12cm
    `short_description` VARCHAR(500) NULL,
    `detailed_description` LONGTEXT NULL,
    `care_instructions` TEXT NULL,
    `primary_image` VARCHAR(255) NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `is_bestseller` TINYINT(1) NOT NULL DEFAULT 0,
    `is_new_arrival` TINYINT(1) NOT NULL DEFAULT 1,
    `status` ENUM('published', 'draft', 'archived') NOT NULL DEFAULT 'published',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
    INDEX `idx_prod_sku` (`sku`),
    INDEX `idx_prod_slug` (`slug`),
    INDEX `idx_prod_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. PRODUCT GALLERY IMAGES
-- ---------------------------------------------------------------------
CREATE TABLE `product_images` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `image_url` VARCHAR(255) NOT NULL,
    `alt_text` VARCHAR(200) NULL,
    `color_name` VARCHAR(100) NULL DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    CONSTRAINT `fk_pi_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. PRODUCT COUNTRY PRICING & INVENTORY MATRIX
-- ---------------------------------------------------------------------
CREATE TABLE `product_country_pricing` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `country_id` INT NOT NULL,
    `original_price` DECIMAL(10, 2) NOT NULL,
    `discount_price` DECIMAL(10, 2) NULL,
    `discount_start_date` DATE NULL,
    `discount_end_date` DATE NULL,
    `stock_quantity` INT NOT NULL DEFAULT 0,
    `low_stock_threshold` INT NOT NULL DEFAULT 5,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_prod_country` (`product_id`, `country_id`),
    CONSTRAINT `fk_pcp_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pcp_country` FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE CASCADE,
    INDEX `idx_pcp_available` (`country_id`, `is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. CUSTOMER ADDRESSES
-- ---------------------------------------------------------------------
CREATE TABLE `customer_addresses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `country_id` INT NOT NULL,
    `address_type` ENUM('shipping', 'billing', 'both') NOT NULL DEFAULT 'shipping',
    `recipient_name` VARCHAR(150) NOT NULL,
    `phone_number` VARCHAR(50) NOT NULL,
    `street_address_1` VARCHAR(255) NOT NULL,
    `street_address_2` VARCHAR(255) NULL,
    `city` VARCHAR(100) NOT NULL,
    `state_province` VARCHAR(100) NOT NULL,
    `postal_code` VARCHAR(30) NOT NULL,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_addr_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_addr_country` FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. ORDERS
-- ---------------------------------------------------------------------
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(60) NOT NULL UNIQUE,
    `customer_id` INT NULL,
    `country_id` INT NOT NULL,
    `customer_name` VARCHAR(150) NOT NULL,
    `customer_email` VARCHAR(191) NOT NULL,
    `customer_phone` VARCHAR(50) NOT NULL,
    `shipping_address_json` TEXT NOT NULL,
    `billing_address_json` TEXT NULL,
    `currency_code` VARCHAR(10) NOT NULL,
    `currency_symbol` VARCHAR(10) NOT NULL,
    `subtotal` DECIMAL(10, 2) NOT NULL,
    `tax_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `shipping_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `discount_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `coupon_code` VARCHAR(50) NULL,
    `grand_total` DECIMAL(10, 2) NOT NULL,
    `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    `order_status` ENUM('placed', 'processing', 'dispatched', 'delivered', 'cancelled', 'returned') NOT NULL DEFAULT 'placed',
    `shipping_carrier` VARCHAR(100) NULL,
    `tracking_number` VARCHAR(100) NULL,
    `tracking_url` VARCHAR(255) NULL,
    `customer_notes` TEXT NULL,
    `admin_notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_orders_country` FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE RESTRICT,
    INDEX `idx_order_number` (`order_number`),
    INDEX `idx_order_country` (`country_id`),
    INDEX `idx_order_status` (`order_status`),
    INDEX `idx_order_payment` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. ORDER ITEMS
-- ---------------------------------------------------------------------
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NULL,
    `sku` VARCHAR(50) NOT NULL,
    `product_title` VARCHAR(200) NOT NULL,
    `product_image` VARCHAR(255) NULL,
    `unit_price` DECIMAL(10, 2) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `total_price` DECIMAL(10, 2) NOT NULL,
    CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_oi_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 11. PAYMENTS
-- ---------------------------------------------------------------------
CREATE TABLE `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `payment_method` ENUM('razorpay', 'stripe', 'cod', 'bank_transfer') NOT NULL,
    `transaction_reference` VARCHAR(150) NULL,
    `gateway_order_id` VARCHAR(150) NULL,
    `gateway_payment_id` VARCHAR(150) NULL,
    `gateway_signature` VARCHAR(255) NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `currency` VARCHAR(10) NOT NULL,
    `status` ENUM('initiated', 'captured', 'failed', 'refunded') NOT NULL DEFAULT 'initiated',
    `gateway_payload_json` LONGTEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 12. PROMOTIONAL COUPONS
-- ---------------------------------------------------------------------
CREATE TABLE `coupons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `country_id` INT NULL,                      -- NULL = valid across all countries
    `discount_type` ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
    `discount_value` DECIMAL(10, 2) NOT NULL,
    `min_spend` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `valid_from` DATE NULL,
    `valid_until` DATE NULL,
    `usage_limit` INT NOT NULL DEFAULT 0,       -- 0 = unlimited
    `times_used` INT NOT NULL DEFAULT 0,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_coupons_country` FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE CASCADE,
    INDEX `idx_coupon_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 13. WISHLISTS
-- ---------------------------------------------------------------------
CREATE TABLE `wishlists` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_wishlist` (`user_id`, `product_id`),
    CONSTRAINT `fk_wl_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_wl_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 14. PRODUCT REVIEWS
-- ---------------------------------------------------------------------
CREATE TABLE `product_reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `country_id` INT NOT NULL,
    `rating` INT NOT NULL,
    `review_title` VARCHAR(150) NOT NULL,
    `review_body` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_pr_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pr_country` FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE CASCADE,
    INDEX `idx_pr_approved` (`product_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 15. ADMINISTRATIVE AUDIT LOGS
-- ---------------------------------------------------------------------
CREATE TABLE `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `user_email` VARCHAR(191) NULL,
    `country_id` INT NULL,
    `action` VARCHAR(100) NOT NULL,
    `target_type` VARCHAR(50) NOT NULL,
    `target_id` INT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `details_json` LONGTEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_audit_user` (`user_id`),
    INDEX `idx_audit_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 16. SYSTEM SETTINGS (Key-Value Config)
-- ---------------------------------------------------------------------
CREATE TABLE `system_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` LONGTEXT NULL,
    `group_name` VARCHAR(50) NOT NULL DEFAULT 'general',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_settings_key` (`setting_key`),
    INDEX `idx_settings_group` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 17. CONTACT INQUIRIES
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_inquiries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `country_code` VARCHAR(10) NOT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(50) NULL,
    `inquiry_type` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('new', 'in_progress', 'resolved') DEFAULT 'new',
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
