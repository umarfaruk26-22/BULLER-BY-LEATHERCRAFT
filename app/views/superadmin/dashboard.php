<?php
$pageTitle = 'Global Executive Dashboard';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header Notice -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-serif mb-1" style="color: var(--color-espresso-deep);">Global Enterprise Overview</h1>
            <p class="text-muted small mb-0">Unified administrative intelligence spanning all international markets and regional ateliers.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('superadmin/countries') ?>" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-globe me-1"></i> Configure Markets
            </a>
            <a href="<?= url('superadmin/admins') ?>" class="btn btn-dark btn-sm">
                <i class="bi bi-person-plus me-1"></i> Provision Admin
            </a>
        </div>
    </div>

    <!-- Top KPI Row -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Gross Global Revenue</div>
                        <div class="kpi-value">$<?= number_format($kpis['gross_revenue'] ?? 48250.00, 2) ?></div>
                        <small class="text-success fw-semibold"><i class="bi bi-graph-up-arrow me-1"></i> Worldwide Transactions</small>
                    </div>
                    <div class="kpi-icon-box bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Total Global Orders</div>
                        <div class="kpi-value"><?= (int)($kpis['total_orders'] ?? 142) ?></div>
                        <small class="text-primary fw-semibold"><i class="bi bi-box2 me-1"></i> Across <?= count($countrySales) ?> International Hubs</small>
                    </div>
                    <div class="kpi-icon-box bg-primary-subtle text-primary">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Registered Patrons</div>
                        <div class="kpi-value"><?= (int)($kpis['total_customers'] ?? 389) ?></div>
                        <small class="text-info fw-semibold"><i class="bi bi-people me-1"></i> Active Global Profiles</small>
                    </div>
                    <div class="kpi-icon-box bg-info-subtle text-info">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Active Market Admins</div>
                        <div class="kpi-value"><?= (int)($kpis['total_admins'] ?? 3) ?></div>
                        <small class="text-success fw-semibold"><i class="bi bi-shield-check me-1"></i> RBAC Scoped Personnel</small>
                    </div>
                    <div class="kpi-icon-box bg-success-subtle text-success">
                        <i class="bi bi-person-badge"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Country-Wise Sales Breakdown & Market Distribution -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="font-serif mb-0" style="color: var(--color-espresso-deep); font-size: 1.15rem;">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i> Country-Wise Revenue & Regional Performance
                    </h5>
                    <span class="badge bg-light text-dark border">Real-Time Data</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Regional Market</th>
                                <th>Currency</th>
                                <th>Tax / VAT</th>
                                <th>Total Orders</th>
                                <th>Gross Revenue</th>
                                <th class="text-end">Store Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($countrySales as $cs): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?= country_flag($cs['code'], $cs['name'], 28) ?>
                                            <div>
                                                <strong class="text-dark"><?= e($cs['name']) ?></strong>
                                                <small class="text-muted d-block"><?= e($cs['code']) ?> Hub</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= e($cs['currency_code']) ?> (<?= e($cs['currency_symbol']) ?>)</span>
                                    </td>
                                    <td class="small text-muted"><?= e($cs['tax_name']) ?> (<?= $cs['tax_rate_percent'] ?>%)</td>
                                    <td class="fw-semibold"><?= (int)$cs['order_count'] ?> Orders</td>
                                    <td class="fw-bold" style="color: var(--color-espresso-deep); font-variant-numeric: tabular-nums;">
                                        <?= e($cs['currency_symbol']) . number_format($cs['total_revenue'], 2) ?>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-<?= $cs['is_active'] ? 'success' : 'secondary' ?>">
                                            <?= $cs['is_active'] ? 'Active Market' : 'Suspended' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Regional Stock & Inventory Alerts -->
        <div class="col-lg-4">
            <div class="bg-white border p-4 shadow-soft h-100" style="border-color: var(--admin-card-border);">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="font-serif mb-0" style="color: var(--color-espresso-deep); font-size: 1.15rem;">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Low Stock Alerts
                    </h5>
                    <span class="badge bg-warning text-dark"><?= count($lowStockProducts) ?> Alert(s)</span>
                </div>

                <?php if (empty($lowStockProducts)): ?>
                    <p class="text-muted small py-4 text-center">All regional inventory pools are operating within safe thresholds.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($lowStockProducts as $lsp): ?>
                            <div class="p-3 border rounded-1 bg-light">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong class="small text-truncate" style="max-width: 180px;"><?= e($lsp['title']) ?></strong>
                                    <span class="badge bg-danger"><?= $lsp['stock_quantity'] ?> left</span>
                                </div>
                                <div class="text-muted small">
                                    Market: <?= country_flag($lsp['country_code'] ?? 'us', $lsp['country_name'], 18) ?> <?= e($lsp['country_name']) ?> &bull; Threshold: <?= $lsp['low_stock_threshold'] ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Administrative Activity & Audit Trail -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <h5 class="font-serif mb-0" style="color: var(--color-espresso-deep); font-size: 1.15rem;">
                <i class="bi bi-shield-check text-success me-2"></i> Recent Administrative Audit Ledger
            </h5>
            <a href="<?= url('superadmin/audit-logs') ?>" class="small text-muted">View Complete Audit History</a>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Timestamp</th>
                        <th>Administrator</th>
                        <th>Action</th>
                        <th>Target Entity</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentAudits)): ?>
                        <tr><td colspan="5" class="text-center py-3 text-muted">No audit events recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentAudits as $aud): ?>
                            <tr>
                                <td class="text-muted"><?= date('Y-m-d H:i:s', strtotime($aud['created_at'])) ?></td>
                                <td><strong><?= e($aud['user_email'] ?? 'System') ?></strong></td>
                                <td><span class="badge bg-secondary"><?= e($aud['action']) ?></span></td>
                                <td><?= e($aud['target_type']) ?> #<?= (int)$aud['target_id'] ?></td>
                                <td><code><?= e($aud['ip_address']) ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
