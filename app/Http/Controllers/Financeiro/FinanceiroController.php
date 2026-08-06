<?php

namespace App\Http\Controllers\Financeiro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ExtratoMaquinaService;
use App\Services\MaquinasService;
use App\Services\MensalidadeService;
use Carbon\Carbon;

class FinanceiroController extends Controller
{
    public function index(Request $request)
    {
        // Totais agregados no banco. Antes esta tela baixava TODAS as transações
        // (em lotes paginados) só para somar receita por mês em PHP.
        $resumo = ExtratoMaquinaService::coletarResumoFinanceiro();

        $porMes = collect($resumo['por_mes'])
            ->mapWithKeys(fn($linha) => [
                (string) ($linha['mes'] ?? '') => round((float) ($linha['total'] ?? 0), 2),
            ])
            ->filter(fn($_, $mes) => $mes !== '')
            ->sortKeys();

        if ($porMes->count() > 12) {
            $porMes = $porMes->slice($porMes->count() - 12);
        }

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
        $totalReceitas  = round((float) $resumo['total_receitas'], 2);
        $totalDespesas  = round((float) $resumo['total_despesas'], 2);
        $totalInadimplencia = MensalidadeService::totalInadimplencia((int) env('INADIMPLENCIA_DIAS', 5));

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
            'totalReceitas', 'totalDespesas', 'totalInadimplencia',
            'maquinas'
        ));
    }

    public function inadimplencia(Request $request)
    {
        $diasTolerancia = (int) env('INADIMPLENCIA_DIAS', 5);
        $inadimplentes  = MensalidadeService::listarInadimplentes($diasTolerancia);

        return view('Financeiro.Inadimplencia.index', compact('inadimplentes', 'diasTolerancia'));
    }

    private function formatarMes(string $anoMes): string
    {
        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $data  = $this->parseAnoMes($anoMes);

        if ($data === null) {
            return $anoMes;
        }

        return ($meses[$data->month - 1] ?? (string) $data->month) . '/' . $data->format('y');
    }

    private function mesParaTrimestre(string $anoMes): string
    {
        $data = $this->parseAnoMes($anoMes);

        if ($data === null) {
            return $anoMes;
        }

        $t = (int) ceil($data->month / 3);

        return $data->format('Y') . '-T' . $t;
    }

    private function parseDataTransacao(?string $dataCriacao): ?Carbon
    {
        if ($dataCriacao === null || trim($dataCriacao) === '') {
            return null;
        }

        try {
            return Carbon::parse($dataCriacao);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parseAnoMes(string $anoMes): ?Carbon
    {
        if (preg_match('/^\d{4}-\d{2}$/', $anoMes)) {
            try {
                return Carbon::createFromFormat('Y-m', $anoMes)->startOfMonth();
            } catch (\Throwable $e) {
                return null;
            }
        }

        return $this->parseDataTransacao($anoMes)?->startOfMonth();
    }
}
