<?php
use App\Core\MarketContext;
use App\Core\Wishlist;

$pageTitle = 'Generational Leather Craftsmanship & Heritage Luxury';
require __DIR__ . '/../layouts/header.php';

$activeCountry = MarketContext::getCountry();
?>

<!-- 1. CINEMATIC LUXURY HERO BANNER -->
<section class="hero-slider-section" style="background-image: url('<?= asset('assets/images/banners/hero_leather_craft.jpg') ?>');">
    <div class="hero-overlay"></div>
    <div class="container hero-content py-5">
        <div class="row align-items-center">
            <div class="col-lg-8 col-xl-7">
                <span class="hero-subtitle">
                    <i class="bi bi-gem text-gold me-2"></i> Mastercrafted In Limited Batches
                </span>
                <h1 class="hero-title">
                    Generational Leather. <br>
                    <span style="color: var(--color-gold-bright); font-style: italic;">Forged For Decades.</span>
                </h1>
                <p class="hero-description">
                    Buller Leather crafts heirloom-grade outerwear, executive luggage, and accessories from the finest European full-grain hides. Every piece is constructed to withstand decades and age with peerless patina.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= url('shop') ?>" class="btn btn-buller-gold btn-lg">
                        Explore Master Curations <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                    <a href="<?= url('about') ?>" class="btn btn-buller-outline-light btn-lg">
                        Our Atelier Heritage
                    </a>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-3 gap-md-4 mt-5 pt-3 hero-trust-bar">
                    <div class="hero-trust-item">
                        <strong class="trust-title"><i class="bi bi-shield-check text-warning"></i> 100% Full-Grain</strong>
                        <span class="trust-desc">Hand-Selected European Hides</span>
                    </div>
                    <div class="hero-trust-item hero-trust-divider">
                        <strong class="trust-title"><i class="bi bi-award text-warning"></i> Lifetime Warranty</strong>
                        <span class="trust-desc">On Hardware & Structural Stitching</span>
                    </div>
                    <div class="hero-trust-item hero-trust-divider">
                        <strong class="trust-title">
                            <?= country_flag($activeCountry['code'], $activeCountry['name'], 20) ?> Localized Market
                        </strong>
                        <span class="trust-desc"><?= e($activeCountry['name']) ?> &bull; <?= e($activeCountry['currency_code']) ?> Delivery</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. SHOP BY CATEGORY -->
<section class="py-5" style="background-color: var(--color-cream);">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5">
            <div>
                <span class="text-uppercase small text-muted" style="letter-spacing: 0.18em;">The Buller Ateliers</span>
                <h2 class="font-serif mt-1" style="font-size: 2.3rem; color: var(--color-espresso-deep);">Curated Disciplines</h2>
            </div>
            <a href="<?= url('shop') ?>" class="text-uppercase fw-semibold small mt-3 mt-md-0" style="color: var(--color-cognac); letter-spacing: 0.1em;">
                View All Categories <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($categories as $cat): ?>
                <div class="col-lg-4 col-md-6">
                    <a href="<?= url('shop?category=' . urlencode($cat['slug'])) ?>" class="category-card">
                        <img src="<?= asset($cat['image_url']) ?>" alt="<?= e($cat['name']) ?>" loading="lazy">
                        <div class="category-card-overlay">
                            <h3 class="category-card-title"><?= e($cat['name']) ?></h3>
                            <p class="small text-light mb-2 text-truncate" style="opacity: 0.85;"><?= e($cat['description']) ?></p>
                            <span class="category-card-explore">
                                Discover Atelier <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 3. ATELIER BESTSELLERS (Consolidated Single Section) -->
<section class="py-5" id="bestsellers">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-uppercase small" style="color: var(--color-cognac); letter-spacing: 0.2em; font-weight: 600;">Client Favorites &bull; Hand-Selected For <?= e($activeCountry['name']) ?></span>
            <h2 class="font-serif mt-1" style="font-size: 2.5rem; color: var(--color-espresso-deep);">Atelier Bestsellers</h2>
            <p class="text-muted">Our most revered full-grain creations, chosen by discerning clients worldwide and mastercrafted to endure generations.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($bestsellerProducts as $p): 
                $pricing = MarketContext::evaluatePricing(
                    (float)$p['original_price'],
                    $p['discount_price'] ? (float)$p['discount_price'] : null,
                    $p['discount_start_date'],
                    $p['discount_end_date']
                );
                $isWishlisted = Wishlist::has((int)$p['id']);
            ?>
                <div class="col-lg-3 col-md-6">
                    <div class="product-card">
                        <div class="product-image-wrapper">
                            <img src="<?= asset($p['primary_image']) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                            
                            <div class="product-badges">
                                <?php if ($pricing['is_discounted']): ?>
                                    <span class="badge-luxury badge-sale">-<?= $pricing['discount_percent'] ?>% Privé</span>
                                <?php endif; ?>
                                <span class="badge-luxury badge-bestseller">Bestseller</span>
                            </div>

                            <button class="wishlist-toggle-btn <?= $isWishlisted ? 'active' : '' ?>" data-product-id="<?= $p['id'] ?>" title="Save to Wishlist">
                                <i class="bi <?= $isWishlisted ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                            </button>
                        </div>

                        <div class="product-card-body">
                            <span class="product-leather-type"><?= e($p['leather_type'] ?? 'Full-Grain Leather') ?></span>
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

        <div class="text-center mt-5">
            <a href="<?= url('shop') ?>" class="btn btn-buller-primary btn-lg">
                Explore All Master Curations <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- 4. ART OF BULLER CRAFTSMANSHIP (Story & Heritage) -->
