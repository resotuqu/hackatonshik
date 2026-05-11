    @section('title', $hackaton->title)
    @section('meta_description', $seoDescription)
    @section('canonical_url', route('hackatons.show', $hackaton))
    @if ($heroImage)
        @section('og_image', $heroImage)
    @endif

    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="/">Главная</a></li>
                <li><a href="/hackatons">Хакатоны</a></li>
                <li class="opacity-70">{{ $hackaton->title }}</li>
            </ul>
        </div>

        <div class="tabs tabs-boxed w-full overflow-x-auto scroll-smooth rounded-2xl border border-base-300/60 bg-base-200/50 p-1 shadow-inner focus-within:ring-2 focus-within:ring-primary/30 focus-within:ring-offset-2" role="tablist" aria-label="Разделы хакатона" data-tab-list="hackaton">
            <button type="button" class="tab tab-active" role="tab" aria-selected="true" aria-controls="hackaton-panel-description" data-tab-trigger="hackaton" data-tab-value="description">
                Описание
            </button>
            <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="hackaton-panel-documents" data-tab-trigger="hackaton" data-tab-value="documents">
                Документы
            </button>
            <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="hackaton-panel-announcements" data-tab-trigger="hackaton" data-tab-value="announcements">
                Анонсы
            </button>
            <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="hackaton-panel-cases" data-tab-trigger="hackaton" data-tab-value="cases">
                Кейсы
            </button>
            <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="hackaton-panel-participants" data-tab-trigger="hackaton" data-tab-value="participants">
                Участники
            </button>
            @if($isOrganizer || $isAssignedJudge)
                <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="hackaton-panel-organization" data-tab-trigger="hackaton" data-tab-value="organization">
                    Организация
                </button>
            @endif
        </div>

        @include('pages.hackatons.partials.show.description')
        @include('pages.hackatons.partials.show.documents')
        @include('pages.hackatons.partials.show.announcements')

        <section id="hackaton-panel-cases" role="tabpanel" class="hidden space-y-4" data-tab-panel="hackaton" data-tab-value="cases">
            <livewire:hackatons.show-cases-panel
                :hackaton="$hackaton"
                :isOrganizer="$isOrganizer"
                :isAssignedJudge="$isAssignedJudge"
                :submitterTeams="$submitterTeams"
                :fieldTypeLabels="$fieldTypeLabels" />
        </section>

        @if($isOrganizer || $isAssignedJudge)
            <section id="hackaton-panel-organization" role="tabpanel" class="hidden space-y-6" data-tab-panel="hackaton" data-tab-value="organization">
                <livewire:hackatons.show-organization-panel
                    :hackaton="$hackaton"
                    :isOrganizer="$isOrganizer"
                    :isAssignedJudge="$isAssignedJudge"
                    :metrics="$metrics"
                    :leaderboard="$leaderboard"
                    :judgeCandidates="$judgeCandidates"
                    :pendingJudgeInvitations="$pendingJudgeInvitations"
                    :participantUsers="$participantUsers"
                    :issuedCertificatesByUser="$issuedCertificatesByUser"
                    :modals="$modals" />
            </section>
        @endif

        <section id="hackaton-panel-participants" role="tabpanel" class="hidden" data-tab-panel="hackaton" data-tab-value="participants">
            <livewire:hackatons.show-applications-panel
                :hackaton="$hackaton"
                :isOrganizer="$isOrganizer"
                :applications="$applications"
                :applicationStatusFilter="$applicationStatusFilter" />
        </section>
    </div>

    <script>
        (function () {
            const setupTabGroup = (groupName, fallbackTab) => {
                const triggers = Array.from(document.querySelectorAll(`[data-tab-trigger="${groupName}"]`));
                const panels = Array.from(document.querySelectorAll(`[data-tab-panel="${groupName}"]`));

                if (triggers.length === 0 || panels.length === 0) {
                    return;
                }

                const availableTabs = new Set(triggers.map((trigger) => trigger.dataset.tabValue));
                const hash = window.location.hash;
                const hashPrefix = `#${groupName}-tab-`;
                const requestedTab = hash.startsWith(hashPrefix) ? hash.slice(hashPrefix.length) : null;
                let activeTab = requestedTab && availableTabs.has(requestedTab) ? requestedTab : fallbackTab;

                if (!availableTabs.has(activeTab)) {
                    activeTab = triggers[0].dataset.tabValue;
                }

                const setActiveTab = (tabValue, replace = false) => {
                    if (!availableTabs.has(tabValue)) {
                        return;
                    }

                    triggers.forEach((trigger) => {
                        const isActive = trigger.dataset.tabValue === tabValue;
                        trigger.classList.toggle('tab-active', isActive);
                        trigger.setAttribute('aria-selected', isActive ? 'true' : 'false');
                        trigger.tabIndex = isActive ? 0 : -1;
                    });

                    panels.forEach((panel) => {
                        panel.classList.toggle('hidden', panel.dataset.tabValue !== tabValue);
                    });

                    const nextHash = `${hashPrefix}${tabValue}`;
                    if (replace) {
                        history.replaceState(null, '', nextHash);
                    } else {
                        history.pushState(null, '', nextHash);
                    }
                };

                triggers.forEach((trigger) => {
                    trigger.addEventListener('click', () => setActiveTab(trigger.dataset.tabValue));
                    trigger.addEventListener('keydown', (event) => {
                        if (!['ArrowRight', 'ArrowLeft', 'Home', 'End'].includes(event.key)) {
                            return;
                        }
                        event.preventDefault();
                        const index = triggers.indexOf(trigger);
                        if (index === -1) {
                            return;
                        }
                        let nextIndex = index;
                        if (event.key === 'ArrowRight') {
                            nextIndex = (index + 1) % triggers.length;
                        } else if (event.key === 'ArrowLeft') {
                            nextIndex = (index - 1 + triggers.length) % triggers.length;
                        } else if (event.key === 'Home') {
                            nextIndex = 0;
                        } else if (event.key === 'End') {
                            nextIndex = triggers.length - 1;
                        }
                        const nextTrigger = triggers[nextIndex];
                        setActiveTab(nextTrigger.dataset.tabValue);
                        nextTrigger.focus();
                    });
                });

                setActiveTab(activeTab, true);
            };

            setupTabGroup('hackaton', 'description');

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