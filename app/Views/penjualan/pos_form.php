<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1><?= esc($title) ?></h1>
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
                <form action="<?= isset($penjualan) ? site_url('penjualan/update/' . $penjualan['id']) : site_url('penjualan/store') ?>" method="post" id="form-penjualan">
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>Nomor Nota</label>
                            <input type="text" name="nomor_nota" class="form-control" value="<?= isset($penjualan) ? esc($penjualan['nomor_nota']) : date('YmdHis') ?>" required <?= isset($penjualan) ? 'readonly' : '' ?>>
                        </div>
                        <div class="col-md-3">
                            <label>Tanggal Nota</label>
                            <?php
                            $minDate = '';
                            $maxDate = '';
                            if (($mode_batas_tanggal ?? 'manual') === 'automatic') {
                                $maxDate = date('Y-m-d');
                                $minDate = date('Y-m-d', strtotime('-2 days'));
                            } elseif (!empty($batas_tanggal_sistem)) {
                                $minDate = $batas_tanggal_sistem;
                                $maxDate = date('Y-m-d');
                            }
                            ?>
                            <input type="text" name="tanggal_nota" id="tanggal_nota" class="form-control"
                                style="background:#f8f9fa; cursor:pointer; color:#212529;"
                                value="<?= isset($penjualan) ? date('d/m/Y', strtotime($penjualan['tanggal_nota'])) : date('d/m/Y') ?>"
                                required readonly>
                            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    flatpickr('#tanggal_nota', {
                                        dateFormat: 'd/m/Y',
                                        disableMobile: true,
                                        minDate: <?= $minDate ? "'" . date('d/m/Y', strtotime($minDate)) . "'" : 'null' ?>,
                                        maxDate: <?= $maxDate ? "'" . date('d/m/Y', strtotime($maxDate)) . "'" : 'null' ?>,
                                        allowInput: false
                                    });
                                });
                            </script>
                            <input type="hidden" id="mode_batas_tanggal" name="mode_batas_tanggal" value="<?= esc($mode_batas_tanggal ?? 'manual') ?>">
                            <input type="hidden" id="batas_tanggal_sistem" name="batas_tanggal_sistem" value="<?= esc($batas_tanggal_sistem ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Customer</label>
                            <select name="customer" id="customer" class="form-control select2" required style="width:100%">
                                <option value="">-- Pilih Customer --</option>
                                <?php if (isset($customers)): foreach ($customers as $c): ?>
                                        <option value="<?= esc($c['name']) ?>" <?= (isset($penjualan) && $penjualan['customer'] == $c['name']) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Sales</label>
                            <select name="sales" id="sales" class="form-control select2" required style="width:100%">
                                <option value="">-- Pilih Sales --</option>
                                <?php if (isset($sales_users)): foreach ($sales_users as $s): ?>
                                        <option value="<?= esc($s['username']) ?>" <?= (isset($penjualan) && $penjualan['sales'] == $s['username']) ? 'selected' : '' ?>><?= esc($s['username']) ?></option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table-barang">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Diskon</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-barang">
                                <!-- Baris barang dinamis -->
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success" id="tambah-baris" data-toggle="modal" data-target="#modalProduk"><i class="fas fa-plus"></i> Tambah Barang</button>

                        <!-- Modal Pilih Produk -->
                        <div class="modal fade" id="modalProduk" tabindex="-1" role="dialog" aria-labelledby="modalProdukLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalProdukLabel">Pilih Produk</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-bordered table-hover" id="table-produk">
                                            <thead>
                                                <tr>
                                                    <th>Kode</th>
                                                    <th>Nama</th>
                                                    <th>Harga</th>
                                                    <th>Stok</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (isset($products)): foreach ($products as $p): ?>
                                                        <tr>
                                                            <td><?= esc($p['id']) ?></td>
                                                            <td><?= esc($p['name']) ?></td>
                                                            <td><?= number_format($p['price'], 0, ',', '.') ?></td>
                                                            <td><?= esc($p['stock']) ?></td>
                                                            <td><button type="button" class="btn btn-primary btn-sm pilih-produk" data-id="<?= esc($p['id']) ?>" data-nama="<?= esc($p['name']) ?>" data-harga="<?= esc($p['price']) ?>" data-satuan="pcs">Pilih</button></td>
                                                        </tr>
                                                <?php endforeach;
                                                endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label>Payment System</label>
                            <input type="text" name="payment_system" class="form-control" value="">
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body p-2">
                                    <div class="row mb-1">
                                        <div class="col-6">Total</div>
                                        <div class="col-6 text-right"><span id="total-amount">0,00</span></div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-6">Diskon</div>
                                        <div class="col-6 text-right"><span id="total-diskon">0,00</span></div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-6">Pajak</div>
                                        <div class="col-6 text-right"><span id="total-tax">0,00</span></div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-6 font-weight-bold">Grand Total</div>
                                        <div class="col-6 text-right font-weight-bold"><span id="grand-total">0,00</span></div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-6">Pembayaran A</div>
                                        <div class="col-6 text-right"><input type="number" name="payment_a" class="form-control form-control-sm text-right" value="0"></div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-6">Pembayaran B</div>
                                        <div class="col-6 text-right"><input type="number" name="payment_b" class="form-control form-control-sm text-right" value="0"></div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-6">Piutang</div>
                                        <div class="col-6 text-right"><input type="number" name="account_receivable" class="form-control form-control-sm text-right" value="0"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save mr-2"></i> <?= isset($penjualan) ? 'Update Transaksi' : 'Simpan Transaksi' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?= $this->section('scripts') ?>
