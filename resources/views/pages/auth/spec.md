# Аутентификация и верификация

## Стек

- **Laravel Fortify** — регистрация, вход, сброс пароля, подтверждение пароля, двухфакторная аутентификация (TOTP).
- **Laravel Socialite** — OAuth через Yandex и VK (`/auth/yandex`, `/auth/vk`).
- Livewire-страницы в `resources/views/pages/auth/`.

## Обязательная верификация

Перед участием в хакатонах пользователь должен подтвердить:

- адрес электронной почты (`email_verified_at`);
- номер телефона (`phone_verified_at`) — flash-call через Plusofon.

Middleware `EnsureContactVerified` блокирует действия участника без верификации.

## Роли

| Роль | Guard | Доступ |
|------|-------|--------|
| `admin` | web | `/admin`, Horizon, Pulse |
| `partner` | web | организаторские маршруты (`organizer` middleware) |
| `judge` | web | судейские маршруты (`judge` middleware) |
| `user` | web | участник (команды, заявки, hub) |

## Маршруты Fortify

Настройки в `config/fortify.php` и `App\Providers\FortifyServiceProvider`.

## OAuth

Callback: `/auth/yandex/callback`, `/auth/vk/callback`. Redirect URI должен совпадать с `APP_URL` и настройками провайдера.

## 2FA

Включение и recovery codes — в профиле пользователя. При активной 2FA Fortify запрашивает код после пароля.
