/**
 * BULLER BY LEATHERCRAFT — CINEMATIC WEBSITE INTRO CONTROLLER
 * Handles sequence timing, scroll locking, session persistence,
 * skip triggers, keyboard shortcuts, and DOM cleanup.
 */
(function () {
    'use strict';

    // Global Intro Configuration (Easily customizable)
    window.BULLER_INTRO_CONFIG = Object.assign({
        // 'always' = Har visit / reload par play hoga
        // 'session' = Sirf pehli visit par play hoga (sessionStorage)
        playMode: 'always', 
        
        // Total duration of the animation before fade out starts (milliseconds)
        durationMs: 3100,
        
        // Fade out transition duration
        fadeDurationMs: 400,
        
        // Session storage key
        storageKey: 'buller_intro_viewed_v1'
    }, window.BULLER_INTRO_CONFIG || {});

    // Check URL parameters for explicit overrides:
    // ?intro=1 forces intro replay
    // ?skip_intro=1 disables intro
    var urlParams = new URLSearchParams(window.location.search);
    var forcePlay = urlParams.get('intro') === '1';
    var forceSkip = urlParams.get('skip_intro') === '1';

    // Respect user's OS-level accessibility preference
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function initIntroAnimation() {
        var overlay = document.getElementById('bullerIntroOverlay');
        if (!overlay) return;

        // Skip immediately if motion is reduced or forced via URL
        if (forceSkip || prefersReducedMotion) {
            overlay.classList.add('buller-intro-hidden');
            overlay.remove();
            return;
        }

        // If session mode is active and user has already seen it in this browser session
        if (!forcePlay && window.BULLER_INTRO_CONFIG.playMode === 'session') {
            try {
                if (sessionStorage.getItem(window.BULLER_INTRO_CONFIG.storageKey) === 'true') {
                    overlay.classList.add('buller-intro-hidden');
                    overlay.remove();
                    return;
                }
            } catch (e) {
                // Storage access restricted (e.g. private browsing), proceed gracefully
            }
        }

        // Lock background scroll immediately
        document.body.classList.add('buller-intro-locked');

        var isFinished = false;
        var exitTimer = null;
        var cleanupTimer = null;

        function finishIntro() {
            if (isFinished) return;
            isFinished = true;

            // Clear any pending timers
            if (exitTimer) clearTimeout(exitTimer);
            if (cleanupTimer) clearTimeout(cleanupTimer);

            // Record session view if in session mode
            try {
                sessionStorage.setItem(window.BULLER_INTRO_CONFIG.storageKey, 'true');
            } catch (e) {}

            // Fade out overlay
            overlay.classList.add('buller-intro-fading');

            // Unlock page scrolling smoothly
            document.body.classList.remove('buller-intro-locked');

            // Complete DOM cleanup after fade transition finishes
            setTimeout(function () {
                overlay.classList.add('buller-intro-hidden');
                // Remove overlay from DOM to free GPU memory and ensure zero interference
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }

                // Dispatch custom event for any downstream modules
                window.dispatchEvent(new CustomEvent('buller:introComplete'));
            }, window.BULLER_INTRO_CONFIG.fadeDurationMs);
        }

        // Bind Skip Button Click
        var skipBtn = document.getElementById('bullerIntroSkipBtn');
        if (skipBtn) {
            skipBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                finishIntro();
            });
        }

        // Allow pressing 'Escape' key to skip
        function onKeyDown(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                document.removeEventListener('keydown', onKeyDown);
                finishIntro();
            }
        }
        document.addEventListener('keydown', onKeyDown);

        // Schedule normal cinematic sequence completion
        exitTimer = setTimeout(function () {
            finishIntro();
        }, window.BULLER_INTRO_CONFIG.durationMs);
    }

    // Expose developer / user helper function to replay intro anytime via console
    window.replayBullerIntro = function () {
        try {
            sessionStorage.removeItem(window.BULLER_INTRO_CONFIG.storageKey);
        } catch (e) {}
        var url = new URL(window.location.href);
        url.searchParams.set('intro', '1');
        window.location.href = url.toString();
    };

    // Initialize as soon as DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initIntroAnimation);
    } else {
        initIntroAnimation();
    }
})();
