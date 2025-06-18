<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    // Ubah defaultGroup ke “jincommon” (sesuai dengan tipe aplikasi yang mayoritas menggunakan data common)
    public string $defaultGroup = 'jincommon';

    // Koneksi untuk tabel-tabel common (m_application, m_menugroup, m_menuname, tbua_useraccess, tbua_usermenusetup, dll)
    public array $jincommon = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'postgres',
        'password'     => 'pudyowati14',
        'database'     => 'jincommon',
        'schema'       => 'public',
        'DBDriver'     => 'Postgre',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => (ENVIRONMENT !== 'production'),
        'charset'      => 'utf8',
        'swapPre'      => '',
        'failover'     => [],
        'port'         => 5432,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    // Koneksi untuk tabel-tabel system (m_equipmentcat, m_itequipment, t_dispose, t_equipmentmovement, dll)
    public array $jinsystem = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'postgres',
        'password'     => 'pudyowati14',
        'database'     => 'jinsystem',
        'schema'       => 'public',
        'DBDriver'     => 'Postgre',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => (ENVIRONMENT !== 'production'),
        'charset'      => 'utf8',
        'swapPre'      => '',
        'failover'     => [],
        'port'         => 5432,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}