const globalScrollStyle = document.createElement('style');
globalScrollStyle.innerHTML = `
          html::-webkit-scrollbar, 
          body::-webkit-scrollbar {
            display: none !important;
          }
          html, body {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
          }
        `;
document.head.appendChild(globalScrollStyle);