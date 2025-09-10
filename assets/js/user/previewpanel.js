document.addEventListener('DOMContentLoaded', function () {
    // --- DOM Elements ---
    const previewNameText = document.getElementById('previewNameText');
    const previewSubmitBtn = document.getElementById('previewSubmitBtn');
    const previewForm = document.querySelector('form[action$="user/submit_preview"]');

    function updatePreviewName(name) {
        const displayName = name || '....';
        if (previewNameText) {
            previewNameText.textContent = displayName;
        }
        if (previewSubmitBtn) {
            previewSubmitBtn.disabled = !name;
        }
    }

    // --- Event Listener untuk Form Submit ---
    if (previewForm) {
        previewForm.addEventListener('submit', function () {
            if (previewSubmitBtn) {
                previewSubmitBtn.disabled = true;
                previewSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sedang Memproses...';
            }
        });
    }

    // --- Pusher Connection ---
    try {
        const pusher = new Pusher(PUSHER_CONFIG.key, {
            cluster: PUSHER_CONFIG.cluster
        });
        const channel = pusher.subscribe('stopwatch-channel');

        channel.bind('preview-name-update', (data) => {
            sessionStorage.setItem('surveyName', data.name);
            updatePreviewName(data.name);
            sessionStorage.removeItem('sessionCompleted');
            sessionStorage.removeItem('hasResponded'); // Membersihkan status respons dari sesi sebelumnya
        });

        channel.bind('reset-event', () => {
            sessionStorage.removeItem('surveyName');
            sessionStorage.removeItem('sessionCompleted');
            window.location.reload();
        });

    } catch (e) {
        console.error("Error saat inisialisasi Pusher:", e);
    }

    const savedName = sessionStorage.getItem('surveyName');
    if (savedName) {
        updatePreviewName(savedName);
    }

    const sessionCompleted = sessionStorage.getItem('sessionCompleted');
    if (sessionCompleted === 'true') {
        if (previewSubmitBtn) {
            previewSubmitBtn.disabled = true;
            previewSubmitBtn.textContent = 'Menunggu Sesi Baru';
        }
    }
});