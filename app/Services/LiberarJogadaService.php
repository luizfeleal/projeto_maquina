<?php

namespace App\Services;

use App\Support\ApiClient;

class LiberarJogadaService
{
    public static function criar($dados)
    {
        return ApiClient::post('/hardware/liberarJogada', $dados);
    }
}
