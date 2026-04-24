# ====================== COMPOSER STAGE ======================
FROM composer:latest AS composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist \
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
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

COPY --from=composer /app/vendor ./vendor
COPY --from=frontend /app/public/build/ ./public/build/
COPY . .

# Права для Laravel
RUN chown -R nobody:nobody /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Конфиги
COPY . /etc/nginx/http.d/default.conf
COPY . /etc/php85/conf.d/99-laravel.ini

# OPcache
RUN echo -e "opcache.enable=1\n\
    opcache.memory_consumption=128\n\
    opcache.max_accelerated_files=4000\n\
    opcache.revalidate_freq=60" >> /etc/php85/conf.d/99-laravel.ini

# Возвращаемся к non-root пользователю (рекомендуется)
USER nobody

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]