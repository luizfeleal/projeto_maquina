# Research: Reset Parcial por Dispositivo

**Feature**: `001-reset-parcial-por-dispositivo`
**Date**: 2026-06-08
**Status**: Complete — all NEEDS CLARIFICATION resolved

---

## 1. Onde armazenar o estado do "Reset Parcial"?

**Decisão**: Banco de dados local do Laravel (MySQL — `projeto_maquina`).

**Rationale**: O total acumulado é gerenciado pela API externa e não pode ser modificado (DoD exige que o contador principal permaneça intacto). O reset parcial é um conceito de negócio *local* — é um "snapshot" do valor em um determinado momento. Portanto, deve ser persistido localmente em uma nova tabela `maquina_resets_parciais`.

**Alternativas consideradas**:
- Armazenar na API externa via novo endpoint: rejeitado porque modifica o escopo da API e viola o requisito de "contador intacto".
- Usar cache/sessão do Laravel: rejeitado porque não garante persistência histórica para auditoria.

---

## 2. Como calcular o Saldo do Período?

**Decisão**: `Saldo do Período = total_acumulado_api - valor_ultima_coleta_local`

**Rationale**: O cálculo é sempre feito em tempo real no backend (Controller ou Service) ao combinar o dado da API com o último registro da tabela local. Não é precalculado nem cacheado.

**Fluxo**:
1. Controller chama `ExtratoMaquinaService::coletarAcumulado()` → obtém `total_maquina` por máquina da API.
2. Controller consulta `MaquinaResetParcial::ultimoPorMaquina($id_maquina)` → obtém o último snapshot local.
3. Saldo = total_api - ultimo_snapshot->valor_ultima_coleta (ou total_api se não há snapshot).

**Alternativas consideradas**:
- Calcular no frontend (JS): rejeitado porque expõe lógica de negócio no cliente e dificulta auditoria.

---

## 3. Padrão de Double-Check para ação destrutiva

**Decisão**: Modal Bootstrap com dois passos — clique no botão "Reset" (abre modal) + clique em "Confirmar Reset" dentro do modal.

**Rationale**: O projeto já usa Bootstrap 5 e jQuery. O padrão de modal de confirmação já existe em `resources/views/Admin/Maquinas/index.blade.php` (para exclusão de máquinas). Manter consistência visual e técnica.

**Implementação**: Botão "Reset" aciona `data-bs-toggle="modal"` com `data-maquina-id` e `data-total-atual`. O modal exibe o valor atual e pede confirmação. O submit do modal envia via `fetch()` ou `form submit` para a rota Laravel.

---

## 4. Integração com DataTables ServerSide (tela de Acumulado)

**Decisão**: Adaptar a rota existente de acumulado para incluir os campos adicionais (última coleta, saldo do período, data do último reset) na resposta JSON servida ao DataTables.

**Rationale**: A tela `Acumulado/index.blade.php` já usa DataTables com `serverSide: true` apontando para `services.swiftpaysolucoes.com/api/extrato/acumulado`. O enriquecimento dos dados de reset parcial deve ser feito via uma rota *proxy* no próprio Laravel, que combina o dado da API com o dado local antes de retornar ao DataTables.

**Decisão de rota proxy**: Criar endpoint `/api/maquinas/acumulado-com-reset` no Laravel que:
1. Busca o acumulado da API externa.
2. Cruza com os últimos resets parciais locais.
3. Retorna JSON enriquecido para o DataTables.

**Alternativas consideradas**:
- Modificar a API externa para incluir os dados de reset: fora do escopo e violaria a separação de responsabilidades.
- Fazer o cruzamento no JS do frontend: complexo e inviável com `serverSide: true`.

---

## 5. Histórico de Resets — estrutura e acesso

**Decisão**: Tabela `maquina_resets_parciais` com índice em `id_maquina` e `created_at`. Acesso via nova tela `/maquinas/resets-historico` (Admin) e `/clientes-maquinas/resets-historico` (Cliente).

**Rationale**: A auditoria requer consulta por máquina e por período. Os índices garantem performance mesmo com volume crescente de registros.

---

## 6. Controle de Permissões

**Decisão**: Seguir o middleware `ChecarPermissoes` já existente. Criar nova permissão de tela `maquinas-reset-parcial` para o botão de Reset e `maquinas-resets-historico` para a tela de histórico.

**Rationale**: O padrão existente de controle de acesso por tela/permissão já está implementado. Consistência com o modelo de segurança existente.

---

## 7. Teste unitário do cálculo

**Decisão**: Criar `tests/Unit/ResetParcialCalculoTest.php` usando PHPUnit (já configurado no projeto).

**Rationale**: O DoD exige teste unitário garantindo que o contador principal permanece intacto. O teste deve:
- Mockar o retorno da API com valor X.
- Criar um reset com valor Y.
- Verificar que Saldo = X - Y.
- Verificar que nenhum método de escrita na API é chamado.

---

## Dependências Identificadas

| Dependência | Status | Ação |
|---|---|---|
| Tabela `maquina_resets_parciais` | Nova | Criar migration |
| Rota proxy `/api/maquinas/acumulado-com-reset` | Nova | Criar no `routes/api.php` |
| `MaquinaResetParcialService` | Novo | Criar em `app/Services/` |
| Model `MaquinaResetParcial` | Novo | Criar em `app/Models/` |
| Controller de reset (action `resetParcial`) | Novo | Adicionar em `MaquinasController` |
| View `Acumulado/index.blade.php` | Modificar | Adicionar colunas e botão |
| Tela de histórico | Nova | `Acumulado/historico.blade.php` |
| Permissões | Novas | Via seed ou documentação para admin configurar |
