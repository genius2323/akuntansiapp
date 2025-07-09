<?php
// View: app/Views/general/otoritas_user.php
// Menu Otoritas User
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
                <h4 class="mb-3">Otoritas User</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Departemen</th>
                                <th>Status Otorisasi</th>
                                <th>Aksi Otorisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $i => $user): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= esc($user['username']) ?></td>
                                        <td><?= esc($user['department_name'] ?? '-') ?></td>
                                        <td>
                                            <?php if (($user['otoritas'] ?? null) === 'T'): ?>
                                                <span class="badge badge-success">Sudah Diotorisasi</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Belum</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="post" action="<?= site_url('general/setOtoritasUser') ?>" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <input type="hidden" name="otoritas" value="T">
                                                <button type="submit" class="btn btn-sm btn-warning" <?= (isset($user['otoritas']) && $user['otoritas'] === 'T') ? 'disabled' : '' ?>>
                                                    Otorisasi
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">Belum ada data user</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
