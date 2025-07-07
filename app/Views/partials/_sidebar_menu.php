<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Menu Dinamis Berdasarkan Departemen -->
        <?php if(session('department_id') == 1): ?>
            <!-- Menu POS -->
            <li class="nav-item">
                <a href="/pos" class="nav-link active">
                    <i class="nav-icon fas fa-cash-register"></i>
                    <p>Transaksi</p>
                </a>
            </li>

        <?php elseif(session('department_id') == 2): ?>
            <!-- Menu Accounting -->
            <li class="nav-item">
                <a href="/accounting" class="nav-link active">
                    <i class="nav-icon fas fa-calculator"></i>
                    <p>Akunting</p>
                </a>
            </li>

        <?php elseif(session('department_id') == 3): ?>
            <!-- Menu General/Owner -->

            <!-- MENU MASTER BARU DENGAN DROPDOWN -->
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-database"></i>
                    <p>
                        Master Data
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?= site_url('products') ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Master Produk</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('categories') ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Master Klasifikasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('customers') ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Master Customer</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('suppliers') ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Master Supplier</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('users') ?>" class="nav-link <?= (url_is('users*')) ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Manajemen User</p>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= site_url('penjualan') ?>" class="nav-link <?= (url_is('penjualan*')) ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-chart-line"></i>
                    <p>Input Penjualan</p>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= base_url('users/trash') ?>" class="nav-link">
                    <i class="nav-icon fas fa-trash"></i>
                    <p>Data Terhapus</p>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
