# Хакатонщик

Платформа на Laravel для организации хакатонов, команд, заявок, кейсов, скоринга и сертификатов.

## Стек

- PHP 8.4+, [Laravel 12](https://laravel.com)
- [Livewire 4](https://livewire.laravel.com) (страницы в `resources/views/pages/**/⚡*.blade.php`)
- [Mary UI](https://mary-ui.com/) и DaisyUI
- [Fortify](https://laravel.com/docs/fortify) — аутентификация
- [Laravel Socialite](https://laravel.com/docs/socialite) + community providers — вход через Yandex и VK

## Требования

- PHP с расширениями из `composer.json`
- Node.js 22+ (как в CI)
- Composer 2

## Установка

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

### Прокси (production)

- `TRUSTED_PROXIES` — список IP через запятую, или `*` только если осознанно доверяете всем прокси.
- В `local` и `testing` доверие к прокси шире по умолчанию (см. `bootstrap/app.php`).

### PHPStan

Кэш анализатора: `storage/framework/phpstan` (не коммитится). Запуск:

```bash
./vendor/bin/phpstan analyse --memory-limit=1G
```

## Тесты

```bash
./vendor/bin/pest
```

Используется Pest 4 с `RefreshDatabase` для фич-тестов.

## CI

GitHub Actions: [.github/workflows/tests.yml](.github/workflows/tests.yml) — матрица PHP 8.4/8.5, `composer install`, `npm run build`, `./vendor/bin/pest`.

Для приватных пакетов (например Flux) в CI настроены `secrets` для Composer — см. шаг `composer config http-basic.composer.fluxui.dev` в workflow.

## Структура UI

- Публичные страницы хакатона и команды: Livewire full-page с общим layout (`layouts::app`), контент вынесен в `*-inner.blade.php` для переиспользования.
- Тонкие HTTP-эндпоинты остаются на контроллерах: OAuth callback, RSS, экспорты CSV/ZIP, скачивание сертификатов.

## API

Публичный каталог: префикс `/api/v1` с middleware `throttle:api` (лимиты в `AppServiceProvider::configureRateLimiting()`).

## Лицензия

Проект внутренний; уточните лицензию у владельцев репозитория.
