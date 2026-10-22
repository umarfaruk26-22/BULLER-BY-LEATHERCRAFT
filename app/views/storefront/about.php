<?php
$pageTitle = 'About Us — By Leathercraft | Buller Heritage';
$pageDescription = 'Discover the Buller Leather lineage. Heirloom full-grain leather goods mastercrafted with ancestral European tanning and generational devotion.';
require __DIR__ . '/../layouts/header.php';
?>

<!-- About Hero Section -->
<section class="position-relative py-4 py-md-5 overflow-hidden" style="background: linear-gradient(135deg, var(--color-espresso-deep) 0%, var(--color-espresso) 100%); color: var(--color-ivory);">
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-25" style="background-image: radial-gradient(var(--color-gold) 1px, transparent 1px); background-size: 28px 28px;"></div>
    
    <div class="container position-relative py-3 py-md-5">
        <nav aria-label="breadcrumb" class="mb-3 mb-md-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-decoration-none text-gold-bright small"><i class="bi bi-house-door me-1"></i> Home</a></li>
                <li class="breadcrumb-item active text-white-50 small" aria-current="page">About Us</li>
            </ol>
        </nav>

        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill" style="background: rgba(197, 168, 128, 0.15); border: 1px solid rgba(197, 168, 128, 0.35);">
                    <img src="<?= asset('assets/images/buller-logo-gold.svg') ?>" alt="Buller Crest" style="height: 18px; width: auto; vertical-align: middle;">
                    <span class="text-uppercase fw-semibold" style="letter-spacing: 0.22em; font-size: 0.68rem; color: var(--color-gold-bright);">BULLER &bull; BY LEATHERCRAFT</span>
                </div>
                <h1 class="font-serif fw-bold mb-3 mb-md-4" style="letter-spacing: 0.02em; line-height: 1.18; color: #ffffff; font-size: clamp(1.85rem, 5.5vw, 3rem);">
                    Generational Mastery. <br><span style="color: var(--color-gold-bright); font-style: italic;">By Leathercraft.</span>
                </h1>
                <p class="lead text-light mb-4" style="line-height: 1.75; opacity: 0.9; max-width: 620px; font-size: clamp(0.92rem, 2vw, 1.15rem);">
                    Buller is not merely a brand of fine leather goods; it is a living commitment to ancestral craftsmanship. Every hide we shape carries the lineage of European tanneries, slow bark steeping, and obsessive hand-finishing.
                </p>
                <div class="d-flex flex-wrap gap-2 gap-sm-3">
                    <a href="<?= url('shop') ?>" class="btn btn-buller-gold px-3 px-sm-4 py-2 fw-semibold" style="font-size: 0.88rem;">
                        Explore Master Catalog <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="<?= url('shop?filter=sale') ?>" class="btn btn-outline-light px-3 px-sm-4 py-2" style="border-color: rgba(255,255,255,0.3); font-size: 0.88rem;">
                        Special Archive Sale
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0">
                <div class="position-relative d-inline-block w-100 px-2 px-sm-0" style="max-width: 440px;">
                    <img src="<?= asset('assets/images/banners/hero_leather_craft.jpg') ?>" alt="Buller Leather Workshop" class="img-fluid rounded-1 shadow-deep w-100" style="max-height: 400px; object-fit: cover; border: 2px solid var(--color-gold);">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- By Leathercraft Philosophy -->
