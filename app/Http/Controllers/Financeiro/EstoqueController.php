<?php

namespace App\Http\Controllers\Financeiro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Financeiro\EstoqueService;

class EstoqueController extends Controller
{
    public function index()
    {
        $produtos = collect(EstoqueService::coletar())
            ->map(fn (array $p) => EstoqueService::normalizarParaView($p));

        return view('Financeiro.Estoque.index', compact('produtos'));
    }

    public function create()
    {
        return view('Financeiro.Estoque.create');
    }

    public function show($id)
    {
        $produto = EstoqueService::buscar((int) $id);

        if (!$produto) {
            return redirect()->route('financeiro-estoque')
                ->with('error', 'Produto não encontrado.');
        }

        $produto = EstoqueService::normalizarParaView($produto);

        return view('Financeiro.Estoque.show', compact('produto'));
    }

    public function edit($id)
    {
        $produto = EstoqueService::buscar((int) $id);

        if (!$produto) {
            return redirect()->route('financeiro-estoque')
                ->with('error', 'Produto não encontrado.');
        }

        $produto = EstoqueService::normalizarParaView($produto);

        return view('Financeiro.Estoque.edit', compact('produto'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lote'          => 'nullable|string|max:100',
            'nome_produto'  => 'required|string|max:150',
            'descricao'     => 'nullable|string|max:1000',
            'quantidade'    => 'required|integer|min:0',
            'valor'         => 'required|numeric|min:0',
            'cobrar_mensal' => 'nullable|boolean',
        ]);

        $resultado = EstoqueService::atualizar((int) $id, [
            'lote'          => $request->filled('lote') ? trim($request->lote) : null,
            'nome_produto'  => trim($request->nome_produto),
            'descricao'     => $request->descricao,
            'quantidade'    => $request->quantidade,
            'valor'         => $request->valor,
            'cobrar_mensal' => $request->boolean('cobrar_mensal'),
        ]);

        if ($resultado['success'] ?? false) {
            return redirect()->route('financeiro-estoque')
                ->with('success', 'Produto atualizado com sucesso!');
        }

        return back()->withInput()->with('error', $resultado['message'] ?? 'Houve um erro ao atualizar o produto.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'lote'          => 'nullable|string|max:100',
            'nome_produto'  => 'required|string|max:150',
            'descricao'     => 'nullable|string|max:1000',
            'quantidade'    => 'required|integer|min:0',
            'valor'         => 'required|numeric|min:0',
            'cobrar_mensal' => 'nullable|boolean',
        ]);

        $resultado = EstoqueService::criar([
            'lote'          => $request->filled('lote') ? trim($request->lote) : null,
            'nome_produto'  => trim($request->nome_produto),
            'descricao'     => $request->descricao,
            'quantidade'    => $request->quantidade,
            'valor'         => $request->valor,
            'cobrar_mensal' => $request->boolean('cobrar_mensal'),
        ]);

        if ($resultado['success'] ?? false) {
            return redirect()->route('financeiro-estoque')
                ->with('success', 'Produto registrado no estoque com sucesso!');
        }

        return back()->withInput()->with('error', $resultado['message'] ?? 'Houve um erro ao registrar o produto.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $resultado = EstoqueService::excluir((int) $request->id);

        if ($resultado['success'] ?? false) {
            return back()->with('success', 'Produto removido do estoque com sucesso!');
        }

        return back()->with('error', $resultado['message'] ?? 'Houve um erro ao remover o produto.');
    }
}
