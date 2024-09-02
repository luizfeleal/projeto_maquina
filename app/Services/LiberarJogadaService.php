<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class LiberarJogadaService
{


    public static function criar($dados){
        $url = env('APP_URL_API') . "/hardware/liberarJogada";
								
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $maquinas = $response;

        return $maquinas;
    }


}
