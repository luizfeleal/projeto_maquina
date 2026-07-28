---
description: "Task list for Reset Parcial por Dispositivo"
---

# Tasks: Reset Parcial por Dispositivo

**Input**: Design documents from `/specs/001-reset-parcial-por-dispositivo/`

**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/

**Tests**: FR-008 exige teste unitário na API (repo externo). Neste repo Laravel: teste de delegação do service incluído na fase Polish.

**Organization**: Tasks grouped by user story. Persistência e cálculo na API externa; Laravel consome via `ExtratoMaquinaService`.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: User story label (US1, US2, US3)

## Path Conventions

- Laravel app root: `app/`, `resources/views/`, `routes/`, `tests/`
- Contrato API: `specs/001-reset-parcial-por-dispositivo/contracts/api-endpoints.md`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Validar contexto e alinhar dependências antes da implementação

- [X] T001 Confirmar branch `001-reset-parcial-por-dispositivo` e revisar contratos em `specs/001-reset-parcial-por-dispositivo/contracts/api-endpoints.md` e `specs/001-reset-parcial-por-dispositivo/contracts/web-routes.md`
- [X] T002 [P] Validar variável `APP_URL_API` documentada em `.env.example` para consumo dos novos endpoints
- [X] T003 [P] Mapear endpoints DataTables existentes: Admin usa `GET /extrato/acumulado` em `resources/views/Admin/Maquinas/Acumulado/index.blade.php`; Cliente usa `POST /totalTransacaoMaquinaAcumuladoCliente` em `resources/views/Clientes/Maquinas/Acumulado/index.blade.php` — ambos precisam retornar campos de reset da API

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Camada de serviço e mocks — BLOQUEIA todas as user stories até concluir

**⚠️ CRITICAL**: API externa deve implementar contrato em `specs/001-reset-parcial-por-dispositivo/spec.md` (seção Contrato da API). Para dev local, usar mocks abaixo.

- [X] T004 Implementar `coletarAcumulado($filtros)` em `app/Services/ExtratoMaquinaService.php` chamando `GET /extrato/acumulado` via `ApiClient`
- [X] T005 Implementar `resetParcial($idMaquina, $dados)` em `app/Services/ExtratoMaquinaService.php` chamando `POST /maquinas/{id_maquina}/reset-parcial` via `ApiClient`
- [X] T006 Implementar `historicoResets($filtros)` em `app/Services/ExtratoMaquinaService.php` chamando `GET /reset-parcial/historico` via `ApiClient`
- [X] T007 [P] Estender `app/Mocks/MockData.php` com dados mock de acumulado enriquecido (`ultima_coleta`, `saldo_periodo`, `data_ultimo_reset`, `tem_reset`) e histórico de resets
- [X] T008 [P] Estender `app/Mocks/MockRouter.php` para rotear `GET /extrato/acumulado`, `POST /maquinas/{id}/reset-parcial`, `GET /reset-parcial/historico` e enriquecer resposta de `POST /totalTransacaoMaquinaAcumuladoCliente` com campos de reset
- [X] T009 Registrar rotas web Admin e Cliente (stubs) em `routes/web.php`: `maquinas-reset-parcial`, `maquinas-resets-historico`, `clientes-maquinas-reset-parcial`, `clientes-maquinas-resets-historico`

**Checkpoint**: Service layer pronta; mocks permitem desenvolvimento UI sem API real

---

## Phase 3: User Story 1 — Visualizar Extrato com Saldo do Período (Priority: P1) 🎯 MVP

**Goal**: Tela de Acumulado exibe Total Acumulado, Última Coleta, Saldo do Período e data do último reset por máquina

**Independent Test**: Acessar `/maquinas/acumulado` (Admin) ou `/clientes-maquinas/acumulado` (Cliente) com mock/API retornando campos de reset; tabela deve exibir as três colunas monetárias + timestamp

### Implementation for User Story 1

