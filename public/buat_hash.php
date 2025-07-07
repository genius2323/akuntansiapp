<?php
// File ini hanya untuk membuat hash sementara. Hapus setelah selesai.

// Ganti password di sini jika perlu
$passwordSaya = 'password123';

// Hasilkan hash menggunakan lingkungan PHP server ini
$hash = password_hash($passwordSaya, PASSWORD_DEFAULT);

// Tampilkan hasilnya agar mudah disalin
echo '<h3>Hash Generator</h3>';
echo '<p>Password yang di-hash: <strong>' . htmlspecialchars($passwordSaya) . '</strong></p>';
echo '<p>Gunakan hash di bawah ini untuk dimasukkan ke database:</p>';
echo '<textarea rows="4" cols="70" readonly onclick="this.select()">' . htmlspecialchars($hash) . '</textarea>';
echo '<p><em>Klik pada kotak di atas untuk memilih semua teks secara otomatis.</em></p>';

?>