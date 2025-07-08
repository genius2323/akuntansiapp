<?php
// View: app/Views/jenis/edit.php
?>
<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Jenis</h1>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <?php if (session('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach (session('errors') as $error): ?>
                    <div><?= $error ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <form method="post" action="<?= site_url('jenis/' . $jenis['id']) ?>">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label for="name">Nama Jenis</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= esc($jenis['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description"><?= esc($jenis['description']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= site_url('jenis') ?>" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>