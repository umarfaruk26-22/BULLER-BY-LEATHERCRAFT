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
        <div class="d-flex gap-2">
            <a href="<?= url('admin/products') ?>" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit Scoped Regional Pricing
            </a>
        </div>
    </div>

    <!-- Master Catalog Matrix Card -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th style="width: 30%;">Master Artisan Piece</th>
                        <th>Category</th>
                        <th>Leather Grade</th>
                        <th>Status</th>
                        <th>Regional Pricing Matrix</th>
                        <th class="text-end">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($catalog as $prod): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= asset($prod['primary_image']) ?>" alt="<?= e($prod['title']) ?>" class="border rounded-1" style="width: 52px; height: 58px; object-fit: cover;">
                                    <div>
                                        <strong class="d-block text-dark"><?= e($prod['title']) ?></strong>
                                        <small class="text-muted font-monospace">SKU: <?= e($prod['sku']) ?></small>
                                        <?php if ($prod['is_featured']): ?>
                                            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">Featured</span>
                                        <?php endif; ?>
                                        <?php if ($prod['is_bestseller']): ?>
                                            <span class="badge bg-dark text-warning ms-1" style="font-size: 0.65rem;">Bestseller</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= e($prod['category_name']) ?></span>
                            </td>
                            <td class="small text-muted">
                                <?= e($prod['leather_type'] ?? 'Full-Grain') ?>
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
                            <td class="text-end">
                                <a href="<?= url('admin/products') ?>" class="btn btn-outline-dark btn-sm py-1" title="Fine-tune local market pricing">
                                    <i class="bi bi-sliders me-1"></i> Adjust
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
