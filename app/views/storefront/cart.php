<?php
use App\Core\Cart;
use App\Core\MarketContext;

$pageTitle = 'Your Shopping Bag';
$summary = Cart::getSummary();
$activeCountry = MarketContext::getCountry();

require __DIR__ . '/../layouts/header.php';
?>

<div class="bg-white border-bottom py-3">
    <div class="container small text-muted">
        <a href="<?= url('') ?>" class="text-muted text-decoration-none">Home</a>
        <span class="mx-2">&bull;</span>
        <span class="text-dark fw-semibold">Shopping Bag</span>
    </div>
</div>

<section class="py-5" style="background-color: var(--color-ivory);">
    <div class="container">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-baseline gap-2 mb-4 pb-2 border-bottom">
            <h1 class="font-serif mb-0" style="color: var(--color-espresso-deep); font-size: clamp(1.7rem, 6vw, 2.2rem);">Your Shopping Bag</h1>
            <span class="text-muted small d-inline-flex align-items-center gap-1">Delivering to <?= country_flag($activeCountry['code'], $activeCountry['name'], 20) ?> <strong><?= e($activeCountry['name']) ?></strong></span>
        </div>

        <?php if (empty($summary['items'])): ?>
            <div class="text-center py-5 bg-white border p-5 shadow-soft">
                <i class="bi bi-bag text-muted" style="font-size: 3.5rem;"></i>
                <h3 class="font-serif mt-3" style="color: var(--color-espresso-deep);">Your Shopping Bag Is Empty</h3>
                <p class="text-muted small mb-4">Discover our handcrafted full-grain leather jackets, executive luggage, and accessories.</p>
                <a href="<?= url('shop') ?>" class="btn btn-buller-primary btn-lg">Explore All Products</a>
            </div>
        <?php else: ?>
            <div class="row g-5">
                <!-- Cart Items List -->
                <div class="col-lg-8">
                    <div class="bg-white border shadow-soft p-3 p-md-4" style="border-color: var(--color-border);">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0" style="min-width: 580px;">
                                <thead>
                                    <tr class="border-bottom text-muted small text-uppercase" style="letter-spacing: 0.08em;">
                                        <th style="width: 48%;">Item Details</th>
                                        <th>Unit Price</th>
                                        <th>Quantity</th>
                                        <th class="text-end">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($summary['items'] as $item): ?>
                                        <tr class="border-bottom">
                                            <td class="py-3 py-md-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= asset($item['primary_image']) ?>" alt="<?= e($item['title']) ?>" class="border flex-shrink-0" style="width: 70px; height: 80px; object-fit: cover;">
                                                    <div>
                                                        <span class="text-uppercase small text-muted d-block" style="font-size: 0.7rem;"><?= e($item['sku']) ?></span>
                                                        <h6 class="font-serif mb-1">
                                                            <a href="<?= url('product?slug=' . urlencode($item['slug'])) ?>" class="text-dark text-decoration-none">
                                                                <?= e($item['title']) ?>
                                                            </a>
                                                        </h6>
                                                        <span class="small text-muted"><?= e($item['leather_type'] ?? '') ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?= $item['formatted_unit'] ?></div>
                                                <?php if ($item['is_discounted']): ?>
                                                    <small class="text-muted text-decoration-line-through"><?= MarketContext::formatPrice($item['original_price']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-1">
                                                    <button class="cart-qty-btn" data-action="dec" data-product-id="<?= $item['product_id'] ?>" data-current-qty="<?= $item['quantity'] ?>">-</button>
                                                    <span class="px-2 fw-bold" style="font-size: 0.9rem;"><?= $item['quantity'] ?></span>
                                                    <button class="cart-qty-btn" data-action="inc" data-product-id="<?= $item['product_id'] ?>" data-current-qty="<?= $item['quantity'] ?>">+</button>
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold" style="color: var(--color-espresso-deep); font-size: 1.05rem; font-family: var(--font-sans); font-variant-numeric: tabular-nums;">
                                                <?= $item['formatted_total'] ?>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-link text-muted p-0 cart-item-remove" data-product-id="<?= $item['product_id'] ?>" title="Remove item">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="bg-white border shadow-soft p-4 sticky-top" style="top: 100px; border-color: var(--color-border);">
                        <h5 class="font-serif mb-4 pb-2 border-bottom" style="color: var(--color-espresso-deep);">Order Summary</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Bag Subtotal</span>
                            <span class="fw-semibold"><?= $summary['formatted_subtotal'] ?></span>
                        </div>

                        <?php if ($summary['discount_amount'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Coupon Discount</span>
                                <span>-<?= $summary['formatted_discount'] ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted"><?= e($summary['tax_name']) ?> (<?= $summary['tax_rate'] ?>%)</span>
                            <span><?= $summary['formatted_tax'] ?></span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Estimated Shipping</span>
                            <span class="<?= $summary['is_free_shipping'] ? 'text-success fw-bold' : '' ?>"><?= $summary['formatted_shipping'] ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <span class="fs-5 font-serif fw-bold" style="color: var(--color-espresso-deep);">Estimated Total</span>
                            <span class="fs-4 fw-bold" style="color: var(--color-espresso-deep); font-family: var(--font-sans); font-variant-numeric: tabular-nums;"><?= $summary['formatted_grand_total'] ?></span>
                        </div>

                        <a href="<?= url('checkout') ?>" class="btn btn-buller-gold w-100 py-3 text-center mb-3">
                            Proceed to Checkout <i class="bi bi-arrow-right ms-1"></i>
                        </a>

                        <div class="text-center small text-muted">
                            <i class="bi bi-shield-lock-fill text-warning me-1"></i> Guaranteed Safe & Encrypted Checkout
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
