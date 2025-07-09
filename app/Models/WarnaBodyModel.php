<?php
namespace App\Models;
use CodeIgniter\Model;
class WarnaBodyModel extends Model
{
    protected $table            = 'warna_body';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['name', 'description', 'otoritas', 'batas_tanggal_sistem', 'mode_batas_tanggal'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
