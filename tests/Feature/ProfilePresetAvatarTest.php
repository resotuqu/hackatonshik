<?php

use App\Livewire\Pages\Profile\Index as ProfileIndex;
use App\Models\AvatarPreset;
use App\Models\AvatarPresetPack;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('select preset persists avatar path without global save', function () {
    Storage::fake('public');

    $pack = AvatarPresetPack::factory()->create([
        'slug' => 'test-pack',
        'name' => 'Test pack',
        'is_active' => true,
    ]);
    $path = 'preset_avatars/packs/test-pack/sample.svg';
    Storage::disk('public')->put($path, '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 8"></svg>');
    AvatarPreset::query()->create([
        'avatar_preset_pack_id' => $pack->id,
        'storage_path' => $path,
        'sort_order' => 0,
    ]);

    $user = User::factory()->create([
        'fio' => 'Иванов Иван',
        'date_of_birth' => '1995-03-20',
        'nickname' => 'preset_user_'.uniqid(),
        'avatar_path' => null,
    ]);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->call('selectPreset', $path)
        ->assertSet('selected_preset_path', $path)
        ->assertHasNoErrors();

    expect($user->fresh()->avatar_path)->toBe($path);
});

test('tampered selected preset path does not change stored avatar', function () {
    Storage::fake('public');

    $pack = AvatarPresetPack::factory()->create([
        'slug' => 'valid-pack',
        'name' => 'Valid',
        'is_active' => true,
    ]);
    $current = 'preset_avatars/packs/valid-pack/hold.svg';
    Storage::disk('public')->put($current, '<svg xmlns="http://www.w3.org/2000/svg"></svg>');
    AvatarPreset::query()->create([
        'avatar_preset_pack_id' => $pack->id,
        'storage_path' => $current,
        'sort_order' => 0,
    ]);

    $user = User::factory()->create([
        'fio' => 'Петров Пётр',
        'date_of_birth' => '1992-07-10',
        'nickname' => 'preset_user2_'.uniqid(),
        'avatar_path' => $current,
    ]);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('selected_preset_path', '../../../etc/passwd');

    expect($user->fresh()->avatar_path)->toBe($current);
});

test('selectPreset ignores unknown path', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'fio' => 'Сидоров Сидор',
        'date_of_birth' => '1998-11-05',
        'nickname' => 'preset_user3_'.uniqid(),
    ]);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->call('selectPreset', 'preset_avatars/packs/nope/nope.svg')
        ->assertSet('selected_preset_path', null);
});

test('legacy flat preset path still selectable when file exists on disk', function () {
    Storage::fake('public');
    Storage::disk('public')->put(
        'preset_avatars/legacy-face.svg',
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 8"></svg>'
    );

    $user = User::factory()->create([
        'fio' => 'Кузнецов Кузьма',
        'date_of_birth' => '1991-01-15',
        'nickname' => 'preset_user4_'.uniqid(),
        'avatar_path' => null,
    ]);

    $path = 'preset_avatars/legacy-face.svg';

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->call('selectPreset', $path)
        ->assertSet('selected_preset_path', $path);

    expect($user->fresh()->avatar_path)->toBe($path);
});
