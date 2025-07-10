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
        log_message('debug', 'MASUK CONTROLLER CREATE, METHOD: ' . $this->request->getMethod());
        log_message('debug', 'Session ID: ' . session_id());
        log_message('debug', 'Session on create: ' . print_r(session()->get(), true));


        if ($this->request->getMethod() === 'post') {
            // Jika POST, proses simpan produk
            $rules = [
                'name'        => 'required|min_length[3]',
                'category_id' => 'required|numeric',
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
            ];

            $mainModel = $this->getModel('ProductModel', 'default');
            log_message('debug', '[PRODUK] Akan simpan ke DB utama: ' . print_r($dataToSave, true));
            if ($mainModel->save($dataToSave)) {
                $insertedID = $mainModel->getInsertID();
                $dataToSave['id'] = $insertedID;
                log_message('debug', '[PRODUK] Simpan ke DB utama berhasil, ID: ' . $insertedID);

                $backupModel = $this->getModel('ProductModel', 'db1');
                try {
                    log_message('debug', '[PRODUK] Akan simpan ke DB backup: ' . print_r($dataToSave, true));
                    $backupModel->insert($dataToSave);
                    log_message('debug', '[PRODUK] Simpan ke DB backup berhasil');
                } catch (\Exception $e) {
                    $mainModel->delete($insertedID, true);
                    log_message('error', '[PRODUK] Backup database (product) failed: ' . $e->getMessage());
                    return redirect()->to('/products')->with('error', 'Gagal menyimpan data backup produk.');
                }
            } else {
                log_message('error', '[PRODUK] Gagal simpan ke DB utama: ' . print_r($mainModel->errors(), true));
                return redirect()->to('/products')->with('error', 'Gagal menyimpan data utama produk.');
            }

            return redirect()->to('/products')->with('success', 'Produk berhasil ditambahkan.');
        }

        // Jika GET, tampilkan form dengan semua data master
        $errors = session('errors') ?? [];
        $categoryModel = $this->getModel('CategoryModel', 'default');
        $satuanModel = $this->getModel('SatuanModel', 'default');
        $jenisModel = $this->getModel('JenisModel', 'default');
        $pelengkapModel = $this->getModel('PelengkapModel', 'default');
        $gondolaModel = $this->getModel('GondolaModel', 'default');
        $merkModel = $this->getModel('MerkModel', 'default');
        $warnaSinarModel = $this->getModel('WarnaSinarModel', 'default');
        $ukuranBarangModel = $this->getModel('UkuranBarangModel', 'default');
        $voltaseModel = $this->getModel('VoltaseModel', 'default');
        $dimensiModel = $this->getModel('DimensiModel', 'default');
        $warnaBodyModel = $this->getModel('WarnaBodyModel', 'default');
        $warnaBibirModel = $this->getModel('WarnaBibirModel', 'default');
        $kakiModel = $this->getModel('KakiModel', 'default');
        $modelModel = $this->getModel('ModelModel', 'default');
        $fitingModel = $this->getModel('FitingModel', 'default');
        $dayaModel = $this->getModel('DayaModel', 'default');
        $jumlahMataModel = $this->getModel('JumlahMataModel', 'default');

        $data = [
            'categories'     => $categoryModel->findAll(),
            'satuans'        => $satuanModel->findAll(),
            'jenis'          => $jenisModel->findAll(),
            'pelengkaps'     => $pelengkapModel->findAll(),
            'gondolas'       => $gondolaModel->findAll(),
            'merks'          => $merkModel->findAll(),
            'warna_sinars'   => $warnaSinarModel->findAll(),
            'ukuran_barangs' => $ukuranBarangModel->findAll(),
            'voltases'       => $voltaseModel->findAll(),
            'dimensis'       => $dimensiModel->findAll(),
            'warna_bodys'    => $warnaBodyModel->findAll(),
            'warna_bibirs'   => $warnaBibirModel->findAll(),
            'kakis'          => $kakiModel->findAll(),
            'models'         => $modelModel->findAll(),
            'fitings'        => $fitingModel->findAll(),
            'dayas'          => $dayaModel->findAll(),
            'jumlah_matas'   => $jumlahMataModel->findAll(),
            'errors'         => $errors,
        ];
        return view('products/create', $data);
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
        $rules = [
            'name'        => 'required|min_length[3]',
            'category_id' => 'required|numeric',
            'price'       => 'required|numeric',
            'stock'       => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $product = $this->getModel('ProductModel', 'default')->find($id);
        if (!$product) {
            return redirect()->to('/products')->with('error', 'Produk tidak ditemukan.');
        }

        // Validasi otoritas produk
        if (($product['otoritas'] ?? 'F') !== 'T') {
            return redirect()->to('/products')->with('error', 'Edit produk hanya diizinkan jika sudah diotorisasi oleh Departemen General. Silakan minta otorisasi ke Departemen General.');
        }

        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'category_id' => $this->request->getPost('category_id'),
            'satuan_id'   => $this->request->getPost('satuan_id'),
            'jenis_id'    => $this->request->getPost('jenis_id'),
            'price'       => $this->request->getPost('price'),
            'stock'       => $this->request->getPost('stock'),
            'otoritas'    => null, // reset otoritas setelah edit
            'kode_ky'     => session('kode_ky'),
        ];

        $this->getModel('ProductModel', 'default')->update($id, $dataToUpdate);
        try {
            $this->getModel('ProductModel', 'db1')->update($id, $dataToUpdate);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (product update) failed: ' . $e->getMessage());
        }

        return redirect()->to('/products')->with('success', 'Produk berhasil diperbarui.');
    }

    public function delete($id)
    {
        $product = $this->getModel('ProductModel', 'default')->find($id);
        if (!$product) {
            return redirect()->to('/products')->with('error', 'Produk tidak ditemukan.');
        }
        // Validasi otoritas produk
        if (($product['otoritas'] ?? 'F') !== 'T') {
            return redirect()->to('/products')->with('error', 'Hapus produk hanya diizinkan jika sudah diotorisasi oleh Departemen General. Silakan minta otorisasi ke Departemen General.');
        }
        $dataToUpdate = [
            'otoritas' => null,
            'kode_ky'  => session('kode_ky'),
        ]; // reset otoritas & catat user hapus
        $this->getModel('ProductModel', 'default')->update($id, $dataToUpdate);
        $this->getModel('ProductModel', 'default')->delete($id);
        try {
            $this->getModel('ProductModel', 'db1')->update($id, $dataToUpdate);
            $this->getModel('ProductModel', 'db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (product delete) failed: ' . $e->getMessage());
        }
        return redirect()->to('/products')->with('success', 'Produk berhasil dihapus.');
    }
}
