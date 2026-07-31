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

        function hideSplash(force) {
            if (hidden) {
                return;
            }

            // Sem "force", esperamos a logo carregar (ou falhar) pra evitar um pisca de layout.
            // Mas isso não pode travar a splash pra sempre se o evento load/error do <img> nunca
            // disparar (rede instável, imagem cacheada de forma estranha pelo service worker,
            // etc.) — por isso o timeout de segurança abaixo chama hideSplash(true).
            if (!force && logo && (!logo.complete || logo.naturalWidth === 0)) {
                return;
            }

            var elapsed = Date.now() - shownAt;
            if (!force && elapsed < minVisibleMs) {
                setTimeout(function () { hideSplash(force); }, minVisibleMs - elapsed);
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
            logo.addEventListener('load', function () { hideSplash(false); });
            logo.addEventListener('error', function () { hideSplash(false); });
        }

        window.addEventListener('load', function () { hideSplash(false); });
        // Timeout absoluto: garante que a splash nunca fique travada na tela, mesmo se a
        // imagem nunca terminar de carregar.
        setTimeout(function () { hideSplash(true); }, 10000);
    })();
</script>
