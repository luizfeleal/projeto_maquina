<?php

namespace App\Http\Controllers\Financeiro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Financeiro\EstoquePlacaService;

class PlacasController extends Controller
{
    public function index()
    {
        $placas = collect(EstoquePlacaService::coletar())
            ->map(fn (array $p) => EstoquePlacaService::normalizarParaView($p));

        return view('Financeiro.Placas.index', compact('placas'));
    }

    public function create()
    {
        return view('Financeiro.Placas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'placa_serial' => 'required|string|max:255',
            'id_maquina'   => 'nullable|string|max:100',
        ]);

        $idCliente = null;
        if ($request->filled('id_maquina') && ctype_digit((string) $request->id_maquina)) {
            $idCliente = (int) $request->id_maquina;
        }

        $resultado = EstoquePlacaService::criar([
            'serial'               => strtoupper(trim($request->placa_serial)),
            'status'               => 'disponivel',
            'id_cliente_associado' => $idCliente,
        ]);

        if ($resultado['success'] ?? false) {
            return redirect()->route('financeiro-placas')
                ->with('success', 'Placa registrada com sucesso!');
        }

        return back()->withInput()->with('error', $resultado['message'] ?? 'Houve um erro ao registrar a placa.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $resultado = EstoquePlacaService::excluir((int) $request->id);

        if ($resultado['success'] ?? false) {
            return back()->with('success', 'Placa removida com sucesso!');
        }

        return back()->with('error', $resultado['message'] ?? 'Houve um erro ao remover a placa.');
    }
}
