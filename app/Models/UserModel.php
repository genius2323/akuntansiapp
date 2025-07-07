<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username',
        'password',
        'department_id',
        'kode_ky',
        'alamat',
        'noktp',
        'created_at',
        'updated_at',
        'deleted_at',
        'recovered_at'
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $recoveredField = 'recovered_at';

    protected $validationRules = [
        'username' => 'required|min_length[3]|is_unique[users.username,id,{id}]',
        'password' => 'required|min_length[8]',
        'department_id' => 'required|numeric',
        'noktp' => 'permit_empty|numeric|min_length[16]|max_length[16]'
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $plainPassword = trim($data['data']['password']);
            if (!empty($plainPassword)) {
                $data['data']['password'] = password_hash($plainPassword, PASSWORD_DEFAULT);
            } else {
                unset($data['data']['password']);
            }
        }
        return $data;
    }

    public function verifyUser($username, $password, $departmentId)
    {
        $user = $this->select('users.*, departments.deleted_at as department_deleted_at')
                     ->join('departments', 'departments.id = users.department_id')
                     ->where('users.username', $username)
                     ->where('users.department_id', $departmentId)
                     ->where('users.deleted_at', null)
                     ->first();

        if (!$user) {
            log_message('error', "Login failed: User '{$username}' not found in department '{$departmentId}'.");
            return false;
        }

        if ($user['department_deleted_at'] !== null) {
            log_message('error', "Login failed: Department '{$departmentId}' is inactive for user '{$username}'.");
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            log_message('error', "Login failed: Password mismatch for user '{$username}'.");
            return false;
        }

        return $user;
    }

    public function getUsersForDatatable()
    {
        return $this->select('users.*, departments.name as department_name')
                    ->join('departments', 'departments.id = users.department_id')
                    ->where('users.deleted_at', null);
    }

    public function getDeletedUsers()
    {
        return $this->onlyDeleted()->findAll();
    }

    public function recoverUser($id)
    {
        return $this->protect(false)
                    ->where('id', $id)
                    ->set([
                        'deleted_at' => null,
                        'recovered_at' => date('Y-m-d H:i:s')
                    ])
                    ->update();
    }
}