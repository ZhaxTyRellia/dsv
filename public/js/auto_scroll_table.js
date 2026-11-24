/**
 * Enterprise BUMN Signage Auto-Slide Carousel & Table Auto-Scroll Engine
 *
 * Supports:
 * - Automatic slide rotation across Forex Board, Active Deposit Boards (1..8), and Summary Matrix
 * - Split-Screen mode (Permanent Forex Board + Auto-Sliding Deposit Boards)
 * - Pinned Forex-Only mode
 * - Smooth vertical table auto-scroll inside active slides
 * - Manual pause / next / prev override controls
 */
(function () {
    function initTableAutoscroll() {
        const boxes = document.querySelectorAll('.js-autoscroll-box');
        boxes.forEach(function (container) {
            let isPaused = false;
            setInterval(function () {
                if (isPaused) return;
                const parentSlide = container.closest('.signage-slide-panel');
                if (parentSlide && !parentSlide.classList.contains('active-slide') && !parentSlide.classList.contains('split-visible')) {
                    return;
                }
                if (container.scrollHeight <= container.clientHeight + 2) return;

                if (container.scrollTop + container.clientHeight >= container.scrollHeight - 2) {
                    isPaused = true;
                    setTimeout(function () {
                        container.scrollTop = 0;
                        isPaused = false;
                    }, 2400);
                } else {
                    container.scrollTop += 1;
                }
            }, 40);
        });
    }

    function initSignageCarousel() {
        const body = document.body;
        const slideMode = body.getAttribute('data-slide-mode') || 'auto';
        const durationSec = Math.max(4, parseInt(body.getAttribute('data-slide-duration') || '10', 10));
        const allSlides = Array.from(document.querySelectorAll('.signage-slide-panel'));
        const indicatorsWrap = document.getElementById('slideIndicators');
        const progressBar = document.getElementById('slideProgressBar');
        const modeBadge = document.getElementById('slideModeBadge');

        if (allSlides.length === 0) return;

        // Determine which slides rotate
        let rotatingSlides = allSlides;
        if (slideMode === 'split') {
            const primaryForex = document.querySelector('.slide-forex-primary');
            if (primaryForex) {
                primaryForex.classList.add('split-visible');
            }
            const depositSlides = Array.from(document.querySelectorAll('.slide-deposit-panel'));
            rotatingSlides = depositSlides.length > 0 ? depositSlides : allSlides;
        } else if (slideMode === 'forex_only') {
            allSlides.forEach(function (s, idx) {
                s.classList.toggle('active-slide', idx === 0);
            });
            if (indicatorsWrap) {
                indicatorsWrap.innerHTML = '<span class="slide-dot active">Forex Counter Board</span>';
            }
            if (progressBar) {
                progressBar.style.width = '100%';
            }
            return;
        }

        let currentIndex = 0;
        let isPaused = false;
        let elapsedMs = 0;
        const tickMs = 100;
        const totalMs = durationSec * 1000;

        function renderIndicators() {
            if (!indicatorsWrap) return;
            indicatorsWrap.innerHTML = '';
            rotatingSlides.forEach(function (slide, idx) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'slide-dot' + (idx === currentIndex ? ' active' : '');
                const title = slide.getAttribute('data-slide-title') || ('Slide ' + (idx + 1));
                btn.textContent = (idx + 1) + '. ' + (title.length > 26 ? title.substring(0, 24) + '…' : title);
                btn.addEventListener('click', function () {
                    goToSlide(idx);
                });
                indicatorsWrap.appendChild(btn);
            });
        }

        function applyActiveSlide() {
            rotatingSlides.forEach(function (slide, idx) {
                const isActive = idx === currentIndex;
                slide.classList.toggle('active-slide', isActive);
                if (isActive) {
                    const scrollBox = slide.querySelector('.js-autoscroll-box');
                    if (scrollBox) scrollBox.scrollTop = 0;
                }
            });
            renderIndicators();
        }

        function goToSlide(index) {
            if (rotatingSlides.length <= 1) return;
            currentIndex = (index + rotatingSlides.length) % rotatingSlides.length;
            elapsedMs = 0;
            applyActiveSlide();
        }

        applyActiveSlide();

        setInterval(function () {
            if (isPaused || rotatingSlides.length <= 1) {
                if (progressBar && rotatingSlides.length <= 1) {
                    progressBar.style.width = '100%';
                }
                return;
            }
            elapsedMs += tickMs;
            if (progressBar) {
                const pct = Math.min(100, (elapsedMs / totalMs) * 100);
                progressBar.style.width = pct + '%';
            }
            if (elapsedMs >= totalMs) {
                goToSlide(currentIndex + 1);
            }
        }, tickMs);

        window.SignageCarousel = {
            next: function () { goToSlide(currentIndex + 1); },
            prev: function () { goToSlide(currentIndex - 1); },
            togglePause: function () {
                isPaused = !isPaused;
                if (modeBadge) {
                    modeBadge.textContent = isPaused ? 'PAUSED' : 'AUTO';
                }
            }
        };
    }

    window.addEventListener('DOMContentLoaded', function () {
        initTableAutoscroll();
        initSignageCarousel();
    });
})();
