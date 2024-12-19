<?php

namespace App\Http\Controllers\Clientes;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\ClienteLocalService;
use App\Services\MaquinasCartaoService;
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

    public function viewLiberarJogada(Request $request){
        $id_cliente = session()->get('id_cliente');

        if($request->has('id_maquina')){
            $id_maquina = $request->id_maquina;
        }else{
            $id_maquina = null;
        }
        $maquinas = MaquinasService::coletar();
        $localCliente = ClienteLocalService::coletar();

        $locaisPermitidos = array_filter($localCliente, function ($local) use ($id_cliente) {
            return $local['id_cliente'] == $id_cliente;
        });
        
        $idsLocaisPermitidos = array_column($locaisPermitidos, 'id_local');
        
        $maquinas = array_filter($maquinas, function ($maquina) use ($idsLocaisPermitidos) {
            return in_array($maquina['id_local'], $idsLocaisPermitidos);
        });

        return view('Clientes.Jogadas.create', compact("maquinas", "id_maquina"));
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

    public function editarMaquinas(Request $request)
    {

        if ($request->has('id_maquina')) {
            $id_maquina = $request->id;
            $maquinas = MaquinasService::coletar($id_maquina);
            $id_local = $maquinas[0]['id_local'];
            $locais = LocaisService::coletar($id_local);
            $clienteLocal = ClienteLocalService::coletar();
            $clientes = ClientesService::coletar();

            $maquinaCartao = MaquinasCartaoService::coletar();

            $maquinaCartaoAssociada = array_filter($maquinaCartao, function($item) use($id_maquina){
                return $id_maquina == $item['id_maquina'] && $item['status'] == 1;
            });

            if(empty($maquinaCartaoAssociada)){
                $possuiMaquinaCartaoAssociada = false;
            }else{
                $possuiMaquinaCartaoAssociada = true;
            }

            $qr = QrCodeService::coletar();

            $qrMaquina = array_filter($qr, function($item) use($id_maquina) {
                return $item['ativo'] == 1 && $item['id_maquina'] == $id_maquina;
            });

            if(empty($qrMaquina)){
                $possuiQrCode = false;
            }else{
                $possuiQrCode = true;
            }

            $localCliente = array_filter($clienteLocal, function ($item) use ($id_local) {
                return $item['id_local'] == $id_local;
            });

            // Extraindo apenas os valores de "id_cliente"
            $idClientes = array_map(function ($item) {
                return $item['id_cliente'];
            }, $localCliente);

            $clientes = array_filter($clientes, function ($item) use ($idClientes) {
                return in_array($item['id_cliente'],  $idClientes);
            });

            $maquinas = $maquinas[0];
            return view('Admin.Maquinas.edit', compact('maquinas', 'locais', 'clientes', 'possuiMaquinaCartaoAssociada', 'possuiQrCode', 'localCliente'));
        } else {
            return back()->with('error', 'Máquina não encontrada');
        }
    }


    public function viewMaquinasCartao()
    {
        $id_cliente = session()->get('id_cliente');
        $clienteLocal = ClienteLocalService::coletar();
        $clienteLocal = array_filter($clienteLocal, function($item) use($id_cliente){
            return $item['id_cliente'] == $id_cliente;
        });
        
        $clienteLocal = collect($clienteLocal)->pluck('id_local')->toArray();
        
        $maquinas = MaquinasService::coletar();

        $maquinas = array_filter($maquinas, function($item) use($clienteLocal){
            return in_array($item['id_local'], $clienteLocal);
        });
        $maquinasCartao = MaquinasCartaoService::coletar();

        $maquinasIndexadas = [];
        foreach ($maquinas as $maquina) {
            $maquinasIndexadas[$maquina['id_maquina']] = $maquina;
        }

        $maquinasCartaoFiltradas = [];
        foreach ($maquinasCartao as $maquinaCartao) {
            if (isset($maquinasIndexadas[$maquinaCartao['id_maquina']])) {
                $maquinaCartao['maquina_nome'] = $maquinasIndexadas[$maquinaCartao['id_maquina']]['maquina_nome'];
                $maquinasCartaoFiltradas[] = $maquinaCartao;
            }
        }

        return view('Admin.Maquinas.MaquinaCartao.index', ['maquinasCartao' => $maquinasCartaoFiltradas]);
    }
    public function viewMaquinasCartaoCriar()
    {
        $id_cliente = session()->get('id_cliente');
        $clienteLocal = ClienteLocalService::coletar();
        $clienteLocal = array_filter($clienteLocal, function($item) use($id_cliente){
            return $item['id_cliente'] == $id_cliente;
        });
        
        $clienteLocal = collect($clienteLocal)->pluck('id_local')->toArray();
        
        $maquinas = MaquinasService::coletar();

        $maquinas = array_filter($maquinas, function($item) use($clienteLocal){
            return in_array($item['id_local'], $clienteLocal);
        });

        $maquinasCartao = MaquinasCartaoService::coletar();

        $id_maquinas_com_cartao = [];

        foreach ($maquinasCartao as $item) {
            array_push($id_maquinas_com_cartao, $item['id_maquina']);
        }

        $maquinas_exibir = [];

        foreach ($maquinas as $maquina) {
            if (!in_array($maquina['id_maquina'], $id_maquinas_com_cartao)) {
                array_push($maquinas_exibir, $maquina);
            }
        }

        return view('Admin.Maquinas.MaquinaCartao.create', compact('maquinas_exibir'));
    }

    public function registrarMaquinasCartao(Request $request)
    {
        try {

            $dados = [];
            $dados['id_maquina'] = $request['select-maquina'];
            $dados['device'] = $request['device'];
            $dados['status'] = 1;

            $result = MaquinasCartaoService::criar($dados);

            return back()->with('success', $result['message']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Houve um erro ao tentar cadastrar a máquina');
        }
    }

    public function atualizarMaquina(Request $request)
    {
        try {
            $dados = $request->all();
            $dados_maquina =  $request->except('_token', 'id_maquina');
            $id_maquina = $request['id_maquina'];

            if (array_key_exists('bloqueio_jogada_efi', $dados)) {
                if($dados['bloqueio_jogada_efi'] == "on"){
                    $dados_maquina['bloqueio_jogada_efi'] = 1;
                }else{
                    $dados_maquina['bloqueio_jogada_efi'] = 0;
                }
            }else{
                $dados_maquina['bloqueio_jogada_efi'] = 0;
            }
            
            if (array_key_exists('bloqueio_jogada_pagbank', $dados)) {
                if($dados['bloqueio_jogada_pagbank'] == "on"){
                    $dados_maquina['bloqueio_jogada_pagbank'] = 1;
                }else{
                    $dados_maquina['bloqueio_jogada_pagbank'] = 0;
                }
            }else{
                $dados_maquina['bloqueio_jogada_pagbank'] = 0;
            }

            $result = MaquinasService::atualizar($dados_maquina, $id_maquina);
            
            return back()->with('success', $result['message']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Houve um erro ao tentar atualizar a máquina');
        }
    }

    public function inativarMaquinasCartao(Request $request)
    {
        try {

            $dados = [];
            $dados['id'] = $request['id_device'];
            $dados['status'] = $request['status'];

            $result = MaquinasCartaoService::atualizar($dados);
            return back()->with('success', $result->message);
        } catch (\Throwable $e) {
            return back()->with('error', 'Houve um erro ao tentar cadastrar a máquina');
        }
    }
}
