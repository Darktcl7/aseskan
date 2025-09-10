<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">


            <div class="card shadow-lg mb-4" id="preferencesCard">
                <div class="card-header p-3 text-center">
                    <h5 class="card-title font-weight-bolder mb-0">Preview Preferences</h5>
                    <p class="text-sm text-muted mb-0">Bagaimana perasaan Anda secara keseluruhan terhadap konten ini?</p>
                </div>
                <div class="card-body pt-2 pb-3">
                    <div class="d-flex justify-content-center gap-3 gap-md-4 align-items-center">
                        <button class="btn btn-outline-success btn-lg preference-btn" data-preference="senang">😊 Statisfied</button>
                        <button class="btn btn-outline-warning btn-lg preference-btn" data-preference="biasa">😐 Normal</button>
                        <button class="btn btn-outline-danger btn-lg preference-btn" data-preference="marah">😠 Disappointed</button>
                    </div>
                </div>
            </div>

            <div class="card shadow-lg">
                <div class="card-header p-3 text-center">
                    <h5 class="card-title font-weight-bolder mb-0">Preview Comments</h5>
                    <p class="text-sm text-muted mb-0">Sesi telah berakhir. Silakan berikan komentar Anda.</p>
                </div>
                <div class="card-body px-4 py-4">
                    <div id="notification" class="position-fixed top-5 end-3 z-index-3"></div>
                    <form id="commentForm">
                        <input type="hidden" name="<?= $csrf_token_name; ?>" value="<?= $csrf_hash; ?>">
                        <input type="hidden" id="videoId" name="video_id" value="<?= htmlspecialchars($video_id); ?>">
                        <div class="form-group mb-4">
                            <textarea class="form-control" id="commentInput" name="comment_text" placeholder="Tulis komentar Anda..." required rows="4"></textarea>
                        </div>
                        <div class="d-flex justify-content-center mb-4">
                            <div>
                                <button type="button" class="btn btn-outline-success comment-btn" id="likeBtn" title="Like">
                                    <i class="fas fa-thumbs-up me-1"></i> Suka
                                </button>
                                <button type="button" class="btn btn-outline-danger comment-btn" id="dislikeBtn" title="Dislike">
                                    <i class="fas fa-thumbs-down me-1"></i> Tidak Suka
                                </button>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" id="submitCommentBtn" class="btn w-50 mt-3" style="background-image: linear-gradient(310deg, rgba(137, 101, 224, 0.6), rgba(94, 114, 228, 0.6)); color: #fff;">Kirim Komentar</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const APP_CONFIG = {
        baseUrl: '<?= base_url(); ?>',
        csrfTokenName: '<?= $csrf_token_name; ?>',
        csrfHash: '<?= $csrf_hash; ?>',
        pusherKey: '<?= $this->config->item('pusher_key'); ?>',
        pusherCluster: '<?= $this->config->item('pusher_cluster'); ?>'
    };
</script>

<script src="<?= base_url('assets/js/user/previewcomments.js'); ?>"></script>