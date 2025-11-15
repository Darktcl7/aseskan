document.addEventListener('DOMContentLoaded', function () {
    // --- DOM Elements ---
    const preferenceButtons = document.querySelectorAll('.preference-btn');
    const commentForm = document.getElementById('commentForm');
    const likeBtn = document.getElementById('likeBtn');
    const dislikeBtn = document.getElementById('dislikeBtn');
    const submitCommentBtn = document.getElementById('submitCommentBtn');
    const videoId = document.getElementById('videoId').value;

    // --- State Variables ---
    let isLike = 0;
    let isDislike = 0;

    /**
     * Mengirim data ke server menggunakan Fetch API.
     * @param {string} endpoint - Nama endpoint di controller user (misal: 'record_preference').
     * @param {FormData} formData - Data formulir yang akan dikirim.
     */
    async function sendData(endpoint, formData) {
        try {
            await fetch(`${APP_CONFIG.baseUrl}user/${endpoint}`, {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error(`Gagal mengirim data ke ${endpoint}:`, error);
        }
    }

    /**
     * Menonaktifkan semua tombol preferensi dan menata gayanya setelah salah satu dipilih.
     * @param {HTMLElement|null} clickedButton - Tombol yang diklik oleh pengguna.
     */
    function disableAllPreferenceButtons(clickedButton) {
        preferenceButtons.forEach(btn => {
            btn.disabled = true;
            btn.classList.remove('btn-outline-success', 'btn-outline-warning', 'btn-outline-danger');

            // Beri gaya 'light' pada tombol yang tidak dipilih
            if (clickedButton && btn !== clickedButton) {
                btn.classList.add('btn-light');
            }
        });

        // Beri gaya solid pada tombol yang dipilih
        if (clickedButton) {
            const preference = clickedButton.dataset.preference;
            let solidColorClass = '';
            if (preference === 'senang') solidColorClass = 'btn-success';
            else if (preference === 'biasa') solidColorClass = 'btn-warning';
            else if (preference === 'marah') solidColorClass = 'btn-danger';

            clickedButton.classList.add(solidColorClass, 'text-white');
        }
    }


    // --- Event Listeners ---

    // Event listener untuk tombol-tombol preferensi
    preferenceButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Jangan lakukan apa-apa jika tombol sudah nonaktif
            if (this.disabled) return;

            const preference = this.dataset.preference;

            // Siapkan data untuk dikirim ke server
            const formData = new FormData();
            formData.append('preference_type', preference);
            formData.append('video_id', videoId);
            formData.append(APP_CONFIG.csrfTokenName, APP_CONFIG.csrfHash);
            sendData('record_preference', formData);

            // Simpan status ke sessionStorage agar pilihan tersimpan meski di-refresh
            sessionStorage.setItem('hasGivenPreference', 'true');

            // Nonaktifkan dan ubah gaya semua tombol preferensi
            disableAllPreferenceButtons(this);

            showNotification(`Preferensi Anda (${preference}) telah direkam.`);
        });
    });

    // Event listener untuk tombol Suka (Like)
    if (likeBtn) {
        likeBtn.addEventListener('click', () => {
            if (isLike === 1) {
                // Batal Suka
                isLike = 0;
                likeBtn.classList.replace('btn-success', 'btn-outline-success');
                likeBtn.classList.remove('text-white');
            } else {
                // Suka (dan batal tidak suka jika aktif)
                isLike = 1;
                isDislike = 0;
                likeBtn.classList.replace('btn-outline-success', 'btn-success');
                likeBtn.classList.add('text-white');
                dislikeBtn.classList.replace('btn-danger', 'btn-outline-danger');
                dislikeBtn.classList.remove('text-white');
            }
        });
    }

    // Event listener untuk tombol Tidak Suka (Dislike)
    if (dislikeBtn) {
        dislikeBtn.addEventListener('click', () => {
            if (isDislike === 1) {
                // Batal Tidak Suka
                isDislike = 0;
                dislikeBtn.classList.replace('btn-danger', 'btn-outline-danger');
                dislikeBtn.classList.remove('text-white');
            } else {
                // Tidak Suka (dan batal suka jika aktif)
                isDislike = 1;
                isLike = 0;
                dislikeBtn.classList.replace('btn-outline-danger', 'btn-danger');
                dislikeBtn.classList.add('text-white');
                likeBtn.classList.replace('btn-success', 'btn-outline-success');
                likeBtn.classList.remove('text-white');
            }
        });
    }

    // Event listener untuk pengiriman formulir komentar
    if (commentForm) {
        commentForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            submitCommentBtn.disabled = true;
            submitCommentBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';

            const formData = new FormData(this);
            formData.append('is_like', isLike);
            formData.append('is_dislike', isDislike);

            try {
                const response = await fetch(`${APP_CONFIG.baseUrl}user/save_comment`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.message || 'Terjadi kesalahan pada server.');
                }
                showNotification('Terima kasih! Komentar Anda telah berhasil dikirim.');
                sessionStorage.setItem('sessionCompleted', 'true');

                // Hapus penanda preferensi agar sesi berikutnya bersih
                sessionStorage.removeItem('hasGivenPreference');
                sessionStorage.removeItem('hasResponded'); // Membersihkan status respons dari sesi sebelumnya

                setTimeout(() => {
                    window.location.href = `${APP_CONFIG.baseUrl}user`;
                }, 2000);
            } catch (error) {
                console.error('Gagal mengirim komentar:', error);
                showNotification(error.message, true);
                submitCommentBtn.disabled = false;
                submitCommentBtn.textContent = 'Kirim Komentar';
            }
        });
    }

    // --- Pusher Connection ---
    try {
        const pusher = new Pusher(APP_CONFIG.pusherKey, {
            cluster: APP_CONFIG.pusherCluster
        });
        const channel = pusher.subscribe('stopwatch-channel');

        // Listener untuk event reset dari admin
        channel.bind('reset-event', () => {
            // Hapus semua data sesi yang relevan
            sessionStorage.removeItem('surveyName');
            sessionStorage.removeItem('sessionCompleted');
            sessionStorage.removeItem('hasResponded');
            sessionStorage.removeItem('hasGivenPreference'); // Penting!
            showNotification('Sesi telah direset oleh admin. Anda akan diarahkan kembali.');
            setTimeout(() => {
                window.location.href = `${APP_CONFIG.baseUrl}user`;
            }, 2000);
        });

    } catch (e) {
        console.error("Gagal terhubung ke Pusher di halaman komentar:", e);
        showNotification('Koneksi ke server terputus.', true);
    }

    // --- Initial State ---
    // Saat halaman dimuat, langsung periksa apakah pengguna sudah memberi preferensi
    if (sessionStorage.getItem('hasGivenPreference') === 'true') {
        disableAllPreferenceButtons(null);
    }
});