<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class GeneralOtoritasUserController extends Controller
{
    protected $userModelDefault;
    protected $userModelDb1;

    public function __construct()
    {
        $this->userModelDefault = new UserModel(\Config\Database::connect('default'));
        $this->userModelDb1 = new UserModel(\Config\Database::connect('db1'));
    }

    public function otoritasUser()
    {
        $users = $this->userModelDefault
            ->select('users.*, departments.name as department_name')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->where('users.deleted_at', null)
            ->findAll();
        return view('general/otoritas_user', [
            'users' => $users
        ]);
    }

    public function setOtoritasUser()
    {
        $userId = $this->request->getPost('user_id');
        $otoritas = $this->request->getPost('otoritas') ?? 'T';
        // Pastikan user masih aktif (belum dihapus)
        $userDefault = $this->userModelDefault->find($userId);
        $userDb1 = $this->userModelDb1->find($userId);
        if (!$userDefault || !$userDb1) {
            return redirect()->back()->with('error', 'User tidak ditemukan atau sudah dihapus.');
        }
        // Otorisasi
        $this->userModelDefault->update($userId, ['otoritas' => $otoritas]);
        $this->userModelDb1->update($userId, ['otoritas' => $otoritas]);
        // Reset otoritas setelah update/edit/hapus user (agar tidak bisa edit/hapus berulang tanpa otorisasi ulang)
        // (Akan dikosongkan oleh UserController setelah edit/hapus)
        return redirect()->back()->with('success', 'Otoritas user berhasil diupdate di kedua database!');
    }
}
