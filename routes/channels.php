<?php

use App\Models\TeamRole;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('team.{teamId}', function ($user, $teamId) {
    return TeamRole::where('team_id', $teamId)
        ->where('user_id', $user->id)
        ->exists();
});
