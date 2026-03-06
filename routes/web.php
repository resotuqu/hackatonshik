<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::index')->name('home');

Route::livewire('/about', 'pages::about.index');
Route::livewire('/news', 'pages::news.index');
Route::livewire('/contacts', 'pages::contacts.index');
Route::livewire('/privacy-policy', 'pages::privacy-policy.index');
Route::livewire('/cookie-policy', 'pages::cookie-policy.index');

Route::livewire('/login', 'pages::auth.login');
Route::livewire('/register', 'pages::auth.register');
Route::livewire('/profile', 'pages::profile.index');
Route::livewire('/admin', 'pages::admin.index');
Route::livewire('/admin/login', 'pages::admin.login');

Route::livewire('/teams', 'pages::teams.index');
Route::livewire('/teams/create', 'pages::teams.create');
Route::livewire('/teams/{team}', 'pages::teams.show');
Route::livewire('/teams/{team}/edit', 'pages::teams.edit');
Route::livewire('/profile/teams', 'pages::profile.teams.index');

Route::livewire('/hackatons', 'pages::hackatons.index');
Route::livewire('/hackatons/create', 'pages::hackatons.create');
Route::livewire('/hackatons/{hackaton}/edit', 'pages::hackatons.edit');
Route::livewire('/profile/hackatons', 'pages::profile.hackatons.index');
Route::livewire('/profile/hackatons/{hackaton}/participants', 'pages::profile.hackatons.participants');
Route::livewire('/hackatons/{hackaton}', 'pages::hackatons.show');
