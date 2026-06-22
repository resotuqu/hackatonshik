# Хакатонщик

Платформа на Laravel для организации хакатонов, команд, заявок, кейсов, скоринга и сертификатов.

## Стек

- PHP 8.2+, [Laravel 12](https://laravel.com)
- [Livewire 4](https://livewire.laravel.com) (страницы в `resources/views/pages/**/*.blade.php`, компоненты с ⚡ в `resources/views/components/`)
- [Mary UI](https://mary-ui.com/) и DaisyUI
- [Fortify](https://laravel.com/docs/fortify) — аутентификация
- [Laravel Socialite](https://laravel.com/docs/socialite) + community providers — вход через Yandex и VK

## Требования

- PHP с расширениями из `composer.json`
- Node.js 22+ (как в CI)
- Composer 2

### Локальная разработка через Laravel Sail (Docker)

Альтернатива Herd: полный стек в Docker (PostgreSQL, Redis, Mailpit, Reverb).

**Требования:** [Docker Desktop](https://www.docker.com/products/docker-desktop/) (на Windows — WSL2).

```bash
composer sail:setup   # копирует .env.sail → .env, поднимает контейнеры, migrate --seed, npm build
composer sail:up -d   # поднять контейнеры (если ещё не запущены)
composer sail:dev     # horizon + reverb + vite (через docker compose exec)
```

Приложение: `http://localhost`  
Почта (Mailpit): `http://localhost:8025`  
Vite HMR: порт `5173`

Полезные команды:

```bash
composer sail:up
composer sail:dev
docker compose exec laravel.test php artisan test --compact
docker compose exec laravel.test php artisan migrate:fresh --seed
docker compose exec laravel.test bash
```

На Windows в PowerShell не используйте `./vendor/bin/sail` — он требует bash. Все команды выше работают через `docker compose`.

Переключение окружений:

- **Sail:** `cp .env.sail .env` (или `composer sail:setup`)
- **Herd / без Docker:** свой `.env` с SQLite и `https://*.test`

OAuth: на `http://localhost` виджет Яндекс ID недоступен (нужен HTTPS) — работает server-side redirect (`/auth/yandex/redirect`). VK OAuth проверяйте с redirect URI `http://localhost/auth/vk/callback`.

Production-образ (Octane): `docker compose -f compose.prod.yaml up --build`

`compose.prod.yaml` поднимает `app` (Octane), `horizon`, `reverb`, `redis` и `pgsql`. Для внешнего managed PostgreSQL/Redis замените сервисы и переменные `DB_*` / `REDIS_*` в `.env`.

## Production runbook (кратко)

1. `php artisan migrate --force`
2. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
3. Запустить Horizon (`php artisan horizon`) и Reverb (`php artisan reverb:start`) — или контейнеры `horizon` / `reverb` из `compose.prod.yaml`
4. Проверить `/up` (health) и очереди в Horizon
5. Убедиться, что `APP_URL` HTTPS и OAuth redirect URI совпадают с production-доменом

Подробнее: [docs/DEPLOY_CHECKLIST.md](docs/DEPLOY_CHECKLIST.md).

## Установка (без Docker)

```bash
cp .env.example .env
php artisan key:generate
composer install
npm install
php artisan migrate
npm run build
```

Для локальной разработки вместо `npm run build` можно использовать `npm run dev`.

### База данных

По умолчанию в `.env.example` указан SQLite (`DB_CONNECTION=sqlite`). Создайте файл `database/database.sqlite` при необходимости или переключитесь на MySQL/PostgreSQL.

### OAuth (Yandex / VK)

Заполните в `.env`:

- `YANDEX_CLIENT_ID`, `YANDEX_CLIENT_SECRET`, `YANDEX_REDIRECT_URI`
- `VK_CLIENT_ID`, `VK_CLIENT_SECRET`, `VK_REDIRECT_URI`

Redirect URI должен совпадать с настройками приложения у провайдера и с маршрутами `/auth/yandex/callback` и `/auth/vk/callback`.

**Чеклист staging (OAuth):** `APP_URL` на стенде совпадает с базой callback URL; в кабинетах Yandex/VK добавлены те же redirect URI; для проверки используйте тестовые приложения провайдера; после смены `APP_URL` пересоберите конфиг (`php artisan config:clear`).

### Прокси (production)

- `TRUSTED_PROXIES` — список IP или подсетей через запятую, или `*` только если осознанно доверяете всем прокси.
- За балансировщиком или ingress с TLS укажите реальные адреса прокси; иначе `X-Forwarded-*` не учитываются, и клиентский IP в логах и rate limiting может быть неверным.
- В `local` и `testing` доверие к прокси шире по умолчанию (см. `bootstrap/app.php`).

### PHPStan

Кэш анализатора: `storage/framework/phpstan` (не коммитится). Запуск:

```bash
./vendor/bin/phpstan analyse --memory-limit=1G
```

## Тесты

```bash
php artisan test --compact
```

Используется Pest 4 с `RefreshDatabase` для фич-тестов. End-to-end в браузере: PHPUnit + [Laravel Dusk](https://laravel.com/docs/dusk) в `tests/Browser`, запуск `composer dusk` (скопируйте `.env.dusk.example` → `.env.dusk.local`, нужны Chrome/ChromeDriver и `database/dusk.sqlite`).

### Восстановление тестового окружения

Если локально возникли проблемы с тестовой БД или кешами, используйте:

```bash
composer test:rebuild
```

Перед PR рекомендуется единый pre-check:

```bash
composer lint:check && composer analyse && php artisan test --compact
```

Быстрый pre-commit контур (локально, перед каждым commit):

```bash
composer precommit:quick
```

Browser-тесты (Laravel Dusk) запускаются отдельно — нужен поднятый сервер на `APP_URL` из `.env.dusk.local`:

```bash
php artisan serve --no-reload --env=dusk --port=8000
composer dusk
```

Unit и Feature-тесты не включают Browser suite (см. `phpunit.xml`); в CI они идут отдельными job'ами.

## Demo-данные (local/testing)

```bash
php artisan migrate:fresh --seed
```

Сидеры доступны только в `local` и `testing` (в production `db:seed` пропускается).

| Роль | Email | Пароль |
|------|-------|--------|
| Admin | `admin@demo.hackaton.local` | `password` |
| Организатор | `organizer1@demo.hackaton.local` | `password` |
| Судья | `judge1@demo.hackaton.local` | `password` |

После сида доступны шаблоны хакатонов (GameJam, AI Hack и др.) в мастере создания и публичные хакатоны из `placeholders/` в разных статусах. **SmartOmega GameJab 2026** закрепляется в статусе `finished` через `DemoShowcaseSeeder` — рекомендуется для live-demo (команды, кейсы, submissions, judging, сертификаты).

## CI

GitHub Actions: [.github/workflows/tests.yml](.github/workflows/tests.yml) — матрица PHP 8.4/8.5, `composer install`, `npm run build`, `./vendor/bin/pest` (suites `Unit` и `Feature` с покрытием), затем Laravel Dusk (`php artisan dusk`) против поднятого `php artisan serve`. Перед merge CI отклоняет коммит с файлом `public/test.html` в дереве.

Внутренний чеклист релиза: [docs/DEPLOY_CHECKLIST.md](docs/DEPLOY_CHECKLIST.md).

Форки и публичные клоны: приватные Composer-пакеты в `composer.json` не используются — отдельные HTTP-basic секреты для CI не требуются.

## Структура UI

- Публичные страницы хакатона и команды: Livewire full-page с общим layout (`layouts::app`), контент вынесен в `*-inner.blade.php` для переиспользования.
- Тонкие HTTP-эндпоинты остаются на контроллерах: OAuth callback, RSS, экспорты CSV/ZIP, скачивание сертификатов.

## API

Публичный каталог: префикс `/api/v1` с middleware `throttle:api` (лимиты в `AppServiceProvider::configureRateLimiting()`). Ответы пагинации в формате `data` / `links` / `meta`. Для списка хакатонов доступен фильтр `?upcoming=1` (старт с сегодняшнего дня и позже).

## Лицензия

Проект внутренний; уточните лицензию у владельцев репозитория.
