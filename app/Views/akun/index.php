<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Akun</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode Akun</th>
                    <th>Nama Akun</th>
                    <th>Tipe Akun</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($akun as $a): ?>
                <tr>
                    <td><?= $a['kode_akun'] ?></td>
                    <td><?= $a['nama_akun'] ?></td>
                    <td><?= $a['tipe_akun'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>