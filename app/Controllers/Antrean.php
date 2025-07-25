<?php

namespace App\Controllers;

use App\Models\AntreanModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Antrean extends BaseController
{
    protected $AntreanModel;
    public function __construct()
    {
        $this->AntreanModel = new AntreanModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Antrean - ' . $this->systemName,
                'headertitle' => 'Antrean',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman antrean
            return view('dashboard/antrean/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function list_antrean()
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $nama_jaminan = $this->request->getGet('nama_jaminan');
            $tanggal_antrean = $this->request->getGet('tanggal_antrean');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            if (empty($tanggal_antrean)) {
                // Mengembalikan data pasien dalam format JSON
                return $this->response->setJSON([
                    'antrean' => [],
                    'total' => 0
                ]);
            }

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            $AntreanModel = $this->AntreanModel;

            // Menerapkan filter pencarian berdasarkan nama_jaminan
            if ($nama_jaminan) {
                // Terapkan filter pencarian
                $AntreanModel->groupStart()
                    ->like('nama_jaminan', $nama_jaminan)
                    ->groupEnd();
            }
            // Menerapkan filter pencarian berdasarkan tanggal_antrean
            if ($tanggal_antrean) {
                // Terapkan filter pencarian
                $AntreanModel->groupStart()
                    ->like('tanggal_antrean', $tanggal_antrean)
                    ->groupEnd();
            }

            // Menghitung total hasil pencarian
            $total = $AntreanModel->countAllResults(false);

            // Mendapatkan hasil yang sudah dipaginasi
            $Pasien = $AntreanModel->orderBy('id_antrean', 'DESC')->findAll($limit, $offset);

            // Menghitung nomor urut untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke setiap pasien
            $dataAntrean = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index; // Menetapkan nomor urut
                return $data; // Mengembalikan data yang telah ditambahkan nomor urut
            }, $Pasien, array_keys($Pasien));

            // Mengembalikan data pasien dalam format JSON
            return $this->response->setJSON([
                'antrean' => $dataAntrean,
                'total' => $total // Mengembalikan total hasil
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function cek_antrean($id)
    {
        if (session()->get('role') == 'Admisi') {
            // Mengambil data pengguna berdasarkan ID, kecuali pengguna yang sedang login
            $data = $this->AntreanModel
                ->find($id);
            // Mengembalikan respons JSON dengan data pengguna
            return $this->response->setJSON($data);
        } else if (session()->get('role') == 'Admin') {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Harap masuk sebagai Admisi untuk memanggil antrean ini.',
            ]);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function selesai_antrean($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            $db = db_connect(); // Menghubungkan ke database
            // Data yang akan diupdate
            $data = [
                'loket' => session()->get('fullname'),
                'status' => 'SUDAH DIPANGGIL'
            ];
            // Mengubah status dan mengisi nama loket
            $db->table('antrean')->set($data)->where('id_antrean', $id)->update();
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Antrean sudah selesai dipanggil']);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function batal_antrean($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            $db = db_connect(); // Menghubungkan ke database
            // Data yang akan diupdate
            $data = [
                'loket' => session()->get('fullname'),
                'status' => 'BATAL'
            ];
            // Mengubah status dan mengisi nama loket
            $db->table('antrean')->set($data)->where('id_antrean', $id)->update();
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Antrean dibatalkan']);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function notify_clients()
    {
        $client = \Config\Services::curlrequest();
        $response = $client->post(env('WS-URL-PHP'), [
            'json' => []
        ]);

        return $this->response->setJSON([
            'status' => 'Notification sent',
            'response' => json_decode($response->getBody(), true)
        ]);
    }
}
