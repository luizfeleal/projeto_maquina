<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Mensalidade;

class CheckInadimplencia
{
    public function handle(Request $request, Closure $next)
    {
        $idCliente = session('id_cliente');

        if (!$idCliente) {
            return $next($request);
        }

        $diasTolerancia = (int) env('INADIMPLENCIA_DIAS', 5);

        $inadimplente = Mensalidade::where('id_cliente', $idCliente)
            ->inadimplentes($diasTolerancia)
            ->exists();

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
