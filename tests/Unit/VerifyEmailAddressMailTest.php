<?php

use App\Mail\VerifyEmailAddressMail;
use App\Models\User;

test('verify email mailable has branded subject and body', function () {
    $user = User::factory()->make([
        'email' => 'verify-branded@example.test',
        'fio' => 'Тестовый участник',
    ]);
    $user->id = 1;

    $mailable = new VerifyEmailAddressMail($user, 'https://example.test/email/verify');

    $mailable->assertHasSubject('Подтвердите ваш email — Хакатонщик');
    $mailable->assertTo($user->email);
    $mailable->assertSeeInHtml('Тестовый участник, добро пожаловать на платформу Хакатонщик.');
    $mailable->assertSeeInHtml('Подтвердить email');
    $mailable->assertSeeInHtml('© '.date('Y').' Хакатонщик. Все права защищены.');
});
