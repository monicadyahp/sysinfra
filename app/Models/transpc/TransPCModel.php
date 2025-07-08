<?php

namespace App\Models\transpc;

use CodeIgniter\Model;

class TransPCModel extends Model
{
    protected $db_postgree;
    protected $db_sysinfra;
    protected $session; // Add property to store session instance

    public function __construct()
    {
        $this->db_postgree = \Config\Database::connect('jincommon');
        $this->db_sysinfra = \Config\Database::connect('jinsystem');
        $this->session = \Config\Services::session(); // Initialize session
    }

    /**
     * Helper function to get the current user's ID from the session.
     * Assumes 'user_info' is set in session with 'ua_userid' key upon login.
     *
     * @return int|null The user ID (ua_userid) or null if not found.
     */
    private function getCurrentUserId()
    {
        // Get the entire user_info array from session
        $userInfo = $this->session->get('user_info');
        
        // Return ua_userid if available, otherwise null
        return $userInfo['ua_userid'] ?? null;
    }

    public function getData($statusFilter = null, $typeFilter = null)
    {
        $builder = $this->db_sysinfra->table('t_pc');
        $builder->where('tpc_status <>', 25);

        if ($statusFilter !== null && $statusFilter !== 'All') {
            $builder->where('tpc_status', (int)$statusFilter);
        }

        if ($typeFilter !== null && $typeFilter !== 'All') {
            $builder->where('tpc_type', (int)$typeFilter);
        }

        $builder->orderBy('tpc_id', 'DESC');

        try {
            return $builder->get()->getResult();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getData: ' . $e->getMessage());
            return [];
        }
    }


    public function getPCById($id)
    {
        try {
            return $this->db_sysinfra->table('t_pc')
                                       ->where('tpc_status <>', 25)
                                       ->where('tpc_id', $id)
                                       ->get()
                                       ->getRow();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getPCById: ' . $e->getMessage());
            return null;
        }
    }

    public function storeData($data)
    {
        $lastUser = $this->getCurrentUserId();

        if ($lastUser === null) {
            return ['status' => false, 'message' => 'User session is not set. Please log in again. (Code: TPCM-S1)'];
        }

        $assetNo = null;
        if (isset($data['pc_assetno']) && $data['pc_assetno'] !== '') {
            $assetNo = is_numeric($data['pc_assetno']) ? (int)$data['pc_assetno'] : $data['pc_assetno'];
            
            if (!$this->isAssetAvailable($assetNo)) {
                return ['status' => false, 'message' => 'Asset No is already in use by another PC. Please select a different asset.'];
            }
            
            $pcAssetDetails = $this->getAssetNo($assetNo);
            if (!$pcAssetDetails) {
                return ['status' => false, 'message' => 'Selected asset not found in system.'];
            }
        }

        $finalPCReceiveDate = null;
        if (!empty($data['pc_receive_date'])) {
            $finalPCReceiveDate = $data['pc_receive_date'];
        } elseif (isset($pcAssetDetails) && !empty($pcAssetDetails->e_receivedate)) {
            $finalPCReceiveDate = date('Y-m-d', strtotime($pcAssetDetails->e_receivedate));
        }

        $insertData = [
            'tpc_type'          => $data['pc_type'] ?? 1,
            'tpc_name'          => $data['pc_name'] ?? null,
            'tpc_assetno'       => is_numeric($assetNo) ? (int)$assetNo : null,
            'tpc_pcreceivedate' => $finalPCReceiveDate,
            'tpc_osname'        => $data['os_name'] ?? null,
            'tpc_ipaddress'     => $data['ip_address'] ?? null,
            'tpc_user'          => $data['user'] ?? null,
            'tpc_location'      => $data['location'] ?? null,
            'tpc_status'        => $data['pcstatus'] ?? 1,
            'tpc_lastuser'      => $lastUser, // Use the fetched ua_userid
            'tpc_lastupdate'    => date('Y-m-d H:i:s')
        ];
                                        
        try {
            $this->db_sysinfra->table('t_pc')->insert($insertData);

            if (!empty($insertData['tpc_ipaddress'])) {
                $ipDetails = $this->getIPAddressByIP($insertData['tpc_ipaddress']);
                if (!$ipDetails) {
                    return ['status' => false, 'message' => 'Selected IP Address is not found in IP Master. Please select another IP.'];
                }
                // Only update status to 1 if it's currently 0 (unused)
                if ($ipDetails->mip_status == 0) {
                   $this->updateIPStatus($insertData['tpc_ipaddress'], 1);
                } else {
                   log_message('warning', 'Attempted to use IP ' . $insertData['tpc_ipaddress'] . ' which is already marked as used (status: ' . $ipDetails->mip_status . '). PC created, but IP status was not changed from used.');
                }
            }

            return ['status' => true, 'message' => 'PC created successfully.'];
        } catch (\Exception $e) {
            log_message('error', 'Error creating PC: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error occurred while saving PC data: ' . $e->getMessage()];
        }
    }

