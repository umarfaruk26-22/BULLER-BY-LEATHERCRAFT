<?php
use App\Core\MarketContext;
use App\Core\Wishlist;

$pageTitle = 'Collections & Curations';
require __DIR__ . '/../layouts/header.php';

$activeCountry = MarketContext::getCountry();
?>

<!-- Shop Header Banner -->
<section class="py-5 bg-dark text-white position-relative" style="background-image: url('<?= asset('assets/images/banners/hero_leather_craft.jpg') ?>'); background-size: cover; background-position: center;">
    <div class="hero-overlay" style="background: rgba(18, 11, 6, 0.85);"></div>
    <div class="container position-relative py-4 text-center">
        <span class="text-uppercase small text-gold" style="letter-spacing: 0.25em;">Buller Atelier Curations</span>
        <h1 class="font-serif mt-2 mb-2" style="font-size: 2.8rem; color: #fff;">
            <?= isset($activeCategory) ? e($activeCategory['name']) : 'The Master Collection' ?>
        </h1>
        <p class="text-light small mx-auto mb-0" style="max-width: 600px; opacity: 0.85;">
            <?= isset($activeCategory) ? e($activeCategory['description']) : 'Explore handcrafted heirloom leather goods available for immediate fulfillment in ' . e($activeCountry['name']) . '.' ?>
        </p>
    </div>
</section>

<!-- Active Market Breadcrumb & Notice -->
<div class="bg-white border-bottom py-2">
    <div class="container d-flex flex-wrap justify-content-between align-items-center small text-muted">
        <div>
            <a href="<?= url('') ?>" class="text-muted text-decoration-none">Home</a>
            <span class="mx-2">&bull;</span>
            <span class="text-dark fw-semibold">Collections</span>
            <?php if (isset($activeCategory)): ?>
                <span class="mx-2">&bull;</span>
                <span class="text-dark fw-semibold"><?= e($activeCategory['name']) ?></span>
            <?php endif; ?>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center gap-1">Showing market availability for: <?= country_flag($activeCountry['code'], $activeCountry['name'], 20) ?> <strong><?= e($activeCountry['name']) ?> (<?= e($activeCountry['currency_code']) ?>)</strong></span>
        </div>
    </div>
</div>

