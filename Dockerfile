# ====================== COMPOSER STAGE ======================
FROM composer:latest AS composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist --ignore-platform-reqs \
    && rm -rf /root/.composer/cache


# ====================== FRONTEND STAGE (поддерживает pnpm / yarn / npm) ======================
FROM node:22-alpine AS frontend

WORKDIR /app

# Копируем только файлы зависимостей для отличного кэширования слоёв
COPY package*.json pnpm-lock.yaml* yarn.lock* ./

# Автоматически определяем, какой менеджер пакетов используется
RUN if [ -f "pnpm-lock.yaml" ]; then \
    corepack enable && \
    corepack prepare pnpm@latest --activate && \
    pnpm install --frozen-lockfile; \
    elif [ -f "yarn.lock" ]; then \
    yarn install --frozen-lockfile; \
    else \
    npm ci; \
    fi

# Копируем весь код
COPY . .

# Запускаем сборку фронтенда (Vite / Laravel)
RUN if [ -f "pnpm-lock.yaml" ]; then \
    pnpm run build; \
    elif [ -f "yarn.lock" ]; then \
    yarn build; \
    else \
    npm run build; \
    fi || echo "No build script found or already built"

# Гарантируем, что папка build существует
RUN mkdir -p public/build
# ====================== PRODUCTION STAGE ======================
FROM trafex/php-nginx:latest

# ←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←
USER root
# ←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←

# Устанавливаем только то, чего нет в trafex
RUN apk add --no-cache \
    libpng \
    libzip \
    icu-libs \
    postgresql-libs \
    php85-pecl-redis \
    php85-pdo \
    php85-pdo_sqlite \
    php85-pdo_pgsql \
    php85-pcntl \
    php85-posix \
    && rm -rf /var/cache/apk/*





WORKDIR /var/www/html

COPY --from=composer /app/vendor ./vendor
COPY --from=frontend /app/public/build/ ./public/build/
COPY . .

# Права для Laravel
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs \
    && chown -R nobody:nobody /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache


# Конфиги
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN mkdir -p /var/log/supervisor && chown -R nobody:nobody /var/log/supervisor
RUN touch /etc/php85/conf.d/99-laravel.ini


USER root
RUN php vendor/bin/rr get-binary --location /usr/local/bin \
    && chmod +x /usr/local/bin/rr


# OPcache
RUN echo -e "opcache.enable=1\n\
    opcache.memory_consumption=128\n\
    opcache.max_accelerated_files=4000\n\
    opcache.revalidate_freq=60" >> /etc/php85/conf.d/99-laravel.ini

# Возвращаемся к non-root пользователю (рекомендуется)
USER nobody

EXPOSE 8000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]