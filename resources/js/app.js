import { jalaliPicker } from './jalali-picker';
import { initSimpleMap, waitForLeaflet } from './geo-map';
import { initExplorer } from './map-explorer';
import { initHeroSlider } from './hero-slider';

function bootGeoMaps() {
    document.querySelectorAll('[data-geo-map]:not([data-geo-map-ready])').forEach(initSimpleMap);
    document.querySelectorAll('[data-map-explorer]:not([data-map-explorer-ready])').forEach(initExplorer);
}

waitForLeaflet(() => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootGeoMaps);
    } else {
        bootGeoMaps();
    }
});

document.querySelectorAll('[data-hero-slider]').forEach(initHeroSlider);

document.querySelectorAll('[data-filter-sheet]').forEach((sheet) => {
    const backdrop = document.querySelector('[data-filter-backdrop]');
    const openers = document.querySelectorAll('[data-filter-open]');

    const setOpen = (open) => {
        sheet.dataset.open = String(open);

        if (backdrop) {
            backdrop.dataset.open = String(open);
        }

        document.documentElement.classList.toggle('overflow-hidden', open);
        openers.forEach((opener) => opener.setAttribute('aria-expanded', String(open)));

        if (open) {
            sheet.querySelector('[data-filter-close]')?.focus();
        } else if (document.activeElement && sheet.contains(document.activeElement)) {
            openers[0]?.focus();
        }
    };

    openers.forEach((opener) => opener.addEventListener('click', () => setOpen(true)));
    sheet.querySelectorAll('[data-filter-close]').forEach((button) => button.addEventListener('click', () => setOpen(false)));
    backdrop?.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && sheet.dataset.open === 'true') {
            setOpen(false);
        }
    });
});

document.querySelectorAll('[data-autosubmit]').forEach((form) => {
    const desktop = window.matchMedia('(min-width: 1024px)');

    form.addEventListener('change', () => {
        if (desktop.matches) {
            form.requestSubmit();
        }
    });
});

document.querySelectorAll('[data-tabs]').forEach((root) => {
    const tabs = root.querySelectorAll('[data-tab]');
    const panels = root.querySelectorAll('[data-tab-panel]');

    const moreLink = root.querySelector('[data-tab-more]');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            tabs.forEach((item) => item.setAttribute('aria-selected', String(item === tab)));
            panels.forEach((panel) => {
                panel.hidden = panel.dataset.tabPanel !== tab.dataset.tab;
            });

            if (moreLink && tab.dataset.moreHref) {
                moreLink.href = tab.dataset.moreHref;
                moreLink.querySelector('[data-tab-more-label]').textContent = tab.dataset.moreLabel;
            }
        });
    });
});

const registerJalaliPicker = () => window.Alpine.data('jalaliPicker', jalaliPicker);

if (window.Alpine) {
    registerJalaliPicker();
} else {
    document.addEventListener('alpine:init', registerJalaliPicker);
}

document.querySelectorAll('[data-pricing]').forEach((root) => {
    const termButtons = root.querySelectorAll('[data-term-btn]');
    const tabs = root.querySelectorAll('[data-tab]');

    const activeKey = () => root.querySelector('[data-tab][aria-selected="true"]')?.dataset.tab;

    const sync = (key = activeKey()) => {
        const panel = root.querySelector(`[data-tab-panel="${key}"]`);
        const offered = (panel?.dataset.offered || '').split(',').filter(Boolean).map(Number);
        let term = Number(root.dataset.term);

        if (offered.length && ! offered.includes(term)) {
            term = offered.includes(12) ? 12 : Math.max(...offered);
            root.dataset.term = String(term);
        }

        termButtons.forEach((button) => {
            const months = Number(button.dataset.termBtn);

            button.hidden = offered.length > 0 && ! offered.includes(months);
            button.setAttribute('aria-checked', String(months === term));
        });

        panel?.querySelectorAll('[data-plan]').forEach((card) => {
            card.dataset.na = String(! card.dataset.terms.split(',').map(Number).includes(term));

            card.querySelectorAll('[data-term-block]').forEach((block) => {
                block.hidden = Number(block.dataset.termBlock) !== term;
            });
        });
    };

    termButtons.forEach((button) => {
        button.addEventListener('click', () => {
            root.dataset.term = button.dataset.termBtn;
            sync();
        });
    });

    tabs.forEach((tab) => tab.addEventListener('click', () => sync(tab.dataset.tab)));
    sync();
});
