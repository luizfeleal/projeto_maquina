<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Mensalidade</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
        .container { max-width:600px; margin:40px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
        .header { background:#1a6b4a; padding:32px 24px; text-align:center; }
        .header h1 { color:#fff; margin:0; font-size:1.4rem; }
        .body { padding:32px 24px; }
        .body p { color:#374151; line-height:1.6; margin:0 0 16px; }
        .highlight { background:#e8f5ee; border-left:4px solid #1a6b4a; padding:16px; border-radius:4px; margin:24px 0; }
        .highlight .valor { font-size:1.5rem; font-weight:700; color:#1a6b4a; }
        .badge-urgente { display:inline-block; background:#ef4444; color:#fff; border-radius:4px; padding:4px 10px; font-size:.8rem; font-weight:700; margin-bottom:16px; }
        .badge-atencao { display:inline-block; background:#f59e0b; color:#fff; border-radius:4px; padding:4px 10px; font-size:.8rem; font-weight:700; margin-bottom:16px; }
        .badge-info { display:inline-block; background:#3b82f6; color:#fff; border-radius:4px; padding:4px 10px; font-size:.8rem; font-weight:700; margin-bottom:16px; }
        .footer { background:#f9fafb; padding:20px 24px; text-align:center; color:#9ca3af; font-size:.8rem; border-top:1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>SwiftPay Soluções</h1>
    </div>

    <div class="body">
        <p>Olá, <strong>{{ $nomeCliente }}</strong>!</p>

        @if($diasRestantes === 0)
            <span class="badge-urgente">Vence hoje</span>
            <p>Sua mensalidade <strong>{{ $mensalidade->descricao }}</strong> vence <strong>hoje</strong>. Evite bloqueios realizando o pagamento ainda hoje.</p>
        @elseif($diasRestantes <= 3)
            <span class="badge-atencao">Vence em {{ $diasRestantes }} {{ $diasRestantes === 1 ? 'dia' : 'dias' }}</span>
            <p>Sua mensalidade <strong>{{ $mensalidade->descricao }}</strong> vence em <strong>{{ $diasRestantes }} {{ $diasRestantes === 1 ? 'dia' : 'dias' }}</strong>. Programe o pagamento com antecedência.</p>
        @else
            <span class="badge-info">Vence em {{ $diasRestantes }} dias</span>
            <p>Sua mensalidade <strong>{{ $mensalidade->descricao }}</strong> vence em <strong>{{ $diasRestantes }} dias</strong>.</p>
        @endif

        <div class="highlight">
            <p style="margin:0 0 4px; font-size:.85rem; color:#6b7280;">Valor a pagar</p>
            <p class="valor" style="margin:0;">R$ {{ number_format($mensalidade->valor, 2, ',', '.') }}</p>
            <p style="margin:8px 0 0; font-size:.85rem; color:#6b7280;">
                Vencimento: <strong>{{ $mensalidade->data_vencimento->format('d/m/Y') }}</strong>
            </p>
        </div>

        <p>Em caso de dúvidas, entre em contato com nosso suporte.</p>
        <p>Atenciosamente,<br><strong>Equipe SwiftPay Soluções</strong></p>
    </div>

    <div class="footer">
        Este é um e-mail automático, por favor não responda.
        &copy; {{ date('Y') }} SwiftPay Soluções. Todos os direitos reservados.
    </div>

</div>
</body>
</html>
