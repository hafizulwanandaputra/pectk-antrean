<?php

namespace App\Controllers;

use App\Models\AntreanModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController
{
    protected $AntreanModel;
    public function __construct()
    {
        $this->AntreanModel = new AntreanModel();
    }
    public function index()
    {
        if (session()->get('role') != "Satpam") {
            // GREETINGS
            $seasonalGreetingA = array(); // Array untuk menyimpan ucapan musiman
            $seasonalGreetingA[] = array('dayBegin' => 30, 'monthBegin' => 12, 'dayEnd' => 31, 'monthEnd' => 12, 'text' => 'Selamat Tahun Baru'); // Ucapan untuk Tahun Baru
            $seasonalGreetingA[] = array('dayBegin' => 1, 'monthBegin' => 1, 'dayEnd' => 2, 'monthEnd' => 1, 'text' => 'Selamat Tahun Baru'); // Ucapan untuk hari pertama Tahun Baru

            $timeGreetingA = array(); // Array untuk menyimpan ucapan berdasarkan waktu
            $timeGreetingA[] = array('timeBegin' => 0, 'timeEnd' => 5, 'text' => 'Selamat Malam'); // Ucapan malam
            $timeGreetingA[] = array('timeBegin' => 5, 'timeEnd' => 11, 'text' => 'Selamat Pagi'); // Ucapan pagi
            $timeGreetingA[] = array('timeBegin' => 11, 'timeEnd' => 16, 'text' => 'Selamat Siang'); // Ucapan siang
            $timeGreetingA[] = array('timeBegin' => 16, 'timeEnd' => 19, 'text' => 'Selamat Sore'); // Ucapan sore
            $timeGreetingA[] = array('timeBegin' => 19, 'timeEnd' => 24, 'text' => 'Selamat Malam'); // Ucapan malam

            $standardGreetingA = array(); // Array untuk menyimpan ucapan standar
            $standardGreetingA[] = array('text' => 'Halo'); // Ucapan standar 1
            $standardGreetingA[] = array('text' => 'Hai'); // Ucapan standar 2

            $txtGreeting = ''; // Variabel untuk menyimpan ucapan yang akan ditampilkan

            // Mendapatkan tanggal dan bulan saat ini
            $d = (int)date('d');
            $m = (int)date('m');

            // Memeriksa apakah ada ucapan musiman yang cocok dengan tanggal saat ini
            if ($txtGreeting == '')
                if (count($seasonalGreetingA) > 0)
                    foreach ($seasonalGreetingA as $sgA) {
                        $d1 = $sgA['dayBegin']; // Hari mulai ucapan
                        $m1 = $sgA['monthBegin']; // Bulan mulai ucapan

                        $d2 = $sgA['dayEnd']; // Hari akhir ucapan
                        $m2 = $sgA['monthEnd']; // Bulan akhir ucapan

                        // Memeriksa apakah tanggal saat ini berada dalam rentang ucapan musiman
                        if ($m >= $m1 and $m <= $m2)
                            if ($d >= $d1 and $d <= $d2)
                                $txtGreeting = $sgA['text']; // Menyimpan ucapan musiman yang cocok
                    }

            // Mendapatkan waktu saat ini
            $time = (int)date('H');
            // Memeriksa apakah ada ucapan berdasarkan waktu yang cocok dengan waktu saat ini
            if ($txtGreeting == '')
                if (count($timeGreetingA) > 0)
                    foreach ($timeGreetingA as $tgA) {
                        // Memeriksa apakah waktu saat ini berada dalam rentang ucapan berdasarkan waktu
                        if ($time >= $tgA['timeBegin'] and $time <= $tgA['timeEnd']) {
                            $txtGreeting = $tgA['text']; // Menyimpan ucapan berdasarkan waktu yang cocok
                            break; // Keluar dari loop setelah menemukan ucapan
                        }
                    }

            // Jika tidak ada ucapan musiman atau waktu yang cocok, memilih ucapan standar secara acak
            if ($txtGreeting == '')
                if (count($standardGreetingA) > 0) {
                    $ind = rand(0, count($standardGreetingA) - 1); // Memilih indeks acak dari ucapan standar
                    if (isset($standardGreetingA[$ind])) $txtGreeting = $standardGreetingA[$ind]['text']; // Menyimpan ucapan standar yang dipilih
                }
            // END GREETINGS

            // Menghubungkan ke database
            $db = db_connect();
            // Mendapatkan tabel-tabel yang diperlukan
            $antrean = $db->table('antrean');
            $user = $db->table('user');
            $user_sessions = $db->table('user_sessions');


            // Menghitung total data dari setiap tabel
            $antreanpiegraph = $antrean->select('nama_jaminan, COUNT(*) AS total_antrean')
                ->orderBy('nama_jaminan', 'DESC')
                ->groupBy('nama_jaminan')
                ->get();
            $antreangraph = $antrean->select('DATE_FORMAT(tanggal_antrean, "%Y-%m") AS bulan, COUNT(*) AS total_antrean')
                ->groupBy('DATE_FORMAT(tanggal_antrean, "%Y-%m")')
                ->get();
            $antreankodegraph = $antrean->select('DATE_FORMAT(tanggal_antrean, "%Y-%m") AS bulan, nama_jaminan, COUNT(*) AS total_antrean')
                ->orderBy('nama_jaminan', 'DESC')
                ->groupBy('DATE_FORMAT(tanggal_antrean, "%Y-%m"), nama_jaminan')
                ->get()
                ->getResultArray();

            // Inisialisasi array untuk labels (bulan unik) dan datasets
            $labels_antreankode = [];
            $data_per_nama_jaminanl = [];

            // Proses data hasil query
            foreach ($antreankodegraph as $row) {
                // Tambahkan bulan ke array labels jika belum ada
                if (!in_array($row['bulan'], $labels_antreankode)) {
                    $labels_antreankode[] = $row['bulan'];
                }

                // Atur data rawat jalan per nama_jaminan
                $data_per_nama_jaminanl[$row['nama_jaminan']][$row['bulan']] = $row['total_antrean'];
            }

            // Urutkan labels secara kronologis
            sort($labels_antreankode);

            // Siapkan struktur data untuk Chart.js
            $datasets_antreankode = [];
            foreach ($data_per_nama_jaminanl as $kode => $data_bulan) {
                $dataset = [
                    'label' => $kode,
                    'pointStyle' => 'circle',
                    'pointRadius' => 6,
                    'pointHoverRadius' => 12,
                    'fill' => false,
                    'data' => []
                ];

                // Isi data sesuai urutan bulan di labels
                foreach ($labels_antreankode as $bulan) {
                    // Gunakan nilai rawat jalan jika ada, atau 0 jika tidak ada data untuk bulan tersebut
                    $dataset['data'][] = $data_bulan[$bulan] ?? 0;
                }

                $datasets_antreankode[] = $dataset;
            }

            $total_user = $user->countAllResults(); // Total pengguna
            $total_user_inactive = $user->where('active', 0)->countAllResults(); // Total pengguna nonaktif
            $total_user_active = $user->where('active', 1)->countAllResults(); // Total pengguna aktif

            $currentDateTime = date('Y-m-d H:i:s');
            $total_sessions = $user_sessions->where('session_token !=', session()->get('session_token'))->countAllResults(); // Total sesi
            $total_sessions_expired = $user_sessions->where('expires_at <', $currentDateTime)->where('session_token !=', session()->get('session_token'))->countAllResults(); // Total sesi kedaluwarsa
            $total_sessions_active = $user_sessions->where('expires_at >=', $currentDateTime)->where('session_token !=', session()->get('session_token'))->countAllResults(); // Total sesi aktif

            // Menyusun data untuk ditampilkan di view
            $data = [
                'antreanpiegraph' => $antreanpiegraph,
                'labels_antreankode' => json_encode($labels_antreankode),
                'datasets_antreankode' => json_encode($datasets_antreankode),
                'antreangraph' => $antreangraph,
                'total_user' => $total_user,
                'total_user_inactive' => $total_user_inactive,
                'total_user_active' => $total_user_active,
                'total_sessions' => $total_sessions,
                'total_sessions_expired' => $total_sessions_expired,
                'total_sessions_active' => $total_sessions_active,
                'txtgreeting' => $txtGreeting, // Ucapan yang ditentukan sebelumnya
                'title' => 'Beranda - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Beranda', // Judul header
                'agent' => $this->request->getUserAgent() // Mendapatkan user agent dari request
            ];

            // Mengembalikan tampilan beranda dengan data yang telah disiapkan
            return view('dashboard/home/index', $data);
        } else {
            // Menyusun data untuk ditampilkan di view
            $data = [
                'title' => 'Beranda - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Beranda', // Judul header
                'agent' => $this->request->getUserAgent() // Mendapatkan user agent dari request
            ];

            // Mengembalikan tampilan beranda dengan data yang telah disiapkan
            return view('dashboard/home/satpam', $data);
        }
    }

    public function list_antrean()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Satpam"
        if (session()->get('role') == 'Satpam') {
            // Mengambil data dari permintaan POST
            $request = $this->request->getPost();
            $search = $request['search']['value']; // Nilai pencarian
            $start = $request['start']; // Indeks awal untuk paginasi
            $length = $request['length']; // Panjang halaman
            $draw = $request['draw']; // Hitungan gambar untuk DataTables

            // Mendapatkan parameter pengurutan
            $order = $request['order'];
            $sortColumnIndex = $order[0]['column']; // Indeks kolom
            $sortDirection = $order[0]['dir']; // Arah pengurutan (asc atau desc)

            // Pemetaan indeks kolom ke nama kolom di database
            $columnMapping = [
                0 => 'id_antrean',
                1 => 'id_antrean',
                2 => 'nama_jaminan',
                3 => 'kode_antrean',
                4 => 'tanggal_antrean',
                5 => 'satpam',
            ];

            // Mendapatkan kolom untuk diurutkan
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_antrean';

            // Jika $search kosong, kembalikan data kosong
            if (empty($search)) {
                return $this->response->setJSON([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => []
                ]);
            }

            // Mendapatkan jumlah total catatan
            $totalRecords = $this->AntreanModel
                ->where('status', 'BELUM DIPANGGIL')
                ->countAllResults(true);

            // Menerapkan kueri pencarian
            if ($search) {
                $this->AntreanModel
                    ->groupStart()
                    ->like('tanggal_antrean', $search)
                    ->groupEnd()
                    ->where('status', 'BELUM DIPANGGIL')
                    ->orderBy($sortColumn, $sortDirection); // Mengurutkan hasil
            }

            // Mendapatkan jumlah catatan yang terfilter
            $filteredRecords = $this->AntreanModel
                ->where('status', 'BELUM DIPANGGIL')
                ->countAllResults(false);

            // Mengambil data pengguna
            $users = $this->AntreanModel->where('status', 'BELUM DIPANGGIL')
                ->orderBy($sortColumn, $sortDirection) // Mengurutkan hasil
                ->findAll($length, $start); // Mengambil hasil dengan batasan panjang dan awal

            // Menambahkan kolom 'no' untuk menandai urutan
            foreach ($users as $index => &$item) {
                $item['no'] = $start + $index + 1; // Menambahkan kolom 'no'
            }

            // Mengembalikan respons JSON
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords, // Total catatan
                'recordsFiltered' => $filteredRecords, // Catatan terfilter
                'data' => $users // Data pengguna
            ]);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan error
            ]);
        }
    }

    public function cetak_antrean($id)
    {
        if (session()->get('role') == 'Satpam') {
            // Ambil data antrean
            $db = \Config\Database::connect();
            $builder = $db->table('antrean');
            $builder->where('id_antrean', $id);
            $antrean = $builder->get()->getRowArray();

            if (!$antrean) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

            // Siapkan HTML dari view
            $data = [
                'antrean' => $antrean,
                'title' => 'Cetak Antrean - ' . $this->systemName,
                'agent' => $this->request->getUserAgent()
            ];
            $client = \Config\Services::curlrequest();
            $html = view('dashboard/home/struk', $data);
            $filename = 'antrean.pdf';

            $response = $client->post(env('PDF-URL'), [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'html' => $html,
                    'filename' => $filename,
                    'paper' => [
                        'width' => '80mm',
                        'height' => '300mm', // bisa kamu ubah sesuai kebutuhan
                    ]
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if (isset($result['success']) && $result['success']) {
                $path = WRITEPATH . 'temp/' . $result['file'];

                // Kembalikan file PDF ke browser (stream)
                return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                    ->setBody(file_get_contents($path));
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setBody('Gagal membuat PDF');
            }
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function buat_antrean()
    {
        // Memeriksa peran pengguna, hanya 'Satpam' yang diizinkan
        if (session()->get('role') == 'Satpam') {
            // Ambil parameter jaminan dari URL
            $jaminan = strtoupper($this->request->getVar('jaminan'));

            // Tetapkan kode_antrean berdasarkan jaminan
            switch ($jaminan) {
                case 'UMUM':
                    $kode_antrean = 'U';
                    $nama_jaminan = 'UMUM';
                    break;
                case 'BPJS KESEHATAN':
                    $kode_antrean = 'B';
                    $nama_jaminan = 'BPJS KESEHATAN';
                    break;
                case 'ASURANSI':
                    $kode_antrean = 'A';
                    $nama_jaminan = 'ASURANSI';
                    break;
                default:
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Jaminan tidak dikenali: harus UMUM, BPJS KESEHATAN, atau ASURANSI',
                    ]);
            }

            $db = db_connect();

            // Ambil nomor antrean terakhir berdasarkan kode_antrean
            $lastQueue = $db->table('antrean')
                ->select('RIGHT(nomor_antrean, 3) AS last_number')
                ->where('kode_antrean', $kode_antrean)
                ->where('tanggal_antrean >=', date('Y-m-d 00:00:00'))
                ->where('tanggal_antrean <=', date('Y-m-d 23:59:59'))
                ->orderBy('nomor_antrean', 'DESC')
                ->limit(1)
                ->get()
                ->getRow();

            $queueIncrement = $lastQueue ? intval($lastQueue->last_number) + 1 : 1;
            $nomor_antrean = str_pad($queueIncrement, 3, '0', STR_PAD_LEFT);

            $data = [
                'nama_jaminan' => $nama_jaminan,
                'kode_antrean' => $kode_antrean,
                'nomor_antrean' => $nomor_antrean,
                'satpam' => session()->get('fullname'),
                'status' => 'BELUM DIPANGGIL'
            ];

            $this->AntreanModel->insert($data);
            $insertedId = $this->AntreanModel->insertID();
            $insertedRow = $this->AntreanModel->find($insertedId);

            $this->notify_clients('update');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Nomor antrean berhasil dibuat',
                'data' => [
                    'id_antrean' => $insertedId,
                    'antrean' => $kode_antrean . '-' . $nomor_antrean,
                    'nama_jaminan' => $nama_jaminan,
                    'tanggal_antrean' => $insertedRow['tanggal_antrean']
                ]
            ]);
        } else {
            throw PageNotFoundException::forPageNotFound();
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
