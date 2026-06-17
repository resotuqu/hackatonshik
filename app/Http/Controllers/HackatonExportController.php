<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hackaton;
use App\Models\HackatonDocument;
use App\Models\User;
use App\Models\UserHackatonDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class HackatonExportController extends Controller
{
    public function teams(Hackaton $hackaton): StreamedResponse
    {
        $this->authorizeOrganizer($hackaton);

        @set_time_limit(120);

        $teams = $hackaton->teams()->with(['user:id,fio,email,nickname', 'roles:id,team_id,user_id'])->get();
        $filename = "hackaton_{$hackaton->id}_teams.csv";

        return response()->streamDownload(function () use ($teams): void {
            $stream = fopen('php://output', 'wb');
            fputcsv($stream, ['team_id', 'team_title', 'owner', 'owner_email', 'members_count']);

            foreach ($teams as $team) {
                fputcsv($stream, [
                    $team->id,
                    $team->title,
                    $team->user?->fio,
                    $team->user?->email,
                    $team->roles->whereNotNull('user_id')->count(),
                ]);
            }

            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function participants(Hackaton $hackaton): StreamedResponse
    {
        $this->authorizeOrganizer($hackaton);

        @set_time_limit(120);

        $participants = $this->resolveParticipants($hackaton);
        $filename = "hackaton_{$hackaton->id}_participants.csv";

        return response()->streamDownload(function () use ($participants): void {
            $stream = fopen('php://output', 'wb');
            fputcsv($stream, ['user_id', 'fio', 'email', 'nickname', 'teams_count']);

            foreach ($participants as $participant) {
                fputcsv($stream, [
                    $participant['id'],
                    $participant['fio'],
                    $participant['email'],
                    $participant['nickname'],
                    $participant['teams_count'],
                ]);
            }

            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function documentsZip(Hackaton $hackaton): StreamedResponse|RedirectResponse|BinaryFileResponse
    {
        $this->authorizeOrganizer($hackaton);

        @set_time_limit(300);

        $documents = UserHackatonDocument::query()
            ->whereHas('hackatonDocument', fn ($query) => $query->where('hackaton_id', $hackaton->id))
            ->with(['user:id,fio', 'hackatonDocument:id,name'])
            ->get();

        if ($documents->isEmpty()) {
            return back()->with('warning', 'Пока нет загруженных документов участников для экспорта.');
        }

        $tempFile = storage_path("app/tmp/hackaton_{$hackaton->id}_documents.zip");
        if (! is_dir(dirname($tempFile))) {
            mkdir(dirname($tempFile), 0775, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Не удалось подготовить архив документов.');
        }

        foreach ($documents as $document) {
            if (! Storage::disk('public')->exists($document->file_url)) {
                continue;
            }

            $hackatonDocument = $document->hackatonDocument;
            $name = $hackatonDocument instanceof HackatonDocument ? $hackatonDocument->name : 'document';
            $documentUser = $document->user;
            $user = $documentUser instanceof User ? $documentUser->fio : "user_{$document->user_id}";
            $extension = pathinfo($document->file_url, PATHINFO_EXTENSION);
            $filename = "documents/{$user}/{$name}_{$document->id}.{$extension}";

            $zip->addFromString($filename, Storage::disk('public')->get($document->file_url));
        }

        $zip->close();

        return response()->download($tempFile, "hackaton_{$hackaton->id}_documents.zip")->deleteFileAfterSend(true);
    }

    private function resolveParticipants(Hackaton $hackaton): Collection
    {
        $teams = $hackaton->teams()->with(['user:id,fio,email,nickname', 'roles.user:id,fio,email,nickname'])->get();

        return $teams
            ->flatMap(function ($team): Collection {
                $ownerModel = $team->user;
                $owner = $ownerModel instanceof User ? collect([$ownerModel]) : collect();

                return $owner->merge($team->roles->pluck('user')->filter());
            })
            ->groupBy('id')
            ->map(fn (Collection $rows, $id) => [
                'id' => $id,
                'fio' => ($rows->first() instanceof User) ? $rows->first()->fio : null,
                'email' => ($rows->first() instanceof User) ? $rows->first()->email : null,
                'nickname' => ($rows->first() instanceof User) ? $rows->first()->nickname : null,
                'teams_count' => $rows->count(),
            ])
            ->values();
    }

    private function authorizeOrganizer(Hackaton $hackaton): void
    {
        if ((int) $hackaton->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }
}
