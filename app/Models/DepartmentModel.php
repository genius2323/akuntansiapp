<?php namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';
    protected $allowedFields = ['name', 'description', 'deleted_at', 'recovered_at'];
    protected $useSoftDeletes = true; // Aktifkan soft delete
    
    public function getAllDepartments()
    {
        return $this->where('deleted_at', null) // Hanya department aktif
                ->orderBy('id', 'ASC')
                ->findAll();
    }
    public function isActive($departmentId): bool
    {
        $department = $this->where('id', $departmentId)
                        ->where('deleted_at', null)
                        ->first();
                        
        return ($department !== null);
    }
}