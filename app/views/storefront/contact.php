<?php
use App\Core\Auth;
use App\Core\MarketContext;

$pageTitle = 'Atelier Concierge & Regional Appointments | Buller Leather';
$pageDescription = 'Connect directly with Buller Leather regional ateliers across London, New York, Mumbai, and Dubai for bespoke fitting, corporate commissions, and client support.';

// Get active shopping market
$currentMarket = MarketContext::getCountry();
$selectedCountryCode = strtoupper(trim($_GET['country'] ?? $currentMarket['code'] ?? 'IN'));

// Regional Atelier Directory Configuration
$regionalAteliers = [
    'IN' => [
        'name' => 'India',
        'code' => 'IN',
        'flag' => 'in',
        'city' => 'Mumbai',
        'atelier_title' => 'The Colaba Heritage Atelier & Guild',
        'address' => 'Heritage Wing, 14 B.K. Boman Behram Marg, Colaba, Mumbai, Maharashtra 400001',
        'landmark' => 'Adjacent to Gateway of India Promenade',
        'phone' => '+91 (22) 6842 9100',
        'phone_raw' => '+912268429100',
        'phone_code' => '+91',
        'email' => 'concierge.in@bullerleather.com',
        'whatsapp' => '+91 98200 48210',
        'whatsapp_raw' => '919820048210',
        'hours' => 'Monday – Saturday: 10:00 AM – 8:00 PM IST',
        'sunday' => 'Sunday: By Prior Appointment Only',
        'shipping_partner' => 'Blue Dart Apex & Air Express (Pre-Insured)',
        'turnaround' => '24 – 48 Hours Across Major Metros',
        'services' => [
            'Bespoke Jacket Tailoring & Hide Grading',
            'Corporate Guild Commissions & Gifting',
            'Official GST Tax Invoice Processing',
            'Complimentary Conditioning & Polishing Service'
        ],
        'specialist' => 'Arjun Singhania',
        'specialist_title' => 'Head of Guild Client Services',
        'badge' => 'South Asia Flagship Guild'
    ],
    'US' => [
        'name' => 'United States',
        'code' => 'US',
        'flag' => 'us',
        'city' => 'New York',
        'atelier_title' => 'SoHo Private Salon & Client Suites',
        'address' => '482 Broome Street, 3rd Floor Penthouse, SoHo, New York, NY 10013',
        'landmark' => 'Between Wooster & West Broadway',
        'phone' => '+1 (800) 285-5371',
        'phone_raw' => '+18002855371',
        'phone_code' => '+1',
        'email' => 'concierge.us@bullerleather.com',
        'whatsapp' => '+1 (212) 555-0194',
        'whatsapp_raw' => '12125550194',
        'hours' => 'Monday – Friday: 9:00 AM – 6:00 PM EST',
        'sunday' => 'Saturday & Sunday: Private VIP Showings',
        'shipping_partner' => 'FedEx Air Priority & DHL Express',
        'turnaround' => 'Next-Day Delivery Across Continental US',
        'services' => [
            'Private Penthouse Fitting Appointments',
            'Executive Monogramming & Custom Hot Foil',
            'Corporate VIP Holiday Gifting Programs',
            'Lifetime Hardware & Stitch Warranty Desk'
        ],
        'specialist' => 'Eleanor Vance',
        'specialist_title' => 'Senior Client Director (Americas)',
        'badge' => 'North America Private Salon'
    ],
    'GB' => [
        'name' => 'United Kingdom',
        'code' => 'GB',
        'flag' => 'gb',
        'city' => 'London',
        'atelier_title' => 'Mayfair Historic Atelier & Workshop',
        'address' => '48 Conduit Street, Mayfair, London W1S 2YG, United Kingdom',
        'landmark' => 'Steps from Savile Row & Bond Street',
        'phone' => '+44 (20) 7946 0920',
        'phone_raw' => '+442079460920',
        'phone_code' => '+44',
        'email' => 'concierge.uk@bullerleather.com',
        'whatsapp' => '+44 7700 900142',
        'whatsapp_raw' => '447700900142',
        'hours' => 'Monday – Friday: 9:30 AM – 6:30 PM GMT',
        'sunday' => 'Saturday: 10:00 AM – 5:00 PM GMT',
        'shipping_partner' => 'Royal Mail Special Delivery & DPD Priority',
        'turnaround' => 'Same-Day Central London Chauffeur Dispatch',
        'services' => [
            'Two-Needle Hand-Saddle Stitch Customization',
            'Heritage Vault Archive Viewing',
            'Bespoke English Bridle Belt Sizing',
            'Lifetime Leather Re-waxing & Restoration'
        ],
        'specialist' => 'Charles Sterling',
        'specialist_title' => 'Master Craftsman & Atelier Curator',
        'badge' => 'European Headquarters & Workshop'
    ],
    'AE' => [
        'name' => 'United Arab Emirates',
        'code' => 'AE',
        'flag' => 'ae',
        'city' => 'Dubai',
        'atelier_title' => 'DIFC Private Client Suite & Salon',
        'address' => 'Gate Village 04, Level 5, Dubai International Financial Centre (DIFC), Dubai, UAE',
        'landmark' => 'Adjacent to The Gate Building',
        'phone' => '+971 (4) 362 7800',
        'phone_raw' => '+97143627800',
        'phone_code' => '+971',
        'email' => 'concierge.uae@bullerleather.com',
        'whatsapp' => '+971 50 123 4567',
        'whatsapp_raw' => '971501234567',
        'hours' => 'Sunday – Thursday: 10:00 AM – 7:00 PM GST',
        'sunday' => 'Friday & Saturday: Dedicated Private Appointments',
        'shipping_partner' => 'DHL Express Gulf & VIP Chauffeur Courier',
        'turnaround' => 'Same-Day Delivery in Dubai & Abu Dhabi',
        'services' => [
            'Private Penthouse & Yacht Presentation',
            'Custom Solid Gold Hardware Commissions',
            'Falconry & Heirloom Travel Trunk Advisory',
            'Tax-Free International Export Documentation'
        ],
        'specialist' => 'Tariq Al-Maktoum',
        'specialist_title' => 'GCC Regional Client Director',
        'badge' => 'Middle East Flagship Suite'
    ]
];

