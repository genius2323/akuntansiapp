<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    // ... (bagian lain dari file)

    /**
     * Lets you choose which connection group to
     * use if no other is specified.
     *
     * @var string
     */
    // PERBAIKAN: Menambahkan properti $defaultGroup yang hilang
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     */
    public array $default = [
        'DSN'      => '',
        'hostname' => 'localhost',
        'username' => 'root', // Ganti jika username Anda berbeda
        'password' => '',     // Ganti jika Anda menggunakan password
        'database' => 'db_akuntansi', // Nama database utama
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
    ];

    /**
     * This database connection is used when
     * running PHPUnit database tests.
     */
    public array $tests = [
        // ... (konfigurasi untuk tes, biarkan saja)
    ];

    /**
     * Konfigurasi untuk database backup.
     */
    public array $db1 = [
        'DSN'      => '',
        'hostname' => 'localhost',
        'username' => 'root', // Ganti jika username Anda berbeda
        'password' => '',     // Ganti jika Anda menggunakan password
        'database' => 'db1_akuntansi', // Nama database backup
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
    ];

    public function __construct()
    {
        parent::__construct();

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite, so that
        // we don't overwrite live data on accident.
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}
