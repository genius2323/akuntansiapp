<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BuatHash extends BaseCommand
{
    protected $group       = 'Development';
    protected $name        = 'buat:hash';
    protected $description = 'Membuat hash password untuk debugging.';

    public function run(array $params)
    {
        $password = 'password123';
        $hash = password_hash($password, PASSWORD_DEFAULT);

        CLI::write('Password yang akan di-hash: ' . $password, 'yellow');
        CLI::newLine();
        CLI::write('HASH BARU (Gunakan ini di database):', 'green');
        CLI::write($hash);
        CLI::newLine();
    }
}