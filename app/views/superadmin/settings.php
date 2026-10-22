<?php
use App\Core\Auth;
$pageTitle = 'Global Enterprise System Configuration';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-serif mb-1 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                <i class="bi bi-sliders text-warning"></i> Global System Settings & Infrastructure
            </h1>
            <p class="text-muted small mb-0">Configure master store metadata, customer support contacts, payment gateway credentials, and automated mail delivery.</p>
        </div>
        <div>
            <span class="badge bg-dark text-warning border border-warning px-3 py-2 small">
                <i class="bi bi-shield-lock-fill me-1"></i> Super Admin Secured
            </span>
        </div>
    </div>

    <form action="<?= url('superadmin/settings/save') ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

        <div class="row g-4">
            <!-- Left Column: General & Support Settings -->
            <div class="col-lg-6">
                <!-- Brand & Store Identity -->
                <div class="bg-white border p-4 shadow-soft mb-4" style="border-color: var(--admin-card-border); border-top: 3px solid var(--color-gold) !important;">
                    <h5 class="font-serif mb-3 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                        <i class="bi bi-shop text-warning"></i> Store Brand Identity
                    </h5>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Store Brand Name</label>
                        <input type="text" name="site_name" class="form-control" value="<?= e($settings['site_name'] ?? 'Buller Leather') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Heritage Tagline / Motto</label>
                        <input type="text" name="site_tagline" class="form-control" value="<?= e($settings['site_tagline'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Global Announcement / Free Shipping Banner</label>
                        <input type="text" name="free_shipping_notice" class="form-control" value="<?= e($settings['free_shipping_notice'] ?? '') ?>" placeholder="Complimentary delivery text on top banner">
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Default Fallback Country Code</label>
                        <input type="text" name="default_country_code" class="form-control text-uppercase" style="max-width: 120px;" value="<?= e($settings['default_country_code'] ?? 'US') ?>" required>
                    </div>
                </div>

                <!-- Customer Support & Contact Desk -->
                <div class="bg-white border p-4 shadow-soft mb-4" style="border-color: var(--admin-card-border);">
                    <h5 class="font-serif mb-3 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                        <i class="bi bi-telephone text-warning"></i> Customer Support Desk
                    </h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Support Desk Email</label>
                            <input type="email" name="contact_email" class="form-control" value="<?= e($settings['contact_email'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Global Toll-Free Phone</label>
                            <input type="text" name="contact_phone" class="form-control" value="<?= e($settings['contact_phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Flagship Showroom / Headquarters Address</label>
                        <textarea name="contact_address" class="form-control" rows="2"><?= e($settings['contact_address'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Payment & Mail Server Integrations -->
            <div class="col-lg-6">
                <!-- Payment Gateways Configuration -->
                <div class="bg-white border p-4 shadow-soft mb-4" style="border-color: var(--admin-card-border); border-top: 3px solid var(--color-gold) !important;">
                    <h5 class="font-serif mb-3 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                        <i class="bi bi-credit-card-2-front text-warning"></i> International Payment Gateways
                    </h5>
                    <p class="small text-muted mb-3">API keys for regional routing (Razorpay for India market, Stripe & Amex for Western markets).</p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Razorpay Key ID</label>
                        <input type="text" name="razorpay_key_id" class="form-control font-monospace" value="<?= e($settings['razorpay_key_id'] ?? '') ?>">
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Razorpay Key Secret</label>
                        <input type="password" name="razorpay_key_secret" class="form-control font-monospace" value="<?= e($settings['razorpay_key_secret'] ?? '') ?>">
                    </div>
                </div>

                <!-- SMTP Mail Configuration -->
                <div class="bg-white border p-4 shadow-soft mb-4" style="border-color: var(--admin-card-border);">
                    <h5 class="font-serif mb-3 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                        <i class="bi bi-envelope-at text-warning"></i> SMTP Automated Notifications
                    </h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted">SMTP Server Host</label>
                            <input type="text" name="smtp_host" class="form-control" value="<?= e($settings['smtp_host'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Port</label>
                            <input type="number" name="smtp_port" class="form-control" value="<?= e($settings['smtp_port'] ?? '587') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">SMTP Username / Email</label>
                            <input type="text" name="smtp_user" class="form-control" value="<?= e($settings['smtp_user'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">SMTP Password</label>
                            <input type="password" name="smtp_pass" class="form-control" value="<?= e($settings['smtp_pass'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Sender Display Name</label>
                        <input type="text" name="smtp_from_name" class="form-control" value="<?= e($settings['smtp_from_name'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Save Action Bar -->
        <div class="p-3 bg-white border shadow-soft d-flex justify-content-between align-items-center rounded mt-2">
            <span class="text-muted small">
                <i class="bi bi-info-circle me-1"></i> All modifications will be immediately logged in the audit ledger and activated storefront-wide.
            </span>
            <button type="submit" class="btn btn-dark px-4 py-2" style="background-color: var(--color-espresso-deep); border-color: var(--color-gold);">
                <i class="bi bi-save2 me-1 text-warning"></i> Save Global System Settings
            </button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
