<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
    }

    /**
     * Menampilkan halaman login atau redirect jika sudah login
     */
    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            $role = $this->session->userdata('role');
            if ($role == 'admin') {
                redirect('admin');
            } elseif ($role == 'client') {
                redirect('client');
            } else {
                redirect('user');
            }
        }

        $this->load->view('templates/auth_header');
        $this->load->view('auth/v_login');
        $this->load->view('templates/auth_footer');
    }

    public function process_login()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        // Inisialisasi variabel untuk data permintaan
        $is_mobile_request = false;
        $request_data = [];

        // Cek apakah Content-Type adalah application/json
        if (strpos($this->input->server('CONTENT_TYPE'), 'application/json') !== false) {
            $json_data = file_get_contents('php://input');
            $decoded_data = json_decode($json_data, true); // Decode sebagai associative array

            if (json_last_error() === JSON_ERROR_NONE) {
                $request_data = $decoded_data;
                if (isset($request_data['is_mobile']) && $request_data['is_mobile'] === 'true') {
                    $is_mobile_request = true;
                }
            }
        }

        // Fallback ke traditional POST untuk web atau jika parsing JSON gagal
        if (!$is_mobile_request) {
            $request_data = $this->input->post(); // Gunakan data POST tradisional
            if (isset($request_data['is_mobile']) && $request_data['is_mobile'] === 'true') {
                $is_mobile_request = true;
            }
        }

        // Lewati validasi CSRF jika ini permintaan mobile
        if ($is_mobile_request) {
            // Asumsi CSRF hanya untuk web form, jadi tidak perlu validasi form_validation.run()
            // untuk mobile. Anda bisa menambahkan validasi manual jika diperlukan.
        } else {
            // Validasi untuk web
            $this->form_validation->set_rules('username', 'Username', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required');
        }

        // Jika ini bukan permintaan mobile DAN validasi form web gagal
        if (!$is_mobile_request && $this->form_validation->run() == FALSE) {
            $this->load->view('auth/v_login'); // Tampilkan view login untuk web
        } else {
            // Ambil username dan password dari $request_data
            $username = isset($request_data['username']) ? $request_data['username'] : '';
            $password = isset($request_data['password']) ? $request_data['password'] : '';

            $user = $this->Auth_model->get_user_by_username($username);

            if ($user && password_verify($password, $user->password)) {
                $session_data = array(
                    'user_id'   => $user->id,
                    'username'  => $user->username,
                    'role'      => $user->role,
                    'logged_in' => TRUE
                );
                $this->session->set_userdata($session_data);

                // Cek apakah ini permintaan dari mobile
                if ($is_mobile_request) {
                    // Load library JWT
                    $this->load->library('json_web_token');

                    // Data untuk token
                    $token_data = array(
                        'id' => $user->id,
                        'username' => $user->username
                    );

                    // Buat token
                    $token = $this->json_web_token->encode($token_data);

                    // Hapus password dari data yang dikirim kembali
                    unset($user->password);

                    // Tambahkan token ke data user
                    $user->token = $token;

                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'success',
                            'message' => 'Login berhasil!',
                            'user_data' => $user
                        ]));
                    return; // Hentikan eksekusi agar tidak redirect
                }

                // Redirect berdasarkan role (untuk web)
                if ($user->role == 'admin') {
                    redirect('admin');
                } elseif ($user->role == 'client') {
                    redirect('client');
                } else {
                    redirect('user');
                }
            } else {
                // Cek apakah ini permintaan dari mobile
                if ($is_mobile_request) {
                    $this->output
                        ->set_status_header(401) // Unauthorized
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'error',
                            'message' => 'Username atau Password salah!'
                        ]));
                    return; // Hentikan eksekusi
                }

                $this->session->set_flashdata('error', 'Username atau Password salah!');
                redirect('auth');
            }
        }
    }


    public function get_csrf_token()
    {
        $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
    }

    public function register()
    {
        $this->form_validation->set_rules(
            'username',
            'Username',
            'required|trim|is_unique[users.username]',
            array('is_unique' => 'Username ini sudah terdaftar.')
        );
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('passconf', 'Konfirmasi Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/auth_header');
            $this->load->view('auth/v_register');
            $this->load->view('templates/auth_footer');
        } else {
            $data = array(
                'username' => $this->input->post('username'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                // Pengguna baru dari form registrasi akan selalu menjadi 'user' biasa.
                // Role 'client' diatur manual oleh admin.
                'role'     => 'user'
            );

            if ($this->Auth_model->create_user($data)) {
                $this->session->set_flashdata('success', 'Pendaftaran berhasil! Silakan login.');
                redirect('auth');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan. Coba lagi.');
                redirect('auth/register');
            }
        }
    }


    /**
     * Proses logout
     */
    public function logout()
    {
        // Hapus semua data session
        $this->session->sess_destroy();
        redirect('auth');
    }
}
