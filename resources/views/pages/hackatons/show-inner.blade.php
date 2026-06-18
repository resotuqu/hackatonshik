@section('title', $hackaton->title)
@section('meta_description', $seoDescription)
@section('canonical_url', route('hackatons.show', $hackaton))
@if ($heroImage)
    @section('og_image', $heroImage)
@endif

@php
    $tabFallback = $hackatonTabFallback ?? 'description';
    $sidebarOrganizerNav = $isOrganizer && config('hackaton.organizer_show_sidebar');

    $hackatonNavTabs = [
        ['value' => 'description', 'label' => 'Описание', 'controls' => 'hackaton-panel-description'],
        ['value' => 'documents', 'label' => 'Документы', 'controls' => 'hackaton-panel-documents'],
        ['value' => 'announcements', 'label' => 'Анонсы', 'controls' => 'hackaton-panel-announcements'],
        ['value' => 'cases', 'label' => 'Кейсы', 'controls' => 'hackaton-panel-cases'],
        ['value' => 'participants', 'label' => 'Участники', 'controls' => 'hackaton-panel-participants'],
    ];
    if ($isOrganizer || $isAssignedJudge) {
        $hackatonNavTabs[] = ['value' => 'organization', 'label' => 'Организация', 'controls' => 'hackaton-panel-organization'];
    }
@endphp

<div class="mx-auto w-full max-w-7xl space-y-6">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/hackatons">Хакатоны</a></li>
            <li class="opacity-70">{{ $hackaton->title }}</li>
        </ul>
    </div>

    @if($isOrganizer && $lifecyclePresentation)
        <x-hackatons.organizer-lifecycle-bar :presentation="$lifecyclePresentation" />
    @endif

    @if($isOrganizer && $organizerHeaderMetrics)
        <x-hackatons.organizer-header-metrics :metrics="$organizerHeaderMetrics" />
    @endif

    @if($isOrganizer && ! empty($organizerReadinessChecklist))
        <x-hackatons.organizer-readiness-checklist :items="$organizerReadinessChecklist" />
    @endif

    @if($isOrganizer)
        <x-hackatons.organizer-action-center :hackaton="$hackaton" :modals="$modals" />
    @endif

    <div @class(['flex flex-col gap-6', 'lg:flex-row lg:items-start' => $sidebarOrganizerNav])>
        @if($sidebarOrganizerNav)
            <nav
                class="hidden w-full shrink-0 lg:sticky lg:top-20 lg:block lg:w-52"
                aria-label="Разделы хакатона"
            >
                <div class="rounded-2xl border border-base-300/60 bg-base-200/40 p-2">
                    @foreach($hackatonNavTabs as $tab)
                        <button
                            type="button"
                            role="tab"
                            @class([
                                'btn btn-sm mb-1 flex w-full justify-start rounded-xl border-0',
                                'btn-primary' => $tabFallback === $tab['value'],
                                'btn-ghost' => $tabFallback !== $tab['value'],
                            ])
                            aria-selected="{{ $tabFallback === $tab['value'] ? 'true' : 'false' }}"
                            aria-controls="{{ $tab['controls'] }}"
                            data-tab-trigger="hackaton"
                            data-tab-value="{{ $tab['value'] }}"
                        >
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>
            </nav>
        @endif

        <div class="min-w-0 flex-1 space-y-6">
            <div
                @class([
                    'tabs tabs-boxed w-full overflow-x-auto scroll-smooth rounded-2xl border border-base-300/60 bg-base-200/50 p-1 shadow-inner focus-within:ring-2 focus-within:ring-primary/30 focus-within:ring-offset-2',
                    'lg:hidden' => $sidebarOrganizerNav,
                ])
                role="tablist"
                aria-label="Разделы хакатона"
                data-tab-list="hackaton"
            >
                @foreach($hackatonNavTabs as $tab)
                    <button
                        type="button"
                        @class(['tab', 'tab-active' => $tabFallback === $tab['value']])
                        role="tab"
                        aria-selected="{{ $tabFallback === $tab['value'] ? 'true' : 'false' }}"
                        aria-controls="{{ $tab['controls'] }}"
                        data-tab-trigger="hackaton"
                        data-tab-value="{{ $tab['value'] }}"
                    >
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>

            @include('pages.hackatons.partials.show.description')
            @include('pages.hackatons.partials.show.documents')
            @include('pages.hackatons.partials.show.announcements')

            <section id="hackaton-panel-cases" role="tabpanel" @class(['hidden' => $tabFallback !== 'cases', 'space-y-4' => true]) data-tab-panel="hackaton" data-tab-value="cases">
                <livewire:hackatons.show-cases-panel
                    :hackaton="$hackaton"
                    :isOrganizer="$isOrganizer"
                    :isAssignedJudge="$isAssignedJudge"
                    :fieldTypeLabels="$fieldTypeLabels" />
            </section>

            @if($isOrganizer || $isAssignedJudge)
                <section id="hackaton-panel-organization" role="tabpanel" @class(['hidden' => $tabFallback !== 'organization', 'space-y-6' => true]) data-tab-panel="hackaton" data-tab-value="organization">
                    <livewire:hackatons.show-organization-panel
                        :hackaton="$hackaton"
                        :isOrganizer="$isOrganizer"
                        :isAssignedJudge="$isAssignedJudge"
                        :modals="$modals"
                        :organization-preload="$organizationPreload" />
                </section>
            @endif

            <section id="hackaton-panel-participants" role="tabpanel" @class(['hidden' => $tabFallback !== 'participants']) data-tab-panel="hackaton" data-tab-value="participants">
                <livewire:hackatons.show-applications-panel
                    :hackaton="$hackaton"
                    :isOrganizer="$isOrganizer"
                    :applicationStatusFilter="$applicationStatusFilter" />
            </section>
        </div>
    </div>