<section class="craftsmanship-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="text-uppercase small text-gold" style="letter-spacing: 0.25em;">The Buller Philosophy</span>
                <h2 class="font-serif mt-2 mb-4" style="font-size: 2.6rem; color: #fff;">
                    Rejecting Obsolescence. <br>
                    <span style="color: var(--color-gold);">Embracing Eternity.</span>
                </h2>
                <p class="text-light mb-4" style="line-height: 1.8; opacity: 0.85;">
                    In an era saturated by disposable synthetics and bonded leathers, Buller Leather stands as a resolute guardian of traditional craftsmanship. We reject shortcuts, speed tanning, and chrome-based chemicals.
                </p>

                <div class="craftsmanship-pillar">
                    <div class="pillar-number">01</div>
                    <h4 class="pillar-title">Full-Grain Vegetable Tanned Hides</h4>
                    <p class="pillar-desc">Only the unbuffed top layer of the hide is chosen, steeped in natural tree barks and chestnut extracts over 60 days to retain natural grain signatures.</p>
                </div>

                <div class="craftsmanship-pillar">
                    <div class="pillar-number">02</div>
                    <h4 class="pillar-title">Saddle-Stitched By Master Artisans</h4>
                    <p class="pillar-desc">Constructed using dual-needle hand saddle stitches and waxed linen threads that will not unravel even if an individual stitch suffers surface abrasion.</p>
                </div>

                <div class="craftsmanship-pillar mb-0">
                    <div class="pillar-number">03</div>
                    <h4 class="pillar-title">Solid Forged Brass & Japanese Zips</h4>
                    <p class="pillar-desc">Fitted with sandcast solid brass buckles, hand-burnished rivets, and custom-forged YKK antique brass zippers engineered for millions of cycles.</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="<?= asset('assets/images/banners/hero_leather_craft.jpg') ?>" alt="Buller Leather Atelier Workbench" class="img-fluid shadow-deep rounded-1" style="border: 2px solid var(--color-gold);">
                    <div class="p-4 bg-dark border position-absolute bottom-0 start-0 m-4 shadow-lg d-none d-md-block" style="border-color: var(--color-gold) !important; max-width: 300px;">
                        <span class="text-gold font-serif fs-3 fw-bold d-block">100%</span>
                        <span class="small text-light">Uncompromised European Tannery Provenance</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. VERIFIED PATRON REVIEWS -->
<section class="py-5" style="background-color: var(--color-cream); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-uppercase small" style="color: var(--color-gold-dark); letter-spacing: 0.2em; font-weight: 600;">Worldwide Acclaim</span>
            <h2 class="font-serif mt-1" style="font-size: 2.3rem; color: var(--color-espresso-deep);">From Our Discerning Patrons</h2>
        </div>

        <div class="row g-4">
            <?php foreach ($reviews as $rev): ?>
                <div class="col-md-4">
                    <div class="p-4 bg-white shadow-soft h-100 d-flex flex-column" style="border: 1px solid var(--color-border);">
                        <div class="text-warning mb-3">
                            <?php for ($i = 0; $i < $rev['rating']; $i++): ?>
                                <i class="bi bi-star-fill"></i>
                            <?php endfor; ?>
                        </div>
                        <h5 class="font-serif mb-2" style="font-size: 1.1rem; color: var(--color-espresso-deep); font-weight: 700;">
                            "<?= e($rev['review_title']) ?>"
                        </h5>
                        <p class="small text-muted mb-4 flex-grow-1" style="line-height: 1.7;">
                            <?= e($rev['review_body']) ?>
                        </p>
                        <div class="d-flex align-items-center gap-2 pt-3 border-top small text-muted">
                            <i class="bi bi-patch-check-fill text-success"></i>
                            <strong><?= e($rev['user_name']) ?></strong>
                            <span>&bull; Verified Client (<?= e($rev['country_name']) ?>)</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
