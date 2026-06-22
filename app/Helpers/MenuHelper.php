<?php

if (!function_exists('normalizeGrupoNome')) {
    function normalizeGrupoNome(?string $grupoNome): string
    {
        return strtolower(trim($grupoNome ?? ''));
    }
}

if (!function_exists('resolveHomeRouteForGrupo')) {
    /**
     * Rota inicial após login conforme o grupo de acesso.
     * Mesma página de login para todos; o destino muda por perfil.
     */
    function resolveHomeRouteForGrupo(?string $grupoNome): string
    {
        return match (normalizeGrupoNome($grupoNome)) {
            'admin'      => 'home',
            'financeiro' => 'financeiro-home',
            default      => 'cliente-home',
        };
    }
}

if (!function_exists('getRouteBreadcrumbs')) {
    /**
     * Returns the breadcrumb trail for a given route name.
     * Each item: ['label' => string, 'route' => string|null]
     */
    function getRouteBreadcrumbs(string $routeName, string $role = 'admin'): array
    {
        $homeRoute = $role === 'cliente' ? 'cliente-home' : 'home';
        $home      = ['label' => 'Home', 'route' => $homeRoute];

        $criar     = ['label' => 'Criar',          'route' => null];
        $maquinas  = ['label' => 'Máquinas',        'route' => $role === 'cliente' ? 'clientes-maquinas' : 'maquinas'];
        $qr        = ['label' => 'QR Code',         'route' => $role === 'cliente' ? 'cliente-qr' : 'qr'];
        $relatorio = ['label' => 'Relatórios',      'route' => $role === 'cliente' ? 'cliente-home' : 'relatorio-view'];
        $cred      = ['label' => 'Credenciais',     'route' => $role === 'cliente' ? 'cliente-credencial-listar' : 'credencial-listar'];

        $map = [
            // Admin
            'home'                                  => [$home],
            'maquinas'                              => [$home, $maquinas],
            'maquinas-criar'                        => [$home, $criar, ['label' => 'Nova Máquina']],
            'maquinas-editar'                       => [$home, $maquinas, ['label' => 'Editar Máquina']],
            'maquinas-show'                         => [$home, $maquinas, ['label' => 'Detalhes da Máquina']],
            'maquinas-transacoes'                   => [$home, $maquinas, ['label' => 'Extrato']],
            'maquinas-acumulado'                    => [$home, $maquinas, ['label' => 'Acumulado']],
            'maquinas-resets-historico'             => [$home, $maquinas, ['label' => 'Acumulado', 'route' => 'maquinas-acumulado'], ['label' => 'Histórico de Resets']],
            'maquinas-cartao'                       => [$home, $criar, ['label' => 'Máquina Cartão']],
            'maquinas-cartao-criar'                 => [$home, $criar, ['label' => 'Incluir Máquina Cartão']],
            'local'                                 => [$home, ['label' => 'Locais']],
            'local-criar'                           => [$home, $criar, ['label' => 'Criar Local']],
            'local-show'                            => [$home, ['label' => 'Locais', 'route' => 'local'], ['label' => 'Detalhes do Local']],
            'local-incluir-usuario'                 => [$home, $criar, ['label' => 'Incluir Usuários']],
            'credencial-listar'                     => [$home, $cred],
            'credencial-criar-efi'                  => [$home, $cred, ['label' => 'Nova Credencial EFI']],
            'credencial-criar-pagbank'              => [$home, $cred, ['label' => 'Nova Credencial PagBank']],
            'credencial-editar-efi'                 => [$home, $cred, ['label' => 'Editar Credencial EFI']],
            'credencial-editar-pagbank'             => [$home, $cred, ['label' => 'Editar Credencial PagBank']],
            'qr'                                    => [$home, $qr],
            'qr-criar'                              => [$home, $qr, ['label' => 'Novo QR Code']],
            'relatorio-view'                        => [$home, $relatorio],
            'relatorio-criar'                       => [$home, $relatorio, ['label' => 'Gerar Relatório']],
            'relatorio-exibir'                      => [$home, $relatorio, ['label' => 'Visualizar Relatório']],
            'usuarios'                              => [$home, ['label' => 'Usuários']],
            'usuario-criar'                         => [$home, ['label' => 'Usuários', 'route' => 'usuarios'], ['label' => 'Novo Usuário']],
            'usuario-editar'                        => [$home, ['label' => 'Usuários', 'route' => 'usuarios'], ['label' => 'Editar Usuário']],
            'usuario-detalhar'                      => [$home, ['label' => 'Usuários', 'route' => 'usuarios'], ['label' => 'Detalhes do Usuário']],
            'view-liberar-jogadas'                  => [$home, ['label' => 'Liberar Jogada']],
            'maquinas-liberar-jogada'               => [$home, ['label' => 'Liberar Jogada']],

            // Cliente
            'cliente-home'                          => [['label' => 'Home']],
            'clientes-maquinas'                     => [$home, $maquinas],
            'clientes-maquinas-transacoes'          => [$home, $maquinas, ['label' => 'Extrato']],
            'clientes-maquinas-acumulado'           => [$home, $maquinas, ['label' => 'Acumulado']],
            'clientes-maquinas-resets-historico'    => [$home, $maquinas, ['label' => 'Acumulado', 'route' => 'clientes-maquinas-acumulado'], ['label' => 'Histórico de Resets']],
            'cliente-maquinas-cartao'               => [$home, $criar, ['label' => 'Máquina Cartão']],
            'cliente-credencial-listar'             => [$home, $cred],
            'cliente-credencial-criar-efi'          => [$home, $cred, ['label' => 'Nova Credencial EFI']],
            'cliente-credencial-criar-pagbank'      => [$home, $cred, ['label' => 'Nova Credencial PagBank']],
            'cliente-qr'                            => [$home, $qr],
            'cliente-qr-criar'                      => [$home, $qr, ['label' => 'Novo QR Code']],
            'cliente-relatorio-view'                => [$home, $relatorio],
            'view-clientes-maquinas-liberar-jogadas'=> [$home, ['label' => 'Liberar Jogada']],

            // Financeiro
            'financeiro-home'           => [['label' => 'Dashboard']],
            'financeiro-despesas'       => [['label' => 'Dashboard', 'route' => 'financeiro-home'], ['label' => 'Despesas']],
            'financeiro-despesas-criar' => [['label' => 'Dashboard', 'route' => 'financeiro-home'], ['label' => 'Despesas', 'route' => 'financeiro-despesas'], ['label' => 'Nova Despesa']],
            'financeiro-placas'         => [['label' => 'Dashboard', 'route' => 'financeiro-home'], ['label' => 'Estoque de Placas']],
            'financeiro-placas-criar'   => [['label' => 'Dashboard', 'route' => 'financeiro-home'], ['label' => 'Estoque de Placas', 'route' => 'financeiro-placas'], ['label' => 'Registrar Placa']],
        ];

        return $map[$routeName] ?? [$home];
    }
}

