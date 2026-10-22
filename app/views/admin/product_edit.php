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
                                <input type="hidden" id="finalColorInput" name="color" value="<?= e($product['color']) ?>" required>
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

                    <!-- Existing Gallery Photos -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold d-block text-dark mb-2">
                            <i class="bi bi-collection-play me-1"></i> Existing Gallery Photos (<?= count($galleryImages) ?>)
                        </label>

                        <?php if (empty($galleryImages)): ?>
                            <div class="p-3 text-center text-muted border rounded bg-light small">
                                No additional gallery photos uploaded yet. Use the uploader below to add photos with color swatches.
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($galleryImages as $gImg): ?>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="border rounded p-2 bg-light position-relative shadow-xs h-100 d-flex flex-column justify-content-between">
                                            <div class="position-relative mb-2" style="height: 140px; overflow: hidden; border-radius: 4px;">
                                                <img src="<?= asset($gImg['image_url']) ?>" alt="<?= e($gImg['alt_text']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                                <span class="position-absolute bottom-0 start-0 m-1 badge bg-dark text-warning border" style="font-size: 0.7rem;">
                                                    <i class="bi bi-palette me-1"></i><?= e($gImg['color_name'] ?: 'General') ?>
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold mb-1 text-muted" style="font-size: 0.72rem;">Assigned Color Swatch:</label>
                                                <select name="existing_image_color[<?= $gImg['id'] ?>]" class="form-select form-select-sm color-select-dropdown" data-current="<?= e($gImg['color_name'] ?? '') ?>" onchange="handleColorSelectChange(this)">
                                                    <option value="">-- General (All Colors) --</option>
                                                </select>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between pt-1 border-top">
                                                <small class="text-muted text-truncate" style="max-width: 130px;"><?= e($gImg['alt_text']) ?></small>
                                                <button type="submit" form="deleteImgForm_<?= $gImg['id'] ?>" class="btn btn-outline-danger btn-sm p-1 px-2" title="Delete this photo">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Upload Additional Photos with Color Tagging -->
                    <div class="border-top pt-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label small fw-bold mb-0 text-dark">
                                <i class="bi bi-cloud-arrow-up-fill text-warning me-1"></i> Upload Additional Color Photos
                            </label>
                            <button type="button" class="btn btn-outline-dark btn-sm d-flex align-items-center gap-1 shadow-xs" onclick="addGalleryPhotoRow()">
                                <i class="bi bi-plus-circle-fill text-warning"></i> Add Photo Row
                            </button>
                        </div>
                        <p class="text-muted small mb-3">Add new photos tagged with color swatches so customers see them when they click that color.</p>

                        <!-- Dynamic Photo Rows Container -->
                        <div id="galleryRowsContainer" class="d-flex flex-column gap-3">
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
                                        <select name="gallery_colors[]" class="form-select form-select-sm color-select-dropdown" onchange="handleColorSelectChange(this)">
                                            <option value="">-- General (All Colors) --</option>
                                        </select>
                                    </div>
                                    <div class="col-auto text-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2" onclick="removeGalleryRow(this)" title="Remove this photo row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="addGalleryPhotoRow()">
                                <i class="bi bi-plus-lg me-1"></i> Add Another Photo Row
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

<!-- Dynamic Gallery & Custom Color Script -->
<script>
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
        return;
    }
    productColors.push({ name: name, hex: hex });
    renderColorBadges();
}

function removeColor(index) {
    productColors.splice(index, 1);
    renderColorBadges();
}

function updateAllColorDropdowns() {
    document.querySelectorAll('.color-select-dropdown').forEach(select => {
        const currentVal = select.value || select.dataset.current || '';
        select.innerHTML = '<option value="">-- General (All Colors) --</option>';
        productColors.forEach(col => {
            const opt = document.createElement('option');
            opt.value = col.name;
            opt.textContent = col.name;
            if (opt.value.toLowerCase() === currentVal.toLowerCase()) {
                opt.selected = true;
            }
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
            selectElem.value = selectElem.dataset.current || '';
        }
    }
}

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
                <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2" onclick="removeGalleryRow(this)" title="Remove this photo row">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(div);
}

function removeGalleryRow(btn) {
    const row = btn.closest('.gallery-photo-row');
    if (row) row.remove();
}

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

document.addEventListener('DOMContentLoaded', function() {
    const customNameInput = document.getElementById('customColorNameInput');
    if (customNameInput) {
        customNameInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addNewCustomColor();
            }
        });
    }

    // Initialize with existing product colors
    <?php
        $rawCols = array_filter(array_map('trim', explode(',', $product['color'] ?? '')));
        if (empty($rawCols)) {
            $rawCols = ['Espresso Brown', 'Cognac Tan', 'Classic Black'];
        }
    ?>
    initDefaultColors(<?= json_encode(array_values($rawCols)) ?>);
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
