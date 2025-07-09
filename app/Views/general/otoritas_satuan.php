<?php
// View: app/Views/general/otoritas_satuan.php
// Menu Otoritas Satuan
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