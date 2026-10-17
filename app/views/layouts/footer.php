<?php
use App\Core\Auth;
use App\Core\MarketContext;
$activeCountry = MarketContext::getCountry();
?>
    <!-- Global Luxury Footer -->
    <footer class="buller-footer">
        <div class="container">
            <div class="row g-4 mb-5">
                <!-- Brand Heritage -->
                <div class="col-lg-4 col-md-6">
                    <a href="<?= url('') ?>" class="navbar-brand-logo mb-3">
                        <span class="brand-main" style="color: var(--color-gold-bright);">BULLER</span>
                        <span class="brand-sub" style="color: var(--color-gold);">By leathercraft</span>
                    </a>
                    <p class="mb-4">
                        Forged with unyielding dedication to ancestral leathercraft. We source exclusively prime full-grain hides, vegetable-tanned over months in European tanneries and constructed to outlast generations.
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <a href="#" class="footer-social-icon" title="Instagram" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="footer-social-icon" title="Facebook" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="footer-social-icon" title="Pinterest" aria-label="Pinterest"><i class="bi bi-pinterest"></i></a>
                        <a href="#" class="footer-social-icon" title="YouTube" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <!-- Curations & Categories -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-heading">Collections</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= url('shop?category=leather-jackets') ?>" class="footer-link">Biker & Flight Jackets</a></li>
                        <li><a href="<?= url('shop?category=bags-briefcases') ?>" class="footer-link">Executive Briefcases</a></li>
                        <li><a href="<?= url('shop?category=bags-briefcases') ?>" class="footer-link">Weekend Duffels</a></li>
                        <li><a href="<?= url('shop?category=wallets-accessories') ?>" class="footer-link">Slim Bifold Wallets</a></li>
                        <li><a href="<?= url('shop?category=handcrafted-belts') ?>" class="footer-link">English Bridle Belts</a></li>
                        <li><a href="<?= url('shop?category=footwear-boots') ?>" class="footer-link">Goodyear Welted Boots</a></li>
                    </ul>
                </div>

                <!-- Client Concierge -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-heading">Client Care</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= url('about') ?>" class="footer-link">About Us</a></li>
                        <li><a href="<?= url('track') ?>" class="footer-link">Track Delivery</a></li>
                        <li><a href="<?= url('customer/orders') ?>" class="footer-link">Order Invoices</a></li>
                        <li><a href="<?= url('shipping-policy') ?>" class="footer-link">International Shipping</a></li>
                        <li><a href="<?= url('returns-policy') ?>" class="footer-link">Returns & Exchanges</a></li>
                        <li><a href="<?= url('leather-care') ?>" class="footer-link">Leather Care Guide</a></li>
                        <li><a href="<?= url('contact') ?>" class="footer-link">Atelier Concierge</a></li>
                    </ul>
                </div>

                <!-- Global Market & Newsletter -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="footer-heading">Atelier Privé Gazette</h6>
                    <p class="mb-3">
                        Receive private previews of limited-batch artisan releases and leathercraft journal insights.
                    </p>
                    <form onsubmit="event.preventDefault(); showToast('Subscribed', 'Thank you for joining the Buller Leather Gazette.');" class="mb-4">
                        <div class="input-group">
                            <input type="email" class="form-control newsletter-input" placeholder="Your executive email address" required>
                            <button class="btn btn-buller-gold px-3" type="submit" style="font-size: 0.78rem;">Join</button>
                        </div>
                    </form>

                    <div class="footer-market-box">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="market-label">Currently Shopping In:</span>
                                <strong class="text-white d-inline-flex align-items-center gap-2" style="font-size: 0.9rem;">
                                    <?= country_flag($activeCountry['code'], $activeCountry['name'], 20) ?> <?= e($activeCountry['name']) ?> (<?= e($activeCountry['currency_code']) ?>)
                                </strong>
                            </div>
                            <?php if (\App\Core\Auth::isCountryLocked()): ?>
                                <span class="badge border border-warning" style="background: rgba(197, 168, 128, 0.15); color: var(--color-gold-bright); font-size: 0.72rem; padding: 0.4rem 0.75rem;">
                                    <i class="bi bi-shield-lock-fill me-1 text-warning"></i> Fixed to Account
                                </span>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-warning text-gold" style="border-color: var(--color-gold); font-size: 0.74rem; padding: 0.35rem 0.8rem;" data-bs-toggle="modal" data-bs-target="#marketSelectorModal">
                                    Change
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Legal & Payment Badges -->
            <div class="pt-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3" style="border-color: rgba(197, 168, 128, 0.2) !important;">
                <div class="footer-bottom-legal">
                    &copy; <?= date('Y') ?> Buller Leather & Co. International. All Rights Reserved. Mastercrafted with Heritage Integrity.
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="small me-2" style="color: #c9bfb2;"><i class="bi bi-shield-lock-fill text-warning me-1"></i> 256-Bit SSL Encrypted International Checkout</span>
                    <span class="footer-payment-badge"><i class="bi bi-credit-card me-1 text-warning"></i> Razorpay</span>
                    <span class="footer-payment-badge">Visa</span>
                    <span class="footer-payment-badge">Mastercard</span>
                    <span class="footer-payment-badge">Amex</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Buller Leather Main Client Logic -->
    <?php
    $jsDiskPath = dirname(__DIR__, 3) . '/assets/js/main.js';
    $jsVersion = file_exists($jsDiskPath) ? filemtime($jsDiskPath) : time();
    ?>
    <script src="<?= asset('assets/js/main.js') ?>?v=<?= $jsVersion ?>"></script>
</body>
</html>
