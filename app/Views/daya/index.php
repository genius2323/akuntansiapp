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
                <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addDayaModal">
                    <i class="fas fa-plus mr-1"></i>Tambah Daya
                </button>
            </div>
            <div class="card-body">
                <table id="dayaTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Daya</th>
                            <th>Deskripsi</th>
                            <th>Otoritas</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dayas as $daya): ?>
                            <tr>
                                <td><?= $daya['id'] ?></td>
                                <td><?= esc($daya['name']) ?></td>
                                <td><?= esc($daya['description']) ?></td>
                                <td><?= ($daya['otoritas'] ?? null) === 'T' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' ?></td>
                                <td>
                                    <a href="<?= site_url('daya/' . $daya['id'] . '/edit') ?>" class="btn btn-sm btn-warning" onclick="return cekOtoritasDaya(event, '<?= $daya['otoritas'] ?? '' ?>');">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= site_url('daya/' . $daya['id']) ?>" method="post" class="d-inline" onsubmit="return cekOtoritasDaya(event, '<?= $daya['otoritas'] ?? '' ?>');">
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

<!-- Modal Tambah Daya -->
<div class="modal fade" id="addDayaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('daya/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Daya Baru</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Daya</label>
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
    function cekOtoritasDaya(event, otoritas) {
        if (otoritas !== 'T') {
            alert('Akses edit/delete daya ini membutuhkan otoritas dari departemen yang berwenang. Silakan minta otoritas terlebih dahulu.');
            if (event) event.preventDefault();
            return false;
        }
        return true;
    }
    $(function() {
        $('#dayaTable').DataTable();
    });
</script>
<?= $this->endSection() ?>