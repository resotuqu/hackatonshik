# Редизайн — план и прогресс

Тег точки отсчёта: **v0.1** (commit `3b67f31`)
Последний выполненный этап: **Этап 2** (commit `80f4d03`)

---

## Концепция

Направление: **профессиональный, информативный, без декора**.

| Было (v0.1) | Стало |
|---|---|
| Dot-grid placeholder на обложках | Диагональная штриховка |
| Рамочный hero-блок (`ui-page-hero`) | Белая шапка страницы с хлебными крошками |
| Collapsible фильтры | Постоянный filter-bar под шапкой |
| Цветной urgency-блок с иконкой на карточке | Строка текста цвета ошибки/предупреждения |
| Секция ролей в цвете `secondary` (зелёный) | Нейтральный серый (`base-300`) |
| Рамка/фон вокруг лого в сайдбаре | Просто отступы |
| Split-кнопка входа по центру пустой страницы | Split-layout: тёмный бренд-блок + белая форма |

### Новые CSS-классы (добавлены в `resources/css/app.css`)

- `.ui-page-header` — белая шапка страницы с border-bottom, заменяет `.ui-page-hero` на каталогах
- `.ui-filter-bar` — горизонтальная строка фильтров под шапкой

### Новые props компонентов

- `<x-hackaton-cover :label="$stageLabel" />` — рендерит статус-бейдж поверх обложки

---

## Этапы

### ✅ Этап 1 — Фундамент (commit `80f4d03`)

| Файл | Изменение |
|---|---|
| `resources/css/app.css` | `.ui-cover-placeholder`: dot-grid → diagonal stripes |
| `resources/css/app.css` | Добавлены `.ui-page-header`, `.ui-filter-bar` |
| `resources/views/layouts/app.blade.php` | Убрана рамка/фон вокруг логотипа в сайдбаре |

---

### ✅ Этап 2 — Shared-компоненты (commit `80f4d03`)

| Файл | Изменение |
|---|---|
| `resources/views/components/hackaton-cover.blade.php` | Добавлен prop `$label` — pill-бейдж статуса поверх обложки |
| `resources/views/components/hackaton-card.blade.php` | Тело: 2×2 dl-сетка (призовой/приём заявок/старт/команды), статус на обложке, urgency как текст, кнопка `btn-neutral` |
| `resources/views/components/team-card.blade.php` | Роли: `secondary/20` → `base-300/70`; `style="min-height"` → `class="min-h-[...]"` |

---

### ⬜ Этап 3 — Публичный каталог

Файлы:

| Файл | Что менять |
|---|---|
| `resources/views/pages/home/index.blade.php` | Убрать hero-блок, новый визуальный hero (без orb-glow) |
| `resources/views/pages/home/index-skeleton.blade.php` | Обновить под новую структуру |
| `resources/views/pages/hackatons/index.blade.php` | `ui-page-hero` → `ui-page-header`; collapsible-фильтры → `ui-filter-bar`; вкладки статуса в шапке |
| `resources/views/pages/teams/index.blade.php` | `ui-page-hero` → `ui-page-header`; фильтры → `ui-filter-bar` |
| `resources/views/components/recommended-teams.blade.php` | Привести к новому стилю карточек |
| `resources/views/pages/about/index.blade.php` | `ui-page-hero` → `ui-page-header`, minor polish |

---

### ⬜ Этап 4 — Детальные страницы

Файлы:

| Файл | Что менять |
|---|---|
| `resources/views/pages/hackatons/show-inner.blade.php` | Двухколоночный layout: main (описание+детали) + aside (приз, CTA, участники) |
| `resources/views/pages/hackatons/show-skeleton.blade.php` | Обновить скелетон |
| `resources/views/pages/hackatons/partials/show/description.blade.php` | Привести к новому стилю |
| `resources/views/pages/hackatons/partials/show/announcements.blade.php` | Привести к новому стилю |
| `resources/views/pages/hackatons/partials/show/documents.blade.php` | Привести к новому стилю |
| `resources/views/pages/teams/show.blade.php` | Hero + tabs |
| `resources/views/pages/teams/show-skeleton.blade.php` | Обновить скелетон |
| `resources/views/pages/profile/public-show-inner.blade.php` | Профиль: двухколоночный layout |

---

### ⬜ Этап 5 — Авторизация

Файлы:

| Файл | Что менять |
|---|---|
| `resources/views/pages/auth/login.blade.php` | Split-layout: тёмная левая (бренд+статы) + белая правая (форма) |
| `resources/views/pages/auth/register.blade.php` | Аналогичный split-layout |
| `resources/views/pages/auth/forgot-password.blade.php` | Привести к общему стилю форм |
| `resources/views/pages/auth/reset-password.blade.php` | Привести к общему стилю форм |
| `resources/views/pages/auth/verify-email.blade.php` | Привести к общему стилю форм |

