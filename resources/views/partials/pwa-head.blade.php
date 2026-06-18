{{-- PWA meta tags — incluir no <head> de todos os layouts --}}
<link rel="preload" as="image" href="{{ asset('site/img/swift_pay_solucoes_png.png') }}">
<link rel="manifest" href="{{ asset('site/manifest.json') }}">
<meta name="theme-color" content="#1E2E5E">
<meta name="application-name" content="SwiftPay">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="SwiftPay">

{{-- Splash screen — CSS crítico no head para ficar acima de tudo --}}
<style>
    body.is-app-loading {
        overflow: hidden;
    }

    body.is-app-loading #loader {
        display: none !important;
    }

    #appSplash {
        position: fixed;
        inset: 0;
        z-index: 2147483646 !important;
        isolation: isolate;
        pointer-events: all;
    }

    #appSplash .app-splash__backdrop {
        position: absolute;
        inset: 0;
        z-index: 0;
        background: #ffffff;
    }

    #appSplash .app-splash__content {
        position: absolute;
        inset: 0;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    #appSplash .app-splash__logo {
        position: relative;
        z-index: 2;
        display: block;
        width: min(220px, 65vw) !important;
        max-width: min(220px, 65vw) !important;
        height: auto !important;
        animation: app-splash-pulse 1.5s ease-in-out infinite;
    }

    #appSplash.is-hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.35s ease, visibility 0.35s ease;
    }

    @keyframes app-splash-pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.5;
            transform: scale(0.94);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #appSplash .app-splash__logo {
            animation: none;
        }
    }

    @media (max-width: 991px) {
        .bottom-nav {
            position: fixed !important;
            top: auto !important;
            right: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            width: 100% !important;
            max-width: 100vw !important;
            z-index: 1100 !important;
            transform: translate3d(0, 0, 0);
            -webkit-transform: translate3d(0, 0, 0);
        }
    }
</style>
