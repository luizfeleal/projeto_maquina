@extends('layouts.Login')

@section('title', 'Redefinir Senha')

@section('content')
        <div id="procedimentos" class="login w-100 div-center-column"
                style=" padding-top: 99px;">

                <h1 style="padding-top: 80px; text-align: center;">Redefinir Senha</h1>

                <div class="container section container-platform div-center-column"
                style=" height: 100%;">

                <form action="{{ route('login-redefinir-registrar-senha') }}" id="form_procedimento_criar" method="post" class="form-center needs-validation" novalidate>

                    @csrf
                    <input type="hidden" name="id_usuario" value={{$id_usuario}}>
                    <input type="hidden" name="token" value={{$tokenEmail}}>
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%; margin-top:">
                        <div class="col-md-4">
                            <label for="usuario_senha" class="form-label">Senha*:</label>
                            <input type="password" name="usuario_senha" id="usuario_senha" class="form-control" placeholder="Senha" aria-label="Senha" maxlength="200" required>
                            <div class="invalid-feedback">
                                Campo obrigatório. Insira uma senha válida.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="usuario_confirmacao_senha" class="form-label">Confirme a Senha*:</label>
                            <input type="password" name="usuario_confirmacao_senha" id="usuario_confirmacao_senha" class="form-control" placeholder="Confirme a Senha" aria-label="Confirme a Senha" maxlength="200" required>
                            <div class="invalid-feedback">
                                Campo obrigatório. Insira uma válida correspondente a digitada anteriormente.
                            </div>
                        </div>
                    </div>
                    <div class="div-button" style="padding-top: 70px;">
                        <button class="btn btn-primary" type="submit" style="width: 120px;">Enviar</button>
                    </div>
                </form>

                </div>
        </div>


        @if(session('success'))

            <div class="modal fade show" id="modalSuccess" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-modal="true" role="dialog" style="display: block;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalCenterTitle">Sucesso</h1>
                            <button type="button" class="btn-close" onclick="fechaModal('modalSuccess')" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>{{ session('success') }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="fechaModal('modalSuccess')" data-dismiss="modal" aria-label="Close"><a href="{{route('login-view')}}" style="text-decoration: none; color: #FFF;">Login</a></button>
                            <button type="button" class="btn btn-primary" onclick="fechaModal('modalSuccess')" data-dismiss="modal" aria-label="Close">OK</button>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show" id="modalSuccess-backdrop"></div>
            @elseif(session('error'))
            <div class="modal fade show" id="modalError" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-modal="true" role="dialog" style="display: block;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalCenterTitle">Erro</h1>
                                <button type="button" class="btn-close" onclick="fechaModal('modalError')" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>{{ session('error') }}</p>
                            </div>
                            <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="fechaModal('modalError')" data-bs-dismiss="modal" aria-label="Close">OK</button>
                            </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show" id="modalError-backdrop"></div>
        @endif


@endsection
