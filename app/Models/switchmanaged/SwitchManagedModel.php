<?php

namespace App\Models\SwitchManaged;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time; // <--- TAMBAHKAN BARIS INI

class SwitchManagedModel extends Model
{
    // Properties for tbmst_switch_managed (main table)
    protected $table          = 'tbmst_switch_managed';
    protected $primaryKey     = 'sm_id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'sm_id_switch',
        'sm_asset_no',
        'sm_asset_name',
        'sm_received_date',
        'sm_age',
        'sm_ip',
        'sm_location',
        'sm_status', // BARIS BARU INI
        'sm_lastuser', // This will store an integer ID
        'sm_lastupdate',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'sm_lastupdate';
    protected $updatedField  = 'sm_lastupdate';
    protected $deletedField  = null;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setLastUpdateAndUser'];
    protected $beforeUpdate   = ['setLastUpdateAndUser'];

    protected $db;

    // NEW: Properties for tbmst_switch_managed_detail (detail table)
    protected $switchDetailTable = 'tbmst_switch_managed_detail';
    protected $switchDetailPrimaryKey = 'smd_id';

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect('jinsystem');
    }

    /**
     * Sets sm_lastupdate and sm_lastuser before insert/update for main table.
     * Ensures sm_lastuser is an integer (user ID) or a default.
     */
    protected function setLastUpdateAndUser(array $data)
    {
        $data['data']['sm_lastupdate'] = date('Y-m-d H:i:s'); // Anda bisa juga menggunakan Time::now()->toDateTimeString() di sini

        // Use 'user_id' from session, fallback to 1 (or another default integer ID)
        $loggedInUserId = session()->get('user_id');
        $data['data']['sm_lastuser'] = $loggedInUserId ?? 1; // Fallback to a default user ID like 1

        return $data;
    }

    /**
     * Check for duplicate sm_id_switch or sm_asset_no in main table.
     * @param int|null $idSwitch
     * @param string|null $assetNo
     * @param int|null $smId
     * @return array Returns an array with boolean flags for duplicate_id_switch and duplicate_asset_no
     */
    public function checkDuplicate(
        ?int $idSwitch = null,
        ?string $assetNo = null,
        ?int $smId = null
    ): array {
        $duplicateIdSwitch = false;
        $duplicateAssetNo = false;

        $builder = $this->db->table($this->table);

        if ($idSwitch !== null) {
            $builderIdSwitch = clone $builder;
            $builderIdSwitch->where('sm_id_switch', $idSwitch);
            if ($smId !== null) {
                $builderIdSwitch->where('sm_id !=', $smId);
            }
            if ($builderIdSwitch->countAllResults() > 0) {
                $duplicateIdSwitch = true;
            }
        }

        if ($assetNo !== null) {
            $builderAssetNo = clone $builder;
            $builderAssetNo->where('UPPER(TRIM(sm_asset_no))', strtoupper(trim($assetNo)));
            if ($smId !== null) {
                $builderAssetNo->where('sm_id !=', $smId);
            }
            if ($builderAssetNo->countAllResults() > 0) {
                $duplicateAssetNo = true;
            }
        }

        return ['duplicate_id_switch' => $duplicateIdSwitch, 'duplicate_asset_no' => $duplicateAssetNo];
    }

    /**
     * Get data from m_itequipment for the finder.
     */
    public function getEquipmentDataForFinder(string $searchTerm = null)
    {
        $builder = $this->db->table('public.m_itequipment');
        $builder->select('e_id, e_assetno, e_equipmentname, e_equipmentid, e_serialnumber, e_brand, e_model, e_receivedate');

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

    // --- NEW: Methods for tbmst_switch_managed_detail ---

    /**
     * Get detail ports for a specific switch (by sm_id_switch).
     */
    public function getSwitchDetailPortsByHeaderId(int $sm_id_switch)
    {
        return $this->db->table($this->switchDetailTable . ' smd')
                        ->select('smd.smd_id, smd.smd_header_id_switch, smd.smd_port, smd.smd_type,
                                  smd.smd_vlan_id, smd.smd_vlan_name, smd.smd_status,
                                  smd.smd_lastupdate, smd.smd_lastuser')
                        ->where('smd.smd_header_id_switch', $sm_id_switch)
                        ->whereIn('smd.smd_status', [0, 1]) // BARIS INI TELAH DIUBAH!
                        ->orderBy('smd.smd_port', 'ASC') // Order by port number
                        ->get()
                        ->getResultArray();
    }

    /**
     * Get a single switch detail record by its primary key.
     */
    public function getSwitchDetailPortById(int $smd_id)
    {
        return $this->db->table($this->switchDetailTable)
                        ->select('smd_id, smd_header_id_switch, smd_port, smd_type,
                                  smd_vlan_id, smd_vlan_name, smd_status,
                                  smd_lastupdate, smd_lastuser')
                        ->where($this->switchDetailPrimaryKey, $smd_id)
                        ->get()
                        ->getRowArray();
    }

    /**
     * Count the number of detail ports for a specific switch.
     */
    public function countSwitchDetailPortsByHeaderId(int $sm_id_switch): int
    {
        return $this->db->table($this->switchDetailTable)
                        ->where('smd_header_id_switch', $sm_id_switch)
                        ->countAllResults();
    }

    /**
     * Add a new port detail to tbmst_switch_managed_detail.
     */
    public function addSwitchDetailPort(array $data)
    {
        // Get logged-in user ID from session, fallback to 1
        $loggedInUserId = session()->get('user_id');
        $lastUser = $loggedInUserId ?? 1;

        $insertData = [
            'smd_header_id_switch' => $data['smd_header_id_switch'],
            'smd_port'             => $data['smd_port'],
            'smd_type'             => $data['smd_type'],
            'smd_vlan_id'          => $data['smd_vlan_id'] ?? null,
            'smd_vlan_name'        => $data['smd_vlan_name'] ?? null,
            'smd_status'           => $data['smd_status'] ?? 1,
            'smd_lastuser'         => $lastUser,
            'smd_lastupdate'       => Time::now()->toDateTimeString(), // Perbaikan: Pastikan Time::now() dapat diakses
        ];

        $this->db->table($this->switchDetailTable)->insert($insertData);
        return $this->db->insertID();
    }

    /**
     * Update an existing port detail in tbmst_switch_managed_detail.
     */
    public function updateSwitchDetailPort(int $smd_id, array $data)
    {
        // Get logged-in user ID from session, fallback to 1
        $loggedInUserId = session()->get('user_id');
        $lastUser = $loggedInUserId ?? 1;

        $updateData = [
            'smd_header_id_switch' => $data['smd_header_id_switch'],
            'smd_port'             => $data['smd_port'],
            'smd_type'             => $data['smd_type'],
            'smd_vlan_id'          => $data['smd_vlan_id'] ?? null,
            'smd_vlan_name'        => $data['smd_vlan_name'] ?? null,
            'smd_status'           => $data['smd_status'] ?? 1,
            'smd_lastuser'         => $lastUser,
            'smd_lastupdate'       => Time::now()->toDateTimeString(), // Perbaikan: Pastikan Time::now() dapat diakses
        ];

        return $this->db->table($this->switchDetailTable)
                        ->where($this->switchDetailPrimaryKey, $smd_id)
                        ->update($updateData);
    }

    public function delete($id = null, bool $purge = false)
    {
        if ($id === null) {
            return false;
        }

        // Dapatkan ID pengguna yang login dari sesi, jika tidak ada, gunakan 1 sebagai default
        $loggedInUserId = session()->get('user_id');
        $lastUser = $loggedInUserId ?? 1;

        $updateData = [
            'sm_status'      => 25, // Kunci: Ubah status menjadi 25
            'sm_lastuser'    => $lastUser, // Perbarui pengguna terakhir yang mengubah
            'sm_lastupdate'  => Time::now()->toDateTimeString(), // Perbarui timestamp
        ];

        // Lakukan update pada tabel utama (tbmst_switch_managed)
        return $this->db->table($this->table)
                        ->where($this->primaryKey, $id) // Cari berdasarkan primary key (sm_id)
                        ->update($updateData); // Lakukan operasi update
    }

    /**
     * Changes the status of a port detail record to 25 (deleted/inactive) instead of permanently deleting it.
     */
    public function deleteSwitchDetailPort(int $smd_id)
    {
        // Dapatkan ID pengguna yang login dari sesi, jika tidak ada, gunakan 1 sebagai default
        $loggedInUserId = session()->get('user_id');
        $lastUser = $loggedInUserId ?? 1;

        $updateData = [
            'smd_status'     => 25, // Kunci: Ubah status menjadi 25
            'smd_lastuser'   => $lastUser, // Perbarui pengguna terakhir yang mengubah
            'smd_lastupdate' => Time::now()->toDateTimeString(), // Perbarui timestamp
        ];

        // Lakukan update pada tabel detail (tbmst_switch_managed_detail)
        return $this->db->table($this->switchDetailTable)
                        ->where($this->switchDetailPrimaryKey, $smd_id) // Cari berdasarkan primary key (smd_id)
                        ->update($updateData); // Lakukan operasi update
    }

    /**
     * Changes the status of all port details for a specific switch to 25 (deleted/inactive).
     */
    public function deleteSwitchDetailPortsByHeaderId(int $sm_id_switch)
    {
        // Dapatkan ID pengguna yang login dari sesi, jika tidak ada, gunakan 1 sebagai default
        $loggedInUserId = session()->get('user_id');
        $lastUser = $loggedInUserId ?? 1;

        $updateData = [
            'smd_status'     => 25, // Kunci: Ubah status menjadi 25
            'smd_lastuser'   => $lastUser, // Perbarui pengguna terakhir yang mengubah
            'smd_lastupdate' => Time::now()->toDateTimeString(), // Perbarui timestamp
        ];

        // Lakukan update massal pada tabel detail (tbmst_switch_managed_detail)
        return $this->db->table($this->switchDetailTable)
                        ->where('smd_header_id_switch', $sm_id_switch) // Cari semua detail dengan header ID ini
                        ->update($updateData); // Lakukan operasi update
    }

    /**
     * Check for duplicate port and header_id_switch in tbmst_switch_managed_detail.
     */
    public function checkDuplicateSwitchDetailPort(int $smd_header_id_switch, int $smd_port, ?int $smd_id = null): bool
    {
        $builder = $this->db->table($this->switchDetailTable)
                            ->where('smd_header_id_switch', $smd_header_id_switch)
                            ->where('smd_port', $smd_port);

        if ($smd_id !== null) {
            $builder->where('smd_id !=', $smd_id);
        }

        return (bool)$builder->countAllResults();
    }

    /**
     * Get VLAN data for search modal (from tbmst_vlan).
     */
    public function getVlanDataForFinder(string $searchTerm = null)
    {
        $builder = $this->db->table('public.tbmst_vlan');
        $builder->select('tv_id, tv_id_vlan, tv_name');

        if (!empty($searchTerm)) {
            $searchTerm = strtoupper(trim($searchTerm));
            $builder->groupStart()
                ->like('UPPER(TRIM(tv_id_vlan::text))', $searchTerm)
                ->orLike('UPPER(TRIM(tv_name))', $searchTerm)
                ->groupEnd();
        }
        return $builder->orderBy('tv_id_vlan', 'ASC')->get()->getResultArray();
    }

}