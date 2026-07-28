<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\MensalidadeService;
use Carbon\Carbon;

class CheckInadimplencia
{
    public function handle(Request $request, Closure $next)
    {
        if (!filter_var(env('INADIMPLENCIA_CHECK_ENABLED', true), FILTER_VALIDATE_BOOLEAN)) {
            return $next($request);
        }

        $idCliente = session('id_cliente');

        if (!$idCliente) {
            return $next($request);
        }

        $diasTolerancia  = (int) env('INADIMPLENCIA_DIAS', 5);
        $limiteVencimento = Carbon::today()->subDays($diasTolerancia)->toDateString();

        $mensalidades = MensalidadeService::coletar([
            'id_cliente'        => $idCliente,
            'vencimento_fim'    => $limiteVencimento,
        ]);

        $inadimplente = collect($mensalidades)->contains(fn ($m) => ($m['status'] ?? null) !== 'pago');

        if ($inadimplente) {
            $mensagem = 'Sua conta possui mensalidades em atraso. A liberação de jogadas está bloqueada até a regularização.';

            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'inadimplente',
                    'message' => $mensagem,
                ], 402);
            }

            return redirect()->back()->with('error', $mensagem);
        }

        return $next($request);
    }
}
