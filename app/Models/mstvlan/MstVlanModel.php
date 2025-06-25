<?php

namespace App\Models\mstvlan;

use CodeIgniter\Model;

class MstVlanModel extends Model
{
    protected $table            = 'tbmst_vlan';
    protected $primaryKey       = 'tv_id';      // Primary key is tv_id (auto-increment)
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // TETAP FALSE, karena kita handle manual

    // IMPORTANT: Specify the database group explicitly
    protected $DBGroup = 'jinsystem'; // Ensures the model connects to the right database

    // Allowed fields now include tv_id_vlan, tv_lastuser (integer), and tv_status
    protected $allowedFields = [
        'tv_id_vlan',   // User-inputtable VLAN ID
        'tv_name',
        'tv_lastuser',  // Storing user ID as INTEGER
        'tv_lastupdate',
        'tv_status',    // <--- INI TAMBAHAN
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'tv_lastupdate';
    protected $updatedField  = 'tv_lastupdate';
    protected $deletedField  = null; // TETAP NULL, karena kita handle manual tv_status

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setLastUpdateAndUserAndStatus']; // GANTI NAMA CALLBACK
    protected $beforeUpdate   = ['setLastUpdateAndUser'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Callback function to set tv_lastupdate and tv_status for new records.
     * The 'tv_lastuser' value is now expected to be provided by the controller.
     */
    protected function setLastUpdateAndUserAndStatus(array $data) // NAMA CALLBACK BARU
    {
        $data['data']['tv_lastupdate'] = date('Y-m-d H:i:s');
        // Set tv_status to 1 for new records if not explicitly set
        if (!isset($data['data']['tv_status'])) {
            $data['data']['tv_status'] = 1;
        }
        // The 'tv_lastuser' value is expected to be already set in $data['data'] by the controller.
        return $data;
    }

    /**
     * Callback function to set tv_lastupdate.
     * The 'tv_lastuser' value is now expected to be provided by the controller.
     */
    protected function setLastUpdateAndUser(array $data)
    {
        $data['data']['tv_lastupdate'] = date('Y-m-d H:i:s');
        // The 'tv_lastuser' value is expected to be already set in $data['data'] by the controller.
        // We do NOT set tv_status here, as it should only be changed by the delete/restore logic.
        return $data;
    }


    // OVERRIDE THE DEFAULT DELETE METHOD FOR SOFT DELETE
    public function delete($id = null, bool $purge = false)
    {
        if ($id === null) {
            throw new \RuntimeException('A record ID is required for deletion.');
        }

        // Jika purge adalah true, lakukan penghapusan fisik (hard delete)
        if ($purge) {
            return $this->db->table($this->table)->delete([$this->primaryKey => $id]);
        }

        // Lakukan soft delete dengan mengubah tv_status menjadi 25
        // Pastikan tv_lastuser dan tv_lastupdate juga terupdate saat soft delete
        $loggedInUserId = session()->get('user_id');
        $lastUser = is_numeric($loggedInUserId) ? (int)$loggedInUserId : 1;

        return $this->update($id, [
            'tv_status' => 25,
            'tv_lastuser' => $lastUser,
            'tv_lastupdate' => date('Y-m-d H:i:s') // Ini akan di-override oleh beforeUpdate, jadi tidak terlalu kritis di sini
        ]);
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
                            ->where('UPPER(TRIM(tv_name))', strtoupper(trim($vlanName)))
                            ->where('tv_status', 1); // <--- INI TAMBAHAN

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
                            ->where('tv_id_vlan', $vlanIdInput)
                            ->where('tv_status', 1); // <--- INI TAMBAHAN

        // Exclude the current record if id is provided (for edit operations)
        if ($id !== null) {
            $builder->where('tv_id !=', $id);
        }

        return (bool)$builder->countAllResults();
    }
}