const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// =============================================================================
// Configuração de HMR (Hot Module Replacement) para Docker
// =============================================================================
//
// CONTEXTO: Este projeto usa Laravel Mix (webpack), NÃO Vite.
//
// O problema do HMR no Docker:
//   O webpack-dev-server roda DENTRO do container na porta 8080, mas o
//   navegador está no HOST (sua máquina). Para o HMR funcionar, precisamos:
//
//   1. O servidor escutar em 0.0.0.0 (todas as interfaces do container)
//      para aceitar conexões de fora do container.
//   2. Informar ao Mix que o endereço público do servidor (para o navegador)
//      é `localhost` (host da máquina, com a porta mapeada pelo Docker).
//
//   O helper `mix()` nos templates Blade detecta o arquivo `public/hot`
//   criado pelo `npm run hot`. Quando esse arquivo existe, o Mix redireciona
//   todas as URLs de assets para http://localhost:8080/. O navegador faz
//   requisições a localhost:8080, que o Docker encaminha ao container.
//
// Como ativar HMR:
//   1. No docker-compose.yml: certifique-se que a porta 8080 está mapeada no
//      serviço `node` (já está configurado).
//   2. Suba o serviço node com: docker-compose --profile assets up node
//   3. Dentro do container node: altere o command para `npm run hot`
//      OU execute: docker-compose exec node npm run hot
//
// Para uso sem Docker (desenvolvimento local direto):
//   O bloco abaixo não afeta o comportamento — as opções só têm efeito
//   quando `npm run hot` é executado.
// =============================================================================

const isRunningInDocker = process.env.RUNNING_IN_DOCKER === 'true';
const hmrPort = process.env.MIX_HMR_PORT || 8080;

if (isRunningInDocker) {
    mix.options({
        hmrOptions: {
            // Endereço que o NAVEGADOR usará para conectar ao webpack-dev-server.
            // O Docker mapeia a porta do container para localhost do host.
            host: 'localhost',
            port: hmrPort,
        },
    });

    mix.webpackConfig({
        devServer: {
            // Escuta em todas as interfaces do container para aceitar conexões externas
            host: '0.0.0.0',
            port: hmrPort,
        },
    });
}

// =============================================================================
// Compilação de assets
// =============================================================================

mix
    .sass('resources/scss/style.scss', 'public/site/style.css');
    //.scripts('node_modules/jquery/dist/jquery.js', 'public/site/jquery.js')
    //.scripts('node_modules/bootstrap/dist/js/bootstrap.bundle.js', 'public/site/bootstrap.js');
    //.scripts('node_modules/bootstrap/dist/js/bootstrap.bundle.js.map', 'public/site/bootstrap.bundle.js.map');