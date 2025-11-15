<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white" id="sidenav-main">
    <div class="sidenav-header d-flex align-items-center justify-content-center">
        <i class="fas fa-bars p-3 cursor-pointer text-dark d-xl-none" id="sidenav-toggle"></i>
        <a class="navbar-brand m-0" href="#" target="_blank">
            <img src="<?= base_url('assets/'); ?>/img/aseskan-icon.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold text-dark">Aseskan</span>
        </a>
        <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 d-xl-none" id="sidenav-close"></i>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <?php foreach ($sidebar_menu as $menu) : ?>
                <?php if ($menu['type'] == 'heading') : ?>
                    <li class="nav-item mt-3">
                        <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-8"><?= $menu['title']; ?></h6>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link text-dark <?= $menu['is_active'] ? 'active' : ''; ?>" href="<?= $menu['url']; ?>">
                            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-symbols-rounded"><?= $menu['icon']; ?></i>
                            </div>
                            <span class="nav-link-text ms-1"><?= $menu['title']; ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if ($this->session->userdata('role') == 'admin') : ?>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-8">Account</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="<?= base_url('auth/logout'); ?>">
                        <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded">logout</i>
                        </div>
                        <span class="nav-link-text ms-1">Logout</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <?php if ($this->session->userdata('role') == 'admin') : ?>
        <div class="sidenav-footer position-absolute w-100 bottom-0 mb-4">
            <div class="mx-3">
                <div class="card bg-transparent shadow-none">
                    <div class="card-body text-center p-3 w-100">
                        <form id="saveReportForm" class="mt-3">
                            <div class="input-group input-group-outline my-3">
                                <label class="form-label">preview name</label>
                                <input type="text" class="form-control" id="reportName" required>
                            </div>
                            <button id="saveReportBtn" type="submit" class="btn w-100 mt-3" style="background-image: linear-gradient(310deg, rgba(137, 101, 224, 0.6), rgba(94, 114, 228, 0.6)); color: #fff;">
                                <i class="material-symbols-rounded me-1" style="font-size: 1.1rem; vertical-align: middle;">save</i>
                                Save
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</aside>