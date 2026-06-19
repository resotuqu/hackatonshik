<?php

use App\Models\User;

it('returns Имя О.Ф. for full three-part fio', function () {
    $user = new User(['fio' => 'Иванов Иван Иванович']);
    expect($user->publicName())->toBe('Иван И.И.');
});

it('returns Имя Ф. for two-part fio', function () {
    $user = new User(['fio' => 'Иванов Иван']);
    expect($user->publicName())->toBe('Иван И.');
});

it('returns single word fio as-is', function () {
    $user = new User(['fio' => 'Иванов']);
    expect($user->publicName())->toBe('Иванов');
});

it('falls back to nickname when fio is empty', function () {
    $user = new User(['nickname' => 'vova123']);
    expect($user->publicName())->toBe('vova123');
});

it('falls back to Участник when fio and nickname are both empty', function () {
    $user = new User;
    expect($user->publicName())->toBe('Участник');
});
