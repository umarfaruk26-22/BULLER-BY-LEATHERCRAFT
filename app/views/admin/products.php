<?php
use App\Core\Auth;
$pageTitle = 'Regional Catalog, Pricing & Stock — ' . $currentCountry['name'];
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-serif mb-1 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                <?= country_flag($currentCountry['code'], $currentCountry['name'], 28) ?> <?= e($currentCountry['name']) ?> Product Catalog & Pricing
            </h1>
            <p class="text-muted small mb-0">Regulate country-specific original prices, promotional discounts, discount active periods, and stock quantities.</p>
        </div>
        <div class="badge p-2 px-3 fs-6" style="background-color: var(--color-espresso-deep); color: var(--color-gold);">
            Currency: <?= e($currentCountry['currency_code']) ?> (<?= e($currentCountry['currency_symbol']) ?>)
        </div>
    </div>

    <!-- Products Pricing Matrix Table -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th style="width: 30%;">Artisan Piece</th>
                        <th>Category</th>
                        <th>Original Price</th>
                        <th>Discount Price & Dates</th>
                        <th>Stock Level</th>
                        <th>Market Availability</th>
                        <th class="text-end">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= asset($p['primary_image']) ?>" alt="<?= e($p['title']) ?>" class="border" style="width: 50px; height: 55px; object-fit: cover;">
                                    <div>
                                        <strong class="d-block text-dark"><?= e($p['title']) ?></strong>
                                        <small class="text-muted">SKU: <?= e($p['sku']) ?> &bull; <?= e($p['leather_type'] ?? '') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= e($p['category_name']) ?></span></td>
                            <td>
                                <strong class="fs-6 fw-bold" style="font-variant-numeric: tabular-nums;"><?= e($currentCountry['currency_symbol']) . number_format($p['original_price'], 2) ?></strong>
                            </td>
                            <td>
                                <?php if ($p['discount_price']): ?>
                                    <div class="text-danger fw-bold" style="font-variant-numeric: tabular-nums;"><?= e($currentCountry['currency_symbol']) . number_format($p['discount_price'], 2) ?></div>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                                        <?= e($p['discount_start_date'] ?? 'Immediate') ?> to <?= e($p['discount_end_date'] ?? 'Open') ?>
                                    </small>
                                <?php else: ?>
                                    <span class="text-muted small">No Discount Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold <?= ($p['stock_quantity'] <= $p['low_stock_threshold']) ? 'text-danger' : 'text-success' ?>">
                                    <?= (int)$p['stock_quantity'] ?> units
                                </div>
                                <small class="text-muted">Threshold: <?= (int)$p['low_stock_threshold'] ?></small>
                            </td>
                            <td>
                                <span class="badge bg-<?= $p['is_available'] ? 'success' : 'secondary' ?>">
                                    <?= $p['is_available'] ? 'Available in Market' : 'Hidden from Market' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editPriceModal_<?= $p['id'] ?>">
                                    <i class="bi bi-pencil-square me-1"></i> Edit Pricing
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Pricing Modal -->
                        <div class="modal fade" id="editPriceModal_<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header bg-dark text-white">
                                        <h5 class="modal-title font-serif">Configure <?= e($currentCountry['name']) ?> Pricing</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="<?= url('admin/products/update-pricing') ?>" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="country_id" value="<?= $currentCountry['id'] ?>">

                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-3">
                                                <h6 class="font-serif mb-1"><?= e($p['title']) ?></h6>
                                                <small class="text-muted d-flex align-items-center gap-1">SKU: <?= e($p['sku']) ?> &bull; Market: <?= country_flag($currentCountry['code'], $currentCountry['name'], 16) ?> <?= e($currentCountry['name']) ?></small>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Original Price (<?= e($currentCountry['currency_code']) ?>) *</label>
                                                    <input type="number" step="0.01" name="original_price" class="form-control" value="<?= $p['original_price'] ?>" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Discount Price (Optional)</label>
                                                    <input type="number" step="0.01" name="discount_price" class="form-control" value="<?= $p['discount_price'] ?? '' ?>" placeholder="e.g. Leave blank if none">
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Discount Start Date</label>
                                                    <input type="date" name="discount_start_date" class="form-control" value="<?= $p['discount_start_date'] ?? '' ?>">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Discount End Date</label>
                                                    <input type="date" name="discount_end_date" class="form-control" value="<?= $p['discount_end_date'] ?? '' ?>">
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Regional Inventory Stock *</label>
                                                    <input type="number" name="stock_quantity" class="form-control" value="<?= $p['stock_quantity'] ?>" required min="0">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Low Stock Warning Threshold *</label>
                                                    <input type="number" name="low_stock_threshold" class="form-control" value="<?= $p['low_stock_threshold'] ?>" required min="1">
                                                </div>
                                            </div>

                                            <div class="form-check form-switch pt-2">
                                                <input class="form-check-input" type="checkbox" name="is_available" value="1" id="avail_<?= $p['id'] ?>" <?= $p['is_available'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small fw-bold" for="avail_<?= $p['id'] ?>">
                                                    Available for purchase in <?= e($currentCountry['name']) ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-dark btn-sm">Save Regional Pricing</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
