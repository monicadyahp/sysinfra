<?php

namespace App\Models\TransSwitchManaged;

use CodeIgniter\Model;

class TransSwitchManagedModel extends Model
{
    protected $db_sysinfra;
    protected $db_postgree;

    public function __construct()
    {
        $this->db_sysinfra = db_connect('jinsystem');
        $this->db_postgree = db_connect('jincommon');
    }

    public function getData()
    {
        $query = "
            SELECT
                tsm_id,
                tsm_assetno,
                tsm_assetname,
                tsm_receivedate,
                tsm_ipaddress,
                tsm_location,
                tsm_port,
                tsm_status,
                tsm_lastuser,
                tsm_lastupdate
            FROM t_switchmanaged
            WHERE tsm_status <> 25
            ORDER BY tsm_lastupdate DESC
        ";

        return $this->db_sysinfra->query($query)->getResult();
    }

    public function getSwitchManagedById($id)
    {
        return $this->db_sysinfra->query("
            SELECT *
            FROM t_switchmanaged
            WHERE tsm_status <> 25 AND tsm_id = ?
        ", [$id])->getRow();
    }

    public function storeData($data)
    {
        // Validate asset availability
        if (!empty($data['asset_no'])) {
            if (!$this->isAssetAvailable($data['asset_no'])) {
                return [
                    'status' => false,
                    'message' => 'Asset No is already in use by another switch. Please select a different asset.'
                ];
            }
        }

        // Get asset details if asset_no is provided
        $assetDetails = null;
        if (!empty($data['asset_no'])) {
            $assetDetails = $this->getAssetNo($data['asset_no']);
            
            if (!$assetDetails) {
                return [
                    'status' => false,
                    'message' => 'Selected asset not found in system.'
                ];
            }
        }

        // Use provided receive_date if available, otherwise use from asset details
        $finalReceiveDate = null;
        if (!empty($data['receive_date'])) {
            $finalReceiveDate = $data['receive_date'];
        } elseif ($assetDetails && $assetDetails->e_receivedate) {
            $finalReceiveDate = date('Y-m-d', strtotime($assetDetails->e_receivedate));
        }

        // Convert asset no to integer if provided
        $assetNo = null;
        if (!empty($data['asset_no']) && is_numeric($data['asset_no'])) {
            $assetNo = (int)$data['asset_no'];
        }

        // Validate port count
        $portCount = null;
        if (!empty($data['port_count']) && is_numeric($data['port_count'])) {
            $portCount = (int)$data['port_count'];
            if ($portCount < 1) {
                return [
                    'status' => false,
                    'message' => 'Max port must be more than 1.'
                ];
            }
        }

        $insertData = [
            'tsm_assetno'       => $assetNo,
            'tsm_assetname'     => isset($data['asset_name']) && $data['asset_name'] !== '' ? strtoupper(trim($data['asset_name'])) : null,
            'tsm_receivedate'   => $finalReceiveDate,
            'tsm_ipaddress'     => isset($data['ip']) && $data['ip'] !== '' ? $data['ip'] : null,
            'tsm_location'      => isset($data['location']) && $data['location'] !== '' ? $data['location'] : null,
            'tsm_port'          => $portCount,
            'tsm_status'        => 1,
            'tsm_lastuser'      => session()->get('user_info')['em_emplcode'],
            'tsm_lastupdate'    => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_switchmanaged')->insert($insertData);

            // Update IP status to used (1) for new IP address
            // Panggil handleIPStatusUpdate yang baru untuk mengelola status IP di master
            if (!empty($insertData['tsm_ipaddress'])) {
                $this->handleIPStatusUpdate(null, $insertData['tsm_ipaddress']); // IP lama adalah null untuk entri baru
            }

            return [
                'status' => true,
                'message' => 'Switch Managed created successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error creating Switch Managed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while saving Switch Managed data: ' . $e->getMessage()
            ];
        }
    }

    public function updateData($data)
    {
        // Get old data to compare
        $oldData = $this->getSwitchManagedById($data['tsm_id']);
        $oldAssetNo = $oldData ? $oldData->tsm_assetno : '';
        $oldIPAddress = $oldData ? $oldData->tsm_ipaddress : '';
        $newIPAddress = isset($data['ip']) ? $data['ip'] : '';
        $newAssetNo = null;
        // Validate asset availability (exclude current record)
        if (!empty($data['asset_no']) && is_numeric($data['asset_no'])) {
            $newAssetNo = (int)$data['asset_no'];
        }

        // Get asset details if asset_no is provided
        $assetDetails = null;
        if (!empty($data['asset_no'])) {
            $assetDetails = $this->getAssetNo($data['asset_no']);
            
            if (!$assetDetails) {
                return [
                    'status' => false,
                    'message' => 'Selected asset not found in system.'
                ];
            }
        }

        // Use provided receive_date if available, otherwise use from asset details
        $finalReceiveDate = null;
        if (!empty($data['receive_date'])) {
            $finalReceiveDate = $data['receive_date'];
        } elseif ($assetDetails && $assetDetails->e_receivedate) {
            $finalReceiveDate = date('Y-m-d', strtotime($assetDetails->e_receivedate));
        }

        // Validate port count
        $portCount = null;
        if (!empty($data['port_count']) && is_numeric($data['port_count'])) {
            $portCount = (int)$data['port_count'];
            if ($portCount < 1) {
                return [
                    'status' => false,
                    'message' => 'Max port must be more than 1.'
                ];
            }
        }

        $updateData = [
            'tsm_assetno' => $newAssetNo,
            // 'tsm_assetno'       => isset($data['asset_no']) && $data['asset_no'] !== '' ? $data['asset_no'] : null,
            'tsm_assetname'     => isset($data['asset_name']) && $data['asset_name'] !== '' ? strtoupper(trim($data['asset_name'])) : null,
            'tsm_receivedate'   => $finalReceiveDate,
            'tsm_ipaddress'     => isset($data['ip']) && $data['ip'] !== '' ? $data['ip'] : null,
            'tsm_location'      => isset($data['location']) && $data['location'] !== '' ? $data['location'] : null,
            'tsm_port'          => $portCount,
            'tsm_lastuser'      => session()->get('user_info')['em_emplcode'],
            'tsm_lastupdate'    => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_switchmanaged')
                ->where('tsm_id', $data['tsm_id'])
                ->update($updateData);

            // Panggil handleIPStatusUpdate yang baru untuk mengelola status IP di master
            $this->handleIPStatusUpdate($oldIPAddress, $updateData['tsm_ipaddress']);

            return [
                'status' => true,
                'message' => 'Switch Managed updated successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating Switch Managed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while updating Switch Managed data: ' . $e->getMessage()
            ];
        }
    }

    public function deleteData($id)
    {
        // Get switch data to release IP address
        $switch = $this->getSwitchManagedById($id);

        try {
            // Start transaction to ensure all operations succeed or fail together
            $this->db_sysinfra->transStart();

            // Delete main record
            $this->db_sysinfra->table('t_switchmanaged')
                ->where('tsm_id', $id)
                ->update([
                    'tsm_status'     => 25,
                    'tsm_lastuser'   => session()->get('user_info')['em_emplcode'],
                    'tsm_lastupdate' => date('Y-m-d H:i:s')
                ]);

             // Mark all related port as deleted (status = 25)
            $this->db_sysinfra->table('t_switchmanageddetail')
                ->where('tsd_switchid', $id)
                ->where('tsd_status <>', 25) // Only update non-deleted equipment
                ->update([
                    'tsd_status'     => 25,
                    'tsd_lastuser'   => session()->get('user_info')['em_emplcode'],
                    'tsd_lastupdate' => date('Y-m-d H:i:s')
                ]);

            // Complete transaction
            $this->db_sysinfra->transComplete();

            // Release IP address when switch is deleted
            // Release IP address when switch is deleted using the comprehensive handler
            if ($switch && !empty($switch->tsm_ipaddress)) {
                // Untuk delete, IP lama adalah IP dari switch yang dihapus, dan IP baru adalah null
                $this->handleIPStatusUpdate($switch->tsm_ipaddress, null);
            }

            return [
                'status' => true,
                'message' => 'Switch Managed has been deleted successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting Switch Managed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Failed to delete Switch Managed'
            ];
        }
    }

    public function isAssetAvailable($assetNo, $excludeId = null)
    {
        if (empty($assetNo)) {
            return true; // Empty asset no is allowed
        }
        
        $query = "
            SELECT COUNT(*) as count 
            FROM t_switchmanaged 
            WHERE tsm_assetno = ?
            AND tsm_status <> 25
        ";
        
        $params = [$assetNo];
        
        // Exclude current record when updating
        if ($excludeId !== null) {
            $query .= " AND tsm_id <> ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db_sysinfra->query($query, $params)->getRow();
        
        return $result->count == 0;
    }

    // public function isIpUsedByAnotherSwitch($ipAddress, $excludeSwitchId = null)
    // {
    //     if (empty($ipAddress)) {
    //         return false; // IP kosong tidak dianggap "used"
    //     }

    //     // Check if the IP is used in t_switchmanaged by another entry
    //     $builderSwitch = $this->db_sysinfra->table('t_switchmanaged');
    //     $builderSwitch->where('tsm_ipaddress', $ipAddress);
    //     $builderSwitch->where('tsm_status <>', 25); // Exclude soft-deleted entries
    //     if ($excludeSwitchId !== null) {
    //         $builderSwitch->where('tsm_id <>', $excludeSwitchId);
    //     }
    //     $switchCount = $builderSwitch->countAllResults();

    //     if ($switchCount > 0) {
    //         return true; // Digunakan oleh switch lain
    //     }

    //     // PENTING: Sertakan juga pengecekan di t_pc dan t_pcservervm
    //     // agar IP benar-benar unik di seluruh sistem (PC, VM, Switch)
    //     // jika memang itu yang diinginkan.
    //     $builderPc = $this->db_sysinfra->table('t_pc');
    //     $builderPc->where('tpc_ipaddress', $ipAddress);
    //     $builderPc->where('tpc_status <>', 25);
    //     $pcCount = $builderPc->countAllResults();

    //     $builderVm = $this->db_sysinfra->table('t_pcservervm');
    //     $builderVm->where('tpv_ipaddress', $ipAddress);
    //     $builderVm->where('tpv_status <>', 25);
    //     $vmCount = $builderVm->countAllResults();

    //     if ($pcCount > 0 || $vmCount > 0) {
    //         return true; // Digunakan oleh PC atau VM
    //     }

    //     return false; // Tidak digunakan oleh entitas lain
    // }

    /**
     * Checks if an IP address is currently used by any active device (PC, VM, or Switch).
     * Status 0 means Used, Status 1 means Unused, Status 25 means Soft Deleted.
     *
     * @param string $ipAddress The IP address to check.
     * @param int|null $excludeCurrentEntityId ID of the entity currently being edited (to exclude itself from the check).
     * @param string $entityType The type of entity being checked ('switch', 'pc', 'vm').
     * @return bool True if the IP is used by an active device, false otherwise.
     */
    public function isIpUsedByAnyActiveDevice($ipAddress, $excludeCurrentEntityId = null, $entityType = 'switch')
    {
        if (empty($ipAddress)) {
            return false;
        }

        // Check t_switchmanaged
        $builderSwitch = $this->db_sysinfra->table('t_switchmanaged');
        $builderSwitch->where('tsm_ipaddress', $ipAddress);
        $builderSwitch->where('tsm_status <>', 25); // Exclude soft-deleted
        if ($entityType === 'switch' && $excludeCurrentEntityId !== null) {
            $builderSwitch->where('tsm_id <>', $excludeCurrentEntityId);
        }
        // Menurut definisi baru, 'Used' adalah status 0
        $builderSwitch->where('tsm_status', 0); // Only check actively used IPs
        $switchCount = $builderSwitch->countAllResults();
        if ($switchCount > 0) {
            return true;
        }

        // Check t_pc
        $builderPc = $this->db_sysinfra->table('t_pc');
        $builderPc->where('tpc_ipaddress', $ipAddress);
        $builderPc->where('tpc_status <>', 25); // Exclude soft-deleted
        if ($entityType === 'pc' && $excludeCurrentEntityId !== null) {
             $builderPc->where('tpc_id <>', $excludeCurrentEntityId);
        }
        // Menurut definisi baru, 'Used' adalah status 0
        $builderPc->where('tpc_status', 0); // Only check actively used IPs
        $pcCount = $builderPc->countAllResults();
        if ($pcCount > 0) {
            return true;
        }

        // Check t_pcservervm
        $builderVm = $this->db_sysinfra->table('t_pcservervm');
        $builderVm->where('tpv_ipaddress', $ipAddress);
        $builderVm->where('tpv_status <>', 25); // Exclude soft-deleted
        if ($entityType === 'vm' && $excludeCurrentEntityId !== null) {
            $builderVm->where('tpv_id <>', $excludeCurrentEntityId);
        }
        // Menurut definisi baru, 'Used' adalah status 0
        $builderVm->where('tpv_status', 0); // Only check actively used IPs
        $vmCount = $builderVm->countAllResults();
        if ($vmCount > 0) {
            return true;
        }

        return false;
    }

    public function getAssetNo($assetNo)
    {
        if (empty($assetNo)) {
            return null;
        }
        
        $query = "
                SELECT 
                    e_assetno, 
                    e_equipmentid, 
                    e_serialnumber, 
                    e_equipmentname,
                    e_receivedate,
                    CASE 
                        WHEN e_assetno IS NOT NULL THEN CAST(e_assetno AS VARCHAR)
                        ELSE CAST(e_equipmentid AS VARCHAR)
                    END as display_asset_no
                FROM m_itequipment
                WHERE (
                    (e_assetno = ?)
                    OR 
                    (e_equipmentid = ?)
                )
                AND e_status = 'Active'
                ";
        
        $result = $this->db_sysinfra->query($query, [$assetNo, $assetNo])->getRow();
        
        if ($result) {
            // Format the receive date properly
            $result->formatted_receive_date = null;
            if (!empty($result->e_receivedate)) {
                $result->formatted_receive_date = date('Y-m-d', strtotime($result->e_receivedate));
            }
        }
        
        return $result;
    }

    public function searchAssetNo()
    {
        $query = "
            SELECT 
                e.e_assetno, 
                e.e_equipmentid, 
                e.e_serialnumber, 
                e.e_equipmentname,
                e.e_receivedate,
                CASE 
                    WHEN e.e_assetno IS NOT NULL AND e.e_assetno <> '' THEN CAST(e.e_assetno AS VARCHAR)
                    ELSE CAST(e.e_equipmentid AS VARCHAR)
                END as display_asset_no
            FROM m_itequipment e
            WHERE e.e_status = 'Active'
            AND (
                (
                    e.e_assetno IS NOT NULL AND e.e_assetno <> ''
                    AND NOT EXISTS (
                        SELECT 1 FROM t_switchmanaged sm 
                        -- GANTI BARIS INI:
                        WHERE sm.tsm_assetno = CAST(e.e_assetno AS INTEGER) 
                        AND sm.tsm_status <> 25
                    )
                )
                OR
                (
                    (e.e_assetno IS NULL OR e.e_assetno = '')
                    AND e.e_equipmentid IS NOT NULL
                    AND NOT EXISTS (
                        SELECT 1 FROM t_switchmanaged sm 
                        -- GANTI BARIS INI:
                        WHERE sm.tsm_assetno = e.e_equipmentid 
                        AND sm.tsm_status <> 25
                    )
                )
            )
            ORDER BY 
                CASE WHEN e.e_assetno IS NOT NULL THEN 1 ELSE 2 END,
                e.e_lastupdate DESC
        ";

        return $this->db_sysinfra->query($query)->getResult();
    }

    public function getIPAddressByIP($ipAddress)
    {
        if (empty($ipAddress)) {
            return null;
        }

        $query = "
            SELECT mip_id, mip_vlanid, mip_vlanname, mip_ipadd, mip_status
            FROM m_ipaddress
            WHERE mip_ipadd = ?
        ";

        try {
            return $this->db_sysinfra->query($query, [$ipAddress])->getRow();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransSwitchManagedModel::getIPAddressByIP: ' . $e->getMessage());
            return null;
        }
    }

    public function searchIPAddresses($statusFilter = null)
    {
        $query = "
            SELECT mip_id, mip_vlanid, mip_vlanname, mip_ipadd, mip_status
            FROM m_ipaddress
        ";

        $params = [];
        // Logika filter status sama seperti di TransPCModel
        if ($statusFilter !== null && $statusFilter !== 'All' && ($statusFilter === '0' || $statusFilter === '1')) {
            $query .= " WHERE mip_status = ?";
            $params[] = (int)$statusFilter;
        }
        
        $query .= " ORDER BY mip_ipadd ASC";
        
        // Pastikan selalu mengembalikan array, bahkan jika kosong
        return $this->db_sysinfra->query($query, $params)->getResultArray();
    }

    public function updateIPStatus($ipAddress, $status)
    {
        if (empty($ipAddress)) {
            return false;
        }

        try {
            $this->db_sysinfra->table('m_ipaddress')
                ->where('mip_ipadd', $ipAddress) // <-- PERBAIKAN DI SINI
                ->update([
                    'mip_status' => $status,
                    'mip_lastuser' => session()->get('user_info')['em_emplcode'],
                    'mip_lastupdate' => date('Y-m-d H:i:s')
                ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error updating IP status: ' . $e->getMessage());
            return false;
        }
    }

    private function handleIPStatusUpdate($oldIPAddress, $newIPAddress)
    {
        $currentUserId = session()->get('user_info')['em_emplcode'] ?? null;
        if ($currentUserId === null) {
            log_message('error', 'User session not set in handleIPStatusUpdate (Switch Managed).');
            return;
        }

        // Jika IP lama ada dan berbeda dari IP baru, periksa apakah ada entitas aktif lain yang menggunakannya.
        // Jika tidak ada, tandai IP lama sebagai UNUSED (status 1).
        if (!empty($oldIPAddress) && $oldIPAddress !== $newIPAddress) {
            // Periksa apakah IP lama ini masih digunakan oleh switch lain, PC, atau VM
            // Kita perlu memeriksa apakah IP lama masih terpakai oleh entitas aktif (status 0).
            // Kita tidak mengecualikan entitas saat ini karena entitas ini (switch yang IP-nya diganti/dihapus)
            // tidak lagi menggunakan IP lama, sehingga kita ingin melihat apakah ada entitas *lain* yang masih menggunakannya.
            if (!$this->isIpUsedByAnyActiveDevice($oldIPAddress)) { // Panggil tanpa exclude ID
                $this->db_sysinfra->table('m_ipaddress')
                    ->where('mip_ipadd', $oldIPAddress)
                    ->where('mip_status <>', 25) // Pastikan bukan yang soft-deleted
                    ->update([
                        'mip_status' => 1, // 1 = Unused (berdasarkan definisi baru Anda)
                        'mip_lastuser' => $currentUserId,
                        'mip_lastupdate' => date('Y-m-d H:i:s')
                    ]);
                log_message('info', 'Released old IP ' . $oldIPAddress . ' to UNUSED (1).');
            } else {
                log_message('info', 'Old IP ' . $oldIPAddress . ' is still in use by other active devices. Status remains 0 (USED).');
            }
        }

        // Jika IP baru ada dan berbeda dari IP lama, tandai IP baru sebagai USED (status 0).
        if (!empty($newIPAddress) && $oldIPAddress !== $newIPAddress) {
            $currentIpDetails = $this->db_sysinfra->table('m_ipaddress')
                                                   ->select('mip_status')
                                                   ->where('mip_ipadd', $newIPAddress)
                                                   ->get()
                                                   ->getRow();

            if ($currentIpDetails) {
                if ($currentIpDetails->mip_status == 1) { // Hanya update jika statusnya UNUSED (1)
                    $this->db_sysinfra->table('m_ipaddress')
                        ->where('mip_ipadd', $newIPAddress)
                        ->update([
                            'mip_status' => 0, // 0 = Used (berdasarkan definisi baru Anda)
                            'mip_lastuser' => $currentUserId,
                            'mip_lastupdate' => date('Y-m-d H:i:s')
                        ]);
                    log_message('info', 'Marked new IP ' . $newIPAddress . ' as USED (0).');
                } else if ($currentIpDetails->mip_status == 0) {
                    // IP sudah 0 (Used), mungkin oleh entitas lain atau switch ini sendiri jika ini update.
                    log_message('warning', 'Attempted to assign IP ' . $newIPAddress . ' which is already marked as USED (status: ' . $currentIpDetails->mip_status . '). No status change needed in m_ipaddress.');
                } else if ($currentIpDetails->mip_status == 25) {
                    // IP soft-deleted, ini harusnya ditangani di validasi awal di controller/frontend
                    log_message('warning', 'Attempted to assign IP ' . $newIPAddress . ' which is SOFT DELETED (status: 25).');
                }
            } else {
                log_message('warning', 'Attempted to use IP ' . $newIPAddress . ' which is not in m_ipaddress master data.');
            }
        }
    }

    public function getlocations()
    {
        return $this->db_sysinfra->query("
            SELECT mpl_id, mpl_name
            FROM m_pclocation
            WHERE mpl_status <> 25
            ORDER BY mpl_name ASC
        ")->getResult();
    }

    public function getSwitchDetailPortsByHeaderId($tsd_switchid)
    {
        $query = "
            SELECT 
                tsd_id, 
                tsd_switchid, 
                tsd_port, 
                tsd_type,
                tsd_vlanid, 
                tsd_vlanname, 
                tsd_status,
                tsd_lastupdate, 
                tsd_lastuser
            FROM t_switchmanageddetail 
            WHERE tsd_switchid = ? 
            AND tsd_status <> 25
            ORDER BY tsd_port ASC
        ";
        
        return $this->db_sysinfra->query($query, [$tsd_switchid])->getResult();
    }

    public function getSwitchDetailPortById($tsd_id)
    {
        $query = "
            SELECT 
                tsd_id, 
                tsd_switchid, 
                tsd_port, 
                tsd_type,
                tsd_vlanid, 
                tsd_vlanname, 
                tsd_status,
                tsd_lastupdate, 
                tsd_lastuser
            FROM t_switchmanageddetail
            WHERE tsd_id = ?
            AND tsd_status <> 25
        ";
        
        return $this->db_sysinfra->query($query, [$tsd_id])->getRow();
    }

    public function countSwitchDetailPortsByHeaderId($tsd_switchid)
    {
        $query = "
            SELECT COUNT(*) as total
            FROM t_switchmanageddetail
            WHERE tsd_switchid = ?
            AND tsd_status <> 25
        ";
        
        $result = $this->db_sysinfra->query($query, [$tsd_switchid])->getRow();
        return $result ? $result->total : 0;
    }

    public function storeSwitchDetailPort($data)
    {
        // Validate required fields
        if (empty($data['tsd_switchid'])) {
            return [
                'status' => false,
                'message' => 'Switch ID is required.'
            ];
        }

        if (empty($data['tsd_vlanid'])) {
            return [
                'status' => false,
                'message' => 'VLAN ID is required.'
            ];
        }

        // Validate VLAN ID is numeric
        if (!is_numeric($data['tsd_vlanid'])) {
            return [
                'status' => false,
                'message' => 'VLAN ID must be a number.'
            ];
        }

        // Validate port number if provided
        if (!empty($data['tsd_port']) && (!is_numeric($data['tsd_port']) || (int)$data['tsd_port'] < 1)) {
            return [
                'status' => false,
                'message' => 'Port number must be greater than 0.'
            ];
        }

        // Check if port is already used for this switch
        if (!empty($data['tsd_port'])) {
            $checkPortQuery = "
                SELECT COUNT(*) as count 
                FROM t_switchmanageddetail 
                WHERE tsd_switchid = ? 
                AND tsd_port = ? 
                AND tsd_status <> 25
            ";
            
            $portCheck = $this->db_sysinfra->query($checkPortQuery, [$data['tsd_switchid'], $data['tsd_port']])->getRow();
            
            if ($portCheck->count > 0) {
                return [
                    'status' => false,
                    'message' => 'Port number is already in use for this switch.'
                ];
            }
        }

        $insertData = [
            'tsd_switchid' => (int)$data['tsd_switchid'],
            'tsd_port' => !empty($data['tsd_port']) ? (int)$data['tsd_port'] : null,
            'tsd_type' => !empty($data['tsd_type']) ? $data['tsd_type'] : null,
            'tsd_vlanid' => (int)$data['tsd_vlanid'],
            'tsd_vlanname' => !empty($data['tsd_vlanname']) ? $data['tsd_vlanname'] : null,
            'tsd_status' => isset($data['tsd_status']) ? (int)$data['tsd_status'] : 1,
            'tsd_lastuser' => session()->get('user_info')['em_emplcode'],
            'tsd_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_switchmanageddetail')->insert($insertData);
            
            return [
                'status' => true,
                'message' => 'Port detail added successfully.',
                'id' => $this->db_sysinfra->insertID()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error creating switch detail port: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while saving port detail: ' . $e->getMessage()
            ];
        }
    }

    public function updateSwitchDetailPort($tsd_id, $data)
    {
        // Validate required fields
        if (empty($data['tsd_switchid'])) {
            return [
                'status' => false,
                'message' => 'Switch ID is required.'
            ];
        }

        if (empty($data['tsd_vlanid'])) {
            return [
                'status' => false,
                'message' => 'VLAN ID is required.'
            ];
        }

        // Validate VLAN ID is numeric
        if (!is_numeric($data['tsd_vlanid'])) {
            return [
                'status' => false,
                'message' => 'VLAN ID must be a number.'
            ];
        }

        // Validate port number if provided
        if (!empty($data['tsd_port']) && (!is_numeric($data['tsd_port']) || (int)$data['tsd_port'] < 1)) {
            return [
                'status' => false,
                'message' => 'Port number must be greater than 0.'
            ];
        }

        // Check if port is already used for this switch (excluding current record)
        if (!empty($data['tsd_port'])) {
            $checkPortQuery = "
                SELECT COUNT(*) as count 
                FROM t_switchmanageddetail 
                WHERE tsd_switchid = ? 
                AND tsd_port = ? 
                AND tsd_id <> ?
                AND tsd_status <> 25
            ";
            
            $portCheck = $this->db_sysinfra->query($checkPortQuery, [$data['tsd_switchid'], $data['tsd_port'], $tsd_id])->getRow();
            
            if ($portCheck->count > 0) {
                return [
                    'status' => false,
                    'message' => 'Port number is already in use for this switch.'
                ];
            }
        }

        $updateData = [
            'tsd_switchid' => (int)$data['tsd_switchid'],
            'tsd_port' => !empty($data['tsd_port']) ? (int)$data['tsd_port'] : null,
            'tsd_type' => !empty($data['tsd_type']) ? $data['tsd_type'] : null,
            'tsd_vlanid' => (int)$data['tsd_vlanid'],
            'tsd_vlanname' => !empty($data['tsd_vlanname']) ? $data['tsd_vlanname'] : null,
            'tsd_status' => isset($data['tsd_status']) ? (int)$data['tsd_status'] : 1,
            'tsd_lastuser' => session()->get('user_info')['em_emplcode'],
            'tsd_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_switchmanageddetail')
                            ->where('tsd_id', $tsd_id)
                            ->update($updateData);

            return [
                'status' => true,
                'message' => 'Port detail updated successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating switch detail port: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while updating port detail: ' . $e->getMessage()
            ];
        }
    }

    public function deleteSwitchDetailPort($tsd_id)
    {
        try {
            $this->db_sysinfra->table('t_switchmanageddetail')
                            ->where('tsd_id', $tsd_id)
                            ->update([
                                'tsd_status' => 25,
                                'tsd_lastuser' => session()->get('user_info')['em_emplcode'],
                                'tsd_lastupdate' => date('Y-m-d H:i:s')
                            ]);

            return [
                'status' => true,
                'message' => 'Port detail deleted successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting switch detail port: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while deleting port detail: ' . $e->getMessage()
            ];
        }
    }

    public function getAvailablePortsForSwitch($tsd_switchid)
    {
        // Get switch port count
        $switch = $this->getSwitchManagedById($tsd_switchid);
        if (!$switch || !$switch->tsm_port) {
            return [];
        }

        $maxPorts = (int)$switch->tsm_port;
        
        // Get already used ports (excluding deleted ones) using raw SQL
        $usedPortsQuery = "
            SELECT tsd_port
            FROM t_switchmanageddetail
            WHERE tsd_switchid = ?
            AND tsd_status <> 25
            AND tsd_port IS NOT NULL
        ";
        
        $usedPorts = $this->db_sysinfra->query($usedPortsQuery, [$tsd_switchid])->getResult();
        $usedPortNumbers = array_column($usedPorts, 'tsd_port');
        
        // Generate available ports
        $availablePorts = [];
        for ($i = 1; $i <= $maxPorts; $i++) {
            if (!in_array($i, $usedPortNumbers)) {
                $availablePorts[] = $i;
            }
        }
        
        return $availablePorts;
    }

    public function getVlanData()
    {
        $query = "
            SELECT mv_id, mv_vlanid, mv_name
            FROM m_vlan
            WHERE mv_status <> 25
            ORDER BY mv_vlanid ASC
        ";
        
        return $this->db_sysinfra->query($query)->getResultArray();
    }

    public function getVlanById($vlanId)
    {
        if (empty($vlanId)) {
            return null;
        }
        
        $query = "
            SELECT mv_id, mv_vlanid, mv_name
            FROM m_vlan
            WHERE mv_vlanid = ?
        ";
        
        return $this->db_sysinfra->query($query, [$vlanId])->getRowArray();
    }

    public function getUserDisplayName($userId)
    {
        if (empty($userId)) {
            return 'N/A';
        }

        if (is_numeric($userId)) {
            // Try employee table first
            $employee = $this->db_postgree->table('tbmst_employee')
                                ->select('em_emplname')
                                ->where('em_emplcode', $userId)
                                ->get()
                                ->getRow();

            if ($employee) {
                return $employee->em_emplname;
            }

            // Try user access table
            $userAccess = $this->db_postgree->table('tbua_useraccess')
                                   ->select('ua_username')
                                   ->where('ua_userid', $userId)
                                   ->get()
                                   ->getRow();

            if ($userAccess) {
                return $userAccess->ua_username;
            }

            return $userId;
        } else {
            return $userId;
        }
    }

    public function getSwitchManagedFullDetails($switchId)
    {
        $switch = $this->getSwitchManagedById($switchId);
        if (!$switch) {
            return null;
        }
        
        $ports = $this->getSwitchDetailPortsByHeaderId($switchId);
        
        return [
            'switch' => $switch,
            'ports' => $ports
        ];
    }
}