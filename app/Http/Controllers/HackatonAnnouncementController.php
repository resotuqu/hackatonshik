<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHackatonAnnouncementRequest;
use App\Models\Hackaton;
use App\Models\HackatonAnnouncement;
use App\Models\User;
use App\Notifications\HackatonAnnouncementPublished;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class HackatonAnnouncementController extends Controller
{
    public function store(StoreHackatonAnnouncementRequest $request, Hackaton $hackaton): RedirectResponse
    {
        Gate::authorize('create', [HackatonAnnouncement::class, $hackaton]);

        $validated = $request->validated();
        $isDraft = (bool) ($validated['is_draft'] ?? false);
        $publishedAt = $isDraft ? null : ($validated['published_at'] ?? now());
        $templateKey = $validated['template_key'] ?? null;

        $body = $validated['body'];

        if ($templateKey !== null && trim($body) === '') {
            $body = match ($templateKey) {
                'start' => 'Хакатон стартовал. Проверьте доступные кейсы и дедлайны.',
                'deadline' => 'Напоминаем о дедлайне. Проверьте, что решения отправлены вовремя.',
                'results' => 'Результаты опубликованы. Проверьте итоги и сертификаты.',
                default => $body,
            };
        }

        $announcement = $hackaton->announcements()->create([
            'title' => $validated['title'],
            'body' => $body,
            'is_draft' => $isDraft,
            'template_key' => $templateKey,
            'published_at' => $publishedAt,
            'created_by' => $request->user()->id,
        ]);

        $images = $request->file('images', []);
        foreach ($images as $index => $image) {
            $announcement->images()->create([
                'path' => $image->storePublicly('hackaton_announcements', 'public'),
                'sort_order' => $index,
            ]);
        }

        $participants = User::query()
            ->where('id', '!=', $hackaton->user_id)
            ->where(function (Builder $query) use ($hackaton): void {
                $query
                    ->whereHas('teams', function (Builder $teamsQuery) use ($hackaton): void {
                        $teamsQuery->where('hackaton_id', $hackaton->id);
                    })
                    ->orWhereHas('teamRoles', function (Builder $rolesQuery) use ($hackaton): void {
                        $rolesQuery->whereHas('team', function (Builder $teamsQuery) use ($hackaton): void {
                            $teamsQuery->where('hackaton_id', $hackaton->id);
                        });
                    });
            })
            ->distinct()
            ->get();

        if (! $isDraft && $publishedAt !== null && now()->greaterThanOrEqualTo($publishedAt) && $participants->isNotEmpty()) {
            Notification::send($participants, new HackatonAnnouncementPublished($announcement));
        }

        return back()->with('success', 'Анонс опубликован.');
    }

    public function destroy(Hackaton $hackaton, HackatonAnnouncement $announcement): RedirectResponse
    {
        abort_unless($announcement->hackaton_id === $hackaton->id, 404);
        Gate::authorize('delete', $announcement);

        $announcement->delete();

        return back()->with('success', 'Анонс удалён.');
    }
}
