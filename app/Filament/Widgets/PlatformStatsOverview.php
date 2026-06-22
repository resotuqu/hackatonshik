<?php

namespace App\Filament\Widgets;

use App\Enums\ReportStatus;
use App\Models\ContactMessage;
use App\Models\Hackaton;
use App\Models\Report;
use App\Models\Team;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pendingReports = Report::query()->where('status', ReportStatus::Pending)->count();
        $recentContacts = ContactMessage::query()->where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Пользователи', (string) User::query()->count())
                ->description('Всего зарегистрировано')
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->color('primary'),

            Stat::make('Хакатоны', (string) Hackaton::query()->count())
                ->description('На платформе')
                ->descriptionIcon(Heroicon::OutlinedTrophy)
                ->color('success'),

            Stat::make('Команды', (string) Team::query()->count())
                ->description('Создано')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('info'),

            Stat::make('Жалобы', (string) $pendingReports)
                ->description($pendingReports > 0 ? 'Ожидают рассмотрения' : 'Нет новых')
                ->descriptionIcon(Heroicon::OutlinedFlag)
                ->color($pendingReports > 0 ? 'warning' : 'gray')
                ->url($pendingReports > 0 ? route('filament.admin.resources.reports.index') : null),

            Stat::make('Обращения', (string) $recentContacts)
                ->description('За 7 дней')
                ->descriptionIcon(Heroicon::OutlinedEnvelope)
                ->color($recentContacts > 0 ? 'warning' : 'gray'),
        ];
    }
}
