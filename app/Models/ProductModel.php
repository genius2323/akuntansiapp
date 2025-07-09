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
    
    protected $allowedFields    = [
        'category_id',
        'satuan_id',
        'jenis_id',
        'pelengkap_id',
        'gondola_id',
        'merk_id',
        'warna_sinar_id',
        'ukuran_barang_id',
        'voltase_id',
        'dimensi_id',
        'warna_body_id',
        'warna_bibir_id',
        'kaki_id',
        'model_id',
        'fiting_id',
        'daya_id',
        'jumlah_mata_id',
        'name',
        'price',
        'stock',
        'otoritas', // tambahkan kolom otoritas di tabel products
    ];

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
