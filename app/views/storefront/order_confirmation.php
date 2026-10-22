<?php
use App\Core\MarketContext;

$pageTitle = 'Order Confirmed #' . $order['order_number'];
require __DIR__ . '/../layouts/header.php';

$shippingAddress = json_decode($order['shipping_address_json'], true);
?>

<section class="py-5" style="background-color: var(--color-ivory);">
    <div class="container">
        <div class="max-w-800 mx-auto">
            <!-- Success Banner -->
            <div class="p-4 p-md-5 bg-white border shadow-soft text-center mb-4" style="border-color: var(--color-border); border-top: 4px solid var(--color-gold) !important;">
                <div class="mb-3">
                    <img src="<?= asset('assets/images/buller-logo.svg') ?>" alt="Buller Leather" style="height: 62px; width: auto;">
                </div>
                <h1 class="font-serif mb-2" style="font-size: 2.3rem; color: var(--color-espresso-deep);">Thank You For Your Order</h1>
                <p class="text-muted mb-3">Your order has been placed successfully with Buller Leather.</p>
                <div class="badge p-2 px-3 fs-6" style="background-color: var(--color-espresso-deep); color: var(--color-gold);">
                    Order Reference: #<?= e($order['order_number']) ?>
                </div>
            </div>

            <!-- Order Details Card -->
            <div class="bg-white border shadow-soft p-4 p-md-5 mb-4" style="border-color: var(--color-border);">
                <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                    <div>
                        <span class="text-muted small">Order Status:</span>
                        <span class="badge bg-warning text-dark text-uppercase ms-1"><?= e($order['order_status']) ?></span>
                    </div>
                    <div>
                        <span class="text-muted small">Payment Status:</span>
                        <span class="badge bg-success text-uppercase ms-1"><?= e($order['payment_status']) ?></span>
                    </div>
                    <button class="btn btn-outline-dark btn-sm d-print-none" onclick="window.print();">
                        <i class="bi bi-printer me-1"></i> Print Invoice
                    </button>
                </div>

                <div class="row g-4 mb-4 pb-4 border-bottom">
                    <div class="col-md-6">
                        <h6 class="text-uppercase small fw-bold mb-2" style="color: var(--color-gold-dark);">Delivery Destination</h6>
                        <strong><?= e($order['customer_name']) ?></strong><br>
                        <?= e($shippingAddress['street_1'] ?? '') ?><br>
                        <?php if (!empty($shippingAddress['street_2'])): ?>
                            <?= e($shippingAddress['street_2']) ?><br>
                        <?php endif; ?>
                        <?= e($shippingAddress['city'] ?? '') ?>, <?= e($shippingAddress['state'] ?? '') ?> <?= e($shippingAddress['postal_code'] ?? '') ?><br>
                        <strong><?= e($order['country_name']) ?></strong><br>
                        <small class="text-muted">Contact: <?= e($order['customer_phone']) ?> | <?= e($order['customer_email']) ?></small>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-uppercase small fw-bold mb-2" style="color: var(--color-gold-dark);">Logistics & Tracking</h6>
                        <span class="text-muted d-block small">Carrier: <strong><?= e($order['shipping_carrier'] ?? 'Buller Express International Courier') ?></strong></span>
                        <span class="text-muted d-block small">Tracking Number: <strong><?= e($order['tracking_number'] ?? 'Assigned at dispatch') ?></strong></span>
                        <span class="text-muted d-block small mt-2">Estimated Arrival: <strong>3 - 5 Business Days</strong></span>
                    </div>
                </div>

                <!-- Items Table -->
                <h6 class="text-uppercase small fw-bold mb-3" style="color: var(--color-gold-dark);">Order Items</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderItems as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="<?= asset($item['product_image'] ?? 'assets/images/banners/hero_leather_craft.jpg') ?>" class="border" style="width: 45px; height: 50px; object-fit: cover;">
                                            <div>
                                                <strong><?= e($item['product_title']) ?></strong><br>
                                                <small class="text-muted">SKU: <?= e($item['sku']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end"><?= e($order['currency_symbol']) . number_format($item['unit_price'], 2) ?></td>
                                    <td class="text-end fw-bold"><?= e($order['currency_symbol']) . number_format($item['total_price'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="small">
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <td class="text-end"><?= e($order['currency_symbol']) . number_format($order['subtotal'], 2) ?></td>
                            </tr>
                            <?php if ($order['discount_amount'] > 0): ?>
                                <tr>
                                    <th colspan="3" class="text-end text-success">Discount Applied:</th>
                                    <td class="text-end text-success">-<?= e($order['currency_symbol']) . number_format($order['discount_amount'], 2) ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th colspan="3" class="text-end">Tax / VAT:</th>
                                <td class="text-end"><?= e($order['currency_symbol']) . number_format($order['tax_amount'], 2) ?></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">International Shipping:</th>
                                <td class="text-end"><?= ($order['shipping_amount'] == 0) ? 'Complimentary' : e($order['currency_symbol']) . number_format($order['shipping_amount'], 2) ?></td>
                            </tr>
                            <tr class="fs-6">
                                <th colspan="3" class="text-end fw-bold">Grand Total (<?= e($order['currency_code']) ?>):</th>
                                <td class="text-end fw-bold text-dark font-price" style="font-family: var(--font-price); font-variant-numeric: tabular-nums lining-nums;"><?= e($order['currency_symbol']) . number_format($order['grand_total'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="text-center d-print-none mt-4">
                    <a href="<?= url('') ?>" class="btn btn-buller-primary me-2">Return to Home</a>
                    <a href="<?= url('shop') ?>" class="btn btn-buller-outline">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