// Fallback to IN if code not found
if (!isset($regionalAteliers[$selectedCountryCode])) {
    $selectedCountryCode = 'IN';
}
$currentAtelier = $regionalAteliers[$selectedCountryCode];

require __DIR__ . '/../layouts/header.php';
?>

<!-- Contact Hero Section -->
<section class="position-relative py-4 py-md-5 overflow-hidden" style="background: linear-gradient(135deg, var(--color-espresso-deep) 0%, var(--color-espresso) 100%); color: var(--color-ivory);">
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-20" style="background-image: radial-gradient(var(--color-gold) 1px, transparent 1px); background-size: 28px 28px;"></div>
    
    <div class="container position-relative py-2 py-md-4">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-decoration-none text-gold-bright small"><i class="bi bi-house-door me-1"></i> Home</a></li>
                <li class="breadcrumb-item active text-white-50 small" aria-current="page">Atelier Concierge & Contact</li>
            </ol>
        </nav>

        <div class="row align-items-center g-3 g-md-4">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-2 mb-md-3 rounded-pill" style="background: rgba(197, 168, 128, 0.15); border: 1px solid rgba(197, 168, 128, 0.35);">
                    <i class="bi bi-compass text-warning" style="font-size: 0.85rem;"></i>
                    <span class="text-uppercase fw-semibold" style="letter-spacing: 0.22em; font-size: 0.68rem; color: var(--color-gold-bright);">GLOBAL CONCIERGE NETWORK</span>
                </div>
                <h1 class="font-serif fw-bold mb-2 mb-md-3" style="letter-spacing: 0.02em; line-height: 1.2; color: #ffffff; font-size: clamp(1.75rem, 5vw, 2.8rem);">
                    Connect With Our <br><span style="color: var(--color-gold-bright); font-style: italic;">Regional Ateliers</span>
                </h1>
                <p class="lead text-light mb-0" style="line-height: 1.7; opacity: 0.9; max-width: 650px; font-size: clamp(0.9rem, 2vw, 1.05rem);">
                    Whether commissioning a bespoke leather garment, arranging an executive gifting order, or requesting private salon access, our regional client directors stand at your service.
                </p>
            </div>
            <div class="col-lg-4 text-start text-lg-end mt-3 mt-lg-0">
                <div class="p-3 rounded-2 text-start d-inline-block w-100" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(197, 168, 128, 0.25); max-width: 320px;">
                    <div class="small text-gold fw-semibold mb-1" style="letter-spacing: 0.1em; font-size: 0.7rem; text-transform: uppercase;">
                        <i class="bi bi-clock-history me-1"></i> Global Response Promise
                    </div>
                    <div class="fw-bold text-white fs-6">Under 4 Business Hours</div>
                    <div class="small text-light-50 mt-1" style="font-size: 0.74rem;">Dedicated senior concierge attention with zero automated bot queues.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Country Tabs Selector Bar -->
