function selectThemeCard(radioInput, themeValue) {
    document.querySelectorAll('.theme-card-label').forEach(function (card) {
        card.style.border = '1px solid #cbd5e1';
        card.style.background = '#f8fafc';

        const badge = card.querySelector('.badge-status');
        if (badge) {
            badge.style.display = 'none';
        }
    });

    const selectedCard = document.getElementById('card-theme-' + themeValue);
    if (selectedCard) {
        selectedCard.style.border = '2px solid #2563eb';
        selectedCard.style.background = '#eff6ff';

        const activeBadge = selectedCard.querySelector('.badge-status');
        if (activeBadge) {
            activeBadge.style.display = 'inline-block';
        }
    }

    updateThemePreview(themeValue);
}

function updateThemePreview(themeValue) {
    const frame = document.getElementById('themePreviewFrame');
    if (frame) {
        frame.src = '/dsv/show?preview_theme=' + encodeURIComponent(themeValue);
    }
}
