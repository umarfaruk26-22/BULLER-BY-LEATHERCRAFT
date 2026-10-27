/**
 * BULLER BY LEATHERCRAFT — UNIVERSAL INTRO ANIMATION CONTROLLER
 * Cross-platform support for iOS Safari, WebKit, Android, Windows, macOS.
 * Handles bfcache pageshow, touch gestures, skip events, and clean DOM teardown.
 */
(function () {
    'use strict';

    // Global Intro Configuration
    window.BULLER_INTRO_CONFIG = Object.assign({
        // 'always' = Har visit / reload par play hoga
        // 'session' = Sirf pehli visit par play hoga (sessionStorage)
        playMode: 'always', 
        
        // Total duration before fade out starts (milliseconds) - 2-Stage Sequence: Logo then Name
        durationMs: 2300,
        
        // Fade out transition duration
        fadeDurationMs: 450,
        
        // Session storage key
        storageKey: 'buller_intro_viewed_v2'
    }, window.BULLER_INTRO_CONFIG || {});

    // URL parameter overrides:
    // ?intro=1 forces intro replay
    // ?skip_intro=1 disables intro
    var urlParams = new URLSearchParams(window.location.search);
    var forcePlay = urlParams.get('intro') === '1';
    var forceSkip = urlParams.get('skip_intro') === '1';

    var isInitialized = false;

    function initIntroAnimation() {
        var overlay = document.getElementById('bullerIntroOverlay');
        if (!overlay) return;

        // Force skip via URL only
        if (forceSkip) {
            overlay.classList.add('buller-intro-hidden');
            if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
            return;
        }

        // If session mode is requested and user already viewed in this session
        if (!forcePlay && window.BULLER_INTRO_CONFIG.playMode === 'session') {
            try {
                if (window.sessionStorage && sessionStorage.getItem(window.BULLER_INTRO_CONFIG.storageKey) === 'true') {
                    overlay.classList.add('buller-intro-hidden');
                    if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
                    return;
                }
            } catch (e) {
                // iOS Private Browsing may block sessionStorage, proceed gracefully
            }
        }

        if (isInitialized) return;
        isInitialized = true;

        // Reset viewport scroll to top for iOS Safari
        try { window.scrollTo(0, 0); } catch (e) {}

        // Lock background scroll safely
        document.documentElement.classList.add('buller-intro-locked');
        document.body.classList.add('buller-intro-locked');

        var isFinished = false;
        var exitTimer = null;

        function finishIntro() {
            if (isFinished) return;
            isFinished = true;

            if (exitTimer) {
                clearTimeout(exitTimer);
                exitTimer = null;
            }

            try {
                if (window.sessionStorage) {
                    sessionStorage.setItem(window.BULLER_INTRO_CONFIG.storageKey, 'true');
                }
            } catch (e) {}

            // Smooth fade out
            overlay.classList.add('buller-intro-fading');
            document.documentElement.classList.remove('buller-intro-locked');
            document.body.classList.remove('buller-intro-locked');

            setTimeout(function () {
                overlay.classList.add('buller-intro-hidden');
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
                try {
                    window.dispatchEvent(new CustomEvent('buller:introComplete'));
                } catch (e) {}
            }, window.BULLER_INTRO_CONFIG.fadeDurationMs);
        }

        // Skip Button Listeners (both Click & Touch for iOS)
        var skipBtn = document.getElementById('bullerIntroSkipBtn');
        if (skipBtn) {
            skipBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                finishIntro();
            });
            skipBtn.addEventListener('touchend', function (e) {
                e.preventDefault();
                e.stopPropagation();
                finishIntro();
            }, { passive: false });
        }

        // Keyboard 'Escape' listener
        function onKeyDown(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                document.removeEventListener('keydown', onKeyDown);
                finishIntro();
            }
        }
        document.addEventListener('keydown', onKeyDown);

        // Schedule completion
        exitTimer = setTimeout(function () {
            finishIntro();
        }, window.BULLER_INTRO_CONFIG.durationMs);
    }

    // Expose replay helper function
    window.replayBullerIntro = function () {
        try {
            if (window.sessionStorage) {
                sessionStorage.removeItem(window.BULLER_INTRO_CONFIG.storageKey);
            }
        } catch (e) {}
        var url = new URL(window.location.href);
        url.searchParams.set('intro', '1');
        window.location.href = url.toString();
    };

    // Universal DOM readiness triggers (handles iOS Safari bfcache)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initIntroAnimation);
    } else {
        initIntroAnimation();
    }

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            isInitialized = false;
            initIntroAnimation();
        }
    });
})();