<section class="py-2 py-md-3 border-bottom sticky-top-country" style="background-color: var(--color-cream); border-color: var(--color-border) !important;">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 gap-md-3">
            <div class="d-flex align-items-center gap-2">
                <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.12em; font-size: 0.72rem;">
                    Select Regional Atelier:
                </span>
            </div>

            <!-- Country Switcher Pills -->
            <div class="nav nav-pills contact-country-pills d-flex flex-wrap gap-2" id="countryPills">
                <?php foreach ($regionalAteliers as $code => $at): ?>
                    <button type="button" 
                            class="btn contact-country-btn py-1 py-sm-2 px-2 px-sm-3 <?= ($code === $selectedCountryCode) ? 'active' : '' ?>" 
                            data-country="<?= $code ?>"
                            onclick="switchContactCountry('<?= $code ?>')">
                        <img src="<?= asset('assets/images/flags/' . $at['flag'] . '.png') ?>" alt="<?= $at['name'] ?>" width="18" height="13" class="rounded-1 shadow-xs me-1 me-sm-2">
                        <span class="country-name"><?= $at['name'] ?></span>
                        <span class="country-city text-muted small ms-1">(<?= $at['city'] ?>)</span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Main Contact Area: Atelier Details & Localized Form -->
<section class="py-4 py-md-5" style="background-color: var(--color-ivory);">
    <div class="container py-2 py-md-3">

        <!-- Flash Message Banners -->
        <?php if (has_flash('contact_success')): ?>
            <div class="alert alert-success border-0 shadow-soft p-3 p-md-4 mb-4 mb-md-5 rounded-2 d-flex align-items-start gap-3" id="inquiryConfirmed" style="background-color: #f0fdf4; border-left: 4px solid #16a34a !important;">
                <i class="bi bi-check-circle-fill text-success fs-4 mt-1"></i>
                <div>
                    <h5 class="fw-bold text-success mb-1 font-serif" style="font-size: 1.1rem;">Inquiry Registered With Regional Atelier</h5>
                    <p class="text-dark mb-0 small" style="line-height: 1.6;"><?= flash('contact_success') ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (has_flash('contact_error')): ?>
            <div class="alert alert-danger border-0 shadow-soft p-3 p-md-4 mb-4 mb-md-5 rounded-2 d-flex align-items-start gap-3" style="background-color: #fef2f2; border-left: 4px solid #dc2626 !important;">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-4 mt-1"></i>
                <div>
                    <h5 class="fw-bold text-danger mb-1 font-serif" style="font-size: 1.1rem;">Submission Notice</h5>
                    <p class="text-dark mb-0 small"><?= flash('contact_error') ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="row g-4 g-lg-5">
            <!-- Left Column: Country-Specific Atelier Profile -->
            <div class="col-lg-5">
                <div class="atelier-info-card bg-white p-3 p-sm-4 p-md-5 border shadow-soft rounded-2 position-relative" style="border-color: var(--color-border); border-top: 4px solid var(--color-gold) !important;">
                    
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge px-2 px-sm-3 py-2 text-uppercase fw-semibold" id="atelierBadge" style="background: rgba(140, 74, 47, 0.1); color: var(--color-cognac); letter-spacing: 0.12em; font-size: 0.68rem;">
                            <?= e($currentAtelier['badge']) ?>
                        </span>
                        <img id="atelierFlagImg" src="<?= asset('assets/images/flags/' . $currentAtelier['flag'] . '.png') ?>" alt="Flag" width="28" height="20" class="rounded shadow-xs">
                    </div>

                    <h3 class="font-serif fw-bold mb-2" id="atelierTitle" style="color: var(--color-espresso-deep); font-size: 1.65rem;">
                        <?= e($currentAtelier['atelier_title']) ?>
                    </h3>
                    <p class="text-muted small mb-4" id="atelierLandmark" style="font-style: italic;">
                        <i class="bi bi-geo text-gold me-1"></i> <?= e($currentAtelier['landmark']) ?>
                    </p>

                    <!-- Contact Details -->
                    <div class="d-flex flex-column gap-3 py-3 border-top border-bottom" style="border-color: var(--color-border) !important;">
                        <!-- Address -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="contact-icon-circle">
                                <i class="bi bi-geo-alt-fill text-gold fs-5"></i>
                            </div>
                            <div>
                                <span class="d-block small text-muted text-uppercase fw-bold" style="letter-spacing: 0.1em; font-size: 0.68rem;">Physical Address</span>
                                <span class="d-block text-dark fw-medium" id="atelierAddress" style="font-size: 0.92rem; line-height: 1.5;">
                                    <?= e($currentAtelier['address']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Direct Telephone -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="contact-icon-circle">
                                <i class="bi bi-telephone-fill text-gold fs-5"></i>
                            </div>
                            <div>
                                <span class="d-block small text-muted text-uppercase fw-bold" style="letter-spacing: 0.1em; font-size: 0.68rem;">Direct Atelier Line</span>
                                <a href="tel:<?= e($currentAtelier['phone_raw']) ?>" id="atelierPhoneLink" class="text-decoration-none fw-bold" style="color: var(--color-espresso-deep); font-size: 1rem;">
                                    <?= e($currentAtelier['phone']) ?>
                                </a>
                                <span class="d-block small text-muted mt-0" style="font-size: 0.74rem;">Direct concierge line &bull; Zero toll fees</span>
                            </div>
                        </div>

                        <!-- Email Desk -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="contact-icon-circle">
                                <i class="bi bi-envelope-fill text-gold fs-5"></i>
                            </div>
                            <div>
                                <span class="d-block small text-muted text-uppercase fw-bold" style="letter-spacing: 0.1em; font-size: 0.68rem;">Private Client Desk</span>
                                <a href="mailto:<?= e($currentAtelier['email']) ?>" id="atelierEmailLink" class="text-decoration-none fw-semibold text-primary" style="font-size: 0.92rem;">
                                    <?= e($currentAtelier['email']) ?>
                                </a>
                            </div>
                        </div>

                        <!-- WhatsApp Direct Concierge -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="contact-icon-circle" style="background: rgba(37, 211, 102, 0.1); color: #16a34a;">
                                <i class="bi bi-whatsapp fs-5 text-success"></i>
                            </div>
                            <div>
                                <span class="d-block small text-muted text-uppercase fw-bold" style="letter-spacing: 0.1em; font-size: 0.68rem;">Instant VIP WhatsApp Desk</span>
                                <a href="https://wa.me/<?= e($currentAtelier['whatsapp_raw']) ?>?text=<?= urlencode('Hello Buller Atelier, I would like to inquire about bespoke services.') ?>" target="_blank" id="atelierWhatsappLink" class="text-decoration-none fw-bold text-success" style="font-size: 0.92rem;">
                                    <?= e($currentAtelier['whatsapp']) ?> <i class="bi bi-box-arrow-up-right ms-1 small" style="font-size: 0.72rem;"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Hours & Logistics -->
                    <div class="mt-4">
                        <div class="mb-3">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.1em; font-size: 0.68rem;">Operating Schedule</span>
                            <div class="small fw-semibold text-dark" id="atelierHours"><i class="bi bi-clock me-1 text-gold"></i> <?= e($currentAtelier['hours']) ?></div>
                            <div class="small text-muted" id="atelierSunday" style="font-size: 0.78rem;"><?= e($currentAtelier['sunday']) ?></div>
                        </div>

                        <div class="mb-4">
                            <span class="d-block small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 0.1em; font-size: 0.68rem;">Regional Shipping Partner</span>
                            <div class="small text-dark fw-medium" id="atelierShipping"><i class="bi bi-box-seam me-1 text-gold"></i> <?= e($currentAtelier['shipping_partner']) ?></div>
                            <div class="small text-muted" id="atelierTurnaround" style="font-size: 0.78rem;"><?= e($currentAtelier['turnaround']) ?></div>
                        </div>

                        <!-- Lead Specialist Card -->
                        <div class="p-3 rounded-1" style="background-color: var(--color-cream); border: 1px solid var(--color-border);">
                            <span class="d-block small text-muted text-uppercase fw-bold" style="letter-spacing: 0.1em; font-size: 0.64rem;">Dedicated Regional Director</span>
                            <div class="fw-bold text-dark font-serif" id="atelierSpecialist"><?= e($currentAtelier['specialist']) ?></div>
                            <div class="small text-muted" id="atelierSpecialistTitle" style="font-size: 0.76rem;"><?= e($currentAtelier['specialist_title']) ?></div>
                        </div>
                    </div>

                    <!-- Services Available at this Atelier -->
                    <div class="mt-4 pt-3 border-top" style="border-color: var(--color-border) !important;">
                        <span class="d-block small text-muted text-uppercase fw-bold mb-2" style="letter-spacing: 0.1em; font-size: 0.68rem;">Atelier Capabilities</span>
                        <ul class="list-unstyled mb-0" id="atelierServicesList">
                            <?php foreach ($currentAtelier['services'] as $srv): ?>
                                <li class="small text-muted py-1 d-flex align-items-center gap-2">
                                    <i class="bi bi-check2 text-gold fw-bold"></i> <?= e($srv) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                </div>
            </div>

            <!-- Right Column: Regional Contact Form -->
            <div class="col-lg-7">
                <div class="bg-white p-3 p-sm-4 p-md-5 border shadow-soft rounded-2" style="border-color: var(--color-border);">
                    
                    <!-- Destination Atelier Indicator -->
                    <div class="p-3 mb-4 rounded-1 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2" style="background-color: var(--color-cream); border-left: 3px solid var(--color-cognac);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-envelope-paper-heart text-gold fs-4"></i>
                            <div>
                                <span class="d-block small text-muted text-uppercase fw-bold" style="letter-spacing: 0.1em; font-size: 0.68rem;">Direct Dispatch Destination</span>
                                <strong class="text-dark font-serif" id="formDestinationNotice" style="font-size: clamp(0.88rem, 2vw, 1rem);">
                                    <?= e($currentAtelier['name']) ?> Atelier (<?= e($currentAtelier['city']) ?>) Desk
                                </strong>
                            </div>
                        </div>
                        <span class="badge text-uppercase bg-dark align-self-start align-self-sm-center" id="formCountryBadge" style="letter-spacing: 0.1em; font-size: 0.68rem;">
                            <?= e($currentAtelier['code']) ?> ATELIER
                        </span>
                    </div>

                    <h2 class="font-serif fw-bold mb-2" style="color: var(--color-espresso-deep); font-size: clamp(1.4rem, 3.5vw, 1.85rem);">
                        Direct Atelier Dispatch
                    </h2>
                    <p class="text-muted small mb-4" style="font-size: 0.82rem; line-height: 1.6;">
                        Please provide your contact particulars below. Your inquiry is routed directly to the regional mastercraft desk and assigned to a dedicated client specialist.
                    </p>

                    <!-- Contact Form -->
                    <form action="<?= url('contact') ?>" method="POST" id="regionalContactForm">
                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                        <input type="hidden" name="country_code" id="inputCountryCode" value="<?= e($selectedCountryCode) ?>">

                        <div class="row g-3 mb-3">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <label for="fullName" class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.08em; font-size: 0.72rem;">Full Name *</label>
                                <input type="text" class="form-control luxury-input" id="fullName" name="full_name" placeholder="e.g. Lord Alexander Wright" required value="<?= e($_POST['full_name'] ?? (Auth::user()['full_name'] ?? '')) ?>">
                            </div>

                            <!-- Email Address -->
                            <div class="col-md-6">
                                <label for="emailAddr" class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.08em; font-size: 0.72rem;">Email Address *</label>
                                <input type="email" class="form-control luxury-input" id="emailAddr" name="email" placeholder="alexander@domain.com" required value="<?= e($_POST['email'] ?? (Auth::user()['email'] ?? '')) ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Telephone with Country Prefix -->
                            <div class="col-md-6">
                                <label for="phoneNum" class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.08em; font-size: 0.72rem;">Telephone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border" id="phonePrefixLabel" style="font-weight: 600; color: var(--color-espresso-deep); font-size: 0.88rem;">
                                        <?= e($currentAtelier['phone_code']) ?>
                                    </span>
                                    <input type="tel" class="form-control luxury-input" id="phoneNum" name="phone" placeholder="98200 48210" value="<?= e($_POST['phone'] ?? '') ?>">
                                </div>
                            </div>

                            <!-- Target Country Atelier Selection -->
                            <div class="col-md-6">
                                <label for="selectTargetCountry" class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.08em; font-size: 0.72rem;">Routing Atelier *</label>
                                <select class="form-select luxury-input" id="selectTargetCountry" onchange="switchContactCountry(this.value)">
                                    <?php foreach ($regionalAteliers as $c => $data): ?>
                                        <option value="<?= $c ?>" <?= ($c === $selectedCountryCode) ? 'selected' : '' ?>>
                                            <?= $data['name'] ?> (<?= $data['city'] ?> Atelier)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Inquiry Classification -->
                            <div class="col-md-6">
                                <label for="inquiryType" class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.08em; font-size: 0.72rem;">Inquiry Classification *</label>
                                <select class="form-select luxury-input" id="inquiryType" name="inquiry_type" required>
                                    <option value="Bespoke Sizing & Custom Fitting" <?= (($_POST['inquiry_type'] ?? '') === 'Bespoke Sizing & Custom Fitting') ? 'selected' : '' ?>>Bespoke Sizing & Custom Fitting</option>
                                    <option value="Order Tracking & International Customs" <?= (($_POST['inquiry_type'] ?? '') === 'Order Tracking & International Customs') ? 'selected' : '' ?>>Order Tracking & International Customs</option>
                                    <option value="Private Salon VIP Appointment" <?= (($_POST['inquiry_type'] ?? '') === 'Private Salon VIP Appointment') ? 'selected' : '' ?>>Private Salon VIP Appointment</option>
                                    <option value="Corporate Commissions & Monogramming" <?= (($_POST['inquiry_type'] ?? '') === 'Corporate Commissions & Monogramming') ? 'selected' : '' ?>>Corporate Commissions & Monogramming</option>
                                    <option value="Leather Care, Waxing & Warranty" <?= (($_POST['inquiry_type'] ?? '') === 'Leather Care, Waxing & Warranty') ? 'selected' : '' ?>>Leather Care, Waxing & Warranty</option>
                                    <option value="General Atelier Inquiries" <?= (($_POST['inquiry_type'] ?? '') === 'General Atelier Inquiries') ? 'selected' : '' ?>>General Atelier Inquiries</option>
                                </select>
                            </div>

                            <!-- Preferred Channel -->
                            <div class="col-md-6">
                                <label for="preferredChannel" class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.08em; font-size: 0.72rem;">Preferred Channel</label>
                                <select class="form-select luxury-input" id="preferredChannel" name="preferred_channel">
                                    <option value="Email">Official Concierge Email</option>
                                    <option value="Phone">Direct Phone Call</option>
                                    <option value="WhatsApp">VIP WhatsApp Desk</option>
                                </select>
                            </div>
                        </div>

                        <!-- Subject Line -->
                        <div class="mb-3">
                            <label for="subjectLine" class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.08em; font-size: 0.72rem;">Subject *</label>
                            <input type="text" class="form-control luxury-input" id="subjectLine" name="subject" placeholder="e.g. Inquiring regarding Tuscan Aviator sizing & Mayfair fitting" required value="<?= e($_POST['subject'] ?? '') ?>">
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <label for="messageBody" class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.08em; font-size: 0.72rem;">Message / Request Particulars *</label>
                            <textarea class="form-control luxury-input" id="messageBody" name="message" rows="5" placeholder="Please detail your dimensions, preferred hide grade, delivery requirements, or bespoke inquiries..." required><?= e($_POST['message'] ?? '') ?></textarea>
                            <div class="form-text small text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-shield-check text-success"></i> Your message is handled strictly under European General Data Protection and confidential atelier protocols.
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <button type="submit" class="btn btn-buller-primary py-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center gap-2" style="font-size: 0.88rem; letter-spacing: 0.08em;">
                                <span>Dispatch Inquiry to Atelier</span>
                                <i class="bi bi-send-fill text-warning"></i>
                            </button>
                            <span class="small text-muted" style="font-size: 0.75rem;">
                                Guaranteed response within 4 hours.
                            </span>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</section>

<!-- Regional Ateliers Quick Grid -->
<section class="py-5" style="background-color: var(--color-cream); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-uppercase small fw-bold" style="color: var(--color-cognac); letter-spacing: 0.22em;">Global Network</span>
            <h2 class="font-serif display-6 fw-bold mt-2 mb-3" style="color: var(--color-espresso-deep);">Four International Flagship Salons</h2>
            <p class="text-muted">Direct appointment booking and international logistics support across every primary time zone.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($regionalAteliers as $code => $at): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-soft p-4 text-center <?= ($code === $selectedCountryCode) ? 'border-gold-active' : '' ?>" style="background: #ffffff; border-radius: 8px;">
                        <img src="<?= asset('assets/images/flags/' . $at['flag'] . '.png') ?>" alt="<?= $at['name'] ?>" width="34" height="24" class="rounded shadow-xs mx-auto mb-3">
                        <h5 class="font-serif fw-bold mb-1" style="color: var(--color-espresso-deep);"><?= $at['name'] ?></h5>
                        <p class="small text-muted mb-2"><?= $at['city'] ?> Atelier</p>
                        <div class="small fw-semibold text-dark mb-3" style="font-size: 0.82rem;"><?= $at['phone'] ?></div>
                        <button type="button" class="btn btn-outline-dark btn-sm mt-auto" onclick="switchContactCountry('<?= $code ?>'); window.scrollTo({top: 400, behavior: 'smooth'});">
                            Select Atelier <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Client Concierge FAQ Section -->
<section class="py-5" style="background-color: var(--color-ivory);">
    <div class="container py-4">
        <div class="max-w-800 mx-auto">
            <div class="text-center mb-5">
                <span class="text-uppercase small fw-bold" style="color: var(--color-cognac); letter-spacing: 0.22em;">Client Guidance</span>
                <h2 class="font-serif display-6 fw-bold mt-2 mb-3" style="color: var(--color-espresso-deep);">Frequently Asked Inquiries</h2>
            </div>

            <div class="accordion accordion-buller" id="contactFaqAccordion">
                <div class="accordion-item border mb-3 rounded shadow-xs" style="border-color: var(--color-border) !important;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold font-serif" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            How do I schedule a bespoke jacket fitting in London, New York, Mumbai, or Dubai?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#contactFaqAccordion">
                        <div class="accordion-body text-muted small" style="line-height: 1.8;">
                            Select your regional atelier above, choose <em>"Private Salon VIP Appointment"</em> as your inquiry type, and suggest two preferred dates. Your regional client director will confirm your private salon booking with tailored hide samples prepared in advance.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border mb-3 rounded shadow-xs" style="border-color: var(--color-border) !important;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold font-serif" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Are international shipments insured against loss or transit damage?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#contactFaqAccordion">
                        <div class="accordion-body text-muted small" style="line-height: 1.8;">
                            100% yes. Every Buller Leather parcel travels under full declaration and comprehensive marine & air cargo insurance via DHL Express, Blue Dart Apex, or FedEx Priority. In the rare event of transit damage, an immediate replacement or full restoration is guaranteed.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border mb-3 rounded shadow-xs" style="border-color: var(--color-border) !important;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold font-serif" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Can I request custom hot-foil monogramming or corporate guild embossing?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#contactFaqAccordion">
                        <div class="accordion-body text-muted small" style="line-height: 1.8;">
                            Yes. We offer blind debossing and 24K gold foil monogramming on all briefcases, wallets, belts, and desk accoutrements. For corporate commissions exceeding 10 units, our design team produces custom brass die stamps featuring your crest or corporate mark.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Country Switcher Client Script -->
<script>
const regionalData = <?= json_encode($regionalAteliers) ?>;

function switchContactCountry(code) {
    if (!regionalData[code]) return;
    const at = regionalData[code];

    // Update active pills
    document.querySelectorAll('.contact-country-btn').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-country') === code);
    });

    // Update Atelier Card Details
    document.getElementById('atelierBadge').textContent = at.badge;
    document.getElementById('atelierFlagImg').src = '<?= asset('assets/images/flags/') ?>' + at.flag + '.png';
    document.getElementById('atelierTitle').textContent = at.atelier_title;
    document.getElementById('atelierLandmark').innerHTML = '<i class="bi bi-geo text-gold me-1"></i> ' + at.landmark;
    document.getElementById('atelierAddress').textContent = at.address;
    
    // Telephone
    const phoneLink = document.getElementById('atelierPhoneLink');
    phoneLink.textContent = at.phone;
    phoneLink.href = 'tel:' + at.phone_raw;
    
    // Email
    const emailLink = document.getElementById('atelierEmailLink');
    emailLink.textContent = at.email;
    emailLink.href = 'mailto:' + at.email;

    // WhatsApp
    const waLink = document.getElementById('atelierWhatsappLink');
    waLink.innerHTML = at.whatsapp + ' <i class="bi bi-box-arrow-up-right ms-1 small"></i>';
    waLink.href = 'https://wa.me/' + at.whatsapp_raw + '?text=' + encodeURIComponent('Hello ' + at.city + ' Atelier, I would like to inquire about bespoke services.');

    // Hours & Logistics
    document.getElementById('atelierHours').innerHTML = '<i class="bi bi-clock me-1 text-gold"></i> ' + at.hours;
    document.getElementById('atelierSunday').textContent = at.sunday;
    document.getElementById('atelierShipping').innerHTML = '<i class="bi bi-box-seam me-1 text-gold"></i> ' + at.shipping_partner;
    document.getElementById('atelierTurnaround').textContent = at.turnaround;

    // Specialist
    document.getElementById('atelierSpecialist').textContent = at.specialist;
    document.getElementById('atelierSpecialistTitle').textContent = at.specialist_title;

    // Services
    const srvList = document.getElementById('atelierServicesList');
    srvList.innerHTML = '';
    at.services.forEach(s => {
        const li = document.createElement('li');
        li.className = 'small text-muted py-1 d-flex align-items-center gap-2';
        li.innerHTML = '<i class="bi bi-check2 text-gold fw-bold"></i> ' + s;
        srvList.appendChild(li);
    });

    // Form synchronization
    document.getElementById('inputCountryCode').value = code;
    document.getElementById('selectTargetCountry').value = code;
    document.getElementById('phonePrefixLabel').textContent = at.phone_code;
    document.getElementById('formDestinationNotice').textContent = at.name + ' Atelier (' + at.city + ') Desk';
    document.getElementById('formCountryBadge').textContent = at.code + ' ATELIER';

    // Update URL query string without page reload
    const url = new URL(window.location);
    url.searchParams.set('country', code);
    window.history.replaceState({}, '', url);
}
</script>

