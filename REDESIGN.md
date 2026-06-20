# Редизайн — план и прогресс

Тег точки отсчёта: **v0.1** (commit `3b67f31`)
Последний выполненный этап: **Этап 7** (commit `25e4502`)

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

### ✅ Этап 3 — Публичный каталог (commit `503809d`)

| Файл | Изменение |
|---|---|
| `resources/views/pages/home/index-skeleton.blade.php` | hero skeleton → `rounded border border-base-300 bg-base-100` (совпадает с реальным hero) |
| `resources/views/pages/hackatons/index.blade.php` | `ui-page-hero` → `ui-page-header`; статус-вкладки перенесены в шапку; collapsible убран, фильтры всегда видны; active-filter chips → нейтральные badges |
| `resources/views/pages/teams/index.blade.php` | `ui-page-hero` → `ui-page-header`; Открытые/Все-табы в шапке; `<details>` раскрыт в постоянные поля; chips → нейтральные |
| `resources/views/components/recommended-teams.blade.php` | `border-primary/20` → `border-base-300`; score badge → нейтральный; кнопка `btn-primary` → `btn-neutral` |
| `resources/views/pages/about/index.blade.php` | `ui-page-hero` → `ui-page-header` + `pb-5`; закрывающий блок → `border border-base-300 bg-base-100 p-6` |

---

### ✅ Этап 4 — Детальные страницы (commit `503809d`)

| Файл | Изменение |
|---|---|
| `resources/views/pages/hackatons/partials/show/description.blade.php` | `border-base-200 shadow-sm` → `border-base-300` на обеих карточках |
| `resources/views/pages/hackatons/partials/show/announcements.blade.php` | `border-base-200 shadow-sm` → `border-base-300` |
| `resources/views/pages/hackatons/partials/show/documents.blade.php` | `border-base-200 shadow-sm` → `border-base-300` |
| `resources/views/pages/teams/show.blade.php` | dot-grid radial-gradient fallback → `ui-cover-placeholder` + нейтральная иконка |
| `resources/views/pages/profile/public-show-inner.blade.php` | `ui-page-hero` → `ui-page-header`; однoколонка → `lg:grid-cols-3` (main: bio+skills+history; aside sticky: stats dl + contacts + teams + certs); `text-secondary` числа → `text-base-content`; avatar ring → `ring-base-300` |

---

### ✅ Этап 5 — Авторизация (commit `2b6ff2e`)

| Файл | Изменение |
|---|---|
| `resources/views/pages/auth/login.blade.php` | Светлая левая карточка → тёмный бренд-блок (`bg-base-content`) с заголовком, буллетами и stat-рядом (500+/2000+/120+); форма: `border-base-200 shadow-sm` → `border-base-300` |
| `resources/views/pages/auth/register.blade.php` | Аналогичный тёмный блок, переключает содержимое по `$accountType`; те же правки на форме |
| `resources/views/pages/auth/forgot-password.blade.php` | `card card-border` → `card border border-base-300` |
| `resources/views/pages/auth/reset-password.blade.php` | `card card-border` → `card border border-base-300` |
| `resources/views/pages/auth/verify-email.blade.php` | `card card-border` → `card border border-base-300` |

---

### ✅ Этап 6 — Кабинет участника (commit `f0cd171`)

| Файл | Изменение |
|---|---|
| `resources/views/pages/profile/index.blade.php` | `ui-page-hero` → `ui-page-header`; `ring-secondary` → `ring-base-300`; `badge-primary` → `badge-neutral`; `text-secondary` цифры → `text-base-content`; `progress-secondary` → `progress`; tips-карточка `border-secondary/20 bg-secondary/5` → `border-base-300 bg-base-100` |
| `resources/views/components/profile-nav-tabs.blade.php` | `tabs-boxed` → `tabs-bordered` |
| `resources/views/components/activity-timeline.blade.php` | `border-primary/25` → `border-base-300` |
| `resources/views/pages/participant/hackatons/index.blade.php` | `<header>` → `ui-page-header`; next-step блок `border-primary/20 bg-primary/10` → нейтральный; `card-border shadow-sm` → `border border-base-300`; `border-base-200` → `border-base-300` в списках |
| `resources/views/pages/profile/hackatons/hub-inner.blade.php` | `border-base-200` → `border-base-300` на всех article-карточках |
| `resources/views/pages/profile/hackatons/hub-body.blade.php` | `card card-border` → `card border border-base-300 bg-base-100` |
| `resources/views/pages/profile/teams/index.blade.php` | Gradient-hero → `ui-page-header`; `text-primary/text-secondary` числа → `text-base-content`; `btn-primary` → `btn-neutral` |
| `resources/views/pages/profile/certificates/index.blade.php` | Добавлены `x-profile-nav-tabs` и `ui-page-header`; `card-border` → `border border-base-300`; `btn-primary` → `btn-neutral` |
| `resources/views/pages/profile/watches/index.blade.php` | `<header>` → `ui-page-header` |

---

### ✅ Этап 7 — Кабинет организатора (commit `25e4502`)

| Файл | Изменение |
|---|---|
| `resources/views/components/hackatons/organizer-header-metrics.blade.php` | `border-base-300/50 bg-base-100/80` → `border-base-300 bg-base-100` |
| `resources/views/components/hackatons/organizer-lifecycle-bar.blade.php` | `border-base-300/60` → `border-base-300` |
| `resources/views/components/hackatons/organizer-readiness-checklist.blade.php` | `border-base-300/60 bg-base-200/30` → `border-base-300 bg-base-100` |
| `resources/views/components/hackatons/organizer-action-center.blade.php` | `border-primary/20 bg-primary/5` → `border-base-300 bg-base-100`; `text-primary/80` → `text-base-content/55` |
| `resources/views/pages/hackatons/create.blade.php` | `card-border` → `border border-base-300`; `badge-primary` → `badge-neutral`; `progress-primary` → `progress`; `checkbox-primary` → `checkbox`; info-блок `border-info/25 bg-info/10` → нейтральный |
| `resources/views/pages/hackatons/edit.blade.php` | `card-border` → `border border-base-300`; `progress-primary` → `progress`; `checkbox-primary` → `checkbox`; `btn-primary` добавить документ → `btn-neutral` |
| `resources/views/pages/teams/create.blade.php` | `card-border border-base-200/80 shadow-sm` → `border border-base-300`; `progress-primary` → `progress`; `border-base-200` → `border-base-300` |
| `resources/views/pages/teams/edit.blade.php` | Нейтральный круг инициалов; `text-secondary/text-primary` → `text-base-content`; `bg-primary` прогресс → `bg-base-content/40`; иконки `text-primary` → `text-base-content/60`; убраны `shadow-sm` |
| `resources/views/pages/profile/hackatons/applications.blade.php` | `<header>` → `ui-page-header` |
| `resources/views/pages/profile/hackatons/scoring.blade.php` | `<header>` → `ui-page-header`; убран `shadow-sm` на таблице |
| `resources/views/pages/profile/hackatons/finished.blade.php` | `<header>` → `ui-page-header` |

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
