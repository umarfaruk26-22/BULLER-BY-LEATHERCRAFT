<?php
use App\Core\Auth;
$pageTitle = 'Administrator Management & Role-Based Access Control (RBAC)';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-serif mb-1" style="color: var(--color-espresso-deep);">Administrator Portfolios & Governance</h1>
            <p class="text-muted small mb-0">Provision country administrators, assign geographic regional jurisdictions, and regulate granular operational permissions.</p>
        </div>
        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#createAdminModal">
            <i class="bi bi-person-plus me-1"></i> Provision New Administrator
        </button>
    </div>

    <!-- Admins Table Card -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th>Administrator</th>
                        <th>Governance Role</th>
                        <th>Assigned Regional Markets</th>
                        <th>Permissions Scope</th>
                        <th>Account Status</th>
                        <th>Last Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $adm): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-dark text-gold d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                        <?= strtoupper(substr($adm['full_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark"><?= e($adm['full_name']) ?></strong>
                                        <small class="text-muted"><?= e($adm['email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?= ($adm['role'] === 'super_admin') ? 'dark text-warning' : 'primary-subtle text-primary' ?> text-uppercase">
                                    <?= str_replace('_', ' ', $adm['role']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($adm['role'] === 'super_admin'): ?>
                                    <span class="badge bg-dark"><i class="bi bi-globe me-1"></i> All Global Markets</span>
                                <?php elseif (empty($adm['assigned_countries'])): ?>
                                    <span class="badge bg-secondary">No Market Assigned</span>
                                <?php else: ?>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($adm['assigned_countries'] as $ac): ?>
                                            <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                                                <?= country_flag($ac['code'], $ac['name'], 16) ?> <?= e($ac['name']) ?> (<?= e($ac['code']) ?>)
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($adm['role'] === 'super_admin'): ?>
                                    <small class="text-success fw-semibold"><i class="bi bi-shield-fill-check"></i> Unrestricted Root Privilege</small>
                                <?php else: ?>
                                    <div class="d-flex flex-wrap gap-1" style="max-width: 250px;">
                                        <span class="badge bg-light text-muted border">Products</span>
                                        <span class="badge bg-light text-muted border">Orders</span>
                                        <span class="badge bg-light text-muted border">Discounts</span>
                                        <span class="badge bg-light text-muted border">Reviews</span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= ($adm['status'] === 'active') ? 'success' : 'danger' ?> text-uppercase">
                                    <?= e($adm['status']) ?>
                                </span>
                            </td>
                            <td class="small text-muted">
                                <?= $adm['last_login_at'] ? date('M d, Y H:i', strtotime($adm['last_login_at'])) : 'Never' ?>
                            </td>
                            <td class="text-end">
                                <?php if ($adm['id'] != Auth::id()): ?>
                                    <form action="<?= url('superadmin/admins/toggle') ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                        <input type="hidden" name="admin_id" value="<?= $adm['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-<?= ($adm['status'] === 'active') ? 'danger' : 'success' ?>" title="<?= ($adm['status'] === 'active') ? 'Suspend Account' : 'Activate Account' ?>">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">Current User</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Create Administrator -->
<div class="modal fade" id="createAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-serif">Provision New Administrator (RBAC)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('superadmin/admins/create') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Legal Name *</label>
                            <input type="text" name="full_name" class="form-control" required placeholder="e.g. Eleanor Vance">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Executive Email Address *</label>
                            <input type="email" name="email" class="form-control" required placeholder="admin.region@bullerleather.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Temporary Security Password *</label>
                            <input type="password" name="password" class="form-control" minlength="8" required placeholder="Min 8 characters">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Direct Phone</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+44 20 7946 0991">
                        </div>
                    </div>

                    <!-- Assign Countries (Multi-select) -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold d-block text-dark">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> Assign Geographic Jurisdiction (Select One or Multiple) *
                        </label>
                        <p class="text-muted small mb-2">The administrator will strictly be isolated to the selected regional markets.</p>
                        <div class="row g-2">
                            <?php foreach ($allCountries as $ac): ?>
                                <div class="col-md-6">
                                    <div class="form-check border p-2 rounded-1 bg-light">
                                        <input class="form-check-input ms-1 me-2" type="checkbox" name="assigned_countries[]" value="<?= $ac['id'] ?>" id="chk_country_<?= $ac['id'] ?>">
                                        <label class="form-check-label small fw-semibold d-inline-flex align-items-center gap-2" for="chk_country_<?= $ac['id'] ?>">
                                            <?= country_flag($ac['code'], $ac['name'], 20) ?> <?= e($ac['name']) ?> (<?= e($ac['currency_code']) ?>)
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Operational Permissions -->
                    <div>
                        <label class="form-label small fw-bold d-block text-dark">
                            <i class="bi bi-shield-lock me-1"></i> Granular Operational Privileges
                        </label>
                        <div class="row g-2 small">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="can_manage_products" value="1" checked id="p1">
                                    <label class="form-check-label" for="p1">Manage Catalog & Regional Pricing</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="can_manage_orders" value="1" checked id="p2">
                                    <label class="form-check-label" for="p2">Process Orders & Tracking Dispatches</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="can_manage_discounts" value="1" checked id="p3">
                                    <label class="form-check-label" for="p3">Configure Regional Discount Pricing</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="can_moderate_reviews" value="1" checked id="p4">
                                    <label class="form-check-label" for="p4">Moderate Customer Reviews</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Provision Administrator</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
