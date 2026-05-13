<?php

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\Team;

test('team card does not render the quick apply call to action', function () {
    $team = Team::factory()->make();
    $team->forceFill([
        'id' => 101,
        'empty_roles_count' => 2,
    ]);

    $html = view('components.team-card', [
        'team' => $team,
        'canQuickApply' => true,
        'href' => '/teams/101',
        'vacantRoleNames' => ['Дизайнер'],
    ])->render();

    expect($html)
        ->toContain('Подробнее')
        ->not->toContain('Откликнуться');
});

test('hackaton card does not render the quick apply call to action', function () {
    $hackaton = Hackaton::factory()->make([
        'status' => HackatonStatus::REGISTRATION_OPEN,
    ]);
    $hackaton->forceFill([
        'id' => 202,
        'teams_count' => 7,
        'participants_count' => 28,
    ]);

    $html = view('components.hackaton-card', [
        'hackaton' => $hackaton,
        'canQuickApply' => true,
        'href' => '/hackatons/202',
    ])->render();

    expect($html)
        ->toContain('Подробнее')
        ->not->toContain('Подать заявку');
});