- [X] T010 [US1] Adicionar colunas `ultima_coleta`, `saldo_periodo` e `data_ultimo_reset` no DataTables de `resources/views/Admin/Maquinas/Acumulado/index.blade.php` (thead, tfoot, columns config)
- [X] T011 [P] [US1] Adicionar mesmas colunas no DataTables de `resources/views/Clientes/Maquinas/Acumulado/index.blade.php`
- [X] T012 [US1] Substituir URL hardcoded da API por `APP_URL_API` + path relativo em `resources/views/Admin/Maquinas/Acumulado/index.blade.php`
- [X] T013 [P] [US1] Substituir URL hardcoded da API por `APP_URL_API` + path relativo em `resources/views/Clientes/Maquinas/Acumulado/index.blade.php`
- [X] T014 [US1] Implementar renderização de moeda BRL, exibir "Sem reset" quando `tem_reset === false`, e formatar `data_ultimo_reset` em `resources/views/Admin/Maquinas/Acumulado/index.blade.php`
- [X] T015 [P] [US1] Implementar mesma renderização e exibir `saldo_periodo` negativo em vermelho em `resources/views/Clientes/Maquinas/Acumulado/index.blade.php`

**Checkpoint**: US1 completa — visualização funcional sem botão de reset

---

## Phase 4: User Story 2 — Realizar Reset Parcial com Double-Check (Priority: P1)

**Goal**: Botão Reset com modal de confirmação; submit delega à API sem alterar contador principal

**Independent Test**: Clicar Reset → cancelar (sem chamada API) → confirmar (POST via Laravel → API mock/real) → saldo_periodo volta a 0 na próxima carga

### Implementation for User Story 2

- [X] T016 [US2] Implementar action `resetParcial(Request $request)` em `app/Http/Controllers/MaquinasController.php` validando `id_maquina`, obtendo `realizado_por` da sessão e chamando `ExtratoMaquinaService::resetParcial()`
- [X] T017 [P] [US2] Implementar action `resetParcial(Request $request)` em `app/Http/Controllers/Clientes/MaquinasController.php` com mesmo fluxo e escopo de cliente
- [X] T018 [US2] Conectar rotas POST `maquinas-reset-parcial` e `clientes-maquinas-reset-parcial` em `routes/web.php` aos controllers
- [X] T019 [US2] Adicionar modal Bootstrap double-check e botão "Reset" por linha (coluna Ações) em `resources/views/Admin/Maquinas/Acumulado/index.blade.php` com form POST para rota `maquinas-reset-parcial`
- [X] T020 [P] [US2] Adicionar modal double-check e botão Reset em `resources/views/Clientes/Maquinas/Acumulado/index.blade.php` com form POST para rota `clientes-maquinas-reset-parcial`
- [X] T021 [US2] Tratar respostas success/error da API com flash messages (`back()->with('success'|'error')`) nos controllers `MaquinasController` e `Clientes/MaquinasController`

**Checkpoint**: US1 + US2 completas — visualização e reset funcional end-to-end

---

## Phase 5: User Story 3 — Consultar Histórico de Resets Parciais (Priority: P2)

**Goal**: Tela de auditoria listando resets com filtros por máquina e período

**Independent Test**: Após 1 reset, acessar `/maquinas/resets-historico` e ver registro com data, máquina, valor e usuário

### Implementation for User Story 3

- [X] T022 [US3] Implementar action `historicoResets(Request $request)` em `app/Http/Controllers/MaquinasController.php` repassando filtros à `ExtratoMaquinaService::historicoResets()`
- [X] T023 [P] [US3] Implementar action `historicoResets(Request $request)` em `app/Http/Controllers/Clientes/MaquinasController.php` incluindo `id_cliente` do usuário logado no filtro
- [X] T024 [US3] Criar view `resources/views/Admin/Maquinas/Acumulado/historico.blade.php` com formulário de filtros (`id_maquina`, `data_inicio`, `data_fim`) e tabela paginada
- [X] T025 [P] [US3] Criar view `resources/views/Clientes/Maquinas/Acumulado/historico.blade.php` espelhando Admin com escopo de cliente
- [X] T026 [US3] Conectar rotas GET `maquinas-resets-historico` e `clientes-maquinas-resets-historico` em `routes/web.php`
- [X] T027 [P] [US3] Adicionar links "Histórico de Resets" no menu em `app/Helpers/MenuHelper.php` para rotas Admin e Cliente

**Checkpoint**: Todas as user stories independentemente testáveis

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Testes, permissões e validação final

