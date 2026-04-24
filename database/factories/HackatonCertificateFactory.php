<?php

namespace Database\Factories;

use App\Models\Hackaton;
use App\Models\HackatonCertificate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonCertificate>
 */
class HackatonCertificateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_id' => Hackaton::factory(),
            'user_id' => User::factory(),
            'uploaded_by' => User::factory()->partner(),
            'title' => fake()->sentence(3),
            'file_path' => 'hackaton_certificates/test-certificate.pdf',
            'issued_at' => now(),
        ];
    }
}
