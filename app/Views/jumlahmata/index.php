<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <?= $this->include('partials/_klasifikasi_menu') ?>
</div>
<section class="content">
    <div class="container-fluid">
        <!-- TOMBOL KLASIFIKASI DITAMBAHKAN DI SINI -->

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
                <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addJumlahMataModal">
                    <i class="fas fa-plus mr-1"></i>Tambah Jumlah Mata
                </button>
            </div>
            <div class="card-body">
                <table id="jumlahMataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Jumlah Mata</th>
                            <th>Deskripsi</th>
                            <th>Otoritas</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jumlahmatas as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= esc($row['name']) ?></td>
                                <td><?= esc($row['description']) ?></td>
                                <td><?= ($row['otoritas'] ?? null) === 'T' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' ?></td>
                                <td>
                                    <a href="<?= site_url('jumlah-mata/' . $row['id'] . '/edit') ?>" class="btn btn-sm btn-warning" onclick="return cekOtoritasJumlahMata(event, '<?= $row['otoritas'] ?? '' ?>');">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= site_url('jumlah-mata/' . $row['id']) ?>" method="post" class="d-inline" onsubmit="return cekOtoritasJumlahMata(event, '<?= $row['otoritas'] ?? '' ?>');">
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

    <!-- Modal Tambah Jumlah Mata -->
    <div class="modal fade" id="addJumlahMataModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= site_url('jumlah-mata/create') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jumlah Mata Baru</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Jumlah Mata</label>
                            <input type="text" name="name" class="form-control" required value="<?= old('name') ?>">
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"><?= old('description') ?></textarea>
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
    function cekOtoritasJumlahMata(event, otoritas) {
        if (!otoritas) {
            alert('Akses edit/delete jumlah mata ini membutuhkan otoritas dari departemen yang berwenang. Silakan minta otoritas terlebih dahulu.');
            if (event) event.preventDefault();
            return false;
        }
        return true;
    }
    $(function() {
        $('#jumlahMataTable').DataTable();
    });
</script>
<?= $this->endSection() ?>
</section>