<?php namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;

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
        
        $data = [
            'title'      => 'Master Produk',
            'products'   => $productModel->getProductsWithCategory(),
            'categories' => $categoryModel->findAll()
        ];
        return view('products/index', $data);
    }

    public function create()
    {
        $rules = [
            'name'        => 'required|min_length[3]',
            'category_id' => 'required|numeric',
            'price'       => 'required|numeric',
            'stock'       => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/products')->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToSave = [
            'name'        => $this->request->getPost('name'),
            'category_id' => $this->request->getPost('category_id'),
            'price'       => $this->request->getPost('price'),
            'stock'       => $this->request->getPost('stock'),
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
                return redirect()->to('/products')->with('error', 'Gagal menyimpan data backup produk.');
            }
        } else {
            return redirect()->to('/products')->with('error', 'Gagal menyimpan data utama produk.');
        }

        return redirect()->to('/products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $productModel = $this->getModel('ProductModel', 'default');
        $categoryModel = $this->getModel('CategoryModel', 'default');

        $data = [
            'title'      => 'Edit Produk',
            'product'    => $productModel->find($id),
            'categories' => $categoryModel->findAll()
        ];

        if (empty($data['product'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan.');
        }

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

        $dataToUpdate = [
            'name'        => $this->request->getPost('name'),
            'category_id' => $this->request->getPost('category_id'),
            'price'       => $this->request->getPost('price'),
            'stock'       => $this->request->getPost('stock'),
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
        $this->getModel('ProductModel', 'default')->delete($id);
        
        try {
            $this->getModel('ProductModel', 'db1')->delete($id);
        } catch (\Exception $e) {
            log_message('error', 'Backup database (product delete) failed: ' . $e->getMessage());
        }
        
        return redirect()->to('/products')->with('success', 'Produk berhasil dihapus.');
    }
}
