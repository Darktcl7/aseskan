<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';

use Dompdf\Dompdf;

class Client extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') != 'client') {
            $this->session->set_flashdata('error', 'Anda harus login sebagai client!');
            redirect('auth');
        }
        $this->load->model('Auth_model');
        $this->load->model('Stopwatch_model');
        $this->load->model('Project_continuation_model');
    }

    private function _prepare_sidebar_data()
    {
        return [
            ['type' => 'heading', 'title' => 'Client Menu'],
            ['type' => 'item', 'title' => 'Files', 'url' => base_url('client'), 'icon' => 'folder', 'is_active' => true],
        ];
    }

    public function index()
    {
        $data['title'] = 'Files';
        $user_object = $this->Auth_model->get_user_by_username($this->session->userdata('username'));
        $data['user'] = json_decode(json_encode($user_object), true);
        $data['files'] = $this->Stopwatch_model->get_stats_for_client($data['user']['id']);
        $data['sidebar_menu'] = $this->_prepare_sidebar_data();
        $data['action_base_url'] = 'client/';
        $data['is_admin_view'] = false;
        $data['clients'] = []; // Tambahkan ini untuk memastikan variabel selalu ada
        if (!isset($data['user']['image'])) {
            $data['user']['image'] = 'default.jpg';
        }
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/admin_files', $data);
        $this->load->view('templates/footer');
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
        $data = [
            'report' => $report,
            'comments' => $comments,
            'comment_stats' => ['likes' => $comment_likes, 'dislikes' => $comment_dislikes]
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function download_report_pdf($id)
    {
        try {
            // 1. Ambil data yang diperlukan dari model
            $report = $this->Stopwatch_model->get_stats_by_id((int)$id);
            $comments = $this->Stopwatch_model->get_comments_by_video_id((int)$id);

            // --- PERBAIKAN DIMULAI DI SINI ---

            // Ambil objek pengguna lengkap berdasarkan username dari sesi
            $user_object = $this->Auth_model->get_user_by_username($this->session->userdata('username'));

            // Pastikan user ditemukan sebelum melanjutkan
            if (!$user_object) {
                show_error('Gagal mengidentifikasi pengguna. Sesi mungkin telah berakhir.', 403);
                return;
            }
            $client_id = $user_object->id; // Gunakan ID dari objek pengguna

            // Periksa apakah laporan ada dan apakah klien saat ini memiliki akses
            $client_reports = $this->Stopwatch_model->get_stats_for_client($client_id);
            $has_access = false;
            foreach ($client_reports as $cr) {
                if ($cr['id'] == $id) {
                    $has_access = true;
                    break;
                }
            }

            if (!$report || !$has_access) {
                show_error('Laporan tidak ditemukan atau Anda tidak memiliki hak akses untuk mengunduhnya.', 404);
                return;
            }

            // --- AKHIR DARI PERBAIKAN ---

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
     * View Combined Report dari semua sessions (untuk client)
     */
    public function view_combined_report($project_id)
    {
        // Get user info
        $user_object = $this->Auth_model->get_user_by_username($this->session->userdata('username'));
        if (!$user_object) {
            show_error('User tidak ditemukan.', 404);
            return;
        }
        $client_id = $user_object->id;

        // Check access permission
        $client_reports = $this->Stopwatch_model->get_stats_for_client($client_id);
        $has_access = false;
        foreach ($client_reports as $cr) {
            if ($cr['id'] == $project_id) {
                $has_access = true;
                break;
            }
        }

        if (!$has_access) {
            show_error('Anda tidak memiliki akses ke laporan ini.', 403);
            return;
        }

        // Get combined stats
        $combined = $this->Project_continuation_model->get_combined_stats($project_id);
        if (!$combined) {
            show_error('Project tidak ditemukan.', 404);
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

        // Prepare data for view
        $data['title'] = 'Combined Report';
        $data['user'] = json_decode(json_encode($user_object), true);
        $data['combined'] = $combined;
        $data['all_comments'] = $all_comments;
        $data['project_name'] = $combined['sessions'][0]['nama_video'] ?? 'Combined Report';
        $data['sidebar_menu'] = $this->_prepare_sidebar_data();
        $data['action_base_url'] = 'client/';
        $data['back_to_files_url'] = 'client';

        if (!isset($data['user']['image'])) {
            $data['user']['image'] = 'default.jpg';
        }

        // Load views
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/combined_report', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Download Combined PDF dari semua sessions (untuk client)
     */
    public function download_combined_pdf($project_id)
    {
        try {
            // Get user info
            $user_object = $this->Auth_model->get_user_by_username($this->session->userdata('username'));
            if (!$user_object) {
                show_error('Gagal mengidentifikasi pengguna. Sesi mungkin telah berakhir.', 403);
                return;
            }
            $client_id = $user_object->id;

            // Check access permission
            $client_reports = $this->Stopwatch_model->get_stats_for_client($client_id);
            $has_access = false;
            foreach ($client_reports as $cr) {
                if ($cr['id'] == $project_id) {
                    $has_access = true;
                    break;
                }
            }

            if (!$has_access) {
                show_error('Anda tidak memiliki akses ke laporan ini.', 403);
                return;
            }

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
