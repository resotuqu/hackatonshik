<div
    class="mx-auto w-full max-w-7xl space-y-6"
    role="status"
    aria-busy="true"
    aria-live="polite"
    aria-label="Загрузка кабинета хакатона"
>
    <span class="sr-only">Загрузка личного кабинета хакатона, пожалуйста подождите</span>

    <nav class="text-sm breadcrumbs" aria-hidden="true">
        <ul class="flex flex-wrap items-center gap-1">
            <li><div class="skeleton h-4 w-14 rounded-lg"></div></li>
            <li><div class="skeleton h-4 w-3 rounded"></div></li>
            <li><div class="skeleton h-4 w-16 rounded-lg"></div></li>
            <li><div class="skeleton h-4 w-3 rounded"></div></li>
            <li><div class="skeleton h-4 w-28 rounded-lg"></div></li>
            <li><div class="skeleton h-4 w-3 rounded"></div></li>
            <li><div class="skeleton h-4 w-24 rounded-lg"></div></li>
        </ul>
    </nav>

    <section class="card border border-base-200 bg-base-100 shadow-sm">
        <div class="card-body space-y-4">
            <div class="skeleton h-8 w-2/3 max-w-md rounded-xl"></div>
            <div class="skeleton h-4 w-full max-w-xl rounded-lg"></div>
            <div class="flex flex-wrap gap-2">
                <div class="skeleton h-9 w-28 rounded-lg"></div>
                <div class="skeleton h-9 w-24 rounded-lg"></div>
                <div class="skeleton h-9 w-32 rounded-lg"></div>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                @foreach (range(1, 3) as $_)
                    <div class="ui-surface-soft rounded-xl border border-base-300 p-4">
                        <div class="skeleton mb-2 h-3 w-24 rounded"></div>
                        <div class="skeleton h-8 w-16 rounded-lg"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        @foreach (range(1, 4) as $_)
            <article class="card border border-base-200 bg-base-100 shadow-sm">
                <div class="card-body space-y-3">
                    <div class="skeleton h-6 w-40 rounded-lg"></div>
                    <div class="space-y-2">
                        <div class="skeleton h-12 w-full rounded-lg"></div>
                        <div class="skeleton h-12 w-full rounded-lg"></div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</div>
