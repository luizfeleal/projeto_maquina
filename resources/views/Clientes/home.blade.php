@extends('layouts.Clientes.app')
@section('title', 'Painel')
@section('content')

<div class="dash-page">
    @include('Clientes.partials.dashboard.acoes-rapidas')
    @include('Clientes.partials.dashboard.resumo-financeiro')
    @include('Clientes.partials.dashboard.maquinas-compactas')
    @include('Clientes.partials.dashboard.transacoes-recentes')
    @include('Clientes.partials.dashboard.relatorios-rapidos')
</div>

@endsection

@section('scriptTable')
@include('Clientes.partials.dashboard._styles')
<script>
$(document).ready(function () {

    function ativarRelatorio(targetId) {
        $('.rel-panel').hide();
        $('#' + targetId).show();
        $('.rel-tab-btn').removeClass('active');
        $('.rel-tab-btn[data-target="' + targetId + '"]').addClass('active');
    }

    $('.rel-tab-btn').on('click', function () {
        ativarRelatorio($(this).data('target'));
    });

    $('.rel-atalho').on('click', function () {
        var hash = $(this).attr('href').replace('#', '');
        ativarRelatorio(hash);
        if ($(this).hasClass('rel-atalho-estorno')) {
            $('#relatorios-totalTransacoes select[name="tipo_transacao"]').val('Estorno');
        }
    });

    var hash = window.location.hash.replace('#', '');
    if (hash && hash.startsWith('relatorios-')) {
        ativarRelatorio(hash);
    }

    [
        '.select-maquina-transacoes', '.select-local-transacoes',
        '.select-maquina', '.select-local',
        '.select-maquina-erros', '.select-local-erros'
    ].forEach(function (sel) {
        $(sel).select2({ theme: 'bootstrap-5', width: '100%' });
    });

    // Destaque da seção ativa na navegação ao rolar
    var sections = ['acoes', 'resumo', 'transacoes', 'maquinas', 'relatorios'];
    function updateNav() {
        var scrollY = window.scrollY + 100;
        var current = sections[0];
        sections.forEach(function (id) {
            var el = document.getElementById(id);
            if (el && el.offsetTop <= scrollY) current = id;
        });
        $('.dash-nav-link').removeClass('active');
        $('.dash-nav-link[href="#' + current + '"]').addClass('active');
    }
    $(window).on('scroll', updateNav);
    updateNav();
});
</script>
@endsection
