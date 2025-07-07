<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DepartmentModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $departmentModel;
    protected $helpers = ['form'];

    public function __construct()
    {
        $this->userModel = new UserModel();
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
        $rules = [
            'kode_ky' => 'required|is_unique[users.kode_ky]|max_length[10]',
            'username' => 'required|is_unique[users.username]|min_length[3]',
            'password' => 'required|min_length[8]',
            'department_id' => 'required|numeric',
            'noktp' => 'required|is_unique[users.noktp]|min_length[16]|max_length[16]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Data disimpan langsung, Model akan melakukan hashing pada password secara otomatis via beforeInsert
        $this->userModel->save([
            'kode_ky' => esc($this->request->getPost('kode_ky')),
            'username' => esc($this->request->getPost('username')),
            'password' => $this->request->getPost('password'), // Diteruskan sebagai plain text
            'department_id' => $this->request->getPost('department_id'),
            'alamat' => esc($this->request->getPost('alamat')),
            'noktp' => esc($this->request->getPost('noktp'))
        ]); //

        return redirect()->to('/users')->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/users')->with('error', 'ID tidak valid');
        }

        $user = $this->userModel->find($id); //
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'departments' => $this->departmentModel->findAll(), //
            'validation' => \Config\Services::validation() //
        ];
        
        return view('general/users/edit', $data);
    }

    public function update($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/users')->with('error', 'ID tidak valid');
        }

        $rules = [
            'kode_ky' => "required|is_unique[users.kode_ky,id,{$id}]|max_length[10]",
            'username' => "required|is_unique[users.username,id,{$id}]|min_length[3]",
            'department_id' => 'required|numeric',
            'noktp' => "required|is_unique[users.noktp,id,{$id}]|min_length[16]|max_length[16]",
            'password' => 'permit_empty|min_length[8]' //
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode_ky' => esc($this->request->getPost('kode_ky')),
            'username' => esc($this->request->getPost('username')),
            'department_id' => $this->request->getPost('department_id'),
            'alamat' => esc($this->request->getPost('alamat')),
            'noktp' => esc($this->request->getPost('noktp'))
        ]; //

        // PERBAIKAN UTAMA: Hapus hashing manual.
        // Cukup teruskan password jika ada, Model akan menanganinya via beforeUpdate.
        if ($password = $this->request->getPost('password')) {
            $data['password'] = $password; // Teruskan password sebagai plain text
        }

        $this->userModel->update($id, $data); //
        
        return redirect()->to('/users')->with('success', 'Data user berhasil diperbarui');
    }

    public function delete($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/users')->with('error', 'ID tidak valid');
        }

        $this->userModel->delete($id); //
        return redirect()->to('/users')->with('success', 'User berhasil diarsipkan');
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

        $this->userModel->delete($id, true); // Hard delete
        return redirect()->to('/users/trash')->with('success', 'User berhasil dihapus permanen');
    }
}