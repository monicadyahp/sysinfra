<?php

namespace App\Models\pcclient; // Updated namespace

use CodeIgniter\Model;

class PCClientModel extends Model // Updated class name
{
    protected $db_postgree;
    protected $db_sysinfra;

    public function __construct()
    {
        $this->db_postgree = db_connect('jincommon');
        $this->db_sysinfra = db_connect('jinsystem');
    }

    public function getData()
    {
        return $this->db_sysinfra->query("
            SELECT 
                tpc.tpc_id,
                tpc.tpc_name,
                tpc.tpc_assetno,
                tpc.tpc_monitorassetno,
                tpc.tpc_ipbefore,
                tpc.tpc_ipafter,
                tpc.tpc_itequipment,
                tpc.tpc_user,
                tpc.tpc_area,
                tpc.tpc_status,
                tpc.tpc_lastuser,
                tpc.tpc_lastupdate
            FROM t_pcclient tpc
            WHERE tpc.tpc_status <> 25
            ORDER BY tpc.tpc_id DESC
        ")->getResult();
    }

    public function getPCClientById($id)
    {
        return $this->db_sysinfra->query("
            SELECT *
            FROM t_pcclient
            WHERE tpc_status <> 25 AND tpc_id = ?
        ", [$id])->getRow();
    }

    public function storeData($data)
    {
        $insertData = [
            'tpc_name'          => isset($data['pc_name']) && $data['pc_name'] !== '' ? $data['pc_name'] : null,
            'tpc_assetno'       => isset($data['pc_assetno']) && $data['pc_assetno'] !== '' ? $data['pc_assetno'] : null,
            'tpc_monitorassetno'=> isset($data['monitor_assetno']) && $data['monitor_assetno'] !== '' ? $data['monitor_assetno'] : null,
            'tpc_ipbefore'      => isset($data['ip_before']) && $data['ip_before'] !== '' ? $data['ip_before'] : null,
            'tpc_ipafter'       => isset($data['ip_after']) && $data['ip_after'] !== '' ? $data['ip_after'] : null,
            'tpc_itequipment'   => isset($data['it_equipment']) && $data['it_equipment'] !== '' ? $data['it_equipment'] : null,
            'tpc_user'          => isset($data['user']) && $data['user'] !== '' ? $data['user'] : null,
            'tpc_area'          => isset($data['area']) && $data['area'] !== '' ? $data['area'] : null,
            'tpc_status'        => 1, // 1 for active
            'tpc_lastuser'      => session()->get('user_info')['em_emplcode'] ?? null, // Menggunakan null coalescing operator untuk menghindari error jika tidak ada
            'tpc_lastupdate'    => date('Y-m-d H:i:s')
        ];
    
        try {
            $this->db_sysinfra->table('t_pcclient')->insert($insertData);
    
            // **Sementara komentari baris ini untuk debugging:**
            // $this->handleIPStatusUpdate('', '', $insertData['tpc_ipbefore'], $insertData['tpc_ipafter']);
    
            return [
                'status' => true,
                'message' => 'PC Client created successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error creating PC Client: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while saving PC Client data: ' . $e->getMessage()
            ];
        }
    }

    public function updateData($data)
    {

        // Get old PC Client data to compare IP addresses
        $oldPCClient = $this->getPCClientById($data['tpc_id']);
        $oldIPBefore = $oldPCClient ? $oldPCClient->tpc_ipbefore : '';
        $oldIPAfter = $oldPCClient ? $oldPCClient->tpc_ipafter : '';

        $updateData = [
            'tpc_name'          => isset($data['pc_name']) && $data['pc_name'] !== '' ? $data['pc_name'] : null,
            'tpc_assetno'       => isset($data['pc_assetno']) && $data['pc_assetno'] !== '' ? $data['pc_assetno'] : null,
            'tpc_monitorassetno'=> isset($data['monitor_assetno']) && $data['monitor_assetno'] !== '' ? $data['monitor_assetno'] : null,
            'tpc_ipbefore'      => isset($data['ip_before']) && $data['ip_before'] !== '' ? $data['ip_before'] : null,
            'tpc_ipafter'       => isset($data['ip_after']) && $data['ip_after'] !== '' ? $data['ip_after'] : null,
            'tpc_itequipment'   => isset($data['it_equipment']) && $data['it_equipment'] !== '' ? $data['it_equipment'] : null,
            'tpc_user'          => isset($data['user']) && $data['user'] !== '' ? $data['user'] : null,
            'tpc_area'          => isset($data['area']) && $data['area'] !== '' ? $data['area'] : null,
            'tpc_lastuser'      => session()->get('user_info')['em_emplcode'] ?? null, // Menggunakan null coalescing operator untuk menghindari error jika tidak ada
            'tpc_lastupdate'    => date('Y-m-d H:i:s')
        ];
                         
        try {
            $this->db_sysinfra->table('t_pcclient')
                ->where('tpc_id', $data['tpc_id'])
                ->update($updateData);

            // Handle IP status updates
            $this->handleIPStatusUpdate($oldIPBefore, $oldIPAfter, $updateData['tpc_ipbefore'], $updateData['tpc_ipafter']);

            return [
                'status'  => true,
                'message' => 'PC Client updated successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating PC Client: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Error occurred while updating PC Client data: ' . $e->getMessage()
            ];
        }
    }

    public function deleteData($id)
    {
        // Get PC Client data to release IP addresses
        $pcClient = $this->getPCClientById($id);

        try {
            $this->db_sysinfra->table('t_pcclient')
                ->where('tpc_id', $id)
                ->update([
                    'tpc_status'     => 25,
                    'tpc_lastuser'   => session()->get('user_info')['em_emplcode'],
                    'tpc_lastupdate' => date('Y-m-d H:i:s')
                ]);

            // Release IP addresses when PC Client is deleted
            if ($pcClient) {
                $this->handleIPStatusUpdate($pcClient->tpc_ipbefore, $pcClient->tpc_ipafter, '', '');
            }

            return [
                'status' => true,
                'message' => 'PC Client has been marked as deleted successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error marking PC Client as deleted: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Failed to delete PC Client'
            ];
        }
    }

    public function getAssetNo($assetNo)
    {
        if (empty($assetNo)) {
            return null;
        }
                
        $query = "
            SELECT ea_assetnumber, ea_id, ea_machineno, ea_model
            FROM tbtfa_equipmentacceptance
        ";
                
        return $this->db_sysinfra->query($query, [$assetNo])->getRow();
    }

    public function searchAssetNo($search = '')
    {
        $query = "
            SELECT ea_assetnumber, ea_id, ea_machineno, ea_model
            FROM tbtfa_equipmentacceptance
        ";
                         
        $params = [];
                         
        if (!empty($search)) {
            $query .= " AND (
                CAST(ea_assetnumber AS VARCHAR) ILIKE ? 
                OR ea_id ILIKE ?
                OR ea_machineno ILIKE ?
                OR ea_model ILIKE ?
            )";
            $search_param = '%' . $search . '%';
            $params = array_fill(0, 4, $search_param);
        }
                         
        $query .= " ORDER BY ea_assetnumber ASC LIMIT 100";
                         
        return $this->db_sysinfra->query($query, $params)->getResult();
    }

    public function getIPAddressById($ipId)
    {
        if (empty($ipId)) {
            return null;
        }
                         
        $query = "
            SELECT mip_id, mip_vlanid, mip_vlanname, mip_ipadd, mip_status
            FROM m_ipaddress
            WHERE mip_id = ?
            AND mip_status = 0
        ";
                         
        return $this->db_sysinfra->query($query, [$ipId])->getRow();
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
                         
        return $this->db_sysinfra->query($query, [$ipAddress])->getRow();
    }

    public function searchIPAddresses($search = '', $excludeIPs = [])
    {
        $query = "
            SELECT mip_id, mip_vlanid, mip_vlanname, mip_ipadd, mip_status
            FROM m_ipaddress
            WHERE mip_status = 0
        ";
        
        $params = [];
        
        // Exclude IPs that are already selected in IP Before
        if (!empty($excludeIPs)) {
            $placeholders = implode(',', array_fill(0, count($excludeIPs), '?'));
            $query .= " AND mip_ipadd NOT IN ($placeholders)";
            $params = array_merge($params, $excludeIPs);
        }
        
        if (!empty($search)) {
            $query .= " AND (
                CAST(mip_vlanid AS VARCHAR) ILIKE ?
                OR mip_vlanname ILIKE ?
                OR mip_ipadd ILIKE ?
            )";
            $search_param = '%' . $search . '%';
            $params = array_merge($params, array_fill(0, 3, $search_param));
        }
        
        $query .= " ORDER BY mip_vlanid ASC, mip_ipadd ASC LIMIT 100";
        
        return $this->db_sysinfra->query($query, $params)->getResult();
    }

