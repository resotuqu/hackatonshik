<?php

use App\Mail\EmailChangeCodeMail;
use App\Models\User;

test('email change code mailable for old address renders branded body', function () {
    $user = User::factory()->make([
        'email' => 'old@example.test',
        'fio' => 'Иван Тестов',
    ]);
    $user->id = 1;

    $mailable = new EmailChangeCodeMail(
        user: $user,
        code: '123456',
        mailSubject: 'Код для смены электронной почты — Хакатонщик',
        intro: 'Вы запросили смену адреса электронной почты.',
        disclaimer: 'Если это были не вы, проигнорируйте это письмо.',
        recipientEmail: null,
    );

    $mailable->assertHasSubject('Код для смены электронной почты — Хакатонщик');
    $mailable->assertTo('old@example.test');
    $html = $mailable->render();
    expect($html)->toContain('Иван Тестов, Вы запросили смену адреса электронной почты.')
        ->and($html)->toContain('123456')
        ->and($html)->toContain('Хакатонщик. Все права защищены.');
});

test('phone change email code mailable renders branded body', function () {
    $user = User::factory()->make([
        'email' => 'user@example.test',
        'fio' => 'Пётр',
    ]);
    $user->id = 1;

    $mailable = new EmailChangeCodeMail(
        user: $user,
        code: '888777',
        mailSubject: 'Код для смены номера телефона — Хакатонщик',
        intro: 'Вы начали смену номера телефона в профиле на платформе Хакатонщик. Введите код ниже, чтобы продолжить.',
        disclaimer: 'Если это были не вы, смените пароль и обратитесь в поддержку.',
        recipientEmail: null,
    );

    $mailable->assertHasSubject('Код для смены номера телефона — Хакатонщик');
    $mailable->assertTo('user@example.test');
    expect($mailable->render())->toContain('888777')
        ->and($mailable->render())->toContain('смену номера телефона');
});

test('email change code mailable for new address targets new inbox', function () {
    $user = User::factory()->make([
        'email' => 'old@example.test',
        'fio' => 'Мария',
    ]);
    $user->id = 1;

    $mailable = new EmailChangeCodeMail(
        user: $user,
        code: '654321',
        mailSubject: 'Подтвердите новый адрес — Хакатонщик',
        intro: 'Введите этот код в профиле.',
        disclaimer: 'Если вы не запрашивали смену почты, проигнорируйте это письмо.',
        recipientEmail: 'fresh@example.test',
    );

    $mailable->assertTo('fresh@example.test');
});
