<?php
namespace App\Models;
use CodeIgniter\Model;
class GondolaModel extends Model
{
    protected $table            = 'gondola';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['name', 'description', 'otoritas', 'kode_ky', 'batas_tanggal_sistem', 'mode_batas_tanggal'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
