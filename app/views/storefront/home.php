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

<!-- 2. ATELIER BESTSELLERS (Consolidated Single Section, 2-col on Mobile) -->
<section class="py-4 py-md-5" id="bestsellers">
    <div class="container py-2 py-md-4">
        <div class="text-center max-w-700 mx-auto mb-4 mb-md-5">
            <span class="text-uppercase small" style="color: var(--color-cognac); letter-spacing: 0.2em; font-weight: 600;">Client Favorites &bull; Hand-Selected For <?= e($activeCountry['name']) ?></span>
            <h2 class="font-serif mt-1" style="font-size: clamp(1.75rem, 4vw, 2.5rem); color: var(--color-espresso-deep);">Atelier Bestsellers</h2>
            <p class="text-muted small">Our most revered full-grain creations, chosen by discerning clients worldwide and mastercrafted to endure generations.</p>
        </div>

        <div class="row g-2 g-sm-3 g-md-4">
            <?php foreach ($bestsellerProducts as $p): 
                $pricing = MarketContext::evaluatePricing(
                    (float)$p['original_price'],
                    $p['discount_price'] ? (float)$p['discount_price'] : null,
                    $p['discount_start_date'],
                    $p['discount_end_date']
                );
                $isWishlisted = Wishlist::has((int)$p['id']);
            ?>
                <div class="col-6 col-md-6 col-lg-3 mb-2 mb-md-3">
                    <div class="product-card h-100">
                        <div class="product-image-wrapper">
                            <img src="<?= asset($p['primary_image']) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                            
                            <div class="product-badges">
                                <?php if ($pricing['is_discounted']): ?>
                                    <span class="badge-luxury badge-sale">-<?= $pricing['discount_percent'] ?>%</span>
                                <?php endif; ?>
                                <span class="badge-luxury badge-bestseller d-none d-sm-inline-block">Bestseller</span>
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

        <div class="text-center mt-4 mt-md-5">
            <a href="<?= url('shop') ?>" class="btn btn-buller-primary btn-lg">
                Explore All Master Curations <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- 4. ART OF BULLER CRAFTSMANSHIP (Story & Heritage — Mobile Optimized) -->
<section class="craftsmanship-section py-4 py-md-5">
    <div class="container py-2 py-md-3">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6">
                <div class="d-inline-flex align-items-center gap-2 mb-2">
                    <img src="<?= asset('assets/images/buller-logo-gold.svg') ?>" alt="Buller Crest" style="height: 20px; width: auto;">
                    <span class="text-uppercase small text-gold" style="letter-spacing: 0.25em;">The Buller Philosophy</span>
                </div>
                <h2 class="font-serif mt-2 mb-3 mb-md-4" style="font-size: clamp(1.75rem, 4vw, 2.6rem); color: #fff; line-height: 1.2;">
                    Rejecting Obsolescence. <br>
                    <span style="color: var(--color-gold);">Embracing Eternity.</span>
                </h2>
                <p class="text-light mb-4" style="line-height: 1.75; opacity: 0.88; font-size: clamp(0.88rem, 2vw, 0.98rem);">
                    In an era saturated by disposable synthetics and bonded leathers, Buller Leather stands as a resolute guardian of traditional craftsmanship. We reject shortcuts, speed tanning, and chrome-based chemicals.
                </p>

                <div class="craftsmanship-pillar">
                    <div class="pillar-number">01</div>
                    <div class="pillar-content">
                        <h4 class="pillar-title">Full-Grain Vegetable Tanned Hides</h4>
                        <p class="pillar-desc">Only the unbuffed top layer of the hide is chosen, steeped in natural tree barks and chestnut extracts over 60 days to retain natural grain signatures.</p>
                    </div>
                </div>

                <div class="craftsmanship-pillar">
                    <div class="pillar-number">02</div>
                    <div class="pillar-content">
                        <h4 class="pillar-title">Saddle-Stitched By Master Artisans</h4>
                        <p class="pillar-desc">Constructed using dual-needle hand saddle stitches and waxed linen threads that will not unravel even if an individual stitch suffers surface abrasion.</p>
                    </div>
                </div>

                <div class="craftsmanship-pillar mb-0">
                    <div class="pillar-number">03</div>
                    <div class="pillar-content">
                        <h4 class="pillar-title">Solid Forged Brass & Japanese Zips</h4>
                        <p class="pillar-desc">Fitted with sandcast solid brass buckles, hand-burnished rivets, and custom-forged YKK antique brass zippers engineered for millions of cycles.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="position-relative mt-3 mt-lg-0 text-center">
                    <img src="<?= asset('assets/images/banners/hero_leather_craft.jpg') ?>" alt="Buller Leather Atelier Workbench" class="img-fluid shadow-deep rounded-1 w-100" style="border: 2px solid var(--color-gold); max-height: 480px; object-fit: cover;">
                    <div class="p-3 p-sm-4 bg-dark border position-absolute bottom-0 start-0 m-2 m-sm-4 shadow-lg text-start" style="border-color: var(--color-gold) !important; max-width: 280px; background: rgba(18, 11, 6, 0.92) !important; backdrop-filter: blur(8px);">
                        <span class="text-gold font-serif fs-3 fw-bold d-block" style="line-height: 1;">100%</span>
                        <span class="small text-light" style="font-size: 0.78rem;">Uncompromised European Tannery Provenance</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. VERIFIED PATRON REVIEWS (Horizontal Slide Carousel with 5s Delay) -->
