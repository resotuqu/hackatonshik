FROM composer:latest AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist --ignore-platform-reqs \
    && rm -rf /root/.composer/cache

FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json pnpm-lock.yaml* yarn.lock* ./
RUN if [ -f "pnpm-lock.yaml" ]; then corepack enable && pnpm install --frozen-lockfile; elif [ -f "yarn.lock" ]; then yarn install --frozen-lockfile; else npm ci; fi
COPY . .
RUN if [ -f "pnpm-lock.yaml" ]; then pnpm run build; elif [ -f "yarn.lock" ]; then yarn build; else npm run build; fi || true
RUN mkdir -p public/build

FROM trafex/php-nginx:latest
USER root
RUN apk add --no-cache libpng libzip icu-libs postgresql-libs php85-pecl-redis php85-pdo php85-pdo_sqlite php85-pdo_pgsql php85-pcntl php85-posix tar gzip \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html
COPY --from=composer /app/vendor ./vendor
COPY --from=frontend /app/public/build/ ./public/build/
COPY . .

RUN mkdir -p storage/{app/{private,public},framework/{cache/data,sessions,testing,views},logs,fonts} bootstrap/cache \
    && chown -R nobody:nobody /var/www/html \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN mkdir -p /var/log/supervisor && chown -R nobody:nobody /var/log/supervisor

RUN echo -e "opcache.enable=1\nopcache.memory_consumption=128\nopcache.max_accelerated_files=4000\nopcache.revalidate_freq=60" > /etc/php85/conf.d/99-laravel.ini

RUN mkdir -p /tmp/.config/psysh && chmod -R 777 /tmp/.config

USER nobody
ENV HOME=/tmp
EXPOSE 8000 8080
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
