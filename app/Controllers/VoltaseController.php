<?php
namespace App\Controllers;
use App\Models\VoltaseModel;
class VoltaseController extends BaseController
{
    private function getVoltaseModel(string $dbGroup = 'default'): VoltaseModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new VoltaseModel($db);
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
            ['name' => 'Voltase', 'url' => 'voltase', 'icon' => 'fas fa-bolt', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Voltase',
            'classifications' => $classifications,
            'voltases' => $this->getVoltaseModel('default')->findAll()
        ];
        return view('voltase/index', $data);
    }

    public function edit($id)
    {
        $voltase = $this->getVoltaseModel('default')->find($id);
        if (empty($voltase)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data voltase tidak ditemukan.');
        }
        if (!isset($voltase['otoritas']) || $voltase['otoritas'] !== 'T') {
            return redirect()->to('/voltase')->with('error', 'Akses edit voltase ini membutuhkan otoritas.');
        }
        $mode = $voltase['mode_batas_tanggal'] ?? 'manual';
        $batas = $voltase['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($voltase['created_at']) ? substr($voltase['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/voltase')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($voltase['created_at']) ? substr($voltase['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/voltase')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Voltase',
            'voltase' => $voltase
        ];
        return view('voltase/edit', $data);
    }

    public function create()
    {
        $rules = ['name' => 'required|min_length[2]|is_unique[voltase.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/voltase')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $mainModel = $this->getVoltaseModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getVoltaseModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (voltase) failed: ' . $e->getMessage());
                return redirect()->to('/voltase')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/voltase')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/voltase')->with('success', 'Data voltase berhasil ditambahkan.');
    }

    public function update($id)
    {
        $voltase = $this->getVoltaseModel('default')->find($id);
        if (empty($voltase) || !isset($voltase['otoritas']) || $voltase['otoritas'] !== 'T') {
            return redirect()->to('/voltase')->with('error', 'Akses update voltase ini membutuhkan otoritas.');
        }
        $mode = $voltase['mode_batas_tanggal'] ?? 'manual';
        $batas = $voltase['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($voltase['created_at']) ? substr($voltase['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/voltase')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($voltase['created_at']) ? substr($voltase['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/voltase')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[voltase.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getVoltaseModel('default')->update($id, $dataToUpdate);
        try {
            $this->getVoltaseModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (voltase update) failed: ' . $e->getMessage());
        }
        $this->getVoltaseModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getVoltaseModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (voltase otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/voltase')->with('success', 'Data voltase berhasil diperbarui.');
    }

    public function delete($id)
    {
        $voltase = $this->getVoltaseModel('default')->find($id);
        if (empty($voltase) || !isset($voltase['otoritas']) || $voltase['otoritas'] !== 'T') {
            return redirect()->to('/voltase')->with('error', 'Akses hapus voltase ini membutuhkan otoritas.');
        }
        $mode = $voltase['mode_batas_tanggal'] ?? 'manual';
        $batas = $voltase['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($voltase['created_at']) ? substr($voltase['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/voltase')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($voltase['created_at']) ? substr($voltase['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/voltase')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getVoltaseModel('default')->delete($id);
        try {
            $this->getVoltaseModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (voltase delete) failed: ' . $e->getMessage());
        }
        $this->getVoltaseModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getVoltaseModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (voltase otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/voltase')->with('success', 'Data voltase berhasil dihapus.');
    }
}
