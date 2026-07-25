# Feature Specification: Reset Parcial por Dispositivo (Cobrança Parcial)

**Feature Branch**: `001-reset-parcial-por-dispositivo`

**Created**: 2026-06-08

**Status**: Draft

**Input**: Implementação de uma função de "reset" por dispositivo para cobranças parciais. É preciso que, em extrato/acumulado, tenha um botão de reset e uma informação de total (que seria o acumulado) e o valor que está no dia da aferição. A ideia é ter marcado quando inicia e quando começa o saldo.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Visualizar Extrato com Saldo do Período (Priority: P1)

Um operador (admin ou cliente) acessa a tela de Acumulado de Máquinas e precisa visualizar, para cada máquina, três valores: o **Total Acumulado** (contador principal da API), o valor da **Última Coleta** (snapshot registrado no último reset parcial) e o **Saldo do Período** (diferença calculada pela API). Isso permite saber quanto foi arrecadado desde a última aferição.

**Why this priority**: É o core da feature. Sem essa visualização, a funcionalidade não entrega valor.

**Independent Test**: Pode ser testado acessando `/maquinas/acumulado` com ao menos um reset registrado na API; a tabela deve exibir as três colunas corretamente.

**Acceptance Scenarios**:

1. **Given** que a API retorna para a máquina X `total_maquina = 750.00`, `ultima_coleta = 500.00` e `saldo_periodo = 250.00`, **When** o operador acessa a tela de Acumulado, **Then** a linha da máquina X exibe: Total = R$ 750,00 | Última Coleta = R$ 500,00 | Saldo do Período = R$ 250,00.
2. **Given** que a API retorna `tem_reset = false` para a máquina Y, **When** o operador acessa a tela de Acumulado, **Then** a coluna "Última Coleta" exibe "Sem reset" e o "Saldo do Período" exibe o próprio total acumulado.
3. **Given** que o total acumulado é R$ 1.000,00 e a última coleta é R$ 1.000,00 (reset feito agora), **When** visualizado, **Then** o Saldo do Período é R$ 0,00.

---

### User Story 2 - Realizar Reset Parcial com Double-Check (Priority: P1)

Um operador clica no botão "Reset" de uma máquina na tela de Acumulado. A aplicação Laravel exibe um modal de confirmação (double-check) exigindo uma segunda ação deliberada antes de executar. Ao confirmar, a aplicação chama o endpoint `POST /maquinas/{id_maquina}/reset-parcial` da API externa, que registra o snapshot do valor acumulado atual como "Última Coleta" **sem alterar o contador principal** (`total_maquina`).

**Why this priority**: É a ação central da feature; sem ela, a visualização do Saldo do Período não tem como ser iniciada.

**Independent Test**: Pode ser testado clicando no botão Reset, confirmando o modal, e verificando via API que um novo registro existe em `maquina_resets_parciais` e que `maquina_ultima_coleta` foi atualizado.

**Acceptance Scenarios**:

1. **Given** que o operador clica em "Reset" para a máquina X, **When** o modal de double-check é exibido e o operador cancela, **Then** nenhuma chamada é feita à API e a tela permanece inalterada.
2. **Given** que o operador confirma o reset no modal, **When** a aplicação chama `POST /maquinas/{id_maquina}/reset-parcial`, **Then** a API persiste um registro de histórico com `valor_ultima_coleta`, `valor_acumulado_total`, `realizado_por` e `created_at`, e atualiza `maquina_ultima_coleta` na entidade máquina.
3. **Given** que o reset foi confirmado com sucesso, **When** a tela recarrega, **Then** o "Saldo do Período" da máquina X é exibido como R$ 0,00 e a data do "Último Reset" é atualizada.

---

### User Story 3 - Consultar Histórico de Resets Parciais (Priority: P2)

Um administrador (ou cliente, restrito às suas máquinas) consulta o histórico completo de resets parciais via API, filtrável por máquina, cliente ou período. Cada entrada exibe: data/hora, máquina, valor da coleta e usuário que executou.

**Why this priority**: Exigido pelo DoD como registro histórico para fins de auditoria.

**Independent Test**: Pode ser testado acessando a listagem de histórico após realizar ao menos 1 reset; a API deve retornar o registro com todos os campos preenchidos.

**Acceptance Scenarios**:

