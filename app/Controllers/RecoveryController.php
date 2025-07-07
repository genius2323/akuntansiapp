<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DepartmentModel;

class RecoveryController extends BaseController
{
    protected $userModel;
    protected $departmentModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->departmentModel = new DepartmentModel();
    }

    /**
     * Menampilkan halaman arsip (data yang terhapus)
     */
    public function index()
    {
        $data = [
            'title' => 'Data Arsip',
            'deletedUsers' => $this->userModel->onlyDeleted()->findAll(), // Menggunakan method standar
            'deletedDepartments' => $this->departmentModel->onlyDeleted()->findAll() // Menggunakan method standar
        ];

        // Path view diperbaiki dari 'generals' menjadi 'general'
        return view('general/users/trash', $data);
    }

    /**
     * Memulihkan user
     */
    public function recoverUser($id)
    {
        // Fungsi ini sudah benar, tidak perlu diubah
        if ($this->request->getMethod() !== 'post') {
             return redirect()->back()->with('error', 'Metode tidak diizinkan!');
        }

        if ($this->userModel->recoverUser($id)) {
            return redirect()->to('/users/trash')
                ->with('success', 'User berhasil dipulihkan!');
        }

        return redirect()->back()
            ->with('error', 'Gagal memulihkan user!');
    }
}