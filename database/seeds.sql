-- =====================================================================
-- BULLER LEATHER SEED DATA
-- Default Markets, Super Admin, Country Admins, Categories, Products & Pricing
-- =====================================================================

USE `buller_leather_db`;

-- ---------------------------------------------------------------------
-- 1. COUNTRIES
-- ---------------------------------------------------------------------
INSERT INTO `countries` (`id`, `code`, `name`, `currency_code`, `currency_symbol`, `currency_name`, `exchange_rate_to_usd`, `tax_rate_percent`, `tax_name`, `flat_shipping_rate`, `free_shipping_threshold`, `return_window_days`, `phone_code`, `flag_icon`, `is_active`) VALUES
(1, 'IN', 'India', 'INR', '₹', 'Indian Rupee', 83.500000, 18.00, 'GST (18%)', 250.00, 5000.00, 15, '+91', '🇮🇳', 1),
(2, 'US', 'United States', 'USD', '$', 'US Dollar', 1.000000, 7.50, 'Sales Tax (7.5%)', 15.00, 150.00, 30, '+1', '🇺🇸', 1),
(3, 'GB', 'United Kingdom', 'GBP', '£', 'British Pound', 0.790000, 20.00, 'VAT (20%)', 12.00, 120.00, 30, '+44', '🇬🇧', 1),
(4, 'AE', 'United Arab Emirates', 'AED', 'AED', 'UAE Dirham', 3.672500, 5.00, 'VAT (5%)', 35.00, 350.00, 14, '+971', '🇦🇪', 1);

-- ---------------------------------------------------------------------
-- 2. USERS
-- ---------------------------------------------------------------------
-- Super Admin: superadmin@bullerleather.com / BullerAdmin@2026!
-- India Admin: admin.in@bullerleather.com / IndiaAdmin@2026!
-- West Admin (US & UK): admin.west@bullerleather.com / WestAdmin@2026!
-- Sample Customer: customer@example.com / Customer@2026!

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `phone`, `role`, `default_country_id`, `status`) VALUES
(1, 'Alexander Buller (Super Admin)', 'superadmin@bullerleather.com', '$2y$10$VyTzWEF0M9PXEZbAfkNf9uBVQtWuVMDqsWteqQHPNWwenSbLydHwG', '+1 800 285 5371', 'super_admin', 2, 'active'),
(2, 'Rajesh Sharma (India Admin)', 'admin.in@bullerleather.com', '$2y$10$atrAmlKYCjVz9AJLr8lSoe/juGUP86jqCnp.a89SUpPBmY3raSApy', '+91 98765 43210', 'country_admin', 1, 'active'),
(3, 'Eleanor Vance (US & UK Admin)', 'admin.west@bullerleather.com', '$2y$10$.RxJFa9MtHlX/8/E.w8SA.9ve83RVX78ao/WVyKxzOMTly0KhD4Na', '+44 20 7946 0991', 'country_admin', 3, 'active'),
(4, 'Lord Julian Sterling', 'customer@example.com', '$2y$10$coPN4fvfuGv629mNW26SIeOml.ftRT24gr5i4lEfFLLWHruXq.D1m', '+1 212 555 0199', 'customer', 2, 'active');

-- ---------------------------------------------------------------------
-- 3. COUNTRY ADMIN SCOPED ASSIGNMENTS
-- ---------------------------------------------------------------------
-- Rajesh Sharma manages India (Country ID: 1)
INSERT INTO `admin_country_access` (`admin_id`, `country_id`, `can_manage_products`, `can_manage_orders`, `can_manage_discounts`, `can_manage_inventory`, `can_moderate_reviews`, `can_process_refunds`) VALUES
(2, 1, 1, 1, 1, 1, 1, 1);

-- Eleanor Vance manages United States (Country ID: 2) & United Kingdom (Country ID: 3)
INSERT INTO `admin_country_access` (`admin_id`, `country_id`, `can_manage_products`, `can_manage_orders`, `can_manage_discounts`, `can_manage_inventory`, `can_moderate_reviews`, `can_process_refunds`) VALUES
(3, 2, 1, 1, 1, 1, 1, 1),
(3, 3, 1, 1, 1, 1, 1, 1);

