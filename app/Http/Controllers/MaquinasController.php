<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\AuthService;

class MaquinasController extends Controller
{
    public function coletarMaquinaPorId(Request $request){

        if($request->has('id')){
            $maquinas = MaquinasService::coletarMaquinas($request->id);
            return view('Maquinas.index', compact('maquinas'));
        }else{
            return back()->with('error', 'Máquina não encontrada');
        }

    }

    public function criarMaquinas(Request $request){
        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();

        return view('Maquinas.create', compact('locais', 'clientes'));
    }

    public function registrarMaquinas(Request $request){


        try{

            $dados = [];
            $dados['id_local'] = $request['select-local'];
            $dados['id_placa'] = $request['id_placa_input'];
            $dados['maquina_nome'] = $request['maquina_nome'];
            $dados['maquina_status'] = 0;

            $result = MaquinasService::criar($dados);

    
            return back()->with('success', $result['message']);
        }catch(\Throwable $e){
            return back()->with('error', 'Houve um erro ao tentar cadastrar o local');
        }
    }

    public function gerarIdPlaca(){
        $id_aleatorio = rand(10000000, 99999999);
        $maquinas = MaquinasService::coletarComFiltro(['id_placa' => $id_aleatorio], 'where');
    
        if(empty($maquinas)){
            return response()->json(["id_placa"=>$id_aleatorio], 200); // Correção aqui
        }else{
            return response()->json($maquinas, 200); // Correção aqui
        }
    }

    public function coletarTodasAsMaquinas(Request $request){
        $maquinas = MaquinasService::coletar();
        return view('Maquinas.index', compact('maquinas'));
    }

    public function transacaoMaquinas(Request $request){
        $locais = LocaisService::coletar();
        $maquinas = MaquinasService::coletar();
        $maquinas_extrato = ExtratoMaquinaService::coletar();

        // Indexando locais por id_local
        $locais_indexados = [];
        foreach ($locais as $local) {
            $locais_indexados[$local['id_local']] = $local;
        }

        // Indexando maquinas por id_maquina
        $maquinas_indexadas = [];
        foreach ($maquinas as $maquina) {
            $maquinas_indexadas[$maquina['id_maquina']] = $maquina;
        }

        $resultado = [];
        foreach ($maquinas_extrato as $extrato) {
            $id_maquina = $extrato['id_maquina'];
            
            // Verifica se a máquina existe
            if (isset($maquinas_indexadas[$id_maquina])) {
                $maquina = $maquinas_indexadas[$id_maquina];
                $id_local = $maquina['id_local'];

                // Verifica se o local existe
                if (isset($locais_indexados[$id_local])) {
                    $local = $locais_indexados[$id_local];
                    
                    // Combina as informações
                    $extrato_completo = $extrato;
                    $extrato_completo['maquina'] = $maquina;
                    $extrato_completo['local'] = $local;
                    $resultado[] = $extrato_completo;
                }
            }
        }
        return view('Maquinas.Transacoes.Index', compact('resultado'));
    }

    public function acumuladoMaquinas(Request $request){
        $locais = LocaisService::coletar();
        $maquinas = MaquinasService::coletar();

        $maquinas_extrato = ExtratoMaquinaService::coletar();

        $locais_indexados = [];
        foreach ($locais as $local) {
            $locais_indexados[$local['id_local']] = $local;
        }

        foreach($maquinas as &$maquina){
            $total_pix = 0;
            $total_cartao = 0;
            $total_dinheiro = 0;
            $total_maquina = 0;

            $extrato_por_maquina = array_filter($maquinas_extrato, function($item) use($maquina){
                return $item['id_maquina'] == $maquina['id_maquina'];
            });



            foreach($extrato_por_maquina as $em){
                $total_maquina += $em['extrato_operacao_valor'];
                if($em['extrato_operacao_tipo'] == "PIX"){
                    $total_pix += $em['extrato_operacao_valor'];
                } else if($em['extrato_operacao_tipo'] == "Cartão"){
                    $total_cartao += $em['extrato_operacao_valor'];
                }else if($em['extrato_operacao_tipo'] == "Dinheiro"){
                    $total_dinheiro += $em['extrato_operacao_valor'];
                }
            }
            $maquina['total_pix'] = $total_pix;
            $maquina['total_cartao'] = $total_cartao;
            $maquina['total_dinheiro'] = $total_dinheiro;
            $maquina['total_maquina'] = $total_maquina;
            $maquina['local_nome'] = $locais_indexados[$maquina['id_local']]['local_nome'];

        }

        return view('Maquinas.Acumulado.Index', compact('maquinas'));

    }
}
