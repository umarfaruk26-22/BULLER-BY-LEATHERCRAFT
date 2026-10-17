<?php
use App\Core\Auth;
$pageTitle = 'Global Orders Fulfillment & Cross-Border Dispatch';
$countryFilter = $_GET['country_id'] ?? '';
$statusFilter = $_GET['status'] ?? '';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 font-serif mb-1 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                <i class="bi bi-receipt text-warning"></i> Global Orders Ledger & Fulfillment
            </h1>
            <p class="text-muted small mb-0">Consolidated oversight of acquisitions, international courier manifests, and customs clearances across all markets.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <!-- Filter by Market -->
            <div class="dropdown">
                <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-geo-alt me-1"></i> Market: <?= !empty($filterCountryName) ? e($filterCountryName) : 'All Markets' ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 small">
                    <li><a class="dropdown-item <?= empty($countryFilter) ? 'active fw-bold' : '' ?>" href="<?= url('superadmin/orders' . (!empty($statusFilter) ? '?status=' . urlencode($statusFilter) : '')) ?>">All International Hubs</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <?php foreach ($allCountries as $c): ?>
                        <li>
                            <a class="dropdown-item <?= ($countryFilter == $c['id']) ? 'active fw-bold' : '' ?>" href="<?= url('superadmin/orders?country_id=' . $c['id'] . (!empty($statusFilter) ? '&status=' . urlencode($statusFilter) : '')) ?>">
                                <?= country_flag($c['code'], $c['name'], 20) ?> <?= e($c['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Filter by Status -->
            <div class="dropdown">
                <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-funnel me-1"></i> Status: <?= !empty($statusFilter) ? ucfirst(e($statusFilter)) : 'All Statuses' ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 small">
                    <li><a class="dropdown-item <?= empty($statusFilter) ? 'active' : '' ?>" href="<?= url('superadmin/orders' . (!empty($countryFilter) ? '?country_id=' . $countryFilter : '')) ?>">All Statuses</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item <?= ($statusFilter === 'placed') ? 'active' : '' ?>" href="<?= url('superadmin/orders?status=placed' . (!empty($countryFilter) ? '&country_id=' . $countryFilter : '')) ?>">Placed</a></li>
                    <li><a class="dropdown-item <?= ($statusFilter === 'processing') ? 'active' : '' ?>" href="<?= url('superadmin/orders?status=processing' . (!empty($countryFilter) ? '&country_id=' . $countryFilter : '')) ?>">Processing</a></li>
                    <li><a class="dropdown-item <?= ($statusFilter === 'dispatched') ? 'active' : '' ?>" href="<?= url('superadmin/orders?status=dispatched' . (!empty($countryFilter) ? '&country_id=' . $countryFilter : '')) ?>">Dispatched</a></li>
                    <li><a class="dropdown-item <?= ($statusFilter === 'delivered') ? 'active' : '' ?>" href="<?= url('superadmin/orders?status=delivered' . (!empty($countryFilter) ? '&country_id=' . $countryFilter : '')) ?>">Delivered</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Orders Overview Table -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-box-seam fs-1 mb-2 d-block text-warning"></i>
                <h5>No Orders Match Criteria</h5>
                <p class="small">No orders found for the designated market or fulfillment status.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Order Number</th>
                            <th>Market</th>
                            <th>Distinguished Patron</th>
                            <th>Date</th>
                            <th>Grand Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Carrier & Tracking</th>
                            <th class="text-end">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): 
                            $addr = json_decode($ord['shipping_address_json'], true);
                        ?>
                            <tr>
                                <td>
                                    <strong class="fw-bold" style="font-variant-numeric: tabular-nums;">#<?= e($ord['order_number']) ?></strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?= country_flag($ord['country_code'], $ord['country_name'], 20) ?>
                                        <span class="small fw-semibold"><?= e($ord['country_name']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div><strong><?= e($ord['customer_name']) ?></strong></div>
                                    <small class="text-muted"><?= e($ord['customer_email']) ?></small>
                                </td>
                                <td class="small text-muted"><?= date('M d, Y H:i', strtotime($ord['created_at'])) ?></td>
                                <td>
                                    <strong class="fs-6 fw-bold" style="color: var(--color-espresso-deep); font-variant-numeric: tabular-nums;">
                                        <?= e($ord['currency_symbol']) . number_format($ord['grand_total'], 2) ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?= ($ord['payment_status'] === 'paid') ? 'success' : 'warning text-dark' ?>">
                                        <?= ucfirst($ord['payment_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= in_array($ord['order_status'], ['dispatched', 'delivered']) ? 'info text-dark' : 'secondary' ?>">
                                        <?= ucfirst($ord['order_status']) ?>
                                    </span>
                                </td>
                                <td class="small">
                                    <?php if (!empty($ord['shipping_carrier'])): ?>
                                        <div class="fw-semibold"><?= e($ord['shipping_carrier']) ?></div>
                                        <?php if (!empty($ord['tracking_number'])): ?>
                                            <code class="text-muted"><?= e($ord['tracking_number']) ?></code>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Pending Dispatch</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-outline-dark btn-sm py-1" data-bs-toggle="modal" data-bs-target="#updateOrderModal<?= $ord['id'] ?>">
                                        <i class="bi bi-pencil-square me-1"></i> Update
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal for updating order -->
                            <div class="modal fade" id="updateOrderModal<?= $ord['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header text-white" style="background-color: var(--color-espresso-deep); border-bottom: 2px solid var(--color-gold);">
                                            <h5 class="modal-title font-serif fs-5">Fulfill Order #<?= e($ord['order_number']) ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= url('admin/orders/update-status') ?>" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                            <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                            <input type="hidden" name="country_id" value="<?= $ord['country_id'] ?>">
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted">Fulfillment Status</label>
                                                    <select name="order_status" class="form-select">
                                                        <option value="placed" <?= ($ord['order_status'] === 'placed') ? 'selected' : '' ?>>Placed</option>
                                                        <option value="processing" <?= ($ord['order_status'] === 'processing') ? 'selected' : '' ?>>Processing / Atelier Workshop</option>
                                                        <option value="dispatched" <?= ($ord['order_status'] === 'dispatched') ? 'selected' : '' ?>>Dispatched / In Transit</option>
                                                        <option value="delivered" <?= ($ord['order_status'] === 'delivered') ? 'selected' : '' ?>>Delivered</option>
                                                        <option value="cancelled" <?= ($ord['order_status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted">Payment Status</label>
                                                    <select name="payment_status" class="form-select">
                                                        <option value="paid" <?= ($ord['payment_status'] === 'paid') ? 'selected' : '' ?>>Paid & Settled</option>
                                                        <option value="pending" <?= ($ord['payment_status'] === 'pending') ? 'selected' : '' ?>>Pending Payment</option>
                                                        <option value="refunded" <?= ($ord['payment_status'] === 'refunded') ? 'selected' : '' ?>>Refunded</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted">Logistics Carrier</label>
                                                    <input type="text" name="shipping_carrier" class="form-control" value="<?= e($ord['shipping_carrier']) ?>" placeholder="e.g. DHL Express Worldwide, Blue Dart">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted">Tracking AWB Number</label>
                                                    <input type="text" name="tracking_number" class="form-control" value="<?= e($ord['tracking_number'] ?? '') ?>" placeholder="e.g. BL-EXP-847291">
                                                </div>

                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold text-muted">Tracking Web URL</label>
                                                    <input type="url" name="tracking_url" class="form-control" value="<?= e($ord['tracking_url'] ?? '') ?>" placeholder="https://tracking.carrier.com?awb=...">
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-dark btn-sm" style="background-color: var(--color-espresso-deep); border-color: var(--color-gold);">
                                                    Save Fulfillment
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
