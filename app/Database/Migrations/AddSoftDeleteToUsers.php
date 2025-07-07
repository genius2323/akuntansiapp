<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteToUsers extends Migration
{
    public function up()
{
    $this->forge->addColumn('users', [
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
}