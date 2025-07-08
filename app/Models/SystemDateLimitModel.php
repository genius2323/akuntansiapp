<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemDateLimitModel extends Model
{
    protected $table            = 'system_date_limits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['menu', 'batas_tanggal', 'mode_batas_tanggal', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
