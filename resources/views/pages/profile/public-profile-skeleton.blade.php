<div
    class="mx-auto w-full max-w-6xl space-y-6"
    role="status"
    aria-busy="true"
    aria-live="polite"
    aria-label="Загрузка публичного профиля"
>
    <span class="sr-only">Загрузка профиля пользователя, пожалуйста подождите</span>

    <nav class="text-sm breadcrumbs" aria-hidden="true">
        <ul class="flex flex-wrap items-center gap-1">
            <li><div class="skeleton h-4 w-16 rounded-lg"></div></li>
            <li><div class="skeleton h-4 w-3 rounded"></div></li>
            <li><div class="skeleton h-4 w-36 rounded-lg"></div></li>
        </ul>
    </nav>

    <section
        class="ui-page-hero"
        aria-hidden="true"
    >
        <div class="relative flex flex-col gap-6 md:flex-row md:items-start">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                <div class="skeleton h-32 w-32 shrink-0 rounded-full ring-2 ring-base-300/50 sm:h-36 sm:w-36"></div>
                <div class="min-w-0 flex-1 space-y-3">
                    <div class="skeleton h-6 w-28 rounded-lg"></div>
                    <div class="skeleton h-10 w-full max-w-md rounded-xl"></div>
                    <div class="skeleton h-4 w-40 rounded-lg"></div>
                    <div class="flex flex-wrap gap-3 pt-1">
                        <div class="skeleton h-5 w-24 rounded-lg"></div>
                        <div class="skeleton h-5 w-20 rounded-lg"></div>
                    </div>
                </div>
            </div>
            <div class="skeleton h-10 w-36 shrink-0 rounded-xl sm:ml-auto"></div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3" aria-hidden="true">
        @foreach (range(1, 3) as $_)
            <div class="ui-surface-card p-5">
                <div class="skeleton mb-2 h-4 w-28 rounded-lg"></div>
                <div class="skeleton h-9 w-16 rounded-lg"></div>
            </div>
        @endforeach
    </div>

    <section class="card border border-base-300 bg-base-100" aria-hidden="true">
        <div class="card-body gap-4">
            <div class="skeleton h-6 w-48 rounded-lg"></div>
            <div class="flex flex-wrap gap-2">
                @foreach (range(1, 4) as $_)
                    <div class="skeleton h-8 w-24 rounded-full"></div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2" aria-hidden="true">
        <div class="ui-surface-card p-5">
            <div class="skeleton mb-4 h-6 w-40 rounded-lg"></div>
            <x-team-card-skeleton />
        </div>
        <div class="ui-surface-card p-5">
            <div class="skeleton mb-4 h-6 w-44 rounded-lg"></div>
            <x-hackaton-card-skeleton />
        </div>
    </div>
</div>
