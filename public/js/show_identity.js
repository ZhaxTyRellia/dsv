function refreshIdentity() {
    fetch('/dsv/api/identity')
        .then(response => response.json())
        .then(data => {
            document.getElementById('header-title-text').textContent = data.title;

            document.getElementById('header-name-text').textContent = data.name;
            
            document.getElementById('marquee-text').textContent = data.marquee;

            const logoContainer = document.getElementById('logo-container');
            if (data.logoUrl) {
                const fullLogoPath = `/dsv/img/logo/${data.logoUrl}`;
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
            } else {
                logoContainer.innerHTML = '';
            }
        })
        .catch(error => console.error('Error refresh identity:', error));
}

setInterval(refreshIdentity, 5000);