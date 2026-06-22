@extends('layouts.app')

@section('title', 'Подтверждение приглашения судьи')

@section('slot')
    <div class="mx-auto w-full max-w-xl space-y-4">
        <x-mary-card title="Подтвердите приглашение" class="card border border-base-300 bg-base-100">
            <p class="text-sm text-base-content/70">
                Вы приглашены в качестве судьи на хакатон
                <span class="font-medium">{{ $hackaton->title }}</span>.
            </p>

            <form method="POST" action="{{ route('judges.invitations.accept.store', $invitation->token) }}" class="mt-4">
                @csrf
                <button type="submit" class="ui-cta-primary w-full">
                    Принять приглашение
                </button>
            </form>
        </x-mary-card>
    </div>
@endsection
