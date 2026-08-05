<?php

namespace App\Services;

use App\Support\ApiClient;

class UsuariosService
{
    public static function criar($dados)
    {
        $response = ApiClient::post('/usuarios', $dados);

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json(),
            ];
        }

        return [
            'success' => false,
            'status' => $response->status(),
            'error' => $response->json(),
        ];
    }

    public static function coletar(string $id = null)
    {
        $path = is_null($id) ? '/usuarios' : "/usuarios/{$id}";
        return ApiClient::get($path)->json();
    }

    /**
     * Busca usuários filtrando por login ou email direto no banco (WHERE),
     * em vez de baixar a tabela inteira e filtrar em PHP. Usado no login,
     * onde a tabela completa não é necessária — só o(s) usuário(s) que batem
     * com o login/email informado.
     */
    public static function buscarPorLoginOuEmail(string $valor)
    {
        $response = ApiClient::get('/usuarios', ['login' => $valor]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json();
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/usuarios');

        if (!$response->successful()) {
            return [];
        }

        $usuariosFiltrado = $response->json();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $usuariosFiltrado = array_filter($usuariosFiltrado, function ($usuario) use ($chave, $valor) {
                    return isset($usuario[$chave]) && $usuario[$chave] == $valor;
                });
            }
        }

        return $usuariosFiltrado;
    }

    public static function atualizar($dados, $id)
    {
        return ApiClient::put("/usuarios/{$id}", $dados)->json();
    }
}
