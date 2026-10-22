<?php
use App\Core\Auth;
$pageTitle = 'Product Catalog & Inventory — ' . $currentCountry['name'];
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Top Action Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 font-serif mb-1 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                <?= country_flag($currentCountry['code'], $currentCountry['name'], 28) ?> <?= e($currentCountry['name']) ?> Product Catalog & Inventory
            </h1>
            <p class="text-muted small mb-0">Manage handcrafted leather products, color galleries, regional pricing, and stock availability.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="badge p-2 px-3 fs-6 d-none d-sm-inline-block" style="background-color: var(--color-espresso-deep); color: var(--color-gold);">
                Currency: <?= e($currentCountry['currency_code']) ?> (<?= e($currentCountry['currency_symbol']) ?>)
            </div>
            <a href="<?= url('admin/products/create?country_id=' . $currentCountry['id']) ?>" class="btn btn-buller-gold btn-sm d-flex align-items-center gap-1.5 shadow-sm">
                <i class="bi bi-plus-circle-fill"></i> Add New Product
            </a>
        </div>
    </div>

    <!-- Products Data Table Card -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border); border-radius: 4px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th style="width: 28%;">Artisan Piece</th>
                        <th>Category</th>
                        <th>Available Colors</th>
                        <th>Price & Offer</th>
                        <th>Stock Level</th>
                        <th>Market Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No products found for this regional market. Click "+ Add New Product" to publish an item.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <?php 
                            $galleryCount = !empty($p['gallery_images']) ? count($p['gallery_images']) : 0;
                            $colorList = !empty($p['color']) ? array_map('trim', explode(',', $p['color'])) : [];
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <img src="<?= asset($p['primary_image']) ?>" alt="<?= e($p['title']) ?>" class="border rounded-1" style="width: 54px; height: 60px; object-fit: cover;">
                                            <?php if ($galleryCount > 0): ?>
                                                <span class="position-absolute bottom-0 end-0 badge bg-dark text-white rounded-pill px-1" style="font-size: 0.65rem;" title="<?= $galleryCount ?> Gallery Photos">
                                                    <i class="bi bi-images"></i> <?= $galleryCount ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <strong class="d-block text-dark" style="font-size: 0.95rem;"><?= e($p['title']) ?></strong>
                                            <small class="text-muted font-monospace">SKU: <?= e($p['sku']) ?></small>
                                            <div class="mt-0.5">
                                                <?php if ($p['is_featured']): ?>
                                                    <span class="badge bg-warning text-dark me-1" style="font-size: 0.62rem;">Featured</span>
                                                <?php endif; ?>
                                                <?php if ($p['is_bestseller']): ?>
                                                    <span class="badge bg-dark text-warning me-1" style="font-size: 0.62rem;">Bestseller</span>
                                                <?php endif; ?>
                                                <?php if ($p['is_new_arrival']): ?>
                                                    <span class="badge bg-info text-dark me-1" style="font-size: 0.62rem;">New</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= e($p['category_name']) ?></span>
                                    <small class="d-block text-muted" style="font-size: 0.72rem;"><?= e($p['leather_type'] ?? 'Full-Grain') ?></small>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1" style="max-width: 170px;">
                                        <?php if (!empty($colorList)): ?>
                                            <?php foreach ($colorList as $cName): ?>
                                                <span class="badge bg-light text-dark border" style="font-size: 0.7rem;"><?= e($cName) ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">Standard</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold" style="font-variant-numeric: tabular-nums; font-size: 0.95rem;">
                                        <?= e($currentCountry['currency_symbol']) . number_format($p['original_price'], 2) ?>
                                    </div>
                                    <?php if ($p['discount_price']): ?>
                                        <div class="text-danger fw-bold small" style="font-variant-numeric: tabular-nums;">
                                            Sale: <?= e($currentCountry['currency_symbol']) . number_format($p['discount_price'], 2) ?>
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                                            <?= e($p['discount_start_date'] ?? 'Immediate') ?> - <?= e($p['discount_end_date'] ?? 'Open') ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold <?= ($p['stock_quantity'] <= $p['low_stock_threshold']) ? 'text-danger' : 'text-success' ?>">
                                        <?= (int)$p['stock_quantity'] ?> units
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Alert under: <?= (int)$p['low_stock_threshold'] ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= ($p['status'] === 'published' && $p['is_available']) ? 'success' : 'secondary' ?>">
                                        <?= ($p['status'] === 'published' && $p['is_available']) ? 'Published & Active' : ucfirst($p['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= url('admin/products/edit?id=' . $p['id'] . '&country_id=' . $currentCountry['id']) ?>" class="btn btn-sm btn-outline-dark me-1" title="Edit Details, Pricing & Color Photos">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="<?= url('admin/products/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete <?= e(addslashes($p['title'])) ?>? This cannot be undone.');">
                                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="country_id" value="<?= $currentCountry['id'] ?>">
                                        <input type="hidden" name="redirect_route" value="admin/products?country_id=<?= $currentCountry['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Product">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- ========================================== -->
                            <!-- EDIT PRODUCT MODAL (FULL CRUD + COLOR PHOTOS) -->
                            <!-- ========================================== -->
                            <div class="modal fade" id="editProductModal_<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title font-serif">Edit Product: <?= e($p['title']) ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= url('admin/products/edit') ?>" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="country_id" value="<?= $currentCountry['id'] ?>">
                                            <input type="hidden" name="redirect_route" value="admin/products?country_id=<?= $currentCountry['id'] ?>">

                                            <div class="modal-body p-4 text-start">
                                                <!-- SECTION 1: MASTER ATTRIBUTES -->
                                                <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3">1. Master Piece Information</h6>
                                                
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-8">
                                                        <label class="form-label small fw-bold">Product Title *</label>
                                                        <input type="text" name="title" class="form-control" value="<?= e($p['title']) ?>" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">SKU Identifier</label>
                                                        <input type="text" name="sku" class="form-control font-monospace" value="<?= e($p['sku']) ?>" required>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Category *</label>
                                                        <select name="category_id" class="form-select" required>
                                                            <?php foreach ($allCategories as $cat): ?>
                                                                <option value="<?= $cat['id'] ?>" <?= ($p['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                                                    <?= e($cat['name']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Catalog URL Slug</label>
                                                        <input type="text" name="slug" class="form-control font-monospace" value="<?= e($p['slug']) ?>" required>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Leather Type</label>
                                                        <input type="text" name="leather_type" class="form-control" value="<?= e($p['leather_type'] ?? 'Full-Grain Artisan Leather') ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Hardware Finish</label>
                                                        <input type="text" name="hardware_finish" class="form-control" value="<?= e($p['hardware_finish'] ?? 'Antique Brass') ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Dimensions</label>
                                                        <input type="text" name="dimensions" class="form-control" value="<?= e($p['dimensions'] ?? '') ?>" placeholder="e.g. 16.5 x 12.0 x 4.5 in">
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Available Color Options (Comma-Separated) *</label>
                                                    <input type="text" name="color" id="edit_color_input_<?= $p['id'] ?>" class="form-control" value="<?= e($p['color'] ?? 'Espresso Brown, Cognac Tan, Classic Black') ?>" placeholder="e.g. Espresso Brown, Cognac Tan, Classic Black">
                                                    <small class="text-muted">Customers will see these as selectable color swatches on the product page. Photos below can be tagged with these exact color names.</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Short Summary</label>
                                                    <textarea name="short_description" class="form-control" rows="2"><?= e($p['short_description'] ?? '') ?></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Detailed Craftsmanship Description</label>
                                                    <textarea name="detailed_description" class="form-control" rows="3"><?= e($p['detailed_description'] ?? '') ?></textarea>
                                                </div>

                                                <!-- SECTION 2: PHOTOS & COLOR GALLERIES -->
                                                <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3 mt-4">2. Product Photography & Color Variations</h6>

                                                <div class="mb-3 p-3 bg-light rounded border">
                                                    <label class="form-label small fw-bold d-block">Primary Display Image</label>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="<?= asset($p['primary_image']) ?>" alt="Current Primary" class="border rounded" style="width: 64px; height: 70px; object-fit: cover;">
                                                        <div class="flex-grow-1">
                                                            <input type="file" name="primary_image" class="form-control form-control-sm" accept="image/*">
                                                            <small class="text-muted">Leave empty to keep current image. Accepts JPG, PNG, WEBP.</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Existing Gallery Images with Color Tagging -->
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold d-block">Existing Gallery Photos (<?= count($p['gallery_images'] ?? []) ?> photos)</label>
                                                    <?php if (!empty($p['gallery_images'])): ?>
                                                        <div class="row g-2 mb-3">
                                                            <?php foreach ($p['gallery_images'] as $gImg): ?>
                                                                <div class="col-md-6">
                                                                    <div class="p-2 border rounded bg-white d-flex align-items-center gap-2">
                                                                        <img src="<?= asset($gImg['image_url']) ?>" alt="Gallery" class="border rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                                        <div class="flex-grow-1">
                                                                            <label class="form-label small mb-0 fw-semibold" style="font-size: 0.72rem;">Assigned Color:</label>
                                                                            <input type="text" name="existing_image_color[<?= $gImg['id'] ?>]" class="form-control form-control-sm py-0" value="<?= e($gImg['color_name'] ?? '') ?>" placeholder="e.g. Cognac Tan">
                                                                        </div>
                                                                        <button type="submit" form="deleteImgForm_<?= $gImg['id'] ?>" class="btn btn-outline-danger btn-sm p-1" title="Delete Photo">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <p class="text-muted small">No extra gallery photos yet. Upload photos below and tag them with colors.</p>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Upload More Gallery Images with Color Tagging -->
                                                <div class="mb-3 p-3 bg-light rounded border">
                                                    <label class="form-label small fw-bold d-block">
                                                        <i class="bi bi-cloud-arrow-up-fill text-warning me-1"></i> Upload More Color & Angle Photos
                                                    </label>
                                                    <input type="file" name="gallery_images[]" multiple class="form-control form-control-sm mb-2" accept="image/*" onchange="renderUploadSlots(this, 'editSlots_<?= $p['id'] ?>', 'edit_color_input_<?= $p['id'] ?>')">
                                                    <small class="text-muted d-block mb-2">Select one or multiple images. You can assign each image to a specific color so it switches when that color is clicked on the website.</small>
                                                    <div id="editSlots_<?= $p['id'] ?>" class="mt-2"></div>
                                                </div>

                                                <!-- SECTION 3: REGIONAL PRICING & INVENTORY -->
                                                <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3 mt-4">
                                                    3. <?= country_flag($currentCountry['code'], $currentCountry['name'], 20) ?> <?= e($currentCountry['name']) ?> Pricing & Inventory
                                                </h6>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Original Price (<?= e($currentCountry['currency_code']) ?>) *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><?= e($currentCountry['currency_symbol']) ?></span>
                                                            <input type="number" step="0.01" name="original_price" class="form-control" value="<?= e($p['original_price']) ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Discount Price (<?= e($currentCountry['currency_code']) ?>)</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><?= e($currentCountry['currency_symbol']) ?></span>
                                                            <input type="number" step="0.01" name="discount_price" class="form-control" value="<?= e($p['discount_price'] ?? '') ?>" placeholder="Leave blank if none">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Discount Start Date</label>
                                                        <input type="date" name="discount_start_date" class="form-control" value="<?= e($p['discount_start_date'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Discount End Date</label>
                                                        <input type="date" name="discount_end_date" class="form-control" value="<?= e($p['discount_end_date'] ?? '') ?>">
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Available Stock Quantity *</label>
                                                        <input type="number" name="stock_quantity" class="form-control" value="<?= (int)$p['stock_quantity'] ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Low Stock Warning Threshold</label>
                                                        <input type="number" name="low_stock_threshold" class="form-control" value="<?= (int)$p['low_stock_threshold'] ?>">
                                                    </div>
                                                </div>

                                                <!-- SECTION 4: VISIBILITY & BADGES -->
                                                <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3 mt-4">4. Publishing & Status</h6>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Catalog Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="published" <?= ($p['status'] === 'published') ? 'selected' : '' ?>>Published (Live)</option>
                                                            <option value="draft" <?= ($p['status'] === 'draft') ? 'selected' : '' ?>>Draft (Internal)</option>
                                                            <option value="archived" <?= ($p['status'] === 'archived') ? 'selected' : '' ?>>Archived</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Market Availability</label>
                                                        <div class="form-check form-switch mt-2">
                                                            <input class="form-check-input" type="checkbox" name="is_available" value="1" id="avail_<?= $p['id'] ?>" <?= $p['is_available'] ? 'checked' : '' ?>>
                                                            <label class="form-check-label small" for="avail_<?= $p['id'] ?>">Available to order in <?= e($currentCountry['name']) ?></label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap gap-4 pt-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="feat_<?= $p['id'] ?>" <?= $p['is_featured'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label small fw-semibold" for="feat_<?= $p['id'] ?>">Featured Collection</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_bestseller" value="1" id="best_<?= $p['id'] ?>" <?= $p['is_bestseller'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label small fw-semibold" for="best_<?= $p['id'] ?>">Bestseller Badge</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1" id="new_<?= $p['id'] ?>" <?= $p['is_new_arrival'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label small fw-semibold" for="new_<?= $p['id'] ?>">New Arrival Badge</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-buller-gold">
                                                    <i class="bi bi-check-lg me-1"></i> Save Changes
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden forms for deleting individual gallery images -->
                            <?php if (!empty($p['gallery_images'])): ?>
                                <?php foreach ($p['gallery_images'] as $gImg): ?>
                                    <form id="deleteImgForm_<?= $gImg['id'] ?>" action="<?= url('admin/products/delete-image') ?>" method="POST" class="d-none" onsubmit="return confirm('Delete this gallery photo?');">
                                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                        <input type="hidden" name="image_id" value="<?= $gImg['id'] ?>">
                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="country_id" value="<?= $currentCountry['id'] ?>">
                                        <input type="hidden" name="redirect_route" value="admin/products?country_id=<?= $currentCountry['id'] ?>">
                                    </form>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- ADD NEW PRODUCT MODAL (COMPLETE WITH COLOR PHOTOS ATTACHMENT) -->
<!-- ============================================================== -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-serif">
                    <i class="bi bi-plus-circle text-warning me-2"></i> Add New Handcrafted Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('admin/products/create') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                <input type="hidden" name="country_id" value="<?= $currentCountry['id'] ?>">
                <input type="hidden" name="redirect_route" value="admin/products?country_id=<?= $currentCountry['id'] ?>">

                <div class="modal-body p-4 text-start">
                    <!-- SECTION 1: MASTER INFO -->
                    <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3">1. Master Piece Information</h6>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Product Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. The Sovereign Leather Messenger Briefcase" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">SKU Identifier</label>
                            <input type="text" name="sku" class="form-control font-monospace" placeholder="Auto-generated if blank">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <?php foreach ($allCategories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">URL Slug</label>
                            <input type="text" name="slug" class="form-control font-monospace" placeholder="Auto-slugified if blank">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Leather Grade</label>
                            <input type="text" name="leather_type" class="form-control" value="Full-Grain Artisan Leather">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hardware Finish</label>
                            <input type="text" name="hardware_finish" class="form-control" value="Antique Brass">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Dimensions</label>
                            <input type="text" name="dimensions" class="form-control" placeholder="e.g. 16.5 x 12.0 x 4.5 in">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Available Colors (Comma-Separated) *</label>
                        <input type="text" name="color" id="add_color_input" class="form-control" value="Espresso Brown, Cognac Tan, Classic Black" placeholder="e.g. Espresso Brown, Cognac Tan, Classic Black" required>
                        <small class="text-muted">Enter color names separated by commas. These will generate customer swatches on the product page.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Short Summary</label>
                        <textarea name="short_description" class="form-control" rows="2" placeholder="Brief summary displayed on cards and previews..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Detailed Craftsmanship Description</label>
                        <textarea name="detailed_description" class="form-control" rows="3" placeholder="Full details on the leather tannery, internal compartments, and artisan techniques..."></textarea>
                    </div>

                    <!-- SECTION 2: PRODUCT PHOTOGRAPHY & COLOR ASSIGNMENTS -->
                    <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3 mt-4">2. Product Photography & Color Variations</h6>

                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label small fw-bold d-block">Primary Display Image *</label>
                        <input type="file" name="primary_image" class="form-control form-control-sm mb-1" accept="image/*" required>
                        <small class="text-muted">The main showcase photo. Accepts JPG, PNG, WEBP.</small>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label small fw-bold d-block">
                            <i class="bi bi-images text-warning me-1"></i> Multi-Angle & Color Gallery Photos
                        </label>
                        <input type="file" name="gallery_images[]" multiple class="form-control form-control-sm mb-2" accept="image/*" onchange="renderUploadSlots(this, 'addSlotsContainer', 'add_color_input')">
                        <small class="text-muted d-block">Select multiple photos. You can tag each photo with its matching color so when a customer clicks a color swatch, the gallery updates to show that color!</small>
                        
                        <div id="addSlotsContainer" class="mt-2"></div>
                    </div>

                    <!-- SECTION 3: REGIONAL PRICING & INVENTORY -->
                    <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3 mt-4">
                        3. Initial Pricing for <?= country_flag($currentCountry['code'], $currentCountry['name'], 20) ?> <?= e($currentCountry['name']) ?>
                    </h6>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Original Price (<?= e($currentCountry['currency_code']) ?>) *</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= e($currentCountry['currency_symbol']) ?></span>
                                <input type="number" step="0.01" name="original_price" class="form-control" placeholder="150.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Discount Price (<?= e($currentCountry['currency_code']) ?>)</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= e($currentCountry['currency_symbol']) ?></span>
                                <input type="number" step="0.01" name="discount_price" class="form-control" placeholder="Optional sale price">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Discount Start Date</label>
                            <input type="date" name="discount_start_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Discount End Date</label>
                            <input type="date" name="discount_end_date" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Available Stock Quantity *</label>
                            <input type="number" name="stock_quantity" class="form-control" value="25" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Low Stock Warning Threshold</label>
                            <input type="number" name="low_stock_threshold" class="form-control" value="5">
                        </div>
                    </div>

                    <!-- SECTION 4: BADGES & STATUS -->
                    <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3 mt-4">4. Publishing & Badges</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="published" selected>Published (Immediately visible to customers)</option>
                            <option value="draft">Draft (Hidden from storefront)</option>
                        </select>
                    </div>

                    <div class="d-flex flex-wrap gap-4 pt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="add_feat">
                            <label class="form-check-label small fw-semibold" for="add_feat">Featured Collection</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_bestseller" value="1" id="add_best">
                            <label class="form-check-label small fw-semibold" for="add_best">Bestseller Badge</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1" id="add_new" checked>
                            <label class="form-check-label small fw-semibold" for="add_new">New Arrival Badge</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-buller-gold">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Publish Handcrafted Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Dynamic Photo Color Tagging UI
function renderUploadSlots(input, containerId, colorInputId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = '';

    if (!input.files || input.files.length === 0) return;

    // Get color options from the colors input
    const colorInput = document.getElementById(colorInputId);
    let colors = [];
    if (colorInput && colorInput.value) {
        colors = colorInput.value.split(',').map(c => c.trim()).filter(c => c.length > 0);
    }

    const heading = document.createElement('div');
    heading.className = 'small fw-bold text-dark mb-2 mt-2';
    heading.textContent = 'Tag Uploaded Photos With Specific Colors:';
    container.appendChild(heading);

    const row = document.createElement('div');
    row.className = 'row g-2';

    Array.from(input.files).forEach((file, index) => {
        const col = document.createElement('div');
        col.className = 'col-md-6';

        let optionsHtml = '<option value="">All / General Angle</option>';
        colors.forEach(c => {
            optionsHtml += `<option value="${c}">${c}</option>`;
        });

        col.innerHTML = `
            <div class="p-2 border rounded bg-white small d-flex align-items-center gap-2">
                <i class="bi bi-image text-muted fs-4"></i>
                <div class="flex-grow-1 text-truncate">
                    <span class="d-block text-truncate fw-semibold" style="font-size: 0.75rem;" title="${file.name}">${file.name}</span>
                    <div class="d-flex align-items-center gap-1 mt-1">
                        <span class="text-muted" style="font-size: 0.7rem;">Color:</span>
                        <input type="text" name="gallery_colors[${index}]" list="color_datalist_${containerId}" class="form-control form-control-sm py-0" style="font-size: 0.75rem;" placeholder="e.g. Cognac Tan">
                    </div>
                </div>
            </div>
        `;
        row.appendChild(col);
    });

    // Create datalist for quick autocomplete
    let datalist = document.getElementById(`color_datalist_${containerId}`);
    if (!datalist) {
        datalist = document.createElement('datalist');
        datalist.id = `color_datalist_${containerId}`;
        document.body.appendChild(datalist);
    }
    datalist.innerHTML = colors.map(c => `<option value="${c}"></option>`).join('');

    container.appendChild(row);
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
