<?php

namespace App\Support;

use App\Services\AuthService;
use App\Mocks\MockRouter;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Cliente HTTP unificado para a API externa.
 * Quando API_MOCK_ENABLED=true, delega ao MockRouter sem sair do container.
 */
class ApiClient
{
    public static function get(string $path, array $query = [], int $timeout = 30): Response
    {
        return self::request('GET', $path, ['query' => $query, 'timeout' => $timeout]);
    }

    public static function post(string $path, $data = [], int $timeout = 30): Response
    {
        return self::request('POST', $path, ['data' => $data, 'timeout' => $timeout]);
    }

    public static function put(string $path, array $data = [], int $timeout = 30): Response
    {
        return self::request('PUT', $path, ['data' => $data, 'timeout' => $timeout]);
    }

    public static function delete(string $path, int $timeout = 30): Response
    {
        return self::request('DELETE', $path, ['timeout' => $timeout]);
    }

    /**
     * POST multipart (upload de certificado EFI, etc.)
     *
     * @param array|null $attachment ['name' => string, 'contents' => string, 'filename' => string]
     */
    public static function postMultipart(string $path, array $data, ?array $attachment = null, int $timeout = 30): Response
    {
        return self::request('POST', $path, [
            'data' => $data,
            'attachment' => $attachment,
            'timeout' => $timeout,
            'multipart' => true,
        ]);
    }

    public static function request(string $method, string $path, array $options = []): Response
    {
        $path = '/' . ltrim($path, '/');

        if (ApiMock::enabled()) {
            return MockRouter::handle(
                strtoupper($method),
                $path,
                $options['data'] ?? [],
                $options['query'] ?? [],
                $options
            );
        }

        $token = AuthService::getToken();
        $url = rtrim(env('APP_URL_API'), '/') . $path;

        $request = Http::timeout($options['timeout'] ?? 30)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]);

        if (!empty($options['multipart']) && !empty($options['attachment'])) {
            $request = $request->attach(
                $options['attachment']['name'],
                $options['attachment']['contents'],
                $options['attachment']['filename']
            );
        }

        $data = $options['data'] ?? [];

        switch (strtoupper($method)) {
            case 'GET':
                return $request->get($url, $options['query'] ?? []);
            case 'POST':
                return $request->post($url, $data);
            case 'PUT':
                return $request->put($url, $data);
            case 'DELETE':
                return $request->delete($url);
            default:
                throw new \InvalidArgumentException("Método HTTP não suportado: {$method}");
        }
    }
}
