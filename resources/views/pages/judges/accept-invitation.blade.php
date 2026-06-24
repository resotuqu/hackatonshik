@extends('layouts.app')

@section('title', 'Подтверждение приглашения судьи')

@section('slot')
    <div class="mx-auto w-full max-w-xl space-y-4">
        <x-mary-card title="Приглашение судьи" class="card border border-base-300 bg-base-100">
            <p class="text-sm text-base-content/70">
                Вас приглашают стать судьёй хакатона
                <span class="font-semibold text-base-content">{{ $hackaton->title }}</span>.
            </p>

            <div class="mt-4 space-y-2 rounded-xl border border-base-300 bg-base-200/50 p-4 text-sm">
                @if($hackaton->start_at)
                    <div class="flex items-center gap-2 text-base-content/70">
                        <x-app-icon icon="heroicons:calendar" class="h-4 w-4 shrink-0 text-base-content/40" />
                        <span>
                            {{ $hackaton->start_at->translatedFormat('d M Y') }}
                            @if($hackaton->end_at)
                                — {{ $hackaton->end_at->translatedFormat('d M Y') }}
                            @endif
                        </span>
                    </div>
                @endif

                @php $casesCount = $hackaton->cases()->count(); @endphp
                @if($casesCount > 0)
                    <div class="flex items-center gap-2 text-base-content/70">
                        <x-app-icon icon="heroicons:folder-open" class="h-4 w-4 shrink-0 text-base-content/40" />
                        <span>{{ $casesCount }} {{ $casesCount === 1 ? 'кейс для оценивания' : ($casesCount < 5 ? 'кейса для оценивания' : 'кейсов для оценивания') }}</span>
                    </div>
                @endif

                @if($invitation->expires_at)
                    <div class="flex items-center gap-2 text-base-content/70">
                        <x-app-icon icon="heroicons:clock" class="h-4 w-4 shrink-0 text-base-content/40" />
                        <span>Ссылка действительна до {{ $invitation->expires_at->translatedFormat('d M Y') }}</span>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('judges.invitations.accept.store', $invitation->token) }}" class="mt-4 flex flex-col gap-2">
                @csrf
                <button type="submit" class="ui-cta-primary w-full">
                    Принять приглашение
                </button>
            </form>

            <a href="{{ route('hackatons.index') }}" class="btn btn-ghost mt-1 w-full text-sm">
                Отклонить
            </a>
        </x-mary-card>
    </div>
@endsection
