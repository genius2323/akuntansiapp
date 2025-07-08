<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanItemModel extends Model
{
    protected $table            = 'sales_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'sales_id',
        'product_id',
        'product_code',
        'product_name',
        'qty',
        'unit',
        'price',
        'discount',
        'total',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
