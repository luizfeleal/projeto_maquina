@php
    $anosGrafico     = array_keys($dadosGrafico ?? []);
    $anoAtualGrafico = date('Y');
    $anoInicialGrafico = in_array($anoAtualGrafico, $anosGrafico)
        ? $anoAtualGrafico
        : ($anosGrafico[0] ?? $anoAtualGrafico);

    $mesesGrafico = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
    ];
@endphp

<section id="grafico" class="dash-section">

    <div class="dash-section-header">
        <h2>
            <iconify-icon icon="solar:chart-square-bold-duotone" style="color:var(--sp-teal);"></iconify-icon>
            Total de Recebimentos
        </h2>
        <p>Visão mensal dos recebimentos por forma de pagamento</p>
    </div>

    <div class="dash-card" style="padding:24px;">

        @if(count($listaMaquinas) > 1)
        <form method="GET" action="{{ route('home') }}#grafico"
              style="margin-bottom:16px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <label style="font-size:.8rem; font-weight:600; color:var(--sp-text-sub); white-space:nowrap;">
                <iconify-icon icon="solar:filter-bold-duotone" style="vertical-align:middle;"></iconify-icon>
                Filtrar máquina:
            </label>
            <select name="id_maquina" class="dash-filter-select" onchange="this.form.submit()">
                <option value="">Todas as máquinas</option>
                @foreach($listaMaquinas as $maq)
                    <option value="{{ $maq['id_maquina'] }}"
                        {{ (string)$idMaquinaFiltro === (string)$maq['id_maquina'] ? 'selected' : '' }}>
                        {{ $maq['maquina_nome'] }} — {{ $maq['local_nome'] }}
                    </option>
                @endforeach
            </select>
            @if($idMaquinaFiltro)
                <a href="{{ route('home') }}#grafico"
                   style="font-size:.78rem; color:var(--sp-muted); text-decoration:none;">✕ Limpar</a>
            @endif
        </form>
        @endif

        <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px;
                    flex-wrap:wrap; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <label for="grafico-ano-select"
                           style="font-size:.78rem; font-weight:600; color:var(--sp-text-sub); white-space:nowrap;">
                        <iconify-icon icon="solar:calendar-bold-duotone" style="vertical-align:middle;"></iconify-icon>
                        Ano:
                    </label>
                    <select id="grafico-ano-select" class="form-select"
                            style="width:auto; min-width:90px; font-size:.85rem;">
                        @forelse($anosGrafico as $ano)
                            <option value="{{ $ano }}" {{ $ano == $anoInicialGrafico ? 'selected' : '' }}>
                                {{ $ano }}
                            </option>
                        @empty
                            <option value="{{ $anoAtualGrafico }}">{{ $anoAtualGrafico }}</option>
                        @endforelse
                    </select>
                </div>

                <div style="display:flex; align-items:center; gap:10px;">
                    <label for="grafico-mes-inicio-select"
                           style="font-size:.78rem; font-weight:600; color:var(--sp-text-sub); white-space:nowrap;">
                        De:
                    </label>
                    <select id="grafico-mes-inicio-select" class="form-select"
                            style="width:auto; min-width:130px; font-size:.85rem;">
                        @foreach($mesesGrafico as $numero => $nome)
                            <option value="{{ $numero }}" {{ $numero === 1 ? 'selected' : '' }}>{{ $nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex; align-items:center; gap:10px;">
                    <label for="grafico-mes-fim-select"
                           style="font-size:.78rem; font-weight:600; color:var(--sp-text-sub); white-space:nowrap;">
                        Até:
                    </label>
                    <select id="grafico-mes-fim-select" class="form-select"
                            style="width:auto; min-width:130px; font-size:.85rem;">
                        @foreach($mesesGrafico as $numero => $nome)
                            <option value="{{ $numero }}" {{ $numero === 12 ? 'selected' : '' }}>{{ $nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="grafico-total-label"
                 style="font-size:.95rem; font-weight:700; color:var(--sp-teal);"></div>
        </div>

        <div id="grafico-recebimentos-chart"></div>

    </div>

</section>

@push('scriptTable')
<script>
(function () {
    var dadosGrafico = @json($dadosGrafico ?? []);
    var meses = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

    function formatBRL(v) {
        return 'R$ ' + parseFloat(v || 0).toFixed(2)
            .replace('.', ',')
            .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function isDark() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }

    function mesRange(mesIni, mesFim) {
        mesIni = parseInt(mesIni, 10) || 1;
        mesFim = parseInt(mesFim, 10) || 12;
        if (mesFim < mesIni) {
            var tmp = mesIni; mesIni = mesFim; mesFim = tmp;
        }
        return { ini: mesIni, fim: mesFim };
    }

    function buildSeries(ano, mesIni, mesFim) {
        var range = mesRange(mesIni, mesFim);
        var d = dadosGrafico[ano] || {};
        var pix = [], cartao = [], dinheiro = [], categorias = [];
        for (var m = range.ini; m <= range.fim; m++) {
            var md = d[m] || {};
            pix.push(parseFloat(md.pix || 0));
            cartao.push(parseFloat(md.cartao || 0));
            dinheiro.push(parseFloat(md.dinheiro || 0));
            categorias.push(meses[m - 1]);
        }
        return {
            series: [
                { name: 'PIX',      data: pix },
                { name: 'Cartão',   data: cartao },
                { name: 'Dinheiro', data: dinheiro },
            ],
            categorias: categorias,
        };
    }

    function calcTotal(ano, mesIni, mesFim) {
        var range = mesRange(mesIni, mesFim);
        var d = dadosGrafico[ano] || {};
        var t = 0;
        for (var m = range.ini; m <= range.fim; m++) {
            var md = d[m] || {};
            t += parseFloat(md.pix || 0) + parseFloat(md.cartao || 0) + parseFloat(md.dinheiro || 0);
        }
        return t;
    }

    function updateTotal(ano, mesIni, mesFim) {
        var el = document.getElementById('grafico-total-label');
        if (!el) return;
        var range = mesRange(mesIni, mesFim);
        var label = range.ini === 1 && range.fim === 12
            ? ('Total ' + ano)
            : ('Total ' + meses[range.ini - 1] + '–' + meses[range.fim - 1] + '/' + ano);
        el.textContent = label + ': ' + formatBRL(calcTotal(ano, mesIni, mesFim));
    }

    function buildOptions(series, categorias, dark) {
        return {
            chart: {
                type: 'bar',
                height: 320,
                stacked: true,
                toolbar: { show: false },
                background: 'transparent',
                fontFamily: 'inherit',
                animations: { enabled: true, speed: 400 },
            },
            theme: { mode: dark ? 'dark' : 'light' },
            series: series,
            xaxis: {
                categories: categorias,
                labels: { style: { fontSize: '12px' } },
            },
            yaxis: {
                labels: {
                    formatter: function (v) { return formatBRL(v); },
                    style: { fontSize: '11px' },
                },
            },
            tooltip: {
                y: { formatter: function (v) { return formatBRL(v); } },
            },
            colors: ['#2C9BA5', '#6366f1', '#f59e0b'],
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                fontSize: '13px',
                markers: { size: 8 },
            },
            dataLabels: { enabled: false },
            plotOptions: {
                bar: {
                    borderRadius: 3,
                    columnWidth: '55%',
                },
            },
            grid: {
                borderColor: dark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.06)',
                strokeDashArray: 3,
            },
        };
    }

    var chartEl      = document.getElementById('grafico-recebimentos-chart');
    var selectAno    = document.getElementById('grafico-ano-select');
    var selectMesIni = document.getElementById('grafico-mes-inicio-select');
    var selectMesFim = document.getElementById('grafico-mes-fim-select');
    if (!chartEl || !selectAno || !selectMesIni || !selectMesFim) return;

    function refresh() {
        var ano = selectAno.value || String(new Date().getFullYear());
        var built = buildSeries(ano, selectMesIni.value, selectMesFim.value);
        chart.updateOptions({
            series: built.series,
            xaxis: { categories: built.categorias },
        });
        updateTotal(ano, selectMesIni.value, selectMesFim.value);
    }

    var anoSel  = selectAno.value || String(new Date().getFullYear());
    var initial = buildSeries(anoSel, selectMesIni.value, selectMesFim.value);

    var chart = new ApexCharts(chartEl, buildOptions(initial.series, initial.categorias, isDark()));
    chart.render();
    updateTotal(anoSel, selectMesIni.value, selectMesFim.value);

    selectAno.addEventListener('change', refresh);
    selectMesIni.addEventListener('change', refresh);
    selectMesFim.addEventListener('change', refresh);

    new MutationObserver(function () {
        chart.updateOptions({ theme: { mode: isDark() ? 'dark' : 'light' } });
    }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
})();
</script>
@endpush
