document.addEventListener('DOMContentLoaded', function () {
    // --- DOM Elements ---
    const displayCard = document.getElementById('display-card');
    const statusEl = document.getElementById('status');
    const loveBtn = document.getElementById('loveBtn');
    const shareBtn = document.getElementById('shareBtn');
    const heartContainer = document.getElementById('heart-container');
    const previewNameText = document.getElementById('previewNameText');
    const reactionButtons = document.querySelectorAll('.reaction-btn');

    // --- State Variables ---
    let startTime, elapsedTime = 0,
        timerInterval, running = false;
    let currentVideoId = null;

    // --- Functions ---
    function formatTime(time) {
        const h = String(Math.floor(time / 3600000)).padStart(2, '0');
        const m = String(Math.floor((time % 3600000) / 60000)).padStart(2, '0');
        const s = String(Math.floor((time % 60000) / 1000)).padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    function updatePreviewName(name) {
        const displayName = name || '....';
        if (previewNameText) {
            previewNameText.textContent = displayName;
        }
    }

    function createFloatingHeart(x, y) {
        if (!heartContainer) return;
        const heart = document.createElement('div');
        heart.className = 'floating-heart';
        heart.innerHTML = '❤️';
        heart.style.left = `${x}px`;
        heart.style.top = `${y}px`;
        heartContainer.appendChild(heart);
        setTimeout(() => heart.remove(), 2000);
    }

    function createFloatingShare(x, y) {
        if (!heartContainer) return;
        const shareIcon = document.createElement('div');
        shareIcon.className = 'floating-share';
        shareIcon.innerHTML = '<i class="fas fa-share-alt" style="color: #0b5ac9ff;"></i>';
        shareIcon.style.left = `${x}px`;
        shareIcon.style.top = `${y}px`;
        heartContainer.appendChild(shareIcon);
        setTimeout(() => shareIcon.remove(), 2000);
    }

    function createFloatingResponse(x, y, emoji) {
        if (!heartContainer) return;
        const responseEmoji = document.createElement('div');
        responseEmoji.className = 'floating-response';
        responseEmoji.innerHTML = emoji;
        responseEmoji.style.left = `${x}px`;
        responseEmoji.style.top = `${y}px`;
        heartContainer.appendChild(responseEmoji);
        setTimeout(() => responseEmoji.remove(), 2000);
    }

    function updateTime() {
        elapsedTime = Date.now() - startTime;
        if (displayCard) displayCard.innerHTML = formatTime(elapsedTime);
    }

    function setButtonsState(enabled) {
        if (loveBtn) loveBtn.disabled = !enabled;
        if (shareBtn) shareBtn.disabled = !enabled;

        const hasResponded = sessionStorage.getItem('hasResponded') === 'true';
        reactionButtons.forEach(button => {
            button.disabled = !enabled || hasResponded;
        });
    }

    function startTimer(serverStartTime, serverElapsedTime) {
        if (running) return;
        if (statusEl) statusEl.textContent = "Stopwatch sedang berjalan...";
        elapsedTime = parseInt(serverElapsedTime, 10) || 0;
        startTime = Date.now() - elapsedTime;
        clearInterval(timerInterval);
        updateTime();
        timerInterval = setInterval(updateTime, 1000);
        running = true;
        setButtonsState(true);
    }

    function stopTimer() {
        clearInterval(timerInterval);
        running = false;
        setButtonsState(false);
        if (statusEl) statusEl.textContent = "Sesi berakhir, mengalihkan ke halaman komentar...";

        if (currentVideoId) {
            window.location.href = `${APP_CONFIG.baseUrl}user/previewcomments/${currentVideoId}`;
        } else {
            console.error("Gagal mengalihkan: Video ID tidak ditemukan.");
            if (statusEl) statusEl.textContent = "Error: Sesi tidak valid.";
        }
    }

    async function sendUserActivity(type, value = '') {
        if (!running) return;
        const formData = new FormData();
        formData.append('type', type);
        formData.append('time', elapsedTime);
        formData.append('value', value);
        formData.append('video_id', currentVideoId);
        formData.append(APP_CONFIG.csrfTokenName, APP_CONFIG.csrfHash);

        try {
            await fetch(`${APP_CONFIG.baseUrl}user/broadcast_activity`, {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Gagal mengirim aktivitas:', error);
        }
    }

    // --- Event Listeners ---
    if (loveBtn) {
        loveBtn.addEventListener('click', () => {
            if (!running) return;
            const rect = loveBtn.getBoundingClientRect();
            createFloatingHeart(rect.left + rect.width / 2, rect.top + rect.height / 2);
            sendUserActivity('love');
        });
    }

    if (shareBtn) {
        shareBtn.addEventListener('click', () => {
            if (!running) return;
            const rect = shareBtn.getBoundingClientRect();
            createFloatingShare(rect.left + rect.width / 2, rect.top + rect.height / 2);
            sendUserActivity('share');
        });
    }

    reactionButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (!running || this.disabled) return;
            const rect = this.getBoundingClientRect();
            const emoji = this.querySelector('span').textContent;
            createFloatingResponse(rect.left + rect.width / 2, rect.top + rect.height / 2, emoji);
            sendUserActivity('response', this.dataset.reaction);
            sessionStorage.setItem('hasResponded', 'true');
            reactionButtons.forEach(btn => {
                btn.disabled = true;
            });
        });
    });

    // --- Pusher Connection ---
    try {
        const pusher = new Pusher(APP_CONFIG.pusherKey, {
            cluster: APP_CONFIG.pusherCluster
        });
        const channel = pusher.subscribe('stopwatch-channel');

        channel.bind('preview-name-update', (data) => {
            sessionStorage.removeItem('hasResponded');
            sessionStorage.removeItem('hasGivenPreference');
            sessionStorage.setItem('surveyName', data.name);
            updatePreviewName(data.name);

            // --- PERBAIKAN DI SINI ---
            // Setelah nama di-set, perbarui status tombol.
            // Jika stopwatch sudah berjalan (running === true), maka tombol akan aktif.
            setButtonsState(running);
        });

        channel.bind('session-update', (data) => {
            // Admin has saved a report and created a new session.
            // Update the video ID on the client to match the new session.
            currentVideoId = data.video_id || null;
        });

        channel.bind('start-event', (data) => {
            currentVideoId = data.video_id || null;
            startTimer(data.startTime, data.elapsedTime);
        });

        channel.bind('pause-event', () => {
            if (running) {
                clearInterval(timerInterval);
                running = false;
                if (statusEl) statusEl.textContent = "Stopwatch dijeda...";
            }
        });

        channel.bind('stop-event', stopTimer);

        channel.bind('reset-event', () => {
            sessionStorage.removeItem('surveyName');
            sessionStorage.removeItem('sessionCompleted');
            sessionStorage.removeItem('hasResponded');
            sessionStorage.removeItem('hasGivenPreference');
            window.location.href = `${APP_CONFIG.baseUrl}user`;
        });
    } catch (e) {
        console.error("Error saat inisialisasi Pusher:", e);
        if (statusEl) statusEl.textContent = "Gagal terhubung ke server.";
    }

    // --- Initial State ---
    const savedName = sessionStorage.getItem('surveyName');
    updatePreviewName(savedName);

    if (sessionStorage.getItem('hasResponded') === 'true') {
        reactionButtons.forEach(button => {
            button.disabled = true;
        });
    }

    setButtonsState(false);
});