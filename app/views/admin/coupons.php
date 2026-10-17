<?php
use App\Core\Auth;
$isSuper = Auth::isSuperAdmin();
$pageTitle = 'Promotional Coupons & Privé Codes';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Super Admin Scope Indicator if in regional context -->
    <?php if ($isSuper && isset($selectedCountryId)): ?>
        <div class="alert alert-dark border border-warning d-flex justify-content-between align-items-center py-2 px-3 mb-4 shadow-sm" style="background: #1a120b;">
            <div class="text-white small">
                <i class="bi bi-shield-check text-warning me-2 fs-5 align-middle"></i>
                <strong class="text-warning">Global Super Admin Mode:</strong> Managing Promotional Privé Codes & Discounts
            </div>
            <a href="<?= url('superadmin/dashboard') ?>" class="btn btn-outline-warning btn-sm py-1">
                <i class="bi bi-arrow-left me-1"></i> Back to Global Dashboard
            </a>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 font-serif mb-1 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                <i class="bi bi-ticket-perforated text-warning"></i> Promotional Coupons & Privé Codes
            </h1>
            <p class="text-muted small mb-0">Create, monitor, and regulate country-specific or global discount vouchers for distinguished patrons.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <?php if ($isSuper && !empty($allCountries)): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-funnel me-1"></i> Filter Market: <?= !empty($filterCountryName) ? e($filterCountryName) : 'All Markets' ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 small">
                        <li><a class="dropdown-item <?= empty($_GET['country_id']) ? 'active fw-bold' : '' ?>" href="<?= url('admin/coupons') ?>">All Markets & Global</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php foreach ($allCountries as $c): ?>
                            <li>
                                <a class="dropdown-item <?= (isset($_GET['country_id']) && $_GET['country_id'] == $c['id']) ? 'active fw-bold' : '' ?>" href="<?= url('admin/coupons?country_id=' . $c['id']) ?>">
                                    <?= country_flag($c['code'], $c['name'], 20) ?> <?= e($c['name']) ?> (<?= e($c['code']) ?>)
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <button type="button" class="btn btn-dark btn-sm text-white fw-semibold d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCouponModal" style="background-color: var(--color-espresso-deep); border-color: var(--color-gold); color: #ffffff !important;">
                <i class="bi bi-plus-circle text-warning"></i> New Coupon Voucher
            </button>
        </div>
    </div>

    <!-- Coupons Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Active Coupons</div>
                <div class="kpi-value text-success"><?= count(array_filter($coupons, fn($c) => $c['status'] === 'active')) ?></div>
                <small class="text-muted">Live on Storefront</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Inactive / Paused</div>
                <div class="kpi-value text-secondary"><?= count(array_filter($coupons, fn($c) => $c['status'] === 'inactive')) ?></div>
                <small class="text-muted">Temporarily Disabled</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Total Redemptions</div>
                <div class="kpi-value text-primary"><?= array_sum(array_column($coupons, 'times_used')) ?></div>
                <small class="text-muted">Across Orders</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Total Registered Codes</div>
                <div class="kpi-value" style="color: var(--color-espresso-deep);"><?= count($coupons) ?></div>
                <small class="text-muted">In Atelier Database</small>
            </div>
        </div>
    </div>

    <!-- Coupons Table Card -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <?php if (empty($coupons)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-ticket-perforated fs-1 mb-2 d-block text-warning"></i>
                <h5>No Coupons Found</h5>
                <p class="small">No discount vouchers registered for this selection. Click <strong>"New Coupon Voucher"</strong> to create one.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Coupon Code</th>
                            <th>Target Market</th>
                            <th>Discount Benefit</th>
                            <th>Minimum Spend</th>
                            <th>Validity Window</th>
                            <th>Usage / Limit</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupons as $cpn): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <code class="fs-6 fw-bold px-2 py-1 rounded border bg-light text-dark font-monospace"><?= e($cpn['code']) ?></code>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($cpn['country_id'])): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <?= country_flag($cpn['country_code'] ?? '', $cpn['country_name'] ?? '', 20) ?>
                                            <span class="small fw-semibold"><?= e($cpn['country_name'] ?? 'Regional Market') ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-dark text-warning border border-warning small">
                                            <i class="bi bi-globe me-1"></i> Global (All Markets)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($cpn['discount_type'] === 'percentage'): ?>
                                        <span class="badge bg-success-subtle text-success fs-6 fw-bold border border-success">
                                            <?= (float)$cpn['discount_value'] ?>% OFF
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary-subtle text-primary fs-6 fw-bold border border-primary">
                                            <?= e($cpn['currency_symbol'] ?? '$') . number_format($cpn['discount_value'], 2) ?> OFF
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <?php if ((float)$cpn['min_spend'] > 0): ?>
                                        Min <?= e($cpn['currency_symbol'] ?? '$') . number_format($cpn['min_spend'], 2) ?>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">No Minimum</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <div class="text-muted">
                                        <?= !empty($cpn['valid_from']) ? date('M d, Y', strtotime($cpn['valid_from'])) : 'Immediate' ?>
                                        &rarr;
                                        <?= !empty($cpn['valid_until']) ? date('M d, Y', strtotime($cpn['valid_until'])) : 'No Expiry' ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold small"><?= (int)$cpn['times_used'] ?></span>
                                        <span class="text-muted small">/ <?= ((int)$cpn['usage_limit'] > 0) ? (int)$cpn['usage_limit'] : '&infin;' ?></span>
                                    </div>
                                    <?php if ((int)$cpn['usage_limit'] > 0): 
                                        $pct = min(100, round(($cpn['times_used'] / $cpn['usage_limit']) * 100));
                                    ?>
                                        <div class="progress mt-1" style="height: 4px; width: 80px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $pct ?>%"></div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= ($cpn['status'] === 'active') ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($cpn['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <!-- Toggle Status Form -->
                                        <form action="<?= url('admin/coupons/toggle') ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                            <input type="hidden" name="coupon_id" value="<?= $cpn['id'] ?>">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm" title="Toggle Status">
                                                <i class="bi bi-power"></i>
                                            </button>
                                        </form>

                                        <!-- Delete Form -->
                                        <form action="<?= url('admin/coupons/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete coupon voucher <?= e($cpn['code']) ?>?');">
                                            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                            <input type="hidden" name="coupon_id" value="<?= $cpn['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Coupon">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Create New Coupon Voucher -->
<div class="modal fade" id="createCouponModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: var(--color-espresso-deep); border-bottom: 2px solid var(--color-gold);">
                <h5 class="modal-title font-serif fs-5">
                    <i class="bi bi-ticket-perforated text-warning me-2"></i> Create Privé Coupon Voucher
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('admin/coupons/create') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Coupon Code (Uppercase)</label>
                        <input type="text" name="code" class="form-control text-uppercase font-monospace fw-bold" placeholder="e.g. LUXURY20" required style="letter-spacing: 0.1em;">
                        <small class="text-muted">Distinguished identifier entered by patrons during checkout.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Applicable Market / Country</label>
                        <select name="country_id" class="form-select">
                            <?php if ($isSuper): ?>
                                <option value="">🌐 Global (Applicable across all countries)</option>
                            <?php endif; ?>
                            <?php foreach ($permittedCountries as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= (isset($selectedCountryId) && $selectedCountryId == $c['id']) ? 'selected' : '' ?>>
                                    [<?= e($c['code']) ?>] <?= e($c['name']) ?> (<?= e($c['currency_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Discount Type</label>
                            <select name="discount_type" class="form-select" required>
                                <option value="percentage">Percentage (% Off)</option>
                                <option value="fixed">Fixed Currency Amount</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Discount Value</label>
                            <input type="number" step="0.01" min="0.01" name="discount_value" class="form-control" placeholder="e.g. 15 or 50.00" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Minimum Spend Requirement</label>
                            <input type="number" step="0.01" min="0" name="min_spend" class="form-control" value="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Usage Limit (0 = Unlimited)</label>
                            <input type="number" min="0" name="usage_limit" class="form-control" value="100">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Valid From</label>
                            <input type="date" name="valid_from" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Valid Until</label>
                            <input type="date" name="valid_until" class="form-control" value="<?= date('Y-12-31') ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark btn-sm" style="background-color: var(--color-espresso-deep); border-color: var(--color-gold);">
                        <i class="bi bi-check2-circle me-1"></i> Provision Coupon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
