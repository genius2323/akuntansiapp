<?php
// View: app/Views/general/otoritas_jenis.php
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
                    // ...tambahkan lainnya jika perlu
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
                <h4 class="mb-3">Otoritas Jenis</h4>
                <form method="post" action="<?= site_url('general/setOtoritasJenis') ?>">
                    <div class="form-group">
                        <label for="jenis">Pilih Jenis:</label>
                        <select name="jenis_id" id="jenis_id" class="form-control select2" required>
                            <option value="">-- Cari & Pilih Jenis --</option>
                            <?php foreach ($jenis as $j): ?>
                                <option value="<?= $j['id'] ?>"><?= esc($j['name']) ?></option>
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
        $('#jenis_id').select2({
            placeholder: '-- Cari & Pilih Jenis --',
            width: 'resolve',
            allowClear: true
        });
    });
</script>
<?= $this->endSection() ?>