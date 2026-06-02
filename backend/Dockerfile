FROM php:8.4-fpm-alpine

# ─── System Dependencies ──────────────────────────────────────────────────────
RUN apk add --no-cache \
    bash \
    curl \
    git \
    zip \
    unzip \
    rsync \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    icu-dev \
    shadow

# ─── PHP Extensions ───────────────────────────────────────────────────────────
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        gd \
        mbstring \
        exif \
        pcntl \
        bcmath \
        opcache \
        intl

# ─── PHP Config ───────────────────────────────────────────────────────────────
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/opcache.ini

# ─── Composer ─────────────────────────────────────────────────────────────────
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# ─── Working Directory ────────────────────────────────────────────────────────
WORKDIR /var/www/html

# ─── Entrypoint ───────────────────────────────────────────────────────────────
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
