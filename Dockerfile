# ============================================================
# Stage 1: Install PHP dependencies
# ============================================================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

COPY . .

RUN composer dump-autoload --optimize --no-dev

# ============================================================
# Stage 2: Build frontend assets (needs vendor/ for Flux CSS)
# ============================================================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json vite.config.js vite-module-loader.js ./

RUN npm ci --ignore-scripts

# Copy only source files needed for the Vite build
COPY resources/ resources/
COPY Modules/ Modules/
COPY public/ public/

# Flux CSS + Tailwind content sources live under Composer packages
COPY --from=vendor /app/vendor ./vendor

RUN npm run build

# ============================================================
# Stage 3: Production image
# ============================================================
FROM php:8.4-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    curl-dev \
    libxml2-dev \
    supervisor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        bcmath \
        intl \
        opcache \
        pcntl \
        exif

# Configure OPcache for production
RUN { \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=64'; \
    echo 'opcache.max_accelerated_files=30000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.jit=on'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# PHP production settings
RUN { \
    echo 'upload_max_filesize=64M'; \
    echo 'post_max_size=64M'; \
    echo 'memory_limit=256M'; \
    echo 'max_execution_time=60'; \
    echo 'expose_php=Off'; \
} > /usr/local/etc/php/conf.d/production.ini

# Nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create app user
RUN addgroup -g 1000 -S app \
   && adduser -u 1000 -S app -G app \
   && mkdir -p /var/log/supervisor \
   && mkdir -p /var/run/supervisor

WORKDIR /var/www/html

# Copy application
COPY --from=vendor /app/vendor ./vendor
COPY . .

# Copy built frontend assets
COPY --from=frontend /app/public/build ./public/build

# Entrypoint script (copy before cleanup removes docker/)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Remove dev/build files from final image
RUN rm -rf node_modules tests .env .env.* \
    public/hot public/.hot \
    docker/ .git .github .claude .cursor .vscode \
    *.md phpunit.xml .phpunit.result.cache

# Create required directories and set permissions (only writable paths)
RUN mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R app:www-data storage bootstrap/cache \
    && chmod -R g+rwX storage bootstrap/cache \
    && find storage bootstrap/cache -type d -exec chmod g+s {} \;

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
