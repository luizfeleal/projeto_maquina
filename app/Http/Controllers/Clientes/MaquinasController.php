<?php

namespace App\Http\Controllers\Clientes;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\ClienteLocalService;
use App\Services\LiberarJogadaService;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use Exception;

class MaquinasController extends Controller
{

    public function coletarTodasAsMaquinas(Request $request){
        $id_cliente = session()->get('id_cliente');

             //$clienteLocal = ClienteLocalService::coletarComFiltro(['id_cliente' => $id_cliente], 'where');
             /*$clienteLocal = ClienteLocalService::coletar();

             $clienteLocal = array_filter($clienteLocal, function($item) use($id_cliente){
                return $item['id_cliente'] == $id_cliente;
             });

             
             $idLocais = array_column($clienteLocal, 'id_local');


        $locais = LocaisService::coletar();*/
        //$maquinas = MaquinasService::coletarTodasAsMaquinasComUltimaTransacao();
        $maquinas = ExtratoMaquinaService::coletarExtratoDasMaquinasDeUmCliente(["id_cliente" => $id_cliente]);
        //$maquinas_extrato = ExtratoMaquinaService::coletar();

        // Indexando locais por id_local
        /*$locais_indexados = [];
        foreach ($locais as $local) {
            $locais_indexados[$local['id_local']] = $local;
        }

        
        $locais_indexados = array_filter($locais_indexados, function($item) use($idLocais){
            return in_array($item['id_local'], $idLocais);
        });
        
        $maquinas = array_filter($maquinas, function($item) use($idLocais){
            return in_array($item['id_local'], $idLocais);
        });
        
        // Indexando maquinas por id_maquina
        $maquinas_indexadas = [];
        foreach ($maquinas as $maquina) {
            $maquinas_indexadas[$maquina['id_maquina']] = $maquina;
        }*/
        
        // Array para armazenar o resultado final
        //$resultado = [];
        
        // Percorrendo o extrato para armazenar apenas a última transação de cada máquina
        
            /*foreach ($maquinas_extrato as $extrato) {
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
            }*/
        
        // Verifica máquinas sem extrato e adiciona com transação zero
        /*foreach ($maquinas_indexadas as $id_maquina => $maquina) {
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

        $resultado = array_values($maquinas);

        return view('Clientes.Maquinas.index', compact('resultado'));
    }

    public function transacaoMaquinas(Request $request){

        $id_cliente = session()->get('id_cliente');
/*
        $maquinas = ExtratoMaquinaService::coletarTotalTransacaoDasMaquinasDeUmCliente(["id_cliente" => $id_cliente]);
        $locais = LocaisService::coletar();
        
        $id_cliente = session()->get('id_cliente');

        $clienteLocal = ClienteLocalService::coletar();

             $clienteLocal = array_filter($clienteLocal, function($item) use($id_cliente){
                return $item['id_cliente'] == $id_cliente;
             });
             $idLocais = array_column($clienteLocal, 'id_local');

        $maquinas = MaquinasService::coletar();
        $maquinas_extrato = ExtratoMaquinaService::coletar();

        // Indexando locais por id_local
        $locais_indexados = [];
        foreach ($locais as $local) {
            $locais_indexados[$local['id_local']] = $local;
        }

        $locais_indexados = array_filter($locais_indexados, function($item) use($idLocais){
            return in_array($item, $idLocais);
        });

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
        }*/
        return view('Clientes.Maquinas.Transacoes.index', compact('id_cliente'));
    }

    public function acumuladoMaquinas(Request $request){

        $id_cliente = session()->get('id_cliente');
        return view('Clientes.Maquinas.Acumulado.index', compact('id_cliente'));

    }

    public function viewLiberarJogada(){
        $id_cliente = session()->get('id_cliente');

        $maquinas = MaquinasService::coletar();
        $localCliente = ClienteLocalService::coletar();

        $locaisPermitidos = array_filter($localCliente, function ($local) use ($id_cliente) {
            return $local['id_cliente'] == $id_cliente;
        });
        
        $idsLocaisPermitidos = array_column($locaisPermitidos, 'id_local');
        
        $maquinas = array_filter($maquinas, function ($maquina) use ($idsLocaisPermitidos) {
            return in_array($maquina['id_local'], $idsLocaisPermitidos);
        });

        return view('Clientes.Jogadas.create', compact('maquinas'));
    }

    public function liberarJogada(Request $request){
        try{

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
        }catch(Exception $e){
            return back()->with('error', 'Houve um erro ao tentar se comunicar com a máquina e liberar a jogada.');
        }
    }
}
