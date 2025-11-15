<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_continuation_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Dapatkan session terakhir dari project
     */
    public function get_last_session($project_id)
    {
        $this->db->select_max('project_session');
        $this->db->where('original_project_id', $project_id);
        $this->db->or_where('id', $project_id);
        $query = $this->db->get('stopwatch_stats'); // Changed from 'projects' to 'stopwatch_stats'
        $result = $query->row_array();

        return $result['project_session'] ?? 1;
    }

    /**
     * Dapatkan semua session dari satu project
     */
    public function get_all_sessions($project_id)
    {
        // Cari original project ID dulu
        $this->db->select('original_project_id, id');
        $this->db->where('id', $project_id);
        $project = $this->db->get('stopwatch_stats')->row_array(); // Changed from 'projects' to 'stopwatch_stats'

        $original_id = $project['original_project_id'] ?? $project['id'];

        // Ambil semua sessions
        $this->db->where('original_project_id', $original_id);
        $this->db->or_where('id', $original_id);
        $this->db->order_by('project_session', 'ASC');

        return $this->db->get('stopwatch_stats')->result_array(); // Changed from 'projects' to 'stopwatch_stats'
    }

    /**
     * Cek apakah project bisa di-continue
     */
    public function can_continue($project_id)
    {
        $this->db->where('id', $project_id);
        $project = $this->db->get('stopwatch_stats')->row_array(); // Changed from 'projects' to 'stopwatch_stats'

        if (!$project) {
            return array('status' => FALSE, 'message' => 'Project not found');
        }

        // Tambahkan validasi sesuai kebutuhan
        // Misalnya: project harus sudah selesai, dll

        return array('status' => TRUE, 'message' => 'OK');
    }

    /**
     * Buat continuation project
     */
    public function create_continuation($parent_project_id)
    {
        // Ambil data project parent
        $this->db->where('id', $parent_project_id);
        $parent = $this->db->get('stopwatch_stats')->row_array();

        if (!$parent) {
            return FALSE;
        }

        // Hitung session berikutnya
        $next_session = $this->get_last_session($parent_project_id) + 1;

        // Tentukan original project ID
        $original_id = $parent['original_project_id'] ?? $parent_project_id;

        // Get base project name (remove any existing " - Session X" suffix)
        $base_name = $parent['nama_video'];
        if (preg_match('/^(.+?)\s*-\s*Session\s+\d+$/', $base_name, $matches)) {
            $base_name = trim($matches[1]);
        }

        // Siapkan data project baru - RESET counters untuk session baru
        $data = array(
            'nama_video' => $base_name . ' - Session ' . $next_session,
            'parent_project_id' => $parent_project_id,
            'original_project_id' => $original_id,
            'project_session' => $next_session,
            'is_continuation' => 1,
            'session_start_date' => date('Y-m-d H:i:s'),
            // RESET data untuk session baru (bukan copy dari parent)
            'love_count' => 0,
            'share_count' => 0,
            'response_senang' => 0,
            'response_biasa' => 0,
            'response_sedih' => 0,
            'session_male_count' => 0,
            'session_female_count' => 0,
            'session_average_age' => 0,
            'pref_senang' => 0,
            'pref_biasa' => 0,
            'pref_marah' => 0,
        );

        // Insert project baru
        if ($this->db->insert('stopwatch_stats', $data)) {
            return $this->db->insert_id();
        }

        return FALSE;
    }

    /**
     * Dapatkan gabungan data dari semua session
     */
    public function get_combined_stats($project_id)
    {
        $sessions = $this->get_all_sessions($project_id);
        
        if (empty($sessions)) {
            return NULL;
        }

        // Initialize combined stats
        $combined = array(
            'original_project_id' => $sessions[0]['original_project_id'] ?? $sessions[0]['id'],
            'total_sessions' => count($sessions),
            'sessions' => $sessions,
            // Aggregate data
            'total_love_count' => 0,
            'total_share_count' => 0,
            'total_response_senang' => 0,
            'total_response_biasa' => 0,
            'total_response_sedih' => 0,
            'total_male_count' => 0,
            'total_female_count' => 0,
            'average_age' => 0,
            'total_pref_senang' => 0,
            'total_pref_biasa' => 0,
            'total_pref_marah' => 0,
        );

        $total_age_sum = 0;
        $total_respondents = 0;

        // Sum up all sessions
        foreach ($sessions as $session) {
            $combined['total_love_count'] += intval($session['love_count']);
            $combined['total_share_count'] += intval($session['share_count']);
            $combined['total_response_senang'] += intval($session['response_senang']);
            $combined['total_response_biasa'] += intval($session['response_biasa']);
            $combined['total_response_sedih'] += intval($session['response_sedih']);
            $combined['total_male_count'] += intval($session['session_male_count']);
            $combined['total_female_count'] += intval($session['session_female_count']);
            $combined['total_pref_senang'] += intval($session['pref_senang']);
            $combined['total_pref_biasa'] += intval($session['pref_biasa']);
            $combined['total_pref_marah'] += intval($session['pref_marah']);

            // Calculate weighted average age
            $session_respondents = intval($session['session_male_count']) + intval($session['session_female_count']);
            if ($session_respondents > 0 && floatval($session['session_average_age']) > 0) {
                $total_age_sum += floatval($session['session_average_age']) * $session_respondents;
                $total_respondents += $session_respondents;
            }
        }

        // Calculate average age across all sessions
        if ($total_respondents > 0) {
            $combined['average_age'] = round($total_age_sum / $total_respondents, 1);
        }

        $combined['total_respondents'] = $total_respondents;

        return $combined;
    }
}