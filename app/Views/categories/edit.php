<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1><?= esc($title) ?></h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('categories/' . $category['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" class="form-control" value="<?= esc($category['name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"><?= esc($category['description']) ?></textarea>
                    </div>
                    
                    <a href="<?= site_url('categories') ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
