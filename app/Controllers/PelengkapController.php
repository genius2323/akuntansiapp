<?php
namespace App\Controllers;

use App\Models\PelengkapModel;

class PelengkapController extends BaseController
{
    private function getPelengkapModel(string $dbGroup = 'default'): PelengkapModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new PelengkapModel($db);
    }

    public function index()
    {
        $classifications = [
            ['name' => 'Kategori', 'url' => 'categories', 'icon' => 'fas fa-tags', 'active' => false],
            ['name' => 'Satuan', 'url' => 'satuan', 'icon' => 'fas fa-ruler-combined', 'active' => false],
            ['name' => 'Jenis', 'url' => 'jenis', 'icon' => 'fas fa-boxes', 'active' => false],
            ['name' => 'Pelengkap', 'url' => 'pelengkap', 'icon' => 'fas fa-puzzle-piece', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Pelengkap',
            'classifications' => $classifications,
            'pelengkaps' => $this->getPelengkapModel('default')->findAll()
        ];
        return view('pelengkap/index', $data);
    }
    /**
     * Menampilkan form edit pelengkap
     */
    public function edit($id)
    {
        $pelengkap = $this->getPelengkapModel('default')->find($id);
        if (empty($pelengkap)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data pelengkap tidak ditemukan.');
        }
        if (!isset($pelengkap['otoritas']) || $pelengkap['otoritas'] !== 'T') {
            return redirect()->to('/pelengkap')->with('error', 'Akses edit pelengkap ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $pelengkap['mode_batas_tanggal'] ?? 'manual';
        $batas = $pelengkap['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($pelengkap['created_at']) ? substr($pelengkap['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/pelengkap')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($pelengkap['created_at']) ? substr($pelengkap['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/pelengkap')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Pelengkap',
            'pelengkap' => $pelengkap
        ];
        return view('pelengkap/edit', $data);
    }

    /**
     * Menyimpan data pelengkap baru ke kedua database.
     */
    public function create()
    {
        $rules = ['name' => 'required|min_length[2]|is_unique[pelengkap.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/pelengkap')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $mainModel = $this->getPelengkapModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getPelengkapModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (pelengkap) failed: ' . $e->getMessage());
                return redirect()->to('/pelengkap')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/pelengkap')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/pelengkap')->with('success', 'Data pelengkap berhasil ditambahkan.');
    }

    /**
     * Memperbarui data pelengkap di kedua database.
     */
    public function update($id)
    {
        $pelengkap = $this->getPelengkapModel('default')->find($id);
        if (empty($pelengkap) || !isset($pelengkap['otoritas']) || $pelengkap['otoritas'] !== 'T') {
            return redirect()->to('/pelengkap')->with('error', 'Akses update pelengkap ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $pelengkap['mode_batas_tanggal'] ?? 'manual';
        $batas = $pelengkap['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($pelengkap['created_at']) ? substr($pelengkap['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/pelengkap')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($pelengkap['created_at']) ? substr($pelengkap['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/pelengkap')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[pelengkap.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getPelengkapModel('default')->update($id, $dataToUpdate);
        try {
            $this->getPelengkapModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (pelengkap update) failed: ' . $e->getMessage());
        }
        $this->getPelengkapModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getPelengkapModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (pelengkap otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/pelengkap')->with('success', 'Data pelengkap berhasil diperbarui.');
    }

    /**
     * Menghapus (soft delete) data pelengkap dari kedua database.
     */
    public function delete($id)
    {
        $pelengkap = $this->getPelengkapModel('default')->find($id);
        if (empty($pelengkap) || !isset($pelengkap['otoritas']) || $pelengkap['otoritas'] !== 'T') {
            return redirect()->to('/pelengkap')->with('error', 'Akses hapus pelengkap ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $pelengkap['mode_batas_tanggal'] ?? 'manual';
        $batas = $pelengkap['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($pelengkap['created_at']) ? substr($pelengkap['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/pelengkap')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($pelengkap['created_at']) ? substr($pelengkap['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/pelengkap')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getPelengkapModel('default')->delete($id);
        try {
            $this->getPelengkapModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (pelengkap delete) failed: ' . $e->getMessage());
        }
        $this->getPelengkapModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getPelengkapModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (pelengkap otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/pelengkap')->with('success', 'Data pelengkap berhasil dihapus.');
    }
}
