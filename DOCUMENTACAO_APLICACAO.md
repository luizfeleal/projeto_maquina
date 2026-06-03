# Documentacao da Aplicacao

**Projeto:** Plataforma Swift Pay Solucoes para gestao de maquinas, clientes, QR Codes, credenciais e relatorios  
**Tipo:** Aplicacao web administrativa e portal do cliente  
**Tecnologia principal:** Laravel 8 / PHP  
**Data da documentacao:** 18/05/2026

## 1. Visao geral

Esta aplicacao e uma plataforma web para administracao de maquinas de pagamento/operacao, associacao de clientes a locais, geracao de QR Codes, configuracao de credenciais de pagamento, consulta de transacoes e emissao de relatorios.

O sistema possui dois perfis principais de uso:

- **Administrador:** possui acesso amplo para cadastrar clientes, locais, maquinas, credenciais, QR Codes, maquinas de cartao, liberar jogadas e consultar relatorios gerais.
- **Cliente:** possui acesso restrito aos proprios recursos, podendo consultar suas maquinas, relatorios, transacoes, QR Codes, credenciais e operacoes permitidas conforme grupo de acesso.

A aplicacao funciona como interface web e consome uma API externa configurada no ambiente. As regras de dados, persistencia principal e operacoes de hardware sao executadas por essa API.

## 2. Objetivo do sistema

O objetivo do sistema e centralizar a operacao de maquinas vinculadas a clientes e locais, permitindo:

- Cadastro e manutencao de clientes.
- Cadastro e manutencao de locais de operacao.
- Vinculo entre clientes e locais.
- Cadastro, consulta, edicao e remocao de maquinas.
- Associacao de maquinas fisicas/placas e maquinas de cartao.
- Configuracao de credenciais EFI e PagBank.
- Geracao, visualizacao, download e exclusao de QR Codes.
- Consulta de transacoes, saldos e acumulados.
- Emissao de relatorios operacionais e financeiros.
- Liberacao manual de jogadas/creditos para maquinas.
- Controle de acesso por usuario, grupo e permissao de tela.

## 3. Publico-alvo

O sistema atende dois grupos principais:

- **Equipe operacional/administrativa:** responsavel por cadastrar clientes, configurar locais, associar maquinas, acompanhar operacoes, gerar QR Codes e consultar relatorios consolidados.
- **Clientes finais:** responsaveis por acompanhar suas maquinas, consultar transacoes e relatorios, manter credenciais quando permitido e executar a liberacao de jogadas quando habilitada.

## 4. Arquitetura da aplicacao

A aplicacao foi desenvolvida em Laravel e segue a estrutura padrao MVC:

- **Controllers:** recebem as requisicoes web, validam fluxos e direcionam para as telas.
- **Services:** concentram a comunicacao com a API externa e regras de apoio.
- **Views Blade:** renderizam as telas de administracao, cliente e login.
- **Middleware de permissao:** valida se o usuario logado pode acessar a rota solicitada.
- **Assets publicos:** CSS, JavaScript, imagens e arquivos estaticos usados pela interface.

### 4.1 Estrutura principal de diretorios

| Diretorio/arquivo | Finalidade |
| --- | --- |
| `app/Http/Controllers` | Controllers das areas Admin, Cliente, Login, Maquinas, QR, Credenciais e Relatorios |
| `app/Services` | Servicos de integracao com a API externa |
| `app/Http/Middleware/ChecarPermissoes.php` | Middleware de controle de acesso por permissao |
| `resources/views` | Telas Blade da aplicacao |
| `routes/web.php` | Rotas web da aplicacao |
| `routes/api.php` | Rotas auxiliares de API expostas por esta aplicacao |
| `public/site` | Arquivos estaticos da interface |
| `database/migrations` | Migrations basicas do Laravel e tabelas locais existentes |
| `composer.json` | Dependencias PHP |
| `package.json` | Dependencias front-end e scripts de build |

## 5. Tecnologias e dependencias

