<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Batas Tanggal Sistem</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <?= session('success') ?>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('general/setBatasTanggalSistem') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="menu">Pilih Menu</label>
                        <select name="menu" id="menu" class="form-control" required>
                            <option value="">-- Pilih Menu --</option>
                            <option value="penjualan">Penjualan</option>
                            <option value="produk">Master Produk</option>
                            <option value="pembelian">Pembelian</option>
                            <option value="customer">Customer</option>
                            <option value="supplier">Supplier</option>
                            <!-- Tambahkan menu lain sesuai kebutuhan -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mode_batas_tanggal">Mode</label>
                        <select name="mode_batas_tanggal" id="mode_batas_tanggal" class="form-control" required>
                            <option value="manual">Manual</option>
                            <option value="automatic">Automatic (H-2)</option>
                        </select>
                    </div>
                    <div class="form-group" id="batas-tanggal-group">
                        <label for="batas_tanggal">Batas Tanggal</label>
                        <input type="text" name="batas_tanggal" id="batas_tanggal" class="form-control" readonly>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            flatpickr('#batas_tanggal', {
                                dateFormat: 'd/m/Y',
                                disableMobile: true,
                                allowInput: false
                            });
                        });
                    </script>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <script>
                        $(document).ready(function() {
                            function toggleBatasTanggal() {
                                var mode = $('#mode_batas_tanggal').val();
                                if (mode === 'automatic') {
                                    $('#batas-tanggal-group').hide();
                                    $('#batas_tanggal').prop('required', false);
                                    // Set value batas_tanggal ke H-2 (untuk backend)
                                    var today = new Date();
                                    today.setHours(0, 0, 0, 0);
                                    var h2 = new Date(today);
                                    h2.setDate(h2.getDate() - 2);
                                    var h2str = h2.toISOString().slice(0, 10);
                                    $('#batas_tanggal').val(h2str);
                                } else {
                                    $('#batas-tanggal-group').show();
                                    $('#batas_tanggal').prop('required', true);
                                    $('#batas_tanggal').val('');
                                }
                            }
                            $('#mode_batas_tanggal').on('change', toggleBatasTanggal);
                            toggleBatasTanggal();
                        });
                    </script>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>