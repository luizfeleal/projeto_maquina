<?php

namespace App\Http\Controllers\Financeiro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Financeiro\DespesaService;

class DespesasController extends Controller
{
    public function index(Request $request)
    {
        $despesas = collect(DespesaService::coletar())
            ->map(fn (array $d) => DespesaService::normalizarParaView($d));

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

        $resultado = DespesaService::criar([
            'titulo'    => $request->descricao,
            'descricao' => $request->tipo,
            'valor'     => $request->valor,
            'data'      => $request->data_despesa,
        ], $request->file('comprovante'));

        if ($resultado['success'] ?? false) {
            return redirect()->route('financeiro-despesas')
                ->with('success', 'Despesa registrada com sucesso!');
        }

        $mensagem = $resultado['message'] ?? 'Houve um erro ao registrar a despesa.';
        return back()->withInput()->with('error', $mensagem);
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $resultado = DespesaService::excluir((int) $request->id);

        if ($resultado['success'] ?? false) {
            return back()->with('success', 'Despesa removida com sucesso!');
        }

        return back()->with('error', $resultado['message'] ?? 'Houve um erro ao remover a despesa.');
    }
}
