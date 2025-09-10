document.addEventListener('DOMContentLoaded', function () {

    // --- Cek apakah objek konfigurasi ada ---
    if (typeof AppConfig === 'undefined') {
        console.error('AppConfig object is not defined. Make sure to include it in your PHP view.');
        return;
    }

    // --- DOM Elements ---
    const display = document.getElementById('display');
    const startPauseBtn = document.getElementById('startPauseBtn');
    const stopBtn = document.getElementById('stopBtn');
    const resetBtn = document.getElementById('resetBtn');
    const likeReportsTableBody = document.getElementById('like-reports-table');
    const shareCountDisplay = document.getElementById('share-count-display');
    const shareReportsTableBody = document.getElementById('share-reports-table');
    const saveReportForm = document.getElementById('saveReportForm');
    const reportNameInput = document.getElementById('reportName');
    const notificationContainer = document.getElementById('notification');
    const ctx = document.getElementById('likesChart');
    const totalLikesChartDisplay = document.getElementById('total-like-count-chart');
    const responseSenang = document.getElementById('response-senang-count');
    const responseBiasa = document.getElementById('response-biasa-count');
    const responseSedih = document.getElementById('response-sedih-count');
    const realtimeCommentsList = document.getElementById('realtime-comments-list');
    const likeCountDisplay = document.getElementById('like-count');
    const dislikeCountDisplay = document.getElementById('dislike-count');
    const percentageSenangDisplay = document.getElementById('percentage-senang');
    const percentageBiasaDisplay = document.getElementById('percentage-biasa');
    const percentageSedihDisplay = document.getElementById('percentage-sedih');
    const responseCtx = document.getElementById('responseChart');
    const setPreviewNameBtn = document.getElementById('setPreviewNameBtn');
    const maleCountDisplay = document.getElementById('male-count-display');
    const femaleCountDisplay = document.getElementById('female-count-display');
    const averageAgeDisplay = document.getElementById('average-age-display');
    const preferencesChartCtx = document.getElementById('preferencesChart');

    // --- State Variables ---
    let startTime, elapsedTime = 0,
        timerInterval, running = false;
    let likesPerSecond = {};
    let sharesPerSecond = {};
    let shareCount = 0;
    let responseCounts = {
        senang: 0,
        biasa: 0,
        sedih: 0
    };
    let preferenceCounts = {
        senang: 0,
        biasa: 0,
        marah: 0
    };
    let comments = [];
    let commentLikeCount = 0;
    let commentDislikeCount = 0;
    let likesChart, responseChart, preferencesChart;
    let maleCount = 0;
    let femaleCount = 0;
    let totalAge = 0;
    let ageSubmitCount = 0;

    // --- Chart Functions ---
    function initializeChart() {
        if (likesChart) likesChart.destroy();
        if (!ctx) return;

        const gradientLike = ctx.getContext('2d').createLinearGradient(0, 0, 0, 200);
        gradientLike.addColorStop(0, 'rgba(233, 30, 99, 0.5)');
        gradientLike.addColorStop(1, 'rgba(233, 30, 99, 0)');

        const gradientShare = ctx.getContext('2d').createLinearGradient(0, 0, 0, 200);
        gradientShare.addColorStop(0, 'rgba(3, 169, 244, 0.5)');
        gradientShare.addColorStop(1, 'rgba(3, 169, 244, 0)');

        likesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: "Likeable",
                    tension: 0.4,
                    borderColor: "#E91E63",
                    backgroundColor: gradientLike,
                    fill: true,
                    data: [],
                    borderWidth: 2,
                    pointRadius: 2,
                    pointBackgroundColor: '#E91E63',
                    pointHoverRadius: 5
                }, {
                    label: "Shareable",
                    tension: 0.4,
                    borderColor: "#03A9F4",
                    backgroundColor: gradientShare,
                    fill: true,
                    data: [],
                    borderWidth: 2,
                    pointRadius: 2,
                    pointBackgroundColor: '#03A9F4',
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1500,
                    easing: 'easeInOutCubic'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#344767'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Second',
                            color: '#344767',
                            font: {
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            color: '#344767'
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#344767',
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0,0,0,0.7)',
                        titleFont: { size: 14, family: "'Roboto', sans-serif" },
                        bodyFont: { size: 12, family: "'Roboto', sans-serif" }
                    }
                }
            }
        });
    }

    function updateChart() {
        if (!likesChart) return;
        const allSeconds = [...new Set([...Object.keys(likesPerSecond), ...Object.keys(sharesPerSecond)])].map(Number).sort((a, b) => a - b);
        const likeDataPoints = allSeconds.map(sec => likesPerSecond[sec] || 0);
        const shareDataPoints = allSeconds.map(sec => sharesPerSecond[sec] || 0);
        likesChart.data.labels = allSeconds.map(sec => `${sec}s`);
        likesChart.data.datasets[0].data = likeDataPoints;
        likesChart.data.datasets[1].data = shareDataPoints;
        likesChart.update();
        if (totalLikesChartDisplay) {
            totalLikesChartDisplay.textContent = Object.values(likesPerSecond).reduce((sum, count) => sum + count, 0);
        }
    }

    function initializeResponseChart() {
        if (responseChart) responseChart.destroy();
        if (!responseCtx) return;

        const centerTextPlugin = {
            id: 'centerText',
            afterDraw: (chart) => {
                if (chart.tooltip._active && chart.tooltip._active.length > 0) {
                    const activeSegment = chart.tooltip._active[0];
                    const a_dataset = chart.data.datasets[activeSegment.datasetIndex];
                    const a_value = a_dataset.data[activeSegment.index];
                    const total = a_dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = ((a_value / total) * 100).toFixed(1) + '%';

                    const ctx = chart.ctx;
                    const x = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                    const y = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;

                    ctx.save();
                    ctx.font = 'bold 24px Roboto, sans-serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = '#344767';
                    ctx.fillText(percentage, x, y);
                    ctx.restore();
                }
            }
        };

        const gradientGreen = responseCtx.getContext('2d').createLinearGradient(0, 0, 0, 250);
        gradientGreen.addColorStop(0, 'rgba(76, 175, 80, 1)');
        gradientGreen.addColorStop(1, 'rgba(129, 199, 132, 1)');

        const gradientYellow = responseCtx.getContext('2d').createLinearGradient(0, 0, 0, 250);
        gradientYellow.addColorStop(0, 'rgba(255, 193, 7, 1)');
        gradientYellow.addColorStop(1, 'rgba(255, 204, 51, 1)');

        const gradientRed = responseCtx.getContext('2d').createLinearGradient(0, 0, 0, 250);
        gradientRed.addColorStop(0, 'rgba(244, 67, 54, 1)');
        gradientRed.addColorStop(1, 'rgba(239, 83, 80, 1)');

        responseChart = new Chart(responseCtx, {
            type: 'doughnut',
            data: {
                labels: ['Likes', 'Neutral', 'Dislikes'],
                datasets: [{
                    data: [responseCounts.senang, responseCounts.biasa, responseCounts.sedih],
                    backgroundColor: [gradientGreen, gradientYellow, gradientRed],
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                plugins: {
                    centerText: centerTextPlugin,
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12,
                                family: "'Roboto', sans-serif"
                            },
                            padding: 10
                        }
                    },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0,0,0,0.7)',
                        titleFont: { size: 14, family: "'Roboto', sans-serif" },
                        bodyFont: { size: 12, family: "'Roboto', sans-serif" },
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed;
                                }
                                return label;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }

    function initializePreferencesChart() {
        if (preferencesChart) preferencesChart.destroy();
        if (!preferencesChartCtx) return;

        const gradientBlue = preferencesChartCtx.getContext('2d').createLinearGradient(0, 0, 0, 250);
        gradientBlue.addColorStop(0, 'rgba(33, 150, 243, 1)');
        gradientBlue.addColorStop(1, 'rgba(100, 181, 246, 1)');

        const gradientOrange = preferencesChartCtx.getContext('2d').createLinearGradient(0, 0, 0, 250);
        gradientOrange.addColorStop(0, 'rgba(255, 152, 0, 1)');
        gradientOrange.addColorStop(1, 'rgba(255, 183, 77, 1)');

        const gradientPurple = preferencesChartCtx.getContext('2d').createLinearGradient(0, 0, 0, 250);
        gradientPurple.addColorStop(0, 'rgba(156, 39, 176, 1)');
        gradientPurple.addColorStop(1, 'rgba(186, 104, 200, 1)');

        preferencesChart = new Chart(preferencesChartCtx, {
            type: 'bar',
            data: {
                labels: ['Satisfied', 'Normal', 'Disappointed'],
                datasets: [{
                    label: 'Jumlah Preferensi',
                    data: [preferenceCounts.senang, preferenceCounts.biasa, preferenceCounts.marah],
                    backgroundColor: [gradientBlue, gradientOrange, gradientPurple],
                    borderWidth: 0,
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1200,
                    easing: 'easeOutBounce'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#344767'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#344767',
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0,0,0,0.7)',
                        titleFont: { size: 14, family: "'Roboto', sans-serif" },
                        bodyFont: { size: 12, family: "'Roboto', sans-serif" }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: Math.round,
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        });
    }

    function updateResponseChart() {
        if (!responseChart) return;
        responseChart.data.datasets[0].data = [responseCounts.senang, responseCounts.biasa, responseCounts.sedih];
        responseChart.update();
    }

    // --- Render Reports ---
    function renderLikeReport() {
        if (likeReportsTableBody) {
            likeReportsTableBody.innerHTML = '';
            Object.keys(likesPerSecond).sort((a, b) => b - a).forEach(second => {
                const row = `<tr><td class="align-middle text-center"><span class="text-secondary text-xs font-weight-bold">Detik ${second}</span></td><td class="align-middle text-center text-sm"><span class="badge badge-sm bg-gradient-success">${likesPerSecond[second]}</span></td></tr>`;
                likeReportsTableBody.insertAdjacentHTML('beforeend', row);
            });
        }
        updateChart();
    }

    function renderResponseReport() {
        if (responseSenang) responseSenang.textContent = responseCounts.senang;
        if (responseBiasa) responseBiasa.textContent = responseCounts.biasa;
        if (responseSedih) responseSedih.textContent = responseCounts.sedih;
    }

    function renderResponsePercentages() {
        const totalResponses = responseCounts.senang + responseCounts.biasa + responseCounts.sedih;
        if (totalResponses === 0) {
            if (percentageSenangDisplay) percentageSenangDisplay.textContent = '0%';
            if (percentageBiasaDisplay) percentageBiasaDisplay.textContent = '0%';
            if (percentageSedihDisplay) percentageSedihDisplay.textContent = '0%';
            return;
        }
        const percSenang = Math.round((responseCounts.senang / totalResponses) * 100);
        const percBiasa = Math.round((responseCounts.biasa / totalResponses) * 100);
        const percSedih = Math.round((responseCounts.sedih / totalResponses) * 100);
        if (percentageSenangDisplay) percentageSenangDisplay.textContent = `${percSenang}%`;
        if (percentageBiasaDisplay) percentageBiasaDisplay.textContent = `${percBiasa}%`;
        if (percentageSedihDisplay) percentageSedihDisplay.textContent = `${percSedih}%`;
    }

    function renderShareReport() {
        if (shareReportsTableBody) {
            shareReportsTableBody.innerHTML = '';
            Object.keys(sharesPerSecond).sort((a, b) => b - a).forEach(second => {
                const row = `<tr><td class="align-middle text-center"><span class="text-secondary text-xs font-weight-bold">Detik ${second}</span></td><td class="align-middle text-center text-sm"><span class="badge badge-sm bg-gradient-info">${sharesPerSecond[second]}</span></td></tr>`;
                shareReportsTableBody.insertAdjacentHTML('beforeend', row);
            });
        }
        if (shareCountDisplay) {
            shareCountDisplay.textContent = Object.values(sharesPerSecond).reduce((sum, count) => sum + count, 0);
        }
        updateChart();
    }

    function renderPreferenceReport() {
        if (!preferencesChart) return;
        preferencesChart.data.datasets[0].data = [preferenceCounts.senang, preferenceCounts.biasa, preferenceCounts.marah];
        preferencesChart.update();
    }

    // --- Stopwatch Controls ---
    function formatTime(time) {
        const h = String(Math.floor(time / 3600000)).padStart(2, '0');
        const m = String(Math.floor((time % 3600000) / 60000)).padStart(2, '0');
        const s = String(Math.floor((time % 60000) / 1000)).padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    function updateDisplay() {
        elapsedTime = Date.now() - startTime;
        if (display) display.innerHTML = formatTime(elapsedTime);
    }

    function startPauseTimer() {
        if (!running) {
            sendCommand(AppConfig.baseUrl + 'admin/start_timer', {
                startTime: Date.now(),
                elapsedTime: elapsedTime
            });
            startTime = Date.now() - elapsedTime;
            timerInterval = setInterval(updateDisplay, 1000);
            if (startPauseBtn) {
                startPauseBtn.textContent = 'Pause';
                startPauseBtn.classList.replace('bg-gradient-success', 'bg-gradient-warning');
            }
            if (stopBtn) stopBtn.disabled = false;
        } else {
            sendCommand(AppConfig.baseUrl + 'admin/pause_timer');
            clearInterval(timerInterval);
            if (startPauseBtn) {
                startPauseBtn.textContent = 'Continue';
                startPauseBtn.classList.replace('bg-gradient-warning', 'bg-gradient-success');
            }
        }
        running = !running;
    }

    function finalStop() {
        sendCommand(AppConfig.baseUrl + 'admin/stop_timer');
        clearInterval(timerInterval);
        running = false;
        if (startPauseBtn) {
            startPauseBtn.disabled = true;
            startPauseBtn.textContent = 'Finished';
            startPauseBtn.classList.remove('bg-gradient-success', 'bg-gradient-warning');
            startPauseBtn.classList.add('bg-gradient-secondary');
        }
        if (stopBtn) {
            stopBtn.disabled = true;
        }
    }

    function reset() {
        sendCommand(AppConfig.baseUrl + 'admin/reset_timer');
        clearInterval(timerInterval);
        running = false;
        elapsedTime = 0;
        maleCount = 0;
        femaleCount = 0;
        totalAge = 0;
        ageSubmitCount = 0;
        if (maleCountDisplay) maleCountDisplay.textContent = '0';
        if (femaleCountDisplay) femaleCountDisplay.textContent = '0';
        if (averageAgeDisplay) averageAgeDisplay.textContent = '0';
        if (display) display.innerHTML = '00:00:00';

        if (startPauseBtn) {
            startPauseBtn.textContent = 'Start';
            startPauseBtn.disabled = true;
            startPauseBtn.classList.remove('bg-gradient-warning', 'bg-gradient-secondary');
            startPauseBtn.classList.add('bg-gradient-success');
        }
        if (stopBtn) {
            stopBtn.disabled = true;
        }

        const previewNameInput = document.getElementById('previewNameInput');
        const previewNameForm = document.getElementById('previewNameForm');
        const adminPreviewNameDisplay = document.getElementById('adminPreviewNameDisplay');

        if (previewNameInput) previewNameInput.value = '';
        if (previewNameForm) previewNameForm.style.display = 'block';
        if (adminPreviewNameDisplay) adminPreviewNameDisplay.style.display = 'none';
        if (reportNameInput) reportNameInput.value = '';

        likesPerSecond = {};
        sharesPerSecond = {};
        shareCount = 0;
        responseCounts = {
            senang: 0,
            biasa: 0,
            sedih: 0
        };
        comments = [];
        commentLikeCount = 0;
        commentDislikeCount = 0;
        preferenceCounts = {
            senang: 0,
            biasa: 0,
            marah: 0
        };

        if (likeCountDisplay) likeCountDisplay.textContent = '0';
        if (dislikeCountDisplay) dislikeCountDisplay.textContent = '0';
        if (shareCountDisplay) shareCountDisplay.textContent = '0';
        if (realtimeCommentsList) realtimeCommentsList.innerHTML = '<li class="list-group-item border-0 px-0"><p class="text-sm text-muted text-center">Menunggu komentar dari pengguna...</p></li>';

        renderPreferenceReport();
        renderLikeReport();
        renderShareReport();
        renderResponseReport();
        renderResponsePercentages();
        updateResponseChart();
        initializeChart();
        initializeResponseChart();
        initializePreferencesChart();
    }

    async function sendCommand(url, data = {}) {
        const formData = new FormData();
        Object.keys(data).forEach(key => formData.append(key, data[key]));
        formData.append(AppConfig.csrf.name, AppConfig.csrf.hash); // Menggunakan variabel dari AppConfig
        try {
            await fetch(url, {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error("Gagal mengirim perintah ke server:", error);
        }
    }

    // --- Event Listeners ---
    if (setPreviewNameBtn) {
        setPreviewNameBtn.addEventListener('click', async function () {
            const previewNameInput = document.getElementById('previewNameInput');
            const previewName = previewNameInput.value.trim();
            if (!previewName) {
                showNotification('Nama preview wajib diisi!', true);
                return;
            }
            const formData = new FormData();
            formData.append(AppConfig.csrf.name, AppConfig.csrf.hash); // Menggunakan variabel dari AppConfig
            formData.append('preview_name', previewName);
            try {
                const response = await fetch(AppConfig.baseUrl + 'admin/set_preview_name', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal mengirim nama.');
                showNotification('Nama preview diatur: ' + previewName);
                document.getElementById('adminPreviewNameText').textContent = previewName;
                document.getElementById('previewNameForm').style.display = 'none';
                document.getElementById('adminPreviewNameDisplay').style.display = 'block';
                if (reportNameInput) reportNameInput.value = previewName;
                if (startPauseBtn) startPauseBtn.disabled = false;
            } catch (error) {
                showNotification('Gagal mengatur nama: ' + error.message, true);
            }
        });
    }

    if (startPauseBtn) startPauseBtn.addEventListener('click', startPauseTimer);
    if (stopBtn) stopBtn.addEventListener('click', finalStop);
    if (resetBtn) resetBtn.addEventListener('click', reset);

    if (saveReportForm) {
        saveReportForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const reportName = reportNameInput.value.trim();
            if (!reportName) {
                showNotification('Nama laporan wajib diisi!', true);
                return;
            }
            const formData = new FormData();
            formData.append(AppConfig.csrf.name, AppConfig.csrf.hash); // Menggunakan variabel dari AppConfig
            formData.append('nama_video', reportName);
            formData.append('love_count', Object.values(likesPerSecond).reduce((s, c) => s + c, 0));
            formData.append('share_count', Object.values(sharesPerSecond).reduce((s, c) => s + c, 0));
            formData.append('response_senang', responseCounts.senang);
            formData.append('response_biasa', responseCounts.biasa);
            formData.append('response_sedih', responseCounts.sedih);
            formData.append('like_details', JSON.stringify(likesPerSecond));
            formData.append('share_details', JSON.stringify(sharesPerSecond));
            formData.append('comments', JSON.stringify(comments));
            formData.append('session_male_count', maleCount);
            formData.append('session_female_count', femaleCount);
            const averageAge = (ageSubmitCount > 0) ? (totalAge / ageSubmitCount) : 0;
            formData.append('session_average_age', averageAge.toFixed(1));
            formData.append('pref_senang', preferenceCounts.senang);
            formData.append('pref_biasa', preferenceCounts.biasa);
            formData.append('pref_marah', preferenceCounts.marah);

            try {
                const response = await fetch(AppConfig.baseUrl + 'admin/save_report', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal menyimpan.');
                showNotification(result.message || 'Laporan berhasil disimpan!');
                if (reportNameInput) reportNameInput.value = '';
                reset();
            } catch (error) {
                showNotification('Gagal menyimpan laporan: ' + error.message, true);
            }
        });
    }

    // --- Pusher Connection ---
    try {
        const pusher = new Pusher(AppConfig.pusherKey, { // Menggunakan variabel dari AppConfig
            cluster: AppConfig.pusherCluster // Menggunakan variabel dari AppConfig
        });
        const channel = pusher.subscribe('stopwatch-channel');

        channel.bind('gender-update', function (data) {
            if (data.gender === 'Laki-laki') {
                maleCount++;
                if (maleCountDisplay) maleCountDisplay.textContent = maleCount;
            } else if (data.gender === 'Perempuan') {
                femaleCount++;
                if (femaleCountDisplay) femaleCountDisplay.textContent = femaleCount;
            }
            if (data.age && !isNaN(data.age)) {
                totalAge += parseInt(data.age, 10);
                ageSubmitCount++;
                const average = (ageSubmitCount > 0) ? (totalAge / ageSubmitCount).toFixed(1) : 0;
                if (averageAgeDisplay) averageAgeDisplay.textContent = average;
            }
        });

        channel.bind('stats-update', function (data) {
            const second = Math.floor(parseFloat(data.time) / 1000);
            if (data.type === 'love') {
                likesPerSecond[second] = (likesPerSecond[second] || 0) + 1;
                renderLikeReport();
            } else if (data.type === 'response' && data.value) {
                if (responseCounts.hasOwnProperty(data.value)) {
                    responseCounts[data.value]++;
                    renderResponseReport();
                    renderResponsePercentages();
                    updateResponseChart();
                }
            } else if (data.type === 'share') {
                sharesPerSecond[second] = (sharesPerSecond[second] || 0) + 1;
                renderShareReport();
            }
        });

        channel.bind('preference-update', function (data) {
            if (preferenceCounts.hasOwnProperty(data.type)) {
                preferenceCounts[data.type]++;
                renderPreferenceReport();
            }
        });

        channel.bind('comment-reaction-update', function (data) {
            if (data.reaction === 'like') {
                commentLikeCount++;
            } else if (data.reaction === 'dislike') {
                commentDislikeCount++;
            }
            if (likeCountDisplay) likeCountDisplay.textContent = commentLikeCount;
            if (dislikeCountDisplay) dislikeCountDisplay.textContent = commentDislikeCount;
        });

        channel.bind('comment-event', function (data) {
            const placeholder = realtimeCommentsList.querySelector('.text-muted');
            if (placeholder) {
                placeholder.parentElement.remove();
            }
            const li = document.createElement('li');
            li.className = 'list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg';
            let reactionIcon = '<span class="text-secondary" title="Tanpa Reaksi">⸬</span>';
            if (data.is_like === '1') {
                reactionIcon = '<span class="text-success" title="Suka">👍</span>';
            } else if (data.is_dislike === '1') {
                reactionIcon = '<span class="text-danger" title="Tidak Suka">👎</span>';
            }

            comments.push({
                username: data.username || 'Anonim',
                comment_text: data.comment_text || 'Tanpa teks',
                is_like: data.is_like || '0',
                is_dislike: data.is_dislike || '0'
            });

            li.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="d-flex flex-column">
                        <h6 class="mb-1 text-dark text-sm">${data.username || 'Anonim'}</h6>
                        <span class="text-xs">${data.comment_text || 'Tanpa teks'}</span>
                    </div>
                </div>
                <span class="reaction-icon">${reactionIcon}</span>`;
            realtimeCommentsList.prepend(li);
        });

    } catch (e) {
        console.error("Gagal terhubung ke Pusher:", e);
        showNotification('Gagal terhubung ke Pusher.', true);
    }

    // --- Initialize Display ---
    initializeChart();
    initializeResponseChart();
    initializePreferencesChart();
    renderPreferenceReport();
});