    public function updateData($data)
    {
        $lastUser = $this->getCurrentUserId();

        if ($lastUser === null) {
            return ['status' => false, 'message' => 'User session is not set. Please log in again. (Code: TPCM-S1)'];
        }

        $oldPC = $this->getPCById($data['tpc_id']);
        if (!$oldPC) {
             return ['status' => false, 'message' => 'Original PC data not found for update.'];
        }

        $oldIPAddress = $oldPC->tpc_ipaddress ?? '';
        $oldAssetNo = $oldPC->tpc_assetno ?? '';
        $newIPAddress = $data['ip_address'] ?? '';
        $newAssetNoInput = $data['pc_assetno'] ?? '';
        $newAssetNo = is_numeric($newAssetNoInput) ? (int)$newAssetNoInput : $newAssetNoInput;

        if (!empty($newAssetNoInput) && $newAssetNo !== $oldAssetNo) {
            if (!$this->isAssetAvailable($newAssetNo, $data['tpc_id'])) {
                return ['status' => false, 'message' => 'Asset No is already in use by another PC. Please select a different asset.'];
            }
            $pcAssetDetails = $this->getAssetNo($newAssetNo);
            if (!$pcAssetDetails) {
                return ['status' => false, 'message' => 'Selected asset not found in system.'];
            }
        } else {
             $pcAssetDetails = $this->getAssetNo($oldAssetNo); 
        }

        $finalPCReceiveDate = null;
        if (!empty($data['pc_receive_date'])) {
            $finalPCReceiveDate = $data['pc_receive_date'];
        } elseif (isset($pcAssetDetails) && !empty($pcAssetDetails->e_receivedate)) {
            $finalPCReceiveDate = date('Y-m-d', strtotime($pcAssetDetails->e_receivedate));
        }

        $updateData = [
            'tpc_type'          => $data['pc_type'] ?? ($oldPC->tpc_type ?? 1),
            'tpc_name'          => $data['pc_name'] ?? null,
            'tpc_assetno'       => is_numeric($newAssetNo) ? (int)$newAssetNo : null, 
            'tpc_pcreceivedate' => $finalPCReceiveDate,
            'tpc_osname'        => $data['os_name'] ?? null,
            'tpc_ipaddress'     => $newIPAddress,
            'tpc_user'          => $data['user'] ?? null,
            'tpc_location'      => $data['location'] ?? null,
            'tpc_status'        => $data['pcstatus'] ?? ($oldPC->tpc_status ?? 1),
            'tpc_lastuser'      => $lastUser, // Use the fetched ua_userid
            'tpc_lastupdate'    => date('Y-m-d H:i:s')
        ];
                                        
        try {
            $this->db_sysinfra->table('t_pc')
                ->where('tpc_id', $data['tpc_id'])
                ->update($updateData);

            $this->handleIPStatusUpdate($oldIPAddress, $updateData['tpc_ipaddress']);

            return ['status' => true, 'message' => 'PC updated successfully.'];
        } catch (\Exception $e) {
            log_message('error', 'Error updating PC: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error occurred while updating PC data: ' . $e->getMessage()];
        }
    }

