</div>
</main>

<script src="<?= base_url('assets/'); ?>/js/core/popper.min.js"></script>
<script src="<?= base_url('assets/'); ?>/js/core/bootstrap.min.js"></script>
<script src="<?= base_url('assets/'); ?>/js/plugins/perfect-scrollbar.min.js"></script>
<script src="<?= base_url('assets/'); ?>/js/plugins/smooth-scrollbar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidenav = document.getElementById('sidenav-main');
        const toggles = document.querySelectorAll('#topbar-sidenav-toggle, #sidenav-toggle, #sidenav-close');

        if (sidenav) {
            toggles.forEach(function(toggle) {
                if (toggle) {
                    toggle.addEventListener('click', function() {
                        sidenav.classList.toggle('active');
                        console.log('Sidebar state toggled by:', this.id); // Untuk debugging
                    });
                }
            });
        }
    });


    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>

<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('assets/'); ?>/js/material-dashboard.min.js?v=3.2.0"></script>
</body>

</html>