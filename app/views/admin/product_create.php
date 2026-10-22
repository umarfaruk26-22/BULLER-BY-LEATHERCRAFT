<?php
use App\Core\Auth;

$isSuper = Auth::isSuperAdmin();
$pageTitle = 'Add New Handcrafted Product — ' . e($currentCountry['name']);
require __DIR__ . '/../layouts/admin_header.php';

$redirectRoute = $_GET['redirect_route'] ?? ($isSuper ? 'superadmin/products' : ('admin/products?country_id=' . $currentCountry['id']));
?>

<div class="container-fluid p-0">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item">
                        <a href="<?= url($isSuper ? 'superadmin/products' : ('admin/products?country_id=' . $currentCountry['id'])) ?>" class="text-decoration-none text-muted">
                            <i class="bi bi-arrow-left me-1"></i> Products Catalog
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">New Product</li>
                </ol>
            </nav>
            <h1 class="h3 font-serif mb-1" style="color: var(--color-espresso-deep);">
                <i class="bi bi-plus-circle-fill text-warning me-2"></i> Add New Handcrafted Product
            </h1>
            <p class="text-muted small mb-0">Publish an artisan leather piece to the <?= country_flag($currentCountry['code'], $currentCountry['name'], 18) ?> <strong><?= e($currentCountry['name']) ?></strong> catalog with color photo galleries and localized pricing.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="<?= url($redirectRoute) ?>" class="btn btn-outline-secondary btn-sm px-3">
                <i class="bi bi-x-circle me-1"></i> Discard
            </a>
            <button type="submit" form="createProductForm" class="btn btn-buller-gold btn-sm px-4 shadow-sm fw-semibold">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Publish Handcrafted Product
            </button>
        </div>
    </div>

    <!-- Main Form -->
    <form id="createProductForm" action="<?= url('admin/products/create') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
        <input type="hidden" name="country_id" value="<?= $currentCountry['id'] ?>">
        <input type="hidden" name="redirect_route" value="<?= e($redirectRoute) ?>">

        <div class="row g-4">
            <!-- ============================================================== -->
            <!-- LEFT COLUMN (8 COLS): MASTER SPECS & COLOR GALLERIES -->
            <!-- ============================================================== -->
            <div class="col-lg-8">
                <!-- 1. Master Piece Information -->
                <div class="bg-white border p-4 shadow-soft mb-4" style="border-color: var(--admin-card-border); border-radius: 4px;">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <h5 class="font-serif mb-0 text-dark">
                            <i class="bi bi-journal-text text-warning me-2"></i> 1. Master Piece Specifications
                        </h5>
                        <span class="badge bg-light text-muted border">Core Catalog Attributes</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Product Title *</label>
                            <input type="text" id="prodTitle" name="title" class="form-control form-control-lg fs-6" placeholder="e.g. The Sovereign Full-Grain Biker Jacket" required autocomplete="off">
                            <small class="text-muted">The primary headline displayed across global storefronts.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">SKU Identifier</label>
                            <input type="text" id="prodSku" name="sku" class="form-control form-control-lg fs-6 font-monospace text-uppercase" placeholder="Auto-generated if empty">
                            <small class="text-muted">Unique inventory barcode / stock code.</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" disabled selected>-- Select Boutique Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Storefront URL Slug</label>
                            <input type="text" id="prodSlug" name="slug" class="form-control font-monospace" placeholder="e.g. the-sovereign-full-grain-biker-jacket">
                            <small class="text-muted">Leave blank to auto-generate from title.</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Leather Grade / Material</label>
                            <input type="text" name="leather_type" class="form-control" placeholder="e.g. Full-Grain Artisan Calfskin" value="Full-Grain Artisan Leather">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hardware Finish</label>
                            <input type="text" name="hardware_finish" class="form-control" placeholder="e.g. Antique Solid Brass" value="Antique Brass">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Dimensions / Proportions</label>
                            <input type="text" name="dimensions" class="form-control" placeholder="e.g. 16.5 x 12.0 x 4.5 in">
                        </div>
                    </div>



                    <div class="mb-3">
                        <label class="form-label small fw-bold">Short Product Summary</label>
                        <textarea name="short_description" class="form-control" rows="2" placeholder="Brief summary displayed on catalog cards, cart, and search previews..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Detailed Craftsmanship & Heritage Story</label>
                        <textarea name="detailed_description" class="form-control font-monospace small" rows="6" placeholder="Describe the artisan hand-stitching, tanning process, pocket architecture, lifetime warranty, and heritage details..."></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Leather Care & Conditioning Instructions</label>
                        <input type="text" name="care_instructions" class="form-control" placeholder="e.g. Condition biannually with natural beeswax leather balm; protect from prolonged precipitation.">
                    </div>
                </div>

                <!-- 2. Color-Tagged Photos & Gallery Manager -->
                <div class="bg-white border p-4 shadow-soft mb-4" style="border-color: var(--admin-card-border); border-radius: 4px;">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <div>
                            <h5 class="font-serif mb-1 text-dark">
                                <i class="bi bi-images text-warning me-2"></i> 2. Product Photos & Color Gallery
                            </h5>
                            <p class="text-muted small mb-0">Upload high-resolution photography and link each photo to a specific color swatch.</p>
                        </div>
                        <span class="badge bg-warning text-dark"><i class="bi bi-magic me-1"></i> Dynamic PDP Swatches</span>
                    </div>

                    <!-- Primary Showcase Photo -->
                    <div class="p-3 border rounded mb-4" style="background: rgba(197, 160, 89, 0.05); border-color: rgba(197, 160, 89, 0.3) !important;">
                        <div class="row align-items-center g-3">
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-dark d-flex align-items-center gap-1">
                                    <i class="bi bi-star-fill text-warning"></i> Primary Showcase Photo (Main Catalog Image) *
                                </label>
                                <input type="file" id="primaryImageInput" name="primary_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" required onchange="previewSingleImage(this, 'primaryPreview')">
                                <small class="text-muted">High-res front showcase photo (JPG, PNG, WEBP).</small>
                            </div>
                            <div class="col-md-5 text-center">
                                <div class="border rounded bg-white p-2 d-inline-block shadow-xs" style="width: 110px; height: 110px;">
                                    <img id="primaryPreview" src="<?= asset('assets/images/buller-logo-gold.svg') ?>" alt="Primary Preview" style="width: 100%; height: 100%; object-fit: contain; opacity: 0.4;">
                                </div>
                                <div class="small text-muted mt-1">Primary View</div>
                            </div>
                        </div>
                    </div>

                    <!-- Minimal Product Colors Bar (Directly Linked to Gallery Photos) -->
                    <div class="p-3 bg-light border rounded mb-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <!-- Left: Active Color Pills -->
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="small fw-bold text-dark d-flex align-items-center gap-1" style="font-size: 0.8rem;">
                                    <i class="bi bi-palette-fill text-warning"></i> Product Colors:
                                </span>
                                <div id="activeColorsContainer" class="d-flex flex-wrap gap-1.5 align-items-center">
                                    <!-- Populated dynamically by JS -->
                                </div>
                                <input type="hidden" id="finalColorInput" name="color" value="Espresso Brown, Cognac Tan, Classic Black" required>
                            </div>

                            <!-- Right: Compact Inline Custom Color Creator -->
                            <div class="d-flex align-items-center gap-2 ms-md-auto">
                                <div class="input-group input-group-sm" style="max-width: 270px;">
                                    <input type="color" id="customColorPicker" class="form-control form-control-color p-0 border" value="#5c3a21" title="Pick color shade" style="width: 32px; height: 31px; border-radius: 4px 0 0 4px;">
                                    <input type="text" id="customColorNameInput" class="form-control form-control-sm" placeholder="Add new color..." autocomplete="off">
                                    <button type="button" class="btn btn-buller-gold btn-sm px-2.5 fw-semibold text-nowrap" onclick="addNewCustomColor()">
                                        <i class="bi bi-plus-lg"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Multi-Photo Uploader with Color Tagging -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label small fw-bold mb-0 text-dark">
                                <i class="bi bi-camera me-1"></i> Color-Specific Gallery Photos
                            </label>
                            <button type="button" class="btn btn-outline-dark btn-sm d-flex align-items-center gap-1 shadow-xs" onclick="addGalleryPhotoRow()">
                                <i class="bi bi-plus-circle-fill text-warning"></i> Add Another Photo
                            </button>
                        </div>
                        <p class="text-muted small mb-3">When a customer clicks a color swatch on the product page, the gallery automatically switches to show the photos tagged with that color.</p>

                        <!-- Dynamic Photo Rows Container -->
                        <div id="galleryRowsContainer" class="d-flex flex-column gap-3">
                            <!-- Initial Photo Row -->
                            <div class="gallery-photo-row p-3 border rounded bg-light position-relative">
                                <div class="row g-3 align-items-center">
                                    <div class="col-auto">
                                        <div class="gallery-thumb-box border rounded bg-white d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; overflow: hidden;">
                                            <i class="bi bi-image text-muted fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold">Select Photo File</label>
                                        <input type="file" name="gallery_images[]" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewGalleryRow(this)">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold">Assign to Color Swatch</label>
                                        <select name="gallery_colors[]" class="form-select form-select-sm color-select-dropdown">
                                            <option value="">-- General (All Colors) --</option>
                                        </select>
                                    </div>
                                    <div class="col-auto text-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2" onclick="removeGalleryRow(this)" title="Remove this photo">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="addGalleryPhotoRow()">
                                <i class="bi bi-plus-lg me-1"></i> Add Another Photo
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- RIGHT COLUMN (4 COLS): REGIONAL PRICING, INVENTORY & STATUS -->
            <!-- ============================================================== -->
            <div class="col-lg-4">
                <!-- Market & Local Pricing Card -->
                <div class="bg-white border p-4 shadow-soft mb-4" style="border-color: var(--admin-card-border); border-radius: 4px;">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <h5 class="font-serif mb-0 text-dark">
                            <i class="bi bi-currency-dollar text-warning me-1"></i> Local Market Pricing
                        </h5>
                        <span class="badge bg-dark text-warning"><?= e($currentCountry['code']) ?></span>
                    </div>

                    <div class="p-2 mb-3 rounded border d-flex align-items-center gap-2" style="background: rgba(197, 160, 89, 0.08); border-color: rgba(197, 160, 89, 0.3) !important;">
                        <?= country_flag($currentCountry['code'], $currentCountry['name'], 24) ?>
                        <div>
                            <strong class="small d-block text-dark"><?= e($currentCountry['name']) ?> Market</strong>
                            <span class="small text-muted">Currency: <?= e($currentCountry['currency_code']) ?> (<?= e($currentCountry['currency_symbol']) ?>)</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Regular / Retail Price (<?= e($currentCountry['currency_symbol']) ?>) *</label>
                        <div class="input-group">
                            <span class="input-group-text"><?= e($currentCountry['currency_symbol']) ?></span>
                            <input type="number" step="0.01" name="original_price" class="form-control form-control-lg fs-5 fw-bold" placeholder="0.00" required>
                        </div>
                        <small class="text-muted">Standard artisan catalog price.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Promotional Discount Price (<?= e($currentCountry['currency_symbol']) ?>)</label>
                        <div class="input-group">
                            <span class="input-group-text"><?= e($currentCountry['currency_symbol']) ?></span>
                            <input type="number" step="0.01" name="discount_price" class="form-control" placeholder="Leave empty if no discount">
                        </div>
                        <small class="text-muted">Shows strike-through price and sale badge.</small>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold" style="font-size: 0.75rem;">Discount Start</label>
                            <input type="date" name="discount_start_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold" style="font-size: 0.75rem;">Discount End</label>
                            <input type="date" name="discount_end_date" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>

                <!-- Inventory & Stock Card -->
                <div class="bg-white border p-4 shadow-soft mb-4" style="border-color: var(--admin-card-border); border-radius: 4px;">
                    <div class="pb-3 mb-3 border-bottom">
                        <h5 class="font-serif mb-0 text-dark">
                            <i class="bi bi-box-seam text-warning me-1"></i> Inventory & Stock
                        </h5>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Physical Stock Quantity *</label>
                        <input type="number" name="stock_quantity" class="form-control form-control-lg fs-5" value="25" min="0" required>
                        <small class="text-muted">Available stock in regional fulfillment warehouse.</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Low Stock Warning Threshold</label>
                        <input type="number" name="low_stock_threshold" class="form-control" value="5" min="1">
                        <small class="text-muted">Triggers low-stock alerts on executive dashboard.</small>
                    </div>
                </div>

                <!-- Visibility, Merchandising & Status Card -->
                <div class="bg-white border p-4 shadow-soft mb-4" style="border-color: var(--admin-card-border); border-radius: 4px;">
                    <div class="pb-3 mb-3 border-bottom">
                        <h5 class="font-serif mb-0 text-dark">
                            <i class="bi bi-eye text-warning me-1"></i> Catalog Visibility
                        </h5>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Publishing State *</label>
                        <select name="status" class="form-select">
                            <option value="published" selected>Published (Live on Storefront)</option>
                            <option value="draft">Draft (Hidden from Public)</option>
                            <option value="archived">Archived / Retired</option>
                        </select>
                    </div>

                    <div class="d-flex flex-column gap-2 p-3 bg-light rounded border mb-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1" id="isNewArrival" checked>
                            <label class="form-check-label small fw-semibold" for="isNewArrival">
                                Mark as New Arrival
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured">
                            <label class="form-check-label small fw-semibold" for="isFeatured">
                                Feature on Homepage Showcase
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_bestseller" value="1" id="isBestseller">
                            <label class="form-check-label small fw-semibold" for="isBestseller">
                                Mark as Heritage Bestseller
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Final Action Box -->
                <div class="bg-dark text-white p-4 rounded shadow-soft text-center" style="background: var(--color-espresso-deep) !important; border: 1px solid var(--color-gold);">
                    <button type="submit" class="btn btn-buller-gold btn-lg w-100 fw-bold mb-2 shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Publish Handcrafted Product
                    </button>
                    <a href="<?= url($redirectRoute) ?>" class="text-white-50 small text-decoration-none d-block">
                        <i class="bi bi-arrow-left me-1"></i> Discard and Return to Products
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Dynamic Gallery Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Auto-Slug generator
    const titleInput = document.getElementById('prodTitle');
    const slugInput = document.getElementById('prodSlug');
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function() {
            if (!slugInput.dataset.touched) {
                slugInput.value = titleInput.value.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .trim()
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });
        slugInput.addEventListener('input', function() {
            slugInput.dataset.touched = 'true';
        });
    }

    // 2. Interactive Dynamic Custom Color Manager
    const customNameInput = document.getElementById('customColorNameInput');
    if (customNameInput) {
        customNameInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addNewCustomColor();
            }
        });
    }

    // Initialize default colors
    initDefaultColors(['Espresso Brown', 'Cognac Tan', 'Classic Black']);
});

