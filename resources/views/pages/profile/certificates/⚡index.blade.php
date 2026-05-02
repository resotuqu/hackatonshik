<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Сертификаты'])]
class extends Component {
    #[Computed]
    public function certificates()
    {
        return Auth::user()?->certificates()->with('hackaton')->latest('issued_at')->get() ?? collect();
    }
};
?>

<div class="mx-auto w-full max-w-5xl space-y-4">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/profile">Профиль</a></li>
            <li class="opacity-70">Сертификаты</li>
        </ul>
    </div>

    <x-mary-card title="Мои сертификаты" class="card card-border bg-base-100">
        @if($this->certificates->isEmpty())
            <p class="text-base-content/70">Сертификаты пока не выданы.</p>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach($this->certificates as $certificate)
                    <article class="rounded-xl border border-base-300 p-4">
                        <p class="font-semibold">{{ $certificate->title }}</p>
                        <p class="text-sm text-base-content/70">{{ $certificate->hackaton->title }}</p>
                        <p class="mt-1 text-xs text-base-content/60">{{ $certificate->issued_at?->format('d.m.Y') ?? '—' }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-sm btn-primary">Скачать</a>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline"
                                onclick="if (navigator.share) { navigator.share({ title: '{{ $certificate->title }}', text: 'Мой сертификат на Хакатонщике', url: '{{ route('certificates.download', $certificate) }}' }); } else { navigator.clipboard.writeText('{{ route('certificates.download', $certificate) }}'); this.innerText = 'Ссылка скопирована'; }"
                            >
                                Поделиться
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </x-mary-card>
</div>
