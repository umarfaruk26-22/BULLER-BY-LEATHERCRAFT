<?php
use App\Core\Auth;
use App\Core\MarketContext;
$isSuper = Auth::isSuperAdmin();
$currentRoute = $_GET['route'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | Buller Admin Portal' : 'Buller Leather | Management Portal' ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Admin Styling -->
    <?php
    $adminCssDiskPath = dirname(__DIR__, 3) . '/assets/css/admin-theme.css';
    $adminCssVer = file_exists($adminCssDiskPath) ? filemtime($adminCssDiskPath) : time();
    ?>
    <link rel="stylesheet" href="<?= asset('assets/css/admin-theme.css') ?>?v=<?= $adminCssVer ?>">

    <script>
        function switchAdminMarket(countryId) {
            const url = new URL(window.location.href);
            url.searchParams.set('country_id', countryId);
            window.location.href = url.toString();
        }
    </script>
</head>
<body class="admin-body">

    <?php require __DIR__ . '/admin_sidebar.php'; ?>

    <div class="admin-main-wrapper">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-dark btn-sm d-lg-none" type="button" onclick="document.querySelector('.admin-sidebar').classList.toggle('show');">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h4 class="mb-0 font-serif fs-5 fw-bold" style="color: var(--color-espresso-deep);">
                    <?= isset($pageTitle) ? e($pageTitle) : 'Atelier Administration' ?>
                </h4>
            </div>

            <div class="d-flex align-items-center gap-3">
                <?php if (isset($permittedCountries) && count($permittedCountries) > 1): ?>
                    <!-- Scoped Country Selector -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted text-nowrap d-none d-sm-inline">Market Scope:</span>
                        <select class="form-select form-select-sm" style="min-width: 160px;" onchange="switchAdminMarket(this.value)">
                            <?php foreach ($permittedCountries as $pc): ?>
                                <option value="<?= $pc['id'] ?>" <?= (isset($selectedCountryId) && $selectedCountryId == $pc['id']) ? 'selected' : '' ?>>
                                    [<?= e($pc['code']) ?>] <?= e($pc['name']) ?> (<?= e($pc['currency_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <a href="<?= url('') ?>" target="_blank" class="btn btn-outline-dark btn-sm" title="View Storefront">
                    <i class="bi bi-eye me-1"></i> <span class="d-none d-md-inline">Preview Storefront</span>
                </a>
                
                <div class="dropdown">
                    <button class="btn btn-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> <?= e(Auth::user()['name']) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 small">
                        <li><span class="dropdown-item-text text-muted"><?= e(Auth::user()['email']) ?></span></li>
                        <li><span class="dropdown-item-text"><span class="badge bg-warning text-dark text-uppercase"><?= e(Auth::user()['role']) ?></span></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php if ($isSuper): ?>
                            <li><a class="dropdown-item" href="<?= url('superadmin/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i> Super Admin Panel</a></li>
                            <li><a class="dropdown-item" href="<?= url('superadmin/settings') ?>"><i class="bi bi-sliders me-2"></i> System Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Log Out</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Super Admin Context Alert if visiting regional route -->
        <?php if ($isSuper && str_starts_with($currentRoute, 'admin/')): ?>
            <div class="px-4 pt-3 pb-0">
                <div class="d-flex align-items-center justify-content-between p-2 px-3 rounded shadow-sm" style="background: #1a120b; border: 1px solid #c5a059;">
                    <div class="d-flex align-items-center gap-2 text-white small">
                        <span class="badge bg-warning text-dark text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.05em;">SUPER ADMIN ACTIVE</span>
                        <span class="text-white-50 d-none d-md-inline">&bull; Scoped Regional View:</span>
                        <strong class="text-warning"><?= isset($currentCountry) ? e($currentCountry['name']) : 'Boutique' ?></strong>
                    </div>
                    <a href="<?= url('superadmin/dashboard') ?>" class="btn btn-outline-warning btn-sm py-1 px-2" style="font-size: 0.75rem;">
                        <i class="bi bi-arrow-left me-1"></i> Return to Global Super Admin
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Flash messages for Admin -->
        <div class="px-4 pt-3">
            <?php if (has_flash('admin_success')): ?>
                <div class="alert alert-success alert-dismissible fade show small" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= flash('admin_success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (has_flash('admin_error')): ?>
                <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= flash('admin_error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>

        <main class="admin-content-area">
