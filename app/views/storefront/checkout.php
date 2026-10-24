<?php
use App\Core\Cart;
use App\Core\MarketContext;
use App\Core\Auth;

$pageTitle = 'Checkout';
$summary = Cart::getSummary();
$activeCountry = MarketContext::getCountry();
$currentUser = Auth::user();

require __DIR__ . '/../layouts/header.php';
?>

<div class="bg-white border-bottom py-3">
    <div class="container small text-muted">
        <a href="<?= url('') ?>" class="text-muted text-decoration-none">Home</a>
        <span class="mx-2">&bull;</span>
        <a href="<?= url('cart') ?>" class="text-muted text-decoration-none">Bag</a>
        <span class="mx-2">&bull;</span>
        <span class="text-dark fw-semibold">Checkout</span>
    </div>
</div>

<section class="py-5" style="background-color: var(--color-ivory);">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-uppercase small text-gold" style="letter-spacing: 0.25em;">Buller Leather Official</span>
            <h1 class="font-serif mt-1" style="font-size: 2.4rem; color: var(--color-espresso-deep);">Secure International Checkout</h1>
            <p class="text-muted small">Shipping directly to <strong class="d-inline-flex align-items-center gap-1"><?= country_flag($activeCountry['code'], $activeCountry['name'], 20) ?> <?= e($activeCountry['name']) ?></strong> &bull; Currency: <strong><?= e($activeCountry['currency_code']) ?> (<?= e($activeCountry['currency_symbol']) ?>)</strong></p>
        </div>

        <?php if (empty($summary['items'])): ?>
            <div class="text-center py-5 bg-white border p-5">
                <i class="bi bi-bag-x text-muted" style="font-size: 3rem;"></i>
                <h4 class="font-serif mt-3">Your bag is empty</h4>
                <p class="text-muted small">Please select products to proceed with checkout.</p>
                <a href="<?= url('shop') ?>" class="btn btn-buller-primary btn-sm mt-2">Browse Collections</a>
            </div>
        <?php else: ?>
            <div class="row g-5">
                <!-- Left: Delivery & Payment Details Form -->
                <div class="col-lg-7">
                    <form action="<?= url('order/process') ?>" method="POST" id="checkoutForm">
                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                        <input type="hidden" name="country_id" value="<?= $activeCountry['id'] ?>">

                        <!-- 1. Customer Contact Details -->
                        <div class="bg-white border shadow-soft p-4 mb-4" style="border-color: var(--color-border);">
                            <h5 class="font-serif mb-4 pb-2 border-bottom" style="color: var(--color-espresso-deep);">
                                1. Customer & Contact Information
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Full Name *</label>
                                    <input type="text" name="customer_name" class="form-control" value="<?= e($currentUser['name'] ?? '') ?>" required placeholder="e.g. Julian Sterling">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Email Address *</label>
                                    <input type="email" name="customer_email" class="form-control" value="<?= e($currentUser['email'] ?? '') ?>" required placeholder="your.name@domain.com">
                                    <small class="text-muted">Order confirmation & tracking will be sent here.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Phone Number *</label>
                                    <input type="tel" name="customer_phone" class="form-control" required placeholder="<?= e($activeCountry['phone_code']) ?> 98765 43210">
                                    <small class="text-muted">Required by international couriers.</small>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Delivery Address -->
                        <div class="bg-white border shadow-soft p-4 mb-4" style="border-color: var(--color-border);">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <h5 class="font-serif mb-0" style="color: var(--color-espresso-deep);">
                                    2. Delivery Destination
                                </h5>
                                <span class="badge d-inline-flex align-items-center gap-2" style="background-color: var(--color-cream); color: var(--color-espresso-deep); border: 1px solid var(--color-border);">
                                    <?= country_flag($activeCountry['code'], $activeCountry['name'], 20) ?> <?= e($activeCountry['name']) ?>
                                </span>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Street Address *</label>
                                    <input type="text" name="street_address_1" class="form-control" required placeholder="Street address, house/building name">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Apartment, Suite, Unit (Optional)</label>
                                    <input type="text" name="street_address_2" class="form-control" placeholder="Apt, Suite, Floor">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">City *</label>
                                    <input type="text" name="city" class="form-control" required placeholder="City">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">State / Province *</label>
                                    <input type="text" name="state" class="form-control" required placeholder="State or County">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Postal / ZIP Code *</label>
                                    <input type="text" name="postal_code" class="form-control" required placeholder="Postal Code">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Special Delivery Instructions (Optional)</label>
                                    <textarea name="customer_notes" class="form-control" rows="2" placeholder="Gate codes, delivery time preferences, delivery notes..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Payment Method -->
                        <div class="bg-white border shadow-soft p-4 mb-4" style="border-color: var(--color-border);">
                            <h5 class="font-serif mb-4 pb-2 border-bottom" style="color: var(--color-espresso-deep);">
                                3. Payment Method
                            </h5>

                            <div class="d-flex flex-column gap-3">
                                <?php if ($activeCountry['code'] === 'IN'): ?>
                                    <label class="border p-3 d-flex align-items-center gap-3 cursor-pointer rounded-1 bg-light">
                                        <input type="radio" name="payment_method" value="razorpay" checked class="form-check-input">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold" style="color: var(--color-espresso-deep);">Razorpay Secure Checkout</div>
                                            <div class="text-muted small">Cards, UPI, Netbanking, Cred & EMI via Razorpay Gateway</div>
                                        </div>
                                        <span class="badge bg-dark">Razorpay</span>
                                    </label>
                                <?php endif; ?>

                                <label class="border p-3 d-flex align-items-center gap-3 cursor-pointer rounded-1 <?= ($activeCountry['code'] !== 'IN') ? 'bg-light' : '' ?>">
                                    <input type="radio" name="payment_method" value="stripe" <?= ($activeCountry['code'] !== 'IN') ? 'checked' : '' ?> class="form-check-input">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold" style="color: var(--color-espresso-deep);">International Credit / Debit Card</div>
                                        <div class="text-muted small">Visa, MasterCard, American Express, Apple Pay</div>
                                    </div>
                                    <div class="d-flex gap-1 text-muted">
                                        <i class="bi bi-credit-card-2-front fs-4"></i>
                                    </div>
                                </label>

                                <label class="border p-3 d-flex align-items-center gap-3 cursor-pointer rounded-1">
                                    <input type="radio" name="payment_method" value="cod" class="form-check-input">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold" style="color: var(--color-espresso-deep);">Cash on Delivery (COD)</div>
                                        <div class="text-muted small">Pay upon receipt after product inspection at delivery</div>
                                    </div>
                                    <i class="bi bi-cash-coin fs-4 text-muted"></i>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Order Button -->
                        <button type="submit" class="btn btn-buller-gold btn-lg w-100 py-3 shadow-deep">
                            Authorize & Confirm Order (<?= $summary['formatted_grand_total'] ?>) <i class="bi bi-shield-check ms-2"></i>
                        </button>
                    </form>
                </div>

                <!-- Right: Order Summary Breakdown -->
                <div class="col-lg-5">
                    <div class="bg-white border shadow-soft p-4 sticky-top" style="top: 100px; border-color: var(--color-border);">
                        <h5 class="font-serif mb-4 pb-2 border-bottom" style="color: var(--color-espresso-deep);">
                            Order Summary (<?= $summary['items_count'] ?> items)
                        </h5>

                        <div class="mb-4" style="max-height: 280px; overflow-y: auto;">
                            <?php foreach ($summary['items'] as $item): ?>
                                <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                    <img src="<?= asset($item['primary_image']) ?>" alt="<?= e($item['title']) ?>" class="border" style="width: 55px; height: 60px; object-fit: cover;">
                                    <div class="flex-grow-1 small">
                                        <div class="fw-semibold text-truncate text-dark" style="max-width: 200px;"><?= e($item['title']) ?></div>
                                        <div class="text-muted">Qty: <?= $item['quantity'] ?> &bull; <?= $item['formatted_unit'] ?></div>
                                    </div>
                                    <div class="fw-bold" style="color: var(--color-espresso-deep); font-family: var(--font-sans); font-variant-numeric: tabular-nums;">
                                        <?= $item['formatted_total'] ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Coupon Code Box -->
                        <form action="<?= url('coupon/apply') ?>" method="POST" class="mb-4">
                            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                            <label class="form-label small fw-bold">Discount Code / Promo Code</label>
                            <div class="input-group">
                                <input type="text" name="coupon_code" class="form-control text-uppercase" placeholder="e.g. WELCOME10" required>
                                <button class="btn btn-buller-primary px-3" type="submit" style="font-size: 0.75rem;">Apply</button>
                            </div>
                            <?php if (has_flash('coupon_error')): ?>
                                <small class="text-danger mt-1 d-block"><?= flash('coupon_error') ?></small>
                            <?php elseif (has_flash('coupon_success')): ?>
                                <small class="text-success mt-1 d-block"><?= flash('coupon_success') ?></small>
                            <?php endif; ?>
                        </form>

                        <!-- Price Breakdown -->
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold"><?= $summary['formatted_subtotal'] ?></span>
                        </div>

                        <?php if ($summary['discount_amount'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2 small text-success">
                                <span>Coupon Discount (<?= e($summary['coupon']['code'] ?? '') ?>)</span>
                                <span>-<?= $summary['formatted_discount'] ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted"><?= e($summary['tax_name']) ?> (<?= $summary['tax_rate'] ?>%)</span>
                            <span><?= $summary['formatted_tax'] ?></span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom small">
                            <span class="text-muted">International Shipping</span>
                            <span class="<?= $summary['is_free_shipping'] ? 'text-success fw-bold' : '' ?>"><?= $summary['formatted_shipping'] ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <span class="fs-5 font-serif fw-bold" style="color: var(--color-espresso-deep);">Grand Total</span>
                            <span class="fs-4 fw-bold font-price" style="color: var(--color-espresso-deep); font-family: var(--font-price); font-variant-numeric: tabular-nums lining-nums;"><?= $summary['formatted_grand_total'] ?></span>
                        </div>

                        <div class="p-3 bg-light border small text-muted">
                            <i class="bi bi-shield-lock-fill text-warning me-1"></i>
                            <span>All transactions are processed through 256-bit PCI-DSS encrypted banking channels.</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
