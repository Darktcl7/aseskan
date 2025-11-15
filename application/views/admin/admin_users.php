<div class="container py-6">
    <div id="notification" class="position-fixed top-5 end-3 z-index-3" style="z-index: 1056;"></div>

    <div class="row justify-content-center">
        <div class="col-lg-12">

            <div class="card shadow-lg border-0" style="border-radius: 15px;">
                <div class="card-header p-3" style="background-color: #344767; border-radius: 15px 15px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-white">
                            <i class="material-symbols-rounded opacity-10 me-2" style="vertical-align: middle;">group</i>
                            Users Management
                        </h5>
                        <button class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="material-symbols-rounded me-1" style="font-size: 1.2rem;">add</i>Add User
                        </button>
                    </div>
                </div>

                <div class="card-body pt-4 p-3">
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($all_users)) : ?>
                            <?php foreach ($all_users as $u) :
                                $formattedDate = date('d M Y', strtotime($u['created_at']));

                                // Tentukan warna badge berdasarkan peran
                                $badge_class = 'bg-gradient-secondary';
                                $badge_icon = 'person';
                                if ($u['role'] == 'admin') {
                                    $badge_class = 'bg-gradient-danger';
                                    $badge_icon = 'shield_person';
                                }
                                if ($u['role'] == 'client') {
                                    $badge_class = 'bg-gradient-info';
                                    $badge_icon = 'work';
                                }
                                if ($u['role'] == 'user') {
                                    $badge_class = 'bg-gradient-success';
                                    $badge_icon = 'face';
                                }
                            ?>
                                <li id="user-row-<?= $u['id']; ?>" class="list-group-item border-0 d-flex p-4 mb-3 bg-gray-100 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= base_url('assets/profile/') . $u['image']; ?>" class="avatar avatar-lg me-3 border-radius-lg shadow-sm">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark font-weight-bold text-sm"><?= htmlspecialchars($u['username']); ?></h6>
                                            <span class="mb-2 text-xs">
                                                Role:
                                                <span class="badge badge-sm <?= $badge_class; ?> ms-1">
                                                    <i class="material-symbols-rounded me-1" style="font-size: 0.9rem; vertical-align: text-bottom;"><?= $badge_icon; ?></i>
                                                    <?= ucfirst($u['role']); ?>
                                                </span>
                                            </span>
                                            <span class="text-xs">
                                                Registered on:
                                                <span class="text-dark ms-sm-2 font-weight-bold"><?= $formattedDate; ?></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ms-auto text-end d-flex flex-column justify-content-center">
                                        <button class="btn btn-link text-dark px-3 mb-0 edit-user-btn"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal"
                                            data-id="<?= $u['id']; ?>"
                                            data-username="<?= htmlspecialchars($u['username']); ?>"
                                            data-role="<?= $u['role']; ?>"
                                            data-image="<?= base_url('assets/profile/') . $u['image']; ?>">
                                            <i class="material-symbols-rounded text-sm me-2">edit</i>Edit
                                        </button>
                                        <?php if ($this->session->userdata('user_id') != $u['id']) : ?>
                                            <button class="btn btn-link text-danger text-gradient px-3 mb-0 delete-user-btn" data-id="<?= $u['id']; ?>">
                                                <i class="material-symbols-rounded text-sm me-2">delete</i>Delete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <li class="list-group-item border-0 text-center p-4">
                                <p class="text-muted">No users found.</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="addUserForm" action="<?= base_url('admin/add_user'); ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-normal" id="addUserModalLabel">Create a New User</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <h6 class="font-weight-bolder">Account Details</h6>
                    <div class="input-group input-group-outline my-3">
                        <span class="input-group-text"><i class="material-symbols-rounded">person</i></span>
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group input-group-outline mb-3">
                        <span class="input-group-text"><i class="material-symbols-rounded">lock</i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <h6 class="font-weight-bolder mt-4">Assign Role</h6>
                    <div class="row">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="role" id="newUserRoleUser" value="user" autocomplete="off" checked>
                            <label class="btn btn-outline-success w-100 py-3" for="newUserRoleUser">
                                <i class="material-symbols-rounded d-block mb-1">face</i> User
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="role" id="newUserRoleClient" value="client" autocomplete="off">
                            <label class="btn btn-outline-info w-100 py-3" for="newUserRoleClient">
                                <i class="material-symbols-rounded d-block mb-1">work</i> Client
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="editUserForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-normal" id="editUserModalLabel">Edit User Details</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-md-4 text-center border-end">
                            <img id="editUserImage" src="" class="avatar avatar-xxl rounded-circle shadow-sm mb-3">
                            <h6 id="editUsernameDisplay" class="mb-0"></h6>
                            <p class="text-sm text-muted">User Profile</p>
                        </div>
                        <div class="col-md-8">
                            <h6 class="font-weight-bolder">User Details</h6>
                            <div class="input-group input-group-outline my-3 is-filled">
                                <span class="input-group-text"><i class="material-symbols-rounded">person</i></span>
                                <input type="text" class="form-control" name="username" id="editUsername" required>
                            </div>
                            <h6 class="font-weight-bolder mt-4">Assign Role</h6>
                            <div class="row">
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="role" id="roleUser" value="user" autocomplete="off">
                                    <label class="btn btn-outline-success w-100" for="roleUser"><i class="material-symbols-rounded d-block mb-1">face</i> User</label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="role" id="roleClient" value="client" autocomplete="off">
                                    <label class="btn btn-outline-info w-100" for="roleClient"><i class="material-symbols-rounded d-block mb-1">work</i> Client</label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="role" id="roleAdmin" value="admin" autocomplete="off">
                                    <label class="btn btn-outline-danger w-100" for="roleAdmin"><i class="material-symbols-rounded d-block mb-1">shield_person</i> Admin</label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <h6 class="font-weight-bolder mb-0">Manage Password</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="changePasswordToggle">
                                    <label class="form-check-label" for="changePasswordToggle">Change</label>
                                </div>
                            </div>
                            <div id="password-section" style="display: none;">
                                <div class="input-group input-group-outline mt-2">
                                    <span class="input-group-text"><i class="material-symbols-rounded">lock</i></span>
                                    <input type="password" class="form-control" name="password" placeholder="Enter New Password">
                                </div>
                                <small class="form-text text-muted w-100">Leave blank to keep current password.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/utils.js'); ?>"></script>
<script>
    // Objek ini WAJIB ada di file PHP untuk menjembatani data server ke JavaScript
    const AppConfig = {
        baseUrl: '<?= base_url(); ?>',
        csrf: {
            name: '<?= $this->security->get_csrf_token_name(); ?>',
            hash: '<?= $this->security->get_csrf_hash(); ?>'
        }
    };
</script>
<script src="<?= base_url('assets/js/admin/users.js'); ?>"></script>