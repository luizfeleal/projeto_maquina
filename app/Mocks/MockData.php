<?php

namespace App\Mocks;

/**
 * Dados iniciais (seed) para o ambiente de mocks.
 * Credenciais de login no modo mock:
 *   admin   / admin123   → área administrativa
 *   cliente / cliente123 → área do cliente
 */
class MockData
{
    /** PNG 1x1 transparente em base64 (usado no mock de QR Code). */
    public const QR_IMAGE_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    public static function seed(): array
    {
        $now = now()->format('Y-m-d H:i:s');

        return [
            'gruposAcesso' => [
                ['id_grupo_acesso' => 1, 'grupo_acesso_nome' => 'admin'],
                ['id_grupo_acesso' => 2, 'grupo_acesso_nome' => 'cliente_efi_pagbank'],
                ['id_grupo_acesso' => 3, 'grupo_acesso_nome' => 'cliente_efi'],
                ['id_grupo_acesso' => 4, 'grupo_acesso_nome' => 'cliente_pagbank'],
                ['id_grupo_acesso' => 5, 'grupo_acesso_nome' => 'cliente'],
            ],

            'clientes' => [
                [
                    'id_cliente' => 1,
                    'cliente_nome' => 'Cliente Demo',
                    'cliente_celular' => '11999990001',
                    'cliente_email' => 'cliente.demo@mock.local',
                    'cliente_cpf_cnpj' => '12345678901',
                    'checkbox_efi' => 1,
                    'checkbox_pagbank' => 1,
                    'data_criacao' => $now,
                ],
                [
                    'id_cliente' => 2,
                    'cliente_nome' => 'Cliente Teste',
                    'cliente_celular' => '11999990002',
                    'cliente_email' => 'cliente.teste@mock.local',
                    'cliente_cpf_cnpj' => '98765432100',
                    'checkbox_efi' => 0,
                    'checkbox_pagbank' => 0,
                    'data_criacao' => $now,
                ],
            ],

            'usuarios' => [
                [
                    'id_usuario' => 1,
                    'id_grupo_acesso' => 1,
                    'id_cliente' => null,
                    'usuario_nome' => 'Administrador Mock',
                    'usuario_email' => 'admin@mock.local',
                    'usuario_login' => 'admin',
                    'usuario_senha' => 'admin123',
                    'ativo' => 1,
                    'data_inclusao' => $now,
                ],
                [
                    'id_usuario' => 2,
                    'id_grupo_acesso' => 5,
                    'id_cliente' => 1,
                    'usuario_nome' => 'Cliente Mock',
                    'usuario_email' => 'cliente@mock.local',
                    'usuario_login' => 'cliente',
                    'usuario_senha' => 'cliente123',
                    'ativo' => 1,
                    'data_inclusao' => $now,
                ],
            ],

            'locais' => [
                [
                    'id_local' => 1,
                    'local_nome' => 'Local Central Mock',
                    'data_criacao' => $now,
                ],
                [
                    'id_local' => 2,
                    'local_nome' => 'Local Filial Mock',
                    'data_criacao' => $now,
                ],
            ],

            'clienteLocal' => [
                [
                    'id_cliente_local' => 1,
                    'id_cliente' => 1,
                    'id_local' => 1,
                    'cliente_local_principal' => 1,
                ],
                [
                    'id_cliente_local' => 2,
                    'id_cliente' => 2,
                    'id_local' => 2,
                    'cliente_local_principal' => 1,
                ],
            ],

            'maquinas' => [
                [
                    'id_maquina' => 1,
                    'id_local' => 1,
                    'id_placa' => 'PLACA-MOCK-001',
                    'maquina_nome' => 'Máquina Demo 01',
                    'maquina_status' => 1,
                    'maquina_ultimo_contato' => $now,
                    'data_criacao' => $now,
                    'deleted_at' => null,
                ],
                [
                    'id_maquina' => 2,
                    'id_local' => 1,
                    'id_placa' => 'PLACA-MOCK-002',
                    'maquina_nome' => 'Máquina Demo 02',
                    'maquina_status' => 0,
                    'maquina_ultimo_contato' => $now,
                    'data_criacao' => $now,
                    'deleted_at' => null,
                ],
            ],

            'maquinasCartao' => [
                [
                    'id_maquina_cartao' => 1,
                    'id_maquina' => 1,
                    'device' => 'DEVICE-MOCK-001',
                    'status' => 1,
                    'data_criacao' => $now,
                ],
            ],

            'credApiPix' => [
                [
                    'id_credencial' => 1,
                    'id_cliente' => 1,
                    'client_id' => 'mock-efi-client-id',
                    'client_secret' => 'mock-efi-secret',
                    'tipo_cred' => 'efi',
                    'data_criacao' => $now,
                ],
                [
                    'id_credencial' => 2,
                    'id_cliente' => 1,
                    'client_id' => 'mockpagbankclientid',
                    'client_secret' => 'mock-pagbank-secret',
                    'tipo_cred' => 'pagbank',
                    'data_criacao' => $now,
                ],
            ],

            'qrcode' => [
                [
                    'id_qr' => 1,
                    'id_local' => 1,
                    'id_maquina' => 2,
                    'id_cliente' => 1,
                    'id_usuario' => 1,
                    'ativo' => 1,
                    'qr_image' => 'data:image/png;base64,' . self::QR_IMAGE_BASE64,
                    'data_criacao' => $now,
                ],
            ],

            'logs' => [
                [
                    'id' => 1,
                    'id_usuario' => 1,
                    'descricao' => 'Erro de comunicação com hardware (mock)',
                    'status' => 'erro',
                    'acao' => 'liberar_jogada',
                    'id_maquina' => 1,
                    'id_local' => 1,
                    'data_criacao' => $now,
                ],
                [
                    'id' => 2,
                    'id_usuario' => 2,
                    'descricao' => 'Timeout na transação PIX (mock)',
                    'status' => 'erro',
                    'acao' => 'transacao',
                    'id_maquina' => 2,
                    'id_local' => 1,
                    'data_criacao' => $now,
                ],
            ],

            'extratoMaquina' => [
                [
                    'id_extrato_maquina' => 1,
                    'id_maquina' => 1,
                    'extrato_operacao_tipo' => 'PIX',
                    'extrato_operacao_valor' => 10.50,
                    'extrato_operacao_status' => 'aprovado',
                    'extrato_operacao' => 'C',
                    'extrato_operacao_saldo' => 110.50,
                    'data_criacao' => $now,
                ],
                [
                    'id_extrato_maquina' => 2,
                    'id_maquina' => 1,
                    'extrato_operacao_tipo' => 'Cartão',
                    'extrato_operacao_valor' => 5.00,
                    'extrato_operacao_status' => 'aprovado',
                    'extrato_operacao' => 'C',
                    'extrato_operacao_saldo' => 115.50,
                    'data_criacao' => $now,
                ],
                [
                    'id_extrato_maquina' => 3,
                    'id_maquina' => 2,
                    'extrato_operacao_tipo' => 'Dinheiro',
                    'extrato_operacao_valor' => 2.00,
                    'extrato_operacao_status' => 'aprovado',
                    'extrato_operacao' => 'C',
                    'extrato_operacao_saldo' => 2.00,
                    'data_criacao' => $now,
                ],
                [
                    'id_extrato_maquina' => 4,
                    'id_maquina' => 1,
                    'extrato_operacao_tipo' => 'Taxa',
                    'extrato_operacao_valor' => 0.50,
                    'extrato_operacao_status' => 'aprovado',
                    'extrato_operacao' => 'D',
                    'extrato_operacao_saldo' => 115.00,
                    'data_criacao' => $now,
                ],
            ],

            'extratoCliente' => [],

            'resetsParciais' => [
                [
                    'id' => 1,
                    'id_maquina' => 1,
                    'maquina_nome' => 'Máquina Demo 01',
                    'local_nome' => 'Local Central Mock',
                    'valor_ultima_coleta' => 500.00,
                    'valor_acumulado_total' => 500.00,
                    'realizado_por' => '1',
                    'realizado_por_nome' => 'Administrador Mock',
                    'observacao' => null,
                    'created_at' => now()->subDays(3)->format('Y-m-d H:i:s'),
                ],
            ],

            'acessosTela' => self::buildAcessosTela(),

            'placasDisponiveis' => [
                'PLACA-MOCK-003',
                'PLACA-MOCK-004',
                'PLACA-MOCK-005',
            ],

            '_counters' => [
                'clientes' => 2,
                'usuarios' => 2,
                'locais' => 2,
                'clienteLocal' => 2,
                'maquinas' => 2,
                'maquinasCartao' => 1,
                'credApiPix' => 2,
                'qrcode' => 1,
                'logs' => 2,
                'extratoMaquina' => 4,
                'extratoCliente' => 0,
                'resetsParciais' => 1,
                'acessosTela' => 0,
            ],
        ];
    }

