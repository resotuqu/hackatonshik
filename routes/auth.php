<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\SocialAuthController;
use App\Livewire\Pages\Auth\Login as AuthLogin;
use App\Livewire\Pages\Auth\OAuthConsent;
use App\Livewire\Pages\Auth\Register as AuthRegister;
use Illuminate\Support\Facades\Route;

Route::get('/login', AuthLogin::class)->name('login');
Route::get('/register', AuthRegister::class)->name('register');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/auth/oauth/consent', OAuthConsent::class)->name('auth.oauth.consent');
});

Route::get('/auth/yandex/redirect', [SocialAuthController::class, 'redirect'])->defaults('provider', 'yandex')->name('auth.yandex.redirect');
Route::get('/auth/yandex/callback', [SocialAuthController::class, 'callback'])->defaults('provider', 'yandex');
Route::get('/auth/yandex/token-page', [SocialAuthController::class, 'yandexTokenPage'])->name('auth.yandex.token-page');
Route::post('/auth/yandex/token', [SocialAuthController::class, 'yandexToken'])
    ->middleware('throttle:login')
    ->name('auth.yandex.token');
Route::get('/auth/vk/redirect', [SocialAuthController::class, 'vkRedirect'])->name('auth.vk.redirect');
Route::get('/auth/vk/callback', [SocialAuthController::class, 'vkCallback'])->name('auth.vk.callback');
Route::post('/auth/vk/token', [SocialAuthController::class, 'vkToken'])
    ->middleware('throttle:login')
    ->name('auth.vk.token');
