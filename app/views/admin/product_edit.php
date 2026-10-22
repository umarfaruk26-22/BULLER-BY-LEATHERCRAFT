<?php
use App\Core\Auth;

$isSuper = Auth::isSuperAdmin();
$pageTitle = 'Edit Handcrafted Product — ' . e($product['title']);
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
                        <a href="<?= url($redirectRoute) ?>" class="text-decoration-none text-muted">
                            <i class="bi bi-arrow-left me-1"></i> Products Catalog
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
                </ol>
            </nav>
            <h1 class="h3 font-serif mb-1" style="color: var(--color-espresso-deep);">
                <i class="bi bi-pencil-square text-warning me-2"></i> Edit Product: <?= e($product['title']) ?>
            </h1>
            <p class="text-muted small mb-0">Market Scoped to: <?= country_flag($currentCountry['code'], $currentCountry['name'], 18) ?> <strong><?= e($currentCountry['name']) ?></strong> &bull; SKU: <span class="font-monospace text-dark fw-bold"><?= e($product['sku']) ?></span></p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="<?= url($redirectRoute) ?>" class="btn btn-outline-secondary btn-sm px-3">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </a>
            <button type="submit" form="editProductForm" class="btn btn-buller-gold btn-sm px-4 shadow-sm fw-semibold">
                <i class="bi bi-check2-circle me-1"></i> Save Product Changes
            </button>
        </div>
    </div>

    <!-- Main Form -->
    <form id="editProductForm" action="<?= url('admin/products/edit') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
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
                        <span class="badge bg-light text-muted border">ID #<?= $product['id'] ?></span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Product Title *</label>
                            <input type="text" id="prodTitle" name="title" class="form-control form-control-lg fs-6" value="<?= e($product['title']) ?>" required autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">SKU Identifier</label>
                            <input type="text" id="prodSku" name="sku" class="form-control form-control-lg fs-6 font-monospace text-uppercase" value="<?= e($product['sku']) ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $product['category_id']) ? 'selected' : '' ?>>
                                        <?= e($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Storefront URL Slug</label>
                            <input type="text" id="prodSlug" name="slug" class="form-control font-monospace" value="<?= e($product['slug']) ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Leather Grade / Material</label>
                            <input type="text" name="leather_type" class="form-control" value="<?= e($product['leather_type']) ?>" placeholder="e.g. Full-Grain Artisan Calfskin">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hardware Finish</label>
                            <input type="text" name="hardware_finish" class="form-control" value="<?= e($product['hardware_finish']) ?>" placeholder="e.g. Antique Solid Brass">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Dimensions / Proportions</label>
                            <input type="text" name="dimensions" class="form-control" value="<?= e($product['dimensions']) ?>" placeholder="e.g. 16.5 x 12.0 x 4.5 in">
                        </div>
                    </div>



                    <div class="mb-3">
                        <label class="form-label small fw-bold">Short Product Summary</label>
                        <textarea name="short_description" class="form-control" rows="2"><?= e($product['short_description']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Detailed Craftsmanship & Heritage Story</label>
                        <textarea name="detailed_description" class="form-control font-monospace small" rows="6"><?= e($product['detailed_description']) ?></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Leather Care & Conditioning Instructions</label>
                        <input type="text" name="care_instructions" class="form-control" value="<?= e($product['care_instructions']) ?>">
                    </div>
                </div>

                <!-- 2. Color-Tagged Photos & Gallery Manager -->
                <div class="bg-white border p-4 shadow-soft mb-4" style="border-color: var(--admin-card-border); border-radius: 4px;">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <div>
                            <h5 class="font-serif mb-1 text-dark">
                                <i class="bi bi-images text-warning me-2"></i> 2. Product Photos & Color Gallery
                            </h5>
                            <p class="text-muted small mb-0">Manage existing photography and upload new photos assigned to color variants.</p>
                        </div>
                        <span class="badge bg-warning text-dark"><i class="bi bi-magic me-1"></i> Dynamic PDP Swatches</span>
                    </div>

                    <!-- Primary Showcase Photo -->
                    <div class="p-3 border rounded mb-4" style="background: rgba(197, 160, 89, 0.05); border-color: rgba(197, 160, 89, 0.3) !important;">
                        <div class="row align-items-center g-3">
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-dark d-flex align-items-center gap-1">
                                    <i class="bi bi-star-fill text-warning"></i> Primary Showcase Photo
                                </label>
                                <input type="file" id="primaryImageInput" name="primary_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewSingleImage(this, 'primaryPreview')">
                                <small class="text-muted">Upload a new photo to replace the current primary image. Leave blank to keep current photo.</small>
                            </div>
                            <div class="col-md-5 text-center">
                                <div class="border rounded bg-white p-2 d-inline-block shadow-xs" style="width: 110px; height: 110px;">
                                    <img id="primaryPreview" src="<?= asset($product['primary_image']) ?>" alt="Primary Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="small text-muted mt-1">Current Primary Image</div>
                            </div>
                        </div>
                    </div>

                    <!-- Unified "Product Colors & Dedicated Photos" Card Exactly Matching User's Design -->
                    <?php
                        $assignedColors = array_filter(array_map('trim', array_column($galleryImages, 'color_name')));
                        $allProductColors = array_filter(array_map('trim', explode(',', $product['color'] ?? '')));
                        $unassignedColors = array_values(array_diff($allProductColors, $assignedColors));
                    ?>
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
                            <input type="number" step="0.01" name="original_price" class="form-control form-control-lg fs-5 fw-bold" value="<?= e($pricing['original_price'] ?? 0) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Promotional Discount Price (<?= e($currentCountry['currency_symbol']) ?>)</label>
                        <div class="input-group">
                            <span class="input-group-text"><?= e($currentCountry['currency_symbol']) ?></span>
                            <input type="number" step="0.01" name="discount_price" class="form-control" value="<?= e($pricing['discount_price'] ?? '') ?>" placeholder="Leave empty if no discount">
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold" style="font-size: 0.75rem;">Discount Start</label>
                            <input type="date" name="discount_start_date" class="form-control form-control-sm" value="<?= e($pricing['discount_start_date'] ?? '') ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold" style="font-size: 0.75rem;">Discount End</label>
                            <input type="date" name="discount_end_date" class="form-control form-control-sm" value="<?= e($pricing['discount_end_date'] ?? '') ?>">
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
                        <input type="number" name="stock_quantity" class="form-control form-control-lg fs-5" value="<?= (int)($pricing['stock_quantity'] ?? 0) ?>" min="0" required>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Low Stock Warning Threshold</label>
                        <input type="number" name="low_stock_threshold" class="form-control" value="<?= (int)($pricing['low_stock_threshold'] ?? 5) ?>" min="1">
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
                            <option value="published" <?= ($product['status'] === 'published') ? 'selected' : '' ?>>Published (Live on Storefront)</option>
                            <option value="draft" <?= ($product['status'] === 'draft') ? 'selected' : '' ?>>Draft (Hidden from Public)</option>
                            <option value="archived" <?= ($product['status'] === 'archived') ? 'selected' : '' ?>>Archived / Retired</option>
                        </select>
                    </div>

                    <div class="d-flex flex-column gap-2 p-3 bg-light rounded border mb-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1" id="isNewArrival" <?= !empty($product['is_new_arrival']) ? 'checked' : '' ?>>
                            <label class="form-check-label small fw-semibold" for="isNewArrival">
                                Mark as New Arrival
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured" <?= !empty($product['is_featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label small fw-semibold" for="isFeatured">
                                Feature on Homepage Showcase
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_bestseller" value="1" id="isBestseller" <?= !empty($product['is_bestseller']) ? 'checked' : '' ?>>
                            <label class="form-check-label small fw-semibold" for="isBestseller">
                                Mark as Heritage Bestseller
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Final Action Box -->
                <div class="bg-dark text-white p-4 rounded shadow-soft text-center" style="background: var(--color-espresso-deep) !important; border: 1px solid var(--color-gold);">
                    <button type="submit" class="btn btn-buller-gold btn-lg w-100 fw-bold mb-2 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Save Product Changes
                    </button>
                    <a href="<?= url($redirectRoute) ?>" class="text-white-50 small text-decoration-none d-block">
                        <i class="bi bi-arrow-left me-1"></i> Cancel and Return to Products
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Hidden forms for deleting existing gallery photos -->
<?php if (!empty($galleryImages)): ?>
    <?php foreach ($galleryImages as $gImg): ?>
        <form id="deleteImgForm_<?= $gImg['id'] ?>" action="<?= url('admin/products/delete-image') ?>" method="POST" class="d-none" onsubmit="return confirm('Delete this gallery photo?');">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <input type="hidden" name="image_id" value="<?= $gImg['id'] ?>">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="hidden" name="country_id" value="<?= $currentCountry['id'] ?>">
            <input type="hidden" name="redirect_route" value="admin/products/edit?id=<?= $product['id'] ?>&country_id=<?= $currentCountry['id'] ?>">
        </form>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Dynamic Gallery & Color Photo Script -->
<script>
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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
    const container = document.getElementById('colorPhotosContainer');
    if (container && container.querySelectorAll('.color-photo-row').length === 0) {
        addNewColorRow('Emerald Green');
        addNewColorRow('Midnight Black');
    }
    syncFinalColors();
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
