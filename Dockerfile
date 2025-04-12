FROM php:8.4-cli-bullseye

WORKDIR /app

ENV APP_BASE_PATH=/app

RUN apt-get update && apt-get install -y libxml2-dev linux-headers-generic libsodium-dev libpcre3-dev libicu-dev libgmp-dev libzip-dev \
    curl gnupg && \
    # Add Node.js repository
    mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" > /etc/apt/sources.list.d/nodesource.list && \
    apt-get update && \
    apt-get install -y nodejs && \
    docker-php-ext-install -j$(nproc) pcntl xml sockets sodium pdo pdo_mysql intl gmp zip

COPY . .
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY php-ini-overrides.ini /usr/local/etc/php/conf.d/php-ini-overrides.ini

RUN composer install --no-dev --optimize-autoloader --no-interaction && \
    php vendor/bin/rr get-binary && \
    npm install && \
    npm run build && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

EXPOSE 8000

CMD ["sh", "entrypoint.sh"]
