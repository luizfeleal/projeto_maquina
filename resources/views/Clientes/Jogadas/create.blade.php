@extends('layouts.Clientes.app')
@section('title', 'Liberar Jogada')
@section('content')

<div id="local-incluir-usuario" class="local div-center-column w-100" style="padding-top: 99px;">
    <div class="container section container-platform div-center-column" style="margin-top: 15px; height: 100%;">

        {{-- Alerta de inadimplência (redireccionamento via back() com error) --}}
        @if(session('error'))
            @php $msg = session('error'); @endphp
            @if(str_contains($msg, 'mensalidade') || str_contains($msg, 'inadimpl') || str_contains($msg, 'atraso'))
                <div class="alert alert-danger d-flex align-items-center gap-3 mb-4 shadow-sm" role="alert">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone" style="font-size:1.8rem; flex-shrink:0;"></iconify-icon>
                    <div>
                        <strong>Jogada bloqueada</strong><br>
                        <span class="small">{{ $msg }}</span>
                    </div>
                </div>
            @endif
        @endif

        <form action="{{ route('clientes-maquinas-liberar-jogadas') }}" method="post" id="liberar-jogada-form" class="w-100">
            @csrf

            <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                <div class="col-md-6">
                    <label for="nome_local" class="form-label">Máquina*:</label>
                    <select class="select-local js-example-basic-multiple js-states form-control" id="select-id-placa" placeholder="Selecione" name="select-id-placa">

                    @if(!$id_maquina)
                    <option value="" selected>Selecione</option>
                    @endif
                    @foreach($maquinas as $maquina)
                    @if($id_maquina && $maquina['id_maquina'] == $id_maquina)
                        <option value="{{$maquina['id_placa']}}" selected>{{$maquina['maquina_nome']}}</option>
                    @else
                        <option value="{{$maquina['id_placa']}}">{{$maquina['maquina_nome']}}</option>
                    @endif
                    @endforeach
                    </select>
                    <div class="invalid-feedback">
                        <p class="invalid-p" id="select_local_mensagem">Campo obrigatório</p>
                    </div>
                </div>
            </div>

            <div class="row" style="display: flex; flex-direction: row; justify-content: center;  width: 100%; ">
                <div class="col-md-6">
                    <label for="Valor Credito" class="form-label">Valor Crédito*:</label>
                    <input type="number" name="valor_credito" id="valor_credito" class="form-control input-text" placeholder="Valor Crédito" min="1" max="100" aria-label="Valor Credito" required>
                    <div class="invalid-feedback">
                        <p class="invalid-p" id="select_cliente_mensagem">Campo obrigatório</p>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px;">
                <button class="btn btn-primary" type="submit" id="btn-liberar">Enviar</button>
            </div>
        </form>

    </div>
</div>

{{-- Modal de bloqueio por inadimplência (para intercepção AJAX futura) --}}
<div class="modal fade" id="modalInadimplencia" tabindex="-1" aria-labelledby="modalInadimplenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone" style="font-size:1.6rem; color:#ef4444;"></iconify-icon>
                    <h5 class="modal-title fw-bold" id="modalInadimplenciaLabel">Jogada Bloqueada</h5>
                </div>
            </div>
            <div class="modal-body pt-2">
                <p id="modalInadimplenciaMsg" class="text-muted mb-0">
                    Sua conta possui mensalidades em atraso. A liberação de jogadas está bloqueada até a regularização.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scriptTable')
<script>
    $(document).ready(function() {
        $('.select-local').select2({ theme: 'bootstrap-5' });

        // Intercepção AJAX: submete via fetch e trata 402/403
        const form = document.getElementById('liberar-jogada-form');
        const btn  = document.getElementById('btn-liberar');

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            btn.disabled = true;
            btn.textContent = 'Enviando...';

            const formData = new FormData(form);

            try {
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData,
                });

                if (resp.status === 402 || resp.status === 403) {
                    const data = await resp.json().catch(() => ({}));
                    const msg  = data.message || 'Jogada bloqueada por pendências financeiras.';
                    document.getElementById('modalInadimplenciaMsg').textContent = msg;
                    new bootstrap.Modal(document.getElementById('modalInadimplencia')).show();
                    return;
                }

                // Resposta de redirecionamento (200 com HTML) — recarrega
                window.location.reload();
            } catch (err) {
                // Fallback: submissão tradicional
                form.submit();
            } finally {
                btn.disabled = false;
                btn.textContent = 'Enviar';
            }
        });
    });
</script>
@endsection
