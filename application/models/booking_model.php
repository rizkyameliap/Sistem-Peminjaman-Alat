<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Ambil semua data booking dengan join ke tabel mahasiswa dan alat
    public function get_all() {
        $this->db->select('booking.*, mahasiswa.nim, mahasiswa.nama as nama_mahasiswa, alat.nama_alat');
        $this->db->from('booking');
        $this->db->join('mahasiswa', 'mahasiswa.id_mahasiswa = booking.id_mahasiswa');
        $this->db->join('alat', 'alat.id_alat = booking.id_alat');
        $this->db->order_by('booking.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    // Ambil booking berdasarkan ID mahasiswa
    public function get_by_mahasiswa($id_mahasiswa) {
        $this->db->select('booking.*, alat.nama_alat');
        $this->db->from('booking');
        $this->db->join('alat', 'alat.id_alat = booking.id_alat');
        $this->db->where('booking.id_mahasiswa', $id_mahasiswa);
        $this->db->order_by('booking.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    // Ambil booking berdasarkan ID
    public function get_by_id($id) {
        $this->db->select('booking.*, mahasiswa.nim, mahasiswa.nama as nama_mahasiswa, alat.nama_alat');
        $this->db->from('booking');
        $this->db->join('mahasiswa', 'mahasiswa.id_mahasiswa = booking.id_mahasiswa');
        $this->db->join('alat', 'alat.id_alat = booking.id_alat');
        $this->db->where('booking.id_booking', $id);
        $query = $this->db->get();
        return $query->row();
    }

    // Tambah booking baru
    public function insert($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = 'pending';
        
        return $this->db->insert('booking', $data);
    }

    // Update data booking
    public function update($id, $data) {
        $this->db->where('id_booking', $id);
        return $this->db->update('booking', $data);
    }

    // Hapus booking
    public function delete($id) {
        $this->db->where('id_booking', $id);
        return $this->db->delete('booking');
    }

    // Ambil booking yang pending untuk diverifikasi admin
    public function get_pending() {
        $this->db->select('booking.*, mahasiswa.nim, mahasiswa.nama as nama_mahasiswa, alat.nama_alat');
        $this->db->from('booking');
        $this->db->join('mahasiswa', 'mahasiswa.id_mahasiswa = booking.id_mahasiswa');
        $this->db->join('alat', 'alat.id_alat = booking.id_alat');
        $this->db->where('booking.status', 'pending');
        $this->db->order_by('booking.created_at', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    // Verifikasi booking oleh admin (approve/reject)
    public function verify($id, $status, $keterangan = '') {
        $data = array(
            'status' => $status,
            'keterangan' => $keterangan
        );
        
        $this->db->where('id_booking', $id);
        return $this->db->update('booking', $data);
    }

    // Hitung jumlah booking berdasarkan status
    public function count_by_status($status) {
        $this->db->where('status', $status);
        return $this->db->count_all_results('booking');
    }
}