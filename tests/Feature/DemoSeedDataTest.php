<?php

declare(strict_types=1);

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonTemplate;
use Database\Seeders\DemoShowcaseSeeder;
use Database\Seeders\HackatonTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('hackaton template seeder creates active presets for create wizard', function () {
    $this->seed(HackatonTemplateSeeder::class);

    expect(HackatonTemplate::query()->where('slug', 'gamejam')->where('is_active', true)->exists())->toBeTrue()
        ->and(HackatonTemplate::query()->where('slug', 'ai-hack')->where('is_active', true)->exists())->toBeTrue();
});

test('demo showcase seeder pins smartomega hackathon to finished', function () {
    $hackaton = Hackaton::factory()->create([
        'title' => DemoShowcaseSeeder::SHOWCASE_TITLE,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'is_public' => true,
    ]);

    $this->seed(DemoShowcaseSeeder::class);

    $hackaton->refresh();

    expect($hackaton->status)->toBe(HackatonStatus::FINISHED)
        ->and($hackaton->auto_issue_certificates)->toBeTrue()
        ->and($hackaton->auto_publish_results_announcement)->toBeTrue();
});
