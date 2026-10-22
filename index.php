<?php
/**
 * BULLER LEATHER — MASTER FRONT CONTROLLER & ROUTER
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/core/Helpers.php';

use App\Core\Database;
use App\Core\Auth;
use App\Core\MarketContext;
use App\Core\Cart;
use App\Core\Wishlist;
use App\Core\AuditLogger;

// Initialize market context
$activeCountry = MarketContext::init();
$db = Database::getConnection();

// Determine current route
$route = $_GET['route'] ?? '';
$route = trim($route, '/');
if (empty($route)) {
    $route = 'home';
}
if (str_starts_with($route, 'product/')) {
    $_GET['slug'] = substr($route, strlen('product/'));
    $route = 'product';
}

// Global Category Helper
$catStmt = $db->query("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC");
$categories = $catStmt->fetchAll();

// =========================================================================
// ROUTE HANDLING
// =========================================================================

switch ($route) {
    // ---------------------------------------------------------------------
    // 1. STOREFRONT: HOMEPAGE
    // ---------------------------------------------------------------------
    case 'home':
        $countryId = (int)$activeCountry['id'];

        // Featured Products
        $featStmt = $db->prepare("SELECT p.*, pcp.original_price, pcp.discount_price, pcp.discount_start_date, pcp.discount_end_date, pcp.stock_quantity 
                                  FROM products p 
                                  JOIN product_country_pricing pcp ON p.id = pcp.product_id 
                                  WHERE pcp.country_id = ? AND pcp.is_available = 1 AND p.is_featured = 1 AND p.status = 'published' 
                                  ORDER BY p.id ASC LIMIT 4");
        $featStmt->execute([$countryId]);
        $featuredProducts = $featStmt->fetchAll();

        // Bestseller Products
        $bestStmt = $db->prepare("SELECT p.*, pcp.original_price, pcp.discount_price, pcp.discount_start_date, pcp.discount_end_date, pcp.stock_quantity 
                                  FROM products p 
                                  JOIN product_country_pricing pcp ON p.id = pcp.product_id 
                                  WHERE pcp.country_id = ? AND pcp.is_available = 1 AND p.is_bestseller = 1 AND p.status = 'published' 
                                  ORDER BY p.id ASC LIMIT 4");
        $bestStmt->execute([$countryId]);
        $bestsellerProducts = $bestStmt->fetchAll();

        // Customer Reviews
        $revStmt = $db->query("SELECT pr.*, u.full_name as user_name, c.name as country_name 
                               FROM product_reviews pr 
                               JOIN users u ON pr.user_id = u.id 
                               JOIN countries c ON pr.country_id = c.id 
                               WHERE pr.status = 'approved' ORDER BY pr.id DESC LIMIT 3");
        $reviews = $revStmt->fetchAll();

        require __DIR__ . '/app/views/storefront/home.php';
        break;

    // ---------------------------------------------------------------------
    // 2. STOREFRONT: SHOP / CATALOG
    // ---------------------------------------------------------------------
    case 'shop':
    case 'sale':
        $countryId = (int)$activeCountry['id'];
        $categorySlug = $_GET['category'] ?? null;
        $filter = $_GET['filter'] ?? (($route === 'sale') ? 'sale' : null);
        $search = $_GET['q'] ?? null;
        $sort = $_GET['sort'] ?? 'featured';

        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                       pcp.original_price, pcp.discount_price, pcp.discount_start_date, pcp.discount_end_date, pcp.stock_quantity
                FROM products p
                JOIN categories c ON p.category_id = c.id
                JOIN product_country_pricing pcp ON p.id = pcp.product_id
                WHERE pcp.country_id = ? AND pcp.is_available = 1 AND p.status = 'published'";

        $params = [$countryId];

        if ($categorySlug) {
            $sql .= " AND c.slug = ?";
            $params[] = $categorySlug;

            $catInfoStmt = $db->prepare("SELECT * FROM categories WHERE slug = ? LIMIT 1");
            $catInfoStmt->execute([$categorySlug]);
            $activeCategory = $catInfoStmt->fetch();
        }

        if ($filter === 'sale') {
            $sql .= " AND pcp.discount_price IS NOT NULL AND pcp.discount_price < pcp.original_price";
        } elseif ($filter === 'bestseller') {
            $sql .= " AND p.is_bestseller = 1";
        } elseif ($filter === 'new') {
            $sql .= " AND p.is_new_arrival = 1";
        }

        if ($search) {
            $sql .= " AND (p.title LIKE ? OR p.short_description LIKE ? OR p.leather_type LIKE ? OR p.sku LIKE ?)";
            $searchTerm = "%$search%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        // Sorting
        if ($sort === 'price_asc') {
            $sql .= " ORDER BY COALESCE(pcp.discount_price, pcp.original_price) ASC";
        } elseif ($sort === 'price_desc') {
            $sql .= " ORDER BY COALESCE(pcp.discount_price, pcp.original_price) DESC";
        } elseif ($sort === 'newest') {
            $sql .= " ORDER BY p.id DESC";
        } else {
            $sql .= " ORDER BY p.is_featured DESC, p.id ASC";
        }

        $prodStmt = $db->prepare($sql);
        $prodStmt->execute($params);
        $products = $prodStmt->fetchAll();

        require __DIR__ . '/app/views/storefront/shop.php';
        break;

    // ---------------------------------------------------------------------
    // 3. STOREFRONT: PRODUCT DETAIL & TESTIMONIAL SUBMISSION
    // ---------------------------------------------------------------------
    case 'product':
        $slug = $_GET['slug'] ?? '';
        $countryId = (int)$activeCountry['id'];

        $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug,
                                     pcp.original_price, pcp.discount_price, pcp.discount_start_date, pcp.discount_end_date, 
                                     pcp.stock_quantity, pcp.low_stock_threshold, pcp.is_available
                              FROM products p
                              JOIN categories c ON p.category_id = c.id
                              JOIN product_country_pricing pcp ON p.id = pcp.product_id
                              WHERE p.slug = ? AND pcp.country_id = ? AND p.status = 'published' LIMIT 1");
        $stmt->execute([$slug, $countryId]);
        $product = $stmt->fetch();

        if (!$product) {
            http_response_code(404);
            require __DIR__ . '/app/views/errors/404.php';
            exit;
        }

        // Multi-angle Gallery Images
        $imgStmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
        $imgStmt->execute([$product['id']]);
        $galleryImages = $imgStmt->fetchAll();

        // Fallback if gallery empty
        if (empty($galleryImages)) {
            $galleryImages = [
                ['image_url' => $product['primary_image'], 'alt_text' => $product['title'] . ' Primary View', 'sort_order' => 1]
            ];
        }

        // Approved Reviews for this product
        $revStmt = $db->prepare("SELECT pr.*, u.full_name as user_name, c.name as country_name, c.code as country_code 
                                 FROM product_reviews pr 
                                 JOIN users u ON pr.user_id = u.id 
                                 JOIN countries c ON pr.country_id = c.id 
                                 WHERE pr.product_id = ? AND pr.status = 'approved' ORDER BY pr.id DESC");
        $revStmt->execute([$product['id']]);
        $reviews = $revStmt->fetchAll();

        $avgRating = !empty($reviews) ? round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1) : 5.0;

        // Related Curations in Active Market
        $relStmt = $db->prepare("SELECT p.*, c.name as category_name,
                                        pcp.original_price, pcp.discount_price, pcp.discount_start_date, pcp.discount_end_date, pcp.stock_quantity 
                                 FROM products p 
                                 JOIN categories c ON p.category_id = c.id
                                 JOIN product_country_pricing pcp ON p.id = pcp.product_id 
                                 WHERE pcp.country_id = ? AND pcp.is_available = 1 AND p.status = 'published' AND p.id != ? 
                                 ORDER BY (p.category_id = ?) DESC, p.is_bestseller DESC, p.id ASC LIMIT 4");
        $relStmt->execute([$countryId, $product['id'], $product['category_id']]);
        $relatedProducts = $relStmt->fetchAll();

        require __DIR__ . '/app/views/storefront/product.php';
        break;

    case 'product/review/submit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                die("CSRF Security Token Verification Failed.");
            }

            $productId = (int)($_POST['product_id'] ?? 0);
            $slug = trim($_POST['product_slug'] ?? '');
            $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
            $title = trim($_POST['review_title'] ?? 'Exceptional Masterpiece');
            $body = trim($_POST['review_body'] ?? '');
            $authorName = trim($_POST['patron_name'] ?? '');
            $countryId = (int)$activeCountry['id'];

            if ($productId <= 0 || empty($body)) {
                flash('review_error', 'Please provide your honest impressions and rating.');
                redirect('product?slug=' . urlencode($slug));
            }

            $userId = Auth::id();
            if (!$userId) {
                $email = trim($_POST['patron_email'] ?? 'patron@bullerleather.com');
                $chk = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                $chk->execute([$email]);
                $existingUser = $chk->fetch();
                if ($existingUser) {
                    $userId = (int)$existingUser['id'];
                } else {
                    $insU = $db->prepare("INSERT INTO users (full_name, email, password_hash, role, default_country_id, status) VALUES (?, ?, ?, 'customer', ?, 'active')");
                    $insU->execute([$authorName ?: 'Verified Buyer', $email, password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT), $countryId]);
                    $userId = (int)$db->lastInsertId();
                }
            }

            $insRev = $db->prepare("INSERT INTO product_reviews (product_id, user_id, country_id, rating, review_title, review_body, status) VALUES (?, ?, ?, ?, ?, ?, 'approved')");
            $insRev->execute([$productId, $userId, $countryId, $rating, $title, $body]);
            $newRevId = (int)$db->lastInsertId();

            AuditLogger::log($userId, 'submit_review', 'product_reviews', $newRevId, ['product_id' => $productId, 'rating' => $rating]);
            flash('review_success', 'Thank you! Your product review has been submitted successfully.');
            redirect('product?slug=' . urlencode($slug) . '#reviewsTab');
        }
        break;

    // ---------------------------------------------------------------------
    // 4. STOREFRONT: CART & CHECKOUT
    // ---------------------------------------------------------------------
    case 'cart':
        require __DIR__ . '/app/views/storefront/cart.php';
        break;

    case 'checkout':
        require __DIR__ . '/app/views/storefront/checkout.php';
        break;

    case 'coupon/apply':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
            $countryId = (int)$activeCountry['id'];

            $cpnStmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND (country_id IS NULL OR country_id = ?) LIMIT 1");
            $cpnStmt->execute([$code, $countryId]);
            $coupon = $cpnStmt->fetch();

            if ($coupon) {
                $_SESSION['applied_coupon'] = $coupon;
                flash('coupon_success', "Coupon code '$code' applied successfully!");
            } else {
                unset($_SESSION['applied_coupon']);
                flash('coupon_error', "The coupon code '$code' is invalid or not eligible for your selected country.");
            }
            redirect('checkout');
        }
        break;

    case 'order/process':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                die("Invalid security token.");
            }

            $summary = Cart::getSummary();
            if (empty($summary['items'])) {
                redirect('shop');
            }

            $countryId = (int)$activeCountry['id'];
            $orderNumber = 'BL-' . $activeCountry['code'] . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $customerName = trim($_POST['customer_name'] ?? 'Guest');
            $customerEmail = trim($_POST['customer_email'] ?? 'guest@bullerleather.com');
            $customerPhone = trim($_POST['customer_phone'] ?? '');
            $paymentMethod = $_POST['payment_method'] ?? 'stripe';

            $shippingAddress = [
                'recipient' => $customerName,
                'phone' => $customerPhone,
                'street_1' => trim($_POST['street_address_1'] ?? ''),
                'street_2' => trim($_POST['street_address_2'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'state' => trim($_POST['state'] ?? ''),
                'postal_code' => trim($_POST['postal_code'] ?? ''),
                'country' => $activeCountry['name'],
                'country_code' => $activeCountry['code']
            ];

            // Insert Order
            $orderStmt = $db->prepare("INSERT INTO orders (order_number, customer_id, country_id, customer_name, customer_email, customer_phone, shipping_address_json, currency_code, currency_symbol, subtotal, tax_amount, shipping_amount, discount_amount, coupon_code, grand_total, payment_status, order_status, shipping_carrier, customer_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $customerId = Auth::id();
            $orderStatus = 'processing';
            $paymentStatus = ($paymentMethod === 'cod') ? 'pending' : 'paid';
            $carrier = ($activeCountry['code'] === 'IN') ? 'Blue Dart Express' : 'DHL Express Worldwide';

            $orderStmt->execute([
                $orderNumber,
                $customerId,
                $countryId,
                $customerName,
                $customerEmail,
                $customerPhone,
                json_encode($shippingAddress),
                $activeCountry['currency_code'],
                $activeCountry['currency_symbol'],
                $summary['subtotal'],
                $summary['tax_amount'],
                $summary['shipping_amount'],
                $summary['discount_amount'],
                $summary['coupon']['code'] ?? null,
                $summary['grand_total'],
                $paymentStatus,
                $orderStatus,
                $carrier,
                trim($_POST['customer_notes'] ?? '')
            ]);

            $orderId = (int)$db->lastInsertId();

            // Insert Order Items & Decrement Inventory
            $itemStmt = $db->prepare("INSERT INTO order_items (order_id, product_id, sku, product_title, product_image, unit_price, quantity, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $decStmt = $db->prepare("UPDATE product_country_pricing SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE product_id = ? AND country_id = ?");

            foreach ($summary['items'] as $it) {
                $itemStmt->execute([
                    $orderId,
                    $it['product_id'],
                    $it['sku'],
                    $it['title'],
                    $it['primary_image'],
                    $it['unit_price'],
                    $it['quantity'],
                    $it['total_price']
                ]);

                $decStmt->execute([$it['quantity'], $it['product_id'], $countryId]);
            }

            // Insert Payment Record
            $payStmt = $db->prepare("INSERT INTO payments (order_id, payment_method, transaction_reference, amount, currency, status) VALUES (?, ?, ?, ?, ?, ?)");
            $payStmt->execute([
                $orderId,
                $paymentMethod,
                'TXN-' . strtoupper(bin2hex(random_bytes(6))),
                $summary['grand_total'],
                $activeCountry['currency_code'],
                ($paymentStatus === 'paid') ? 'captured' : 'initiated'
            ]);

            // Clear Cart
            Cart::clear();

            // Redirect to Confirmation
            redirect('order/confirmation?id=' . $orderId);
        }
        break;

    case 'order/confirmation':
        $orderId = (int)($_GET['id'] ?? 0);
        $ordStmt = $db->prepare("SELECT o.*, c.name as country_name FROM orders o JOIN countries c ON o.country_id = c.id WHERE o.id = ?");
        $ordStmt->execute([$orderId]);
        $order = $ordStmt->fetch();

        if (!$order) {
            redirect('shop');
        }

        $itemsStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $itemsStmt->execute([$orderId]);
        $orderItems = $itemsStmt->fetchAll();

        require __DIR__ . '/app/views/storefront/order_confirmation.php';
        break;

    case 'wishlist':
        $countryId = (int)$activeCountry['id'];
        $wIds = [];
        if (Auth::isLoggedIn()) {
            $wStmt = $db->prepare("SELECT product_id FROM wishlists WHERE user_id = ?");
            $wStmt->execute([Auth::id()]);
            $wIds = $wStmt->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $wIds = $_SESSION['guest_wishlist'] ?? [];
        }

        $products = [];
        if (!empty($wIds)) {
            $inPlace = implode(',', array_fill(0, count($wIds), '?'));
            $sql = "SELECT p.*, pcp.original_price, pcp.discount_price, pcp.discount_start_date, pcp.discount_end_date 
                    FROM products p 
                    JOIN product_country_pricing pcp ON p.id = pcp.product_id 
                    WHERE pcp.country_id = ? AND p.id IN ($inPlace) AND p.status = 'published'";
            $pStmt = $db->prepare($sql);
            $pStmt->execute(array_merge([$countryId], $wIds));
            $products = $pStmt->fetchAll();
        }

        require __DIR__ . '/app/views/storefront/wishlist.php';
        break;

    // ---------------------------------------------------------------------
    // 5. AUTHENTICATION & CUSTOMER PORTAL
    // ---------------------------------------------------------------------
    case 'login':
        if (Auth::isLoggedIn()) {
            if (Auth::isSuperAdmin()) redirect('superadmin/dashboard');
            if (Auth::isCountryAdmin()) redirect('admin/dashboard');
            redirect('customer/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                die("Invalid security token.");
            }
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (Auth::attempt($email, $password)) {
                // Remove admin login from user side: admins must authenticate via /admin
                if (Auth::isSuperAdmin() || Auth::isCountryAdmin()) {
                    Auth::logout();
                    flash('login_error', 'Administrative accounts cannot sign in through the customer portal. Please navigate to /admin to access the Admin Portal.');
                    redirect('login');
                }
                redirect('customer/dashboard');
            } else {
                flash('login_error', 'Invalid email credentials or unauthorized account status.');
                redirect('login');
            }
        }

        require __DIR__ . '/app/views/auth/login.php';
        break;

    case 'register':
        if (Auth::isLoggedIn()) {
            redirect('customer/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                die("Invalid security token.");
            }

            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $countryId = (int)($_POST['default_country_id'] ?? 1);
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['password_confirm'] ?? '';

            if (empty($fullName) || empty($email) || empty($password)) {
                flash('register_error', 'Please fill in all mandatory fields.');
                redirect('register');
            }

            if ($password !== $confirm) {
                flash('register_error', 'Password confirmation does not match.');
                redirect('register');
            }

            // Check existing
            $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                flash('register_error', 'An account with this email address already exists.');
                redirect('register');
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $insStmt = $db->prepare("INSERT INTO users (full_name, email, password_hash, phone, role, default_country_id, status) VALUES (?, ?, ?, ?, 'customer', ?, 'active')");
            $insStmt->execute([$fullName, $email, $hash, $phone, $countryId]);

            Auth::attempt($email, $password);
            redirect('customer/dashboard');
        }

        $countries = MarketContext::getAllActiveCountries();
        require __DIR__ . '/app/views/auth/register.php';
        break;

    case 'logout':
        $wasAdmin = Auth::isSuperAdmin() || Auth::isCountryAdmin();
        Auth::logout();
        if ($wasAdmin) {
            flash('admin_login_success', 'You have been securely signed out of the administrative session.');
            redirect('admin');
        }
        redirect('');
        break;

    case 'customer/dashboard':
    case 'customer/orders':
        if (!Auth::isLoggedIn()) {
            redirect('login');
        }

        $userId = Auth::id();
        $uStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $uStmt->execute([$userId]);
        $user = $uStmt->fetch();

        $ordStmt = $db->prepare("SELECT o.*, COUNT(oi.id) as item_count 
                                 FROM orders o 
                                 LEFT JOIN order_items oi ON o.id = oi.order_id 
                                 WHERE o.customer_id = ? 
                                 GROUP BY o.id ORDER BY o.id DESC");
        $ordStmt->execute([$userId]);
        $recentOrders = $ordStmt->fetchAll();

        require __DIR__ . '/app/views/customer/dashboard.php';
        break;

    // ---------------------------------------------------------------------
    // SECURE DEDICATED ADMIN PORTAL ROUTE (/admin, /admin/login, /superadmin)
    // ---------------------------------------------------------------------
    case 'admin':
    case 'admin/login':
    case 'superadmin':
        if (Auth::isSuperAdmin()) {
            redirect('superadmin/dashboard');
        }
        if (Auth::isCountryAdmin()) {
            redirect('admin/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                die("Invalid security token.");
            }
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (Auth::attempt($email, $password)) {
                if (Auth::isSuperAdmin()) {
                    redirect('superadmin/dashboard');
                }
                if (Auth::isCountryAdmin()) {
                    redirect('admin/dashboard');
                }
                // If a customer attempts to sign in via /admin:
                Auth::logout();
                flash('admin_login_error', 'Access Restricted: This portal is exclusively for authorized administrators. Customers please sign in via customer portal.');
                redirect('admin');
            } else {
                flash('admin_login_error', 'Invalid administrator credentials or unauthorized account status.');
                redirect('admin');
            }
        }

        require __DIR__ . '/app/views/admin/login.php';
        break;

    // ---------------------------------------------------------------------
    // 6. SUPER ADMIN MANAGEMENT
    // ---------------------------------------------------------------------
    case 'superadmin/dashboard':
        if (!Auth::isSuperAdmin()) {
            redirect('admin');
        }

        // Global KPIs
        $revTotal = (float)$db->query("SELECT SUM(grand_total) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
        $orderCount = (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $custCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
        $adminCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'country_admin' AND status = 'active'")->fetchColumn();

        $kpis = [
            'gross_revenue' => $revTotal,
            'total_orders' => $orderCount,
            'total_customers' => $custCount,
            'total_admins' => $adminCount
        ];

        // Country-wise sales breakdown
        $csStmt = $db->query("SELECT c.*, 
                                     COALESCE(COUNT(o.id), 0) as order_count, 
                                     COALESCE(SUM(o.grand_total), 0) as total_revenue
                              FROM countries c
                              LEFT JOIN orders o ON c.id = o.country_id AND o.payment_status = 'paid'
                              GROUP BY c.id ORDER BY total_revenue DESC");
        $countrySales = $csStmt->fetchAll();

        // Low stock products across all countries
        $lsStmt = $db->query("SELECT p.title, p.sku, c.code as country_code, c.name as country_name, c.flag_icon, pcp.stock_quantity, pcp.low_stock_threshold
                              FROM product_country_pricing pcp
                              JOIN products p ON pcp.product_id = p.id
                              JOIN countries c ON pcp.country_id = c.id
                              WHERE pcp.stock_quantity <= pcp.low_stock_threshold
                              ORDER BY pcp.stock_quantity ASC LIMIT 5");
        $lowStockProducts = $lsStmt->fetchAll();

        // Recent Audit logs
        $audStmt = $db->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 5");
        $recentAudits = $audStmt->fetchAll();

        require __DIR__ . '/app/views/superadmin/dashboard.php';
        break;

    case 'superadmin/countries':
        if (!Auth::isSuperAdmin()) redirect('admin');
        $cStmt = $db->query("SELECT * FROM countries ORDER BY id ASC");
        $countries = $cStmt->fetchAll();
        require __DIR__ . '/app/views/superadmin/countries.php';
        break;

    case 'superadmin/countries/create':
        if (!Auth::isSuperAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF error");

            $name = trim($_POST['name'] ?? '');
            $code = strtoupper(trim($_POST['code'] ?? ''));
            $flag = trim($_POST['flag_icon'] ?? '🌐');
            $currCode = strtoupper(trim($_POST['currency_code'] ?? 'USD'));
            $currSym = trim($_POST['currency_symbol'] ?? '$');
            $currName = trim($_POST['currency_name'] ?? 'US Dollar');
            $taxName = trim($_POST['tax_name'] ?? 'VAT / Tax');
            $taxRate = (float)($_POST['tax_rate_percent'] ?? 0);
            $flatShip = (float)($_POST['flat_shipping_rate'] ?? 20);
            $freeThresh = (float)($_POST['free_shipping_threshold'] ?? 200);
            $returnWin = (int)($_POST['return_window_days'] ?? 30);
            $phoneCode = trim($_POST['phone_code'] ?? '+1');

            $stmt = $db->prepare("INSERT INTO countries (code, name, currency_code, currency_symbol, currency_name, tax_rate_percent, tax_name, flat_shipping_rate, free_shipping_threshold, return_window_days, phone_code, flag_icon, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$code, $name, $currCode, $currSym, $currName, $taxRate, $taxName, $flatShip, $freeThresh, $returnWin, $phoneCode, $flag]);

            $newCountryId = (int)$db->lastInsertId();

            // Populate product_country_pricing for all existing master products with base defaults
            $allProd = $db->query("SELECT id FROM products")->fetchAll(PDO::FETCH_COLUMN);
            $pcpIns = $db->prepare("INSERT INTO product_country_pricing (product_id, country_id, original_price, stock_quantity, low_stock_threshold, is_available) VALUES (?, ?, 100.00, 20, 5, 1)");
            foreach ($allProd as $pid) {
                $pcpIns->execute([$pid, $newCountryId]);
            }

            AuditLogger::log(Auth::id(), 'create_country', 'countries', $newCountryId, ['name' => $name, 'code' => $code]);
            flash('admin_success', "International market '$name' successfully provisioned with default catalog pricing.");
            redirect('superadmin/countries');
        }
        break;

    case 'superadmin/countries/toggle':
        if (!Auth::isSuperAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF error");
            $countryId = (int)($_POST['country_id'] ?? 0);
            $db->prepare("UPDATE countries SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?")->execute([$countryId]);
            AuditLogger::log(Auth::id(), 'toggle_country_status', 'countries', $countryId);
            flash('admin_success', 'Regional market status updated.');
            redirect('superadmin/countries');
        }
        break;

    case 'superadmin/admins':
        if (!Auth::isSuperAdmin()) redirect('admin');
        
        $admStmt = $db->query("SELECT u.* FROM users u WHERE u.role IN ('super_admin', 'country_admin') ORDER BY u.id ASC");
        $admins = $admStmt->fetchAll();

        foreach ($admins as &$admin) {
            $cStmt = $db->prepare("SELECT c.id, c.name, c.code, c.flag_icon FROM admin_country_access aca JOIN countries c ON aca.country_id = c.id WHERE aca.admin_id = ?");
            $cStmt->execute([$admin['id']]);
            $admin['assigned_countries'] = $cStmt->fetchAll();
        }

        $allCountriesStmt = $db->query("SELECT * FROM countries WHERE is_active = 1");
        $allCountries = $allCountriesStmt->fetchAll();

        require __DIR__ . '/app/views/superadmin/admins.php';
        break;

    case 'superadmin/admins/create':
        if (!Auth::isSuperAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF error");

            $name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $phone = trim($_POST['phone'] ?? '');
            $assignedCountries = $_POST['assigned_countries'] ?? [];

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT INTO users (full_name, email, password_hash, phone, role, status) VALUES (?, ?, ?, ?, 'country_admin', 'active')");
            $stmt->execute([$name, $email, $hash, $phone]);
            $adminId = (int)$db->lastInsertId();

            // Assign country access
            $acaStmt = $db->prepare("INSERT INTO admin_country_access (admin_id, country_id, can_manage_products, can_manage_orders, can_manage_discounts, can_moderate_reviews) VALUES (?, ?, 1, 1, 1, 1)");
            foreach ($assignedCountries as $cid) {
                $acaStmt->execute([$adminId, (int)$cid]);
            }

            AuditLogger::log(Auth::id(), 'create_admin', 'users', $adminId, ['email' => $email, 'assigned_countries' => $assignedCountries]);
            flash('admin_success', "Administrator '$name' successfully created and scoped to designated markets.");
            redirect('superadmin/admins');
        }
        break;

    case 'superadmin/admins/toggle':
        if (!Auth::isSuperAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF error");
            $adminId = (int)($_POST['admin_id'] ?? 0);
            if ($adminId !== Auth::id()) {
                $db->prepare("UPDATE users SET status = IF(status = 'active', 'suspended', 'active') WHERE id = ?")->execute([$adminId]);
                AuditLogger::log(Auth::id(), 'toggle_admin_status', 'users', $adminId);
                flash('admin_success', 'Administrator account status updated.');
            }
            redirect('superadmin/admins');
        }
        break;

    case 'superadmin/products':
        Auth::requireSuperAdmin();
        $catStmt = $db->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id ASC");
        $catalog = $catStmt->fetchAll();
        $imgStmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC");
        foreach ($catalog as &$prod) {
            $prStmt = $db->prepare("SELECT pcp.*, co.name as country_name, co.code as country_code, co.currency_symbol, co.currency_code 
                                    FROM product_country_pricing pcp 
                                    JOIN countries co ON pcp.country_id = co.id 
                                    WHERE pcp.product_id = ? ORDER BY co.id ASC");
            $prStmt->execute([$prod['id']]);
            $prod['country_prices'] = $prStmt->fetchAll();

            $imgStmt->execute([$prod['id']]);
            $prod['gallery_images'] = $imgStmt->fetchAll();
        }
        $allCategories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, name ASC")->fetchAll();
        require __DIR__ . '/app/views/superadmin/products.php';
        break;

    case 'superadmin/orders':
        Auth::requireSuperAdmin();
        $allCountries = $db->query("SELECT * FROM countries ORDER BY name ASC")->fetchAll();
        $countryFilter = $_GET['country_id'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        
        $filterCountryName = '';
        $sql = "SELECT o.*, c.name as country_name, c.code as country_code FROM orders o JOIN countries c ON o.country_id = c.id WHERE 1=1";
        $params = [];
        if (!empty($countryFilter)) {
            $sql .= " AND o.country_id = ?";
            $params[] = (int)$countryFilter;
            foreach ($allCountries as $ac) {
                if ($ac['id'] == $countryFilter) {
                    $filterCountryName = $ac['name'];
                    break;
                }
            }
        }
        if (!empty($statusFilter)) {
            $sql .= " AND o.order_status = ?";
            $params[] = $statusFilter;
        }
        $sql .= " ORDER BY o.id DESC";
        $ordStmt = $db->prepare($sql);
        $ordStmt->execute($params);
        $orders = $ordStmt->fetchAll();
        require __DIR__ . '/app/views/superadmin/orders.php';
        break;

    case 'superadmin/settings':
        Auth::requireSuperAdmin();
        $settStmt = $db->query("SELECT setting_key, setting_value FROM system_settings");
        $settings = $settStmt->fetchAll(PDO::FETCH_KEY_PAIR);
        require __DIR__ . '/app/views/superadmin/settings.php';
        break;

    case 'superadmin/settings/save':
        Auth::requireSuperAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                AuditLogger::log(Auth::id(), 'csrf_violation', 'system_settings');
                die("CSRF Security Validation Failed.");
            }
            $upStmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            foreach ($_POST as $k => $v) {
                if ($k === 'csrf_token') continue;
                $upStmt->execute([$k, trim($v)]);
            }
            AuditLogger::log(Auth::id(), 'update_settings', 'system_settings', null, ['keys' => array_keys($_POST)]);
            flash('admin_success', 'Global system configuration successfully updated.');
            redirect('superadmin/settings');
        }
        break;

    case 'superadmin/audit-logs':
        Auth::requireSuperAdmin();
        $actionFilter = $_GET['action'] ?? '';
        if (!empty($actionFilter)) {
            $stmt = $db->prepare("SELECT * FROM audit_logs WHERE action = ? ORDER BY id DESC LIMIT 200");
            $stmt->execute([$actionFilter]);
        } else {
            $stmt = $db->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 200");
        }
        $logs = $stmt->fetchAll();
        require __DIR__ . '/app/views/superadmin/audit_logs.php';
        break;


    // ---------------------------------------------------------------------
    // 7. COUNTRY ADMIN (SCOPED ACCESS CONTROL)
    // ---------------------------------------------------------------------
    case 'admin/dashboard':
    case 'admin/products':
    case 'admin/orders':
        // Super Admin dashboard redirect (Super Admin can access admin/products and admin/orders to manage local pricing and dispatches)
        if (Auth::isSuperAdmin() && $route === 'admin/dashboard') {
            redirect('superadmin/dashboard');
        }

        if (!Auth::isCountryAdmin()) {
            redirect('admin');
        }

        $permittedIds = Auth::getPermittedCountryIds();
        if (empty($permittedIds)) {
            die("Access Denied: You do not have permissions assigned to any regional markets.");
        }

        // Selected country resolution
        $selectedCountryId = isset($_GET['country_id']) ? (int)$_GET['country_id'] : (int)$permittedIds[0];

        // STRICT BACKEND VERIFICATION: Enforce that admin has access to this country!
        Auth::enforceCountryAccess($selectedCountryId);

        // Fetch current country details
        $ccStmt = $db->prepare("SELECT * FROM countries WHERE id = ?");
        $ccStmt->execute([$selectedCountryId]);
        $currentCountry = $ccStmt->fetch();

        // Permitted countries list for header switch dropdown
        $inPerm = implode(',', array_fill(0, count($permittedIds), '?'));
        $pStmt = $db->prepare("SELECT * FROM countries WHERE id IN ($inPerm) ORDER BY name ASC");
        $pStmt->execute($permittedIds);
        $permittedCountries = $pStmt->fetchAll();

        if ($route === 'admin/dashboard') {
            // Scoped KPIs
            $scopedRev = (float)$db->prepare("SELECT SUM(grand_total) FROM orders WHERE country_id = ? AND payment_status = 'paid'")->execute([$selectedCountryId]) ? $db->query("SELECT SUM(grand_total) FROM orders WHERE country_id = $selectedCountryId AND payment_status = 'paid'")->fetchColumn() : 0.0;
            $scopedOrders = (int)$db->query("SELECT COUNT(*) FROM orders WHERE country_id = $selectedCountryId")->fetchColumn();
            $pendingOrders = (int)$db->query("SELECT COUNT(*) FROM orders WHERE country_id = $selectedCountryId AND order_status IN ('placed', 'processing')")->fetchColumn();
            $availProd = (int)$db->query("SELECT COUNT(*) FROM product_country_pricing WHERE country_id = $selectedCountryId AND is_available = 1")->fetchColumn();
            $lowStock = (int)$db->query("SELECT COUNT(*) FROM product_country_pricing WHERE country_id = $selectedCountryId AND stock_quantity <= low_stock_threshold")->fetchColumn();

            $kpis = [
                'revenue' => $scopedRev,
                'total_orders' => $scopedOrders,
                'pending_orders' => $pendingOrders,
                'available_products' => $availProd,
                'low_stock_count' => $lowStock
            ];

            $recOrdStmt = $db->prepare("SELECT * FROM orders WHERE country_id = ? ORDER BY id DESC LIMIT 5");
            $recOrdStmt->execute([$selectedCountryId]);
            $recentOrders = $recOrdStmt->fetchAll();

            require __DIR__ . '/app/views/admin/dashboard.php';
        } elseif ($route === 'admin/products') {
            $prodSql = "SELECT p.*, c.name as category_name, pcp.original_price, pcp.discount_price, pcp.discount_start_date, pcp.discount_end_date, pcp.stock_quantity, pcp.low_stock_threshold, pcp.is_available
                        FROM products p
                        JOIN categories c ON p.category_id = c.id
                        JOIN product_country_pricing pcp ON p.id = pcp.product_id
                        WHERE pcp.country_id = ?
                        ORDER BY p.id ASC";
            $prodStmt = $db->prepare($prodSql);
            $prodStmt->execute([$selectedCountryId]);
            $products = $prodStmt->fetchAll();

            $imgStmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC");
            foreach ($products as &$p) {
                $imgStmt->execute([$p['id']]);
                $p['gallery_images'] = $imgStmt->fetchAll();
            }

            $allCategories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, name ASC")->fetchAll();

            require __DIR__ . '/app/views/admin/products.php';
        } elseif ($route === 'admin/orders') {
            $ordSql = "SELECT * FROM orders WHERE country_id = ? ORDER BY id DESC";
            $ordStmt = $db->prepare($ordSql);
            $ordStmt->execute([$selectedCountryId]);
            $orders = $ordStmt->fetchAll();

            require __DIR__ . '/app/views/admin/orders.php';
        }
        break;

    // ---------------------------------------------------------------------
    // PRODUCT CRUD ACTIONS (COUNTRY ADMIN & SUPER ADMIN)
    // ---------------------------------------------------------------------
    case 'admin/products/create':
        if (!Auth::isCountryAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("Security token mismatch.");

            $title = trim($_POST['title'] ?? '');
            if (empty($title)) {
                flash('admin_error', 'Product Title is required.');
                redirect('admin/products');
            }

            $slug = slugify(trim($_POST['slug'] ?? '') ?: $title);
            $sku = strtoupper(trim($_POST['sku'] ?? ''));
            if (empty($sku)) {
                $sku = 'BLR-' . strtoupper(substr(md5(uniqid()), 0, 8));
            }

            // Ensure unique slug
            $chkSlug = $db->prepare("SELECT id FROM products WHERE slug = ? LIMIT 1");
            $chkSlug->execute([$slug]);
            if ($chkSlug->fetch()) {
                $slug .= '-' . rand(100, 999);
            }

            // Ensure unique SKU
            $chkSku = $db->prepare("SELECT id FROM products WHERE sku = ? LIMIT 1");
            $chkSku->execute([$sku]);
            if ($chkSku->fetch()) {
                $sku .= '-' . rand(10, 99);
            }

            $categoryId = (int)($_POST['category_id'] ?? 1);
            $leatherType = trim($_POST['leather_type'] ?? 'Full-Grain Artisan Leather');
            $hardwareFinish = trim($_POST['hardware_finish'] ?? 'Antique Brass');
            $sizes = trim($_POST['sizes'] ?? 'S, M, L, XL, XXL');
            $color = trim($_POST['color'] ?? 'Espresso Brown, Cognac Tan, Classic Black');
            if (!empty($_POST['color_names']) && is_array($_POST['color_names'])) {
                $cleanNames = array_values(array_filter(array_map('trim', $_POST['color_names'])));
                if (!empty($cleanNames)) {
                    $color = implode(', ', array_unique($cleanNames));
                }
            }
            $dimensions = trim($_POST['dimensions'] ?? '');
            $shortDesc = trim($_POST['short_description'] ?? '');
            $detailedDesc = trim($_POST['detailed_description'] ?? '');
            $careInstructions = trim($_POST['care_instructions'] ?? '');
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $isBestseller = isset($_POST['is_bestseller']) ? 1 : 0;
            $isNewArrival = isset($_POST['is_new_arrival']) ? 1 : 0;
            $status = in_array($_POST['status'] ?? '', ['published', 'draft', 'archived']) ? $_POST['status'] : 'published';

            // Upload dedicated color photos
            $uploadedColorPhotos = [];
            if (isset($_FILES['color_photos'])) {
                $uploadedColorPhotos = upload_multiple_images('color_photos', 'products');
            }

            // Upload gallery images
            $uploadedGallery = [];
            if (isset($_FILES['gallery_images'])) {
                $uploadedGallery = upload_multiple_images('gallery_images', 'products');
            }

            // Primary image upload or fallback
            $primaryImage = null;
            if (isset($_FILES['primary_image']) && $_FILES['primary_image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = upload_image_file($_FILES['primary_image'], 'products');
                if ($uploaded) {
                    $primaryImage = $uploaded;
                }
            } elseif (!empty($_POST['primary_image_url'])) {
                $primaryImage = trim($_POST['primary_image_url']);
            }

            if (!$primaryImage) {
                if (!empty($uploadedGallery)) {
                    $primaryImage = reset($uploadedGallery);
                } elseif (!empty($uploadedColorPhotos)) {
                    $primaryImage = reset($uploadedColorPhotos);
                } else {
                    $primaryImage = 'assets/images/products/briefcase-1.jpg';
                }
            }

            // Insert product with sizes & hardware_finish
            $insProd = $db->prepare("INSERT INTO products (sku, title, slug, category_id, leather_type, hardware_finish, color, sizes, dimensions, short_description, detailed_description, care_instructions, primary_image, is_featured, is_bestseller, is_new_arrival, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insProd->execute([$sku, $title, $slug, $categoryId, $leatherType, $hardwareFinish, $color, $sizes, $dimensions, $shortDesc, $detailedDesc, $careInstructions, $primaryImage, $isFeatured, $isBestseller, $isNewArrival, $status]);
            $productId = (int)$db->lastInsertId();

            // Populate product_country_pricing
            $basePrice = (float)($_POST['original_price'] ?? 150.00);
            $sellPrice = !empty($_POST['selling_price']) ? (float)$_POST['selling_price'] : (!empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null);
            $discountPrice = ($sellPrice !== null && $sellPrice < $basePrice) ? $sellPrice : null;
            $discStart = !empty($_POST['discount_start_date']) ? $_POST['discount_start_date'] : null;
            $discEnd = !empty($_POST['discount_end_date']) ? $_POST['discount_end_date'] : null;
            $stockQty = (int)($_POST['stock_quantity'] ?? 25);
            $threshold = (int)($_POST['low_stock_threshold'] ?? 5);
            $currentCountryId = (int)($_POST['country_id'] ?? 1);

            $allCountries = $db->query("SELECT id, currency_code FROM countries")->fetchAll();
            $insPcp = $db->prepare("INSERT INTO product_country_pricing (product_id, country_id, original_price, discount_price, discount_start_date, discount_end_date, stock_quantity, low_stock_threshold, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
            foreach ($allCountries as $c) {
                $cPrice = $basePrice;
                if ($c['currency_code'] === 'INR' && $basePrice < 5000) $cPrice = $basePrice * 80;
                elseif ($c['currency_code'] === 'GBP') $cPrice = round($basePrice * 0.8, 2);
                elseif ($c['currency_code'] === 'AED') $cPrice = round($basePrice * 3.67, 2);
                if ((int)$c['id'] === $currentCountryId) {
                    $cPrice = $basePrice;
                }
                $insPcp->execute([$productId, $c['id'], $cPrice, ((int)$c['id'] === $currentCountryId ? $discountPrice : null), ((int)$c['id'] === $currentCountryId ? $discStart : null), ((int)$c['id'] === $currentCountryId ? $discEnd : null), $stockQty, $threshold]);
            }

            // Gallery images & color assignments
            $insImg = $db->prepare("INSERT INTO product_images (product_id, image_url, alt_text, color_name, sort_order) VALUES (?, ?, ?, ?, ?)");
            
            // First color from comma-separated list
            $firstColor = !empty($color) ? trim(explode(',', $color)[0]) : null;
            $insImg->execute([$productId, $primaryImage, $title . ' Primary View', $firstColor, 0]);

            $order = 1;
            // Insert color-dedicated photos
            if (!empty($uploadedColorPhotos)) {
                $colorNames = $_POST['color_names'] ?? [];
                foreach ($uploadedColorPhotos as $idx => $imgPath) {
                    if ($imgPath === $primaryImage) continue;
                    $cTag = !empty($colorNames[$idx]) ? trim($colorNames[$idx]) : null;
                    $insImg->execute([$productId, $imgPath, $title . ' - ' . ($cTag ?: 'Color ' . $order), $cTag, $order]);
                    $order++;
                }
            }

            // Gallery images support
            if (!empty($uploadedGallery)) {
                $galleryColors = $_POST['gallery_colors'] ?? [];
                foreach ($uploadedGallery as $idx => $imgPath) {
                    if ($imgPath === $primaryImage) continue;
                    $colorTag = !empty($galleryColors[$idx]) ? trim($galleryColors[$idx]) : null;
                    $insImg->execute([$productId, $imgPath, $title . ' - ' . ($colorTag ?: 'Angle ' . $order), $colorTag, $order]);
                    $order++;
                }
            }

            AuditLogger::log(Auth::id(), 'create_product', 'products', $productId, ['title' => $title, 'sku' => $sku]);
            flash('admin_success', "Product '{$title}' successfully published to the catalog!");
            
            $defaultRedirect = Auth::isSuperAdmin() ? 'superadmin/products' : ('admin/products?country_id=' . $currentCountryId);
            $redirectTarget = !empty($_POST['redirect_route']) ? $_POST['redirect_route'] : $defaultRedirect;
            redirect($redirectTarget);
        }

        // GET: Dedicated Product Create Page
        $currentCountryId = isset($_GET['country_id']) ? (int)$_GET['country_id'] : (int)(Auth::getPermittedCountryIds()[0] ?? 1);
        if (!Auth::isSuperAdmin()) {
            Auth::enforceCountryAccess($currentCountryId);
        }
        $permittedIds = Auth::getPermittedCountryIds();
        $inPerm = implode(',', array_fill(0, count($permittedIds), '?'));
        $pStmt = $db->prepare("SELECT * FROM countries WHERE id IN ($inPerm) ORDER BY name ASC");
        $pStmt->execute($permittedIds);
        $permittedCountries = $pStmt->fetchAll();

        $ccStmt = $db->prepare("SELECT * FROM countries WHERE id = ?");
        $ccStmt->execute([$currentCountryId]);
        $currentCountry = $ccStmt->fetch() ?: $permittedCountries[0];

        $categories = $db->query("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC, name ASC")->fetchAll();
        require __DIR__ . '/app/views/admin/product_create.php';
        break;

    case 'admin/products/edit':
        if (!Auth::isCountryAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("Security token mismatch.");
            $productId = (int)($_POST['product_id'] ?? 0);
            $currentCountryId = (int)($_POST['country_id'] ?? 1);

            $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            if (!$product) {
                flash('admin_error', 'Product not found.');
                $defaultRedirect = Auth::isSuperAdmin() ? 'superadmin/products' : ('admin/products?country_id=' . $currentCountryId);
                redirect(!empty($_POST['redirect_route']) ? $_POST['redirect_route'] : $defaultRedirect);
            }

            $title = trim($_POST['title'] ?? $product['title']);
            $slug = slugify(trim($_POST['slug'] ?? '') ?: $title);
            $sku = strtoupper(trim($_POST['sku'] ?? $product['sku']));
            $categoryId = (int)($_POST['category_id'] ?? $product['category_id']);
            $leatherType = trim($_POST['leather_type'] ?? ($product['leather_type'] ?? 'Full-Grain Artisan Leather'));
            $hardwareFinish = trim($_POST['hardware_finish'] ?? ($product['hardware_finish'] ?? 'Antique Brass'));
            $sizes = trim($_POST['sizes'] ?? ($product['sizes'] ?? 'S, M, L, XL, XXL'));
            $color = trim($_POST['color'] ?? $product['color']);
            $allActiveColors = [];
            $deletedIds = $_POST['delete_image_ids'] ?? [];

            if (!empty($_POST['existing_color_name']) && is_array($_POST['existing_color_name'])) {
                foreach ($_POST['existing_color_name'] as $imgId => $cName) {
                    if (!in_array((string)$imgId, array_map('strval', $deletedIds)) && trim($cName)) {
                        $allActiveColors[] = trim($cName);
                    }
                }
            }
            if (!empty($_POST['new_color_names']) && is_array($_POST['new_color_names'])) {
                foreach ($_POST['new_color_names'] as $cName) {
                    if (trim($cName)) {
                        $allActiveColors[] = trim($cName);
                    }
                }
            }
            if (!empty($allActiveColors)) {
                $color = implode(', ', array_unique($allActiveColors));
            }
            $dimensions = trim($_POST['dimensions'] ?? $product['dimensions']);
            $shortDesc = trim($_POST['short_description'] ?? $product['short_description']);
            $detailedDesc = trim($_POST['detailed_description'] ?? $product['detailed_description']);
            $careInstructions = trim($_POST['care_instructions'] ?? $product['care_instructions']);
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $isBestseller = isset($_POST['is_bestseller']) ? 1 : 0;
            $isNewArrival = isset($_POST['is_new_arrival']) ? 1 : 0;
            $status = in_array($_POST['status'] ?? '', ['published', 'draft', 'archived']) ? $_POST['status'] : $product['status'];

            $primaryImage = $product['primary_image'];
            if (isset($_FILES['primary_image']) && $_FILES['primary_image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = upload_image_file($_FILES['primary_image'], 'products');
                if ($uploaded) {
                    $primaryImage = $uploaded;
                }
            }

            // Update master product
            $upProd = $db->prepare("UPDATE products SET title = ?, slug = ?, sku = ?, category_id = ?, leather_type = ?, hardware_finish = ?, color = ?, sizes = ?, dimensions = ?, short_description = ?, detailed_description = ?, care_instructions = ?, primary_image = ?, is_featured = ?, is_bestseller = ?, is_new_arrival = ?, status = ? WHERE id = ?");
            $upProd->execute([$title, $slug, $sku, $categoryId, $leatherType, $hardwareFinish, $color, $sizes, $dimensions, $shortDesc, $detailedDesc, $careInstructions, $primaryImage, $isFeatured, $isBestseller, $isNewArrival, $status, $productId]);

            // Update country pricing & stock for current country
            if (isset($_POST['original_price'])) {
                $origPrice = (float)$_POST['original_price'];
                $sellPrice = !empty($_POST['selling_price']) ? (float)$_POST['selling_price'] : (!empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null);
                $discPrice = ($sellPrice !== null && $sellPrice < $origPrice) ? $sellPrice : null;
                $discStart = !empty($_POST['discount_start_date']) ? $_POST['discount_start_date'] : null;
                $discEnd = !empty($_POST['discount_end_date']) ? $_POST['discount_end_date'] : null;
                $stockQty = (int)($_POST['stock_quantity'] ?? 0);
                $threshold = (int)($_POST['low_stock_threshold'] ?? 5);
                $isAvailable = isset($_POST['is_available']) ? 1 : 1;

                $upPcp = $db->prepare("INSERT INTO product_country_pricing (product_id, country_id, original_price, discount_price, discount_start_date, discount_end_date, stock_quantity, low_stock_threshold, is_available) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) 
                                       ON DUPLICATE KEY UPDATE original_price = VALUES(original_price), discount_price = VALUES(discount_price), discount_start_date = VALUES(discount_start_date), discount_end_date = VALUES(discount_end_date), stock_quantity = VALUES(stock_quantity), low_stock_threshold = VALUES(low_stock_threshold), is_available = VALUES(is_available)");
                $upPcp->execute([$productId, $currentCountryId, $origPrice, $discPrice, $discStart, $discEnd, $stockQty, $threshold, $isAvailable]);
            }

            // 1. Delete removed photos
            if (!empty($deletedIds) && is_array($deletedIds)) {
                $delStmt = $db->prepare("DELETE FROM product_images WHERE id = ? AND product_id = ?");
                foreach ($deletedIds as $dId) {
                    $delStmt->execute([(int)$dId, $productId]);
                }
            }

            // 2. Update color names on existing images
            if (!empty($_POST['existing_color_name']) && is_array($_POST['existing_color_name'])) {
                $upImgColor = $db->prepare("UPDATE product_images SET color_name = ? WHERE id = ? AND product_id = ?");
                foreach ($_POST['existing_color_name'] as $imgId => $cName) {
                    if (!in_array((string)$imgId, array_map('strval', $deletedIds))) {
                        $cVal = trim($cName) ?: null;
                        $upImgColor->execute([$cVal, (int)$imgId, $productId]);
                    }
                }
            }
            if (!empty($_POST['existing_image_color']) && is_array($_POST['existing_image_color'])) {
                $upImgColor = $db->prepare("UPDATE product_images SET color_name = ? WHERE id = ? AND product_id = ?");
                foreach ($_POST['existing_image_color'] as $imgId => $cName) {
                    $cVal = trim($cName) ?: null;
                    $upImgColor->execute([$cVal, (int)$imgId, $productId]);
                }
            }

            // 3. Replace existing photos if new file was selected
            if (isset($_FILES['replace_color_photo']) && is_array($_FILES['replace_color_photo']['name'])) {
                $upImgUrl = $db->prepare("UPDATE product_images SET image_url = ? WHERE id = ? AND product_id = ?");
                foreach ($_FILES['replace_color_photo']['name'] as $imgId => $rName) {
                    if (!empty($rName) && $_FILES['replace_color_photo']['error'][$imgId] === UPLOAD_ERR_OK) {
                        $singleFile = [
                            'name'     => $_FILES['replace_color_photo']['name'][$imgId],
                            'type'     => $_FILES['replace_color_photo']['type'][$imgId],
                            'tmp_name' => $_FILES['replace_color_photo']['tmp_name'][$imgId],
                            'error'    => $_FILES['replace_color_photo']['error'][$imgId],
                            'size'     => $_FILES['replace_color_photo']['size'][$imgId],
                        ];
                        $uploadedPath = upload_image_file($singleFile, 'products');
                        if ($uploadedPath) {
                            $upImgUrl->execute([$uploadedPath, (int)$imgId, $productId]);
                        }
                    }
                }
            }

            // 4. Upload any new dedicated color photos
            if (isset($_FILES['new_color_photos'])) {
                $newUploaded = upload_multiple_images('new_color_photos', 'products');
                $newNames = $_POST['new_color_names'] ?? [];
                $insImg = $db->prepare("INSERT INTO product_images (product_id, image_url, alt_text, color_name, sort_order) VALUES (?, ?, ?, ?, ?)");
                
                $maxStmt = $db->prepare("SELECT COALESCE(MAX(sort_order), 0) FROM product_images WHERE product_id = ?");
                $maxStmt->execute([$productId]);
                $currMax = (int)$maxStmt->fetchColumn();

                foreach ($newUploaded as $idx => $imgPath) {
                    $currMax++;
                    $cTag = !empty($newNames[$idx]) ? trim($newNames[$idx]) : null;
                    $insImg->execute([$productId, $imgPath, $title . ' - ' . ($cTag ?: 'Variant'), $cTag, $currMax]);
                }
            }

            // 5. Legacy gallery photos support
            if (isset($_FILES['gallery_images'])) {
                $newUploaded = upload_multiple_images('gallery_images', 'products');
                $newGalleryColors = $_POST['gallery_colors'] ?? [];
                $insImg = $db->prepare("INSERT INTO product_images (product_id, image_url, alt_text, color_name, sort_order) VALUES (?, ?, ?, ?, ?)");
                
                $maxStmt = $db->prepare("SELECT COALESCE(MAX(sort_order), 0) FROM product_images WHERE product_id = ?");
                $maxStmt->execute([$productId]);
                $currMax = (int)$maxStmt->fetchColumn();

                foreach ($newUploaded as $idx => $imgPath) {
                    $currMax++;
                    $cTag = !empty($newGalleryColors[$idx]) ? trim($newGalleryColors[$idx]) : null;
                    $insImg->execute([$productId, $imgPath, $title . ' - ' . ($cTag ?: 'Gallery'), $cTag, $currMax]);
                }
            }

            AuditLogger::log(Auth::id(), 'update_product', 'products', $productId, ['title' => $title]);
            flash('admin_success', "Product '{$title}' updated successfully!");

            $defaultRedirect = Auth::isSuperAdmin() ? 'superadmin/products' : ('admin/products?country_id=' . $currentCountryId);
            $redirectTarget = !empty($_POST['redirect_route']) ? $_POST['redirect_route'] : $defaultRedirect;
            redirect($redirectTarget);
        }

        // GET: Dedicated Product Edit Page
        $productId = (int)($_GET['id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        if (!$product) {
            flash('admin_error', 'Product not found.');
            redirect('admin/products');
        }

        $currentCountryId = isset($_GET['country_id']) ? (int)$_GET['country_id'] : (int)(Auth::getPermittedCountryIds()[0] ?? 1);
        if (!Auth::isSuperAdmin()) {
            Auth::enforceCountryAccess($currentCountryId);
        }
        $permittedIds = Auth::getPermittedCountryIds();
        $inPerm = implode(',', array_fill(0, count($permittedIds), '?'));
        $pStmt = $db->prepare("SELECT * FROM countries WHERE id IN ($inPerm) ORDER BY name ASC");
        $pStmt->execute($permittedIds);
        $permittedCountries = $pStmt->fetchAll();

        $ccStmt = $db->prepare("SELECT * FROM countries WHERE id = ?");
        $ccStmt->execute([$currentCountryId]);
        $currentCountry = $ccStmt->fetch() ?: $permittedCountries[0];

        // Fetch pricing for this country
        $pcpStmt = $db->prepare("SELECT * FROM product_country_pricing WHERE product_id = ? AND country_id = ?");
        $pcpStmt->execute([$productId, $currentCountryId]);
        $pricing = $pcpStmt->fetch() ?: [
            'original_price' => 0,
            'discount_price' => null,
            'discount_start_date' => null,
            'discount_end_date' => null,
            'stock_quantity' => 0,
            'low_stock_threshold' => 5,
            'is_available' => 1
        ];

        // Fetch gallery images
        $imgStmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC");
        $imgStmt->execute([$productId]);
        $galleryImages = $imgStmt->fetchAll();

        $categories = $db->query("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC, name ASC")->fetchAll();
        require __DIR__ . '/app/views/admin/product_edit.php';
        break;

    case 'admin/products/delete':
        if (!Auth::isCountryAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("Security token mismatch.");
            $productId = (int)($_POST['product_id'] ?? 0);
            $currentCountryId = (int)($_POST['country_id'] ?? 1);

            $stmt = $db->prepare("SELECT title FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $title = $stmt->fetchColumn() ?: 'Product';

            $db->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$productId]);
            $db->prepare("DELETE FROM product_country_pricing WHERE product_id = ?")->execute([$productId]);
            $db->prepare("DELETE FROM cart_items WHERE product_id = ?")->execute([$productId]);
            $db->prepare("DELETE FROM wishlist_items WHERE product_id = ?")->execute([$productId]);
            $db->prepare("DELETE FROM products WHERE id = ?")->execute([$productId]);

            AuditLogger::log(Auth::id(), 'delete_product', 'products', $productId, ['title' => $title]);
            flash('admin_success', "Product '{$title}' permanently removed from catalog.");

            $defaultRedirect = Auth::isSuperAdmin() ? 'superadmin/products' : ('admin/products?country_id=' . $currentCountryId);
            $redirectTarget = !empty($_POST['redirect_route']) ? $_POST['redirect_route'] : $defaultRedirect;
            redirect($redirectTarget);
        }
        break;

    case 'admin/products/delete-image':
        if (!Auth::isCountryAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF error");
            $imageId = (int)($_POST['image_id'] ?? 0);
            $productId = (int)($_POST['product_id'] ?? 0);
            $currentCountryId = (int)($_POST['country_id'] ?? 1);

            $db->prepare("DELETE FROM product_images WHERE id = ?")->execute([$imageId]);
            AuditLogger::log(Auth::id(), 'delete_product_image', 'product_images', $imageId, ['product_id' => $productId]);
            flash('admin_success', 'Gallery photo removed.');

            $defaultRedirect = Auth::isSuperAdmin() ? 'superadmin/products' : ('admin/products?country_id=' . $currentCountryId);
            $redirectTarget = !empty($_POST['redirect_route']) ? $_POST['redirect_route'] : $defaultRedirect;
            redirect($redirectTarget);
        }
        break;

    case 'admin/products/update-pricing':
        if (!Auth::isCountryAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF error");
            $countryId = (int)($_POST['country_id'] ?? 0);
            $productId = (int)($_POST['product_id'] ?? 0);

            Auth::enforceCountryAccess($countryId);

            $originalPrice = (float)($_POST['original_price'] ?? 0);
            $discountPrice = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
            $startDate = !empty($_POST['discount_start_date']) ? $_POST['discount_start_date'] : null;
            $endDate = !empty($_POST['discount_end_date']) ? $_POST['discount_end_date'] : null;
            $stock = (int)($_POST['stock_quantity'] ?? 0);
            $threshold = (int)($_POST['low_stock_threshold'] ?? 5);
            $isAvailable = isset($_POST['is_available']) ? 1 : 0;

            $stmt = $db->prepare("UPDATE product_country_pricing 
                                  SET original_price = ?, discount_price = ?, discount_start_date = ?, discount_end_date = ?, stock_quantity = ?, low_stock_threshold = ?, is_available = ? 
                                  WHERE product_id = ? AND country_id = ?");
            $stmt->execute([$originalPrice, $discountPrice, $startDate, $endDate, $stock, $threshold, $isAvailable, $productId, $countryId]);

            AuditLogger::log(Auth::id(), 'update_pricing', 'product_country_pricing', $productId, ['country_id' => $countryId, 'price' => $originalPrice, 'discount' => $discountPrice]);
            flash('admin_success', 'Regional pricing and stock levels successfully updated.');
            redirect('admin/products?country_id=' . $countryId);
        }
        break;

    // ---------------------------------------------------------------------
    // CATEGORY CRUD ACTIONS (COUNTRY ADMIN & SUPER ADMIN)
    // ---------------------------------------------------------------------
    case 'superadmin/categories':
    case 'admin/categories':
        if (Auth::isSuperAdmin() && $route === 'admin/categories') {
            redirect('superadmin/categories');
        }
        if (!Auth::isCountryAdmin()) redirect('admin');
        $catSql = "SELECT c.*, COUNT(p.id) as product_count 
                   FROM categories c 
                   LEFT JOIN products p ON c.id = p.category_id 
                   GROUP BY c.id 
                   ORDER BY c.sort_order ASC, c.name ASC";
        $categories = $db->query($catSql)->fetchAll();
        require __DIR__ . '/app/views/admin/categories.php';
        break;

    case 'superadmin/categories/create':
    case 'admin/categories/create':
        if (!Auth::isCountryAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF validation error.");
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                flash('admin_error', 'Category Name is required.');
                redirect(Auth::isSuperAdmin() ? 'superadmin/categories' : 'admin/categories');
            }
            $slug = slugify(trim($_POST['slug'] ?? '') ?: $name);
            $description = trim($_POST['description'] ?? '');
            $sortOrder = (int)($_POST['sort_order'] ?? 0);
            $status = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';

            $check = $db->prepare("SELECT id FROM categories WHERE slug = ? LIMIT 1");
            $check->execute([$slug]);
            if ($check->fetch()) {
                $slug .= '-' . time();
            }

            $imageUrl = null;
            if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
                $imageUrl = upload_image_file($_FILES['category_image'], 'categories');
            }

            $stmt = $db->prepare("INSERT INTO categories (name, slug, description, image_url, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $imageUrl, $sortOrder, $status]);
            $newCatId = (int)$db->lastInsertId();

            AuditLogger::log(Auth::id(), 'create_category', 'categories', $newCatId, ['name' => $name, 'slug' => $slug]);
            flash('admin_success', "Category '{$name}' successfully created.");
            redirect(Auth::isSuperAdmin() ? 'superadmin/categories' : 'admin/categories');
        }
        break;

    case 'superadmin/categories/edit':
    case 'admin/categories/edit':
        if (!Auth::isCountryAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF validation error.");
            $catId = (int)($_POST['category_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $slug = slugify(trim($_POST['slug'] ?? '') ?: $name);
            $description = trim($_POST['description'] ?? '');
            $sortOrder = (int)($_POST['sort_order'] ?? 0);
            $status = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';

            $exStmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
            $exStmt->execute([$catId]);
            $existing = $exStmt->fetch();
            if (!$existing) {
                flash('admin_error', 'Category not found.');
                redirect(Auth::isSuperAdmin() ? 'superadmin/categories' : 'admin/categories');
            }

            $check = $db->prepare("SELECT id FROM categories WHERE slug = ? AND id != ? LIMIT 1");
            $check->execute([$slug, $catId]);
            if ($check->fetch()) {
                $slug .= '-' . time();
            }

            $imageUrl = $existing['image_url'];
            if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = upload_image_file($_FILES['category_image'], 'categories');
                if ($uploaded) {
                    $imageUrl = $uploaded;
                }
            }

            $stmt = $db->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, image_url = ?, sort_order = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $imageUrl, $sortOrder, $status, $catId]);

            AuditLogger::log(Auth::id(), 'update_category', 'categories', $catId, ['name' => $name, 'slug' => $slug]);
            flash('admin_success', "Category '{$name}' successfully updated.");
            redirect(Auth::isSuperAdmin() ? 'superadmin/categories' : 'admin/categories');
        }
        break;

    case 'superadmin/categories/delete':
    case 'admin/categories/delete':
        if (!Auth::isCountryAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF validation error.");
            $catId = (int)($_POST['category_id'] ?? 0);

            $chkStmt = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $chkStmt->execute([$catId]);
            $pCount = (int)$chkStmt->fetchColumn();

            if ($pCount > 0) {
                flash('admin_error', "Cannot delete category: {$pCount} products are currently assigned to this category. Please reassign those products first.");
                redirect(Auth::isSuperAdmin() ? 'superadmin/categories' : 'admin/categories');
            }

            $db->prepare("DELETE FROM categories WHERE id = ?")->execute([$catId]);
            AuditLogger::log(Auth::id(), 'delete_category', 'categories', $catId);
            flash('admin_success', "Category permanently deleted.");
            redirect(Auth::isSuperAdmin() ? 'superadmin/categories' : 'admin/categories');
        }
        break;

    case 'superadmin/orders/update-status':
    case 'admin/orders/update-status':
        if (!Auth::isCountryAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF error");
            $orderId = (int)($_POST['order_id'] ?? 0);
            $countryId = (int)($_POST['country_id'] ?? 0);

            // Strict country scoping check (only for regional admins)
            if (!Auth::isSuperAdmin()) {
                Auth::enforceCountryAccess($countryId);
            }

            $orderStatus = $_POST['order_status'] ?? 'placed';
            $paymentStatus = $_POST['payment_status'] ?? 'pending';
            $carrier = trim($_POST['shipping_carrier'] ?? '');
            $trackingNum = trim($_POST['tracking_number'] ?? '');
            $trackingUrl = trim($_POST['tracking_url'] ?? '');

            $stmt = $db->prepare("UPDATE orders SET order_status = ?, payment_status = ?, shipping_carrier = ?, tracking_number = ?, tracking_url = ? WHERE id = ? AND country_id = ?");
            $stmt->execute([$orderStatus, $paymentStatus, $carrier, $trackingNum, $trackingUrl, $orderId, $countryId]);

            AuditLogger::log(Auth::id(), 'update_order_status', 'orders', $orderId, ['status' => $orderStatus, 'tracking' => $trackingNum]);
            flash('admin_success', "Order #$orderId fulfillment status successfully updated.");
            redirect(Auth::isSuperAdmin() ? 'superadmin/orders' : ('admin/orders?country_id=' . $countryId));
        }
        break;

    case 'superadmin/coupons':
    case 'admin/coupons':
        if (Auth::isSuperAdmin() && $route === 'admin/coupons') {
            redirect('superadmin/coupons');
        }
        if (!Auth::isSuperAdmin() && $route === 'superadmin/coupons') {
            redirect('admin/coupons');
        }

        Auth::requireCountryAdmin();
        $permittedIds = Auth::getPermittedCountryIds();
        $allCountries = $db->query("SELECT * FROM countries ORDER BY name ASC")->fetchAll();
        $selectedCountryId = isset($_GET['country_id']) && !empty($_GET['country_id']) ? (int)$_GET['country_id'] : null;
        
        if ($selectedCountryId) {
            Auth::enforceCountryAccess($selectedCountryId);
        }

        $filterCountryName = '';
        if ($selectedCountryId) {
            foreach ($allCountries as $ac) {
                if ($ac['id'] == $selectedCountryId) {
                    $filterCountryName = $ac['name'];
                    break;
                }
            }
        }

        if (Auth::isSuperAdmin()) {
            $permittedCountries = $allCountries;
        } else {
            $inPerm = implode(',', array_fill(0, count($permittedIds), '?'));
            $pStmt = $db->prepare("SELECT * FROM countries WHERE id IN ($inPerm) ORDER BY name ASC");
            $pStmt->execute($permittedIds);
            $permittedCountries = $pStmt->fetchAll();
        }

        $sql = "SELECT cp.*, c.name as country_name, c.code as country_code, c.currency_symbol 
                FROM coupons cp 
                LEFT JOIN countries c ON cp.country_id = c.id 
                WHERE 1=1";
        $params = [];

        if (!Auth::isSuperAdmin()) {
            if (!empty($permittedIds)) {
                $inPerm = implode(',', array_fill(0, count($permittedIds), '?'));
                $sql .= " AND (cp.country_id IS NULL OR cp.country_id IN ($inPerm))";
                $params = array_merge($params, $permittedIds);
            } else {
                $sql .= " AND cp.country_id IS NULL";
            }
        }

        if ($selectedCountryId) {
            $sql .= " AND cp.country_id = ?";
            $params[] = $selectedCountryId;
        }

        $sql .= " ORDER BY cp.id DESC";
        $cpnStmt = $db->prepare($sql);
        $cpnStmt->execute($params);
        $coupons = $cpnStmt->fetchAll();

        require __DIR__ . '/app/views/admin/coupons.php';
        break;

    case 'superadmin/coupons/create':
    case 'admin/coupons/create':
        Auth::requireCountryAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                AuditLogger::log(Auth::id(), 'csrf_violation', 'coupons');
                die("CSRF Security Validation Failed.");
            }

            $code = strtoupper(trim($_POST['code'] ?? ''));
            $countryId = !empty($_POST['country_id']) ? (int)$_POST['country_id'] : null;
            $discType = $_POST['discount_type'] ?? 'percentage';
            $discVal = (float)($_POST['discount_value'] ?? 0);
            $minSpend = (float)($_POST['min_spend'] ?? 0);
            $usageLimit = (int)($_POST['usage_limit'] ?? 0);
            $validFrom = !empty($_POST['valid_from']) ? $_POST['valid_from'] : date('Y-m-d');
            $validUntil = !empty($_POST['valid_until']) ? $_POST['valid_until'] : null;

            if ($countryId) {
                Auth::enforceCountryAccess($countryId);
            } elseif (!Auth::isSuperAdmin()) {
                AuditLogger::log(Auth::id(), 'security_violation', 'coupons', null, ['message' => 'Country admin attempted to create global coupon']);
                die("Only Super Admin can create Global coupons.");
            }

            $targetRoute = Auth::isSuperAdmin() ? 'superadmin/coupons' : 'admin/coupons';

            if (empty($code) || $discVal <= 0) {
                flash('admin_error', 'Invalid coupon code or discount value.');
                redirect($targetRoute);
            }

            $chkStmt = $db->prepare("SELECT id FROM coupons WHERE code = ? AND (country_id = ? OR (country_id IS NULL AND ? IS NULL)) LIMIT 1");
            $chkStmt->execute([$code, $countryId, $countryId]);
            if ($chkStmt->fetch()) {
                flash('admin_error', "Coupon voucher code '$code' already exists for this market.");
                redirect($targetRoute);
            }

            $insStmt = $db->prepare("INSERT INTO coupons (code, country_id, discount_type, discount_value, min_spend, valid_from, valid_until, usage_limit, times_used, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 'active')");
            $insStmt->execute([$code, $countryId, $discType, $discVal, $minSpend, $validFrom, $validUntil, $usageLimit]);
            $newCouponId = (int)$db->lastInsertId();

            AuditLogger::log(Auth::id(), 'create_coupon', 'coupons', $newCouponId, ['code' => $code, 'country_id' => $countryId, 'value' => $discVal]);
            flash('admin_success', "Coupon voucher '$code' created successfully.");
            
            $redirectUrl = Auth::isSuperAdmin() ? 'superadmin/coupons' : ('admin/coupons' . ($countryId ? '?country_id=' . $countryId : ''));
            redirect($redirectUrl);
        }
        break;

    case 'superadmin/coupons/toggle':
    case 'admin/coupons/toggle':
        Auth::requireCountryAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF error");
            $cpnId = (int)($_POST['coupon_id'] ?? 0);

            $chkStmt = $db->prepare("SELECT * FROM coupons WHERE id = ?");
            $chkStmt->execute([$cpnId]);
            $coupon = $chkStmt->fetch();

            if ($coupon) {
                if ($coupon['country_id']) {
                    Auth::enforceCountryAccess((int)$coupon['country_id']);
                } elseif (!Auth::isSuperAdmin()) {
                    die("Access Denied to Global Coupon.");
                }

                $newStatus = ($coupon['status'] === 'active') ? 'inactive' : 'active';
                $db->prepare("UPDATE coupons SET status = ? WHERE id = ?")->execute([$newStatus, $cpnId]);
                AuditLogger::log(Auth::id(), 'toggle_coupon_status', 'coupons', $cpnId, ['status' => $newStatus]);
                flash('admin_success', "Coupon voucher status updated to '$newStatus'.");
            }
            $redirectUrl = Auth::isSuperAdmin() ? 'superadmin/coupons' : ('admin/coupons' . (!empty($coupon['country_id']) ? '?country_id=' . $coupon['country_id'] : ''));
            redirect($redirectUrl);
        }
        break;

    case 'superadmin/coupons/delete':
    case 'admin/coupons/delete':
        Auth::requireCountryAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF error");
            $cpnId = (int)($_POST['coupon_id'] ?? 0);

            $chkStmt = $db->prepare("SELECT * FROM coupons WHERE id = ?");
            $chkStmt->execute([$cpnId]);
            $coupon = $chkStmt->fetch();

            if ($coupon) {
                if ($coupon['country_id']) {
                    Auth::enforceCountryAccess((int)$coupon['country_id']);
                } elseif (!Auth::isSuperAdmin()) {
                    die("Access Denied to Global Coupon.");
                }

                $db->prepare("DELETE FROM coupons WHERE id = ?")->execute([$cpnId]);
                AuditLogger::log(Auth::id(), 'delete_coupon', 'coupons', $cpnId, ['code' => $coupon['code']]);
                flash('admin_success', "Coupon voucher '{$coupon['code']}' permanently deleted.");
            }
            $redirectUrl = Auth::isSuperAdmin() ? 'superadmin/coupons' : ('admin/coupons' . (!empty($coupon['country_id']) ? '?country_id=' . $coupon['country_id'] : ''));
            redirect($redirectUrl);
        }
        break;

    case 'superadmin/reviews':
    case 'admin/reviews':
        if (Auth::isSuperAdmin() && $route === 'admin/reviews') {
            redirect('superadmin/reviews');
        }
        if (!Auth::isSuperAdmin() && $route === 'superadmin/reviews') {
            redirect('admin/reviews');
        }

        Auth::requireCountryAdmin();
        $permittedIds = Auth::getPermittedCountryIds();
        $allCountries = $db->query("SELECT * FROM countries ORDER BY name ASC")->fetchAll();
        $selectedCountryId = isset($_GET['country_id']) && !empty($_GET['country_id']) ? (int)$_GET['country_id'] : null;
        $statusFilter = $_GET['status'] ?? 'all';

        if ($selectedCountryId) {
            Auth::enforceCountryAccess($selectedCountryId);
        }

        $filterCountryName = '';
        if ($selectedCountryId) {
            foreach ($allCountries as $ac) {
                if ($ac['id'] == $selectedCountryId) {
                    $filterCountryName = $ac['name'];
                    break;
                }
            }
        }

        if (Auth::isSuperAdmin()) {
            $permittedCountries = $allCountries;
        } else {
            $inPerm = implode(',', array_fill(0, count($permittedIds), '?'));
            $pStmt = $db->prepare("SELECT * FROM countries WHERE id IN ($inPerm) ORDER BY name ASC");
            $pStmt->execute($permittedIds);
            $permittedCountries = $pStmt->fetchAll();
        }

        $countSql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                        AVG(rating) as avg_rating
                     FROM product_reviews WHERE 1=1";
        $countParams = [];
        if (!Auth::isSuperAdmin() && !empty($permittedIds)) {
            $inPerm = implode(',', array_fill(0, count($permittedIds), '?'));
            $countSql .= " AND country_id IN ($inPerm)";
            $countParams = $permittedIds;
        }
        $cStmt = $db->prepare($countSql);
        $cStmt->execute($countParams);
        $statusCounts = $cStmt->fetch();

        $sql = "SELECT pr.*, p.title as product_title, p.sku, p.primary_image as product_image,
                       u.full_name as user_name, u.email as user_email,
                       c.name as country_name, c.code as country_code
                FROM product_reviews pr
                JOIN products p ON pr.product_id = p.id
                JOIN users u ON pr.user_id = u.id
                JOIN countries c ON pr.country_id = c.id
                WHERE 1=1";
        $params = [];

        if (!Auth::isSuperAdmin() && !empty($permittedIds)) {
            $inPerm = implode(',', array_fill(0, count($permittedIds), '?'));
            $sql .= " AND pr.country_id IN ($inPerm)";
            $params = array_merge($params, $permittedIds);
        }

        if ($selectedCountryId) {
            $sql .= " AND pr.country_id = ?";
            $params[] = $selectedCountryId;
        }

        if ($statusFilter !== 'all' && in_array($statusFilter, ['pending', 'approved', 'rejected'])) {
            $sql .= " AND pr.status = ?";
            $params[] = $statusFilter;
        }

        $sql .= " ORDER BY pr.id DESC";
        $rStmt = $db->prepare($sql);
        $rStmt->execute($params);
        $reviews = $rStmt->fetchAll();

        require __DIR__ . '/app/views/admin/reviews.php';
        break;

    case 'superadmin/reviews/update-status':
    case 'admin/reviews/update-status':
        Auth::requireCountryAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF error");
            $revId = (int)($_POST['review_id'] ?? 0);
            $newStatus = $_POST['status'] ?? '';

            if (!in_array($newStatus, ['pending', 'approved', 'rejected'])) {
                die("Invalid review status.");
            }

            $chkStmt = $db->prepare("SELECT * FROM product_reviews WHERE id = ?");
            $chkStmt->execute([$revId]);
            $review = $chkStmt->fetch();

            if ($review) {
                if (!Auth::isSuperAdmin()) {
                    Auth::enforceCountryAccess((int)$review['country_id']);
                }
                $db->prepare("UPDATE product_reviews SET status = ? WHERE id = ?")->execute([$newStatus, $revId]);
                AuditLogger::log(Auth::id(), 'moderate_review', 'product_reviews', $revId, ['status' => $newStatus]);
                flash('admin_success', "Testimonial review status updated to '$newStatus'.");
            }
            redirect(Auth::isSuperAdmin() ? 'superadmin/reviews' : 'admin/reviews');
        }
        break;

    case 'superadmin/reviews/delete':
    case 'admin/reviews/delete':
        Auth::requireCountryAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) die("CSRF error");
            $revId = (int)($_POST['review_id'] ?? 0);

            $chkStmt = $db->prepare("SELECT * FROM product_reviews WHERE id = ?");
            $chkStmt->execute([$revId]);
            $review = $chkStmt->fetch();

            if ($review) {
                if (!Auth::isSuperAdmin()) {
                    Auth::enforceCountryAccess((int)$review['country_id']);
                }
                $db->prepare("DELETE FROM product_reviews WHERE id = ?")->execute([$revId]);
                AuditLogger::log(Auth::id(), 'delete_review', 'product_reviews', $revId);
                flash('admin_success', "Customer testimonial permanently removed.");
            }
            redirect(Auth::isSuperAdmin() ? 'superadmin/reviews' : 'admin/reviews');
        }
        break;


    // ---------------------------------------------------------------------
    // 8. STATIC BRAND & POLICY PAGES
    // ---------------------------------------------------------------------
    case 'about':
    case 'heritage':
        require __DIR__ . '/app/views/storefront/about.php';
        break;

    case 'contact':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                die("Security Verification Notice: Invalid or expired CSRF token.");
            }

            $countryCode = strtoupper(trim($_POST['country_code'] ?? 'IN'));
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $inquiryType = trim($_POST['inquiry_type'] ?? 'General Inquiries');
            $preferredChannel = trim($_POST['preferred_channel'] ?? 'Email');
            $subject = trim($_POST['subject'] ?? 'Atelier Contact Inquiry');
            $message = trim($_POST['message'] ?? '');
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

            if (empty($fullName) || empty($email) || empty($message)) {
                flash('contact_error', 'Please provide your name, valid email address, and message particulars.');
                redirect('contact?country=' . urlencode($countryCode));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('contact_error', 'Please provide a valid executive email address.');
                redirect('contact?country=' . urlencode($countryCode));
            }

            // Append preferred contact channel into message metadata
            $fullMessage = $message . "\n\n[Client Channel Preference: " . $preferredChannel . " | Direct Phone: " . $phone . "]";

            $insStmt = $db->prepare("INSERT INTO contact_inquiries (country_code, full_name, email, phone, inquiry_type, subject, message, status, ip_address) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, 'new', ?)");
            $insStmt->execute([$countryCode, $fullName, $email, $phone, $inquiryType, $subject, $fullMessage, $ipAddress]);
            $inquiryId = (int)$db->lastInsertId();

            // Log event in audit logs
            AuditLogger::log(Auth::id() ?? 0, 'contact_inquiry_submitted', 'contact_inquiries', $inquiryId, [
                'country_code' => $countryCode,
                'inquiry_type' => $inquiryType,
                'email' => $email
            ]);

            $countryNames = ['IN' => 'Mumbai (India)', 'US' => 'New York (United States)', 'GB' => 'London (United Kingdom)', 'AE' => 'Dubai (United Arab Emirates)'];
            $destName = $countryNames[$countryCode] ?? $countryCode;

            flash('contact_success', "Thank you, " . e($fullName) . ". Your message (#REF-" . str_pad($inquiryId, 4, '0', STR_PAD_LEFT) . ") has been received by our " . $destName . " Support Team. A customer care specialist will respond within 4 business hours.");
            redirect('contact?country=' . urlencode($countryCode) . '#inquiryConfirmed');
        }

        require __DIR__ . '/app/views/storefront/contact.php';
        break;

    // ---------------------------------------------------------------------
    // 9. 404 NOT FOUND
    // ---------------------------------------------------------------------
    default:
        http_response_code(404);
        require __DIR__ . '/app/views/errors/404.php';
        break;
}
