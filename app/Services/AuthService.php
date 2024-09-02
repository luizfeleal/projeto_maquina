<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
class AuthService
{
    public static function getToken()
    {
        $url = env('APP_URL_API') . '/auth/login';
        $response = Http::post($url, [
            'email' => env('EMAIL_API'), // Substitua pelo email do usuário
            'password' => env('PASSWORD_API') // Substitua pela senha do usuário
        ]);

        $token = $response['access_token'];
        return $token;
    }
}