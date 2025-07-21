<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
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
        $antreanpiegraph = $antrean->select('kode_antrean, COUNT(*) AS total_antrean')
            ->orderBy('kode_antrean', 'ASC')
            ->groupBy('kode_antrean')
            ->get();
        $antreangraph = $antrean->select('DATE_FORMAT(tanggal_antrean, "%Y-%m") AS bulan, COUNT(*) AS total_antrean')
            ->groupBy('DATE_FORMAT(tanggal_antrean, "%Y-%m")')
            ->get();
        $antreankodegraph = $antrean->select('DATE_FORMAT(tanggal_antrean, "%Y-%m") AS bulan, kode_antrean, COUNT(*) AS total_antrean')
            ->orderBy('kode_antrean', 'ASC')
            ->groupBy('DATE_FORMAT(tanggal_antrean, "%Y-%m"), kode_antrean')
            ->get()
            ->getResultArray();

        // Inisialisasi array untuk labels (bulan unik) dan datasets
        $labels_antreankode = [];
        $data_per_kode_antreanl = [];

        // Proses data hasil query
        foreach ($antreankodegraph as $row) {
            // Tambahkan bulan ke array labels jika belum ada
            if (!in_array($row['bulan'], $labels_antreankode)) {
                $labels_antreankode[] = $row['bulan'];
            }

            // Atur data rawat jalan per dokter
            $data_per_kode_antreanl[$row['dokter']][$row['bulan']] = $row['total_antrean'];
        }

        // Urutkan labels secara kronologis
        sort($labels_antreankode);

        // Siapkan struktur data untuk Chart.js
        $datasets_antreankode = [];
        foreach ($data_per_kode_antreanl as $kode => $data_bulan) {
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
    }

    public function icd_x()
    {
        if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") {
            $db = db_connect();
            $bulan = $this->request->getGet('bulan');

            if (!$bulan) {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Silakan masukkan bulan.'
                ]);
            }

            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $startNumber = $offset + 1;

            $icdx_bulanan_subquery = "
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icdx_kode_1 AS icdx_kode FROM medrec_assesment WHERE icdx_kode_1 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icdx_kode_2 AS icdx_kode FROM medrec_assesment WHERE icdx_kode_2 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icdx_kode_3 AS icdx_kode FROM medrec_assesment WHERE icdx_kode_3 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icdx_kode_4 AS icdx_kode FROM medrec_assesment WHERE icdx_kode_4 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icdx_kode_5 AS icdx_kode FROM medrec_assesment WHERE icdx_kode_5 IS NOT NULL
    ";

            $query = "
        SELECT 
            icdx_data.bulan, 
            icdx_data.icdx_kode, 
            icd_x.icdNamaInggris AS icdx_nama,
            COUNT(*) AS total_icdx 
        FROM ({$icdx_bulanan_subquery}) AS icdx_data 
        JOIN icd_x ON icdx_data.icdx_kode = icd_x.icdKode
        WHERE icdx_data.bulan LIKE '" . $db->escapeLikeString($bulan) . "%' 
    ";

            $query .= " GROUP BY icdx_data.bulan, icdx_data.icdx_kode, icd_x.icdNamaInggris ";
            $query .= " ORDER BY icdx_data.bulan DESC, total_icdx DESC ";
            $query .= " LIMIT {$limit} OFFSET {$offset} ";

            $icdx_bulanan = $db->query($query)->getResultArray();

            foreach ($icdx_bulanan as $index => &$data) {
                $data['number'] = $startNumber + $index;
            }

            $totalQuery = "
        SELECT COUNT(*) as total FROM (
            SELECT icdx_data.bulan FROM ({$icdx_bulanan_subquery}) AS icdx_data 
            JOIN icd_x ON icdx_data.icdx_kode = icd_x.icdKode
            WHERE icdx_data.bulan LIKE '" . $db->escapeLikeString($bulan) . "%' 
    ";
            $totalQuery .= " GROUP BY icdx_data.bulan, icdx_data.icdx_kode, icd_x.icdNamaInggris 
        ) AS count_table ";

            $total = $db->query($totalQuery)->getRowArray()['total'] ?? 0;

            return $this->response->setJSON([
                'data' => $icdx_bulanan,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function icd_9()
    {
        if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") {
            $db = db_connect();
            $bulan = $this->request->getGet('bulan');

            if (!$bulan) {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Silakan masukkan bulan.'
                ]);
            }

            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $startNumber = $offset + 1;

            $icd9_bulanan_subquery = "
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icd9_kode_1 AS icd9_kode FROM medrec_assesment WHERE icd9_kode_1 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icd9_kode_2 AS icd9_kode FROM medrec_assesment WHERE icd9_kode_2 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icd9_kode_3 AS icd9_kode FROM medrec_assesment WHERE icd9_kode_3 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icd9_kode_4 AS icd9_kode FROM medrec_assesment WHERE icd9_kode_4 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icd9_kode_5 AS icd9_kode FROM medrec_assesment WHERE icd9_kode_5 IS NOT NULL
    ";

            $query = "
        SELECT 
            icd9_data.bulan, 
            icd9_data.icd9_kode, 
            icd_9.icdNamaInggris AS icd9_nama,
            COUNT(*) AS total_icd9 
        FROM ({$icd9_bulanan_subquery}) AS icd9_data 
        JOIN icd_9 ON icd9_data.icd9_kode = icd_9.icdKode
        WHERE icd9_data.bulan LIKE '" . $db->escapeLikeString($bulan) . "%' 
    ";

            $query .= " GROUP BY icd9_data.bulan, icd9_data.icd9_kode, icd_9.icdNamaInggris ";
            $query .= " ORDER BY icd9_data.bulan DESC, total_icd9 DESC ";
            $query .= " LIMIT {$limit} OFFSET {$offset} ";

            $icd9_bulanan = $db->query($query)->getResultArray();

            foreach ($icd9_bulanan as $index => &$data) {
                $data['number'] = $startNumber + $index;
            }

            $totalQuery = "
        SELECT COUNT(*) as total FROM (
            SELECT icd9_data.bulan FROM ({$icd9_bulanan_subquery}) AS icd9_data 
            JOIN icd_9 ON icd9_data.icd9_kode = icd_9.icdKode
            WHERE icd9_data.bulan LIKE '" . $db->escapeLikeString($bulan) . "%' 
    ";
            $totalQuery .= " GROUP BY icd9_data.bulan, icd9_data.icd9_kode, icd_9.icdNamaInggris 
        ) AS count_table ";

            $total = $db->query($totalQuery)->getRowArray()['total'] ?? 0;

            return $this->response->setJSON([
                'data' => $icd9_bulanan,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
