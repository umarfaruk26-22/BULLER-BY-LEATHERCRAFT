<?php
use App\Core\Auth;
$isSuper = Auth::isSuperAdmin();
$pageTitle = 'Category Management & Catalog Taxonomies';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Top Action Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 font-serif mb-1 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                <i class="bi bi-tags-fill text-warning"></i> Categories & Catalog Taxonomies
            </h1>
            <p class="text-muted small mb-0">Manage boutique collection categories, hero banners, display order, and active customer storefront taxonomy.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-buller-gold btn-sm d-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-circle-fill"></i> Add New Category
            </button>
        </div>
    </div>

    <?php if (has_flash('admin_success')): ?>
        <div class="alert alert-success alert-dismissible fade show small py-2 mb-3 shadow-xs" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> <?= flash('admin_success') ?>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (has_flash('admin_error')): ?>
        <div class="alert alert-danger alert-dismissible fade show small py-2 mb-3 shadow-xs" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= flash('admin_error') ?>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Categories Data Card -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border); border-radius: 4px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th style="width: 70px;">Banner</th>
                        <th>Category Name</th>
                        <th>Catalog Slug</th>
                        <th>Description</th>
                        <th class="text-center">Live Products</th>
                        <th class="text-center">Sort Order</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No categories created yet. Click "+ Add New Category" to create one.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($cat['image_url'])): ?>
                                        <img src="<?= asset($cat['image_url']) ?>" alt="<?= e($cat['name']) ?>" class="border rounded-1" style="width: 52px; height: 52px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="border rounded-1 d-flex align-items-center justify-content-center bg-light text-muted" style="width: 52px; height: 52px; font-size: 1.25rem;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-dark d-block" style="font-size: 0.95rem;"><?= e($cat['name']) ?></strong>
                                    <small class="text-muted">ID: #<?= (int)$cat['id'] ?></small>
                                </td>
                                <td>
                                    <code class="text-dark bg-light px-2 py-1 rounded small border"><?= e($cat['slug']) ?></code>
                                </td>
                                <td class="small text-muted text-truncate" style="max-width: 260px;">
                                    <?= e($cat['description'] ?? 'No description provided.') ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <?= (int)($cat['product_count'] ?? 0) ?> items
                                    </span>
                                </td>
                                <td class="text-center small font-monospace">
                                    <?= (int)$cat['sort_order'] ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= ($cat['status'] === 'active') ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($cat['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-dark me-1" data-bs-toggle="modal" data-bs-target="#editCategoryModal_<?= $cat['id'] ?>" title="Edit Category">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <form action="<?= url('admin/categories/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete category <?= e(addslashes($cat['name'])) ?>?');">
                                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                        <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Category" <?= ($cat['product_count'] > 0) ? 'disabled title="Cannot delete category containing active products"' : '' ?>>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Category Modal -->
                            <div class="modal fade" id="editCategoryModal_<?= $cat['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title font-serif">Edit Category: <?= e($cat['name']) ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= url('admin/categories/edit') ?>" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                            <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">

                                            <div class="modal-body p-4 text-start">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Category Name *</label>
                                                    <input type="text" name="name" class="form-control" value="<?= e($cat['name']) ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">URL Slug *</label>
                                                    <input type="text" name="slug" class="form-control font-monospace" value="<?= e($cat['slug']) ?>" required>
                                                    <small class="text-muted">Unique clean URL parameter (e.g. leather-jackets).</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Description</label>
                                                    <textarea name="description" class="form-control" rows="2"><?= e($cat['description'] ?? '') ?></textarea>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Display Sort Order</label>
                                                        <input type="number" name="sort_order" class="form-control" value="<?= (int)$cat['sort_order'] ?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Status *</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" <?= ($cat['status'] === 'active') ? 'selected' : '' ?>>Active (Visible)</option>
                                                            <option value="inactive" <?= ($cat['status'] === 'inactive') ? 'selected' : '' ?>>Inactive (Hidden)</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Category Photo / Banner</label>
                                                    <?php if (!empty($cat['image_url'])): ?>
                                                        <div class="d-flex align-items-center gap-2 mb-2 p-2 bg-light border rounded">
                                                            <img src="<?= asset($cat['image_url']) ?>" alt="" style="width: 48px; height: 48px; object-fit: cover;" class="rounded border">
                                                            <span class="small text-muted text-truncate"><?= e($cat['image_url']) ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="file" name="category_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                                                    <small class="text-muted">Upload replacement photo (JPG, PNG, WEBP). Leave blank to keep existing photo.</small>
                                                </div>
                                            </div>

                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-buller-gold btn-sm px-3">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Create Category -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-serif">Provision New Boutique Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('admin/categories/create') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Category Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Leather Briefcases" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">URL Slug (Optional)</label>
                        <input type="text" name="slug" class="form-control font-monospace" placeholder="e.g. leather-briefcases (auto-generated if empty)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Short Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Heritage full-grain pieces crafted for timeless longevity..."></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Display Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Status *</label>
                            <select name="status" class="form-select">
                                <option value="active">Active (Visible)</option>
                                <option value="inactive">Inactive (Hidden)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Category Banner Image</label>
                        <input type="file" name="category_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                        <small class="text-muted">High-resolution showcase image (JPG, PNG, WEBP).</small>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-buller-gold btn-sm px-3">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
