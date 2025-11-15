<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Pusher\Pusher;
use Dompdf\Dompdf;

class Admin extends CI_Controller
{
    private $pusher;

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') != 'admin') {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini!');
            redirect('auth');
        }
        $this->load->model('Auth_model');
        $this->load->model('Stopwatch_model');
        $this->load->model('User_model');
        $this->load->model('Project_continuation_model');

        $options = [
            'cluster' => $this->config->item('pusher_cluster'),
            'useTLS' => true
        ];
        $this->pusher = new Pusher(
            $this->config->item('pusher_key'),
            $this->config->item('pusher_secret'),
            $this->config->item('pusher_app_id'),
            $options
        );
    }

    private function _prepare_sidebar_data($current_title)
    {
        return [
            ['type' => 'heading', 'title' => 'Main Menu'],
            ['type' => 'item', 'title' => 'Dashboard', 'url' => base_url('admin'), 'icon' => 'dashboard', 'is_active' => ($current_title == 'Admin')],
            ['type' => 'item', 'title' => 'Files', 'url' => base_url('admin/files'), 'icon' => 'folder', 'is_active' => ($current_title == 'Files')],
            ['type' => 'item', 'title' => 'Users Management', 'url' => base_url('admin/users'), 'icon' => 'manage_accounts', 'is_active' => ($current_title == 'Users Management')],
        ];
    }

    public function index()
    {
        $data['title'] = 'Admin';
        $user_object = $this->Auth_model->get_user_by_username($this->session->userdata('username'));
        $data['user'] = json_decode(json_encode($user_object), true);
        $data['sidebar_menu'] = $this->_prepare_sidebar_data($data['title']);
        $data['csrf_token_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        // Mengambil data proyek lanjutan dari session
        $data['continuing_project_id'] = $this->session->userdata('continuing_project_id');
        $data['continuing_project_name'] = $this->session->userdata('continuing_project_name');
        $data['continuing_project_data'] = $this->session->userdata('continuing_project_data');

        if (!isset($data['user']['image'])) {
            $data['user']['image'] = 'default.jpg';
        }
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/admin_dashboard', $data);
        $this->load->view('templates/footer', $data);
        // $this->load->view('admin/admin_js', $data);
    }

    public function users()
    {
        $data['title'] = 'Users Management';
        $user_object = $this->Auth_model->get_user_by_username($this->session->userdata('username'));
        $data['user'] = json_decode(json_encode($user_object), true);
        $data['sidebar_menu'] = $this->_prepare_sidebar_data($data['title']);

        // Ambil semua data user dari User_model
        $data['all_users'] = $this->User_model->get_all_users();

        if (!isset($data['user']['image'])) {
            $data['user']['image'] = 'default.jpg';
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/admin_users', $data); // Load view baru
        $this->load->view('templates/footer');
    }

    public function add_user()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[user,client]');

        if ($this->form_validation->run() == FALSE) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
            return;
        }

        $data = [
            'username' => $this->input->post('username'),
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role'     => $this->input->post('role')
        ];

        if ($this->User_model->create_user($data)) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'User baru berhasil ditambahkan! Halaman akan dimuat ulang.']));
        } else {
            $this->output->set_status_header(500)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menambahkan user baru ke database.']));
        }
    }

    /**
     * Memproses pengeditan data pengguna (sudah diubah untuk AJAX)
     */
    public function edit_user($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[user,client,admin]');

        if ($this->form_validation->run() == FALSE) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
            return;
        }

        $data = [
            'username' => $this->input->post('username'),
            'role'     => $this->input->post('role')
        ];

        $password = $this->input->post('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Ambil data user sebelum diupdate untuk perbandingan role
        $old_user = $this->User_model->get_user_by_id($id);
        $old_role = $old_user ? $old_user['role'] : '';

        if ($this->User_model->update_user($id, $data)) {
            // Cek jika role berubah dari client ke role lain
            $new_role = $this->input->post('role');
            if ($old_role == 'client' && $new_role != 'client') {
                $this->Stopwatch_model->revoke_all_access_for_user($id);
            }

            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Data user berhasil diperbarui! Halaman akan dimuat ulang.']));
        } else {
            $this->output->set_status_header(500)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data user.']));
        }
    }

    /**
     * Menghapus pengguna (sudah diubah untuk AJAX)
     */
    public function delete_user($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if ($id == $this->session->userdata('user_id')) {
            $this->output->set_status_header(403)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Anda tidak dapat menghapus akun Anda sendiri.']));
            return;
        }

        if ($this->User_model->delete_user($id)) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'User berhasil dihapus!']));
        } else {
            $this->output->set_status_header(500)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menghapus user dari database.']));
        }
    }


    public function files()
    {
        $data['title'] = 'Files';
        $user_object = $this->Auth_model->get_user_by_username($this->session->userdata('username'));
        $data['user'] = json_decode(json_encode($user_object), true);
        $data['sidebar_menu'] = $this->_prepare_sidebar_data('Files');
        $data['files'] = $this->Stopwatch_model->get_all_stats();
        $data['action_base_url'] = 'admin/';
        $data['is_admin_view'] = true;
        $data['clients'] = $this->Stopwatch_model->get_clients();
        if (!isset($data['user']['image'])) {
            $data['user']['image'] = 'default.jpg';
        }
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/admin_files', $data);
        $this->load->view('templates/footer');
    }


    public function get_file_clients($file_id)
    {
        //  request AJAX
        $client_ids = $this->Stopwatch_model->get_file_access($file_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'client_ids' => $client_ids]));
    }


    public function update_file_access()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $file_id = $this->input->post('file_id');
        $client_ids = $this->input->post('client_ids') ?: [];

        if (empty($file_id)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'File ID tidak boleh kosong.']));
            return;
        }

        $result = $this->Stopwatch_model->update_file_access($file_id, $client_ids);

        if ($result) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Hak akses client berhasil diperbarui.']));
        } else {
            $this->output->set_status_header(500)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal memperbarui hak akses di database.']));
        }
    }

    public function get_report_details($id)
    {
        $report = $this->Stopwatch_model->get_stats_by_id((int)$id);
        $comments = $this->Stopwatch_model->get_comments_by_video_id((int)$id);
        if (!$report) {
            $this->output->set_status_header(404)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Laporan tidak ditemukan.']));
            return;
        }
        $comment_likes = 0;
        $comment_dislikes = 0;
        if (!empty($comments)) {
            foreach ($comments as $comment) {
                if ($comment['is_like'] == 1) $comment_likes++;
                if ($comment['is_dislike'] == 1) $comment_dislikes++;
            }
        }

        // Check if this project has multiple sessions (is continuation or has continuations)
        $combined_stats = null;
        $all_sessions = null;
        $has_multiple_sessions = false;
        
        if (isset($report['is_continuation']) || isset($report['original_project_id'])) {
            $all_sessions = $this->Project_continuation_model->get_all_sessions((int)$id);
            if (count($all_sessions) > 1) {
                $has_multiple_sessions = true;
                $combined_stats = $this->Project_continuation_model->get_combined_stats((int)$id);
            }
        }

        $data = [
            'report' => $report,
            'comments' => $comments,
            'comment_stats' => ['likes' => $comment_likes, 'dislikes' => $comment_dislikes],
            'all_sessions' => $all_sessions,
            'combined_stats' => $combined_stats,
            'has_multiple_sessions' => $has_multiple_sessions
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * View combined report dari semua sessions
     */
    public function view_combined_report($project_id)
    {
        // Get combined stats
        $combined = $this->Project_continuation_model->get_combined_stats($project_id);
        
        if (!$combined) {
            $this->session->set_flashdata('error', 'Project tidak ditemukan atau tidak memiliki multiple sessions.');
            redirect('admin/files');
            return;
        }

        // Get all comments from all sessions
        $all_comments = [];
        foreach ($combined['sessions'] as $session) {
            $session_comments = $this->Stopwatch_model->get_comments_by_video_id($session['id']);
            foreach ($session_comments as $comment) {
                $comment['session_name'] = $session['nama_video'];
                $comment['session_number'] = $session['project_session'];
                $all_comments[] = $comment;
            }
        }

        // Prepare like and share details for Report Details section
        $all_likes = [];
        $all_shares = [];
        foreach ($combined['sessions'] as $session) {
            $like_details = json_decode($session['like_details'], true) ?? [];
            foreach ($like_details as $second => $count) {
                $all_likes[] = [
                    'second' => $second,
                    'count' => $count,
                    'session' => $session['project_session']
                ];
            }

            $share_details = json_decode($session['share_details'], true) ?? [];
            foreach ($share_details as $second => $count) {
                $all_shares[] = [
                    'second' => $second,
                    'count' => $count,
                    'session' => $session['project_session']
                ];
            }
        }

        // Sort by second (descending)
        usort($all_likes, function($a, $b) { return $b['second'] - $a['second']; });
        usort($all_shares, function($a, $b) { return $b['second'] - $a['second']; });

        // Prepare data for view
        $data['title'] = 'Combined Report';
        $user_object = $this->Auth_model->get_user_by_username($this->session->userdata('username'));
        $data['user'] = json_decode(json_encode($user_object), true);
        $data['sidebar_menu'] = $this->_prepare_sidebar_data('Files');
        
        $data['combined'] = $combined;
        $data['all_comments'] = $all_comments;
        $data['all_likes'] = $all_likes;
        $data['all_shares'] = $all_shares;
        $data['project_name'] = $combined['sessions'][0]['nama_video'] ?? 'Combined Report';
        $data['action_base_url'] = 'admin/';
        $data['back_to_files_url'] = 'admin/files';

        if (!isset($data['user']['image'])) {
            $data['user']['image'] = 'default.jpg';
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/combined_report', $data);
        $this->load->view('templates/footer');
    }

    public function delete_report()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // Ambil ID dari POST request
        $report_id = $this->input->post('id');

        if (!$report_id || !is_numeric($report_id)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID laporan tidak valid.']));
            return;
        }

        // Panggil fungsi model yang baru untuk menghapus semuanya
        $deleted = $this->Stopwatch_model->delete_report_fully($report_id);

        if ($deleted) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Laporan berhasil dihapus.']));
        } else {
            $this->output->set_status_header(500)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menghapus laporan dari database.']));
        }
    }



    public function continue_project()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $project_id = $this->input->post('project_id');
        $confirm = $this->input->post('confirm'); // untuk konfirmasi create

        if (empty($project_id) || !is_numeric($project_id)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid project ID.']));
            return;
        }

        // Jika belum konfirmasi, kembalikan data project dan sessions untuk ditampilkan
        if (empty($confirm)) {
            $project_data = $this->Stopwatch_model->get_stats_by_id($project_id);
            $sessions = $this->Project_continuation_model->get_all_sessions($project_id);

            if ($project_data) {
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'confirm',
                        'project' => $project_data,
                        'sessions' => $sessions,
                        'next_session' => count($sessions) + 1
                    ]));
            } else {
                $this->output->set_status_header(404)->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Project not found.']));
            }
            return;
        }

        // Jika sudah konfirmasi, create continuation project
        $new_project_id = $this->Project_continuation_model->create_continuation($project_id);

        if ($new_project_id) {
            $new_project = $this->Stopwatch_model->get_stats_by_id($new_project_id);
            
            // Set session untuk continuing project
            $this->session->set_userdata('continuing_project_id', $new_project_id);
            $this->session->set_userdata('continuing_project_name', $new_project['nama_video']);
            $this->session->set_userdata('continuing_project_data', $new_project);
            $this->session->set_userdata('original_project_id', $new_project['original_project_id']);

            $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => "New session created: {$new_project['nama_video']}. Redirecting to dashboard...",
                    'new_project_id' => $new_project_id
                ]));
        } else {
            $this->output->set_status_header(500)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to create continuation project.']));
        }
    }

    public function clear_continuing_project()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // Get continuing project ID from session before clearing
        $continuing_project_id = $this->session->userdata('continuing_project_id');

        // If there's a continuing project, delete it from database
        if ($continuing_project_id) {
            $this->load->model('Stopwatch_model');
            
            // Delete the project record fully (including related data)
            $deleted = $this->Stopwatch_model->delete_report_fully($continuing_project_id);
            
            if ($deleted) {
                log_message('info', "Deleted continuing project ID: {$continuing_project_id} - User cancelled continuation");
            }
        }

        // Clear session data
        $this->session->unset_userdata('continuing_project_id');
        $this->session->unset_userdata('continuing_project_name');
        $this->session->unset_userdata('continuing_project_data');
        $this->session->unset_userdata('original_project_id');

        $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Continuing project cleared and deleted successfully.'
            ]));
    }

    public function my_profile()
    {
        $data['title'] = 'My Profile';
        $user_object = $this->Auth_model->get_user_by_username($this->session->userdata('username'));
        $data['user'] = json_decode(json_encode($user_object), true);

        // Mengambil data tambahan dari previewpanel
        $preview_data = $this->Auth_model->get_preview_data_by_user_id($user_object->id);
        if ($preview_data) {
            $data['user'] = array_merge($data['user'], $preview_data);
        }

        $data['sidebar_menu'] = $this->_prepare_sidebar_data($data['title']);
        if (!isset($data['user']['image'])) {
            $data['user']['image'] = 'default.jpg';
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/admin_profile', $data);
        $this->load->view('templates/footer', $data);
    }

    public function update_profile()
    {
        $this->load->library('upload');
        $username = $this->session->userdata('username');
        $user_object = $this->Auth_model->get_user_by_username($username);

        // 1. Data untuk tabel 'previewpanel'
        $preview_data = [
            'user_id'       => $user_object->id,
            'nama_lengkap'  => $this->input->post('nama_lengkap'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'usia'          => $this->input->post('usia'),
            'pekerjaan'     => $this->input->post('pekerjaan'),
            'bidang_kerja'  => $this->input->post('bidang_kerja')
        ];
        $this->Auth_model->save_preview_data($preview_data);

        // 2. Data untuk tabel 'users' (hanya jika ada upload gambar)
        $user_data = [];
        if (!empty($_FILES['image']['name'])) {
            $config['upload_path']   = './assets/profile/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size']      = 2048;
            $config['file_name']     = 'profile_' . time();
            $this->upload->initialize($config);

            if ($this->upload->do_upload('image')) {
                $upload_data = $this->upload->data();
                $user_data['image'] = $upload_data['file_name'];

                if ($user_object->image != 'default.jpg' && file_exists($config['upload_path'] . $user_object->image)) {
                    unlink($config['upload_path'] . $user_object->image);
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Gagal mengunggah foto: ' . $this->upload->display_errors('', '') . '</div>');
                redirect('admin/my_profile');
                return;
            }
        }

        if (!empty($user_data)) {
            $this->Auth_model->update_user($username, $user_data);
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success">Profil berhasil diperbarui!</div>');
        redirect('admin/my_profile');
    }

    public function change_password()
    {
        if ($this->session->userdata('role') != 'admin') {
            redirect('auth');
        }
        $current_password = $this->input->post('current_password');
        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');
        if ($new_password !== $confirm_password) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Password baru dan konfirmasi password tidak cocok!</div>');
            redirect('admin/my_profile');
        }
        $user = $this->Auth_model->get_user_by_username($this->session->userdata('username'));
        if (!$this->Auth_model->verify_password($current_password, $user->password)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Password saat ini salah!</div>');
            redirect('admin/my_profile');
        }
        $this->Auth_model->update_password($this->session->userdata('username'), password_hash($new_password, PASSWORD_DEFAULT));
        $this->session->set_flashdata('message', '<div class="alert alert-success">Password berhasil diganti!</div>');
        redirect('admin/my_profile');
    }

    public function set_preview_name()
    {
        $preview_name = $this->input->post('preview_name');

        if (empty($preview_name)) {
            $this->output->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Nama preview tidak boleh kosong.']));
            return;
        }

        // Broadcast nama preview ke semua user
        $this->pusher->trigger('stopwatch-channel', 'preview-name-update', ['name' => $preview_name]);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Nama preview berhasil diatur.']));
    }



    public function start_timer()
    {
        $data = [
            'startTime' => $this->input->post('startTime'),
            'elapsedTime' => $this->input->post('elapsedTime'),
            'video_id' => $this->Stopwatch_model->get_latest_video_id()
        ];
        $this->pusher->trigger('stopwatch-channel', 'start-event', $data);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success']));
    }

    public function pause_timer()
    {
        $this->pusher->trigger('stopwatch-channel', 'pause-event', ['message' => 'paused']);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success']));
    }

    public function stop_timer()
    {
        $this->pusher->trigger('stopwatch-channel', 'stop-event', ['message' => 'stopped']);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success']));
    }

    public function reset_timer()
    {
        $this->pusher->trigger('stopwatch-channel', 'reset-event', ['message' => 'reset']);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success']));
    }

    public function save_report()
    {
        $nama_video = $this->input->post('nama_video');
        $love_count = $this->input->post('love_count');
        $share_count = $this->input->post('share_count');
        $response_senang = $this->input->post('response_senang');
        $response_biasa = $this->input->post('response_biasa');
        $response_sedih = $this->input->post('response_sedih');
        $like_details = $this->input->post('like_details');
        $share_details = $this->input->post('share_details');
        $comments_json = $this->input->post('comments');
        $session_male = $this->input->post('session_male_count');
        $session_female = $this->input->post('session_female_count');
        $session_avg_age = $this->input->post('session_average_age');
        $pref_senang = $this->input->post('pref_senang');
        $pref_biasa = $this->input->post('pref_biasa');
        $pref_marah = $this->input->post('pref_marah');

        if (empty($nama_video)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Nama laporan tidak boleh kosong.']));
            return;
        }

        // Check if we're continuing a project
        $continuing_project_id = $this->session->userdata('continuing_project_id');
        $is_continuing = !empty($continuing_project_id);

        if ($is_continuing) {
            // ===== MODE: LANJUTKAN PROYEK (UPDATE SESSION YANG SUDAH ADA) =====
            // Session sudah dibuat saat click "Continue", sekarang hanya UPDATE data survey
            
            $current_project = $this->Stopwatch_model->get_stats_by_id($continuing_project_id);
            if (!$current_project) {
                $this->output->set_status_header(404)->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Session project tidak ditemukan.']));
                return;
            }

            // Update data untuk session yang sudah ada (bukan insert baru!)
            $stats_data = [
                'love_count' => (int)$love_count,
                'share_count' => (int)$share_count,
                'response_senang' => (int)$response_senang,
                'response_biasa' => (int)$response_biasa,
                'response_sedih' => (int)$response_sedih,
                'like_details' => $like_details,
                'share_details' => $share_details,
                'session_male_count' => (int)$session_male,
                'session_female_count' => (int)$session_female,
                'session_average_age' => (float)$session_avg_age,
                'pref_senang' => (int)$pref_senang,
                'pref_biasa' => (int)$pref_biasa,
                'pref_marah' => (int)$pref_marah,
                'session_end_date' => date('Y-m-d H:i:s'), // Mark session as completed
                'last_updated' => date('Y-m-d H:i:s')
            ];

            $this->db->trans_start();
            
            // UPDATE existing session (tidak insert baru!)
            $this->db->where('id', $continuing_project_id);
            $this->db->update('stopwatch_stats', $stats_data);
            $updated_video_id = $continuing_project_id; // Use existing ID

        } else {
            // ===== MODE: PROYEK BARU (INSERT BARU) =====
            $stats_data = [
                'nama_video' => $nama_video,
                'love_count' => (int)$love_count,
                'response_senang' => (int)$response_senang,
                'response_biasa' => (int)$response_biasa,
                'response_sedih' => (int)$response_sedih,
                'share_count' => (int)$share_count,
                'like_details' => $like_details,
                'share_details' => $share_details,
                'session_male_count' => (int)$session_male,
                'session_female_count' => (int)$session_female,
                'session_average_age' => (float)$session_avg_age,
                'pref_senang' => (int)$pref_senang,
                'pref_biasa' => (int)$pref_biasa,
                'pref_marah' => (int)$pref_marah,
                'project_session' => 1,
                'is_continuation' => 0,
                'session_start_date' => date('Y-m-d H:i:s'),
                'last_updated' => date('Y-m-d H:i:s')
            ];

            $this->db->trans_start();
            $this->db->insert('stopwatch_stats', $stats_data);
            $updated_video_id = $this->db->insert_id();

            // Set original_project_id ke ID sendiri (untuk project baru)
            $this->db->where('id', $updated_video_id);
            $this->db->update('stopwatch_stats', ['original_project_id' => $updated_video_id]);
        }

        // Save comments (untuk kedua mode)
        if ($updated_video_id) {
            $comments = json_decode($comments_json, true) ?? [];
            foreach ($comments as $comment) {
                $comment_data = [
                    'video_id' => $updated_video_id,
                    'username' => $comment['username'] ?? 'Anonim',
                    'komentar' => $comment['comment_text'] ?? '',
                    'is_like' => isset($comment['is_like']) && $comment['is_like'] == '1' ? 1 : 0,
                    'is_dislike' => isset($comment['is_dislike']) && $comment['is_dislike'] == '1' ? 1 : 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->Stopwatch_model->save_comment($comment_data);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->output->set_status_header(500)->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan laporan: Transaksi gagal.']));
                return;
            }

            // Clear continuing project session after successful save
            if ($is_continuing) {
                $this->session->unset_userdata('continuing_project_id');
                $this->session->unset_userdata('continuing_project_name');
                $this->session->unset_userdata('continuing_project_data');
                $this->session->unset_userdata('original_project_id');
            }

            // Broadcast update
            $this->pusher->trigger('stopwatch-channel', 'session-update', ['video_id' => $updated_video_id]);

            $message = $is_continuing ?
                'Data sesi berhasil disimpan! Data akan digabungkan dengan sesi sebelumnya.' :
                'Laporan berhasil disimpan!';

            $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => $message,
                    'video_id' => $updated_video_id,
                    'is_continuing' => $is_continuing
                ]));
        } else {
            $this->db->trans_rollback();
            $this->output->set_status_header(500)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan laporan ke database.']));
        }
    }

    public function download_report_pdf($id)
    {
        try {
            // 1. Ambil data yang diperlukan dari model
            $report = $this->Stopwatch_model->get_stats_by_id((int)$id);
            $comments = $this->Stopwatch_model->get_comments_by_video_id((int)$id);

            if (!$report) {
                show_error('Laporan tidak ditemukan.', 404);
                return;
            }

            // 2. Proses data untuk view
            $comment_likes = 0;
            $comment_dislikes = 0;
            if (!empty($comments)) {
                foreach ($comments as $comment) {
                    if ($comment['is_like'] == 1) $comment_likes++;
                    if ($comment['is_dislike'] == 1) $comment_dislikes++;
                }
            }

            $data['report'] = $report;
            $data['comments'] = $comments;
            $data['comment_stats'] = ['likes' => $comment_likes, 'dislikes' => $comment_dislikes];

            // Ambil gambar chart dari POST request
            $data['responseChartImg'] = $this->input->post('responseChartImg');
            $data['preferencesChartImg'] = $this->input->post('preferencesChartImg');
            $data['engagementChartImg'] = $this->input->post('engagementChartImg');

            // 3. Load view ke dalam variabel
            $html = $this->load->view('admin/pdf_template', $data, TRUE);

            // 4. Konfigurasi dan generate PDF dengan Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // 5. Kirim PDF ke browser untuk di-download
            $filename = "Laporan_Aseskan_" . str_replace(' ', '_', $report['nama_video']) . ".pdf";
            // Hapus output buffer sebelum mengirimkan PDF
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            $dompdf->stream($filename, ['Attachment' => 1]);
        } catch (Exception $e) {
            // Tampilkan error jika terjadi
            die('Error saat membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download Combined PDF dari semua sessions
     */
    public function download_combined_pdf($project_id)
    {
        try {
            // Get combined stats
            $combined = $this->Project_continuation_model->get_combined_stats($project_id);
            
            if (!$combined) {
                show_error('Project tidak ditemukan atau tidak memiliki multiple sessions.', 404);
                return;
            }

            // Get all comments from all sessions
            $all_comments = [];
            foreach ($combined['sessions'] as $session) {
                $session_comments = $this->Stopwatch_model->get_comments_by_video_id($session['id']);
                foreach ($session_comments as $comment) {
                    $comment['session_name'] = $session['nama_video'];
                    $comment['session_number'] = $session['project_session'];
                    $all_comments[] = $comment;
                }
            }

            // Prepare like and share details
            $all_likes = [];
            $all_shares = [];
            foreach ($combined['sessions'] as $session) {
                $like_details = json_decode($session['like_details'], true) ?? [];
                foreach ($like_details as $second => $count) {
                    $all_likes[] = [
                        'second' => $second,
                        'count' => $count,
                        'session' => $session['project_session']
                    ];
                }

                $share_details = json_decode($session['share_details'], true) ?? [];
                foreach ($share_details as $second => $count) {
                    $all_shares[] = [
                        'second' => $second,
                        'count' => $count,
                        'session' => $session['project_session']
                    ];
                }
            }

            usort($all_likes, function($a, $b) { return $b['second'] - $a['second']; });
            usort($all_shares, function($a, $b) { return $b['second'] - $a['second']; });

            $data['combined'] = $combined;
            $data['all_comments'] = $all_comments;
            $data['all_likes'] = $all_likes;
            $data['all_shares'] = $all_shares;
            $data['project_name'] = $combined['sessions'][0]['nama_video'] ?? 'Combined Report';

            // Get chart images from POST request
            $data['responseChartImg'] = $this->input->post('responseChartImg');
            $data['preferencesChartImg'] = $this->input->post('preferencesChartImg');
            $data['engagementChartImg'] = $this->input->post('engagementChartImg');

            // Load view ke dalam variabel
            $html = $this->load->view('admin/combined_pdf_template', $data, TRUE);

            // Konfigurasi dan generate PDF dengan Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Kirim PDF ke browser untuk di-download
            $filename = "Combined_Report_" . str_replace(' ', '_', $data['project_name']) . "_" . $combined['total_sessions'] . "_Sessions.pdf";
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            $dompdf->stream($filename, ['Attachment' => 1]);
        } catch (Exception $e) {
            log_message('error', 'Combined PDF generation failed: ' . $e->getMessage());
            die('Error saat membuat Combined PDF: ' . $e->getMessage());
        }
    }

}
