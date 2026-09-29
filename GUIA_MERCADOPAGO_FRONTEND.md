# Roteiro: suporte a Mercado Pago no front (`projeto_maquina`)

Este documento orienta as mudanças necessárias neste projeto (o painel Blade/PHP que
consome a API) para dar suporte ao QR Code do Mercado Pago, cuja API já está pronta em
`projeto_maquina_api`. Hoje o front só conhece `efi` e `pagbank` — os pontos abaixo estão
todos hardcoded para esses dois tipos e precisam ganhar uma terceira opção.

## Contratos da API (já prontos, sem mudanças)

**Credencial** — `POST /credApiPix` (igual ao fluxo existente, só muda o `tipo_cred`):
```
{ "id_cliente": 1, "client_id": "<segredo do webhook>", "client_secret": "<access token>", "tipo_cred": "mercadopago" }
```
Atenção aos rótulos: para Mercado Pago, `client_id` = segredo de assinatura do webhook (não é
o "Client ID" que aparece no painel do MP) e `client_secret` = o Access Token. Não há upload de
certificado (assim como PagBank).

**Loja** (passo novo, só existe para Mercado Pago) — `POST /mercadopagoLoja`:
```
Request:  { "id_cliente": 1 }
201:      { "message": "...", "response": { "id", "id_cliente", "mp_user_id", "mp_store_id", "external_store_id", "created_at", "updated_at" } }
400:      credencial mercadopago não cadastrada para o cliente
```
`GET /mercadopagoLoja` (lista todas) e `GET /mercadopagoLoja/{id}` também existem.

**QR Code (POS)** — `POST /mercadopagoQr`:
```
Request:  { "id_cliente": 1, "select_local": 3, "select_maquina": 7 }
201:      { "message": "...", "response": { "id", "id_cliente", "id_local", "id_maquina", "id_mercadopago_loja", "mp_pos_id", "external_pos_id", "qr_image" (base64 PNG), "qr_data", "ativo" } }
400:      sem credencial mercadopago OU loja ainda não cadastrada para o cliente
```
`GET /mercadopagoQr` (aceita `?sem_imagem=1`), `GET /mercadopagoQr/{id}`, `DELETE /mercadopagoQr/{id}`.

Fluxo obrigatório, nessa ordem: **1) credencial → 2) loja (uma vez por cliente) → 3) QR/POS
(uma vez por máquina)**. O passo 3 falha com 400 se o passo 2 não tiver sido feito.

## O que muda em cada arquivo

### 1. Credenciais

- `routes/web.php` — hoje só existem `credencial-criar-efi` / `credencial-criar-pagbank` /
  `credencial-editar-efi` / `credencial-editar-pagbank` (linhas ~131-139, e o espelho em
  `clientes-credenciais`, ~121-129). Adicionar `credencial-criar-mercadopago` e
  `credencial-editar-mercadopago` nos dois blocos.
- `app/Http/Controllers/CredenciaisController.php`:
  - Adicionar `criarCredencialMercadopago()` / `editarCredencialMercadopago($id)`, espelhando
    `criarCredencialPagbank()` / `editarCredencialPagbank()` (mesma estrutura, sem certificado).
  - `registrarCredencial()` (linha ~63) e `atualizarCredencial()` (linha ~144) têm um
    `if/else` que trata `pagbank` diferente de `efi` (lowercase do client_id, certificado
    condicional) — acrescentar o ramo `mercadopago` (sem certificado, sem lowercase).
- Nova pasta de views `Admin/Credenciais/MercadoPago/create.blade.php` e `edit.blade.php` —
  copiar a estrutura de `Admin/Credenciais/PagBank/*` (sem upload de arquivo), mas com os
  rótulos corretos: "Segredo do Webhook" (campo `client_id`) e "Access Token" (campo
  `client_secret`), para não confundir o operador com a nomenclatura do painel do MP.
  Repetir em `Clientes/Credenciais/MercadoPago/*`.
- `Admin/Credenciais/index.blade.php` (e o espelho em `Clientes/Credenciais/index.blade.php`):
  - Filtro `tipo_cred` (linhas ~37-41): acrescentar `<option value="mercadopago">Mercado
    Pago</option>`.
  - Modal SweetAlert2 de "Criar credencial" (linhas ~135-163): acrescentar
    `mercadopago: 'Mercado Pago'` em `inputOptions` e o redirecionamento para
    `credencial-criar-mercadopago`.
  - Badge da listagem (linha ~74) e link de edição (linhas ~79-88): acrescentar o ramo
    `mercadopago` (nova classe CSS `badge-cred-mercadopago`, e link para
    `credencial-editar-mercadopago`).

