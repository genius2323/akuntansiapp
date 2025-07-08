<?php
// View: app/Views/jenis/index.php
?>
<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Master Jenis</h1>
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
        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <?= session('error') ?>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <a href="<?= site_url('jenis/create') ?>" class="btn btn-primary mb-3">Tambah Jenis</a>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Jenis</th>
                            <th>Deskripsi</th>
                            <th>Otoritas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jenis as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= esc($row['name']) ?></td>
                                <td><?= esc($row['description']) ?></td>
                                <td><?= $row['otoritas'] === 'T' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' ?></td>
                                <td>
                                    <a href="<?= site_url('jenis/' . $row['id'] . '/edit') ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="<?= site_url('jenis/' . $row['id']) ?>" method="post" style="display:inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>