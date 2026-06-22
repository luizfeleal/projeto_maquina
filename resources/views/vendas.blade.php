@extends('layouts.public')

@section('title', 'SwiftPay — Placas de Pagamento para Máquinas')

@section('head')
<style>
    /* ── Hero ── */
    .hero {
        background: linear-gradient(135deg, #1E2E5E 0%, #0f4a33 100%);
        color: #fff;
        padding: 100px max(24px, 5vw) 80px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 999px;
        padding: 6px 16px;
        font-size: .8rem;
        font-weight: 600;
        letter-spacing: .04em;
        margin-bottom: 24px;
    }
    .hero h1 {
        font-size: clamp(2rem, 5vw, 3.2rem);
        font-weight: 800;
        line-height: 1.15;
        max-width: 700px;
        margin: 0 auto 20px;
        letter-spacing: -.02em;
    }
    .hero h1 span { color: #86efac; }
    .hero p {
        font-size: clamp(.95rem, 2vw, 1.15rem);
        max-width: 560px;
        margin: 0 auto 36px;
        color: rgba(255,255,255,.8);
        line-height: 1.7;
    }
    .hero-cta-group {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .btn-hero-primary {
        background: #27ae60;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 14px 32px;
        font-size: 1rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background .2s;
    }
    .btn-hero-primary:hover { background: #1a6b4a; color: #fff; }
    .btn-hero-ghost {
        background: transparent;
        color: #fff;
        border: 2px solid rgba(255,255,255,.4);
        border-radius: 10px;
        padding: 14px 32px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: border-color .2s;
    }
    .btn-hero-ghost:hover { border-color: #fff; color: #fff; }

    /* ── Estatísticas ── */
    .stats-bar {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        padding: 28px max(24px, 5vw);
    }
    .stats-bar .inner {
        max-width: 900px;
        margin: 0 auto;
        display: flex;
        gap: 32px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .stat-item { text-align: center; }
    .stat-item .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1E2E5E;
        line-height: 1;
    }
    .stat-item .stat-label {
        font-size: .8rem;
        color: #6b7280;
        margin-top: 4px;
        font-weight: 500;
    }

    /* ── Cards de produto ── */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
    }
    .product-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 28px 24px;
        transition: box-shadow .2s, transform .2s;
        position: relative;
    }
    .product-card:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,.1);
        transform: translateY(-2px);
    }
    .product-card.featured {
        border-color: #1a6b4a;
        box-shadow: 0 0 0 2px rgba(26,107,74,.15);
    }
    .product-card .featured-badge {
        position: absolute;
        top: -1px;
        right: 24px;
        background: #1a6b4a;
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 0 0 8px 8px;
        letter-spacing: .04em;
    }
    .product-card .product-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        font-size: 1.6rem;
    }
    .product-card h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: #111827;
    }
    .product-card p {
        font-size: .88rem;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    .product-price {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1E2E5E;
    }
    .product-price span {
        font-size: .8rem;
        font-weight: 500;
        color: #9ca3af;
    }
    .product-features {
        list-style: none;
        margin: 16px 0 24px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .product-features li {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .85rem;
        color: #374151;
    }
    .product-features li::before {
        content: '';
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #dcfce7;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%2316a34a'%3E%3Cpath fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        background-size: 12px;
    }
    .btn-product {
        width: 100%;
        text-align: center;
        background: #1E2E5E;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        font-size: .9rem;
        font-weight: 600;
        text-decoration: none;
        display: block;
        transition: background .2s;
    }
    .btn-product:hover { background: #162248; color: #fff; }
    .btn-product.btn-green { background: #1a6b4a; }
    .btn-product.btn-green:hover { background: #0f4a33; }

    /* ── Como funciona ── */
    .steps {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 24px;
        counter-reset: step;
    }
    .step-item {
        text-align: center;
        padding: 24px 16px;
        counter-increment: step;
        position: relative;
    }
    .step-num {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--pub-primary);
        color: #fff;
        font-size: 1.1rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }
    .step-item h4 { font-size: .95rem; font-weight: 700; margin-bottom: 8px; }
    .step-item p  { font-size: .82rem; color: #6b7280; }

    /* ── Seções ── */
    .section-title {
        text-align: center;
        margin-bottom: 48px;
    }
    .section-title h2 {
        font-size: clamp(1.5rem, 3vw, 2.2rem);
        font-weight: 800;
        letter-spacing: -.02em;
        color: #111827;
    }
    .section-title p {
        color: #6b7280;
        font-size: .95rem;
        max-width: 480px;
        margin: 12px auto 0;
    }

    /* ── CTA Final ── */
    .cta-final {
        background: linear-gradient(135deg, #1E2E5E 0%, #2C9BA5 100%);
        color: #fff;
        text-align: center;
        padding: 80px max(24px, 5vw);
    }
    .cta-final h2 {
        font-size: clamp(1.6rem, 4vw, 2.4rem);
        font-weight: 800;
        margin-bottom: 16px;
    }
    .cta-final p {
        color: rgba(255,255,255,.8);
        margin-bottom: 36px;
        font-size: 1.05rem;
    }
</style>
@endsection

@section('content')

{{-- ── Hero ── --}}
<section class="hero">
    <div style="position:relative; z-index:1;">
        <div class="hero-badge">
            <iconify-icon icon="solar:cpu-bold-duotone" style="font-size:1rem;"></iconify-icon>
            Hardware certificado &amp; homologado
        </div>
        <h1>
            Placas de Pagamento para<br>
            <span>Máquinas Inteligentes</span>
        </h1>
        <p>
            Integre Pix, cartão e dinheiro na sua máquina com a tecnologia SwiftPay.
            Instalação simples, ativação imediata e gestão 100% online.
        </p>
        <div class="hero-cta-group">
            <a href="#planos" class="btn-hero-primary">
                <iconify-icon icon="solar:cart-large-minimalistic-bold-duotone"></iconify-icon>
                Ver planos e preços
            </a>
            <a href="{{ route('login-view') }}" class="btn-hero-ghost">
                <iconify-icon icon="solar:login-3-bold-duotone"></iconify-icon>
                Já sou cliente
            </a>
        </div>
    </div>
</section>

{{-- ── Stats ── --}}
<div class="stats-bar">
    <div class="inner">
        <div class="stat-item">
            <div class="stat-value">+500</div>
            <div class="stat-label">Máquinas ativas</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">R$ 2M+</div>
            <div class="stat-label">Transacionados/mês</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">99,7%</div>
            <div class="stat-label">Uptime garantido</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">24h</div>
            <div class="stat-label">Suporte técnico</div>
        </div>
    </div>
</div>

{{-- ── Produtos / Planos ── --}}
<section class="pub-section" id="planos">
    <div class="section-title">
        <h2>Escolha o plano ideal para sua operação</h2>
        <p>Placas homologadas para aceitar Pix, cartão e dinheiro — com ativação em minutos.</p>
    </div>

    <div class="product-grid">

        {{-- Plano Básico --}}
        <div class="product-card">
            <div class="product-icon" style="background:#eff6ff;">
                <iconify-icon icon="solar:cpu-bold-duotone" style="color:#3b82f6;"></iconify-icon>
            </div>
            <h3>Placa Starter</h3>
            <p>Ideal para quem está começando. Aceita Pix e dinheiro físico com monitoramento remoto básico.</p>
            <div class="product-price">R$ 490 <span>/ unidade</span></div>
            <ul class="product-features">
                <li>Aceita Pix instantâneo</li>
                <li>Aceita dinheiro físico</li>
                <li>Dashboard online de gestão</li>
                <li>Relatórios mensais</li>
                <li>Suporte por e-mail</li>
            </ul>
            <a href="{{ route('login-view') }}" class="btn-product">Solicitar proposta</a>
        </div>

        {{-- Plano Pro (destaque) --}}
        <div class="product-card featured">
            <span class="featured-badge">Mais popular</span>
            <div class="product-icon" style="background:#e8f5ee;">
                <iconify-icon icon="solar:medal-ribbons-star-bold-duotone" style="color:#1a6b4a;"></iconify-icon>
            </div>
            <h3>Placa Pro</h3>
            <p>A escolha certa para operações em crescimento. Pix, cartão e dinheiro com gestão financeira completa.</p>
            <div class="product-price">R$ 790 <span>/ unidade</span></div>
            <ul class="product-features">
                <li>Aceita Pix, cartão e dinheiro</li>
                <li>Dashboard financeiro avançado</li>
                <li>Alertas de inadimplência automáticos</li>
                <li>Relatórios e extratos detalhados</li>
                <li>Reset de aferição remoto</li>
                <li>Suporte prioritário</li>
            </ul>
            <a href="{{ route('login-view') }}" class="btn-product btn-green">Solicitar proposta</a>
        </div>

        {{-- Plano Enterprise --}}
        <div class="product-card">
            <div class="product-icon" style="background:#faf5ff;">
                <iconify-icon icon="solar:buildings-3-bold-duotone" style="color:#7c3aed;"></iconify-icon>
            </div>
            <h3>Placa Enterprise</h3>
            <p>Para frotas grandes. Integração via API, gestão multiunidade e SLA dedicado.</p>
            <div class="product-price" style="font-size:1.2rem; color:#7c3aed;">Sob consulta</div>
            <ul class="product-features">
                <li>Tudo do plano Pro</li>
                <li>API de integração completa</li>
                <li>Módulo financeiro dedicado</li>
                <li>Gestão multiunidade / franquia</li>
                <li>SLA 99,9% com suporte 24h</li>
                <li>Onboarding presencial</li>
            </ul>
            <a href="{{ route('login-view') }}" class="btn-product" style="background:#7c3aed;">
                Falar com especialista
            </a>
        </div>

    </div>
</section>

{{-- ── Como funciona ── --}}
<div class="pub-section--alt">
    <div class="inner">
        <div class="section-title">
            <h2>Como funciona</h2>
            <p>Em 4 passos simples sua máquina começa a aceitar pagamentos digitais.</p>
        </div>
        <div class="steps">
            <div class="step-item">
                <div class="step-num">1</div>
                <h4>Escolha o plano</h4>
                <p>Selecione a placa ideal para o seu tipo de máquina e volume de transações.</p>
            </div>
            <div class="step-item">
                <div class="step-num">2</div>
                <h4>Instalação rápida</h4>
                <p>Receba a placa e instale em minutos com nosso guia passo a passo ou suporte remoto.</p>
            </div>
            <div class="step-item">
                <div class="step-num">3</div>
                <h4>Ativação online</h4>
                <p>Cadastre a placa no painel SwiftPay e configure suas credenciais de pagamento.</p>
            </div>
            <div class="step-item">
                <div class="step-num">4</div>
                <h4>Gerencie tudo</h4>
                <p>Acompanhe extratos, receitas, alertas e status das máquinas em tempo real.</p>
            </div>
        </div>
    </div>
</div>

{{-- ── Diferenciais ── --}}
<section class="pub-section">
    <div class="section-title">
        <h2>Por que escolher a SwiftPay?</h2>
    </div>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:24px;">
        @php
        $diferenciais = [
            ['icon' => 'solar:shield-check-bold-duotone', 'cor' => '#16a34a', 'bg' => '#dcfce7',
             'titulo' => 'Segurança certificada',
             'desc' => 'Transações criptografadas e homologadas pelo Banco Central.'],
            ['icon' => 'solar:chart-2-bold-duotone', 'cor' => '#2C9BA5', 'bg' => '#e0f2fe',
             'titulo' => 'Dashboard em tempo real',
             'desc' => 'Visualize extrato, saldo e alertas de inadimplência a qualquer hora.'],
            ['icon' => 'solar:wifi-bold-duotone', 'cor' => '#7c3aed', 'bg' => '#f5f3ff',
             'titulo' => 'Monitoramento remoto',
             'desc' => 'Saiba instantaneamente quando uma máquina fica offline ou com falha.'],
            ['icon' => 'solar:smartphone-update-bold-duotone', 'cor' => '#f59e0b', 'bg' => '#fffbeb',
             'titulo' => 'App mobile-first',
             'desc' => 'Experiência semelhante à de um app instalado — acesse direto do celular.'],
        ];
        @endphp
        @foreach($diferenciais as $d)
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:24px;">
            <div style="width:48px; height:48px; border-radius:12px; background:{{ $d['bg'] }};
                        display:flex; align-items:center; justify-content:center; margin-bottom:16px;">
                <iconify-icon icon="{{ $d['icon'] }}"
                              style="font-size:1.5rem; color:{{ $d['cor'] }};"></iconify-icon>
            </div>
            <h4 style="font-size:.95rem; font-weight:700; margin-bottom:8px;">{{ $d['titulo'] }}</h4>
            <p style="font-size:.84rem; color:#6b7280; line-height:1.6;">{{ $d['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ── CTA Final ── --}}
<section class="cta-final">
    <h2>Pronto para modernizar suas máquinas?</h2>
    <p>Entre em contato e receba uma proposta personalizada para sua operação.</p>
    <a href="{{ route('login-view') }}" class="btn-hero-primary"
       style="display:inline-flex; margin:0 auto;">
        <iconify-icon icon="solar:arrow-right-bold-duotone"></iconify-icon>
        Começar agora
    </a>
</section>

@endsection

@section('scripts')
<script>
// Smooth scroll para âncoras internas
document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>
@endsection
