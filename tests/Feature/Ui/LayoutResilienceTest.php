<?php

use App\Livewire\TeamChat;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

test('authenticated layout uses safe z-index for avatar dropdown', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk()
        ->assertSee('dropdown-content bg-base-100 rounded-box z-50 mt-3 w-52 p-2 shadow', false)
        ->assertDontSee('dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow', false);
});

test('authenticated layout scrolls only the sidebar menu on mobile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk()
        ->assertSee('min-h-0 flex-1 overflow-y-auto overscroll-y-contain px-0 lg:overflow-y-visible', false);
});

test('team card shows overflow counter for extra skill tags', function () {
    $team = Team::factory()->make();
    $team->forceFill([
        'id' => 101,
        'empty_roles_count' => 0,
    ]);

    $html = view('components.team-card', [
        'team' => $team,
        'skillTags' => ['PHP', 'Laravel', 'Vue', 'React', 'Docker', 'Redis'],
        'href' => '/teams/101',
    ])->render();

    expect($html)->toContain('+2');
});

test('team chat allows overflow for emoji picker and opens picker downward', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create([
        'title' => 'Alpha Team With A Very Long Name That Should Truncate',
    ]);

    $this->actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->assertSee('!overflow-visible', false)
        ->assertSee('max-w-[50%] truncate', false);

    expect(file_get_contents(resource_path('views/livewire/team-chat.blade.php')))
        ->toContain('absolute top-full mt-1 z-50');
});

test('layout cookie banner sits above mobile bottom nav with extra main padding', function () {
    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSee('z-[65]', false)
        ->assertSee('cookieConsent', false)
        ->assertSee('cookie-consent-accepted', false)
        ->assertSee('10.5rem', false)
        ->assertSee('lg:left-80', false)
        ->assertSee('lg:pb-24', false)
        ->assertSee('Cookie для работы сервиса', false);
});

test('about page timeline is clipped to prevent mobile overflow', function () {
    $response = $this->get('/about');

    $response->assertOk()
        ->assertSee('about-history-timeline', false)
        ->assertSee('overflow-x-clip', false);
});

test('login oauth buttons constrain provider label width', function () {
    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSee('min-w-0 truncate', false)
        ->assertSee(__('ui.auth.login.vk'), false);

    expect(file_get_contents(resource_path('views/components/oauth-buttons.blade.php')))
        ->toContain('oauth-provider-btn')
        ->toContain('min-w-0 truncate');
});

test('news index filter uses correct tag label', function () {
    $response = $this->get(route('news.index'));

    $response->assertOk()
        ->assertSee('Тег')
        ->assertDontSee('Ter', false);
});

test('auth login grid top-aligns brand and form cards', function () {
    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSee('grid grid-cols-1 items-start gap-4 lg:grid-cols-5', false);
});

test('organizer dashboard header vertically centers action button', function () {
    $organizer = User::factory()->partner()->create();

    $response = $this->actingAs($organizer)->get(route('organizer.dashboard'));

    $response->assertOk()
        ->assertSee('sm:items-center sm:justify-between', false);
});
