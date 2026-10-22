document.addEventListener('DOMContentLoaded', function () {
    const videos = document.querySelectorAll('.preview-video-trigger');

    videos.forEach(video => {
        video.addEventListener('click', function () {
            if (!video.paused) {
                video.pause();
            } else {
                videos.forEach(otherVideo => {
                    if (otherVideo !== video) {
                        otherVideo.pause();
                        otherVideo.currentTime = 0;
                    }
                });

                video.muted = false;
                video.play().catch(error => {
                    console.log("Autoplay with sound blocked by browser before user interaction: ", error);
                });
            }
        });

        video.addEventListener('ended', function () {
            video.currentTime = 0;
        });
    });
});