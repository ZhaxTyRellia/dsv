document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const isPreview = urlParams.has('preview_theme');
    const availableThemes = ['show-0.css', 'show-1.css', 'show-2.css', 'show-3.css'];
    const appBasePath = (typeof window.APP_BASE_PATH === 'string' ? window.APP_BASE_PATH : '').replace(/\/+$/, '');
    const themeLink = document.getElementById('theme-stylesheet');

    let configuredMode = document.body.getAttribute('data-auto-cycle-themes') === '1' ? 'auto-cycle' : '';
    let currentCssFile = 'show-0.css';
    if (themeLink && themeLink.getAttribute('href')) {
        const parts = themeLink.getAttribute('href').split('/');
        currentCssFile = parts[parts.length - 1].split('?')[0];
    }

    function applyCssTheme(cssFile) {
        if (!themeLink || !availableThemes.includes(cssFile)) return;
        if (currentCssFile !== cssFile) {
            currentCssFile = cssFile;
            themeLink.href = appBasePath + '/css/' + cssFile + '?v=' + new Date().getTime();
        }
    }

    // Automatic Theme Rotation Cycle (every 25 seconds when auto-cycle is enabled)
    let cycleIdx = Math.max(0, availableThemes.indexOf(currentCssFile));
    setInterval(function () {
        if (configuredMode === 'auto-cycle') {
            cycleIdx = (cycleIdx + 1) % availableThemes.length;
            applyCssTheme(availableThemes[cycleIdx]);
        }
    }, 25000);

    if (isPreview) {
        return;
    }

    function checkDatabaseTheme() {
        fetch(appBasePath + '/api/themes')
            .then(response => response.json())
            .then(data => {
                if (data && data.status === 'success' && data.theme) {
                    if (data.theme === 'auto-cycle') {
                        configuredMode = 'auto-cycle';
                    } else {
                        configuredMode = data.theme;
                        applyCssTheme(data.theme);
                    }
                }
            })
            .catch(err => console.error('Error checking theme:', err));
    }

    setInterval(checkDatabaseTheme, 4000);
});
