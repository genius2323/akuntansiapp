<?php namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    // Hapus baris DBGroup agar terhubung ke database 'default' (db_akuntansi)
    
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    protected $useSoftDeletes   = true;
    
    protected $allowedFields    = ['category_id', 'name', 'price', 'stock'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Mengambil semua produk dengan nama kategori.
     */
    public function getProductsWithCategory()
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id')
                    ->where('products.deleted_at', null)
                    ->findAll();
    }
}
