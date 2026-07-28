{{-- PWA meta tags — módulo Financeiro --}}
<link rel="preload" as="image" href="{{ asset('site/img/swift_pay_solucoes_png.png') }}">
<link rel="manifest" href="{{ asset('site/manifest-financeiro.json') }}">
<meta name="theme-color" content="#1a6b4a">
<meta name="application-name" content="SwiftPay Financeiro">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="SwiftPay Financeiro">

<style>
    body.is-app-loading { overflow: hidden; }
    body.is-app-loading #loader { display: none !important; }

    #appSplash {
        position: fixed; inset: 0;
        z-index: 2147483646 !important;
        isolation: isolate;
        pointer-events: all;
    }
    #appSplash .app-splash__backdrop {
        position: absolute; inset: 0; z-index: 0;
        background: #0f4a33;
    }
    #appSplash .app-splash__content {
        position: absolute; inset: 0; z-index: 1;
        display: flex; align-items: center; justify-content: center; padding: 24px;
    }
    #appSplash .app-splash__logo {
        position: relative; z-index: 2; display: block;
        width: min(200px, 60vw) !important;
        max-width: min(200px, 60vw) !important;
        height: auto !important;
        animation: app-splash-pulse 1.5s ease-in-out infinite;
        filter: brightness(0) invert(1);
    }
    #appSplash.is-hidden {
        opacity: 0; visibility: hidden; pointer-events: none;
        transition: opacity 0.35s ease, visibility 0.35s ease;
    }

    @keyframes app-splash-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.5; transform: scale(0.94); }
    }

    @media (prefers-reduced-motion: reduce) {
        #appSplash .app-splash__logo { animation: none; }
    }

    @media (max-width: 991px) {
        html:has(body.has-bottom-nav),
        body.has-bottom-nav { height: 100%; overflow: hidden; }
        body.has-bottom-nav .bottom-nav {
            position: fixed; right: 0; bottom: 0; left: 0; z-index: 1100;
        }
    }
</style>

{{-- Registro do Service Worker --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then(function (reg) {
                    console.info('[SW Financeiro] Registrado:', reg.scope);
                })
                .catch(function (err) {
                    console.warn('[SW Financeiro] Falha ao registrar:', err);
                });
        });
    }
</script>
