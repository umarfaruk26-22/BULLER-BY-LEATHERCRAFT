<?php
use App\Core\Auth;
$pageTitle = 'Global Enterprise System Configuration';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0 pb-5" style="max-width: 1200px; margin: 0 auto;">
    <!-- Top Header: Title & Direct Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h1 class="h3 font-serif mb-1 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep); letter-spacing: -0.01em;">
                <i class="bi bi-sliders text-warning"></i> Global System Settings & Infrastructure
            </h1>
            <p class="text-muted small mb-0">
                Configure master store metadata, customer support contacts, payment gateway credentials, and automated mail delivery.
            </p>
        </div>

        <!-- Header Action Controls -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-dark text-warning border border-warning px-3 py-2 small shadow-xs">
                <i class="bi bi-shield-lock-fill me-1"></i> Super Admin Secured
            </span>
            <button type="button" class="btn btn-outline-secondary btn-sm px-3 shadow-xs" onclick="resetSettingsForm()" title="Revert to last saved settings">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Discard
            </button>
            <button type="submit" form="settingsForm" id="headerSaveBtn" class="btn btn-dark btn-sm fw-bold px-3 shadow-xs d-flex align-items-center gap-1.5" style="background: #0f382a !important; border-color: #0f382a; padding-top: 7px; padding-bottom: 7px; border-radius: 6px;">
                <i class="bi bi-check2-circle fs-6"></i> Save Changes
            </button>
        </div>
    </div>

    <form id="settingsForm" action="<?= url('superadmin/settings/save') ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

        <div class="row g-4">
            <!-- Left Column: Brand, Status & Support Settings -->
            <div class="col-lg-6">
                <!-- 1. Store Brand Identity & System Mode -->
                <div class="bg-white border rounded-3 p-4 shadow-soft mb-4" style="border-color: #e2e8f0; border-top: 4px solid var(--color-gold) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2 text-dark" style="font-size: 1.05rem;">
                            <i class="bi bi-shop text-warning fs-5"></i> Store Brand Identity
                        </h5>
                        <span class="badge bg-light text-muted border">Brand & Localization</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Store Brand Name *</label>
                        <input type="text" name="site_name" class="form-control" value="<?= e($settings['site_name'] ?? 'Buller Leather') ?>" required placeholder="e.g. Buller Leather">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Heritage Tagline / Motto *</label>
                        <input type="text" name="site_tagline" class="form-control" value="<?= e($settings['site_tagline'] ?? 'Generational Leather Craftsmanship & Heritage Luxury') ?>" required placeholder="e.g. Generational Leather Craftsmanship & Heritage Luxury">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Global Announcement / Free Shipping Banner</label>
                        <input type="text" name="free_shipping_notice" class="form-control" value="<?= e($settings['free_shipping_notice'] ?? '') ?>" placeholder="Complimentary delivery text on top banner">
                        <small class="text-muted" style="font-size: 0.72rem;">Displayed in the uppermost announcement strip across all regional storefronts.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">Default Fallback Country</label>
                            <input type="text" name="default_country_code" class="form-control text-uppercase font-monospace" style="max-width: 140px;" value="<?= e($settings['default_country_code'] ?? 'US') ?>" required placeholder="US">
                            <small class="text-muted" style="font-size: 0.72rem;">ISO 2-letter country code (US, IN, GB, AE)</small>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">Order Tracking Prefix</label>
                            <input type="text" name="order_prefix" class="form-control font-monospace" style="max-width: 140px;" value="<?= e($settings['order_prefix'] ?? 'BL-') ?>" placeholder="BL-">
                            <small class="text-muted" style="font-size: 0.72rem;">Prefix applied to invoice numbers</small>
                        </div>
                    </div>
                </div>

                <!-- 2. Customer Support & Contact Desk -->
                <div class="bg-white border rounded-3 p-4 shadow-soft mb-4" style="border-color: #e2e8f0;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2 text-dark" style="font-size: 1.05rem;">
                            <i class="bi bi-headset text-warning fs-5"></i> Customer Support Desk
                        </h5>
                        <span class="badge bg-light text-muted border">Concierge</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Support Desk Email *</label>
                            <input type="email" name="contact_email" class="form-control" value="<?= e($settings['contact_email'] ?? 'concierge@bullerleather.com') ?>" required placeholder="support@bullerleather.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Global Toll-Free Phone</label>
                            <input type="text" name="contact_phone" class="form-control font-monospace" value="<?= e($settings['contact_phone'] ?? '+1 (800) 285-5371') ?>" placeholder="+1 (800) 285-5371">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">WhatsApp Concierge Hotline</label>
                        <input type="text" name="contact_whatsapp" class="form-control font-monospace" value="<?= e($settings['contact_whatsapp'] ?? '+44 7400 123456') ?>" placeholder="+44 7400 123456">
                        <small class="text-muted" style="font-size: 0.72rem;">Powers the direct concierge messaging link on the contact page.</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-dark mb-1">Flagship Showroom / Headquarters Address</label>
                        <textarea name="contact_address" class="form-control" rows="2" placeholder="Street, Atelier, City, Postal Code, Country"><?= e($settings['contact_address'] ?? 'Buller Atelier, 48 Conduit Street, Mayfair, London W1S 2YG, UK') ?></textarea>
                    </div>
                </div>

                <!-- 3. Social Media Presence -->
                <div class="bg-white border rounded-3 p-4 shadow-soft mb-4" style="border-color: #e2e8f0;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2 text-dark" style="font-size: 1.05rem;">
                            <i class="bi bi-share text-warning fs-5"></i> Social Media & Lookbook Channels
                        </h5>
                        <span class="badge bg-light text-muted border">Storefront Footer</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Instagram Profile URL</label>
                            <input type="url" name="social_instagram" class="form-control" value="<?= e($settings['social_instagram'] ?? 'https://instagram.com/bullerleather') ?>" placeholder="https://instagram.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Facebook Brand Page</label>
                            <input type="url" name="social_facebook" class="form-control" value="<?= e($settings['social_facebook'] ?? 'https://facebook.com/bullerleather') ?>" placeholder="https://facebook.com/...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Payment & Mail Server Integrations -->
            <div class="col-lg-6">
                <!-- 4. Payment Gateways Configuration -->
                <div class="bg-white border rounded-3 p-4 shadow-soft mb-4" style="border-color: #e2e8f0; border-top: 4px solid var(--color-gold) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2 text-dark" style="font-size: 1.05rem;">
                            <i class="bi bi-credit-card-2-front text-warning fs-5"></i> International Payment Gateways
                        </h5>
                        <span class="badge bg-light text-muted border">Encrypted</span>
                    </div>
                    <p class="small text-muted mb-3" style="font-size: 0.82rem;">
                        API keys for regional customer checkout (Razorpay for India market, Stripe & Amex for Western markets).
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Razorpay Key ID</label>
                        <input type="text" name="razorpay_key_id" class="form-control font-monospace" value="<?= e($settings['razorpay_key_id'] ?? '') ?>" placeholder="rzp_test_..." autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Razorpay Key Secret</label>
                        <div class="input-group">
                            <input type="password" id="razorpaySecretInput" name="razorpay_key_secret" class="form-control font-monospace" value="<?= e($settings['razorpay_key_secret'] ?? '') ?>" placeholder="Secret Key" autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('razorpaySecretInput', this)" title="Show/Hide Secret">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-2.5 bg-light rounded border small text-muted d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <span>Checkout transactions are routed through HTTPS SHA-256 webhooks for zero-touch reconciliation.</span>
                    </div>
                </div>

                <!-- 5. SMTP Mail Configuration -->
                <div class="bg-white border rounded-3 p-4 shadow-soft mb-4" style="border-color: #e2e8f0;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2 text-dark" style="font-size: 1.05rem;">
                            <i class="bi bi-envelope-at text-warning fs-5"></i> SMTP Automated Notifications
                        </h5>
                        <span class="badge bg-light text-muted border">Mail Server</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-dark mb-1">SMTP Server Host</label>
                            <input type="text" name="smtp_host" class="form-control font-monospace" value="<?= e($settings['smtp_host'] ?? 'smtp.bullerleather.com') ?>" placeholder="smtp.bullerleather.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Port</label>
                            <input type="number" name="smtp_port" class="form-control font-monospace" value="<?= e($settings['smtp_port'] ?? '587') ?>" placeholder="587">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">SMTP Username / Email</label>
                            <input type="text" name="smtp_user" class="form-control font-monospace" value="<?= e($settings['smtp_user'] ?? 'notifications@bullerleather.com') ?>" placeholder="notifications@bullerleather.com" autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">SMTP Password</label>
                            <div class="input-group">
                                <input type="password" id="smtpPassInput" name="smtp_pass" class="form-control font-monospace" value="<?= e($settings['smtp_pass'] ?? '') ?>" placeholder="SMTP Password" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('smtpPassInput', this)" title="Show/Hide Password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-dark mb-1">Sender Display Name</label>
                        <input type="text" name="smtp_from_name" class="form-control" value="<?= e($settings['smtp_from_name'] ?? 'Buller Leather Concierge') ?>" placeholder="Buller Leather Concierge">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Floating Action Bar (Always Visible on Screen) -->
        <div class="sticky-settings-bar p-3 bg-white border rounded-3 shadow-lg d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mt-4" style="position: sticky; bottom: 20px; z-index: 1020; border-color: #cbd5e1 !important; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.97) !important;">
            <div class="d-flex align-items-center gap-2">
                <span id="settingsStatusBadge" class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fw-semibold d-flex align-items-center gap-1">
                    <i class="bi bi-check-circle-fill"></i> Ready
                </span>
                <span class="text-muted small d-none d-md-inline" style="font-size: 0.82rem;">
                    All modifications are logged in security audit logs and applied storefront-wide.
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 w-100 w-sm-auto justify-content-end">
                <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-semibold btn-sm shadow-xs" onclick="resetSettingsForm()">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Discard
                </button>
                <button type="submit" id="bottomSaveBtn" class="btn btn-dark px-4 py-2 fw-bold btn-sm shadow-sm d-flex align-items-center gap-2" style="background: #0f382a !important; border-color: #0f382a; letter-spacing: 0.02em;">
                    <i class="bi bi-save2"></i> Save Global System Settings
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Toggle Password Visibility (Eye Icon)
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
}

