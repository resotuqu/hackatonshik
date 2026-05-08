<div
    class="team-page mx-auto w-full max-w-7xl space-y-6"
    role="status"
    aria-busy="true"
    aria-live="polite"
    aria-label="Загрузка страницы команды"
>
    <span class="sr-only">Загрузка страницы команды, пожалуйста подождите</span>

    <nav class="flex flex-wrap items-center gap-1 text-sm motion-safe:animate-card-enter" aria-hidden="true" aria-label="Навигация">
        <div class="skeleton h-4 w-14 rounded-lg"></div>
        <div class="skeleton h-4 w-4 shrink-0 rounded"></div>
        <div class="skeleton h-4 w-16 rounded-lg"></div>
        <div class="skeleton h-4 w-4 shrink-0 rounded"></div>
        <div class="skeleton h-4 min-w-[8rem] max-w-[12rem] flex-1 rounded-lg"></div>
    </nav>

    <div
        class="tabs tabs-boxed w-full overflow-x-auto rounded-2xl border border-base-300/60 bg-base-200/50 p-1 shadow-inner motion-safe:animate-card-enter"
        role="tablist"
        aria-hidden="true"
    >
        <div class="flex gap-1">
            @foreach (range(1, 4) as $_)
                <div class="skeleton h-12 min-w-[8.5rem] shrink-0 rounded-xl"></div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div
            class="relative overflow-hidden rounded-3xl border border-base-300 bg-linear-to-br from-base-100 via-base-100 to-primary/15 shadow-lg motion-safe:animate-card-enter lg:col-span-2"
            aria-hidden="true"
        >
            <div class="pointer-events-none absolute -top-24 -right-20 h-64 w-64 rounded-full bg-secondary/15 blur-3xl motion-reduce:opacity-40"></div>
            <div class="pointer-events-none absolute -bottom-28 -left-20 h-72 w-72 rounded-full bg-primary/12 blur-3xl motion-reduce:opacity-40"></div>

            <div class="relative border-b border-base-300/50 px-5 pb-4 pt-5 sm:px-7 sm:pt-6">
                <div class="skeleton h-10 w-4/5 max-w-lg rounded-xl sm:h-12"></div>
            </div>
            <div class="skeleton aspect-video w-full rounded-none"></div>
            <div class="relative space-y-3 px-5 py-5 sm:px-7">
                <div class="flex flex-wrap gap-3">
                    <div class="skeleton h-12 w-40 rounded-2xl"></div>
                    <div class="skeleton h-10 w-28 rounded-xl"></div>
                </div>
                <div class="space-y-2 pt-1">
                    <div class="skeleton h-4 w-full rounded-lg"></div>
                    <div class="skeleton h-4 w-full rounded-lg"></div>
                    <div class="skeleton h-4 w-4/5 rounded-lg"></div>
                </div>
            </div>
        </div>

        <aside class="ui-surface-card motion-safe:animate-card-enter space-y-4 overflow-hidden p-4 sm:p-5">
            <div class="skeleton h-6 w-1/2 rounded-lg"></div>
            <div class="grid grid-cols-2 gap-3">
                <div class="skeleton h-24 rounded-2xl"></div>
                <div class="skeleton h-24 rounded-2xl"></div>
            </div>
            <div class="skeleton h-32 w-full rounded-2xl"></div>
        </aside>
    </div>
</div>
