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

    // Update only visible photos, once per scroll frame; no continuous render loop.
    const visibleDetails = new Set();
    let detailFrame = 0;
    const updateDetailProgress = () => {
        detailFrame = 0;
        visibleDetails.forEach((section) => {
            const photo = section.querySelector('.home-detail-photo');
            if (!photo) return;
            const bounds = photo.getBoundingClientRect();
            const progress = Math.max(0, Math.min(1, (window.innerHeight - bounds.top) / (window.innerHeight + bounds.height)));
            section.style.setProperty('--detail-progress', progress.toFixed(4));
        });
    };
    const scheduleDetailProgress = () => {
        if (!detailFrame && !document.hidden) detailFrame = requestAnimationFrame(updateDetailProgress);
    };
    const motionObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) visibleDetails.add(entry.target);
            else visibleDetails.delete(entry.target);
        });
        scheduleDetailProgress();
    });
    detailSections.forEach((section) => motionObserver.observe(section));
    window.addEventListener('scroll', scheduleDetailProgress, { passive: true });
    window.addEventListener('resize', scheduleDetailProgress);
    document.addEventListener('visibilitychange', scheduleDetailProgress);
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