// State of active colors: [{ name: '...', hex: '...' }]
let productColors = [];

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getSmartHex(name) {
    const lc = name.toLowerCase();
    if (lc.includes('black') || lc.includes('raven') || lc.includes('noir') || lc.includes('dark')) return '#181818';
    if (lc.includes('cognac') || lc.includes('tan') || lc.includes('caramel') || lc.includes('camel')) return '#9a532e';
    if (lc.includes('espresso') || lc.includes('brown') || lc.includes('chocolate')) return '#2e1c14';
    if (lc.includes('chestnut') || lc.includes('wood') || lc.includes('cedar')) return '#5c3a21';
    if (lc.includes('oxblood') || lc.includes('burgundy') || lc.includes('wine') || lc.includes('maroon') || lc.includes('bordeaux')) return '#4c1e1a';
    if (lc.includes('navy') || lc.includes('blue') || lc.includes('indigo') || lc.includes('sapphire')) return '#1c2833';
    if (lc.includes('olive') || lc.includes('green') || lc.includes('forest') || lc.includes('sage')) return '#3d493a';
    if (lc.includes('grey') || lc.includes('gray') || lc.includes('slate') || lc.includes('charcoal')) return '#4f4f4f';
    if (lc.includes('sand') || lc.includes('beige') || lc.includes('cream') || lc.includes('ivory') || lc.includes('nude') || lc.includes('taupe')) return '#d4b895';
    if (lc.includes('red') || lc.includes('ruby')) return '#8b1e1a';
    if (lc.includes('gold') || lc.includes('yellow') || lc.includes('mustard')) return '#c5a059';
    if (lc.includes('orange') || lc.includes('rust') || lc.includes('copper')) return '#b85d19';
    if (lc.includes('white') || lc.includes('pearl')) return '#f0f0f0';
    return '#5c3a21';
}

