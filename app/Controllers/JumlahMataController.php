<?php
namespace App\Controllers;
use App\Models\JumlahMataModel;
class JumlahMataController extends BaseController
{
    private function getJumlahMataModel(string $dbGroup = 'default'): JumlahMataModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new JumlahMataModel($db);
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
            ['name' => 'Daya', 'url' => 'daya', 'icon' => 'fas fa-power-off', 'active' => false],
            ['name' => 'Jumlah Mata', 'url' => 'jumlah-mata', 'icon' => 'fas fa-eye', 'active' => true],
        ];
        $data = [
            'title' => 'Master Klasifikasi - Jumlah Mata',
            'classifications' => $classifications,
            'jumlahmatas' => $this->getJumlahMataModel('default')->findAll()
        ];
        return view('jumlahmata/index', $data);
    }
    public function edit($id)
    {
        $jumlahmata = $this->getJumlahMataModel('default')->find($id);
        if (empty($jumlahmata)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data jumlah mata tidak ditemukan.');
        }
        if (!isset($jumlahmata['otoritas']) || $jumlahmata['otoritas'] !== 'T') {
            return redirect()->to('/jumlah-mata')->with('error', 'Akses edit jumlah mata ini membutuhkan otoritas.');
        }
        $mode = $jumlahmata['mode_batas_tanggal'] ?? 'manual';
        $batas = $jumlahmata['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($jumlahmata['created_at']) ? substr($jumlahmata['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/jumlah-mata')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($jumlahmata['created_at']) ? substr($jumlahmata['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/jumlah-mata')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Jumlah Mata',
            'jumlahmata' => $jumlahmata
        ];
        return view('jumlahmata/edit', $data);
    }

    public function create()
    {
        $rules = ['name' => 'required|min_length[2]|is_unique[jumlah_mata.name]'];
        if (!$this->validate($rules)) {
            return redirect()->to('/jumlah-mata')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $mainModel = $this->getJumlahMataModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getJumlahMataModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (jumlah_mata) failed: ' . $e->getMessage());
                return redirect()->to('/jumlah-mata')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/jumlah-mata')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/jumlah-mata')->with('success', 'Data jumlah mata berhasil ditambahkan.');
    }

    public function update($id)
    {
        $jumlahmata = $this->getJumlahMataModel('default')->find($id);
        if (empty($jumlahmata) || !isset($jumlahmata['otoritas']) || $jumlahmata['otoritas'] !== 'T') {
            return redirect()->to('/jumlah-mata')->with('error', 'Akses update jumlah mata ini membutuhkan otoritas.');
        }
        $mode = $jumlahmata['mode_batas_tanggal'] ?? 'manual';
        $batas = $jumlahmata['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($jumlahmata['created_at']) ? substr($jumlahmata['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/jumlah-mata')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($jumlahmata['created_at']) ? substr($jumlahmata['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/jumlah-mata')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[jumlah_mata.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getJumlahMataModel('default')->update($id, $dataToUpdate);
        try {
            $this->getJumlahMataModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (jumlah_mata update) failed: ' . $e->getMessage());
        }
        $this->getJumlahMataModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getJumlahMataModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (jumlah_mata otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/jumlah-mata')->with('success', 'Data jumlah mata berhasil diperbarui.');
    }

    public function delete($id)
    {
        $jumlahmata = $this->getJumlahMataModel('default')->find($id);
        if (empty($jumlahmata) || !isset($jumlahmata['otoritas']) || $jumlahmata['otoritas'] !== 'T') {
            return redirect()->to('/jumlah-mata')->with('error', 'Akses hapus jumlah mata ini membutuhkan otoritas.');
        }
        $mode = $jumlahmata['mode_batas_tanggal'] ?? 'manual';
        $batas = $jumlahmata['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($jumlahmata['created_at']) ? substr($jumlahmata['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/jumlah-mata')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($jumlahmata['created_at']) ? substr($jumlahmata['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/jumlah-mata')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getJumlahMataModel('default')->delete($id);
        try {
            $this->getJumlahMataModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (jumlah_mata delete) failed: ' . $e->getMessage());
        }
        $this->getJumlahMataModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getJumlahMataModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (jumlah_mata otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/jumlah-mata')->with('success', 'Data jumlah mata berhasil dihapus.');
    }
}
