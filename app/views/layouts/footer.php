<?php
use App\Core\Auth;
use App\Core\MarketContext;
$activeCountry = MarketContext::getCountry();
?>
    <!-- Global Luxury Footer -->
    <footer class="buller-footer py-5">
        <div class="container">
            <div class="row g-4 mb-4 mb-md-5">
                <!-- Brand Heritage -->
                <div class="col-12 col-lg-4 col-md-6">
                    <a href="<?= url('') ?>" class="navbar-brand-logo mb-3" title="Buller Leather - Return to Storefront">
                        <img src="<?= asset('assets/images/buller-logo-gold.svg') ?>" alt="Buller Leather Logo" class="brand-logo-icon">
                        <div class="brand-text-group">
                            <span class="brand-main" style="color: var(--color-gold-bright);">BULLER</span>
                            <span class="brand-sub" style="color: var(--color-gold);">By leathercraft</span>
                        </div>
                    </a>
                    <p class="mb-4 text-light-50" style="font-size: 0.88rem; line-height: 1.7; opacity: 0.85;">
                        Forged with unyielding dedication to ancestral leathercraft. We source exclusively prime full-grain hides, vegetable-tanned over months in European tanneries and constructed to outlast generations.
                    </p>
                    <div class="d-flex align-items-center gap-2 mb-3 mb-md-0">
                        <a href="#" class="footer-social-icon" title="Instagram" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="footer-social-icon" title="Facebook" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="footer-social-icon" title="Pinterest" aria-label="Pinterest"><i class="bi bi-pinterest"></i></a>
                        <a href="#" class="footer-social-icon" title="YouTube" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <!-- Curations & Categories (2-col side by side on mobile) -->
                <div class="col-6 col-md-6 col-lg-2">
                    <h6 class="footer-heading">Collections</h6>
                    <ul class="list-unstyled mb-0 footer-links-list">
                        <li><a href="<?= url('shop?category=leather-jackets') ?>" class="footer-link">Biker & Jackets</a></li>
                        <li><a href="<?= url('shop?category=bags-briefcases') ?>" class="footer-link">Briefcases</a></li>
                        <li><a href="<?= url('shop?category=bags-briefcases') ?>" class="footer-link">Weekend Duffels</a></li>
                        <li><a href="<?= url('shop?category=wallets-accessories') ?>" class="footer-link">Slim Wallets</a></li>
                        <li><a href="<?= url('shop?category=handcrafted-belts') ?>" class="footer-link">Bridle Belts</a></li>
                        <li><a href="<?= url('shop?category=footwear-boots') ?>" class="footer-link">Welted Boots</a></li>
                    </ul>
                </div>

                <!-- Client Concierge (2-col side by side on mobile) -->
                <div class="col-6 col-md-6 col-lg-2">
                    <h6 class="footer-heading">Client Care</h6>
                    <ul class="list-unstyled mb-0 footer-links-list">
                        <li><a href="<?= url('about') ?>" class="footer-link">About Us</a></li>
                        <li><a href="<?= url('track') ?>" class="footer-link">Track Delivery</a></li>
                        <li><a href="<?= url('customer/orders') ?>" class="footer-link">Order Invoices</a></li>
                        <li><a href="<?= url('shipping-policy') ?>" class="footer-link">Shipping Policy</a></li>
                        <li><a href="<?= url('returns-policy') ?>" class="footer-link">Returns Policy</a></li>
                        <li><a href="<?= url('contact') ?>" class="footer-link">Atelier Desk</a></li>
                    </ul>
                </div>

                <!-- Global Market & Newsletter -->
                <div class="col-12 col-lg-4 col-md-6">
                    <h6 class="footer-heading">Atelier Privé Gazette</h6>
                    <p class="mb-3 text-light-50" style="font-size: 0.85rem; line-height: 1.6; opacity: 0.85;">
                        Receive private previews of limited-batch artisan releases and leathercraft journal insights.
                    </p>
                    <form onsubmit="event.preventDefault(); showToast('Subscribed', 'Thank you for joining the Buller Leather Gazette.');" class="mb-4">
                        <div class="input-group">
                            <input type="email" class="form-control newsletter-input" placeholder="Your executive email address" required style="font-size: 0.85rem;">
                            <button class="btn btn-buller-gold px-3" type="submit" style="font-size: 0.78rem;">Join</button>
                        </div>
                    </form>

                    <div class="footer-market-box p-3 rounded" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(197, 168, 128, 0.25);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="market-label" style="font-size: 0.68rem; color: #b5aba0; letter-spacing: 0.1em; text-transform: uppercase;">Regional Market:</span>
                                <strong class="text-white d-inline-flex align-items-center gap-2 mt-1" style="font-size: 0.86rem;">
                                    <?= country_flag($activeCountry['code'], $activeCountry['name'], 20) ?> <?= e($activeCountry['name']) ?> (<?= e($activeCountry['currency_code']) ?>)
                                </strong>
                            </div>
                            <span class="badge border border-warning" style="background: rgba(197, 168, 128, 0.15); color: var(--color-gold-bright); font-size: 0.68rem; padding: 0.35rem 0.65rem;">
                                <i class="bi bi-shield-lock-fill me-1 text-warning"></i> <?= \App\Core\Auth::isLoggedIn() ? 'Fixed' : 'Active' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Legal & Payment Badges -->
            <div class="pt-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 text-center text-md-start" style="border-color: rgba(197, 168, 128, 0.2) !important;">
                <div class="footer-bottom-legal" style="font-size: 0.76rem; color: #b0a69a;">
                    &copy; <?= date('Y') ?> Buller Leather & Co. International. All Rights Reserved. Mastercrafted with Heritage Integrity.
                </div>
                <div class="d-flex flex-wrap justify-content-center justify-content-md-end align-items-center gap-2">
                    <span class="small me-1 text-light-50" style="font-size: 0.72rem; color: #c9bfb2;"><i class="bi bi-shield-lock-fill text-warning me-1"></i> 256-Bit SSL Checkout</span>
                    <span class="footer-payment-badge">Razorpay</span>
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
