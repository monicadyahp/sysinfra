<?php
// app/Models/MstIPAdd/MstIPAddModel.php

namespace App\Models\mstipadd;

use CodeIgniter\Model;

class MstIPAddModel extends Model
{
    protected $db_sysinfra;

    public function __construct()
    {
        // Pastikan 'jinsystem' terdefinisi di app/Config/Database.php
        $this->db_sysinfra = db_connect('jinsystem');
    }

    public function getData($statusFilter = null)
    {
        $query = "SELECT 
                    mip_id,
                    mip_vlanid,
                    mip_vlanname,
                    mip_ipadd,
                    mip_status,
                    mip_lastupdate,
                    mip_lastuser
                FROM m_ipaddress";
        
        $params = [];
        
        // Add conditions based on status filter (reverted to old logic for consistency)
        if ($statusFilter !== null && $statusFilter !== 'All') {
            $query .= " WHERE ";
            
            if ($statusFilter === 'Used') {
                // 'Used': mip_lastuser BUKAN 0 (dan bukan NULL) AND mip_status = 0
                $query .= "mip_lastuser != 0 AND mip_status = 0 AND mip_lastuser IS NOT NULL";
            } else { // 'Unused'
                // 'Unused': mip_lastuser = 0 (atau NULL) OR mip_status = 1
                $query .= "(mip_lastuser = 0 OR mip_status = 1 OR mip_lastuser IS NULL)";
            }
        }

        $query .= " ORDER BY mip_lastupdate DESC";
        
        return $this->db_sysinfra->query($query, $params)->getResult();
    }

    public function toggleStatus($id, $currentMipStatus, $currentMipLastUser)
    {
        // Determine current logical status based on the *consistent* definition:
        // 'Used' if mip_lastuser is NOT 0 AND mip_status is 0
        $isCurrentlyUsedLogical = ($currentMipLastUser !== 0 && $currentMipStatus === 0);
        
        $newMipStatus;
        $newMipLastUser;

        // *** PERBAIKAN: Menangani kemungkinan session()->get('user_info') bernilai null ***
        $userInfo = session()->get('user_info');
        $loggedInUserId = null;

        if (is_array($userInfo) && isset($userInfo['em_emplcode'])) {
            $loggedInUserId = $userInfo['em_emplcode'];
        } else {
            // Fallback jika session user ID tidak tersedia atau tidak dalam format yang diharapkan
            // Anda bisa menggunakan ID default (misal 1) atau 0 jika tidak ada pengguna yang login.
            // Saya akan menggunakan 1 sebagai fallback, seperti yang Anda gunakan sebelumnya.
            log_message('warning', 'session()->get("user_info") is null or missing "em_emplcode", defaulting mip_lastuser to 1.');
            $loggedInUserId = 1; 
        }

        if ($isCurrentlyUsedLogical) {
            // Currently "Used" (mip_lastuser != 0, mip_status = 0) -> Change to "Unused"
            $newMipStatus = 1; // Set mip_status to 1 (Unused)
            $newMipLastUser = 0; // Set mip_lastuser to 0 (Unused)
        } else {
            // Currently "Unused" (mip_lastuser = 0 OR mip_status = 1) -> Change to "Used"
            $newMipStatus = 0; // Set mip_status to 0 (Used)
            $newMipLastUser = $loggedInUserId; // Menggunakan ID pengguna yang sudah divalidasi
        }

        try {
            $this->db_sysinfra->table('m_ipaddress')
                ->where('mip_id', $id)
                ->update([
                    'mip_status' => $newMipStatus,
                    'mip_lastuser' => $newMipLastUser,
                    'mip_lastupdate' => date('Y-m-d H:i:s')
                ]);

            return [
                'status' => true,
                'message' => 'Status updated successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating IP status for ID ' . $id . ': ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ];
        }
    }
}