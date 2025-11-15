<div class="full-page-center">
    <link rel="stylesheet" href="<?= base_url('assets/css/previewpanel.css'); ?>">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-10">

                <div id="project-name-display" class="text-center mb-4">
                    <h4 class="text-dark font-weight-bolder">
                        <span style="display: inline-block; background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; padding: 8px 15px; min-width: 280px; text-align: center;">
                            Preview Name: <span id="previewNameText">...</span>
                        </span>
                    </h4>
                </div>

                <div class="card shadow-lg">
                    <div class="card-body px-4 py-4">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="font-weight-bolder mb-0">Preview Panel</h6>
                            <a href="<?= base_url('auth/logout'); ?>" class="btn btn-white btn-sm shadow-sm mb-0" title="Keluar">
                                <img src="<?= base_url('assets/img/logout.png'); ?>" alt="Logout" style="width: 50px; height: 50px; vertical-align: middle;" class="me-1">

                            </a>
                        </div>

                        <form action="<?= base_url('user/submit_preview'); ?>" method="post">
                            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                            <div class="input-group input-group-static my-4">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" value="<?= isset($user['nama_lengkap']) ? htmlspecialchars($user['nama_lengkap']) : ''; ?>" required>
                            </div>

                            <div class="input-group input-group-static my-4">
                                <label for="jenis_kelamin" class="ms-0">Jenis Kelamin</label>
                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="Laki-laki" <?= (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>

                            <div class="input-group input-group-static my-4">
                                <label>Usia</label>
                                <input type="number" class="form-control" name="usia" value="<?= isset($user['usia']) ? htmlspecialchars($user['usia']) : ''; ?>">
                            </div>

                            <div class="input-group input-group-static my-4">
                                <label>Pekerjaan</label>
                                <input type="text" class="form-control" name="pekerjaan" value="<?= isset($user['pekerjaan']) ? htmlspecialchars($user['pekerjaan']) : ''; ?>">
                            </div>

                            <div class="input-group input-group-static my-4">
                                <label>Bidang Kerja</label>
                                <input type="text" class="form-control" name="bidang_kerja" value="<?= isset($user['bidang_kerja']) ? htmlspecialchars($user['bidang_kerja']) : ''; ?>">
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" id="previewSubmitBtn" class="btn w-50 mt-3" style="background-image: linear-gradient(310deg, rgba(137, 101, 224, 0.6), rgba(94, 114, 228, 0.6)); color: #fff;" disabled>Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    const PUSHER_CONFIG = {
        key: '<?= $this->config->item('pusher_key'); ?>',
        cluster: '<?= $this->config->item('pusher_cluster'); ?>'
    };
</script>

<script src="<?= base_url('assets/js/user/previewpanel.js'); ?>"></script>