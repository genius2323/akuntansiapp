<?php

namespace App\Controllers;

use App\Models\SatuanModel;

class SatuanController extends BaseController
{
    /**
     * Menghubungkan model ke database yang benar.
     * @param string $dbGroup Nama grup database ('default' atau 'db1')
     * @return SatuanModel
     */
    private function getSatuanModel(string $dbGroup = 'default'): SatuanModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new SatuanModel($db);
    }

    /**
     * Menampilkan halaman daftar satuan.
     */
    public function index()
    {
        // Data lengkap untuk tombol-tombol klasifikasi
        $classifications = [
            ['name' => 'Kategori', 'url' => 'categories', 'icon' => 'fas fa-tags', 'active' => false],
            ['name' => 'Satuan', 'url' => 'satuan', 'icon' => 'fas fa-ruler-combined', 'active' => true],
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
            ['name' => 'Fiting', 'url' => 'fiting', 'icon' => 'fas fa-plug', 'active' => false],
            ['name' => 'Daya', 'url' => 'daya', 'icon' => 'fas fa-power-off', 'active' => false],
            ['name' => 'Jumlah Mata', 'url' => 'jumlah-mata', 'icon' => 'fas fa-eye', 'active' => false],
        ];

        $data = [
            'title'           => 'Master Klasifikasi - Satuan',
            'classifications' => $classifications,
            'satuans'         => $this->getSatuanModel('default')->findAll()
        ];
        return view('satuan/index', $data);
    }

    /**
     * Menampilkan halaman form untuk mengedit satuan.
     */
    public function edit($id)
    {
        $satuan = $this->getSatuanModel('default')->find($id);
        if (empty($satuan)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data satuan tidak ditemukan.');
        }
        // Cek otoritas
        if (!isset($satuan['otoritas']) || $satuan['otoritas'] !== 'T') {
            return redirect()->to('/satuan')->with('error', 'Akses edit satuan ini membutuhkan otoritas dari departemen yang berwenang.');
        }
        // Validasi batas tanggal
        $mode = $satuan['mode_batas_tanggal'] ?? 'manual';
        $batas = $satuan['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($satuan['created_at']) ? substr($satuan['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/satuan')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($satuan['created_at']) ? substr($satuan['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/satuan')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title'   => 'Edit Satuan',
            'satuan'  => $satuan
        ];
        return view('satuan/edit', $data);
    }

    /**
     * Menyimpan data satuan baru ke kedua database.
     */
    public function create()
    {
        // Log debug dihapus setelah verifikasi sukses

        $rules = ['name' => 'required|min_length[2]|is_unique[satuan.name]'];

        if (!$this->validate($rules)) {
            return redirect()->to('/satuan')->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'kode_ky'     => session('kode_ky'),
        ];

        $mainModel = $this->getSatuanModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;

            $backupModel = $this->getSatuanModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (satuan) failed: ' . $e->getMessage());
                return redirect()->to('/satuan')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/satuan')->with('error', 'Gagal menyimpan data utama.');
        }

        return redirect()->to('/satuan')->with('success', 'Data satuan berhasil ditambahkan.');
    }

    /**
     * Memperbarui data satuan di kedua database.
     */
    public function update($id)
    {
        $satuan = $this->getSatuanModel('default')->find($id);
        if (empty($satuan) || !isset($satuan['otoritas']) || $satuan['otoritas'] !== 'T') {
            return redirect()->to('/satuan')->with('error', 'Akses update satuan ini membutuhkan otoritas dari departemen yang berwenang.');
        }
        // Validasi batas tanggal
        $mode = $satuan['mode_batas_tanggal'] ?? 'manual';
        $batas = $satuan['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($satuan['created_at']) ? substr($satuan['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/satuan')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($satuan['created_at']) ? substr($satuan['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/satuan')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[satuan.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        // 1. Update database utama
        $this->getSatuanModel('default')->update($id, $dataToUpdate);
        // 2. Update database backup
        try {
            $this->getSatuanModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (satuan update) failed: ' . $e->getMessage());
        }
        // Update kode_ky setelah update data
        $this->getSatuanModel('default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getSatuanModel('db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (satuan kode_ky update) failed: ' . $e->getMessage());
        }
        // Kosongkan kolom otoritas setelah update
        $this->getSatuanModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getSatuanModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (satuan otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/satuan')->with('success', 'Data satuan berhasil diperbarui.');
    }

    /**
     * Menghapus (soft delete) data satuan dari kedua database.
     * Soft delete berarti data tidak benar-benar hilang, hanya diberi penanda 'deleted_at'.
     * Model secara otomatis tidak akan menampilkan data yang sudah di-soft-delete.
     */
    public function delete($id)
    {
        $satuan = $this->getSatuanModel('default')->find($id);
        if (empty($satuan) || !isset($satuan['otoritas']) || $satuan['otoritas'] !== 'T') {
            return redirect()->to('/satuan')->with('error', 'Akses hapus satuan ini membutuhkan otoritas dari departemen yang berwenang.');
        }
        // Validasi batas tanggal
        $mode = $satuan['mode_batas_tanggal'] ?? 'manual';
        $batas = $satuan['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($satuan['created_at']) ? substr($satuan['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/satuan')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($satuan['created_at']) ? substr($satuan['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/satuan')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        // 1. Soft delete dari database utama
        $this->getSatuanModel('default')->delete($id);
        // 2. Soft delete dari database backup
        try {
            $this->getSatuanModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (satuan delete) failed: ' . $e->getMessage());
        }
        // Update kode_ky setelah soft delete
        $this->getSatuanModel('default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getSatuanModel('db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (satuan kode_ky update after delete) failed: ' . $e->getMessage());
        }
        // Kosongkan kolom otoritas setelah delete (jika soft delete, update data; jika hard delete, ini bisa di-skip)
        $this->getSatuanModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getSatuanModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (satuan otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/satuan')->with('success', 'Data satuan berhasil dihapus.');
    }
}
