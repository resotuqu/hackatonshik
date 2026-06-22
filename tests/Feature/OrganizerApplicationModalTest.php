<?php

declare(strict_types=1);

use App\Enums\OrganizerApplicationStatus;
use App\Enums\OrganizerEntityType;
use App\Livewire\OrganizerApplicationModal;
use App\Livewire\Pages\Home\Index as HomePage;
use App\Models\OrganizerApplication;
use App\Models\User;
use Livewire\Livewire;

test('pending organizer application opens review modal on home page', function () {
    $user = User::factory()->create();
    OrganizerApplication::factory()->for($user)->pending()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Ваша заявка рассматривается');

    Livewire::actingAs($user)
        ->test(OrganizerApplicationModal::class)
        ->assertSet('showModal', true)
        ->assertSet('applicationStatus', OrganizerApplicationStatus::Pending);
});

test('rejected organizer application shows resubmit form with admin note', function () {
    $user = User::factory()->create();
    OrganizerApplication::factory()->for($user)->rejected('Уточните юридический статус')->create([
        'entity_type' => OrganizerEntityType::Company,
        'company_name' => 'ООО Старое',
        'note' => 'Первоначальное примечание достаточной длины для теста.',
    ]);

    Livewire::actingAs($user)
        ->test(OrganizerApplicationModal::class)
        ->assertSet('showModal', true)
        ->assertSet('applicationStatus', OrganizerApplicationStatus::Rejected)
        ->assertSet('adminNote', 'Уточните юридический статус')
        ->assertSee('Комментарий администратора')
        ->assertSee('Отправить повторно');
});

test('resubmit from modal updates application to pending', function () {
    $user = User::factory()->create();
    $application = OrganizerApplication::factory()->for($user)->rejected()->create([
        'note' => 'Первоначальное примечание достаточной длины для теста.',
    ]);

    Livewire::actingAs($user)
        ->test(OrganizerApplicationModal::class)
        ->set('organizerEntityType', 'company')
        ->set('organizerCompanyName', 'ООО Повтор')
        ->set('organizerNote', 'Новое подробное примечание для повторной отправки заявки.')
        ->call('resubmit')
        ->assertSet('applicationStatus', OrganizerApplicationStatus::Pending);

    $application->refresh();

    expect($application->status)->toBe(OrganizerApplicationStatus::Pending)
        ->and($application->company_name)->toBe('ООО Повтор')
        ->and($application->admin_note)->toBeNull();
});

test('approved organizer application does not render modal component state', function () {
    $user = User::factory()->partner()->create();
    OrganizerApplication::factory()->for($user)->create([
        'status' => OrganizerApplicationStatus::Approved,
    ]);

    Livewire::actingAs($user)
        ->test(OrganizerApplicationModal::class)
        ->assertSet('applicationStatus', null)
        ->assertSet('showModal', false);
});

test('closing pending modal shows banner with reopen action', function () {
    $user = User::factory()->create();
    OrganizerApplication::factory()->for($user)->pending()->create();

    Livewire::actingAs($user)
        ->test(OrganizerApplicationModal::class)
        ->call('closeModal')
        ->assertSet('showModal', false)
        ->assertSee('Подробнее');
});

test('home page loads for user with pending application', function () {
    $user = User::factory()->create();
    OrganizerApplication::factory()->for($user)->pending()->create();

    Livewire::actingAs($user)
        ->test(HomePage::class)
        ->assertSuccessful();
});
