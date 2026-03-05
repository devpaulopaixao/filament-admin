# ── Base ─────────────────────────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS base

RUN apk add --no-cache \
        bash curl git unzip zip \
        freetype-dev libjpeg-turbo-dev libpng-dev libwebp-dev \
        libzip-dev oniguruma-dev icu-dev supervisor

RUN docker-php-ext-configure gd \
        --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j$(nproc) \
        bcmath exif gd intl mbstring opcache pcntl pdo_mysql zip

# Redis extension
RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS linux-headers \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && apk del .phpize-deps

WORKDIR /var/www
EXPOSE 9000

# ── Development ───────────────────────────────────────────────────────────────
FROM base AS development

# Xdebug
RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS linux-headers \
 && pecl install xdebug \
 && docker-php-ext-enable xdebug \
 && apk del .phpize-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]

# ── Production ────────────────────────────────────────────────────────────────
FROM base AS production

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]