### 5.1 Back-end

- PHP `^7.3` ou `^8.0`
- Laravel Framework `^8.75`
- Laravel Sanctum `^2.11`
- Guzzle HTTP `^7.0.1`
- PhpSpreadsheet `^2.2`

### 5.2 Front-end

- Laravel Mix `^6.0.6`
- Bootstrap `^5.3.3`
- jQuery
- DataTables
- Select2
- Font Awesome
- Sass

### 5.3 Integracoes externas

A aplicacao depende de uma API externa, configurada pela variavel:

- `APP_URL_API`

Essa API e utilizada para autenticar a aplicacao e manipular entidades como clientes, usuarios, locais, maquinas, extratos, logs, credenciais, QR Codes e operacoes de hardware.

## 6. Configuracoes de ambiente

As principais variaveis de ambiente utilizadas pela aplicacao sao:

| Variavel | Finalidade |
| --- | --- |
| `APP_NAME` | Nome da aplicacao Laravel |
| `APP_ENV` | Ambiente de execucao |
| `APP_KEY` | Chave criptografica do Laravel |
| `APP_DEBUG` | Habilita/desabilita modo debug |
| `APP_URL` | URL publica da aplicacao web |
| `APP_URL_API` | URL base da API externa |
| `EMAIL_API` | Email usado para autenticar a aplicacao na API externa |
| `PASSWORD_API` | Senha usada para autenticar a aplicacao na API externa |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Configuracoes de banco local Laravel, quando utilizado |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | Configuracoes para envio de email |
| `CACHE_DRIVER` | Driver de cache usado para armazenar o token da API |
| `SESSION_DRIVER` | Driver de sessao dos usuarios logados |

## 7. Autenticacao e controle de acesso

O login e feito pela tela `/login`.

O usuario informa login e senha. A aplicacao consulta a API externa por meio do servico de usuarios e, em caso de sucesso, cria uma sessao contendo:

- `id_usuario`
- `id_grupo_acesso`
- `usuario_nome`
- `grupo_nome`
- `id_cliente`

Conforme o grupo de acesso:

- Usuarios do grupo `admin` sao redirecionados para a area administrativa.
- Demais usuarios sao redirecionados para a area do cliente.

O controle de acesso e feito pelo middleware `permission`, implementado em `ChecarPermissoes`. Ele consulta as permissoes de tela da API externa e verifica se o grupo do usuario possui permissao para a rota acessada.

Quando o usuario nao esta autenticado, ele e redirecionado para o login. Quando nao possui permissao para a tela, recebe mensagem de acesso negado.

## 8. Modulos funcionais

## 8.1 Home

A Home apresenta a entrada da area administrativa ou da area do cliente.

Rotas principais:

- Admin: `/home`
- Cliente: `/cliente-home`

## 8.2 Clientes / Usuarios

Modulo utilizado pelo administrador para cadastrar, consultar, editar e excluir clientes.

Funcionalidades:

- Listar clientes.
- Cadastrar novo cliente.
- Criar automaticamente usuario de acesso para o cliente.
- Definir permissoes relacionadas a EFI e PagBank.
- Editar dados do cliente.
- Atualizar grupo de acesso conforme permissoes selecionadas.
- Visualizar detalhes do cliente, locais vinculados e credenciais.
- Excluir cliente quando nao houver local associado.

Rotas principais:

- `/usuarios`
- `/usuarios/criar`
- `/usuarios/detalhar/{id}`
- `/usuarios/editar/{id}`
- `/usuarios/registrar`
- `/usuarios/atualizar`
- `/usuarios/excluir`

## 8.3 Locais

Modulo utilizado para cadastrar e gerenciar locais de operacao.

Funcionalidades:

- Criar local.
- Vincular um ou mais clientes ao local.
- Definir cliente principal do local no momento do cadastro.
- Listar locais.
- Consultar detalhes do local.
- Incluir usuarios/clientes adicionais em um local.
- Excluir local, desde que nao existam maquinas associadas.

