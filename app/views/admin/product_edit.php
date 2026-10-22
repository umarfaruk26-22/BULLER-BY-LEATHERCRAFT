<?php
use App\Core\Auth;

$isSuper = Auth::isSuperAdmin();
$pageTitle = 'Edit Handcrafted Product — ' . e($product['title']);
require __DIR__ . '/../layouts/admin_header.php';

$redirectRoute = $_GET['redirect_route'] ?? ($isSuper ? 'superadmin/products' : ('admin/products?country_id=' . $currentCountry['id']));
?>

<div class="container-fluid py-3" style="max-width: 1060px; margin: 0 auto;">
    <!-- Top Header: Title & Back Button matching reference -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0" style="letter-spacing: -0.02em;">
                Edit Handcrafted Product
            </h3>
            <p class="text-muted small mb-0 mt-1">
                Updating catalog specifications for <strong><?= e($product['title']) ?></strong> in <?= country_flag($currentCountry['code'], $currentCountry['name'], 16) ?> <?= e($currentCountry['name']) ?>.
            </p>
        </div>
        <a href="<?= url($redirectRoute) ?>" class="btn btn-outline-secondary btn-sm px-3 shadow-xs d-flex align-items-center gap-1.5 align-self-start align-self-sm-center" style="border-radius: 6px;">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
    </div>

    <!-- Main Card Container -->
    <div class="bg-white border rounded-3 p-4 p-md-5 shadow-soft mb-5" style="border-color: #e2e8f0;">
        <form id="productForm" action="<?= url('admin/products/edit') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="hidden" name="country_id" value="<?= $currentCountry['id'] ?>">
            <input type="hidden" name="redirect_route" value="<?= e($redirectRoute) ?>">

            <!-- ROW 1: Garment/Product Name, SKU, Category (3 Columns) -->
            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-dark mb-1">Garment Name *</label>
                    <input type="text" id="prodTitle" name="title" class="form-control" placeholder="e.g. Signature Emerald Heavyweight Tee" value="<?= e($product['title']) ?>" required autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-dark mb-1">Unique SKU *</label>
                    <input type="text" id="prodSku" name="sku" class="form-control font-monospace text-uppercase" placeholder="e.g. GLA-TSH-008" value="<?= e($product['sku']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-dark mb-1">Category *</label>
                    <select name="category_id" class="form-select" required>
                        <option value="" disabled>Select Category...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $product['category_id']) ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- ROW 2: URL Slug, Status (2 Columns) -->
            <div class="row g-3 mb-3">
                <div class="col-md-7">
                    <label class="form-label small fw-bold text-dark mb-1">URL Slug (Auto-generated if left blank)</label>
                    <input type="text" id="prodSlug" name="slug" class="form-control font-monospace" placeholder="signature-emerald-heavyweight-tee" value="<?= e($product['slug']) ?>">
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-dark mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="published" <?= ($product['status'] === 'published') ? 'selected' : '' ?>>Active (Visible in Store)</option>
                        <option value="draft" <?= ($product['status'] === 'draft') ? 'selected' : '' ?>>Draft (Hidden from Catalog)</option>
                        <option value="archived" <?= ($product['status'] === 'archived') ? 'selected' : '' ?>>Archived / Discontinued</option>
                    </select>
                </div>
            </div>

            <!-- ROW 3: Craftsmanship & Leather Specifications (Buller Specific Details) -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-dark mb-1">Leather / Fabric Material</label>
                    <input type="text" name="leather_type" class="form-control" placeholder="e.g. Full-Grain Artisan Calfskin" value="<?= e($product['leather_type'] ?? 'Full-Grain Artisan Leather') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-dark mb-1">Hardware & Trim Finish</label>
                    <input type="text" name="hardware_finish" class="form-control" placeholder="e.g. Antique Solid Brass" value="<?= e($product['hardware_finish'] ?? 'Antique Brass') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-dark mb-1">Dimensions / Proportions</label>
                    <input type="text" name="dimensions" class="form-control" placeholder="e.g. 16.5 x 12.0 x 4.5 in / Tailored Fit" value="<?= e($product['dimensions'] ?? '') ?>">
                </div>
            </div>

            <!-- PRICING & DISCOUNT CONFIGURATION CARD (Exact Match to Reference Image 1) -->
            <?php
                $origVal = $pricing ? number_format((float)$pricing['original_price'], 2, '.', '') : '2499.00';
                $sellVal = ($pricing && !empty($pricing['discount_price'])) ? number_format((float)$pricing['discount_price'], 2, '.', '') : $origVal;
                $stockVal = $pricing ? (int)$pricing['stock_quantity'] : 30;
                $threshVal = $pricing ? (int)$pricing['low_stock_threshold'] : 5;
            ?>
            <div class="border rounded-3 p-3 p-md-4 mb-4" style="background: #ffffff; border-color: #e2e8f0;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-tag-fill text-dark"></i>
                    <strong class="text-uppercase small fw-bold text-dark" style="letter-spacing: 0.05em;">PRICING & DISCOUNT CONFIGURATION</strong>
                    <span class="badge bg-light text-dark border ms-auto"><?= e($currentCountry['code']) ?> (<?= e($currentCountry['currency_symbol']) ?>)</span>
                </div>

                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Original Price (<?= e($currentCountry['currency_symbol']) ?>) *</label>
                        <input type="number" step="0.01" id="origPriceInput" name="original_price" class="form-control" placeholder="2499.00" value="<?= $origVal ?>" required data-symbol="<?= e($currentCountry['currency_symbol']) ?>" oninput="calculateLiveDiscount()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Selling Price (<?= e($currentCountry['currency_symbol']) ?>) *</label>
                        <input type="number" step="0.01" id="sellingPriceInput" name="selling_price" class="form-control" placeholder="1499.00" value="<?= $sellVal ?>" oninput="calculateLiveDiscount()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Automatic Discount Preview</label>
                        <div id="discountPreviewText" class="p-2 border rounded bg-light small text-muted d-flex align-items-center justify-content-center" style="min-height: 38px;">
                            Enter prices above to calculate live discount
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Total Base Stock *</label>
                        <input type="number" name="stock_quantity" class="form-control" placeholder="30" value="<?= $stockVal ?>" min="0" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-dark mb-1">Sizes (Comma-separated)</label>
                        <input type="text" name="sizes" class="form-control font-monospace" placeholder="S,M,L,XL,XXL" value="<?= e($product['sizes'] ?? 'S,M,L,XL,XXL') ?>">
                        <small class="text-muted" style="font-size: 0.72rem;">e.g. S,M,L,XL or 38,40,42,44</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark mb-1">Low Stock Alert</label>
                        <input type="number" name="low_stock_threshold" class="form-control" placeholder="5" value="<?= $threshVal ?>" min="1">
                    </div>
                </div>
            </div>

            <!-- PRODUCT COLORS & DEDICATED PHOTOS CARD (Exact Match to Reference Image 2 & 4) -->
            <?php
                $assignedColors = array_filter(array_map('trim', array_column($galleryImages, 'color_name')));
                $allProductColors = array_filter(array_map('trim', explode(',', $product['color'] ?? '')));
                $unassignedColors = array_values(array_diff($allProductColors, $assignedColors));
            ?>
            <div class="border rounded-3 p-3 p-md-4 mb-4" style="background: #ffffff; border-color: #e2e8f0;">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between pb-3 mb-3 border-bottom gap-2">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2" style="font-size: 1rem;">
                            <span>🎨</span> Product Colors & Dedicated Photos
                        </h6>
                        <p class="text-muted small mb-0" style="font-size: 0.82rem;">
                            Add each garment color with its matching photo. Customers will see this exact photo when selecting that color!
                        </p>
                    </div>
                    <button type="button" class="btn btn-outline-dark btn-sm fw-semibold d-flex align-items-center gap-1.5 px-3 shadow-xs text-nowrap align-self-start align-self-sm-center" onclick="addNewColorRow()" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg text-dark"></i> Add Another Color
                    </button>
                </div>

                <!-- Dynamic Color & Photo Rows Container -->
                <div id="colorPhotosContainer" class="d-flex flex-column gap-3">
                    <!-- 1. Existing Uploaded Gallery Photos with Colors -->
                    <?php foreach ($galleryImages as $gImg): ?>
                        <div class="color-photo-row p-3 border rounded-3 bg-white shadow-xs" id="existing_row_<?= $gImg['id'] ?>" style="border-color: #e2e8f0;">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-dark mb-1">Color Name</label>
                                    <input type="text" name="existing_color_name[<?= $gImg['id'] ?>]" class="form-control color-name-input" value="<?= e($gImg['color_name'] ?: '') ?>" placeholder="e.g. Emerald Green" oninput="syncFinalColors()">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Color Photo (Displays when user clicks this color)</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="border rounded bg-light d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 38px; height: 38px; overflow: hidden;" title="Current Saved Photo">
                                            <img src="<?= asset($gImg['image_url']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file" name="replace_color_photo[<?= $gImg['id'] ?>]" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewColorRow(this)" title="Choose new file to replace photo">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-auto text-end">
                                    <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 6px;" onclick="markDeleteImage(<?= $gImg['id'] ?>, this)" title="Remove this color & photo">
                                        <i class="bi bi-trash fs-6"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- 2. Existing Product Colors without Photos yet -->
                    <?php foreach ($unassignedColors as $uIdx => $uColor): ?>
                        <div class="color-photo-row p-3 border rounded-3 bg-white shadow-xs" style="border-color: #e2e8f0;">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-dark mb-1">Color Name</label>
                                    <input type="text" name="new_color_names[]" class="form-control color-name-input" value="<?= e($uColor) ?>" placeholder="e.g. Emerald Green" oninput="syncFinalColors()">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Color Photo (Displays when user clicks this color)</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="file" name="new_color_photos[]" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewColorRow(this)">
                                        <div class="color-row-thumb border rounded bg-light d-none align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 38px; height: 38px; overflow: hidden;">
                                            <img src="" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-auto text-end">
                                    <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 6px;" onclick="removeNewColorRow(this)" title="Remove this color">
                                        <i class="bi bi-trash fs-6"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" id="finalColorInput" name="color" value="<?= e($product['color']) ?>">
            </div>

            <!-- DESCRIPTIONS (Exact Match to Reference Image 2) -->
            <div class="mb-3">
                <label class="form-label small fw-bold text-dark mb-1">Short Description (Catalog Summary)</label>
                <textarea name="short_description" class="form-control" rows="2" placeholder="Crafted from luxury full-grain leather with gold branding..."><?= e($product['short_description'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-dark mb-1">Detailed Description & Fabric Specs (HTML supported)</label>
                <textarea name="detailed_description" class="form-control font-monospace small" rows="5" placeholder="<p>Detailed tailoring breakdown, care instructions, and fabric weights...</p>"><?= e($product['detailed_description'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-dark mb-1">Leather Care & Conditioning Instructions</label>
                <input type="text" name="care_instructions" class="form-control" placeholder="e.g. Condition biannually with natural beeswax leather balm; protect from prolonged precipitation." value="<?= e($product['care_instructions'] ?? '') ?>">
            </div>

            <!-- UPLOAD PRODUCT IMAGES (Select multiple) (Exact Match to Reference Image 3) -->
            <div class="mb-4">
                <label class="form-label small fw-bold text-dark mb-1">Upload Product Images (Select multiple)</label>
                <input type="file" name="gallery_images[]" class="form-control" multiple accept="image/jpeg,image/png,image/webp,image/gif">
                <small class="text-muted d-block mt-1">Upload additional angles. New photos will be added to this garment's gallery.</small>
            </div>

            <!-- VISIBILITY & BADGES CHECKBOXES (Exact Match to Reference Image 3) -->
            <div class="d-flex flex-wrap align-items-center gap-4 py-3 my-4 border-top border-bottom">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="featCheck" <?= !empty($product['is_featured']) ? 'checked' : '' ?>>
                    <label class="form-check-label small fw-semibold text-dark" for="featCheck">
                        Featured in Spotlight
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1" id="newCheck" <?= !empty($product['is_new_arrival']) ? 'checked' : '' ?>>
                    <label class="form-check-label small fw-semibold text-dark" for="newCheck">
                        Mark as New Arrival
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_bestseller" value="1" id="bestCheck" <?= !empty($product['is_bestseller']) ? 'checked' : '' ?>>
                    <label class="form-check-label small fw-semibold text-dark" for="bestCheck">
                        Mark as Icon / Best Seller
                    </label>
                </div>
            </div>

            <!-- SUBMIT BUTTON (Exact Match to Reference Image 3) -->
            <div class="mt-4">
                <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="background: #0f382a !important; border-color: #0f382a; padding: 13px; border-radius: 6px; letter-spacing: 0.05em; font-size: 0.95rem;">
                    <i class="bi bi-save2"></i> SAVE PRODUCT CHANGES
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Dynamic Script for Colors, Slug, and Live Discount Calculation -->
<script>
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Live Discount Calculator (Image 1)
function calculateLiveDiscount() {
    const origInput = document.getElementById('origPriceInput');
    const sellInput = document.getElementById('sellingPriceInput');
    const preview = document.getElementById('discountPreviewText');
    if (!origInput || !sellInput || !preview) return;

    const orig = parseFloat(origInput.value);
    const sell = parseFloat(sellInput.value);
    const symbol = origInput.dataset.symbol || '$';

    if (!isNaN(orig) && orig > 0 && !isNaN(sell) && sell > 0 && sell < orig) {
        const pct = Math.round(((orig - sell) / orig) * 100);
        const save = (orig - sell).toFixed(2);
        preview.innerHTML = `<span class="badge bg-danger me-1 font-monospace">${pct}% OFF</span> <span class="text-success fw-bold small">Customer saves ${symbol}${save}</span>`;
    } else if (!isNaN(orig) && !isNaN(sell) && sell >= orig) {
        preview.innerHTML = `<span class="badge bg-secondary me-1">Regular Price</span> <span class="text-muted small">No discount applied</span>`;
    } else {
        preview.innerHTML = `Enter prices above to calculate live discount`;
    }
}

// Add new color row in edit mode
function addNewColorRow(colorName = '') {
    const container = document.getElementById('colorPhotosContainer');
    const row = document.createElement('div');
    row.className = 'color-photo-row p-3 border rounded-3 bg-white mb-3 shadow-xs';
    row.style.borderColor = '#e2e8f0';

    row.innerHTML = `
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-bold text-dark mb-1">Color Name</label>
                <input type="text" name="new_color_names[]" class="form-control color-name-input" placeholder="e.g. Desert Sand" value="${escapeHtml(colorName)}" required oninput="syncFinalColors()">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark mb-1">Color Photo (Displays when user clicks this color)</label>
                <div class="d-flex align-items-center gap-2">
                    <input type="file" name="new_color_photos[]" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewColorRow(this)">
                    <div class="color-row-thumb border rounded bg-light d-none align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 38px; height: 38px; overflow: hidden;">
                        <img src="" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
            </div>
            <div class="col-md-1 col-auto text-end">
                <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 6px;" onclick="removeNewColorRow(this)" title="Remove this color">
                    <i class="bi bi-trash fs-6"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(row);
    syncFinalColors();
}

// Remove dynamically added new color row
function removeNewColorRow(btn) {
    const row = btn.closest('.color-photo-row');
    if (row) {
        row.remove();
        syncFinalColors();
    }
}

// Mark existing saved photo/color for deletion
function markDeleteImage(id, btn) {
    if (!confirm('Are you sure you want to remove this color and its photo?')) return;
    const row = btn.closest('.color-photo-row');
    if (row) {
        row.remove();
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_image_ids[]';
        input.value = id;
        document.getElementById('productForm').appendChild(input);
        syncFinalColors();
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
    const container = document.getElementById('colorPhotosContainer');
    if (container && container.querySelectorAll('.color-photo-row').length === 0) {
        addNewColorRow('Emerald Green');
        addNewColorRow('Midnight Black');
    }
    syncFinalColors();
    calculateLiveDiscount();
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