1. **Given** que existem 3 registros de reset para máquinas diferentes, **When** o administrador consulta `GET /reset-parcial/historico`, **Then** todos os 3 registros são listados com data, máquina, valor e usuário.
2. **Given** que o administrador filtra por `id_maquina = X`, **When** o filtro é aplicado, **Then** apenas os resets da máquina X são retornados.

---

### Edge Cases

- O que acontece se a API falhar ao retornar o total acumulado no momento do reset? → O reset não deve ser registrado e uma mensagem de erro deve ser exibida na UI.
- O que acontece se dois operadores tentam resetar a mesma máquina simultaneamente? → A API deve garantir consistência (lock ou validação por timestamp); apenas um snapshot válido por operação.
- O que acontece se o total acumulado for menor que `maquina_ultima_coleta` (caso de estorno)? → O Saldo do Período pode ser negativo; a UI deve exibir em vermelho.
- Máquinas sem nenhum reset: a API retorna `tem_reset = false`, `ultima_coleta = null` e `saldo_periodo = total_maquina`.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE exibir na tela de Acumulado três colunas por máquina: "Total Acumulado", "Última Coleta" e "Saldo do Período".
- **FR-002**: A API DEVE calcular e retornar o Saldo do Período como `total_maquina - maquina_ultima_coleta` (ou `total_maquina` quando não há reset).
- **FR-003**: O operador DEVE poder acionar um botão "Reset" por máquina que abre um modal de confirmação (double-check) na aplicação Laravel.
- **FR-004**: A API DEVE, ao confirmar o reset, persistir um snapshot com: `id_maquina`, `valor_ultima_coleta`, `valor_acumulado_total`, `realizado_por`, `created_at`, e atualizar `maquina_ultima_coleta` na tabela de máquinas.
- **FR-005**: A API DEVE preservar intacto o contador principal (`total_maquina` / extrato acumulado); o reset parcial NÃO zera nem altera transações ou totais de extrato.
- **FR-006**: A API DEVE manter histórico completo e imutável de todos os resets para auditoria.
- **FR-007**: O sistema DEVE exibir a data/hora do último reset em cada linha da tabela de Acumulado (campo `data_ultimo_reset` da API).
- **FR-008**: Deve existir teste unitário (na API) verificando que o reset parcial não altera o contador principal de acumulado.

### Key Entities *(include if feature involves data)*

- **Maquina** (API): entidade existente, ganha coluna `maquina_ultima_coleta` (DECIMAL 10,2, nullable) — valor do snapshot do último reset.
- **MaquinaResetParcial** (API): evento de reset parcial. Atributos: `id`, `id_maquina`, `valor_ultima_coleta`, `valor_acumulado_total`, `realizado_por`, `observacao` (opcional), `created_at`.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: O operador consegue visualizar o Saldo do Período de qualquer máquina em menos de 3 segundos após abrir a tela de Acumulado.
- **SC-002**: O botão de Reset requer exatamente 2 ações do operador (clique + confirmação no modal) antes de chamar a API.
- **SC-003**: 100% dos resets realizados são persistidos na API com todos os campos obrigatórios preenchidos.
- **SC-004**: O total acumulado (`total_maquina`) permanece inalterado após qualquer operação de reset parcial (verificável via teste unitário na API).
- **SC-005**: Histórico de resets exibe corretamente os dados de auditoria (quem, quando, qual valor).

## Assumptions

- A persistência, o cálculo do saldo e o histórico de resets são responsabilidade da **API externa** (`APP_URL_API` / `services.swiftpaysolucoes.com`), não do banco local do Laravel.
- A aplicação Laravel consome a API via `ApiClient` / `ExtratoMaquinaService`, seguindo o padrão já existente no projeto.
- O endpoint existente `GET /extrato/acumulado` será **estendido** para incluir os campos de reset parcial (retrocompatível: campos novos adicionados ao payload).
- O usuário logado possui identificador repassado à API como `realizado_por` no momento do reset.
- A tela de Acumulado existente (`/maquinas/acumulado` e `/clientes-maquinas/acumulado`) é estendida — não criada do zero.
- A funcionalidade está disponível para Administradores e Clientes, sujeita ao middleware `ChecarPermissoes` na aplicação Laravel e às regras de autorização da API.

---

## Contrato da API Externa (Backend)

> **Escopo**: Contrato que o backend da API (`services.swiftpaysolucoes.com`) **DEVE** implementar. A aplicação Laravel consome estes endpoints; não persiste dados de reset localmente.

**Base URL**: `{APP_URL_API}` (ex.: `https://services.swiftpaysolucoes.com/api`)

