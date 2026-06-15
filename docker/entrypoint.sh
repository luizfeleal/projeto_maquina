#!/bin/bash
# =============================================================================
# entrypoint.sh — Script de entrada do container PHP-FPM
# Projeto: SwiftPay / ProjetoMaquina
# =============================================================================
#
# Por que este script existe?
#
# O problema clássico de permissões Docker ocorre quando:
#   - O código fonte é montado via bind mount (- .:/var/www/html)
#   - O PHP-FPM roda como usuário `www-data` (UID 33 na imagem Debian)
#   - No host macOS/Linux, os arquivos pertencem ao usuário do desenvolvedor
#
# A solução adotada aqui:
#   1. Lê as variáveis PUID e PGID do ambiente (definidas no docker-compose.yml)
#   2. Altera o UID/GID do usuário `www-data` para coincidir com o do host
#   3. Corrige permissões das pastas que o Laravel precisa escrever
#   4. Inicia PHP-FPM como root (processo master); workers usam www-data do pool.
#      NÃO usar gosu para php-fpm — no Linux da VPS isso quebra stderr e o FPM cai
#      com "Permission denied" em /proc/self/fd/2.
#   5. Para artisan/composer interativos, usa gosu www-data.
#
# IMPORTANTE para macOS:
#   Docker Desktop no Mac usa uma VM intermediária. As permissões de bind mount
#   são tratadas de forma transparente pela VM, então PUID/PGID=1000 funciona
#   bem mesmo que seu usuário no Mac tenha UID 501.
#
# IMPORTANTE para Linux:
#   Execute `id -u` e `id -g` no host e defina no seu .env:
#   PUID=1000  # substitua pelo valor real
#   PGID=1000  # substitua pelo valor real
# =============================================================================

set -e

# Valores padrão: UID/GID 1000 (usuário padrão do Linux e do Docker Desktop)
PUID=${PUID:-1000}
PGID=${PGID:-1000}

echo "[entrypoint] Configurando usuário www-data → UID=${PUID} GID=${PGID}"

# -----------------------------------------------------------------------------
# 1. Ajusta o grupo www-data para usar o PGID do host
# -----------------------------------------------------------------------------
# `|| true` evita falha se o GID já estiver em uso por outro grupo
groupmod -g "${PGID}" www-data 2>/dev/null || true

# -----------------------------------------------------------------------------
# 2. Ajusta o usuário www-data para usar o PUID do host
# -----------------------------------------------------------------------------
usermod -u "${PUID}" -g "${PGID}" www-data 2>/dev/null || true

# -----------------------------------------------------------------------------
# 3. Garante que as pastas que o Laravel precisa escrever existam e sejam
#    acessíveis ao www-data.
#
#    storage/         → logs, cache de views, sessões (driver file), uploads
#    bootstrap/cache/ → cache de rotas, configurações e serviços compilados
# -----------------------------------------------------------------------------
echo "[entrypoint] Ajustando permissões em storage/ e bootstrap/cache/"
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# -----------------------------------------------------------------------------
# 4. Inicia o processo principal.
# -----------------------------------------------------------------------------
echo "[entrypoint] Iniciando: $@"

if [ "$1" = "php-fpm" ]; then
    shift
    exec php-fpm -F "$@"
fi

exec gosu www-data "$@"
