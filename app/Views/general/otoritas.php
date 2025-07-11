<?php
// View: app/Views/general/otoritas.php
// Menu Otoritas Kategori
?>

<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>
<div class="content-header">
    <?= $this->include('partials/_otoritas_menu') ?>
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