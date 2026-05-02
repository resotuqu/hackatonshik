<?php

namespace Database\Factories;

use App\Models\NewsPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NewsPost>
 */
class NewsPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(5);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('###'),
            'excerpt' => fake()->text(180),
            'body' => fake()->paragraphs(5, true),
            'cover_image' => null,
            'category' => fake()->randomElement(['Релизы', 'События', 'Обновления', 'Сообщество']),
            'tags' => fake()->randomElements(['livewire', 'команды', 'анонсы', 'кейсы', 'платформа', 'участники'], random_int(2, 4)),
            'published_at' => now()->subDays(random_int(1, 40)),
            'is_published' => true,
        ];
    }
}
