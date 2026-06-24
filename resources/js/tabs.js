/** @type {Map<string, { setActiveTab: (tabValue: string, replace?: boolean) => void }>} */
const tabControllers = new Map();

let delegationBound = false;

/**
 * Sets up an accessible tab group driven by data attributes.
 *
 * Triggers: [data-tab-trigger="<group>"][data-tab-value="<value>"]
 * Panels:   [data-tab-panel="<group>"][data-tab-value="<value>"]
 *
 * @param {string} groupName
 * @param {string} fallbackTab
 * @returns {{ setActiveTab: (tabValue: string, replace?: boolean) => void }}
 */
export function setupTabGroup(groupName, fallbackTab) {
    const allTriggers = Array.from(document.querySelectorAll(`[data-tab-trigger="${groupName}"]`));
    const panels = Array.from(document.querySelectorAll(`[data-tab-panel="${groupName}"]`));

    if (allTriggers.length === 0 || panels.length === 0) {
        return { setActiveTab: () => {} };
    }

    const primaryTriggers = (() => {
        const visible = allTriggers.filter((t) => t.offsetParent !== null);
        const source = visible.length > 0 ? visible : allTriggers;
        const byValue = new Map();
        source.forEach((t) => {
            const v = t.dataset.tabValue;
            if (v && !byValue.has(v)) {
                byValue.set(v, t);
            }
        });
        return Array.from(byValue.values());
    })();

    const availableTabs = new Set(allTriggers.map((trigger) => trigger.dataset.tabValue));
    let hash = window.location.hash;
    if (hash === '#organizer-team-applications') {
        hash = '#hackaton-tab-participants';
    }
    const hashPrefix = `#${groupName}-tab-`;
    const requestedTab = hash.startsWith(hashPrefix) ? hash.slice(hashPrefix.length) : null;
    let activeTab = requestedTab && availableTabs.has(requestedTab) ? requestedTab : fallbackTab;

    if (!availableTabs.has(activeTab)) {
        activeTab = primaryTriggers[0]?.dataset.tabValue ?? fallbackTab;
    }

    const setActiveTab = (tabValue, replace = false) => {
        if (!availableTabs.has(tabValue)) {
            return;
        }

        allTriggers.forEach((trigger) => {
            const isActive = trigger.dataset.tabValue === tabValue;
            if (trigger.classList.contains('tab')) {
                trigger.classList.toggle('tab-active', isActive);
            } else {
                trigger.classList.toggle('btn-primary', isActive);
                trigger.classList.toggle('btn-ghost', !isActive);
            }
            trigger.setAttribute('aria-selected', isActive ? 'true' : 'false');
            trigger.tabIndex = isActive ? 0 : -1;
        });

        panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.tabValue !== tabValue);
        });

        const nextHash = `${hashPrefix}${tabValue}`;
        const nextUrl = `${window.location.pathname}${window.location.search}${nextHash}`;
        if (replace) {
            history.replaceState(null, '', nextUrl);
        } else {
            history.pushState(null, '', nextUrl);
        }
    };

    primaryTriggers.forEach((trigger) => {
        if (trigger.dataset.tabKeybound === 'true') {
            return;
        }

        trigger.dataset.tabKeybound = 'true';
        trigger.addEventListener('keydown', (event) => {
            if (!['ArrowRight', 'ArrowLeft', 'Home', 'End'].includes(event.key)) {
                return;
            }
            event.preventDefault();
            const index = primaryTriggers.indexOf(trigger);
            if (index === -1) {
                return;
            }
            let nextIndex = index;
            if (event.key === 'ArrowRight') {
                nextIndex = (index + 1) % primaryTriggers.length;
            } else if (event.key === 'ArrowLeft') {
                nextIndex = (index - 1 + primaryTriggers.length) % primaryTriggers.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else if (event.key === 'End') {
                nextIndex = primaryTriggers.length - 1;
            }
            const nextTrigger = primaryTriggers[nextIndex];
            setActiveTab(nextTrigger.dataset.tabValue);
            nextTrigger.focus();
        });
    });

    setActiveTab(activeTab, true);

    return { setActiveTab };
}

/**
 * Binds organizer shortcut buttons that switch tabs and optionally open modals.
 *
 * @param {ParentNode} root
 * @param {{ setActiveTab: (tabValue: string, replace?: boolean) => void }} controller
 */
function bindOrganizerTabActions(root, controller) {
    root.querySelectorAll('[data-organizer-action="tab"]:not([data-organizer-tab-bound])').forEach((btn) => {
        btn.dataset.organizerTabBound = 'true';
        btn.addEventListener('click', () => {
            const tabValue = btn.getAttribute('data-tab-target');
            if (tabValue) {
                controller.setActiveTab(tabValue);
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
}

/**
 * Initializes tab groups declared via data-tab-init on a root element.
 *
 * @param {ParentNode} [root=document]
 */
export function initTabGroups(root = document) {
    root.querySelectorAll('[data-tab-init]:not([data-tabs-initialized])').forEach((el) => {
        const groupName = el.getAttribute('data-tab-init');
        const fallback = el.getAttribute('data-tab-fallback') ?? '';

        if (!groupName) {
            return;
        }

        el.setAttribute('data-tabs-initialized', 'true');

        const controller = setupTabGroup(groupName, fallback);
        tabControllers.set(groupName, controller);
        bindOrganizerTabActions(el, controller);
    });
}

/**
 * Registers delegated click handlers once for all tab groups.
 */
export function bootstrapTabSystem() {
    if (delegationBound) {
        return;
    }

    delegationBound = true;

    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof Element)) {
            return;
        }

        const trigger = target.closest('[data-tab-trigger]');
        if (!trigger) {
            return;
        }

        const groupName = trigger.getAttribute('data-tab-trigger');
        const tabValue = trigger.getAttribute('data-tab-value');
        const controller = groupName ? tabControllers.get(groupName) : undefined;

        if (controller && tabValue) {
            controller.setActiveTab(tabValue);
        }
    });
}
