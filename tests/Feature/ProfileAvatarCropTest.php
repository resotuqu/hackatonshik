<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('profile save stores uploaded avatar when jpeg file is set on component', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'fio' => 'Тестов Тест',
        'date_of_birth' => '1990-01-15',
        'nickname' => 'avatar_crop_'.uniqid(),
        'avatar_path' => null,
    ]);

    $file = UploadedFile::fake()->image('cropped.jpg', 400, 400);

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('avatar', $file)
        ->call('save')
        ->assertHasNoErrors();

    $fresh = $user->fresh();
    expect($fresh->avatar_path)->not->toBeNull()
        ->and($fresh->avatar_path)->toStartWith('avatars/');

    expect(Storage::disk('public')->exists($fresh->avatar_path))->toBeTrue();
});
