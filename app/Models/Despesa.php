<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Despesa extends Model
{
    protected $table = 'despesas';

    protected $fillable = [
        'id_cliente',
        'descricao',
        'valor',
        'data_despesa',
        'tipo',
        'comprovante_path',
    ];

    protected $casts = [
        'data_despesa' => 'date',
        'valor'        => 'decimal:2',
    ];

    public function getComprovanteUrlAttribute(): ?string
    {
        return $this->comprovante_path
            ? asset('storage/' . $this->comprovante_path)
            : null;
    }
}
