@extends('layouts.Clientes.app')
@section('title', 'Criar Credencial Mercado Pago')
@section('content')

        <div  class="usuarios div-center-column w-100"
                style="padding-top: 99px;">

            <div class="container section container-platform div-center-column"
                style="margin-top: 15px; height: 100%;">

                <form action="{{ route('cliente-credencial-registrar') }}" id="novo-local-form"  class="w-100 needs-validation form-center"  method="post" enctype="multipart/form-data" novalidate>
                    @csrf

                    <input type="hidden" name="tipo_cred" value="mercadopago">
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;  width: 100%;  margin-bottom: 20px;">
                        <div class="col-md-8">
                            <label for="select-cliente" class="form-label">Selecione o cliente responsável*:</label>
                            <select class="select-cliente js-example-basic-multiple js-states form-control" id="select-cliente" placeholder="Selecione" name="select-cliente" required>

                            <option value="">Selecione</option>
                            @foreach($clientes as $cliente)
                                <option value="{{$cliente['id_cliente']}}">{{$cliente['cliente_nome']}}</option>
                            @endforeach
                            </select>
                            <div class="invalid-feedback">
                                <p class="invalid-p" id="select_cliente_mensagem">Campo obrigatório</p>
                            </div>

                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center;width: 100%; margin-bottom: 20px;">
                        <div class="col-md-4">
                            <label for="cliente_id" class="form-label">Segredo do Webhook: <i class="fa-solid fa-circle-info"  data-bs-toggle="tooltip" data-bs-title="Não é o Client ID do painel do Mercado Pago. Para conseguir: no painel de desenvolvedores, entre em Suas integrações, abra sua aplicação, vá em Webhooks, Configurar notificação, salve a URL de notificação e clique em Revelar chave para ver este segredo."></i></label>
                            <input type="text" class="form-control" name="cliente_id" id="cliente_id" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <label for="cliente_secret" class="form-label">Access Token: <i class="fa-solid fa-circle-info"  data-bs-toggle="tooltip" data-bs-title="No painel de desenvolvedores do Mercado Pago: Suas integrações, abra sua aplicação, vá em Credenciais de produção (ou de teste, para homologação) e copie o Access Token."></i></label>
                            <input type="text" class="form-control" name="cliente_secret" id="cliente_secret" required>
                            <div class="invalid-feedback">
                                <p class="invalid-p invalid-p-name">Campo obrigatório</p>
                            </div>

                        </div>

                    </div>

                    <div style="display:flex; justify-content: center; align-items: center; margin-top: 50px;">
                        <button class="btn btn-primary"  type="submit">Criar credencial</button>
                    </div>
                </form>
            </div>
        </div>

@endsection

@section('scriptTable')

<script>
    $(document).ready(function() {
        $('.select-cliente').select2({
            theme: 'bootstrap-5'
        });

        validaData();
    });
</script>

@endsection