### 2. Loja (tela nova, sem equivalente na Efí)

Como a Efí não tem esse conceito, é uma tela nova, não uma extensão de tela existente.

- Rotas novas (`routes/web.php`, mesmo prefixo `credenciais` ou um novo `mercadopago`):
  `mercadopago-loja` (listar), `mercadopago-loja/criar` (form), `mercadopago-loja/registrar`
  (POST). Provavelmente só faz sentido no lado Admin (loja é por cliente, cadastro
  administrativo), mas replicar em `clientes-mercadopago-loja` se o cliente também gerencia
  suas próprias integrações (ver como `credenciais` está espelhado hoje).
- Controller novo `MercadopagoLojaController.php` (ou método dentro de
  `CredenciaisController`, se preferir manter tudo junto): `index()`, `criar()`,
  `registrar()` — este último só posta `id_cliente` para `POST /mercadopagoLoja`.
- Service novo `app/Services/MercadopagoLojaService.php`, wrapper fino sobre `ApiClient`
  (mesmo padrão de `CredApiPixService`).
- Views novas: `Admin/Mercadopago/Loja/create.blade.php` (select2 de cliente + botão
  "Cadastrar Loja") e `index.blade.php` (listagem simples: cliente, `mp_store_id`,
  `external_store_id`, data de criação).

### 3. QR Code / POS (estender a tela existente da Efí)

Diferente da loja, aqui a tela já existe (`Admin/QR/*`) e o objeto (`QR Code`) é o mesmo
conceito visual nos dois gateways — só a origem dos dados muda.

- `app/Http/Controllers/QrCodeController.php::registrarQr()` (linha ~84-141): o ponto crítico
  é o filtro hardcoded na linha ~114-116:
  ```php
  $credencial = array_filter($credenciais, function($item) use($id_cliente){
      return $item['id_cliente'] == $id_cliente && $item['tipo_cred'] == "efi";
  });
  ```
  Isso precisa virar dinâmico: adicionar um campo `gateway` (`efi` ou `mercadopago`) no
  formulário e usar esse valor no filtro, decidindo também para qual endpoint enviar
  (`POST /QRCode` para efi, `POST /mercadopagoQr` para mercadopago).
- `Admin/QR/create.blade.php`: acrescentar um seletor "Gateway" (Efí / Mercado Pago) junto
  dos selects de local/máquina já existentes.
- Novo `app/Services/MercadopagoQrService.php` (wrapper sobre `/mercadopagoQr`, mesmo
  formato de `QrCodeService`), e o controller passa a chamar um ou outro service conforme o
  `gateway` escolhido.
- Tratar o erro 400 "loja ainda não cadastrada" com uma mensagem clara (SweetAlert) sugerindo
  ir cadastrar a loja primeiro — evita o operador ficar sem entender por que o QR não foi
  criado.
- `Admin/QR/index.blade.php`: decidir se a listagem mistura os dois gateways (precisaria de
  uma coluna "Gateway") ou se ganha abas/filtro separado — mais simples manter uma única
  listagem com uma coluna extra, já que o campo `qr_image` tem o mesmo formato nos dois casos.

### 4. Menu e breadcrumbs

- `app/Helpers/MenuHelper.php`:
  - O item de menu está hardcoded como **`'Gerar QR (Efí)'`** (linhas ~152-160 admin, e
    ~265-273 cliente) — renomear para algo genérico ("Gerar QR Code"), já que a tela passa a
    suportar os dois gateways.
  - Acrescentar item "Loja Mercado Pago" na seção "Criar", ao lado de "Credenciais" e
    "Máquina Cartão (Pagbank)".
  - `getRouteBreadcrumbs()` precisa de uma entrada por rota nova (`credencial-criar-mercadopago`,
    `credencial-editar-mercadopago`, `mercadopago-loja`, `mercadopago-loja-criar`, etc.) — sem
    isso, o breadcrumb cai silenciosamente para só a home.

### 5. Modo mock (dev local)

`app/Mocks/MockRouter.php` já trata os recursos genéricos `credApiPix`/`QRCode` — conferir se
isso é suficiente ou se precisa de entradas específicas para `mercadopagoLoja`/`mercadopagoQr`
em `MockRouter.php`/`MockData.php`, para quem desenvolve localmente sem bater na API real.

## Ordem sugerida de implementação

1. **Credenciais** — menor risco, permite testar a comunicação com o Mercado Pago isoladamente.
2. **Loja** — tela nova e simples, depende só das credenciais.
3. **QR/POS** — estende a tela existente, depende da loja já existir.
4. **Menu/breadcrumbs** — ajuste cosmético, fazer por último.
5. **Mock router** — só necessário para desenvolvimento local sem a API real.