**Autenticação**: Bearer Token (mesmo padrão dos endpoints existentes, ex.: `/extrato/acumulado`)

**Content-Type**: `application/json`

---

### Alteração de banco de dados (API)

| Alteração | Tabela | Coluna | Tipo | Descrição |
|---|---|---|---|---|
| Nova coluna | `maquinas` | `maquina_ultima_coleta` | DECIMAL(10,2) NULL | Valor do snapshot no último reset parcial. `NULL` = nunca resetada. |
| Nova tabela | `maquina_resets_parciais` | — | — | Histórico imutável de resets (ver entidade abaixo). |

**Tabela `maquina_resets_parciais`**:

| Coluna | Tipo | Obrigatório | Descrição |
|---|---|---|---|
| `id` | BIGINT PK | Sim | Identificador do registro |
| `id_maquina` | VARCHAR/INT | Sim | FK lógica para `maquinas` |
| `valor_ultima_coleta` | DECIMAL(10,2) | Sim | Valor gravado como referência (= `total_maquina` no momento do reset) |
| `valor_acumulado_total` | DECIMAL(10,2) | Sim | Total bruto no momento do reset (auditoria) |
| `realizado_por` | INT/VARCHAR | Sim | ID ou identificador do usuário que executou |
| `observacao` | TEXT | Não | Comentário opcional |
| `created_at` | TIMESTAMP | Sim | Data/hora do reset |

**Índices recomendados**: `(id_maquina)`, `(id_maquina, created_at DESC)`.

**Regra de cálculo (obrigatória na API)**:

```
saldo_periodo = total_maquina - COALESCE(maquina_ultima_coleta, 0)
```

Quando `maquina_ultima_coleta IS NULL` (sem reset): `saldo_periodo = total_maquina` e `tem_reset = false`.

**Invariante**: `POST /maquinas/{id}/reset-parcial` **NÃO** altera `total_maquina`, transações de extrato ou qualquer contador de acumulado. Apenas atualiza `maquina_ultima_coleta` e insere registro em `maquina_resets_parciais`.

---

### Endpoint 1 — Estender acumulado com dados de reset

**`GET /extrato/acumulado`** *(endpoint existente — estendido)*

Estende a resposta paginada existente com campos de reset parcial por máquina.

**Query params** (existentes + sem alteração):

| Param | Tipo | Descrição |
|---|---|---|
| `page` | int | Página |
| `per_page` | int | Registros por página |
| `search_value` | string | Busca livre |
| `id_cliente` | int/array | Filtro por cliente (opcional) |

**Campos adicionados em cada item de `data[]`**:

| Campo | Tipo | Descrição |
|---|---|---|
| `ultima_coleta` | decimal \| null | Valor de `maquina_ultima_coleta`. `null` se nunca resetada. |
| `saldo_periodo` | decimal | `total_maquina - ultima_coleta` (ou `total_maquina` se sem reset) |
| `data_ultimo_reset` | ISO 8601 \| null | `created_at` do último registro em `maquina_resets_parciais` |
| `tem_reset` | boolean | `true` se já houve ao menos um reset parcial |

**Exemplo de item enriquecido**:

```json
{
  "id_maquina": "42",
  "maquina_nome": "Máquina Central",
  "local_nome": "Shopping Vitória",
  "total_maquina": 750.00,
  "total_pix": 400.00,
  "total_cartao": 250.00,
  "total_dinheiro": 100.00,
  "ultima_coleta": 500.00,
  "saldo_periodo": 250.00,
  "data_ultimo_reset": "2026-06-01T14:30:00.000000Z",
  "tem_reset": true
}
```

**Exemplo sem reset**:

```json
{
  "id_maquina": "43",
  "total_maquina": 300.00,
  "ultima_coleta": null,
  "saldo_periodo": 300.00,
  "data_ultimo_reset": null,
  "tem_reset": false
}
```

---

### Endpoint 2 — Executar reset parcial

**`POST /maquinas/{id_maquina}/reset-parcial`**

Registra um reset parcial para a máquina informada.

**Path params**:

| Param | Tipo | Descrição |
|---|---|---|
| `id_maquina` | string/int | ID da máquina |

**Request body**:

```json
{
  "realizado_por": "123",
  "observacao": "Coleta semanal — opcional"
}
```

| Campo | Tipo | Obrigatório | Descrição |
|---|---|---|---|
| `realizado_por` | string/int | Sim | ID do usuário que executa o reset |
| `observacao` | string | Não | Comentário livre |

