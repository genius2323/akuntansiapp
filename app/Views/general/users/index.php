<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $title ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('general') ?>">General</a></li>
                    <li class="breadcrumb-item active">Manajemen User</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <?php if (session('message')): ?>
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <?= session('message') ?>
    </div>
    <?php endif; ?>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
                    <i class="fas fa-plus mr-2"></i>Tambah User
                </button>
            </div>
            <div class="card-body">
                <table id="usersTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kode KY</th>
                            <th>Username</th>
                            <th>Departemen</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= esc($user['kode_ky']) ?></td>
                            <td><?= esc($user['username']) ?></td>
                            <td><?= esc($user['department_name'] ?? 'N/A') ?></td>
                            <td><?= esc($user['alamat']) ?></td>
                            <td>
                                <!-- Tombol Edit -->
                                <a href="<?= base_url("users/edit/$user[id]") ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Tombol Soft Delete -->
                                <form action="<?= base_url("users/delete/$user[id]") ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Arsipkan user ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
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

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('users/create') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode KY</label>
                        <input type="text" name="kode_ky" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Departemen</label>
                        <select name="department_id" class="form-control" required>
                            <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>"><?= $dept['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>No. KTP</label>
                        <input type="text" name="noktp" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function () {
        $('#usersTable').DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    });
</script>
<?= $this->endSection() ?>