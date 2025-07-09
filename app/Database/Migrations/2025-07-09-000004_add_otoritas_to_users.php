<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOtoritasToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'otoritas' => [
                'type' => 'CHAR',
                'constraint' => 1,
                'null' => true,
                'default' => 'T',
                'after' => 'recovered_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'otoritas');
    }
}
