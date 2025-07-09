<?php
namespace App\Controllers;
use App\Models\GondolaModel;
class GondolaController extends BaseController
{
    private function getGondolaModel(string $dbGroup = 'default'): GondolaModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new GondolaModel($db);
    }
    public function index()
    {
        $classifications = [
            ['name' => 'Kategori', 'url' => 'categories', 'icon' => 'fas fa-tags', 'active' => false],
            ['name' => 'Satuan', 'url' => 'satuan', 'icon' => 'fas fa-ruler-combined', 'active' => false],
            ['name' => 'Jenis', 'url' => 'jenis', 'icon' => 'fas fa-boxes', 'active' => false],
            ['name' => 'Pelengkap', 'url' => 'pelengkap', 'icon' => 'fas fa-puzzle-piece', 'active' => false],
            ['name' => 'Gondola', 'url' => 'gondola', 'icon' => 'fas fa-store-alt', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Gondola',
            'classifications' => $classifications,
            'gondolas' => $this->getGondolaModel('default')->findAll()
        ];
        return view('gondola/index', $data);
    }
    /**
     * Menampilkan form edit gondola
     */
    public function edit($id)
    {
        $gondola = $this->getGondolaModel('default')->find($id);
        if (empty($gondola)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data gondola tidak ditemukan.');
        }
        if (!isset($gondola['otoritas']) || $gondola['otoritas'] !== 'T') {
            return redirect()->to('/gondola')->with('error', 'Akses edit gondola ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $gondola['mode_batas_tanggal'] ?? 'manual';
        $batas = $gondola['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($gondola['created_at']) ? substr($gondola['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/gondola')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($gondola['created_at']) ? substr($gondola['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/gondola')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Gondola',
            'gondola' => $gondola
        ];
        return view('gondola/edit', $data);
    }

    /**
     * Menyimpan data gondola baru ke kedua database.
     */
    public function create()
    {
        $rules = ['name' => 'required|min_length[2]|is_unique[gondola.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/gondola')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $mainModel = $this->getGondolaModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getGondolaModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (gondola) failed: ' . $e->getMessage());
                return redirect()->to('/gondola')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/gondola')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/gondola')->with('success', 'Data gondola berhasil ditambahkan.');
    }

    /**
     * Memperbarui data gondola di kedua database.
     */
    public function update($id)
    {
        $gondola = $this->getGondolaModel('default')->find($id);
        if (empty($gondola) || !isset($gondola['otoritas']) || $gondola['otoritas'] !== 'T') {
            return redirect()->to('/gondola')->with('error', 'Akses update gondola ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $gondola['mode_batas_tanggal'] ?? 'manual';
        $batas = $gondola['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($gondola['created_at']) ? substr($gondola['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/gondola')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($gondola['created_at']) ? substr($gondola['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/gondola')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[gondola.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getGondolaModel('default')->update($id, $dataToUpdate);
        try {
            $this->getGondolaModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (gondola update) failed: ' . $e->getMessage());
        }
        $this->getGondolaModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getGondolaModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (gondola otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/gondola')->with('success', 'Data gondola berhasil diperbarui.');
    }

    /**
     * Menghapus (soft delete) data gondola dari kedua database.
     */
    public function delete($id)
    {
        $gondola = $this->getGondolaModel('default')->find($id);
        if (empty($gondola) || !isset($gondola['otoritas']) || $gondola['otoritas'] !== 'T') {
            return redirect()->to('/gondola')->with('error', 'Akses hapus gondola ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $gondola['mode_batas_tanggal'] ?? 'manual';
        $batas = $gondola['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($gondola['created_at']) ? substr($gondola['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/gondola')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($gondola['created_at']) ? substr($gondola['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/gondola')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getGondolaModel('default')->delete($id);
        try {
            $this->getGondolaModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (gondola delete) failed: ' . $e->getMessage());
        }
        $this->getGondolaModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getGondolaModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (gondola otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/gondola')->with('success', 'Data gondola berhasil dihapus.');
    }
}
