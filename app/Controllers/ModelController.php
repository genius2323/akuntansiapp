<?php
namespace App\Controllers;
use App\Models\ModelModel;
class ModelController extends BaseController
{
    private function getModelModel(string $dbGroup = 'default'): ModelModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new ModelModel($db);
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
            ['name' => 'Kaki', 'url' => 'kaki', 'icon' => 'fas fa-shoe-prints', 'active' => false],
            ['name' => 'Model', 'url' => 'model', 'icon' => 'fas fa-star', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Model',
            'classifications' => $classifications,
            'models' => $this->getModelModel('default')->findAll()
        ];
        return view('model/index', $data);
    }
    public function edit($id)
    {
        $model = $this->getModelModel('default')->find($id);
        if (empty($model)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data model tidak ditemukan.');
        }
        if (!isset($model['otoritas']) || $model['otoritas'] !== 'T') {
            return redirect()->to('/model')->with('error', 'Akses edit model ini membutuhkan otoritas.');
        }
        $mode = $model['mode_batas_tanggal'] ?? 'manual';
        $batas = $model['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($model['created_at']) ? substr($model['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/model')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($model['created_at']) ? substr($model['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/model')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Model',
            'model' => $model
        ];
        return view('model/edit', $data);
    }

    public function create()
    {
        $rules = ['name' => 'required|min_length[2]|is_unique[model.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/model')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $mainModel = $this->getModelModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getModelModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (model) failed: ' . $e->getMessage());
                return redirect()->to('/model')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/model')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/model')->with('success', 'Data model berhasil ditambahkan.');
    }

    public function update($id)
    {
        $model = $this->getModelModel('default')->find($id);
        if (empty($model) || !isset($model['otoritas']) || $model['otoritas'] !== 'T') {
            return redirect()->to('/model')->with('error', 'Akses update model ini membutuhkan otoritas.');
        }
        $mode = $model['mode_batas_tanggal'] ?? 'manual';
        $batas = $model['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($model['created_at']) ? substr($model['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/model')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($model['created_at']) ? substr($model['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/model')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[model.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getModelModel('default')->update($id, $dataToUpdate);
        try {
            $this->getModelModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (model update) failed: ' . $e->getMessage());
        }
        $this->getModelModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getModelModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (model otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/model')->with('success', 'Data model berhasil diperbarui.');
    }

    public function delete($id)
    {
        $model = $this->getModelModel('default')->find($id);
        if (empty($model) || !isset($model['otoritas']) || $model['otoritas'] !== 'T') {
            return redirect()->to('/model')->with('error', 'Akses hapus model ini membutuhkan otoritas.');
        }
        $mode = $model['mode_batas_tanggal'] ?? 'manual';
        $batas = $model['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($model['created_at']) ? substr($model['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/model')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($model['created_at']) ? substr($model['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/model')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getModelModel('default')->delete($id);
        try {
            $this->getModelModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (model delete) failed: ' . $e->getMessage());
        }
        $this->getModelModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getModelModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (model otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/model')->with('success', 'Data model berhasil dihapus.');
    }
}
