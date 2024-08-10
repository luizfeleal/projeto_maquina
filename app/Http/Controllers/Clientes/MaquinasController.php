<?php

namespace App\Http\Controllers\Clientes;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\ClienteLocalService;
use App\Services\AuthService;
use App\Http\Controllers\Controller;

class MaquinasController extends Controller
{

    public function coletarTodasAsMaquinas(Request $request){
        $id_cliente = session()->get('id_cliente');

             //$clienteLocal = ClienteLocalService::coletarComFiltro(['id_cliente' => $id_cliente], 'where');
             $clienteLocal = ClienteLocalService::coletar();

             $clienteLocalFiltrado = array_filter($clienteLocal, function($item) use($id_cliente){
                return $item['id_cliente'] == $id_cliente;
             });
             $idLocais = array_column($clienteLocal, 'id_local');

             
        $locais = LocaisService::coletar();
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

        $maquinas = array_filter($maquinas, function($item) use($idLocais){
            return in_array($item['id_local'], $idLocais);
        });
        // Indexando maquinas por id_maquina
        $maquinas_indexadas = [];
        foreach ($maquinas as $maquina) {
            $maquinas_indexadas[$maquina['id_maquina']] = $maquina;
        }

        // Array para armazenar o resultado final
        $resultado = [];

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

        // Se você quiser um array com índices numéricos simples, pode utilizar array_values
        $resultado = array_values($resultado);
        return view('Clientes.Maquinas.index', compact('resultado'));
    }

    public function transacaoMaquinas(Request $request){
        $locais = LocaisService::coletar();
        
        $id_cliente = session()->get('id_cliente');

        $clienteLocal = ClienteLocalService::coletar();

             $clienteLocalFiltrado = array_filter($clienteLocal, function($item) use($id_cliente){
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
        }
        return view('Clientes.Maquinas.Transacoes.Index', compact('resultado'));
    }

    public function acumuladoMaquinas(Request $request){
        $locais = LocaisService::coletar();
        $id_cliente = session()->get('id_cliente');
        $clienteLocal = ClienteLocalService::coletar();

        $clienteLocalFiltrado = array_filter($clienteLocal, function($item) use($id_cliente){
           return $item['id_cliente'] == $id_cliente;
        });
        $idLocais = array_column($clienteLocal, 'id_local');

        $maquinas = MaquinasService::coletar();

        $maquinas_extrato = ExtratoMaquinaService::coletar();

        $locais_indexados = [];
        foreach ($locais as $local) {
            $locais_indexados[$local['id_local']] = $local;
        }

        $locais_indexados = array_filter($locais_indexados, function($item) use($idLocais){
            return in_array($item, $idLocais);
        });

        if(empty($locais_indexados)){
            $maquinas = [];
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
            return $locais_indexados;
            $maquina['total_pix'] = $total_pix;
            $maquina['total_cartao'] = $total_cartao;
            $maquina['total_dinheiro'] = $total_dinheiro;
            $maquina['total_maquina'] = $total_maquina;
            $maquina['local_nome'] = $locais_indexados[$maquina['id_local']]['local_nome'];

        }

        return view('Clientes.Maquinas.Acumulado.Index', compact('maquinas'));

    }
}
