<?php
use App\Core\MarketContext;
use App\Core\Wishlist;

$pageTitle = 'Your Wishlist';
$activeCountry = MarketContext::getCountry();

require __DIR__ . '/../layouts/header.php';
?>

<div class="bg-white border-bottom py-3">
    <div class="container small text-muted">
        <a href="<?= url('') ?>" class="text-muted text-decoration-none">Home</a>
        <span class="mx-2">&bull;</span>
        <span class="text-dark fw-semibold">Wishlist</span>
    </div>
</div>

<section class="py-5" style="background-color: var(--color-ivory);">
    <div class="container">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-baseline mb-4 pb-2 border-bottom gap-2">
            <h1 class="font-serif mb-0" style="color: var(--color-espresso-deep); font-size: clamp(1.5rem, 4vw, 2.2rem);">Your Saved Wishlist</h1>
            <span class="text-muted small d-inline-flex align-items-center gap-1">Viewing prices in <?= country_flag($activeCountry['code'], $activeCountry['name'], 20) ?> <strong><?= e($activeCountry['name']) ?> (<?= e($activeCountry['currency_code']) ?>)</strong></span>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-5 bg-white border p-4 p-md-5 shadow-soft rounded-2">
                <i class="bi bi-heart text-muted" style="font-size: 3rem;"></i>
                <h3 class="font-serif mt-3" style="color: var(--color-espresso-deep); font-size: clamp(1.3rem, 3vw, 1.8rem);">Your Wishlist Is Empty</h3>
                <p class="text-muted small mb-4">Click the heart icon on any product to save it to your wishlist.</p>
                <a href="<?= url('shop') ?>" class="btn btn-buller-primary btn-lg">Explore All Products</a>
            </div>
        <?php else: ?>
            <div class="row g-2 g-sm-3 g-md-4">
                <?php foreach ($products as $p): 
                    $pricing = MarketContext::evaluatePricing(
                        (float)$p['original_price'],
                        $p['discount_price'] ? (float)$p['discount_price'] : null,
                        $p['discount_start_date'],
                        $p['discount_end_date']
                    );
                ?>
                    <div class="col-6 col-md-6 col-lg-3 mb-2 mb-md-3">
                        <div class="product-card h-100" data-product-url="<?= url('product?slug=' . urlencode($p['slug'])) ?>">
                            <div class="product-image-wrapper">
                                <a href="<?= url('product?slug=' . urlencode($p['slug'])) ?>" class="product-image-link">
                                    <img src="<?= asset($p['primary_image']) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                                </a>
                                <button class="wishlist-toggle-btn active" data-product-id="<?= $p['id'] ?>" title="Remove from Wishlist">
                                    <i class="bi bi-heart-fill"></i>
                                </button>
                            </div>
                            <div class="product-card-body">
                                <span class="product-leather-type"><?= e($p['leather_type'] ?? 'Handcrafted Leather') ?></span>
                                <h4 class="product-card-title">
                                    <a href="<?= url('product?slug=' . urlencode($p['slug'])) ?>"><?= e($p['title']) ?></a>
                                </h4>
                                <div class="product-card-pricing">
                                    <span class="price-current"><?= $pricing['formatted_final'] ?></span>
                                    <?php if ($pricing['is_discounted']): ?>
                                        <span class="price-original"><?= $pricing['formatted_original'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="btn btn-quick-add mt-auto" data-product-id="<?= $p['id'] ?>">
                                    <i class="bi bi-bag-plus me-1"></i> Move to Bag
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
