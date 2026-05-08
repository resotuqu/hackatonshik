<?php

use App\Livewire\Pages\Admin\AvatarPresets;
use App\Models\AvatarPreset;
use App\Models\AvatarPresetPack;
use App\Models\User;
use App\Support\PresetAvatar;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('admin avatar presets page is forbidden for non-admin', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/avatar-presets')->assertForbidden();
});

test('admin can create avatar preset pack', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(AvatarPresets::class)
        ->set('new_pack_name', 'Тестовый пак')
        ->set('new_pack_slug', 'test-pack-admin')
        ->set('new_pack_sort_order', 5)
        ->call('createPack')
        ->assertHasNoErrors();

    expect(AvatarPresetPack::query()->where('slug', 'test-pack-admin')->exists())->toBeTrue();
});

test('admin can upload multiple preset images to pack via Livewire', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $pack = AvatarPresetPack::factory()->create([
        'slug' => 'multi-up-pack',
        'name' => 'Multi up',
        'is_active' => true,
    ]);
    Storage::disk('public')->makeDirectory(PresetAvatar::packStorageDirectory($pack->slug));

    $one = UploadedFile::fake()->image('c1.jpg', 100, 100);
    $two = UploadedFile::fake()->image('c2.jpg', 100, 100);

    Livewire::actingAs($admin)
        ->test(AvatarPresets::class)
        ->set('upload_pack_id', $pack->id)
        ->set('upload_files', [$one, $two])
        ->call('uploadToPack')
        ->assertHasNoErrors();

    $paths = AvatarPreset::query()
        ->where('avatar_preset_pack_id', $pack->id)
        ->pluck('storage_path')
        ->all();

    expect($paths)->toHaveCount(2);

    foreach ($paths as $path) {
        expect($path)->toStartWith('preset_avatars/packs/multi-up-pack/')
            ->and(Storage::disk('public')->exists($path))->toBeTrue();
    }
});
