<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocaisService;
use App\Services\ClienteLocalService;
use App\Services\MaquinasService;
use App\Services\ExtratoMaquinaService;
use App\Services\ClientesService;
use App\Services\UsuariosService;
use App\Services\CredApiPixService;
use App\Services\GruposAcessoService;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function coletarToken(){
        $token = AuthService::getToken();
        return response()->json(['token' => $token]);

    }

    
}
