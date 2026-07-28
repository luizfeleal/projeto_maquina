<div id="appSplash" role="status" aria-live="polite" aria-label="Carregando SwiftPay">
    <div class="app-splash__backdrop" aria-hidden="true"></div>
    <div class="app-splash__content">
        <img
            src="{{ asset('site/img/swift_pay_solucoes_png.png') }}"
            alt="SwiftPay Soluções"
            class="app-splash__logo"
            width="220"
            height="88"
            fetchpriority="high"
            decoding="sync"
        >
    </div>
</div>

<script>
    (function () {
        var splash = document.getElementById('appSplash');
        if (!splash) {
            return;
        }

        document.body.classList.add('is-app-loading');

        var logo = splash.querySelector('.app-splash__logo');
        var hidden = false;
        var shownAt = Date.now();
        var minVisibleMs = 500;

        function hideSplash() {
            if (hidden) {
                return;
            }

            if (logo && (!logo.complete || logo.naturalWidth === 0)) {
                return;
            }

            var elapsed = Date.now() - shownAt;
            if (elapsed < minVisibleMs) {
                setTimeout(hideSplash, minVisibleMs - elapsed);
                return;
            }

            hidden = true;
            splash.classList.add('is-hidden');
            document.body.classList.remove('is-app-loading');

            setTimeout(function () {
                splash.remove();
            }, 400);
        }

        if (logo) {
            logo.addEventListener('load', hideSplash);
            logo.addEventListener('error', hideSplash);
        }

        window.addEventListener('load', hideSplash);
        setTimeout(hideSplash, 10000);
    })();
</script>
