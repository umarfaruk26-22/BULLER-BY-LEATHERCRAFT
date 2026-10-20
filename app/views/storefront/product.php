<?php
use App\Core\MarketContext;
use App\Core\Wishlist;
use App\Core\Auth;

$activeCountry = MarketContext::getCountry();
$pricing = MarketContext::evaluatePricing(
    (float)$product['original_price'],
    $product['discount_price'] ? (float)$product['discount_price'] : null,
    $product['discount_start_date'],
    $product['discount_end_date']
);
$isWishlisted = Wishlist::has((int)$product['id']);
$pageTitle = $product['title'] . ' | Buller Leather Heritage';

require __DIR__ . '/../layouts/header.php';
?>

<!-- Breadcrumbs Navigation -->
<div class="bg-white border-bottom py-2 py-md-3">
    <div class="container small text-muted pdp-breadcrumbs">
        <a href="<?= url('') ?>" class="text-muted text-decoration-none">Home</a>
        <span class="mx-1 mx-md-2 text-muted">&bull;</span>
        <a href="<?= url('shop') ?>" class="text-muted text-decoration-none">Collections</a>
        <span class="mx-1 mx-md-2 text-muted">&bull;</span>
        <a href="<?= url('shop?category=' . urlencode($product['category_slug'])) ?>" class="text-muted text-decoration-none"><?= e($product['category_name']) ?></a>
        <span class="mx-1 mx-md-2 text-muted">&bull;</span>
        <span class="text-dark fw-semibold active-crumb"><?= e($product['title']) ?></span>
    </div>
</div>

