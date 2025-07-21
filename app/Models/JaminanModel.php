<?php

namespace App\Models;

use CodeIgniter\Model;

class JaminanModel extends Model
{
    protected $table = 'jaminan';
    protected $primaryKey = 'jaminanId';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'jaminanKode',
        'jaminanAntrian',
        'jaminanNama',
        'jaminanStatus'
    ];
}