<!-- Select2 CDN (jika belum ada di layout) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let no = 1;

    function hitungTotal() {
        let total = 0,
            diskon = 0,
            pajak = 0;
        $("#tbody-barang tr").each(function() {
            let qty = parseFloat($(this).find('.qty').val()) || 0;
            let harga = parseFloat($(this).find('.harga').val()) || 0;
            let dsk = parseFloat($(this).find('.diskon').val()) || 0;
            let subtotal = (qty * harga) - dsk;
            $(this).find('.total').val(subtotal.toFixed(2));
            total += qty * harga;
            diskon += dsk;
        });
        $("#total-amount").text(total.toLocaleString('id-ID'));
        $("#total-diskon").text(diskon.toLocaleString('id-ID'));
        $("#grand-total").text((total - diskon + pajak).toLocaleString('id-ID'));
    }

    function tambahBaris(data = {}) {
        let row = `<tr>
        <td class="text-center">${no++}</td>
        <td><input type="text" name="kode[]" class="form-control kode" value="${data.kode||''}" readonly required></td>
        <td><input type="text" name="nama[]" class="form-control nama" value="${data.nama||''}" readonly required></td>
        <td><input type="number" name="qty[]" class="form-control qty" min="1" value="${data.qty||1}" required></td>
        <td><input type="text" name="satuan[]" class="form-control satuan" value="${data.satuan||'pcs'}" readonly required></td>
        <td><input type="number" name="harga[]" class="form-control harga" min="0" value="${data.harga||0}" readonly required></td>
        <td><input type="number" name="diskon[]" class="form-control diskon" min="0" value="${data.diskon||0}" required></td>
        <td><input type="number" name="total[]" class="form-control total" value="${data.total||0}" readonly></td>
        <td><button type="button" class="btn btn-warning btn-sm edit-baris"><i class="fas fa-edit"></i></button> <button type="button" class="btn btn-danger btn-sm hapus-baris"><i class="fas fa-trash"></i></button></td>
    </tr>`;
        $('#tbody-barang').append(row);
        hitungTotal();
    }

    // Pilih produk dari modal
    $(document).on('click', '.pilih-produk', function() {
        let kode = $(this).data('id');
        let nama = $(this).data('nama');
        let harga = $(this).data('harga');
        let satuan = $(this).data('satuan');
        tambahBaris({
            kode,
            nama,
            harga,
            satuan,
            qty: 1,
            diskon: 0,
            total: harga
        });
        $('#modalProduk').modal('hide');
    });
    // Hapus inisialisasi datepicker agar hanya native input type=date yang aktif
    // Validasi tanggal nota sebagai date (H-2 jika automatic, atau sesuai batas manual)
    function validateTanggalNota(val) {
        if (val) {
            let inputDate = new Date(val + 'T00:00:00');
            let today = new Date();
            today.setHours(0, 0, 0, 0);
            let batas = new Date(today);
            batas.setDate(batas.getDate() - 2);
            let mode = $('#mode_batas_tanggal').val();
            let batasManual = $('#batas_tanggal_sistem').val();
            if (mode === 'automatic' && inputDate > batas) {
                alert('Otorisasi Batas Tanggal Sistem ke Departemen General! (Tanggal maksimal mundur 2 hari dari hari ini)');
                // Set kembali ke tanggal H-2
                let h2str = batas.toISOString().slice(0, 10);
                $('#tanggal_nota').val(h2str);
                return false;
            } else if (mode === 'manual' && batasManual) {
                let batasDate = new Date(batasManual + 'T00:00:00');
                if (inputDate > batasDate) {
                    alert('Tanggal melebihi batas yang diizinkan oleh sistem!');
                    $('#tanggal_nota').val(batasManual);
                    return false;
                }
            }
        }
        return true;
    }

    $(document).ready(function() {
        // Inisialisasi select2 untuk customer & sales
        $('#customer').select2({
            placeholder: '-- Pilih Customer --',
            allowClear: true,
            width: 'resolve',
            minimumResultsForSearch: 0 // Selalu tampilkan search box
        }).on('select2:open', function() {
            setTimeout(function() {
                document.querySelector('.select2-container--open .select2-search__field').focus();
            }, 10);
        });
        $('#sales').select2({
            placeholder: '-- Pilih Sales --',
            allowClear: true,
            width: 'resolve',
            minimumResultsForSearch: 0 // Selalu tampilkan search box
        }).on('select2:open', function() {
            setTimeout(function() {
                document.querySelector('.select2-container--open .select2-search__field').focus();
            }, 10);
        });
        // Validasi saat change dan input (native input type=date)
        $('#tanggal_nota').on('change input', function() {
            validateTanggalNota($(this).val());
        });
    });
    $(document).on('input', '.qty, .harga, .diskon', function() {
        hitungTotal();
    });
    $(document).on('click', '.hapus-baris', function() {
        $(this).closest('tr').remove();
        hitungTotal();
    });
    $(document).on('click', '.edit-baris', function() {
        $(this).closest('tr').find('input').prop('readonly', false);
    });

    <?php if (isset($items) && is_array($items)): ?>
        // Auto-populate barang saat edit
        $(document).ready(function() {
            let items = <?= json_encode($items) ?>;
            no = 1;
            for (let i = 0; i < items.length; i++) {
                tambahBaris({
                    kode: items[i].product_code,
                    nama: items[i].product_name,
                    qty: items[i].qty,
                    satuan: items[i].unit,
                    harga: items[i].price,
                    diskon: items[i].discount,
                    total: items[i].total
                });
            }
        });
    <?php endif; ?>
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>