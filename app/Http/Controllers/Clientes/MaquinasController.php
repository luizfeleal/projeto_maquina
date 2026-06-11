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
use App\Services\QrCodeService;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class MaquinasController extends Controller
{

    public function coletarTodasAsMaquinas(Request $request){
        $id_cliente = session()->get('id_cliente');
        $busca      = trim((string) $request->input('busca', ''));
        $perPage    = 10;
        $page       = max(1, (int) $request->input('page', 1));

        // Fonte de status: mesma lógica da home (MaquinasService + ClienteLocalService)
        $clienteLocal = ClienteLocalService::coletar();
        $clienteLocal = array_filter($clienteLocal, fn($item) => $item['id_cliente'] == $id_cliente);
        $idsLocais    = array_column(array_values($clienteLocal), 'id_local');

        $todasMaquinas = MaquinasService::coletar();
        $maquinasPorId = [];
        foreach ($todasMaquinas as $m) {
            if (in_array($m['id_local'], $idsLocais)) {
                $maquinasPorId[(string) $m['id_maquina']] = $m;
            }
        }

        $qrPorMaquina = [];
        foreach (QrCodeService::coletar() as $qr) {
            if (($qr['ativo'] ?? 0) == 1) {
                $qrPorMaquina[(string) $qr['id_maquina']] = true;
            }
        }

        $listaBase = [];
        foreach ($maquinasPorId as $idMaq => $maq) {
            $listaBase[] = [
                'id_maquina'     => $idMaq,
                'id_local'       => $maq['id_local']       ?? null,
                'id_placa'       => $maq['id_placa']       ?? '—',
                'possui_qr'      => isset($qrPorMaquina[$idMaq]),
                'maquina_nome'   => $maq['maquina_nome']   ?? '',
                'local_nome'     => $maq['local_nome']     ?? '—',
                'maquina_status' => $maq['maquina_status'] ?? 1,
            ];
        }

        if ($busca !== '') {
            $buscaLower = mb_strtolower($busca);
            $listaBase = array_values(array_filter($listaBase, function ($maq) use ($buscaLower) {
                $haystack = mb_strtolower(
                    ($maq['maquina_nome'] ?? '') . ' ' .
                    ($maq['local_nome'] ?? '') . ' ' .
                    ($maq['id_placa'] ?? '')
                );

                return str_contains($haystack, $buscaLower);
            }));
        }

        $totalItens = count($listaBase);
        $offset     = ($page - 1) * $perPage;
        $paginaBase = array_slice($listaBase, $offset, $perPage);

        $idsPagina = array_column($paginaBase, 'id_maquina');
        $acumuladoPorId = [];
        if (!empty($idsPagina)) {
            $acumulado     = ExtratoMaquinaService::coletarAcumulado(['id_cliente' => $id_cliente]);
            $acumuladoData = $acumulado['data'] ?? (is_array($acumulado) ? $acumulado : []);
            foreach ($acumuladoData as $item) {
                $idMaq = (string) ($item['id_maquina'] ?? '');
                if (in_array($idMaq, $idsPagina, true)) {
                    $acumuladoPorId[$idMaq] = $item;
                }
            }
        }

        $maquinas = [];
        foreach ($paginaBase as $maq) {
            $idMaq = (string) $maq['id_maquina'];
            $fin   = $acumuladoPorId[$idMaq] ?? [];
            $maquinas[] = array_merge($maq, [
                'total_maquina'     => $fin['total_maquina']     ?? 0,
                'saldo_periodo'     => $fin['saldo_periodo']     ?? 0,
                'tem_reset'         => $fin['tem_reset']         ?? false,
                'data_ultimo_reset' => $fin['data_ultimo_reset'] ?? null,
            ]);
        }

        $paginator = new LengthAwarePaginator(
            $maquinas,
            $totalItens,
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        $temMaquinas = count($maquinasPorId) > 0;

        return view('Clientes.Maquinas.index', compact('maquinas', 'paginator', 'busca', 'temMaquinas'));
    }

    public function transacaoMaquinas(Request $request){

        $id_cliente    = session()->get('id_cliente');
        $idMaquinaSel  = $request->input('id_maquina');

        // Todas as transações do cliente
        $todasTransacoes = array_values(
            ExtratoMaquinaService::coletarExtratoDasMaquinasDeUmCliente(['id_cliente' => $id_cliente])
        );

        // Acumulado por máquina (usado para o resumo e lista de máquinas do filtro)
        $acumulado     = ExtratoMaquinaService::coletarAcumulado(['id_cliente' => $id_cliente]);
        $maquinasAcum  = $acumulado['data'] ?? (is_array($acumulado) ? $acumulado : []);

        // Lista de máquinas para o filtro (nome + local)
        $listaMaquinas = array_map(fn($m) => [
            'id_maquina'   => $m['id_maquina'],
            'maquina_nome' => $m['maquina_nome'] ?? '—',
            'local_nome'   => $m['local_nome']   ?? '—',
        ], $maquinasAcum);

        // Filtrar transações pela máquina selecionada
        $resultado = $idMaquinaSel
            ? array_values(array_filter($todasTransacoes, fn($tx) => (string)($tx['id_maquina'] ?? '') === (string)$idMaquinaSel))
            : $todasTransacoes;

        // Acumulado filtrado para os cards de resumo
        $maquinasFiltradas = $idMaquinaSel
            ? array_values(array_filter($maquinasAcum, fn($m) => (string)$m['id_maquina'] === (string)$idMaquinaSel))
            : $maquinasAcum;

        // Totais por tipo de pagamento
        $totalPix = $totalCartao = $totalDinheiro = $totalDevolucao = 0.0;
        foreach ($resultado as $tx) {
            $valor = (float)($tx['extrato_operacao_valor'] ?? 0);
            $tipo  = strtolower($tx['extrato_operacao_tipo'] ?? '');
            $op    = $tx['extrato_operacao'] ?? 'C';

            if ($op === 'D') {
                $totalDevolucao += $valor;
            } elseif (str_contains($tipo, 'pix')) {
                $totalPix += $valor;
            } elseif (str_contains($tipo, 'cart')) {
                $totalCartao += $valor;
            } elseif (str_contains($tipo, 'dinheir') || str_contains($tipo, 'físic') || str_contains($tipo, 'fisic')) {
                $totalDinheiro += $valor;
            }
        }

        $resumo = [
            'total_acumulado'  => array_sum(array_column($maquinasFiltradas, 'total_maquina')),
            'total_saldo'      => array_sum(array_column($maquinasFiltradas, 'saldo_periodo')),
            'tem_reset'        => !empty(array_filter(array_column($maquinasFiltradas, 'tem_reset'))),
            'ids_maquinas'     => array_column($maquinasFiltradas, 'id_maquina'),
            'total_pix'        => $totalPix,
            'total_cartao'     => $totalCartao,
            'total_dinheiro'   => $totalDinheiro,
            'total_devolucao'  => $totalDevolucao,
        ];

        return view('Clientes.Maquinas.Transacoes.index', compact('resultado', 'resumo', 'listaMaquinas', 'idMaquinaSel'));
    }

    public function resetParcialTodas(Request $request)
    {
        $id_cliente   = session()->get('id_cliente');
        $realizadoPor = (string) (session('id_usuario') ?? session('usuario_id') ?? '1');

        $ids = $request->input('ids_maquinas', []);

        if (empty($ids)) {
            return back()->with('error', 'Nenhuma máquina encontrada para resetar.');
        }

        $erros   = [];
        $sucesso = 0;

        foreach ($ids as $idMaquina) {
            $resultado = ExtratoMaquinaService::resetParcial((string) $idMaquina, [
                'realizado_por' => $realizadoPor,
                'observacao'    => $request->input('observacao'),
            ]);

            if ($resultado['success'] ?? false) {
                $sucesso++;
            } else {
                $erros[] = $idMaquina;
            }
        }

        if (empty($erros)) {
            return back()->with('success', "Reset parcial registrado com sucesso em {$sucesso} máquina(s).");
        }

        if ($sucesso > 0) {
            return back()->with('error', "Reset aplicado em {$sucesso} máquina(s), mas falhou em " . count($erros) . ' máquina(s).');
        }

        return back()->with('error', 'Houve um erro ao registrar o reset parcial.');
    }

    public function acumuladoMaquinas(Request $request){

        $id_cliente = session()->get('id_cliente');
        return view('Clientes.Maquinas.Acumulado.index', compact('id_cliente'));

    }

    public function resetParcial(Request $request)
    {
        $request->validate(['id_maquina' => 'required']);

        try {
            $idMaquina    = $request->input('id_maquina');
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
            \Log::error('[cliente.resetParcial] Erro: ' . $e->getMessage());
            return back()->with('error', 'Houve um erro ao registrar o reset parcial.');
        }
    }

    public function historicoResets(Request $request)
    {
        $id_cliente = session()->get('id_cliente');

        $filtros = array_filter([
            'id_maquina' => $request->input('id_maquina'),
            'data_inicio' => $request->input('data_inicio'),
            'data_fim'    => $request->input('data_fim'),
            'id_cliente'  => $id_cliente,
            'page'        => $request->input('page', 1),
        ]);

        $resets = ExtratoMaquinaService::historicoResets($filtros);

        return view('Clientes.Maquinas.Acumulado.historico', compact('resets'));
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
            $id_maquina = $request->id_maquina;
            $maquina = MaquinasService::coletar($id_maquina);

            if (!$maquina) {
                return back()->with('error', 'Máquina não encontrada');
            }

            // Validar se a máquina pertence ao cliente
            $id_cliente = session()->get('id_cliente');
            $id_local = $maquina['id_local'];
            $clienteLocal = ClienteLocalService::coletar();
            
            $pertenceAoCliente = array_filter($clienteLocal, function ($item) use ($id_cliente, $id_local) {
                return $item['id_cliente'] == $id_cliente && $item['id_local'] == $id_local;
            });

            if (empty($pertenceAoCliente)) {
                return back()->with('error', 'Você não tem permissão para editar esta máquina');
            }

            $locais = LocaisService::coletar($id_local);
            $clientes = ClientesService::coletar();

            $maquinaCartao = MaquinasCartaoService::coletar();

            $maquinaCartaoAssociada = array_filter($maquinaCartao, function($item) use($id_maquina){
                return $id_maquina == $item['id_maquina'] && $item['status'] == 1;
            });

            $possuiMaquinaCartaoAssociada = !empty($maquinaCartaoAssociada);

            $qr = QrCodeService::coletar();

            $qrMaquina = array_filter($qr, function($item) use($id_maquina) {
                return $item['ativo'] == 1 && $item['id_maquina'] == $id_maquina;
            });

            $possuiQrCode = !empty($qrMaquina);

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

            $maquinas = $maquina;
            return view('Clientes.Maquinas.edit', compact('maquinas', 'locais', 'clientes', 'possuiMaquinaCartaoAssociada', 'possuiQrCode', 'localCliente'));
        } else {
            return back()->with('error', 'ID da máquina não informado');
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
                $maquinaCartao['id'] = $maquinaCartao['id_maquina_cartao'] ?? $maquinaCartao['id'] ?? null;
                $maquinasCartaoFiltradas[] = $maquinaCartao;
            }
        }

        return view('Clientes.Maquinas.MaquinaCartao.index', ['maquinasCartao' => $maquinasCartaoFiltradas]);
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

        return view('Clientes.Maquinas.MaquinaCartao.create', compact('maquinas_exibir'));
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
            
            return redirect()->route('cliente-home')->with('success', $result['message']);
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

    public function excluirMaquinasCartao(Request $request)
    {
        try {
            $id = $request->input('id_device');
            if (!$id) {
                return back()->with('error', 'ID da máquina não informado.');
            }
            $result = MaquinasCartaoService::excluir($id);
            if ($result['success']) {
                return redirect()->route('cliente-maquinas-cartao')->with('success', 'Máquina de cartão excluída com sucesso.');
            }
            return back()->with('error', $result['message'] ?? 'Erro ao excluir a máquina de cartão.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Erro ao excluir a máquina de cartão: ' . $e->getMessage());
        }
    }
}
