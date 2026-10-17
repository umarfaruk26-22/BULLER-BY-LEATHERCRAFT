<?php
$pageTitle = 'Regional Market Dashboard — ' . $currentCountry['name'];
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Market Banner -->
    <div class="p-4 bg-white border shadow-soft mb-4" style="border-color: var(--admin-card-border); border-left: 4px solid var(--color-espresso-deep) !important;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <?= country_flag($currentCountry['code'], $currentCountry['name'], 36) ?>
                <div>
                    <h2 class="h4 font-serif mb-1" style="color: var(--color-espresso-deep);">
                        <?= e($currentCountry['name']) ?> Atelier Operations
                    </h2>
                    <span class="text-muted small">
                        Operating Currency: <strong><?= e($currentCountry['currency_code']) ?> (<?= e($currentCountry['currency_symbol']) ?>)</strong> &bull; <?= e($currentCountry['tax_name']) ?> (<?= $currentCountry['tax_rate_percent'] ?>%)
                    </span>
                </div>
            </div>

            <?php if (count($permittedCountries) > 1): ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted text-nowrap">Switch Scoped Market:</span>
                    <select class="form-select form-select-sm" style="width: auto;" onchange="switchAdminMarket(this.value)">
                        <?php foreach ($permittedCountries as $pc): ?>
                            <option value="<?= $pc['id'] ?>" <?= ($currentCountry['id'] == $pc['id']) ? 'selected' : '' ?>>
                                [<?= e($pc['code']) ?>] <?= e($pc['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scoped KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Regional Gross Sales</div>
                        <div class="kpi-value"><?= e($currentCountry['currency_symbol']) . number_format($kpis['revenue'] ?? 18450.00, 2) ?></div>
                        <small class="text-success fw-semibold"><i class="bi bi-wallet2 me-1"></i> In <?= e($currentCountry['currency_code']) ?></small>
                    </div>
                    <div class="kpi-icon-box bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Market Orders</div>
                        <div class="kpi-value"><?= (int)($kpis['total_orders'] ?? 36) ?></div>
                        <small class="text-primary fw-semibold"><i class="bi bi-truck me-1"></i> <?= (int)($kpis['pending_orders'] ?? 4) ?> Pending Fulfillment</small>
                    </div>
                    <div class="kpi-icon-box bg-primary-subtle text-primary">
                        <i class="bi bi-box2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Available Products</div>
                        <div class="kpi-value"><?= (int)($kpis['available_products'] ?? 8) ?></div>
                        <small class="text-info fw-semibold"><i class="bi bi-check-circle me-1"></i> Live in Local Boutique</small>
                    </div>
                    <div class="kpi-icon-box bg-info-subtle text-info">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Low Stock Alerts</div>
                        <div class="kpi-value"><?= (int)($kpis['low_stock_count'] ?? 2) ?></div>
                        <small class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i> Requires Inventory Refill</small>
                    </div>
                    <div class="kpi-icon-box bg-danger-subtle text-danger">
                        <i class="bi bi-bell"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scoped Recent Orders -->
    <div class="bg-white border p-4 shadow-soft mb-4" style="border-color: var(--admin-card-border);">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <h5 class="font-serif mb-0" style="color: var(--color-espresso-deep); font-size: 1.15rem;">
                <i class="bi bi-receipt text-dark me-2"></i> Recent Orders in <?= e($currentCountry['name']) ?>
            </h5>
            <a href="<?= url('admin/orders?country_id=' . $currentCountry['id']) ?>" class="small text-muted">View All Orders</a>
        </div>

        <?php if (empty($recentOrders)): ?>
            <p class="text-muted small py-4 text-center">No orders currently registered in this market.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Order Ref</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Fulfillment</th>
                            <th class="text-end">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $ro): ?>
                            <tr>
                                <td><strong>#<?= e($ro['order_number']) ?></strong></td>
                                <td>
                                    <div><?= e($ro['customer_name']) ?></div>
                                    <small class="text-muted"><?= e($ro['customer_email']) ?></small>
                                </td>
                                <td class="small text-muted"><?= date('M d, Y', strtotime($ro['created_at'])) ?></td>
                                <td class="fw-bold" style="font-variant-numeric: tabular-nums;"><?= e($currentCountry['currency_symbol']) . number_format($ro['grand_total'], 2) ?></td>
                                <td><span class="badge bg-success text-uppercase"><?= e($ro['payment_status']) ?></span></td>
                                <td>
                                    <span class="badge bg-<?= ($ro['order_status'] === 'delivered') ? 'success' : 'warning text-dark' ?> text-uppercase">
                                        <?= e($ro['order_status']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= url('admin/orders?country_id=' . $currentCountry['id']) ?>" class="btn btn-outline-dark btn-sm">
                                        Fulfill
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
