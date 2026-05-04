<?php

namespace Database\Factories;

use App\Models\HackatonDocument;
use App\Models\User;
use App\Models\UserHackatonDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserHackatonDocument>
 */
class UserHackatonDocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'hackaton_document_id' => HackatonDocument::factory(),
            'file_url' => 'user_hackaton_documents/'.fake()->uuid().'.pdf',
        ];
    }
}
