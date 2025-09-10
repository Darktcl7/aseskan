<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{

    /**
     * Mengambil semua data pengguna dari database.
     * @return array
     */
    public function get_all_users()
    {
        // Mengurutkan berdasarkan ID, bisa juga berdasarkan username atau role
        $this->db->order_by('id', 'ASC');
        return $this->db->get('users')->result_array();
    }

    /**
     * Mengambil satu data user berdasarkan ID.
     * @param int $id
     * @return array|null
     */
    public function get_user_by_id($id)
    {
        return $this->db->get_where('users', ['id' => $id])->row_array();
    }

    /**
     * Menambahkan pengguna baru ke database.
     * @param array $data
     * @return bool
     */
    public function create_user($data)
    {
        return $this->db->insert('users', $data);
    }

    /**
     * Memperbarui data pengguna berdasarkan ID.
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_user($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    /**
     * Menghapus pengguna berdasarkan ID secara komprehensif, termasuk data terkait.
     * @param int $id
     * @return bool
     */
    public function delete_user($id)
    {
        // Ambil data user (terutama username) sebelum dihapus
        $user = $this->get_user_by_id($id);
        if (!$user) {
            return false; // User tidak ditemukan
        }

        $this->db->trans_start();

        // 1. Hapus dari tabel 'previewpanel'
        $this->db->where('user_id', $id);
        $this->db->delete('previewpanel');

        // 2. Hapus dari tabel 'file_access'
        $this->db->where('user_id', $id);
        $this->db->delete('file_access');

        // 3. Hapus dari tabel 'komentar' (menggunakan username)
        $this->db->where('username', $user['username']);
        $this->db->delete('komentar');

        // 4. Hapus dari tabel 'users'
        $this->db->where('id', $id);
        $this->db->delete('users');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
