<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtColumn extends Migration
{
    public function up()
    {
        // Untuk tabel users
        $this->forge->addColumn('users', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'noktp' // Sesuaikan dengan kolom terakhir di tabel Anda
            ]
        ]);

        // Jika perlu, tambahkan juga ke tabel lain
        // $this->forge->addColumn('nama_tabel_lain', [...]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'deleted_at');
        // $this->forge->dropColumn('nama_tabel_lain', 'deleted_at');
    }
}