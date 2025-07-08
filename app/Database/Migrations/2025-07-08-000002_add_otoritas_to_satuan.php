<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOtoritasToSatuan extends Migration
{
    public function up()
    {
        $fields = [
            'otoritas' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'description',
            ],
        ];

        // Tambah kolom di database default
        $this->forge->addColumn('satuan', $fields);

        // Tambah kolom di database kedua (db1)
        $db2 = \Config\Database::connect('db1');
        $forge2 = \Config\Services::forge($db2);
        $forge2->addColumn('satuan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('satuan', 'otoritas');

        $db2 = \Config\Database::connect('db1');
        $forge2 = \Config\Services::forge($db2);
        $forge2->dropColumn('satuan', 'otoritas');
    }
}
