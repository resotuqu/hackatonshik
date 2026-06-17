<div
    class="mx-auto w-full max-w-7xl space-y-12 sm:space-y-16"
    role="status"
    aria-busy="true"
    aria-live="polite"
    aria-label="Загрузка главной страницы"
>
    <span class="sr-only">Загрузка главной страницы, пожалуйста подождите</span>

    <section class="ui-page-hero relative min-h-[22rem] overflow-hidden sm:min-h-[26rem]" aria-hidden="true">
        <div class="relative flex flex-col gap-6 px-5 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:gap-10">
            <div class="flex-1 space-y-4">
                <div class="skeleton h-10 w-5/6 rounded-2xl sm:h-14"></div>
                <div class="skeleton h-4 w-full max-w-xl rounded-xl"></div>
                <div class="skeleton h-4 w-3/4 max-w-lg rounded-xl"></div>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <div class="skeleton h-12 w-full rounded-2xl sm:w-52"></div>
                    <div class="skeleton h-12 w-full rounded-2xl sm:w-44"></div>
                </div>
            </div>
            <div class="skeleton mx-auto aspect-square w-full max-w-[18rem] rounded-[var(--radius-card)] sm:max-w-[20rem] lg:max-w-[22rem]"></div>
        </div>
    </section>

    <section class="space-y-5" aria-hidden="true">
        <div class="flex items-end justify-between gap-4">
            <div class="skeleton h-8 w-56 rounded-2xl sm:h-10"></div>
            <div class="skeleton h-10 w-40 rounded-2xl"></div>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            @foreach (range(1, 4) as $_)
                <x-hackaton-card-skeleton />
            @endforeach
        </div>
    </section>

    <section class="ui-surface-card p-6 sm:p-8" aria-hidden="true">
        <div class="skeleton h-8 w-64 rounded-2xl sm:h-10"></div>
        <div class="mt-3 space-y-2">
            <div class="skeleton h-4 w-5/6 max-w-2xl rounded-xl"></div>
            <div class="skeleton h-4 w-3/4 max-w-xl rounded-xl"></div>
        </div>
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-3 sm:gap-6">
            @foreach (range(1, 3) as $_)
                <div class="ui-surface-soft-muted rounded-2xl border border-base-300/80 p-6 text-center sm:p-7">
                    <div class="mx-auto mb-5 skeleton h-20 w-20 rounded-2xl"></div>
                    <div class="skeleton mx-auto h-4 w-24 rounded-xl"></div>
                    <div class="skeleton mx-auto mt-4 h-12 w-28 rounded-2xl"></div>
                </div>
            @endforeach
        </div>
    </section>
</div>
