<?php

namespace App\Controllers;

use App\Models\JenisModel;

class JenisController extends BaseController
{
    private function getJenisModel(string $dbGroup = 'default'): JenisModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new JenisModel($db);
    }

    public function index()
    {
        $classifications = [
            ['name' => 'Kategori', 'url' => 'categories', 'icon' => 'fas fa-tags', 'active' => false],
            ['name' => 'Satuan', 'url' => 'satuan', 'icon' => 'fas fa-ruler-combined', 'active' => false],
            ['name' => 'Jenis', 'url' => 'jenis', 'icon' => 'fas fa-boxes', 'active' => true],
            // ...tambahkan lainnya jika perlu
        ];
        $data = [
            'title'           => 'Master Klasifikasi - Jenis',
            'classifications' => $classifications,
            'jenis'           => $this->getJenisModel('default')->select('id, name, description, otoritas')->findAll()
        ];
        return view('jenis/index', $data);
    }

    public function edit($id)
    {
        $jenis = $this->getJenisModel('default')->find($id);
        if (empty($jenis)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data jenis tidak ditemukan.');
        }
        if (!isset($jenis['otoritas']) || $jenis['otoritas'] !== 'T') {
            return redirect()->to('/jenis')->with('error', 'Akses edit jenis ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $jenis['mode_batas_tanggal'] ?? 'manual';
        $batas = $jenis['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($jenis['created_at']) ? substr($jenis['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/jenis')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($jenis['created_at']) ? substr($jenis['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/jenis')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = [
            'title' => 'Edit Jenis',
            'jenis' => $jenis
        ];
        return view('jenis/edit', $data);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[3]|is_unique[jenis.name]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->to('/jenis')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $mainModel = $this->getJenisModel('default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getJenisModel('db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (jenis) failed: ' . $e->getMessage());
                return redirect()->to('/jenis')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/jenis')->with('error', 'Gagal menyimpan data utama.');
        }
        return redirect()->to('/jenis')->with('success', 'Data jenis berhasil ditambahkan.');
    }

    public function update($id)
    {
        $jenis = $this->getJenisModel('default')->find($id);
        if (empty($jenis) || !isset($jenis['otoritas']) || $jenis['otoritas'] !== 'T') {
            return redirect()->to('/jenis')->with('error', 'Akses update jenis ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $jenis['mode_batas_tanggal'] ?? 'manual';
        $batas = $jenis['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($jenis['created_at']) ? substr($jenis['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/jenis')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($jenis['created_at']) ? substr($jenis['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/jenis')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $rules = [
            'name' => "required|min_length[3]|is_unique[jenis.name,id,{$id}]"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];
        $this->getJenisModel('default')->update($id, $dataToUpdate);
        try {
            $this->getJenisModel('db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (jenis update) failed: ' . $e->getMessage());
        }
        $this->getJenisModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getJenisModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (jenis otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/jenis')->with('success', 'Data jenis berhasil diperbarui.');
    }

    public function delete($id)
    {
        $jenis = $this->getJenisModel('default')->find($id);
        if (empty($jenis) || !isset($jenis['otoritas']) || $jenis['otoritas'] !== 'T') {
            return redirect()->to('/jenis')->with('error', 'Akses hapus jenis ini membutuhkan otoritas.');
        }
        // Validasi batas tanggal
        $mode = $jenis['mode_batas_tanggal'] ?? 'manual';
        $batas = $jenis['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            $created = isset($jenis['created_at']) ? substr($jenis['created_at'], 0, 10) : $today;
            if ($created > $maxDate) {
                return redirect()->to('/jenis')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            $created = isset($jenis['created_at']) ? substr($jenis['created_at'], 0, 10) : $today;
            if ($created > $batas) {
                return redirect()->to('/jenis')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $this->getJenisModel('default')->delete($id);
        try {
            $this->getJenisModel('db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (jenis delete) failed: ' . $e->getMessage());
        }
        $this->getJenisModel('default')->update($id, ['otoritas' => null]);
        try {
            $this->getJenisModel('db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (jenis otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/jenis')->with('success', 'Data jenis berhasil dihapus.');
    }
}
