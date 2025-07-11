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
                <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addDimensiModal">
                    <i class="fas fa-plus mr-1"></i>Tambah Dimensi
                </button>
            </div>
            <div class="card-body">
                <table id="dimensiTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Dimensi</th>
                            <th>Deskripsi</th>
                            <th>Otoritas</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dimensis as $dimensi): ?>
                            <tr>
                                <td><?= $dimensi['id'] ?></td>
                                <td><?= esc($dimensi['name']) ?></td>
                                <td><?= esc($dimensi['description']) ?></td>
                                <td><?= ($dimensi['otoritas'] ?? null) === 'T' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' ?></td>
                                <td>
                                    <a href="<?= site_url('dimensi/' . $dimensi['id'] . '/edit') ?>" class="btn btn-sm btn-warning" onclick="return cekOtoritasDimensi(event, '<?= $dimensi['otoritas'] ?? '' ?>');">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= site_url('dimensi/' . $dimensi['id']) ?>" method="post" class="d-inline" onsubmit="return cekOtoritasDimensi(event, '<?= $dimensi['otoritas'] ?? '' ?>');">
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

<!-- Modal Tambah Dimensi -->
<div class="modal fade" id="addDimensiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('dimensi/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Dimensi Baru</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Dimensi</label>
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
    function cekOtoritasDimensi(event, otoritas) {
        if (otoritas !== 'T') {
            alert('Akses edit/delete dimensi ini membutuhkan otoritas dari departemen yang berwenang. Silakan minta otoritas terlebih dahulu.');
            if (event) event.preventDefault();
            return false;
        }
        return true;
    }
    $(function() {
        $('#dimensiTable').DataTable();
    });
</script>
<?= $this->endSection() ?>