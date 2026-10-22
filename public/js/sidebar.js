function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function switchMenu(menuId) {
    document.querySelectorAll('.menu-section').forEach(section => {
        section.classList.remove('active');
    });
    document.querySelectorAll('.nav-item-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    const targetSection = document.getElementById('menu-section-' + menuId);
    if (targetSection) targetSection.classList.add('active');

    const targetBtn = document.querySelectorAll('.nav-item-btn')[menuId - 1];
    if (targetBtn) targetBtn.classList.add('active');

    setCookie("active_sidebar_menu", menuId, 7);

    if (window.innerWidth <= 992) {
        toggleSidebar(false);
    }
}

function toggleSidebar(forceState = null) {
    const sidebar = document.getElementById('sidebarMenu');
    const overlay = document.getElementById('sidebarOverlay');

    if (forceState !== null) {
        if (forceState) {
            sidebar.classList.add('open');
            overlay.classList.add('open');
        } else {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        }
    } else {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    }
}

document.addEventListener("DOMContentLoaded", function () {
    let lastActiveMenu = getCookie("active_sidebar_menu");
    if (lastActiveMenu !== "") {
        switchMenu(parseInt(lastActiveMenu));
    } else {
        switchMenu(1); 
    }
});