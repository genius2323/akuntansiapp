<?php
namespace App\Controllers;
use App\Models\DayaModel;
class DayaController extends BaseController
{
    private function getDayaModel(string $dbGroup = 'default'): DayaModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new DayaModel($db);
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
            ['name' => 'Model', 'url' => 'model', 'icon' => 'fas fa-star', 'active' => false],
            ['name' => 'Fiting', 'url' => 'fiting', 'icon' => 'fas fa-plug', 'active' => false],
            ['name' => 'Daya', 'url' => 'daya', 'icon' => 'fas fa-power-off', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Daya',
            'classifications' => $classifications,
            'dayas' => $this->getDayaModel('default')->findAll()
        ];
        return view('daya/index', $data);
    }
    public function edit($id)
    {
        $daya = $this->getDayaModel('default')->find($id);
        if (empty($daya)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data daya tidak ditemukan.');
        }
        if (!isset($daya['otoritas']) || $daya['otoritas'] !== 'T') {
            return redirect()->to('/daya')->with('error', 'Akses edit daya ini membutuhkan otoritas.');
        }
        $mode = $daya['mode_batas_tanggal'] ?? 'manual';
        $batas = $daya['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($daya['created_at']) ? substr($daya['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/daya')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($daya['created_at']) ? substr($daya['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/daya')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Daya',
            'daya' => $daya
        ];
        return view('daya/edit', $data);
    }

    public function create()
    {
        $rules = ['name' => 'required|min_length[2]|is_unique[daya.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/daya')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $mainModel = $this->getDayaModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getDayaModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (daya) failed: ' . $e->getMessage());
                return redirect()->to('/daya')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/daya')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/daya')->with('success', 'Data daya berhasil ditambahkan.');
    }

    public function update($id)
    {
        $daya = $this->getDayaModel('default')->find($id);
        if (empty($daya) || !isset($daya['otoritas']) || $daya['otoritas'] !== 'T') {
            return redirect()->to('/daya')->with('error', 'Akses update daya ini membutuhkan otoritas.');
        }
        $mode = $daya['mode_batas_tanggal'] ?? 'manual';
        $batas = $daya['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($daya['created_at']) ? substr($daya['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/daya')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($daya['created_at']) ? substr($daya['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/daya')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[daya.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getDayaModel('default')->update($id, $dataToUpdate);
        try {
            $this->getDayaModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (daya update) failed: ' . $e->getMessage());
        }
        $this->getDayaModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getDayaModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (daya otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/daya')->with('success', 'Data daya berhasil diperbarui.');
    }

    public function delete($id)
    {
        $daya = $this->getDayaModel('default')->find($id);
        if (empty($daya) || !isset($daya['otoritas']) || $daya['otoritas'] !== 'T') {
            return redirect()->to('/daya')->with('error', 'Akses hapus daya ini membutuhkan otoritas.');
        }
        $mode = $daya['mode_batas_tanggal'] ?? 'manual';
        $batas = $daya['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($daya['created_at']) ? substr($daya['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/daya')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($daya['created_at']) ? substr($daya['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/daya')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getDayaModel('default')->delete($id);
        try {
            $this->getDayaModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (daya delete) failed: ' . $e->getMessage());
        }
        $this->getDayaModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getDayaModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (daya otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/daya')->with('success', 'Data daya berhasil dihapus.');
    }
}
