<?php

use App\Livewire\Pages\Admin\Index as AdminIndex;
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

test('admin can toggle platform feature from admin panel', function () {
    $admin = User::factory()->admin()->create();

    Cache::flush();

    PlatformSetting::query()->where('key', 'feature.chat_large_files')->update(['value' => '1']);

    Livewire::actingAs($admin)
        ->test(AdminIndex::class)
        ->call('togglePlatformFeature', 'feature.chat_large_files')
        ->assertHasNoErrors();

    expect(PlatformSetting::isEnabled('feature.chat_large_files'))->toBeFalse();
});

test('non-admin cannot toggle platform features', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(AdminIndex::class)
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
