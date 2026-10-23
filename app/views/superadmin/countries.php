<?php
use App\Core\Auth;
$pageTitle = 'International Country & Regional Market Management';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-serif mb-1" style="color: var(--color-espresso-deep);">International Markets & Jurisdictions</h1>
            <p class="text-muted small mb-0">Add, configure, and regulate international shipping zones, currencies, and statutory tax rates.</p>
        </div>
        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addCountryModal">
            <i class="bi bi-plus-lg me-1"></i> Add New Regional Market
        </button>
    </div>

    <!-- Countries Table Card -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th>Country / Region</th>
                        <th>ISO Code</th>
                        <th>Currency</th>
                        <th>Tax Structure</th>
                        <th>Shipping Policy</th>
                        <th>Return Window</th>
                        <th>Storefront Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($countries as $c): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?= country_flag($c['code'], $c['name'], 28) ?>
                                    <div>
                                        <strong class="d-block text-dark"><?= e($c['name']) ?></strong>
                                        <small class="text-muted">Dial: <?= e($c['phone_code']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-dark"><?= e($c['code']) ?></span>
                            </td>
                            <td>
                                <div><strong><?= e($c['currency_code']) ?> (<?= e($c['currency_symbol']) ?>)</strong></div>
                                <small class="text-muted"><?= e($c['currency_name']) ?></small>
                            </td>
                            <td>
                                <div><?= e($c['tax_name']) ?></div>
                                <span class="badge bg-secondary"><?= $c['tax_rate_percent'] ?>%</span>
                            </td>
                            <td>
                                <div class="small">Flat: <strong><?= e($c['currency_symbol']) . number_format($c['flat_shipping_rate'], 2) ?></strong></div>
                                <div class="small text-muted">Free Above: <?= e($c['currency_symbol']) . number_format($c['free_shipping_threshold'], 2) ?></div>
                            </td>
                            <td class="small">
                                <?= (int)$c['return_window_days'] ?> Days
                            </td>
                            <td>
                                <?php if ($c['is_active']): ?>
                                    <span class="badge bg-success">Active Market</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Deactivated</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <form action="<?= url('superadmin/countries/toggle') ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                    <input type="hidden" name="country_id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-<?= $c['is_active'] ? 'danger' : 'success' ?>" title="<?= $c['is_active'] ? 'Deactivate Market' : 'Activate Market' ?>">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add New Country with Smart Auto-Fill -->
<div class="modal fade" id="addCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
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
                            <i class="bi bi-info-circle me-1"></i> Or simply start typing the country name in the input below. All currency, tax rate, shipping fee, flag and phone codes will populate automatically!
                        </small>
                    </div>

                    <!-- Live Auto-Fill Success Notification -->
                    <div id="autofillNotice" class="alert alert-success py-2 px-3 small d-none align-items-center gap-2 mb-3 shadow-xs" role="alert">
                        <i class="bi bi-check-circle-fill text-success fs-6"></i>
                        <span id="autofillNoticeText"></span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Country Legal Name *</label>
                            <input type="text" id="countryNameInput" name="name" class="form-control" list="countryDatalist" required placeholder="Type country name (e.g. Canada, Germany, Japan...)" autocomplete="off" oninput="detectCountryAndFill(this.value)">
                            <datalist id="countryDatalist"></datalist>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">ISO 2-Letter Code *</label>
                            <input type="text" id="countryCodeInput" name="code" class="form-control text-uppercase font-monospace" maxlength="5" required placeholder="e.g. CA">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Flag Emoji or Icon *</label>
                            <input type="text" id="flagIconInput" name="flag_icon" class="form-control" required placeholder="🇨🇦, 🇩🇪">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Currency Code *</label>
                            <input type="text" id="currencyCodeInput" name="currency_code" class="form-control text-uppercase font-monospace" maxlength="5" required placeholder="CAD">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Currency Symbol *</label>
                            <input type="text" id="currencySymbolInput" name="currency_symbol" class="form-control font-monospace" required placeholder="$">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Currency Title *</label>
                            <input type="text" id="currencyNameInput" name="currency_name" class="form-control" required placeholder="Canadian Dollar">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Statutory Tax Name *</label>
                            <input type="text" id="taxNameInput" name="tax_name" class="form-control" value="VAT / Sales Tax" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Tax Rate Percentage (%) *</label>
                            <input type="number" step="0.01" id="taxRateInput" name="tax_rate_percent" class="form-control" value="5.00" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Flat Shipping Fee *</label>
                            <input type="number" step="0.01" id="flatShippingInput" name="flat_shipping_rate" class="form-control" value="20.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Free Shipping Threshold *</label>
                            <input type="number" step="0.01" id="freeShippingThresholdInput" name="free_shipping_threshold" class="form-control" value="200.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Return Window (Days) *</label>
                            <input type="number" id="returnWindowInput" name="return_window_days" class="form-control" value="30" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Phone Dial Code</label>
                            <input type="text" id="phoneCodeInput" name="phone_code" class="form-control font-monospace" placeholder="+1, +49">
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

<!-- Smart Country Data & Auto-Fill Script -->
<script>
// Existing countries in database
const EXISTING_COUNTRY_CODES = <?= json_encode(array_map('strtoupper', array_column($countries, 'code'))) ?>;
const EXISTING_COUNTRY_NAMES = <?= json_encode(array_map('strtolower', array_column($countries, 'name'))) ?>;

// Comprehensive world countries specifications database
const WORLD_COUNTRIES = [
    { name: "Canada", code: "CA", flag: "🇨🇦", currCode: "CAD", currSym: "$", currName: "Canadian Dollar", taxName: "GST / HST", taxRate: 13.00, flatShip: 25.00, freeShip: 200.00, returnDays: 30, phone: "+1" },
    { name: "Germany", code: "DE", flag: "🇩🇪", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "VAT (MwSt)", taxRate: 19.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+49" },
    { name: "France", code: "FR", flag: "🇫🇷", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "TVA", taxRate: 20.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+33" },
    { name: "Italy", code: "IT", flag: "🇮🇹", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "IVA", taxRate: 22.00, flatShip: 18.00, freeShip: 150.00, returnDays: 30, phone: "+39" },
    { name: "Spain", code: "ES", flag: "🇪🇸", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "IVA", taxRate: 21.00, flatShip: 18.00, freeShip: 150.00, returnDays: 30, phone: "+34" },
    { name: "Australia", code: "AU", flag: "🇦🇺", currCode: "AUD", currSym: "A$", currName: "Australian Dollar", taxName: "GST", taxRate: 10.00, flatShip: 30.00, freeShip: 250.00, returnDays: 30, phone: "+61" },
    { name: "Japan", code: "JP", flag: "🇯🇵", currCode: "JPY", currSym: "¥", currName: "Japanese Yen", taxName: "Consumption Tax", taxRate: 10.00, flatShip: 3000.00, freeShip: 30000.00, returnDays: 30, phone: "+81" },
    { name: "Saudi Arabia", code: "SA", flag: "🇸🇦", currCode: "SAR", currSym: "﷼", currName: "Saudi Riyal", taxName: "VAT", taxRate: 15.00, flatShip: 75.00, freeShip: 750.00, returnDays: 30, phone: "+966" },
    { name: "Qatar", code: "QA", flag: "🇶🇦", currCode: "QAR", currSym: "﷼", currName: "Qatari Riyal", taxName: "Excise / Customs Duty", taxRate: 5.00, flatShip: 75.00, freeShip: 750.00, returnDays: 30, phone: "+974" },
    { name: "Kuwait", code: "KW", flag: "🇰🇼", currCode: "KWD", currSym: "KD", currName: "Kuwaiti Dinar", taxName: "Customs Duty", taxRate: 5.00, flatShip: 10.00, freeShip: 100.00, returnDays: 30, phone: "+965" },
    { name: "Singapore", code: "SG", flag: "🇸🇬", currCode: "SGD", currSym: "S$", currName: "Singapore Dollar", taxName: "GST", taxRate: 9.00, flatShip: 25.00, freeShip: 250.00, returnDays: 30, phone: "+65" },
    { name: "Switzerland", code: "CH", flag: "🇨🇭", currCode: "CHF", currSym: "CHF", currName: "Swiss Franc", taxName: "MWST", taxRate: 8.10, flatShip: 20.00, freeShip: 200.00, returnDays: 30, phone: "+41" },
    { name: "Netherlands", code: "NL", flag: "🇳🇱", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "BTW", taxRate: 21.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+31" },
    { name: "Sweden", code: "SE", flag: "🇸🇪", currCode: "SEK", currSym: "kr", currName: "Swedish Krona", taxName: "Moms", taxRate: 25.00, flatShip: 200.00, freeShip: 2000.00, returnDays: 30, phone: "+46" },
    { name: "Norway", code: "NO", flag: "🇳🇴", currCode: "NOK", currSym: "kr", currName: "Norwegian Krone", taxName: "MVA", taxRate: 25.00, flatShip: 200.00, freeShip: 2000.00, returnDays: 30, phone: "+47" },
    { name: "Denmark", code: "DK", flag: "🇩🇰", currCode: "DKK", currSym: "kr", currName: "Danish Krone", taxName: "Moms", taxRate: 25.00, flatShip: 150.00, freeShip: 1500.00, returnDays: 30, phone: "+45" },
    { name: "Ireland", code: "IE", flag: "🇮🇪", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "VAT", taxRate: 23.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+353" },
    { name: "New Zealand", code: "NZ", flag: "🇳🇿", currCode: "NZD", currSym: "NZ$", currName: "New Zealand Dollar", taxName: "GST", taxRate: 15.00, flatShip: 35.00, freeShip: 300.00, returnDays: 30, phone: "+64" },
    { name: "South Africa", code: "ZA", flag: "🇿🇦", currCode: "ZAR", currSym: "R", currName: "South African Rand", taxName: "VAT", taxRate: 15.00, flatShip: 350.00, freeShip: 3500.00, returnDays: 30, phone: "+27" },
    { name: "Turkey", code: "TR", flag: "🇹🇷", currCode: "TRY", currSym: "₺", currName: "Turkish Lira", taxName: "KDV", taxRate: 20.00, flatShip: 500.00, freeShip: 5000.00, returnDays: 30, phone: "+90" },
    { name: "Brazil", code: "BR", flag: "🇧🇷", currCode: "BRL", currSym: "R$", currName: "Brazilian Real", taxName: "ICMS", taxRate: 17.00, flatShip: 120.00, freeShip: 1200.00, returnDays: 30, phone: "+55" },
    { name: "Mexico", code: "MX", flag: "🇲🇽", currCode: "MXN", currSym: "Mex$", currName: "Mexican Peso", taxName: "IVA", taxRate: 16.00, flatShip: 350.00, freeShip: 3500.00, returnDays: 30, phone: "+52" },
    { name: "Oman", code: "OM", flag: "🇴🇲", currCode: "OMR", currSym: "OMR", currName: "Omani Rial", taxName: "VAT", taxRate: 5.00, flatShip: 10.00, freeShip: 100.00, returnDays: 30, phone: "+968" },
    { name: "Bahrain", code: "BH", flag: "🇧🇭", currCode: "BHD", currSym: "BD", currName: "Bahraini Dinar", taxName: "VAT", taxRate: 10.00, flatShip: 10.00, freeShip: 100.00, returnDays: 30, phone: "+973" },
    { name: "Belgium", code: "BE", flag: "🇧🇪", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "TVA / BTW", taxRate: 21.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+32" },
    { name: "Austria", code: "AT", flag: "🇦🇹", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "USt", taxRate: 20.00, flatShip: 15.00, freeShip: 150.00, returnDays: 30, phone: "+43" },
    { name: "Portugal", code: "PT", flag: "🇵🇹", currCode: "EUR", currSym: "€", currName: "Euro", taxName: "IVA", taxRate: 23.00, flatShip: 18.00, freeShip: 150.00, returnDays: 30, phone: "+351" },
    { name: "Poland", code: "PL", flag: "🇵🇱", currCode: "PLN", currSym: "zł", currName: "Polish Zloty", taxName: "VAT", taxRate: 23.00, flatShip: 80.00, freeShip: 800.00, returnDays: 30, phone: "+48" },
    { name: "Malaysia", code: "MY", flag: "🇲🇾", currCode: "MYR", currSym: "RM", currName: "Malaysian Ringgit", taxName: "SST", taxRate: 10.00, flatShip: 80.00, freeShip: 800.00, returnDays: 30, phone: "+60" },
    { name: "South Korea", code: "KR", flag: "🇰🇷", currCode: "KRW", currSym: "₩", currName: "South Korean Won", taxName: "VAT", taxRate: 10.00, flatShip: 25000.00, freeShip: 250000.00, returnDays: 30, phone: "+82" },
    { name: "Hong Kong", code: "HK", flag: "🇭🇰", currCode: "HKD", currSym: "HK$", currName: "Hong Kong Dollar", taxName: "Customs / Duty-Free", taxRate: 0.00, flatShip: 150.00, freeShip: 1500.00, returnDays: 30, phone: "+852" },
    { name: "Thailand", code: "TH", flag: "🇹🇭", currCode: "THB", currSym: "฿", currName: "Thai Baht", taxName: "VAT", taxRate: 7.00, flatShip: 600.00, freeShip: 6000.00, returnDays: 30, phone: "+66" },
    { name: "Indonesia", code: "ID", flag: "🇮🇩", currCode: "IDR", currSym: "Rp", currName: "Indonesian Rupiah", taxName: "PPN", taxRate: 11.00, flatShip: 250000.00, freeShip: 2500000.00, returnDays: 30, phone: "+62" },
    { name: "Philippines", code: "PH", flag: "🇵🇭", currCode: "PHP", currSym: "₱", currName: "Philippine Peso", taxName: "VAT", taxRate: 12.00, flatShip: 1000.00, freeShip: 10000.00, returnDays: 30, phone: "+63" },
    { name: "Israel", code: "IL", flag: "🇮🇱", currCode: "ILS", currSym: "₪", currName: "Israeli Shekel", taxName: "VAT", taxRate: 17.00, flatShip: 70.00, freeShip: 700.00, returnDays: 30, phone: "+972" },
    { name: "Egypt", code: "EG", flag: "🇪🇬", currCode: "EGP", currSym: "E£", currName: "Egyptian Pound", taxName: "VAT", taxRate: 14.00, flatShip: 500.00, freeShip: 5000.00, returnDays: 30, phone: "+20" }
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

// Core function: Auto-fills all modal fields with authentic specs
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

    // Check if this country is already in catalog
    const isAlreadyPresent = EXISTING_COUNTRY_CODES.includes(data.code.toUpperCase());
    const noticeBox = document.getElementById('autofillNotice');
    const noticeText = document.getElementById('autofillNoticeText');
    const submitBtn = document.getElementById('provisionSubmitBtn');

    if (isAlreadyPresent) {
        if (noticeBox && noticeText) {
            noticeBox.className = 'alert alert-warning py-2 px-3 small d-flex align-items-center gap-2 mb-3 shadow-xs';
            noticeText.innerHTML = `<strong>⚠️ Already in Catalog:</strong> ${data.flag} ${data.name} (Code: <code>${data.code}</code>) is already added in your store! Adding it again is not needed.`;
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
            noticeText.innerHTML = `<strong>Auto-filled specifications for ${data.flag} ${data.name}:</strong> Currency: <strong>${data.currCode} (${data.currSym})</strong>, Tax: <strong>${data.taxName} (${data.taxRate}%)</strong>, Dial: <strong>${data.phone}</strong>. You can adjust any field before provisioning.`;
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


