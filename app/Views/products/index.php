<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <?= session('success') ?>
        </div>
        <?php endif; ?>
        
        <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <?= session('error') ?>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">
                    <i class="fas fa-plus mr-2"></i>Tambah Produk
                </button>
            </div>
            <div class="card-body">
                <table id="productsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= esc($product['name']) ?></td>
                            <td><?= esc($product['category_name']) ?></td>
                            <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                            <td><?= esc($product['stock']) ?></td>
                            <td>
                                <a href="<?= site_url('products/' . $product['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- PERBAIKAN: Mengubah action form delete -->
                                <form action="<?= site_url('products/' . $product['id']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal Tambah Produk (tidak ada perubahan) -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('products/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk Baru</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stock" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <select name="satuan_id" class="form-control">
                            <option value="">-- Pilih Satuan --</option>
                            <?php foreach ($satuans as $satuan): ?>
                            <option value="<?= $satuan['id'] ?>"><?= esc($satuan['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis</label>
                        <select name="jenis_id" class="form-control">
                            <option value="">-- Pilih Jenis --</option>
                            <?php foreach ($jenis as $j): ?>
                            <option value="<?= $j['id'] ?>"><?= esc($j['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function () {
        $('#productsTable').DataTable();
    });
</script>
<?= $this->endSection() ?>
