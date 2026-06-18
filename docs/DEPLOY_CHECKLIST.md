# Чеклист релиза «Хакатонщик»

Краткий внутренний список перед выкладкой на production (VPS: PHP-FPM + Nginx).

## Окружение

- `APP_ENV=production`, `APP_DEBUG=false`, корректный `APP_URL` (совпадает с публичным URL для signed-ссылок и почты).
- `APP_KEY` задан; секреты только в env, не в репозитории.
- `TRUSTED_PROXIES`: реальные IP/CIDR прокси за балансировщиком (см. README).
- `TELESCOPE_ENABLED=false` (Telescope — dev-only пакет).
- Redis: `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`.
- Real-time: `BROADCAST_CONNECTION=reverb`, все `REVERB_*` и `VITE_REVERB_*` заданы до `npm run build`.
- `LOG_LEVEL=info`, `LOG_STACK` включает `telegram` при необходимости алертов.
- База: **только** `php artisan migrate --force`; **не** запускать `db:seed` в production (demo-пользователи с паролем `password`).
- Перед `config:cache`: `php artisan app:validate-production-config`.
- После деплоя: `php artisan config:cache`, `route:cache`, `view:cache` по политике команды.

## Сервер (PHP-FPM + Nginx)

- PHP 8.5 + расширения: redis, pdo_pgsql (или pdo_mysql), intl, mbstring, zip, gd.
- Nginx: `root` → `public/`, `try_files $uri $uri/ /index.php?$query_string`.
- WebSocket для Reverb: см. [`deploy/nginx/reverb-websocket.conf.example`](../deploy/nginx/reverb-websocket.conf.example) (`location /app` → `127.0.0.1:8080`).
- Supervisor: [`deploy/supervisor/horizon.conf`](../deploy/supervisor/horizon.conf), [`deploy/supervisor/reverb.conf`](../deploy/supervisor/reverb.conf), [`deploy/supervisor/pulse.conf`](../deploy/supervisor/pulse.conf).
- Cron: `* * * * * cd /var/www/hackatonshik && php artisan schedule:run >> /dev/null 2>&1`.
- `php artisan storage:link`; права `storage/` и `bootstrap/cache/` для пользователя PHP-FPM.
- `composer install --no-dev --optimize-autoloader`.

## Почта и очереди

- `MAIL_*` указывает на рабочий транспорт; тестовое письмо (верификация / приглашение судьи) доходит.
- Horizon запущен (`php artisan horizon` через supervisor); не использовать `queue:work` вручную.
- Reverb запущен (`php artisan reverb:start` через supervisor).

## OAuth (Yandex / VK)

- Redirect URI в кабинетах провайдеров совпадают с `APP_URL` и маршрутами `/auth/yandex/callback`, `/auth/vk/callback`.
- После смены домена: `php artisan config:clear`, проверка входа на staging.

## Сборка и артефакты

- `npm run build` (с `VITE_REVERB_*` из `.env`); в репозитории нет `public/test.html` и прочих локальных артефактов (CI шаг в `.github/workflows/tests.yml`).
- `public/storage` или облачный диск для загрузок по необходимости.

## Smoke-тест после деплоя

- `/up` возвращает 200 (Redis + queue проверяются).
- Вход, верификация email/телефона.
- Real-time чат команды: два браузера в одной команде видят сообщения без refresh.
- Horizon dashboard (`/horizon`) и Pulse (`/pulse`) доступны только admin.

## Demo smoke (local / staging с demo-seed)

Ручная проверка перед защитой или демо на стенде с `php artisan migrate:fresh --seed`:

| Роль | Действие | URL / учётная запись |
|------|----------|----------------------|
| **Guest** | Каталог, RSS, публичный профиль | `/hackatons`, `/news/rss`, `/u/admin_demo` |
| **Участник** | Команда → hub → submission | `organizer1@…` не нужен; любой `user` из сидов, `/teams/create`, `/participant/hackatons/{id}/hub` |
| **Организатор** | Создание из шаблона, заявки, scoring | `organizer1@demo.hackaton.local` / `password`, `/hackatons/create` |
| **Судья** | Приглашение → scoring | `judge1@demo.hackaton.local` / `password` |
| **Admin** | Пользователи, новости | `admin@demo.hackaton.local` / `password`, `/admin` |

**Эталонный demo-хакатон:** после сида «SmartOmega GameJab 2026» закреплён в статусе `finished` (`DemoShowcaseSeeder`) — полный цикл: команды, кейсы, submissions, judging, сертификаты.

Автотесты smoke-сценариев: `composer dusk` (9 browser-тестов, включая hub и create wizard).

## Staging перед production

1. Скопировать production `.env` на staging; `APP_DEBUG=false`, отдельная БД.
2. `composer install --no-dev`, `npm run build`, `php artisan migrate --force`.
3. `php artisan app:validate-production-config` (при `APP_ENV=production`).
4. Поднять Horizon, Reverb, Pulse через supervisor (см. `deploy/supervisor/`).
5. Пройти smoke-тест выше + OAuth на staging-домене.
6. Manual `workflow_dispatch` deploy workflow на staging, затем production.

## Мониторинг

- Health: маршрут `/up` доступен с балансировщика.
- Pulse: `PULSE_ENABLED=true`, supervisor [`deploy/supervisor/pulse.conf`](../deploy/supervisor/pulse.conf), cron выполняет `pulse:check`.
- Логи (`storage/logs`, stderr) и резервное копирование БД по регламенту команды.
- Telegram-алерты на `critical` (если `TELEGRAM_BOT_TOKEN` и `TELEGRAM_CHAT_ID` заданы).

## Runbook: релиз и откат

- GitHub deploy workflow: [`.github/workflows/deploy.yml`](../.github/workflows/deploy.yml) (manual `workflow_dispatch`, `environment: Production`).
- Перед деплоем: убедитесь, что `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_KEY` заданы в secrets.
- Пайплайн выполняет: `app:validate-production-config` → `migrate --force` → cache warmup → `/up` smoke.
- При ошибке включён авто-rollback на предыдущий commit с повторной установкой prod-зависимостей и прогревом кешей.
- После релиза: проверить `/horizon`, `/pulse`, критичные пользовательские сценарии входа и подачи заявок.
- Инцидент-порядок: зафиксировать timestamp/commit, сохранить фрагмент логов, выполнить rollback, затем RCA в отдельном issue.

## Accepted risks

- CSP разрешает `unsafe-inline` и `unsafe-eval` для Livewire/Alpine (см. [`app/Http/Middleware/SecurityHeaders.php`](../app/Http/Middleware/SecurityHeaders.php)). Ужесточение — отдельный epic (nonce/CSP hash).

## Опционально (не для основного VPS-деплоя)

- OpenAPI: [`docs/api/openapi.yaml`](../docs/api/openapi.yaml).
- Docker/Octane: [`Dockerfile`](../Dockerfile), [`supervisord.conf`](../supervisord.conf) — альтернативный путь, не обязателен при PHP-FPM.
