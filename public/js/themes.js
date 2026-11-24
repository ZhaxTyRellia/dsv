let currentPreviewTheme = 'show-0.css';
let currentPreviewOrientation = 'portrait';

function selectThemeCard(radioInput, themeValue) {
    currentPreviewTheme = themeValue;
    document.querySelectorAll('.theme-card-label').forEach(function (card) {
        card.classList.remove('active-theme');
    });

    const selectedCard = document.getElementById('card-theme-' + themeValue);
    if (selectedCard) {
        selectedCard.classList.add('active-theme');
    }

    updateThemePreview();
}

function setPreviewOrientation(orientation) {
    currentPreviewOrientation = orientation;
    const container = document.getElementById('themePreviewContainer');
    const frame = document.getElementById('themePreviewFrame');
    const btnPortrait = document.getElementById('btnPrevPortrait');
    const btnLandscape = document.getElementById('btnPrevLandscape');

    if (btnPortrait && btnLandscape) {
        btnPortrait.classList.toggle('active-orient', orientation === 'portrait');
        btnLandscape.classList.toggle('active-orient', orientation === 'landscape');
    }

    if (container && frame) {
        if (orientation === 'landscape') {
            container.style.width = '512px';
            container.style.height = '288px';
            frame.style.width = '1920px';
            frame.style.height = '1080px';
            frame.style.transform = 'scale(0.2666)';
        } else {
            container.style.width = '300px';
            container.style.height = '533px';
            frame.style.width = '1080px';
            frame.style.height = '1920px';
            frame.style.transform = 'scale(0.2778)';
        }
    }

    updateThemePreview();
}

function updateThemePreview() {
    const frame = document.getElementById('themePreviewFrame');
    if (frame) {
        const appBasePath = (typeof window.APP_BASE_PATH === 'string' ? window.APP_BASE_PATH : '').replace(/\/+$/, '');
        frame.src = appBasePath + '/show?preview_theme=' + encodeURIComponent(currentPreviewTheme) + '&preview_orientation=' + encodeURIComponent(currentPreviewOrientation);
    }
}
