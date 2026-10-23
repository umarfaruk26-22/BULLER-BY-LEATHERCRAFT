<?php
use App\Core\Auth;
$pageTitle = 'Edit ' . e($country['name']) . ' (' . e($country['code']) . ') — Regional Market & Showroom';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-2 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?= url('admin') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('superadmin/countries') ?>" class="text-decoration-none text-muted">International Markets</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= e($country['name']) ?> (<?= e($country['code']) ?>)</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="flex-shrink-0">
                    <?= country_flag($country['code'], $country['name'], 36) ?>
                </div>
                <div>
                    <h1 class="h3 font-serif mb-0 text-dark">
                        Edit <?= e($country['name']) ?> Market & Showroom
                    </h1>
                    <small class="text-muted">
                        Configure physical atelier address, customer support lines, statutory taxes, and checkout currency.
                    </small>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('contact?country=' . $country['code']) ?>" target="_blank" class="btn btn-outline-dark btn-sm d-inline-flex align-items-center gap-1.5 shadow-xs">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>View Live on Contact Page</span>
            </a>
            <a href="<?= url('superadmin/countries') ?>" class="btn btn-secondary btn-sm d-inline-flex align-items-center gap-1.5 shadow-xs">
                <i class="bi bi-arrow-left"></i>
                <span>Back to All Markets</span>
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="<?= url('superadmin/countries/edit') ?>" method="POST" id="editCountryPageForm">
        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
        <input type="hidden" name="country_id" value="<?= (int)$country['id'] ?>">

        <div class="row g-4">
            <!-- Left Column: Showroom Address & Fiscal Policies -->
            <div class="col-lg-8">
                
                <!-- 1. Physical Showroom & Atelier Details -->
                <div class="card border-0 shadow-soft rounded-3 mb-4 p-4" style="background: #ffffff; border-top: 4px solid var(--color-espresso) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(26,26,26,0.06);">
                                <i class="bi bi-building fs-5 text-dark"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark font-serif mb-0" style="font-size: 1.1rem;">1. Physical Atelier & Showroom Presence</h5>
                                <small class="text-muted" style="font-size: 0.76rem;">Appears in storefront customer contact directories and regional office suites.</small>
                            </div>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle small fw-semibold">
                            <i class="bi bi-geo-alt-fill me-1"></i> Customer-Facing
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold text-dark mb-1">Showroom / Atelier Name</label>
                            <input type="text" name="atelier_title" class="form-control" value="<?= e($country['atelier_title'] ?? '') ?>" placeholder="e.g. The Colaba Heritage Showroom & Workshop">
                            <small class="text-muted" style="font-size: 0.72rem;">Title displayed to clients booking bespoke fitting appointments.</small>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-dark mb-1">City / Regional Hub *</label>
                            <input type="text" name="city" class="form-control" value="<?= e($country['city'] ?? '') ?>" placeholder="e.g. Mumbai, New York, London, Dubai" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark mb-1">Complete Physical Street Address *</label>
                            <textarea name="address" rows="3" class="form-control" placeholder="e.g. Heritage Wing, 14 B.K. Boman Behram Marg, Colaba, Mumbai, Maharashtra 400001" required><?= e($country['address'] ?? '') ?></textarea>
                            <small class="text-muted" style="font-size: 0.72rem;">Exact physical address for GPS directions, customer mailings, and statutory trade invoices.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Showroom & Support Operating Hours</label>
                            <input type="text" name="support_hours" class="form-control" value="<?= e($country['support_hours'] ?? '') ?>" placeholder="e.g. Monday – Saturday: 10:00 AM – 8:00 PM IST">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Regional Courier & Logistics Partner</label>
                            <input type="text" name="shipping_partner" class="form-control" value="<?= e($country['shipping_partner'] ?? '') ?>" placeholder="e.g. Blue Dart Apex & Air Express (Pre-Insured)">
                        </div>
                    </div>
                </div>

                <!-- 2. Direct Customer Support Lines -->
                <div class="card border-0 shadow-soft rounded-3 mb-4 p-4" style="background: #ffffff; border-top: 4px solid #16a34a !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(22,163,74,0.1);">
                                <i class="bi bi-headset fs-5 text-success"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark font-serif mb-0" style="font-size: 1.1rem;">2. Direct Customer Support & Concierge Channels</h5>
                                <small class="text-muted" style="font-size: 0.76rem;">Customers can click directly to call, WhatsApp, or email your regional staff.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Direct Support Phone *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" name="contact_phone" class="form-control" value="<?= e($country['contact_phone'] ?? '') ?>" placeholder="+91 (22) 6842 9100" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">WhatsApp Concierge Phone</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" name="contact_whatsapp" class="form-control" value="<?= e($country['contact_whatsapp'] ?? '') ?>" placeholder="+91 98200 48210">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Customer Support Email *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="contact_email" class="form-control" value="<?= e($country['contact_email'] ?? '') ?>" placeholder="support.in@bullerleather.com" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Fiscal, Tax & Shipping Policy -->
                <div class="card border-0 shadow-soft rounded-3 mb-4 p-4" style="background: #ffffff; border-top: 4px solid #c5a880 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(197,168,128,0.2);">
                                <i class="bi bi-receipt fs-5" style="color: #8c4a2f;"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark font-serif mb-0" style="font-size: 1.1rem;">3. Fiscal, Tax & Shipping Policies</h5>
                                <small class="text-muted" style="font-size: 0.76rem;">Governs automated checkout calculations and statutory invoicing.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Statutory Tax Name *</label>
                            <input type="text" name="tax_name" class="form-control" value="<?= e($country['tax_name'] ?? 'VAT / Tax') ?>" required placeholder="e.g. GST, VAT, Sales Tax">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Tax Rate Percentage (%) *</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="tax_rate_percent" class="form-control" value="<?= e(number_format((float)$country['tax_rate_percent'], 2, '.', '')) ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Flat Shipping Fee *</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= e($country['currency_symbol'] ?: '$') ?></span>
                                <input type="number" step="0.01" name="flat_shipping_rate" class="form-control" value="<?= e(number_format((float)$country['flat_shipping_rate'], 2, '.', '')) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Free Shipping Threshold *</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= e($country['currency_symbol'] ?: '$') ?></span>
                                <input type="number" step="0.01" name="free_shipping_threshold" class="form-control" value="<?= e(number_format((float)$country['free_shipping_threshold'], 2, '.', '')) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Return Window (Days) *</label>
                            <div class="input-group">
                                <input type="number" name="return_window_days" class="form-control" value="<?= (int)$country['return_window_days'] ?>" required>
                                <span class="input-group-text">Days</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Currency & Market Status -->
            <div class="col-lg-4">
                
                <!-- Market Status & Storefront Visibility Card -->
                <div class="card border-0 shadow-soft rounded-3 mb-4 p-3 p-md-4" style="background: #ffffff; border-left: 4px solid <?= $country['is_active'] ? '#16a34a' : '#64748b' ?> !important;">
                    <h6 class="fw-bold text-dark font-serif mb-2">Storefront Market Visibility</h6>
                    <p class="text-muted small mb-3" style="font-size: 0.78rem;">
                        Control whether international shoppers can switch to this market and checkout in this local currency.
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Status:</label>
                        <select name="is_active" class="form-select fw-semibold">
                            <option value="1" <?= $country['is_active'] ? 'selected' : '' ?>>🟢 Active Storefront Market</option>
                            <option value="0" <?= !$country['is_active'] ? 'selected' : '' ?>>⚪ Deactivated (Hidden from Public)</option>
                        </select>
                    </div>
                </div>

                <!-- Currency & Regional Specs Card -->
                <div class="card border-0 shadow-soft rounded-3 mb-4 p-3 p-md-4" style="background: #ffffff;">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="bi bi-globe2 text-dark fs-5"></i>
                        <h6 class="fw-bold text-dark font-serif mb-0">Currency & Regional Identity</h6>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Country Legal Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= e($country['name']) ?>" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">ISO 2-Letter Code</label>
                            <input type="text" class="form-control font-monospace text-uppercase bg-light fw-bold" value="<?= e($country['code']) ?>" readonly disabled title="ISO code is permanent to preserve pricing integrity">
                            <small class="text-muted" style="font-size: 0.68rem;">Permanent ISO Code</small>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">Flag Emoji / Icon</label>
                            <input type="text" name="flag_icon" class="form-control text-center fs-5" value="<?= e($country['flag_icon'] ?: '🌐') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Phone Dial Prefix</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="phone_code" class="form-control font-monospace" value="<?= e($country['phone_code'] ?? '') ?>" placeholder="+1">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">Currency Code *</label>
                            <input type="text" name="currency_code" class="form-control font-monospace text-uppercase fw-bold" value="<?= e($country['currency_code']) ?>" maxlength="5" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">Currency Symbol *</label>
                            <input type="text" name="currency_symbol" class="form-control font-monospace fw-bold text-center" value="<?= e($country['currency_symbol']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-dark mb-1">Currency Title *</label>
                        <input type="text" name="currency_name" class="form-control" value="<?= e($country['currency_name']) ?>" required placeholder="e.g. Indian Rupee">
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card border-0 rounded-3 p-3 bg-light" style="border: 1px dashed #cbd5e1 !important;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle-fill text-primary mt-0.5"></i>
                        <div class="small text-muted" style="font-size: 0.76rem; line-height: 1.5;">
                            Saving updates this regional showroom address on the public storefront immediately. Pricing across all master products remains safely intact.
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sticky Footer Save Action Bar -->
        <div class="d-flex align-items-center justify-content-between p-3 mt-4 bg-white border rounded-3 shadow-soft" style="border-color: var(--admin-card-border);">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                    <i class="bi bi-shield-check me-1"></i> Ready to Save
                </span>
                <span class="text-muted small d-none d-md-inline" style="font-size: 0.8rem;">
                    Editing specifications for <strong><?= e($country['name']) ?> (<?= e($country['code']) ?>)</strong>.
                </span>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= url('superadmin/countries') ?>" class="btn btn-outline-secondary px-3">
                    Cancel
                </a>
                <button type="submit" id="saveCountryBtn" class="btn btn-dark px-4 fw-bold shadow-sm" style="background: #0f382a !important; border-color: #0f382a;">
                    <i class="bi bi-check2-circle me-1"></i> Save Country Details
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editCountryPageForm');
    if (form) {
        form.addEventListener('submit', function() {
            const btn = document.getElementById('saveCountryBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving Changes...';
            }
        });
    }
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