<section class="py-4 py-md-5" style="background-color: var(--color-ivory);">
    <div class="container py-2 py-md-4">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6">
                <span class="text-uppercase small fw-bold" style="color: var(--color-cognac); letter-spacing: 0.24em; font-size: 0.72rem;">The Lineage</span>
                <h2 class="font-serif fw-bold mt-2 mb-3 mb-md-4" style="color: var(--color-espresso-deep); font-size: clamp(1.65rem, 4vw, 2.3rem);">
                    Why We Stand “By Leathercraft”
                </h2>
                <p class="text-muted" style="line-height: 1.8; font-size: clamp(0.86rem, 2vw, 0.95rem);">
                    In an industry flooded with speed-bonded leathers and polyurethane coatings masquerading as luxury, Buller remains unyielding. We believe that leather is an organic, noble canvas that deserves the patience of traditional vegetable bark tanning and skilled human hands.
                </p>
                <p class="text-muted" style="line-height: 1.8; font-size: clamp(0.86rem, 2vw, 0.95rem);">
                    The words <strong>"By Leathercraft"</strong> are our immutable pledge: every jacket, briefcase, weekend duffel, and wallet is made with zero synthetic fillers, zero split-grain shortcuts, and 100% full-grain European cowhide that grows richer, softer, and more storied with every passing decade.
                </p>
                <div class="p-3 my-3 my-md-4 rounded-1 border-start border-4" style="background-color: var(--color-cream); border-color: var(--color-gold) !important;">
                    <p class="fst-italic mb-0 small" style="color: var(--color-espresso-deep); font-family: var(--font-heading); font-size: clamp(0.92rem, 2.5vw, 1.05rem); line-height: 1.6;">
                        “True luxury does not degrade with time; it absorbs life’s journeys, deepens in color, and acquires a peerless patina that tells your story.”
                    </p>
                </div>
            </div>

            <div class="col-lg-6 mt-3 mt-lg-0">
                <div class="row g-2 g-sm-3">
                    <div class="col-6">
                        <div class="p-3 p-sm-4 bg-white border rounded-1 shadow-soft text-center h-100">
                            <i class="bi bi-tree fs-2 fs-sm-1 text-gold d-block mb-2"></i>
                            <h5 class="font-serif fw-bold mb-1" style="font-size: clamp(0.88rem, 2vw, 1.05rem);">Tuscan Tannery</h5>
                            <p class="small text-muted mb-0" style="font-size: 0.74rem;">Vegetable tanned with mimosa and chestnut extracts.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 p-sm-4 bg-white border rounded-1 shadow-soft text-center h-100">
                            <i class="bi bi-scissors fs-2 fs-sm-1 text-gold d-block mb-2"></i>
                            <h5 class="font-serif fw-bold mb-1" style="font-size: clamp(0.88rem, 2vw, 1.05rem);">Hand Saddle-Stitched</h5>
                            <p class="small text-muted mb-0" style="font-size: 0.74rem;">Two-needle waxed linen threads that never unravel.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 p-sm-4 bg-white border rounded-1 shadow-soft text-center h-100">
                            <i class="bi bi-award fs-2 fs-sm-1 text-gold d-block mb-2"></i>
                            <h5 class="font-serif fw-bold mb-1" style="font-size: clamp(0.88rem, 2vw, 1.05rem);">Full-Grain Hides</h5>
                            <p class="small text-muted mb-0" style="font-size: 0.74rem;">Top unbuffed hide preserving natural strength & grain.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 p-sm-4 bg-white border rounded-1 shadow-soft text-center h-100">
                            <i class="bi bi-shield-lock fs-2 fs-sm-1 text-gold d-block mb-2"></i>
                            <h5 class="font-serif fw-bold mb-1" style="font-size: clamp(0.88rem, 2vw, 1.05rem);">Lifetime Integrity</h5>
                            <p class="small text-muted mb-0" style="font-size: 0.74rem;">Engineered to endure as an heirloom for generations.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- The 4 Pillars of Craftsmanship -->
<section class="py-4 py-md-5" style="background-color: var(--color-cream); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
    <div class="container py-2 py-md-4">
        <div class="text-center max-w-700 mx-auto mb-4 mb-md-5">
            <span class="text-uppercase small fw-bold" style="color: var(--color-cognac); letter-spacing: 0.22em; font-size: 0.72rem;">Mastercraft Standards</span>
            <h2 class="font-serif fw-bold mt-2 mb-2" style="color: var(--color-espresso-deep); font-size: clamp(1.6rem, 4vw, 2.2rem);">The Four Pillars of Buller Artistry</h2>
            <p class="text-muted small mb-0">From hide selection to final edge-burnishing, every step conforms to uncompromising heritage standards.</p>
        </div>

        <div class="row g-3 g-md-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-soft p-3 p-md-4" style="background-color: #ffffff; border-radius: 8px;">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded-circle" style="width: 48px; height: 48px; background: rgba(140, 74, 47, 0.1); color: var(--color-cognac);">
                        <i class="bi bi-gem fs-5"></i>
                    </div>
                    <h5 class="font-serif fw-bold mb-2" style="color: var(--color-espresso-deep); font-size: 1.05rem;">1. Prime Hide Selection</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.7; font-size: 0.82rem;">
                        We accept only the top 5% of European full-grain hides, free from deep scarring and chemical pre-treatments, ensuring maximum resilience.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-soft p-3 p-md-4" style="background-color: #ffffff; border-radius: 8px;">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded-circle" style="width: 48px; height: 48px; background: rgba(197, 168, 128, 0.2); color: var(--color-gold-dark);">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                    <h5 class="font-serif fw-bold mb-2" style="color: var(--color-espresso-deep); font-size: 1.05rem;">2. 60-Day Bark Steeping</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.7; font-size: 0.82rem;">
                        Submerged in organic vegetable tanning pits for two full months. The natural plant tannins infuse rich scent, water resistance, and structural strength.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-soft p-3 p-md-4" style="background-color: #ffffff; border-radius: 8px;">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded-circle" style="width: 48px; height: 48px; background: rgba(140, 74, 47, 0.1); color: var(--color-cognac);">
                        <i class="bi bi-tools fs-5"></i>
                    </div>
                    <h5 class="font-serif fw-bold mb-2" style="color: var(--color-espresso-deep); font-size: 1.05rem;">3. Artisanal Beveling</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.7; font-size: 0.82rem;">
                        Leather edges are hand-beveled, heat-creased, and buffed with natural beeswax to produce smooth, sealed, weather-resistant perimeters.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-soft p-3 p-md-4" style="background-color: #ffffff; border-radius: 8px;">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded-circle" style="width: 48px; height: 48px; background: rgba(197, 168, 128, 0.2); color: var(--color-gold-dark);">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>
                    <h5 class="font-serif fw-bold mb-2" style="color: var(--color-espresso-deep); font-size: 1.05rem;">4. Solid Brass Hardware</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.7; font-size: 0.82rem;">
                        Fitted with forged solid antique brass buckles, Swiss engineered zippers, and double-capped rivets tested to endure extreme loads.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Global Showroom Hubs -->
