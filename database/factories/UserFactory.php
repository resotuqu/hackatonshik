<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fio' => fake()->name(),
            'date_of_birth' => '2000-01-01',
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'nickname' => fake()->name(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->phoneNumber(),
            'phone_verified_at' => now(),
            'role' => 'user',
            'is_profile_public' => true,
            'show_email_on_profile' => false,
            'show_phone_on_profile' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Email remains verified; phone is not (used for phone verification funnel tests).
     */
    public function withoutPhoneVerification(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_verified_at' => null,
        ]);
    }

    public function partner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'partner',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function judge(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'judge',
        ]);
    }
}
