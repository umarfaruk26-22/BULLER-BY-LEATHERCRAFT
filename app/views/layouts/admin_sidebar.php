<?php
use App\Core\Auth;
$isSuper = Auth::isSuperAdmin();
$currentRoute = $_GET['route'] ?? '';
?>

<div class="admin-sidebar">
    <div class="admin-sidebar-brand text-center position-relative">
        <button type="button" class="btn btn-sm btn-link text-white position-absolute top-0 end-0 m-2 d-lg-none" onclick="document.querySelector('.admin-sidebar').classList.remove('show'); const bd = document.querySelector('.admin-sidebar-backdrop'); if(bd) bd.classList.remove('show');" aria-label="Close Sidebar" style="text-decoration: none; opacity: 0.8;">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
        <a href="<?= url('') ?>" class="navbar-brand-logo text-decoration-none" title="Buller Leather - Return to Storefront">
            <img src="<?= asset('assets/images/buller-logo-gold.svg') ?>?v=<?= file_exists(dirname(__DIR__, 3) . '/assets/images/buller-logo-gold.svg') ? filemtime(dirname(__DIR__, 3) . '/assets/images/buller-logo-gold.svg') : '2.0.0' ?>" alt="Buller Leather Logo" class="brand-logo-icon-admin">
            <span class="brand-main">BULLER</span>
            <span class="brand-sub">By leathercraft</span>
        </a>
        <div class="mt-2 pt-1">
            <span class="admin-role-badge">
                <i class="bi <?= $isSuper ? 'bi-shield-fill-check' : 'bi-geo-alt-fill' ?> me-1"></i>
                <?= $isSuper ? 'Global Executive' : 'Regional Administration' ?>
            </span>
        </div>
    </div>

    <div class="flex-grow-1 overflow-auto py-2">
        <?php if ($isSuper): ?>
            <!-- SUPER ADMIN SECTION (100% EXCLUSIVE TO GLOBAL EXECUTIVE) -->
            <div class="admin-nav-heading">Global Executive</div>
            <a href="<?= url('superadmin/dashboard') ?>" class="admin-nav-link <?= ($currentRoute === 'superadmin/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Global Dashboard
            </a>
            <a href="<?= url('superadmin/products') ?>" class="admin-nav-link <?= ($currentRoute === 'superadmin/products') ? 'active' : '' ?>">
                <i class="bi bi-box-seam-fill"></i> Master Product Catalog
            </a>
            <a href="<?= url('admin/products') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'admin/product')) ? 'active' : '' ?>">
                <i class="bi bi-box-seam"></i> Products & Local Pricing
            </a>
            <a href="<?= url('superadmin/categories') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'superadmin/categories')) ? 'active' : '' ?>">
                <i class="bi bi-tags-fill"></i> Product Categories
            </a>
            <a href="<?= url('superadmin/orders') ?>" class="admin-nav-link <?= ($currentRoute === 'superadmin/orders') ? 'active' : '' ?>">
                <i class="bi bi-receipt"></i> Global Orders & Fulfillment
            </a>
            <a href="<?= url('admin/orders') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'admin/order')) ? 'active' : '' ?>">
                <i class="bi bi-truck"></i> Orders & Dispatch Tracking
            </a>
            <a href="<?= url('superadmin/coupons') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'superadmin/coupons')) ? 'active' : '' ?>">
                <i class="bi bi-ticket-perforated-fill"></i> Promotional Coupons
            </a>
            <a href="<?= url('superadmin/reviews') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'superadmin/reviews')) ? 'active' : '' ?>">
                <i class="bi bi-star-fill text-warning"></i> Review Moderation
            </a>
            <a href="<?= url('superadmin/countries') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'superadmin/countries')) ? 'active' : '' ?>">
                <i class="bi bi-globe-americas"></i> Country Markets (<?= $totalCountriesCount ?? 4 ?>)
            </a>
            <a href="<?= url('superadmin/admins') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'superadmin/admins')) ? 'active' : '' ?>">
                <i class="bi bi-person-badge-fill"></i> Admin Management (RBAC)
            </a>
            <a href="<?= url('superadmin/settings') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'superadmin/settings')) ? 'active' : '' ?>">
                <i class="bi bi-sliders"></i> Global System Settings
            </a>
            <a href="<?= url('superadmin/audit-logs') ?>" class="admin-nav-link <?= ($currentRoute === 'superadmin/audit-logs') ? 'active' : '' ?>">
                <i class="bi bi-shield-shaded"></i> Security Audit Logs
            </a>

        <?php else: ?>
            <!-- COUNTRY ADMIN SECTION (SCOPED) -->
            <div class="admin-nav-heading">Regional Management</div>
            <a href="<?= url('admin/dashboard') ?>" class="admin-nav-link <?= ($currentRoute === 'admin/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Country Dashboard
            </a>
            <a href="<?= url('admin/products') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'admin/product')) ? 'active' : '' ?>">
                <i class="bi bi-box-seam"></i> Products & Local Pricing
            </a>
            <a href="<?= url('admin/categories') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'admin/categories')) ? 'active' : '' ?>">
                <i class="bi bi-tags-fill"></i> Product Categories
            </a>
            <a href="<?= url('admin/orders') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'admin/order')) ? 'active' : '' ?>">
                <i class="bi bi-receipt"></i> Orders & Dispatch Tracking
            </a>
            <a href="<?= url('admin/coupons') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'admin/coupons')) ? 'active' : '' ?>">
                <i class="bi bi-ticket-perforated"></i> Regional Coupons
            </a>
            <a href="<?= url('admin/reviews') ?>" class="admin-nav-link <?= (str_starts_with($currentRoute, 'admin/reviews')) ? 'active' : '' ?>">
                <i class="bi bi-star"></i> Review Moderation
            </a>
        <?php endif; ?>

        <div class="admin-nav-heading">Storefront</div>
        <a href="<?= url('') ?>" target="_blank" class="admin-nav-link">
            <i class="bi bi-box-arrow-up-right"></i> Live Boutique Storefront
        </a>
    </div>

    <!-- User Profile Footer -->
    <div class="p-3 border-top border-secondary text-muted small" style="background: #0d0804;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="text-truncate me-2">
                <strong class="text-white d-block text-truncate"><?= e(Auth::user()['name']) ?></strong>
                <span class="badge bg-warning text-dark text-uppercase" style="font-size: 0.65rem;"><?= Auth::user()['role'] ?></span>
            </div>
            <a href="<?= url('logout') ?>" class="text-danger" title="Sign Out">
                <i class="bi bi-box-arrow-right fs-5"></i>
            </a>
        </div>
    </div>
</div>