<section class="py-4 py-md-5 patron-reviews-section" style="background-color: var(--color-cream); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
    <div class="container py-2 py-md-4">
        <div class="text-center max-w-700 mx-auto mb-4 mb-md-5">
            <span class="text-uppercase small" style="color: var(--color-gold-dark); letter-spacing: 0.2em; font-weight: 600;">Worldwide Acclaim</span>
            <h2 class="font-serif mt-1" style="font-size: clamp(1.65rem, 4vw, 2.3rem); color: var(--color-espresso-deep);">From Our Discerning Patrons</h2>
            <p class="text-muted small mb-0">Direct impressions from patrons across our international ateliers.</p>
        </div>

        <div id="patronReviewsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="hover">
            <div class="carousel-inner">
                <?php foreach ($reviews as $idx => $rev): ?>
                    <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 col-md-10 col-12">
                                <div class="p-4 p-md-5 bg-white shadow-soft rounded-2 text-center position-relative mx-auto" style="border: 1px solid var(--color-border); max-width: 720px;">
                                    <div class="text-warning mb-3">
                                        <?php for ($i = 0; $i < $rev['rating']; $i++): ?>
                                            <i class="bi bi-star-fill me-1"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <h4 class="font-serif mb-2 mb-md-3" style="font-size: clamp(1.15rem, 2.5vw, 1.35rem); color: var(--color-espresso-deep); font-weight: 700;">
                                        "<?= e($rev['review_title']) ?>"
                                    </h4>
                                    <p class="text-muted mb-4" style="line-height: 1.8; font-size: clamp(0.86rem, 2vw, 0.98rem);">
                                        <?= e($rev['review_body']) ?>
                                    </p>
                                    <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 pt-3 border-top small text-muted" style="border-color: var(--color-border) !important;">
                                        <i class="bi bi-patch-check-fill text-success"></i>
                                        <strong style="color: var(--color-espresso-deep);"><?= e($rev['user_name']) ?></strong>
                                        <span>&bull; Verified Client (<?= e($rev['country_name']) ?>)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Carousel Nav Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#patronReviewsCarousel" data-bs-slide="prev" aria-label="Previous Review">
                <span class="carousel-nav-btn"><i class="bi bi-chevron-left"></i></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#patronReviewsCarousel" data-bs-slide="next" aria-label="Next Review">
                <span class="carousel-nav-btn"><i class="bi bi-chevron-right"></i></span>
            </button>

            <!-- Indicators -->
            <div class="carousel-indicators position-relative mt-4 mb-0">
                <?php foreach ($reviews as $idx => $rev): ?>
                    <button type="button" data-bs-target="#patronReviewsCarousel" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>" aria-current="<?= $idx === 0 ? 'true' : 'false' ?>" aria-label="Review <?= $idx + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