Rotas principais:

- `/local`
- `/local/criar`
- `/local/detalhar/{id}`
- `/local/incluirUsuario`
- `/local/registrarUsuarioLocal`
- `/local/excluir`

## 8.4 Maquinas

Modulo responsavel por gerenciar maquinas vinculadas aos locais.

Funcionalidades administrativas:

- Listar maquinas com dados de ultima transacao.
- Cadastrar nova maquina.
- Selecionar placas disponiveis na API/hardware.
- Visualizar detalhes da maquina.
- Editar maquina.
- Remover maquina.
- Consultar transacoes.
- Consultar acumulado.
- Configurar bloqueio de jogada EFI e PagBank.
- Verificar associacao com maquina de cartao.
- Verificar existencia de QR Code ativo.

Funcionalidades da area do cliente:

- Listar apenas maquinas vinculadas aos locais do cliente.
- Consultar transacoes e acumulados.
- Editar dados permitidos.
- Liberar jogadas quando permitido.

Rotas principais:

- Admin: `/maquinas`
- Admin: `/maquinas/criar`
- Admin: `/maquinas/visualizar`
- Admin: `/maquinas/editar`
- Admin: `/maquinas/transacoes`
- Admin: `/maquinas/acumulado`
- Cliente: `/clientes-maquinas`
- Cliente: `/clientes-maquinas/transacoes`
- Cliente: `/clientes-maquinas/acumulado`

## 8.5 Maquinas de cartao

Modulo para associar dispositivos de cartao a maquinas.

Funcionalidades:

- Listar maquinas de cartao cadastradas.
- Associar uma maquina de cartao a uma maquina.
- Impedir associacao ativa duplicada do mesmo dispositivo.
- Inativar ou reativar associacoes.
- Excluir maquina de cartao.

Rotas principais:

- Admin: `/maquinas/maquinasCartao`
- Admin: `/maquinas/maquinasCartao/criar`
- Cliente: `/clientes-maquinas/maquinasCartao`
- Cliente: `/clientes-maquinas/maquinasCartao/criar`

## 8.6 Credenciais EFI e PagBank

Modulo usado para cadastrar e manter credenciais de pagamento por cliente.

Tipos suportados:

- EFI
- PagBank

Funcionalidades:

- Listar credenciais.
- Filtrar por cliente e tipo.
- Criar credencial EFI.
- Criar credencial PagBank.
- Editar credencial EFI.
- Editar credencial PagBank.
- Excluir credencial.
- Enviar certificado quando o tipo de credencial exigir.

Regras observadas:

- Cada cliente nao deve possuir credencial duplicada do mesmo tipo.
- Credenciais PagBank normalizam o `client_id` para letras minusculas.
- Credenciais EFI podem envolver envio de certificado.

Rotas principais:

- Admin: `/credenciais`
- Admin: `/credenciais/criar/efi`
- Admin: `/credenciais/criar/pagbank`
- Cliente: `/clientes-credenciais`
- Cliente: `/clientes-credenciais/criar/efi`
- Cliente: `/clientes-credenciais/criar/pagbank`

## 8.7 QR Codes

Modulo usado para gerar, visualizar, baixar e excluir QR Codes associados a local e maquina.

Funcionalidades:

- Listar QR Codes.
- Criar novo QR Code.
- Validar se ja existe QR Code para o mesmo local e maquina.
- Validar se o local possui cliente principal.
- Validar se o cliente possui credencial EFI cadastrada.
- Gerar imagem final do QR Code com fundo padrao e identificacao da placa.
- Baixar QR Code.
- Excluir QR Code.

Observacao: a imagem final do QR Code usa o arquivo de fundo `public/site/img/qr-background.png`. Caso exista a fonte `public/site/fonts/DejaVuSans.ttf`, ela e usada para renderizar o texto da placa na imagem; caso contrario, a aplicacao utiliza fonte padrao do GD.

