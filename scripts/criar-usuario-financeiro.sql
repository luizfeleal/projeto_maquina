-- ============================================================================
-- Script: criação de usuário do grupo "financeiro" (id_grupo_acesso = 9)
-- Executar no banco de dados da API (não no MySQL local do app Laravel).
--
-- IMPORTANTE antes de rodar:
--   1. Confira a estrutura real das tabelas com:
--        DESCRIBE grupos_acesso;
--        DESCRIBE usuarios;
--        DESCRIBE acessos_tela;
--      e ajuste nomes de colunas caso divirjam do script abaixo.
--   2. Troque 'TROQUE_ESSA_SENHA' por uma senha forte antes de executar.
--   3. Se `acessos_tela` tiver uma PK própria (ex.: id_acesso_tela) diferente
--      de id_grupo_acesso, remova o ON DUPLICATE KEY da 3ª seção ou ajuste.
-- ============================================================================

-- 1) Grupo de acesso "financeiro"
INSERT INTO grupos_acesso (id_grupo_acesso, grupo_acesso_nome)
VALUES (9, 'financeiro')
ON DUPLICATE KEY UPDATE grupo_acesso_nome = VALUES(grupo_acesso_nome);

-- 2) Usuário do grupo financeiro
INSERT INTO usuarios
    (id_grupo_acesso, id_cliente, usuario_nome, usuario_email,
     usuario_login, usuario_senha, ativo, usuario_ultimo_acesso,
     data_inclusao, data_alteracao)
VALUES
    (9, NULL, 'Financeiro', 'financeiro@suaempresa.com',
     'financeiro', 'TROQUE_ESSA_SENHA', 1, NOW(),
     NOW(), NOW());

-- 3) Permissões de tela para as rotas financeiro-* (routes/web.php)
INSERT INTO acessos_tela (id_grupo_acesso, acesso_tela_viewname, acesso_tela_nome, ativo)
VALUES
    (9, 'financeiro-home',               'Financeiro - Início',            1),
    (9, 'financeiro-despesas',           'Financeiro - Despesas',          1),
    (9, 'financeiro-despesas-criar',     'Financeiro - Criar despesa',     1),
    (9, 'financeiro-despesas-registrar', 'Financeiro - Registrar despesa', 1),
    (9, 'financeiro-despesas-excluir',   'Financeiro - Excluir despesa',   1),
    (9, 'financeiro-estoque',            'Financeiro - Estoque',           1),
    (9, 'financeiro-estoque-criar',      'Financeiro - Criar produto',     1),
    (9, 'financeiro-estoque-registrar',  'Financeiro - Registrar produto', 1),
    (9, 'financeiro-estoque-excluir',    'Financeiro - Excluir produto',   1),
    (9, 'financeiro-estoque-detalhar',   'Financeiro - Detalhar produto',  1),
    (9, 'financeiro-estoque-editar',     'Financeiro - Editar produto',    1),
    (9, 'financeiro-estoque-atualizar',  'Financeiro - Atualizar produto', 1);
