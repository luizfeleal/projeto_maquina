(function () {
    'use strict';

    var deferredPrompt = null;
    var STORAGE_KEY = 'swiftpay_pwa_install_dismissed';
    var DISMISS_DAYS = 7;

    function isStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches
            || window.navigator.standalone === true;
    }

    function isIos() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent)
            && !window.MSStream;
    }

    function isDismissed() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) {
                return false;
            }
            var data = JSON.parse(raw);
            return Date.now() < data.until;
        } catch (e) {
            return false;
        }
    }

    function dismissPrompt() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({
            until: Date.now() + (DISMISS_DAYS * 24 * 60 * 60 * 1000),
        }));
    }

    function getModal() {
        return document.getElementById('pwaInstallModal');
    }

    function showInstallModal(forceIos) {
        var modal = getModal();
        if (!modal || isStandalone()) {
            return;
        }

        var iosBlock = modal.querySelector('.pwa-ios-instructions');
        var androidBlock = modal.querySelector('.pwa-android-instructions');
        var installBtn = document.getElementById('pwaBtnInstall');
        var useIos = forceIos || (isIos() && !deferredPrompt);

        if (useIos) {
            iosBlock?.classList.remove('d-none');
            androidBlock?.classList.add('d-none');
            if (installBtn) {
                installBtn.textContent = 'Entendi';
            }
        } else {
            iosBlock?.classList.add('d-none');
            androidBlock?.classList.remove('d-none');
            if (installBtn) {
                installBtn.innerHTML = '<iconify-icon icon="solar:download-minimalistic-bold-duotone"></iconify-icon> Instalar app';
            }
        }

        if (typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(modal).show();
        }
    }

    function registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            return;
        }

        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(function (err) {
                console.warn('[PWA] Falha ao registrar service worker:', err);
            });
        });
    }

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;

        if (!isStandalone() && !isDismissed()) {
            setTimeout(function () {
                showInstallModal(false);
            }, 2500);
        }

        updateNavbarInstallBtn();
    });

    function updateNavbarInstallBtn() {
        var btn = document.getElementById('navbarPwaInstallBtn');
        if (!btn || isStandalone()) {
            return;
        }

        if (window.SwiftPayPWA && window.SwiftPayPWA.canInstall()) {
            btn.classList.remove('d-none');
        }
    }

    window.SwiftPayPWA = {
        openInstallModal: function () {
            showInstallModal(isIos() && !deferredPrompt);
        },
        canInstall: function () {
            return !isStandalone() && (!!deferredPrompt || isIos());
        },
    };

    document.addEventListener('DOMContentLoaded', function () {
        registerServiceWorker();

        var btnInstall = document.getElementById('pwaBtnInstall');
        var btnLater = document.getElementById('pwaBtnLater');
        var navbarBtn = document.getElementById('navbarPwaInstallBtn');

        if (navbarBtn) {
            navbarBtn.addEventListener('click', function () {
                window.SwiftPayPWA.openInstallModal();
            });
        }

        if (btnInstall) {
            btnInstall.addEventListener('click', function () {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then(function (result) {
                        if (result.outcome === 'accepted') {
                            dismissPrompt();
                        }
                        deferredPrompt = null;
                        var modal = getModal();
                        if (modal && typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modal)?.hide();
                        }
                    });
                    return;
                }

                if (isIos()) {
                    dismissPrompt();
                    var modal = getModal();
                    if (modal && typeof bootstrap !== 'undefined') {
                        bootstrap.Modal.getInstance(modal)?.hide();
                    }
                }
            });
        }

        if (btnLater) {
            btnLater.addEventListener('click', dismissPrompt);
        }

        if (isIos() && !isStandalone() && !isDismissed() && !deferredPrompt) {
            setTimeout(function () {
                showInstallModal(true);
            }, 3500);
        }

        updateNavbarInstallBtn();
    });

    window.addEventListener('appinstalled', function () {
        deferredPrompt = null;
        dismissPrompt();
        updateNavbarInstallBtn();
    });
})();
