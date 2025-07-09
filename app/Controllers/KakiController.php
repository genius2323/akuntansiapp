<?php
namespace App\Controllers;
use App\Models\KakiModel;
class KakiController extends BaseController
{
    private function getKakiModel(string $dbGroup = 'default'): KakiModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new KakiModel($db);
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
            ['name' => 'Kaki', 'url' => 'kaki', 'icon' => 'fas fa-shoe-prints', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Kaki',
            'classifications' => $classifications,
            'kakis' => $this->getKakiModel('default')->findAll()
        ];
        return view('kaki/index', $data);
    }

    public function edit($id)
    {
        $kaki = $this->getKakiModel('default')->find($id);
        if (empty($kaki)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data kaki tidak ditemukan.');
        }
        if (!isset($kaki['otoritas']) || $kaki['otoritas'] !== 'T') {
            return redirect()->to('/kaki')->with('error', 'Akses edit kaki ini membutuhkan otoritas.');
        }
        $mode = $kaki['mode_batas_tanggal'] ?? 'manual';
        $batas = $kaki['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($kaki['created_at']) ? substr($kaki['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/kaki')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($kaki['created_at']) ? substr($kaki['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/kaki')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Kaki',
            'kaki' => $kaki
        ];
        return view('kaki/edit', $data);
    }

    public function create()
    {
        $rules = ['name' => 'required|min_length[2]|is_unique[kaki.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/kaki')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $mainModel = $this->getKakiModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getKakiModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (kaki) failed: ' . $e->getMessage());
                return redirect()->to('/kaki')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/kaki')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/kaki')->with('success', 'Data kaki berhasil ditambahkan.');
    }

    public function update($id)
    {
        $kaki = $this->getKakiModel('default')->find($id);
        if (empty($kaki) || !isset($kaki['otoritas']) || $kaki['otoritas'] !== 'T') {
            return redirect()->to('/kaki')->with('error', 'Akses update kaki ini membutuhkan otoritas.');
        }
        $mode = $kaki['mode_batas_tanggal'] ?? 'manual';
        $batas = $kaki['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($kaki['created_at']) ? substr($kaki['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/kaki')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($kaki['created_at']) ? substr($kaki['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/kaki')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[kaki.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getKakiModel('default')->update($id, $dataToUpdate);
        try {
            $this->getKakiModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (kaki update) failed: ' . $e->getMessage());
        }
        $this->getKakiModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getKakiModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (kaki otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/kaki')->with('success', 'Data kaki berhasil diperbarui.');
    }

    public function delete($id)
    {
        $kaki = $this->getKakiModel('default')->find($id);
        if (empty($kaki) || !isset($kaki['otoritas']) || $kaki['otoritas'] !== 'T') {
            return redirect()->to('/kaki')->with('error', 'Akses hapus kaki ini membutuhkan otoritas.');
        }
        $mode = $kaki['mode_batas_tanggal'] ?? 'manual';
        $batas = $kaki['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($kaki['created_at']) ? substr($kaki['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/kaki')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($kaki['created_at']) ? substr($kaki['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/kaki')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getKakiModel('default')->delete($id);
        try {
            $this->getKakiModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (kaki delete) failed: ' . $e->getMessage());
        }
        $this->getKakiModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getKakiModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (kaki otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/kaki')->with('success', 'Data kaki berhasil dihapus.');
    }
}
