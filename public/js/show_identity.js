const APP_BASE_PATH = (typeof window.APP_BASE_PATH === 'string' ? window.APP_BASE_PATH : '').replace(/\/+$/, '');
let lastStateSignature = null;

function refreshIdentity() {
    fetch(`${APP_BASE_PATH}/api/identity`)
        .then(response => response.json())
        .then(data => {
            if (!data || data.status === 'locked') return;

            const titleEl = document.getElementById('header-title-text');
            if (titleEl && typeof data.title === 'string') {
                titleEl.textContent = data.title;
            }

            const nameEl = document.getElementById('header-name-text');
            if (nameEl && typeof data.name === 'string') {
                nameEl.textContent = data.name;
            }

            const marqueeEl = document.getElementById('marquee-text');
            if (marqueeEl && typeof data.marquee === 'string') {
                marqueeEl.textContent = data.marquee;
            }

            const logoContainer = document.getElementById('logo-container');
            if (logoContainer && data.logoUrl) {
                const fullLogoPath = `${APP_BASE_PATH}/img/logo/${data.logoUrl}`;
                let logoImg = document.getElementById('header-logo-img');

                if (!logoImg) {
                    logoImg = document.createElement('img');
                    logoImg.id = 'header-logo-img';
                    logoImg.className = 'header-logo';
                    logoImg.alt = 'Logo';
                    logoContainer.appendChild(logoImg);
                }

                if (logoImg.getAttribute('src') !== fullLogoPath) {
                    logoImg.src = fullLogoPath;
                }
            }

            // Automatically reload signage screen if rates, deposits, titles, videos, or layout options changed
            if (data.stateSignature) {
                if (lastStateSignature !== null && lastStateSignature !== data.stateSignature) {
                    window.location.reload();
                    return;
                }
                lastStateSignature = data.stateSignature;
            }
        })
        .catch(error => console.error('Error refreshing signage state:', error));
}

refreshIdentity();
setInterval(refreshIdentity, 5000);
