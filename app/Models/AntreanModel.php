<?php

namespace App\Models;

use CodeIgniter\Model;

class AntreanModel extends Model
{
    protected $table = 'antrean';
    protected $primaryKey = 'id_antrean';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'kode_antrean',
        'nomor_antrean',
        'tanggal_antrean',
        'satpam',
        'status'
    ];
}
