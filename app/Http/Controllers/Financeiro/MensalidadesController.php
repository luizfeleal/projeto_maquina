<?php

namespace App\Http\Controllers\Financeiro;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Services\ClientesService;
use App\Services\MensalidadeService;

class MensalidadesController extends Controller
{
    private const STATUS = ['pago', 'pendente', 'atrasado'];

    public function index(Request $request)
    {
        $filtros = array_filter([
            'id_cliente'        => $request->input('id_cliente'),
            'status'            => $request->input('status'),
            'vencimento_inicio' => $request->input('vencimento_inicio'),
            'vencimento_fim'    => $request->input('vencimento_fim'),
        ], fn ($v) => $v !== null && $v !== '');

        $mensalidades = MensalidadeService::listarComCliente($filtros);
        $resumo       = MensalidadeService::resumo($filtros);

        $clientes = collect(ClientesService::coletar())
            ->map(fn ($c) => ['id_cliente' => $c['id_cliente'] ?? null, 'cliente_nome' => $c['cliente_nome'] ?? '—'])
            ->filter(fn ($c) => $c['id_cliente'])
            ->values();

        return view('Financeiro.Mensalidades.index', compact('mensalidades', 'resumo', 'clientes', 'filtros'));
    }

    public function create()
    {
        $clientes = collect(ClientesService::coletar())
            ->map(fn ($c) => ['id_cliente' => $c['id_cliente'] ?? null, 'cliente_nome' => $c['cliente_nome'] ?? '—'])
            ->filter(fn ($c) => $c['id_cliente'])
            ->values();

        $status = self::STATUS;

        return view('Financeiro.Mensalidades.create', compact('clientes', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cliente'   => 'required|integer',
            'valor'        => 'required|numeric|min:0.01',
            'vencimento'   => 'required|date',
            'status'       => ['nullable', Rule::in(self::STATUS)],
            'gerar_boleto' => 'nullable|boolean',
        ]);

        $resultado = MensalidadeService::criar([
            'id_cliente'   => $request->id_cliente,
            'valor'        => $request->valor,
            'vencimento'   => $request->vencimento,
            'status'       => $request->status ?: 'pendente',
            'gerar_boleto' => $request->boolean('gerar_boleto'),
        ]);

        if ($resultado['success'] ?? false) {
            return redirect()->route('financeiro-mensalidades')
                ->with('success', 'Mensalidade cadastrada com sucesso!');
        }

        return back()->withInput()->with('error', $resultado['message'] ?? 'Houve um erro ao cadastrar a mensalidade.');
    }

    public function show($id)
    {
        $mensalidade = MensalidadeService::buscar((int) $id);

        if (!$mensalidade) {
            return redirect()->route('financeiro-mensalidades')
                ->with('error', 'Mensalidade não encontrada.');
        }

        $status = self::STATUS;

        return view('Financeiro.Mensalidades.show', compact('mensalidade', 'status'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'valor'      => 'required|numeric|min:0.01',
            'vencimento' => 'required|date',
            'status'     => ['required', Rule::in(self::STATUS)],
        ]);

        $resultado = MensalidadeService::atualizar((int) $id, [
            'valor'      => $request->valor,
            'vencimento' => $request->vencimento,
            'status'     => $request->status,
        ]);

        if ($resultado['success'] ?? false) {
            return redirect()->route('financeiro-mensalidades-detalhar', $id)
                ->with('success', 'Mensalidade atualizada com sucesso!');
        }

        return back()->withInput()->with('error', $resultado['message'] ?? 'Houve um erro ao atualizar a mensalidade.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $resultado = MensalidadeService::excluir((int) $request->id);

        if ($resultado['success'] ?? false) {
            return redirect()->route('financeiro-mensalidades')
                ->with('success', 'Mensalidade excluída com sucesso!');
        }

        return back()->with('error', $resultado['message'] ?? 'Houve um erro ao excluir a mensalidade.');
    }

    public function gerarBoleto($id)
    {
        $resultado = MensalidadeService::gerarBoleto((int) $id);

        if ($resultado['success'] ?? false) {
            return back()->with('success', 'Boleto gerado com sucesso!');
        }

        return back()->with('error', $resultado['message'] ?? 'Houve um erro ao gerar o boleto.');
    }

    public function cancelarBoleto($id)
    {
        $resultado = MensalidadeService::cancelarBoleto((int) $id);

        if ($resultado['success'] ?? false) {
            return back()->with('success', 'Boleto cancelado com sucesso!');
        }

        return back()->with('error', $resultado['message'] ?? 'Houve um erro ao cancelar o boleto.');
    }

    public function reenviarBoleto(Request $request, $id)
    {
        $request->validate(['email' => 'nullable|email']);

        $resultado = MensalidadeService::reenviarBoleto((int) $id, $request->email);

        if ($resultado['success'] ?? false) {
            return back()->with('success', 'Boleto reenviado com sucesso!');
        }

        return back()->with('error', $resultado['message'] ?? 'Houve um erro ao reenviar o boleto.');
    }
}
