<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\Team;
use App\Models\User;

test('guest sees marketing landing on home', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Найдите команду. Проведите хакатон.', false);
    $response->assertDontSee('Краткая сводка', false);
    $response->assertDontSee('Популярные команды', false);
    $response->assertSee('Активные хакатоны', false);
    $response->assertSee('Платформа в цифрах', false);
    $response->assertSee('Как это работает', false);
    $response->assertDontSee('Отзывы участников', false);
    $response->assertSee('Первые хакатоны уже скоро!', false);
    $response->assertSee('Следите за обновлениями — скоро здесь появятся интересные события.', false);
});

test('guest sees featured hackatons when public events exist', function () {
    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'title' => 'FeaturedDashboardHackUnique',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee($hackaton->title, false)
        ->assertDontSee('Первые хакатоны уже скоро!', false);
});

test('participant next step links to team create when user has no teams', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Создайте команду', false)
        ->assertSee('/teams/create', false);
});

test('authenticated participant sees dashboard summary on home', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
    $response->assertSee('Участник', false);
    $response->assertSee('data-test="home-dashboard"', false);
    $response->assertSee('Создайте команду', false);
    $response->assertSee('Следующий шаг', false);
});

test('participant with unverified phone is redirected from home to phone verification', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'phone' => '+79990001122',
        'phone_verified_at' => null,
    ]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertRedirect(route('phone.verify.notice'));
});

test('organizer with pending hackaton application sees review link', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);
    $captain = User::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $captain->id,
        'hackaton_id' => $hackaton->id,
    ]);
    HackatonApplication::factory()->create([
        'team_id' => $team->id,
        'hackaton_id' => $hackaton->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    $this->actingAs($organizer)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Рассмотреть заявки', false)
        ->assertSee('applications_status=pending', false)
        ->assertSee('hackaton-tab-participants', false);
});

test('judge without assigned hackatons sees empty state', function () {
    $judge = User::factory()->judge()->create();

    $this->actingAs($judge)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('data-testid="judge-dashboard-empty"', false)
        ->assertSee('Пока нет назначенных хакатонов', false);
});

test('admin sees extended kpi cards on home', function () {
    User::factory()->count(2)->create();
    Hackaton::factory()->count(1)->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Пользователей', false)
        ->assertSee('Хакатонов', false)
        ->assertSee('Организаторов', false)
        ->assertSee('Заявок на рассмотрении', false);
});
