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
                <p><?= esc($user['username']) ?></p>
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
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>