- [X] T028 [P] Criar `tests/Unit/ExtratoMaquinaResetParcialTest.php` mockando `ApiClient` para verificar que `resetParcial()` chama apenas `POST /maquinas/{id}/reset-parcial` e não persiste dados localmente
- [X] T029 [P] Registrar rotas de permissão `maquinas-reset-parcial`, `maquinas-resets-historico`, `clientes-maquinas-reset-parcial`, `clientes-maquinas-resets-historico` em `app/Mocks/MockData.php` (rotas permitidas em dev mock)
- [X] T030 Validar manualmente critérios de aceite das US1–US3 conforme `specs/001-reset-parcial-por-dispositivo/spec.md` (colunas acumulado, double-check, histórico auditoria, saldo negativo em vermelho)

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências — iniciar imediatamente
- **Foundational (Phase 2)**: Depende de Phase 1 — **BLOQUEIA** US1, US2, US3
- **US1 (Phase 3)**: Depende de Phase 2 (T004, T007–T008 para mocks)
- **US2 (Phase 4)**: Depende de Phase 2 (T005) + US1 (tela Acumulado existente com colunas)
- **US3 (Phase 5)**: Depende de Phase 2 (T006); independente de US2 para listagem, mas precisa de resets existentes para teste completo
- **Polish (Phase 6)**: Depende das stories desejadas concluídas

### User Story Dependencies

| Story | Depende de | Independente quando |
|---|---|---|
| US1 (P1) | Foundational | Mock/API retorna campos estendidos |
| US2 (P1) | Foundational + US1 (UI base) | Modal + POST funcionam isolados |
| US3 (P2) | Foundational | Lista histórico mesmo sem US2 (dados vazios) |

### Parallel Opportunities

```bash
# Phase 2 — mocks em paralelo:
T007 MockData.php
T008 MockRouter.php

# Phase 3 — Admin vs Cliente views:
T011 Cliente acumulado columns
T013 Cliente API URL
T015 Cliente renderização

# Phase 4 — controllers Admin vs Cliente:
T017 Clientes/MaquinasController resetParcial
T020 Cliente modal reset

# Phase 5 — histórico Admin vs Cliente:
T023 Clientes historicoResets
T025 Cliente historico view
T027 MenuHelper links
```

---

## Parallel Example: User Story 1

```bash
# Após T010 (Admin columns), lançar em paralelo:
Task T011: Colunas Cliente em resources/views/Clientes/Maquinas/Acumulado/index.blade.php
Task T013: URL env Cliente em resources/views/Clientes/Maquinas/Acumulado/index.blade.php
Task T015: Renderização Cliente em resources/views/Clientes/Maquinas/Acumulado/index.blade.php
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Phase 1: Setup
2. Phase 2: Foundational (T004, T007–T008 mínimo para mock)
3. Phase 3: User Story 1
4. **STOP and VALIDATE**: Acumulado exibe Total, Última Coleta, Saldo do Período

### Incremental Delivery

1. Setup + Foundational → base pronta
2. US1 → visualização (MVP)
3. US2 → reset com double-check
4. US3 → histórico auditoria
5. Polish → testes e permissões

### API Externa (repo separado — pré-requisito integração real)

Antes de deploy em produção, a API deve entregar (ver `specs/001-reset-parcial-por-dispositivo/spec.md`):

| Entregável API | Endpoint |
|---|---|
| Coluna `maquinas.maquina_ultima_coleta` + tabela `maquina_resets_parciais` | Migration API |
| Acumulado com reset | `GET /extrato/acumulado` estendido |
| Acumulado cliente com reset | `POST /totalTransacaoMaquinaAcumuladoCliente` estendido |
| Executar reset | `POST /maquinas/{id}/reset-parcial` |
| Histórico | `GET /reset-parcial/historico` |
| Teste contador intacto | Teste unitário na API (FR-008) |

---

## Notes

- Laravel **não** cria migration local — persistência é 100% na API
- Cliente usa endpoint diferente do Admin; ambos precisam dos campos de reset na API
- `[P]` = arquivos diferentes, sem dependência de task incompleta no mesmo arquivo
- Commit após cada fase ou grupo lógico de tasks
