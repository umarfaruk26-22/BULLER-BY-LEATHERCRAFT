<?php
use App\Core\MarketContext;
use App\Core\Auth;
use App\Core\Helpers;

$activeCountry = MarketContext::getCountry();
$allCountries = MarketContext::getAllActiveCountries();
$isLocked = Auth::isCountryLocked();
?>

<!-- Market & Currency Switcher Modal -->
<div class="modal fade" id="marketSelectorModal" tabindex="-1" aria-labelledby="marketSelectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background-color: var(--color-ivory);">
            <div class="modal-header border-bottom" style="border-color: var(--color-border) !important;">
                <div>
                    <h5 class="modal-title font-serif" id="marketSelectorModalLabel" style="color: var(--color-espresso-deep); font-weight: 700;">
                        <?= $isLocked ? 'Your Registered Regional Market' : 'Select Your Shopping Region' ?>
                    </h5>
                    <p class="text-muted small mb-0">
                        <?= $isLocked ? 'Your portfolio and localized pricing are permanently bound to your account.' : 'Delivery, pricing, and currency will automatically update to your selected market.' ?>
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <?php if ($isLocked): ?>
                    <div class="alert alert-warning border-0 p-3 mb-4 rounded-2 d-flex align-items-start gap-3" style="background-color: #fefce8; color: #854d0e; border-left: 4px solid var(--color-gold) !important;">
                        <i class="bi bi-lock-fill fs-4 text-warning mt-1"></i>
                        <div>
                            <strong class="d-block mb-1">Account Region Fixed</strong>
                            <p class="small mb-0" style="line-height: 1.6;">
                                You are signed in as <strong><?= e(Auth::user()['name']) ?></strong> with your account registered in <strong><?= e($activeCountry['name']) ?> (<?= e($activeCountry['currency_code']) ?>)</strong>. Regional market switching is fixed to your profile to ensure consistent local currency (<?= e($activeCountry['currency_symbol']) ?>), domestic taxes, and express delivery.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-column gap-3">
                    <?php foreach ($allCountries as $c): 
                        $isActive = ($c['code'] === $activeCountry['code']);
                    ?>
                        <div class="market-card-option <?= $isActive ? 'active' : '' ?> <?= ($isLocked && !$isActive) ? 'opacity-50 pe-none' : '' ?>" data-country-code="<?= e($c['code']) ?>">
                            <div class="d-flex align-items-center gap-3">
                                <?= country_flag($c['code'], $c['name'], 36) ?>
                                <div>
                                    <div class="fw-bold" style="color: var(--color-espresso-deep); font-size: 1rem;">
                                        <?= e($c['name']) ?>
                                    </div>
                                    <div class="text-muted small">
                                        Currency: <strong><?= e($c['currency_code']) ?> (<?= e($c['currency_symbol']) ?>)</strong> &bull; <?= e($c['tax_name']) ?>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <?php if ($isActive): ?>
                                    <span class="badge" style="background-color: var(--color-espresso-deep); color: var(--color-gold);">
                                        <i class="bi bi-shield-lock-fill me-1"></i> Active Market
                                    </span>
                                <?php elseif ($isLocked): ?>
                                    <span class="badge bg-light text-muted border small"><i class="bi bi-lock me-1"></i> Locked</span>
                                <?php else: ?>
                                    <span class="text-muted small">Switch <i class="bi bi-arrow-right"></i></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer border-top text-center justify-content-center" style="border-color: var(--color-border) !important;">
                <span class="text-muted small">
                    <?= $isLocked ? 'Contact customer support if you are relocating or require address changes.' : 'All international duties & localized taxes calculated at checkout.' ?>
                </span>
            </div>
        </div>
    </div>
</div>
