<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1><?= esc($page_heading) ?></h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Info Box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Selamat Datang</h3>
                <p><?= esc($user['username'] ?? session('username')) ?></p>
            </div>
            <div class="icon">
                <i class="fas fa-user"></i>
            </div>
        </div>

        <!-- Departemen Specific Content -->
        <?php if(session('department_id') == 1): ?>
            <!-- POS Content -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fitur Kasir</h3>
                </div>
                <div class="card-body">
                    <a href="/transactions/new" class="btn btn-primary">
                        <i class="fas fa-cash-register mr-2"></i> Transaksi Baru
                    </a>
                </div>
            </div>

        <?php elseif(session('department_id') == 2): ?>
            <!-- Accounting Content -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fitur Akunting</h3>
                </div>
                <div class="card-body">
                    <a href="/reports" class="btn btn-success">
                        <i class="fas fa-file-invoice-dollar mr-2"></i> Laporan Keuangan
                    </a>
                </div>
            </div>

        <?php else: ?>
            <!-- General/Owner Content -->
            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>150</h3>
                            <p>Total Produk</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- ... -->
                </div>
            </div>

            <!-- Tambahan untuk tabel penjualan -->
            <div class="card mb-3">
                <div class="card-header">
                    <a href="<?= site_url('penjualan') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Input Penjualan
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Nota</th>
                                    <th>Tanggal Nota</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($penjualans)): ?>
                                    <?php foreach ($penjualans as $i => $row): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= esc($row['nomor_nota']) ?></td>
                                            <td><?= esc($row['tanggal_nota']) ?></td>
                                            <td><?= esc($row['customer']) ?></td>
                                            <td><?= number_format($row['grand_total'], 0, ',', '.') ?></td>
                                            <td>
                                                <a href="<?= site_url('penjualan/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <form action="<?= site_url('penjualan/delete/' . $row['id']) ?>" method="post" style="display:inline;">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center">Belum ada data penjualan</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>