<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\HackatonDocument;
use App\Models\User;
use App\Models\UserHackatonDocument;
use Illuminate\Database\Seeder;

class UserHackatonDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->where('role', 'user')->orderBy('id')->get();
        if ($users->isEmpty()) {
            return;
        }

        foreach (HackatonDocument::query()->orderBy('id')->take(12)->get() as $doc) {
            $user = $users->random();
            UserHackatonDocument::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'hackaton_document_id' => $doc->id,
                ],
                [
                    'file_url' => 'user_hackaton_documents/u'.$user->id.'-d'.$doc->id.'.pdf',
                ],
            );
        }
    }
}
