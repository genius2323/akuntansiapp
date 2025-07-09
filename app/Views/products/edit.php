<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1><?= esc($title) ?></h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <!-- PERBAIKAN: Mengubah action form update -->
                <form action="<?= site_url('products/' . $product['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="name" class="form-control" value="<?= esc($product['name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category_id" class="form-control" required>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= ($category['id'] == $product['category_id']) ? 'selected' : '' ?>>
                                <?= esc($category['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="price" class="form-control" value="<?= esc($product['price']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stock" class="form-control" value="<?= esc($product['stock']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Satuan</label>
                        <select name="satuan_id" class="form-control">
                            <option value="">-- Pilih Satuan --</option>
                            <?php foreach ($satuans as $satuan): ?>
                            <option value="<?= $satuan['id'] ?>" <?= ($satuan['id'] == ($product['satuan_id'] ?? '')) ? 'selected' : '' ?>>
                                <?= esc($satuan['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis</label>
                        <select name="jenis_id" class="form-control">
                            <option value="">-- Pilih Jenis --</option>
                            <?php foreach ($jenis as $j): ?>
                            <option value="<?= $j['id'] ?>" <?= ($j['id'] == ($product['jenis_id'] ?? '')) ? 'selected' : '' ?>>
                                <?= esc($j['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <a href="<?= site_url('products') ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
