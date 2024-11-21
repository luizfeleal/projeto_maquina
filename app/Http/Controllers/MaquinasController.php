<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\Hardware\MaquinasService as HardwareMaquinas;
use App\Services\MaquinasCartaoService;
use App\Services\LiberarJogadaService;
use App\Services\ClientesService;
use App\Services\ClienteLocalService;
use App\Services\AuthService;
use App\Services\QrCodeService;

class MaquinasController extends Controller
{
    public function coletarMaquinaPorId(Request $request)
    {

        if ($request->has('id')) {
            $id_maquina = $request->id;
            $maquinas = MaquinasService::coletar($id_maquina);
            $id_local = $maquinas['id_local'];
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

            $clienteLocalFiltrado = array_filter($clienteLocal, function ($item) use ($id_local) {
                return $item['id_local'] == $id_local;
            });

            // Extraindo apenas os valores de "id_cliente"
            $idClientes = array_map(function ($item) {
                return $item['id_cliente'];
            }, $clienteLocalFiltrado);

            $clientes = array_filter($clientes, function ($item) use ($idClientes) {
                return in_array($item['id_cliente'],  $idClientes);
            });
            return view('Admin.Maquinas.show', compact('maquinas', 'locais', 'clientes', 'possuiMaquinaCartaoAssociada', 'possuiQrCode'));
        } else {
            return back()->with('error', 'Máquina não encontrada');
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
            return view('Admin.Maquinas.edit', compact('maquinas', 'locais', 'clientes', 'possuiMaquinaCartaoAssociada', 'possuiQrCode', 'localCliente'));
        } else {
            return back()->with('error', 'Máquina não encontrada');
        }
    }

    public function criarMaquinas(Request $request)
    {
        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();
        $maquinas = MaquinasService::coletarPlacasDisponiveis();
        $localCliente = ClienteLocalService::coletar();
        return view('Admin.Maquinas.create', compact('locais', 'clientes', 'maquinas', 'localCliente'));
    }

    public function registrarMaquinas(Request $request)
    {


        try {

            $dados = [];
            $dados['id_local'] = $request['select-local'];
            $dados['id_placa'] = $request['id_placa'];
            $dados['maquina_nome'] = $request['maquina_nome'];
            $dados['maquina_status'] = 0;

            $result = MaquinasService::criar($dados);

            return back()->with('success', $result['message']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Houve um erro ao tentar cadastrar a máquina');
        }
    }

    public function gerarIdPlaca()
    {
        $maquinas = HardwareMaquinas::coletarPlacasDisponivel();

        if (empty($maquinas)) {
            return response()->json(["placas" => $maquinas], 200); // Correção aqui
        } else {
            return response()->json($maquinas, 200); // Correção aqui
        }
    }

    public function coletarTodasAsMaquinas(Request $request)
    {
        $locais = LocaisService::coletar();
        $maquinas = MaquinasService::coletar();
        $maquinas_extrato = MaquinasService::coletarTodasAsMaquinasComUltimaTransacao();

        // Indexando locais por id_local
        $locais_indexados = [];
        foreach ($locais as $local) {
            $locais_indexados[$local['id_local']] = $local;
        }

        // Se você quiser um array com índices numéricos simples, pode utilizar array_values
        $resultado = array_values($maquinas_extrato);



        return view('Admin.Maquinas.index', compact('resultado'));
        //return view('Admin.Maquinas.index');
    }

    public function transacaoMaquinas(Request $request)
    {
        return view('Admin.Maquinas.Transacoes.index');
    }

    public function acumuladoMaquinas(Request $request)
    {
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

    public function excluirMaquinas(Request $request)
    {
        try {

            $id_maquina = $request['id_maquina'];

            $result = MaquinasService::deletar($id_maquina);

            return back()->with('success', $result['message']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Houve um erro ao tentar remover a máquina');
        }
    }

    public function liberarJogada(Request $request)
    {
        $dados = [
            "id_placa" => $request['select-id-placa'],
            "valor" => $request['valor_credito'],
            "id_transacao" => "CD" . rand(10000000, 99999999)
        ];
        $jogada = LiberarJogadaService::criar($dados);

        if ($jogada['message'] == "Jogada liberada com sucesso") {
            return back()->with('success', "Jogada liberada com sucesso!");
        } else {
            return back()->with('error', 'Houve um erro ao tentar liberar a jogada.');
        }
    }
    public function viewLiberarJogada(Request $request)
    {

        if($request->has('id_maquina')){
            $id_maquina = $request->id_maquina;
        }else{
            $id_maquina = null;
        }
        $maquinas = MaquinasService::coletar();

        return view('Admin.Jogadas.create', compact("maquinas", "id_maquina"));
    }

    public function viewMaquinasCartao()
    {
        $maquinas = MaquinasService::coletar();
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
        $maquinas = MaquinasService::coletar();
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
            }
            
            if (array_key_exists('bloqueio_jogada_pagbank', $dados)) {
                if($dados['bloqueio_jogada_pagbank'] == "on"){
                    $dados_maquina['bloqueio_jogada_pagbank'] = 1;
                }else{
                    $dados_maquina['bloqueio_jogada_pagbank'] = 0;
                }
            }

            $result = MaquinasService::atualizar($dados, $id_maquina);

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
