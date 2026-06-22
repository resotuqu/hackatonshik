<?php

namespace Database\Factories;

use App\Enums\OrganizerApplicationStatus;
use App\Enums\OrganizerEntityType;
use App\Models\OrganizerApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizerApplication>
 */
class OrganizerApplicationFactory extends Factory
{
    protected $model = OrganizerApplication::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $entityType = fake()->randomElement(OrganizerEntityType::cases());

        return [
            'user_id' => User::factory(),
            'entity_type' => $entityType,
            'company_name' => $entityType === OrganizerEntityType::Company
                ? fake()->company()
                : null,
            'note' => fake()->paragraph(),
            'status' => OrganizerApplicationStatus::Pending,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'admin_note' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrganizerApplicationStatus::Pending,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'admin_note' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrganizerApplicationStatus::Approved,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(?string $adminNote = 'Недостаточно информации'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrganizerApplicationStatus::Rejected,
            'reviewed_at' => now(),
            'admin_note' => $adminNote,
        ]);
    }
}
