
<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1 class="mb-4">Tambah Produk</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form action="<?= site_url('products/create') ?>" method="post">
                    <?= csrf_field() ?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
        </div>
        <div class="form-group">
            <label>Harga</label>
            <input type="number" name="price" class="form-control" value="<?= old('price') ?>" required>
        </div>
        <div class="form-group">
            <label>Kategori</label>
            <select name="category_id" class="form-control" required>
                <option value="">Pilih Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Jenis</label>
            <select name="jenis_id" class="form-control">
                <option value="">Pilih Jenis</option>
                <?php foreach ($jenis as $j): ?>
                    <option value="<?= $j['id'] ?>" <?= old('jenis_id') == $j['id'] ? 'selected' : '' ?>><?= esc($j['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Satuan</label>
            <select name="satuan_id" class="form-control">
                <option value="">Pilih Satuan</option>
                <?php foreach ($satuans as $sat): ?>
                    <option value="<?= $sat['id'] ?>" <?= old('satuan_id') == $sat['id'] ? 'selected' : '' ?>><?= esc($sat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Pelengkap</label>
            <select name="pelengkap_id" class="form-control">
                <option value="">Pilih Pelengkap</option>
                <?php foreach ($pelengkaps as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= old('pelengkap_id') == $p['id'] ? 'selected' : '' ?>><?= esc($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Gondola</label>
            <select name="gondola_id" class="form-control">
                <option value="">Pilih Gondola</option>
                <?php foreach ($gondolas as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= old('gondola_id') == $g['id'] ? 'selected' : '' ?>><?= esc($g['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Merk</label>
            <select name="merk_id" class="form-control">
                <option value="">Pilih Merk</option>
                <?php foreach ($merks as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= old('merk_id') == $m['id'] ? 'selected' : '' ?>><?= esc($m['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stock" class="form-control" value="<?= old('stock') ?>" required>
        </div>
        <div class="form-group">
            <label>Warna Sinar</label>
            <select name="warna_sinar_id" class="form-control">
                <option value="">Pilih Warna Sinar</option>
                <?php foreach ($warna_sinars as $ws): ?>
                    <option value="<?= $ws['id'] ?>" <?= old('warna_sinar_id') == $ws['id'] ? 'selected' : '' ?>><?= esc($ws['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Ukuran Barang</label>
            <select name="ukuran_barang_id" class="form-control">
                <option value="">Pilih Ukuran Barang</option>
                <?php foreach ($ukuran_barangs as $ub): ?>
                    <option value="<?= $ub['id'] ?>" <?= old('ukuran_barang_id') == $ub['id'] ? 'selected' : '' ?>><?= esc($ub['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Voltase</label>
            <select name="voltase_id" class="form-control">
                <option value="">Pilih Voltase</option>
                <?php foreach ($voltases as $v): ?>
                    <option value="<?= $v['id'] ?>" <?= old('voltase_id') == $v['id'] ? 'selected' : '' ?>><?= esc($v['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Dimensi</label>
            <select name="dimensi_id" class="form-control">
                <option value="">Pilih Dimensi</option>
                <?php foreach ($dimensis as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= old('dimensi_id') == $d['id'] ? 'selected' : '' ?>><?= esc($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Warna Body</label>
            <select name="warna_body_id" class="form-control">
                <option value="">Pilih Warna Body</option>
                <?php foreach ($warna_bodys as $wb): ?>
                    <option value="<?= $wb['id'] ?>" <?= old('warna_body_id') == $wb['id'] ? 'selected' : '' ?>><?= esc($wb['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Warna Bibir</label>
            <select name="warna_bibir_id" class="form-control">
                <option value="">Pilih Warna Bibir</option>
                <?php foreach ($warna_bibirs as $wbb): ?>
                    <option value="<?= $wbb['id'] ?>" <?= old('warna_bibir_id') == $wbb['id'] ? 'selected' : '' ?>><?= esc($wbb['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Kaki</label>
            <select name="kaki_id" class="form-control">
                <option value="">Pilih Kaki</option>
                <?php foreach ($kakis as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= old('kaki_id') == $k['id'] ? 'selected' : '' ?>><?= esc($k['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Model</label>
            <select name="model_id" class="form-control">
                <option value="">Pilih Model</option>
                <?php foreach ($models as $mdl): ?>
                    <option value="<?= $mdl['id'] ?>" <?= old('model_id') == $mdl['id'] ? 'selected' : '' ?>><?= esc($mdl['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Fiting</label>
            <select name="fiting_id" class="form-control">
                <option value="">Pilih Fiting</option>
                <?php foreach ($fitings as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= old('fiting_id') == $f['id'] ? 'selected' : '' ?>><?= esc($f['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Daya</label>
            <select name="daya_id" class="form-control">
                <option value="">Pilih Daya</option>
                <?php foreach ($dayas as $dy): ?>
                    <option value="<?= $dy['id'] ?>" <?= old('daya_id') == $dy['id'] ? 'selected' : '' ?>><?= esc($dy['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Jumlah Mata</label>
            <select name="jumlah_mata_id" class="form-control">
                <option value="">Pilih Jumlah Mata</option>
                <?php foreach ($jumlah_matas as $jm): ?>
                    <option value="<?= $jm['id'] ?>" <?= old('jumlah_mata_id') == $jm['id'] ? 'selected' : '' ?>><?= esc($jm['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>
                    <div class="form-group mt-3">
                        <a href="<?= site_url('products') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
