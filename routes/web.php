<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('home');

Route::livewire('/teams', 'pages::teams.index');
Route::livewire('/teams/create', 'pages::teams.create');
Route::livewire('/teams/{team}/edit', 'pages::teams.edit');
Route::livewire('/profile/teams', 'pages::profile.teams.index');

Route::livewire('/hackatons', 'pages::hackatons.index');
Route::livewire('/hackatons/create', 'pages::hackatons.create');
Route::livewire('/profile/hackatons', 'pages::profile.hackatons.index');
Route::livewire('/hackatons/{hackaton}', 'pages::hackatons.show');
