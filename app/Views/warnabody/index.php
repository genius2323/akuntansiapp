<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>
<div class="content-header">
    <?= $this->include('partials/_klasifikasi_menu') ?>
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
        <?php if (session('errors')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <p class="mb-0"><strong>Terdapat kesalahan validasi:</strong></p>
                <ul>
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addWarnaBodyModal">
                    <i class="fas fa-plus mr-1"></i>Tambah Warna Body
                </button>
            </div>
            <div class="card-body">
                <table id="warnaBodyTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Warna Body</th>
                            <th>Deskripsi</th>
                            <th>Otoritas</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($warnabodys as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= esc($row['name']) ?></td>
                                <td><?= esc($row['description']) ?></td>
                                <td><?= ($row['otoritas'] ?? null) === 'T' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' ?></td>
                                <td>
                                    <a href="<?= site_url('warna-body/' . $row['id'] . '/edit') ?>" class="btn btn-sm btn-warning" onclick="return cekOtoritasWarnaBody(event, '<?= $row['otoritas'] ?? '' ?>');">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= site_url('warna-body/' . $row['id']) ?>" method="post" class="d-inline" onsubmit="return cekOtoritasWarnaBody(event, '<?= $row['otoritas'] ?? '' ?>');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
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

<!-- Modal Tambah Warna Body -->
<div class="modal fade" id="addWarnaBodyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('warna-body/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Warna Body Baru</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Warna Body</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
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
    function cekOtoritasWarnaBody(event, otoritas) {
        if (otoritas !== 'T') {
            alert('Akses edit/delete warna body ini membutuhkan otoritas dari departemen yang berwenang. Silakan minta otoritas terlebih dahulu.');
            if (event) event.preventDefault();
            return false;
        }
        return true;
    }
    $(function() {
        $('#warnaBodyTable').DataTable();
    });
</script>
<?= $this->endSection() ?>
