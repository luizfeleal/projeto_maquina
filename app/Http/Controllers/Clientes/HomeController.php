<?php

namespace App\Http\Controllers\Clientes;

use App\Models\ExtratoMaquina;
use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\ClienteLocalService;
use App\Services\QrCodeService;
use App\Services\AuthService;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function coletar(Request $request)
    {
        $id_cliente      = session()->get('id_cliente');
        $idMaquinaFiltro = $request->input('id_maquina');

        $saldo      = ExtratoMaquinaService::coletarSaldoTotal($id_cliente);
        $devolucoes = ExtratoMaquinaService::coletarDevolucoes($id_cliente);

        $cliente_local = array_filter(ClienteLocalService::coletar(), fn($item) => $item['id_cliente'] == $id_cliente);
        $ids_locais    = collect($cliente_local)->pluck('id_local');
        $ids_locais_arr = $ids_locais->toArray();

        $maquinas = array_values(array_filter(MaquinasService::coletar(), fn($item) => in_array($item['id_local'], $ids_locais_arr)));
        $maquinas_ids = collect($maquinas)->pluck('id_maquina')->toArray();

        $maquinas_online = array_values(array_filter($maquinas, fn($item) => $item['maquina_status'] == 1));
        $maquinas_offline = array_values(array_filter($maquinas, fn($item) => $item['maquina_status'] == 0));

        $locais = array_values(array_filter(LocaisService::coletar(), fn($item) => in_array($item['id_local'], $ids_locais_arr)));
        $locaisPorId = [];
        foreach ($locais as $local) {
            $locaisPorId[$local['id_local']] = $local['local_nome'] ?? '—';
        }
        $maquinasRelatorio = $maquinas;

        // Máquinas enriquecidas (status + financeiro + QR)
        $acumulado = ExtratoMaquinaService::coletarAcumulado([
            'id_cliente' => $id_cliente,
            'length'     => 5000,
            'start'      => 0,
            'order'      => [['column' => 4, 'dir' => 'desc']],
        ]);
        $acumuladoData = $acumulado['data'] ?? (is_array($acumulado) ? $acumulado : []);
        $acumuladoPorId = [];
        foreach ($acumuladoData as $item) {
            $acumuladoPorId[(string) $item['id_maquina']] = $item;
        }

        $qrPorMaquina = [];
        foreach (QrCodeService::coletar() as $qr) {
            if (!is_array($qr) || !isset($qr['id_maquina'])) {
                continue;
            }
            if (($qr['ativo'] ?? 0) == 1) {
                $qrPorMaquina[(string) $qr['id_maquina']] = true;
            }
        }

        $maquinasDashboard = [];
        foreach ($maquinas as $maq) {
            $idMaq   = (string) $maq['id_maquina'];
            $fin     = $acumuladoPorId[$idMaq] ?? [];
            $idLocal = $maq['id_local'] ?? ($fin['id_local'] ?? null);
            $localNome = trim((string) ($maq['local_nome'] ?? ($fin['local_nome'] ?? '')));
            if ($localNome === '' && $idLocal !== null && isset($locaisPorId[$idLocal])) {
                $localNome = (string) $locaisPorId[$idLocal];
            }
            if ($localNome === '') {
                $localNome = '—';
            }

            $maquinasDashboard[] = array_merge($fin, [
                'id_maquina'        => $idMaq,
                'id_local'          => $idLocal,
                'possui_qr'         => isset($qrPorMaquina[$idMaq]),
                'maquina_nome'      => $maq['maquina_nome'] ?? ($fin['maquina_nome'] ?? ''),
                'local_nome'        => $localNome,
                'id_placa'          => $maq['id_placa'] ?? '—',
                'maquina_status'    => $maq['maquina_status'] ?? 1,
                'total_maquina'     => $fin['total_maquina'] ?? 0,
                'saldo_periodo'     => $fin['saldo_periodo'] ?? 0,
                'tem_reset'         => $fin['tem_reset'] ?? false,
                'data_ultimo_reset' => $fin['data_ultimo_reset'] ?? null,
            ]);
        }

        $listaMaquinas = array_map(fn($m) => [
            'id_maquina'   => $m['id_maquina'],
            'maquina_nome' => $m['maquina_nome'] ?? '—',
            'local_nome'   => $m['local_nome'] ?? '—',
        ], $maquinasDashboard);

        // Transações recentes + totais por tipo
        $todasTransacoes = array_values(
            ExtratoMaquinaService::coletarExtratoDasMaquinasDeUmCliente(['id_cliente' => $id_cliente])
        );

        $transacoesFiltradas = $idMaquinaFiltro
            ? array_values(array_filter($todasTransacoes, fn($tx) => (string)($tx['id_maquina'] ?? '') === (string)$idMaquinaFiltro))
            : $todasTransacoes;

        usort($transacoesFiltradas, fn($a, $b) => strtotime($b['data_criacao'] ?? 0) - strtotime($a['data_criacao'] ?? 0));
        $ultimasTransacoes = array_slice($transacoesFiltradas, 0, 15);

        $dadosGrafico = [];
        foreach ($todasTransacoes as $tx) {
            $valor = (float)($tx['extrato_operacao_valor'] ?? 0);
            $tipo  = strtolower($tx['extrato_operacao_tipo'] ?? '');
            $op    = $tx['extrato_operacao'] ?? 'C';
            $data  = $tx['data_criacao'] ?? null;
            if (!$data || $op === 'D') continue;
            $ts = strtotime($data);
            if (!$ts) continue;
            $ano = (int) date('Y', $ts);
            $mes = (int) date('n', $ts);
            if (!isset($dadosGrafico[$ano])) {
                for ($i = 1; $i <= 12; $i++) {
                    $dadosGrafico[$ano][$i] = ['pix' => 0.0, 'cartao' => 0.0, 'dinheiro' => 0.0];
                }
            }
            if (str_contains($tipo, 'pix')) {
                $dadosGrafico[$ano][$mes]['pix'] += $valor;
            } elseif (str_contains($tipo, 'cart')) {
                $dadosGrafico[$ano][$mes]['cartao'] += $valor;
            } elseif (str_contains($tipo, 'dinheir') || str_contains($tipo, 'físic') || str_contains($tipo, 'fisic')) {
                $dadosGrafico[$ano][$mes]['dinheiro'] += $valor;
            }
        }
        krsort($dadosGrafico);

        $totalPix = $totalCartao = $totalDinheiro = $totalDevolucao = 0.0;
        foreach ($transacoesFiltradas as $tx) {
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

        $maquinasFiltradasAcum = $idMaquinaFiltro
            ? array_values(array_filter($maquinasDashboard, fn($m) => (string)$m['id_maquina'] === (string)$idMaquinaFiltro))
            : $maquinasDashboard;

        $resumoFinanceiro = [
            'total_acumulado' => array_sum(array_column($maquinasFiltradasAcum, 'total_maquina')),
            'total_saldo'     => array_sum(array_column($maquinasFiltradasAcum, 'saldo_periodo')),
            'total_pix'       => $totalPix,
            'total_cartao'    => $totalCartao,
            'total_dinheiro'  => $totalDinheiro,
            'total_devolucao' => $totalDevolucao,
        ];

        return view('Clientes.home', compact(
            'maquinas', 'maquinas_online', 'maquinas_offline',
            'saldo', 'devolucoes', 'locais', 'maquinasRelatorio',
            'maquinasDashboard', 'ultimasTransacoes', 'resumoFinanceiro',
            'listaMaquinas', 'idMaquinaFiltro', 'dadosGrafico'
        ));
    }

    public function registrarLocais(Request $request){

        try{
            $clientes = $request['select-cliente'];
            $dados = [];
            $dados['local_nome'] = $request['nome_local'];
            $local = LocaisService::criar($dados);
            $id_local = $local['response']['id_local'];
            foreach($clientes as $index => $cliente){
                $dadosClienteLocal = [];
                $dadosClienteLocal['id_cliente'] = $cliente;
                $dadosClienteLocal['id_local'] = $id_local;
                $dadosClienteLocal['cliente_local_principal'] = $index == 0 ? 1 : 0;
                ClienteLocalService::criar($dadosClienteLocal);

            }

            return back()->with('success', 'Local cadastrado com sucesso!');
        }catch(\Throwable $e){
            return back()->with('error', 'Houve um erro ao tentar cadastrar o local');
        }
    }

    public function coletarLocaisPorId($id){
        $local = LocaisService::coletar($id);

        if(empty($local)){
            return back()->with('error', 'Local não encontrado!');
        }
        $clienteLocal = ClienteLocalService::coletar();
        $clientes= ClientesService::coletar();
        $maquinas = MaquinasService::coletar();

        
        $maquinasFiltradas = array_filter($maquinas, function($item) use($id){
            return $item['id_local'] == $id;
        });
        
        $clienteLocalFiltrado = array_filter($clienteLocal, function($item) use($id){
            return $item['id_local'] == $id;
        });
        
        
        
        
        // Extraindo apenas os valores de "id_cliente"
        $idClientes = array_map(function($item) {
            return $item['id_cliente'];
        }, $clienteLocalFiltrado);
        
        $clienteFiltrado = array_filter($clientes, function($item) use($idClientes){
            return in_array($item['id_cliente'],  $idClientes);
        });


        return view('Admin.Local.show', compact('clienteFiltrado', 'local'));
    }

    public function coletarLocais(Request $request){
        if($request->has('id')){
            $locais = LocaisService::coletar($request->id);
        }else{
            $locais = LocaisService::coletar();
            $clientes = ClientesService::coletar();
            $clientesPorId = collect($clientes)->keyBy('id_cliente')->toArray();
            $clienteLocal = collect(ClienteLocalService::coletar())->keyBy('id_local')->toArray();
            $maquinas = MaquinasService::coletar();
            $maquinas_extrato = ExtratoMaquinaService::coletar();
        
	                
            $locais_indexados = [];
        foreach ($locais as &$local) {
            
            $id_local = $local['id_local'];
            $clienteId = $clienteLocal[$local['id_local']]['id_cliente'] ?? null;
            $nome_local = isset($clientesPorId[$clienteId]['cliente_nome']) ? $clientesPorId[$clienteId]['cliente_nome'] : '';
            $local['cliente_nome'] = $nome_local;
            $maquinasDoLocal = array_filter($maquinas, function($item) use($id_local){
                return $item['id_local'] == $id_local;
            });
            $local['qtde_maquinas'] = count($maquinasDoLocal);
            $locais_indexados[$local['id_local']] = $local;
        }

            
            
        return view('Admin.Local.index', compact( 'locais', 'clientes', 'maquinas'));
        }

    }

    public function incluirUsuarioLocal(){
        $locais = LocaisService::coletar();
        $clientes = ClientesService::coletar();
        $cliente_local = ClienteLocalService::coletar();

        return view('Admin.Local.Usuarios.create', compact('locais', 'clientes', 'cliente_local'));
    }

    public function registrarUsuarioLocal(Request $request){
        $clientes = $request['select-cliente'];

        $local = $request['select-local'];


        foreach($clientes as $cliente){
            $localCliente = ClienteLocalService::coletar();

            $localEncontrado = array_filter($localCliente, function($item) use($cliente, $local){
                return $item['id_cliente'] == $cliente && $item['id_local'] == $local;
            });

            
            if(empty($localEncontrado)){

                ClienteLocalService::criar(["id_cliente" => $cliente, "id_local"=>$local]);
            }
            //}
        }

        return back()->with("success", "Cliente(s) incluso com sucesso!");
    }

    public function excluirLocais(Request $request){
        try{

             $id_local = $request['id_local'];
             $maquinas = MaquinasService::coletarComFiltro(["id_local"=>$id_local],'where');

             if(!empty($maquinas)){
                return back()->with('error', 'O local não pôde ser removido pois há máquina(s) associada(s) à ele.');
             }
 
             $result = LocaisService::deletar($id_local);
             $clienteLocalService = ClienteLocalService::coletar();
             $clienteLocalService = array_filter($clienteLocalService, function($item) use($id_local){
                return $item['id_local'] == $id_local;
             });
             foreach($clienteLocalService as $associacao){
                 ClienteLocalService::deletar($associacao['id_cliente_local']);
             }
             return back()->with('success', $result['message']);
         }catch(\Throwable $e){
             return back()->with('error', 'Houve um erro ao tentar remover o local');
         }
    }
}
