@props([
    'subject',
    'limit' => 20,
])

@php
    use App\Support\ActivityDescription;
    use Illuminate\Database\Eloquent\Model;
    use Spatie\Activitylog\Models\Activity;

    $activities = $subject instanceof Model
        ? Activity::query()
            ->forSubject($subject)
            ->latest()
            ->with('causer')
            ->limit((int) $limit)
            ->get()
        : collect();
@endphp

<div {{ $attributes->merge(['class' => 'space-y-3']) }}>
    <h3 class="text-sm font-semibold text-base-content">История изменений</h3>

    @if ($activities->isEmpty())
        <p class="text-sm text-base-content/60">История изменений пока пуста.</p>
    @else
        <ul class="space-y-3">
            @foreach ($activities as $activity)
                <li class="border-l-2 border-base-300 pl-4">
                    <p class="text-sm text-base-content/90">{{ ActivityDescription::format($activity) }}</p>
                    <p class="mt-1 text-xs text-base-content/50">
                        {{ ActivityDescription::actorName($activity) }}
                        ·
                        <x-datetime :value="$activity->created_at" mode="relative" />
                    </p>
                </li>
            @endforeach
        </ul>
    @endif
</div>
