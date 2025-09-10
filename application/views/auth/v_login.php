<div class="limiter">
    <div class="container-login100" style="background-image: url('<?= base_url('Login_v16/'); ?>images/bg-01.jpg');">
        <div class="wrap-login100 p-t-30 p-b-50">
            <span class="login100-form-title p-b-41">
                Account Login
            </span>

            <!-- Tampilkan pesan error dari session -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form class="login100-form validate-form p-b-33 p-t-5" method="post" action="<?= base_url('auth/process_login'); ?>">

                <div class="wrap-input100 validate-input" data-validate="Enter Username">
                    <input class="input100" type="text" id="username" name="username" placeholder="username" value="<?= set_value('username'); ?>">
                    <span class="focus-input100" data-placeholder="&#xe82a;"></span>
                    <?= form_error('username', '<small class="text-danger pl-3">', '</small>'); ?>
                </div>

                <div class="wrap-input100 validate-input" data-validate="Enter password">
                    <input class="input100" type="password" id="password" name="password" placeholder="Password">
                    <span class="focus-input100" data-placeholder="&#xe80f;"></span>
                    <?= form_error('password', '<small class="text-danger pl-3">', '</small>'); ?>
                </div>

                <div class="container-login100-form-btn m-t-32">
                    <button class="login100-form-btn">
                        Login
                    </button>
                </div>

                <div class="text-center">
                    <a class="small" href="<?= base_url('auth/register') ?>">Register account!</a>
                </div>

            </form>
        </div>