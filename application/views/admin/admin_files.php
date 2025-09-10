<div class="container py-6">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin_files.css'); ?>">

    <div class="row justify-content-center">
        <div class="col-lg-7 mb-4">

            <div class="card shadow-lg border-0" style=" background: linear-gradient(135deg, #f9fafc 0%, #ffffff 100%);">

                <div class="card-body pt-4 p-3">
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($files)) : ?>
                            <?php foreach ($files as $file) :
                                $formattedDate = date('d M Y, H:i', strtotime($file['last_updated']));
                            ?>
                                <li class="list-group-item border-0 d-flex p-4 mb-3 bg-gray-100 border-radius-lg">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-3 text-sm"><?= htmlspecialchars($file['nama_video']); ?></h6>
                                        <span class="mb-2 text-xs">Date & Time:
                                            <span class="text-dark font-weight-bold ms-sm-2"><?= $formattedDate; ?></span>
                                        </span>
                                        <span class="text-xs">ID Preview:
                                            <span class="text-dark font-weight-bold ms-sm-2"><?= $file['id']; ?></span>
                                        </span>
                                    </div>
                                    <div class="ms-auto text-end">
                                        <a class="btn btn-link text-dark px-3 mb-0" data-id="<?= $file['id']; ?>" data-name="<?= htmlspecialchars($file['nama_video']); ?>" data-date="<?= $formattedDate; ?>" data-bs-toggle="modal" data-bs-target="#detailModal">
                                            <i class="material-symbols-rounded text-sm me-2">visibility</i>Details
                                        </a>

                                        <?php if ($is_admin_view) : ?>
                                            <a class="btn btn-link text-dark px-3 mb-0 manage-access-btn" data-file-id="<?= $file['id']; ?>" data-bs-toggle="modal" data-bs-target="#manageAccessModal">
                                                <i class="material-symbols-rounded text-sm me-2">group</i>Accsess
                                            </a>
                                            <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="#" data-id="<?= $file['id']; ?>">
                                                <i class="material-symbols-rounded text-sm me-2">delete</i>Delete
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <li class="list-group-item border-0 text-center p-4 bg-light rounded">
                                <i class="material-icons text-secondary" style="font-size: 48px;">folder_off</i>
                                <p class="mt-2 text-secondary fw-bold mb-0">There are no report files yet.</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        </div>
        <div class="col-lg-5">
            <div class="card card-profile">
                <img src="<?= base_url('assets/img/bg-profile.jpg'); ?>" alt="Image placeholder" class="card-img-top">
                <div class="row justify-content-center">
                    <div class="col-4 col-lg-4 order-lg-2">
                        <div class="mt-n4 mt-lg-n6 mb-4 mb-lg-0">
                            <a href="javascript:;">
                                <img src="<?= base_url('assets/img/aseskan_files.png'); ?>" class="rounded-circle img-fluid border border-2 border-white"> </a>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-0">

                    <div class="text-center mt-4">
                        <h5>
                            <span class="font-weight-light"> <?= $user['username']; ?></span>
                        </h5>
                        <div class="h6 font-weight-300">
                            <i class="ni location_pin mr-2"></i>Surabaya, Indonesia
                        </div>
                        <div class="h6 mt-4">
                            <i class="ni business_briefcase-24 mr-2"></i>Controller, Dashboard, Viewing Procedure
                        </div>
                        <div>
                            <i class="ni education_hat mr-2"></i>Aseskan, 2025
                        </div>
                        <a href="<?= base_url('auth/logout'); ?>" class="btn btn-white btn-sm shadow-sm w-100 mt-4" title="Keluar">
                            <img src="<?= base_url('assets/img/logout.png'); ?>" alt="Logout" style="width: 50px; height: 50px; margin-right: 8px; vertical-align: text-bottom;">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <p class="text-center">Loading data...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="downloadPdfBtn">
                    Download PDF
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Closed</button>
            </div>
        </div>
    </div>
</div>

<?php if ($is_admin_view) : ?>
    <div class="modal fade" id="manageAccessModal" tabindex="-1" role="dialog" aria-labelledby="manageAccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-normal" id="manageAccessModalLabel">Manage Client Access</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="manageAccessForm">
                        <input type="hidden" id="csrfTokenName" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="file_id" id="modalFileId">
                        <p>Select clients that can view this file:</p>
                        <div class="client-list" style="max-height: 350px; overflow-y: auto;">
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($clients)) : ?>
                                    <?php foreach ($clients as $client) : ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-1">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= base_url('assets/profile/') . ($client['image'] ?? 'default.jpg'); ?>" alt="<?= htmlspecialchars($client['username']); ?>" class="avatar avatar-sm me-3 border-radius-lg shadow-sm">
                                                <label class="form-check-label mb-0" for="client_check_<?= $client['id']; ?>">
                                                    <?= htmlspecialchars($client['username']); ?>
                                                </label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="client_ids[]" value="<?= $client['id']; ?>" id="client_check_<?= $client['id']; ?>">
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <li class="list-group-item text-center text-muted">There are no registered clients.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Closed</button>
                    <button type="button" class="btn bg-gradient-primary" id="saveAccessBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this report? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<script src="<?= base_url('assets/js/utils.js'); ?>"></script>

<script>
    // Membuat objek AppConfig untuk menjembatani data dari PHP ke JavaScript
    const AppConfig = {
        baseUrl: '<?= base_url(); ?>',
        actionBaseUrl: '<?= base_url($action_base_url); ?>',
        isAdminView: <?= $is_admin_view ? 'true' : 'false'; ?>,
        csrf: {
            name: '<?= $this->security->get_csrf_token_name(); ?>',
            hash: '<?= $this->security->get_csrf_hash(); ?>'
        }
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
    Chart.register(ChartDataLabels);
</script>
<script src="<?= base_url('assets/js/admin/files.js'); ?>"></script>