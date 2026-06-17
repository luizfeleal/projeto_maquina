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
use App\Services\ExtratoMaquinaService;
use App\Support\ApiClient;

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
            \Log::error("erro ao registrar a maquina");
            \Log::error($e);
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
        return view('Admin.Maquinas.index');
    }

    public function coletarTodasAsMaquinasDados(Request $request)
    {
        $maquinas = MaquinasService::coletarTodasAsMaquinasComUltimaTransacao();

        if (!is_array($maquinas)) {
            return response()->json([]);
        }

        return response()->json(array_values($maquinas));
    }

    public function transacaoMaquinas(Request $request)
    {
        $clientes     = ClientesService::coletar();
        $locais       = LocaisService::coletar();
        $maquinas     = MaquinasService::coletar();
        $clienteLocal = ClienteLocalService::coletar();

        return view('Admin.Maquinas.Transacoes.index', compact('clientes', 'locais', 'maquinas', 'clienteLocal'));
    }

    public function transacaoMaquinasDados(Request $request)
    {
        $length      = max((int) $request->input('length', 10), 1);
        $start       = (int) $request->input('start', 0);
        $mostrarTaxas = $request->boolean('mostrar_taxas');

        $idCliente = $request->input('id_cliente');
        $idLocal   = $request->input('id_local');
        $idMaquina = $request->input('id_maquina');

        $hasFilter = !empty($idCliente) || !empty($idLocal) || !empty($idMaquina);
        $needsLocalProcessing = $hasFilter || !$mostrarTaxas;

        $params = $this->buildExtratoMaquinaQueryParams($request);

        if ($needsLocalProcessing) {
            $params['start']  = 0;
            $params['length'] = 5000;
        }

        try {
            $response = ApiClient::get('/extratoMaquina', $params);
        } catch (\Throwable $e) {
            \Log::error('[transacaoMaquinasDados] ' . $e->getMessage());

            return response()->json([
                'draw'            => (int) $request->input('draw', 1),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
            ]);
        }

        if (!$response->successful()) {
            \Log::error('[transacaoMaquinasDados] API status ' . $response->status(), [
                'body' => substr($response->body(), 0, 500),
            ]);

            return response()->json([
                'draw'            => (int) $request->input('draw', 1),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
            ]);
        }

        $body = $response->json();
        if (!is_array($body)) {
            return response()->json([
                'draw'            => (int) $request->input('draw', 1),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
            ]);
        }

        $data = $body['data'] ?? [];
        if (!is_array($data)) {
            $data = [];
        }
        $data = array_values(array_filter($data, fn($tx) => is_array($tx)));

        if (!$mostrarTaxas) {
            $data = array_values(array_filter($data, fn($tx) => !$this->isTransacaoTaxa($tx)));
        }

        if ($hasFilter) {
            $maquinasFiltradas = $this->resolveMaquinasFiltradas($idCliente, $idLocal, $idMaquina);

            if (empty($maquinasFiltradas)) {
                return response()->json([
                    'draw'            => (int) $request->input('draw', 1),
                    'recordsTotal'    => 0,
                    'recordsFiltered' => 0,
                    'data'            => [],
                ]);
            }

            $data = array_values(array_filter(
                $data,
                fn($tx) => $this->transacaoCorrespondeMaquinas($tx, $maquinasFiltradas)
            ));
        }

        if ($needsLocalProcessing) {
            $total = count($data);
            $data  = array_slice($data, $start, $length);

            return response()->json([
                'draw'            => (int) $request->input('draw', 1),
                'recordsTotal'    => $body['recordsTotal'] ?? $total,
                'recordsFiltered' => $total,
                'data'            => $data,
            ]);
        }

        return response()->json([
            'draw'            => (int) $request->input('draw', 1),
            'recordsTotal'    => $body['recordsTotal'] ?? count($data),
            'recordsFiltered' => $body['recordsFiltered'] ?? count($data),
            'data'            => $data,
        ]);
    }

    private function isTransacaoTaxa(array $tx): bool
    {
        $tipo = strtolower(trim((string) ($tx['extrato_operacao_tipo'] ?? '')));

        return str_contains($tipo, 'taxa');
    }

    private function resolveMaquinasFiltradas(?string $idCliente, ?string $idLocal, ?string $idMaquina): array
    {
        $maquinas = MaquinasService::coletar();
        if (!is_array($maquinas)) {
            return [];
        }

        $filtradas = array_values($maquinas);

        if ($idMaquina) {
            $filtradas = array_values(array_filter(
                $filtradas,
                fn($m) => (string) ($m['id_maquina'] ?? '') === (string) $idMaquina
            ));
        }

        if ($idLocal) {
            $filtradas = array_values(array_filter(
                $filtradas,
                fn($m) => (string) ($m['id_local'] ?? '') === (string) $idLocal
            ));
        }

        if ($idCliente) {
            $locaisCliente = array_column(
                array_filter(
                    ClienteLocalService::coletar(),
                    fn($cl) => (string) ($cl['id_cliente'] ?? '') === (string) $idCliente
                ),
                'id_local'
            );
            $filtradas = array_values(array_filter(
                $filtradas,
                fn($m) => in_array($m['id_local'] ?? null, $locaisCliente)
            ));
        }

        return $filtradas;
    }

    private function transacaoCorrespondeMaquinas(array $tx, array $maquinasFiltradas): bool
    {
        $txMaquina = trim((string) ($tx['maquina_nome'] ?? ''));
        $txIdMaq   = $tx['id_maquina'] ?? null;

        foreach ($maquinasFiltradas as $maq) {
            $idMaq = $maq['id_maquina'] ?? null;

            if ($txIdMaq !== null && (string) $txIdMaq === (string) $idMaq) {
                return true;
            }

            if ($txMaquina !== '' && strcasecmp($txMaquina, trim((string) ($maq['maquina_nome'] ?? ''))) === 0) {
                return true;
            }
        }

        return false;
    }

    private function buildExtratoMaquinaQueryParams(Request $request): array
    {
        $params = [];

        foreach ($request->query() as $key => $value) {
            if ($key === 'search' && is_array($value)) {
                $params['search'] = $value['value'] ?? '';
                continue;
            }

            if (in_array($key, ['search_value', 'page', 'per_page', 'id_cliente', 'id_local', 'id_maquina', 'mostrar_taxas'], true)) {
                continue;
            }

            $params[$key] = $value;
        }

        if (!array_key_exists('search', $params)) {
            $params['search'] = $request->input('search.value', '');
        }

        return $params;
    }

    public function resetParcial(Request $request)
    {
        $request->validate(['id_maquina' => 'required']);

        try {
            $idMaquina   = $request->input('id_maquina');
            $realizadoPor = session('id_usuario') ?? session('usuario_id') ?? auth()->id() ?? '1';

            $dados = [
                'realizado_por' => (string) $realizadoPor,
                'observacao'    => $request->input('observacao'),
            ];

            $resultado = ExtratoMaquinaService::resetParcial($idMaquina, $dados);

            if ($resultado['success'] ?? false) {
                return back()->with('success', 'Reset parcial registrado com sucesso.');
            }

            return back()->with('error', $resultado['message'] ?? 'Houve um erro ao registrar o reset parcial.');
        } catch (\Throwable $e) {
            \Log::error('[resetParcial] Erro: ' . $e->getMessage());
            return back()->with('error', 'Houve um erro ao registrar o reset parcial.');
        }
    }

    public function historicoResets(Request $request)
    {
        $filtros = array_filter([
            'id_maquina' => $request->input('id_maquina'),
            'data_inicio' => $request->input('data_inicio'),
            'data_fim'    => $request->input('data_fim'),
            'page'        => $request->input('page', 1),
        ]);

        $resets = ExtratoMaquinaService::historicoResets($filtros);

        return view('Admin.Maquinas.Acumulado.historico', compact('resets'));
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

        if (isset($jogada['message']) && $jogada['message'] == "Jogada liberada com sucesso") {
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
                $maquinaCartao['id'] = $maquinaCartao['id_maquina_cartao'] ?? $maquinaCartao['id'] ?? null;
                $maquinasCartaoFiltradas[] = $maquinaCartao;
            }else{
                $maquinaCartao['maquina_nome'] = 'Máquina Removida';
                $maquinaCartao['id'] = $maquinaCartao['id_maquina_cartao'] ?? $maquinaCartao['id'] ?? null;
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

        $maquinasCartao = array_filter($maquinasCartao, function($item) {
            return $item['status'] == 1;
        });

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

    public function editarMaquinas(Request $request)
    {

        if ($request->has('id_maquina')) {
            $id_maquina = $request->id_maquina;
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

            $maquinas = $maquinas;
            return view('Admin.Maquinas.edit', compact('maquinas', 'locais', 'clientes', 'possuiMaquinaCartaoAssociada', 'possuiQrCode', 'localCliente'));
        } else {
            return back()->with('error', 'Máquina não encontrada');
        }
    }

    public function registrarMaquinasCartao(Request $request)
    {
        try {

            $dados = [];
            $dados['id_maquina'] = $request['select-maquina'];
            $dados['device'] = $request['device'];
            $dados['status'] = 1;

            $deviceNumber = $request['device'];

            $maquinasCartaoExistente = MaquinasCartaoService::coletar();

            $maquinasCartaoExistente = array_filter($maquinasCartaoExistente, function($item) use($deviceNumber){
                return $item['device'] == $deviceNumber && $item['status'] == 1;
            });

            if(!empty($maquinasCartaoExistente)){
                return back()->with('error', 'A máquina de cartão escolhida já está associada a uma placa. Caso queira prosseguir, inative essa máquina de cartão e associe novamente');
            }

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
        //try {

            $dados = [];
            $dados['id'] = $request['id_device'];
            $dados['status'] = $request['status'];

            $id_device = $request['id_device'];


            if($request['status'] == 1){
                $maquinasCartaoExistente = MaquinasCartaoService::coletar();

                $maquinasCartaoExistenteAcao = array_filter($maquinasCartaoExistente, function($item) use($id_device){
                    return $item['id'] == $id_device;
                });

                $maquinasCartaoExistenteAcao = array_values($maquinasCartaoExistenteAcao);

                $deviceExistente = $maquinasCartaoExistenteAcao[0]['device'];

                $maquinasCartaoExistente = array_filter($maquinasCartaoExistente, function($item) use($deviceExistente){
                    return $item['device'] == $deviceExistente && $item['status'];
                });

                if(!empty($maquinasCartaoExistente)){
                    return back()->with('error', 'A máquina de cartão escolhida não pode ser ativada. Caso queira prosseguir, inative a máquina de cartão que está ativa e associada.');
                }
            }

            $result = MaquinasCartaoService::atualizar($dados);
            return back()->with('success', $result->message);
        //} catch (\Throwable $e) {
            return back()->with('error', 'Houve um erro ao tentar cadastrar a máquina');
        //}
    }

    public function excluirMaquinasCartao(Request $request)
    {
        try {
            $id = $request->input('id_device');
            if (!$id) {
                return back()->with('error', 'ID da máquina não informado.');
            }
            $result = MaquinasCartaoService::excluir($id);
            if ($result['success']) {
                return redirect()->route('maquinas-cartao')->with('success', 'Máquina de cartão excluída com sucesso.');
            }
            return back()->with('error', $result['message'] ?? 'Erro ao excluir a máquina de cartão.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Erro ao excluir a máquina de cartão: ' . $e->getMessage());
        }
    }
}
