<?php
use App\Core\Auth;
$pageTitle = 'Regional Order Fulfillment — ' . $currentCountry['name'];
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-serif mb-1 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                <?= country_flag($currentCountry['code'], $currentCountry['name'], 28) ?> <?= e($currentCountry['name']) ?> Customer Orders & Dispatch
            </h1>
            <p class="text-muted small mb-0">Process order dispatches, assign international courier air waybills (AWBs), and monitor payment status.</p>
        </div>
        <div class="badge p-2 px-3 fs-6" style="background-color: var(--color-espresso-deep); color: var(--color-gold);">
            Total Market Orders: <?= count($orders) ?>
        </div>
    </div>

    <!-- Orders Table Card -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-box-seam fs-1 mb-2 d-block"></i>
                <p>No orders currently registered for <?= e($currentCountry['name']) ?>.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Order Ref</th>
                            <th>Patron</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Payment</th>
                            <th>Fulfillment Status</th>
                            <th>Courier & Tracking</th>
                            <th class="text-end">Fulfill</th>
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
                                    <div><strong><?= e($ord['customer_name']) ?></strong></div>
                                    <small class="text-muted"><?= e($ord['customer_email']) ?> &bull; <?= e($ord['customer_phone']) ?></small>
                                </td>
                                <td class="small text-muted"><?= date('M d, Y H:i', strtotime($ord['created_at'])) ?></td>
                                <td>
                                    <strong class="fs-6 fw-bold" style="color: var(--color-espresso-deep); font-variant-numeric: tabular-nums;">
                                        <?= e($ord['currency_symbol']) . number_format($ord['grand_total'], 2) ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?= ($ord['payment_status'] === 'paid') ? 'success' : 'warning text-dark' ?> text-uppercase">
                                        <?= e($ord['payment_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= ($ord['order_status'] === 'delivered') ? 'success' : (($ord['order_status'] === 'dispatched') ? 'info text-dark' : 'warning text-dark') ?> text-uppercase">
                                        <?= e($ord['order_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($ord['tracking_number'])): ?>
                                        <div class="small"><strong><?= e($ord['shipping_carrier']) ?></strong></div>
                                        <code class="small"><?= e($ord['tracking_number']) ?></code>
                                    <?php else: ?>
                                        <span class="text-muted small">Awaiting Dispatch</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#fulfillModal_<?= $ord['id'] ?>">
                                        <i class="bi bi-truck me-1"></i> Update
                                    </button>
                                </td>
                            </tr>

                            <!-- Fulfill Order Modal -->
                            <div class="modal fade" id="fulfillModal_<?= $ord['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title font-serif">Fulfill Order #<?= e($ord['order_number']) ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= url('admin/orders/update-status') ?>" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                            <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                            <input type="hidden" name="country_id" value="<?= $currentCountry['id'] ?>">

                                            <div class="modal-body p-4 text-start">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Order Fulfillment State *</label>
                                                    <select name="order_status" class="form-select" required>
                                                        <option value="placed" <?= ($ord['order_status'] === 'placed') ? 'selected' : '' ?>>Placed (Awaiting Preparation)</option>
                                                        <option value="processing" <?= ($ord['order_status'] === 'processing') ? 'selected' : '' ?>>Processing in Atelier</option>
                                                        <option value="dispatched" <?= ($ord['order_status'] === 'dispatched') ? 'selected' : '' ?>>Dispatched via Courier</option>
                                                        <option value="delivered" <?= ($ord['order_status'] === 'delivered') ? 'selected' : '' ?>>Delivered to Patron</option>
                                                        <option value="cancelled" <?= ($ord['order_status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Courier / Carrier Name</label>
                                                    <input type="text" name="shipping_carrier" class="form-control" value="<?= e($ord['shipping_carrier'] ?? 'DHL Express') ?>" placeholder="DHL Express, FedEx, UPS, Blue Dart">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Tracking Number / AWB</label>
                                                    <input type="text" name="tracking_number" class="form-control" value="<?= e($ord['tracking_number'] ?? '') ?>" placeholder="e.g. DHL-849204819">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Tracking URL</label>
                                                    <input type="url" name="tracking_url" class="form-control" value="<?= e($ord['tracking_url'] ?? '') ?>" placeholder="https://www.dhl.com/track?num=...">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Payment Status Update</label>
                                                    <select name="payment_status" class="form-select">
                                                        <option value="pending" <?= ($ord['payment_status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                                                        <option value="paid" <?= ($ord['payment_status'] === 'paid') ? 'selected' : '' ?>>Paid</option>
                                                        <option value="refunded" <?= ($ord['payment_status'] === 'refunded') ? 'selected' : '' ?>>Refunded</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-dark btn-sm">Save Fulfillment</button>
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
