<!-- LOADING SCREEN -->
<style>
    #loading-screen {
        position: fixed;
        z-index: 9999;
        inset: 0;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.5s;
    }

    #loading-screen.hide {
        opacity: 0;
        pointer-events: none;
    }

    .loading-svg {
        width: 70px;
        height: 70px;
        animation: pulse 1.2s infinite alternate;
    }

    @keyframes pulse {
        0% {
            filter: drop-shadow(0 0 0 #d9003c);
        }

        100% {
            filter: drop-shadow(0 0 12px #d9003c);
        }
    }

    .loading-text {
        margin-top: 18px;
        text-align: center;
        font-size: 1.1em;
        color: #d9003c;
        font-family: Arial, sans-serif;
        letter-spacing: 1px;
        animation: fadein 1.2s infinite alternate;
    }

    @keyframes fadein {
        0% {
            opacity: 0.6;
        }

        100% {
            opacity: 1;
        }
    }
</style>
<div id="loading-screen">
    <div style="display: flex; flex-direction: column; align-items: center;">
        <svg class="loading-svg" viewBox="0 0 50 50">
            <circle cx="25" cy="25" r="20" fill="none" stroke="#d9003c" stroke-width="5" stroke-linecap="round"
                stroke-dasharray="31.4 31.4" stroke-dashoffset="0">
                <animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="1s"
                    repeatCount="indefinite" />
            </circle>
        </svg>
        <div class="loading-text">Loading .....</div>
    </div>
</div>
<script>
    window.addEventListener('load', function() {
        const loading = document.getElementById('loading-screen');
        if (loading) {
            loading.classList.add('hide');
            setTimeout(() => loading.style.display = 'none', 500);
        }
    });
</script>