<!-- Flash alerts for reviews -->
<div class="container pt-3">
    <?php if (has_flash('review_success')): ?>
        <div class="alert alert-success alert-dismissible fade show small" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= flash('review_success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (has_flash('review_error')): ?>
        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= flash('review_error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>

<!-- Product Detail Stage -->
<section class="py-3 py-md-5" style="background-color: var(--color-ivory);">
    <div class="container">
        <div class="row g-3 g-md-5">
            <!-- Left: Multi-Angle Photography Showcase -->
            <div class="col-lg-7">
                <div class="position-sticky" style="top: 100px;">
                    <!-- Main Master Showcase Display -->
                    <div class="position-relative bg-white border p-2 p-md-4 shadow-soft mb-2 mb-md-3" style="border-color: var(--color-border); border-radius: 2px;">
                        <img id="mainProductPhoto" src="<?= asset($product['primary_image']) ?>" alt="<?= e($product['title']) ?>" class="img-fluid w-100" style="max-height: 580px; object-fit: contain; transition: transform 0.3s ease;">
                        
                        <!-- Luxury Product Badges -->
                        <div class="product-badges pdp-badges">
                            <?php if ($pricing['is_discounted']): ?>
                                <span class="badge-luxury badge-sale mb-1 shadow-sm">-<?= $pricing['discount_percent'] ?>% Privé Privilege</span>
                            <?php endif; ?>
                            <?php if ($product['is_bestseller']): ?>
                                <span class="badge-luxury badge-bestseller mb-1 shadow-sm"><i class="bi bi-award-fill me-1"></i> Signature Masterpiece</span>
                            <?php endif; ?>
                            <span class="badge-luxury mb-1 shadow-sm badge-hide" style="background: rgba(26, 18, 11, 0.88); color: var(--color-gold); border: 1px solid var(--color-gold);">
                                <i class="bi bi-shield-check me-1"></i> 100% Full-Grain Hide
                            </span>
                        </div>

                        <!-- Zoom hint icon -->
                        <div class="position-absolute bottom-0 end-0 p-3 text-muted small d-none d-md-flex align-items-center gap-1 opacity-75">
                            <i class="bi bi-arrows-fullscreen"></i>
                            <span>Atelier High-Resolution View</span>
                        </div>
                    </div>

                    <!-- Interactive Angle Thumbnails Strip -->
                    <?php if (!empty($galleryImages)): ?>
                        <div class="pdp-thumbnails-strip d-flex gap-2 mb-3 mb-md-4">
                            <?php foreach ($galleryImages as $idx => $img): ?>
                                <div class="product-thumb-box border bg-white cursor-pointer <?= ($idx === 0) ? 'active-thumb' : '' ?>" 
                                     style="border-color: <?= ($idx === 0) ? 'var(--color-gold) !important; box-shadow: 0 0 0 1px var(--color-gold);' : 'var(--color-border);' ?>"
                                     onclick="switchProductPhoto(this, '<?= asset($img['image_url']) ?>')">
                                    <img src="<?= asset($img['image_url']) ?>" alt="<?= e($img['alt_text'] ?? $product['title']) ?>" class="w-100 h-100" style="object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Craftsmanship Certification Banner (Desktop sticky column only) -->
                    <div class="p-3 bg-white border shadow-soft d-none d-lg-flex align-items-center justify-content-between rounded-1 small text-muted" style="border-color: var(--color-border);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-patch-check-fill text-warning fs-5"></i>
                            <div>
                                <strong class="text-dark d-block">Hand-Inspected Masterpiece</strong>
                                <span>Certified authentic vegetable pit-tanned hide</span>
                            </div>
                        </div>
                        <span class="badge bg-light text-dark border">Lot: <?= e($product['sku']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Right: Commercial Specifications & Interactive Purchase Engine -->
            <div class="col-lg-5">
                <div class="ps-lg-3">
                    <!-- Brand Origin Tag -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase small fw-bold" style="letter-spacing: 0.25em; color: var(--color-gold-dark); font-size: 0.75rem;">
                            Buller Atelier &bull; <?= e($product['leather_type'] ?? 'Prime European Hide') ?>
                        </span>
                        <?= country_flag($activeCountry['code'], $activeCountry['name'], 22) ?>
                    </div>

                    <!-- Product Main Title -->
                    <h1 class="font-serif pdp-title mt-1 mb-2">
                        <?= e($product['title']) ?>
                    </h1>

                    <!-- SKU & Patron Ratings Summary -->
                    <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 text-muted small mb-3 pb-2 border-bottom">
                        <div>SKU: <strong class="text-dark"><?= e($product['sku']) ?></strong></div>
                        <span>&bull;</span>
                        <div class="d-flex align-items-center gap-1">
                            <div class="text-warning small">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= ($i <= round($avgRating)) ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <a href="#reviewsTab" onclick="document.querySelector('#reviewsTab-tab').click();" class="text-muted text-decoration-underline ms-1">
                                <?= $avgRating ?> (<?= count($reviews) ?> Reviews)
                            </a>
                        </div>
                    </div>

                    <!-- Localized Pricing Card -->
                    <div class="p-3 p-md-4 bg-white border shadow-soft mb-3 mb-md-4 position-relative" style="border-color: var(--color-border); border-left: 4px solid var(--color-gold) !important;">
                        <div class="d-flex flex-wrap align-items-baseline gap-2 gap-md-3">
                            <span class="pdp-price-value fw-bold">
                                <?= $pricing['formatted_final'] ?>
                            </span>
                            <?php if ($pricing['is_discounted']): ?>
                                <span class="fs-6 fs-md-5 text-muted text-decoration-line-through">
                                    <?= $pricing['formatted_original'] ?>
                                </span>
                                <span class="badge bg-danger px-2 py-1 small">
                                    Save <?= MarketContext::formatPrice($pricing['original_price'] - $pricing['final_price']) ?> (<?= $pricing['discount_percent'] ?>% OFF)
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted small mt-2 d-flex flex-column gap-1" style="font-size: 0.8rem;">
                            <div>
                                <i class="bi bi-shield-check text-success me-1"></i>
                                Includes localized <strong><?= e($activeCountry['tax_name']) ?> (<?= $activeCountry['tax_rate_percent'] ?>%)</strong>. Formatted in <strong><?= e($activeCountry['currency_code']) ?> (<?= e($activeCountry['currency_symbol']) ?>)</strong>.
                            </div>
                            <div class="text-success fw-semibold">
                                <i class="bi bi-truck me-1"></i> Complimentary International Air Delivery to <?= e($activeCountry['name']) ?> included.
                            </div>
                        </div>
                    </div>

                    <!-- Regional Stock & Availability Status -->
                    <div class="mb-3 mb-md-4 p-2 p-md-3 bg-white border rounded-1" style="border-color: var(--color-border);">
                        <?php if ((int)$product['stock_quantity'] > 5): ?>
                            <div class="d-flex align-items-center gap-2 text-success small fw-semibold">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                                <div>
                                    <strong class="d-block text-dark">In Stock & Prepared for Dispatch</strong>
                                    <span><?= (int)$product['stock_quantity'] ?> units available in <?= e($activeCountry['name']) ?> regional fulfillment center.</span>
                                </div>
                            </div>
                        <?php elseif ((int)$product['stock_quantity'] > 0): ?>
                            <div class="d-flex align-items-center gap-2 text-warning-emphasis small fw-semibold">
                                <i class="bi bi-fire text-danger fs-5"></i>
                                <div>
                                    <strong class="d-block text-danger">Limited Atelier Vault Inventory</strong>
                                    <span>Only <?= (int)$product['stock_quantity'] ?> pieces remaining for <?= e($activeCountry['name']) ?> patrons.</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="d-flex align-items-center gap-2 text-danger small fw-semibold">
                                <i class="bi bi-x-circle-fill fs-5"></i>
                                <div>
                                    <strong class="d-block text-danger">Currently Reserved / Out of Stock</strong>
                                    <span>This piece is awaiting next tanning release for <?= e($activeCountry['name']) ?>.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Dynamic Size & Specification Selector (All Categories) -->
                    <?php 
                        $catSlug = $product['category_slug'] ?? '';
                    ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold text-muted mb-0">Select Atelier Fit / Size:</label>
                            <button type="button" class="btn btn-link p-0 small text-decoration-underline" style="color: var(--color-cognac);" data-bs-toggle="modal" data-bs-target="#sizeGuideModal">
                                <i class="bi bi-rulers me-1"></i> Interactive Size & Fit Guide
                            </button>
                        </div>

                        <div class="d-flex flex-wrap gap-2" id="productSizeOptions">
                            <?php if ($catSlug === 'leather-jackets'): ?>
                                <button type="button" class="size-pill-btn active" onclick="selectProductSize(this, '38 / S')">38 (S)</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, '40 / M')">40 (M)</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, '42 / L')">42 (L)</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, '44 / XL')">44 (XL)</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, '46 / XXL')">46 (XXL)</button>
                            <?php elseif ($catSlug === 'shoes-boots'): ?>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, 'UK 7 / US 8')">UK 7 / US 8</button>
                                <button type="button" class="size-pill-btn active" onclick="selectProductSize(this, 'UK 8 / US 9')">UK 8 / US 9</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, 'UK 9 / US 10')">UK 9 / US 10</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, 'UK 10 / US 11')">UK 10 / US 11</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, 'UK 11 / US 12')">UK 11 / US 12</button>
                            <?php elseif ($catSlug === 'belts'): ?>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, '32 in')">32" (80cm)</button>
                                <button type="button" class="size-pill-btn active" onclick="selectProductSize(this, '34 in')">34" (85cm)</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, '36 in')">36" (90cm)</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, '38 in')">38" (95cm)</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, '40 in')">40" (100cm)</button>
                            <?php elseif (str_contains($catSlug, 'briefcase') || str_contains($catSlug, 'bag') || $product['id'] == 2): ?>
                                <button type="button" class="size-pill-btn active" onclick="selectProductSize(this, 'Executive 16in Laptop Fit')">Executive 16" Laptop Fit</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, 'Classic 14in Edition')">Classic 14" Edition</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, 'Slimline 13in Profile')">Slimline 13" Profile</button>
                            <?php elseif (str_contains($catSlug, 'duffel') || str_contains($catSlug, 'travel') || $product['id'] == 3): ?>
                                <button type="button" class="size-pill-btn active" onclick="selectProductSize(this, '48-Hour Weekender (45L)')">48-Hour Weekender (45L)</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, 'Grand Voyage (60L)')">Grand Voyage (60L)</button>
                            <?php elseif (str_contains($catSlug, 'wallet') || $product['id'] == 4): ?>
                                <button type="button" class="size-pill-btn active" onclick="selectProductSize(this, 'Classic Bifold Profile (8 Cards)')">Classic Bifold Profile</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, 'Ultra-Slim Minimalist (6 Cards)')">Ultra-Slim Minimalist</button>
                            <?php elseif (str_contains($catSlug, 'watch') || $product['id'] == 7): ?>
                                <button type="button" class="size-pill-btn active" onclick="selectProductSize(this, '3-Timepiece Vault Edition')">3-Timepiece Vault Edition</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, 'Dual-Timepiece Travel Case')">Dual-Timepiece Travel Case</button>
                            <?php else: ?>
                                <button type="button" class="size-pill-btn active" onclick="selectProductSize(this, 'Standard Heirloom Fit')">Standard Heirloom Edition</button>
                                <button type="button" class="size-pill-btn" onclick="selectProductSize(this, 'Custom Atelier Fit')">Bespoke Atelier Fit</button>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" id="selectedProductSize" value="Standard Heirloom Edition">
                    </div>

                    <!-- Interactive Curated Colorway Selector -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold text-muted mb-0">Select Curated Leather Colorway:</label>
                            <span id="selectedColorName" class="badge bg-white text-dark border shadow-sm small fw-bold" style="border-color: var(--color-gold) !important; color: var(--color-espresso-deep) !important;">
                                <?= e($product['color'] ?? 'Heritage Deep Espresso') ?>
                            </span>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2" id="productColorOptions">
                            <button type="button" class="color-swatch-btn active" onclick="selectProductColor(this, '<?= e($product['color'] ?? 'Heritage Deep Espresso') ?>')" title="<?= e($product['color'] ?? 'Heritage Deep Espresso') ?>">
                                <span class="swatch-circle" style="background-color: <?= ($product['color'] === 'Espresso Brown' || $product['color'] === 'Dark Mahogany') ? '#2e1c14' : (($product['color'] === 'Cognac Tan' || str_contains($product['color'] ?? '', 'Tan') || str_contains($product['color'] ?? '', 'Chestnut')) ? '#8c4a2f' : '#1a1a1a') ?>;"></span>
                            </button>
                            <button type="button" class="color-swatch-btn" onclick="selectProductColor(this, 'Tuscan Cognac Tan')" title="Tuscan Cognac Tan">
                                <span class="swatch-circle" style="background-color: #8c4a2f;"></span>
                            </button>
                            <button type="button" class="color-swatch-btn" onclick="selectProductColor(this, 'Obsidian Raven Black')" title="Obsidian Raven Black">
                                <span class="swatch-circle" style="background-color: #161616;"></span>
                            </button>
                            <button type="button" class="color-swatch-btn" onclick="selectProductColor(this, 'Dark Mahogany Brown')" title="Dark Mahogany Brown">
                                <span class="swatch-circle" style="background-color: #3b2219;"></span>
                            </button>
                        </div>
                        <input type="hidden" id="selectedProductColor" value="<?= e($product['color'] ?? 'Heritage Deep Espresso') ?>">
                    </div>

                    <!-- Bespoke Hot Foil Monogramming Option -->
                    <div class="mb-4">
                        <div class="p-3 bg-white border rounded-1 shadow-sm" style="border-color: var(--color-border);">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="monogramToggle" onchange="document.getElementById('monogramBox').classList.toggle('d-none');">
                                <label class="form-check-label small fw-bold text-dark cursor-pointer" for="monogramToggle">
                                    <i class="bi bi-pen-fill text-warning me-1"></i> Add Complimentary Bespoke Monogramming
                                </label>
                            </div>
                            <div id="monogramBox" class="mt-3 pt-3 border-top d-none">
                                <div class="row g-2 align-items-center">
                                    <div class="col-6">
                                        <label class="form-label small text-muted mb-1">Initials (Max 3):</label>
                                        <input type="text" id="monogramInitials" maxlength="3" class="form-control form-control-sm text-uppercase fw-bold font-monospace" placeholder="e.g. ABL">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-muted mb-1">Foil Stamping:</label>
                                        <select id="monogramStyle" class="form-select form-select-sm">
                                            <option value="gold">24k Gold Hot Foil</option>
                                            <option value="blind">Blind Debossed Heat Stamp</option>
                                        </select>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">* Hand-stamped by our master embossers prior to sealing and dispatch.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase & Bag Action Buttons -->
                    <div class="pdp-actions-container d-flex flex-column gap-2 mb-3 mb-md-4">
                        <div class="d-flex align-items-stretch gap-2 gap-sm-3 pdp-action-group">
                            <!-- Quantity Counter -->
                            <div class="pdp-qty-box d-flex align-items-center border bg-white rounded-1" style="border-color: var(--color-border); height: 48px;">
                                <button type="button" class="btn btn-link text-dark px-2 px-md-3 py-2 text-decoration-none fw-bold" onclick="let input = document.getElementById('productQty'); if(parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;">&minus;</button>
                                <input type="number" id="productQty" class="form-control text-center border-0 p-0 fw-bold" value="1" min="1" max="<?= max(1, (int)$product['stock_quantity']) ?>" style="width: 38px; font-size: 0.95rem;">
                                <button type="button" class="btn btn-link text-dark px-2 px-md-3 py-2 text-decoration-none fw-bold" onclick="let input = document.getElementById('productQty'); if(parseInt(input.value) < <?= max(1, (int)$product['stock_quantity']) ?>) input.value = parseInt(input.value) + 1;">&#43;</button>
                            </div>

                            <!-- Add to Bag Button -->
                            <button type="button" class="btn btn-buller-gold flex-grow-1 py-2 py-md-3 btn-add-to-cart fw-bold d-flex align-items-center justify-content-center" data-product-id="<?= $product['id'] ?>" onclick="this.dataset.quantity = document.getElementById('productQty').value;" style="min-height: 48px; font-size: 0.92rem; letter-spacing: 0.05em;">
                                <i class="bi bi-bag-plus me-2 fs-5"></i> <span>Add To Bag</span>
                            </button>

                            <!-- Wishlist Toggle Button -->
                            <button type="button" class="btn btn-outline-secondary wishlist-toggle-btn pdp-wishlist-btn position-static <?= $isWishlisted ? 'active' : '' ?>" data-product-id="<?= $product['id'] ?>" title="Save to Curations" style="width: 48px !important; height: 48px !important; min-width: 48px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;">
                                <i class="bi <?= $isWishlisted ? 'bi-heart-fill text-danger' : 'bi-heart' ?> fs-5"></i>
                            </button>
                        </div>

                        <!-- Instant Buy Now Button -->
                        <button type="button" class="btn btn-dark w-100 py-3 text-uppercase fw-bold shadow-sm d-flex align-items-center justify-content-center" style="background-color: var(--color-espresso-deep); border-color: var(--color-gold); letter-spacing: 0.1em; min-height: 48px; font-size: 0.88rem;" onclick="instantBuyNow(<?= $product['id'] ?>)">
                            <i class="bi bi-lightning-charge-fill text-warning me-2"></i> Instant Privé Checkout
                        </button>
                    </div>

                    <!-- Atelier Guarantees & Pillars -->
                    <div class="p-3 bg-white border text-muted small shadow-soft" style="border-color: var(--color-border); border-radius: 2px;">
                        <div class="d-flex align-items-center gap-2 gap-md-3 mb-2">
                            <i class="bi bi-shield-check text-warning fs-5"></i>
                            <div>
                                <strong class="text-dark d-block">Buller Generational Guarantee</strong>
                                <span style="font-size: 0.8rem;">Unconditional lifetime structural warranty on leather hide, stitching, and hardware.</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 gap-md-3 mb-2">
                            <i class="bi bi-airplane text-dark fs-5"></i>
                            <div>
                                <strong class="text-dark d-block">Priority Express Air Cargo</strong>
                                <span style="font-size: 0.8rem;">Dispatched via DHL / Blue Dart with full customs clearance and real-time live AWB tracking.</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 gap-md-3">
                            <i class="bi bi-arrow-repeat text-dark fs-5"></i>
                            <div>
                                <strong class="text-dark d-block"><?= (int)$activeCountry['return_window_days'] ?>-Day Complimentary Exchanges</strong>
                                <span style="font-size: 0.8rem;">Complimentary sizing exchanges and returns within <?= (int)$activeCountry['return_window_days'] ?> days of receipt in <?= e($activeCountry['name']) ?>.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Craftsmanship Certification Banner (Mobile placement below actions & guarantees) -->
                    <div class="p-3 bg-white border shadow-soft d-flex d-lg-none align-items-center justify-content-between rounded-1 small text-muted mt-3" style="border-color: var(--color-border);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-patch-check-fill text-warning fs-5"></i>
                            <div>
                                <strong class="text-dark d-block">Hand-Inspected Masterpiece</strong>
                                <span style="font-size: 0.8rem;">Certified authentic vegetable pit-tanned hide</span>
                            </div>
                        </div>
                        <span class="badge bg-light text-dark border">Lot: <?= e($product['sku']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Deep-Dive Documentation Tabs -->
        <div class="mt-4 mt-md-5 pt-2 pt-md-4">
            <ul class="nav nav-tabs justify-content-start justify-content-md-center border-bottom mobile-pdp-tabs" style="border-color: var(--color-border) !important;" id="productDetailTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active font-serif text-uppercase fw-semibold" id="descTab-tab" data-bs-toggle="tab" data-bs-target="#descTab">
                        Atelier Architecture
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link font-serif text-uppercase fw-semibold" id="specsTab-tab" data-bs-toggle="tab" data-bs-target="#specsTab">
                        Hide & Materials Matrix
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link font-serif text-uppercase fw-semibold" id="careTab-tab" data-bs-toggle="tab" data-bs-target="#careTab">
                        Heritage Preservation
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link font-serif text-uppercase fw-semibold" id="deliveryTab-tab" data-bs-toggle="tab" data-bs-target="#deliveryTab">
                        <?= e($activeCountry['name']) ?> Delivery & Customs
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link font-serif text-uppercase fw-semibold" id="reviewsTab-tab" data-bs-toggle="tab" data-bs-target="#reviewsTab">
                        Patron Reviews (<?= count($reviews) ?>)
                    </button>
                </li>
            </ul>

            <div class="tab-content p-3 p-md-5 bg-white border border-top-0 shadow-soft" style="border-color: var(--color-border);">
                <!-- Tab 1: Description -->
                <div class="tab-pane fade show active" id="descTab">
                    <div class="max-w-800 mx-auto">
                        <span class="text-uppercase small text-gold fw-bold" style="letter-spacing: 0.2em;">Master Artisan Narrative</span>
                        <h3 class="font-serif mt-1 mb-3" style="color: var(--color-espresso-deep); font-size: 1.8rem;">
                            Crafted With Heirloom Integrity
                        </h3>
                        <p class="text-muted" style="line-height: 1.9; font-size: 1.05rem;">
                            <?= nl2br(e($product['detailed_description'])) ?>
                        </p>
                        
                        <div class="row g-4 mt-3 pt-3 border-top">
                            <div class="col-md-4">
                                <strong class="d-block text-dark font-serif fs-5">01. Hide Selection</strong>
                                <p class="small text-muted mt-1">Sourced exclusively from certified European organic pastures where livestock graze free from barbed fencing.</p>
                            </div>
                            <div class="col-md-4">
                                <strong class="d-block text-dark font-serif fs-5">02. 60-Day Tanning</strong>
                                <p class="small text-muted mt-1">Slow-steeped in natural chestnut and mimosa bark infusions to preserve the hide's resilient fibrous matrix.</p>
                            </div>
                            <div class="col-md-4">
                                <strong class="d-block text-dark font-serif fs-5">03. Hand Saddle-Stitch</strong>
                                <p class="small text-muted mt-1">Hand-threaded with two needles simultaneously using waxed French linen thread that will never unravel.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Specs & Hide Matrix -->
                <div class="tab-pane fade" id="specsTab">
                    <div class="max-w-800 mx-auto">
                        <h4 class="font-serif mb-3" style="color: var(--color-espresso-deep);">Authentic Technical Ledger</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <tbody>
                                    <tr>
                                        <th class="bg-light text-muted" style="width: 35%;">Hide Classification</th>
                                        <td><strong><?= e($product['leather_type'] ?? 'Prime European Full-Grain Hide') ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light text-muted">Tannery Provenance</th>
                                        <td>Santa Croce sull'Arno, Tuscany (Italy) / Northamptonshire (UK)</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light text-muted">Tanning Process</th>
                                        <td>100% Vegetable Pit-Tanned using organic oak, mimosa, and chestnut barks</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light text-muted">Hardware Casting</th>
                                        <td><?= e($product['hardware_finish'] ?? 'Solid Cast Antique Brass with Lacquer Protection') ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light text-muted">Hardware Mechanism</th>
                                        <td>Heavy-Gauge YKK Excella&reg; Polished Dual-Direction Tooth Zippers</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light text-muted">Thread Specification</th>
                                        <td>Fil Au Chinois Waxed French Linen Thread (#432 Gauge)</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light text-muted">Interior Lining</th>
                                        <td>Herringbone Woven Heavyweight Cotton Canvas / Japanese Cupro Silk</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light text-muted">Dimensions & Fit</th>
                                        <td><?= e($product['dimensions'] ?? 'Bespoke Proportions') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Care Instructions -->
                <div class="tab-pane fade" id="careTab">
                    <div class="max-w-800 mx-auto">
                        <h4 class="font-serif mb-3" style="color: var(--color-espresso-deep);">Preserving Your Leather Over Decades</h4>
                        <p class="text-muted" style="line-height: 1.8;">
                            <?= nl2br(e($product['care_instructions'] ?? 'Condition bi-annually with natural beeswax balm. Keep away from prolonged excessive heat sources and store in the provided cotton dust cover.')) ?>
                        </p>

                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-1 bg-light">
                                    <strong class="text-dark d-block mb-1"><i class="bi bi-droplet-half text-primary me-2"></i> Water & Rain Care</strong>
                                    <p class="small text-muted mb-0">If exposed to rain, allow the hide to dry naturally at ambient room temperature. Never place adjacent to radiators or hairdryers.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-1 bg-light">
                                    <strong class="text-dark d-block mb-1"><i class="bi bi-sun text-warning me-2"></i> Patina Evolution</strong>
                                    <p class="small text-muted mb-0">Full-grain vegetable leather absorbs natural oils from your hands, developing a deep, glossy cognac sheen unique to your journey.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Delivery & Customs for Active Country -->
                <div class="tab-pane fade" id="deliveryTab">
                    <div class="max-w-800 mx-auto">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <?= country_flag($activeCountry['code'], $activeCountry['name'], 28) ?>
                            <h4 class="font-serif mb-0" style="color: var(--color-espresso-deep);">
                                Localized Delivery To <?= e($activeCountry['name']) ?>
                            </h4>
                        </div>
                        <p class="text-muted" style="line-height: 1.8;">
                            All orders bound for <strong><?= e($activeCountry['name']) ?></strong> are dispatched via priority air cargo directly from our European atelier hub to your doorstep with full tracking.
                        </p>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th class="bg-light text-muted" style="width: 35%;">Designated Carrier</th>
                                        <td><strong><?= ($activeCountry['code'] === 'IN') ? 'Blue Dart Express / DHL Express' : 'DHL Express Worldwide / FedEx International' ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light text-muted">Transit Window</th>
                                        <td>2 &ndash; 4 Business Days with Signature On Delivery</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light text-muted">Customs & Import Duties</th>
                                        <td><span class="badge bg-success">Pre-Cleared & Paid by Buller Leather</span> (Zero Hidden Fees)</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light text-muted">Return Policy</th>
                                        <td><?= (int)$activeCountry['return_window_days'] ?> Days Return & Exchange Period within <?= e($activeCountry['name']) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Client Reviews & Write a Review Form -->
                <div class="tab-pane fade" id="reviewsTab">
                    <div class="max-w-800 mx-auto">
                        <!-- Score Header & Write Review Button -->
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
                            <div>
                                <h4 class="font-serif mb-1" style="color: var(--color-espresso-deep);">Patron Endorsements & Impressions</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-4 fw-bold text-dark"><?= $avgRating ?></span>
                                    <div class="text-warning small">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?= ($i <= round($avgRating)) ? '-fill' : '' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-muted small">&bull; <?= count($reviews) ?> Verified Endorsements</span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-dark btn-sm" onclick="document.getElementById('writeReviewCard').classList.toggle('d-none');">
                                <i class="bi bi-pencil-square me-1"></i> Write Patron Testimonial
                            </button>
                        </div>

                        <!-- Write Review Card Form (Hidden by default, toggleable) -->
                        <div id="writeReviewCard" class="card border p-4 mb-4 shadow-sm d-none" style="border-color: var(--color-border); background-color: var(--color-ivory);">
                            <h5 class="font-serif mb-3" style="color: var(--color-espresso-deep);">Share Your Atelier Experience</h5>
                            <form action="<?= url('product/review/submit') ?>" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="product_slug" value="<?= $product['slug'] ?>">

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Your Full Name</label>
                                        <input type="text" name="patron_name" class="form-control" value="<?= Auth::isLoggedIn() ? e(Auth::user()['name']) : '' ?>" placeholder="e.g. Lord Julian Sterling" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Email Address (Kept Private)</label>
                                        <input type="email" name="patron_email" class="form-control" value="<?= Auth::isLoggedIn() ? e(Auth::user()['email']) : '' ?>" placeholder="name@domain.com" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted d-block">Overall Impression Rating</label>
                                    <div class="d-flex gap-2 text-warning fs-4 cursor-pointer" id="starPicker">
                                        <i class="bi bi-star-fill" data-rating="1" onclick="setRating(1)"></i>
                                        <i class="bi bi-star-fill" data-rating="2" onclick="setRating(2)"></i>
                                        <i class="bi bi-star-fill" data-rating="3" onclick="setRating(3)"></i>
                                        <i class="bi bi-star-fill" data-rating="4" onclick="setRating(4)"></i>
                                        <i class="bi bi-star-fill" data-rating="5" onclick="setRating(5)"></i>
                                    </div>
                                    <input type="hidden" name="rating" id="selectedRating" value="5">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Headline / Title</label>
                                    <input type="text" name="review_title" class="form-control" placeholder="e.g. Flawless Leather Craftsmanship & Majestic Finish" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Your Detailed Impressions</label>
                                    <textarea name="review_body" rows="4" class="form-control" placeholder="Share your experience regarding hide touch, scent, weight, craftsmanship, and delivery..." required></textarea>
                                </div>

                                <button type="submit" class="btn btn-buller-primary py-2 px-4">
                                    <i class="bi bi-send-check me-1"></i> Submit Patron Review
                                </button>
                            </form>
                        </div>

                        <!-- Existing Reviews List -->
                        <?php if (empty($reviews)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-chat-square-quote fs-1 mb-2 d-block text-warning"></i>
                                <h5>Be The First To Endorse This Piece</h5>
                                <p class="small">No customer impressions recorded yet. Click <strong>"Write Patron Testimonial"</strong> above to share your thoughts.</p>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-4">
                                <?php foreach ($reviews as $rev): ?>
                                    <div class="p-3 border rounded-1 bg-white shadow-soft">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="text-warning small">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi bi-star<?= ($i <= $rev['rating']) ? '-fill' : '' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="text-muted small"><?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                                        </div>
                                        <h6 class="font-serif fw-bold mb-2" style="color: var(--color-espresso-deep); font-size: 1.1rem;">
                                            &ldquo;<?= e($rev['review_title']) ?>&rdquo;
                                        </h6>
                                        <p class="small text-muted mb-3" style="line-height: 1.7;"><?= nl2br(e($rev['review_body'])) ?></p>
                                        <div class="d-flex align-items-center gap-2 small text-muted pt-2 border-top">
                                            <?= country_flag($rev['country_code'] ?? 'US', $rev['country_name'] ?? 'United States', 18) ?>
                                            <strong class="text-dark"><?= e($rev['user_name']) ?></strong>
                                            <span class="badge bg-success-subtle text-success border border-success" style="font-size: 0.65rem;">
                                                <i class="bi bi-patch-check-fill me-1"></i> Verified Patron
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Frequently Asked Questions (FAQ) Section -->
        <div class="mt-5 pt-4">
            <div class="max-w-800 mx-auto">
                <div class="text-center mb-4">
                    <span class="text-uppercase small text-gold fw-bold" style="letter-spacing: 0.2em;">Bespoke Inquiries</span>
                    <h3 class="font-serif mt-1" style="color: var(--color-espresso-deep);">Frequently Asked Questions</h3>
                </div>

                <div class="accordion accordion-flush bg-white border p-3 shadow-soft" id="productFaqAccordion" style="border-color: var(--color-border);">
                    <div class="accordion-item border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed font-serif fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How will this leather age over months and decades?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#productFaqAccordion">
                            <div class="accordion-body text-muted small" style="line-height: 1.8;">
                                Our vegetable-tanned hides do not deteriorate like plastic-coated synthetic leathers. Instead, exposure to sunlight, ambient air, and natural oils from your touch produces a coveted rich caramel or deep espresso patina over time that tells your unique life journey.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed font-serif fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                What if it rains or liquid spills on the piece?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#productFaqAccordion">
                            <div class="accordion-body text-muted small" style="line-height: 1.8;">
                                Simply dab the excess moisture gently with a clean microfibre cloth. Do not rub vigorously or apply artificial heat like hairdryers. Allow the hide to dry at ambient room temperature, and any temporary water spot will harmonize naturally into the leather grain.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed font-serif fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Can I exchange for a different size if it doesn't fit?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#productFaqAccordion">
                            <div class="accordion-body text-muted small" style="line-height: 1.8;">
                                Absolutely. We provide a <?= (int)$activeCountry['return_window_days'] ?>-day complimentary size exchange guarantee for patrons in <?= e($activeCountry['name']) ?>. Our concierge desk will arrange courier collection from your address at no added charge.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed font-serif fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                How do I activate or claim the Lifetime Warranty?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#productFaqAccordion">
                            <div class="accordion-body text-muted small" style="line-height: 1.8;">
                                Every piece is stamped with its unique Atelier ID and registered in our worldwide ledger. If any brass rivet, zipper, or saddle stitch ever requires restoration, contact our client concierge at <code>concierge@bullerleather.com</code> for lifetime atelier service.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Masterpieces / "You May Also Admire" -->
        <?php if (!empty($relatedProducts)): ?>
            <div class="mt-5 pt-5 border-top" style="border-color: var(--color-border) !important;">
                <div class="text-center mb-5">
                    <span class="text-uppercase small text-gold fw-bold" style="letter-spacing: 0.25em;">Curated Complements</span>
                    <h2 class="font-serif mt-1" style="color: var(--color-espresso-deep); font-size: 2.2rem;">You May Also Admire</h2>
                </div>

                <div class="row g-2 g-sm-3 g-md-4">
                    <?php foreach ($relatedProducts as $rel): 
                        $relPrice = MarketContext::evaluatePricing(
                            (float)$rel['original_price'],
                            $rel['discount_price'] ? (float)$rel['discount_price'] : null,
                            $rel['discount_start_date'],
                            $rel['discount_end_date']
                        );
                    ?>
                        <div class="col-6 col-md-6 col-lg-3 mb-2 mb-md-3">
                            <div class="product-card h-100 bg-white border p-2 p-sm-3 shadow-soft d-flex flex-column justify-content-between" data-product-url="<?= url('product?slug=' . urlencode($rel['slug'])) ?>" style="border-color: var(--color-border);">
                                <div class="position-relative mb-2 mb-sm-3 overflow-hidden related-prod-img-wrapper">
                                    <a href="<?= url('product?slug=' . urlencode($rel['slug'])) ?>">
                                        <img src="<?= asset($rel['primary_image']) ?>" alt="<?= e($rel['title']) ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.4s ease;">
                                    </a>
                                    <?php if ($relPrice['is_discounted']): ?>
                                        <div class="product-badges">
                                            <span class="badge-luxury badge-sale">-<?= $relPrice['discount_percent'] ?>%</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="flex-grow-1">
                                    <span class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.08em;"><?= e($rel['category_name']) ?></span>
                                    <h5 class="font-serif mt-1 mb-2" style="font-size: clamp(0.88rem, 2vw, 1.05rem); line-height: 1.3;">
                                        <a href="<?= url('product?slug=' . urlencode($rel['slug'])) ?>" class="text-decoration-none" style="color: var(--color-espresso-deep);">
                                            <?= e($rel['title']) ?>
                                        </a>
                                    </h5>
                                </div>

                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mt-2 pt-2 border-top gap-1">
                                    <div>
                                        <strong class="font-serif" style="color: var(--color-espresso-deep); font-size: clamp(0.85rem, 2vw, 1rem);">
                                            <?= $relPrice['formatted_final'] ?>
                                        </strong>
                                        <?php if ($relPrice['is_discounted']): ?>
                                            <small class="text-muted text-decoration-line-through d-block" style="font-size: 0.72rem;">
                                                <?= $relPrice['formatted_original'] ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= url('product?slug=' . urlencode($rel['slug'])) ?>" class="btn btn-outline-dark btn-sm py-1 px-2 px-sm-3 w-100 w-sm-auto mt-1 mt-sm-0" style="font-size: 0.72rem;">
                                        View Piece
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Size & Fit Guide Modal -->
<div class="modal fade" id="sizeGuideModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: var(--color-espresso-deep); border-bottom: 2px solid var(--color-gold);">
                <h5 class="modal-title font-serif fs-5">
                    <i class="bi bi-rulers text-warning me-2"></i> Atelier Size & Fit Guide — <?= e($product['category_name']) ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4">
                    Buller Leather garments and accessories are crafted to traditional tailored proportions. Refer to the matrix below or contact our private client concierge for bespoke sizing appointments.
                </p>

                <?php if ($catSlug === 'leather-jackets'): ?>
                    <h6 class="font-serif fw-bold mb-2" style="color: var(--color-espresso-deep);">Biker & Flight Jacket Sizing Matrix (Inches & CM)</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center small align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Size Label</th>
                                    <th>Chest (Inches / CM)</th>
                                    <th>Shoulder Width</th>
                                    <th>Sleeve Length</th>
                                    <th>Jacket Length</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td><strong>38 (S)</strong></td><td>38" / 96 cm</td><td>17.5" / 44 cm</td><td>25.5" / 65 cm</td><td>25.0" / 63 cm</td></tr>
                                <tr class="table-warning-subtle"><td><strong>40 (M)</strong></td><td>40" / 102 cm</td><td>18.0" / 46 cm</td><td>26.0" / 66 cm</td><td>25.5" / 65 cm</td></tr>
                                <tr><td><strong>42 (L)</strong></td><td>42" / 107 cm</td><td>18.5" / 47 cm</td><td>26.5" / 67 cm</td><td>26.0" / 66 cm</td></tr>
                                <tr><td><strong>44 (XL)</strong></td><td>44" / 112 cm</td><td>19.0" / 48 cm</td><td>27.0" / 68 cm</td><td>26.5" / 67 cm</td></tr>
                                <tr><td><strong>46 (XXL)</strong></td><td>46" / 117 cm</td><td>19.5" / 50 cm</td><td>27.5" / 70 cm</td><td>27.0" / 68 cm</td></tr>
                            </tbody>
                        </table>
                    </div>
                <?php elseif ($catSlug === 'shoes-boots'): ?>
                    <h6 class="font-serif fw-bold mb-2" style="color: var(--color-espresso-deep);">Footwear Sizing Conversion Matrix</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center small align-middle">
                            <thead class="table-light">
                                <tr><th>UK Size</th><th>US Size</th><th>EU Size</th><th>Foot Length (CM)</th><th>Foot Length (Inches)</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>UK 7</td><td>US 8</td><td>EU 41</td><td>25.8 cm</td><td>10.15 in</td></tr>
                                <tr class="table-warning-subtle"><td>UK 8</td><td>US 9</td><td>EU 42</td><td>26.6 cm</td><td>10.47 in</td></tr>
                                <tr><td>UK 9</td><td>US 10</td><td>EU 43</td><td>27.5 cm</td><td>10.82 in</td></tr>
                                <tr><td>UK 10</td><td>US 11</td><td>EU 44</td><td>28.3 cm</td><td>11.14 in</td></tr>
                                <tr><td>UK 11</td><td>US 12</td><td>EU 45</td><td>29.2 cm</td><td>11.49 in</td></tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <h6 class="font-serif fw-bold mb-2" style="color: var(--color-espresso-deep);">Belts & Accessories Sizing</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center small align-middle">
                            <thead class="table-light">
                                <tr><th>Belt Size</th><th>Waist Measurement</th><th>Total Strap Length</th><th>Width</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>32" (80cm)</td><td>30 - 32 inches</td><td>38 inches</td><td>35 mm / 1.38 in</td></tr>
                                <tr class="table-warning-subtle"><td>34" (85cm)</td><td>32 - 34 inches</td><td>40 inches</td><td>35 mm / 1.38 in</td></tr>
                                <tr><td>36" (90cm)</td><td>34 - 36 inches</td><td>42 inches</td><td>35 mm / 1.38 in</td></tr>
                                <tr><td>38" (95cm)</td><td>36 - 38 inches</td><td>44 inches</td><td>35 mm / 1.38 in</td></tr>
                                <tr><td>40" (100cm)</td><td>38 - 40 inches</td><td>46 inches</td><td>35 mm / 1.38 in</td></tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-dark btn-sm" data-bs-dismiss="modal">Close Guide</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Product Detail Styling */
.pdp-breadcrumbs {
    display: flex;
    align-items: center;
    white-space: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.pdp-breadcrumbs::-webkit-scrollbar {
    display: none;
}
.pdp-breadcrumbs .active-crumb {
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: inline-block;
    vertical-align: bottom;
}
@media (min-width: 768px) {
    .pdp-breadcrumbs .active-crumb {
        max-width: none;
    }
}

.pdp-title {
    font-size: clamp(1.4rem, 5vw, 2.25rem);
    color: var(--color-espresso-deep);
    line-height: 1.2;
    letter-spacing: -0.01em;
}

.pdp-price-value {
    color: var(--color-espresso-deep);
    font-size: clamp(1.6rem, 5.5vw, 2.2rem);
    font-family: var(--font-sans);
    font-variant-numeric: tabular-nums;
    line-height: 1.1;
}

/* Badges on main showcase */
.pdp-badges {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    z-index: 2;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.pdp-badges .badge-luxury {
    font-size: 0.62rem;
    padding: 3px 8px;
    letter-spacing: 0.05em;
    border-radius: 2px;
}
@media (min-width: 768px) {
    .pdp-badges {
        top: 1.25rem;
        left: 1.25rem;
        gap: 6px;
    }
    .pdp-badges .badge-luxury {
        font-size: 0.75rem;
        padding: 4px 10px;
    }
}

/* Gallery Thumbnails Strip */
.pdp-thumbnails-strip {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.pdp-thumbnails-strip::-webkit-scrollbar {
    display: none;
}
.pdp-thumbnails-strip .product-thumb-box {
    flex: 0 0 62px;
    width: 62px;
    height: 62px;
    padding: 2px;
    border-radius: 2px;
    transition: all 0.2s ease;
    cursor: pointer;
}
@media (min-width: 768px) {
    .pdp-thumbnails-strip .product-thumb-box {
        flex: 0 0 85px;
        width: 85px;
        height: 85px;
        padding: 3px;
    }
}

/* Size Pills */
.size-pill-btn {
    border: 1px solid var(--color-border);
    background: #ffffff;
    color: var(--color-espresso-deep);
    padding: 6px 14px;
    font-size: 0.82rem;
    font-weight: 600;
    border-radius: 2px;
    transition: all 0.2s ease;
    cursor: pointer;
    min-height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.size-pill-btn:hover {
    border-color: var(--color-gold);
    color: var(--color-gold-dark);
}
.size-pill-btn.active {
    background-color: var(--color-espresso-deep);
    color: #ffffff !important;
    border-color: var(--color-gold);
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

/* Color Swatches */
.color-swatch-btn {
    background: transparent;
    border: 2px solid transparent;
    border-radius: 50%;
    padding: 2px;
    cursor: pointer;
    transition: all 0.22s ease-in-out;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.color-swatch-btn .swatch-circle {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: block;
    box-shadow: inset 0 0 4px rgba(0,0,0,0.4);
}
.color-swatch-btn:hover {
    border-color: var(--color-gold);
    transform: scale(1.08);
}
.color-swatch-btn.active {
    border-color: var(--color-gold) !important;
    box-shadow: 0 0 0 2px var(--color-espresso-deep), 0 0 8px rgba(197, 168, 128, 0.4);
    transform: scale(1.08);
}

/* Active Thumb Highlight */
.active-thumb {
    border-color: var(--color-gold) !important;
    box-shadow: 0 0 0 2px var(--color-gold);
}
.cursor-pointer {
    cursor: pointer;
}

/* Action Bar PDP wishlist & quantity override */
.pdp-action-group {
    min-height: 48px;
}
.pdp-qty-box {
    flex: 0 0 auto;
}
.pdp-wishlist-btn {
    width: 48px !important;
    height: 48px !important;
    min-width: 48px !important;
    max-width: 48px !important;
    top: auto !important;
    right: auto !important;
    font-size: 1.15rem !important;
    border-radius: 2px !important;
    padding: 0 !important;
    border: 1px solid var(--color-border) !important;
    background: #ffffff !important;
}
.pdp-wishlist-btn:hover {
    border-color: var(--color-gold) !important;
    background: var(--color-ivory) !important;
}

/* Mobile Tabs Horizontal Swipeable Pill Bar */
@media (max-width: 767.98px) {
    #mainProductPhoto {
        max-height: 360px !important;
    }
    .mobile-pdp-tabs {
        display: flex !important;
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        scrollbar-width: none !important;
        gap: 0.45rem !important;
        padding-bottom: 0.6rem !important;
        border-bottom: 1px solid var(--color-border) !important;
        justify-content: flex-start !important;
    }
    .mobile-pdp-tabs::-webkit-scrollbar {
        display: none !important;
    }
    .mobile-pdp-tabs .nav-item {
        flex: 0 0 auto !important;
    }
    .mobile-pdp-tabs .nav-link {
        padding: 0.5rem 0.9rem !important;
        font-size: 0.76rem !important;
        letter-spacing: 0.04em !important;
        border-radius: 20px !important;
        border: 1px solid var(--color-border) !important;
        background: #ffffff !important;
        color: var(--color-espresso-deep) !important;
        white-space: nowrap !important;
        margin-bottom: 0 !important;
    }
    .mobile-pdp-tabs .nav-link.active {
        background-color: var(--color-espresso-deep) !important;
        color: var(--color-gold) !important;
        border-color: var(--color-espresso-deep) !important;
        box-shadow: 0 2px 6px rgba(18, 11, 6, 0.15) !important;
    }
    .tab-content .table th,
    .tab-content .table td {
        padding: 0.45rem 0.6rem !important;
        font-size: 0.8rem !important;
    }
}
@media (min-width: 768px) {
    .mobile-pdp-tabs .nav-link {
        padding: 0.9rem 1.4rem;
        letter-spacing: 0.08em;
        color: var(--color-espresso-deep);
        border: none;
        border-bottom: 2px solid transparent;
    }
    .mobile-pdp-tabs .nav-link.active {
        border-bottom-color: var(--color-gold);
        color: var(--color-espresso-deep);
        background: transparent;
    }
}
</style>

<script>
// Switch main gallery photo
function switchProductPhoto(thumbElement, newSrc) {
    document.getElementById('mainProductPhoto').src = newSrc;
    document.querySelectorAll('.product-thumb-box').forEach(el => {
        el.classList.remove('active-thumb');
        el.style.borderColor = 'var(--color-border)';
    });
    thumbElement.classList.add('active-thumb');
    thumbElement.style.borderColor = 'var(--color-gold)';
}

// Select product size
function selectProductSize(button, sizeValue) {
    document.querySelectorAll('#productSizeOptions .size-pill-btn').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
    const sizeInput = document.getElementById('selectedProductSize');
    if (sizeInput) sizeInput.value = sizeValue;
}

// Select product colorway
function selectProductColor(button, colorName) {
    document.querySelectorAll('#productColorOptions .color-swatch-btn').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
    const colorLabel = document.getElementById('selectedColorName');
    if (colorLabel) colorLabel.textContent = colorName;
    const colorInput = document.getElementById('selectedProductColor');
    if (colorInput) colorInput.value = colorName;
}

// Instant Buy Now Checkout
async function instantBuyNow(productId) {
    const qty = parseInt(document.getElementById('productQty').value || '1', 10);
    try {
        const res = await fetch(window.BULLER_CONFIG.baseUrl + '/api/cart.php?action=add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: qty })
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = window.BULLER_CONFIG.baseUrl + '/checkout';
        } else {
            showToast('Notice', data.message, 'warning');
        }
    } catch(err) {
        window.location.href = window.BULLER_CONFIG.baseUrl + '/checkout';
    }
}

// Set interactive review star rating
function setRating(val) {
    document.getElementById('selectedRating').value = val;
    const stars = document.querySelectorAll('#starPicker i');
    stars.forEach((star, index) => {
        if (index < val) {
            star.classList.remove('bi-star');
            star.classList.add('bi-star-fill');
        } else {
            star.classList.remove('bi-star-fill');
            star.classList.add('bi-star');
        }
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
