# Implementation Plan: Reset Parcial por Dispositivo

**Branch**: `001-reset-parcial-por-dispositivo` | **Date**: 2026-06-08 | **Spec**: [spec.md](./spec.md)

**Input**: Feature specification from `/specs/001-reset-parcial-por-dispositivo/spec.md`

## Summary

Implementar uma funcionalidade de "reset parcial" por máquina na tela de Acumulado. O operador poderá registrar um snapshot do valor acumulado atual (obtido da API externa) sem modificar o contador principal da API. A tela de Acumulado passará a exibir: Total Acumulado (API), Última Coleta (snapshot local) e Saldo do Período (diferença). Um botão de Reset com double-check modal registra o snapshot localmente. Histórico de resets disponível para auditoria.

## Technical Context

**Language/Version**: PHP 8.0 (compatível com 7.3+), Laravel 8.75

**Primary Dependencies**: Laravel Framework 8.75, Guzzle 7.x, Bootstrap 5.3, jQuery, DataTables 1.13, Font Awesome

**Storage**: MySQL (`projeto_maquina`) — nova tabela `maquina_resets_parciais` via migration Laravel. API externa (`services.swiftpaysolucoes.com`) para dados de acumulado (somente leitura nesta feature).

**Testing**: PHPUnit 9.6 (já configurado em `phpunit.xml`)

**Target Platform**: Servidor Linux + Nginx (Docker — `docker-compose.yml` existente)

**Project Type**: Web application — Laravel MVC + Blade + jQuery/Bootstrap frontend consumindo API externa

**Performance Goals**: Tela de Acumulado carrega em < 3 segundos; resposta do endpoint proxy < 500ms além do tempo da API externa

**Constraints**: Não escrever na API externa durante o reset; manter compatibilidade com o DataTables `serverSide: true` existente; seguir o middleware `ChecarPermissoes` para permissões de tela

**Scale/Scope**: Dezenas a centenas de máquinas por cliente; histórico de resets com potencial de milhares de registros ao longo do tempo

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

> **Nota**: A `constitution.md` deste projeto ainda está no template padrão (não customizada). Aplicando princípios genéricos de qualidade de software:

| Gate | Status | Justificativa |
|---|---|---|
| Não modifica API externa | ✅ PASS | Reset apenas grava localmente; API é somente leitura nesta feature |
| Teste unitário obrigatório (DoD) | ✅ PASS | `ResetParcialCalculoTest` planejado |
| Double-check para ações destrutivas | ✅ PASS | Modal de confirmação com 2 etapas |
| Registro histórico para auditoria | ✅ PASS | Tabela imutável de snapshots |
| Consistência com padrões do projeto | ✅ PASS | Seguindo MVC, Services, Blade, Bootstrap, jQuery, ChecarPermissoes |

*Post-design re-check*: ✅ Todos os gates mantidos. A rota proxy `/api/maquinas/acumulado-com-reset` não modifica dados externos.

## Project Structure

### Documentation (this feature)

```text
specs/001-reset-parcial-por-dispositivo/
├── plan.md              # Este arquivo
├── spec.md              # Especificação da feature
├── research.md          # Fase 0 — decisões e justificativas
├── data-model.md        # Fase 1 — modelo de dados e migration
├── contracts/
│   └── web-routes.md    # Contratos de rotas e API proxy
└── tasks.md             # Fase 2 — gerado por /speckit-tasks
```

### Source Code (repository root)

```text
app/
├── Http/
│   └── Controllers/
│       ├── MaquinasController.php          # + resetParcial(), historicoResets()
│       ├── Clientes/
│       │   └── MaquinasController.php      # + resetParcial(), historicoResets()
│       └── Api/
│           └── AcumuladoController.php     # NOVO — proxy endpoint para DataTables
├── Models/
│   └── MaquinaResetParcial.php             # NOVO
└── Services/
    └── MaquinaResetParcialService.php      # NOVO

database/
└── migrations/
    └── YYYY_MM_DD_HHMMSS_create_maquina_resets_parciais_table.php   # NOVA

resources/views/
└── Admin/
    └── Maquinas/
        └── Acumulado/
            ├── index.blade.php             # MODIFICADO — novas colunas + botão Reset + modal
            └── historico.blade.php         # NOVO — listagem histórico de resets
    └── Clientes/
        └── Maquinas/
            └── Acumulado/
                ├── index.blade.php         # MODIFICADO (idem Admin)
                └── historico.blade.php     # NOVO

routes/
├── web.php                                 # + 4 novas rotas (Admin + Cliente)
└── api.php                                 # + rota proxy /api/maquinas/acumulado-com-reset

tests/
└── Unit/
    └── ResetParcialCalculoTest.php         # NOVO — teste unitário do cálculo
```

**Structure Decision**: Web application com frontend Blade + jQuery. Nova camada de Model+Service+Controller seguindo o padrão MVC existente. Endpoint API proxy para o DataTables serverSide.

## Complexity Tracking

> Nenhuma violação identificada. O design segue os padrões existentes.

---

## Artifacts Gerados

| Artifact | Status | Local |
|---|---|---|
| `spec.md` | ✅ Completo | `specs/001-reset-parcial-por-dispositivo/spec.md` |
| `research.md` | ✅ Completo | `specs/001-reset-parcial-por-dispositivo/research.md` |
| `data-model.md` | ✅ Completo | `specs/001-reset-parcial-por-dispositivo/data-model.md` |
| `contracts/web-routes.md` | ✅ Completo | `specs/001-reset-parcial-por-dispositivo/contracts/web-routes.md` |
| `tasks.md` | ⏳ Pendente | Gerado por `/speckit-tasks` |

---

## Definition of Done (DoD) — Mapeamento

| Item do DoD | Entregável Planejado |
|---|---|
| Nova coluna no banco para "Última Coleta" | Migration `maquina_resets_parciais` com `valor_ultima_coleta` |
| Lógica de cálculo (Total Acumulado - Última Coleta) validada | `MaquinaResetParcialService` + `AcumuladoController` (proxy) |
| Botão de Reset com Double Check | Modal Bootstrap na `Acumulado/index.blade.php` |
| Registro histórico para auditoria | Tabela `maquina_resets_parciais` (imutável) + tela `historico.blade.php` |
| Teste unitário — contador principal intacto | `tests/Unit/ResetParcialCalculoTest.php` |
