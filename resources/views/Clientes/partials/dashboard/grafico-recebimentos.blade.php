@php
    $anosGrafico     = array_keys($dadosGrafico ?? []);
    $anoAtualGrafico = date('Y');
    $anoInicialGrafico = in_array($anoAtualGrafico, $anosGrafico)
        ? $anoAtualGrafico
        : ($anosGrafico[0] ?? $anoAtualGrafico);
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

        <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px;
                    flex-wrap:wrap; justify-content:space-between;">
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

    function buildSeries(ano) {
        var d = dadosGrafico[ano] || {};
        var pix = [], cartao = [], dinheiro = [];
        for (var m = 1; m <= 12; m++) {
            var md = d[m] || {};
            pix.push(parseFloat(md.pix || 0));
            cartao.push(parseFloat(md.cartao || 0));
            dinheiro.push(parseFloat(md.dinheiro || 0));
        }
        return [
            { name: 'PIX',      data: pix },
            { name: 'Cartão',   data: cartao },
            { name: 'Dinheiro', data: dinheiro },
        ];
    }

    function calcTotal(ano) {
        var d = dadosGrafico[ano] || {};
        var t = 0;
        for (var m = 1; m <= 12; m++) {
            var md = d[m] || {};
            t += parseFloat(md.pix || 0) + parseFloat(md.cartao || 0) + parseFloat(md.dinheiro || 0);
        }
        return t;
    }

    function updateTotal(ano) {
        var el = document.getElementById('grafico-total-label');
        if (el) el.textContent = 'Total ' + ano + ': ' + formatBRL(calcTotal(ano));
    }

    function buildOptions(series, dark) {
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
                categories: meses,
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

    var chartEl = document.getElementById('grafico-recebimentos-chart');
    var select  = document.getElementById('grafico-ano-select');
    if (!chartEl || !select) return;

    var anoSel = select.value || String(new Date().getFullYear());

    var chart = new ApexCharts(chartEl, buildOptions(buildSeries(anoSel), isDark()));
    chart.render();
    updateTotal(anoSel);

    select.addEventListener('change', function () {
        chart.updateSeries(buildSeries(this.value));
        updateTotal(this.value);
    });

    new MutationObserver(function () {
        chart.updateOptions({ theme: { mode: isDark() ? 'dark' : 'light' } });
    }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
})();
</script>
@endpush
