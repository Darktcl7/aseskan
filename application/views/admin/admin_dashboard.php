<div class="container-fluid py-4">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin_dashboard.css'); ?>">
    <div class="row justify-content-center">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-warning shadow-light text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">groups</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize text-dark">Preview Panel</p>
                            <h4 class="mb-0" id="total-users-display">-</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3 d-flex justify-content-around">
                        <div class="text-center">
                            <h6 class="mb-0" id="male-count-display">0</h6>
                            <p class="text-secondary text-xs font-weight-bold mb-0">Man</p>
                        </div>
                        <div class="text-center">
                            <h6 class="mb-0" id="female-count-display">0</h6>
                            <p class="text-secondary text-xs font-weight-bold mb-0">Woman</p>
                        </div>
                        <div class="text-center">
                            <h6 class="mb-0" id="average-age-display">0</h6>
                            <p class="text-secondary text-xs font-weight-bold mb-0">Average age</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-warning shadow-light text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">thumb_up</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize text-dark">Preview Engagement Metrics</p>
                            <h4 class="mb-0" id="total-engagement-display">-</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3 d-flex justify-content-around">
                        <div class="text-center">
                            <h6 class="mb-0" id="total-like-count-chart">0</h6>
                            <p class="text-secondary text-xs font-weight-bold mb-0">Loveable</p>
                        </div>
                        <div class="text-center">
                            <h6 class="mb-0" id="share-count-display">0</h6>
                            <p class="text-secondary text-xs font-weight-bold mb-0">Shareable</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-warning shadow-light text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">chat</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize text-dark">Preview Engagement Metrics</p>
                            <h4 class="mb-0" id="total-comment-reaction-display">-</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3 d-flex justify-content-around">
                        <div class="text-center">
                            <h6 class="mb-0" id="like-count">0</h6>
                            <p class="text-secondary text-xs font-weight-bold mb-0">👍 Like</p>
                        </div>
                        <div class="text-center">
                            <h6 class="mb-0" id="dislike-count">0</h6>
                            <p class="text-secondary text-xs font-weight-bold mb-0">👎 Dislike</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-warning shadow-light text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">sentiment_satisfied</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize text-dark">Preview Responses</p>
                            <h4 class="mb-0" id="dominant-response">-</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3 d-flex justify-content-around">
                        <div class="text-center">
                            <h6 class="mb-0 text-success" id="percentage-senang">0%</h6>
                            <p class="text-secondary text-xs font-weight-bold mb-0">😊</p>
                        </div>
                        <div class="text-center">
                            <h6 class="mb-0 text-warning" id="percentage-biasa">0%</h6>
                            <p class="text-secondary text-xs font-weight-bold mb-0">😐</p>
                        </div>
                        <div class="text-center">
                            <h6 class="mb-0 text-danger" id="percentage-sedih">0%</h6>
                            <p class="text-secondary text-xs font-weight-bold mb-0">😢</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-6">
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-light shadow-warning  pt-4 pb-3">
                            <h6 class="text-dark text-capitalize ps-3"><i class="material-symbols-rounded opacity-10 me-1">settings_suggest</i>Controller, Dashboard, Viewing Procedure</h6>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-lg-7 mb-lg-0 mb-4">
                                <div class="card bg-gray-100 h-100">
                                    <div id="notification" class="position-fixed top-5 end-3 z-index-3"></div>
                                    <div class="card-body d-flex flex-column justify-content-center text-center">
                                        <div class="stopwatch-circle mx-auto mb-4">
                                            <span id="display" class="font-weight-bolder">00:00:00</span>
                                        </div>
                                        <div class="stopwatch-controls px-md-4">
                                            <div class="btn-group w-100" role="group" aria-label="Stopwatch Controls">
                                                <button id="startPauseBtn" class="btn btn-lg bg-gradient-success w-100 mb-0" disabled>Start</button>
                                                <button id="stopBtn" class="btn btn-lg bg-gradient-danger w-100 mb-0 mx-2" disabled>Stop</button>
                                                <button id="resetBtn" class="btn btn-lg bg-gradient-info w-100 mb-0">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="card h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <div id="previewNameForm">
                                            <h6 class="font-weight-bolder">Preview Name</h6>
                                            <div class="input-group input-group-outline my-3">
                                                <label class="form-label">Input</label>
                                                <input type="text" class="form-control" id="previewNameInput">
                                            </div>
                                            <button id="setPreviewNameBtn" class="btn w-100" style="background-image: linear-gradient(310deg, rgba(137, 101, 224, 0.6), rgba(94, 114, 228, 0.6)); color: #fff;">Set Preview Name</button>
                                        </div>
                                        <div id="adminPreviewNameDisplay" style="display: none;">
                                            <i class="material-symbols-rounded text-success" style="font-size: 3rem;">task_alt</i>
                                            <h5 class="font-weight-bolder mt-2" id="adminPreviewNameText"></h5>
                                            <p class="mb-1 text-sm text-secondary">Nama Preview Aktif</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row mt-6">
            <div class="col-lg-8 mb-lg-0 mb-4">
                <div class="card h-100">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-warning shadow-warning  pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Preview Timeline</h6>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart">
                                    <canvas id="likesChart" class="chart-canvas" height="300"></canvas>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-lg-0 mb-4">
                <div class="card h-100">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-warning shadow-warning  pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Preview Log</h6>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6 pe-1">
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Log Loveable</th>
                                        </tr>
                                    </thead>
                                    <tbody id="like-reports-table"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-6 ps-1">
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Log Shareable</th>
                                        </tr>
                                    </thead>
                                    <tbody id="share-reports-table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <div class="row mt-6">
            <div class="col-lg-4 mb-lg-0 mb-4">
                <div class="card h-100">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-warning shadow-warning  pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Preview Comments</h6>
                        </div>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <ul id="realtime-comments-list" class="list-group list-group-flush border-radius-lg custom-scrollbar" style="max-height: 250px; overflow-y: auto; border: 1px solid #e9ecef;">
                            <li class="list-group-item border-0 px-3">
                                <p class="text-sm text-muted text-center">Waiting for comments...</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-lg-0 mb-4">
                <div class="card h-100">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-warning shadow-warning  pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Preview Preferences</h6>
                        </div>
                    </div>
                    <div class=" d-flex flex-column justify-content-center text-center border-end pe-2">
                        <div class="w-full" style="height: 270px;">
                            <canvas id="preferencesChart" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-lg-0 mb-4">
                <div class="card h-100">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-warning shadow-warning  pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Preview Responses</h6>
                        </div>
                    </div>
                    <div class=" d-flex flex-column justify-content-center text-center border-end pe-2">
                        <div class="chart mx-auto" style="height: 290px; max-width: 290px;">
                            <canvas id="responseChart" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Objek untuk mengirim data dari PHP ke JavaScript
    const AppConfig = {
        baseUrl: '<?= base_url(); ?>',
        csrf: {
            name: '<?= $this->security->get_csrf_token_name(); ?>',
            hash: '<?= $this->security->get_csrf_hash(); ?>'
        },
        pusherKey: '<?= $this->config->item('pusher_key'); ?>',
        pusherCluster: '<?= $this->config->item('pusher_cluster'); ?>'
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
    Chart.register(ChartDataLabels);
</script>
<script src="<?= base_url('assets/js/admin/dashboard.js'); ?>"></script>