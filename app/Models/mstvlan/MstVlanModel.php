<?php

namespace App\Models\mstvlan;

use CodeIgniter\Model;

class MstVlanModel extends Model
{
    protected $table            = 'tbmst_vlan';
    protected $primaryKey       = 'tv_id';      // Primary key is tv_id (auto-increment)
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // IMPORTANT: Specify the database group explicitly
    protected $DBGroup = 'jinsystem'; // Ensures the model connects to the right database

    // Allowed fields now include tv_id_vlan and tv_lastuser (integer)
    protected $allowedFields = [
        'tv_id_vlan',   // User-inputtable VLAN ID
        'tv_name',
        'tv_lastuser',  // Storing user ID as INTEGER
        'tv_lastupdate',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'tv_lastupdate';
    protected $updatedField  = 'tv_lastupdate';
    protected $deletedField  = null;

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setLastUpdateAndUser'];
    protected $beforeUpdate   = ['setLastUpdateAndUser'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Callback function to set tv_lastupdate.
     * The 'tv_lastuser' value is now expected to be provided by the controller.
     */
    protected function setLastUpdateAndUser(array $data)
    {
        $data['data']['tv_lastupdate'] = date('Y-m-d H:i:s');
        // The 'tv_lastuser' value is expected to be already set in $data['data'] by the controller.
        // No default or fallback for tv_lastuser here, rely solely on controller.
        return $data;
    }

    /**
     * Checks for duplicate VLAN names in the database.
     *
     * @param string $vlanName The VLAN name to check.
     * @param int|null $id The auto-increment primary key (tv_id) of the current record being edited (null for new records).
     * @return bool True if a duplicate is found, false otherwise.
     */
    public function checkDuplicateName(string $vlanName, ?int $id = null): bool
    {
        $builder = $this->db->table($this->table)
                            ->where('UPPER(TRIM(tv_name))', strtoupper(trim($vlanName)));

        // Exclude the current record if id is provided (for edit operations)
        if ($id !== null) {
            $builder->where('tv_id !=', $id);
        }

        return (bool)$builder->countAllResults();
    }

    /**
     * Checks for duplicate user-inputted VLAN IDs (tv_id_vlan) in the database.
     *
     * @param int $vlanIdInput The user-input VLAN ID (tv_id_vlan) to check.
     * @param int|null $id The auto-increment primary key (tv_id) of the current record being edited (null for new records).
     * @return bool True if a duplicate is found, false otherwise.
     */
    public function checkDuplicateVlanId(int $vlanIdInput, ?int $id = null): bool
    {
        $builder = $this->db->table($this->table)
                            ->where('tv_id_vlan', $vlanIdInput);

        // Exclude the current record if id is provided (for edit operations)
        if ($id !== null) {
            $builder->where('tv_id !=', $id);
        }

        return (bool)$builder->countAllResults();
    }
}