<style>
.contact-country-btn {
    background: #ffffff;
    border: 1px solid var(--color-border);
    color: var(--color-espresso-deep);
    padding: 0.45rem 0.9rem;
    font-size: 0.82rem;
    font-weight: 600;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    transition: var(--transition-smooth);
}
.contact-country-btn:hover {
    border-color: var(--color-gold);
    color: var(--color-cognac);
    background: #ffffff;
}
.contact-country-btn.active {
    background: var(--color-espresso-deep) !important;
    color: var(--color-gold-bright) !important;
    border-color: var(--color-espresso-deep) !important;
    box-shadow: var(--shadow-soft);
}
.contact-icon-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(197, 168, 128, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.luxury-input {
    border: 1px solid var(--color-border);
    padding: 0.65rem 0.9rem;
    font-size: 0.88rem;
    border-radius: 4px;
    background-color: #ffffff;
    color: var(--color-espresso-deep);
    transition: var(--transition-smooth);
}
.luxury-input:focus {
    border-color: var(--color-gold);
    box-shadow: 0 0 0 3px rgba(197, 168, 128, 0.2);
    outline: none;
}
.border-gold-active {
    border: 2px solid var(--color-gold) !important;
    position: relative;
}
.border-gold-active::after {
    content: 'ACTIVE ATELIER';
    position: absolute;
    top: -10px;
    right: 12px;
    background: var(--color-cognac);
    color: #fff;
    font-size: 0.58rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    padding: 2px 8px;
    border-radius: 3px;
}
</style>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
