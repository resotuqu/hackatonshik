<div
    class="mx-auto w-full max-w-7xl space-y-6"
    role="status"
    aria-busy="true"
    aria-live="polite"
    aria-label="Загрузка страницы хакатона"
>
    <span class="sr-only">Загрузка страницы хакатона, пожалуйста подождите</span>

    <nav class="text-sm breadcrumbs" aria-hidden="true">
        <ul class="flex flex-wrap items-center gap-1">
            <li><div class="skeleton h-4 w-16 rounded-lg"></div></li>
            <li><div class="skeleton h-4 w-3 rounded"></div></li>
            <li><div class="skeleton h-4 w-20 rounded-lg"></div></li>
            <li><div class="skeleton h-4 w-3 rounded"></div></li>
            <li><div class="skeleton h-4 w-48 max-w-full rounded-lg"></div></li>
        </ul>
    </nav>

    <div
        class="flex gap-1 w-full overflow-x-auto scroll-smooth rounded-panel border border-base-300 bg-base-200/50 p-1 shadow-inner"
        role="tablist"
        aria-hidden="true"
    >
        <div class="flex gap-1 pb-0">
            @foreach (range(1, 6) as $_)
                <div class="skeleton h-12 min-w-[7rem] shrink-0 rounded-xl px-3"></div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="ui-surface-card overflow-hidden lg:col-span-2">
            <div class="skeleton aspect-video w-full rounded-none"></div>
            <div class="ui-surface-soft space-y-3 p-4 sm:p-6">
                <div class="skeleton h-8 w-3/4 max-w-md rounded-xl"></div>
                <div class="skeleton h-4 w-full rounded-lg"></div>
                <div class="skeleton h-4 w-full rounded-lg"></div>
                <div class="skeleton h-4 w-5/6 rounded-lg"></div>
            </div>
        </div>
        <aside class="ui-surface-card space-y-4 p-4 sm:p-5">
            <div class="skeleton h-6 w-2/3 rounded-lg"></div>
            <div class="skeleton h-10 w-full rounded-xl"></div>
            <div class="skeleton h-10 w-full rounded-xl"></div>
            <div class="skeleton h-24 w-full rounded-panel"></div>
        </aside>
    </div>
</div>
