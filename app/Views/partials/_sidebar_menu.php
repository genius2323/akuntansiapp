<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Menu Dinamis Berdasarkan Departemen -->
        <?php if (session('department_id') == 1): ?>
            <!-- Menu POS -->
            <li class="nav-item">
                <a href="/pos" class="nav-link active">
                    <i class="nav-icon fas fa-cash-register text-warning"></i>
                    <p>Transaksi</p>
                </a>
            </li>

        <?php elseif (session('department_id') == 2): ?>
            <!-- Menu Accounting -->
            <li class="nav-item">
                <a href="/accounting" class="nav-link active">
                    <i class="nav-icon fas fa-calculator text-warning"></i>
                    <p>Akunting</p>
                </a>
            </li>

        <?php elseif (session('department_id') == 3): ?>
            <!-- Menu Batas Tanggal Sistem -->
            <li class="nav-item">
                <a href="<?= site_url('general/batasTanggalSistem') ?>" class="nav-link <?= (url_is('general/batasTanggalSistem*')) ? 'active bg-primary text-white' : '' ?>">
                    <i class="nav-icon fas fa-calendar-alt text-warning"></i>
                    <p>Batas Tanggal Sistem</p>
                </a>
            </li>
            <!-- Menu General/Owner -->
            <!-- MENU MASTER BARU DENGAN DROPDOWN -->
            <li class="nav-item has-treeview <?= (url_is('products*') || url_is('categories*') || url_is('satuan*') || url_is('jenis*') || url_is('customers*') || url_is('suppliers*')) ? 'menu-open' : '' ?>">
                <a href="#" class="nav-link <?= (url_is('products*') || url_is('categories*') || url_is('satuan*') || url_is('jenis*') || url_is('customers*') || url_is('suppliers*')) ? 'active bg-primary text-white' : '' ?>">
                    <i class="nav-icon fas fa-database text-warning"></i>
                    <p>
                        Master Data
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?= site_url('products') ?>" class="nav-link <?= (url_is('products*')) ? 'active bg-primary text-white' : '' ?>">
                            <i class="far fa-circle nav-icon text-warning"></i>
                            <p>Master Produk</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('categories') ?>" class="nav-link <?= (url_is('categories*') || url_is('satuan*') || url_is('jenis*')) ? 'active bg-primary text-white' : '' ?>">
                            <i class="far fa-circle nav-icon text-warning"></i>
                            <p>Master Klasifikasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('customers') ?>" class="nav-link <?= (url_is('customers*')) ? 'active bg-primary text-white' : '' ?>">
                            <i class="far fa-circle nav-icon text-warning"></i>
                            <p>Master Customer</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('suppliers') ?>" class="nav-link <?= (url_is('suppliers*')) ? 'active bg-primary text-white' : '' ?>">
                            <i class="far fa-circle nav-icon text-warning"></i>
                            <p>Master Supplier</p>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (session('department_id') == 3): ?>
            <li class="nav-item">
                <a href="<?= site_url('general/otoritasKategori') ?>" class="nav-link <?= (url_is('general/otoritasKategori') || url_is('general/otoritasSatuan') || url_is('general/otoritasJenis')) ? 'active bg-primary text-white' : '' ?>">
                    <i class="nav-icon fas fa-key text-warning"></i>
                    <p>Otoritas</p>
                </a>
            </li>
        <?php endif; ?>

        <?php if (session('department_id') == 3): ?>
            <!-- Menu Karyawan (dropdown) -->
            <li class="nav-item has-treeview <?= (url_is('users*') || url_is('users/trash')) ? 'menu-open' : '' ?>">
                <a href="#" class="nav-link <?= (url_is('users*') || url_is('users/trash')) ? 'active bg-primary text-white' : '' ?>">
                    <i class="nav-icon fas fa-users text-warning"></i>
                    <p>
                        Karyawan
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?= site_url('users') ?>" class="nav-link <?= (url_is('users') || url_is('users/edit*')) ? 'active bg-primary text-white' : '' ?>">
                            <i class="far fa-circle nav-icon text-warning"></i>
                            <p>Manajemen User</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('users/trash') ?>" class="nav-link <?= (url_is('users/trash')) ? 'active bg-primary text-white' : '' ?>">
                            <i class="far fa-circle nav-icon text-warning"></i>
                            <p>Data Terhapus</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-treeview <?= (url_is('penjualan*')) ? 'menu-open' : '' ?>">
                <a href="#" class="nav-link <?= (url_is('penjualan*')) ? 'active bg-primary text-white' : '' ?>">
                    <i class="nav-icon fas fa-chart-line text-warning"></i>
                    <p>
                        Penjualan
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?= site_url('penjualan/dashboard') ?>" class="nav-link <?= (url_is('penjualan/dashboard*')) ? 'active bg-primary text-white' : '' ?>">
                            <i class="far fa-circle nav-icon text-warning"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('penjualan') ?>" class="nav-link <?= (url_is('penjualan') && !url_is('penjualan/dashboard*') && !url_is('penjualan/laporan*')) ? 'active bg-primary text-white' : '' ?>">
                            <i class="far fa-circle nav-icon text-warning"></i>
                            <p>Input Penjualan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('penjualan/laporan') ?>" class="nav-link <?= (url_is('penjualan/laporan*')) ? 'active bg-primary text-white' : '' ?>">
                            <i class="far fa-circle nav-icon text-warning"></i>
                            <p>Laporan Penjualan</p>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
</nav>