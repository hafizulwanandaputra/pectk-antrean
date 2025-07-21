<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailResepModel extends Model
{
    protected $table = 'detail_resep';
    protected $primaryKey = 'id_detail_resep';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'id_resep',
        'id_obat',
        'id_batch_obat',
        'nama_obat',
        'kategori_obat',
        'bentuk_obat',
        'nama_batch',
        'signa',
        'catatan',
        'cara_pakai',
        'jumlah',
        'harga_satuan'
    ];
}
