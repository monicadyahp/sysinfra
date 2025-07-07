<?php

namespace App\Models\transpc;

use CodeIgniter\Model;

class TransPCModel extends Model
{
    protected $db_postgree;
    protected $db_sysinfra;

    public function __construct()
    {
        $this->db_postgree = db_connect('db_postgree');
        $this->db_sysinfra = db_connect('db_sysinfra');
    }

    public function getData($statusFilter = null, $typeFilter = null)
    {
        $query = "
            SELECT
                tpc_id, 
                tpc_type,
                tpc_name,
                tpc_assetno,
                tpc_pcreceivedate,
                tpc_osname,
                tpc_ipaddress,
                tpc_user,
                tpc_location,
                tpc_status,
                tpc_lastuser,
                tpc_lastupdate
            FROM t_pc
            WHERE tpc_status <> 25
        ";

        $params = [];
        
        if ($statusFilter !== null && $statusFilter !== 'All') {
            $query .= " AND tpc_status = ?";
            $params[] = (int)$statusFilter;
        }
        
        if ($typeFilter !== null && $typeFilter !== 'All') {
            $query .= " AND tpc_type = ?";
            $params[] = (int)$typeFilter;
        }

        $query .= " ORDER BY tpc_id DESC";

        return $this->db_sysinfra->query($query, $params)->getResult();
    }

