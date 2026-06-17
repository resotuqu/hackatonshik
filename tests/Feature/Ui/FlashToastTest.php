<?php

use App\Support\FlashToast;

test('flash toast helper maps session keys to toast payloads', function () {
    session()->flash('success', 'Готово');
    session()->flash('warning', 'Внимание');

    $toasts = FlashToast::fromSession();

    expect($toasts)->toHaveCount(2)
        ->and($toasts[0]['type'])->toBe('success')
        ->and($toasts[0]['title'])->toBe('Готово')
        ->and($toasts[0]['position'])->toBe(FlashToast::POSITION)
        ->and($toasts[1]['type'])->toBe('warning')
        ->and($toasts[1]['title'])->toBe('Внимание');
});

test('layout renders flash toast bridge for redirect flashes', function () {
    $response = $this->withSession(['success' => 'Заявка отправлена'])
        ->get(route('home'));

    $response->assertOk()
        ->assertSee('window.toast', false);
});
