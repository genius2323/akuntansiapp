<?php
namespace App\Controllers;
use App\Models\MerkModel;
class MerkController extends BaseController
{
    private function getMerkModel(string $dbGroup = 'default'): MerkModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new MerkModel($db);
    }
    public function index()
    {
        $classifications = [
            ['name' => 'Kategori', 'url' => 'categories', 'icon' => 'fas fa-tags', 'active' => false],
            ['name' => 'Satuan', 'url' => 'satuan', 'icon' => 'fas fa-ruler-combined', 'active' => false],
            ['name' => 'Jenis', 'url' => 'jenis', 'icon' => 'fas fa-boxes', 'active' => false],
            ['name' => 'Pelengkap', 'url' => 'pelengkap', 'icon' => 'fas fa-puzzle-piece', 'active' => false],
            ['name' => 'Gondola', 'url' => 'gondola', 'icon' => 'fas fa-store-alt', 'active' => false],
            ['name' => 'Merk Barang', 'url' => 'merk', 'icon' => 'fas fa-copyright', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Merk Barang',
            'classifications' => $classifications,
            'merks' => $this->getMerkModel('default')->findAll()
        ];
        return view('merk/index', $data);
    }
    /**
     * Menampilkan form edit merk
     */
    public function edit($id)
    {
        $merk = $this->getMerkModel('default')->find($id);
        if (empty($merk)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data merk tidak ditemukan.');
        }
        if (!isset($merk['otoritas']) || $merk['otoritas'] !== 'T') {
            return redirect()->to('/merk')->with('error', 'Akses edit merk ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $merk['mode_batas_tanggal'] ?? 'manual';
        $batas = $merk['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($merk['created_at']) ? substr($merk['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/merk')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($merk['created_at']) ? substr($merk['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/merk')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Merk',
            'merk' => $merk
        ];
        return view('merk/edit', $data);
    }

    /**
     * Menyimpan data merk baru ke kedua database.
     */
    public function create()
    {
        // Log debug dihapus setelah verifikasi sukses
        $rules = ['name' => 'required|min_length[2]|is_unique[merk.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/merk')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'kode_ky'     => session('kode_ky'),
        ];
        $mainModel = $this->getMerkModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getMerkModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (merk) failed: ' . $e->getMessage());
                return redirect()->to('/merk')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/merk')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/merk')->with('success', 'Data merk berhasil ditambahkan.');
    }

    /**
     * Memperbarui data merk di kedua database.
     */
    public function update($id)
    {
        $merk = $this->getMerkModel('default')->find($id);
        if (empty($merk) || !isset($merk['otoritas']) || $merk['otoritas'] !== 'T') {
            return redirect()->to('/merk')->with('error', 'Akses update merk ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $merk['mode_batas_tanggal'] ?? 'manual';
        $batas = $merk['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($merk['created_at']) ? substr($merk['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/merk')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($merk['created_at']) ? substr($merk['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/merk')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[merk.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getMerkModel('default')->update($id, $dataToUpdate);
        try {
            $this->getMerkModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (merk update) failed: ' . $e->getMessage());
        }
        $this->getMerkModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getMerkModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (merk otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/merk')->with('success', 'Data merk berhasil diperbarui.');
    }

    /**
     * Menghapus (soft delete) data merk dari kedua database.
     */
    public function delete($id)
    {
        $merk = $this->getMerkModel('default')->find($id);
        if (empty($merk) || !isset($merk['otoritas']) || $merk['otoritas'] !== 'T') {
            return redirect()->to('/merk')->with('error', 'Akses hapus merk ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $merk['mode_batas_tanggal'] ?? 'manual';
        $batas = $merk['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($merk['created_at']) ? substr($merk['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/merk')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($merk['created_at']) ? substr($merk['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/merk')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getMerkModel('default')->delete($id);
        try {
            $this->getMerkModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (merk delete) failed: ' . $e->getMessage());
        }
        $this->getMerkModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getMerkModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (merk otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/merk')->with('success', 'Data merk berhasil dihapus.');
    }
}
