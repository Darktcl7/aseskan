<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{

    /**
     * Mengambil data user berdasarkan username
     * @param string $username
     * @return object|null
     */
    public function get_user_by_username($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get('users');

        // Mengembalikan satu baris hasil sebagai objek
        if ($query->num_rows() == 1) {
            return $query->row();
        }

        return null;
    }

    public function create_user($data)
    {
        // Menyimpan data ke tabel 'users'
        return $this->db->insert('users', $data);
    }

    public function verify_password($password, $stored_password)
    {
        return password_verify($password, $stored_password);
    }

    public function update_user($username, $data)
    {
        $this->db->where('username', $username);
        return $this->db->update('users', $data);
    }

    public function update_password($username, $hashed_password)
    {
        $this->db->where('username', $username);
        return $this->db->update('users', ['password' => $hashed_password]);
    }

    public function save_preview_data($data)
    {
        if (!isset($data['user_id'])) {
            return false;
        }

        $user_id = $data['user_id'];
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('previewpanel');

        if ($query->num_rows() > 0) {
            // Jika sudah ada, lakukan UPDATE
            $this->db->where('user_id', $user_id);
            return $this->db->update('previewpanel', $data);
        } else {
            // Jika belum ada, lakukan INSERT
            return $this->db->insert('previewpanel', $data);
        }
    }

    public function get_preview_data_by_user_id($user_id)
    {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('previewpanel');
        return $query->row_array(); // Mengembalikan satu baris sebagai array
    }

    public function get_gender_counts()
    {
        $this->db->select('jenis_kelamin, COUNT(*) as total');
        $this->db->from('previewpanel'); // Mengambil data dari tabel previewpanel
        $this->db->where_in('jenis_kelamin', ['Laki-laki', 'Perempuan']); // Pastikan hanya menghitung nilai yang valid
        $this->db->group_by('jenis_kelamin');
        $query = $this->db->get();

        log_message('debug', 'Gender counts query: ' . $this->db->last_query());
        $results = $query->result_array();

        $gender_counts = [
            'Laki-laki' => 0,
            'Perempuan' => 0
        ];

        foreach ($results as $row) {
            if (isset($gender_counts[$row['jenis_kelamin']])) {
                $gender_counts[$row['jenis_kelamin']] = (int)$row['total'];
            }
        }

        return $gender_counts;
    }
}
