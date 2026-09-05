# Development image for the Laravel API.
# Production is deployed separately via .github/workflows (SSH + composer + artisan
# on the target server), so this image is only meant for local development
# (see the devstack.tobias-hopp.de repo for the full local-stack docker-compose.yml).

FROM php:8.3-cli-alpine

RUN apk add --no-cache \
        git \
        curl-dev \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        bcmath \
        intl \
        zip \
        gd \
        exif \
        pcntl \
        curl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install dependencies first so this layer is cached unless composer.* changes.
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-interaction --prefer-dist --no-progress

# Copy the rest of the source (overridden by the bind mount in docker-compose.yml at runtime).
COPY . .
RUN chmod +x docker/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["sh", "docker/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