**Comportamento interno (API)**:

1. Obtém `total_maquina` atual (contador principal — **somente leitura**).
2. Insere registro em `maquina_resets_parciais` com `valor_ultima_coleta = total_maquina` e `valor_acumulado_total = total_maquina`.
3. Atualiza `maquinas.maquina_ultima_coleta = total_maquina`.
4. Retorna o snapshot criado.

**Response `201 Created`**:

```json
{
  "message": "Reset parcial registrado com sucesso.",
  "data": {
    "id": 15,
    "id_maquina": "42",
    "valor_ultima_coleta": 750.00,
    "valor_acumulado_total": 750.00,
    "realizado_por": "123",
    "observacao": null,
    "created_at": "2026-06-08T22:30:00.000000Z",
    "saldo_periodo": 0.00
  }
}
```

**Erros**:

| HTTP | Condição | Body exemplo |
|---|---|---|
| `404` | Máquina não encontrada | `{ "message": "Máquina não encontrada." }` |
| `422` | Validação falhou | `{ "message": "...", "errors": { "realizado_por": ["..."] } }` |
| `503` | Falha ao obter total acumulado | `{ "message": "Não foi possível obter o total acumulado." }` |

---

### Endpoint 3 — Histórico de resets parciais

**`GET /reset-parcial/historico`**

Lista paginada do histórico de resets para auditoria.

**Query params**:

| Param | Tipo | Descrição |
|---|---|---|
| `page` | int | Página (default: 1) |
| `per_page` | int | Registros por página (default: 10) |
| `id_maquina` | string/int | Filtrar por máquina (opcional) |
| `id_cliente` | int | Filtrar por cliente — retorna resets das máquinas do cliente (opcional) |
| `data_inicio` | date (Y-m-d) | Filtrar a partir de (opcional) |
| `data_fim` | date (Y-m-d) | Filtrar até (opcional) |

**Response `200 OK`**:

```json
{
  "current_page": 1,
  "last_page": 3,
  "per_page": 10,
  "total": 25,
  "data": [
    {
      "id": 15,
      "id_maquina": "42",
      "maquina_nome": "Máquina Central",
      "local_nome": "Shopping Vitória",
      "valor_ultima_coleta": 750.00,
      "valor_acumulado_total": 750.00,
      "realizado_por": "123",
      "realizado_por_nome": "João Silva",
      "observacao": null,
      "created_at": "2026-06-08T22:30:00.000000Z"
    }
  ]
}
```

---

### Endpoint 4 — Último reset de uma máquina *(opcional, auxiliar)*

**`GET /maquinas/{id_maquina}/reset-parcial/ultimo`**

Retorna apenas o último reset registrado para uma máquina. Útil para validações pontuais.

**Response `200 OK`**:

```json
{
  "data": {
    "id": 15,
    "id_maquina": "42",
    "valor_ultima_coleta": 750.00,
    "valor_acumulado_total": 750.00,
    "realizado_por": "123",
    "created_at": "2026-06-08T22:30:00.000000Z"
  }
}
```

**Response `404`**: quando não há reset registrado.

---

### Integração na aplicação Laravel (consumidor)

A aplicação Laravel **não** persiste resets localmente. Deve:

| Ação | Service / método sugerido | Endpoint API |
|---|---|---|
| Listar acumulado com reset | `ExtratoMaquinaService::coletarAcumulado()` | `GET /extrato/acumulado` |
| Executar reset | `ExtratoMaquinaService::resetParcial($id, $dados)` | `POST /maquinas/{id}/reset-parcial` |
| Consultar histórico | `ExtratoMaquinaService::historicoResets($filtros)` | `GET /reset-parcial/historico` |

Rotas web Laravel (Admin/Cliente) recebem o submit do modal e delegam à API via `ApiClient`.

---

### Definition of Done — Mapeamento API

| Item DoD | Entregável na API |
|---|---|
| Nova coluna "Última Coleta" | `maquinas.maquina_ultima_coleta` |
| Lógica `Total Acumulado - Última Coleta` | Cálculo de `saldo_periodo` em `GET /extrato/acumulado` |
| Botão Reset com double-check | UI Laravel; API expõe `POST /maquinas/{id}/reset-parcial` |
| Registro histórico para auditoria | Tabela `maquina_resets_parciais` + `GET /reset-parcial/historico` |
| Teste unitário — contador intacto | Teste na API: após reset, `total_maquina` permanece igual |
