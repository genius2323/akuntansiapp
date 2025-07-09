<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1><?= $title ?></h1>
    </div>
</div>

<section class="content">
    <?php if (session('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="<?= base_url("users/update/$user[id]") ?>" method="post">
                    <div class="form-group">
                        <label>Kode KY</label>
                        <input type="text" name="kode_ky" class="form-control" value="<?= esc($user['kode_ky']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?= esc($user['username']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Departemen</label>
                        <select name="department_id" class="form-control" required>
                            <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= ($dept['id'] == $user['department_id']) ? 'selected' : '' ?>>
                                <?= esc($dept['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control"><?= esc($user['alamat']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>No. KTP</label>
                        <input type="text" name="noktp" class="form-control" value="<?= esc($user['noktp']) ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>