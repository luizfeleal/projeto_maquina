<?php

namespace App\Http\Controllers\Financeiro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ExtratoMaquinaService;
use App\Services\MaquinasService;
use Carbon\Carbon;

class FinanceiroController extends Controller
{
    public function index(Request $request)
    {
        // Transações dos últimos 12 meses para os gráficos
        $transacoesRaw = ExtratoMaquinaService::coletarComPaginacao([
            'length' => 5000,
            'start'  => 0,
            'order'  => [['column' => 4, 'dir' => 'desc']],
        ]);
        $transacoes = collect($transacoesRaw['data'] ?? (is_array($transacoesRaw) ? $transacoesRaw : []))
            ->filter(fn($tx) => is_array($tx) && isset($tx['data_criacao']));

        // Agrupa por mês (últimos 12 meses)
        $porMes = $transacoes
            ->filter(fn($tx) => ($tx['extrato_operacao'] ?? 'C') === 'C')
            ->groupBy(fn($tx) => substr($tx['data_criacao'], 0, 7))
            ->map(fn($grupo) => round($grupo->sum(fn($tx) => (float)($tx['extrato_operacao_valor'] ?? 0)), 2))
            ->sortKeys()
            ->takeLast(12);

        $mesesLabels  = $porMes->keys()->map(fn($k) => $this->formatarMes($k))->values()->toArray();
        $mesesValores = $porMes->values()->toArray();

        // Agrupa por trimestre
        $porTrimestre = $porMes
            ->groupBy(fn($_, $mes) => $this->mesParaTrimestre($mes))
            ->map(fn($grupo) => round($grupo->sum(), 2))
            ->sortKeys();

        $trimestresLabels  = $porTrimestre->keys()->values()->toArray();
        $trimestresValores = $porTrimestre->values()->toArray();

        // Totais do dashboard
        $totalReceitas  = round($transacoes->where('extrato_operacao', 'C')->sum(fn($tx) => (float)($tx['extrato_operacao_valor'] ?? 0)), 2);
        $totalDespesas  = round($transacoes->where('extrato_operacao', 'D')->sum(fn($tx) => (float)($tx['extrato_operacao_valor'] ?? 0)), 2);

        // Máquinas com status_comunicacao
        $maquinas = collect(MaquinasService::coletar())->map(fn($m) => [
            'id_maquina'        => $m['id_maquina'] ?? null,
            'maquina_nome'      => $m['maquina_nome'] ?? '—',
            'maquina_status'    => $m['maquina_status'] ?? 1,
            'status_pix'        => $m['status_pix'] ?? $m['pix_ativo'] ?? $m['maquina_status'] ?? 1,
            'status_cartao'     => $m['status_cartao'] ?? $m['cartao_ativo'] ?? $m['maquina_status'] ?? 1,
            'local_nome'        => $m['local_nome'] ?? '—',
        ])->values();

        return view('Financeiro.home', compact(
            'mesesLabels', 'mesesValores',
            'trimestresLabels', 'trimestresValores',
            'totalReceitas', 'totalDespesas',
            'maquinas'
        ));
    }

    private function formatarMes(string $anoMes): string
    {
        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        [$ano, $mes] = explode('-', $anoMes);
        return ($meses[(int)$mes - 1] ?? $mes) . '/' . substr($ano, 2);
    }

    private function mesParaTrimestre(string $anoMes): string
    {
        [$ano, $mes] = explode('-', $anoMes);
        $t = (int) ceil((int)$mes / 3);
        return "{$ano}-T{$t}";
    }
}
