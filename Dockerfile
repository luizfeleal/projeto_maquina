# =============================================================================
# Dockerfile — PHP 8.1-FPM (Desenvolvimento)
# Projeto: SwiftPay / ProjetoMaquina — Laravel 8
# =============================================================================
# Usa Debian Bookworm (base oficial PHP) por ter melhor compatibilidade com
# extensões nativas que precisam de compilação (GD com freetype/jpeg, intl).
# Alpine não é recomendado aqui por conta das libs GD para geração de QR Code.
# =============================================================================
FROM php:8.1-fpm-bookworm

# -----------------------------------------------------------------------------
# 1. Argumentos de build (podem ser sobrescritos via docker-compose build args)
# -----------------------------------------------------------------------------
ARG PUID=1000
ARG PGID=1000

# -----------------------------------------------------------------------------
# 2. Dependências do sistema operacional
# -----------------------------------------------------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    # Ferramentas essenciais
    git \
    curl \
    unzip \
    zip \
    nano \
    # Libs para extensão GD (obrigatório para geração de QR Code com imagem)
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    # Libs para extensão intl
    libicu-dev \
    # Libs para extensão xml / mbstring
    libxml2-dev \
    libonig-dev \
    # Libs para extensão zip (PhpSpreadsheet)
    libzip-dev \
    # gosu: permite trocar de usuário de forma segura no entrypoint
    gosu \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------------------------
# 3. Extensões PHP
# -----------------------------------------------------------------------------
# Configura GD com suporte a freetype (fontes TTF para QR Code), JPEG e WebP
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp

# Instala todas as extensões necessárias para o Laravel 8 + requisitos do projeto
RUN docker-php-ext-install \
    pdo_mysql \      
    mbstring \       
    bcmath \         
    xml \            
    zip \            
    opcache \        
    intl \           
    exif \           
    pcntl \          
    gd               

# -----------------------------------------------------------------------------
# 4. Composer (copiado da imagem oficial — versão mais recente)
# -----------------------------------------------------------------------------
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# -----------------------------------------------------------------------------
# 5. Configuração do PHP para desenvolvimento
# -----------------------------------------------------------------------------
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Tuning para desenvolvimento: aumenta limites de memória e tempo de execução
RUN echo "memory_limit = 512M" >> "$PHP_INI_DIR/conf.d/99-custom.ini" \
    && echo "max_execution_time = 300" >> "$PHP_INI_DIR/conf.d/99-custom.ini" \
    && echo "upload_max_filesize = 64M" >> "$PHP_INI_DIR/conf.d/99-custom.ini" \
    && echo "post_max_size = 64M" >> "$PHP_INI_DIR/conf.d/99-custom.ini" \
    && echo "display_errors = On" >> "$PHP_INI_DIR/conf.d/99-custom.ini"

# -----------------------------------------------------------------------------
# 6. Diretório de trabalho
# -----------------------------------------------------------------------------
WORKDIR /var/www/html

# -----------------------------------------------------------------------------
# 7. Script de entrada (resolve UID/GID e permissões em runtime)
# -----------------------------------------------------------------------------
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
