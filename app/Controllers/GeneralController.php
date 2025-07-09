<?php

namespace App\Controllers;

use App\Models\SystemDateLimitModel;
use App\Models\CategoryModel;
use App\Models\SatuanModel;
use App\Models\JenisModel;

class GeneralController extends BaseController
{
    /**
     * Menu pengaturan Batas Tanggal Sistem (global)
     */
    public function batasTanggalSistem()
    {
        $model = new SystemDateLimitModel();
        $limits = $model->findAll();
        $data = [
            'title' => 'Batas Tanggal Sistem',
            'limits' => $limits
        ];
        return view('general/batas_tanggal_sistem', $data);
    }

    /**
     * Simpan pengaturan Batas Tanggal Sistem
     */
    public function setBatasTanggalSistem()
    {
        $menu = $this->request->getPost('menu');
        $batas = $this->request->getPost('batas_tanggal');
        $mode = $this->request->getPost('mode_batas_tanggal');
        // Konversi format d/m/Y ke Y-m-d jika manual
        if ($mode === 'manual' && $batas) {
            $parts = explode('/', $batas);
            if (count($parts) === 3) {
                $batas = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            }
        }
        $modelDefault = new SystemDateLimitModel(\Config\Database::connect('default'));
        $modelDb1 = new SystemDateLimitModel(\Config\Database::connect('db1'));
        // Upsert (insert or update)
        $data = [
            'menu' => $menu,
            'batas_tanggal' => $batas,
            'mode_batas_tanggal' => $mode
        ];
        $modelDefault->where('menu', $menu)->delete();
        $modelDefault->insert($data);
        $modelDb1->where('menu', $menu)->delete();
        $modelDb1->insert($data);
        return redirect()->to('/general/batasTanggalSistem')->with('success', 'Batas tanggal sistem berhasil disimpan!');
    }

    public function index()
    {
        $data = [
            'title' => 'General Dashboard',
            'page_heading' => 'General Dashboard',
            'user' => session()->get()
        ];
        return view('dashboard_view', $data);
    }

    // Menu Otoritas Satuan
    public function otoritasSatuan()
    {
        $satuanModel = new SatuanModel();
        $data = [
            'satuans' => $satuanModel->select('id, name')->findAll(),
        ];
        return view('general/otoritas_satuan', $data);
    }

    // Proses set otoritas satuan
    public function setOtoritasSatuan()
    {
        $satuanId = $this->request->getPost('satuan_id');
        $otoritas = $this->request->getPost('otoritas');
        $batasTanggal = $this->request->getPost('batas_tanggal_sistem');
        $modeBatas = $this->request->getPost('mode_batas_tanggal');
        // Update di database default
        $satuanModelDefault = new SatuanModel(\Config\Database::connect('default'));
        $satuanModelDefault->update($satuanId, [
            'otoritas' => $otoritas,
            'batas_tanggal_sistem' => $batasTanggal,
            'mode_batas_tanggal' => $modeBatas
        ]);
        // Update juga di database kedua (db1)
        $satuanModelDb1 = new SatuanModel(\Config\Database::connect('db1'));
        $satuanModelDb1->update($satuanId, [
            'otoritas' => $otoritas,
            'batas_tanggal_sistem' => $batasTanggal,
            'mode_batas_tanggal' => $modeBatas
        ]);
        if ($otoritas === 'T') {
            return redirect()->to('/general/otoritasSatuan')->with('success', 'Satuan berhasil diotorisasi & batas tanggal disimpan di kedua database. Sekarang bisa diedit atau dihapus.');
        } else {
            return redirect()->to('/general/otoritasSatuan')->with('success', 'Otoritas satuan dinonaktifkan & batas tanggal disimpan di kedua database.');
        }
    }

    // Menu Otoritas Kategori
    public function otoritasKategori()
    {
        $categoryModel = new CategoryModel();
        // Ambil semua kolom yang dibutuhkan agar status otoritas dan deskripsi tampil dan update
        $data = [
            'categories' => $categoryModel->select('id, name, description, otoritas')->findAll(),
        ];
        return view('general/otoritas', $data);
    }

