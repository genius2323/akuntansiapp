<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixUsersTableStructure extends Migration
{
    public function up()
    {
        // Modifikasi kolom yang sudah ada
        $this->forge->modifyColumn('users', [
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'kode_ky' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'noktp' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'recovered_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        // Tidak bisa rollback modifyColumn secara sempurna
        // Disarankan backup database sebelum migrasi
    }
}