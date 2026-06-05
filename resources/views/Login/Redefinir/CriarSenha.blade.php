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




@endsection
