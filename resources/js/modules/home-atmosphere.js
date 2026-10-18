// Reveal once; content remains visible without JavaScript or motion support.
const detailSections = document.querySelectorAll('[data-home-reveal]');
if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const detailObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            detailObserver.unobserve(entry.target);
        });
    }, { threshold: 0.12 });
    detailSections.forEach((section) => {
        section.classList.add('reveal-ready');
        detailObserver.observe(section);
    });
}

const atmosphereStory = document.querySelector('[data-camry-story]');
if (atmosphereStory) {
    let onScreen = true;
    function syncAtmosphere() {
        atmosphereStory.classList.toggle('effects-paused', document.hidden || !onScreen);
    }
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(([entry]) => {
            onScreen = entry.isIntersecting;
            syncAtmosphere();
        });
        observer.observe(atmosphereStory);
    }
    document.addEventListener('visibilitychange', syncAtmosphere);
    syncAtmosphere();
}
