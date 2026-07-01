<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Mensalidade;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

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

        try {
            $diasTolerancia = (int) env('INADIMPLENCIA_DIAS', 5);

            $inadimplente = Mensalidade::where('id_cliente', $idCliente)
                ->inadimplentes($diasTolerancia)
                ->exists();
        } catch (QueryException $e) {
            Log::warning('[CheckInadimplencia] Banco indisponível; verificação de mensalidades ignorada.', [
                'id_cliente' => $idCliente,
                'erro'       => $e->getMessage(),
            ]);

            return $next($request);
        }

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
