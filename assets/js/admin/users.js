// Menjalankan semua skrip setelah halaman selesai dimuat
document.addEventListener('DOMContentLoaded', function () {
    // Pastikan objek konfigurasi dari file PHP sudah ada
    if (typeof AppConfig === 'undefined' || !AppConfig.baseUrl) {
        console.error('AppConfig object is not defined or baseUrl is missing.');
        return;
    }

    /**
     * Memperbarui token CSRF setelah setiap request AJAX untuk keamanan.
     */
    function refreshCsrfToken() {
        fetch(`${AppConfig.baseUrl}auth/get_csrf_token`)
            .then(r => r.json())
            .then(d => {
                if (d.csrf_token_name && d.csrf_hash) {
                    AppConfig.csrf.name = d.csrf_token_name;
                    AppConfig.csrf.hash = d.csrf_hash;
                    // Update semua input CSRF yang ada di halaman
                    document.querySelectorAll(`input[name="${d.csrf_token_name}"]`).forEach(i => {
                        i.value = d.csrf_hash;
                    });
                }
            }).catch(e => console.error('Failed to refresh CSRF token:', e));
    }

    // --- Logic untuk menangani submit form (Add & Edit) ---
    const handleFormSubmit = async (form, modal) => {
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Saving...`;

        const formData = new FormData(form);
        formData.set(AppConfig.csrf.name, AppConfig.csrf.hash);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'An error occurred.');

            showNotification(result.message); // Notifikasi toast sukses
            modal.hide();
            setTimeout(() => location.reload(), 1500); // Reload halaman untuk melihat perubahan

        } catch (error) {
            showNotification(error.message, true); // Notifikasi toast error
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            refreshCsrfToken();
        }
    };

    // --- Inisialisasi Modal Add User ---
    const addUserModalEl = document.getElementById('addUserModal');
    if (addUserModalEl) {
        const addUserModal = new bootstrap.Modal(addUserModalEl);
        const addUserForm = document.getElementById('addUserForm');
        addUserForm.addEventListener('submit', (e) => {
            e.preventDefault();
            handleFormSubmit(addUserForm, addUserModal);
        });

        // Reset form "Add User" saat modal ditutup
        addUserModalEl.addEventListener('hidden.bs.modal', function () {
            addUserForm.reset();
        });
    }

    // --- Inisialisasi Modal Edit User ---
    const editUserModalEl = document.getElementById('editUserModal');
    if (editUserModalEl) {
        const editModalInstance = new bootstrap.Modal(editUserModalEl);
        const editUserForm = document.getElementById('editUserForm');
        editUserForm.addEventListener('submit', (e) => {
            e.preventDefault();
            handleFormSubmit(editUserForm, editModalInstance);
        });

        // Mengisi data ke dalam modal saat ditampilkan
        const passwordSection = editUserModalEl.querySelector('#password-section');
        const passwordToggle = editUserModalEl.querySelector('#changePasswordToggle');

        passwordToggle.addEventListener('change', function () {
            passwordSection.style.display = this.checked ? 'block' : 'none';
        });

        editUserModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const modalForm = editUserModalEl.querySelector('#editUserForm');

            modalForm.action = `${AppConfig.baseUrl}admin/edit_user/${button.dataset.id}`;
            editUserModalEl.querySelector('#editUserImage').src = button.dataset.image;
            editUserModalEl.querySelector('#editUsernameDisplay').textContent = button.dataset.username;
            editUserModalEl.querySelector('#editUsername').value = button.dataset.username;

            const roleRadio = editUserModalEl.querySelector(`input[name="role"][value="${button.dataset.role}"]`);
            if (roleRadio) roleRadio.checked = true;

            passwordToggle.checked = false;
            passwordSection.style.display = 'none';
            modalForm.querySelector('input[name="password"]').value = '';
        });
    }

    // --- Logic untuk Delete User via AJAX ---
    document.querySelector('.list-group')?.addEventListener('click', function (e) {
        const deleteButton = e.target.closest('.delete-user-btn');
        if (deleteButton) {
            const userId = deleteButton.dataset.id;

            Swal.fire({
                title: 'Anda Yakin?',
                text: "Data pengguna yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#344767',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append(AppConfig.csrf.name, AppConfig.csrf.hash);
                    try {
                        const response = await fetch(`${AppConfig.baseUrl}admin/delete_user/${userId}`, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            body: formData
                        });
                        const res = await response.json();
                        if (!response.ok) throw new Error(res.message);

                        Swal.fire('Dihapus!', 'Data pengguna berhasil dihapus.', 'success');

                        const row = document.getElementById(`user-row-${userId}`);
                        if (row) {
                            row.style.transition = 'opacity 0.5s ease';
                            row.style.opacity = '0';
                            setTimeout(() => row.remove(), 500);
                        }
                    } catch (error) {
                        Swal.fire('Gagal!', error.message || 'Terjadi kesalahan.', 'error');
                    } finally {
                        refreshCsrfToken();
                    }
                }
            });
        }
    });
});