@extends('layouts.app')
@section('title', 'Painel')
@section('content')

<div class="dash-page">
    @include('Admin.partials.dashboard.acoes-rapidas')
    @include('Admin.partials.dashboard.resumo-financeiro')
    @include('Admin.partials.dashboard.maquinas-compactas')
    @include('Admin.partials.dashboard.transacoes-recentes')
    @include('Admin.partials.dashboard.relatorios-rapidos')
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
        '.select-cliente-transacoes', '.select-maquina-transacoes',
        '.select-cliente', '.select-maquina', '.select-local',
        '.select-cliente-erros', '.select-maquina-erros', '.select-local-erros'
    ].forEach(function (sel) {
        $(sel).select2({ theme: 'bootstrap-5', width: '100%' });
    });

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
