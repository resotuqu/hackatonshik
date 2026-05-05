<div class="mx-auto w-full max-w-7xl space-y-6" aria-busy="true" aria-label="Загрузка страницы хакатона">
    <div class="flex flex-wrap gap-2">
        <div class="skeleton h-4 w-24 rounded-lg"></div>
        <div class="skeleton h-4 w-4 rounded"></div>
        <div class="skeleton h-4 w-20 rounded-lg"></div>
        <div class="skeleton h-4 w-4 rounded"></div>
        <div class="skeleton h-4 w-40 rounded-lg"></div>
    </div>

    <div class="flex gap-2 overflow-x-auto pb-1">
        @foreach (range(1, 6) as $_)
            <div class="skeleton h-12 w-28 shrink-0 rounded-xl"></div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="ui-surface-card lg:col-span-2">
            <div class="skeleton aspect-video w-full rounded-none"></div>
            <div class="space-y-3 p-4 sm:p-6">
                <div class="skeleton h-8 w-3/4 rounded-xl"></div>
                <div class="skeleton h-4 w-full rounded-lg"></div>
                <div class="skeleton h-4 w-full rounded-lg"></div>
                <div class="skeleton h-4 w-5/6 rounded-lg"></div>
            </div>
        </div>
        <div class="ui-surface-card space-y-4 p-4 sm:p-5">
            <div class="skeleton h-6 w-2/3 rounded-lg"></div>
            <div class="skeleton h-10 w-full rounded-xl"></div>
            <div class="skeleton h-10 w-full rounded-xl"></div>
            <div class="skeleton h-24 w-full rounded-2xl"></div>
        </div>
    </div>
</div>
