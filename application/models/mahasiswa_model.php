<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Cek login mahasiswa
    public function login($nim, $password) {
        $query = $this->db->get_where('mahasiswa', array('nim' => $nim));
        
        if ($query->num_rows() == 1) {
            $mahasiswa = $query->row();
            if (password_verify($password, $mahasiswa->password)) {
                return $mahasiswa;
            }
        }
        
        return false;
    }

    // Ambil semua data mahasiswa
    public function get_all() {
        $query = $this->db->get('mahasiswa');
        return $query->result();
    }

    // Ambil data mahasiswa berdasarkan ID
    public function get_by_id($id) {
        $query = $this->db->get_where('mahasiswa', array('id_mahasiswa' => $id));
        return $query->row();
    }

    // Tambah mahasiswa baru
    public function insert($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('mahasiswa', $data);
    }

    // Update data mahasiswa
    public function update($id, $data) {
        // Hash password jika diubah
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        
        $this->db->where('id_mahasiswa', $id);
        return $this->db->update('mahasiswa', $data);
    }

    // Hapus mahasiswa
    public function delete($id) {
        $this->db->where('id_mahasiswa', $id);
        return $this->db->delete('mahasiswa');
    }

    // Cek jika NIM sudah terdaftar
    public function nim_exists($nim, $exclude_id = null) {
        $this->db->where('nim', $nim);
        if ($exclude_id) {
            $this->db->where('id_mahasiswa !=', $exclude_id);
        }
        $query = $this->db->get('mahasiswa');
        return ($query->num_rows() > 0);
    }
}