function initDefaultColors(names) {
    productColors = [];
    names.forEach(n => {
        if (n && n.trim()) {
            const clean = n.trim();
            productColors.push({ name: clean, hex: getSmartHex(clean) });
        }
    });
    renderColorBadges();
}

function renderColorBadges() {
    const container = document.getElementById('activeColorsContainer');
    const badgeCount = document.getElementById('colorCountBadge');
    const finalInput = document.getElementById('finalColorInput');

    container.innerHTML = '';
    if (productColors.length === 0) {
        container.innerHTML = '<span class="text-danger small fst-italic">Please add at least one color for this product.</span>';
        if (badgeCount) badgeCount.textContent = '0 Colors';
        finalInput.value = '';
        updateAllColorDropdowns();
        return;
    }

    if (badgeCount) badgeCount.textContent = `${productColors.length} Color${productColors.length > 1 ? 's' : ''} Active`;
    finalInput.value = productColors.map(c => c.name).join(', ');

    productColors.forEach((col, index) => {
        const pill = document.createElement('div');
        pill.className = 'd-inline-flex align-items-center gap-1.5 py-0.5 px-2 bg-white border rounded shadow-xs';
        pill.style.fontSize = '0.78rem';
        pill.innerHTML = `
            <span class="rounded-circle border" style="width: 11px; height: 11px; background-color: ${col.hex}; display: inline-block;"></span>
            <strong class="text-dark">${escapeHtml(col.name)}</strong>
            <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1" onclick="removeColor(${index})" title="Remove ${escapeHtml(col.name)}" style="text-decoration: none; line-height: 1;">
                <i class="bi bi-x-circle-fill" style="font-size: 0.8rem;"></i>
            </button>
        `;
        container.appendChild(pill);
    });

    updateAllColorDropdowns();
}

