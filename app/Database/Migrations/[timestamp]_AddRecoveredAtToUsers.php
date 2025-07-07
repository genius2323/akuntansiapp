<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRecoveredAtToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'recovered_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'deleted_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'recovered_at');
    }
}