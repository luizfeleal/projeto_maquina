<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\ClienteLocalService;
use App\Services\AuthService;

class HomeController extends Controller
{
    public function coletar(Request $request)
    {
        $idMaquinaFiltro = $request->input('id_maquina');

        // Resumo consolidado: 1 chamada à API em vez das 7 sequenciais de antes
        // (saldo, devolucoes, maquinas, locais, clientes, acumulado, qrcode),
        // com os totais de transações já agregados no banco.
        $resumo = ExtratoMaquinaService::coletarResumoHome($idMaquinaFiltro);

        $saldo      = $resumo['saldo'] ?? ['hoje' => 0, 'mes_atual' => 0, 'mes_passado' => 0];
        $devolucoes = $resumo['devolucoes'] ?? ['hoje' => 0, 'mes_atual' => 0, 'mes_passado' => 0];
        $maquinas   = $resumo['maquinas'] ?? [];
        $locais     = $resumo['locais'] ?? [];
        $clientes   = $resumo['clientes'] ?? [];

        $locaisPorId = [];
        foreach ($locais as $local) {
            $locaisPorId[$local['id_local']] = $local['local_nome'] ?? '—';
        }

        $maquinas         = array_values($maquinas);
        $maquinas_online  = array_values(array_filter($maquinas, fn($item) => $item['maquina_status'] == 1));
        $maquinas_offline = array_values(array_filter($maquinas, fn($item) => $item['maquina_status'] == 0));
        $maquinasRelatorio = $maquinas;

        $acumuladoData = $resumo['acumulado'] ?? [];
        $acumuladoPorId = [];
        foreach ($acumuladoData as $item) {
            $acumuladoPorId[(string) $item['id_maquina']] = $item;
        }

        $qrPorMaquina = [];
        foreach ($resumo['qr_codes'] ?? [] as $qr) {
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

        // Antes: baixava TODAS as transações (coletarTudo, em lotes paginados) e
        // somava/agrupava em PHP. Agora a API já devolve as últimas 15 prontas
        // e os totais por tipo/mês agregados via SUM/GROUP BY no banco — o
        // filtro por id_maquina já foi aplicado do lado da API.
        $ultimasTransacoes = $resumo['ultimas_transacoes'] ?? [];

        $dadosGrafico = [];
        $totalPix = $totalCartao = $totalDinheiro = 0.0;
        foreach ($resumo['totais_por_tipo_mes'] ?? [] as $linha) {
            $ano   = (int) ($linha['ano'] ?? 0);
            $mes   = (int) ($linha['mes'] ?? 0);
            $tipo  = strtolower((string) ($linha['tipo'] ?? ''));
            $valor = (float) ($linha['total'] ?? 0);
            if (!$ano || !$mes) continue;

            if (!isset($dadosGrafico[$ano])) {
                for ($i = 1; $i <= 12; $i++) {
                    $dadosGrafico[$ano][$i] = ['pix' => 0.0, 'cartao' => 0.0, 'dinheiro' => 0.0];
                }
            }
            if (str_contains($tipo, 'pix')) {
                $dadosGrafico[$ano][$mes]['pix'] += $valor;
                $totalPix += $valor;
            } elseif (str_contains($tipo, 'cart')) {
                $dadosGrafico[$ano][$mes]['cartao'] += $valor;
                $totalCartao += $valor;
            } elseif (str_contains($tipo, 'dinheir') || str_contains($tipo, 'físic') || str_contains($tipo, 'fisic')) {
                $dadosGrafico[$ano][$mes]['dinheiro'] += $valor;
                $totalDinheiro += $valor;
            }
        }
        krsort($dadosGrafico);

        $totalDevolucao = (float) ($resumo['total_devolucao_filtro'] ?? 0);

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

        return view('Admin.home', compact(
            'maquinas', 'maquinas_online', 'maquinas_offline',
            'saldo', 'devolucoes', 'locais', 'clientes', 'maquinasRelatorio',
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
