<?php namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    // Menghubungkan ke grup database 'db1'
    protected $DBGroup          = 'db1';

    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Mengaktifkan soft delete
    protected $useSoftDeletes   = true;

    protected $allowedFields    = ['name', 'description'];

    // Mengaktifkan timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
    