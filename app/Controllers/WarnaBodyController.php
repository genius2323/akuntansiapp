<?php
namespace App\Controllers;
use App\Models\WarnaBodyModel;
class WarnaBodyController extends BaseController
{
    private function getWarnaBodyModel(string $dbGroup = 'default'): WarnaBodyModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new WarnaBodyModel($db);
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
            ['name' => 'Warna Body', 'url' => 'warna-body', 'icon' => 'fas fa-palette', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Warna Body',
            'classifications' => $classifications,
            'warnabodys' => $this->getWarnaBodyModel('default')->findAll()
        ];
        return view('warnabody/index', $data);
    }

    public function edit($id)
    {
        $warnabody = $this->getWarnaBodyModel('default')->find($id);
        if (empty($warnabody)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data warna body tidak ditemukan.');
        }
        if (!isset($warnabody['otoritas']) || $warnabody['otoritas'] !== 'T') {
            return redirect()->to('/warna-body')->with('error', 'Akses edit warna body ini membutuhkan otoritas.');
        }
        $mode = $warnabody['mode_batas_tanggal'] ?? 'manual';
        $batas = $warnabody['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($warnabody['created_at']) ? substr($warnabody['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/warna-body')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($warnabody['created_at']) ? substr($warnabody['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/warna-body')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Warna Body',
            'warnabody' => $warnabody
        ];
        return view('warnabody/edit', $data);
    }

    public function create()
    {
        $rules = ['name' => 'required|min_length[2]|is_unique[warna_body.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/warna-body')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $mainModel = $this->getWarnaBodyModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getWarnaBodyModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (warna_body) failed: ' . $e->getMessage());
                return redirect()->to('/warna-body')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/warna-body')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/warna-body')->with('success', 'Data warna body berhasil ditambahkan.');
    }

    public function update($id)
    {
        $warnabody = $this->getWarnaBodyModel('default')->find($id);
        if (empty($warnabody) || !isset($warnabody['otoritas']) || $warnabody['otoritas'] !== 'T') {
            return redirect()->to('/warna-body')->with('error', 'Akses update warna body ini membutuhkan otoritas.');
        }
        $mode = $warnabody['mode_batas_tanggal'] ?? 'manual';
        $batas = $warnabody['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($warnabody['created_at']) ? substr($warnabody['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/warna-body')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($warnabody['created_at']) ? substr($warnabody['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/warna-body')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[warna_body.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getWarnaBodyModel('default')->update($id, $dataToUpdate);
        try {
            $this->getWarnaBodyModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_body update) failed: ' . $e->getMessage());
        }
        $this->getWarnaBodyModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getWarnaBodyModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_body otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/warna-body')->with('success', 'Data warna body berhasil diperbarui.');
    }

    public function delete($id)
    {
        $warnabody = $this->getWarnaBodyModel('default')->find($id);
        if (empty($warnabody) || !isset($warnabody['otoritas']) || $warnabody['otoritas'] !== 'T') {
            return redirect()->to('/warna-body')->with('error', 'Akses hapus warna body ini membutuhkan otoritas.');
        }
        $mode = $warnabody['mode_batas_tanggal'] ?? 'manual';
        $batas = $warnabody['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($warnabody['created_at']) ? substr($warnabody['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/warna-body')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($warnabody['created_at']) ? substr($warnabody['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/warna-body')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getWarnaBodyModel('default')->delete($id);
        try {
            $this->getWarnaBodyModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_body delete) failed: ' . $e->getMessage());
        }
        $this->getWarnaBodyModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getWarnaBodyModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_body otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/warna-body')->with('success', 'Data warna body berhasil dihapus.');
    }
}
