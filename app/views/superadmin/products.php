<?php
use App\Core\Auth;
$pageTitle = 'Master Global Catalog & Multi-Currency Pricing';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 font-serif mb-1 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                <i class="bi bi-box-seam-fill text-warning"></i> Master Global Catalog & Multi-Market Matrix
            </h1>
            <p class="text-muted small mb-0">Centralized governance of handcrafted leather goods, master SKUs, and cross-border currency price tiers.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('admin/products') ?>" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-geo-alt me-1"></i> View Scoped Regional View
            </a>
            <button type="button" class="btn btn-buller-gold btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#addSuperProductModal">
                <i class="bi bi-plus-circle-fill"></i> Add New Product
            </button>
        </div>
    </div>

    <!-- Master Catalog Matrix Card -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border); border-radius: 4px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th style="width: 28%;">Master Artisan Piece</th>
                        <th>Category</th>
                        <th>Colors & Leather</th>
                        <th>Status</th>
                        <th>Regional Pricing Matrix</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($catalog)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No products found. Click "+ Add New Product" to create one.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($catalog as $prod): ?>
                            <?php 
                            $galleryCount = !empty($prod['gallery_images']) ? count($prod['gallery_images']) : 0;
                            $colorList = !empty($prod['color']) ? array_map('trim', explode(',', $prod['color'])) : [];
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <img src="<?= asset($prod['primary_image']) ?>" alt="<?= e($prod['title']) ?>" class="border rounded-1" style="width: 52px; height: 58px; object-fit: cover;">
                                            <?php if ($galleryCount > 0): ?>
                                                <span class="position-absolute bottom-0 end-0 badge bg-dark text-white rounded-pill px-1" style="font-size: 0.65rem;" title="<?= $galleryCount ?> Photos">
                                                    <i class="bi bi-images"></i> <?= $galleryCount ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <strong class="d-block text-dark"><?= e($prod['title']) ?></strong>
                                            <small class="text-muted font-monospace">SKU: <?= e($prod['sku']) ?></small>
                                            <div class="mt-0.5">
                                                <?php if ($prod['is_featured']): ?>
                                                    <span class="badge bg-warning text-dark me-1" style="font-size: 0.62rem;">Featured</span>
                                                <?php endif; ?>
                                                <?php if ($prod['is_bestseller']): ?>
                                                    <span class="badge bg-dark text-warning me-1" style="font-size: 0.62rem;">Bestseller</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= e($prod['category_name']) ?></span>
                                </td>
                                <td>
                                    <small class="d-block text-muted mb-1"><?= e($prod['leather_type'] ?? 'Full-Grain') ?></small>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($colorList as $cName): ?>
                                            <span class="badge bg-light text-dark border" style="font-size: 0.68rem;"><?= e($cName) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= ($prod['status'] === 'published') ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($prod['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($prod['country_prices'] as $cp): ?>
                                            <div class="p-1 px-2 border rounded-1 bg-light small d-flex align-items-center gap-1">
                                                <?= country_flag($cp['country_code'], $cp['country_name'], 16) ?>
                                                <strong class="text-dark"><?= e($cp['currency_symbol']) . number_format($cp['original_price'], 0) ?></strong>
                                                <?php if (!empty($cp['discount_price'])): ?>
                                                    <span class="text-danger fw-bold">(<?= e($cp['currency_symbol']) . number_format($cp['discount_price'], 0) ?>)</span>
                                                <?php endif; ?>
                                                <span class="badge bg-<?= ($cp['stock_quantity'] <= $cp['low_stock_threshold']) ? 'danger' : 'secondary' ?> ms-1" style="font-size: 0.65rem;">
                                                    <?= (int)$cp['stock_quantity'] ?> left
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-outline-dark btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editSuperProductModal_<?= $prod['id'] ?>" title="Edit Master Piece & Photos">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <form action="<?= url('admin/products/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete <?= e(addslashes($prod['title'])) ?>?');">
                                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                        <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                        <input type="hidden" name="redirect_route" value="superadmin/products">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Product">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Master Product Modal -->
                            <div class="modal fade" id="editSuperProductModal_<?= $prod['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title font-serif">Edit Master Product: <?= e($prod['title']) ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= url('admin/products/edit') ?>" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                            <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                            <input type="hidden" name="redirect_route" value="superadmin/products">

                                            <div class="modal-body p-4 text-start">
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-8">
                                                        <label class="form-label small fw-bold">Title *</label>
                                                        <input type="text" name="title" class="form-control" value="<?= e($prod['title']) ?>" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">SKU</label>
                                                        <input type="text" name="sku" class="form-control font-monospace" value="<?= e($prod['sku']) ?>" required>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Category *</label>
                                                        <select name="category_id" class="form-select" required>
                                                            <?php foreach ($allCategories as $cat): ?>
                                                                <option value="<?= $cat['id'] ?>" <?= ($prod['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">URL Slug</label>
                                                        <input type="text" name="slug" class="form-control font-monospace" value="<?= e($prod['slug']) ?>" required>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Leather Grade</label>
                                                        <input type="text" name="leather_type" class="form-control" value="<?= e($prod['leather_type'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Hardware Finish</label>
                                                        <input type="text" name="hardware_finish" class="form-control" value="<?= e($prod['hardware_finish'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Dimensions</label>
                                                        <input type="text" name="dimensions" class="form-control" value="<?= e($prod['dimensions'] ?? '') ?>">
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Available Colors (Comma-Separated) *</label>
                                                    <input type="text" name="color" id="super_color_input_<?= $prod['id'] ?>" class="form-control" value="<?= e($prod['color'] ?? '') ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Short Description</label>
                                                    <textarea name="short_description" class="form-control" rows="2"><?= e($prod['short_description'] ?? '') ?></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Detailed Craftsmanship</label>
                                                    <textarea name="detailed_description" class="form-control" rows="3"><?= e($prod['detailed_description'] ?? '') ?></textarea>
                                                </div>

                                                <!-- Photos & Colors -->
                                                <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3 mt-4">Product Photos & Color Assignments</h6>
                                                
                                                <div class="mb-3 p-3 bg-light rounded border">
                                                    <label class="form-label small fw-bold d-block">Primary Image</label>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="<?= asset($prod['primary_image']) ?>" alt="Primary" class="border rounded" style="width: 55px; height: 60px; object-fit: cover;">
                                                        <div class="flex-grow-1">
                                                            <input type="file" name="primary_image" class="form-control form-control-sm" accept="image/*">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold d-block">Existing Gallery Photos (<?= count($prod['gallery_images'] ?? []) ?>)</label>
                                                    <?php if (!empty($prod['gallery_images'])): ?>
                                                        <div class="row g-2 mb-2">
                                                            <?php foreach ($prod['gallery_images'] as $gImg): ?>
                                                                <div class="col-md-6">
                                                                    <div class="p-2 border rounded bg-white d-flex align-items-center gap-2">
                                                                        <img src="<?= asset($gImg['image_url']) ?>" alt="Gallery" class="border rounded" style="width: 45px; height: 45px; object-fit: cover;">
                                                                        <div class="flex-grow-1">
                                                                            <input type="text" name="existing_image_color[<?= $gImg['id'] ?>]" class="form-control form-control-sm py-0" value="<?= e($gImg['color_name'] ?? '') ?>" placeholder="Color name">
                                                                        </div>
                                                                        <button type="submit" form="deleteSuperImg_<?= $gImg['id'] ?>" class="btn btn-outline-danger btn-sm p-1" title="Delete">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="mb-3 p-3 bg-light rounded border">
                                                    <label class="form-label small fw-bold d-block">Upload More Photos</label>
                                                    <input type="file" name="gallery_images[]" multiple class="form-control form-control-sm mb-2" accept="image/*" onchange="renderUploadSlots(this, 'superSlots_<?= $prod['id'] ?>', 'super_color_input_<?= $prod['id'] ?>')">
                                                    <div id="superSlots_<?= $prod['id'] ?>"></div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="published" <?= ($prod['status'] === 'published') ? 'selected' : '' ?>>Published</option>
                                                            <option value="draft" <?= ($prod['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
                                                            <option value="archived" <?= ($prod['status'] === 'archived') ? 'selected' : '' ?>>Archived</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-4 pt-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="s_feat_<?= $prod['id'] ?>" <?= $prod['is_featured'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label small" for="s_feat_<?= $prod['id'] ?>">Featured</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_bestseller" value="1" id="s_best_<?= $prod['id'] ?>" <?= $prod['is_bestseller'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label small" for="s_best_<?= $prod['id'] ?>">Bestseller</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-buller-gold">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($prod['gallery_images'])): ?>
                                <?php foreach ($prod['gallery_images'] as $gImg): ?>
                                    <form id="deleteSuperImg_<?= $gImg['id'] ?>" action="<?= url('admin/products/delete-image') ?>" method="POST" class="d-none">
                                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                        <input type="hidden" name="image_id" value="<?= $gImg['id'] ?>">
                                        <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                        <input type="hidden" name="redirect_route" value="superadmin/products">
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

<!-- Add Master Product Modal for Super Admin -->
<div class="modal fade" id="addSuperProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-serif">Add New Master Catalog Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('admin/products/create') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                <input type="hidden" name="redirect_route" value="superadmin/products">

                <div class="modal-body p-4 text-start">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. The Executive Italian Leather Duffle" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">SKU</label>
                            <input type="text" name="sku" class="form-control font-monospace" placeholder="Auto-generated if empty">
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
                            <input type="text" name="slug" class="form-control font-monospace" placeholder="Auto-slugified if empty">
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
                            <input type="text" name="dimensions" class="form-control" placeholder="e.g. 21 x 11 x 10 in">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Available Colors (Comma-Separated) *</label>
                        <input type="text" name="color" id="super_add_color_input" class="form-control" value="Espresso Brown, Cognac Tan, Classic Black" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Short Summary</label>
                        <textarea name="short_description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Detailed Craftsmanship Description</label>
                        <textarea name="detailed_description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label small fw-bold d-block">Primary Showcase Photo *</label>
                        <input type="file" name="primary_image" class="form-control form-control-sm" accept="image/*" required>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label small fw-bold d-block">Multi-Angle & Color Gallery Photos</label>
                        <input type="file" name="gallery_images[]" multiple class="form-control form-control-sm mb-2" accept="image/*" onchange="renderUploadSlots(this, 'superAddSlots', 'super_add_color_input')">
                        <div id="superAddSlots"></div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Base Reference Price (USD) *</label>
                            <input type="number" step="0.01" name="original_price" class="form-control" value="180.00" required>
                            <small class="text-muted">Automatically converted into other regional markets with initial catalog pricing.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Initial Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control" value="20" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="published" selected>Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-buller-gold">Publish Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function renderUploadSlots(input, containerId, colorInputId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = '';

    if (!input.files || input.files.length === 0) return;

    const colorInput = document.getElementById(colorInputId);
    let colors = [];
    if (colorInput && colorInput.value) {
        colors = colorInput.value.split(',').map(c => c.trim()).filter(c => c.length > 0);
    }

    const row = document.createElement('div');
    row.className = 'row g-2 mt-2';

    Array.from(input.files).forEach((file, index) => {
        const col = document.createElement('div');
        col.className = 'col-md-6';
        col.innerHTML = `
            <div class="p-2 border rounded bg-white small d-flex align-items-center gap-2">
                <i class="bi bi-image text-muted fs-4"></i>
                <div class="flex-grow-1 text-truncate">
                    <span class="d-block text-truncate fw-semibold" style="font-size: 0.75rem;" title="${file.name}">${file.name}</span>
                    <input type="text" name="gallery_colors[${index}]" list="color_datalist_${containerId}" class="form-control form-control-sm py-0 mt-1" style="font-size: 0.75rem;" placeholder="Assigned color (e.g. Cognac Tan)">
                </div>
            </div>
        `;
        row.appendChild(col);
    });

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
