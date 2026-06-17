@props([
    'status',
    'size' => null,
])

@php
    use App\Enums\ApplicationStatus;

    $badgeClass = $status instanceof ApplicationStatus
        ? $status->badgeClass()
        : 'badge-ghost';

    $sizeClass = match ($size) {
        'sm' => 'badge-sm',
        'xs' => 'badge-xs',
        default => '',
    };
@endphp

<span {{ $attributes->class(['badge', $badgeClass, $sizeClass]) }}>
    {{ $status instanceof ApplicationStatus ? $status->label() : (string) $status }}
</span>
