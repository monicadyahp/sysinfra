<?php

namespace App\Models\pcserver;

use CodeIgniter\Model;

class PCServerModel extends Model
{
    protected $table            = 't_pcserver';
    protected $primaryKey       = 'srv_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'srv_asset_no',
        'srv_asset_name',
        'srv_location',
        'srv_receive_date',
        'srv_age',
        'srv_hdd',
        'srv_ram',
        'srv_vga',
        'srv_ethernet',
        'srv_remark',
        'srv_status',
        'srv_lastupdate', // Pastikan ini juga ada di allowedFields
        'srv_lastuser',   // Pastikan ini juga ada di allowedFields
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'srv_lastupdate';
    protected $updatedField  = 'srv_lastupdate';
    protected $deletedField  = null;

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setLastUpdateAndUser'];
    protected $beforeUpdate   = ['setLastUpdateAndUser'];

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect('jinsystem');
    }

    /**
     * Sets srv_lastupdate and srv_lastuser before insert/update.
     * Ensures srv_lastuser is a string (username) or null.
     */
    protected function setLastUpdateAndUser(array $data)
    {
        $data['data']['srv_lastupdate'] = date('Y-m-d H:i:s');

        // Ambil username dari session. Asumsi 'user_name' adalah nama user yang login.
        // Jika tidak ada, gunakan 'postgres' sebagai fallback.
        $loggedInUserName = session()->get('user_name');
        $data['data']['srv_lastuser'] = $loggedInUserName ?? 'postgres';

        return $data;
    }
    /**
     * Check for duplicate srv_asset_no
     * @param string $assetNo
     * @param int|null $srvId
     * @return bool
     */
    public function checkDuplicateAssetNo(string $assetNo, ?int $srvId = null): bool
    {
        $builder = $this->db->table($this->table)
                            ->where('UPPER(TRIM(srv_asset_no))', strtoupper(trim($assetNo)));

        if ($srvId !== null) {
            $builder->where('srv_id !=', $srvId);
        }

        return (bool)$builder->countAllResults();
    }

    /**
     * Get data from m_itequipment for the finder.
     */
    public function getEquipmentDataForFinder(string $searchTerm = null)
    {
        $builder = $this->db->table('m_itequipment');
        $builder->select('e_id, e_assetno, e_equipmentname, e_equipmentid, e_serialnumber, e_brand, e_model');

        if (!empty($searchTerm)) {
            $searchTerm = strtoupper(trim($searchTerm));
            $builder->groupStart()
                        ->like('UPPER(TRIM(e_assetno))', $searchTerm)
                        ->orLike('UPPER(TRIM(e_equipmentname))', $searchTerm)
                        ->orLike('UPPER(TRIM(e_serialnumber))', $searchTerm)
                        ->orLike('UPPER(TRIM(e_brand))', $searchTerm)
                        ->orLike('UPPER(TRIM(e_model))', $searchTerm)
                        ->orLike('CAST(e_equipmentid AS TEXT)', $searchTerm)
                        ->groupEnd();
        }
        return $builder->orderBy('e_assetno', 'ASC')->get()->getResultArray();
    }
}