<?php
// View: app/Views/general/otoritas.php
// Menu Otoritas Kategori
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
                <!-- Tombol Klasifikasi (semua jenis) -->
                <?php
                $classifications = [
                    ['name' => 'Kategori', 'url' => 'categories', 'icon' => 'fas fa-tags'],
                    ['name' => 'Satuan', 'url' => 'satuan', 'icon' => 'fas fa-ruler-combined'],
                    ['name' => 'Jenis', 'url' => 'jenis', 'icon' => 'fas fa-boxes'],
                    ['name' => 'Pelengkap', 'url' => 'pelengkap', 'icon' => 'fas fa-puzzle-piece'],
                    ['name' => 'Gondola', 'url' => 'gondola', 'icon' => 'fas fa-store-alt'],
                    ['name' => 'Merk Barang', 'url' => 'merk', 'icon' => 'fas fa-copyright'],
                    ['name' => 'Warna Sinar', 'url' => 'warna-sinar', 'icon' => 'fas fa-lightbulb'],
                    ['name' => 'Ukuran Barang', 'url' => 'ukuran-barang', 'icon' => 'fas fa-expand-arrows-alt'],
                    ['name' => 'Voltase', 'url' => 'voltase', 'icon' => 'fas fa-bolt'],
                    ['name' => 'Dimensi', 'url' => 'dimensi', 'icon' => 'fas fa-ruler'],
                    ['name' => 'Warna Body', 'url' => 'warna-body', 'icon' => 'fas fa-palette'],
                    ['name' => 'Warna Bibir', 'url' => 'warna-bibir', 'icon' => 'fas fa-tint'],
                    ['name' => 'Kaki', 'url' => 'kaki', 'icon' => 'fas fa-shoe-prints'],
                    ['name' => 'Model', 'url' => 'model', 'icon' => 'fas fa-star'],
                    ['name' => 'Fiting', 'url' => 'fiting', 'icon' => 'fas fa-plug'],
                    ['name' => 'Daya', 'url' => 'daya', 'icon' => 'fas fa-power-off'],
                    ['name' => 'Jumlah Mata', 'url' => 'jumlah-mata', 'icon' => 'fas fa-eye'],
                ];
                ?>
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
                    ['name' => 'Produk', 'slug' => 'produk', 'icon' => 'fas fa-box', 'url' => site_url('general/otoritasProduk')],
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
                <h4 class="mb-3">Otoritas Kategori</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Status Otorisasi</th>
                                <th>Aksi Otorisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $i => $cat): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= esc($cat['name']) ?></td>
                                        <td><?= esc(isset($cat['description']) ? $cat['description'] : '-') ?></td>
                                        <td>
                                            <?php if (($cat['otoritas'] ?? null) === 'T'): ?>
                                                <span class="badge badge-success">Sudah Diotorisasi</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Belum</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="post" action="<?= site_url('general/setOtoritasKategori') ?>" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="kategori_id" value="<?= $cat['id'] ?>">
                                                <input type="hidden" name="otoritas" value="T">
                                                <button type="submit" class="btn btn-sm btn-warning" <?= (isset($cat['otoritas']) && $cat['otoritas'] === 'T') ? 'disabled' : '' ?>>
                                                    Otorisasi
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">Belum ada data kategori</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
        $('#kategori_id').select2({
            placeholder: '-- Cari & Pilih Kategori --',
            width: 'resolve',
            allowClear: true
        });
    });
</script>
<?= $this->endSection() ?>