-- ---------------------------------------------------------------------
-- 4. CATEGORIES
-- ---------------------------------------------------------------------
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image_url`, `parent_id`, `sort_order`, `status`) VALUES
(1, 'Leather Jackets', 'leather-jackets', 'Iconic cafe racers, shearling bombers, and tailored leather jackets forged from full-grain hides.', 'assets/images/categories/cat_jackets.jpg', NULL, 1, 'active'),
(2, 'Bags & Briefcases', 'bags-briefcases', 'Architectural briefcases, weekend duffels, and everyday backpacks engineered for generational resilience.', 'assets/images/categories/cat_bags.jpg', NULL, 2, 'active'),
(3, 'Wallets & Small Leather Goods', 'wallets-accessories', 'Slim bifold wallets, cardholders, and travel organizers crafted with saddle-stitched precision.', 'assets/images/categories/cat_wallets.jpg', NULL, 3, 'active'),
(4, 'Handcrafted Belts', 'handcrafted-belts', 'Vegetable-tanned full-grain leather belts fitted with hand-poured antique brass hardware.', 'assets/images/categories/cat_belts.jpg', NULL, 4, 'active'),
(5, 'Footwear & Boots', 'footwear-boots', 'Goodyear welted Chelsea boots, oxford brogues, and handcrafted heritage leather loafers.', 'assets/images/categories/cat_shoes.jpg', NULL, 5, 'active'),
(6, 'Desk & Travel Curations', 'travel-accessories', 'Passport folios, leather watch rolls, and executive desk pads in supple pull-up hides.', 'assets/images/categories/cat_accessories.jpg', NULL, 6, 'active');

-- ---------------------------------------------------------------------
-- 5. PRODUCTS
-- ---------------------------------------------------------------------
INSERT INTO `products` (`id`, `sku`, `title`, `slug`, `category_id`, `leather_type`, `hardware_finish`, `color`, `dimensions`, `short_description`, `detailed_description`, `care_instructions`, `primary_image`, `is_featured`, `is_bestseller`, `is_new_arrival`, `status`) VALUES
(1, 'BL-JKT-001', 'The Sovereign Full-Grain Biker Jacket', 'the-sovereign-full-grain-biker-jacket', 1, 'Full-Grain Italian Cowhide (1.3mm)', 'Antiqued Heavy-Duty Brass YKK', 'Espresso Brown', 'Regular Tailored Cut', 'An heirloom-grade motorcycle jacket crafted from 1.3mm drum-dyed Italian cowhide with asymmetric brass hardware.', 'The Sovereign Biker Jacket is the pinnacle of Buller Leather outerwear. Hand-selected Italian hides are drum-dyed and wax-finished to develop an unparalleled patina over decades of wear. Features gusseted bi-swing shoulders for ergonomic riding comfort, quilted Japanese cupro lining, and custom-forged antique brass zips.', 'Condition bi-annually with natural beeswax cream. Hang on wide-shoulder cedar hangers.', 'assets/images/products/sovereign_jacket.jpg', 1, 1, 0, 'published'),

(2, 'BL-BAG-002', 'The Diplomat Executive Leather Briefcase', 'the-diplomat-executive-leather-briefcase', 2, 'Full-Grain Tuscan Vachetta Leather', 'Solid Cast Brass Hardware with Key Lock', 'Cognac Tan', '41cm x 30cm x 9cm (15.6 Laptop Slot)', 'A structured formal briefcase featuring triple-compartment architecture, suede lining, and a lockable brass clasp.', 'Designed for discerning international executives, The Diplomat is hand-assembled over 36 painstaking hours. The Tuscan vegetable-tanned leather displays rich pull-up characteristics that deepen with age. Equipped with a padded sleeve for laptops up to 16 inches, dedicated fountain pen sleeves, and a reinforced leather top handle.', 'Protect from heavy rain; wipe with dry microfiber cloth and polish with neutral wax balm.', 'assets/images/products/diplomat_briefcase.jpg', 1, 1, 0, 'published'),

(3, 'BL-BAG-003', 'The Voyager 48-Hour Leather Duffel', 'the-voyager-48-hour-leather-duffel', 2, 'Hand-Waxed Buffalo Leather', 'Gunmetal Matte Hardened Steel', 'Midnight Charcoal', '52cm x 28cm x 26cm (Carry-on compliant)', 'The ultimate weekend companion with dedicated ventilated shoe tunnel and reinforced load-bearing straps.', 'Handcrafted from robust 2.0mm water-resistant buffalo hide, The Voyager meets international airline carry-on specifications. Boasts double-layered reinforced base studs, a detachable padded shoulder strap, and an exterior quick-access boarding pass sleeve.', 'Wipe clean with a damp cloth; buff with leather conditioner when dry.', 'assets/images/products/voyager_duffel.jpg', 1, 0, 1, 'published'),

(4, 'BL-WLT-004', 'The Heritage Slim Bifold Wallet', 'the-heritage-slim-bifold-wallet', 3, 'Top-Grain French Calfskin', 'Hand-Stitched Fil Au Chinois Thread', 'Espresso / Bourbon Interior', '11cm x 8.8cm x 1.2cm', 'Ultra-slim dual-tone bifold with 8 dedicated card slots, dual currency sleeves, and RFID shield protection.', 'Engineered for absolute pocket slimness without compromising storage. The Heritage features hand-beveled and edge-painted edges, lined with an ultra-thin metallic layer that protects contactless credit cards from wireless skimmers.', 'Avoid overstuffing beyond capacity. Polish gently with a soft lint-free cloth.', 'assets/images/products/heritage_wallet.jpg', 1, 1, 0, 'published'),

(5, 'BL-BLT-005', 'The Master Artisan Harness Belt', 'the-master-artisan-harness-belt', 4, 'English Bridle Vegetable-Tanned Leather', 'Solid Sandcast Brass Roller Buckle', 'Rich Chestnut Tan', '35mm Width (Lengths: 32 - 44 inches)', 'Solid 10oz English bridle leather belt with hand-burnished beeswax edges and heavy sandcast roller buckle.', 'Cut from the prime center section of heavy English bridle leather hides, this belt is virtually indestructible. Sealed with a centuries-old beeswax buffing technique that repels moisture and sweat while creating a buttery smooth edge.', 'Condition with neatsfoot oil every 12 months for lasting suppleness.', 'assets/images/products/artisan_belt.jpg', 0, 1, 0, 'published'),

(6, 'BL-SHOE-006', 'The Kensington Chelsea Leather Boot', 'the-kensington-chelsea-leather-boot', 5, 'Full-Grain Box Calf Leather', 'Goodyear Welt 360° with Dainite Rubber Sole', 'Dark Mahogany', 'UK 7 to UK 12 / US 8 to US 13', 'A quintessential British silhouette featuring 360° Goodyear welt construction and durable Dainite studded rubber soles.', 'The Kensington merges boardroom elegance with all-weather rugged durability. Constructed with twin-needle reinforced elastic side gores, full glove-leather interior lining, and storm welted Dainite soles that can be resoled indefinitely.', 'Use cedar shoe trees after each wear. Buff with saphir cream polish.', 'assets/images/products/kensington_boots.jpg', 1, 0, 1, 'published'),

(7, 'BL-ACC-007', 'The Connoisseur Triple Watch Roll', 'the-connoisseur-triple-watch-roll', 6, 'Nappa Leather & Alcantara Interior', 'Concealed Magnetic Brass Snaps', 'British Racing Green & Tan', '22cm x 10cm x 7.5cm', 'Sculpted protective case accommodating 3 luxury timepieces with removable flexible slide-in cushions.', 'Cradle your horological treasures in sumptuous Alcantara fleece. Designed with rigid perimeter walling to prevent crush damage during international air travel.', 'Keep away from excessive heat and direct sunlight.', 'assets/images/products/watch_roll.jpg', 0, 0, 1, 'published'),

(8, 'BL-JKT-008', 'The Aviator Shearling Leather Flight Jacket', 'the-aviator-shearling-leather-flight-jacket', 1, 'Heavy Spanish Merino Shearling Lambskin', 'Heavy Gauge Brass Buckles & Zippers', 'Antiqued Distressed Brown', 'Heritage Relaxed Fit', 'A cold-weather masterpiece crafted from plush Spanish merino shearling with buckled collar and waist straps.', 'Inspired by 1940s RAF flight jackets, this piece offers unmatched warmth in sub-zero climates. The thick 15mm natural wool interior breathes naturally while blocking biting winds, complemented by distressed brown vintage exterior patina.', 'Professional leather and fur cleaning only.', 'assets/images/products/aviator_jacket.jpg', 1, 0, 1, 'published');

-- ---------------------------------------------------------------------
-- 6. PRODUCT COUNTRY PRICING & INVENTORY MATRIX
-- ---------------------------------------------------------------------
-- Country 1: India (INR ₹)
-- Country 2: United States (USD $)
-- Country 3: United Kingdom (GBP £)
-- Country 4: United Arab Emirates (AED)

INSERT INTO `product_country_pricing` (`product_id`, `country_id`, `original_price`, `discount_price`, `discount_start_date`, `discount_end_date`, `stock_quantity`, `low_stock_threshold`, `is_available`) VALUES
-- 1. Sovereign Biker Jacket
(1, 1, 38500.00, 32900.00, '2026-01-01', '2026-12-31', 18, 4, 1),
(1, 2, 495.00, 420.00, '2026-01-01', '2026-12-31', 25, 5, 1),
(1, 3, 395.00, 345.00, '2026-01-01', '2026-12-31', 15, 3, 1),
(1, 4, 1850.00, 1590.00, '2026-01-01', '2026-12-31', 12, 3, 1),

-- 2. Diplomat Briefcase
(2, 1, 28500.00, NULL, NULL, NULL, 22, 5, 1),
(2, 2, 360.00, NULL, NULL, NULL, 30, 5, 1),
(2, 3, 290.00, NULL, NULL, NULL, 20, 4, 1),
(2, 4, 1350.00, NULL, NULL, NULL, 16, 4, 1),

-- 3. Voyager 48-Hour Duffel
(3, 1, 32000.00, 27500.00, '2026-01-01', '2026-12-31', 14, 3, 1),
(3, 2, 410.00, 350.00, '2026-01-01', '2026-12-31', 20, 4, 1),
(3, 3, 330.00, 285.00, '2026-01-01', '2026-12-31', 18, 3, 1),
(3, 4, 1520.00, 1290.00, '2026-01-01', '2026-12-31', 10, 2, 1),

-- 4. Heritage Slim Bifold Wallet
(4, 1, 6500.00, 5200.00, '2026-01-01', '2026-12-31', 65, 10, 1),
(4, 2, 85.00, 68.00, '2026-01-01', '2026-12-31', 80, 15, 1),
(4, 3, 68.00, 55.00, '2026-01-01', '2026-12-31', 50, 10, 1),
(4, 4, 310.00, 250.00, '2026-01-01', '2026-12-31', 40, 8, 1),

-- 5. Master Artisan Harness Belt
(5, 1, 7500.00, NULL, NULL, NULL, 40, 8, 1),
(5, 2, 95.00, NULL, NULL, NULL, 55, 10, 1),
(5, 3, 75.00, NULL, NULL, NULL, 35, 8, 1),
(5, 4, 350.00, NULL, NULL, NULL, 25, 5, 1),

-- 6. Kensington Chelsea Boot
(6, 1, 24500.00, 21900.00, '2026-01-01', '2026-12-31', 16, 4, 1),
(6, 2, 310.00, 275.00, '2026-01-01', '2026-12-31', 22, 5, 1),
(6, 3, 250.00, 220.00, '2026-01-01', '2026-12-31', 25, 5, 1),
(6, 4, 1150.00, 990.00, '2026-01-01', '2026-12-31', 12, 3, 1),

-- 7. Connoisseur Triple Watch Roll
(7, 1, 11500.00, NULL, NULL, NULL, 28, 5, 1),
(7, 2, 145.00, NULL, NULL, NULL, 35, 6, 1),
(7, 3, 115.00, NULL, NULL, NULL, 24, 4, 1),
(7, 4, 530.00, NULL, NULL, NULL, 18, 4, 1),

-- 8. Aviator Shearling Flight Jacket
(8, 1, 68000.00, 59900.00, '2026-01-01', '2026-12-31', 8, 2, 1),
(8, 2, 850.00, 750.00, '2026-01-01', '2026-12-31', 12, 3, 1),
(8, 3, 680.00, 595.00, '2026-01-01', '2026-12-31', 10, 2, 1),
(8, 4, 3150.00, 2750.00, '2026-01-01', '2026-12-31', 6, 2, 1);

-- ---------------------------------------------------------------------
-- 7. PROMOTIONAL COUPONS
-- ---------------------------------------------------------------------
INSERT INTO `coupons` (`id`, `code`, `country_id`, `discount_type`, `discount_value`, `min_spend`, `valid_from`, `valid_until`, `usage_limit`, `times_used`, `status`) VALUES
(1, 'WELCOME10', NULL, 'percentage', 10.00, 50.00, '2026-01-01', '2026-12-31', 500, 12, 'active'),
(2, 'ROYALINDIA', 1, 'fixed', 2500.00, 20000.00, '2026-01-01', '2026-12-31', 200, 4, 'active'),
(3, 'WEST50', 2, 'fixed', 50.00, 300.00, '2026-01-01', '2026-12-31', 150, 7, 'active'),
(4, 'LONDON40', 3, 'fixed', 40.00, 250.00, '2026-01-01', '2026-12-31', 100, 3, 'active');

-- ---------------------------------------------------------------------
-- 8. CUSTOMER ADDRESSES
-- ---------------------------------------------------------------------
INSERT INTO `customer_addresses` (`id`, `user_id`, `country_id`, `address_type`, `recipient_name`, `phone_number`, `street_address_1`, `street_address_2`, `city`, `state_province`, `postal_code`, `is_default`) VALUES
(1, 4, 2, 'both', 'Lord Julian Sterling', '+1 212 555 0199', '740 Park Avenue', 'Apt 12B', 'New York', 'NY', '10021', 1);

-- ---------------------------------------------------------------------
-- 9. PRODUCT REVIEWS
-- ---------------------------------------------------------------------
INSERT INTO `product_reviews` (`id`, `product_id`, `user_id`, `country_id`, `rating`, `review_title`, `review_body`, `status`) VALUES
(1, 1, 4, 2, 5, 'Exceptional craftsmanship beyond expectations', 'The weight and smell of the Italian cowhide is unmatched. The brass zipper is remarkably robust. Best leather jacket I have owned in twenty years.', 'approved'),
(2, 2, 4, 2, 5, 'Flawless briefcase for high-level meetings', 'I receive compliments every time I carry The Diplomat. The Tuscan vachetta is developing a subtle cognac sheen after just two months.', 'approved'),
(3, 4, 4, 2, 5, 'Elegantly thin yet spacious', 'Slides into bespoke suit pockets without disturbing silhouette lines. The RFID protection gives great peace of mind.', 'approved');

-- ---------------------------------------------------------------------
-- 10. SYSTEM SETTINGS
-- ---------------------------------------------------------------------
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `group_name`) VALUES
('site_name', 'Buller Leather', 'brand'),
('site_tagline', 'Generational Leather Craftsmanship & Heritage Luxury', 'brand'),
('contact_email', 'concierge@bullerleather.com', 'contact'),
('contact_phone', '+1 (800) 285-5371', 'contact'),
('contact_address', 'Buller Atelier, 48 Conduit Street, Mayfair, London W1S 2YG, UK', 'contact'),
('default_country_code', 'US', 'localization'),
('razorpay_key_id', 'rzp_test_bullerleather_placeholder', 'payments'),
('razorpay_key_secret', 'buller_secret_key_placeholder', 'payments'),
('smtp_host', 'smtp.bullerleather.com', 'email'),
('smtp_port', '587', 'email'),
('smtp_user', 'notifications@bullerleather.com', 'email'),
('smtp_pass', 'BullerMailSecure2026', 'email'),
('smtp_from_name', 'Buller Leather Concierge', 'email'),
('free_shipping_notice', 'Complimentary International Express Delivery on Qualifying Curations', 'announcement');
