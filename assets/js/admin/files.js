/**
 * files.js - COMPLETE MERGED VERSION
 * Menggabungkan semua fitur:
 * 1. Modal detail laporan + charts
 * 2. Download PDF
 * 3. Manage access clients
 * 4. Delete report
 * 5. Continue Project (BARU!)
 * 6. Search & Filter (BARU!)
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 Files.js (Merged Version) initialized');

    if (typeof AppConfig === 'undefined') {
        console.error('AppConfig object is not defined. Make sure to include it in your PHP view.');
        return;
    }

    // ====================================================================
    // MODAL DETAIL LAPORAN + CHARTS (ORIGINAL)
    // ====================================================================
    const detailModal = document.getElementById('detailModal');
    const detailModalBody = document.getElementById('detailModalBody');
    const downloadPdfBtn = document.getElementById('downloadPdfBtn');

    let detailResponseChart, detailEngagementChart, detailPreferencesChart;
    let currentReportData = null;
    let currentReportName = '';
    let currentReportDate = '';

    if (detailModal) {
        detailModal.addEventListener('show.bs.modal', async function (event) {
            currentReportData = null;
            downloadPdfBtn.disabled = true;
            const button = event.relatedTarget;
            const reportId = button.getAttribute('data-id');
            currentReportName = button.getAttribute('data-name');
            currentReportDate = button.getAttribute('data-date');

            if (detailModalBody) detailModalBody.innerHTML = '<p class="text-center py-5">Memuat data...</p>';

            try {
                const response = await fetch(`${AppConfig.actionBaseUrl}get_report_details/${reportId}`);
                if (!response.ok) throw new Error('Gagal memuat data laporan.');
                const data = await response.json();
                currentReportData = data;
                downloadPdfBtn.disabled = false;

                const rSenang = parseInt(data.report.response_senang) || 0;
                const rBiasa = parseInt(data.report.response_biasa) || 0;
                const rSedih = parseInt(data.report.response_sedih) || 0;
                const totalResponses = rSenang + rBiasa + rSedih;
                const pSenang = totalResponses > 0 ? ((rSenang / totalResponses) * 100).toFixed(1) : '0.0';
                const pBiasa = totalResponses > 0 ? ((rBiasa / totalResponses) * 100).toFixed(1) : '0.0';
                const pSedih = totalResponses > 0 ? ((rSedih / totalResponses) * 100).toFixed(1) : '0.0';

                if (detailModalBody) {
                    detailModalBody.innerHTML = `
                        <div class="container-fluid">
                            <div class="text-center p-3 mb-4 rounded-3 shadow-sm" style="background-color: #344767; color: white;">
                                <h4 class="mb-0 text-white">Aseskan</h4>
                                <p class="mb-0 opacity-8">${htmlspecialchars(currentReportName)}</p>
                            </div>

                            <div class="card mb-4">
                                <div class="card-body p-3">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item list-group-item-light fw-bold text-dark ps-0 py-2">Preview Panel</li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Man</span>
                                            <span>${data.report.session_male_count || 0}</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Woman</span>
                                            <span>${data.report.session_female_count || 0}</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Average</span>
                                            <span>${data.report.session_average_age || 0}</span>
                                        </li>

                                        <li class="list-group-item list-group-item-light fw-bold text-dark mt-2 ps-0 py-2">Preview Engagement Metrics</li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Likeable (Video)</span>
                                            <span>${data.report.love_count || 0}</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Shareable (Video)</span>
                                            <span>${data.report.share_count || 0}</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Likes (Comment)</span>
                                            <span>${data.comment_stats.likes || 0} 👍</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Dislikes (Comment)</span>
                                            <span>${data.comment_stats.dislikes || 0} 👎</span>
                                        </li>

                                        <li class="list-group-item list-group-item-light fw-bold text-dark mt-2 ps-0 py-2">Preview Responses</li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Likes</span>
                                            <span>${pSenang}%</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Neutral</span>
                                            <span>${pBiasa}%</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Dislike</span>
                                            <span>${pSedih}%</span>
                                        </li>

                                        <li class="list-group-item list-group-item-light fw-bold text-dark mt-2 ps-0 py-2">Preview Preferences</li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Statisfied</span>
                                            <span>${data.report.pref_senang || 0}</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Normal</span>
                                            <span>${data.report.pref_biasa || 0}</span>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center ps-0 py-1 gap-3">
                                            <span class="label-text">Disappointed</span>
                                            <span>${data.report.pref_marah || 0}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header pb-0"><h6>Responses Chart</h6></div>
                                        <div class="card-body d-flex align-items-center justify-content-center">
                                            <div style="height:250px; width:100%;"><canvas id="detailResponseChart"></canvas></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header pb-0"><h6>Preferences Chart</h6></div>
                                        <div class="card-body">
                                            <div style="height:250px; width:100%;"><canvas id="detailPreferencesChart"></canvas></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header pb-0"><h6>Preview Timeline</h6></div>
                                <div class="card-body">
                                    <div style="height:300px;"><canvas id="detailEngagementChart"></canvas></div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header"><h6>Preview Comments</h6></div>
                                <div class="card-body pt-0">
                                    <ul id="detailCommentsList" class="list-group list-group-flush"></ul>
                                </div>
                            </div>
                        </div>
                    `;
                }

                renderResponseChart([rSenang, rBiasa, rSedih]);
                renderCommentsList(data.comments || []);
                renderEngagementChart(
                    JSON.parse(data.report.like_details || '{}'),
                    JSON.parse(data.report.share_details || '{}')
                );
                renderPreferencesChart([
                    parseInt(data.report.pref_senang || 0),
                    parseInt(data.report.pref_biasa || 0),
                    parseInt(data.report.pref_marah || 0)
                ]);

            } catch (error) {
                console.error('❌ Error loading report details:', error);
                if (detailModalBody) {
                    detailModalBody.innerHTML = `<p class="text-danger text-center py-5">${error.message}</p>`;
                }
            }
        });
    }

    // ====================================================================
    // CHART RENDERING FUNCTIONS (ORIGINAL)
    // ====================================================================
    function renderResponseChart(data) {
        const ctx = document.getElementById('detailResponseChart')?.getContext('2d');
        if (!ctx) return;
        if (detailResponseChart) detailResponseChart.destroy();

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

        const gradientGreen = ctx.createLinearGradient(0, 0, 0, 250);
        gradientGreen.addColorStop(0, 'rgba(76, 175, 80, 1)');
        gradientGreen.addColorStop(1, 'rgba(129, 199, 132, 1)');

        const gradientYellow = ctx.createLinearGradient(0, 0, 0, 250);
        gradientYellow.addColorStop(0, 'rgba(255, 193, 7, 1)');
        gradientYellow.addColorStop(1, 'rgba(255, 204, 51, 1)');

        const gradientRed = ctx.createLinearGradient(0, 0, 0, 250);
        gradientRed.addColorStop(0, 'rgba(244, 67, 54, 1)');
        gradientRed.addColorStop(1, 'rgba(239, 83, 80, 1)');

        detailResponseChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Likes', 'Neutral', 'Dislikes'],
                datasets: [{
                    data: data,
                    backgroundColor: [gradientGreen, gradientYellow, gradientRed],
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { animateScale: true, animateRotate: true },
                plugins: {
                    centerText: centerTextPlugin,
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: { size: 12, family: "'Roboto', sans-serif" },
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
                                if (label) label += ': ';
                                if (context.parsed !== null) label += context.parsed + '%';
                                return label;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }

    function renderPreferencesChart(data) {
        const ctx = document.getElementById('detailPreferencesChart')?.getContext('2d');
        if (!ctx) return;
        if (detailPreferencesChart) detailPreferencesChart.destroy();

        const gradientBlue = ctx.createLinearGradient(0, 0, 0, 250);
        gradientBlue.addColorStop(0, 'rgba(33, 150, 243, 1)');
        gradientBlue.addColorStop(1, 'rgba(100, 181, 246, 1)');

        const gradientOrange = ctx.createLinearGradient(0, 0, 0, 250);
        gradientOrange.addColorStop(0, 'rgba(255, 152, 0, 1)');
        gradientOrange.addColorStop(1, 'rgba(255, 183, 77, 1)');

        const gradientPurple = ctx.createLinearGradient(0, 0, 0, 250);
        gradientPurple.addColorStop(0, 'rgba(156, 39, 176, 1)');
        gradientPurple.addColorStop(1, 'rgba(186, 104, 200, 1)');

        detailPreferencesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Satisfied', 'Normal', 'Disappointed'],
                datasets: [{
                    label: 'Jumlah Preferensi',
                    data: data,
                    backgroundColor: [gradientBlue, gradientOrange, gradientPurple],
                    borderWidth: 0,
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1200, easing: 'easeOutBounce' },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#344767' },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        ticks: { color: '#344767', font: { size: 10 } },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
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
                        font: { weight: 'bold' }
                    }
                }
            }
        });
    }

    function renderEngagementChart(likeData, shareData) {
        const ctx = document.getElementById('detailEngagementChart')?.getContext('2d');
        if (!ctx) return;
        if (detailEngagementChart) detailEngagementChart.destroy();

        const allKeys = [...Object.keys(likeData), ...Object.keys(shareData)];
        const numericKeys = allKeys.map(k => parseInt(k, 10)).filter(k => !isNaN(k));
        const uniqueKeys = [...new Set(numericKeys)];
        uniqueKeys.sort((a, b) => a - b);
        const labels = uniqueKeys.map(sec => `${sec}s`);

        const gradientLike = ctx.createLinearGradient(0, 0, 0, 200);
        gradientLike.addColorStop(0, 'rgba(233, 30, 99, 0.5)');
        gradientLike.addColorStop(1, 'rgba(233, 30, 99, 0)');

        const gradientShare = ctx.createLinearGradient(0, 0, 0, 200);
        gradientShare.addColorStop(0, 'rgba(3, 169, 244, 0.5)');
        gradientShare.addColorStop(1, 'rgba(3, 169, 244, 0)');

        detailEngagementChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: "Likeable",
                    tension: 0.4,
                    borderColor: "#E91E63",
                    backgroundColor: gradientLike,
                    fill: true,
                    data: uniqueKeys.map(key => likeData[key] || 0),
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
                    data: uniqueKeys.map(key => shareData[key] || 0),
                    borderWidth: 2,
                    pointRadius: 2,
                    pointBackgroundColor: '#03A9F4',
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1500, easing: 'easeInOutCubic' },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#344767' },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Second',
                            color: '#344767',
                            font: { weight: 'bold' }
                        },
                        ticks: { color: '#344767' },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { color: '#344767', font: { size: 14 } }
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

    function renderCommentsList(comments) {
        const listEl = document.getElementById('detailCommentsList');
        if (!listEl) return;
        listEl.innerHTML = '';
        if (!comments || comments.length === 0) {
            listEl.innerHTML = '<li class="list-group-item text-center text-muted">Tidak ada komentar.</li>';
            return;
        }
        comments.forEach(c => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-start px-0 py-2';
            li.innerHTML = `
                <div>
                    <p class="fw-bold mb-0 text-dark">${htmlspecialchars(c.username)}</p>
                    <p class="mb-0 text-sm text-muted">${htmlspecialchars(c.komentar)}</p>
                </div>
            `;
            listEl.appendChild(li);
        });
    }

    // ====================================================================
    // DOWNLOAD PDF (ORIGINAL)
    // ====================================================================
    if (downloadPdfBtn) {
        downloadPdfBtn.addEventListener('click', function () {
            if (currentReportData && currentReportData.report && currentReportData.report.id) {
                const reportId = currentReportData.report.id;

                const responseChartImg = detailResponseChart ? detailResponseChart.toBase64Image() : null;
                const preferencesChartImg = detailPreferencesChart ? detailPreferencesChart.toBase64Image() : null;
                const engagementChartImg = detailEngagementChart ? detailEngagementChart.toBase64Image() : null;

                const form = document.createElement('form');
                form.method = 'POST';
                if (AppConfig.isAdminView) {
                    form.action = `${AppConfig.baseUrl}admin/download_report_pdf/${reportId}`;
                } else {
                    form.action = `${AppConfig.baseUrl}client/download_report_pdf/${reportId}`;
                }
                form.style.display = 'none';

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = AppConfig.csrf.name;
                csrfInput.value = AppConfig.csrf.hash;
                form.appendChild(csrfInput);

                if (responseChartImg) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'responseChartImg';
                    input.value = responseChartImg;
                    form.appendChild(input);
                }
                if (preferencesChartImg) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'preferencesChartImg';
                    input.value = preferencesChartImg;
                    form.appendChild(input);
                }
                if (engagementChartImg) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'engagementChartImg';
                    input.value = engagementChartImg;
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            } else {
                console.error('Tidak ada data laporan atau ID laporan yang tersedia untuk diunduh.');
                alert('Tidak dapat mengunduh PDF, data laporan tidak ditemukan.');
            }
        });
    }

    // ====================================================================
    // SEARCH FILES BY PROJECT NAME ONLY
    // ====================================================================
    const searchInputEl = document.getElementById('searchInput');

    if (searchInputEl) {
        function performSearch() {
            const searchValue = searchInputEl.value.toLowerCase().trim();
            const projectItems = document.querySelectorAll('.list-group-item[data-name]');

            projectItems.forEach(function(item) {
                const projectName = item.getAttribute('data-name');
                
                if (!searchValue || (projectName && projectName.toLowerCase().includes(searchValue))) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        }
        
        searchInputEl.addEventListener('keyup', performSearch);
        searchInputEl.addEventListener('input', performSearch);
    }

    
    // ====================================================================
    // CONTINUE PROJECT FUNCTIONALITY (BARU!)
    // ====================================================================
    const listGroup = document.querySelector('.list-group');
    if (listGroup) {
        listGroup.addEventListener('click', async function (e) {
            const continueButton = e.target.closest('.continue-project-btn');

            if (continueButton) {
                e.preventDefault();
                await handleContinueProject(continueButton);
            }
        });
    }

    async function handleContinueProject(button) {
        const projectId = button.dataset.projectId;
        const projectName = button.dataset.projectName;

        console.log('🔄 Continue project requested:', { projectId, projectName });

        // Step 1: Get project info and existing sessions
        try {
            Swal.fire({
                title: 'Loading...',
                text: 'Getting project information...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const formData = new FormData();
            formData.append(AppConfig.csrf.name, AppConfig.csrf.hash);
            formData.append('project_id', projectId);

            const response = await fetch(`${AppConfig.actionBaseUrl}continue_project`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();
            console.log('📥 Project info response:', result);

            if (result.status === 'confirm') {
                // Display confirmation modal with sessions
                const sessionsHtml = result.sessions.map((session, index) => `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <strong>Session ${session.project_session}:</strong> ${session.nama_video}
                        </span>
                        <span class="badge bg-info">${session.session_start_date || 'N/A'}</span>
                    </li>
                `).join('');

                const confirmResult = await Swal.fire({
                    title: 'Lanjutkan Project?',
                    html: `
                        <div class="text-start">
                            <p>Project saat ini: <strong>${result.project.nama_video}</strong></p>
                            
                            <div class="alert alert-info mt-3">
                                <strong><i class="fas fa-list"></i> Sesi yang Ada:</strong>
                                <ul class="list-group list-group-flush mt-2">
                                    ${sessionsHtml}
                                </ul>
                            </div>

                            <div class="alert alert-success">
                                <strong><i class="fas fa-plus-circle"></i> Sesi Baru:</strong><br>
                                Sesi ${result.next_session} akan dibuat
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i> <strong>Catatan:</strong>
                                <ul class="mb-0 mt-2 text-start">
                                    <li>Data survey baru akan digabungkan dengan data yang ada</li>
                                    <li>Total responden akan bertambah</li>
                                    <li>Data lama tidak akan berubah</li>
                                </ul>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check"></i> Ya, Buat Sesi ' + result.next_session,
                    cancelButtonText: '<i class="fas fa-times"></i> Batal',
                    width: '700px',
                    customClass: {
                        htmlContainer: 'text-start'
                    }
                });

                if (confirmResult.isConfirmed) {
                    await executeContinueProject(projectId, projectName, true);
                }
            } else if (result.status === 'error') {
                Swal.fire({
                    title: 'Error!',
                    text: result.message,
                    icon: 'error'
                });
            }

        } catch (error) {
            console.error('❌ Error getting project info:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to load project information',
                icon: 'error'
            });
        }
    }

    async function executeContinueProject(projectId, projectName, confirmed = false) {
        try {
            Swal.fire({
                title: 'Creating New Session...',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const formData = new FormData();
            formData.append(AppConfig.csrf.name, AppConfig.csrf.hash);
            formData.append('project_id', projectId);
            formData.append('confirm', confirmed ? '1' : '0');

            console.log('📤 Creating continuation project:', projectId);

            const response = await fetch(`${AppConfig.actionBaseUrl}continue_project`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();
            console.log('📥 Continue project response:', result);

            if (result.status === 'success') {
                await Swal.fire({
                    title: 'Success!',
                    html: `
                        <p>Project continuation berhasil diaktifkan!</p>
                        <p class="mb-0"><strong>${projectName}</strong></p>
                        <p class="text-muted">Anda akan diarahkan ke dashboard...</p>
                    `,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                console.log('✅ Redirecting to dashboard...');
                window.location.href = `${AppConfig.baseUrl}admin`;

            } else {
                throw new Error(result.message || 'Failed to continue project');
            }

        } catch (error) {
            console.error('❌ Continue project error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to continue project: ' + error.message,
                icon: 'error'
            });
        }
    }

    // ====================================================================
    // MANAGE ACCESS CLIENTS (ORIGINAL - ADMIN ONLY)
    // ====================================================================
    if (AppConfig.isAdminView) {
        const manageAccessModalEl = document.getElementById('manageAccessModal');
        if (manageAccessModalEl) {
            const manageAccessModal = new bootstrap.Modal(manageAccessModalEl);
            const manageAccessForm = document.getElementById('manageAccessForm');
            const modalFileIdInput = document.getElementById('modalFileId');

            document.querySelectorAll('.manage-access-btn').forEach(button => {
                button.addEventListener('click', async function () {
                    console.log("📋 Manage access button clicked");

                    const fileId = this.dataset.fileId;
                    modalFileIdInput.value = fileId;
                    console.log(`File ID: ${fileId}`);

                    manageAccessForm.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

                    try {
                        const response = await fetch(`${AppConfig.baseUrl}admin/get_file_clients/${fileId}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (!response.ok) {
                            throw new Error(`Network response error: ${response.status}`);
                        }

                        const data = await response.json();

                        if (data.status === 'success' && Array.isArray(data.client_ids)) {
                            data.client_ids.forEach(clientId => {
                                const checkbox = document.getElementById(`client_check_${clientId}`);
                                if (checkbox) checkbox.checked = true;
                            });
                            console.log('✅ Access data loaded');
                        } else {
                            console.error("Invalid data format:", data);
                        }

                    } catch (error) {
                        console.error('❌ Error fetching client access:', error);
                        if (typeof showNotification === 'function') {
                            showNotification('Gagal mengambil data akses klien.', true);
                        }
                    }
                });
            });

            const saveAccessBtn = document.getElementById('saveAccessBtn');
            if (saveAccessBtn) {
                saveAccessBtn.addEventListener('click', async function () {
                    const button = this;
                    const formData = new FormData(manageAccessForm);
                    formData.append(AppConfig.csrf.name, AppConfig.csrf.hash);

                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

                    try {
                        const response = await fetch(`${AppConfig.baseUrl}admin/update_file_access`, {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const result = await response.json();

                        if (!response.ok || result.status !== 'success') {
                            throw new Error(result.message || 'Failed to save access rights.');
                        }

                        if (typeof showNotification === 'function') {
                            showNotification(result.message);
                        } else {
                            alert(result.message);
                        }
                        manageAccessModal.hide();

                    } catch (error) {
                        console.error('❌ Error saving access:', error);
                        if (typeof showNotification === 'function') {
                            showNotification(error.message, true);
                        } else {
                            alert('Error: ' + error.message);
                        }
                    } finally {
                        button.disabled = false;
                        button.innerHTML = 'Save';
                        refreshCsrfToken();
                    }
                });
            }
        }

        // ====================================================================
        // DELETE REPORT (ORIGINAL - ADMIN ONLY)
        // ====================================================================
        document.querySelector('.list-group')?.addEventListener('click', function (e) {
            const deleteButton = e.target.closest('a.delete-report-btn');
            if (!deleteButton) return;

            e.preventDefault();
            const reportId = deleteButton.dataset.id;

            Swal.fire({
                title: 'Anda Yakin?',
                text: "Laporan yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#344767',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const postData = new FormData();
                    postData.append(AppConfig.csrf.name, AppConfig.csrf.hash);
                    postData.append('id', reportId);

                    try {
                        const response = await fetch(`${AppConfig.baseUrl}admin/delete_report`, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            body: postData
                        });

                        const res = await response.json();
                        if (!response.ok || res.status !== 'success') {
                            throw new Error(res.message || 'Gagal menghapus laporan.');
                        }

                        Swal.fire('Dihapus!', 'Laporan berhasil dihapus.', 'success');

                        const listItem = deleteButton.closest('li');
                        if (listItem) {
                            listItem.style.transition = 'opacity 0.5s ease';
                            listItem.style.opacity = '0';
                            setTimeout(() => listItem.remove(), 500);
                        }

                    } catch (error) {
                        console.error('❌ Delete error:', error);
                        Swal.fire('Gagal!', error.message || 'Terjadi kesalahan.', 'error');
                    } finally {
                        refreshCsrfToken();
                    }
                }
            });
        });
    }

    // ====================================================================
    // UTILITY FUNCTIONS
    // ====================================================================
    function htmlspecialchars(str) {
        if (typeof str !== 'string') return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return str.replace(/[&<>"']/g, m => map[m]);
    }

    function refreshCsrfToken() {
        fetch(`${AppConfig.baseUrl}auth/get_csrf_token`)
            .then(r => r.json())
            .then(d => {
                if (d.csrf_token_name && d.csrf_hash) {
                    AppConfig.csrf.name = d.csrf_token_name;
                    AppConfig.csrf.hash = d.csrf_hash;
                    document.querySelectorAll(`input[name="${d.csrf_token_name}"]`).forEach(i => {
                        i.value = d.csrf_hash;
                    });
                    console.log('✅ CSRF token refreshed');
                }
            })
            .catch(e => console.error('❌ Failed to refresh CSRF token:', e));
    }

    // ====================================================================
    // INITIALIZATION COMPLETE
    // ====================================================================
    console.log('✅ Files.js (Merged Version) ready!');
    console.log('📊 Features loaded:');
    console.log('  - Modal detail laporan ✓');
    console.log('  - Chart rendering ✓');
    console.log('  - Download PDF ✓');
    console.log('  - Search & Filter ✓');
    console.log('  - Continue Project ✓');
    if (AppConfig.isAdminView) {
        console.log('  - Manage access ✓');
        console.log('  - Delete report ✓');
    }

    const totalFiles = document.querySelectorAll('.list-group-item').length;
    console.log(`📁 Total files loaded: ${totalFiles}`);
});