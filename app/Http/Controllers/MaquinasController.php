<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\Hardware\MaquinasService as HardwareMaquinas;
use App\Services\ExtratoMaquinaService;
use App\Services\LiberarJogadaService;
use App\Services\ClientesService;
use App\Services\ClienteLocalService;
use App\Services\AuthService;

class MaquinasController extends Controller
{
    public function coletarMaquinaPorId(Request $request){

        if($request->has('id')){
            $maquinas = MaquinasService::coletar($request->id);
            $id_local = $maquinas['id_local'];
            $locais = LocaisService::coletar($id_local);
            $clienteLocal = ClienteLocalService::coletar();
            $clientes = ClientesService::coletar();

            $clienteLocalFiltrado = array_filter($clienteLocal, function($item) use($id_local){
                return $item['id_local'] == $id_local;
            });
    
            // Extraindo apenas os valores de "id_cliente"
            $idClientes = array_map(function($item) {
                return $item['id_cliente'];
            }, $clienteLocalFiltrado);
    
            $clientes = array_filter($clientes, function($item) use($idClientes){
                return in_array($item['id_cliente'],  $idClientes);
            });
            return view('Admin.Maquinas.show', compact('maquinas', 'locais', 'clientes'));
        }else{
            return back()->with('error', 'Máquina não encontrada');
        }

    }

    public function criarMaquinas(Request $request){
        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();
        $maquinas = MaquinasService::coletarPlacasDisponiveis();
        $localCliente = ClienteLocalService::coletar();
        return view('Admin.Maquinas.create', compact('locais', 'clientes', 'maquinas', 'localCliente'));
    }

    public function registrarMaquinas(Request $request){


        try{

            $dados = [];
            $dados['id_local'] = $request['select-local'];
            $dados['id_placa'] = $request['id_placa'];
            $dados['maquina_nome'] = $request['maquina_nome'];
            $dados['maquina_status'] = 0;

            $result = MaquinasService::criar($dados);

            return back()->with('success', $result['message']);
        }catch(\Throwable $e){
            return back()->with('error', 'Houve um erro ao tentar cadastrar a máquina');
        }
    }

    public function gerarIdPlaca(){
        $maquinas = HardwareMaquinas::coletarPlacasDisponivel();
    	
        if(empty($maquinas)){
            return response()->json(["placas"=>$maquinas], 200); // Correção aqui
        }else{
            return response()->json($maquinas, 200); // Correção aqui
        }
    }

    public function coletarTodasAsMaquinas(Request $request) {
        $locais = LocaisService::coletar();
        $maquinas = MaquinasService::coletar();
        $maquinas_extrato = MaquinasService::coletarTodasAsMaquinasComUltimaTransacao();
    
        // Indexando locais por id_local
        $locais_indexados = [];
        foreach ($locais as $local) {
            $locais_indexados[$local['id_local']] = $local;
        }
    
        // Indexando maquinas por id_maquina
        /*$maquinas_indexadas = [];
        foreach ($maquinas as $maquina) {
            $maquinas_indexadas[$maquina['id_maquina']] = $maquina;
        }
    
        // Array para armazenar o resultado final
        $resultado = [];
    
        return $maquinas_extrato;
        // Percorrendo o extrato para armazenar apenas a última transação de cada máquina
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
    
                    // Armazena ou substitui a última transação da máquina no array de resultados
                    $resultado[$id_maquina] = $extrato_completo;
                }
            }
        }
    
        // Verifica máquinas sem extrato e adiciona com transação zero
        foreach ($maquinas_indexadas as $id_maquina => $maquina) {
            if (!isset($resultado[$id_maquina])) {
                $id_local = $maquina['id_local'];
    
                if (isset($locais_indexados[$id_local])) {
                    $local = $locais_indexados[$id_local];
    
                    $resultado[$id_maquina] = [
                        'id_maquina' => $id_maquina,
                        'extrato_operacao_valor' => 0, // Define a última transação como 0
                        'extrato_operacao' => "C",
                        'extrato_operacao_tipo' => "N/A",
                        'data_criacao' => $maquina['data_criacao'],
                        'maquina' => $maquina,
                        'local' => $local
                    ];
                }
            }
        }*/

        // Se você quiser um array com índices numéricos simples, pode utilizar array_values
        $resultado = array_values($maquinas_extrato);

        
    
        return view('Admin.Maquinas.index', compact('resultado'));
        //return view('Admin.Maquinas.index');
    }

    public function transacaoMaquinas(Request $request){
        return view('Admin.Maquinas.Transacoes.index');
    }

    public function acumuladoMaquinas(Request $request){
        /*$locais = LocaisService::coletar();
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

        }*/

        return view('Admin.Maquinas.Acumulado.index');

    }

    public function excluirMaquinas(Request $request){
        try{

           $id_maquina = $request['id_maquina'];

            $result = MaquinasService::deletar($id_maquina);
    
            return back()->with('success', $result['message']);
        }catch(\Throwable $e){
            return back()->with('error', 'Houve um erro ao tentar remover a máquina');
        }
    }

    public function liberarJogada(Request $request){
        $dados = [
            "id_placa" => $request['select-id-placa'],
            "valor" =>$request['valor_credito'],
            "id_transacao" => "CD" . rand(10000000, 99999999)
        ];
	    $jogada = LiberarJogadaService::criar($dados);
	
        if($jogada['message'] == "Jogada liberada com sucesso"){
            return back()->with('success', "Jogada liberada com sucesso!");
        }else{
            return back()->with('error', 'Houve um erro ao tentar liberar a jogada.');
        }
    }
public function viewLiberarJogada(Request $request){
        $maquinas = MaquinasService::coletar();

        return view('Admin.Jogadas.create', compact("maquinas"));
    }
}
