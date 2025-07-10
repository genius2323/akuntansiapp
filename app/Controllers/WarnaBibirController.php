<?php
namespace App\Controllers;
use App\Models\WarnaBibirModel;
class WarnaBibirController extends BaseController
{
    private function getWarnaBibirModel(string $dbGroup = 'default'): WarnaBibirModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new WarnaBibirModel($db);
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
            ['name' => 'Warna Bibir', 'url' => 'warna-bibir', 'icon' => 'fas fa-tint', 'active' => true],
            // ...lanjutkan sesuai kebutuhan
        ];
        $data = [
            'title' => 'Master Klasifikasi - Warna Bibir',
            'classifications' => $classifications,
            'warnabibirs' => $this->getWarnaBibirModel('default')->findAll()
        ];
        return view('warnabibir/index', $data);
    }

    public function edit($id)
    {
        $warnabibir = $this->getWarnaBibirModel('default')->find($id);
        if (empty($warnabibir)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data warna bibir tidak ditemukan.');
        }
        if (!isset($warnabibir['otoritas']) || $warnabibir['otoritas'] !== 'T') {
            return redirect()->to('/warna-bibir')->with('error', 'Akses edit warna bibir ini membutuhkan otoritas.');
        }
        $mode = $warnabibir['mode_batas_tanggal'] ?? 'manual';
        $batas = $warnabibir['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($warnabibir['created_at']) ? substr($warnabibir['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/warna-bibir')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($warnabibir['created_at']) ? substr($warnabibir['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/warna-bibir')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Warna Bibir',
            'warnabibir' => $warnabibir
        ];
        return view('warnabibir/edit', $data);
    }

    public function create()
    {
        // Log debug dihapus setelah verifikasi sukses
        $rules = ['name' => 'required|min_length[2]|is_unique[warna_bibir.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/warna-bibir')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'kode_ky'     => session('kode_ky'),
        ];
        $mainModel = $this->getWarnaBibirModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getWarnaBibirModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (warna_bibir) failed: ' . $e->getMessage());
                return redirect()->to('/warna-bibir')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/warna-bibir')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/warna-bibir')->with('success', 'Data warna bibir berhasil ditambahkan.');
    }

    public function update($id)
    {
        $warnabibir = $this->getWarnaBibirModel('default')->find($id);
        if (empty($warnabibir) || !isset($warnabibir['otoritas']) || $warnabibir['otoritas'] !== 'T') {
            return redirect()->to('/warna-bibir')->with('error', 'Akses update warna bibir ini membutuhkan otoritas.');
        }
        $mode = $warnabibir['mode_batas_tanggal'] ?? 'manual';
        $batas = $warnabibir['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($warnabibir['created_at']) ? substr($warnabibir['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/warna-bibir')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($warnabibir['created_at']) ? substr($warnabibir['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/warna-bibir')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[warna_bibir.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getWarnaBibirModel('default')->update($id, $dataToUpdate);
        try {
            $this->getWarnaBibirModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_bibir update) failed: ' . $e->getMessage());
        }
        // Update kode_ky setelah update data
        $this->getWarnaBibirModel('default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getWarnaBibirModel('db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_bibir kode_ky update) failed: ' . $e->getMessage());
        }
        $this->getWarnaBibirModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getWarnaBibirModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_bibir otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/warna-bibir')->with('success', 'Data warna bibir berhasil diperbarui.');
    }

    public function delete($id)
    {
        $warnabibir = $this->getWarnaBibirModel('default')->find($id);
        if (empty($warnabibir) || !isset($warnabibir['otoritas']) || $warnabibir['otoritas'] !== 'T') {
            return redirect()->to('/warna-bibir')->with('error', 'Akses hapus warna bibir ini membutuhkan otoritas.');
        }
        $mode = $warnabibir['mode_batas_tanggal'] ?? 'manual';
        $batas = $warnabibir['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($warnabibir['created_at']) ? substr($warnabibir['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/warna-bibir')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($warnabibir['created_at']) ? substr($warnabibir['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/warna-bibir')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getWarnaBibirModel('default')->delete($id);
        try {
            $this->getWarnaBibirModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_bibir delete) failed: ' . $e->getMessage());
        }
        // Update kode_ky setelah soft delete
        $this->getWarnaBibirModel('default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getWarnaBibirModel('db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_bibir kode_ky update after delete) failed: ' . $e->getMessage());
        }
        $this->getWarnaBibirModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getWarnaBibirModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (warna_bibir otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/warna-bibir')->with('success', 'Data warna bibir berhasil dihapus.');
    }
}
