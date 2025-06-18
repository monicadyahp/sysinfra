<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class ChangePassword extends Controller
{
    public function update()
    {
        // Pastikan hanya menerima request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request'])->setStatusCode(400);
        }

        // Ambil data dari request
        $username = $this->request->getPost('ums_userid'); // PostgreSQL username
        $old_password = $this->request->getPost('old_password');
        $new_password = $this->request->getPost('new_password');

        // Validasi input tidak boleh kosong
        if (empty($username) || empty($old_password) || empty($new_password)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Semua kolom harus diisi'])->setStatusCode(400);
        }

        // Koneksi ke database sebagai user yang login
        $db_postgree = \Config\Database::connect([
            'DSN'      => '',
            'hostname' => '192.9.18.28',
            'username' => $username,
            'password' => $old_password, // Pakai password lama user
            'database' => 'common',
            'schema'   => 'public',
            'DBDriver' => 'Postgre',
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug'  => true,
            'charset'  => 'utf8',
            'swapPre'  => '',
            'failover' => [],
            'port'     => 5432,
            'dateFormat' => [
                'date'     => 'Y-m-d',
                'datetime' => 'Y-m-d H:i:s',
                'time'     => 'H:i:s',
            ],
        ]);

        // Coba koneksi untuk verifikasi password lama
        try {
            $db_postgree->connect();
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Password lama salah'])->setStatusCode(403);
        }

        // Ubah password dengan ALTER ROLE
        try {
            $db_postgree->query("ALTER ROLE \"$username\" WITH PASSWORD '$new_password'");

            // Hapus sesi user untuk logout
            session()->destroy();

            return $this->response->setJSON(['status' => 'success', 'message' => 'Password berhasil diperbarui. Anda akan logout.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui password: ' . $e->getMessage()])->setStatusCode(500);
        }
    }
}
