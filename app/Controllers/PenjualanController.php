<?php

namespace App\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanItemModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\UserModel;

class PenjualanController extends BaseController
{
    private function getPenjualanModel(string $dbGroup = 'default'): PenjualanModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new PenjualanModel($db);
    }
    private function getPenjualanItemModel(string $dbGroup = 'default'): PenjualanItemModel
    {
        $db = \Config\Database::connect($dbGroup);
        return new PenjualanItemModel($db);
    }
    /**
     * Menampilkan halaman form input penjualan (POS).
     */
    public function index()
    {
        $productModel = new ProductModel();
        $customerModel = new CustomerModel();
        $userModel = new UserModel();
        // Ambil batas tanggal sistem untuk menu penjualan
        $systemDateLimitModel = new \App\Models\SystemDateLimitModel();
        $limit = $systemDateLimitModel->where('menu', 'penjualan')->first();
        $mode_batas_tanggal = $limit['mode_batas_tanggal'] ?? 'manual';
        $batas_tanggal_sistem = $limit['batas_tanggal'] ?? null;
        $data = [
            'title' => 'Input Penjualan (POS)',
            'products' => $productModel->where('deleted_at', null)->findAll(),
            'customers' => $customerModel->where('deleted_at', null)->findAll(),
            'sales_users' => $userModel->where('deleted_at', null)->findAll(),
            'mode_batas_tanggal' => $mode_batas_tanggal,
            'batas_tanggal_sistem' => $batas_tanggal_sistem,
        ];
        return view('penjualan/pos_form', $data); // Menggunakan view baru
    }

    /**
     * Menyimpan data transaksi penjualan ke database.
     */
    public function store()
    {
        $data = $this->request->getPost();
        // Validasi batas tanggal sistem (mode manual/automatic)
        $mode = $data['mode_batas_tanggal'] ?? 'manual';
        $batas = $data['batas_tanggal_sistem'] ?? null;
        $tanggalNota = $data['tanggal_nota'] ?? date('Y-m-d');
        $today = date('Y-m-d');
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            if ($tanggalNota > $maxDate) {
                return redirect()->back()->withInput()->with('error', 'Input hanya diizinkan untuk tanggal H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            if ($tanggalNota > $batas) {
                return redirect()->back()->withInput()->with('error', 'Input hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }

        // Simpan ke dua database
        $penjualanData = [
            'nomor_nota' => $data['nomor_nota'],
            'tanggal_nota' => $tanggalNota,
            'customer' => $data['customer'],
            'sales' => $data['sales'],
            'total' => str_replace(',', '', $data['total'] ?? 0),
            'discount' => str_replace(',', '', $data['discount'] ?? 0),
            'tax' => str_replace(',', '', $data['tax'] ?? 0),
            'grand_total' => str_replace(',', '', $data['grand_total'] ?? 0),
            'payment_a' => str_replace(',', '', $data['payment_a'] ?? 0),
            'payment_b' => str_replace(',', '', $data['payment_b'] ?? 0),
            'account_receivable' => str_replace(',', '', $data['account_receivable'] ?? 0),
            'payment_system' => $data['payment_system'],
            'otoritas' => $data['otoritas'] ?? null,
            'batas_tanggal_sistem' => $batas,
            'mode_batas_tanggal' => $mode,
        ];
        $mainModel = $this->getPenjualanModel('default');
        $backupModel = $this->getPenjualanModel('db1');
        $mainModel->transStart();
        $mainId = $mainModel->insert($penjualanData, true);
        $backupId = $backupModel->insert($penjualanData, true);

        // Simpan detail barang
        $items = [];
        if (isset($data['kode']) && is_array($data['kode'])) {
            foreach ($data['kode'] as $i => $kode) {
                $items[] = [
                    'sales_id' => $mainId,
                    'product_code' => $kode,
                    'product_name' => $data['nama'][$i],
                    'qty' => $data['qty'][$i],
                    'unit' => $data['satuan'][$i],
                    'price' => $data['harga'][$i],
                    'discount' => $data['diskon'][$i],
                    'total' => $data['total'][$i],
                ];
            }
        }
        $itemModel = $this->getPenjualanItemModel('default');
        $itemModelBackup = $this->getPenjualanItemModel('db1');
        foreach ($items as $item) {
            $item['sales_id'] = $mainId;
            $itemModel->insert($item);
            $item['sales_id'] = $backupId;
            $itemModelBackup->insert($item);
        }
        $mainModel->transComplete();
        return redirect()->to('/penjualan')->with('success', 'Transaksi berhasil disimpan!');
    }

    /**
     * Edit transaksi penjualan (tampilkan form edit)
     */
    public function edit($id)
    {
        $mainModel = $this->getPenjualanModel('default');
        $itemModel = $this->getPenjualanItemModel('default');
        $penjualan = $mainModel->find($id);
        if (!$penjualan) {
            return redirect()->to('/penjualan')->with('error', 'Data penjualan tidak ditemukan.');
        }
        // Validasi otorisasi
        if ($penjualan['otoritas'] !== 'T') {
            return redirect()->to('/penjualan')->with('error', 'Edit membutuhkan otorisasi.');
        }
        // Validasi batas tanggal
        $mode = $penjualan['mode_batas_tanggal'] ?? 'manual';
        $batas = $penjualan['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        $tanggalNota = $penjualan['tanggal_nota'] ?? $today;
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            if ($tanggalNota > $maxDate) {
                return redirect()->to('/penjualan')->with('error', 'Edit hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            if ($tanggalNota > $batas) {
                return redirect()->to('/penjualan')->with('error', 'Edit hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $items = $itemModel->where('sales_id', $id)->findAll();
        $data = [
            'title' => 'Edit Penjualan',
            'penjualan' => $penjualan,
            'items' => $items
        ];
        return view('penjualan/pos_form', $data); // Gunakan view yang sama, bedakan mode edit/input di view
    }

    /**
     * Update transaksi penjualan
     */
    public function update($id)
    {
        $mainModel = $this->getPenjualanModel('default');
        $backupModel = $this->getPenjualanModel('db1');
        $itemModel = $this->getPenjualanItemModel('default');
        $itemModelBackup = $this->getPenjualanItemModel('db1');
        $penjualan = $mainModel->find($id);
        if (!$penjualan) {
            return redirect()->to('/penjualan')->with('error', 'Data penjualan tidak ditemukan.');
        }
        // Validasi otorisasi
        if ($penjualan['otoritas'] !== 'T') {
            return redirect()->to('/penjualan')->with('error', 'Update membutuhkan otorisasi.');
        }
        // Validasi batas tanggal
        $mode = $penjualan['mode_batas_tanggal'] ?? 'manual';
        $batas = $penjualan['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        $tanggalNota = $penjualan['tanggal_nota'] ?? $today;
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            if ($tanggalNota > $maxDate) {
                return redirect()->to('/penjualan')->with('error', 'Update hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            if ($tanggalNota > $batas) {
                return redirect()->to('/penjualan')->with('error', 'Update hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $data = $this->request->getPost();
        $penjualanData = [
            'customer' => $data['customer'],
            'sales' => $data['sales'],
            'total' => str_replace(',', '', $data['total'] ?? 0),
            'discount' => str_replace(',', '', $data['discount'] ?? 0),
            'tax' => str_replace(',', '', $data['tax'] ?? 0),
            'grand_total' => str_replace(',', '', $data['grand_total'] ?? 0),
            'payment_a' => str_replace(',', '', $data['payment_a'] ?? 0),
            'payment_b' => str_replace(',', '', $data['payment_b'] ?? 0),
            'account_receivable' => str_replace(',', '', $data['account_receivable'] ?? 0),
            'payment_system' => $data['payment_system'],
        ];
        $mainModel->update($id, $penjualanData);
        $backupModel->update($id, $penjualanData);
        // Update detail barang: hapus semua, insert ulang
        $itemModel->where('sales_id', $id)->delete();
        $itemModelBackup->where('sales_id', $id)->delete();
        if (isset($data['kode']) && is_array($data['kode'])) {
            foreach ($data['kode'] as $i => $kode) {
                $item = [
                    'sales_id' => $id,
                    'product_code' => $kode,
                    'product_name' => $data['nama'][$i],
                    'qty' => $data['qty'][$i],
                    'unit' => $data['satuan'][$i],
                    'price' => $data['harga'][$i],
                    'discount' => $data['diskon'][$i],
                    'total' => $data['total'][$i],
                ];
                $itemModel->insert($item);
                $itemModelBackup->insert($item);
            }
        }
        return redirect()->to('/penjualan')->with('success', 'Transaksi berhasil diupdate!');
    }

    /**
     * Soft delete transaksi penjualan
     */
    public function delete($id)
    {
        $mainModel = $this->getPenjualanModel('default');
        $backupModel = $this->getPenjualanModel('db1');
        $itemModel = $this->getPenjualanItemModel('default');
        $itemModelBackup = $this->getPenjualanItemModel('db1');
        $penjualan = $mainModel->find($id);
        if (!$penjualan) {
            return redirect()->to('/penjualan')->with('error', 'Data penjualan tidak ditemukan.');
        }
        // Validasi otorisasi
        if ($penjualan['otoritas'] !== 'T') {
            return redirect()->to('/penjualan')->with('error', 'Hapus membutuhkan otorisasi.');
        }
        // Validasi batas tanggal
        $mode = $penjualan['mode_batas_tanggal'] ?? 'manual';
        $batas = $penjualan['batas_tanggal_sistem'] ?? null;
        $today = date('Y-m-d');
        $tanggalNota = $penjualan['tanggal_nota'] ?? $today;
        if ($mode === 'automatic') {
            $maxDate = date('Y-m-d', strtotime($today . ' -2 days'));
            if ($tanggalNota > $maxDate) {
                return redirect()->to('/penjualan')->with('error', 'Hapus hanya diizinkan untuk data H-2 atau lebih lama (mode automatic).');
            }
        } elseif ($mode === 'manual' && $batas) {
            if ($tanggalNota > $batas) {
                return redirect()->to('/penjualan')->with('error', 'Hapus hanya diizinkan sampai batas tanggal yang ditentukan.');
            }
        }
        $mainModel->delete($id);
        $backupModel->delete($id);
        $itemModel->where('sales_id', $id)->delete();
        $itemModelBackup->where('sales_id', $id)->delete();
        return redirect()->to('/penjualan')->with('success', 'Transaksi berhasil dihapus!');
    }

    /**
     * Dashboard penjualan: tombol input & tabel data
     */
    public function dashboard()
    {
        $mainModel = $this->getPenjualanModel('default');
        $penjualans = $mainModel->where('deleted_at', null)->orderBy('tanggal_nota', 'desc')->findAll();
        $data = [
            'page_heading' => 'Dashboard Penjualan',
            'penjualans' => $penjualans
        ];
        return view('dashboard_view', $data);
    }
}
