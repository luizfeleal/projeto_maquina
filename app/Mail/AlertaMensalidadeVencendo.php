<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Mensalidade;

class AlertaMensalidadeVencendo extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Mensalidade $mensalidade,
        public readonly int $diasRestantes,
        public readonly string $nomeCliente,
        public readonly string $emailCliente,
    ) {}

    public function build(): static
    {
        $assunto = match(true) {
            $this->diasRestantes === 0 => 'Sua mensalidade vence HOJE — SwiftPay',
            $this->diasRestantes === 1 => 'Sua mensalidade vence amanhã — SwiftPay',
            default                    => "Sua mensalidade vence em {$this->diasRestantes} dias — SwiftPay",
        };

        return $this->to($this->emailCliente, $this->nomeCliente)
                    ->subject($assunto)
                    ->view('emails.mensalidade.alerta');
    }
}
