<?= $this->extend('layouts/adminlte');?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header bg-danger">
        <h3 class="card-title">
            <i class="fas fa-trash-alt mr-2"></i>
            Data Terarsip - User
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('users') ?>" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Username</th>
                    <th>Dihapus Pada</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php // PERBAIKAN 1: Menggunakan variabel $deletedUsers ?>
                <?php foreach ($deletedUsers as $user): ?> 
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($user['deleted_at'])) ?></td>
                    <td class="text-center">
                        <?php // PERBAIKAN 2: Mengubah route form ?>
                        <form action="<?= base_url('users/restore/'.$user['id']) ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-success" title="Pulihkan">
                                <i class="fas fa-trash-restore"></i>
                            </button>
                        </form>
                        <form action="<?= base_url('users/force-delete/'.$user['id']) ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen" onclick="return confirm('Yakin hapus permanen?')">
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
<?= $this->endSection() ?>