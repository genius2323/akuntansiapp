<?php namespace App\Controllers;

class PenjualanController extends BaseController
{
    /**
     * Menampilkan halaman form input penjualan (POS).
     */
    public function index()
    {
        $data = [
            'title' => 'Input Penjualan (POS)',
            // Nantinya di sini kita bisa menambahkan data produk dari database
            // 'products' => $this->productModel->findAll()
        ];
        
        return view('penjualan/pos_form', $data); // Menggunakan view baru
    }

    /**
     * Menyimpan data transaksi penjualan ke database.
     */
    public function store()
    {
        // Logika untuk validasi dan penyimpanan data akan ditambahkan di sini.
        // Contoh:
        // $data = $this->request->getPost();
        // $this->penjualanModel->save($data);
        
        // Setelah menyimpan, kembali ke halaman form dengan pesan sukses.
        return redirect()->to('/penjualan')->with('success', 'Transaksi berhasil disimpan!');
    }
}