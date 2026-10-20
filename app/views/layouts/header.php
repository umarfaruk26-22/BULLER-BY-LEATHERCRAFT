<?php
use App\Core\Auth;
use App\Core\Cart;
use App\Core\Wishlist;
use App\Core\MarketContext;

$activeCountry = MarketContext::getCountry();
$cartCount = Cart::getCount();
$wishlistCount = Wishlist::getCount();
$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' . APP_NAME : APP_NAME . ' | Generational Leather Craftsmanship & Heritage Luxury' ?></title>
    <meta name="description" content="<?= isset($pageDescription) ? e($pageDescription) : 'Buller Leather offers heirloom-grade full-grain leather jackets, executive briefcases, travel duffels, and bespoke accessories with worldwide international delivery.' ?>">
    
    <!-- Favicon / Brand Medallion (High-Contrast for Browser Tabs) -->
    <?php
    $favDiskPath = dirname(__DIR__, 3) . '/assets/images/buller-favicon.svg';
    $favVer = file_exists($favDiskPath) ? filemtime($favDiskPath) : '2.0.0';
    ?>
    <link rel="icon" type="image/svg+xml" href="<?= asset('assets/images/buller-favicon.svg') ?>?v=<?= $favVer ?>">
    <link rel="alternate icon" type="image/svg+xml" href="<?= asset('assets/images/buller-favicon.svg') ?>?v=<?= $favVer ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <?php
    $cssDiskPath = dirname(__DIR__, 3) . '/assets/css/luxury-theme.css';
    $cssVersion = time();
    ?>
    <!-- Custom Luxury Design System -->
    <link rel="stylesheet" href="<?= asset('assets/css/luxury-theme.css') ?>?v=<?= $cssVersion ?>">
    
    <script>
        window.BULLER_CONFIG = {
            baseUrl: '<?= rtrim(BASE_URL, '/') ?>',
            activeCountry: <?= json_encode($activeCountry) ?>,
            csrfToken: '<?= Auth::csrfToken() ?>'
        };
    </script>
