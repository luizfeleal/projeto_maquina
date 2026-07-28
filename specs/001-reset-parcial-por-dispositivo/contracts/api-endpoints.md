# Contrato da API Externa: Reset Parcial por Dispositivo

**Feature**: `001-reset-parcial-por-dispositivo`
**Date**: 2026-06-08
**Fonte canônica**: [spec.md](../spec.md) — seção "Contrato da API Externa (Backend)"

> Este documento resume o contrato que o backend da API **DEVE** implementar. Detalhes completos, exemplos e regras de negócio estão na spec.

**Base URL**: `{APP_URL_API}`  
**Autenticação**: Bearer Token  
**Content-Type**: `application/json`

---

## Alterações de banco (API)

| Alteração | Detalhe |
|---|---|
| Coluna nova | `maquinas.maquina_ultima_coleta` DECIMAL(10,2) NULL |
| Tabela nova | `maquina_resets_parciais` (histórico imutável) |

**Cálculo obrigatório**: `saldo_periodo = total_maquina - COALESCE(maquina_ultima_coleta, 0)`

**Invariante**: Reset parcial **não** altera `total_maquina` nem transações de extrato.

---

## Endpoints

### 1. `GET /extrato/acumulado` *(estendido)*

Estende payload existente com:

| Campo | Tipo |
|---|---|
| `ultima_coleta` | decimal \| null |
| `saldo_periodo` | decimal |
| `data_ultimo_reset` | ISO 8601 \| null |
| `tem_reset` | boolean |

---

### 2. `POST /maquinas/{id_maquina}/reset-parcial`

**Body**:
```json
{ "realizado_por": "123", "observacao": "opcional" }
```

**Ações**: snapshot em `maquina_resets_parciais` + update `maquina_ultima_coleta`.

**Response**: `201` com registro criado e `saldo_periodo: 0.00`.

---

### 3. `GET /reset-parcial/historico`

**Query**: `page`, `per_page`, `id_maquina`, `id_cliente`, `data_inicio`, `data_fim`

**Response**: `200` paginado com histórico completo para auditoria.

---

### 4. `GET /maquinas/{id_maquina}/reset-parcial/ultimo` *(opcional)*

Retorna o último reset da máquina. `404` se inexistente.

---

## Consumo Laravel

| Método Service | Endpoint |
|---|---|
| `ExtratoMaquinaService::coletarAcumulado()` | `GET /extrato/acumulado` |
| `ExtratoMaquinaService::resetParcial()` | `POST /maquinas/{id}/reset-parcial` |
| `ExtratoMaquinaService::historicoResets()` | `GET /reset-parcial/historico` |
