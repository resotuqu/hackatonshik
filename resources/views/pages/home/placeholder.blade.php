<div class="mx-auto w-full max-w-7xl space-y-12 sm:space-y-16">
    <section class="relative overflow-hidden rounded-3xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:gap-10">
            <div class="flex-1 space-y-4">
                <div class="skeleton h-10 w-5/6 rounded-2xl sm:h-12"></div>
                <div class="skeleton h-4 w-4/5 rounded-xl"></div>
                <div class="skeleton h-4 w-3/5 rounded-xl"></div>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <div class="skeleton h-12 w-full rounded-2xl sm:w-48"></div>
                    <div class="skeleton h-12 w-full rounded-2xl sm:w-48"></div>
                </div>
            </div>
            <div class="skeleton aspect-square w-full max-w-[20rem] rounded-3xl lg:max-w-[22rem]"></div>
        </div>
    </section>

    <section class="space-y-5">
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

    <section class="rounded-3xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
        <div class="skeleton h-8 w-64 rounded-2xl sm:h-10"></div>
        <div class="mt-3 space-y-2">
            <div class="skeleton h-4 w-5/6 rounded-xl"></div>
            <div class="skeleton h-4 w-3/4 rounded-xl"></div>
        </div>
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-3 sm:gap-6">
            @foreach (range(1, 3) as $_)
                <div class="rounded-2xl border border-base-300/80 bg-base-200/50 p-6 text-center sm:p-7">
                    <div class="mx-auto mb-5 skeleton h-20 w-20 rounded-2xl"></div>
                    <div class="skeleton mx-auto h-4 w-24 rounded-xl"></div>
                    <div class="skeleton mx-auto mt-4 h-12 w-28 rounded-2xl"></div>
                </div>
            @endforeach
        </div>
    </section>
</div>

