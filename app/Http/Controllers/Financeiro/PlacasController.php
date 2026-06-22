<?php

namespace App\Http\Controllers\Financeiro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Placa;

class PlacasController extends Controller
{
    public function index()
    {
        $placas = Placa::orderByDesc('created_at')->get();
        return view('Financeiro.Placas.index', compact('placas'));
    }

    public function create()
    {
        return view('Financeiro.Placas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'placa_serial' => 'required|string|max:255|unique:placas,placa_serial',
            'id_maquina'   => 'nullable|string|max:100',
        ]);

        Placa::create([
            'placa_serial' => strtoupper(trim($request->placa_serial)),
            'id_maquina'   => $request->id_maquina,
        ]);

        return redirect()->route('financeiro-placas')
                         ->with('success', 'Placa registrada com sucesso!');
    }

    public function destroy(Request $request)
    {
        Placa::findOrFail($request->id)->delete();
        return back()->with('success', 'Placa removida com sucesso!');
    }
}
