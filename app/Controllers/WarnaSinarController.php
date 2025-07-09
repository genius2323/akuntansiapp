<?php
namespace App\Controllers;
use App\Models\WarnaSinarModel;
class WarnaSinarController extends BaseController
{
    private function getWarnaSinarModel(string $dbGroup = 'default'): WarnaSinarModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new WarnaSinarModel($db);
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
            ['name' => 'Warna Sinar', 'url' => 'warna-sinar', 'icon' => 'fas fa-lightbulb', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Warna Sinar',
            'classifications' => $classifications,
            'warnasinars' => $this->getWarnaSinarModel('default')->findAll()
        ];
        return view('warnasinar/index', $data);
    }

    public function edit($id)
    {
        $warnasinar = $this->getWarnaSinarModel('default')->find($id);
        if (empty($warnasinar)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data warna sinar tidak ditemukan.');
        }
        if (!isset($warnasinar['otoritas']) || $warnasinar['otoritas'] !== 'T') {
            return redirect()->to('/warna-sinar')->with('error', 'Akses edit warna sinar ini membutuhkan otoritas.');
        }
        $mode = $warnasinar['mode_batas_tanggal'] ?? 'manual';
        $batas = $warnasinar['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($warnasinar['created_at']) ? substr($warnasinar['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/warna-sinar')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($warnasinar['created_at']) ? substr($warnasinar['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/warna-sinar')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Warna Sinar',
            'warnasinar' => $warnasinar
        ];
        return view('warnasinar/edit', $data);
    }

    public function create()
    {
        $rules = ['name' => 'required|min_length[2]|is_unique[warna_sinar.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/warna-sinar')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $mainModel = $this->getWarnaSinarModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getWarnaSinarModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (warna_sinar) failed: ' . $e->getMessage());
                return redirect()->to('/warna-sinar')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/warna-sinar')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/warna-sinar')->with('success', 'Data warna sinar berhasil ditambahkan.');
    }

    public function update($id)
    {
        $warnasinar = $this->getWarnaSinarModel('default')->find($id);
        if (empty($warnasinar) || !isset($warnasinar['otoritas']) || $warnasinar['otoritas'] !== 'T') {
            return redirect()->to('/warna-sinar')->with('error', 'Akses update warna sinar ini membutuhkan otoritas.');
        }
        $mode = $warnasinar['mode_batas_tanggal'] ?? 'manual';
        $batas = $warnasinar['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($warnasinar['created_at']) ? substr($warnasinar['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/warna-sinar')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($warnasinar['created_at']) ? substr($warnasinar['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/warna-sinar')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[warna_sinar.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getWarnaSinarModel('default')->update($id, $dataToUpdate);
        try {
            $this->getWarnaSinarModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_sinar update) failed: ' . $e->getMessage());
        }
        $this->getWarnaSinarModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getWarnaSinarModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_sinar otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/warna-sinar')->with('success', 'Data warna sinar berhasil diperbarui.');
    }

    public function delete($id)
    {
        $warnasinar = $this->getWarnaSinarModel('default')->find($id);
        if (empty($warnasinar) || !isset($warnasinar['otoritas']) || $warnasinar['otoritas'] !== 'T') {
            return redirect()->to('/warna-sinar')->with('error', 'Akses hapus warna sinar ini membutuhkan otoritas.');
        }
        $mode = $warnasinar['mode_batas_tanggal'] ?? 'manual';
        $batas = $warnasinar['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($warnasinar['created_at']) ? substr($warnasinar['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/warna-sinar')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($warnasinar['created_at']) ? substr($warnasinar['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/warna-sinar')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getWarnaSinarModel('default')->delete($id);
        try {
            $this->getWarnaSinarModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_sinar delete) failed: ' . $e->getMessage());
        }
        $this->getWarnaSinarModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getWarnaSinarModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_sinar otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/warna-sinar')->with('success', 'Data warna sinar berhasil dihapus.');
    }
}
