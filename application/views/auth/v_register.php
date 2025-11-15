 <div class="main-container">
     <div class="left-panel">
     </div>

     <div class="right-panel">
         <div class="login-wrapper">
             <div class="app-logo">
                 <img src="<?= base_url('assets/img/aseskan-icon.png'); ?>" alt="Logo Aseskan">
                 <h2>BUAT AKUN BARU</h2>
                 <p class="text-muted">Web Control Panel</p>
             </div>

             <form method="post" action="<?= base_url('auth/register'); ?>">
                 <div class="mb-3">
                     <label for="username" class="form-label">Nama Pengguna</label>
                     <input class="form-control form-control-lg" type="text" id="username" name="username" value="<?= set_value('username'); ?>" required>
                     <?= form_error('username', '<small class="text-danger ps-1">', '</small>'); ?>
                 </div>

                 <div class="mb-3">
                     <label for="password" class="form-label">Kata Sandi</label>
                     <input class="form-control form-control-lg" type="password" id="password" name="password" required>
                     <?= form_error('password', '<small class="text-danger ps-1">', '</small>'); ?>
                 </div>

                 <div class="mb-4">
                     <label for="passconf" class="form-label">Konfirmasi Kata Sandi</label>
                     <input class="form-control form-control-lg" type="password" id="passconf" name="passconf" required>
                     <?= form_error('passconf', '<small class="text-danger ps-1">', '</small>'); ?>
                 </div>

                 <div class="d-grid mb-3">
                     <button type="submit" class="btn btn-custom btn-lg">Daftar</button>
                 </div>

                 <div class="text-center mt-4">
                     <p class="mb-0">Sudah punya akun? <a href="<?= base_url('auth') ?>">Masuk di sini</a></p>
                 </div>
             </form>
         </div>
     </div>
 </div>
 </body>

 </html>