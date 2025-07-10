<?php
// Partial: app/Views/partials/_klasifikasi_menu.php
$uri = service('request')->getUri();
$segments = $uri->getSegments();
$current = strtolower($segments[0] ?? '');
$klasifikasiMenus = [
    ['name' => 'Kategori', 'slug' => 'categories', 'icon' => 'fas fa-tags', 'url' => site_url('categories')],
    ['name' => 'Satuan', 'slug' => 'satuan', 'icon' => 'fas fa-ruler-combined', 'url' => site_url('satuan')],
    ['name' => 'Jenis', 'slug' => 'jenis', 'icon' => 'fas fa-boxes', 'url' => site_url('jenis')],
    ['name' => 'Pelengkap', 'slug' => 'pelengkap', 'icon' => 'fas fa-puzzle-piece', 'url' => site_url('pelengkap')],
    ['name' => 'Gondola', 'slug' => 'gondola', 'icon' => 'fas fa-store-alt', 'url' => site_url('gondola')],
    ['name' => 'Merk Barang', 'slug' => 'merk', 'icon' => 'fas fa-copyright', 'url' => site_url('merk')],
    ['name' => 'Warna Sinar', 'slug' => 'warna-sinar', 'icon' => 'fas fa-lightbulb', 'url' => site_url('warna-sinar')],
    ['name' => 'Ukuran Barang', 'slug' => 'ukuran-barang', 'icon' => 'fas fa-expand-arrows-alt', 'url' => site_url('ukuran-barang')],
    ['name' => 'Voltase', 'slug' => 'voltase', 'icon' => 'fas fa-bolt', 'url' => site_url('voltase')],
    ['name' => 'Dimensi', 'slug' => 'dimensi', 'icon' => 'fas fa-ruler', 'url' => site_url('dimensi')],
    ['name' => 'Warna Body', 'slug' => 'warna-body', 'icon' => 'fas fa-palette', 'url' => site_url('warna-body')],
    ['name' => 'Warna Bibir', 'slug' => 'warna-bibir', 'icon' => 'fas fa-tint', 'url' => site_url('warna-bibir')],
    ['name' => 'Kaki', 'slug' => 'kaki', 'icon' => 'fas fa-shoe-prints', 'url' => site_url('kaki')],
    ['name' => 'Model', 'slug' => 'model', 'icon' => 'fas fa-star', 'url' => site_url('model')],
    ['name' => 'Fiting', 'slug' => 'fiting', 'icon' => 'fas fa-plug', 'url' => site_url('fiting')],
    ['name' => 'Daya', 'slug' => 'daya', 'icon' => 'fas fa-power-off', 'url' => site_url('daya')],
    ['name' => 'Jumlah Mata', 'slug' => 'jumlah-mata', 'icon' => 'fas fa-eye', 'url' => site_url('jumlah-mata')],
];
?>
<div class="card mb-3">
    <div class="card-body">
        <h3 class="mb-3">Klasifikasi</h3>
        <style>
        .btn-klasifikasi-active {
            background-color: #007bff !important;
            color: #fff !important;
            box-shadow: 0 2px 6px rgba(0,123,255,0.2);
        }
        </style>
        <?php foreach ($klasifikasiMenus as $item): ?>
            <a href="<?= $item['url'] ?>" class="btn btn-app mb-2 <?= ($current === $item['slug']) ? 'btn-klasifikasi-active' : 'bg-secondary' ?>">
                <i class="<?= $item['icon'] ?>"></i> <?= $item['name'] ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
