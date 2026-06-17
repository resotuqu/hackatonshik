<?php

declare(strict_types=1);

use App\Livewire\Pages\Hackatons\Edit as HackatonsEdit;
use App\Models\Hackaton;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('hackaton owner can save own hackaton via livewire', function () {
    $owner = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($owner)->create([
        'title' => 'Original title',
        'description' => 'Original description',
    ]);

    actingAs($owner);

    Livewire::test(HackatonsEdit::class, ['hackaton' => $hackaton])
        ->set('title', 'Updated title')
        ->call('save')
        ->assertHasNoErrors();

    expect($hackaton->fresh()->title)->toBe('Updated title');
});

test('another organizer cannot save someone elses hackaton via livewire', function () {
    $owner = User::factory()->partner()->create();
    $otherOrganizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($owner)->create([
        'title' => 'Original title',
    ]);

    actingAs($otherOrganizer);

    Livewire::test(HackatonsEdit::class, ['hackaton' => $hackaton])
        ->assertForbidden();

    expect($hackaton->fresh()->title)->toBe('Original title');
});
