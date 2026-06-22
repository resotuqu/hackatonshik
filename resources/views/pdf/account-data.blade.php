<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Данные аккаунта</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #fff;
            line-height: 1.5;
        }

        .page { padding: 32px 36px; }

        /* Header */
        .header {
            border-bottom: 3px solid #6366f1;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #6366f1;
            margin-bottom: 2px;
        }
        .header .subtitle {
            font-size: 10px;
            color: #6b7280;
        }
        .header .meta {
            text-align: right;
            font-size: 9px;
            color: #9ca3af;
        }

        /* Legal note */
        .legal-note {
            background: #f0f0ff;
            border: 1px solid #c7d2fe;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 24px;
            font-size: 9.5px;
            color: #4338ca;
        }

        /* Section */
        .section {
            margin-bottom: 22px;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #4f46e5;
            border-bottom: 1px solid #e0e7ff;
            padding-bottom: 5px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 6px 8px;
            vertical-align: top;
        }
        table tr:nth-child(odd) td {
            background: #f8f9ff;
        }
        .label {
            width: 40%;
            color: #6b7280;
            font-weight: 600;
            font-size: 10px;
        }
        .value {
            color: #111827;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
        }
        .badge-yes { background: #d1fae5; color: #065f46; }
        .badge-no  { background: #fee2e2; color: #991b1b; }
        .badge-role { background: #e0e7ff; color: #3730a3; }

        /* Tag list */
        .tags { display: flex; flex-wrap: wrap; gap: 4px; }
        .tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            background: #e0e7ff;
            color: #3730a3;
            font-size: 9px;
        }

        /* Hackaton list */
        .hackaton-item {
            padding: 7px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            margin-bottom: 6px;
        }
        .hackaton-name { font-weight: 700; font-size: 10.5px; color: #1f2937; }
        .hackaton-meta { font-size: 9px; color: #6b7280; margin-top: 2px; }

        /* Footer */
        .footer {
            border-top: 1px solid #e5e7eb;
            margin-top: 30px;
            padding-top: 12px;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-top">
            <div>
                <h1>Хакатонщик</h1>
                <div class="subtitle">Выгрузка персональных данных</div>
            </div>
            <div class="meta">
                Сформировано: {{ $generatedAt->format('d.m.Y H:i') }}<br>
                Документ предоставлен в соответствии с ФЗ-152
            </div>
        </div>
    </div>

    {{-- Legal note --}}
    <div class="legal-note">
        Настоящий документ содержит все персональные данные, которые хранятся о вас в системе «Хакатонщик».
        Предоставляется в соответствии со ст.&nbsp;14 Федерального закона от 27.07.2006 №&nbsp;152-ФЗ «О персональных данных».
        Оператор: ИП / ООО Хакатонщик, контакт: sekhmych@yandex.ru
    </div>

    {{-- Personal data --}}
    <div class="section">
        <div class="section-title">Личные данные</div>
        <table>
            <tr>
                <td class="label">Полное имя (ФИО)</td>
                <td class="value">{{ $user->fio ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Дата рождения</td>
                <td class="value">{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d.m.Y') : '—' }}</td>
            </tr>
            <tr>
                <td class="label">Псевдоним</td>
                <td class="value">{{ $user->nickname }}</td>
            </tr>
            <tr>
                <td class="label">Роль</td>
                <td class="value"><span class="badge badge-role">{{ $user->role->value }}</span></td>
            </tr>
            <tr>
                <td class="label">О себе</td>
                <td class="value">{{ $user->description ?: '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- Contact data --}}
    <div class="section">
        <div class="section-title">Контактные данные</div>
        <table>
            <tr>
                <td class="label">Электронная почта</td>
                <td class="value">{{ $user->email }}</td>
            </tr>
            <tr>
                <td class="label">Почта подтверждена</td>
                <td class="value">
                    @if ($user->email_verified_at)
                        <span class="badge badge-yes">Да — {{ $user->email_verified_at->format('d.m.Y') }}</span>
                    @else
                        <span class="badge badge-no">Нет</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Телефон</td>
                <td class="value">{{ $user->phone ? '+'.ltrim($user->phone, '+') : '—' }}</td>
            </tr>
            <tr>
                <td class="label">Телефон подтверждён</td>
                <td class="value">
                    @if ($user->phone_verified_at)
                        <span class="badge badge-yes">Да — {{ $user->phone_verified_at->format('d.m.Y') }}</span>
                    @else
                        <span class="badge badge-no">Нет</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Account metadata --}}
    <div class="section">
        <div class="section-title">Сведения об аккаунте</div>
        <table>
            <tr>
                <td class="label">Дата регистрации</td>
                <td class="value">{{ $user->created_at->format('d.m.Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Согласие на обработку ПДн</td>
                <td class="value">
                    @if ($user->pd_consent_accepted_at)
                        <span class="badge badge-yes">Получено — {{ $user->pd_consent_accepted_at->format('d.m.Y H:i') }}</span>
                    @else
                        <span class="badge badge-no">Не зафиксировано</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Двухфакторная аутентификация</td>
                <td class="value">
                    @if ($user->two_factor_confirmed_at)
                        <span class="badge badge-yes">Включена с {{ $user->two_factor_confirmed_at->format('d.m.Y') }}</span>
                    @else
                        <span class="badge badge-no">Выключена</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Язык интерфейса</td>
                <td class="value">{{ strtoupper($user->locale ?? 'ru') }}</td>
            </tr>
        </table>
    </div>

    {{-- Privacy settings --}}
    <div class="section">
        <div class="section-title">Настройки приватности</div>
        <table>
            <tr>
                <td class="label">Профиль публичный</td>
                <td class="value"><span class="badge {{ $user->is_profile_public ? 'badge-yes' : 'badge-no' }}">{{ $user->is_profile_public ? 'Да' : 'Нет' }}</span></td>
            </tr>
            <tr>
                <td class="label">Email виден на профиле</td>
                <td class="value"><span class="badge {{ $user->show_email_on_profile ? 'badge-yes' : 'badge-no' }}">{{ $user->show_email_on_profile ? 'Да' : 'Нет' }}</span></td>
            </tr>
            <tr>
                <td class="label">Телефон виден на профиле</td>
                <td class="value"><span class="badge {{ $user->show_phone_on_profile ? 'badge-yes' : 'badge-no' }}">{{ $user->show_phone_on_profile ? 'Да' : 'Нет' }}</span></td>
            </tr>
            <tr>
                <td class="label">Открыт для приглашений в команды</td>
                <td class="value"><span class="badge {{ $user->open_to_teams ? 'badge-yes' : 'badge-no' }}">{{ $user->open_to_teams ? 'Да' : 'Нет' }}</span></td>
            </tr>
            <tr>
                <td class="label">Навыки видны на профиле</td>
                <td class="value"><span class="badge {{ $user->show_skills_on_profile ? 'badge-yes' : 'badge-no' }}">{{ $user->show_skills_on_profile ? 'Да' : 'Нет' }}</span></td>
            </tr>
        </table>
    </div>

    {{-- Skills --}}
    @if ($user->skills->isNotEmpty())
        <div class="section">
            <div class="section-title">Навыки ({{ $user->skills->count() }})</div>
            <div class="tags">
                @foreach ($user->skills as $skill)
                    <span class="tag">{{ $skill->name }}</span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Hackaton participation --}}
    @php
        $acceptedApplications = $user->teamApplications->filter(fn($a) => $a->teamRole?->team?->hackaton !== null);
        $uniqueHackatons = $acceptedApplications->map(fn($a) => $a->teamRole->team->hackaton)->unique('id');
    @endphp

    @if ($uniqueHackatons->isNotEmpty())
        <div class="section">
            <div class="section-title">Участие в хакатонах ({{ $uniqueHackatons->count() }})</div>
            @foreach ($uniqueHackatons as $hackaton)
                @php
                    $app = $acceptedApplications->first(fn($a) => $a->teamRole->team->hackaton_id === $hackaton->id);
                    $team = $app?->teamRole?->team;
                @endphp
                <div class="hackaton-item">
                    <div class="hackaton-name">{{ $hackaton->title ?? 'Без названия' }}</div>
                    <div class="hackaton-meta">
                        Команда: {{ $team?->title ?? '—' }}
                        @if ($hackaton->starts_at)
                            · {{ \Carbon\Carbon::parse($hackaton->starts_at)->format('d.m.Y') }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Certificates --}}
    @if ($user->certificates->isNotEmpty())
        <div class="section">
            <div class="section-title">Сертификаты ({{ $user->certificates->count() }})</div>
            @foreach ($user->certificates as $cert)
                <div class="hackaton-item">
                    <div class="hackaton-name">{{ $cert->hackaton?->title ?? 'Без названия' }}</div>
                    <div class="hackaton-meta">Выдан: {{ $cert->created_at?->format('d.m.Y') ?? '—' }}</div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Документ сформирован автоматически платформой «Хакатонщик» {{ $generatedAt->format('d.m.Y') }} в {{ $generatedAt->format('H:i') }} (UTC+{{ $generatedAt->format('P') }}).
        По вопросам обработки данных обращайтесь: sekhmych@yandex.ru
    </div>

</div>
</body>
</html>
