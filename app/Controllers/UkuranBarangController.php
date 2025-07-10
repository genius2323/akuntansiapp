<?php
namespace App\Controllers;
use App\Models\UkuranBarangModel;
class UkuranBarangController extends BaseController
{
    private function getUkuranBarangModel(string $dbGroup = 'default'): UkuranBarangModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new UkuranBarangModel($db);
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
            ['name' => 'Ukuran Barang', 'url' => 'ukuran-barang', 'icon' => 'fas fa-expand-arrows-alt', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Ukuran Barang',
            'classifications' => $classifications,
            'ukuranbarangs' => $this->getUkuranBarangModel('default')->findAll()
        ];
        return view('ukuranbarang/index', $data);
    }

    public function edit($id)
    {
        $ukuranbarang = $this->getUkuranBarangModel('default')->find($id);
        if (empty($ukuranbarang)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data ukuran barang tidak ditemukan.');
        }
        if (!isset($ukuranbarang['otoritas']) || $ukuranbarang['otoritas'] !== 'T') {
            return redirect()->to('/ukuran-barang')->with('error', 'Akses edit ukuran barang ini membutuhkan otoritas.');
        }
        $mode = $ukuranbarang['mode_batas_tanggal'] ?? 'manual';
        $batas = $ukuranbarang['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($ukuranbarang['created_at']) ? substr($ukuranbarang['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/ukuran-barang')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($ukuranbarang['created_at']) ? substr($ukuranbarang['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/ukuran-barang')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Ukuran Barang',
            'ukuranbarang' => $ukuranbarang
        ];
        return view('ukuranbarang/edit', $data);
    }

    public function create()
    {
        // Log debug dihapus setelah verifikasi sukses
        $rules = ['name' => 'required|min_length[2]|is_unique[ukuran_barang.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/ukuran-barang')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'kode_ky'     => session('kode_ky'),
        ];
        $mainModel = $this->getUkuranBarangModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getUkuranBarangModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (ukuran_barang) failed: ' . $e->getMessage());
                return redirect()->to('/ukuran-barang')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/ukuran-barang')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/ukuran-barang')->with('success', 'Data ukuran barang berhasil ditambahkan.');
    }

    public function update($id)
    {
        $ukuranbarang = $this->getUkuranBarangModel('default')->find($id);
        if (empty($ukuranbarang) || !isset($ukuranbarang['otoritas']) || $ukuranbarang['otoritas'] !== 'T') {
            return redirect()->to('/ukuran-barang')->with('error', 'Akses update ukuran barang ini membutuhkan otoritas.');
        }
        $mode = $ukuranbarang['mode_batas_tanggal'] ?? 'manual';
        $batas = $ukuranbarang['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($ukuranbarang['created_at']) ? substr($ukuranbarang['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/ukuran-barang')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($ukuranbarang['created_at']) ? substr($ukuranbarang['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/ukuran-barang')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[ukuran_barang.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getUkuranBarangModel('default')->update($id, $dataToUpdate);
        try {
            $this->getUkuranBarangModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (ukuran_barang update) failed: ' . $e->getMessage());
        }
        // Update kode_ky setelah update data
        $this->getUkuranBarangModel('default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getUkuranBarangModel('db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (ukuran_barang kode_ky update) failed: ' . $e->getMessage());
        }
        $this->getUkuranBarangModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getUkuranBarangModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (ukuran_barang otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/ukuran-barang')->with('success', 'Data ukuran barang berhasil diperbarui.');
    }

    public function delete($id)
    {
        $ukuranbarang = $this->getUkuranBarangModel('default')->find($id);
        if (empty($ukuranbarang) || !isset($ukuranbarang['otoritas']) || $ukuranbarang['otoritas'] !== 'T') {
            return redirect()->to('/ukuran-barang')->with('error', 'Akses hapus ukuran barang ini membutuhkan otoritas.');
        }
        $mode = $ukuranbarang['mode_batas_tanggal'] ?? 'manual';
        $batas = $ukuranbarang['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($ukuranbarang['created_at']) ? substr($ukuranbarang['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/ukuran-barang')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($ukuranbarang['created_at']) ? substr($ukuranbarang['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/ukuran-barang')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getUkuranBarangModel('default')->delete($id);
        try {
            $this->getUkuranBarangModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (ukuran_barang delete) failed: ' . $e->getMessage());
        }
        // Update kode_ky setelah soft delete
        $this->getUkuranBarangModel('default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getUkuranBarangModel('db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (ukuran_barang kode_ky update after delete) failed: ' . $e->getMessage());
        }
        $this->getUkuranBarangModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getUkuranBarangModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (ukuran_barang otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/ukuran-barang')->with('success', 'Data ukuran barang berhasil dihapus.');
    }
}
