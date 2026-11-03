<?php
/**
 * BULLER BY LEATHERCRAFT — CINEMATIC INTRO OVERLAY COMPONENT
 * Renders full-screen fixed luxury intro stages using the brand's original vector assets.
 */
$favDiskPath = dirname(__DIR__, 3) . '/assets/images/buller-logo.svg';
$introFavVer = file_exists($favDiskPath) ? filemtime($favDiskPath) : '2.0.0';
?>

<!-- Preload original vector logo asset for zero-latency instant rendering -->
<link rel="preload" href="<?= asset('assets/images/buller-logo.svg') ?>?v=<?= $introFavVer ?>" as="image" type="image/svg+xml">

<!-- Custom Cinematic Intro Stylesheet -->
<link rel="stylesheet" href="<?= asset('assets/css/intro-animation.css') ?>?v=<?= file_exists(dirname(__DIR__, 3) . '/assets/css/intro-animation.css') ? filemtime(dirname(__DIR__, 3) . '/assets/css/intro-animation.css') : time() ?>">

<!-- Early Internal Navigation & Reload Detection to prevent flicker -->
<script>
(function() {
    try {
        var isReload = false;
        if (window.performance) {
            var nav = performance.getEntriesByType ? performance.getEntriesByType('navigation') : null;
            if (nav && nav.length > 0) {
                isReload = (nav[0].type === 'reload');
            } else if (performance.navigation) {
                isReload = (performance.navigation.type === 1);
            }
        }
        var isInternal = false;
        if (window.sessionStorage && sessionStorage.getItem('buller_internal_nav') === 'true') {
            isInternal = true;
            sessionStorage.removeItem('buller_internal_nav');
        }
        if (!isInternal && document.referrer) {
            try {
                var refUrl = new URL(document.referrer);
                if (refUrl.hostname === window.location.hostname) {
                    isInternal = true;
                }
            } catch (e) {
                if (document.referrer.indexOf(window.location.hostname) !== -1) {
                    isInternal = true;
                }
            }
        }
        var urlParams = new URLSearchParams(window.location.search);
        var forcePlay = urlParams.get('intro') === '1';

        // If internal click to Home (and NOT a browser reload), skip intro instantly with zero flash
        if (isInternal && !isReload && !forcePlay) {
            document.documentElement.classList.add('buller-skip-intro');
        }
    } catch (e) {}
})();
</script>

<!-- MASTER INTRO OVERLAY -->
<div id="bullerIntroOverlay" role="dialog" aria-modal="true" aria-label="Buller By Leathercraft Introduction">
    <!-- Top Luxury Animated Progress Indicator -->
    <div class="buller-intro-progress-line" aria-hidden="true"></div>

    <!-- Ambient luxury vignette & subtle grain -->
    <div class="buller-intro-vignette" aria-hidden="true"></div>

    <!-- Elegant Skip Button for returning or rapid-browse visitors -->
    <button type="button" class="buller-intro-skip-btn" id="bullerIntroSkipBtn" title="Skip Intro Animation (or press Esc)" aria-label="Skip Introduction">
        <span>Skip</span>
        <i class="bi bi-x-lg" aria-hidden="true"></i>
    </button>

    <!-- Master Stage Sequence Container -->
    <div class="buller-intro-stage-container">
        
        <!-- STAGE 1: Standalone 3D Bull Head Icon (Phases 2 & 3: Reveal, Hover, Depth Exit) -->
        <div class="buller-intro-stage-bull" id="bullerIntroStageBull" aria-hidden="true">
            <img src="<?= asset('assets/images/buller-logo.svg') ?>?v=<?= $introFavVer ?>" alt="Buller Heritage Crest" class="buller-intro-bull-icon" width="1976" height="1853">
        </div>

        <!-- STAGE 2: Harmonious Typography Reveal (Baad me Name) -->
        <div class="buller-intro-stage-text" id="bullerIntroStageText" aria-label="BULLER BY LEATHERCRAFT">
            <div class="buller-intro-wordmark">BULLER</div>
            <div class="buller-intro-tagline-wrapper">
                <span class="buller-intro-line line-left"></span>
                <span class="buller-intro-tagline">BY LEATHERCRAFT</span>
                <span class="buller-intro-line line-right"></span>
            </div>
        </div>

    </div>
</div>

<!-- Intro Controller Logic -->
<script src="<?= asset('assets/js/intro-animation.js') ?>?v=<?= file_exists(dirname(__DIR__, 3) . '/assets/js/intro-animation.js') ? filemtime(dirname(__DIR__, 3) . '/assets/js/intro-animation.js') : time() ?>"></script>
