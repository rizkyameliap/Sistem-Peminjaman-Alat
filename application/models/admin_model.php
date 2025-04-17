<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Cek login admin
    public function login($username, $password) {
        $query = $this->db->get_where('admin', array('username' => $username));
        
        if ($query->num_rows() == 1) {
            $admin = $query->row();
            if (password_verify($password, $admin->password)) {
                return $admin;
            }
        }
        
        return false;
    }

    // Ambil semua data admin
    public function get_all() {
        $query = $this->db->get('admin');
        return $query->result();
    }

    // Ambil data admin berdasarkan ID
    public function get_by_id($id) {
        $query = $this->db->get_where('admin', array('id_admin' => $id));
        return $query->row();
    }

    // Tambah admin baru
    public function insert($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('admin', $data);
    }

    // Update data admin
    public function update($id, $data) {
        // Hash password jika diubah
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        
        $this->db->where('id_admin', $id);
        return $this->db->update('admin', $data);
    }

    // Hapus admin
    public function delete($id) {
        $this->db->where('id_admin', $id);
        return $this->db->delete('admin');
    }
}