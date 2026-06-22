<?php

namespace App\Http\Controllers\Financeiro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Despesa;
use Illuminate\Support\Facades\Storage;

class DespesasController extends Controller
{
    public function index(Request $request)
    {
        $idCliente = session('id_cliente');
        $despesas  = Despesa::where('id_cliente', $idCliente)
                            ->orderByDesc('data_despesa')
                            ->get();

        return view('Financeiro.Despesas.index', compact('despesas'));
    }

    public function create()
    {
        return view('Financeiro.Despesas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'descricao'    => 'required|string|max:255',
            'valor'        => 'required|numeric|min:0.01',
            'data_despesa' => 'required|date',
            'tipo'         => 'nullable|string|max:100',
            'comprovante'  => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:5120',
        ]);

        $comprovantePath = null;

        if ($request->hasFile('comprovante')) {
            $comprovantePath = $request->file('comprovante')
                ->store('comprovantes_despesas', 'public');
        }

        Despesa::create([
            'id_cliente'       => session('id_cliente'),
            'descricao'        => $request->descricao,
            'valor'            => $request->valor,
            'data_despesa'     => $request->data_despesa,
            'tipo'             => $request->tipo,
            'comprovante_path' => $comprovantePath,
        ]);

        return redirect()->route('financeiro-despesas')
                         ->with('success', 'Despesa registrada com sucesso!');
    }

    public function destroy(Request $request)
    {
        $despesa = Despesa::where('id', $request->id)
                          ->where('id_cliente', session('id_cliente'))
                          ->firstOrFail();

        if ($despesa->comprovante_path) {
            Storage::disk('public')->delete($despesa->comprovante_path);
        }

        $despesa->delete();

        return back()->with('success', 'Despesa removida com sucesso!');
    }
}