    public function updateIPStatus($ipAddress, $status)
    {
        if (empty($ipAddress)) {
            return false;
        }

        try {
            $this->db_sysinfra->table('m_ipaddress')
                ->where('mip_ipadd', $ipAddress)
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

    private function handleIPStatusUpdate($oldIPBefore, $oldIPAfter, $newIPBefore, $newIPAfter)
    {
        // Release old IP addresses (set status back to 0 - unused)
        if (!empty($oldIPBefore) && $oldIPBefore !== $newIPBefore) {
            $this->updateIPStatus($oldIPBefore, 0); // Set to unused
        }
        if (!empty($oldIPAfter) && $oldIPAfter !== $newIPAfter) {
            $this->updateIPStatus($oldIPAfter, 0); // Set to unused
        }

        // Mark new IP addresses with correct status
        if (!empty($newIPBefore)) {
            $this->updateIPStatus($newIPBefore, 0); // ip_before should be unused
        }
        if (!empty($newIPAfter)) {
            $this->updateIPStatus($newIPAfter, 1); // ip_after should be used
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

    public function searchEmployees($search = '', $exclude = '')
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

        $params = [];

        if (!empty($search)) {
            $query .= " AND (
                emp.em_emplname ILIKE ? 
                OR CAST(emp.em_emplcode AS VARCHAR) ILIKE ?
                OR pos.pm_positionname ILIKE ?
                OR sec.sec_section ILIKE ?
            )";
            $search_param = '%' . $search . '%';
            $params = array_fill(0, 4, $search_param);
        }

        if (!empty($exclude)) {
            $query .= " AND emp.em_emplname != ?";
            $params[] = $exclude;
        }

        $query .= " ORDER BY emp.em_emplname ASC LIMIT 100";

        return $this->db_postgree->query($query, $params)->getResult();
    }

    public function getAreas()
    {
        return $this->db_postgree->query("
            SELECT sec_sectioncode, sec_teamnaming
            FROM tbmst_section 
            WHERE sec_status = 1
            ORDER BY sec_teamnaming ASC
        ")->getResult();
    }

}