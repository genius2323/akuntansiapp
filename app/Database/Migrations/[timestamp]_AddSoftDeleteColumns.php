<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteColumns extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'noktp'
            ],
            'recovered_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'deleted_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'deleted_at');
        $this->forge->dropColumn('users', 'recovered_at');
    }
}