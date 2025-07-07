<?php

namespace App\Models\mstvlan;

use CodeIgniter\Model;

class MstVLANModel extends Model
{
    protected $db_sysinfra;

    public function __construct()
    {
        $this->db_sysinfra = db_connect('jinsystem');
    }

    public function getData()
    {
        $query = "
            SELECT
                mv_id,
                mv_vlanid,
                mv_name
            FROM m_vlan
            WHERE mv_status <> 25
            ORDER BY mv_id DESC
        ";

        return $this->db_sysinfra->query($query)->getResult();
    }

    public function getVLANById($id)
    {
        return $this->db_sysinfra->query("
            SELECT *
            FROM m_vlan
            WHERE mv_status <> 25 AND mv_id = ?
        ", [$id])->getRow();
    }

    public function storeData($data)
    {
        // Validate at least one field is filled
        if (empty($data['vlan_id']) && empty($data['name'])) {
            return [
                'status' => false,
                'message' => 'Either VLAN ID or VLAN Name must be filled.'
            ];
        }

        // Check for duplicates
        if (!empty($data['vlan_id'])) {
            if (!$this->isVLANIdAvailable($data['vlan_id'])) {
                return [
                    'status' => false,
                    'message' => 'This VLAN ID already exists.'
                ];
            }
        }

        if (!empty($data['name'])) {
            if (!$this->isVLANNameAvailable($data['name'])) {
                return [
                    'status' => false,
                    'message' => 'This VLAN Name already exists.'
                ];
            }
        }

        // Define mv_lastuser before insert/update
        $lastUser = null;
        if (session()->has('user_info') && isset(session()->get('user_info')['em_emplcode'])) {
            $lastUser = session()->get('user_info')['em_emplcode'];
        }

        // Then use $lastUser in your data array:
        $insertData = [
            'mv_vlanid'     => isset($data['vlan_id']) && $data['vlan_id'] !== '' ? (int)$data['vlan_id'] : null,
            'mv_name'       => isset($data['name']) && $data['name'] !== '' ? trim($data['name']) : null,
            'mv_status'     => 1,
            'mv_lastuser'   => $lastUser, // Use the variable here
            'mv_lastupdate' => date('Y-m-d H:i:s')
        ];


        try {
            $this->db_sysinfra->table('m_vlan')->insert($insertData);
            return [
                'status' => true,
                'message' => 'VLAN created successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error creating VLAN: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while saving VLAN data: ' . $e->getMessage()
            ];
        }
    }

    public function updateData($data)
    {
        // Validate at least one field is filled
        if (empty($data['vlan_id']) && empty($data['name'])) {
            return [
                'status' => false,
                'message' => 'Either VLAN ID or VLAN Name must be filled.'
            ];
        }

        // Check for duplicates (exclude current record)
        if (!empty($data['vlan_id'])) {
            if (!$this->isVLANIdAvailable($data['vlan_id'], $data['id'])) {
                return [
                    'status' => false,
                    'message' => 'This VLAN ID already exists.'
                ];
            }
        }

        if (!empty($data['name'])) {
            if (!$this->isVLANNameAvailable($data['name'], $data['id'])) {
                return [
                    'status' => false,
                    'message' => 'This VLAN Name already exists.'
                ];
            }
        }

        // Define mv_lastuser before insert/update
        $lastUser = null;
        if (session()->has('user_info') && isset(session()->get('user_info')['em_emplcode'])) {
            $lastUser = session()->get('user_info')['em_emplcode'];
        }

        // And similarly for updateData
        $updateData = [
            'mv_vlanid' => isset($data['vlan_id']) && $data['vlan_id'] !== '' ? (int)$data['vlan_id'] : null,
            'mv_name' => isset($data['name']) && $data['name'] !== '' ? trim($data['name']) : null,
            'mv_lastuser' => $lastUser, // Use the variable here
            'mv_lastupdate' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('m_vlan')
                ->where('mv_id', $data['id'])
                ->update($updateData);

            return [
                'status' => true,
                'message' => 'VLAN updated successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating VLAN: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while updating VLAN data: ' . $e->getMessage()
            ];
        }
    }
    
    // app > Models > mstvlan > MstVLANModel.php

    public function deleteData($id)
    {
        try {
            // Define mv_lastuser safely
            $lastUser = null;
            if (session()->has('user_info') && isset(session()->get('user_info')['em_emplcode'])) {
                $lastUser = session()->get('user_info')['em_emplcode'];
            }
            // Alternatively, using null coalescing operator (PHP 7.0+):
            // $lastUser = session()->get('user_info')['em_emplcode'] ?? null;


            // Mark VLAN as deleted (status = 25)
            $this->db_sysinfra->table('m_vlan')
                ->where('mv_id', $id)
                ->update([
                    'mv_status'     => 25,
                    'mv_lastuser'   => $lastUser, // Use the safely retrieved user code
                    'mv_lastupdate' => date('Y-m-d H:i:s')
                ]);

            return [
                'status' => true,
                'message' => 'VLAN data have been marked as deleted successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error marking VLAN as deleted: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Failed to delete VLAN: ' . $e->getMessage()
            ];
        }
    }

    public function isVLANIdAvailable($vlanId, $excludeId = null)
    {
        if (empty($vlanId)) {
            return true; // Empty VLAN ID is allowed
        }

        $query = "
            SELECT COUNT(*) as count 
            FROM m_vlan 
            WHERE mv_vlanid = ?
        ";
        
        $params = [(int)$vlanId];
        
        // Exclude current record when updating
        if ($excludeId !== null) {
            $query .= " AND mv_id <> ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db_sysinfra->query($query, $params)->getRow();
        
        return $result->count == 0;
    }

    public function isVLANNameAvailable($vlanName, $excludeId = null)
    {
        if (empty($vlanName)) {
            return true; // Empty VLAN Name is allowed
        }

        $query = "
            SELECT COUNT(*) as count 
            FROM m_vlan 
            WHERE TRIM(mv_name) = ?
        ";
        
        $params = [trim($vlanName)];
        
        // Exclude current record when updating
        if ($excludeId !== null) {
            $query .= " AND mv_id <> ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db_sysinfra->query($query, $params)->getRow();
        
        return $result->count == 0;
    }
}