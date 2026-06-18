/**
 * Sets up an accessible tab group driven by data attributes.
 *
 * Triggers: [data-tab-trigger="<group>"][data-tab-value="<value>"]
 * Panels:   [data-tab-panel="<group>"][data-tab-value="<value>"]
 *
 * Supports two trigger styles:
 *   - DaisyUI `.tab` — toggles `tab-active`
 *   - Button group — toggles `btn-primary` / `btn-ghost`
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
        activeTab = primaryTriggers[0].dataset.tabValue;
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
        if (replace) {
            history.replaceState(null, '', nextHash);
        } else {
            history.pushState(null, '', nextHash);
        }
    };

    allTriggers.forEach((trigger) => {
        trigger.addEventListener('click', () => setActiveTab(trigger.dataset.tabValue));
    });

    primaryTriggers.forEach((trigger) => {
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
