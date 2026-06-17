<?php

use App\Support\FormatsDateTime;
use Illuminate\Support\Carbon;

test('formats datetime in absolute mode', function () {
    $formatted = FormatsDateTime::absolute(Carbon::parse('2026-06-17 14:30:00'));

    expect($formatted)->toBe('17.06.2026 14:30');
});

test('formats datetime in relative mode with russian locale', function () {
    Carbon::setLocale('ru');

    $formatted = FormatsDateTime::relative(now()->subHour());

    expect($formatted)->toContain('час');
});

test('datetime component renders iso attribute and formatted text', function () {
    $html = view('components.datetime', [
        'value' => Carbon::parse('2026-06-17 14:30:00'),
        'mode' => 'absolute',
    ])->render();

    expect($html)
        ->toContain('<time')
        ->toContain('datetime=')
        ->toContain('17.06.2026 14:30');
});