Rotas principais:

- Admin: `/qr`
- Admin: `/qr/criar`
- Admin: `/qr/download`
- Cliente: `/clientes-qr`
- Cliente: `/clientes-qr/criar`
- Cliente: `/clientes-qr/download`

## 8.8 Relatorios

Modulo para consulta e exportacao de relatorios.

Relatorios disponiveis:

- **Maquinas On/Off:** exibe maquinas online e offline, com local e ultimo contato.
- **Total de Transacoes:** consolida transacoes por filtros como cliente, maquina, tipo e periodo.
- **Taxas de Desconto:** apresenta valores relacionados a taxas/descontos nas transacoes.
- **Relatorio de Erros:** lista logs de erro, com filtros por maquina e local quando informados.

Funcionalidades:

- Exibir relatorios em tela.
- Filtrar dados por cliente, maquina, local, tipo de transacao e periodo, conforme relatorio.
- Exportar relatorios para XLSX.
- Calcular totais e subtotais em determinados relatorios.

Rotas principais:

- Admin: `/relatorios`
- Admin: `/relatorios/exibir`
- Admin: `/relatorios/download`
- Cliente: `/clientes-relatorio`
- Cliente: `/clientes-relatorio/exibir`
- Cliente: `/clientes-relatorio/download`

## 8.9 Liberacao de jogada

Modulo para liberacao manual de jogada/credito em uma maquina.

Funcionalidades:

- Selecionar maquina ou placa.
- Informar valor de credito.
- Gerar identificador de transacao no formato `CD` + numero aleatorio.
- Enviar solicitacao para a API/hardware.
- Exibir retorno de sucesso ou erro.

Rotas principais:

- Admin: `/maquinas/liberarJogada`
- Admin: `/maquinas/liberarJogadaRegistrar`
- Cliente: `/clientes-maquinas/viewLiberarJogada`
- Cliente: `/clientes-maquinas/liberarJogada`

## 8.10 Redefinicao de senha

Fluxo para redefinir senha de usuario.

Funcionalidades:

- Tela de solicitacao de redefinicao.
- Envio de email com link de redefinicao.
- Validacao por token baseado no email.
- Tela para informar nova senha e confirmacao.
- Atualizacao da senha pela API externa.

Rotas principais:

- `/redefinirView`
- `/redefinirConfirmacao`
- `/login/senha/alterar`
- `/criarSenhaRegistrar`

## 9. Integracao com API externa

A maior parte das operacoes da aplicacao e feita por chamadas HTTP para uma API externa.

Antes de chamar a API, a aplicacao obtem um token em:

- `POST {APP_URL_API}/auth/login`

As credenciais usadas sao:

- `EMAIL_API`
- `PASSWORD_API`

O token retornado e armazenado em cache por 50 minutos com a chave `api_token`.

Principais endpoints consumidos:

| Recurso | Endpoint base |
| --- | --- |
| Autenticacao | `/auth/login` |
| Clientes | `/clientes` |
| Usuarios | `/usuarios` |
| Locais | `/locais` |
| Cliente x Local | `/clienteLocal` |
| Maquinas | `/maquinas` |
| Maquinas com ultima transacao | `/totalMaquinas` |
| Placas disponiveis | `/hardware/maquinasDisponiveis` |
| Liberacao de jogada | `/hardware/liberarJogada` |
| Maquinas de cartao | `/maquinasCartao` |
| QR Codes | `/qrCode` |
| Credenciais de API Pix | `/credApiPix` |
| Extrato de maquina | `/extratoMaquina` |
| Relatorio total de transacoes | `/relatorioTotalTransacoes` |
| Totais de transacoes | `/relatorioTotalTransacoesTotal` |
| Taxas de transacoes | `/relatorioTotalTransacoesTaxa` |
| Logs | `/logs` |
| Grupos de acesso | `/gruposAcesso` |
| Acessos de tela | `/acessosTela` |

## 10. Rotas expostas pela aplicacao