// Reset form to saved values
function resetSettingsForm() {
    if (confirm('Discard all unsaved changes and reload original settings?')) {
        const form = document.getElementById('settingsForm');
        if (form) {
            form.reset();
            updateUnsavedStatus(false);
        }
    }
}

// Unsaved changes tracker
function updateUnsavedStatus(hasChanges) {
    const badge = document.getElementById('settingsStatusBadge');
    const headerBtn = document.getElementById('headerSaveBtn');
    const bottomBtn = document.getElementById('bottomSaveBtn');

    if (!badge) return;

    if (hasChanges) {
        badge.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning px-2.5 py-1.5 fw-bold d-flex align-items-center gap-1';
        badge.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Unsaved Changes';
        if (headerBtn) {
            headerBtn.classList.remove('btn-dark');
            headerBtn.classList.add('btn-success');
        }
        if (bottomBtn) {
            bottomBtn.classList.remove('btn-dark');
            bottomBtn.classList.add('btn-success');
        }
    } else {
        badge.className = 'badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fw-semibold d-flex align-items-center gap-1';
        badge.innerHTML = '<i class="bi bi-check-circle-fill"></i> Ready';
        if (headerBtn) {
            headerBtn.classList.remove('btn-success');
            headerBtn.classList.add('btn-dark');
        }
        if (bottomBtn) {
            bottomBtn.classList.remove('btn-success');
            bottomBtn.classList.add('btn-dark');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('settingsForm');
    if (form) {
        form.addEventListener('input', function() {
            updateUnsavedStatus(true);
        });
        form.addEventListener('change', function() {
            updateUnsavedStatus(true);
        });
        form.addEventListener('submit', function() {
            const btns = form.querySelectorAll('button[type="submit"], #headerSaveBtn');
            btns.forEach(b => {
                b.disabled = true;
                b.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';
            });
        });
    }
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