<!-- Shop Main Content -->
<section class="py-5" style="background-color: var(--color-ivory);">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <!-- Mobile Filter Toggle Button -->
                <div class="d-lg-none mb-3">
                    <button class="btn btn-outline-dark w-100 d-flex justify-content-between align-items-center py-2 px-3 bg-white shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#shopFilterSidebar" aria-expanded="false" aria-controls="shopFilterSidebar">
                        <span><i class="bi bi-funnel me-2 text-warning"></i> Filter Curations & Categories</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>

                <div class="collapse d-lg-block" id="shopFilterSidebar">
                    <div class="p-4 bg-white border shadow-soft sticky-top mb-4 mb-lg-0" style="top: 100px; border-color: var(--color-border); z-index: 10;">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <h5 class="font-serif mb-0" style="color: var(--color-espresso-deep); font-size: 1.15rem;">Filter Ateliers</h5>
                            <a href="<?= url('shop') ?>" class="text-muted small text-decoration-none">Reset</a>
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-4">
                            <h6 class="text-uppercase small fw-bold mb-3" style="letter-spacing: 0.1em; color: var(--color-gold-dark);">Categories</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="<?= url('shop') ?>" class="d-flex justify-content-between align-items-center <?= (!isset($_GET['category'])) ? 'fw-bold text-dark' : 'text-muted' ?>" style="font-size: 0.9rem;">
                                        <span>All Curations</span>
                                    </a>
                                </li>
                                <?php foreach ($categories as $cat): ?>
                                    <li class="mb-2">
                                        <a href="<?= url('shop?category=' . urlencode($cat['slug'])) ?>" class="d-flex justify-content-between align-items-center <?= (($_GET['category'] ?? '') === $cat['slug']) ? 'fw-bold text-dark' : 'text-muted' ?>" style="font-size: 0.9rem;">
                                            <span><?= e($cat['name']) ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Curations Special Filter -->
                        <div class="mb-4 pt-3 border-top">
                            <h6 class="text-uppercase small fw-bold mb-3" style="letter-spacing: 0.1em; color: var(--color-gold-dark);">Special Editions</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="<?= url('shop?filter=sale') ?>" class="d-flex align-items-center gap-2 <?= (($_GET['filter'] ?? '') === 'sale') ? 'fw-bold text-danger' : 'text-muted' ?>" style="font-size: 0.9rem;">
                                        <i class="bi bi-tag-fill text-danger"></i> Privé Sale Curations
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('shop?filter=bestseller') ?>" class="d-flex align-items-center gap-2 <?= (($_GET['filter'] ?? '') === 'bestseller') ? 'fw-bold text-dark' : 'text-muted' ?>" style="font-size: 0.9rem;">
                                        <i class="bi bi-star-fill text-warning"></i> Bestseller Classics
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('shop?filter=new') ?>" class="d-flex align-items-center gap-2 <?= (($_GET['filter'] ?? '') === 'new') ? 'fw-bold text-dark' : 'text-muted' ?>" style="font-size: 0.9rem;">
                                        <i class="bi bi-sparkles text-primary"></i> New Atelier Releases
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Market Assistance Notice -->
                        <div class="p-3 bg-light border small text-muted rounded-1">
                            <i class="bi bi-info-circle text-primary me-1"></i>
                            <span>All prices include <strong><?= e($activeCountry['tax_name']) ?></strong> for seamless customs delivery.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <!-- Sorting & Count Bar -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center pb-3 mb-4 border-bottom">
                    <span class="text-muted small mb-2 mb-sm-0">
                        Displaying <strong><?= count($products) ?></strong> artisan creations available in <?= e($activeCountry['name']) ?>
                    </span>

                    <div class="d-flex align-items-center gap-2">
                        <label for="sortSelect" class="small text-muted text-nowrap">Sort By:</label>
                        <select id="sortSelect" class="form-select form-select-sm" style="width: auto; min-width: 170px;" onchange="location = this.value;">
                            <option value="<?= url('shop' . (isset($_GET['category']) ? '?category=' . urlencode($_GET['category']) : '')) ?>" <?= empty($_GET['sort']) ? 'selected' : '' ?>>Featured Creations</option>
                            <option value="<?= url('shop?sort=price_asc' . (isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '')) ?>" <?= (($_GET['sort'] ?? '') === 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="<?= url('shop?sort=price_desc' . (isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '')) ?>" <?= (($_GET['sort'] ?? '') === 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="<?= url('shop?sort=newest' . (isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '')) ?>" <?= (($_GET['sort'] ?? '') === 'newest') ? 'selected' : '' ?>>Newest Arrivals</option>
                        </select>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                    <div class="text-center py-5 bg-white border p-5">
                        <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                        <h4 class="font-serif mt-3" style="color: var(--color-espresso-deep);">No Products Found</h4>
                        <p class="text-muted small">We could not locate items matching your search criteria in this region.</p>
                        <a href="<?= url('shop') ?>" class="btn btn-buller-primary btn-sm mt-2">Clear Filters</a>
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
                            $isWishlisted = Wishlist::has((int)$p['id']);
                        ?>
                            <div class="col-6 col-sm-6 col-md-4 mb-2 mb-md-3">
                                <div class="product-card h-100">
                                    <div class="product-image-wrapper">
                                        <img src="<?= asset($p['primary_image']) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                                        
                                        <div class="product-badges">
                                            <?php if ($pricing['is_discounted']): ?>
                                                <span class="badge-luxury badge-sale">-<?= $pricing['discount_percent'] ?>%</span>
                                            <?php endif; ?>
                                            <?php if ($p['is_bestseller']): ?>
                                                <span class="badge-luxury badge-bestseller d-none d-sm-inline-block">Bestseller</span>
                                            <?php endif; ?>
                                        </div>

                                        <button class="wishlist-toggle-btn <?= $isWishlisted ? 'active' : '' ?>" data-product-id="<?= $p['id'] ?>" title="Save to Wishlist">
                                            <i class="bi <?= $isWishlisted ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
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
                                            <i class="bi bi-bag-plus me-1"></i> Add To Bag
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