> Независим от этапов 3–4, можно делать параллельно.

---

### ⬜ Этап 6 — Кабинет участника

Файлы:

| Файл | Что менять |
|---|---|
| `resources/views/pages/profile/index.blade.php` | `ui-page-hero` → `ui-page-header`, profile-tabs под шапкой |
| `resources/views/components/profile-nav-tabs.blade.php` | Горизонтальная полоска вкладок |
| `resources/views/components/activity-timeline.blade.php` | Minor polish |
| `resources/views/pages/participant/hackatons/index.blade.php` | `ui-page-header` + `ui-filter-bar` |
| `resources/views/pages/profile/hackatons/hub-inner.blade.php` | Привести к стилю |
| `resources/views/pages/profile/hackatons/hub-body.blade.php` | Привести к стилю |
| `resources/views/pages/profile/teams/index.blade.php` | Привести к стилю |
| `resources/views/pages/profile/certificates/index.blade.php` | Привести к стилю |
| `resources/views/pages/profile/watches/index.blade.php` | Привести к стилю |

---

### ⬜ Этап 7 — Кабинет организатора

Файлы:

| Файл | Что менять |
|---|---|
| `resources/views/pages/organizer/dashboard.blade.php` | Метрики → горизонтальный ряд stat-плашек |
| `resources/views/components/hackatons/organizer-header-metrics.blade.php` | Привести к стилю |
| `resources/views/components/hackatons/organizer-lifecycle-bar.blade.php` | Привести к стилю |
| `resources/views/components/hackatons/organizer-readiness-checklist.blade.php` | Привести к стилю |
| `resources/views/components/hackatons/organizer-action-center.blade.php` | Привести к стилю |
| `resources/views/pages/hackatons/create.blade.php` | Двухколоночный layout полей формы |
| `resources/views/pages/hackatons/edit.blade.php` | Двухколоночный layout полей формы |
| `resources/views/pages/teams/create.blade.php` | Привести к стилю |
| `resources/views/pages/teams/edit.blade.php` | Привести к стилю |
| `resources/views/pages/profile/hackatons/applications.blade.php` | Привести к стилю |
| `resources/views/pages/profile/hackatons/scoring.blade.php` | Привести к стилю |
| `resources/views/pages/profile/hackatons/finished.blade.php` | Привести к стилю |

---

### ⬜ Этап 8 — Панель судьи

Файлы:

| Файл | Что менять |
|---|---|
| `resources/views/pages/judge/dashboard.blade.php` | `ui-page-header` |
| `resources/views/pages/judge/hackaton-show.blade.php` | `ui-page-header` |
| `resources/views/pages/judge/submission-list.blade.php` | Список работ — таблица вместо карточек |
| `resources/views/pages/judge/evaluate-submission.blade.php` | Двухколоночный layout: работа слева, форма оценки справа |
| `resources/views/pages/hackatons/results.blade.php` | Привести к стилю |
| `resources/views/pages/judges/accept-invitation.blade.php` | Привести к стилю |
| `resources/views/components/judge/⚡dashboard.blade.php` | Привести к стилю |
| `resources/views/components/judge/⚡submission-list.blade.php` | Привести к стилю |
| `resources/views/components/judge/⚡evaluate-submission.blade.php` | Привести к стилю |

---

### ⬜ Этап 9 — Вспомогательные страницы

Низкий трафик, минимальные изменения — только `ui-page-hero` → `ui-page-header`.

| Файл |
|---|
| `resources/views/pages/news/index.blade.php` |
| `resources/views/pages/news/show.blade.php` |
| `resources/views/pages/contacts/index.blade.php` |
| `resources/views/pages/privacy-policy/index.blade.php` |
| `resources/views/pages/cookie-policy/index.blade.php` |
| `resources/views/pages/templates/index.blade.php` |
| `resources/views/pages/templates/show.blade.php` |
| `resources/views/pages/admin/index.blade.php` |
| `resources/views/pages/admin/users.blade.php` |
| `resources/views/pages/admin/news.blade.php` |
| `resources/views/pages/admin/avatar-presets.blade.php` |

---

## Правила при реализации

1. **Не менять PHP-логику** — только Blade/CSS/Tailwind.
2. **`ui-page-hero` → `ui-page-header`** — в каталогах. На детальных страницах — двухколоночный layout.
3. **Фильтры** — всегда `ui-filter-bar` (постоянный, не сворачиваемый). Старую collapsible-панель убирать.
4. **Кнопки** — главная CTA в карточках: `btn-neutral`. Основная CTA на странице (`Подать заявку`): `btn-primary`.
5. **После каждого этапа** — `vendor/bin/pint --dirty`, `php artisan test --compact tests/Unit`, коммит.
6. **Обновлять этот файл** — ставить ✅ и добавлять хэш коммита после завершения этапа.
