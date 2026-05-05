<div class="team-page mx-auto w-full max-w-7xl space-y-6" aria-busy="true" aria-label="Загрузка страницы команды">
    <div class="flex flex-wrap items-center gap-2">
        <div class="skeleton h-4 w-16 rounded-lg"></div>
        <div class="skeleton h-4 w-4 rounded"></div>
        <div class="skeleton h-4 w-14 rounded-lg"></div>
        <div class="skeleton h-4 w-4 rounded"></div>
        <div class="skeleton h-4 w-48 max-w-full rounded-lg"></div>
    </div>

    <div class="flex gap-2 overflow-x-auto pb-1">
        @foreach (range(1, 4) as $_)
            <div class="skeleton h-12 w-36 shrink-0 rounded-xl"></div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="ui-surface-card overflow-hidden lg:col-span-2">
            <div class="skeleton h-14 w-full rounded-none"></div>
            <div class="skeleton aspect-video w-full rounded-none"></div>
            <div class="space-y-3 p-5 sm:p-7">
                <div class="skeleton h-6 w-full rounded-lg"></div>
                <div class="skeleton h-4 w-full rounded-lg"></div>
                <div class="skeleton h-4 w-4/5 rounded-lg"></div>
            </div>
        </div>
        <div class="ui-surface-card space-y-4 p-4 sm:p-5">
            <div class="skeleton h-8 w-1/2 rounded-lg"></div>
            <div class="skeleton h-10 w-full rounded-xl"></div>
            <div class="skeleton h-32 w-full rounded-2xl"></div>
        </div>
    </div>
</div>
