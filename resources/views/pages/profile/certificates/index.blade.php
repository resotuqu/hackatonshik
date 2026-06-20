<div class="mx-auto w-full max-w-5xl space-y-4">
    <div wire:loading class="space-y-4" aria-busy="true" aria-label="Загрузка сертификатов">
        <div class="skeleton h-4 w-52 rounded-xl"></div>
        <div class="card card-border bg-base-100">
            <div class="p-5 space-y-4">
                <div class="skeleton h-6 w-44 rounded-xl"></div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach (range(1, 4) as $_)
                        <div class="rounded-xl border border-base-300 p-4 space-y-3">
                            <div class="skeleton h-5 w-4/5 rounded-xl"></div>
                            <div class="skeleton h-4 w-3/5 rounded-xl"></div>
                            <div class="skeleton h-3 w-24 rounded-xl"></div>
                            <div class="flex gap-2 pt-2">
                                <div class="skeleton h-9 w-28 rounded-xl"></div>
                                <div class="skeleton h-9 w-28 rounded-xl"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div wire:loading.remove>
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/profile">Профиль</a></li>
            <li class="opacity-70">Сертификаты</li>
        </ul>
    </div>

    <x-profile-nav-tabs active="certificates" />

    <section class="ui-page-header">
        <div class="pb-5">
            <h1 class="ui-heading-display text-3xl font-bold sm:text-4xl">Мои сертификаты</h1>
            <p class="mt-1 text-base-content/70">Сертификаты, полученные за участие в хакатонах.</p>
        </div>
    </section>

    <div class="card border border-base-300 bg-base-100">
        <div class="card-body">
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
                            <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-sm btn-neutral">Скачать</a>
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
        </div>
    </div>
</div>
    </div>
