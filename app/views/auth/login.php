<?php
use App\Core\Auth;
use App\Core\MarketContext;

$pageTitle = 'Sign In | Client & Atelier Portal';
$activeCountry = MarketContext::getCountry();

require __DIR__ . '/../layouts/header.php';
?>

<section class="py-5" style="background-color: var(--color-ivory); min-height: 75vh;">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="bg-white border shadow-soft p-4 p-md-5" style="border-color: var(--color-border); border-top: 4px solid var(--color-gold) !important;">
                    <div class="text-center mb-4">
                        <a href="<?= url('') ?>" class="navbar-brand-logo navbar-brand-logo-stacked text-decoration-none" title="Buller Leather - Return to Storefront">
                            <img src="<?= asset('assets/images/buller-logo.svg') ?>" alt="Buller Leather Logo" class="brand-logo-icon mb-2" style="height: 52px;">
                            <div class="brand-text-group">
                                <span class="brand-main fs-3">BULLER</span>
                                <span class="brand-sub">By leathercraft</span>
                            </div>
                        </a>
                    </div>

                    <?php if (has_flash('login_error')): ?>
                        <div class="alert alert-danger small py-2 mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= flash('login_error') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (has_flash('login_success')): ?>
                        <div class="alert alert-success small py-2 mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i> <?= flash('login_success') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= url('login') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control" required placeholder="name@domain.com">
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold text-muted mb-0">Password</label>
                                <a href="#" class="small text-muted">Forgot password?</a>
                            </div>
                            <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        </div>

                        <button type="submit" class="btn btn-buller-primary w-100 py-3 mb-3">
                            Sign In To Atelier Account
                        </button>

                        <div class="text-center small text-muted">
                            Don't have an account yet? <a href="<?= url('register') ?>" class="fw-semibold text-decoration-underline" style="color: var(--color-cognac);">Create Account</a>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
