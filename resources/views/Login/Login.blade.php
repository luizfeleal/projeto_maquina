@extends('layouts.Login')

@section('title', 'Login')

@section('content')

<div class="login-page">

    {{-- Left: branding panel --}}
    <div class="login-brand-panel">
        <img src="{{ asset('site/img/swift_pay_solucoes_png.png') }}" alt="Swift Pay Soluções" class="brand-logo">
        <h1 class="brand-title">Swift Pay Soluções</h1>
        <p class="brand-subtitle">Gestão completa de máquinas de pagamento na palma da sua mão.</p>
        <div class="brand-badge">
            <iconify-icon icon="solar:shield-check-bold-duotone"></iconify-icon>
            Acesso seguro
        </div>
    </div>

    {{-- Right: form panel --}}
    <div class="login-form-panel">
        <div class="login-card">

            <div class="login-card-header">
                <h2>Bem-vindo de volta</h2>
                <p>Insira suas credenciais para acessar o painel.</p>
            </div>

            <form action="{{ route('autenticar') }}" id="form_login" method="post" class="needs-validation" novalidate>
                @csrf

                <div class="form-group">
                    <label for="usuario">Usuário</label>
                    <input
                        type="text"
                        name="usuario"
                        id="usuario"
                        class="form-control"
                        placeholder="Digite seu usuário"
                        maxlength="200"
                        required
                        autocomplete="username"
                    >
                    <div class="invalid-feedback">Campo obrigatório. Insira um usuário válido.</div>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input
                        type="password"
                        name="senha"
                        id="senha"
                        class="form-control"
                        placeholder="Digite sua senha"
                        required
                        autocomplete="current-password"
                    >
                    <div class="invalid-feedback">Campo obrigatório. Insira uma senha válida.</div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('login-redefinir-view') }}" class="login-forgot">Esqueci minha senha</a>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <span class="btn-login-spinner" aria-hidden="true"></span>
                    <span class="btn-login-text">Acessar</span>
                </button>

            </form>
        </div>
    </div>

</div>

<script>
    (function () {
        var form = document.getElementById('form_login');
        var btn  = document.getElementById('btnLogin');
        var text = btn ? btn.querySelector('.btn-login-text') : null;
        if (!form || !btn) return;

        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            btn.classList.add('is-loading');
            btn.setAttribute('disabled', 'disabled');
            if (text) text.textContent = 'Acessando...';
        }, false);
    })();
</script>

@endsection
