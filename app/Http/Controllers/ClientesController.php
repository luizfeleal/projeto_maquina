<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\ClienteLocalService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\UsuariosService;
use App\Services\CredApiPixService;
use App\Services\GruposAcessoService;
use App\Services\AuthService;
use Exception;
use Illuminate\Support\Facades\Log;

class ClientesController extends Controller
{
    public function coletarClientePorId($id){

        if($id){
            $cliente = ClientesService::coletar($id);
            //$clienteLocal = collect(ClienteLocalService::coletarComFiltro(['id_cliente' => $id], 'where'))->pluck('id_local');
            $clienteLocal = ClienteLocalService::coletar();
            $clienteLocal = array_filter($clienteLocal, function($item) use($cliente){
                return $item['id_cliente'] == $cliente['id_cliente'];
            });

            $clienteLocal = collect($clienteLocal)->pluck('id_local');
            
            $local = LocaisService::coletar();
            $locais = array_filter($local, function($item) use($clienteLocal){
                return in_array($item['id_local'], $clienteLocal->toArray());
            });

            $credenciais = CredApiPixService::coletar();
            $credencial_efi = array_filter($credenciais, function($item) use ($id){
                return $item['id_cliente'] == $id && $item['tipo_cred'] == 'efi';
            });
            $credencial_pagbank = array_filter($credenciais, function($item) use ($id){
                return $item['id_cliente'] == $id && $item['tipo_cred'] == 'pagbank';
            });

            return view('Admin.Usuarios.show', compact('cliente', 'locais', 'credencial_efi', 'credencial_pagbank'));
        }else{
            return back()->with('error', 'Cliente não encontrada');
        }

    }

    public function criarCliente(Request $request){
        $grupos = GruposAcessoService::coletar();
        $clientes = ClientesService::coletar();

        return view('Admin.Usuarios.create', compact('grupos', 'clientes'));
    }
    public function registrarCliente(Request $request){

        //try{
            $dados = $request->all();

            $permissaoPagbank = false;
            $permissaoEfi = false;
        
            $dadosCliente = $request->except(['cliente_senha', 'cliente_confirmar_senha', 'cliente_id', 'cliente_secret', 'cliente_certificado', 'checkbox_pagbank', 'checkbox_efi']);
            if (array_key_exists('checkbox_pagbank', $dados)) {
                $permissaoPagbank = true;
                $dadosCliente['checkbox_pagbank'] = 1;
            }else{
                $dadosCliente['checkbox_pagbank'] = 0;
            }
            
            if (array_key_exists('checkbox_efi', $dados)) {
                $permissaoEfi = true;
                $dadosCliente['checkbox_efi'] = 1;
            }else{
                $dadosCliente['checkbox_efi'] = 0;
            }
    
            if($permissaoEfi && $permissaoPagbank){
                $id_grupo_acesso = 2;
            }else if($permissaoEfi){
                $id_grupo_acesso = 3;
            }else if($permissaoPagbank){
                $id_grupo_acesso = 4;
            }
    
            $cliente = ClientesService::criar($dadosCliente);
            return $cliente;
            if($cliente['success']){
    
                //Cadastrar credenciais
                $id_cliente = $cliente['data']['response']['id_cliente'];
                
                //Criar acesso a plataforma
                $dadoUsuarioAcesso = [
                    "id_cliente" => $id_cliente,
                    "id_grupo_acesso" => $id_grupo_acesso,
                    "usuario_nome" => $request['cliente_nome'],
                    "usuario_email" => $request['cliente_email'],
                    "usuario_login" => $request['cliente_email'],
                    "usuario_senha" => $request['cliente_senha'],
                    "ativo" => 1
                ];
    
                UsuariosService::criar($dadoUsuarioAcesso);
                return back()->with('success', 'Cliente cadastrado com sucesso!');
            }
            return back()->with('error', 'Houve um erro ao tentar cadastrar o cliente com os dados prechidos!');
        /*} catch(Exception $e){
            Log::error($e);
            return back()->with('error', 'Houve um erro ao tentar cadastrar o cliente com os dados prechidos!');
        }*/
        

    }

    public function editarCliente($id){
        $id_cliente = $id;
        $cliente = ClientesService::coletar($id);

        return view('Admin.Usuarios.edit', compact('cliente'));
    }

    public function atualizarCliente(Request $request){
        $id_cliente = $request['id_cliente'];
        $dados_cliente = $request->except('_token', 'id_cliente', 'checkbox_pagbank', 'checkbox_efi');

        $dados = $request->all();

        $permissaoPagbank = false;
            $permissaoEfi = false;

        if (array_key_exists('checkbox_pagbank', $dados)) {
            $permissaoPagbank = true;
            $dados_cliente['checkbox_pagbank'] = 1;
        }else{
            $dados_cliente['checkbox_pagbank'] = 0;
        }
        
        if (array_key_exists('checkbox_efi', $dados)) {
            $permissaoEfi = true;
            $dados_cliente['checkbox_efi'] = 1;
        }else{
            $dados_cliente['checkbox_efi'] = 0;
        }

        if($permissaoEfi && $permissaoPagbank){
            $id_grupo_acesso = 2;
        }else if($permissaoEfi){
            $id_grupo_acesso = 3;
        }else if($permissaoPagbank){
            $id_grupo_acesso = 4;
        }
       
        ClientesService::atualizar($dados_cliente, $id_cliente);
        $usuarios = UsuariosService::coletar();

        $usuarios = array_filter($usuarios, function($item) use($id_cliente){
            return $item['id_cliente'] == $id_cliente;
        });

        $usuarios = array_values($usuarios);
        UsuariosService::atualizar(['id_grupo_acesso' => $id_grupo_acesso], $usuarios[0]['id_usuario']);

        return back()->with('success', "Usuário atualizado com sucesso!");
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

    public function coletarCliente(Request $request){
        $clientes = ClientesService::coletar();
        
        return view('Admin.Usuarios.index', compact('clientes'));
    }

    public function transacaoMaquinas(Request $request){
        $maquinas_extrato = ExtratoMaquinaService::coletar();
        return view('Admin.Maquinas.Transacoes.Index', compact('maquinas_extrato'));
    }

    public function acumuladoMaquinas(Request $request){
        $maquinas_extrato = ExtratoMaquinaService::coletar();
        return view('Admin.Maquinas.Acumulado.Index', compact('maquinas_extrato'));

    }

    public function excluirCliente(Request $request){
       // try{

             $id_cliente = $request['id_cliente'];

             //$clienteLocal = ClienteLocalService::coletarComFiltro(['id_cliente' => $id_cliente], 'where');
             $clienteLocal = ClienteLocalService::coletar();

             $clienteLocalFiltrado = array_filter($clienteLocal, function($item) use($id_cliente){
                return $item['id_cliente'] == $id_cliente;
             });

             if(!empty($clienteLocalFiltrado)){
                return back()->with('error', 'O cliente não pôde ser excluído pois há local/locais associado(s) à ele.');
             }
             $result = ClientesService::deletar($id_cliente);

             return back()->with('success', $result['message']);
         //}catch(\Throwable $e){
             //return back()->with('error', 'Houve um erro ao tentar excluir o cliente.');
         //}
    }
}
