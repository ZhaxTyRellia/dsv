function showToast(message, type = 'success') {
    const eksekusiToast = function () {
        const container = document.getElementById('toast-container');
        if (!container) {
            setTimeout(eksekusiToast, 50);
            return;
        }

        let iconSvg = '';
        if (type === 'success') {
            iconSvg = `<circle cx="12" cy="12" r="12" fill="#bbf7d0"/><path d="M8 12l3 3 6-6" stroke="#22c55e" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>`;
        } else if (type === 'danger') {
            iconSvg = `<circle cx="12" cy="12" r="12" fill="#fee2e2"/><path d="M12 8v4M12 16h.01" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>`;
        } else {
            iconSvg = `<circle cx="12" cy="12" r="12" fill="#fef08a"/><path d="M12 8v4M12 16h.01" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>`;
        }

        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-body">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="toast-icon">${iconSvg}</svg>
                <span class="toast-message">${message}</span>
                <span class="toast-close" onclick="this.closest('.toast-notification').remove();">&times;</span>
            </div>
            <div class="toast-progress-bar bar-${type}"></div>
        `;

        container.appendChild(toast);

        setTimeout(function () {
            toast.classList.add('toast-fade-out');
            setTimeout(function () {
                toast.remove();
            }, 400);
        }, 5000);
    };

    eksekusiToast();
}