</head>
<body>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg buller-navbar sticky-top">
        <div class="container-fluid px-xl-5 px-lg-4 px-3 d-flex align-items-center justify-content-between">
            <!-- Mobile Left Hamburger Toggler (Visible ONLY on mobile/tablet d-lg-none) -->
            <button class="navbar-toggler border-0 p-1 me-2 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#bullerNavDrawer" aria-controls="bullerNavDrawer" aria-label="Open Navigation Panel">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>

            <!-- Brand Logo -->
            <a class="navbar-brand navbar-brand-logo me-auto me-lg-4" href="<?= url('') ?>" title="Buller Leather - Return to Storefront">
                <img src="<?= asset('assets/images/buller-logo.svg') ?>" alt="Buller Leather Logo" class="brand-logo-icon">
                <div class="brand-text-group">
                    <span class="brand-main">BULLER</span>
                    <span class="brand-sub">By leathercraft</span>
                </div>
            </a>

            <!-- Action Icons (Right side of navbar on both mobile and desktop) -->
            <div class="d-flex align-items-center gap-1 gap-xl-2 nav-actions-wrapper ms-auto ms-lg-0 order-2 order-lg-3">
                <!-- Market & Regional Selector Pill (Desktop View >= 992px) -->
                <button type="button" class="btn buller-market-pill-btn d-none d-lg-inline-flex align-items-center gap-2 me-1 me-xl-2" data-bs-toggle="modal" data-bs-target="#marketSelectorModal" style="background-color: #1a1410 !important; border: 1px solid rgba(197, 168, 128, 0.45) !important; color: #ffffff !important; border-radius: 8px !important; padding: 0.38rem 0.85rem !important; font-size: 0.8rem !important; box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important; white-space: nowrap !important;" title="Select Shopping Region & Currency" aria-label="Selected Region">
                    <?= country_flag($activeCountry['code'], $activeCountry['name'], 18) ?>
                    <span class="buller-market-pill-name" style="color: #ded6cb !important; font-weight: 500 !important;"><?= e($activeCountry['name']) ?></span>
                    <span class="buller-market-pill-dot" style="color: rgba(197, 168, 128, 0.75) !important;">&bull;</span>
                    <strong class="buller-market-pill-currency" style="color: #ffffff !important; font-weight: 700 !important;"><?= e($activeCountry['currency_code']) ?> (<?= e($activeCountry['currency_symbol']) ?>)</strong>
                    <i class="bi bi-chevron-down ms-1" style="font-size: 0.65rem; color: var(--color-gold) !important;"></i>
                </button>


                <!-- 1. Search Trigger -->
                <button type="button" id="bullerSearchBtn" class="btn nav-actions-icon nav-action-search" data-bs-toggle="modal" data-bs-target="#searchModal" title="Search Curations" aria-label="Search">
                    <i class="bi bi-search"></i>
                </button>

                <!-- 2. Account Dropdown (Directly clickable on both mobile and desktop) -->
                <div class="dropdown nav-action-account-dropdown">
                    <button type="button" id="bullerAccountBtn" class="btn nav-actions-icon nav-action-account" aria-expanded="false" title="Client Account" aria-label="Account">
                        <i class="bi bi-person"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-buller shadow-lg border-0 p-2" aria-labelledby="bullerAccountBtn" style="min-width: 250px;">
                        <?php if ($currentUser): ?>
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-truncate" style="font-size: 0.92rem; color: var(--color-espresso-deep);"><?= e($currentUser['name']) ?></div>
                                <div class="text-muted small text-truncate" style="font-size: 0.78rem;"><?= e($currentUser['email']) ?></div>
                                <div class="mt-1 d-flex align-items-center gap-1">
                                    <span class="badge border" style="background: rgba(197, 168, 128, 0.15); border-color: rgba(197, 168, 128, 0.4) !important; color: var(--color-cognac); font-size: 0.68rem;">
                                        <?= country_flag($activeCountry['code'], $activeCountry['name'], 14) ?> <?= e($activeCountry['name']) ?>
                                    </span>
                                </div>
                            </li>
                            <?php if ($currentUser['role'] === 'super_admin'): ?>
                                <li><a class="dropdown-item py-2" href="<?= url('superadmin/dashboard') ?>"><i class="bi bi-speedometer2 me-2 text-warning"></i> Global Dashboard</a></li>
                                <li><a class="dropdown-item py-2" href="<?= url('superadmin/countries') ?>"><i class="bi bi-globe me-2 text-primary"></i> Manage Countries</a></li>
                                <li><a class="dropdown-item py-2" href="<?= url('superadmin/admins') ?>"><i class="bi bi-people me-2 text-success"></i> Administrator RBAC</a></li>
                            <?php elseif ($currentUser['role'] === 'country_admin'): ?>
                                <li><a class="dropdown-item py-2" href="<?= url('admin/dashboard') ?>"><i class="bi bi-speedometer2 me-2 text-warning"></i> Country Dashboard</a></li>
                                <li><a class="dropdown-item py-2" href="<?= url('admin/products') ?>"><i class="bi bi-box-seam me-2 text-primary"></i> Market Products</a></li>
                                <li><a class="dropdown-item py-2" href="<?= url('admin/orders') ?>"><i class="bi bi-receipt me-2 text-success"></i> Customer Orders</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item py-2" href="<?= url('customer/dashboard') ?>"><i class="bi bi-person-circle me-2 text-warning"></i> My Account</a></li>
                                <li><a class="dropdown-item py-2" href="<?= url('customer/orders') ?>"><i class="bi bi-box2 me-2"></i> Order History</a></li>
                                <li><a class="dropdown-item py-2" href="<?= url('customer/addresses') ?>"><i class="bi bi-geo-alt me-2"></i> Saved Addresses</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</a></li>
                        <?php else: ?>
                            <li class="px-3 py-2">
                                <div class="fw-bold mb-1 font-serif" style="color: var(--color-espresso-deep); font-size: 1.05rem;">Client Concierge</div>
                                <p class="text-muted small mb-3" style="font-size: 0.78rem; line-height: 1.4;">Sign in to access bespoke commissions, order tracking, and generational guarantees.</p>
                                <a href="<?= url('login') ?>" class="btn btn-buller-primary btn-sm w-100 mb-2">Sign In</a>
                                <div class="text-center small mb-2">
                                    New client? <a href="<?= url('register') ?>" class="text-decoration-underline fw-semibold" style="color: var(--color-cognac);">Create Account</a>
                                </div>
                                <hr class="dropdown-divider my-2">
                                <div>
                                    <a class="dropdown-item py-1 px-1 text-muted small" href="<?= url('track') ?>"><i class="bi bi-truck me-2 text-gold"></i> Track Order Delivery</a>
                                    <a class="dropdown-item py-1 px-1 text-muted small" href="<?= url('contact') ?>"><i class="bi bi-headset me-2 text-gold"></i> Atelier Concierge</a>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- 3. Wishlist Icon -->
                <a href="<?= url('wishlist') ?>" id="bullerWishlistBtn" class="nav-actions-icon nav-action-wishlist position-relative" title="Client Wishlist" aria-label="Wishlist">
                    <i class="bi bi-heart"></i>
                    <span class="badge-counter wishlist-count-badge" style="display: <?= $wishlistCount > 0 ? 'flex' : 'none' ?>;">
                        <?= $wishlistCount ?>
                    </span>
                </a>

                <!-- 4. Cart Trigger Button -->
                <button type="button" id="bullerCartBtn" class="btn nav-actions-icon nav-action-cart position-relative" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" title="Shopping Bag" aria-label="Cart">
                    <i class="bi bi-bag"></i>
                    <span class="badge-counter cart-count-badge" style="display: <?= $cartCount > 0 ? 'flex' : 'none' ?>;">
                        <?= $cartCount ?>
                    </span>
                </button>
            </div>

            <!-- Desktop Center Links (Hidden on mobile/tablet, visible on desktop >= 992px) -->
            <div class="d-none d-lg-flex justify-content-center order-2 mx-auto">
                <ul class="navbar-nav mx-auto mb-0 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link nav-link-buller <?= (!isset($_GET['route']) || $_GET['route'] === '' || $_GET['route'] === 'home') ? 'active' : '' ?>" href="<?= url('') ?>">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-link-buller <?= (($_GET['route'] ?? '') === 'shop' && !isset($_GET['category']) && ($_GET['filter'] ?? '') !== 'sale') ? 'active' : '' ?>" href="<?= url('shop') ?>" id="collectionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Collections <i class="bi bi-chevron-down ms-1" style="font-size: 0.62rem; opacity: 0.7;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-buller shadow-lg border-0 py-2" aria-labelledby="collectionsDropdown">
                            <li><a class="dropdown-item py-2 px-3 fw-semibold" href="<?= url('shop') ?>"><i class="bi bi-grid me-2 text-warning"></i> View All Curations</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item py-2 px-3" href="<?= url('shop?category=leather-jackets') ?>"><i class="bi bi-tag me-2"></i> Tuscan Leather Jackets</a></li>
                            <li><a class="dropdown-item py-2 px-3" href="<?= url('shop?category=bags-briefcases') ?>"><i class="bi bi-briefcase me-2"></i> Bags & Briefcases</a></li>
                            <li><a class="dropdown-item py-2 px-3" href="<?= url('shop?category=wallets-accessories') ?>"><i class="bi bi-wallet2 me-2"></i> Wallets & Small Goods</a></li>
                            <li><a class="dropdown-item py-2 px-3" href="<?= url('shop?category=handcrafted-belts') ?>"><i class="bi bi-slash-circle me-2"></i> Handcrafted Belts</a></li>
                            <li><a class="dropdown-item py-2 px-3" href="<?= url('shop?category=footwear-boots') ?>"><i class="bi bi-boot me-2"></i> Footwear & Boots</a></li>
                            <li><a class="dropdown-item py-2 px-3" href="<?= url('shop?category=travel-accessories') ?>"><i class="bi bi-compass me-2"></i> Desk & Travel Curations</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item py-2 px-3 text-danger fw-semibold" href="<?= url('shop?filter=sale') ?>"><i class="bi bi-stars me-2 text-warning"></i> Privé Archive Sale</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-buller nav-link-prive <?= (($_GET['route'] ?? '') === 'sale' || ($_GET['filter'] ?? '') === 'sale') ? 'active' : '' ?>" href="<?= url('shop?filter=sale') ?>">
                            Privé Sale <span class="badge-sale-dot"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-buller <?= (in_array($_GET['route'] ?? '', ['about', 'heritage'])) ? 'active' : '' ?>" href="<?= url('about') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-buller <?= (($_GET['route'] ?? '') === 'contact') ? 'active' : '' ?>" href="<?= url('contact') ?>">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation Drawer Panel (Slides out from LEFT side on mobile view) -->
    <div class="offcanvas offcanvas-start buller-mobile-drawer" tabindex="-1" id="bullerNavDrawer" aria-labelledby="bullerNavDrawerLabel">
        <div class="offcanvas-header border-bottom py-3 px-3" style="background-color: var(--color-espresso-deep); border-color: rgba(197, 168, 128, 0.25) !important;">
            <div class="d-flex align-items-center gap-2" id="bullerNavDrawerLabel">
                <img src="<?= asset('assets/images/buller-logo-gold.svg') ?>" alt="Buller Logo" style="height: 32px; width: auto;">
                <div class="d-flex flex-column">
                    <span class="brand-main" style="color: var(--color-gold-bright); font-size: 1.15rem; font-family: var(--font-brand); letter-spacing: 0.16em; line-height: 1.1;">BULLER</span>
                    <span class="brand-sub" style="color: var(--color-gold); font-size: 0.52rem; letter-spacing: 0.14em; text-transform: uppercase;">By leathercraft</span>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        
        <div class="offcanvas-body p-0 d-flex flex-column justify-content-between" style="background-color: var(--color-ivory);">
            <div class="p-3">
                <!-- Active Regional Market Pill (Mobile View in Drawer where all pages are) -->
                <div class="mb-3">
                    <button type="button" class="btn buller-market-pill-btn buller-market-pill-drawer w-100 d-flex align-items-center justify-content-between py-2 px-3" data-bs-toggle="modal" data-bs-target="#marketSelectorModal" style="background: linear-gradient(135deg, #1a1410 0%, #221812 100%) !important; border: 1px solid rgba(197, 168, 128, 0.45) !important; color: #ffffff !important; border-radius: 10px !important; box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;" title="Select Shopping Region & Currency" aria-label="Selected Region">
                        <div class="d-flex align-items-center gap-2">
                            <?= country_flag($activeCountry['code'], $activeCountry['name'], 20) ?>
                            <div class="text-start" style="line-height: 1.25;">
                                <div class="buller-market-pill-name" style="font-size: 0.84rem; color: #ded6cb !important; font-weight: 600 !important;"><?= e($activeCountry['name']) ?></div>
                                <div class="buller-market-pill-currency" style="font-size: 0.74rem; color: #ffffff !important; font-weight: 700 !important;"><?= e($activeCountry['currency_code']) ?> (<?= e($activeCountry['currency_symbol']) ?>)</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge border small" style="background: rgba(255, 255, 255, 0.08) !important; border-color: rgba(197, 168, 128, 0.4) !important; font-size: 0.68rem !important; color: var(--color-gold) !important;">
                                Change <i class="bi bi-chevron-right ms-1"></i>
                            </span>
                        </div>
                    </button>
                </div>

                <!-- Navigation Items -->
                <ul class="nav flex-column mb-3 mobile-nav-items">
                    <li class="nav-item">
                        <a class="nav-link py-2 px-3 fw-semibold rounded mobile-drawer-link <?= (!isset($_GET['route']) || $_GET['route'] === '' || $_GET['route'] === 'home') ? 'active' : '' ?>" href="<?= url('') ?>">
                            <i class="bi bi-house me-2 text-gold"></i> Home
                        </a>
                    </li>
                    
                    <!-- Collections Dropdown / Collapsible -->
                    <li class="nav-item">
                        <a class="nav-link py-2 px-3 fw-semibold rounded mobile-drawer-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#drawerCollectionsCollapse" role="button" aria-expanded="false" aria-controls="drawerCollectionsCollapse">
                            <span><i class="bi bi-grid me-2 text-gold"></i> Collections</span>
                            <i class="bi bi-chevron-down small text-muted"></i>
                        </a>
                        <div class="collapse ps-3 pe-1 py-1" id="drawerCollectionsCollapse">
                            <ul class="nav flex-column border-start ps-2" style="border-color: rgba(197, 168, 128, 0.4) !important;">
                                <li class="nav-item"><a class="nav-link py-2 px-2 small fw-bold text-dark" href="<?= url('shop') ?>"><i class="bi bi-arrow-right-short text-warning"></i> View All Curations</a></li>
                                <li class="nav-item"><a class="nav-link py-1 px-2 small text-muted" href="<?= url('shop?category=leather-jackets') ?>"><i class="bi bi-tag me-1 text-gold"></i> Tuscan Leather Jackets</a></li>
                                <li class="nav-item"><a class="nav-link py-1 px-2 small text-muted" href="<?= url('shop?category=bags-briefcases') ?>"><i class="bi bi-briefcase me-1 text-gold"></i> Bags & Briefcases</a></li>
                                <li class="nav-item"><a class="nav-link py-1 px-2 small text-muted" href="<?= url('shop?category=wallets-accessories') ?>"><i class="bi bi-wallet2 me-1 text-gold"></i> Wallets & Small Goods</a></li>
                                <li class="nav-item"><a class="nav-link py-1 px-2 small text-muted" href="<?= url('shop?category=handcrafted-belts') ?>"><i class="bi bi-slash-circle me-1 text-gold"></i> Handcrafted Belts</a></li>
                                <li class="nav-item"><a class="nav-link py-1 px-2 small text-muted" href="<?= url('shop?category=footwear-boots') ?>"><i class="bi bi-boot me-1 text-gold"></i> Footwear & Boots</a></li>
                                <li class="nav-item"><a class="nav-link py-1 px-2 small text-muted" href="<?= url('shop?category=travel-accessories') ?>"><i class="bi bi-compass me-1 text-gold"></i> Desk & Travel Curations</a></li>
                                <li class="nav-item"><a class="nav-link py-1 px-2 small text-danger fw-semibold" href="<?= url('shop?filter=sale') ?>"><i class="bi bi-stars me-1 text-warning"></i> Privé Archive Sale</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link py-2 px-3 fw-semibold rounded mobile-drawer-link text-danger <?= (($_GET['route'] ?? '') === 'sale' || ($_GET['filter'] ?? '') === 'sale') ? 'active' : '' ?>" href="<?= url('shop?filter=sale') ?>">
                            <i class="bi bi-stars me-2 text-warning"></i> Privé Sale
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-2 px-3 fw-semibold rounded mobile-drawer-link <?= (in_array($_GET['route'] ?? '', ['about', 'heritage'])) ? 'active' : '' ?>" href="<?= url('about') ?>">
                            <i class="bi bi-journal-text me-2 text-gold"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-2 px-3 fw-semibold rounded mobile-drawer-link <?= (($_GET['route'] ?? '') === 'contact') ? 'active' : '' ?>" href="<?= url('contact') ?>">
                            <i class="bi bi-headset me-2 text-gold"></i> Contact Concierge
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-2 px-3 fw-semibold rounded mobile-drawer-link <?= (($_GET['route'] ?? '') === 'wishlist') ? 'active' : '' ?>" href="<?= url('wishlist') ?>">
                            <i class="bi bi-heart me-2 text-gold"></i> My Wishlist
                        </a>
                    </li>
                </ul>

                <!-- Client Account Drawer Section -->
                <div class="pt-3 border-top" style="border-color: var(--color-border) !important;">
                    <div class="small text-uppercase fw-bold text-muted mb-2 px-2" style="letter-spacing: 0.1em; font-size: 0.7rem;">Client Concierge</div>
                    <?php if ($currentUser): ?>
                        <div class="p-2 mb-2 rounded bg-white border d-flex align-items-center justify-content-between">
                            <div class="text-truncate me-2">
                                <div class="fw-bold small text-truncate" style="color: var(--color-espresso-deep);"><i class="bi bi-person-circle me-1 text-warning"></i> <?= e($currentUser['name']) ?></div>
                                <div class="text-muted small text-truncate" style="font-size: 0.72rem;"><?= e($currentUser['email']) ?></div>
                            </div>
                            <a href="<?= url('logout') ?>" class="btn btn-outline-danger btn-sm py-1 px-2" style="font-size: 0.7rem;">Sign Out</a>
                        </div>
                        <div class="d-grid gap-1 mb-2">
                            <?php if ($currentUser['role'] === 'super_admin'): ?>
                                <a href="<?= url('superadmin/dashboard') ?>" class="btn btn-sm btn-outline-dark text-start py-2"><i class="bi bi-speedometer2 me-2 text-warning"></i> Super Admin Panel</a>
                            <?php elseif ($currentUser['role'] === 'country_admin'): ?>
                                <a href="<?= url('admin/dashboard') ?>" class="btn btn-sm btn-outline-dark text-start py-2"><i class="bi bi-gear-wide-connected me-2 text-warning"></i> Country Admin Panel</a>
                            <?php else: ?>
                                <a href="<?= url('customer/dashboard') ?>" class="btn btn-sm btn-outline-dark text-start py-2"><i class="bi bi-person me-2 text-warning"></i> Customer Dashboard</a>
                                <a href="<?= url('customer/orders') ?>" class="btn btn-sm btn-outline-secondary text-start py-1" style="font-size: 0.78rem;"><i class="bi bi-box2 me-2"></i> Order History</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="d-grid gap-2 mb-3">
                            <a href="<?= url('login') ?>" class="btn btn-buller-primary btn-sm py-2">Sign In to Atelier</a>
                            <a href="<?= url('register') ?>" class="btn btn-outline-secondary btn-sm py-2">Create VIP Account</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


        </div>
    </div>


    <!-- Search Modal (Optimized & Sleek on Both Mobile & Desktop) -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg mx-auto px-2 px-sm-0">
            <div class="modal-content border-0 shadow-deep rounded-3" style="background-color: var(--color-ivory);">
                <div class="modal-header border-0 pb-1 pt-3 px-3 px-sm-4 d-flex justify-content-between align-items-center">
                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.15em; font-size: 0.72rem;">Buller Atelier Search</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-sm-4 pt-1">
                    <form action="<?= url('shop') ?>" method="GET">
                        <div class="input-group search-modal-input-group shadow-sm rounded-2 overflow-hidden border" style="border-color: var(--color-gold) !important; background: #fff;">
                            <span class="input-group-text bg-white border-0 text-gold pe-1 ps-3"><i class="bi bi-search"></i></span>
                            <input type="text" name="q" class="form-control border-0 py-2 py-sm-3 buller-search-input" placeholder="Search jackets, briefcases, boots, belts..." autofocus required>
                            <button class="btn btn-buller-primary px-3 px-sm-4 d-inline-flex align-items-center gap-1" type="submit">
                                <i class="bi bi-search d-sm-none"></i>
                                <span class="d-none d-sm-inline">Search</span>
                            </button>
                        </div>
                    </form>
                    <div class="mt-3">
                        <div class="text-muted small mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">Suggested Master Curations:</div>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="<?= url('shop?q=Full-Grain') ?>" class="badge bg-white text-dark border py-2 px-2 text-decoration-none">Full-Grain</a>
                            <a href="<?= url('shop?q=Vachetta') ?>" class="badge bg-white text-dark border py-2 px-2 text-decoration-none">Tuscan Vachetta</a>
                            <a href="<?= url('shop?q=Biker') ?>" class="badge bg-white text-dark border py-2 px-2 text-decoration-none">Biker Jackets</a>
                            <a href="<?= url('shop?q=Chelsea') ?>" class="badge bg-white text-dark border py-2 px-2 text-decoration-none">Chelsea Boots</a>
                            <a href="<?= url('shop?q=Duffel') ?>" class="badge bg-white text-dark border py-2 px-2 text-decoration-none">Duffel Bags</a>
                            <a href="<?= url('shop?q=Bridle') ?>" class="badge bg-white text-dark border py-2 px-2 text-decoration-none">Bridle Belts</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Cart Drawer -->
    <?php require __DIR__ . '/cart_drawer.php'; ?>

    <!-- Include Market Selector Modal -->
    <?php require __DIR__ . '/market_modal.php'; ?>
