<?php
// View: app/Views/general/otoritas_satuan.php
// Menu Otoritas Satuan
?>

<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!-- Heading dipindah ke bawah -->
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
        <div class="card">
            <div class="card-body">
                <h3 class="mb-3">Otoritas</h3>
                <?php
                $uri = service('request')->getUri();
                $segments = $uri->getSegments();
                $current = isset($segments[1]) ? $segments[1] : 'kategori';
                $otoritasMenus = [
                    ['name' => 'Kategori', 'slug' => 'kategori', 'icon' => 'fas fa-tags', 'url' => site_url('general/otoritasKategori')],
                    ['name' => 'Satuan', 'slug' => 'satuan', 'icon' => 'fas fa-ruler-combined', 'url' => site_url('general/otoritasSatuan')],
                    ['name' => 'Jenis', 'slug' => 'jenis', 'icon' => 'fas fa-boxes', 'url' => site_url('general/otoritasJenis')],
                    ['name' => 'Pelengkap', 'slug' => 'pelengkap', 'icon' => 'fas fa-puzzle-piece', 'url' => site_url('general/otoritasPelengkap')],
                    ['name' => 'Gondola', 'slug' => 'gondola', 'icon' => 'fas fa-store-alt', 'url' => site_url('general/otoritasGondola')],
                    ['name' => 'Merk Barang', 'slug' => 'merk', 'icon' => 'fas fa-copyright', 'url' => site_url('general/otoritasMerk')],
                    ['name' => 'Warna Sinar', 'slug' => 'warna-sinar', 'icon' => 'fas fa-lightbulb', 'url' => site_url('general/otoritasWarnaSinar')],
                    ['name' => 'Ukuran Barang', 'slug' => 'ukuran-barang', 'icon' => 'fas fa-expand-arrows-alt', 'url' => site_url('general/otoritasUkuranBarang')],
                    ['name' => 'Voltase', 'slug' => 'voltase', 'icon' => 'fas fa-bolt', 'url' => site_url('general/otoritasVoltase')],
                    ['name' => 'Dimensi', 'slug' => 'dimensi', 'icon' => 'fas fa-ruler', 'url' => site_url('general/otoritasDimensi')],
                    ['name' => 'Warna Body', 'slug' => 'warna-body', 'icon' => 'fas fa-palette', 'url' => site_url('general/otoritasWarnaBody')],
                    ['name' => 'Warna Bibir', 'slug' => 'warna-bibir', 'icon' => 'fas fa-tint', 'url' => site_url('general/otoritasWarnaBibir')],
                    ['name' => 'Kaki', 'slug' => 'kaki', 'icon' => 'fas fa-shoe-prints', 'url' => site_url('general/otoritasKaki')],
                    ['name' => 'Model', 'slug' => 'model', 'icon' => 'fas fa-star', 'url' => site_url('general/otoritasModel')],
                    ['name' => 'Fiting', 'slug' => 'fiting', 'icon' => 'fas fa-plug', 'url' => site_url('general/otoritasFiting')],
                    ['name' => 'Daya', 'slug' => 'daya', 'icon' => 'fas fa-power-off', 'url' => site_url('general/otoritasDaya')],
                    ['name' => 'Jumlah Mata', 'slug' => 'jumlah-mata', 'icon' => 'fas fa-eye', 'url' => site_url('general/otoritasJumlahMata')],
                ];
                ?>
                <?php foreach ($otoritasMenus as $item): ?>
                    <a href="<?= $item['url'] ?>" class="btn btn-app mb-2 <?= ($current === $item['slug'] || $current === 'otoritas' . ucfirst($item['slug'])) ? 'bg-success text-white' : 'bg-secondary' ?>">
                        <i class="<?= $item['icon'] ?>"></i> <?= $item['name'] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3">Otoritas Satuan</h4>
                <form method="post" action="<?= site_url('general/setOtoritasSatuan') ?>">
                    <div class="form-group">
                        <label for="satuan">Pilih Satuan:</label>
                        <select name="satuan_id" id="satuan_id" class="form-control select2" required>
                            <option value="">-- Cari & Pilih Satuan --</option>
                            <?php foreach ($satuans as $sat): ?>
                                <option value="<?= $sat['id'] ?>"><?= esc($sat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="batas_tanggal_sistem">Batas Tanggal Sistem:</label>
                        <input type="date" class="form-control" id="batas_tanggal_sistem" name="batas_tanggal_sistem" required>
                    </div>
                    <div class="form-group">
                        <label for="mode_batas_tanggal">Mode Batas Tanggal:</label>
                        <select class="form-control" id="mode_batas_tanggal" name="mode_batas_tanggal" required>
                            <option value="manual">Manual</option>
                            <option value="automatic">Automatic (H-2)</option>
                        </select>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="otoritas" name="otoritas" value="T">
                        <label class="form-check-label" for="otoritas">Izinkan Edit/Hapus (Otoritas Aktif)</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Set Otoritas</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<!-- Select2 CSS & JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#satuan_id').select2({
            placeholder: '-- Cari & Pilih Satuan --',
            width: 'resolve',
            allowClear: true
        });
    });
</script>
<?= $this->endSection() ?>