document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('preview_theme')) {
        return;
    }

    let activeTheme = "";
    const currentThemeLink = document.getElementById('theme-stylesheet');
    if (currentThemeLink && currentThemeLink.getAttribute('href')) {
        const parts = currentThemeLink.getAttribute('href').split('/');
        activeTheme = parts[parts.length - 1].split('?')[0];
    }

    function checkDatabaseTheme() {
        fetch('/dsv/api/themes')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.theme) {
                    if (data.theme !== activeTheme) {
                        activeTheme = data.theme;

                        const themeLink = document.getElementById('theme-stylesheet');
                        if (themeLink) {
                            themeLink.href = '/dsv/css/' + data.theme + '?v=' + new Date().getTime();
                            console.log("Theme automatically updated to: " + data.theme);
                        }
                    }
                }
            })
            .catch(err => console.error("Error checking theme:", err));
    }

    setInterval(checkDatabaseTheme, 3000);
});