if (!function_exists('getSidebar')) {
    function getSidebar(string $role = 'admin'): array
    {
        if ($role === 'admin') {
            return [
                [
                    'title'         => 'Home',
                    'icon'          => 'solar:home-2-bold-duotone',
                    'route'         => 'home',
                    'active_routes' => ['home'],
                ],
                [
                    'title' => 'Menu Principal',
                ],
                [
                    'title'         => 'Criar',
                    'icon'          => 'solar:add-circle-bold-duotone',
                    'active_routes' => ['maquinas-criar', 'local-incluir-usuario', 'local-criar', 'credencial-listar', 'credencial-criar-efi', 'credencial-criar-pagbank', 'credencial-editar-efi', 'credencial-editar-pagbank', 'maquinas-cartao'],
                    'sub_menu'      => [
                        ['title' => 'Nova máquina',     'route' => 'maquinas-criar'],
                        ['title' => 'Incluir usuários', 'route' => 'local-incluir-usuario'],
                        ['title' => 'Criar local',      'route' => 'local-criar'],
                        ['title' => 'Credenciais',      'route' => 'credencial-listar'],
                        ['title' => 'Máquina Cartão',   'route' => 'maquinas-cartao'],
                    ],
                ],
                [
                    'title'         => 'Locais',
                    'icon'          => 'solar:map-point-bold-duotone',
                    'active_routes' => ['local'],
                    'sub_menu'      => [
                        ['title' => 'Exibir locais', 'route' => 'local'],
                    ],
                ],
                [
                    'title'         => 'Minhas máquinas',
                    'icon'          => 'solar:monitor-bold-duotone',
                    'active_routes' => ['maquinas', 'maquinas-transacoes', 'maquinas-acumulado', 'maquinas-resets-historico', 'relatorio-view', 'relatorio-criar', 'relatorio-exibir'],
                    'sub_menu'      => [
                        ['title' => 'Exibir máquinas', 'route' => 'maquinas'],
                        ['title' => 'Extrato',      'route' => 'maquinas-transacoes'],
                    ],
                ],
                [
                    'title'         => 'Gerar QR',
                    'icon'          => 'solar:qr-code-bold-duotone',
                    'active_routes' => ['qr-criar', 'qr'],
                    'sub_menu'      => [
                        ['title' => 'Novo QR',    'route' => 'qr-criar'],
                        ['title' => 'Listar QRs', 'route' => 'qr'],
                    ],
                ],
                [
                    'title'         => 'Usuários',
                    'icon'          => 'solar:users-group-two-rounded-bold-duotone',
                    'route'         => 'usuarios',
                    'active_routes' => ['usuarios', 'usuario-criar'],
                ],
                [
                    'title'         => 'Liberar Jogada',
                    'icon'          => 'solar:play-circle-bold-duotone',
                    'route'         => 'view-liberar-jogadas',
                    'active_routes' => ['view-liberar-jogadas', 'maquinas-liberar-jogada'],
                ],
                [
                    'title'         => 'Sair',
                    'icon'          => 'solar:logout-3-bold-duotone',
                    'route'         => 'logout',
                    'active_routes' => [],
                ],
            ];
        }

        if ($role === 'financeiro') {
            return [
                [
                    'title'         => 'Dashboard',
                    'icon'          => 'solar:pie-chart-2-bold-duotone',
                    'route'         => 'financeiro-home',
                    'active_routes' => ['financeiro-home'],
                ],
                [
                    'title' => 'Módulo Financeiro',
                ],
                [
                    'title'         => 'Despesas',
                    'icon'          => 'solar:bill-list-bold-duotone',
                    'active_routes' => ['financeiro-despesas', 'financeiro-despesas-criar'],
                    'sub_menu'      => [
                        ['title' => 'Listar despesas', 'route' => 'financeiro-despesas'],
                        ['title' => 'Nova despesa',    'route' => 'financeiro-despesas-criar'],
                    ],
                ],
                [
                    'title'         => 'Estoque de Placas',
                    'icon'          => 'solar:cpu-bold-duotone',
                    'active_routes' => ['financeiro-placas', 'financeiro-placas-criar'],
                    'sub_menu'      => [
                        ['title' => 'Listar placas',     'route' => 'financeiro-placas'],
                        ['title' => 'Registrar placa',   'route' => 'financeiro-placas-criar'],
                    ],
                ],
                [
                    'title'         => 'Inadimplência',
                    'icon'          => 'solar:danger-triangle-bold-duotone',
                    'active_routes' => ['financeiro-inadimplencia'],
                    'sub_menu'      => [
                        ['title' => 'Listar inadimplentes', 'route' => 'financeiro-home'],
                    ],
                ],
                [
                    'title'         => 'Mensagens',
                    'icon'          => 'solar:chat-round-dots-bold-duotone',
                    'route'         => 'financeiro-home',
                    'active_routes' => ['financeiro-mensagens'],
                ],
                [
                    'title'         => 'Sair',
                    'icon'          => 'solar:logout-3-bold-duotone',
                    'route'         => 'logout',
                    'active_routes' => [],
                ],
            ];
        }

        return [
            [
                'title'         => 'Home',
                'icon'          => 'solar:home-2-bold-duotone',
                'route'         => 'cliente-home',
                'active_routes' => ['cliente-home'],
            ],
            [
                'title' => 'Menu Principal',
            ],
            [
                'title'         => 'Criar',
                'icon'          => 'solar:add-circle-bold-duotone',
                'active_routes' => ['cliente-credencial-listar', 'cliente-credencial-criar-efi', 'cliente-credencial-criar-pagbank', 'cliente-credencial-editar-efi', 'cliente-credencial-editar-pagbank', 'cliente-maquinas-cartao'],
                'sub_menu'      => [
                    ['title' => 'Credenciais',    'route' => 'cliente-credencial-listar'],
                    ['title' => 'Máquina Cartão', 'route' => 'cliente-maquinas-cartao'],
                ],
            ],
            [
                'title'         => 'Gerar QR',
                'icon'          => 'solar:qr-code-bold-duotone',
                'active_routes' => ['cliente-qr-criar', 'cliente-qr'],
                'sub_menu'      => [
                    ['title' => 'Novo QR',    'route' => 'cliente-qr-criar'],
                    ['title' => 'Listar QRs', 'route' => 'cliente-qr'],
                ],
            ],
            [
                'title'         => 'Minhas máquinas',
                'icon'          => 'solar:monitor-bold-duotone',
                    'active_routes' => ['clientes-maquinas', 'clientes-maquinas-transacoes', 'clientes-maquinas-acumulado', 'clientes-maquinas-resets-historico'],
                    'sub_menu'      => [
                        ['title' => 'Exibir máquinas', 'route' => 'clientes-maquinas'],
                        ['title' => 'Extrato',      'route' => 'clientes-maquinas-transacoes'],
                    ],
            ],
            [
                'title'         => 'Liberar Jogada',
                'icon'          => 'solar:play-circle-bold-duotone',
                'route'         => 'view-clientes-maquinas-liberar-jogadas',
                'active_routes' => ['view-clientes-maquinas-liberar-jogadas'],
            ],
            [
                'title'         => 'Sair',
                'icon'          => 'solar:logout-3-bold-duotone',
                'route'         => 'logout',
                'active_routes' => [],
            ],
        ];
    }
}
