<style>
    /* Desktop view - maintain original appearance */
    .chart-container-small {
        position: relative;
        height: 300px;
    }
    
    .chart-container-large {
        position: relative;
        height: 400px;
    }
    
    /* Responsive chart container for mobile only */
    @media (max-width: 767px) {
        .chart-container-small {
            height: 250px !important;
        }
        
        .chart-container-large {
            height: 300px !important;
        }
        
        /* Stack charts vertically on mobile */
        .col-12.col-md-6 {
            margin-bottom: 1rem;
        }
        
        /* Header responsive for mobile */
        .card-header .d-flex.align-items-center.justify-content-between {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 1rem;
        }
        
        .card-header h5 {
            font-size: 0.9rem !important;
        }
        
        .card-header h5 i {
            font-size: 1rem !important;
        }
        
        .card-header .d-flex.align-items-center.justify-content-between > div:last-child {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 0.5rem;
        }
        
        .card-header .btn {
            width: 100%;
            margin: 0 !important;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">
                                <i class="material-symbols-rounded opacity-10">analytics</i>
                                Combined Report - <?= htmlspecialchars($project_name); ?>
                            </h5>
                            <!--<p class="text-sm mb-0"><?= htmlspecialchars($project_name); ?></p>-->
                        </div>
                        <div>
                            <button onclick="downloadCombinedPDF()"
                                class="btn btn-sm btn-danger me-2">
                                <i class="material-symbols-rounded text-sm">download</i> Download PDF
                            </button>
                            <a href="<?= base_url($back_to_files_url); ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="material-symbols-rounded text-sm">arrow_back</i> Back to Files
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Panel Summary (Style seperti Modal Details) -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-3">
                            <?php
                            // Hitung total responses dan persentase
                            $total_responses = $combined['total_response_senang'] + $combined['total_response_biasa'] + $combined['total_response_sedih'];
                            $perc_senang = $total_responses > 0 ? round(($combined['total_response_senang'] / $total_responses) * 100, 1) : 0;
                            $perc_biasa = $total_responses > 0 ? round(($combined['total_response_biasa'] / $total_responses) * 100, 1) : 0;
                            $perc_sedih = $total_responses > 0 ? round(($combined['total_response_sedih'] / $total_responses) * 100, 1) : 0;

                            // Hitung total comment likes/dislikes
                            $total_comment_likes = 0;
                            $total_comment_dislikes = 0;
                            if (!empty($all_comments)) {
                                foreach ($all_comments as $comment) {
                                    if ($comment['is_like'] == 1) $total_comment_likes++;
                                    if ($comment['is_dislike'] == 1) $total_comment_dislikes++;
                                }
                            }
                            ?>

                            <ul class="list-group list-group-flush">
                                <!-- Preview Panel -->
                                <li class="list-group-item list-group-item-light fw-bold text-dark ps-0 py-2">
                                    <i class="material-symbols-rounded opacity-10">groups</i> Preview Panel
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Man</span>
                                    <span><?= $combined['total_male_count']; ?></span>
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Woman</span>
                                    <span><?= $combined['total_female_count']; ?></span>
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Average Age</span>
                                    <span><?= $combined['average_age']; ?></span>
                                </li>

                                <!-- Preview Engagement Metrics -->
                                <li class="list-group-item list-group-item-light fw-bold text-dark mt-2 ps-0 py-2">
                                    <i class="material-symbols-rounded opacity-10">favorite</i> Preview Engagement Metrics
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Likeable (Video)</span>
                                    <span><?= $combined['total_love_count']; ?></span>
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Shareable (Video)</span>
                                    <span><?= $combined['total_share_count']; ?></span>
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Likes (Comment)</span>
                                    <span><?= $total_comment_likes; ?> 👍</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Dislikes (Comment)</span>
                                    <span><?= $total_comment_dislikes; ?> 👎</span>
                                </li>

                                <!-- Preview Responses -->
                                <li class="list-group-item list-group-item-light fw-bold text-dark mt-2 ps-0 py-2">
                                    <i class="material-symbols-rounded opacity-10">mood</i> Preview Responses
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Likes</span>
                                    <span><?= $perc_senang; ?>%</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Neutral</span>
                                    <span><?= $perc_biasa; ?>%</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Dislikes</span>
                                    <span><?= $perc_sedih; ?>%</span>
                                </li>

                                <!-- Preview Preferences -->
                                <li class="list-group-item list-group-item-light fw-bold text-dark mt-2 ps-0 py-2">
                                    <i class="material-symbols-rounded opacity-10">sentiment_satisfied</i> Preview Preferences
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Satisfied</span>
                                    <span><?= $combined['total_pref_senang']; ?></span>
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Normal</span>
                                    <span><?= $combined['total_pref_biasa']; ?></span>
                                </li>
                                <li class="list-group-item d-flex align-items-center ps-0 py-1">
                                    <span class="label-text" style="min-width: 180px;">Disappointed</span>
                                    <span><?= $combined['total_pref_marah']; ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mb-4">
                <!-- Responses Chart -->
                <div class="col-12 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <h6><i class="material-symbols-rounded opacity-10">bar_chart</i> Responses Chart</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container-small">
                                <canvas id="responsesChartCombined"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preferences Chart -->
                <div class="col-12 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <h6><i class="material-symbols-rounded opacity-10">pie_chart</i> Preferences Chart</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container-small">
                                <canvas id="preferencesChartCombined"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Timeline Chart -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6><i class="material-symbols-rounded opacity-10">timeline</i> Preview Timeline (Love & Share Combined)</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container-large">
                                <canvas id="timelineChartCombined"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sessions Breakdown -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6><i class="material-symbols-rounded opacity-10">list</i> Sessions Breakdown</h6>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Session</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Respondents</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Loves</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Shares</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Responses</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($combined['sessions'] as $session):
                                            $session_respondents = intval($session['session_male_count']) + intval($session['session_female_count']);
                                            $session_responses = intval($session['response_senang']) + intval($session['response_biasa']) + intval($session['response_sedih']);
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?= htmlspecialchars($session['nama_video']); ?></h6>
                                                            <p class="text-xs text-secondary mb-0">Session <?= $session['project_session']; ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0"><?= date('d M Y', strtotime($session['last_updated'])); ?></p>
                                                    <p class="text-xs text-secondary mb-0"><?= date('H:i', strtotime($session['last_updated'])); ?></p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold"><?= $session_respondents; ?></span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold"><?= $session['love_count']; ?></span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold"><?= $session['share_count']; ?></span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold"><?= $session_responses; ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Comments -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6><i class="material-symbols-rounded opacity-10">comment</i> All Comments (<?= count($all_comments); ?>)</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($all_comments)): ?>
                                <div class="list-group">
                                    <?php foreach ($all_comments as $comment): ?>
                                        <div class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <h6 class="mb-0 text-sm"><?= htmlspecialchars($comment['username']); ?></h6>
                                                    <span class="badge badge-sm bg-gradient-info ms-2" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">Session <?= $comment['session_number']; ?></span>
                                                    <?php if ($comment['is_like'] == 1): ?>
                                                        <span class="ms-2 text-success">👍</span>
                                                    <?php elseif ($comment['is_dislike'] == 1): ?>
                                                        <span class="ms-2 text-danger">👎</span>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="mb-2 text-sm"><?= htmlspecialchars($comment['komentar']); ?></p>
                                                <p class="text-xs text-secondary mb-0"><?= date('d M Y, H:i', strtotime($comment['created_at'])); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-center text-secondary">No comments available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
// Prepare likes and shares data for chart
$all_likes = [];
foreach ($combined['sessions'] as $session) {
    $like_details = json_decode($session['like_details'], true) ?? [];
    foreach ($like_details as $second => $count) {
        $all_likes[] = [
            'second' => $second,
            'count' => $count,
            'session' => $session['project_session']
        ];
    }
}

$all_shares = [];
foreach ($combined['sessions'] as $session) {
    $share_details = json_decode($session['share_details'], true) ?? [];
    foreach ($share_details as $second => $count) {
        $all_shares[] = [
            'second' => $second,
            'count' => $count,
            'session' => $session['project_session']
        ];
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart.js Configuration
    Chart.defaults.font.family = 'Roboto, sans-serif';

    // Prepare data from PHP
    const combinedData = {
        responses: {
            senang: <?= $combined['total_response_senang']; ?>,
            biasa: <?= $combined['total_response_biasa']; ?>,
            sedih: <?= $combined['total_response_sedih']; ?>
        },
        preferences: {
            senang: <?= $combined['total_pref_senang']; ?>,
            biasa: <?= $combined['total_pref_biasa']; ?>,
            marah: <?= $combined['total_pref_marah']; ?>
        },
        likes: <?= json_encode($all_likes); ?>,
        shares: <?= json_encode($all_shares); ?>
    };

    // 1. Responses Chart (Bar Chart)
    const responsesCtx = document.getElementById('responsesChartCombined').getContext('2d');
    new Chart(responsesCtx, {
        type: 'bar',
        data: {
            labels: ['Senang', 'Biasa', 'Sedih'],
            datasets: [{
                label: 'Responses',
                data: [
                    combinedData.responses.senang,
                    combinedData.responses.biasa,
                    combinedData.responses.sedih
                ],
                backgroundColor: [
                    'rgba(130, 214, 22, 0.8)',
                    'rgba(251, 140, 0, 0.8)',
                    'rgba(234, 6, 6, 0.8)'
                ],
                borderColor: [
                    'rgb(130, 214, 22)',
                    'rgb(251, 140, 0)',
                    'rgb(234, 6, 6)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Combined Responses from All Sessions'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // 2. Preferences Chart (Doughnut Chart)
    const preferencesCtx = document.getElementById('preferencesChartCombined').getContext('2d');
    new Chart(preferencesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Senang', 'Biasa', 'Marah'],
            datasets: [{
                data: [
                    combinedData.preferences.senang,
                    combinedData.preferences.biasa,
                    combinedData.preferences.marah
                ],
                backgroundColor: [
                    'rgba(130, 214, 22, 0.8)',
                    'rgba(251, 140, 0, 0.8)',
                    'rgba(234, 6, 6, 0.8)'
                ],
                borderColor: [
                    'rgb(130, 214, 22)',
                    'rgb(251, 140, 0)',
                    'rgb(234, 6, 6)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Combined Preferences from All Sessions'
                }
            }
        }
    });

    // 3. Timeline Chart (Line Chart - Love & Share Combined)
    // Prepare timeline data
    const timelineData = {};

    // Add loves to timeline
    combinedData.likes.forEach(item => {
        const second = item.second;
        if (!timelineData[second]) {
            timelineData[second] = {
                loves: 0,
                shares: 0
            };
        }
        timelineData[second].loves += item.count;
    });

    // Add shares to timeline
    combinedData.shares.forEach(item => {
        const second = item.second;
        if (!timelineData[second]) {
            timelineData[second] = {
                loves: 0,
                shares: 0
            };
        }
        timelineData[second].shares += item.count;
    });

    // Sort by second
    const sortedSeconds = Object.keys(timelineData).map(Number).sort((a, b) => a - b);
    const lovesData = sortedSeconds.map(sec => timelineData[sec].loves);
    const sharesData = sortedSeconds.map(sec => timelineData[sec].shares);

    const timelineCtx = document.getElementById('timelineChartCombined').getContext('2d');
    new Chart(timelineCtx, {
        type: 'line',
        data: {
            labels: sortedSeconds.map(sec => `${sec}s`),
            datasets: [{
                    label: 'Loves',
                    data: lovesData,
                    borderColor: 'rgb(234, 6, 6)',
                    backgroundColor: 'rgba(234, 6, 6, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Shares',
                    data: sharesData,
                    borderColor: 'rgb(23, 193, 232)',
                    backgroundColor: 'rgba(23, 193, 232, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Combined Love & Share Timeline (All Sessions)'
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            return 'Second: ' + context[0].label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Time (seconds)'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count'
                    },
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Function to download PDF with charts
    function downloadCombinedPDF() {
        // Get chart images as base64
        const responsesChart = document.getElementById('responsesChartCombined');
        const preferencesChart = document.getElementById('preferencesChartCombined');
        const timelineChart = document.getElementById('timelineChartCombined');

        const responsesImg = responsesChart.toDataURL('image/png');
        const preferencesImg = preferencesChart.toDataURL('image/png');
        const timelineImg = timelineChart.toDataURL('image/png');

        // Create form and submit with chart data
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url($action_base_url . 'download_combined_pdf/' . $combined['sessions'][0]['id']); ?>';
        form.target = '_blank';

        const fields = {
            'responseChartImg': responsesImg,
            'preferencesChartImg': preferencesImg,
            'engagementChartImg': timelineImg
        };

        for (const key in fields) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = fields[key];
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
</script>