<?php
namespace App\Models;
use CodeIgniter\Model;
class VoltaseModel extends Model
{
    protected $table            = 'voltase';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
protected $allowedFields    = ['name', 'description', 'otoritas', 'batas_tanggal_sistem', 'mode_batas_tanggal', 'kode_ky'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
