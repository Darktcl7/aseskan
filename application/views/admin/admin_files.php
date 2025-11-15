<div class="container py-6">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin_files.css'); ?>">
    
    <style>
        /* Badge styling untuk info project */
        .badge.badge-sm.d-flex {
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 6px;
            white-space: nowrap;
        }
        
        .badge.badge-sm.d-flex i {
            font-size: 14px;
        }
        
        /* Button action styling */
        .btn-group-action .btn {
            border-width: 1.5px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
        }
        
        .btn-group-action .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn-group-action .btn i {
            font-size: 16px;
        }
        
        /* Responsive untuk mobile */
        @media (max-width: 767px) {
            .badge.badge-sm.d-flex {
                padding: 4px 8px;
                font-size: 0.7rem;
            }
            
            .badge.badge-sm.d-flex i {
                font-size: 12px;
            }
            
            /* Di mobile, tombol hanya tampilkan icon */
            .btn-group-action .btn {
                min-width: 36px;
                padding: 6px 10px;
            }
            
            .btn-group-action .btn i {
                font-size: 18px;
                margin: 0 !important;
            }
            
            /* Hide text label di mobile */
            .btn-group-action .d-none.d-md-inline {
                display: none !important;
            }
        }
        
        /* Desktop view - show text */
        @media (min-width: 768px) {
            .btn-group-action .btn {
                padding: 6px 14px;
            }
        }
        
        /* Child sessions styling */
        .child-sessions {
            transition: all 0.3s ease;
        }
        
        .child-sessions .list-group-item {
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .toggle-sessions-btn {
            transition: all 0.3s ease;
        }
        
        .toggle-sessions-btn.expanded i {
            transform: rotate(180deg);
        }
        
        .toggle-sessions-btn:hover {
            color: #17c1e8 !important;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-lg-8 mb-4">

            <!-- Search Controls -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-3">
                    <div class="input-group input-group-outline is-focused">
                        <label class="form-label">Cari project...</label>
                        <input type="text" class="form-control" id="searchInput">
                    </div>
                </div>
            </div>

            <!-- File List Card -->
            <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, #f9fafc 0%, #ffffff 100%);">
                <div class="card-body pt-4 p-3">
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($files)) : ?>
                            <?php 
                            // Group files by project (Session 1 dengan child sessions-nya)
                            $groupedFiles = [];
                            
                            // PASS 1: Create all parent projects first (Session 1)
                            foreach ($files as $file) {
                                $sessionNumber = isset($file['project_session']) ? $file['project_session'] : 1;
                                
                                if ($sessionNumber == 1) {
                                    $groupedFiles[$file['id']] = [
                                        'parent' => $file,
                                        'children' => []
                                    ];
                                }
                            }
                            
                            // PASS 2: Add child sessions to their parents
                            foreach ($files as $file) {
                                $sessionNumber = isset($file['project_session']) ? $file['project_session'] : 1;
                                $isContinuation = isset($file['is_continuation']) && $file['is_continuation'] == 1;
                                
                                if ($sessionNumber > 1 && $isContinuation) {
                                    $originalId = $file['original_project_id'] ?? null;
                                    if ($originalId && isset($groupedFiles[$originalId])) {
                                        $groupedFiles[$originalId]['children'][] = $file;
                                    }
                                }
                            }
                            
                            // Sort children by session number
                            foreach ($groupedFiles as &$group) {
                                if (!empty($group['children'])) {
                                    usort($group['children'], function($a, $b) {
                                        $sessionA = $a['project_session'] ?? 1;
                                        $sessionB = $b['project_session'] ?? 1;
                                        return $sessionA - $sessionB;
                                    });
                                }
                            }
                            unset($group);
                            
                            // Render grouped files
                            foreach ($groupedFiles as $projectId => $group) :
                                $file = $group['parent'];
                                $children = $group['children'];
                                
                                $formattedDate = date('d M Y, H:i', strtotime($file['last_updated']));
                                $isContinuation = isset($file['is_continuation']) && $file['is_continuation'] == 1;
                                $sessionNumber = isset($file['project_session']) ? $file['project_session'] : 1;

                                // Get display name (remove " - Session X" suffix if exists)
                                $displayName = $file['nama_video'];
                                if (preg_match('/^(.+?)\s*-\s*Session\s+\d+$/', $displayName, $matches)) {
                                    $displayName = trim($matches[1]);
                                }

                                // Check if this project has multiple sessions
                                $hasMultipleSessions = !empty($children);
                            ?>
                                <li class="list-group-item border-0 d-flex p-4 mb-3 bg-gray-100 border-radius-lg"
                                    data-date="<?= $file['last_updated']; ?>"
                                    data-name="<?= htmlspecialchars($file['nama_video']); ?>">

                                    <div class="d-flex flex-column flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <h6 class="mb-0 text-sm project-name"><?= htmlspecialchars($displayName); ?></h6>

                                            <!-- Badge untuk Session (tidak tampil untuk Session 1) -->
                                            <?php if ($sessionNumber != 1): ?>
                                                <span class="badge badge-sm bg-gradient-info ms-2">
                                                    Session <?= $sessionNumber; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <span class="badge badge-sm bg-gradient-secondary d-flex align-items-center gap-1">
                                                <i class="material-symbols-rounded text-xs">calendar_today</i>
                                                <span><?= $formattedDate; ?></span>
                                            </span>
                                            <span class="badge badge-sm bg-gradient-dark d-flex align-items-center gap-1">
                                                <i class="material-symbols-rounded text-xs">tag</i>
                                                <span>ID: <?= $file['id']; ?></span>
                                            </span>
                                        </div>
                                        
                                        <!-- Toggle button untuk expand/collapse child sessions (di baris baru) -->
                                        <?php if ($hasMultipleSessions): ?>
                                            <div class="mb-2">
                                                <button class="btn btn-sm btn-link text-secondary p-0 toggle-sessions-btn" 
                                                        data-project-id="<?= $file['id']; ?>"
                                                        title="Show/Hide Sessions">
                                                    <i class="material-symbols-rounded text-sm me-1">expand_more</i>
                                                    <span class="text-xs"><?= count($children); ?> Session(s)</span>
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Info untuk Continuation Project -->
                                        <?php if ($isContinuation && isset($file['parent_project_id'])): ?>
                                            <span class="text-xs text-info mt-1">
                                                <i class="material-symbols-rounded text-xs">link</i>
                                                Continued from project #<?= $file['parent_project_id']; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="ms-auto text-end">
                                        <div class="btn-group-action d-flex flex-wrap gap-2 justify-content-end">
                                            <?php if ($is_admin_view) : ?>
                                                <!-- View Details Button (Admin only) -->
                                                <a class="btn btn-sm btn-outline-dark"
                                                    data-id="<?= $file['id']; ?>"
                                                    data-name="<?= htmlspecialchars($file['nama_video']); ?>"
                                                    data-date="<?= $formattedDate; ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailModal"
                                                    title="View Details">
                                                    <i class="material-symbols-rounded text-sm me-1">visibility</i>
                                                    <span class="d-none d-md-inline">Details</span>
                                                </a>
                                            <?php else : ?>
                                                <!-- View Combined Report Button (Client only) -->
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="<?= base_url('client/view_combined_report/' . $file['id']); ?>"
                                                    title="View Combined Report">
                                                    <i class="material-symbols-rounded text-sm me-1">analytics</i>
                                                    <span class="d-none d-md-inline">Combined</span>
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($is_admin_view) : ?>
                                                <!-- View Combined Report Button (only for Session 1 with multiple sessions) -->
                                                <?php if ($sessionNumber == 1 && $hasMultipleSessions): ?>
                                                    <a class="btn btn-sm btn-outline-primary"
                                                        href="<?= base_url('admin/view_combined_report/' . $file['id']); ?>"
                                                        title="View Combined Report from All Sessions">
                                                        <i class="material-symbols-rounded text-sm me-1">analytics</i>
                                                        <span class="d-none d-md-inline">Combined</span>
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Continue Project Button (only for Session 1) -->
                                                <?php if ($sessionNumber == 1): ?>
                                                    <a class="btn btn-sm btn-outline-success continue-project-btn"
                                                        href="#"
                                                        data-project-id="<?= $file['id']; ?>"
                                                        data-project-name="<?= htmlspecialchars($file['nama_video']); ?>"
                                                        title="Continue this project">
                                                        <i class="material-symbols-rounded text-sm me-1">play_arrow</i>
                                                        <span class="d-none d-md-inline">Continue</span>
                                                    </a>

                                                    <!-- Manage Access Button (only for Session 1) -->
                                                    <a class="btn btn-sm btn-outline-secondary manage-access-btn"
                                                        data-file-id="<?= $file['id']; ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#manageAccessModal"
                                                        title="Manage Client Access">
                                                        <i class="material-symbols-rounded text-sm me-1">group</i>
                                                        <span class="d-none d-md-inline">Access</span>
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Delete Button -->
                                                <a class="btn btn-sm btn-outline-danger delete-report-btn"
                                                    href="#"
                                                    data-id="<?= $file['id']; ?>"
                                                    title="Delete Report">
                                                    <i class="material-symbols-rounded text-sm me-1">delete</i>
                                                    <span class="d-none d-md-inline">Delete</span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                                
                                <!-- Child Sessions (Hidden by default) -->
                                <?php if ($hasMultipleSessions): ?>
                                    <div class="child-sessions" id="sessions-<?= $file['id']; ?>" style="display: none;">
                                        <?php foreach ($children as $childFile): 
                                            $childDate = date('d M Y, H:i', strtotime($childFile['last_updated']));
                                            $childSession = isset($childFile['project_session']) ? $childFile['project_session'] : 2;
                                            
                                            $childDisplayName = $childFile['nama_video'];
                                            if (preg_match('/^(.+?)\s*-\s*Session\s+\d+$/', $childDisplayName, $matches)) {
                                                $childDisplayName = trim($matches[1]);
                                            }
                                        ?>
                                            <li class="list-group-item border-0 d-flex p-3 mb-2 bg-light border-radius-lg ms-5"
                                                style="border-left: 3px solid #17c1e8 !important;"
                                                data-date="<?= $childFile['last_updated']; ?>"
                                                data-name="<?= htmlspecialchars($childFile['nama_video']); ?>">
                                                
                                                <div class="d-flex flex-column flex-grow-1">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="material-symbols-rounded text-xs text-secondary me-2">subdirectory_arrow_right</i>
                                                        <h6 class="mb-0 text-sm project-name"><?= htmlspecialchars($childDisplayName); ?></h6>
                                                        <span class="badge badge-sm bg-gradient-info ms-2">
                                                            Session <?= $childSession; ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-center gap-3 mb-2 ms-4">
                                                        <span class="badge badge-sm bg-gradient-secondary d-flex align-items-center gap-1">
                                                            <i class="material-symbols-rounded text-xs">calendar_today</i>
                                                            <span><?= $childDate; ?></span>
                                                        </span>
                                                        <span class="badge badge-sm bg-gradient-dark d-flex align-items-center gap-1">
                                                            <i class="material-symbols-rounded text-xs">tag</i>
                                                            <span>ID: <?= $childFile['id']; ?></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="ms-auto text-end">
                                                    <div class="btn-group-action d-flex flex-wrap gap-2 justify-content-end">
                                                        <?php if ($is_admin_view) : ?>
                                                            <!-- View Details Button -->
                                                            <a class="btn btn-sm btn-outline-dark"
                                                                data-id="<?= $childFile['id']; ?>"
                                                                data-name="<?= htmlspecialchars($childFile['nama_video']); ?>"
                                                                data-date="<?= $childDate; ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#detailModal"
                                                                title="View Details">
                                                                <i class="material-symbols-rounded text-sm me-1">visibility</i>
                                                                <span class="d-none d-md-inline">Details</span>
                                                            </a>
                                                            
                                                            <!-- Delete Button -->
                                                            <a class="btn btn-sm btn-outline-danger delete-report-btn"
                                                                href="#"
                                                                data-id="<?= $childFile['id']; ?>"
                                                                title="Delete Report">
                                                                <i class="material-symbols-rounded text-sm me-1">delete</i>
                                                                <span class="d-none d-md-inline">Delete</span>
                                                            </a>
                                                        <?php else : ?>
                                                            <!-- View Combined Report Button (Client) -->
                                                            <a class="btn btn-sm btn-outline-primary"
                                                                href="<?= base_url('client/view_combined_report/' . $childFile['id']); ?>"
                                                                title="View Combined Report">
                                                                <i class="material-symbols-rounded text-sm me-1">analytics</i>
                                                                <span class="d-none d-md-inline">Combined</span>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <li class="list-group-item border-0 text-center p-4 bg-light rounded">
                                <i class="material-icons text-secondary" style="font-size: 48px;">folder_off</i>
                                <p class="mt-2 text-secondary fw-bold mb-0">There are no report files yet.</p>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <!-- FITUR BARU: Empty State (after filtering) -->
                    <div id="emptyState" style="display: none;" class="text-center p-4">
                        <i class="material-icons text-secondary" style="font-size: 48px;">search_off</i>
                        <p class="mt-2 text-secondary fw-bold mb-0">No files found matching your search.</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Profile Card (Right Side) -->
        <div class="col-lg-4">
            <div class="card card-profile">
                <img src="<?= base_url('assets/img/bg-profile.jpg'); ?>" alt="Image placeholder" class="card-img-top">
                <div class="row justify-content-center">
                    <div class="col-4 col-lg-4 order-lg-2">
                        <div class="mt-n4 mt-lg-n6 mb-4 mb-lg-0">
                            <a href="javascript:;">
                                <img src="<?= base_url('assets/img/aseskan_files.png'); ?>"
                                    class="rounded-circle img-fluid border border-2 border-white">
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-0">
                    <div class="text-center mt-4">
                        <h5>
                            <span class="font-weight-light"><?= $user['username']; ?></span>
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

                        <!-- FITUR BARU: Statistics (opsional) -->
                        <?php if ($is_admin_view && !empty($files)): ?>
                            <div class="card mt-4">
                                <div class="card-body p-3">
                                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-2">Statistics</h6>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-xs">Total Projects:</span>
                                        <span class="text-xs font-weight-bold"><?= count($files); ?></span>
                                    </div>
                                    <?php
                                    $continuationCount = 0;
                                    foreach ($files as $f) {
                                        if (isset($f['is_continuation']) && $f['is_continuation'] == 1) {
                                            $continuationCount++;
                                        }
                                    }
                                    ?>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-xs">Continued Projects:</span>
                                        <span class="text-xs font-weight-bold"><?= $continuationCount; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <a href="<?= base_url('auth/logout'); ?>"
                            class="btn btn-white btn-sm shadow-sm w-100 mt-4"
                            title="Keluar">
                            <img src="<?= base_url('assets/img/logout.png'); ?>"
                                alt="Logout"
                                style="width: 50px; height: 50px; margin-right: 8px; vertical-align: text-bottom;">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Report Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <p class="text-center">Loading data...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="downloadPdfBtn">
                    <i class="material-symbols-rounded me-1">download</i>Download PDF
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Manage Access Modal (Admin Only) -->
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
                                                <img src="<?= base_url('assets/profile/') . ($client['image'] ?? 'default.jpg'); ?>"
                                                    alt="<?= htmlspecialchars($client['username']); ?>"
                                                    class="avatar avatar-sm me-3 border-radius-lg shadow-sm">
                                                <label class="form-check-label mb-0" for="client_check_<?= $client['id']; ?>">
                                                    <?= htmlspecialchars($client['username']); ?>
                                                </label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    name="client_ids[]"
                                                    value="<?= $client['id']; ?>"
                                                    id="client_check_<?= $client['id']; ?>">
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
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn bg-gradient-primary" id="saveAccessBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Scripts - URUTAN PENTING! -->

<!-- 1. UTILITIES -->
<script src="<?= base_url('assets/js/utils.js'); ?>"></script>

<!-- 2. CHART.JS (HARUS PERTAMA sebelum plugin) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- 3. CHART.JS PLUGINS -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<!-- 4. REGISTER CHART PLUGINS -->
<script>
    if (typeof Chart !== 'undefined') {
        Chart.register(ChartDataLabels);
        console.log('✅ Chart.js loaded and ChartDataLabels registered');
    } else {
        console.error('❌ Chart.js not loaded!');
    }
</script>

<!-- 5. APP CONFIG -->
<script>
    // AppConfig untuk menjembatani data dari PHP ke JavaScript
    const AppConfig = {
        baseUrl: '<?= base_url(); ?>',
        actionBaseUrl: '<?= base_url($action_base_url); ?>',
        isAdminView: <?= $is_admin_view ? 'true' : 'false'; ?>,
        csrf: {
            name: '<?= $this->security->get_csrf_token_name(); ?>',
            hash: '<?= $this->security->get_csrf_hash(); ?>'
        }
    };

    console.log('✅ AppConfig loaded:', AppConfig);
</script>



<!-- 6. MAIN FILES.JS (TERAKHIR) -->
<script src="<?= base_url('assets/js/admin/files.js?v=') . time(); ?>"></script>

<!-- 7. TOGGLE SESSIONS SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle toggle sessions button
    document.querySelectorAll('.toggle-sessions-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const projectId = this.dataset.projectId;
            const sessionsDiv = document.getElementById(`sessions-${projectId}`);
            const icon = this.querySelector('i');
            
            if (sessionsDiv) {
                if (sessionsDiv.style.display === 'none') {
                    // Expand
                    sessionsDiv.style.display = 'block';
                    this.classList.add('expanded');
                } else {
                    // Collapse
                    sessionsDiv.style.display = 'none';
                    this.classList.remove('expanded');
                }
            }
        });
    });
    
    console.log('✅ Toggle sessions functionality loaded');
});
</script>