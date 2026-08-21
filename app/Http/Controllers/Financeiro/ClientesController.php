<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Services\ClienteRegistroService;
use App\Services\ClientesService;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        $clientes = collect(ClientesService::coletar())
            ->sortBy('cliente_nome')
            ->values();

        return view('Financeiro.Clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('Financeiro.Clientes.create', ClienteRegistroService::dadosFormulario());
    }

    public function store(Request $request)
    {
        $resultado = ClienteRegistroService::registrar($request);

        if (!($resultado['success'] ?? false)) {
            return back()->withInput()->with('error', $resultado['message'] ?? 'Houve um erro ao tentar cadastrar o cliente com os dados prechidos!');
        }

        if (!empty($resultado['warning'])) {
            return redirect()->route('financeiro-clientes')->with('warning', $resultado['warning']);
        }

        return redirect()->route('financeiro-clientes')->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function edit($id)
    {
        return view('Financeiro.Clientes.edit', ClienteRegistroService::dadosEdicao((int) $id));
    }

    public function update(Request $request, $id)
    {
        $resultado = ClienteRegistroService::atualizar($request, (int) $id);

        if (!($resultado['success'] ?? false)) {
            return back()->withInput()->with('error', $resultado['message'] ?? 'Houve um erro ao tentar atualizar o cliente.');
        }

        if (!empty($resultado['warning'])) {
            return redirect()->route('financeiro-clientes')->with('warning', $resultado['warning']);
        }

        return redirect()->route('financeiro-clientes')->with('success', 'Cliente atualizado com sucesso!');
    }
}
