<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alat_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Ambil semua data alat
    public function get_all() {
        $this->db->order_by('nama_alat', 'ASC');
        $query = $this->db->get('alat');
        return $query->result();
    }

    // Ambil alat dengan stok > 0
    public function get_available() {
        $this->db->where('stok >', 0);
        $this->db->order_by('nama_alat', 'ASC');
        $query = $this->db->get('alat');
        return $query->result();
    }

    // Ambil data alat berdasarkan ID
    public function get_by_id($id) {
        $query = $this->db->get_where('alat', array('id_alat' => $id));
        return $query->row();
    }

    // Tambah alat baru
    public function insert($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('alat', $data);
    }

    // Update data alat
    public function update($id, $data) {
        $this->db->where('id_alat', $id);
        return $this->db->update('alat', $data);
    }

    // Hapus alat
    public function delete($id) {
        $this->db->where('id_alat', $id);
        return $this->db->delete('alat');
    }

    // Cek ketersediaan alat pada tanggal tertentu
    public function check_availability($id_alat, $tanggal_pinjam, $tanggal_kembali, $jumlah, $exclude_booking_id = null) {
        // Ambil stok total alat
        $alat = $this->get_by_id($id_alat);
        if (!$alat) {
            return false;
        }
        $total_stok = $alat->stok;
        
        // Hitung jumlah alat yang sudah dibooking pada rentang tanggal tersebut
        $this->db->select_sum('jumlah');
        $this->db->from('booking');
        $this->db->where('id_alat', $id_alat);
        $this->db->where('status', 'approved');
        
        // Kondisi tanggal booking tumpang tindih dengan rentang tanggal yang diminta
        $this->db->group_start();
        $this->db->where("tanggal_pinjam <= '$tanggal_kembali'");
        $this->db->where("tanggal_kembali >= '$tanggal_pinjam'");
        $this->db->group_end();
        
        // Jika ini adalah update booking, exclude booking yang sedang diupdate
        if ($exclude_booking_id) {
            $this->db->where('id_booking !=', $exclude_booking_id);
        }
        
        $query = $this->db->get();
        $used = $query->row()->jumlah ?? 0;
        
        // Hitung sisa stok yang tersedia
        $available = $total_stok - $used;
        
        // Cek apakah jumlah yang diminta masih tersedia
        return $available >= $jumlah;
    }

    // Kurangi stok alat saat booking disetujui
    public function reduce_stock($id_alat, $jumlah) {
        $this->db->set('stok', 'stok - ' . $jumlah, FALSE);
        $this->db->where('id_alat', $id_alat);
        return $this->db->update('alat');
    }

    // Tambah stok alat saat alat dikembalikan
    public function increase_stock($id_alat, $jumlah) {
        $this->db->set('stok', 'stok + ' . $jumlah, FALSE);
        $this->db->where('id_alat', $id_alat);
        return $this->db->update('alat');
    }
}