function addNewCustomColor() {
    const nameInput = document.getElementById('customColorNameInput');
    const picker = document.getElementById('customColorPicker');
    const name = nameInput.value.trim();
    if (!name) {
        nameInput.focus();
        return;
    }

    // Check if already exists
    if (productColors.some(c => c.name.toLowerCase() === name.toLowerCase())) {
        alert(`Color "${name}" is already in your product colors list.`);
        nameInput.value = '';
        return;
    }

    const hex = picker.value || getSmartHex(name);
    productColors.push({ name: name, hex: hex });
    nameInput.value = '';
    renderColorBadges();
}

function addQuickColor(name, hex) {
    if (productColors.some(c => c.name.toLowerCase() === name.toLowerCase())) {
        return; // already present
    }
    productColors.push({ name: name, hex: hex });
    renderColorBadges();
}

function removeColor(index) {
    productColors.splice(index, 1);
    renderColorBadges();
}

// Update all color dropdowns in gallery rows
function updateAllColorDropdowns() {
    document.querySelectorAll('.color-select-dropdown').forEach(select => {
        const currentVal = select.value;
        select.innerHTML = '<option value="">-- General (All Colors) --</option>';
        productColors.forEach(col => {
            const opt = document.createElement('option');
            opt.value = col.name;
            opt.textContent = col.name;
            if (opt.value === currentVal) opt.selected = true;
            select.appendChild(opt);
        });
        const customOpt = document.createElement('option');
        customOpt.value = '__ADD_NEW__';
        customOpt.textContent = '➕ Add New Custom Color...';
        customOpt.style.fontWeight = 'bold';
        customOpt.style.color = '#c5a059';
        select.appendChild(customOpt);
    });
}

