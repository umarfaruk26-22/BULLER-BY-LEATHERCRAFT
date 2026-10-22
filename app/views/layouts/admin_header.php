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
    
    <!-- Favicon (High-Contrast Medallion) -->
    <?php
    $favDiskPath = dirname(__DIR__, 3) . '/assets/images/buller-favicon.svg';
    $favVer = file_exists($favDiskPath) ? filemtime($favDiskPath) : '2.0.0';
    ?>
    <link rel="icon" type="image/svg+xml" href="<?= asset('assets/images/buller-favicon.svg') ?>?v=<?= $favVer ?>">
    <link rel="alternate icon" type="image/svg+xml" href="<?= asset('assets/images/buller-favicon.svg') ?>?v=<?= $favVer ?>">

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

    <div class="admin-sidebar-backdrop d-lg-none" onclick="document.querySelector('.admin-sidebar').classList.remove('show'); this.classList.remove('show');"></div>

    <div class="admin-main-wrapper">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-dark btn-sm d-lg-none p-1 px-2" type="button" onclick="document.querySelector('.admin-sidebar').classList.toggle('show'); document.querySelector('.admin-sidebar-backdrop').classList.toggle('show');" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h4 class="mb-0 font-serif fw-bold text-truncate admin-topbar-title" style="color: var(--color-espresso-deep); font-size: 1.05rem; max-width: 140px;">
                    <?= isset($pageTitle) ? e($pageTitle) : 'Buller Administration' ?>
                </h4>
            </div>

            <div class="d-flex align-items-center gap-2 gap-sm-3">
                <?php if (isset($permittedCountries) && count($permittedCountries) > 1): ?>
                    <!-- Market Country Selector -->
                    <div class="d-flex align-items-center gap-1">
                        <span class="small text-muted text-nowrap d-none d-md-inline"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Market:</span>
                        <select class="form-select form-select-sm" style="max-width: 150px;" onchange="switchAdminMarket(this.value)">
                            <?php foreach ($permittedCountries as $pc): ?>
                                <option value="<?= $pc['id'] ?>" <?= (isset($selectedCountryId) && $selectedCountryId == $pc['id']) ? 'selected' : '' ?>>
                                    <?= e($pc['name']) ?> (<?= e($pc['currency_symbol']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <a href="<?= url('') ?>" target="_blank" class="btn btn-outline-dark btn-sm p-1 px-2" title="Preview Storefront">
                    <i class="bi bi-eye"></i> <span class="d-none d-md-inline ms-1">Storefront</span>
                </a>
                
                <div class="dropdown">
                    <button class="btn btn-dark btn-sm dropdown-toggle d-flex align-items-center gap-1 p-1 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="<?= e(Auth::user()['name']) ?>">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-md-inline text-truncate" style="max-width: 130px;"><?= e(Auth::user()['name']) ?></span>
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
