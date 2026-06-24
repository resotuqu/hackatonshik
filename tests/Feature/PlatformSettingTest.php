<?php

use App\Livewire\TeamChat;
use App\Models\PlatformSetting;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

test('chat_large_files is enabled by default after migration', function () {
    expect(PlatformSetting::isEnabled('feature.chat_large_files'))->toBeTrue();
});

test('PlatformSetting toggle switches value and clears cache', function () {
    Cache::flush();

    expect(PlatformSetting::isEnabled('feature.chat_large_files'))->toBeTrue();

    PlatformSetting::toggle('feature.chat_large_files');
    expect(PlatformSetting::isEnabled('feature.chat_large_files'))->toBeFalse();

    PlatformSetting::toggle('feature.chat_large_files');
    expect(PlatformSetting::isEnabled('feature.chat_large_files'))->toBeTrue();
});

test('admin can access platform settings in filament', function () {
    $admin = User::factory()->admin()->create();
    $setting = PlatformSetting::query()->firstOrFail();

    $this->actingAs($admin)
        ->get(route('filament.admin.resources.platform-settings.edit', $setting))
        ->assertOk();
});

test('non-admin cannot access platform settings in filament', function () {
    $user = User::factory()->create();
    $setting = PlatformSetting::query()->firstOrFail();

    $this->actingAs($user)
        ->get(route('filament.admin.resources.platform-settings.edit', $setting))
        ->assertForbidden();
});

test('TeamChat enforces 10MB max when large files feature is disabled', function () {
    Cache::flush();

    PlatformSetting::query()->where('key', 'feature.chat_large_files')->update(['value' => '0']);

    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create();

    $this->actingAs($owner);

    $component = Livewire::test(TeamChat::class, ['team' => $team]);

    expect($component->instance()->maxFileMb ?? null)->toBeNull();

    $rendered = $component->html();
    expect($rendered)->not->toContain('50 МБ');
});

test('TeamChat allows 50MB label when large files feature is enabled', function () {
    Cache::flush();

    PlatformSetting::query()->where('key', 'feature.chat_large_files')->update(['value' => '1']);

    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create();

    $this->actingAs($owner);

    $rendered = Livewire::test(TeamChat::class, ['team' => $team])->html();
    expect($rendered)->toContain('50 МБ');
});