    public function deleteData($id)
    {
        $pc = $this->getPCById($id);
        $lastUser = $this->getCurrentUserId();

        if ($lastUser === null) {
            return ['status' => false, 'message' => 'User session is not set. Please log in again. (Code: TPCM-S1)'];
        }

        try {
            $this->db_sysinfra->transStart();

            $this->db_sysinfra->table('t_pc')
                ->where('tpc_id', $id)
                ->update([
                    'tpc_status'    => 25,
                    'tpc_lastuser'  => $lastUser, // Use the fetched ua_userid
                    'tpc_lastupdate' => date('Y-m-d H:i:s')
                ]);

            $this->db_sysinfra->table('t_pcitequipment')
                ->where('tpi_pcid', $id)
                ->where('tpi_status <>', 25)
                ->update([
                    'tpi_status'    => 25,
                    'tpi_lastuser'  => $lastUser, // Use the fetched ua_userid
                    'tpi_lastupdate' => date('Y-m-d H:i:s')
                ]);

            $this->db_sysinfra->table('t_pcservervm')
                ->where('tpv_pcid', $id)
                ->where('tpv_status <>', 25)
                ->update([
                    'tpv_status'    => 25,
                    'tpv_lastuser'  => $lastUser, // Use the fetched ua_userid
                    'tpv_lastupdate' => date('Y-m-d H:i:s')
                ]);

            $this->db_sysinfra->transComplete();

            if ($this->db_sysinfra->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            if ($pc && !empty($pc->tpc_ipaddress)) {
                $this->handleIPStatusUpdate($pc->tpc_ipaddress, null);
            }

            return ['status' => true, 'message' => 'PC and all related data have been marked as deleted successfully'];
        } catch (\Exception $e) {
            log_message('error', 'Error marking PC as deleted: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to delete PC: ' . $e->getMessage()];
        }
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
                COALESCE(
                    NULLIF(e.e_assetno, ''), 
                    e.e_equipmentid::VARCHAR 
                ) AS display_asset_no
            FROM m_itequipment e
            WHERE e.e_status = 'Active'
            AND (
                (
                    e.e_assetno IS NOT NULL AND e.e_assetno <> ''
                    AND NOT EXISTS (
                        SELECT 1 FROM t_pc pc 
                        WHERE pc.tpc_assetno = CAST(e.e_assetno AS INTEGER) 
                        AND pc.tpc_status <> 25
                    )
                    AND NOT EXISTS (
                        SELECT 1 FROM t_pcitequipment pi 
                        WHERE pi.tpi_assetno = CAST(e.e_assetno AS INTEGER) 
                        AND pi.tpi_status <> 25
                    )
                )
                OR
                (
                    (e.e_assetno IS NULL OR e.e_assetno = '')
                    AND e.e_equipmentid IS NOT NULL
                    AND NOT EXISTS (
                        SELECT 1 FROM t_pc pc 
                        WHERE pc.tpc_assetno = e.e_equipmentid 
                        AND pc.tpc_status <> 25
                    )
                    AND NOT EXISTS (
                        SELECT 1 FROM t_pcitequipment pi 
                        WHERE pi.tpi_assetno = e.e_equipmentid 
                        AND pi.tpi_status <> 25
                    )
                )
            )
            ORDER BY 
                CASE WHEN e.e_assetno IS NOT NULL AND e.e_assetno <> '' THEN 1 ELSE 2 END,
                e.e_lastupdate DESC
        ";

        try {
            return $this->db_sysinfra->query($query)->getResult();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::searchAssetNo: ' . $e->getMessage());
            return [];
        }
    }

    public function getAssetNo($assetNo)
    {
        if (empty($assetNo)) {
            return null;
        }
        
        $assetNoInt = is_numeric($assetNo) ? (int)$assetNo : null;
        $assetNoStr = (string)$assetNo; 

        $query = "
            SELECT 
                e_assetno, 
                e_equipmentid, 
                e_serialnumber, 
                e_equipmentname,
                e_receivedate,
                COALESCE(
                    NULLIF(e_assetno, ''), 
                    e_equipmentid::VARCHAR
                ) AS display_asset_no
            FROM m_itequipment
            WHERE e_status = 'Active'
            AND (
                (e_assetno = ? AND e_assetno IS NOT NULL) 
                OR 
                (e_equipmentid = ? AND (e_assetno IS NULL OR e_assetno = '')) 
            )
        ";
        
        try {
            $result = $this->db_sysinfra->query($query, [$assetNoStr, $assetNoInt])->getRow();
            
            if ($result) {
                $result->formatted_receive_date = null;
                if (!empty($result->e_receivedate)) {
                    $result->formatted_receive_date = date('Y-m-d', strtotime($result->e_receivedate));
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getAssetNo: ' . $e->getMessage());
            return null;
        }
    }
    

    public function isAssetAvailable($assetNo, $excludePCId = null)
    {
        if (empty($assetNo)) {
            return true;
        }
        
        $assetNoToCompare = is_numeric($assetNo) ? (int)$assetNo : null;
        if ($assetNoToCompare === null) {
            return false;
        }

        $builder = $this->db_sysinfra->table('t_pc');
        $builder->where('tpc_assetno', $assetNoToCompare); 
        $builder->where('tpc_status <>', 25);
        
        if ($excludePCId !== null) {
            $builder->where('tpc_id <>', $excludePCId);
        }
        
        try {
            $result = $builder->countAllResults();
            return $result == 0;
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::isAssetAvailable: ' . $e->getMessage());
            return false;
        }
    }

    public function isAssetAvailableForEquipment($assetNo, $excludeEquipmentId = null)
    {
        if (empty($assetNo)) {
            return true;
        }
        
        $assetNoToCompare = is_numeric($assetNo) ? (int)$assetNo : null;
        if ($assetNoToCompare === null) {
            return false;
        }

        $builderPc = $this->db_sysinfra->table('t_pc');
        $builderPc->where('tpc_assetno', $assetNoToCompare);
        $builderPc->where('tpc_status <>', 25);
        try {
            $pcUsageCount = $builderPc->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error checking asset availability in t_pc for equipment: ' . $e->getMessage());
            return false;
        }
        
        if ($pcUsageCount > 0) {
            return false;
        }
        
        $builderEquipment = $this->db_sysinfra->table('t_pcitequipment');
        $builderEquipment->where('tpi_assetno', $assetNoToCompare);
        $builderEquipment->where('tpi_status <>', 25);
        
        if ($excludeEquipmentId !== null) {
            $builderEquipment->where('tpi_id <>', $excludeEquipmentId);
        }
        
        try {
            $equipmentUsageCount = $builderEquipment->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error checking asset availability in t_pcitequipment: ' . $e->getMessage());
            return false;
        }
        
        return $equipmentUsageCount == 0;
    }


    public function getIPAddressByIP($ipAddress)
    {
        if (empty($ipAddress)) {
            return null;
        }
                                        
        $builder = $this->db_sysinfra->table('m_ipaddress');
        $builder->select('mip_id, mip_vlanid, mip_vlanname, mip_ipadd, mip_status'); 
        $builder->where('mip_ipadd', $ipAddress); 

        try {
            return $builder->get()->getRow();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getIPAddressByIP: ' . $e->getMessage());
            return null;
        }
    }

    public function searchIPAddresses()
    {
        $builder = $this->db_sysinfra->table('m_ipaddress');
        $builder->select('mip_id, mip_vlanid, mip_vlanname, mip_ipadd, mip_status'); 
        $builder->orderBy('mip_ipadd', 'ASC'); 

        try {
            $results = $builder->get()->getResultArray(); 
            return $results;
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::searchIPAddresses: ' . $e->getMessage());
            return [];
        }
    }

    public function updateIPStatus($ipAddress, $status)
    {
        if (empty($ipAddress)) {
            return false;
        }
        $lastUser = $this->getCurrentUserId();
    
        if ($lastUser === null) {
            log_message('error', 'User session not set in updateIPStatus for IP: ' . $ipAddress);
            return false; // Or throw an exception, depending on desired error handling
        }

        try {
            $this->db_sysinfra->table('m_ipaddress')
                ->where('mip_ipadd', $ipAddress) 
                ->update([
                    'mip_status' => $status,
                    'mip_lastuser' => $lastUser, // Use $lastUser (ua_userid)
                    'mip_lastupdate' => date('Y-m-d H:i:s')
                ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error updating IP status for ' . $ipAddress . ': ' . $e->getMessage());
            return false;
        }
    }

    private function handleIPStatusUpdate($oldIPAddress, $newIPAddress)
    {
        $currentUserId = $this->getCurrentUserId();
    
        if ($currentUserId === null) {
            log_message('error', 'User session not set in handleIPStatusUpdate.');
            return; // Exit if user session is not set, as we can't update last user info
        }
    
        // If old IP address exists and is different from the new one, and no other PC/VM is using it, mark it as unused (0)
        if (!empty($oldIPAddress) && $oldIPAddress !== $newIPAddress) {
            $countPcUsingOldIp = $this->db_sysinfra->table('t_pc')
                                                    ->where('tpc_ipaddress', $oldIPAddress)
                                                    ->where('tpc_status <>', 25)
                                                    ->countAllResults();
            $countVmUsingOldIp = $this->db_sysinfra->table('t_pcservervm')
                                                    ->where('tpv_ipaddress', $oldIPAddress)
                                                    ->where('tpv_status <>', 25)
                                                    ->countAllResults();
    
            if ($countPcUsingOldIp == 0 && $countVmUsingOldIp == 0) {
                $this->db_sysinfra->table('m_ipaddress')
                    ->where('mip_ipadd', $oldIPAddress)
                    ->update([
                        'mip_status' => 0,
                        'mip_lastuser' => $currentUserId,
                        'mip_lastupdate' => date('Y-m-d H:i:s')
                    ]);
            }
        }
    
        // If a new IP address is provided and it's different from the old one, mark it as used (1) if it's currently unused (0)
        if (!empty($newIPAddress) && $oldIPAddress !== $newIPAddress) {
            $currentIpDetails = $this->db_sysinfra->table('m_ipaddress')
                                                    ->select('mip_status')
                                                    ->where('mip_ipadd', $newIPAddress)
                                                    ->get()
                                                    ->getRow();
    
            if ($currentIpDetails) {
                if ($currentIpDetails->mip_status == 0) {
                    $this->db_sysinfra->table('m_ipaddress')
                        ->where('mip_ipadd', $newIPAddress)
                        ->update([
                            'mip_status' => 1,
                            'mip_lastuser' => $currentUserId,
                            'mip_lastupdate' => date('Y-m-d H:i:s')
                        ]);
                } else {
                    // Log a warning if the new IP is already marked as used by another entity (could be a different PC or VM)
                    log_message('warning', 'Attempted to assign IP ' . $newIPAddress . ' which is already marked as used (status: ' . $currentIpDetails->mip_status . ') by another entity. New PC/VM might share this IP logically in DB, but master status remains as is.');
                }
            } else {
                log_message('warning', 'Attempted to use IP ' . $newIPAddress . ' which is not in m_ipaddress master data.');
            }
        }
    }

    public function getEmployeeById($employeeId)
    {
        try {
            // Updated to use ua_emplcode for joining from tbmst_employee
            // This method seems to retrieve employee details based on em_emplcode, which is an int.
            // The getUserDisplayName function will call this when it can't find ua_userid.
            return $this->db_postgree->table('tbmst_employee AS emp')
                                       ->select('emp.em_emplcode, emp.em_emplname, sec.sec_section, sec.sec_sectioncode AS em_sectioncode, pos.pm_positionname')
                                       ->join('tbmst_section AS sec', 'emp.em_sectioncode = sec.sec_sectioncode', 'left')
                                       ->join('tbmst_position AS pos', 'emp.em_positioncode = pos.pm_code', 'left')
                                       ->where('emp.em_emplcode', $employeeId)
                                       ->where('emp.em_emplstatus <', 200)
                                       ->get()
                                       ->getRow();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getEmployeeById: ' . $e->getMessage());
            return null;
        }
    }

    public function searchEmployees()
    {
        try {
            return $this->db_postgree->table('tbmst_employee AS emp')
                                       ->select('emp.em_emplcode, emp.em_emplname, sec.sec_section, sec.sec_sectioncode AS em_sectioncode, pos.pm_positionname')
                                       ->join('tbmst_section AS sec', 'emp.em_sectioncode = sec.sec_sectioncode', 'left')
                                       ->join('tbmst_position AS pos', 'emp.em_positioncode = pos.pm_code', 'left')
                                       ->where('emp.em_emplstatus <', 200)
                                       ->get()
                                       ->getResult();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::searchEmployees: ' . $e->getMessage());
            return [];
        }
    }

    public function getLocations()
    {
        try {
            return $this->db_sysinfra->table('m_pclocation')
                                       ->where('mpl_status <>', 25)
                                       ->orderBy('mpl_name', 'ASC')
                                       ->get()
                                       ->getResult();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getLocations: ' . $e->getMessage());
            return [];
        }
    }

    public function getOSList()
    {
        try {
            return $this->db_sysinfra->table('m_pcos')
                                       ->where('mpo_status <>', 25)
                                       ->orderBy('mpo_osname', 'ASC')
                                       ->get()
                                       ->getResult();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getOSList: ' . $e->getMessage());
            return [];
        }
    }

    public function getPCSpecs($pcId)
    {
        if (empty($pcId)) {
            return null;
        }
        
        try {
            return $this->db_sysinfra->table('t_pcspec')
                                       ->select('tps_id, tps_pcid, tps_processor, tps_ram, tps_storage, tps_vga, tps_ethernet, tps_lastuser, tps_lastupdate')
                                       ->where('tps_pcid', $pcId)
                                       ->get()
                                       ->getRow();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getPCSpecs: ' . $e->getMessage());
            return null;
        }
    }

    public function getPCEquipment($pcId)
    {
        if (empty($pcId)) {
            return [];
        }
        
        try {
            return $this->db_sysinfra->table('t_pcitequipment')
                                       ->select('tpi_id, tpi_type, tpi_pcid, tpi_assetno, tpi_receivedate, tpi_status, tpi_lastuser, tpi_lastupdate')
                                       ->where('tpi_pcid', $pcId)
                                       ->where('tpi_status <>', 25)
                                       ->orderBy('tpi_type', 'ASC')
                                       ->orderBy('tpi_id', 'ASC')
                                       ->get()
                                       ->getResult();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getPCEquipment: ' . $e->getMessage());
            return [];
        }
    }

    public function updatePCSpecs($data, $isUpdate = true)
    {
        $lastUser = $this->getCurrentUserId();
    
        if ($lastUser === null) {
            return ['status' => false, 'message' => 'User session is not set. Please log in again. (Code: TPCM-S1)'];
        }

        $specsData = [
            'tps_pcid' => $data['pc_id'],
            'tps_processor' => $data['processor'] ?? null,
            'tps_ram' => $data['ram'] ?? null,
            'tps_storage' => $data['storage'] ?? null,
            'tps_vga' => $data['vga'] ?? null,
            'tps_ethernet' => $data['ethernet'] ?? null,
            'tps_lastuser' => $lastUser, // Use the fetched ua_userid
            'tps_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            if ($isUpdate) {
                $this->db_sysinfra->table('t_pcspec')
                    ->where('tps_pcid', $data['pc_id'])
                    ->update($specsData);
            } else {
                $this->db_sysinfra->table('t_pcspec')->insert($specsData);
            }
            return ['status' => true, 'message' => 'PC specifications saved successfully.'];
        } catch (\Exception $e) {
            log_message('error', 'Error saving PC specs: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error occurred while saving PC specifications: ' . $e->getMessage()];
        }
    }

    public function storePCEquipment($data)
    {
        $lastUser = $this->getCurrentUserId();
    
        if ($lastUser === null) {
            return ['status' => false, 'message' => 'User session is not set. Please log in again. (Code: TPCM-S1)'];
        }

        $assetNo = null;
        if (isset($data['asset_no']) && $data['asset_no'] !== '') {
            $assetNo = is_numeric($data['asset_no']) ? (int)$data['asset_no'] : $data['asset_no'];

            if (!$this->isAssetAvailableForEquipment($assetNo)) {
                return ['status' => false, 'message' => 'Asset No is already in use by another PC or equipment. Please select a different asset.'];
            }
            
            $assetDetails = $this->getAssetNo($assetNo);
            if (!$assetDetails) {
                return ['status' => false, 'message' => 'Selected asset not found in system.'];
            }
        }

        $finalReceiveDate = null;
        if (!empty($data['receive_date'])) {
            $finalReceiveDate = $data['receive_date'];
        } elseif (isset($assetDetails) && !empty($assetDetails->e_receivedate)) {
            $finalReceiveDate = date('Y-m-d', strtotime($assetDetails->e_receivedate));
        }

        $insertData = [
            'tpi_type' => $data['equipment_type'],
            'tpi_pcid' => $data['pc_id'],
            'tpi_assetno' => is_numeric($assetNo) ? (int)$assetNo : null, 
            'tpi_receivedate' => $finalReceiveDate,
            'tpi_status' => 1,
            'tpi_lastuser' => $lastUser, // Use the fetched ua_userid
            'tpi_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_pcitequipment')->insert($insertData);
            return ['status' => true, 'message' => 'IT Equipment added successfully.'];
        } catch (\Exception $e) {
            log_message('error', 'Error creating PC equipment: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error occurred while saving IT equipment: ' . $e->getMessage()];
        }
    }

    public function updatePCEquipment($data)
    {
        $lastUser = $this->getCurrentUserId();
    
        if ($lastUser === null) {
            return ['status' => false, 'message' => 'User session is not set. Please log in again. (Code: TPCM-S1)'];
        }

        $oldEquipment = $this->getPCEquipmentById($data['equipment_id']);
        if (!$oldEquipment) {
            return ['status' => false, 'message' => 'Original equipment data not found for update.'];
        }
        $oldAssetNo = $oldEquipment->tpi_assetno ?? '';
        $newAssetNoInput = $data['asset_no'] ?? '';
        $newAssetNo = is_numeric($newAssetNoInput) ? (int)$newAssetNoInput : $newAssetNoInput;

        if (!empty($newAssetNoInput) && $newAssetNo !== $oldAssetNo) {
            if (!$this->isAssetAvailableForEquipment($newAssetNo, $data['equipment_id'])) {
                return ['status' => false, 'message' => 'Asset No is already in use by another PC or equipment. Please select a different asset.'];
            }
            $assetDetails = $this->getAssetNo($newAssetNo);
            if (!$assetDetails) {
                return ['status' => false, 'message' => 'Selected asset not found in system.'];
            }
        } else {
            $assetDetails = $this->getAssetNo($oldAssetNo); 
        }

        $finalReceiveDate = null;
        if (!empty($data['receive_date'])) {
            $finalReceiveDate = $data['receive_date']; // Pastikan ini mengambil dari 'receive_date'
        } elseif (isset($assetDetails) && !empty($assetDetails->e_receivedate)) {
            $finalReceiveDate = date('Y-m-d', strtotime($assetDetails->e_receivedate));
        }

        $updateData = [
            'tpi_type' => $data['equipment_type'],
            'tpi_assetno' => is_numeric($newAssetNo) ? (int)$newAssetNo : null, 
            'tpi_receivedate' => $finalReceiveDate,
            'tpi_lastuser' => $lastUser, // Use the fetched ua_userid
            'tpi_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_pcitequipment')
                ->where('tpi_id', $data['equipment_id'])
                ->update($updateData);
            return ['status' => true, 'message' => 'IT Equipment updated successfully.'];
        } catch (\Exception $e) {
            log_message('error', 'Error updating PC equipment: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error occurred while updating IT equipment: ' . $e->getMessage()];
        }
    }

    public function deletePCEquipment($id)
    {
        $lastUser = $this->getCurrentUserId();
    
        if ($lastUser === null) {
            return ['status' => false, 'message' => 'User session is not set. Please log in again. (Code: TPCM-S1)'];
        }
        try {
            $this->db_sysinfra->table('t_pcitequipment')
                ->where('tpi_id', $id)
                ->update([
                    'tpi_status' => 25,
                    'tpi_lastuser' => $lastUser, // Use the fetched ua_userid
                    'tpi_lastupdate' => date('Y-m-d H:i:s')
                ]);
            return ['status' => true, 'message' => 'IT Equipment deleted successfully.'];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting PC equipment: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error occurred while deleting IT equipment: ' . $e->getMessage()];
        }
    }

    public function getPCEquipmentById($id)
    {
        if (empty($id)) {
            return null;
        }
        
        try {
            return $this->db_sysinfra->table('t_pcitequipment')
                                       ->select('tpi_id, tpi_type, tpi_pcid, tpi_assetno, tpi_receivedate, tpi_status, tpi_lastuser, tpi_lastupdate')
                                       ->where('tpi_id', $id)
                                       ->where('tpi_status <>', 25)
                                       ->get()
                                       ->getRow();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getPCEquipmentById: ' . $e->getMessage());
            return null;
        }
    }

    public function getPCServerVM($pcId)
    {
        if (empty($pcId)) {
            return [];
        }
        
        try {
            return $this->db_sysinfra->table('t_pcservervm')
                                       ->select('tpv_id, tpv_pcid, tpv_name, tpv_processor, tpv_ram, tpv_storage, tpv_vga, tpv_ethernet, tpv_ipaddress, tpv_services, tpv_remark, tpv_status, tpv_lastuser, tpv_lastupdate')
                                       ->where('tpv_pcid', $pcId)
                                       ->where('tpv_status <>', 25)
                                       ->orderBy('tpv_id', 'ASC')
                                       ->get()
                                       ->getResult();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getPCServerVM: ' . $e->getMessage());
            return [];
        }
    }

    public function storePCServerVM($data)
    {
        $lastUser = $this->getCurrentUserId();
    
        if ($lastUser === null) {
            return ['status' => false, 'message' => 'User session is not set. Please log in again. (Code: TPCM-S1)'];
        }

        $insertData = [
            'tpv_pcid' => $data['pc_id'],
            'tpv_name' => $data['vm_name'] ?? null,
            'tpv_processor' => $data['vm_processor'] ?? null,
            'tpv_ram' => $data['vm_ram'] ?? null,
            'tpv_storage' => $data['vm_storage'] ?? null,
            'tpv_vga' => $data['vm_vga'] ?? null,
            'tpv_ethernet' => $data['vm_ethernet'] ?? null,
            'tpv_ipaddress' => $data['vm_ip_address'] ?? null,
            'tpv_services' => $data['vm_services'] ?? null,
            'tpv_remark' => $data['vm_remark'] ?? null,
            'tpv_status' => 1,
            'tpv_lastuser' => $lastUser, // Use the fetched ua_userid
            'tpv_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_pcservervm')->insert($insertData);
            return ['status' => true, 'message' => 'Server VM added successfully.'];
        } catch (\Exception $e) {
            log_message('error', 'Error creating PC Server VM: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error occurred while saving Server VM: ' . $e->getMessage()];
        }
    }

    public function updatePCServerVM($data)
    {
        $lastUser = $this->getCurrentUserId();
    
        if ($lastUser === null) {
            return ['status' => false, 'message' => 'User session is not set. Please log in again. (Code: TPCM-S1)'];
        }
        
        $updateData = [
            'tpv_name' => $data['vm_name'] ?? null,
            'tpv_processor' => $data['vm_processor'] ?? null,
            'tpv_ram' => $data['vm_ram'] ?? null,
            'tpv_storage' => $data['vm_storage'] ?? null,
            'tpv_vga' => $data['vm_vga'] ?? null,
            'tpv_ethernet' => $data['vm_ethernet'] ?? null,
            'tpv_ipaddress' => $data['vm_ip_address'] ?? null,
            'tpv_services' => $data['vm_services'] ?? null,
            'tpv_remark' => $data['vm_remark'] ?? null,
            'tpv_lastuser' => $lastUser, // Use the fetched ua_userid
            'tpv_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_pcservervm')
                ->where('tpv_id', $data['vm_id'])
                ->update($updateData);
            return ['status' => true, 'message' => 'Server VM updated successfully.'];
        } catch (\Exception $e) {
            log_message('error', 'Error updating PC Server VM: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error occurred while updating Server VM: ' . $e->getMessage()];
        }
    }

