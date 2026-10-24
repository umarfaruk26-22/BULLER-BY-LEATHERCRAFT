<?php
use App\Core\Auth;
use App\Core\MarketContext;

$pageTitle = 'Create Account';
$countries = MarketContext::getAllActiveCountries();
$activeCountry = MarketContext::getCountry();

require __DIR__ . '/../layouts/header.php';
?>

<section class="py-5" style="background-color: var(--color-ivory); min-height: 75vh;">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="bg-white border shadow-soft p-4 p-md-5" style="border-color: var(--color-border); border-top: 4px solid var(--color-gold) !important;">
                    <div class="text-center mb-4">
                        <a href="<?= url('') ?>" class="navbar-brand-logo navbar-brand-logo-stacked text-decoration-none" title="Buller Leather - Return to Storefront">
                            <img src="<?= asset('assets/images/buller-logo.svg') ?>?v=<?= file_exists(dirname(__DIR__, 3) . '/assets/images/buller-logo.svg') ? filemtime(dirname(__DIR__, 3) . '/assets/images/buller-logo.svg') : '2.0.0' ?>" alt="Buller Leather Logo" class="brand-logo-icon mb-2" style="height: 52px;">
                            <div class="brand-text-group">
                                <span class="brand-main fs-3">BULLER</span>
                                <span class="brand-sub">By leathercraft</span>
                            </div>
                        </a>
                    </div>

                    <?php if (has_flash('register_error')): ?>
                        <div class="alert alert-danger small py-2 mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= flash('register_error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= url('register') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Full Legal Name *</label>
                            <input type="text" name="full_name" class="form-control" required placeholder="e.g. Julian Sterling">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Email Address *</label>
                                <input type="email" name="email" class="form-control" required placeholder="name@domain.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Contact Phone</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+1 212 555 0199">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted d-flex align-items-center justify-content-between">
                                <span>Primary Regional Market *</span>
                                <span class="text-gold" style="font-size: 0.72rem; letter-spacing: 0.05em;"><i class="bi bi-shield-lock-fill me-1"></i> Fixed on Registration</span>
                            </label>
                            <select name="default_country_id" class="form-select" required style="border-color: var(--color-border); padding: 0.65rem 0.85rem;">
                                <?php foreach ($countries as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($c['code'] === $activeCountry['code']) ? 'selected' : '' ?>>
                                        <?= e($c['name']) ?> — <?= e($c['currency_code']) ?> (<?= e($c['currency_symbol']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text small text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1 text-gold"></i> Your account, local currency (₹, $, £, AED), taxes, and courier delivery will be permanently configured for this selected country.
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Create Password *</label>
                                <input type="password" name="password" class="form-control" minlength="8" required placeholder="Min 8 characters">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Confirm Password *</label>
                                <input type="password" name="password_confirm" class="form-control" minlength="8" required placeholder="Re-enter password">
                            </div>
                        </div>

                        <div class="form-check mb-4 small text-muted">
                            <input class="form-check-input" type="checkbox" required id="agreeTerms">
                            <label class="form-check-label" for="agreeTerms">
                                I agree to the Buller Leather Terms of Service and European Data Protection Policies.
                            </label>
                        </div>

                        <button type="submit" class="btn btn-buller-primary w-100 py-3 mb-3">
                            Create Customer Account
                        </button>

                        <div class="text-center small text-muted">
                            Already registered? <a href="<?= url('login') ?>" class="fw-semibold text-decoration-underline" style="color: var(--color-cognac);">Sign In</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
