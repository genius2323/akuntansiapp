<?php namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\SatuanModel;
use App\Models\JenisModel;

class ProductController extends BaseController
{
    /**
     * Menghubungkan model ke database yang benar.
     * @param string $modelName Nama kelas model ('ProductModel' atau 'CategoryModel')
     * @param string $dbGroup Nama grup database ('default' atau 'db1')
     * @return \CodeIgniter\Model
     */
    private function getModel(string $modelName, string $dbGroup = 'default')
    {
        $db = \Config\Database::connect($dbGroup);
        // Membuat instance model secara dinamis
        $class = "App\\Models\\{$modelName}";
        return new $class($db);
    }

    public function index()
    {
        $productModel = $this->getModel('ProductModel', 'default');
        $categoryModel = $this->getModel('CategoryModel', 'default');
        $satuanModel = $this->getModel('SatuanModel', 'default');
        $jenisModel = $this->getModel('JenisModel', 'default');
        
        $data = [
            'title'      => 'Master Produk',
            'products'   => $productModel->getProductsWithCategory(),
            'categories' => $categoryModel->findAll(),
            'satuans'    => $satuanModel->findAll(),
            'jenis'      => $jenisModel->findAll(),
        ];
        return view('products/index', $data);
    }

    public function create()
    {
        $rules = [
            'name'        => 'required|min_length[3]',
            'category_id' => 'required|numeric',
            'satuan_id'   => 'required|numeric',
            'price'       => 'required|numeric',
            'stock'       => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/products/create')->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToSave = [
            'name'            => $this->request->getPost('name'),
            'category_id'     => $this->request->getPost('category_id'),
            'satuan_id'       => $this->request->getPost('satuan_id'),
            'jenis_id'        => $this->request->getPost('jenis_id'),
            'pelengkap_id'    => $this->request->getPost('pelengkap_id'),
            'gondola_id'      => $this->request->getPost('gondola_id'),
            'merk_id'         => $this->request->getPost('merk_id'),
            'warna_sinar_id'  => $this->request->getPost('warna_sinar_id'),
            'ukuran_barang_id'=> $this->request->getPost('ukuran_barang_id'),
            'voltase_id'      => $this->request->getPost('voltase_id'),
            'dimensi_id'      => $this->request->getPost('dimensi_id'),
            'warna_body_id'   => $this->request->getPost('warna_body_id'),
            'warna_bibir_id'  => $this->request->getPost('warna_bibir_id'),
            'kaki_id'         => $this->request->getPost('kaki_id'),
            'model_id'        => $this->request->getPost('model_id'),
            'fiting_id'       => $this->request->getPost('fiting_id'),
            'daya_id'         => $this->request->getPost('daya_id'),
            'jumlah_mata_id'  => $this->request->getPost('jumlah_mata_id'),
            'price'           => $this->request->getPost('price'),
            'stock'           => $this->request->getPost('stock'),
            'kode_ky'         => session('kode_ky'),
            'otoritas'        => 'T',
        ];

        $mainModel = $this->getModel('ProductModel', 'default');
        if ($mainModel->save($dataToSave)) {
            $insertedID = $mainModel->getInsertID();
            $dataToSave['id'] = $insertedID;
            $backupModel = $this->getModel('ProductModel', 'db1');
            try {
                $backupModel->insert($dataToSave);
            } catch (\Exception $e) {
                $mainModel->delete($insertedID, true);
                log_message('error', 'Backup database (product) failed: ' . $e->getMessage());
                return redirect()->to('/products')->with('error', 'Gagal menyimpan data backup. Data utama dibatalkan.');
            }
        } else {
            return redirect()->to('/products')->with('error', 'Gagal menyimpan data utama produk.');
        }
        return redirect()->to('/products')->with('success', 'Produk berhasil ditambahkan di kedua database.');
    }

    public function edit($id)
    {
        $productModel = $this->getModel('ProductModel', 'default');
        $categoryModel = $this->getModel('CategoryModel', 'default');
        $satuanModel = $this->getModel('SatuanModel', 'default');
        $jenisModel = $this->getModel('JenisModel', 'default');

        $product = $productModel->find($id);
        if (empty($product)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan.');
        }

        // Validasi otoritas: hanya bisa edit jika otoritas produk = 'T'
        if (($product['otoritas'] ?? 'F') !== 'T') {
            return redirect()->to('/products')->with('error', 'Edit produk hanya diizinkan jika sudah diotorisasi oleh Departemen General. Silakan minta otorisasi ke Departemen General.');
        }

        $data = [
            'title'      => 'Edit Produk',
            'product'    => $product,
            'categories' => $categoryModel->findAll(),
            'satuans'    => $satuanModel->findAll(),
            'jenis'      => $jenisModel->findAll(),
        ];

        return view('products/edit', $data);
    }

