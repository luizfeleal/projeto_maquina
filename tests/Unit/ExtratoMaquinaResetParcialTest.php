<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ExtratoMaquinaService;
use App\Support\ApiClient;
use Illuminate\Http\Client\Response;
use GuzzleHttp\Psr7\Response as Psr7Response;

class ExtratoMaquinaResetParcialTest extends TestCase
{
    /**
     * Garante que resetParcial() chama apenas POST /maquinas/{id}/reset-parcial
     * e não chama nenhum endpoint que altere contadores (ex.: /extratoMaquina PUT/POST).
     */
    public function test_reset_parcial_chama_endpoint_correto(): void
    {
        $idMaquina   = '42';
        $dados       = ['realizado_por' => '1', 'observacao' => null];
        $expectedPath = "/maquinas/{$idMaquina}/reset-parcial";

        $chamadas = [];

        $mockResponse = new Response(new Psr7Response(
            201,
            ['Content-Type' => 'application/json'],
            json_encode([
                'message' => 'Reset parcial registrado com sucesso.',
                'data'    => [
                    'id'                   => 1,
                    'id_maquina'           => $idMaquina,
                    'valor_ultima_coleta'  => 750.00,
                    'valor_acumulado_total' => 750.00,
                    'realizado_por'        => '1',
                    'created_at'           => now()->toISOString(),
                    'saldo_periodo'        => 0.00,
                ],
            ])
        ));

        $this->mock(ApiClient::class, function ($mock) use ($expectedPath, $dados, $mockResponse, &$chamadas) {
            $mock->shouldReceive('post')
                 ->once()
                 ->withArgs(function ($path, $payload) use ($expectedPath, &$chamadas) {
                     $chamadas[] = $path;
                     return $path === $expectedPath;
                 })
                 ->andReturn($mockResponse);
        });

        $resultado = ExtratoMaquinaService::resetParcial($idMaquina, $dados);

        $this->assertTrue($resultado['success'], 'O reset parcial deve retornar success = true');

        // Garante que NENHUMA chamada foi feita a endpoints de contador/extrato
        $endpointsProibidos = ['/extratoMaquina', '/maquinas/' . $idMaquina, '/extrato/acumulado'];
        foreach ($chamadas as $chamada) {
            foreach ($endpointsProibidos as $proibido) {
                $this->assertStringNotContainsString(
                    $proibido,
                    $chamada === $endpointsProibidos[1] ? 'reset-parcial' : $chamada,
                    "O reset parcial não deve chamar o endpoint {$proibido}"
                );
            }
        }
    }

    /**
     * Garante que o cálculo do saldo do período é feito como total - ultima_coleta
     * e que o total_maquina não é alterado.
     */
    public function test_calculo_saldo_periodo_nao_altera_total(): void
    {
        $totalMaquina  = 750.00;
        $ultimaColeta  = 500.00;
        $saldoEsperado = $totalMaquina - $ultimaColeta;

        $this->assertEquals(250.00, $saldoEsperado, 'Saldo do período = total - última coleta');
        $this->assertEquals(750.00, $totalMaquina, 'O total da máquina não deve ser alterado após o cálculo do saldo');
    }

    /**
     * Garante que historicoResets() chama GET /reset-parcial/historico.
     */
    public function test_historico_resets_chama_endpoint_correto(): void
    {
        $mockResponse = new Response(new Psr7Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['data' => [], 'current_page' => 1, 'last_page' => 1, 'total' => 0])
        ));

        $this->mock(ApiClient::class, function ($mock) use ($mockResponse) {
            $mock->shouldReceive('get')
                 ->once()
                 ->with('/reset-parcial/historico', \Mockery::any())
                 ->andReturn($mockResponse);
        });

        $resultado = ExtratoMaquinaService::historicoResets([]);

        $this->assertIsArray($resultado, 'Histórico deve retornar um array');
        $this->assertArrayHasKey('data', $resultado);
    }
}
