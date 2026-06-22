{{-- Modal de instalação PWA --}}
<div class="modal fade" id="pwaInstallModal" tabindex="-1" aria-labelledby="pwaInstallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content pwa-install-modal">
            <div class="modal-header border-0 pb-0">
                <div class="pwa-install-icon" aria-hidden="true">
                    <img src="{{ asset('site/img/icon-192.png') }}" alt="" width="56" height="56">
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body pt-2">
                <h5 class="modal-title fw-bold mb-2" id="pwaInstallModalLabel">Instalar SwiftPay</h5>
                <p class="text-muted mb-3" style="font-size:.9rem;">
                    Instale o app no seu dispositivo para acesso rápido, tela cheia e melhor experiência offline.
                </p>

                <div class="pwa-android-instructions">
                    <ul class="pwa-install-list">
                        <li>Acesso direto pela tela inicial</li>
                        <li>Experiência semelhante à de um aplicativo instalado</li>
                        <li>Recursos essenciais disponíveis offline</li>
                    </ul>
                </div>

                <div class="pwa-ios-instructions d-none">
                    <p class="mb-2" style="font-size:.85rem; font-weight:600; color:#374151;">No iPhone/iPad:</p>
                    <ol class="pwa-install-list pwa-install-list--ordered">
                        <li>Toque em <strong>Compartilhar</strong> <span aria-hidden="true">(ícone de exportar)</span></li>
                        <li>Selecione <strong>Adicionar à Tela de Início</strong></li>
                        <li>Confirme tocando em <strong>Adicionar</strong></li>
                    </ol>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 flex-column flex-sm-row gap-2">
                <button type="button" class="btn btn-outline-secondary w-100 w-sm-auto" id="pwaBtnLater" data-bs-dismiss="modal">
                    Agora não
                </button>
                <button type="button" class="btn btn-primary w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-2" id="pwaBtnInstall">
                    <iconify-icon icon="solar:download-minimalistic-bold-duotone"></iconify-icon>
                    Instalar app
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('site/pwa.js') }}?v=1"></script>
