const AUTOPLAY_MS = 7000;
const SWIPE_THRESHOLD = 50;

export function initHeroSlider(root) {
    const slides = [...root.querySelectorAll('[data-hero-slide]')];

    if (slides.length < 2) {
        return;
    }

    const captions = [...root.querySelectorAll('[data-hero-caption]')];
    const dots = [...root.querySelectorAll('[data-hero-dot]')];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let current = 0;
    let timer = null;

    const show = (index) => {
        current = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            const active = i === current;

            slide.dataset.active = String(active);
            slide.inert = !active;
        });

        captions.forEach((caption, i) => {
            caption.dataset.active = String(i === current);
            caption.querySelector('a')?.setAttribute('tabindex', i === current ? '0' : '-1');
        });

        dots.forEach((dot, i) => {
            dot.setAttribute('aria-current', String(i === current));
        });
    };

    const stop = () => {
        clearInterval(timer);
        timer = null;
    };

    const start = () => {
        if (reducedMotion || timer || document.hidden) {
            return;
        }

        timer = setInterval(() => show(current + 1), AUTOPLAY_MS);
    };

    const go = (index) => {
        show(index);
        stop();
        start();
    };

    root.querySelector('[data-hero-next]')?.addEventListener('click', () => go(current + 1));
    root.querySelector('[data-hero-prev]')?.addEventListener('click', () => go(current - 1));
    dots.forEach((dot, i) => dot.addEventListener('click', () => go(i)));

    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', start);
    root.addEventListener('focusin', stop);
    root.addEventListener('focusout', start);
    document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));

    root.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') {
            go(current + 1);
        } else if (event.key === 'ArrowRight') {
            go(current - 1);
        }
    });

    let touchStartX = null;

    root.addEventListener('touchstart', (event) => {
        touchStartX = event.touches[0].clientX;
    }, { passive: true });

    root.addEventListener('touchend', (event) => {
        if (touchStartX === null) {
            return;
        }

        const delta = event.changedTouches[0].clientX - touchStartX;
        touchStartX = null;

        if (Math.abs(delta) >= SWIPE_THRESHOLD) {
            go(current + (delta > 0 ? 1 : -1));
        }
    }, { passive: true });

    start();
}
