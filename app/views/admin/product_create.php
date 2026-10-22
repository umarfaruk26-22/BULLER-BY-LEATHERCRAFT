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

                <!-- 2. Product Photos & Color Gallery -->
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
                                    <i class="bi bi-star-fill text-warning"></i> Primary Showcase Photo (Main Catalog Image)
                                </label>
                                <input type="file" id="primaryImageInput" name="primary_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewSingleImage(this, 'primaryPreview')">
                                <small class="text-muted">High-res front showcase photo. (If left blank, the first color photo below will automatically be used).</small>
                            </div>
                            <div class="col-md-5 text-center">
                                <div class="border rounded bg-white p-2 d-inline-block shadow-xs" style="width: 100px; height: 100px;">
                                    <img id="primaryPreview" src="<?= asset('assets/images/buller-logo-gold.svg') ?>" alt="Primary Preview" style="width: 100%; height: 100%; object-fit: contain; opacity: 0.4;">
                                </div>
                                <div class="small text-muted mt-1" style="font-size: 0.72rem;">Catalog Showcase</div>
                            </div>
                        </div>
                    </div>

                    <!-- Unified "Product Colors & Dedicated Photos" Card Exactly Matching User's Design -->
                    <div class="border rounded-3 p-4 bg-white shadow-soft mb-2" style="border-color: #e2e8f0;">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between pb-3 mb-3 border-bottom gap-2">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                                    <span style="font-size: 1.2rem;">🎨</span> Product Colors & Dedicated Photos
                                </h6>
                                <p class="text-muted small mb-0" style="font-size: 0.84rem;">
                                    Add each garment color with its matching photo. Customers will see this exact photo when selecting that color!
                                </p>
                            </div>
                            <button type="button" class="btn btn-outline-dark btn-sm fw-semibold d-flex align-items-center gap-1.5 px-3 shadow-xs text-nowrap align-self-start align-self-sm-center" onclick="addColorRow()" style="border-radius: 6px;">
                                <i class="bi bi-plus-lg text-dark"></i> Add Another Color
                            </button>
                        </div>

                        <!-- Dynamic Color & Photo Rows -->
                        <div id="colorPhotosContainer" class="d-flex flex-column gap-3">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <input type="hidden" id="finalColorInput" name="color" value="Emerald Green, Midnight Black">
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
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Add another color row
function addColorRow(colorName = '') {
    const container = document.getElementById('colorPhotosContainer');
    const row = document.createElement('div');
    row.className = 'color-photo-row p-3 border rounded-3 bg-white shadow-xs';
    row.style.borderColor = '#e2e8f0';

    row.innerHTML = `
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-bold text-dark mb-1">Color Name</label>
                <input type="text" name="color_names[]" class="form-control color-name-input" placeholder="e.g. Emerald Green" value="${escapeHtml(colorName)}" required oninput="syncFinalColors()">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark mb-1">Color Photo (Displays when user clicks this color)</label>
                <div class="d-flex align-items-center gap-2">
                    <input type="file" name="color_photos[]" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewColorRow(this)">
                    <div class="color-row-thumb border rounded bg-light d-none align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 38px; height: 38px; overflow: hidden;">
                        <img src="" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
            </div>
            <div class="col-md-1 col-auto text-end">
                <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 6px;" onclick="removeColorRow(this)" title="Remove this color">
                    <i class="bi bi-trash fs-6"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(row);
    syncFinalColors();
}

// Remove color row
function removeColorRow(btn) {
    const row = btn.closest('.color-photo-row');
    if (row) {
        row.remove();
        syncFinalColors();
        const container = document.getElementById('colorPhotosContainer');
        if (container.querySelectorAll('.color-photo-row').length === 0) {
            addColorRow('');
        }
    }
}

// Live preview photo for chosen file
function previewColorRow(input) {
    const row = input.closest('.color-photo-row');
    const thumbBox = row.querySelector('.color-row-thumb');
    if (thumbBox && input.files && input.files[0]) {
        const thumbImg = thumbBox.querySelector('img');
        const reader = new FileReader();
        reader.onload = function(e) {
            thumbImg.src = e.target.result;
            thumbBox.classList.remove('d-none');
            thumbBox.classList.add('d-flex');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Live preview for primary showcase photo
function previewSingleImage(input, targetImgId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(targetImgId);
            if (img) {
                img.src = e.target.result;
                img.style.opacity = '1';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Sync comma-separated colors list into hidden input for DB
function syncFinalColors() {
    const inputs = document.querySelectorAll('.color-name-input');
    const names = [];
    inputs.forEach(inp => {
        const val = inp.value.trim();
        if (val && !names.includes(val)) {
            names.push(val);
        }
    });
    const finalInput = document.getElementById('finalColorInput');
    if (finalInput) {
        finalInput.value = names.join(', ');
    }
}

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

    // 2. Initialize default rows matching user spec
    addColorRow('Emerald Green');
    addColorRow('Midnight Black');
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
