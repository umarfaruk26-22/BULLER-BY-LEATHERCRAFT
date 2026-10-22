<?php
use App\Core\Cart;
use App\Core\MarketContext;

$cartSummary = Cart::getSummary();
?>

<!-- Offcanvas Cart Drawer -->
<div class="offcanvas offcanvas-end cart-drawer" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel">
    <div class="offcanvas-header border-bottom py-3" style="border-color: var(--color-border) !important;">
        <div>
            <h5 class="offcanvas-title font-serif" id="cartDrawerLabel" style="color: var(--color-espresso-deep); font-weight: 700;">
                Your Shopping Bag
            </h5>
            <div class="text-muted small">
                Shipping to <strong><?= e(MarketContext::getCountry()['name']) ?></strong> (<?= e(MarketContext::getCountry()['currency_code']) ?>)
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Free Shipping Progress -->
    <?php 
    $country = MarketContext::getCountry();
    $threshold = (float)$country['free_shipping_threshold'];
    $subtotal = (float)$cartSummary['subtotal'];
    $diff = max(0, $threshold - $subtotal);
    ?>
    <?php if ($threshold > 0): ?>
        <div class="p-3 bg-white border-bottom" style="border-color: var(--color-border) !important;">
            <?php if ($diff > 0): ?>
                <div class="d-flex justify-content-between text-muted small mb-1">
                    <span>Add <strong><?= MarketContext::formatPrice($diff) ?></strong> more for Complimentary Delivery</span>
                    <span><?= round(min(100, ($subtotal / $threshold) * 100)) ?>%</span>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar" role="progressbar" style="width: <?= min(100, ($subtotal / $threshold) * 100) ?>%; background-color: var(--color-gold-dark);"></div>
                </div>
            <?php else: ?>
                <div class="text-success small fw-semibold text-center">
                    <i class="bi bi-patch-check-fill me-1"></i> You have unlocked Complimentary International Delivery!
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Items Container -->
    <div class="offcanvas-body p-4" id="cart-drawer-items-container">
        <?php if (empty($cartSummary['items'])): ?>
            <div class="text-center py-5">
                <i class="bi bi-bag text-muted" style="font-size: 3rem;"></i>
                <p class="mt-3 text-muted">Your luxury shopping bag is currently empty.</p>
                <a href="<?= url('shop') ?>" class="btn btn-buller-primary btn-sm mt-2">Explore Collections</a>
            </div>
        <?php else: ?>
            <?php foreach ($cartSummary['items'] as $item): ?>
                <div class="cart-item-row">
                    <img src="<?= asset($item['primary_image']) ?>" alt="<?= e($item['title']) ?>" class="cart-item-img">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="mb-1" style="font-size: 0.95rem; font-family: var(--font-heading);">
                                <a href="<?= url('product?slug=' . urlencode($item['slug'])) ?>" class="text-dark">
                                    <?= e($item['title']) ?>
                                </a>
                            </h6>
                            <button class="btn btn-link text-muted p-0 cart-item-remove" data-product-id="<?= $item['product_id'] ?>">
                                <i class="bi bi-trash3" style="font-size: 0.85rem;"></i>
                            </button>
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem;"><?= e($item['leather_type'] ?? '') ?></div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="d-flex align-items-center gap-1">
                                <button class="cart-qty-btn" data-action="dec" data-product-id="<?= $item['product_id'] ?>" data-current-qty="<?= $item['quantity'] ?>">-</button>
                                <span class="px-2" style="font-size: 0.85rem; font-weight: 600;"><?= $item['quantity'] ?></span>
                                <button class="cart-qty-btn" data-action="inc" data-product-id="<?= $item['product_id'] ?>" data-current-qty="<?= $item['quantity'] ?>">+</button>
                            </div>
                            <div class="fw-bold font-price cart-item-total" style="font-size: 0.95rem; color: var(--color-espresso-deep); font-family: var(--font-price); font-variant-numeric: tabular-nums lining-nums;">
                                <?= $item['formatted_total'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Drawer Footer -->
    <div class="offcanvas-footer p-4 border-top bg-white" style="border-color: var(--color-border) !important;">
        <div class="d-flex justify-content-between align-items-baseline mb-2">
            <span class="text-muted" style="text-transform: uppercase; letter-spacing: 0.08em; font-size: 0.85rem;">Estimated Subtotal</span>
            <span class="fs-5 fw-bold font-price" id="cart-drawer-subtotal" style="color: var(--color-espresso-deep); font-family: var(--font-price); font-variant-numeric: tabular-nums lining-nums;">
                <?= $cartSummary['formatted_subtotal'] ?>
            </span>
        </div>
        <div class="text-muted small mb-3">Taxes and final delivery duties calculated during checkout.</div>
        <div class="d-grid gap-2">
            <a href="<?= url('checkout') ?>" class="btn btn-buller-gold py-2 text-center">
                Proceed to Checkout <i class="bi bi-arrow-right ms-1"></i>
            </a>
            <a href="<?= url('cart') ?>" class="btn btn-buller-outline py-2 text-center" style="font-size: 0.8rem;">
                View Full Bag Details
            </a>
        </div>
    </div>
</div>
