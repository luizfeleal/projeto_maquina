<?php

namespace App\Http\Controllers\Financeiro;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Services\Financeiro\DespesaService;
use App\Services\MaquinasService;

class DespesasController extends Controller
{
    private const CATEGORIAS = [
        'Manutenção de máquinas',
        'Compra de estoque/produtos',
        'Aluguel de ponto',
        'Impostos e taxas',
        'Salários e comissões',
        'Combustível e transporte',
        'Marketing',
        'Equipamentos e hardware',
        'Serviços de TI',
        'Outros',
    ];

    private const CATEGORIA_COMPRA = 'Compra de estoque/produtos';

    private const FORMAS_PAGAMENTO = [
        'Dinheiro',
        'Pix',
        'Cartão de crédito',
        'Cartão de débito',
        'Boleto',
        'Transferência bancária',
        'Outro',
    ];

    public function index(Request $request)
    {
        $despesas = collect(DespesaService::coletar())
            ->map(fn (array $d) => DespesaService::normalizarParaView($d));

        return view('Financeiro.Despesas.index', compact('despesas'));
    }

    public function create()
    {
        $maquinas = collect(MaquinasService::coletar())
            ->map(fn ($m) => [
                'id_maquina'   => $m['id_maquina'] ?? null,
                'maquina_nome' => $m['maquina_nome'] ?? '—',
                'local_nome'   => $m['local_nome'] ?? null,
            ])
            ->filter(fn ($m) => $m['id_maquina'])
            ->values();

        $categorias = self::CATEGORIAS;
        $formasPagamento = self::FORMAS_PAGAMENTO;

        return view('Financeiro.Despesas.create', compact('maquinas', 'categorias', 'formasPagamento'));
    }

    public function show($id)
    {
        $despesaBruta = DespesaService::buscar((int) $id);

        if (!$despesaBruta) {
            abort(404);
        }

        $despesa = DespesaService::normalizarParaView($despesaBruta);
        $despesa['maquina_nome'] = null;

        if ($despesa['id_maquina']) {
            try {
                $maquina = MaquinasService::coletar((string) $despesa['id_maquina']);
                $despesa['maquina_nome'] = $maquina['maquina_nome'] ?? null;
            } catch (\Throwable $e) {
                $despesa['maquina_nome'] = null;
            }
        }

        return view('Financeiro.Despesas.show', compact('despesa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'descricao'    => 'nullable|string|max:255',
            'valor'        => 'required|numeric|min:0.01',
            'data_despesa' => 'required|date',
            'tipo'         => ['nullable', 'string', Rule::in(self::CATEGORIAS)],
            'id_maquina'   => 'nullable|integer',
            'forma_pagamento' => ['nullable', 'string', Rule::in(self::FORMAS_PAGAMENTO)],
            'comprovante'  => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:5120',
        ]);

        $realizadoPor = session('id_usuario') ?? session('usuario_id') ?? auth()->id() ?? '1';

        $resultado = DespesaService::criar([
            'titulo'        => $request->descricao ?: ($request->tipo ?? 'Despesa'),
            'descricao'     => $request->tipo,
            'valor'         => $request->valor,
            'data'          => $request->data_despesa,
            'id_maquina'      => $request->id_maquina,
            'realizado_por'   => (string) $realizadoPor,
            'forma_pagamento' => $request->forma_pagamento,
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
            return redirect()->route('financeiro-despesas')
                ->with('success', 'Despesa removida com sucesso!');
        }

        return back()->with('error', $resultado['message'] ?? 'Houve um erro ao remover a despesa.');
    }
}