### 10.1 Rotas web publicas

- `/` redireciona para login.
- `/login` exibe a tela de login.
- `/autenticar` autentica usuario.
- `/logout` encerra sessao.
- `/redefinirView` exibe solicitacao de redefinicao de senha.
- `/redefinirConfirmacao` envia email de redefinicao.
- `/login/senha/alterar` exibe tela de nova senha.
- `/criarSenhaRegistrar` registra nova senha.

### 10.2 Rotas web protegidas

As rotas de Home, Maquinas, Clientes, Locais, QR, Credenciais, Relatorios e Liberacao de Jogada usam o middleware `permission`.

O acesso depende de:

- Usuario autenticado em sessao.
- Permissao cadastrada para o grupo de acesso na API externa.
- Nome da rota compativel com a permissao de tela.

### 10.3 Rotas API locais

Esta aplicacao tambem possui rotas auxiliares em `routes/api.php`:

- `GET /api/user` protegido por Sanctum.
- `GET /api/gerarIdPlaca` para gerar/coletar placa.
- `GET /api/getToken` para obter token da API externa.

## 11. Regras de negocio observadas

- Cliente com local associado nao pode ser excluido.
- Local com maquina associada nao pode ser excluido.
- Maquina de cartao ativa nao pode ser duplicada para o mesmo dispositivo.
- QR Code nao pode ser gerado se ja existir QR para o mesmo local e maquina.
- QR Code exige que o local tenha cliente principal.
- QR Code exige credencial EFI cadastrada para o cliente.
- Usuarios recebem grupo de acesso conforme permissoes selecionadas no cadastro/edicao do cliente.
- O acesso as telas depende das permissoes cadastradas por grupo.
- O portal do cliente filtra dados conforme o `id_cliente` da sessao e os locais vinculados.

## 12. Exportacao de dados

A aplicacao utiliza a biblioteca PhpSpreadsheet para gerar arquivos XLSX.

Os relatorios exportaveis incluem:

- Total de transacoes.
- Taxas de desconto.

Durante a exportacao, o sistema:

- Monta cabecalhos a partir dos dados.
- Aplica formatacao basica.
- Formata valores monetarios quando aplicavel.
- Adiciona totais em relatorios especificos.

## 13. Interface e navegacao

A interface usa Bootstrap, DataTables e Select2 para navegacao, tabelas e formularios.

Menus principais da area administrativa:

- Home
- Criar
- Local
- Minhas maquinas
- Gerar QR
- Usuarios
- Liberar Jogada
- Sair

Menus principais da area do cliente:

- Home
- Criar
- Gerar QR
- Minhas maquinas
- Liberar Jogada
- Sair

## 14. Instalacao e execucao

### 14.1 Pre-requisitos

- PHP compativel com Laravel 8.
- Composer.
- Node.js e npm.
- Extensoes PHP necessarias ao Laravel.
- Extensao GD para manipulacao de imagem do QR Code.
- Acesso a API externa configurada em `APP_URL_API`.
- Credenciais validas da API externa em `EMAIL_API` e `PASSWORD_API`.

### 14.2 Passos de instalacao

1. Instalar dependencias PHP:

```bash
composer install
```

2. Instalar dependencias front-end:

```bash
npm install
```

3. Configurar arquivo `.env` com as variaveis do ambiente.

4. Gerar chave da aplicacao, se necessario:

```bash
php artisan key:generate
```

5. Executar migrations se o ambiente utilizar banco local:

```bash
php artisan migrate
```

6. Compilar assets:

```bash
npm run dev
```

ou, para producao:

```bash
npm run prod
```

7. Iniciar servidor local para desenvolvimento:

```bash
php artisan serve
```

## 15. Operacao em producao

Para operacao em producao, recomenda-se:

- Configurar `APP_ENV=production`.
- Configurar `APP_DEBUG=false`.
- Configurar corretamente `APP_URL`.
- Garantir conectividade com `APP_URL_API`.
- Configurar email SMTP para redefinicao de senha.
- Garantir permissao de escrita nas pastas `storage` e `bootstrap/cache`.
- Habilitar cache adequado para sessoes e token da API.
- Configurar servidor web apontando para a pasta `public`.
- Executar build de assets em modo producao.

## 16. Logs e rastreabilidade

A aplicacao registra logs pelo mecanismo padrao do Laravel, normalmente em:

- `storage/logs/laravel.log`

O middleware de permissao registra eventos como:

- Execucao da verificacao de permissao.
- Rota acessada.
- Grupo do usuario.
- Resultado da verificacao.
- Negativas de acesso.

Os relatorios de erro tambem consomem registros da API externa por meio do servico de logs.

## 17. Consideracoes de seguranca

- O sistema utiliza sessao Laravel para manter usuario autenticado.
- O token da API externa e armazenado em cache por 50 minutos.
- As telas protegidas dependem do middleware de permissao.
- As credenciais EFI/PagBank devem ser protegidas no ambiente da API e no transporte HTTPS.
- Recomenda-se que a aplicacao e a API externa sejam acessadas apenas via HTTPS em producao.
- O arquivo `.env` nao deve ser versionado ou disponibilizado ao cliente final sem tratamento adequado de senhas e chaves.
- Permissoes de tela devem ser revisadas por grupo de acesso antes da entrada em producao.

## 18. Limitacoes e pontos de atencao

- A aplicacao depende fortemente da disponibilidade da API externa.
- Sem `APP_URL_API`, `EMAIL_API` e `PASSWORD_API` validos, as principais telas nao carregam dados.
- O banco local possui migrations basicas, mas a persistencia principal das entidades de negocio e feita pela API externa.
- A geracao visual do QR Code depende da extensao GD do PHP.
- A renderizacao de texto no QR Code melhora quando a fonte TTF esperada esta disponivel em `public/site/fonts/DejaVuSans.ttf`.
- As rotas protegidas dependem do cadastro correto de permissoes de tela na API externa.

## 19. Criterios de aceite atendidos

Com base na implementacao analisada, a aplicacao contempla:

- Login e logout de usuarios.
- Separacao de area administrativa e area do cliente.
- Controle de acesso por grupo e permissao de rota.
- Cadastro e manutencao de clientes.
- Cadastro e manutencao de locais.
- Associacao entre clientes e locais.
- Cadastro e manutencao de maquinas.
- Associacao de maquinas de cartao.
- Cadastro e manutencao de credenciais EFI e PagBank.
- Geracao e download de QR Codes.
- Consulta de transacoes e acumulados.
- Relatorios operacionais e financeiros.
- Exportacao de relatorios para XLSX.
- Liberacao manual de jogada.
- Redefinicao de senha por email.

## 20. Responsabilidades por camada

| Camada | Responsabilidade |
| --- | --- |
| Interface Blade | Apresentar telas, formularios, menus, tabelas e mensagens |
| Controllers | Controlar fluxo web, tratar requests, selecionar views e validar regras locais |
| Services | Integrar com API externa e encapsular chamadas HTTP |
| Middleware | Validar autenticacao e permissao de acesso |
| API externa | Persistir dados, autenticar aplicacao, executar regras principais e operacoes de hardware |
| Assets publicos | Estilos, scripts, imagens e recursos visuais da aplicacao |

## 21. Conclusao

A aplicacao entrega uma plataforma operacional para gestao de clientes, locais, maquinas, QR Codes, credenciais, transacoes, relatorios e liberacao de jogadas. Sua arquitetura centraliza a experiencia do usuario em uma interface Laravel e delega a persistencia e integracoes criticas para uma API externa autenticada.

Para uso em ambiente produtivo, os principais pontos de configuracao sao a conectividade com a API externa, as credenciais de autenticacao da API, o servico de email, as permissoes de acesso por grupo e a correta publicacao dos assets da interface.
