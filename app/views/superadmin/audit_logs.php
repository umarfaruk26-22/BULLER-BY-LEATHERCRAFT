<?php
use App\Core\Auth;
$pageTitle = 'Security & Administrative Audit Logs';
$actionFilter = $_GET['action'] ?? '';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 font-serif mb-1 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                <i class="bi bi-shield-shaded text-warning"></i> Immutable Administrative Audit Ledger
            </h1>
            <p class="text-muted small mb-0">Complete historical security trail recording administrator logins, pricing revisions, coupon generation, and authorization checks.</p>
        </div>

        <div class="d-flex gap-2">
            <!-- Action filter dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-funnel me-1"></i> Filter Action: <?= !empty($actionFilter) ? e($actionFilter) : 'All Recorded Actions' ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 small">
                    <li><a class="dropdown-item <?= empty($actionFilter) ? 'active fw-bold' : '' ?>" href="<?= url('superadmin/audit-logs') ?>">All Audit Logs</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item <?= ($actionFilter === 'update_pricing') ? 'active' : '' ?>" href="<?= url('superadmin/audit-logs?action=update_pricing') ?>">Pricing Revisions</a></li>
                    <li><a class="dropdown-item <?= ($actionFilter === 'update_order_status') ? 'active' : '' ?>" href="<?= url('superadmin/audit-logs?action=update_order_status') ?>">Order Fulfillment Updates</a></li>
                    <li><a class="dropdown-item <?= ($actionFilter === 'create_coupon') ? 'active' : '' ?>" href="<?= url('superadmin/audit-logs?action=create_coupon') ?>">Coupon Creations</a></li>
                    <li><a class="dropdown-item <?= ($actionFilter === 'moderate_review') ? 'active' : '' ?>" href="<?= url('superadmin/audit-logs?action=moderate_review') ?>">Review Moderation</a></li>
                    <li><a class="dropdown-item <?= ($actionFilter === 'create_admin') ? 'active' : '' ?>" href="<?= url('superadmin/audit-logs?action=create_admin') ?>">Admin Management</a></li>
                    <li><a class="dropdown-item <?= ($actionFilter === 'security_violation') ? 'active' : '' ?>" href="<?= url('superadmin/audit-logs?action=security_violation') ?>">Security Violations (403)</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Audit Stats KPI -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Total Logged Events</div>
                <div class="kpi-value" style="color: var(--color-espresso-deep);"><?= count($logs) ?></div>
                <small class="text-muted">In Current View</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Security Alerts (403)</div>
                <div class="kpi-value text-danger">
                    <?= count(array_filter($logs, fn($l) => str_contains($l['action'], 'violation') || str_contains($l['action'], 'unauthorized'))) ?>
                </div>
                <small class="text-muted">Unauthorized Interceptions</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Pricing & Order Changes</div>
                <div class="kpi-value text-primary">
                    <?= count(array_filter($logs, fn($l) => in_array($l['action'], ['update_pricing', 'update_order_status']))) ?>
                </div>
                <small class="text-muted">Catalog & Fulfillments</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Unique Admin IPs</div>
                <div class="kpi-value text-success">
                    <?= count(array_unique(array_column($logs, 'ip_address'))) ?>
                </div>
                <small class="text-muted">Origin Terminals</small>
            </div>
        </div>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <?php if (empty($logs)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shield-check fs-1 mb-2 d-block text-success"></i>
                <h5>No Audit Records Found</h5>
                <p class="small">No administrative activity matches the selected criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-uppercase">
                        <tr>
                            <th style="width: 15%;">Timestamp</th>
                            <th style="width: 20%;">Actor / Admin</th>
                            <th style="width: 18%;">Action</th>
                            <th style="width: 15%;">Target Entity</th>
                            <th style="width: 12%;">IP Address</th>
                            <th style="width: 20%;">Metadata Payload</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): 
                            $details = json_decode($log['details_json'] ?? '{}', true);
                            $isSecurity = str_contains($log['action'], 'violation');
                        ?>
                            <tr class="<?= $isSecurity ? 'table-danger-subtle' : '' ?>">
                                <td class="text-nowrap text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?>
                                </td>
                                <td>
                                    <strong><?= e($log['user_email'] ?? 'System / Anonymous') ?></strong>
                                    <?php if (!empty($log['user_id'])): ?>
                                        <span class="text-muted d-block" style="font-size: 0.72rem;">User ID: #<?= (int)$log['user_id'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isSecurity): ?>
                                        <span class="badge bg-danger text-white border border-danger">
                                            <i class="bi bi-exclamation-octagon-fill me-1"></i> <?= e($log['action']) ?>
                                        </span>
                                    <?php elseif (str_starts_with($log['action'], 'create')): ?>
                                        <span class="badge bg-success-subtle text-success border border-success">
                                            <i class="bi bi-plus-circle me-1"></i> <?= e($log['action']) ?>
                                        </span>
                                    <?php elseif (str_starts_with($log['action'], 'update')): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary">
                                            <i class="bi bi-pencil me-1"></i> <?= e($log['action']) ?>
                                        </span>
                                    <?php elseif (str_starts_with($log['action'], 'delete') || str_starts_with($log['action'], 'reject')): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger">
                                            <i class="bi bi-trash me-1"></i> <?= e($log['action']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border">
                                            <?= e($log['action']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <code><?= e($log['target_type']) ?></code>
                                    <?php if (!empty($log['target_id'])): ?>
                                        <span class="badge bg-light text-dark border">#<?= (int)$log['target_id'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="font-monospace text-dark"><?= e($log['ip_address']) ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($details)): ?>
                                        <pre class="mb-0 p-1 rounded bg-light border text-truncate" style="max-width: 250px; font-size: 0.7rem;"><?= e(json_encode($details, JSON_UNESCAPED_SLASHES)) ?></pre>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">—</span>
                                    <?php endif; ?>
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
