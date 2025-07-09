<?php
// Partial: app/Views/partials/_otoritas_menu.php
$uri = service('request')->getUri();
$segments = $uri->getSegments();
// Ambil segmen terakhir yang mengandung kata 'otoritas' dan nama menu
$current = '';
foreach ($segments as $seg) {
    if (stripos($seg, 'otoritas') !== false) {
        $current = strtolower(str_replace('otoritas', '', $seg));
        break;
    }
}
if ($current === '') $current = 'kategori';
$otoritasMenus = [
    ['name' => 'Kategori', 'slug' => 'kategori', 'icon' => 'fas fa-tags', 'url' => site_url('general/otoritasKategori')],
    ['name' => 'Satuan', 'slug' => 'satuan', 'icon' => 'fas fa-ruler-combined', 'url' => site_url('general/otoritasSatuan')],
    ['name' => 'Jenis', 'slug' => 'jenis', 'icon' => 'fas fa-boxes', 'url' => site_url('general/otoritasJenis')],
    ['name' => 'Pelengkap', 'slug' => 'pelengkap', 'icon' => 'fas fa-puzzle-piece', 'url' => site_url('general/otoritasPelengkap')],
    ['name' => 'Gondola', 'slug' => 'gondola', 'icon' => 'fas fa-store-alt', 'url' => site_url('general/otoritasGondola')],
    ['name' => 'Merk Barang', 'slug' => 'merk', 'icon' => 'fas fa-copyright', 'url' => site_url('general/otoritasMerk')],
    ['name' => 'Warna Sinar', 'slug' => 'warna-sinar', 'icon' => 'fas fa-lightbulb', 'url' => site_url('general/otoritasWarnaSinar')],
    ['name' => 'Ukuran Barang', 'slug' => 'ukuran-barang', 'icon' => 'fas fa-expand-arrows-alt', 'url' => site_url('general/otoritasUkuranBarang')],
    ['name' => 'Voltase', 'slug' => 'voltase', 'icon' => 'fas fa-bolt', 'url' => site_url('general/otoritasVoltase')],
    ['name' => 'Dimensi', 'slug' => 'dimensi', 'icon' => 'fas fa-ruler', 'url' => site_url('general/otoritasDimensi')],
    ['name' => 'Warna Body', 'slug' => 'warna-body', 'icon' => 'fas fa-palette', 'url' => site_url('general/otoritasWarnaBody')],
    ['name' => 'Warna Bibir', 'slug' => 'warna-bibir', 'icon' => 'fas fa-tint', 'url' => site_url('general/otoritasWarnaBibir')],
    ['name' => 'Kaki', 'slug' => 'kaki', 'icon' => 'fas fa-shoe-prints', 'url' => site_url('general/otoritasKaki')],
    ['name' => 'Model', 'slug' => 'model', 'icon' => 'fas fa-star', 'url' => site_url('general/otoritasModel')],
    ['name' => 'Fiting', 'slug' => 'fiting', 'icon' => 'fas fa-plug', 'url' => site_url('general/otoritasFiting')],
    ['name' => 'Daya', 'slug' => 'daya', 'icon' => 'fas fa-power-off', 'url' => site_url('general/otoritasDaya')],
    ['name' => 'Jumlah Mata', 'slug' => 'jumlah-mata', 'icon' => 'fas fa-eye', 'url' => site_url('general/otoritasJumlahMata')],
    ['name' => 'Produk', 'slug' => 'produk', 'icon' => 'fas fa-box', 'url' => site_url('general/otoritasProduk')],
    ['name' => 'User', 'slug' => 'user', 'icon' => 'fas fa-user', 'url' => site_url('general/otoritasUser')],
];
?>
<div class="card">
    <div class="card-body">
        <h3 class="mb-3">Otoritas</h3>
        <style>
        .btn-otoritas-active {
            background-color: #28a745 !important;
            color: #fff !important;
            box-shadow: 0 2px 6px rgba(40,167,69,0.2);
        }
        </style>
        <?php foreach ($otoritasMenus as $item): ?>
            <a href="<?= $item['url'] ?>" class="btn btn-app mb-2 <?= ($current === $item['slug']) ? 'btn-otoritas-active' : 'bg-secondary' ?>">
                <i class="<?= $item['icon'] ?>"></i> <?= $item['name'] ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
