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
                <?= $this->include('partials/_otoritas_menu') ?>
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