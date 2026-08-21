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

    public static function dadosEdicao(int $idCliente): array
    {
        return array_merge(self::dadosFormulario(), [
            'cliente'            => ClientesService::coletar((string) $idCliente),
            'produtosVinculados' => collect(ClienteEstoqueProdutoService::listarPorCliente($idCliente))->values(),
        ]);
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

    public static function atualizar(Request $request, int $idCliente): array
    {
        $dados = $request->all();

        $permissaoPagbank = false;
        $permissaoEfi = false;

        $dadosCliente = $request->except(['id_cliente', 'checkbox_pagbank', 'checkbox_efi', 'produtos', 'produtos_removidos']);

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
            ClientesService::atualizar($dadosCliente, $idCliente);

            // Atualiza o grupo de acesso, nome e email do usuário de acesso vinculado
            $usuarios = collect(UsuariosService::coletar())
                ->filter(fn ($u) => $u['id_cliente'] == $idCliente)
                ->values();

            if ($usuarios->isNotEmpty()) {
                UsuariosService::atualizar([
                    'id_grupo_acesso' => $id_grupo_acesso,
                    'usuario_email'   => $request['cliente_email'],
                    'usuario_nome'    => $request['cliente_nome'],
                ], $usuarios[0]['id_usuario']);
            }

            $avisoEstoque = null;

            // Remove os vínculos de produto desmarcados na tela
            $removidos = collect($request->input('produtos_removidos', []))->filter();
            foreach ($removidos as $idVinculo) {
                $desvinculo = ClienteEstoqueProdutoService::desvincular((int) $idVinculo);
                if (!($desvinculo['success'] ?? false)) {
                    Log::warning('[ClienteRegistroService] Falha ao desvincular produto do cliente.', [
                        'id_cliente' => $idCliente,
                        'id_vinculo' => $idVinculo,
                        'erro'       => $desvinculo['message'] ?? null,
                    ]);
                    $avisoEstoque = $desvinculo['message'] ?? 'Houve um erro ao desvincular um produto do estoque.';
                }
            }

            // Vincula os novos produtos escolhidos e dá baixa nas quantidades
            $produtosSelecionados = collect($request->input('produtos', []))
                ->filter(fn ($p) => !empty($p['id_estoque_produto']) && !empty($p['quantidade']))
                ->values()
                ->all();

            if (!empty($produtosSelecionados)) {
                $vinculo = ClienteEstoqueProdutoService::vincular($idCliente, $produtosSelecionados);
                if (!($vinculo['success'] ?? false)) {
                    Log::warning('[ClienteRegistroService] Falha ao vincular produtos do estoque ao cliente.', [
                        'id_cliente' => $idCliente,
                        'erro'       => $vinculo['message'] ?? null,
                    ]);
                    $avisoEstoque = $vinculo['message'] ?? 'Houve um erro ao vincular os produtos do estoque.';
                }
            }

            if ($avisoEstoque) {
                return ['success' => true, 'warning' => "Cliente atualizado, mas houve um erro ao atualizar os produtos do estoque: {$avisoEstoque}"];
            }

            return ['success' => true];
        } catch (\Exception $e) {
            Log::error($e);
            return ['success' => false, 'message' => 'Houve um erro ao tentar atualizar o cliente.'];
        }
    }
}
