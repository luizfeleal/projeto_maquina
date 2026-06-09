<?php

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
        $relatorio = ['label' => 'Relatórios',      'route' => $role === 'cliente' ? 'cliente-relatorio-view' : 'relatorio-view'];
        $cred      = ['label' => 'Credenciais',     'route' => $role === 'cliente' ? 'cliente-credencial-listar' : 'credencial-listar'];

        $map = [
            // Admin
            'home'                                  => [$home],
            'maquinas'                              => [$home, $maquinas],
            'maquinas-criar'                        => [$home, $criar, ['label' => 'Nova Máquina']],
            'maquinas-editar'                       => [$home, $maquinas, ['label' => 'Editar Máquina']],
            'maquinas-show'                         => [$home, $maquinas, ['label' => 'Detalhes da Máquina']],
            'maquinas-transacoes'                   => [$home, $maquinas, ['label' => 'Transações']],
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
            'clientes-maquinas-transacoes'          => [$home, $maquinas, ['label' => 'Transações']],
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
                    'active_routes' => ['maquinas-criar', 'local-incluir-usuario', 'local-criar', 'credencial-criar-efi', 'credencial-criar-pagbank', 'credencial-listar', 'maquinas-cartao'],
                    'sub_menu'      => [
                        ['title' => 'Nova máquina',       'route' => 'maquinas-criar'],
                        ['title' => 'Incluir usuários',   'route' => 'local-incluir-usuario'],
                        ['title' => 'Criar local',        'route' => 'local-criar'],
                        ['title' => 'Credencial EFI',     'route' => 'credencial-criar-efi'],
                        ['title' => 'Credencial PagBank', 'route' => 'credencial-criar-pagbank'],
                        ['title' => 'Editar credenciais', 'route' => 'credencial-listar'],
                        ['title' => 'Máquina Cartão',     'route' => 'maquinas-cartao'],
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
                        ['title' => 'Exibir máquinas',    'route' => 'maquinas'],
                        ['title' => 'Transações',          'route' => 'maquinas-transacoes'],
                        ['title' => 'Acumulado',           'route' => 'maquinas-acumulado'],
                        ['title' => 'Histórico de Resets', 'route' => 'maquinas-resets-historico'],
                        ['title' => 'Relatórios',          'route' => 'relatorio-view'],
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
                'active_routes' => ['cliente-credencial-criar-efi', 'cliente-credencial-criar-pagbank', 'cliente-credencial-listar', 'cliente-maquinas-cartao'],
                'sub_menu'      => [
                    ['title' => 'Credencial EFI',     'route' => 'cliente-credencial-criar-efi'],
                    ['title' => 'Credencial PagBank', 'route' => 'cliente-credencial-criar-pagbank'],
                    ['title' => 'Editar credenciais', 'route' => 'cliente-credencial-listar'],
                    ['title' => 'Máquina Cartão',     'route' => 'cliente-maquinas-cartao'],
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
                    'active_routes' => ['clientes-maquinas', 'cliente-relatorio-view', 'clientes-maquinas-transacoes', 'clientes-maquinas-acumulado', 'clientes-maquinas-resets-historico'],
                    'sub_menu'      => [
                        ['title' => 'Exibir máquinas',    'route' => 'clientes-maquinas'],
                        ['title' => 'Relatórios',          'route' => 'cliente-relatorio-view'],
                        ['title' => 'Transações',          'route' => 'clientes-maquinas-transacoes'],
                        ['title' => 'Acumulado',           'route' => 'clientes-maquinas-acumulado'],
                        ['title' => 'Histórico de Resets', 'route' => 'clientes-maquinas-resets-historico'],
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
