<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Mensalidade extends Model
{
    protected $table = 'mensalidades';

    protected $fillable = [
        'id_cliente',
        'descricao',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'status',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'data_pagamento'  => 'date',
        'valor'           => 'decimal:2',
    ];

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente')->whereNull('data_pagamento');
    }

    public function scopeVencendoEm($query, int $dias)
    {
        $data = Carbon::today()->addDays($dias)->toDateString();
        return $query->where('data_vencimento', $data);
    }

    public function scopeInadimplentes($query, int $diasTolerancia)
    {
        $limite = Carbon::today()->subDays($diasTolerancia)->toDateString();
        return $query->where('status', 'pendente')
                     ->whereNull('data_pagamento')
                     ->where('data_vencimento', '<=', $limite);
    }

    public function getDiasAtrasoAttribute(): int
    {
        if ($this->status === 'pago') {
            return 0;
        }
        return max(0, Carbon::today()->diffInDays($this->data_vencimento, false) * -1);
    }
}
