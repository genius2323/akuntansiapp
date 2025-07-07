<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteToDepartments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('departments', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'description'
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
        $this->forge->dropColumn('departments', ['deleted_at', 'recovered_at']);
    }
}