@props([
    'items' => [],
])

@if(count($items) > 0)
    <section class="rounded-2xl border border-base-300 bg-base-100 p-4 sm:p-5" aria-label="Чек-лист готовности">
        <h2 class="text-sm font-bold uppercase tracking-widest text-base-content/55">Прогресс подготовки</h2>
        <ul class="mt-3 space-y-2">
            @foreach($items as $row)
                <li class="flex items-start gap-2 text-sm">
                    @if(! empty($row['done']))
                        <x-app-icon icon="heroicons:check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-success" />
                    @else
                        <x-app-icon icon="heroicons:minus-circle" class="mt-0.5 h-5 w-5 shrink-0 text-base-content/35" />
                    @endif
                    <span @class(['text-base-content/80' => empty($row['done']), 'font-medium text-base-content' => ! empty($row['done'])])>
                        @if(! empty($row['href']))
                            <a href="{{ $row['href'] }}" class="link link-hover">{{ $row['label'] }}</a>
                        @else
                            {{ $row['label'] }}
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    </section>
@endif
