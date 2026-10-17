<?php
use App\Core\Auth;
$pageTitle = 'International Country & Regional Market Management';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-serif mb-1" style="color: var(--color-espresso-deep);">International Markets & Jurisdictions</h1>
            <p class="text-muted small mb-0">Add, configure, and regulate international shipping zones, currencies, and statutory tax rates.</p>
        </div>
        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addCountryModal">
            <i class="bi bi-plus-lg me-1"></i> Add New Regional Market
        </button>
    </div>

    <!-- Countries Table Card -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th>Country / Region</th>
                        <th>ISO Code</th>
                        <th>Currency</th>
                        <th>Tax Structure</th>
                        <th>Shipping Policy</th>
                        <th>Return Window</th>
                        <th>Storefront Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($countries as $c): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?= country_flag($c['code'], $c['name'], 28) ?>
                                    <div>
                                        <strong class="d-block text-dark"><?= e($c['name']) ?></strong>
                                        <small class="text-muted">Dial: <?= e($c['phone_code']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-dark"><?= e($c['code']) ?></span>
                            </td>
                            <td>
                                <div><strong><?= e($c['currency_code']) ?> (<?= e($c['currency_symbol']) ?>)</strong></div>
                                <small class="text-muted"><?= e($c['currency_name']) ?></small>
                            </td>
                            <td>
                                <div><?= e($c['tax_name']) ?></div>
                                <span class="badge bg-secondary"><?= $c['tax_rate_percent'] ?>%</span>
                            </td>
                            <td>
                                <div class="small">Flat: <strong><?= e($c['currency_symbol']) . number_format($c['flat_shipping_rate'], 2) ?></strong></div>
                                <div class="small text-muted">Free Above: <?= e($c['currency_symbol']) . number_format($c['free_shipping_threshold'], 2) ?></div>
                            </td>
                            <td class="small">
                                <?= (int)$c['return_window_days'] ?> Days
                            </td>
                            <td>
                                <?php if ($c['is_active']): ?>
                                    <span class="badge bg-success">Active Market</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Deactivated</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <form action="<?= url('superadmin/countries/toggle') ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                    <input type="hidden" name="country_id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-<?= $c['is_active'] ? 'danger' : 'success' ?>" title="<?= $c['is_active'] ? 'Deactivate Market' : 'Activate Market' ?>">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add New Country -->
<div class="modal fade" id="addCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-serif">Provision New International Market</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('superadmin/countries/create') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Country Legal Name *</label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Canada, Germany, France, Australia">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">ISO 2-Letter Code *</label>
                            <input type="text" name="code" class="form-control text-uppercase" maxlength="5" required placeholder="e.g. CA, DE, FR, AU">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Flag Emoji or Icon *</label>
                            <input type="text" name="flag_icon" class="form-control" required placeholder="🇨🇦, 🇩🇪, 🇫🇷, 🇦🇺">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Currency Code *</label>
                            <input type="text" name="currency_code" class="form-control text-uppercase" maxlength="5" required placeholder="CAD, EUR, AUD">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Currency Symbol *</label>
                            <input type="text" name="currency_symbol" class="form-control" required placeholder="$, €, £">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Currency Title *</label>
                            <input type="text" name="currency_name" class="form-control" required placeholder="Canadian Dollar, Euro">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Statutory Tax Name *</label>
                            <input type="text" name="tax_name" class="form-control" value="VAT / Sales Tax" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tax Rate Percentage (%) *</label>
                            <input type="number" step="0.01" name="tax_rate_percent" class="form-control" value="5.00" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Flat Shipping Fee *</label>
                            <input type="number" step="0.01" name="flat_shipping_rate" class="form-control" value="20.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Free Shipping Threshold *</label>
                            <input type="number" step="0.01" name="free_shipping_threshold" class="form-control" value="200.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Return Window (Days) *</label>
                            <input type="number" name="return_window_days" class="form-control" value="30" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Phone Dial Code</label>
                            <input type="text" name="phone_code" class="form-control" placeholder="+1, +49, +33">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Provision Regional Market</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
