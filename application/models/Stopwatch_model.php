<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stopwatch_model extends CI_Model
{
    public function save_stats($data)
    {
        $this->db->insert('stopwatch_stats', $data);
        return $this->db->insert_id();
    }

    public function save_comment($data)
    {
        return $this->db->insert('komentar', $data);
    }

    public function video_exists($video_id)
    {
        $this->db->where('id', $video_id);
        $query = $this->db->get('stopwatch_stats');
        return $query->num_rows() > 0;
    }

    public function get_all_stats()
    {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('stopwatch_stats');
        return $query->result_array();
    }

    public function get_stats_by_id($id)
    {
        return $this->db->get_where('stopwatch_stats', ['id' => $id])->row_array();
    }

    public function get_comments_by_video_id($id)
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get_where('komentar', ['video_id' => $id])->result_array();
    }

    public function delete_stats_by_id($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('stopwatch_stats');
        return $this->db->affected_rows() > 0;
    }

    public function get_latest_video_id()
    {
        $this->db->select('id');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('stopwatch_stats');
        return $query->row() ? $query->row()->id : null;
    }

    public function get_clients()
    {
        return $this->db->get_where('users', ['role' => 'client'])->result_array();
    }

    public function get_file_access($file_id)
    {
        $this->db->select('user_id'); // DIUBAH dari client_id
        $this->db->from('file_access'); // DIUBAH dari file_client_access
        $this->db->where('file_id', $file_id);
        $query = $this->db->get();

        $result_array = $query->result_array();
        return array_column($result_array, 'user_id'); // DIUBAH dari client_id
    }


    public function update_file_access($file_id, $user_ids)
    {
        $this->db->trans_start();
        $this->db->where('file_id', $file_id);
        $this->db->delete('file_access');
        if (!empty($user_ids)) {
            $data_to_insert = [];
            foreach ($user_ids as $user_id) {
                $data_to_insert[] = [
                    'file_id'   => $file_id,
                    'user_id' => (int) $user_id
                ];
            }
            $this->db->insert_batch('file_access', $data_to_insert);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_stats_for_client($user_id)
    {
        $this->db->select('s.*');
        $this->db->from('stopwatch_stats s');
        $this->db->join('file_access fa', 's.id = fa.file_id');
        $this->db->where('fa.user_id', $user_id);

        $this->db->order_by('s.last_updated', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function delete_report_fully($report_id)
    {
        $this->db->trans_start();

        $this->db->where('file_id', $report_id);
        $this->db->delete('file_access');

        $this->db->where('video_id', $report_id);
        $this->db->delete('komentar');

        $this->db->where('id', $report_id);
        $this->db->delete('stopwatch_stats');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function revoke_all_access_for_user($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->delete('file_access');
    }
}
