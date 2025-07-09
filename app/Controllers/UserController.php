<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DepartmentModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $userModelDb1;
    protected $departmentModel;
    protected $helpers = ['form'];

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userModelDb1 = new UserModel(\Config\Database::connect('db1'));
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen User',
            'users' => $this->userModel->getUsersForDatatable()->findAll(), //
            'departments' => $this->departmentModel->findAll(), //
            'active_menu' => 'users'
        ];
        
        return view('general/users/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah User Baru',
            'departments' => $this->departmentModel->findAll(), //
            'validation' => \Config\Services::validation() //
        ];
        
        return view('general/users/create', $data);
    }

    public function store()
    {
        // PERBAIKAN: Menambahkan aturan validasi yang sebelumnya tidak ada
        // Validasi: noktp boleh kosong, numeric, 16 digit, dan unique jika diisi
        $rules = [
            'kode_ky' => 'required|is_unique[users.kode_ky]|max_length[10]',
            'username' => 'required|is_unique[users.username]|min_length[3]',
            'password' => 'required|min_length[8]',
            'department_id' => 'required|numeric',
            // is_unique tanpa placeholder id untuk insert
            'noktp' => 'permit_empty|numeric|min_length[16]|max_length[16]|is_unique[users.noktp]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode_ky' => esc($this->request->getPost('kode_ky')),
            'username' => esc($this->request->getPost('username')),
            'password' => $this->request->getPost('password'),
            'department_id' => $this->request->getPost('department_id'),
            'alamat' => esc($this->request->getPost('alamat')),
            'otoritas' => null
        ];
        $noktp = $this->request->getPost('noktp');
        if (!empty($noktp)) {
            $data['noktp'] = esc($noktp);
        }

        // Simpan ke database default
        if (!$this->userModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors() ?: ['Gagal menyimpan user (db default)']);
        }
        $userId = $this->userModel->getInsertID();
        // Simpan ke database kedua dengan id yang sama
        $userModelDb1 = new \App\Models\UserModel(\Config\Database::connect('db1'));
        $data['id'] = $userId;
        if (!$userModelDb1->insert($data)) {
            // Rollback: hapus user di db default jika gagal di db1
            $this->userModel->delete($userId);
            return redirect()->back()->withInput()->with('errors', $userModelDb1->errors() ?: ['Gagal menyimpan user (db1)']);
        }

        return redirect()->to('/users')->with('success', 'User berhasil ditambahkan di kedua database');
    }

    public function edit($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/users')->with('error', 'ID tidak valid');
        }

        $user = $this->userModel->find($id);
        $userDb1 = $this->userModelDb1->find($id);
        if (!$user || !$userDb1) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }
        if (($user['otoritas'] ?? null) !== 'T' || ($userDb1['otoritas'] ?? null) !== 'T') {
            return redirect()->to('/users')->with('error', 'User ini belum diotorisasi, tidak bisa diedit. Silakan otorisasi dulu di menu otoritas.');
        }

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'departments' => $this->departmentModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('general/users/edit', $data);
    }

    public function update($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/users')->with('error', 'ID tidak valid');
        }

        // Validasi otoritas edit di kedua database
        $user = $this->userModel->find($id);
        $userDb1 = $this->userModelDb1->find($id);
        if (!$user || !$userDb1 || ($user['otoritas'] ?? null) !== 'T' || ($userDb1['otoritas'] ?? null) !== 'T') {
            return redirect()->to('/users')->with('error', 'User ini belum diotorisasi, tidak bisa edit. Silakan otorisasi dulu di menu otoritas.');
        }

        $rules = [
            'kode_ky' => "required|is_unique[users.kode_ky,id,{$id}]|max_length[10]",
            'username' => "required|is_unique[users.username,id,{$id}]|min_length[3]",
            'department_id' => 'required|numeric',
            'password' => 'permit_empty|min_length[8]',
            'noktp' => 'permit_empty|numeric|min_length[16]|max_length[16]|is_unique[users.noktp,id,{id}]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode_ky' => esc($this->request->getPost('kode_ky')),
            'username' => esc($this->request->getPost('username')),
            'department_id' => $this->request->getPost('department_id'),
            'alamat' => esc($this->request->getPost('alamat')),
            'otoritas' => $user['otoritas'] ?? null
        ];
        $noktp = $this->request->getPost('noktp');
        if (!empty($noktp)) {
            $data['noktp'] = esc($noktp);
        }
        if ($password = $this->request->getPost('password')) {
            $data['password'] = $password;
        }

        // Update ke dua database
        if (!$this->userModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors() ?: ['Gagal update user (db default)']);
        }
        if (!$this->userModelDb1->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModelDb1->errors() ?: ['Gagal update user (db1)']);
        }

        // Reset otoritas setelah update agar tidak bisa edit/hapus berulang tanpa otorisasi ulang
        $this->userModel->update($id, ['otoritas' => null]);
        $this->userModelDb1->update($id, ['otoritas' => null]);

        return redirect()->to('/users')->with('success', 'Data user berhasil diperbarui di kedua database. Otoritas telah direset, silakan otorisasi ulang jika ingin edit/hapus lagi.');
    }

    public function delete($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/users')->with('error', 'ID tidak valid');
        }
        $user = $this->userModel->find($id);
        $userDb1 = $this->userModelDb1->find($id);
        if (!$user || !$userDb1 || ($user['otoritas'] ?? null) !== 'T' || ($userDb1['otoritas'] ?? null) !== 'T') {
            return redirect()->to('/users')->with('error', 'Akses hapus user ini membutuhkan otorisasi. Silakan otorisasi dulu di menu otoritas.');
        }
        // Soft delete di kedua database
        $this->userModel->delete($id);
        $this->userModelDb1->delete($id);
        // Reset otoritas setelah hapus
        $this->userModel->update($id, ['otoritas' => null]);
        $this->userModelDb1->update($id, ['otoritas' => null]);
        return redirect()->to('/users')->with('success', 'User berhasil diarsipkan di kedua database. Otoritas telah direset, silakan otorisasi ulang jika ingin edit/hapus lagi.');
    }

    public function trash()
    {
        $data = [
            'title' => 'User Terarsip',
            'deletedUsers' => $this->userModel->getDeletedUsers(), //
            'active_menu' => 'users'
        ];
        
        return view('general/users/trash', $data);
    }

    public function restore($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/users/trash')->with('error', 'ID tidak valid');
        }

        if ($this->userModel->recoverUser($id)) { //
            return redirect()->to('/users/trash')->with('success', 'User berhasil dipulihkan');
        }

        return redirect()->to('/users/trash')->with('error', 'Gagal memulihkan user');
    }

    public function forceDelete($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/users/trash')->with('error', 'ID tidak valid');
        }
        $user = $this->userModel->onlyDeleted()->find($id);
        if (!$user || !isset($user['otoritas']) || $user['otoritas'] !== 'T') {
            return redirect()->to('/users/trash')->with('error', 'Akses hapus permanen user ini membutuhkan otorisasi.');
        }
        $this->userModel->delete($id, true); // Hard delete
        return redirect()->to('/users/trash')->with('success', 'User berhasil dihapus permanen');
    }
}