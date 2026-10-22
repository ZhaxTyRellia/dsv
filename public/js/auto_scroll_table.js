function initTableAutoscroll() {
    const container = document.getElementById('kursTableContainer');
    if (!container) return;

    let scrollSpeed = 1;
    let isPaused = false;

    function pageScroll() {
        if (isPaused) return;

        if (container.scrollHeight <= container.clientHeight) return;

        if (container.scrollTop + container.clientHeight >= container.scrollHeight - 1) {
            isPaused = true;
            setTimeout(() => {
                container.scrollTop = 0;
                isPaused = false;
            }, 2500);
        } else {
            container.scrollTop += scrollSpeed;
        }
    }

    setInterval(pageScroll, 35);
}

window.addEventListener('DOMContentLoaded', () => {
    initTableAutoscroll();
});