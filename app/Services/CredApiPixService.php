<?php

namespace App\Services;

use App\Support\ApiClient;

class CredApiPixService
{
    public static function criar($dados)
    {
        $payload = [
            'id_cliente' => $dados['id_cliente'],
            'client_secret' => $dados['client_secret'],
            'client_id' => $dados['client_id'],
            'tipo_cred' => $dados['tipo_cred'],
        ];

        $attachment = null;
        if (isset($dados['caminho_certificado'])) {
            $attachment = [
                'name' => 'caminho_certificado',
                'contents' => file_get_contents($dados['caminho_certificado']->getRealPath()),
                'filename' => $dados['caminho_certificado']->getClientOriginalName(),
            ];
        }

        $response = $attachment
            ? ApiClient::postMultipart('/credApiPix', $payload, $attachment)
            : ApiClient::post('/credApiPix', $payload);

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
        $path = is_null($id) ? '/credApiPix' : "/credApiPix/{$id}";
        $response = ApiClient::get($path);

        if (!is_null($id) && !$response->successful()) {
            return null;
        }

        return $response->json();
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $response = ApiClient::get('/credApiPix');

        if (!$response->successful()) {
            return [];
        }

        $credenciais = $response->json();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $credenciais = array_filter($credenciais, function ($item) use ($chave, $valor) {
                    return isset($item[$chave]) && $item[$chave] == $valor;
                });
            }
        }

        return $credenciais;
    }

    public function atualizar($dados, $id)
    {
        return ApiClient::post("/usuarios/{$id}", $dados)->json();
    }

    public static function atualizarCredencial($dados, $id)
    {
        $payload = [
            'id_cliente' => $dados['id_cliente'],
            'client_secret' => $dados['client_secret'],
            'client_id' => $dados['client_id'],
            'tipo_cred' => $dados['tipo_cred'],
        ];

        $temCertificado = isset($dados['caminho_certificado']) && $dados['caminho_certificado'];

        if ($temCertificado) {
            $response = ApiClient::postMultipart(
                "/credApiPix/{$id}/atualizar",
                $payload,
                [
                    'name' => 'caminho_certificado',
                    'contents' => file_get_contents($dados['caminho_certificado']->getRealPath()),
                    'filename' => $dados['caminho_certificado']->getClientOriginalName(),
                ]
            );
        } else {
            $response = ApiClient::put("/credApiPix/{$id}", $payload);
        }

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

    public static function excluirCredencial($id)
    {
        $response = ApiClient::delete("/credApiPix/{$id}");

        if ($response->successful()) {
            return ['success' => true];
        }

        $body = $response->json();
        return [
            'success' => false,
            'error' => $body['message'] ?? $body['response'] ?? 'Erro ao excluir credencial',
        ];
    }
}
