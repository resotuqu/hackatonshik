<?php

declare(strict_types=1);

use App\Livewire\Pages\Admin\AvatarPresets;
use App\Livewire\Pages\Admin\Index as AdminIndex;
use App\Livewire\Pages\Hackatons\Create as HackatonsCreate;
use App\Livewire\Pages\Hackatons\Edit as HackatonsEdit;
use App\Livewire\Pages\Teams\Create as TeamsCreate;
use App\Livewire\Pages\Teams\Index as TeamsIndex;
use App\Models\Hackaton;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('participant cannot create hackaton when livewire mount is bypassed', function () {
    $organizer = User::factory()->partner()->create();
    $participant = User::factory()->create();

    actingAs($organizer);

    $component = Livewire::test(HackatonsCreate::class);

    Auth::login($participant);

    $component->call('save')->assertForbidden();
});

test('non owner cannot save hackaton when livewire mount is bypassed', function () {
    $owner = User::factory()->partner()->create();
    $attacker = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($owner)->create([
        'title' => 'Original title',
        'description' => 'Original description',
    ]);

    actingAs($owner);

    $component = Livewire::test(HackatonsEdit::class, ['hackaton' => $hackaton])
        ->set('title', 'Stolen title');

    Auth::login($attacker);

    $component->call('save')->assertForbidden();

    expect($hackaton->fresh()->title)->toBe('Original title');
});

test('non admin cannot create partner when livewire mount is bypassed', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    actingAs($admin);

    $component = Livewire::test(AdminIndex::class)
        ->set('fio', 'Test Partner')
        ->set('date_of_birth', '1990-01-01')
        ->set('email', 'partner-bypass@example.com')
        ->set('nickname', 'partner_bypass')
        ->set('phone', '+79001234567')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123');

    Auth::login($user);

    $component->call('savePartner')->assertForbidden();

    expect(User::query()->where('email', 'partner-bypass@example.com')->exists())->toBeFalse();
});

test('non admin cannot create avatar pack when livewire mount is bypassed', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    actingAs($admin);

    $component = Livewire::test(AvatarPresets::class)
        ->set('new_pack_name', 'Bypass Pack')
        ->set('new_pack_slug', 'bypass-pack')
        ->set('new_pack_sort_order', 1);

    Auth::login($user);

    $component->call('createPack')->assertForbidden();
});

test('judge cannot create team when livewire mount is bypassed', function () {
    $participant = User::factory()->create();
    $judge = User::factory()->judge()->create();

    actingAs($participant);

    $component = Livewire::test(TeamsCreate::class);

    Auth::login($judge);

    $component->call('save')->assertForbidden();
});

test('judge cannot quick apply to team when livewire mount is bypassed', function () {
    $participant = User::factory()->create();
    $judge = User::factory()->judge()->create();
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $roleCategory = Role::factory()->create();
    TeamRole::factory()->create([
        'team_id' => $team->id,
        'user_id' => null,
        'role_id' => $roleCategory->id,
    ]);

    actingAs($participant);

    $component = Livewire::test(TeamsIndex::class);

    Auth::login($judge);

    $component->call('quickApplyTeam', $team->id)->assertForbidden();
});

test('non admin cannot mount admin dashboard livewire component', function () {
    actingAs(User::factory()->create());

    Livewire::test(AdminIndex::class)->assertForbidden();
});

test('participant cannot mount hackaton create livewire component', function () {
    actingAs(User::factory()->create());

    Livewire::test(HackatonsCreate::class)->assertForbidden();
});
