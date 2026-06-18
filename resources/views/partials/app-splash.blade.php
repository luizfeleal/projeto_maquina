{{-- Splash de carregamento — CSS inline para exibir antes dos assets --}}
<style>
    body.is-app-loading {
        overflow: hidden;
    }

    .app-splash {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        transition: opacity 0.35s ease, visibility 0.35s ease;
    }

    .app-splash.is-hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .app-splash__logo {
        width: min(200px, 58vw);
        height: auto;
        animation: app-splash-pulse 1.5s ease-in-out infinite;
    }

    @keyframes app-splash-pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.45;
            transform: scale(0.94);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .app-splash__logo {
            animation: none;
        }
    }
</style>

<div id="appSplash" class="app-splash" role="status" aria-live="polite" aria-label="Carregando SwiftPay">
    <img
        src="{{ asset('site/img/swift_pay_solucoes_png.png') }}"
        alt="SwiftPay Soluções"
        class="app-splash__logo"
        width="200"
        height="80"
        decoding="async"
    >
</div>

<script>
    (function () {
        var splash = document.getElementById('appSplash');
        if (!splash) {
            return;
        }

        document.body.classList.add('is-app-loading');

        function hideSplash() {
            if (splash.classList.contains('is-hidden')) {
                return;
            }

            splash.classList.add('is-hidden');
            document.body.classList.remove('is-app-loading');

            setTimeout(function () {
                splash.remove();
            }, 400);
        }

        window.addEventListener('load', hideSplash);
        setTimeout(hideSplash, 10000);
    })();
</script>
