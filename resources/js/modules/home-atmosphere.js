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
