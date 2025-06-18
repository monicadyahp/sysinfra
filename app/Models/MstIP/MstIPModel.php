<?php

namespace App\Models\MstIP;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class MstIPModel extends Model
{
    protected $table            = 'm_ipaddress';
    protected $primaryKey       = 'mip_id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'mip_vlanid',
        'mip_vlanname',
        'mip_ipadd',
        'mip_status',
        'mip_lastupdate',
        'mip_lastuser',
    ];

    protected $useTimestamps    = false;
    protected $deletedField     = null;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect('jinsystem');
    }
}