<?php

use App\Livewire\Pages\Profile\Index as ProfileIndex;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('uploaded avatar persists when file is set on component', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'fio' => 'Тестов Тест',
        'date_of_birth' => '1990-01-15',
        'nickname' => 'avatar_crop_'.uniqid(),
        'avatar_path' => null,
    ]);

    $file = UploadedFile::fake()->image('cropped.jpg', 400, 400);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('avatar', $file)
        ->assertHasNoErrors();

    $fresh = $user->fresh();
    expect($fresh->avatar_path)->not->toBeNull()
        ->and($fresh->avatar_path)->toStartWith('avatars/');

    expect(Storage::disk('public')->exists($fresh->avatar_path))->toBeTrue();
});

test('profile page includes avatar cropper bootstrap when bundle is stale', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSee('x-data="avatarCropper(', false)
        ->assertSee('window.createAvatarCropperModal = function createAvatarCropperModal', false);
});
