const story = document.querySelector('[data-camry-story]');
if (story) {
    const video = story.querySelector('[data-camry-video]');
    const canvas = story.querySelector('[data-camry-canvas]');
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    const width = canvas.width, height = canvas.height;
    const visited = new Uint8Array(width * height);
    const queue = new Int32Array(width * height);
    const poster = story.querySelector('[data-camry-poster]');
    const copy = story.querySelector('[data-camry-copy]');
    const phase = story.querySelector('[data-camry-phase]');
    const progressBar = story.querySelector('[data-camry-progress]');
    let progress = 0, targetTime = 0, frame = 0, ready = false, failed = !ctx;
    function draw() {
        if (failed || !ctx || video.readyState < 2) return;
        try {
            ctx.drawImage(video, 0, 0, width, height);
            const image = ctx.getImageData(0, 0, width, height), rgba = image.data;
            visited.fill(0);
            let head = 0, tail = 0;
            function push(i) {
                if (visited[i]) return;
                visited[i] = 1;
                const p = i * 4;
                if (rgba[p] < 7 && rgba[p + 1] < 7 && rgba[p + 2] < 7) queue[tail++] = i;
            }
            for (let x = 0; x < width; x++) { push(x); push((height - 1) * width + x); }
            for (let y = 0; y < height; y++) { push(y * width); push(y * width + width - 1); }
            while (head < tail) {
                const i = queue[head++];
                rgba[i * 4 + 3] = 0;
                if (i % width > 0) push(i - 1);
                if (i % width < width - 1) push(i + 1);
                if (i >= width) push(i - width);
                if (i < width * (height - 1)) push(i + width);
            }
            ctx.putImageData(image, 0, 0);
            ready = true;
            updateOpacity();
        } catch { fallback(); }
    }
    function updateOpacity() {
        const blend = ready && !failed ? Math.min(1, progress / .08) : 0;
        canvas.style.opacity = String(blend);
        poster.style.opacity = String(1 - blend);
    }
    function fallback() {
        failed = true;
        canvas.style.opacity = '0';
        poster.style.opacity = '1';
        story.querySelector('[data-camry-fallback]').hidden = false;
    }
    function seek() {
        if (failed || !Number.isFinite(video.duration) || video.seeking) return;
        if (Math.abs(video.currentTime - targetTime) > .035) video.currentTime = targetTime;
    }
    function update() {
        frame = 0;
        const sticky = story.querySelector('.camry-sticky');
        const distance = Math.max(1, story.offsetHeight - sticky.offsetHeight);
        progress = Math.max(0, Math.min(1, -story.getBoundingClientRect().top / distance));
        const shift = failed ? 0 : Math.max(0, Math.min(1, (progress - .25) / .3));
        story.style.setProperty('--camry-shift', shift.toFixed(3));
        copy.setAttribute('aria-hidden', String(shift < .5));
        progressBar.style.transform = `scaleX(${progress})`;
        phase.textContent = progress < .25 ? '01 — NGOẠI THẤT' : progress < .7 ? '02 — TỪNG CHI TIẾT' : '03 — GÓC NHÌN MỚI';
        // Hold the exploded composition selected by the user; do not show the
        // remaining source clip. Reverse scrolling still reassembles the car.
        const endTime = Number.isFinite(video.duration) ? Math.min(8, video.duration - .08) : 0;
        targetTime = Math.min(endTime, progress * 11);
        updateOpacity();
        seek();
    }
    function schedule() { if (!frame) frame = requestAnimationFrame(update); }
    video.muted = true;
    video.addEventListener('loadeddata', () => { draw(); schedule(); });
    video.addEventListener('seeked', () => { draw(); seek(); });
    video.addEventListener('error', fallback);
    video.addEventListener('loadedmetadata', schedule);
    window.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule);
    if (video.readyState >= 2) draw();
    // PHP's development server does not return HTTP byte ranges for MP4.
    // A fully downloaded local Blob gives the browser a seekable timeline.
    fetch(video.querySelector('source').src)
        .then((response) => {
            if (!response.ok) throw new Error('Video unavailable');
            return response.blob();
        })
        .then((blob) => {
            const url = URL.createObjectURL(blob);
            video.src = url;
            video.load();
            window.addEventListener('pagehide', (event) => { if (!event.persisted) URL.revokeObjectURL(url); });
        })
        .catch(fallback);
    schedule();
}
