document.addEventListener("DOMContentLoaded", function () {
    const savedTheme = localStorage.getItem("theme") || "light";
    document.documentElement.setAttribute("data-theme", savedTheme);
    updateToggleButton(savedTheme);
});

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute("data-theme");
    const newTheme = currentTheme === "dark" ? "light" : "dark";

    document.documentElement.setAttribute("data-theme", newTheme);
    localStorage.setItem("theme", newTheme);

    updateToggleButton(newTheme);
}

function updateToggleButton(theme) {
    const btn = document.getElementById("theme-toggle-btn");
    if (!btn) return;

    if (theme === "dark") {
        btn.innerHTML = "☀️ Light";
        btn.setAttribute("title", "Switch to Light Mode");
    } else {
        btn.innerHTML = "🌙 Dark";
        btn.setAttribute("title", "Switch to Dark Mode");
    }
}