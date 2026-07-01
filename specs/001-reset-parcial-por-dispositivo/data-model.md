# Data Model: Reset Parcial por Dispositivo

**Feature**: `001-reset-parcial-por-dispositivo`
**Date**: 2026-06-08

---

## Entidade: MaquinaResetParcial

Representa um evento de "reset parcial" realizado em uma máquina específica. Cada registro é um snapshot imutável do valor acumulado no momento da aferição.

### Tabela: `maquina_resets_parciais`

| Coluna | Tipo | Nullable | Default | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NOT NULL | AUTO_INCREMENT | PK |
| `id_maquina` | VARCHAR(255) | NOT NULL | — | ID da máquina na API externa (string ou inteiro) |
| `valor_ultima_coleta` | DECIMAL(10,2) | NOT NULL | — | Valor acumulado total da máquina no momento do reset (= snapshot da API) |
| `valor_acumulado_total` | DECIMAL(10,2) | NOT NULL | — | Redundante para auditoria: total bruto da API no momento do reset |
| `realizado_por` | BIGINT UNSIGNED | NOT NULL | — | FK → `users.id` (usuário local que executou o reset) |
| `observacao` | TEXT | NULL | NULL | Campo livre opcional para comentário do operador |
| `created_at` | TIMESTAMP | NOT NULL | CURRENT_TIMESTAMP | Data/hora do reset |
| `updated_at` | TIMESTAMP | NOT NULL | CURRENT_TIMESTAMP | Controle Laravel |

### Índices

| Índice | Colunas | Tipo |
|---|---|---|
| `PRIMARY` | `id` | PRIMARY |
| `idx_id_maquina` | `id_maquina` | INDEX |
| `idx_id_maquina_created` | `id_maquina`, `created_at` | INDEX |
| `idx_realizado_por` | `realizado_por` | INDEX |

### Relacionamentos

- `realizado_por` → `users.id` (tabela local de usuários do Laravel)
- `id_maquina` → identificador externo (API) — sem FK pois a entidade vive na API

---

## Migration Laravel

```php
// database/migrations/YYYY_MM_DD_HHMMSS_create_maquina_resets_parciais_table.php

Schema::create('maquina_resets_parciais', function (Blueprint $table) {
    $table->id();
    $table->string('id_maquina')->index();
    $table->decimal('valor_ultima_coleta', 10, 2);
    $table->decimal('valor_acumulado_total', 10, 2);
    $table->unsignedBigInteger('realizado_por');
    $table->text('observacao')->nullable();
    $table->timestamps();

    $table->foreign('realizado_por')->references('id')->on('users')->onDelete('restrict');
    $table->index(['id_maquina', 'created_at'], 'idx_id_maquina_created');
});
```

---

## Model: `App\Models\MaquinaResetParcial`

```php
class MaquinaResetParcial extends Model
{
    protected $table = 'maquina_resets_parciais';

    protected $fillable = [
        'id_maquina',
        'valor_ultima_coleta',
        'valor_acumulado_total',
        'realizado_por',
        'observacao',
    ];

    protected $casts = [
        'valor_ultima_coleta'  => 'decimal:2',
        'valor_acumulado_total' => 'decimal:2',
    ];

    // Escopo: último reset de uma máquina
    public function scopeUltimoPorMaquina($query, string $idMaquina)
    {
        return $query->where('id_maquina', $idMaquina)
                     ->latest('created_at')
                     ->first();
    }

    public function realizadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'realizado_por');
    }
}
```

---

## Objeto de Domínio: AcumuladoComReset (DTO)

Estrutura de dados retornada pelo proxy endpoint ao DataTables.

```json
{
  "id_maquina": "MAC-001",
  "maquina_nome": "Máquina Central",
  "local_nome": "Shopping Vitória",
  "total_maquina": 750.00,
  "total_pix": 400.00,
  "total_cartao": 250.00,
  "total_dinheiro": 100.00,
  "ultima_coleta": 500.00,
  "saldo_periodo": 250.00,
  "data_ultimo_reset": "2026-06-01T14:30:00Z",
  "tem_reset": true
}
```

Quando não há nenhum reset registrado:
```json
{
  "ultima_coleta": null,
  "saldo_periodo": 750.00,
  "data_ultimo_reset": null,
  "tem_reset": false
}
```

---

## Estado / Transições

```
[Sem Reset]
    │
    │  Operador clica "Reset" e confirma
    ▼
[Reset Parcial Registrado]
    │  saldo_periodo = total_acumulado - valor_ultima_coleta
    │
    │  Operador clica "Reset" novamente e confirma
    ▼
[Novo Reset Parcial Registrado]
    │  saldo_periodo reinicia a zero
    │  histórico mantém todos os eventos anteriores
```

**Invariante**: O `total_acumulado` na API nunca é modificado por esta feature. Apenas o snapshot local muda.
