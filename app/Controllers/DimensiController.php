<?php
namespace App\Controllers;
use App\Models\DimensiModel;
class DimensiController extends BaseController
{
    private function getDimensiModel(string $dbGroup = 'default'): DimensiModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new DimensiModel($db);
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
            ['name' => 'Dimensi', 'url' => 'dimensi', 'icon' => 'fas fa-ruler', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Dimensi',
            'classifications' => $classifications,
            'dimensis' => $this->getDimensiModel('default')->findAll()
        ];
        return view('dimensi/index', $data);
    }

    public function edit($id)
    {
        $dimensi = $this->getDimensiModel('default')->find($id);
        if (empty($dimensi)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data dimensi tidak ditemukan.');
        }
        if (!isset($dimensi['otoritas']) || $dimensi['otoritas'] !== 'T') {
            return redirect()->to('/dimensi')->with('error', 'Akses edit dimensi ini membutuhkan otoritas.');
        }
        $mode = $dimensi['mode_batas_tanggal'] ?? 'manual';
        $batas = $dimensi['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($dimensi['created_at']) ? substr($dimensi['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/dimensi')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($dimensi['created_at']) ? substr($dimensi['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/dimensi')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Dimensi',
            'dimensi' => $dimensi
        ];
        return view('dimensi/edit', $data);
    }

    public function create()
    {
        // Log debug dihapus setelah verifikasi sukses
        $rules = ['name' => 'required|min_length[2]|is_unique[dimensi.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/dimensi')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'kode_ky'     => session('kode_ky'),
        ];
        $mainModel = $this->getDimensiModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getDimensiModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (dimensi) failed: ' . $e->getMessage());
                return redirect()->to('/dimensi')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/dimensi')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/dimensi')->with('success', 'Data dimensi berhasil ditambahkan.');
    }

    public function update($id)
    {
        $dimensi = $this->getDimensiModel('default')->find($id);
        if (empty($dimensi) || !isset($dimensi['otoritas']) || $dimensi['otoritas'] !== 'T') {
            return redirect()->to('/dimensi')->with('error', 'Akses update dimensi ini membutuhkan otoritas.');
        }
        $mode = $dimensi['mode_batas_tanggal'] ?? 'manual';
        $batas = $dimensi['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($dimensi['created_at']) ? substr($dimensi['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/dimensi')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($dimensi['created_at']) ? substr($dimensi['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/dimensi')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[dimensi.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getDimensiModel('default')->update($id, $dataToUpdate);
        try {
            $this->getDimensiModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (dimensi update) failed: ' . $e->getMessage());
        }
        // Update kode_ky setelah update data
        $this->getDimensiModel('default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getDimensiModel('db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (dimensi kode_ky update) failed: ' . $e->getMessage());
        }
        $this->getDimensiModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getDimensiModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (dimensi otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/dimensi')->with('success', 'Data dimensi berhasil diperbarui.');
    }

    public function delete($id)
    {
        $dimensi = $this->getDimensiModel('default')->find($id);
        if (empty($dimensi) || !isset($dimensi['otoritas']) || $dimensi['otoritas'] !== 'T') {
            return redirect()->to('/dimensi')->with('error', 'Akses hapus dimensi ini membutuhkan otoritas.');
        }
        $mode = $dimensi['mode_batas_tanggal'] ?? 'manual';
        $batas = $dimensi['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($dimensi['created_at']) ? substr($dimensi['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/dimensi')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($dimensi['created_at']) ? substr($dimensi['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/dimensi')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getDimensiModel('default')->delete($id);
        try {
            $this->getDimensiModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (dimensi delete) failed: ' . $e->getMessage());
        }
        // Update kode_ky setelah soft delete
        $this->getDimensiModel('default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getDimensiModel('db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (dimensi kode_ky update after delete) failed: ' . $e->getMessage());
        }
        $this->getDimensiModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getDimensiModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (dimensi otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/dimensi')->with('success', 'Data dimensi berhasil dihapus.');
    }
}
