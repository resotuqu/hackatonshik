<div class="mx-auto w-full max-w-6xl space-y-6" aria-busy="true" aria-label="Загрузка профиля">
    <div class="skeleton h-4 w-64 rounded-lg"></div>
    <div class="ui-surface-card">
        <div class="flex flex-col gap-6 p-6 md:flex-row md:items-center">
            <div class="skeleton h-32 w-32 shrink-0 rounded-full"></div>
            <div class="min-w-0 flex-1 space-y-3">
                <div class="skeleton h-5 w-24 rounded-lg"></div>
                <div class="skeleton h-10 w-3/4 max-w-md rounded-xl"></div>
                <div class="skeleton h-4 w-40 rounded-lg"></div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        @foreach (range(1, 3) as $_)
            <div class="skeleton h-24 rounded-2xl"></div>
        @endforeach
    </div>
    <div class="skeleton h-48 w-full rounded-2xl"></div>
</div>
