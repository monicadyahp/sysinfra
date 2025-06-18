<?php

namespace App\Models\TransHandover;

use CodeIgniter\Model;
use Config\Database;

class TransHandoverModel extends Model
{
    protected $db_sysinfra;
    protected $db_postgree;

    public function __construct()
    {
        parent::__construct();
        // Koneksi ke DB system (jinsystem)
        $this->db_sysinfra = Database::connect('jinsystem');
        // Koneksi ke DB common (jincommon)
        $this->db_postgree = Database::connect('jincommon');
    }

    protected function getLastUserCode()
    {
        if (session()->has('username')) {
            return $this->db_postgree->table('tbua_useraccess')
                        ->select('ua_emplcode')
                        ->where('ua_username', session()->get('username'))
                        ->get()
                        ->getRow('ua_emplcode') ?? 0;
        }
        return 0;
    }

    public function getHandoverData()
    {
        $handovers = $this->db_sysinfra->query("
            SELECT th_recordno, th_requestdate, th_empno_rep, th_empname_rep, th_sectioncode_rep, th_purpose, th_reason, th_status, th_lastuser, th_lastupdate
            FROM t_handover
            WHERE th_status <> 25
            ORDER BY th_lastupdate DESC
        ")->getResult();
        
        // Get section names for each handover
        foreach ($handovers as $handover) {
            $employee = $this->getEmployeeById($handover->th_empno_rep);
            $handover->section_name = $employee ? $employee->sec_section : '';
        }
        
        return $handovers;
    }
    
    public function getHandoverById($id)
    {
        $handover = $this->db_sysinfra->query("
            SELECT *
            FROM t_handover
            WHERE th_recordno = ?
        ", [$id])->getRow();
        
        if ($handover) {
            $employee = $this->getEmployeeById($handover->th_empno_rep);
            $handover->section_name = $employee ? $employee->sec_section : '';
        }
        
        return $handover;
    }

    public function checkRecordNoExists($recordNo)
    {
        return $this->db_sysinfra->query("
            SELECT COUNT(*) as count
            FROM t_handover
            WHERE th_recordno = ?
            AND th_status <> 25
        ", [$recordNo])->getRow()->count > 0;
    }

    public function storeHandoverData($data)
    {
        // Check if record no already exists and is active
        if ($this->checkRecordNoExists($data['record_no'])) {
            return [
                'status' => false,
                'message' => 'Record no already exists. Please use a different record no.'
            ];
        }
        
        $insertData = [
            'th_recordno'       => isset($data['record_no']) && $data['record_no'] !== '' ? $data['record_no'] : null,
            'th_requestdate'    => date('Y-m-d H:i:s', strtotime($data['request_date'])),
            'th_empno_rep'      => isset($data['employee_no']) && $data['employee_no'] !== '' ? $data['employee_no'] : null,
            'th_empname_rep'    => isset($data['employee_name']) && $data['employee_name'] !== '' ? $data['employee_name'] : null,
            'th_sectioncode_rep'=> isset($data['section_code']) && $data['section_code'] !== '' ? $data['section_code'] : null,
            'th_purpose'        => isset($data['purpose_content']) && $data['purpose_content'] !== '' ? $data['purpose_content'] : null,
            'th_reason'         => isset($data['reason_content']) && $data['reason_content'] !== '' ? $data['reason_content'] : null,
            'th_status'         => 1,
            'th_lastuser'       => $this->getLastUserCode(),
            'th_lastupdate'     => date('Y-m-d H:i:s')
        ];
        
        try {
            $this->db_sysinfra->table('t_handover')->insert($insertData);
            return [
                'status' => true,
                'message' => 'Handover created successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error creating handover: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while saving handover data: ' . $e->getMessage()
            ];
        }
    }
    
    public function updateHandoverData($data)
    {
        // Get the original record number
        $originalRecordNo = $data['original_record_no'] ?? $data['record_no'];
        $newRecordNo = $data['record_no'];
        
        // Check if the record number is being changed
        $isRecordNoChanged = ($originalRecordNo != $newRecordNo);
        
        // If changing record number, check if the new number already exists
        if ($isRecordNoChanged && $this->checkRecordNoExists($newRecordNo)) {
            return [
                'status' => false,
                'message' => 'Record no already exists. Please use a different record no.'
            ];
        }
        
        $updateData = [
            'th_requestdate'    => date('Y-m-d H:i:s', strtotime($data['request_date'])),
            'th_empno_rep'      => isset($data['employee_no']) && $data['employee_no'] !== '' ? $data['employee_no'] : null,
            'th_empname_rep'    => isset($data['employee_name']) && $data['employee_name'] !== '' ? $data['employee_name'] : null,
            'th_sectioncode_rep'=> isset($data['section_code']) && $data['section_code'] !== '' ? $data['section_code'] : null,
            'th_purpose'        => isset($data['purpose_content']) && $data['purpose_content'] !== '' ? $data['purpose_content'] : null,
            'th_reason'         => isset($data['reason_content']) && $data['reason_content'] !== '' ? $data['reason_content'] : null,
            'th_lastuser'       => $this->getLastUserCode(),
            'th_lastupdate'     => date('Y-m-d H:i:s')
        ];
        
        try {
            // Begin transaction
            $this->db_sysinfra->transBegin();
            
            if ($isRecordNoChanged) {
                // Get the current record to preserve any fields not included in the update
                $currentRecord = $this->getHandoverById($originalRecordNo);
                
                if (!$currentRecord) {
                    throw new \Exception("Original record not found.");
                }
                
                // Create a full record with the new record number
                $newRecord = [
                    'th_recordno'       => $newRecordNo,
                    'th_requestdate'    => date('Y-m-d H:i:s', strtotime($data['request_date'])),
                    'th_empno_rep'      => isset($data['employee_no']) && $data['employee_no'] !== '' ? $data['employee_no'] : null,
                    'th_empname_rep'    => isset($data['employee_name']) && $data['employee_name'] !== '' ? $data['employee_name'] : null,
                    'th_sectioncode_rep'=> isset($data['section_code']) && $data['section_code'] !== '' ? $data['section_code'] : null,
                    'th_purpose'        => isset($data['purpose_content']) && $data['purpose_content'] !== '' ? $data['purpose_content'] : null,
                    'th_reason'         => isset($data['reason_content']) && $data['reason_content'] !== '' ? $data['reason_content'] : null,
                    'th_status'         => $currentRecord->th_status, // Preserve the original status
                    'th_lastuser'       => $this->getLastUserCode(),
                    'th_lastupdate'     => date('Y-m-d H:i:s')
                ];
                
                // Insert new record
                $this->db_sysinfra->table('t_handover')->insert($newRecord);
                
                // Update all related detail records
                $this->db_sysinfra->table('t_handoverdetail')
                    ->where('hd_recordno', $originalRecordNo)
                    ->update(['hd_recordno' => $newRecordNo]);
                
                // Delete old record
                $this->db_sysinfra->table('t_handover')
                    ->where('th_recordno', $originalRecordNo)
                    ->delete();
            } else {
                // Just update the regular fields without changing the record number
                $this->db_sysinfra->table('t_handover')
                    ->where('th_recordno', $originalRecordNo)
                    ->update($updateData);
            }
            
            // Commit transaction if all is well
            if ($this->db_sysinfra->transStatus() === FALSE) {
                $this->db_sysinfra->transRollback();
                return [
                    'status' => false,
                    'message' => 'Error occurred while updating handover data.'
                ];
            } else {
                $this->db_sysinfra->transCommit();
                return [
                    'status' => true,
                    'message' => 'Handover updated successfully.'
                ];
            }
        } catch (\Exception $e) {
            $this->db_sysinfra->transRollback();
            log_message('error', 'Error updating handover: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while updating handover data: ' . $e->getMessage()
            ];
        }
    }

    public function updateDeletedRecord($data)
    {
        $updateData = [
            'th_requestdate'    => date('Y-m-d H:i:s', strtotime($data['request_date'])),
            'th_empno_rep'      => isset($data['employee_no']) && $data['employee_no'] !== '' ? $data['employee_no'] : null,
            'th_empname_rep'    => isset($data['employee_name']) && $data['employee_name'] !== '' ? $data['employee_name'] : null,
            'th_sectioncode_rep'=> isset($data['section_code']) && $data['section_code'] !== '' ? $data['section_code'] : null,
            'th_purpose'        => isset($data['purpose_content']) && $data['purpose_content'] !== '' ? $data['purpose_content'] : null,
            'th_reason'         => isset($data['reason_content']) && $data['reason_content'] !== '' ? $data['reason_content'] : null,
            'th_status'         => 1,  // Set to active
            'th_lastuser'       => $this->getLastUserCode(),
            'th_lastupdate'     => date('Y-m-d H:i:s')
        ];
        
        try {
            $this->db_sysinfra->table('t_handover')
                ->where('th_recordno', $data['record_no'])
                ->update($updateData);
            return [
                'status' => true,
                'message' => 'Handover created successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error reactivating handover: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while reactivating handover data: ' . $e->getMessage()
            ];
        }
    }

    public function deleteHandoverData($id)
    {
        try {
            $this->db_sysinfra->table('t_handover')
                ->where('th_recordno', $id)
                ->update([
                    'th_status' => 25, 
                    'th_lastuser' => $this->getLastUserCode(),
                    'th_lastupdate' => date('Y-m-d H:i:s')
                ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error marking handover as deleted: ' . $e->getMessage());
            return false;
        }
    }

    public function getHandoverDetailData($recordNo)
    {
        return $this->db_sysinfra->query("
            SELECT hd_id, hd_recordno, hd_assetno, hd_serialnumber, hd_equipmentname,
                  hd_delivereddate, hd_deliveredempno_rep, hd_deliveredname_rep,
                  hd_returneddate, hd_returnedempno_rep, hd_returnedname_rep,
                 hd_status, hd_lastuser, hd_lastupdate, hd_category
            FROM t_handoverdetail
            WHERE hd_recordno = ?
            AND hd_status <> 25
            ORDER BY hd_lastupdate DESC
        ", [$recordNo])->getResult();
    } 
    
    public function getHandoverDetailById($id)
    {
        $query = "
            SELECT *
            FROM t_handoverdetail
            WHERE hd_id = ?
            AND hd_status <> 25
        ";
        
        return $this->db_sysinfra->query($query, [$id])->getRow();
    }

    public function storeHandoverDetailData($data)
    {
        // Determine asset no dan serial number berdasarkan add_type
        $assetNo = null;
        $serialNumber = null;
        
        if (isset($data['add_type'])) {
            if ($data['add_type'] === 'asset' && !empty($data['asset_no'])) {
                $assetNo = $data['asset_no'];
                $serialNumber = !empty($data['serial_number']) ? $data['serial_number'] : null;
            } else if ($data['add_type'] === 'serial' && !empty($data['serial_number'])) {
                $serialNumber = $data['serial_number'];
                $assetNo = null;
            } else if ($data['add_type'] === 'equipment') {
                $assetNo = null;
                $serialNumber = null;
            }
        }
        
        $insertData = [
            'hd_recordno'           => $data['recordno'],
            'hd_assetno'            => $assetNo,
            'hd_serialnumber'       => $serialNumber,
            'hd_equipmentname'      => $data['equipment_name'] ?? '',
            'hd_category'           => $data['equipment_category'] ?? null,
            'hd_delivereddate'      => !empty($data['delivered_date']) ? date('Y-m-d', strtotime($data['delivered_date'])) : null,
            'hd_deliveredempno_rep' => !empty($data['delivered_sic_empno']) ? $data['delivered_sic_empno'] : null,
            'hd_deliveredname_rep'  => !empty($data['delivered_sic_name']) ? $data['delivered_sic_name'] : null,
            'hd_returneddate'       => !empty($data['returned_date']) ? date('Y-m-d', strtotime($data['returned_date'])) : null,
            'hd_returnedempno_rep'  => !empty($data['returned_date']) && !empty($data['returned_sic_empno']) ? $data['returned_sic_empno'] : null,
            'hd_returnedname_rep'   => !empty($data['returned_date']) && !empty($data['returned_sic_name']) ? $data['returned_sic_name'] : null,
            'hd_status'             => 1, // 1 for active
            'hd_lastuser'           => $this->getLastUserCode(),
            'hd_lastupdate'         => date('Y-m-d H:i:s')
        ];
        
        try {
            $this->db_sysinfra->table('t_handoverdetail')->insert($insertData);
            return [
                'status' => true,
                'message' => 'Handover detail created successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error creating handover detail: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while saving handover detail data: ' . $e->getMessage()
            ];
        }
    }

    public function updateHandoverDetailData($data)
    {
        // Handle asset_no dan serial_number berdasarkan detail_type
        $assetNo = null;
        $serialNumber = null;
        
        if (isset($data['detail_type'])) {
            if ($data['detail_type'] === 'asset' && !empty($data['asset_no'])) {
                $assetNo = $data['asset_no'];
                $serialNumber = !empty($data['serial_number']) ? $data['serial_number'] : null;
            } else if ($data['detail_type'] === 'serial' && !empty($data['serial_number'])) {
                $serialNumber = $data['serial_number'];
                $assetNo = null;
            } else if ($data['detail_type'] === 'equipment') {
                $assetNo = null;
                $serialNumber = null;
            }
        }
        
        $updateData = [
            'hd_recordno'           => $data['recordno'],
            'hd_assetno'            => $assetNo,
            'hd_serialnumber'       => $serialNumber,
            'hd_equipmentname'      => $data['equipment_name'] ?? '',
            'hd_category'           => $data['equipment_category'] ?? null,
            'hd_delivereddate'      => !empty($data['delivered_date']) ? date('Y-m-d', strtotime($data['delivered_date'])) : null,
            'hd_deliveredempno_rep' => $data['delivered_sic_empno'] ?? null,
            'hd_deliveredname_rep'  => $data['delivered_sic_name'] ?? null,
            'hd_returneddate'       => !empty($data['returned_date']) ? date('Y-m-d', strtotime($data['returned_date'])) : null,
            'hd_returnedempno_rep'  => !empty($data['returned_date']) ? ($data['returned_sic_empno'] ?? null) : null,
            'hd_returnedname_rep'   => !empty($data['returned_date']) ? ($data['returned_sic_name'] ?? null) : null,
            'hd_lastuser'           => $this->getLastUserCode(),
            'hd_lastupdate'         => date('Y-m-d H:i:s')
        ];
        
        try {
            $this->db_sysinfra->table('t_handoverdetail')
                ->where('hd_id', $data['id'])
                ->update($updateData);
            return [
                'status' => true,
                'message' => 'Handover detail updated successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating handover detail: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while updating handover detail data: ' . $e->getMessage()
            ];
        }
    }

    public function deleteHandoverDetailData($id)
    {
        try {
            $this->db_sysinfra->table('t_handoverdetail')
                ->where('hd_id', $id)
                ->update([
                    'hd_status' => 25, // Marked as deleted
                    'hd_lastuser' => $this->getLastUserCode(),
                    'hd_lastupdate' => date('Y-m-d H:i:s')
                ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error marking handover detail as deleted: ' . $e->getMessage());
            return false;
        }
    }
    
    public function searchEmployees($search = '', $exclude = '')
    {
        $query = "
            SELECT emp.em_emplcode, emp.em_emplname, sec.sec_section, sec.sec_sectioncode AS em_sectioncode, pos.pm_positionname
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
        
        // Searching filter
        if (!empty($search)) {
            $query .= " AND (
                emp.em_emplname ILIKE ? 
                OR CAST(emp.em_emplcode AS VARCHAR) ILIKE ?
                OR pos.pm_positionname ILIKE ?
                OR sec.sec_section ILIKE ?
            )";
            $search_param = '%' . $search . '%';
            $params = array_fill(0, 4, $search_param); // Fill 4 parameters with the same value
        }
        
        // Exclude certain employee name if needed
        if (!empty($exclude)) {
            $query .= " AND emp.em_emplname != ?";
            $params[] = $exclude;
        }
        
        $query .= " ORDER BY emp.em_emplname ASC LIMIT 100";
        
        return $this->db_postgree->query($query, $params)->getResult();
    }

    public function searchSystemEmployees($search = '')
    {
        
        // Base query
        $query = "
            SELECT emp.em_emplcode, emp.em_emplname, sec.sec_section, sec.sec_team, sec.sec_sectioncode as em_sectioncode, pos.pm_positionname
            FROM tbmst_employee emp
            LEFT JOIN tbmst_section sec ON emp.em_sectioncode = sec.sec_sectioncode
            LEFT JOIN tbmst_position pos ON emp.em_positioncode = pos.pm_code
            WHERE emp.em_emplstatus < 200
            AND em_sectioncode in (6000, 6001)
            ORDER BY em_sectioncode ASC, em_emplcode ASC
        ";
        
        return $this->db_postgree->query($query)->getResult();
    }
    
    public function getEmployeeById($employeeId)
    {
        $query = "
            SELECT emp.em_emplcode, emp.em_emplname, sec.sec_section, sec.sec_sectioncode as em_sectioncode, pos.pm_positionname
            FROM 
                tbmst_employee emp
            LEFT JOIN 
                tbmst_section sec ON emp.em_sectioncode = sec.sec_sectioncode
            LEFT JOIN
                tbmst_position pos ON emp.em_positioncode = pos.pm_code
            WHERE 
                emp.em_emplcode = ? AND emp.em_emplstatus < 200 
        ";
        
        return $this->db_postgree->query($query, [$employeeId])->getRow();
    }

    public function searchAssets($search = '')
    {
        // Base query to get assets from m_itequipment table
        $query = "
            SELECT e_id, e_assetno, e_equipmentid, e_kind, e_brand, e_model, e_serialnumber, e_equipmentname, e_status
            FROM m_itequipment
            WHERE e_status <> 'Disposed'
        ";
        
        $params = [];
        
        // Add search condition if search term is provided
        if (!empty($search)) {
            $query .= " AND (
                CAST(e_assetno AS VARCHAR) ILIKE ? 
                OR e_serialnumber ILIKE ?
                OR e_equipmentname ILIKE ?
                OR e_brand ILIKE ?
                OR e_model ILIKE ?
            )";
            $search_param = '%' . $search . '%';
            $params = array_fill(0, 5, $search_param); // Fill 5 parameters with the same value
        }
        
        // Order by asset number
        $query .= " ORDER BY e_assetno ASC LIMIT 100";
        
        return $this->db_sysinfra->query($query, $params)->getResult();
    }

    public function getEquipmentByAssetNo($assetNo)
    {
        if (empty($assetNo)) {
            return null;
        }
        
        $query = "
            SELECT e_id, e_assetno, e_equipmentid, e_kind, e_brand, e_model, e_serialnumber, e_equipmentname, e_status
            FROM m_itequipment
            WHERE e_assetno = ?
            AND e_status <> 'Disposed'
        ";
        
        return $this->db_sysinfra->query($query, [$assetNo])->getRow();
    }

    public function searchEquipmentBySerialNumber($search = '')
    {
        $query = "
            SELECT e_id, e_assetno, e_equipmentid, e_kind, e_brand, e_model, e_serialnumber, e_equipmentname, e_status
            FROM m_itequipment
            WHERE e_status <> 'Disposed'
        ";
        
        $params = [];
        
        // Add search condition if search term is provided
        if (!empty($search)) {
            $query .= " AND (
                e_serialnumber ILIKE ?
                OR e_equipmentname ILIKE ?
                OR e_brand ILIKE ?
                OR e_model ILIKE ?
            )";
            $search_param = '%' . $search . '%';
            $params = array_fill(0, 4, $search_param); // Fill 4 parameters with the same value
        }
        
        // Order by serial number
        $query .= " ORDER BY e_serialnumber ASC LIMIT 100";
        
        return $this->db_sysinfra->query($query, $params)->getResult();
    }

    public function getEquipmentBySerialNumber($serialNumber)
    {
        if (empty($serialNumber)) {
            return null;
        }
        
        $query = "
            SELECT e_id, e_assetno, e_equipmentid, e_kind, e_brand, e_model, e_serialnumber, e_equipmentname, e_status
            FROM m_itequipment
            WHERE e_serialnumber = ?
            AND e_status <> 'Disposed'
        ";
        
        return $this->db_sysinfra->query($query, [$serialNumber])->getRow();
    }

    public function getEquipmentCategories()
    {
        return $this->db_sysinfra->query("
            SELECT equipmentcat
            FROM m_equipmentcat
            WHERE ec_status <> 25
            ORDER BY equipmentcat ASC
        ")->getResult();
    }
}
