<?php

use App\Models\Team;
use App\Models\User;

test('team owner can view team edit page', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('teams.edit', $team))
        ->assertOk()
        ->assertSee('Редактирование команды', false)
        ->assertSee('Прогресс заполнения', false);
});
