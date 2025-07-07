<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
    <div class="content-header">
        <div class="container-fluid">
            <h1><?= esc($title) ?></h1>
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

            <div class="card">
                <div class="card-body">
                    <form action="<?= site_url('penjualan/store') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_id">Produk</label>
                                    <select name="product_id" id="product_id" class="form-control" required>
                                        <option value="">-- Pilih Produk --</option>
                                        <?php // Nanti diisi dari database. Ini contoh saja: ?>
                                        <option value="1">Produk A</option>
                                        <option value="2">Produk B</option>
                                        <option value="3">Produk C</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="quantity">Jumlah</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="price">Harga Satuan</label>
                                    <input type="number" name="price" id="price" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i> Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>