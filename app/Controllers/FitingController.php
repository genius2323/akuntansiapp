<?php
namespace App\Controllers;
use App\Models\FitingModel;
class FitingController extends BaseController
{
    private function getFitingModel(string $dbGroup = 'default'): FitingModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new FitingModel($db);
    }
    public function index()
    {
        $classifications = [
            ['name' => 'Kategori', 'url' => 'categories', 'icon' => 'fas fa-tags', 'active' => false],
            ['name' => 'Satuan', 'url' => 'satuan', 'icon' => 'fas fa-ruler-combined', 'active' => false],
            ['name' => 'Jenis', 'url' => 'jenis', 'icon' => 'fas fa-boxes', 'active' => false],
            ['name' => 'Pelengkap', 'url' => 'pelengkap', 'icon' => 'fas fa-puzzle-piece', 'active' => false],
            ['name' => 'Gondola', 'url' => 'gondola', 'icon' => 'fas fa-store-alt', 'active' => false],
            ['name' => 'Merk Barang', 'url' => 'merk', 'icon' => 'fas fa-copyright', 'active' => false],
            ['name' => 'Warna Sinar', 'url' => 'warna-sinar', 'icon' => 'fas fa-lightbulb', 'active' => false],
            ['name' => 'Ukuran Barang', 'url' => 'ukuran-barang', 'icon' => 'fas fa-expand-arrows-alt', 'active' => false],
            ['name' => 'Voltase', 'url' => 'voltase', 'icon' => 'fas fa-bolt', 'active' => false],
            ['name' => 'Dimensi', 'url' => 'dimensi', 'icon' => 'fas fa-ruler', 'active' => false],
            ['name' => 'Warna Body', 'url' => 'warna-body', 'icon' => 'fas fa-palette', 'active' => false],
            ['name' => 'Warna Bibir', 'url' => 'warna-bibir', 'icon' => 'fas fa-tint', 'active' => false],
            ['name' => 'Kaki', 'url' => 'kaki', 'icon' => 'fas fa-shoe-prints', 'active' => false],
            ['name' => 'Model', 'url' => 'model', 'icon' => 'fas fa-star', 'active' => false],
            ['name' => 'Fiting', 'url' => 'fiting', 'icon' => 'fas fa-plug', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Fiting',
            'classifications' => $classifications,
            'fitings' => $this->getFitingModel('default')->findAll()
        ];
        return view('fiting/index', $data);
    }
    public function edit($id)
    {
        $fiting = $this->getFitingModel('default')->find($id);
        if (empty($fiting)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data fiting tidak ditemukan.');
        }
        if (!isset($fiting['otoritas']) || $fiting['otoritas'] !== 'T') {
            return redirect()->to('/fiting')->with('error', 'Akses edit fiting ini membutuhkan otoritas.');
        }
        $mode = $fiting['mode_batas_tanggal'] ?? 'manual';
        $batas = $fiting['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($fiting['created_at']) ? substr($fiting['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/fiting')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($fiting['created_at']) ? substr($fiting['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/fiting')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Fiting',
            'fiting' => $fiting
        ];
        return view('fiting/edit', $data);
    }

    public function create()
    {
        // Log debug dihapus setelah verifikasi sukses
        $rules = ['name' => 'required|min_length[2]|is_unique[fiting.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/fiting')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'kode_ky'     => session('kode_ky'),
        ];
        $mainModel = $this->getFitingModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getFitingModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (fiting) failed: ' . $e->getMessage());
                return redirect()->to('/fiting')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/fiting')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/fiting')->with('success', 'Data fiting berhasil ditambahkan.');
    }

    public function update($id)
    {
        $fiting = $this->getFitingModel('default')->find($id);
        if (empty($fiting) || !isset($fiting['otoritas']) || $fiting['otoritas'] !== 'T') {
            return redirect()->to('/fiting')->with('error', 'Akses update fiting ini membutuhkan otoritas.');
        }
        $mode = $fiting['mode_batas_tanggal'] ?? 'manual';
        $batas = $fiting['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($fiting['created_at']) ? substr($fiting['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/fiting')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($fiting['created_at']) ? substr($fiting['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/fiting')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[fiting.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getFitingModel('default')->update($id, $dataToUpdate);
        // Update kode_ky setelah update data
        $this->getFitingModel('default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getFitingModel('db1')->update($id, $dataToUpdate);
            $this->getFitingModel('db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (fiting update) failed: ' . $e->getMessage());
        }
        $this->getFitingModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getFitingModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (fiting otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/fiting')->with('success', 'Data fiting berhasil diperbarui.');
    }

    public function delete($id)
    {
        $fiting = $this->getFitingModel('default')->find($id);
        if (empty($fiting) || !isset($fiting['otoritas']) || $fiting['otoritas'] !== 'T') {
            return redirect()->to('/fiting')->with('error', 'Akses hapus fiting ini membutuhkan otoritas.');
        }
        $mode = $fiting['mode_batas_tanggal'] ?? 'manual';
        $batas = $fiting['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($fiting['created_at']) ? substr($fiting['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/fiting')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($fiting['created_at']) ? substr($fiting['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/fiting')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        // Update kode_ky sebelum soft delete
        $this->getFitingModel('default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getFitingModel('db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (fiting kode_ky update before delete) failed: ' . $e->getMessage());
        }
        $this->getFitingModel('default')->delete($id);
        try {
            $this->getFitingModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (fiting delete) failed: ' . $e->getMessage());
        }
        $this->getFitingModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getFitingModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (fiting otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/fiting')->with('success', 'Data fiting berhasil dihapus.');
    }
}