    // Proses set otoritas kategori
    public function setOtoritasKategori()
    {
        $kategoriId = $this->request->getPost('kategori_id');
        $otoritas = $this->request->getPost('otoritas');
        $batasTanggal = $this->request->getPost('batas_tanggal_sistem');
        $modeBatas = $this->request->getPost('mode_batas_tanggal');
        // Update di database default
        $categoryModelDefault = new CategoryModel(\Config\Database::connect('default'));
        $categoryModelDefault->update($kategoriId, [
            'otoritas' => $otoritas,
            'batas_tanggal_sistem' => $batasTanggal,
            'mode_batas_tanggal' => $modeBatas
        ]);
        // Update juga di database kedua (db1)
        $categoryModelDb1 = new CategoryModel(\Config\Database::connect('db1'));
        $categoryModelDb1->update($kategoriId, [
            'otoritas' => $otoritas,
            'batas_tanggal_sistem' => $batasTanggal,
            'mode_batas_tanggal' => $modeBatas
        ]);
        if ($otoritas === 'T') {
            return redirect()->to('/general/otoritasKategori')->with('success', 'Kategori berhasil diotorisasi & batas tanggal disimpan di kedua database. Sekarang bisa diedit atau dihapus.');
        } else {
            return redirect()->to('/general/otoritasKategori')->with('success', 'Otoritas kategori dinonaktifkan & batas tanggal disimpan di kedua database.');
        }
    }

    // Menu Otoritas Jenis
    public function otoritasJenis()
    {
        $jenisModel = new JenisModel();
        $data = [
            'jenis' => $jenisModel->select('id, name')->findAll(),
        ];
        return view('general/otoritas_jenis', $data);
    }

    // Proses set otoritas jenis
    public function setOtoritasJenis()
    {
        $jenisId = $this->request->getPost('jenis_id');
        $otoritas = $this->request->getPost('otoritas');
        $batasTanggal = $this->request->getPost('batas_tanggal_sistem');
        $modeBatas = $this->request->getPost('mode_batas_tanggal');
        // Update di database default
        $jenisModelDefault = new JenisModel(\Config\Database::connect('default'));
        $jenisModelDefault->update($jenisId, [
            'otoritas' => $otoritas,
            'batas_tanggal_sistem' => $batasTanggal,
            'mode_batas_tanggal' => $modeBatas
        ]);
        // Update juga di database kedua (db1)
        $jenisModelDb1 = new JenisModel(\Config\Database::connect('db1'));
        $jenisModelDb1->update($jenisId, [
            'otoritas' => $otoritas,
            'batas_tanggal_sistem' => $batasTanggal,
            'mode_batas_tanggal' => $modeBatas
        ]);

        if ($otoritas === 'T') {
            return redirect()->to('/general/otoritasJenis')->with('success', 'Jenis berhasil diotorisasi & batas tanggal disimpan di kedua database. Sekarang bisa diedit atau dihapus.');
        } else {
            return redirect()->to('/general/otoritasJenis')->with('success', 'Otoritas jenis dinonaktifkan & batas tanggal disimpan di kedua database.');
        }
    }

    // Menu Otoritas Produk
    public function otoritasProduk()
    {
        $productModel = new \App\Models\ProductModel();
        $data = [
            'products' => $productModel->where('deleted_at', null)->findAll()
        ];
        return view('general/otoritas_produk', $data);
    }

    // Proses set otoritas produk
    public function setOtoritasProduk()
    {
        $produkId = $this->request->getPost('produk_id');
        $otoritas = $this->request->getPost('otoritas') === 'T' ? 'T' : null;
        $mainModel = new \App\Models\ProductModel(\Config\Database::connect('default'));
        $backupModel = new \App\Models\ProductModel(\Config\Database::connect('db1'));
        if ($produkId) {
            $mainModel->update($produkId, ['otoritas' => $otoritas]);
            $backupModel->update($produkId, ['otoritas' => $otoritas]);
            return redirect()->to('/general/otoritasProduk')->with('success', 'Otoritas produk berhasil diubah.');
        }
        return redirect()->to('/general/otoritasProduk')->with('error', 'Produk tidak ditemukan.');
    }
}
