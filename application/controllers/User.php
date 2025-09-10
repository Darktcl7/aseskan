<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';

use Pusher\Pusher;

class User extends CI_Controller
{
    private $pusher;

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') != 'user') {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini!');
            redirect('auth');
        }
        $this->load->helper('jwt_helper');
        $this->load->model('Auth_model');
        $this->load->model('Stopwatch_model');
        $options = array(
            'cluster' => $this->config->item('pusher_cluster'),
            'useTLS' => true
        );
        $this->pusher = new Pusher(
            $this->config->item('pusher_key'),
            $this->config->item('pusher_secret'),
            $this->config->item('pusher_app_id'),
            $options
        );
    }


    public function index()
    {
        $this->session->unset_userdata('preview_completed');
        $data['title'] = 'Preview Panel';
        $username_session = $this->session->userdata('username');
        $user_object = $this->Auth_model->get_user_by_username($username_session);
        $data['user'] = json_decode(json_encode($user_object), true);

        $preview_data = $this->Auth_model->get_preview_data_by_user_id($user_object->id);
        if ($preview_data) {
            $data['user'] = array_merge($data['user'], $preview_data);
        }

        $data['pusher_app_key'] = $this->config->item('pusher_key');
        $data['pusher_cluster'] = $this->config->item('pusher_cluster');
        $data['csrf_token_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        if (!isset($data['user']['image'])) {
            $data['user']['image'] = 'default.jpg';
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/previewpanel', $data);
        $this->load->view('templates/footer', $data);
        // $this->load->view('user/previewpanel_js', $data);
    }

    public function dashboard()
    {
        if (!$this->session->userdata('preview_completed')) {
            $this->session->set_flashdata('message', '<div class="alert alert-warning">Anda harus melengkapi data di Preview Panel terlebih dahulu!</div>');
            redirect('user');
            return;
        }

        $data['title'] = 'User Dashboard';
        $username_session = $this->session->userdata('username');
        $user_object = $this->Auth_model->get_user_by_username($username_session);
        $data['user'] = json_decode(json_encode($user_object), true);
        $data['pusher_app_key'] = 'e754e6dd8fb98c29661f';
        $data['pusher_cluster'] = 'ap1';
        $data['csrf_token_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['initial_video_id'] = $this->Stopwatch_model->get_latest_video_id();
        if (!isset($data['user']['image'])) {
            $data['user']['image'] = 'default.jpg';
        }
        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/user_dashboard', $data);
        $this->load->view('templates/footer', $data);
        //  $this->load->view('user/user_dashboard_js', $data);
    }

    public function submit_preview()
    {
        if ($this->session->userdata('preview_completed')) {
            redirect('user/dashboard');
            return; // Hentikan eksekusi fungsi
        }
        $username = $this->session->userdata('username');
        $user_object = $this->Auth_model->get_user_by_username($username);

        if (!$user_object) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Sesi tidak valid. Silakan login kembali.</div>');
            redirect('auth');
            return;
        }

        $usia = $this->input->post('usia');
        if (!is_numeric($usia) || $usia <= 0) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Usia harus berupa angka positif.</div>');
            redirect('user');
            return;
        }

        $data = [
            'user_id'       => $user_object->id,
            'nama_lengkap'  => $this->input->post('nama_lengkap'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'usia'          => (int)$usia,
            'pekerjaan'     => $this->input->post('pekerjaan'),
            'bidang_kerja'  => $this->input->post('bidang_kerja')
        ];

        $this->Auth_model->save_preview_data($data);

        $gender = $this->input->post('jenis_kelamin');
        $age = (int)$usia;
        $this->pusher->trigger('stopwatch-channel', 'gender-update', [
            'gender' => $gender,
            'age'    => $age
        ]);

        $this->session->set_userdata('preview_completed', true);
        $this->session->set_flashdata('message', '<div class="alert alert-success">Terima kasih telah melengkapi data. Silakan ikuti survei.</div>');
        redirect('user/dashboard');
    }


    public function previewcomments($video_id = null)
    {
        if (!$this->session->userdata('preview_completed')) {
            redirect('user');
            return;
        }
        // Validasi video_id untuk keamanan
        if (is_null($video_id) || !is_numeric($video_id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Sesi preview tidak valid atau telah berakhir.</div>');
            redirect('user');
            return;
        }

        $data['title'] = 'Preview Comments';
        $username_session = $this->session->userdata('username');
        $user_object = $this->Auth_model->get_user_by_username($username_session);
        $data['user'] = json_decode(json_encode($user_object), true);

        // Teruskan video_id dan token CSRF ke view
        $data['video_id'] = $video_id;
        $data['csrf_token_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        if (!isset($data['user']['image'])) {
            $data['user']['image'] = 'default.jpg';
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/previewcomments', $data); // Memuat view HTML
        $this->load->view('templates/footer', $data);
        //    $this->load->view('user/previewcomments_js', $data);
    }

    public function record_preference()
    {
        $preference_type = $this->input->post('preference_type');

        // Validasi
        if (!in_array($preference_type, ['senang', 'biasa', 'marah'])) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Tipe preferensi tidak valid.']));
            return;
        }

        $data_to_broadcast = [
            'type'      => $preference_type,
            'video_id'  => $this->input->post('video_id')
        ];

        // Menyiarkan event 'preference-update' ke admin
        $this->pusher->trigger('stopwatch-channel', 'preference-update', $data_to_broadcast);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success']));
    }



    public function broadcast_activity()
    {
        $activity_type = $this->input->post('type');

        // Memastikan hanya tipe aktivitas yang diharapkan yang diproses
        if (!in_array($activity_type, ['love', 'share', 'response'])) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Tipe aktivitas tidak valid.']));
            return;
        }

        $data_to_broadcast = [
            'type'      => $activity_type,
            'time'      => $this->input->post('time'),
            'video_id'  => $this->input->post('video_id')
        ];

        // Menambahkan 'value' jika tipenya adalah 'response'
        if ($activity_type === 'response') {
            $data_to_broadcast['value'] = $this->input->post('value');
        }

        // Menggunakan Pusher untuk menyiarkan event 'stats-update'
        $this->pusher->trigger('stopwatch-channel', 'stats-update', $data_to_broadcast);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success']));
    }


    public function my_profile()
    {
        $data['title'] = 'My Profile';
        $username_session = $this->session->userdata('username');
        $user_object = $this->Auth_model->get_user_by_username($username_session);
        $data['user'] = json_decode(json_encode($user_object), true);
        $preview_data = $this->Auth_model->get_preview_data_by_user_id($user_object->id);
        if ($preview_data) {
            $data['user'] = array_merge($data['user'], $preview_data);
        }

        if (!isset($data['user']['image'])) {
            $data['user']['image'] = 'default.jpg';
        }
        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/user_profile', $data);
        $this->load->view('templates/footer', $data);
    }

    public function update_profile()
    {
        $this->load->library('upload');
        $username = $this->session->userdata('username');
        $user_object = $this->Auth_model->get_user_by_username($username);

        $preview_data = [
            'user_id'       => $user_object->id,
            'nama_lengkap'  => $this->input->post('nama_lengkap'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'usia'          => $this->input->post('usia'),
            'pekerjaan'     => $this->input->post('pekerjaan'),
            'bidang_kerja'  => $this->input->post('bidang_kerja')
        ];

        $this->Auth_model->save_preview_data($preview_data);
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
                redirect('user/my_profile');
                return;
            }
        }

        if (!empty($user_data)) {
            $this->Auth_model->update_user($username, $user_data);
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success">Profil berhasil diperbarui!</div>');
        redirect('user/my_profile');
    }

    public function change_password()
    {
        $current_password = $this->input->post('current_password');
        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');
        if ($new_password !== $confirm_password) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Password baru dan konfirmasi password tidak cocok!</div>');
            redirect('user/my_profile');
        }
        $user = $this->Auth_model->get_user_by_username($this->session->userdata('username'));
        if (!$this->Auth_model->verify_password($current_password, $user->password)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Password saat ini salah!</div>');
            redirect('user/my_profile');
        }
        $this->Auth_model->update_password($this->session->userdata('username'), password_hash($new_password, PASSWORD_DEFAULT));
        $this->session->set_flashdata('message', '<div class="alert alert-success">Password berhasil diganti!</div>');
        redirect('user/my_profile');
    }

    public function record_comment_reaction()
    {
        $reaction_type = $this->input->post('reaction_type');
        if (!in_array($reaction_type, ['like', 'dislike'])) {
            $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error']));
            return;
        }

        $this->pusher->trigger('stopwatch-channel', 'comment-reaction-update', [
            'reaction' => $reaction_type,
            'video_id' => $this->input->post('video_id')
        ]);

        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success']));
    }

    // Fungsi ini menangani penyimpanan data komentar dari halaman baru
    public function save_comment()
    {
        $username = $this->session->userdata('username');
        $video_id = $this->input->post('video_id');
        $comment_text = $this->input->post('comment_text');
        $is_like = $this->input->post('is_like');
        $is_dislike = $this->input->post('is_dislike');

        if (empty($comment_text)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Komentar tidak boleh kosong.']));
            return;
        }

        // Broadcast ke admin secara real-time
        $data_to_broadcast = [
            'username'     => $username,
            'comment_text' => $comment_text,
            'is_like'      => $is_like,
            'is_dislike'   => $is_dislike,
            'video_id'     => $video_id
        ];
        $this->pusher->trigger('stopwatch-channel', 'comment-event', $data_to_broadcast);
        $this->session->unset_userdata('preview_completed');
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Komentar berhasil dikirim.']));
    }

    public function submit_preview_api()
    {
        // Memberi izin akses dari luar (CORS)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
            exit;
        }

        // 1. Validasi token JWT untuk keamanan
        $decoded_token = validate_jwt_token();
        if ($decoded_token === null) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Akses ditolak: Token tidak valid.']));
            return;
        }

        // 2. Ambil user_id dari token
        $user_id = $decoded_token->id;

        // Mengambil data dari request
        $nama_lengkap = $this->input->post('nama_lengkap');
        $jenis_kelamin = $this->input->post('jenis_kelamin');
        $usia = $this->input->post('usia');
        $pekerjaan = $this->input->post('pekerjaan');
        $bidang_kerja = $this->input->post('bidang_kerja');

        // Validasi input yang lebih ketat
        if (!in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'])) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Jenis kelamin tidak valid.']));
            return;
        }

        if (!is_numeric($usia) || (int)$usia <= 0) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Usia harus berupa angka positif.']));
            return;
        }

        // 3. Hapus user_id dari validasi empty
        if (empty($nama_lengkap) || empty($pekerjaan) || empty($bidang_kerja) || empty($jenis_kelamin) || empty($usia)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Semua data wajib diisi.']));
            return;
        }

        $data = [
            'user_id'       => (int)$user_id, // 4. Gunakan user_id dari token
            'nama_lengkap'  => $nama_lengkap,
            'jenis_kelamin' => $jenis_kelamin,
            'usia'          => (int)$usia,
            'pekerjaan'     => $pekerjaan,
            'bidang_kerja'  => $bidang_kerja
        ];

        // Memanggil model yang benar untuk menyimpan data
        $this->Auth_model->save_preview_data($data);

        // Mengirim event ke Pusher
        $this->pusher->trigger('stopwatch-channel', 'gender-update', [
            'gender' => $jenis_kelamin,
            'age'    => (int)$usia
        ]);

        // Mengirim respons sukses
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Profil berhasil disimpan.']));
    }

    // application/controllers/User.php

    // Tambahkan fungsi ini di dalam class User
    public function broadcast_activity_api()
    {
        // Beri izin akses dari luar (CORS)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        // Validasi token JWT di sini (jika sudah diimplementasikan)
        // ...

        $activity_type = $this->input->post('type');

        if (!in_array($activity_type, ['love', 'share', 'response'])) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Tipe aktivitas tidak valid.']));
            return;
        }

        $data_to_broadcast = [
            'type'      => $activity_type,
            'time'      => $this->input->post('time'),
            'value'     => $this->input->post('value') ?? '',
            'video_id'  => $this->input->post('video_id')
        ];

        $this->pusher->trigger('stopwatch-channel', 'stats-update', $data_to_broadcast);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success']));
    }

    public function save_comment_api()
    {
        // Beri izin akses dari luar (CORS)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
            exit;
        }

        // 1. Validasi token JWT untuk keamanan
        $decoded_token = validate_jwt_token();
        if ($decoded_token === null) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Akses ditolak: Token tidak valid.']));
            return;
        }

        // 2. Ambil data dari request
        $username = $decoded_token->username;
        $video_id = $this->input->post('video_id');
        $comment_text = $this->input->post('comment_text');
        $preference = $this->input->post('preference');
        $is_like = $this->input->post('is_like') ? 1 : 0;
        $is_dislike = $this->input->post('is_dislike') ? 1 : 0;

        // 3. Validasi input
        if (empty($video_id) || empty($comment_text) || empty($preference)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Data tidak lengkap. Pastikan semua kolom terisi.']));
            return;
        }

        // 4. Siapkan data untuk disiarkan ke admin via Pusher
        $data_to_broadcast = [
            'username'     => $username,
            'comment_text' => $comment_text,
            'preference'   => $preference,
            'is_like'      => $is_like,
            'is_dislike'   => $is_dislike,
            'video_id'     => $video_id
        ];
        $this->pusher->trigger('stopwatch-channel', 'comment-event', $data_to_broadcast);

        // 5. Kirim respons sukses
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Komentar berhasil dikirim.']));
    }


    public function get_preview_data_api()
    {
        // Beri izin akses dari luar (CORS)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
            exit;
        }

        // Validasi token JWT untuk keamanan
        $decoded_token = validate_jwt_token();
        if ($decoded_token === null) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Akses ditolak: Token tidak valid.']));
            return;
        }

        // Ambil user_id dari token yang sudah divalidasi
        $user_id = $decoded_token->id;

        // Panggil model untuk mengambil data dari database
        $preview_data = $this->Auth_model->get_preview_data_by_user_id($user_id);

        if ($preview_data) {
            // Jika data ditemukan, kirim kembali sebagai JSON
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'data' => $preview_data]));
        } else {
            // Jika data tidak ditemukan
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'not_found', 'message' => 'Data profil belum ada.']));
        }
    }
}