<section class="py-4 py-md-5" style="background-color: var(--color-ivory);">
    <div class="container py-2 py-md-4">
        <div class="text-center max-w-700 mx-auto mb-4 mb-md-5">
            <span class="text-uppercase small fw-bold" style="color: var(--color-cognac); letter-spacing: 0.22em; font-size: 0.72rem;">International Presence</span>
            <h2 class="font-serif fw-bold mt-2 mb-2" style="color: var(--color-espresso-deep); font-size: clamp(1.6rem, 4vw, 2.2rem);">Our Global Showroom Hubs</h2>
            <p class="text-muted small mb-0">Serving customers across London, New York, Mumbai, and Dubai with express delivery and dedicated customer support.</p>
        </div>

        <div class="row g-2 g-sm-3 text-center">
            <div class="col-6 col-md-3">
                <div class="p-3 p-sm-4 bg-white border rounded-1 shadow-soft h-100">
                    <img src="<?= asset('assets/images/flags/gb.png') ?>" alt="UK" width="32" class="mb-2 rounded shadow-sm">
                    <h6 class="font-serif fw-bold mb-1" style="font-size: clamp(0.85rem, 2vw, 1rem);">London Mayfair</h6>
                    <p class="small text-muted mb-0" style="font-size: 0.72rem;">Heritage Showroom & Bespoke Desk</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 p-sm-4 bg-white border rounded-1 shadow-soft h-100">
                    <img src="<?= asset('assets/images/flags/us.png') ?>" alt="USA" width="32" class="mb-2 rounded shadow-sm">
                    <h6 class="font-serif fw-bold mb-1" style="font-size: clamp(0.85rem, 2vw, 1rem);">New York SoHo</h6>
                    <p class="small text-muted mb-0" style="font-size: 0.72rem;">Private Showroom & Client Fittings</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 p-sm-4 bg-white border rounded-1 shadow-soft h-100">
                    <img src="<?= asset('assets/images/flags/in.png') ?>" alt="India" width="32" class="mb-2 rounded shadow-sm">
                    <h6 class="font-serif fw-bold mb-1" style="font-size: clamp(0.85rem, 2vw, 1rem);">Mumbai Colaba</h6>
                    <p class="small text-muted mb-0" style="font-size: 0.72rem;">Flagship Showroom & Workshop</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 p-sm-4 bg-white border rounded-1 shadow-soft h-100">
                    <img src="<?= asset('assets/images/flags/ae.png') ?>" alt="UAE" width="32" class="mb-2 rounded shadow-sm">
                    <h6 class="font-serif fw-bold mb-1" style="font-size: clamp(0.85rem, 2vw, 1rem);">Dubai DIFC</h6>
                    <p class="small text-muted mb-0" style="font-size: 0.72rem;">Customer Support & Gulf Logistics</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-4 py-md-5 text-center text-white" style="background: linear-gradient(135deg, var(--color-espresso) 0%, var(--color-espresso-deep) 100%);">
    <div class="container py-3 py-md-4">
        <h2 class="font-serif fw-bold mb-3" style="color: var(--color-gold-bright); font-size: clamp(1.6rem, 4vw, 2.2rem);">Experience Handcrafted Leathercraft</h2>
        <p class="text-light-50 max-w-600 mx-auto mb-4" style="line-height: 1.7; font-size: clamp(0.88rem, 2vw, 0.98rem); opacity: 0.85;">
            Explore our collections or reach out to our customer support team for custom orders and sizing guidance.
        </p>
        <div class="d-flex justify-content-center gap-2 gap-sm-3 flex-wrap">
            <a href="<?= url('shop') ?>" class="btn btn-buller-gold px-3 px-sm-4 py-2 fw-semibold" style="font-size: 0.88rem;">
                Shop Collections <i class="bi bi-arrow-right ms-1"></i>
            </a>
            <a href="<?= url('contact') ?>" class="btn btn-outline-light px-3 px-sm-4 py-2" style="font-size: 0.88rem;">
                Contact Support
            </a>
        </div>
    </div>
</section>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
