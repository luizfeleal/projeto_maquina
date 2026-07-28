@extends('layouts.Login')

@section('title', 'Redefinir Senha')

@section('content')
        <div id="procedimentos" class="login w-100 div-center-column"
                style=" padding-top: 99px;">
                
                <h1 style="padding-top: 80px; text-align: center;">Redefinir Senha</h1>

                <div class="container section container-platform div-center-column"
                style=" height: 100%;">

                <form action="{{ route('login-redefinir-confirmar') }}" id="form_procedimento_criar" method="post" class="form-center needs-validation" novalidate>

                    @csrf
                    <div class="row" style="display: flex; flex-direction: row; justify-content: center; margin-bottom: 20px; width: 100%; margin-top:">
                        <div class="col-md-8">
                            <label for="usuario" class="form-label">Email*:</label>
                            <input type="text" name="usuario_email" id="usuario_emaiil" class="form-control" placeholder="Email" aria-label="Email" maxlength="200" required>
                            <div class="invalid-feedback">
                                Campo obrigatório. Insira um usuário válido.
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
