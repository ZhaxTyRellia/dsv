const logoInput = document.getElementById('logo-input');

if (logoInput) {
    logoInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById('logo-preview');
        
        if (file && preview) {
            const url = URL.createObjectURL(file);
            preview.src = url;
            
            preview.classList.remove('inner-modal-logo-preview-hidden');
            
            preview.onload = function() {
                URL.revokeObjectURL(url);
            };
        } else if (preview) {
            preview.src = '';
            preview.classList.add('inner-modal-logo-preview-hidden');
        }
    });
}