    private static function buildAcessosTela(): array
    {
        $adminRoutes = [
            'home', 'maquinas', 'maquinas-criar', 'maquinas-editar', 'maquinas-visualizar',
            'maquinas-registrar', 'maquinas-gerar-id-placa', 'maquinas-transacoes', 'maquinas-acumulado',
            'maquinas-reset-parcial', 'maquinas-resets-historico',
            'maquinas-cartao', 'maquinas-cartao-criar', 'maquinas-cartao-registrar', 'maquinas-cartao-inativar',
            'maquinas-cartao-excluir', 'maquinas-excluir', 'maquinas-atualizar', 'maquinas-liberar-jogada',
            'view-liberar-jogadas', 'local', 'local-incluir-usuario', 'local-registrar-usuario', 'local-criar',
            'local-detalhar', 'local-registrar', 'local-excluir', 'usuarios', 'usuario-criar', 'usuario-detalhar',
            'usuario-editar', 'usuario-atualizar', 'usuario-registrar', 'usuario-excluir', 'qr', 'qr-criar',
            'qr-registrar', 'qr-download', 'qr-excluir', 'relatorio-view', 'relatorio-criar', 'relatorio-xlsx-download',
            'credencial-listar', 'credencial-criar-efi', 'credencial-criar-pagbank', 'credencial-registrar',
            'credencial-editar-efi', 'credencial-editar-pagbank', 'credencial-atualizar', 'credencial-excluir',
        ];

        $clienteRoutes = [
            'cliente-home', 'clientes-maquinas', 'clientes-maquinas-transacoes', 'clientes-maquinas-acumulado',
            'clientes-maquinas-reset-parcial', 'clientes-maquinas-reset-parcial-todas', 'clientes-maquinas-resets-historico',
            'view-clientes-maquinas-liberar-jogadas', 'clientes-maquinas-liberar-jogadas', 'cliente-maquinas-cartao',
            'cliente-maquinas-cartao-criar', 'cliente-maquinas-cartao-registrar', 'cliente-maquinas-cartao-inativar',
            'cliente-maquinas-cartao-excluir', 'clientes-maquinas-editar', 'clientes-maquinas-atualizar',
            'cliente-relatorio-view', 'cliente-relatorio-criar', 'cliente-relatorio-xlsx-download',
            'cliente-qr', 'cliente-qr-criar', 'cliente-qr-registrar', 'cliente-qr-download', 'cliente-qr-excluir',
            'cliente-credencial-listar', 'cliente-credencial-criar-efi', 'cliente-credencial-criar-pagbank',
            'cliente-credencial-registrar', 'cliente-credencial-editar-efi', 'cliente-credencial-editar-pagbank',
            'cliente-credencial-atualizar', 'cliente-credencial-excluir',
        ];

        $acessos = [];
        $id = 1;

        foreach ($adminRoutes as $route) {
            $acessos[] = [
                'id_acesso_tela' => $id++,
                'id_grupo_acesso' => 1,
                'acesso_tela_viewname' => $route,
                'acesso_tela_nome' => $route,
            ];
        }

        $gruposCliente = [2, 3, 4, 5];

        foreach ($gruposCliente as $idGrupoCliente) {
            foreach ($clienteRoutes as $route) {
                $acessos[] = [
                    'id_acesso_tela' => $id++,
                    'id_grupo_acesso' => $idGrupoCliente,
                    'acesso_tela_viewname' => $route,
                    'acesso_tela_nome' => $route,
                ];
            }
        }

        return $acessos;
    }
}
