<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mensalidade;
use App\Mail\AlertaMensalidadeVencendo;
use App\Services\ClientesService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EnviarAlertasMensalidade extends Command
{
    protected $signature = 'app:enviar-alertas-mensalidade';

    protected $description = 'Envia e-mails de alerta para mensalidades vencendo em 5, 3 e 0 dias';

    private const DIAS_ALERTA = [5, 3, 0];

    public function handle(): int
    {
        $totalEnviados = 0;

        foreach (self::DIAS_ALERTA as $dias) {
            $mensalidades = Mensalidade::pendentes()->vencendoEm($dias)->get();

            foreach ($mensalidades as $mensalidade) {
                try {
                    [$nome, $email] = $this->dadosCliente($mensalidade->id_cliente);

                    if (!$email) {
                        $this->warn("Cliente {$mensalidade->id_cliente} sem e-mail cadastrado.");
                        continue;
                    }

                    Mail::send(new AlertaMensalidadeVencendo($mensalidade, $dias, $nome, $email));

                    $totalEnviados++;
                    $this->info("Alerta enviado para {$email} (vencimento em {$dias} dias).");
                } catch (\Throwable $e) {
                    Log::error('Erro ao enviar alerta de mensalidade', [
                        'id_mensalidade' => $mensalidade->id,
                        'id_cliente'     => $mensalidade->id_cliente,
                        'dias'           => $dias,
                        'error'          => $e->getMessage(),
                    ]);
                    $this->error("Falha ao enviar alerta para mensalidade #{$mensalidade->id}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Total de alertas enviados: {$totalEnviados}");

        return 0;
    }

    private function dadosCliente(int $idCliente): array
    {
        try {
            $clientes = ClientesService::coletar();
            $cliente  = collect($clientes)->firstWhere('id_cliente', $idCliente);

            return [
                $cliente['cliente_nome'] ?? "Cliente #{$idCliente}",
                $cliente['cliente_email'] ?? null,
            ];
        } catch (\Throwable) {
            return ["Cliente #{$idCliente}", null];
        }
    }
}
