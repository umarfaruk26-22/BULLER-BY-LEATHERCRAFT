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
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <?php
    $cssDiskPath = dirname(__DIR__, 3) . '/assets/css/luxury-theme.css';
    $cssVersion = file_exists($cssDiskPath) ? filemtime($cssDiskPath) : '1.0.0';
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

    <!-- Top Announcement & Market Utility Bar -->
    <div class="top-market-bar">
        <div class="container-fluid px-xl-5 px-lg-4 px-3 d-flex justify-content-between align-items-center">
            <div class="d-none d-md-block text-truncate pe-2">
                <i class="bi bi-shield-check text-warning me-1"></i>
                <span>Generational Craftsmanship &bull; Complimentary International Delivery Available</span>
            </div>
                <?php if (Auth::isCountryLocked()): ?>
                    <button type="button" class="market-selector-btn market-selector-btn-locked" data-bs-toggle="modal" data-bs-target="#marketSelectorModal" title="Market locked to your registered profile (<?= e($activeCountry['name']) ?>)">
                        <?= country_flag($activeCountry['code'], $activeCountry['name'], 20) ?>
                        <span class="d-none d-sm-inline"><?= e($activeCountry['name']) ?> &bull;</span>
                        <strong><?= e($activeCountry['currency_code']) ?> (<?= e($activeCountry['currency_symbol']) ?>)</strong>
                        <i class="bi bi-shield-lock-fill ms-1 text-gold" style="font-size: 0.72rem;"></i>
                    </button>
                <?php else: ?>
                    <button type="button" class="market-selector-btn" data-bs-toggle="modal" data-bs-target="#marketSelectorModal">
                        <?= country_flag($activeCountry['code'], $activeCountry['name'], 20) ?>
                        <span class="d-none d-sm-inline"><?= e($activeCountry['name']) ?> &bull;</span>
                        <strong><?= e($activeCountry['currency_code']) ?> (<?= e($activeCountry['currency_symbol']) ?>)</strong>
                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.65rem;"></i>
                    </button>
                <?php endif; ?>

                <div class="border-start border-secondary ps-3 d-none d-sm-flex align-items-center gap-3">
                    <?php if (Auth::isSuperAdmin()): ?>
                        <a href="<?= url('superadmin/dashboard') ?>" class="text-warning small text-decoration-none fw-semibold">
                            <i class="bi bi-speedometer2"></i> Super Admin Panel
                        </a>
                    <?php elseif (Auth::isCountryAdmin()): ?>
                        <a href="<?= url('admin/dashboard') ?>" class="text-warning small text-decoration-none fw-semibold">
                            <i class="bi bi-gear-wide-connected"></i> Country Admin Panel
                        </a>
                    <?php endif; ?>
                    <a href="<?= url('contact') ?>" class="text-light small text-decoration-none">Atelier Concierge</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg buller-navbar sticky-top">
        <div class="container-fluid px-xl-5 px-lg-4 px-3 d-flex align-items-center justify-content-between">
            <!-- Brand Logo -->
            <a class="navbar-brand navbar-brand-logo me-2 me-xl-4" href="<?= url('') ?>">
                <span class="brand-main">BULLER</span>
                <span class="brand-sub">By leathercraft</span>
            </a>

            <!-- Navbar Center Links (Collapsible for mobile/tablet) -->
            <div class="collapse navbar-collapse justify-content-center" id="bullerNavMenu">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
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

                <!-- Mobile Drawer Extras inside collapsed menu -->
                <div class="d-lg-none mt-3 pt-3 border-top">
                    <?php if ($currentUser): ?>
                        <div class="d-flex align-items-center justify-content-between px-2 mb-2">
                            <span class="fw-bold small text-truncate" style="color: var(--color-espresso-deep);"><i class="bi bi-person-circle me-1"></i> <?= e($currentUser['name']) ?></span>
                            <a href="<?= url('logout') ?>" class="text-danger small text-decoration-none">Sign Out</a>
                        </div>
                        <div class="d-grid gap-1 px-2 mb-3">
                            <?php if ($currentUser['role'] === 'super_admin'): ?>
                                <a href="<?= url('superadmin/dashboard') ?>" class="btn btn-sm btn-outline-dark text-start"><i class="bi bi-speedometer2 me-2"></i> Super Admin Panel</a>
                            <?php elseif ($currentUser['role'] === 'country_admin'): ?>
                                <a href="<?= url('admin/dashboard') ?>" class="btn btn-sm btn-outline-dark text-start"><i class="bi bi-gear-wide-connected me-2"></i> Country Admin Panel</a>
                            <?php else: ?>
                                <a href="<?= url('customer/dashboard') ?>" class="btn btn-sm btn-outline-dark text-start"><i class="bi bi-person me-2"></i> Customer Portal</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="d-grid gap-2 px-2 mb-3">
                            <a href="<?= url('login') ?>" class="btn btn-buller-primary btn-sm">Sign In to Atelier</a>
                            <a href="<?= url('register') ?>" class="btn btn-outline-secondary btn-sm">Create VIP Account</a>
                        </div>
                    <?php endif; ?>
                    <div class="px-2 text-center">
                        <?php if (Auth::isCountryLocked()): ?>
                            <div class="small p-2 bg-light border rounded text-muted">
                                <i class="bi bi-shield-lock-fill text-gold me-1"></i> Market Fixed: <strong><?= e($activeCountry['name']) ?> (<?= e($activeCountry['currency_code']) ?>)</strong>
                            </div>
                        <?php else: ?>
                            <button type="button" class="btn btn-sm btn-outline-dark w-100" data-bs-toggle="modal" data-bs-target="#marketSelectorModal">
                                <?= country_flag($activeCountry['code'], $activeCountry['name'], 18) ?> Switch Market: <strong><?= e($activeCountry['name']) ?> (<?= e($activeCountry['currency_code']) ?>)</strong>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Action Icons & Mobile Hamburger (Always visible on right side) -->
            <div class="d-flex align-items-center gap-1 gap-xl-2 nav-actions-wrapper ms-auto ms-lg-0">
                <!-- 1. Search Trigger -->
                <button type="button" id="bullerSearchBtn" class="btn nav-actions-icon nav-action-search" data-bs-toggle="modal" data-bs-target="#searchModal" title="Search Curations" aria-label="Search">
                    <i class="bi bi-search"></i>
                </button>

                <!-- 2. Account Dropdown -->
                <div class="dropdown nav-action-account-dropdown">
                    <button type="button" id="bullerAccountBtn" class="btn nav-actions-icon nav-action-account dropdown-toggle-custom" data-bs-toggle="dropdown" aria-expanded="false" title="Client Account" aria-label="Account">
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

                <!-- Mobile Hamburger Toggler (Rightmost on mobile) -->
                <button class="navbar-toggler border-0 p-1 ms-1 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#bullerNavMenu" aria-controls="bullerNavMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-2 text-dark"></i>
                </button>
            </div>
        </div>
    </nav>


    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg" style="background-color: var(--color-ivory);">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-1">
                    <form action="<?= url('shop') ?>" method="GET">
                        <div class="input-group mb-3 border-bottom pb-2" style="border-color: var(--color-espresso-deep) !important;">
                            <span class="input-group-text bg-transparent border-0 fs-4"><i class="bi bi-search"></i></span>
                            <input type="text" name="q" class="form-control bg-transparent border-0 fs-4" placeholder="Search jackets, briefcases, boots, belts..." autofocus required>
                            <button class="btn btn-buller-primary" type="submit">Search</button>
                        </div>
                    </form>
                    <div class="mt-3">
                        <span class="text-muted small me-2">Suggested Curations:</span>
                        <a href="<?= url('shop?q=Full-Grain') ?>" class="badge bg-light text-dark border me-1 text-decoration-none">Full-Grain</a>
                        <a href="<?= url('shop?q=Vachetta') ?>" class="badge bg-light text-dark border me-1 text-decoration-none">Tuscan Vachetta</a>
                        <a href="<?= url('shop?q=Biker') ?>" class="badge bg-light text-dark border me-1 text-decoration-none">Biker Jackets</a>
                        <a href="<?= url('shop?q=Chelsea') ?>" class="badge bg-light text-dark border me-1 text-decoration-none">Chelsea Boots</a>
                        <a href="<?= url('shop?q=Duffel') ?>" class="badge bg-light text-dark border text-decoration-none">Duffel Bags</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Cart Drawer & Market Modal Components -->
    <?php require __DIR__ . '/cart_drawer.php'; ?>
    <?php require __DIR__ . '/market_modal.php'; ?>