function handleColorSelectChange(selectElem) {
    if (selectElem.value === '__ADD_NEW__') {
        const newColor = prompt('Enter New Color Name (e.g. Royal Navy, Olive Green, Bordeaux):');
        if (newColor && newColor.trim()) {
            const cleanName = newColor.trim();
            if (!productColors.some(c => c.name.toLowerCase() === cleanName.toLowerCase())) {
                productColors.push({ name: cleanName, hex: getSmartHex(cleanName) });
                renderColorBadges();
            }
            selectElem.value = cleanName;
        } else {
            selectElem.value = '';
        }
    }
}

// Add new gallery photo row
function addGalleryPhotoRow() {
    const container = document.getElementById('galleryRowsContainer');

    const div = document.createElement('div');
    div.className = 'gallery-photo-row p-3 border rounded bg-light position-relative';
    
    let optionsHtml = '<option value="">-- General (All Colors) --</option>';
    productColors.forEach(col => {
        optionsHtml += `<option value="${escapeHtml(col.name)}">${escapeHtml(col.name)}</option>`;
    });
    optionsHtml += '<option value="__ADD_NEW__" style="font-weight:bold; color:#c5a059;">➕ Add New Custom Color...</option>';

    div.innerHTML = `
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <div class="gallery-thumb-box border rounded bg-white d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; overflow: hidden;">
                    <i class="bi bi-image text-muted fs-4"></i>
                </div>
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-bold">Select Photo File</label>
                <input type="file" name="gallery_images[]" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewGalleryRow(this)">
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-bold">Assign to Color Swatch</label>
                <select name="gallery_colors[]" class="form-select form-select-sm color-select-dropdown" onchange="handleColorSelectChange(this)">
                    ${optionsHtml}
                </select>
            </div>
            <div class="col-auto text-end">
                <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2" onclick="removeGalleryRow(this)" title="Remove this photo">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(div);
}

// Remove gallery row
function removeGalleryRow(btn) {
    const row = btn.closest('.gallery-photo-row');
    if (row) row.remove();
}

// Preview single image
function previewSingleImage(input, targetImgId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(targetImgId);
            img.src = e.target.result;
            img.style.opacity = '1';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview gallery row image
function previewGalleryRow(input) {
    const row = input.closest('.gallery-photo-row');
    const thumbBox = row.querySelector('.gallery-thumb-box');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            thumbBox.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