    public function getPCById($id)
    {
        return $this->db_sysinfra->query("
            SELECT *
            FROM t_pc
            WHERE tpc_status <> 25 AND tpc_id = ?
        ", [$id])->getRow();
    }

    public function storeData($data)
    {
        // Validate asset availability
        if (!empty($data['pc_assetno'])) {
            if (!$this->isAssetAvailable($data['pc_assetno'])) {
                return [
                    'status' => false,
                    'message' => 'Asset No is already in use by another PC. Please select a different asset.'
                ];
            }
        }

        // Get asset details if asset_no is provided
        $pcAssetDetails = null;
        
        if (!empty($data['pc_assetno'])) {
            $pcAssetDetails = $this->getAssetNo($data['pc_assetno']);
            
            // Double check if asset exists
            if (!$pcAssetDetails) {
                return [
                    'status' => false,
                    'message' => 'Selected asset not found in system.'
                ];
            }
        }

        // Use provided receive_date if available, otherwise use from asset details
        $finalPCReceiveDate = null;
        if (!empty($data['pc_receive_date'])) {
            $finalPCReceiveDate = $data['pc_receive_date'];
        } elseif ($pcAssetDetails && $pcAssetDetails->e_receivedate) {
            $finalPCReceiveDate = date('Y-m-d', strtotime($pcAssetDetails->e_receivedate));
        }

        // Convert asset no to integer if provided
        $assetNo = null;
        if (!empty($data['pc_assetno']) && is_numeric($data['pc_assetno'])) {
            $assetNo = (int)$data['pc_assetno'];
        }

        $insertData = [
            'tpc_type'              => isset($data['pc_type']) && $data['pc_type'] !== '' ? $data['pc_type'] : 1,
            'tpc_name'              => isset($data['pc_name']) && $data['pc_name'] !== '' ? $data['pc_name'] : null,
            'tpc_assetno'           => $assetNo,
            'tpc_pcreceivedate'     => $finalPCReceiveDate,
            'tpc_osname'            => isset($data['os_name']) && $data['os_name'] !== '' ? $data['os_name'] : null,
            'tpc_ipaddress'         => isset($data['ip_address']) && $data['ip_address'] !== '' ? $data['ip_address'] : null,
            'tpc_user'              => isset($data['user']) && $data['user'] !== '' ? $data['user'] : null,
            'tpc_location'          => isset($data['location']) && $data['location'] !== '' ? $data['location'] : null,
            'tpc_status'            => isset($data['pcstatus']) && $data['pcstatus'] !== '' ? $data['pcstatus'] : 1,
            'tpc_lastuser'          => session()->get('user_info')['em_emplcode'],
            'tpc_lastupdate'        => date('Y-m-d H:i:s')
        ];
                            
        try {
            $this->db_sysinfra->table('t_pc')->insert($insertData);

            // Update IP status to used (1) for new IP address
            if (!empty($insertData['tpc_ipaddress'])) {
                $ipDetails = $this->getIPAddressByIP($insertData['tpc_ipaddress']);
                
                // Jika IP tidak available
                if (!$ipDetails) {
                    return [
                        'status' => false,
                        'message' => 'IP Address is not available. Please select another IP'
                    ];
                }
                
                // Update status IP menjadi used
                $this->updateIPStatus($insertData['tpc_ipaddress'], 1);
            }

            return [
                'status' => true,
                'message' => 'PC created successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error creating PC: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while saving PC data: ' . $e->getMessage()
            ];
        }
    }

    public function updateData($data)
    {
        // Get old PC data to compare IP address
        $oldPC = $this->getPCById($data['tpc_id']);
        $oldIPAddress = $oldPC ? $oldPC->tpc_ipaddress : '';
        $oldAssetNo = $oldPC ? $oldPC->tpc_assetno : '';
        $newIPAddress = isset($data['ip_address']) ? $data['ip_address'] : '';

        // Validate asset availability (exclude current PC)
        if (!empty($data['pc_assetno']) && $data['pc_assetno'] !== $oldAssetNo) {
            if (!$this->isAssetAvailable($data['pc_assetno'], $data['tpc_id'])) {
                return [
                    'status' => false,
                    'message' => 'Asset No is already in use by another PC. Please select a different asset.'
                ];
            }
        }

        // Get asset details if asset_no is provided
        $pcAssetDetails = null;
        
        if (!empty($data['pc_assetno'])) {
            $pcAssetDetails = $this->getAssetNo($data['pc_assetno']);
            
            // Double check if asset exists
            if (!$pcAssetDetails) {
                return [
                    'status' => false,
                    'message' => 'Selected asset not found in system.'
                ];
            }
        }

        // Use provided receive_date if available, otherwise use from asset details
        $finalPCReceiveDate = null;
        if (!empty($data['pc_receive_date'])) {
            $finalPCReceiveDate = $data['pc_receive_date'];
        } elseif ($pcAssetDetails && $pcAssetDetails->e_receivedate) {
            $finalPCReceiveDate = date('Y-m-d', strtotime($pcAssetDetails->e_receivedate));
        }

        $updateData = [
            'tpc_type'              => isset($data['pc_type']) && $data['pc_type'] !== '' ? $data['pc_type'] : ($oldPC ? $oldPC->tpc_type : 1),
            'tpc_name'              => isset($data['pc_name']) && $data['pc_name'] !== '' ? $data['pc_name'] : null,
            'tpc_assetno'           => isset($data['pc_assetno']) && $data['pc_assetno'] !== '' ? $data['pc_assetno'] : null,
            'tpc_pcreceivedate'     => $finalPCReceiveDate,
            'tpc_osname'            => isset($data['os_name']) && $data['os_name'] !== '' ? $data['os_name'] : null,
            'tpc_ipaddress'         => isset($data['ip_address']) && $data['ip_address'] !== '' ? $data['ip_address'] : null,
            'tpc_user'              => isset($data['user']) && $data['user'] !== '' ? $data['user'] : null,
            'tpc_location'          => isset($data['location']) && $data['location'] !== '' ? $data['location'] : null,
            'tpc_status'            => isset($data['pcstatus']) && $data['pcstatus'] !== '' ? $data['pcstatus'] : ($oldPC ? $oldPC->tpc_status : 1),
            'tpc_lastuser'          => session()->get('user_info')['em_emplcode'],
            'tpc_lastupdate'        => date('Y-m-d H:i:s')
        ];
                            
        try {
            $this->db_sysinfra->table('t_pc')
                ->where('tpc_id', $data['tpc_id'])
                ->update($updateData);

            // Handle IP status updates
            $this->handleIPStatusUpdate($oldIPAddress, $updateData['tpc_ipaddress']);

            return [
                'status'  => true,
                'message' => 'PC updated successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating PC: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Error occurred while updating PC data: ' . $e->getMessage()
            ];
        }
    }

    public function deleteData($id)
    {
        // Get PC data to release IP address
        $pc = $this->getPCById($id);

        try {
            // Start transaction to ensure all operations succeed or fail together
            $this->db_sysinfra->transStart();

            // Mark PC as deleted (status = 25)
            $this->db_sysinfra->table('t_pc')
                ->where('tpc_id', $id)
                ->update([
                    'tpc_status'     => 25,
                    'tpc_lastuser'   => session()->get('user_info')['em_emplcode'],
                    'tpc_lastupdate' => date('Y-m-d H:i:s')
                ]);

            // Mark all related PC equipment as deleted (status = 25)
            $this->db_sysinfra->table('t_pcitequipment')
                ->where('tpi_pcid', $id)
                ->where('tpi_status <>', 25) // Only update non-deleted equipment
                ->update([
                    'tpi_status'     => 25,
                    'tpi_lastuser'   => session()->get('user_info')['em_emplcode'],
                    'tpi_lastupdate' => date('Y-m-d H:i:s')
                ]);

            // Mark all related PC server VMs as deleted (status = 25)
            $this->db_sysinfra->table('t_pcservervm')
                ->where('tpv_pcid', $id)
                ->where('tpv_status <>', 25) // Only update non-deleted VMs
                ->update([
                    'tpv_status'     => 25,
                    'tpv_lastuser'   => session()->get('user_info')['em_emplcode'],
                    'tpv_lastupdate' => date('Y-m-d H:i:s')
                ]);

            // Complete transaction
            $this->db_sysinfra->transComplete();

            // Check if transaction was successful
            if ($this->db_sysinfra->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            // Release IP address when PC is deleted (after successful transaction)
            if ($pc && !empty($pc->tpc_ipaddress)) {
                $this->updateIPStatus($pc->tpc_ipaddress, 0);
            }

            return [
                'status' => true,
                'message' => 'PC and all related data have been marked as deleted successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error marking PC as deleted: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Failed to delete PC: ' . $e->getMessage()
            ];
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
                    CASE 
                        WHEN e.e_assetno IS NOT NULL THEN CAST(e.e_assetno AS VARCHAR)
                        ELSE CAST(e.e_equipmentid AS VARCHAR)
                    END as display_asset_no
                FROM m_itequipment e
                WHERE e.e_status = 'Active'
                AND (
                    -- Case 1: Equipment punya e_assetno dan belum digunakan di kedua tabel
                    (
                        e.e_assetno IS NOT NULL 
                        AND NOT EXISTS (
                            SELECT 1 FROM t_pc pc 
                            WHERE pc.tpc_assetno = e.e_assetno
                            AND pc.tpc_status <> 25
                        )
                        AND NOT EXISTS (
                            SELECT 1 FROM t_pcitequipment pi 
                            WHERE pi.tpi_assetno = e.e_assetno
                            AND pi.tpi_status <> 25
                        )
                    )
                    OR
                    -- Case 2: Equipment tidak punya e_assetno, gunakan e_equipmentid
                    (
                        e.e_assetno IS NULL 
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
                    CASE WHEN e.e_assetno IS NOT NULL THEN 1 ELSE 2 END,
                    e.e_lastupdate DESC
            ";

        return $this->db_sysinfra->query($query)->getResult();
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

    public function isAssetAvailable($assetNo, $excludePCId = null)
    {
        if (empty($assetNo)) {
            return true; // Empty asset no is allowed
        }
        
        $query = "
            SELECT COUNT(*) as count 
            FROM t_pc 
            WHERE tpc_assetno = ? 
            AND tpc_status <> 25
        ";
        
        $params = [$assetNo];
        
        // Exclude current PC when updating
        if ($excludePCId !== null) {
            $query .= " AND tpc_id <> ?";
            $params[] = $excludePCId;
        }
        
        $result = $this->db_sysinfra->query($query, $params)->getRow();
        
        return $result->count == 0;
    }

    public function isAssetAvailableForEquipment($assetNo, $excludeEquipmentId = null)
    {
        if (empty($assetNo)) {
            return true; // Empty asset no is allowed
        }
        
        // Convert string input to integer if it's numeric
        $searchValue = is_numeric($assetNo) ? (int)$assetNo : null;
        
        if ($searchValue === null) {
            return false; // Non-numeric asset no is not valid
        }
        
        // Check if asset is already used in t_pc
        $pcUsage = $this->db_sysinfra->query("
            SELECT COUNT(*) as count 
            FROM t_pc 
            WHERE tpc_assetno = ? 
            AND tpc_status <> 25
        ", [$searchValue])->getRow();
        
        if ($pcUsage->count > 0) {
            return false;
        }
        
        // Check if asset is already used in t_pcitequipment
        // Assuming tpi_assetno is also integer or can be converted
        $query = "
            SELECT COUNT(*) as count 
            FROM t_pcitequipment 
            WHERE tpi_assetno = ? 
            AND tpi_status <> 25
        ";
        
        $params = [$assetNo]; // Keep as string for equipment table
        
        // Exclude current equipment when updating
        if ($excludeEquipmentId !== null) {
            $query .= " AND tpi_id <> ?";
            $params[] = $excludeEquipmentId;
        }
        
        $equipmentUsage = $this->db_sysinfra->query($query, $params)->getRow();
        
        return $equipmentUsage->count == 0;
    }

    public function getIPAddressByIP($ipAddress)
    {
        if (empty($ipAddress)) {
            return null;
        }
                            
        $query = "
            SELECT mip_id, mip_vlanid, mip_vlanname, mip_ipaddress, mip_status
            FROM m_ipaddress
            WHERE mip_ipaddress = ?
            AND mip_status = 0
        ";
                            
        return $this->db_sysinfra->query($query, [$ipAddress])->getRow();
    }

    public function searchIPAddresses()
    {
        $query = "
            SELECT mip_id, mip_vlanid, mip_vlanname, mip_ipaddress, mip_status
            FROM m_ipaddress
            WHERE mip_status = 0
        ";
        
        return $this->db_sysinfra->query($query)->getResult();
    }

    public function updateIPStatus($ipAddress, $status)
    {
        if (empty($ipAddress)) {
            return false;
        }

        try {
            $this->db_sysinfra->table('m_ipaddress')
                ->where('mip_ipaddress', $ipAddress)
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
        // Release old IP address (set status back to 0 - unused)
        if (!empty($oldIPAddress) && $oldIPAddress !== $newIPAddress) {
            $this->updateIPStatus($oldIPAddress, 0);
        }

        // Mark new IP address as used (1)
        if (!empty($newIPAddress)) {
            $this->updateIPStatus($newIPAddress, 1);
        }
    }

    public function getEmployeeById($employeeId)
    {
        $query = "
            SELECT 
                emp.em_emplcode,
                emp.em_emplname,
                sec.sec_section,
                sec.sec_sectioncode as em_sectioncode,
                pos.pm_positionname
            FROM 
                tbmst_employee emp
            LEFT JOIN 
                tbmst_section sec ON emp.em_sectioncode = sec.sec_sectioncode
            LEFT JOIN
                tbmst_position pos ON emp.em_positioncode = pos.pm_code
            WHERE 
                emp.em_emplcode = ? and emp.em_emplstatus < 200
        ";
                         
        return $this->db_postgree->query($query, [$employeeId])->getRow();
    }

    public function searchEmployees()
    {
        $query = "
            SELECT 
                emp.em_emplcode,
                emp.em_emplname,
                sec.sec_section,
                sec.sec_sectioncode AS em_sectioncode,
                pos.pm_positionname
            FROM 
                tbmst_employee emp
            LEFT JOIN 
                tbmst_section sec ON emp.em_sectioncode = sec.sec_sectioncode
            LEFT JOIN
                tbmst_position pos ON emp.em_positioncode = pos.pm_code
            WHERE 
                emp.em_emplstatus < 200
        ";

        return $this->db_postgree->query($query)->getResult();
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

    public function getOSList()
    {
        return $this->db_sysinfra->query("
            SELECT mpo_id, mpo_osname
            FROM m_pcos 
            WHERE mpo_status <> 25
            ORDER BY mpo_osname ASC
        ")->getResult();
    }

    public function getPCSpecs($pcId)
    {
        if (empty($pcId)) {
            return null;
        }
        
        $query = "
            SELECT 
                tps_id,
                tps_pcid,
                tps_processor,
                tps_ram,
                tps_storage,
                tps_vga,
                tps_ethernet,
                tps_lastuser,
                tps_lastupdate
            FROM t_pcspec
            WHERE tps_pcid = ?
        ";
        
        return $this->db_sysinfra->query($query, [$pcId])->getRow();
    }

    public function getPCEquipment($pcId)
    {
        if (empty($pcId)) {
            return [];
        }
        
        $query = "
            SELECT 
                tpi_id,
                tpi_type,
                tpi_pcid,
                tpi_assetno,
                tpi_receivedate,
                tpi_status,
                tpi_lastuser,
                tpi_lastupdate
            FROM t_pcitequipment
            WHERE tpi_pcid = ?
            AND tpi_status <> 25
            ORDER BY tpi_type, tpi_id
        ";
        
        return $this->db_sysinfra->query($query, [$pcId])->getResult();
    }

    public function updatePCSpecs($data, $isUpdate = true)
    {
        $specsData = [
            'tps_pcid' => $data['pc_id'],
            'tps_processor' => isset($data['processor']) && $data['processor'] !== '' ? $data['processor'] : null,
            'tps_ram' => isset($data['ram']) && $data['ram'] !== '' ? $data['ram'] : null,
            'tps_storage' => isset($data['storage']) && $data['storage'] !== '' ? $data['storage'] : null,
            'tps_vga' => isset($data['vga']) && $data['vga'] !== '' ? $data['vga'] : null,
            'tps_ethernet' => isset($data['ethernet']) && $data['ethernet'] !== '' ? $data['ethernet'] : null,
            'tps_lastuser' => session()->get('user_info')['em_emplcode'],
            'tps_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            if ($isUpdate) {
                // UPDATE existing specs
                $this->db_sysinfra->table('t_pcspec')
                    ->where('tps_pcid', $data['pc_id'])
                    ->update($specsData);
            } else {
                // INSERT new specs
                $this->db_sysinfra->table('t_pcspec')->insert($specsData);
            }

            return [
                'status' => true,
                'message' => 'PC specifications saved successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error saving PC specs: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while saving PC specifications: ' . $e->getMessage()
            ];
        }
    }

    // PC IT Equipment CRUD Methods
    public function storePCEquipment($data)
    {
        // Validate asset availability for equipment
        if (!empty($data['asset_no'])) {
            if (!$this->isAssetAvailableForEquipment($data['asset_no'])) {
                return [
                    'status' => false,
                    'message' => 'Asset No is already in use by another PC or equipment. Please select a different asset.'
                ];
            }
            
            // Verify asset exists in system
            $assetDetails = $this->getAssetNo($data['asset_no']);
            if (!$assetDetails) {
                return [
                    'status' => false,
                    'message' => 'Selected asset not found in system.'
                ];
            }
        }

        // Get asset details if asset_no is provided
        $assetDetails = null;
        if (!empty($data['asset_no'])) {
            $assetDetails = $this->getAssetNo($data['asset_no']);
        }

        // Use provided receive_date if available, otherwise use from asset details
        $finalReceiveDate = null;
        if (!empty($data['receive_date'])) {
            $finalReceiveDate = $data['receive_date'];
        } elseif ($assetDetails && $assetDetails->e_receivedate) {
            $finalReceiveDate = date('Y-m-d', strtotime($assetDetails->e_receivedate));
        }

        $insertData = [
            'tpi_type' => $data['equipment_type'],
            'tpi_pcid' => $data['pc_id'],
            'tpi_assetno' => isset($data['asset_no']) && $data['asset_no'] !== '' ? $data['asset_no'] : null,
            'tpi_receivedate' => $finalReceiveDate,
            'tpi_status' => 1, // Active
            'tpi_lastuser' => session()->get('user_info')['em_emplcode'],
            'tpi_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_pcitequipment')->insert($insertData);
            return [
                'status' => true,
                'message' => 'IT Equipment added successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error creating PC equipment: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while saving IT equipment: ' . $e->getMessage()
            ];
        }
    }

    public function updatePCEquipment($data)
{
        // Get old equipment data
        $oldEquipment = $this->getPCEquipmentById($data['equipment_id']);
        $oldAssetNo = $oldEquipment ? $oldEquipment->tpi_assetno : '';

        // Validate asset availability (exclude current equipment)
        if (!empty($data['asset_no']) && $data['asset_no'] !== $oldAssetNo) {
            if (!$this->isAssetAvailableForEquipment($data['asset_no'], $data['equipment_id'])) {
                return [
                    'status' => false,
                    'message' => 'Asset No is already in use by another PC or equipment. Please select a different asset.'
                ];
            }
            
            // Verify asset exists in system
            $assetDetails = $this->getAssetNo($data['asset_no']);
            if (!$assetDetails) {
                return [
                    'status' => false,
                    'message' => 'Selected asset not found in system.'
                ];
            }
        }

        // Get asset details if asset_no is provided
        $assetDetails = null;
        if (!empty($data['asset_no'])) {
            $assetDetails = $this->getAssetNo($data['asset_no']);
        }

        // Use provided receive_date if available, otherwise use from asset details
        $finalReceiveDate = null;
        if (!empty($data['receive_date'])) {
            $finalReceiveDate = $data['receive_date'];
        } elseif ($assetDetails && $assetDetails->e_receivedate) {
            $finalReceiveDate = date('Y-m-d', strtotime($assetDetails->e_receivedate));
        }

        $updateData = [
            'tpi_type' => $data['equipment_type'],
            'tpi_assetno' => isset($data['asset_no']) && $data['asset_no'] !== '' ? $data['asset_no'] : null,
            'tpi_receivedate' => $finalReceiveDate,
            'tpi_lastuser' => session()->get('user_info')['em_emplcode'],
            'tpi_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_pcitequipment')
                ->where('tpi_id', $data['equipment_id'])
                ->update($updateData);

            return [
                'status' => true,
                'message' => 'IT Equipment updated successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating PC equipment: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while updating IT equipment: ' . $e->getMessage()
            ];
        }
    }

    public function deletePCEquipment($id)
    {
        try {
            $this->db_sysinfra->table('t_pcitequipment')
                ->where('tpi_id', $id)
                ->update([
                    'tpi_status' => 25, // Deleted
                    'tpi_lastuser' => session()->get('user_info')['em_emplcode'],
                    'tpi_lastupdate' => date('Y-m-d H:i:s')
                ]);

            return [
                'status' => true,
                'message' => 'IT Equipment deleted successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting PC equipment: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while deleting IT equipment: ' . $e->getMessage()
            ];
        }
    }

    public function getPCEquipmentById($id)
    {
        if (empty($id)) {
            return null;
        }
        
        $query = "
            SELECT 
                tpi_id,
                tpi_type,
                tpi_pcid,
                tpi_assetno,
                tpi_receivedate,
                tpi_status,
                tpi_lastuser,
                tpi_lastupdate
            FROM t_pcitequipment
            WHERE tpi_id = ? AND tpi_status <> 25
        ";
        
        return $this->db_sysinfra->query($query, [$id])->getRow();
    }

    // PC Server VM CRUD Methods
    public function getPCServerVM($pcId)
    {
        if (empty($pcId)) {
            return [];
        }
        
        $query = "
            SELECT 
                tpv_id,
                tpv_pcid,
                tpv_type,
                tpv_name,
                tpv_processor,
                tpv_ram,
                tpv_storage,
                tpv_vga,
                tpv_ethernet,
                tpv_ipaddress,
                tpv_services,
                tpv_remark,
                tpv_status,
                tpv_lastuser,
                tpv_lastupdate
            FROM t_pcservervm
            WHERE tpv_pcid = ?
            AND tpv_status <> 25
            ORDER BY tpv_id
        ";
        
        return $this->db_sysinfra->query($query, [$pcId])->getResult();
    }

    public function storePCServerVM($data)
    {
        $insertData = [
            'tpv_pcid' => $data['pc_id'],
            'tpv_name' => isset($data['vm_name']) && $data['vm_name'] !== '' ? $data['vm_name'] : null,
            'tpv_processor' => isset($data['vm_processor']) && $data['vm_processor'] !== '' ? $data['vm_processor'] : null,
            'tpv_ram' => isset($data['vm_ram']) && $data['vm_ram'] !== '' ? $data['vm_ram'] : null,
            'tpv_storage' => isset($data['vm_storage']) && $data['vm_storage'] !== '' ? $data['vm_storage'] : null,
            'tpv_vga' => isset($data['vm_vga']) && $data['vm_vga'] !== '' ? $data['vm_vga'] : null,
            'tpv_ethernet' => isset($data['vm_ethernet']) && $data['vm_ethernet'] !== '' ? $data['vm_ethernet'] : null,
            'tpv_ipaddress' => isset($data['vm_ip_address']) && $data['vm_ip_address'] !== '' ? $data['vm_ip_address'] : null,
            'tpv_services' => isset($data['vm_services']) && $data['vm_services'] !== '' ? $data['vm_services'] : null,
            'tpv_remark' => isset($data['vm_remark']) && $data['vm_remark'] !== '' ? $data['vm_remark'] : null,
            'tpv_status' => 1, // Active
            'tpv_lastuser' => session()->get('user_info')['em_emplcode'],
            'tpv_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_pcservervm')->insert($insertData);
            return [
                'status' => true,
                'message' => 'Server VM added successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error creating PC Server VM: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while saving Server VM: ' . $e->getMessage()
            ];
        }
    }

    public function updatePCServerVM($data)
    {
        $updateData = [
            'tpv_name' => isset($data['vm_name']) && $data['vm_name'] !== '' ? $data['vm_name'] : null,
            'tpv_processor' => isset($data['vm_processor']) && $data['vm_processor'] !== '' ? $data['vm_processor'] : null,
            'tpv_ram' => isset($data['vm_ram']) && $data['vm_ram'] !== '' ? $data['vm_ram'] : null,
            'tpv_storage' => isset($data['vm_storage']) && $data['vm_storage'] !== '' ? $data['vm_storage'] : null,
            'tpv_vga' => isset($data['vm_vga']) && $data['vm_vga'] !== '' ? $data['vm_vga'] : null,
            'tpv_ethernet' => isset($data['vm_ethernet']) && $data['vm_ethernet'] !== '' ? $data['vm_ethernet'] : null,
            'tpv_ipaddress' => isset($data['vm_ip_address']) && $data['vm_ip_address'] !== '' ? $data['vm_ip_address'] : null,
            'tpv_services' => isset($data['vm_services']) && $data['vm_services'] !== '' ? $data['vm_services'] : null,
            'tpv_remark' => isset($data['vm_remark']) && $data['vm_remark'] !== '' ? $data['vm_remark'] : null,
            'tpv_lastuser' => session()->get('user_info')['em_emplcode'],
            'tpv_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_pcservervm')
                ->where('tpv_id', $data['vm_id'])
                ->update($updateData);

            return [
                'status' => true,
                'message' => 'Server VM updated successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating PC Server VM: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while updating Server VM: ' . $e->getMessage()
            ];
        }
    }

    public function deletePCServerVM($id)
    {
        try {
            $this->db_sysinfra->table('t_pcservervm')
                ->where('tpv_id', $id)
                ->update([
                    'tpv_status' => 25, // Deleted
                    'tpv_lastuser' => session()->get('user_info')['em_emplcode'],
                    'tpv_lastupdate' => date('Y-m-d H:i:s')
                ]);

            return [
                'status' => true,
                'message' => 'Server VM deleted successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting PC Server VM: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while deleting Server VM: ' . $e->getMessage()
            ];
        }
    }

    public function getPCServerVMById($id)
    {
        if (empty($id)) {
            return null;
        }
        
        $query = "
            SELECT 
                tpv_id,
                tpv_pcid,
                tpv_name,
                tpv_processor,
                tpv_ram,
                tpv_storage,
                tpv_vga,
                tpv_ethernet,
                tpv_ipaddress,
                tpv_services,
                tpv_remark,
                tpv_status,
                tpv_lastuser,
                tpv_lastupdate
            FROM t_pcservervm
            WHERE tpv_id = ? AND tpv_status <> 25
        ";
        
        return $this->db_sysinfra->query($query, [$id])->getRow();
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