<div class="mx-auto w-full max-w-7xl space-y-6" aria-busy="true" aria-label="Загрузка кабинета хакатона">
    <div class="flex flex-wrap gap-2">
        <div class="skeleton h-4 w-20 rounded-lg"></div>
        <div class="skeleton h-4 w-4 rounded"></div>
        <div class="skeleton h-4 w-24 rounded-lg"></div>
        <div class="skeleton h-4 w-4 rounded"></div>
        <div class="skeleton h-4 w-32 rounded-lg"></div>
    </div>

    <div class="ui-surface-card">
        <div class="space-y-4 p-6">
            <div class="skeleton h-8 w-2/3 max-w-md rounded-xl"></div>
            <div class="skeleton h-4 w-full max-w-xl rounded-lg"></div>
            <div class="flex flex-wrap gap-2">
                <div class="skeleton h-9 w-28 rounded-lg"></div>
                <div class="skeleton h-9 w-24 rounded-lg"></div>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                @foreach (range(1, 3) as $_)
                    <div class="skeleton h-20 rounded-xl"></div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        @foreach (range(1, 4) as $_)
            <div class="ui-surface-card p-6">
                <div class="skeleton mb-4 h-6 w-40 rounded-lg"></div>
                <div class="space-y-2">
                    <div class="skeleton h-12 w-full rounded-lg"></div>
                    <div class="skeleton h-12 w-full rounded-lg"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
