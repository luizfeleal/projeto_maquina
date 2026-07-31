@extends('layouts.Login')

@section('title', 'Permissões indisponíveis')

@section('content')

<div class="login-page">
    <div class="login-form-panel" style="width: 100%;">
        <div class="login-card" style="text-align: center;">
            <div class="login-card-header">
                <h2>Não foi possível carregar suas permissões</h2>
                <p>{{ $mensagem ?? 'Houve uma instabilidade ao consultar suas permissões de acesso. Sua sessão continua ativa.' }}</p>
            </div>

            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('login-view') }}" class="btn-login" style="text-decoration: none;">
                <span class="btn-login-text">Tentar novamente</span>
            </a>

            <div class="d-flex justify-content-center" style="margin-top: 16px;">
                <a href="{{ route('logout') }}" class="login-forgot">Sair e fazer login novamente</a>
            </div>
        </div>
    </div>
</div>

@endsection
