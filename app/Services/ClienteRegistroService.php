<?php

namespace App\Services;

use App\Services\Financeiro\EstoqueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Lógica de cadastro de cliente compartilhada entre a tela admin
 * (Admin/Usuarios) e a tela do módulo Financeiro (Financeiro/Clientes) —
 * ambas cadastram o cliente, vinculam produtos do estoque escolhidos e
 * criam o usuário de acesso com o grupo correto (Efí/PagBank/ambos/nenhum).
 */
class ClienteRegistroService
{
    public static function dadosFormulario(): array
    {
        $produtosEstoque = collect(EstoqueService::coletar())
            ->map(fn (array $p) => EstoqueService::normalizarParaView($p))
            ->filter(fn ($p) => $p['quantidade'] > 0)
            ->sortBy('nome_produto')
            ->values();

        return [
            'grupos'          => GruposAcessoService::coletar(),
            'clientes'        => ClientesService::coletar(),
            'produtosEstoque' => $produtosEstoque,
        ];
    }

    public static function registrar(Request $request): array
    {
        $dados = $request->all();

        $permissaoPagbank = false;
        $permissaoEfi = false;

        $dadosCliente = $request->except(['cliente_senha', 'cliente_confirmar_senha', 'cliente_id', 'cliente_secret', 'cliente_certificado', 'checkbox_pagbank', 'checkbox_efi', 'produtos']);

        if (array_key_exists('checkbox_pagbank', $dados)) {
            $permissaoPagbank = true;
            $dadosCliente['checkbox_pagbank'] = 1;
        } else {
            $dadosCliente['checkbox_pagbank'] = 0;
        }

        if (array_key_exists('checkbox_efi', $dados)) {
            $permissaoEfi = true;
            $dadosCliente['checkbox_efi'] = 1;
        } else {
            $dadosCliente['checkbox_efi'] = 0;
        }

        if ($permissaoEfi && $permissaoPagbank) {
            $id_grupo_acesso = 2;
        } elseif ($permissaoEfi) {
            $id_grupo_acesso = 3;
        } elseif ($permissaoPagbank) {
            $id_grupo_acesso = 4;
        } else {
            $id_grupo_acesso = 5;
        }

        try {
            $cliente = ClientesService::criar($dadosCliente);

            if (!($cliente['success'] ?? false)) {
                return ['success' => false, 'message' => 'Houve um erro ao tentar cadastrar o cliente com os dados prechidos!'];
            }

            $id_cliente = $cliente['data']['response']['id_cliente'];

            // Vincula os produtos do estoque escolhidos e dá baixa nas quantidades
            $produtosSelecionados = collect($request->input('produtos', []))
                ->filter(fn ($p) => !empty($p['id_estoque_produto']) && !empty($p['quantidade']))
                ->values()
                ->all();

            $avisoEstoque = null;
            if (!empty($produtosSelecionados)) {
                $vinculo = ClienteEstoqueProdutoService::vincular($id_cliente, $produtosSelecionados);
                if (!($vinculo['success'] ?? false)) {
                    Log::warning('[ClienteRegistroService] Falha ao vincular produtos do estoque ao cliente.', [
                        'id_cliente' => $id_cliente,
                        'erro'       => $vinculo['message'] ?? null,
                    ]);
                    $avisoEstoque = $vinculo['message'] ?? 'Houve um erro ao vincular os produtos do estoque.';
                }
            }

            // Cria o acesso à plataforma
            UsuariosService::criar([
                "id_cliente"      => $id_cliente,
                "id_grupo_acesso" => $id_grupo_acesso,
                "usuario_nome"    => $request['cliente_nome'],
                "usuario_email"   => $request['cliente_email'],
                "usuario_login"   => $request['cliente_email'],
                "usuario_senha"   => $request['cliente_senha'],
                "ativo"           => 1,
            ]);

            if ($avisoEstoque) {
                return ['success' => true, 'warning' => "Cliente cadastrado, mas houve um erro ao vincular produtos do estoque: {$avisoEstoque}"];
            }

            return ['success' => true];
        } catch (\Exception $e) {
            Log::error($e);
            return ['success' => false, 'message' => 'Houve um erro ao tentar cadastrar o cliente com os dados prechidos!'];
        }
    }
}
