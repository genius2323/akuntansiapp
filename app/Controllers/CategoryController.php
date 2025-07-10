<?php

namespace App\Controllers;

use App\Models\CategoryModel;

class CategoryController extends BaseController
{
    private function getCategoryModel(string $dbGroup = 'default'): CategoryModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new CategoryModel($db);
    }

    public function index()
    {
        // Data lengkap untuk tombol-tombol klasifikasi
        $classifications = [
            ['name' => 'Kategori', 'url' => 'categories', 'icon' => 'fas fa-tags', 'active' => true],
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
            ['name' => 'Fiting', 'url' => 'fiting', 'icon' => 'fas fa-plug', 'active' => false],
            ['name' => 'Daya', 'url' => 'daya', 'icon' => 'fas fa-power-off', 'active' => false],
            ['name' => 'Jumlah Mata', 'url' => 'jumlah-mata', 'icon' => 'fas fa-eye', 'active' => false],
        ];

        $data = [
            'title'           => 'Master Klasifikasi - Kategori',
            'classifications' => $classifications,
            'categories'      => $this->getCategoryModel('default')->select('id, kode_cat, name, description, otoritas')->findAll()
        ];
        return view('categories/index', $data);
    }

    public function edit($id)
    {
        $category = $this->getCategoryModel('default')->find($id);
        if (empty($category)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kategori tidak ditemukan.');
        }
        // Cek otoritas
        if ($category['otoritas'] !== 'T') {
            return redirect()->to('/categories')->with('error', 'Akses edit kategori ini membutuhkan otoritas dari departemen yang berwenang.');
        }
        // Validasi batas tanggal
        $mode = $category['mode_batas_tanggal'] ?? 'manual';
        $batas = $category['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($category['created_at']) ? substr($category['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/categories')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($category['created_at']) ? substr($category['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/categories')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title'    => 'Edit Kategori',
            'category' => $category
        ];
        return view('categories/edit', $data);
    }

    public function create()
    {

        $rules = [
            'kode_cat' => 'required|alpha_numeric|max_length[4]',
            'name' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/categories')->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToSave = [
            'kode_cat'    => $this->request->getPost('kode_cat'),
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];

        $mainModel = $this->getCategoryModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;

            $backupModel = $this->getCategoryModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (category) failed: ' . $e->getMessage());
                return redirect()->to('/categories')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/categories')->with('error', 'Gagal menyimpan data utama kategori.');
        }

        return redirect()->to('/categories')->with('success', 'Kategori berhasil ditambahkan di kedua database.');
    }

    public function update($id)
    {
        $category = $this->getCategoryModel('default')->find($id);
        if (empty($category) || $category['otoritas'] !== 'T') {
            return redirect()->to('/categories')->with('error', 'Akses update kategori ini membutuhkan otoritas dari departemen yang berwenang.');
        }
        // Validasi batas tanggal
        $mode = $category['mode_batas_tanggal'] ?? 'manual';
        $batas = $category['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($category['created_at']) ? substr($category['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/categories')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($category['created_at']) ? substr($category['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/categories')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }

        $rules = [
            'kode_cat' => 'required|alpha_numeric|max_length[4]',
            'name' => 'required|min_length[3]'
        ];
        $dataToUpdate = [
            'kode_cat'    => $this->request->getPost('kode_cat'),
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/categories/' . $id . '/edit')->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update data
        $this->getCategoryModel('default')->update($id, $dataToUpdate);
        try {
            $this->getCategoryModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (category update) failed: ' . $e->getMessage());
        }

        // Kosongkan kolom otoritas setelah update
        $this->getCategoryModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getCategoryModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (category otoritas clear) failed: ' . $e->getMessage());
        }

        return redirect()->to('/categories')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete($id)
    {
        $category = $this->getCategoryModel('default')->find($id);
        if (empty($category) || $category['otoritas'] !== 'T') {
            return redirect()->to('/categories')->with('error', 'Akses hapus kategori ini membutuhkan otoritas dari departemen yang berwenang.');
        }
        // Validasi batas tanggal
        $mode = $category['mode_batas_tanggal'] ?? 'manual';
        $batas = $category['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($category['created_at']) ? substr($category['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/categories')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($category['created_at']) ? substr($category['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/categories')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getCategoryModel('default')->delete($id);
        try {
            $this->getCategoryModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (category delete) failed: ' . $e->getMessage());
        }

        // Kosongkan kolom otoritas setelah delete (jika soft delete, update data; jika hard delete, ini bisa di-skip)
        $this->getCategoryModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getCategoryModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (category otoritas clear after delete) failed: ' . $e->getMessage());
        }

        return redirect()->to('/categories')->with('success', 'Kategori berhasil dihapus dari kedua database.');
    }
}
