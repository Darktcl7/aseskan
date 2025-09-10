<div class="container-fluid py-4">
    <div id="notification" class="position-fixed top-5 end-3 z-index-3"></div>
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Profil Saya</h6>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="<?= base_url('assets/profile/') . ($user['image'] ?? 'default.jpg'); ?>" alt="Profile Image" class="img-fluid rounded-circle shadow-sm mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    Edit Profil
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    Ganti Password
                                </button>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Username:</strong> <?= $user['username']; ?></li>
                                <li class="list-group-item"><strong>Nama Lengkap:</strong> <?= !empty($user['nama_lengkap']) ? $user['nama_lengkap'] : '<span class="text-muted">Belum diisi</span>'; ?></li>
                                <li class="list-group-item"><strong>Jenis Kelamin:</strong> <?= !empty($user['jenis_kelamin']) ? $user['jenis_kelamin'] : '<span class="text-muted">Belum diisi</span>'; ?></li>
                                <li class="list-group-item"><strong>Usia:</strong> <?= !empty($user['usia']) ? $user['usia'] : '<span class="text-muted">Belum diisi</span>'; ?></li>
                                <li class="list-group-item"><strong>Pekerjaan:</strong> <?= !empty($user['pekerjaan']) ? $user['pekerjaan'] : '<span class="text-muted">Belum diisi</span>'; ?></li>
                                <li class="list-group-item"><strong>Bidang Kerja:</strong> <?= !empty($user['bidang_kerja']) ? $user['bidang_kerja'] : '<span class="text-muted">Belum diisi</span>'; ?></li>
                                <li class="list-group-item"><strong>Terdaftar Sejak:</strong> <?= date('d F Y', strtotime($user['created_at'])); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($this->session->userdata('role') . '/update_profile'); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= $user['username']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= $user['nama_lengkap'] ?? ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                            <option value="Laki-laki" <?= (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="Perempuan" <?= (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usia">Usia</label>
                        <input type="number" class="form-control" id="usia" name="usia" value="<?= $user['usia'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="pekerjaan">Pekerjaan</label>
                        <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" value="<?= $user['pekerjaan'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="bidang_kerja">Bidang Kerja</label>
                        <input type="text" class="form-control" id="bidang_kerja" name="bidang_kerja" value="<?= $user['bidang_kerja'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="image">Foto Profil</label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Ganti Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($this->session->userdata('role') . '/change_password'); ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label for="current_password">Password Saat Ini</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationContainer = document.getElementById('notification');

        function showNotification(message, isError = false) {
            if (!notificationContainer) return;
            const toastId = 'toast-' + Date.now();
            const toastHTML = `
                <div id="${toastId}" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                    <div class="toast-header">
                        <i class="material-symbols-rounded me-2 ${isError ? 'text-danger' : 'text-success'}">${isError ? 'error' : 'check_circle'}</i>
                        <strong class="me-auto">${isError ? 'Gagal' : 'Sukses'}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">${message}</div>
                </div>`;
            notificationContainer.insertAdjacentHTML('beforeend', toastHTML);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
            toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
        }

        // Cek flashdata dari PHP dan tampilkan sebagai toast
        <?php if ($this->session->flashdata('message')) : ?>
            const flashMessage = `<?= $this->session->flashdata('message'); ?>`;
            // Cek apakah pesan mengandung kata "Gagal" untuk menentukan isError
            const isError = flashMessage.includes('Gagal');
            showNotification(flashMessage.replace(/<\/?div[^>]*>|<\/?alert[^>]*>/g, ''), isError);
        <?php endif; ?>
    });
</script>