<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public static function getToken()
    {
        try {
            $url = env('APP_URL_API') . '/auth/login';
            
            // Verifica se as credenciais estão configuradas
            $email = env('EMAIL_API');
            $password = env('PASSWORD_API');
            
            if (empty($email) || empty($password)) {
                Log::error('Credenciais da API não configuradas no .env');
                throw new \Exception('Credenciais da API não configuradas');
            }
            
            Log::info('Tentando autenticar na API', ['url' => $url, 'email' => $email]);
            
            $response = Http::timeout(10)->post($url, [
                'email' => $email,
                'password' => $password
            ]);

            // Verifica se a requisição foi bem-sucedida
            if (!$response->successful()) {
                Log::error('Falha na autenticação com a API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Falha ao autenticar na API: ' . $response->status());
            }

            $responseData = $response->json();

            // Verifica se o token existe na resposta
            if (!isset($responseData['access_token'])) {
                Log::error('Token não encontrado na resposta da API', [
                    'response' => $responseData
                ]);
                throw new \Exception('Token de acesso não encontrado na resposta da API');
            }

            $token = $responseData['access_token'];
            Log::info('Token obtido com sucesso');
            
            return $token;
            
        } catch (\Exception $e) {
            Log::error('Erro ao obter token: ' . $e->getMessage());
            throw $e;
        }
    }
}