</div>

<script>
    (function () {
        const hackatonTabs = window.setupTabGroup('hackaton', @json($tabFallback));

        document.querySelectorAll('[data-organizer-action="tab"]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const tabValue = btn.getAttribute('data-tab-target');
                if (tabValue) {
                    hackatonTabs.setActiveTab(tabValue);
                }
                const modalId = btn.getAttribute('data-open-modal');
                if (modalId) {
                    const toggle = document.getElementById(modalId);
                    if (toggle) {
                        toggle.checked = true;
                    }
                }
            });
        });

        const carousels = document.querySelectorAll('[data-image-carousel]');

        carousels.forEach((carousel) => {
            const slides = Array.from(carousel.querySelectorAll('[data-carousel-slide]'));
            const prevButton = carousel.querySelector('[data-carousel-prev]');
            const nextButton = carousel.querySelector('[data-carousel-next]');
            const dots = Array.from(carousel.querySelectorAll('[data-carousel-dot]'));

            if (slides.length <= 1) {
                return;
            }

            let currentIndex = 0;

            const render = (nextIndex) => {
                const normalizedIndex = (nextIndex + slides.length) % slides.length;
                currentIndex = normalizedIndex;

                slides.forEach((slide, slideIndex) => {
                    slide.classList.toggle('hidden', slideIndex !== currentIndex);
                });

                dots.forEach((dot, dotIndex) => {
                    dot.classList.toggle('bg-base-100', dotIndex === currentIndex);
                    dot.classList.toggle('bg-base-100/40', dotIndex !== currentIndex);
                });
            };

            prevButton?.addEventListener('click', () => render(currentIndex - 1));
            nextButton?.addEventListener('click', () => render(currentIndex + 1));

            dots.forEach((dot, dotIndex) => {
                dot.addEventListener('click', () => render(dotIndex));
            });
        });
    })();
</script>

@if ($errors->any() && filled(old('_open_modal')))
    <script>
        (function () {
            const modalId = @json(old('_open_modal'));
            const modalToggle = document.getElementById(modalId);

            if (!modalToggle) {
                return;
            }

            modalToggle.checked = true;

            window.requestAnimationFrame(() => {
                const modal = modalToggle.closest('.inline-block');
                const firstField = modal?.querySelector('input:not([type="hidden"]), textarea, select');
                firstField?.focus();
            });
        })();
    </script>
@endif
