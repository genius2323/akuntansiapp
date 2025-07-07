<?php namespace App\Controllers;

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

    public function index()
    {
        // Data untuk tombol-tombol klasifikasi
        $classifications = [
            ['name' => 'Satuan', 'url' => 'satuan', 'icon' => 'fas fa-ruler-combined', 'active' => true],
            ['name' => 'Kategori', 'url' => 'categories', 'icon' => 'fas fa-tags', 'active' => false],
            ['name' => 'Jenis', 'url' => 'jenis', 'icon' => 'fas fa-boxes', 'active' => false],
            ['name' => 'Merk', 'url' => 'merk', 'icon' => 'fas fa-copyright', 'active' => false],
            // ... tambahkan klasifikasi lain di sini
        ];

        $data = [
            'title'           => 'Master Klasifikasi - Satuan',
            'classifications' => $classifications,
            'satuans'         => $this->getSatuanModel('default')->findAll()
        ];
        return view('satuan/index', $data);
    }

    public function edit($id)
    {
        $data = [
            'title'   => 'Edit Satuan',
            'satuan'  => $this->getSatuanModel('default')->find($id)
        ];

        if (empty($data['satuan'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data satuan tidak ditemukan.');
        }

        return view('satuan/edit', $data);
    }

    public function create()
    {
        $rules = ['name' => 'required|min_length[3]|is_unique[satuan.name]'];

        if (!$this->validate($rules)) {
            return redirect()->to('/satuan')->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
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

    public function update($id)
    {
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];

        $this->getSatuanModel('default')->update($id, $dataToUpdate);
        
        try {
            $this->getSatuanModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (satuan update) failed: ' . $e->getMessage());
        }

        return redirect()->to('/satuan')->with('success', 'Data satuan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->getSatuanModel('default')->delete($id);
        
        try {
            $this->getSatuanModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (satuan delete) failed: ' . $e->getMessage());
        }
        
        return redirect()->to('/satuan')->with('success', 'Data satuan berhasil dihapus.');
    }
}
