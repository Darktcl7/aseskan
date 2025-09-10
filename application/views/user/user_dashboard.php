<div class="container-fluid py-4">
    <link rel="stylesheet" href="<?= base_url('assets/css/user_dashboard.css'); ?>">

    <div id="heart-container"></div>
    <div id="notification" class="position-fixed top-5 end-3 z-index-3"></div>
    <div id="project-name-display" class="text-center mb-4">
        <h4 class="text-dark font-weight-bolder">
            <span style="display: inline-block; background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; padding: 8px 15px; min-width: 280px; text-align: center;">
                Preview Name: <span id="previewNameText">...</span>
            </span>
        </h4>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 border-end border-light">
                            <h6 class="text-center">Engagement Metrics</h6>
                            <div class="d-flex justify-content-around align-items-center mt-4 pt-2" style="min-height: 150px;">
                                <div class="text-center">
                                    <button id="loveBtn" class="engagement-button" disabled>
                                        <i class="fas fa-heart text-danger engagement-icon"></i>
                                    </button>
                                    <p class="text-sm mb-0 mt-2">Likeable</p>
                                </div>
                                <div class="text-center">
                                    <button id="shareBtn" class="engagement-button" disabled>
                                        <i class="fas fa-share-alt text-info engagement-icon"></i>
                                    </button>
                                    <p class="text-sm mb-0 mt-2">Shareable</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center">Preview Responses</h6>
                            <div class="d-flex justify-content-center gap-3 gap-md-4 align-items-center mt-4 pt-2" style="min-height: 150px;">
                                <button class="response-button reaction-btn" data-reaction="senang" disabled><span class="badge rounded-circle p-3 large-emoji">😊</span></button>
                                <button class="response-button reaction-btn" data-reaction="biasa" disabled><span class="badge rounded-circle p-3 large-emoji">😐</span></button>
                                <button class="response-button reaction-btn" data-reaction="sedih" disabled><span class="badge rounded-circle p-3 large-emoji">😞</span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="stopwatch-wrapper">
    <div class="stopwatch-container-user">
        <p id="status" class="text-sm">Menunggu Perintah dari Admin...</p>
        <div class="stopwatch-display-user">
            <div id="display-card" class="font-weight-bolder">00:00:00</div>
        </div>
    </div>
</div>

<script>
    const APP_CONFIG = {
        baseUrl: '<?= base_url(); ?>',
        csrfTokenName: '<?= $this->security->get_csrf_token_name(); ?>',
        csrfHash: '<?= $this->security->get_csrf_hash(); ?>',
        pusherKey: '<?= $this->config->item('pusher_key'); ?>',
        pusherCluster: '<?= $this->config->item('pusher_cluster'); ?>',
        initialVideoId: '<?= $initial_video_id ?? null; ?>'
    };
</script>

<script src="<?= base_url('assets/js/user/user_dashboard.js'); ?>"></script>