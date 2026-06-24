<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHackatonCertificateRequest;
use App\Models\Hackaton;
use App\Models\HackatonCertificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class HackatonCertificateController extends Controller
{
    public function store(StoreHackatonCertificateRequest $request, Hackaton $hackaton): RedirectResponse
    {
        Gate::authorize('create', [HackatonCertificate::class, $hackaton]);

        $validated = $request->validated();
        $userIds = collect($validated['user_ids'] ?? [])->filter()->values();
        if ($userIds->isEmpty()) {
            $userIds = collect([(int) $validated['user_id']]);
        }

        $createdCount = 0;
        DB::transaction(function () use ($request, $hackaton, $validated, $userIds, &$createdCount): void {
            $sourcePath = $request->file('file')->store('hackaton_certificates', 'local');
            $sourceContents = Storage::disk('local')->get($sourcePath);

            foreach ($userIds as $userId) {
                $alreadyExists = HackatonCertificate::query()
                    ->where('hackaton_id', $hackaton->id)
                    ->where('user_id', $userId)
                    ->where('title', $validated['title'])
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                $userFilePath = 'hackaton_certificates/'.uniqid("{$hackaton->id}_{$userId}_", true).'.'.pathinfo($sourcePath, PATHINFO_EXTENSION);
                Storage::disk('local')->put($userFilePath, $sourceContents);

                $hackaton->certificates()->create([
                    'user_id' => $userId,
                    'uploaded_by' => $request->user()->id,
                    'title' => $validated['title'],
                    'file_path' => $userFilePath,
                    'issued_at' => $validated['issued_at'] ?? now(),
                ]);

                $createdCount++;
            }

            Storage::disk('local')->delete($sourcePath);
        });

        return back()->with('success', "Сертификаты загружены: {$createdCount} шт.");
    }

    public function download(HackatonCertificate $certificate): mixed
    {
        Gate::authorize('download', $certificate);

        return Storage::disk('local')->download($certificate->file_path);
    }

    public function destroy(Hackaton $hackaton, HackatonCertificate $certificate): RedirectResponse
    {
        abort_unless($certificate->hackaton_id === $hackaton->id, 404);
        Gate::authorize('delete', $certificate);

        $filePath = $certificate->file_path;
        $certificate->delete();

        $stillReferenced = HackatonCertificate::query()
            ->where('file_path', $filePath)
            ->exists();

        if (! $stillReferenced) {
            Storage::disk('local')->delete($filePath);
        }

        return back()->with('success', 'Сертификат удалён.');
    }
}
