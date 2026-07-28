# Contratos de Rotas Web (Laravel — Consumidor da API)

**Feature**: `001-reset-parcial-por-dispositivo`
**Date**: 2026-06-08

> A persistência e o cálculo ficam na API externa. O Laravel expõe rotas web para UI e delega via `ApiClient` / `ExtratoMaquinaService`.  
> Contrato da API: [api-endpoints.md](./api-endpoints.md) | [spec.md](../spec.md)

---

## Rotas Admin

### POST `/maquinas/reset-parcial`

**Nome**: `maquinas-reset-parcial`  
**Controller**: `MaquinasController@resetParcial`  
**Middleware**: `web`, `auth`, `permission`

**Form request**:

| Campo | Tipo | Obrigatório |
|---|---|---|
| `id_maquina` | string | Sim |
| `observacao` | string | Não |

**Comportamento**: Valida double-check (modal já confirmado no frontend), obtém `realizado_por` da sessão, chama `POST /maquinas/{id_maquina}/reset-parcial` na API.

**Response**: Redirect `back()` com flash success/error.

---

### GET `/maquinas/resets-historico`

**Nome**: `maquinas-resets-historico`  
**Controller**: `MaquinasController@historicoResets`  
**Middleware**: `web`, `auth`, `permission`

**Query**: `id_maquina`, `data_inicio`, `data_fim` — repassados à API `GET /reset-parcial/historico`.

---

## Rotas Cliente

### POST `/clientes-maquinas/reset-parcial`

**Nome**: `clientes-maquinas-reset-parcial`  
**Controller**: `Clientes\MaquinasController@resetParcial`

Mesmo contrato Admin; API filtra autorização por cliente.

---

### GET `/clientes-maquinas/resets-historico`

**Nome**: `clientes-maquinas-resets-historico`  
**Controller**: `Clientes\MaquinasController@historicoResets`

Repassa `id_cliente` do usuário logado à API.

---

## DataTables (Acumulado)

A tela `Acumulado/index.blade.php` continua apontando para a API:

```
GET {APP_URL_API}/extrato/acumulado
```

Com os campos estendidos (`ultima_coleta`, `saldo_periodo`, `data_ultimo_reset`, `tem_reset`) retornados diretamente pela API — **sem proxy Laravel**.

---

## Permissões de Tela

| Permissão | Rota | Perfil |
|---|---|---|
| `maquinas-reset-parcial` | POST `/maquinas/reset-parcial` | Admin |
| `maquinas-resets-historico` | GET `/maquinas/resets-historico` | Admin |
| `clientes-maquinas-reset-parcial` | POST `/clientes-maquinas/reset-parcial` | Cliente |
| `clientes-maquinas-resets-historico` | GET `/clientes-maquinas/resets-historico` | Cliente |