    public function update($id)
    {
        $product = $this->getModel('ProductModel', 'default')->find($id);
        if (empty($product) || ($product['otoritas'] ?? 'F') !== 'T') {
            return redirect()->to('/products')->with('error', 'Akses update produk ini membutuhkan otoritas dari departemen yang berwenang.');
        }
        $rules = [
            'name'        => 'required|min_length[3]',
            'category_id' => 'required|numeric',
            'satuan_id'   => 'required|numeric',
            'price'       => 'required|numeric',
            'stock'       => 'required|numeric'
        ];
        if (!$this->validate($rules)) {
            return redirect()->to('/products/' . $id . '/edit')->withInput()->with('errors', $this->validator->getErrors());
        }
        $dataToUpdate = [
            'name'            => $this->request->getPost('name'),
            'category_id'     => $this->request->getPost('category_id'),
            'satuan_id'       => $this->request->getPost('satuan_id'),
            'jenis_id'        => $this->request->getPost('jenis_id'),
            'pelengkap_id'    => $this->request->getPost('pelengkap_id'),
            'gondola_id'      => $this->request->getPost('gondola_id'),
            'merk_id'         => $this->request->getPost('merk_id'),
            'warna_sinar_id'  => $this->request->getPost('warna_sinar_id'),
            'ukuran_barang_id'=> $this->request->getPost('ukuran_barang_id'),
            'voltase_id'      => $this->request->getPost('voltase_id'),
            'dimensi_id'      => $this->request->getPost('dimensi_id'),
            'warna_body_id'   => $this->request->getPost('warna_body_id'),
            'warna_bibir_id'  => $this->request->getPost('warna_bibir_id'),
            'kaki_id'         => $this->request->getPost('kaki_id'),
            'model_id'        => $this->request->getPost('model_id'),
            'fiting_id'       => $this->request->getPost('fiting_id'),
            'daya_id'         => $this->request->getPost('daya_id'),
            'jumlah_mata_id'  => $this->request->getPost('jumlah_mata_id'),
            'price'           => $this->request->getPost('price'),
            'stock'           => $this->request->getPost('stock'),
            'kode_ky'         => session('kode_ky'),
            'otoritas'        => null,
        ];
        $this->getModel('ProductModel', 'default')->update($id, $dataToUpdate);
        try {
            $this->getModel('ProductModel', 'db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (product update) failed: ' . $e->getMessage());
        }
        $this->getModel('ProductModel', 'default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getModel('ProductModel', 'db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (product kode_ky update) failed: ' . $e->getMessage());
        }
        $this->getModel('ProductModel', 'default')->update($id, ['otoritas' => null]);
        try {
            $this->getModel('ProductModel', 'db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (product otoritas clear) failed: ' . $e->getMessage());
        }
        return redirect()->to('/products')->with('success', 'Produk berhasil diperbarui.');
    }

    public function delete($id)
    {
        $product = $this->getModel('ProductModel', 'default')->find($id);
        if (empty($product) || ($product['otoritas'] ?? 'F') !== 'T') {
            return redirect()->to('/products')->with('error', 'Akses hapus produk ini membutuhkan otoritas dari departemen yang berwenang.');
        }
        $this->getModel('ProductModel', 'default')->update($id, ['kode_ky' => session('kode_ky')]);
        try {
            $this->getModel('ProductModel', 'db1')->update($id, ['kode_ky' => session('kode_ky')]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (product kode_ky update before delete) failed: ' . $e->getMessage());
        }
        $this->getModel('ProductModel', 'default')->delete($id);
        try {
            $this->getModel('ProductModel', 'db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (product delete) failed: ' . $e->getMessage());
        }
        $this->getModel('ProductModel', 'default')->update($id, ['otoritas' => null]);
        try {
            $this->getModel('ProductModel', 'db1')->update($id, ['otoritas' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (product otoritas clear after delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/products')->with('success', 'Produk berhasil dihapus dari kedua database.');
    }

    public function testInsertMinimal()
    {
        $dataToSave = [
            'name'        => 'Produk Test Hardcode',
            'category_id' => 1, // pastikan ID 1 ada di tabel categories
            'satuan_id'   => 1, // pastikan ID 1 ada di tabel satuan
            'price'       => 1000,
            'stock'       => 10,
            'kode_ky'     => 'test',
            'otoritas'    => 'T',
        ];
        $mainModel = $this->getModel('ProductModel', 'default');
        $backupModel = $this->getModel('ProductModel', 'db1');
        $result = $mainModel->save($dataToSave);
        $insertedID = $mainModel->getInsertID();
        $dataToSave['id'] = $insertedID;
        $resultBackup = $backupModel->insert($dataToSave);
        echo "Insert utama: ".$result."; Insert backup: ".$resultBackup."; ID: ".$insertedID; exit;
    }
}
