<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

arch('controllers have Controller suffix')
    ->expect('App\Http\Controllers')
    ->classes()
    ->toHaveSuffix('Controller');

arch('policies have Policy suffix')
    ->expect('App\Policies')
    ->classes()
    ->toHaveSuffix('Policy');

arch('livewire pages extend the livewire component')
    ->expect('App\Livewire\Pages')
    ->classes()
    ->toExtend(Component::class);

arch('models live under App\\Models namespace and extend Eloquent')
    ->expect('App\Models')
    ->classes()
    ->toExtend(Model::class)
    ->ignoring(['App\Models\User']);

arch('no debug helpers leak into application code')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->each->not->toBeUsed();

arch('strict types are declared in policies')
    ->expect('App\Policies')
    ->toUseStrictTypes()
    ->ignoring([
        'App\Policies\HackatonAnnouncementPolicy',
        'App\Policies\HackatonCasePolicy',
        'App\Policies\HackatonCaseSubmissionPolicy',
        'App\Policies\HackatonCertificatePolicy',
    ]);
