<?php
use App\Core\Auth;
$pageTitle = 'International Country & Regional Market Management';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 font-serif mb-1" style="color: var(--color-espresso-deep);">International Markets & Regional Presences</h1>
            <p class="text-muted small mb-0">Configure regional showrooms, physical atelier addresses, phone lines, currencies, and statutory tax rates.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('contact') ?>" target="_blank" class="btn btn-outline-dark btn-sm d-inline-flex align-items-center gap-1 shadow-xs">
                <i class="bi bi-box-arrow-up-right"></i> View Storefront Contact Directory
            </a>
            <button type="button" class="btn btn-dark btn-sm d-inline-flex align-items-center gap-1 shadow-sm" style="background: #0f382a !important; border-color: #0f382a;" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                <i class="bi bi-plus-lg"></i> Provision New Regional Market
            </button>
        </div>
    </div>

    <!-- Quick Stats Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-soft h-100 p-3" style="background: #ffffff; border-left: 4px solid var(--color-espresso) !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.08em;">Total Markets</span>
                        <h3 class="fw-bold mb-0 text-dark"><?= count($countries) ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(26,26,26,0.06);">
                        <i class="bi bi-globe-americas fs-5 text-dark"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-soft h-100 p-3" style="background: #ffffff; border-left: 4px solid #16a34a !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.08em;">Active Storefronts</span>
                        <h3 class="fw-bold mb-0 text-success"><?= count(array_filter($countries, fn($c) => $c['is_active'] == 1)) ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(22,163,74,0.1);">
                        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-soft h-100 p-3" style="background: #ffffff; border-left: 4px solid #c5a880 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.08em;">Showrooms Configured</span>
                        <h3 class="fw-bold mb-0" style="color: #8c4a2f;"><?= count(array_filter($countries, fn($c) => !empty($c['address']) && !empty($c['contact_phone']))) ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(197,168,128,0.2);">
                        <i class="bi bi-building fs-5" style="color: #8c4a2f;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-soft h-100 p-3" style="background: #ffffff; border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.08em;">Address Pending</span>
                        <h3 class="fw-bold mb-0 text-warning"><?= count(array_filter($countries, fn($c) => empty($c['address']) || empty($c['contact_phone']))) ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(245,158,11,0.12);">
                        <i class="bi bi-exclamation-triangle fs-5 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Countries Table Card -->
    <div class="bg-white border rounded-3 p-3 p-md-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase" style="letter-spacing: 0.05em;">
                    <tr>
                        <th style="min-width: 170px;">Country & ISO</th>
                        <th style="min-width: 280px;">Showroom, Address & Contact Details</th>
                        <th style="min-width: 160px;">Currency & Tax</th>
                        <th style="min-width: 150px;">Shipping & Policy</th>
                        <th style="min-width: 100px;">Status</th>
                        <th class="text-end" style="min-width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($countries as $c): ?>
                        <tr>
                            <!-- Country & Flag -->
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="flex-shrink-0">
                                        <?= country_flag($c['code'], $c['name'], 30) ?>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <strong class="text-dark" style="font-size: 0.95rem;"><?= e($c['name']) ?></strong>
                                            <span class="badge bg-dark font-monospace" style="font-size: 0.68rem;"><?= e($c['code']) ?></span>
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 0.76rem;">
                                            Dial: <span class="fw-semibold text-secondary"><?= e($c['phone_code'] ?: 'N/A') ?></span>
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <!-- Physical Showroom & Contact Details -->
                            <td>
                                <?php if (!empty($c['address']) || !empty($c['contact_phone'])): ?>
                                    <div class="mb-1">
                                        <span class="badge bg-light text-dark border me-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= e($c['city'] ?: 'Regional Hub') ?>
                                        </span>
                                        <strong class="small text-dark font-serif"><?= e($c['atelier_title'] ?: 'Regional Showroom') ?></strong>
                                    </div>
                                    <?php if (!empty($c['address'])): ?>
                                        <div class="text-muted small text-truncate mb-1" style="max-width: 320px; font-size: 0.78rem;" title="<?= e($c['address']) ?>">
                                            <i class="bi bi-building me-1 text-secondary"></i><?= e($c['address']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex flex-wrap gap-2 small" style="font-size: 0.74rem;">
                                        <?php if (!empty($c['contact_phone'])): ?>
                                            <span class="text-nowrap text-dark">
                                                <i class="bi bi-telephone text-primary me-1"></i><?= e($c['contact_phone']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($c['contact_whatsapp'])): ?>
                                            <span class="text-nowrap text-success">
                                                <i class="bi bi-whatsapp me-1"></i><?= e($c['contact_whatsapp']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($c['contact_email'])): ?>
                                            <span class="text-nowrap text-secondary">
                                                <i class="bi bi-envelope me-1"></i><?= e($c['contact_email']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="py-1">
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle small fw-normal">
                                            <i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Address & Phone Pending
                                        </span>
                                        <small class="d-block text-muted mt-1" style="font-size: 0.73rem;">Click <strong>Edit Details</strong> to assign showroom address.</small>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Currency & Tax -->
                            <td>
                                <div class="fw-bold text-dark small">
                                    <?= e($c['currency_code']) ?> (<?= e($c['currency_symbol']) ?>)
                                </div>
                                <div class="text-muted small mb-1" style="font-size: 0.74rem;"><?= e($c['currency_name']) ?></div>
                                <div class="small">
                                    <span class="badge bg-secondary-subtle text-dark border small" style="font-size: 0.7rem;">
                                        <?= e($c['tax_name']) ?>: <strong><?= $c['tax_rate_percent'] ?>%</strong>
                                    </span>
                                </div>
                            </td>

                            <!-- Shipping & Policy -->
                            <td>
                                <div class="small">
                                    Flat: <strong><?= e($c['currency_symbol']) . number_format($c['flat_shipping_rate'], 2) ?></strong>
                                </div>
                                <div class="small text-muted" style="font-size: 0.76rem;">
                                    Free Over: <?= e($c['currency_symbol']) . number_format($c['free_shipping_threshold'], 2) ?>
                                </div>
                                <div class="small text-muted" style="font-size: 0.76rem;">
                                    Returns: <strong><?= (int)$c['return_window_days'] ?> Days</strong>
                                </div>
                            </td>

                            <!-- Storefront Status -->
                            <td>
                                <?php if ($c['is_active']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                        <i class="bi bi-dot"></i> Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border small">
                                        Deactivated
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1.5">
                                    <!-- Edit Country & Showroom Page Link -->
                                    <a href="<?= url('superadmin/countries/edit?id=' . $c['id']) ?>" 
                                       class="btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-1 shadow-xs fw-medium px-2.5 py-1" 
                                       title="Edit Address, Phone & Market Specs">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                        <span>Edit Details</span>
                                    </a>

                                    <!-- Toggle Active Status Form -->
                                    <form action="<?= url('superadmin/countries/toggle') ?>" method="POST" class="d-inline mb-0">
                                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                        <input type="hidden" name="country_id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-<?= $c['is_active'] ? 'danger' : 'success' ?> p-1 px-2" title="<?= $c['is_active'] ? 'Deactivate Market' : 'Activate Market' ?>">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<!-- ========================================================================= -->
<!-- MODAL: ADD NEW COUNTRY WITH SMART AUTO-FILL & SHOWROOM ADDRESS PROVISIONING -->
<!-- ========================================================================= -->
<div class="modal fade" id="addCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: #0f382a !important;">
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-5">🌍</span>
                    <h5 class="modal-title font-serif mb-0">Provision New International Market</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('superadmin/countries/create') ?>" method="POST" id="provisionCountryForm">
                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                <div class="modal-body p-4">
                    
                    <!-- Smart Auto-Fill Quick Picker Card -->
                    <div class="p-3 border rounded-3 mb-3" style="background: #f8fafc; border-color: #cbd5e1 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <label for="countryPresetDropdown" class="form-label small fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                <i class="bi bi-magic text-warning fs-6"></i> Smart Auto-Fill by Country Name:
                            </label>
                            <span class="badge bg-success-subtle text-success border border-success-subtle small fw-normal" style="font-size: 0.72rem;">
                                50+ World Economies Supported
                            </span>
                        </div>
                        <select id="countryPresetDropdown" class="form-select form-select-sm mb-1" onchange="handlePresetDropdownChange(this.value)">
                            <option value="">-- Choose Country to Auto-Fill Currency, Tax & Specs --</option>
                        </select>
                        <small class="text-muted d-block" style="font-size: 0.74rem;">
                            <i class="bi bi-info-circle me-1"></i> Or start typing country name below. All currency, tax rate, showroom address, flag and phone dial codes will populate automatically!
                        </small>
                    </div>

                    <!-- Live Auto-Fill Success Notification -->
                    <div id="autofillNotice" class="alert alert-success py-2 px-3 small d-none align-items-center gap-2 mb-3 shadow-xs" role="alert">
                        <i class="bi bi-check-circle-fill text-success fs-6"></i>
                        <span id="autofillNoticeText"></span>
                    </div>

                    <!-- Core Country Identification -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Country Legal Name *</label>
                            <input type="text" id="countryNameInput" name="name" class="form-control form-control-sm" list="countryDatalist" required placeholder="Type country name (e.g. Canada, Germany, Japan...)" autocomplete="off" oninput="detectCountryAndFill(this.value)">
                            <datalist id="countryDatalist"></datalist>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">ISO 2-Letter Code *</label>
                            <input type="text" id="countryCodeInput" name="code" class="form-control form-control-sm text-uppercase font-monospace" maxlength="5" required placeholder="e.g. CA">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Flag Emoji or Icon *</label>
                            <input type="text" id="flagIconInput" name="flag_icon" class="form-control form-control-sm" required placeholder="🇨🇦, 🇩🇪">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Currency Code *</label>
                            <input type="text" id="currencyCodeInput" name="currency_code" class="form-control form-control-sm text-uppercase font-monospace" maxlength="5" required placeholder="CAD">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Currency Symbol *</label>
                            <input type="text" id="currencySymbolInput" name="currency_symbol" class="form-control form-control-sm font-monospace" required placeholder="$">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Currency Title *</label>
                            <input type="text" id="currencyNameInput" name="currency_name" class="form-control form-control-sm" required placeholder="Canadian Dollar">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Statutory Tax Name *</label>
                            <input type="text" id="taxNameInput" name="tax_name" class="form-control form-control-sm" value="VAT / Sales Tax" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Tax Rate Percentage (%) *</label>
                            <input type="number" step="0.01" id="taxRateInput" name="tax_rate_percent" class="form-control form-control-sm" value="5.00" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Flat Shipping Fee *</label>
                            <input type="number" step="0.01" id="flatShippingInput" name="flat_shipping_rate" class="form-control form-control-sm" value="20.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Free Shipping Threshold *</label>
                            <input type="number" step="0.01" id="freeShippingThresholdInput" name="free_shipping_threshold" class="form-control form-control-sm" value="200.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Return Window (Days) *</label>
                            <input type="number" id="returnWindowInput" name="return_window_days" class="form-control form-control-sm" value="30" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Phone Dial Code</label>
                            <input type="text" id="phoneCodeInput" name="phone_code" class="form-control form-control-sm font-monospace" placeholder="+1, +49">
                        </div>
                    </div>

                    <!-- Showroom & Regional Contact Details Section -->
                    <div class="border rounded-3 p-3 bg-white" style="border-color: #cbd5e1 !important;">
                        <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <h6 class="fw-bold text-dark font-serif mb-0" style="font-size: 0.9rem;">Regional Showroom & Address Presence (Auto-filled / Customizable)</h6>
                        </div>
                        <div class="row g-2.5">
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-dark mb-1">Showroom Title</label>
                                <input type="text" id="atelierTitleInput" name="atelier_title" class="form-control form-control-sm" placeholder="e.g. Yorkville Private Client Suites">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-dark mb-1">City / Hub</label>
                                <input type="text" id="cityInput" name="city" class="form-control form-control-sm" placeholder="e.g. Toronto">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark mb-1">Physical Street Address</label>
                                <textarea id="addressInput" name="address" rows="2" class="form-control form-control-sm" placeholder="e.g. 130 Bloor Street West, Suite 800, Toronto, ON M5S 1N5"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">Customer Support Phone</label>
                                <input type="text" id="contactPhoneInput" name="contact_phone" class="form-control form-control-sm" placeholder="+1 (800) 285-5371">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">WhatsApp Concierge</label>
                                <input type="text" id="contactWhatsappInput" name="contact_whatsapp" class="form-control form-control-sm" placeholder="+1 (212) 555-0194">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">Support Email</label>
                                <input type="email" id="contactEmailInput" name="contact_email" class="form-control form-control-sm" placeholder="support.ca@bullerleather.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark mb-1">Operating Hours</label>
                                <input type="text" id="supportHoursInput" name="support_hours" class="form-control form-control-sm" placeholder="Monday – Friday: 9:00 AM – 6:00 PM Local Time">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark mb-1">Courier Logistics Partner</label>
                                <input type="text" id="shippingPartnerInput" name="shipping_partner" class="form-control form-control-sm" placeholder="Canada Post & FedEx Priority">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                    <small class="text-muted" style="font-size: 0.75rem;">
                        <i class="bi bi-check2-circle text-success me-1"></i> Automatically initializes local pricing across all master products.
                    </small>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="provisionSubmitBtn" class="btn btn-dark btn-sm px-4 fw-bold shadow-sm" style="background: #0f382a !important; border-color: #0f382a;">
                            <i class="bi bi-plus-circle me-1"></i> Provision Regional Market
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT: SMART AUTO-FILL & MODAL DATA POPULATION -->
<!-- ========================================================================= -->
<script>
// Existing countries in database
const EXISTING_COUNTRY_CODES = <?= json_encode(array_map('strtoupper', array_column($countries, 'code'))) ?>;
const EXISTING_COUNTRY_NAMES = <?= json_encode(array_map('strtolower', array_column($countries, 'name'))) ?>;

// Comprehensive world countries specifications database with realistic localized showroom details
const WORLD_COUNTRIES = [
    { 
        name: "Canada", code: "CA", flag: "🇨🇦", currCode: "CAD", currSym: "$", currName: "Canadian Dollar", taxName: "GST / HST", taxRate: 13.00, flatShip: 25.00, freeShip: 200.00, returnDays: 30, phone: "+1",
        city: "Toronto", atelierTitle: "Yorkville Private Client Suites", address: "130 Bloor Street West, Suite 800, Yorkville, Toronto, ON M5S 1N5", contactPhone: "+1 (800) 285-5371", contactWhatsapp: "+1 (416) 555-0182", contactEmail: "support.ca@bullerleather.com", supportHours: "Monday – Friday: 9:00 AM – 6:00 PM EST", shippingPartner: "Canada Post Xpresspost & FedEx Priority"
    },
    { 
        name: "Germany", code: "DE", flag: "🇩🇪", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "VAT (MwSt)", taxRate: 19.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+49",
        city: "Munich", atelierTitle: "Maximilianstraße Heritage Atelier", address: "Maximilianstraße 35, 80539 München, Germany", contactPhone: "+49 89 2102 9300", contactWhatsapp: "+49 171 555 0142", contactEmail: "support.de@bullerleather.com", supportHours: "Montag – Freitag: 10:00 – 19:00 CET", shippingPartner: "DHL Express Germany & DPD Priority"
    },
    { 
        name: "France", code: "FR", flag: "🇫🇷", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "TVA", taxRate: 20.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+33",
        city: "Paris", atelierTitle: "Place Vendôme Private Salon", address: "15 Place Vendôme, 75001 Paris, France", contactPhone: "+33 1 42 68 55 00", contactWhatsapp: "+33 6 12 34 56 78", contactEmail: "support.fr@bullerleather.com", supportHours: "Lundi – Vendredi: 10:00 – 19:00 CET", shippingPartner: "Chronopost & Colissimo Expert"
    },
    { 
        name: "Italy", code: "IT", flag: "🇮🇹", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "IVA", taxRate: 22.00, flatShip: 18.00, freeShip: 150.00, returnDays: 30, phone: "+39",
        city: "Milan", atelierTitle: "Via Montenapoleone Showroom", address: "Via Montenapoleone 8, 20121 Milano, Italy", contactPhone: "+39 02 8739 1200", contactWhatsapp: "+39 335 123 4567", contactEmail: "support.it@bullerleather.com", supportHours: "Lunedì – Venerdì: 10:00 – 19:30 CET", shippingPartner: "DHL Express Italy & Poste Italiane Crono"
    },
    { 
        name: "Spain", code: "ES", flag: "🇪🇸", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "IVA", taxRate: 21.00, flatShip: 18.00, freeShip: 150.00, returnDays: 30, phone: "+34",
        city: "Madrid", atelierTitle: "Salamanca Heritage Atelier", address: "Calle de Serrano 42, Barrio de Salamanca, 28001 Madrid, Spain", contactPhone: "+34 91 582 3100", contactWhatsapp: "+34 600 123 456", contactEmail: "support.es@bullerleather.com", supportHours: "Lunes – Viernes: 10:00 – 20:00 CET", shippingPartner: "Correos Express & SEUR Courier"
    },
    { 
        name: "Australia", code: "AU", flag: "🇦🇺", currCode: "AUD", currSym: "A$", currName: "Australian Dollar", taxName: "GST", taxRate: 10.00, flatShip: 30.00, freeShip: 250.00, returnDays: 30, phone: "+61",
        city: "Sydney", atelierTitle: "Castlereagh Private Suite", address: "Level 14, 160 Castlereagh Street, Sydney NSW 2000, Australia", contactPhone: "+61 2 9234 5600", contactWhatsapp: "+61 412 345 678", contactEmail: "support.au@bullerleather.com", supportHours: "Monday – Friday: 9:00 AM – 5:30 PM AEST", shippingPartner: "Australia Post Express & StarTrack Priority"
    },
    { 
        name: "Japan", code: "JP", flag: "🇯🇵", currCode: "JPY", currSym: "¥", currName: "Japanese Yen", taxName: "Consumption Tax", taxRate: 10.00, flatShip: 3000.00, freeShip: 30000.00, returnDays: 30, phone: "+81",
        city: "Tokyo", atelierTitle: "Ginza Six Bespoke Salon", address: "6-10-1 Ginza, Chuo-ku, Tokyo 104-0061, Japan", contactPhone: "+81 3 5537 2100", contactWhatsapp: "+81 90 1234 5678", contactEmail: "support.jp@bullerleather.com", supportHours: "Monday – Saturday: 11:00 AM – 8:00 PM JST", shippingPartner: "Yamato Transport (Black Cat) & Sagawa"
    },
    { 
        name: "Saudi Arabia", code: "SA", flag: "🇸🇦", currCode: "SAR", currSym: "﷼", currName: "Saudi Riyal", taxName: "VAT", taxRate: 15.00, flatShip: 75.00, freeShip: 750.00, returnDays: 30, phone: "+966",
        city: "Riyadh", atelierTitle: "Olaya Towers Private VIP Suite", address: "Olaya Street, Al Olaya District, Riyadh 12213, Kingdom of Saudi Arabia", contactPhone: "+966 11 480 9100", contactWhatsapp: "+966 50 123 4567", contactEmail: "support.sa@bullerleather.com", supportHours: "Sunday – Thursday: 10:00 AM – 9:00 PM AST", shippingPartner: "SMSA Express & DHL Express Gulf"
    },
    { 
        name: "Qatar", code: "QA", flag: "🇶🇦", currCode: "QAR", currSym: "﷼", currName: "Qatari Riyal", taxName: "Excise / Customs Duty", taxRate: 5.00, flatShip: 75.00, freeShip: 750.00, returnDays: 30, phone: "+974",
        city: "Doha", atelierTitle: "The Pearl-Qatar Luxury Pavilion", address: "Porto Arabia Boardwalk, Tower 22, The Pearl, Doha, Qatar", contactPhone: "+974 4492 1100", contactWhatsapp: "+974 5512 3456", contactEmail: "support.qa@bullerleather.com", supportHours: "Sunday – Thursday: 10:00 AM – 9:00 PM AST", shippingPartner: "Q-Post Express & DHL Priority"
    },
    { 
        name: "Kuwait", code: "KW", flag: "🇰🇼", currCode: "KWD", currSym: "KD", currName: "Kuwaiti Dinar", taxName: "Customs Duty", taxRate: 5.00, flatShip: 10.00, freeShip: 100.00, returnDays: 30, phone: "+965",
        city: "Kuwait City", atelierTitle: "Arraya Centre VIP Lounge", address: "Al Shuhada Street, Sharq, Kuwait City 13008, Kuwait", contactPhone: "+965 2299 7700", contactWhatsapp: "+965 9912 3456", contactEmail: "support.kw@bullerleather.com", supportHours: "Sunday – Thursday: 9:30 AM – 8:30 PM AST", shippingPartner: "DHL Express Kuwait & Aramex Priority"
    },
    { 
        name: "Bahrain", code: "BH", flag: "🇧🇭", currCode: "BHD", currSym: "BD", currName: "Bahraini Dinar", taxName: "VAT", taxRate: 10.00, flatShip: 10.00, freeShip: 100.00, returnDays: 30, phone: "+973",
        city: "Manama", atelierTitle: "Moda Mall Diplomatic Lounge", address: "Bahrain World Trade Center, King Faisal Highway, Manama 316, Kingdom of Bahrain", contactPhone: "+973 1753 2200", contactWhatsapp: "+973 3912 3456", contactEmail: "support.bh@bullerleather.com", supportHours: "Sunday – Thursday: 10:00 AM – 8:00 PM AST", shippingPartner: "DHL Express Bahrain & Aramex"
    },
    { 
        name: "Singapore", code: "SG", flag: "🇸🇬", currCode: "SGD", currSym: "S$", currName: "Singapore Dollar", taxName: "GST", taxRate: 9.00, flatShip: 25.00, freeShip: 250.00, returnDays: 30, phone: "+65",
        city: "Singapore", atelierTitle: "Marina Bay Sands Suite", address: "10 Bayfront Avenue, Marina Bay Sands The Shoppes, Singapore 018956", contactPhone: "+65 6688 8868", contactWhatsapp: "+65 9123 4567", contactEmail: "support.sg@bullerleather.com", supportHours: "Monday – Sunday: 10:30 AM – 8:30 PM SGT", shippingPartner: "Singapore Post Speedpost & DHL Express"
    },
    { 
        name: "Switzerland", code: "CH", flag: "🇨🇭", currCode: "CHF", currSym: "CHF", currName: "Swiss Franc", taxName: "MWST", taxRate: 8.10, flatShip: 20.00, freeShip: 200.00, returnDays: 30, phone: "+41",
        city: "Zurich", atelierTitle: "Bahnhofstrasse Private Atelier", address: "Bahnhofstrasse 45, 8001 Zürich, Switzerland", contactPhone: "+41 44 215 3200", contactWhatsapp: "+41 79 123 4567", contactEmail: "support.ch@bullerleather.com", supportHours: "Montag – Freitag: 9:30 – 18:30 CET", shippingPartner: "Swiss Post Priority & DHL Express"
    },
    { 
        name: "Netherlands", code: "NL", flag: "🇳🇱", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "BTW", taxRate: 21.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+31",
        city: "Amsterdam", atelierTitle: "P.C. Hooftstraat Showroom", address: "P.C. Hooftstraat 82, 1071 CB Amsterdam, Netherlands", contactPhone: "+31 20 573 8100", contactWhatsapp: "+31 6 1234 5678", contactEmail: "support.nl@bullerleather.com", supportHours: "Maandag – Vrijdag: 10:00 – 18:30 CET", shippingPartner: "PostNL Priority & DHL Parcel"
    },
    { 
        name: "Sweden", code: "SE", flag: "🇸🇪", currCode: "SEK", currSym: "kr", currName: "Swedish Krona", taxName: "Moms", taxRate: 25.00, flatShip: 200.00, freeShip: 2000.00, returnDays: 30, phone: "+46",
        city: "Stockholm", atelierTitle: "Biblioteksgatan Salon", address: "Biblioteksgatan 12, 111 46 Stockholm, Sweden", contactPhone: "+46 8 506 2100", contactWhatsapp: "+46 70 123 4567", contactEmail: "support.se@bullerleather.com", supportHours: "Måndag – Fredag: 10:00 – 18:00 CET", shippingPartner: "PostNord Express & DHL Sweden"
    },
    { 
        name: "Norway", code: "NO", flag: "🇳🇴", currCode: "NOK", currSym: "kr", currName: "Norwegian Krone", taxName: "MVA", taxRate: 25.00, flatShip: 200.00, freeShip: 2000.00, returnDays: 30, phone: "+47",
        city: "Oslo", atelierTitle: "Nedre Slottsgate Atelier", address: "Nedre Slottsgate 15, 0157 Oslo, Norway", contactPhone: "+47 22 41 8200", contactWhatsapp: "+47 90 123 456", contactEmail: "support.no@bullerleather.com", supportHours: "Mandag – Fredag: 10:00 – 18:00 CET", shippingPartner: "Posten Bring & DHL Express"
    },
    { 
        name: "Denmark", code: "DK", flag: "🇩🇰", currCode: "DKK", currSym: "kr", currName: "Danish Krone", taxName: "Moms", taxRate: 25.00, flatShip: 150.00, freeShip: 1500.00, returnDays: 30, phone: "+45",
        city: "Copenhagen", atelierTitle: "Strøget Luxury Suite", address: "Østergade 24, 1100 København K, Denmark", contactPhone: "+45 33 12 4500", contactWhatsapp: "+45 20 123 456", contactEmail: "support.dk@bullerleather.com", supportHours: "Mandag – Fredag: 10:00 – 18:00 CET", shippingPartner: "PostNord Danmark & GLS Priority"
    },
    { 
        name: "Ireland", code: "IE", flag: "🇮🇪", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "VAT", taxRate: 23.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+353",
        city: "Dublin", atelierTitle: "Grafton Street Suites", address: "88 Grafton Street, Dublin 2, D02 F658, Ireland", contactPhone: "+353 1 677 8200", contactWhatsapp: "+353 87 123 4567", contactEmail: "support.ie@bullerleather.com", supportHours: "Monday – Friday: 9:30 AM – 6:00 PM GMT", shippingPartner: "An Post Express & DPD Ireland"
    },
    { 
        name: "New Zealand", code: "NZ", flag: "🇳🇿", currCode: "NZD", currSym: "NZ$", currName: "New Zealand Dollar", taxName: "GST", taxRate: 15.00, flatShip: 35.00, freeShip: 300.00, returnDays: 30, phone: "+64",
        city: "Auckland", atelierTitle: "Queen Street Private Showroom", address: "Level 11, 209 Queen Street, Auckland 1010, New Zealand", contactPhone: "+64 9 309 8100", contactWhatsapp: "+64 21 123 4567", contactEmail: "support.nz@bullerleather.com", supportHours: "Monday – Friday: 9:00 AM – 5:30 PM NZST", shippingPartner: "NZ Post CourierPost & DHL Express"
    },
    { 
        name: "South Africa", code: "ZA", flag: "🇿🇦", currCode: "ZAR", currSym: "R", currName: "South African Rand", taxName: "VAT", taxRate: 15.00, flatShip: 350.00, freeShip: 3500.00, returnDays: 30, phone: "+27",
        city: "Cape Town", atelierTitle: "V&A Waterfront Showroom", address: "Victoria Wharf, V&A Waterfront, Cape Town 8001, South Africa", contactPhone: "+27 21 418 2900", contactWhatsapp: "+27 82 123 4567", contactEmail: "support.za@bullerleather.com", supportHours: "Monday – Saturday: 9:00 AM – 7:00 PM SAST", shippingPartner: "RAM Hand-to-Hand Couriers & DHL"
    },
    { 
        name: "Turkey", code: "TR", flag: "🇹🇷", currCode: "TRY", currSym: "₺", currName: "Turkish Lira", taxName: "KDV", taxRate: 20.00, flatShip: 500.00, freeShip: 5000.00, returnDays: 30, phone: "+90",
        city: "Istanbul", atelierTitle: "Nişantaşı Heritage Atelier", address: "Abdi İpekçi Caddesi 44, Nişantaşı, Şişli, 34367 İstanbul, Turkey", contactPhone: "+90 212 231 4400", contactWhatsapp: "+90 532 123 4567", contactEmail: "support.tr@bullerleather.com", supportHours: "Monday – Saturday: 10:00 AM – 7:30 PM TRT", shippingPartner: "Yurtiçi Kargo & Aramex Express"
    },
    { 
        name: "Brazil", code: "BR", flag: "🇧🇷", currCode: "BRL", currSym: "R$", currName: "Brazilian Real", taxName: "ICMS", taxRate: 17.00, flatShip: 120.00, freeShip: 1200.00, returnDays: 30, phone: "+55",
        city: "São Paulo", atelierTitle: "Jardins Private Suites", address: "Rua Oscar Freire 920, Jardins, São Paulo - SP, 01426-000, Brazil", contactPhone: "+55 11 3064 8100", contactWhatsapp: "+55 11 98765 4321", contactEmail: "support.br@bullerleather.com", supportHours: "Segunda – Sexta: 10:00 – 19:00 BRT", shippingPartner: "Correios Sedex & DHL Express"
    },
    { 
        name: "Mexico", code: "MX", flag: "🇲🇽", currCode: "MXN", currSym: "Mex$", currName: "Mexican Peso", taxName: "IVA", taxRate: 16.00, flatShip: 350.00, freeShip: 3500.00, returnDays: 30, phone: "+52",
        city: "Mexico City", atelierTitle: "Polanco Luxury Salon", address: "Avenida Presidente Masaryk 360, Polanco, CDMX 11560, Mexico", contactPhone: "+52 55 5280 4300", contactWhatsapp: "+52 55 1234 5678", contactEmail: "support.mx@bullerleather.com", supportHours: "Lunes – Viernes: 10:00 – 19:30 CST", shippingPartner: "Estafeta Express & DHL Express Mexico"
    },
    { 
        name: "Oman", code: "OM", flag: "🇴🇲", currCode: "OMR", currSym: "OMR", currName: "Omani Rial", taxName: "VAT", taxRate: 5.00, flatShip: 10.00, freeShip: 100.00, returnDays: 30, phone: "+968",
        city: "Muscat", atelierTitle: "Opera Galleria Private Lounge", address: "Royal Opera House Muscat, Shatti Al Qurum, Muscat 112, Sultanate of Oman", contactPhone: "+968 2440 3300", contactWhatsapp: "+968 9912 3456", contactEmail: "support.om@bullerleather.com", supportHours: "Sunday – Thursday: 10:00 AM – 8:00 PM GST", shippingPartner: "Oman Post & DHL Express"
    },
    { 
        name: "Belgium", code: "BE", flag: "🇧🇪", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "TVA / BTW", taxRate: 21.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+32",
        city: "Brussels", atelierTitle: "Boulevard de Waterloo Salon", address: "Boulevard de Waterloo 28, 1000 Bruxelles, Belgium", contactPhone: "+32 2 512 4300", contactWhatsapp: "+32 470 123 456", contactEmail: "support.be@bullerleather.com", supportHours: "Lundi – Samedi: 10:00 – 18:30 CET", shippingPartner: "bpost Priority & DHL Express"
    },
    { 
        name: "Austria", code: "AT", flag: "🇦🇹", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "USt", taxRate: 20.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+43",
        city: "Vienna", atelierTitle: "Graben Historic Atelier", address: "Graben 19, 1010 Wien, Austria", contactPhone: "+43 1 533 8100", contactWhatsapp: "+43 664 123 4567", contactEmail: "support.at@bullerleather.com", supportHours: "Montag – Freitag: 10:00 – 19:00 CET", shippingPartner: "Österreichische Post & DHL Express"
    },
    { 
        name: "Portugal", code: "PT", flag: "🇵🇹", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "IVA", taxRate: 23.00, flatShip: 18.00, freeShip: 150.00, returnDays: 30, phone: "+351",
        city: "Lisbon", atelierTitle: "Avenida da Liberdade Showroom", address: "Avenida da Liberdade 180, 1250-146 Lisboa, Portugal", contactPhone: "+351 21 347 8200", contactWhatsapp: "+351 912 345 678", contactEmail: "support.pt@bullerleather.com", supportHours: "Segunda – Sexta: 10:00 – 19:00 WET", shippingPartner: "CTT Expresso & Chronopost Portugal"
    },
    { 
        name: "Poland", code: "PL", flag: "🇵🇱", currCode: "PLN", currSym: "zł", currName: "Polish Zloty", taxName: "VAT", taxRate: 23.00, flatShip: 80.00, freeShip: 800.00, returnDays: 30, phone: "+48",
        city: "Warsaw", atelierTitle: "Nowy Świat Private Suite", address: "Nowy Świat 41, 00-042 Warszawa, Poland", contactPhone: "+48 22 826 3100", contactWhatsapp: "+48 501 234 567", contactEmail: "support.pl@bullerleather.com", supportHours: "Poniedziałek – Piątek: 10:00 – 18:30 CET", shippingPartner: "InPost Kurier & DPD Polska"
    },
    { 
        name: "Malaysia", code: "MY", flag: "🇲🇾", currCode: "MYR", currSym: "RM", currName: "Malaysian Ringgit", taxName: "SST", taxRate: 10.00, flatShip: 80.00, freeShip: 800.00, returnDays: 30, phone: "+60",
        city: "Kuala Lumpur", atelierTitle: "Pavilion Kuala Lumpur Suite", address: "168 Jalan Bukit Bintang, Bukit Bintang, 55100 Kuala Lumpur, Malaysia", contactPhone: "+60 3 2141 8200", contactWhatsapp: "+60 12 345 6789", contactEmail: "support.my@bullerleather.com", supportHours: "Monday – Sunday: 10:00 AM – 9:00 PM MYT", shippingPartner: "Pos Laju & DHL Express Malaysia"
    },
    { 
        name: "South Korea", code: "KR", flag: "🇰🇷", currCode: "KRW", currSym: "₩", currName: "South Korean Won", taxName: "VAT", taxRate: 10.00, flatShip: 25000.00, freeShip: 250000.00, returnDays: 30, phone: "+82",
        city: "Seoul", atelierTitle: "Cheongdam-dong Luxury Salon", address: "Apgujeong-ro 432, Gangnam-gu, Seoul 06015, South Korea", contactPhone: "+82 2 541 3300", contactWhatsapp: "+82 10 1234 5678", contactEmail: "support.kr@bullerleather.com", supportHours: "Monday – Saturday: 11:00 AM – 8:00 PM KST", shippingPartner: "CJ Logistics & Korea Post EMS"
    },
    { 
        name: "Hong Kong", code: "HK", flag: "🇭🇰", currCode: "HKD", currSym: "HK$", currName: "Hong Kong Dollar", taxName: "Customs / Duty-Free", taxRate: 0.00, flatShip: 150.00, freeShip: 1500.00, returnDays: 30, phone: "+852",
        city: "Hong Kong", atelierTitle: "Landmark Central VIP Suite", address: "15 Queen's Road Central, Central, Hong Kong", contactPhone: "+852 2845 2100", contactWhatsapp: "+852 9123 4567", contactEmail: "support.hk@bullerleather.com", supportHours: "Monday – Sunday: 10:30 AM – 8:00 PM HKT", shippingPartner: "SF Express & DHL Express HK"
    },
    { 
        name: "Thailand", code: "TH", flag: "🇹🇭", currCode: "THB", currSym: "฿", currName: "Thai Baht", taxName: "VAT", taxRate: 7.00, flatShip: 600.00, freeShip: 6000.00, returnDays: 30, phone: "+66",
        city: "Bangkok", atelierTitle: "Siam Paragon Private Lounge", address: "991 Rama 1 Road, Pathum Wan, Bangkok 10330, Thailand", contactPhone: "+66 2 610 8200", contactWhatsapp: "+66 81 234 5678", contactEmail: "support.th@bullerleather.com", supportHours: "Monday – Sunday: 10:00 AM – 8:30 PM ICT", shippingPartner: "Kerry Express & Thailand Post EMS"
    },
    { 
        name: "Indonesia", code: "ID", flag: "🇮🇩", currCode: "IDR", currSym: "Rp", currName: "Indonesian Rupiah", taxName: "PPN", taxRate: 11.00, flatShip: 250000.00, freeShip: 2500000.00, returnDays: 30, phone: "+62",
        city: "Jakarta", atelierTitle: "Plaza Indonesia VIP Suite", address: "Jl. M.H. Thamrin No.28-30, Gondangdia, Menteng, Jakarta Pusat 10350, Indonesia", contactPhone: "+62 21 310 7500", contactWhatsapp: "+62 811 234 5678", contactEmail: "support.id@bullerleather.com", supportHours: "Monday – Sunday: 10:00 AM – 8:00 PM WIB", shippingPartner: "JNE Express & SiCepat Priority"
    },
    { 
        name: "Philippines", code: "PH", flag: "🇵🇭", currCode: "PHP", currSym: "₱", currName: "Philippine Peso", taxName: "VAT", taxRate: 12.00, flatShip: 1000.00, freeShip: 10000.00, returnDays: 30, phone: "+63",
        city: "Manila", atelierTitle: "Bonifacio High Street Salon", address: "7th Avenue, Bonifacio Global City, Taguig, 1634 Metro Manila, Philippines", contactPhone: "+63 2 8856 2100", contactWhatsapp: "+63 917 123 4567", contactEmail: "support.ph@bullerleather.com", supportHours: "Monday – Saturday: 10:00 AM – 8:00 PM PHT", shippingPartner: "LBC Express & DHL Express PH"
    },
    { 
        name: "Israel", code: "IL", flag: "🇮🇱", currCode: "ILS", currSym: "₪", currName: "Israeli Shekel", taxName: "VAT", taxRate: 17.00, flatShip: 70.00, freeShip: 700.00, returnDays: 30, phone: "+972",
        city: "Tel Aviv", atelierTitle: "Rothschild Boulevard Atelier", address: "Rothschild Boulevard 45, Tel Aviv-Yafo 6578401, Israel", contactPhone: "+972 3 560 8200", contactWhatsapp: "+972 50 123 4567", contactEmail: "support.il@bullerleather.com", supportHours: "Sunday – Thursday: 9:30 AM – 7:30 PM IDT", shippingPartner: "Israel Post Express & DHL Express"
    },
    { 
        name: "Egypt", code: "EG", flag: "🇪🇬", currCode: "EGP", currSym: "E£", currName: "Egyptian Pound", taxName: "VAT", taxRate: 14.00, flatShip: 500.00, freeShip: 5000.00, returnDays: 30, phone: "+20",
        city: "Cairo", atelierTitle: "Zamalek Island Private Salon", address: "15 Hassan Sabry Street, Zamalek, Cairo 11211, Egypt", contactPhone: "+20 2 2735 9100", contactWhatsapp: "+20 10 1234 5678", contactEmail: "support.eg@bullerleather.com", supportHours: "Sunday – Thursday: 10:00 AM – 8:00 PM EET", shippingPartner: "Egypt Post Express & Aramex Egypt"
    }
];



// Initialize Datalist and Quick Preset Dropdown on Page Load
document.addEventListener('DOMContentLoaded', function() {
    const datalist = document.getElementById('countryDatalist');
    const dropdown = document.getElementById('countryPresetDropdown');

    if (datalist && dropdown) {
        // Sort alphabetically
        WORLD_COUNTRIES.sort((a, b) => a.name.localeCompare(b.name));

        WORLD_COUNTRIES.forEach(item => {
            const isAlreadyAdded = EXISTING_COUNTRY_CODES.includes(item.code.toUpperCase());
            const statusLabel = isAlreadyAdded ? " (Already in Store)" : "";

            // Populate datalist options
            const opt = document.createElement('option');
            opt.value = item.name;
            opt.label = `${item.flag} ${item.currCode} (${item.currSym})${statusLabel}`;
            datalist.appendChild(opt);

            // Populate quick preset select
            const dropOpt = document.createElement('option');
            dropOpt.value = item.name;
            dropOpt.textContent = `${item.flag} ${item.name} (${item.currCode} - ${item.currSym})${statusLabel}`;
            if (isAlreadyAdded) {
                dropOpt.style.color = '#94a3b8';
            }
            dropdown.appendChild(dropOpt);
        });
    }

    // Double-click form submission prevention
    const form = document.getElementById('provisionCountryForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const code = (document.getElementById('countryCodeInput').value || '').trim().toUpperCase();
            if (EXISTING_COUNTRY_CODES.includes(code)) {
                e.preventDefault();
                alert(`The country market with ISO code '${code}' is already added in your store!`);
                return false;
            }
            const btn = document.getElementById('provisionSubmitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Provisioning Market...';
            }
        });
    }
});

// Auto-fill logic when user selects from Quick Preset dropdown
function handlePresetDropdownChange(countryName) {
    if (!countryName) return;
    const nameInput = document.getElementById('countryNameInput');
    if (nameInput) {
        nameInput.value = countryName;
    }
    applyCountrySpecs(countryName);
}

// Auto-detect when user types country name in the input field
function detectCountryAndFill(typedVal) {
    const clean = typedVal.trim().toLowerCase();
    if (!clean) return;

    // 1. Exact match or code match
    const found = WORLD_COUNTRIES.find(c => 
        c.name.toLowerCase() === clean || 
        c.code.toLowerCase() === clean
    );

    if (found) {
        applyCountrySpecs(found.name);
        return;
    }

    // 2. StartsWith match if length >= 4 (e.g. "cana" -> Canada)
    if (clean.length >= 4) {
        const partial = WORLD_COUNTRIES.find(c => c.name.toLowerCase().startsWith(clean));
        if (partial) {
            applyCountrySpecs(partial.name, false); // fill specs without overriding typed name yet
        }
    }
}

// Core function: Auto-fills all modal fields with authentic specs & showroom details
function applyCountrySpecs(countryName, syncNameInput = true) {
    const data = WORLD_COUNTRIES.find(c => c.name.toLowerCase() === countryName.toLowerCase());
    if (!data) return;

    if (syncNameInput) {
        const nameInput = document.getElementById('countryNameInput');
        if (nameInput) nameInput.value = data.name;
    }

    // Auto-fill all fields
    const codeInp = document.getElementById('countryCodeInput');
    const flagInp = document.getElementById('flagIconInput');
    const currCodeInp = document.getElementById('currencyCodeInput');
    const currSymInp = document.getElementById('currencySymbolInput');
    const currNameInp = document.getElementById('currencyNameInput');
    const taxNameInp = document.getElementById('taxNameInput');
    const taxRateInp = document.getElementById('taxRateInput');
    const flatShipInp = document.getElementById('flatShippingInput');
    const freeThreshInp = document.getElementById('freeShippingThresholdInput');
    const returnWinInp = document.getElementById('returnWindowInput');
    const phoneInp = document.getElementById('phoneCodeInput');

    // Showroom & contact fields in add modal
    const atelierTitleInp = document.getElementById('atelierTitleInput');
    const cityInp = document.getElementById('cityInput');
    const addressInp = document.getElementById('addressInput');
    const contactPhoneInp = document.getElementById('contactPhoneInput');
    const contactWhatsappInp = document.getElementById('contactWhatsappInput');
    const contactEmailInp = document.getElementById('contactEmailInput');
    const supportHoursInp = document.getElementById('supportHoursInput');
    const shippingPartnerInp = document.getElementById('shippingPartnerInput');

    if (codeInp) codeInp.value = data.code;
    if (flagInp) flagInp.value = data.flag;
    if (currCodeInp) currCodeInp.value = data.currCode;
    if (currSymInp) currSymInp.value = data.currSym;
    if (currNameInp) currNameInp.value = data.currName;
    if (taxNameInp) taxNameInp.value = data.taxName;
    if (taxRateInp) taxRateInp.value = data.taxRate.toFixed(2);
    if (flatShipInp) flatShipInp.value = data.flatShip.toFixed(2);
    if (freeThreshInp) freeThreshInp.value = data.freeShip.toFixed(2);
    if (returnWinInp) returnWinInp.value = data.returnDays;
    if (phoneInp) phoneInp.value = data.phone;

    // Contact & Showroom auto-fills
    if (atelierTitleInp) atelierTitleInp.value = data.atelierTitle || (data.name + ' Showroom & Client Suites');
    if (cityInp) cityInp.value = data.city || '';
    if (addressInp) addressInp.value = data.address || '';
    if (contactPhoneInp) contactPhoneInp.value = data.contactPhone || (data.phone + ' Support Line');
    if (contactWhatsappInp) contactWhatsappInp.value = data.contactWhatsapp || '';
    if (contactEmailInp) contactEmailInp.value = data.contactEmail || ('support.' + data.code.toLowerCase() + '@bullerleather.com');
    if (supportHoursInp) supportHoursInp.value = data.supportHours || 'Monday – Friday: 9:00 AM – 6:00 PM Local Time';
    if (shippingPartnerInp) shippingPartnerInp.value = data.shippingPartner || 'DHL Express Priority Courier';

    // Check if this country is already in catalog
    const isAlreadyPresent = EXISTING_COUNTRY_CODES.includes(data.code.toUpperCase());
    const noticeBox = document.getElementById('autofillNotice');
    const noticeText = document.getElementById('autofillNoticeText');
    const submitBtn = document.getElementById('provisionSubmitBtn');

    if (isAlreadyPresent) {
        if (noticeBox && noticeText) {
            noticeBox.className = 'alert alert-warning py-2 px-3 small d-flex align-items-center gap-2 mb-3 shadow-xs';
            noticeText.innerHTML = `<strong>⚠️ Already in Catalog:</strong> ${data.flag} ${data.name} (Code: <code>${data.code}</code>) is already added in your store! You can edit its address and phone details via the <strong>Edit Details</strong> button.`;
            noticeBox.classList.remove('d-none');
        }
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.65';
            submitBtn.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Market Already in Store';
        }
    } else {
        if (noticeBox && noticeText) {
            noticeBox.className = 'alert alert-success py-2 px-3 small d-flex align-items-center gap-2 mb-3 shadow-xs';
            noticeText.innerHTML = `<strong>Auto-filled specifications for ${data.flag} ${data.name}:</strong> Currency: <strong>${data.currCode} (${data.currSym})</strong>, Showroom City: <strong>${data.city || 'Hub'}</strong>, Tax: <strong>${data.taxName} (${data.taxRate}%)</strong>. All address and phone fields pre-configured!`;
            noticeBox.classList.remove('d-none');
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Provision Regional Market';
        }
    }
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