    public function deletePCServerVM($id)
    {
        $lastUser = $this->getCurrentUserId();
    
        if ($lastUser === null) {
            return ['status' => false, 'message' => 'User session is not set. Please log in again. (Code: TPCM-S1)'];
        }

        try {
            $this->db_sysinfra->table('t_pcservervm')
                ->where('tpv_id', $id)
                ->update([
                    'tpv_status' => 25,
                    'tpv_lastuser' => $lastUser, // Use the fetched ua_userid
                    'tpv_lastupdate' => date('Y-m-d H:i:s')
                ]);
            return ['status' => true, 'message' => 'Server VM deleted successfully.'];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting PC Server VM: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error occurred while deleting Server VM: ' . $e->getMessage()];
        }
    }

    public function getPCServerVMById($id)
    {
        if (empty($id)) {
            return null;
        }
        
        try {
            return $this->db_sysinfra->table('t_pcservervm')
                                       ->select('tpv_id, tpv_pcid, tpv_name, tpv_processor, tpv_ram, tpv_storage, tpv_vga, tpv_ethernet, tpv_ipaddress, tpv_services, tpv_remark, tpv_status, tpv_lastuser, tpv_lastupdate')
                                       ->where('tpv_id', $id)
                                       ->where('tpv_status <>', 25)
                                       ->get()
                                       ->getRow();
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCModel::getPCServerVMById: ' . $e->getMessage());
            return null;
        }
    }

    public function getPCFullDetails($pcId)
    {
        $pc = $this->getPCById($pcId);
        if (!$pc) {
            return null;
        }
        
        $specs = $this->getPCSpecs($pcId);
        $equipment = $this->getPCEquipment($pcId);
        $serverVM = $this->getPCServerVM($pcId);
        
        return [
            'pc' => $pc,
            'specs' => $specs,
            'equipment' => $equipment,
            'servervm' => $serverVM
        ];
    }
}