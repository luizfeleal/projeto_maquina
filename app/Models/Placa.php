<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Placa extends Model
{
    protected $table = 'placas';

    protected $fillable = [
        'id_maquina',
        'placa_serial',
    ];

    public function getSerialCurtoAttribute(): string
    {
        return substr($this->placa_serial, -4);
    }
}
