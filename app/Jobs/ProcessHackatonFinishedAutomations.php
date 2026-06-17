<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\Hackaton\ResolveParticipantUsersForHackatonCertificates;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonCertificate;
use App\Models\User;
use App\Notifications\HackatonAnnouncementPublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class ProcessHackatonFinishedAutomations implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $hackatonId,
    ) {}

    public function handle(ResolveParticipantUsersForHackatonCertificates $resolveParticipants): void
    {
        $hackaton = Hackaton::query()->find($this->hackatonId);

        if ($hackaton === null || $hackaton->status !== HackatonStatus::FINISHED) {
            return;
        }

        if ($hackaton->finished_automations_ran_at !== null) {
            return;
        }

        if ($hackaton->auto_publish_results_announcement) {
            $announcement = $hackaton->announcements()->create([
                'title' => 'Итоги хакатона',
                'body' => 'Хакатон завершён. Результаты опубликованы — проверьте итоги и сертификаты в личном кабинете.',
                'is_draft' => false,
                'template_key' => 'results',
                'published_at' => now(),
                'created_by' => $hackaton->user_id,
            ]);

            $participants = User::query()
                ->whereIn('id', $resolveParticipants->handle($hackaton)->pluck('id'))
                ->get();

            if ($participants->isNotEmpty()) {
                Notification::send($participants, new HackatonAnnouncementPublished($announcement));
            }
        }

        if ($hackaton->auto_issue_certificates && $hackaton->certificate_template_path !== null) {
            $participants = $resolveParticipants->handle($hackaton);
            $title = 'Сертификат участника';

            foreach ($participants as $participant) {
                $exists = HackatonCertificate::query()
                    ->where('hackaton_id', $hackaton->id)
                    ->where('user_id', $participant->id)
                    ->where('title', $title)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $hackaton->certificates()->create([
                    'user_id' => $participant->id,
                    'uploaded_by' => $hackaton->user_id,
                    'title' => $title,
                    'file_path' => $hackaton->certificate_template_path,
                    'issued_at' => now(),
                ]);
            }
        }

        $hackaton->forceFill(['finished_automations_ran_at' => now()])->save();
    }
}
