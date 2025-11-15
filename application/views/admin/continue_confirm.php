<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Continue Project: <?= $project['nama_video'] ?></h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Existing Sessions:</strong>
                <ul>
                    <?php foreach ($sessions as $session): ?>
                        <li>
                            Session <?= $session['project_session'] ?> -
                            <?= $session['nama_video'] ?>
                            (Started: <?= $session['session_start_date'] ?? 'N/A' ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <p>Apakah Anda yakin ingin membuat continuation project?</p>
            <p>Project baru akan dibuat sebagai Session <?= count($sessions) + 1 ?></p>
            <form method="POST">
                <input type="hidden" name="confirm" value="1">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-check"></i> Yes, Continue Project
                </button>
                <a href="<?= base_url('admin/files') ?>" class="btn btn-secondary">
                    <i class="fa fa-times"></i> Cancel
                </a>
            </form>
        </div>
    </div>
</div>