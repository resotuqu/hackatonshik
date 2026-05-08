<?php

declare(strict_types=1);

it('renders the registration page for guests', function () {
    visit('/register')
        ->assertSee('Регистрация')
        ->assertNoJavaScriptErrors();
});

it('renders the login page for guests', function () {
    visit('/login')
        ->assertSee('Войти')
        ->assertNoJavaScriptErrors();
});
