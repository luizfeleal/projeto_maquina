<?php

namespace App\Services;

use App\Support\ApiClient;

class QrCodeService
{
    public static function criar($dados)
    {
        $payload = is_object($dados) && method_exists($dados, 'all') ? $dados->all() : $dados;
        return ApiClient::post('/QRCode', $payload);
    }

    public static function coletar(string $id = null)
    {
        $path = is_null($id) ? '/QRCode' : "/QRCode/{$id}";
        $response = ApiClient::get($path);

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();

        if (!is_array($data)) {
            return [];
        }

        if ($id !== null) {
            return isset($data['id_qr']) || isset($data['id_maquina']) ? [$data] : [];
        }

        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }

        // Resposta de erro da API: { "message": "..." }
        if (isset($data['message']) && !isset($data[0])) {
            return [];
        }

        foreach ($data as $item) {
            if (!is_array($item)) {
                return [];
            }
        }

        return $data;
    }

    public static function coletarComFiltro($filtros, $tipo)
    {
        $qrCode = self::coletar();

        foreach ($filtros as $chave => $valor) {
            if ($valor !== null) {
                $qrCode = array_filter($qrCode, function ($qr) use ($chave, $valor) {
                    return isset($qr[$chave]) && $qr[$chave] == $valor;
                });
            }
        }

        return array_values($qrCode);
    }

    public function atualizar($dados, $id)
    {
        return ApiClient::post("/maquinas/{$id}", $dados)->json();
    }

    public static function deletar($id)
    {
        $response = ApiClient::delete("/QRCode/{$id}");

        if ($response->successful()) {
            return $response->json();
        }

        return response()->json([
            'error' => 'Failed to delete the resource.',
            'status' => $response->status(),
            'message' => $response->body(),
        ], $response->status());
    }
}
