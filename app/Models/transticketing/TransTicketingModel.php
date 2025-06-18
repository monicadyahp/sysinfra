<?php

namespace App\Models\transticketing;

use CodeIgniter\Model;

class TransTicketingModel extends Model
{
    protected $db_postgree;
    protected $db_sysinfra;

    public function __construct()
    {
        $this->db_postgree = db_connect('jincommon');
        $this->db_sysinfra = db_connect('jinsystem');
    }

    protected function getUserSectionCode()
    {
        if (session()->has('username')) {
            $result = $this->db_postgree->table('tbmst_employee')
                ->select('em_sectioncode')
                ->where('em_emplcode', session()->get('user_info')['em_emplcode'])
                ->get()
                ->getRow();
                        
            return $result ? $result->em_sectioncode : null;
        }
        return null;
    }

    public function getCurrentUserData()
    {
        $currentUserData = [
            'employeeCode' => '',
            'employeeName' => '',
            'sectionCode' => '',
            'sectionName' => '',
            'isSystemSection' => false
        ];

        if (session()->has('username')) {
            // First get employee code from username
            $userAccess = $this->db_postgree->table('tbua_useraccess')
                ->select('ua_emplcode')
                ->where('ua_username', session()->get('username'))
                ->get()
                ->getRow();

            if ($userAccess && $userAccess->ua_emplcode) {
                $currentUserData['employeeCode'] = $userAccess->ua_emplcode;
                
                // Get employee details using the employee code
                $employee = $this->db_postgree->table('tbmst_employee')
                    ->select('em_emplcode, em_emplname, em_sectioncode')
                    ->where('em_emplcode', $userAccess->ua_emplcode)
                    ->get()
                    ->getRow();

                if ($employee) {
                    $currentUserData['employeeName'] = $employee->em_emplname;
                    $currentUserData['sectionCode'] = $employee->em_sectioncode;
                    
                    // Get section details
                    $section = $this->db_postgree->table('tbmst_section')
                        ->select('sec_section, sec_sectionnaming')
                        ->where('sec_sectioncode', $employee->em_sectioncode)
                        ->get()
                        ->getRow();

                    if ($section) {
                        // Check if the user is from System Section
                        if ($section->sec_section == 'System Section') {
                            $currentUserData['isSystemSection'] = true;
                        }
                        $currentUserData['sectionName'] = $section->sec_sectionnaming;
                    }
                }
            }
        }

        return $currentUserData;
    }

    public function canUserEditTicket($ticketId)
    {
        $currentUserData = $this->getCurrentUserData();
        
        // System Section can edit any ticket
        if ($currentUserData['isSystemSection']) {
            return true;
        }

        // Non-System Section can only edit their own tickets
        $ticket = $this->db_sysinfra->query("
            SELECT tt_empno_rep
            FROM t_ticketing
            WHERE tt_status <> 25 AND tt_id = ?
        ", [$ticketId])->getRow();

        if ($ticket) {
            return $ticket->tt_empno_rep == $currentUserData['employeeCode'];
        }

        return false;
    }

    public function canUserViewTicket($ticketId)
    {
        $currentUserData = $this->getCurrentUserData();
        
        // System Section can view any ticket
        if ($currentUserData['isSystemSection']) {
            return true;
        }

        // Non-System Section can only view their own tickets
        $ticket = $this->db_sysinfra->query("
            SELECT tt_empno_rep
            FROM t_ticketing
            WHERE tt_status <> 25 AND tt_id = ?
        ", [$ticketId])->getRow();

        if ($ticket) {
            return $ticket->tt_empno_rep == $currentUserData['employeeCode'];
        }

        return false;
    }

    public function getData()
    {
        return $this->db_sysinfra->query("
            SELECT 
                CASE WHEN tt_category = '2' THEN 'Infra' ELSE 'SDO' END AS tt_category,
                tt_empno_rep || ' - ' || tt_empname_rep AS tt_empno_rep,
                tt_id, tt_empname_rep, tt_sectioncode_rep, tt_case, tt_pic_system,
                tt_check_date, tt_finish_date, tt_status, tt_lastuser, tt_lastupdate,
                tt_action, tt_assetno, tt_categoryequip
            FROM t_ticketing t
            WHERE t.tt_status <> 25 
            ORDER BY t.tt_id asc
        ")->getResult();
    }
        
    public function getTicketById($id)
    {
        return $this->db_sysinfra->query("
            SELECT *
            FROM t_ticketing
            WHERE tt_status <> 25 AND tt_id = ?
        ", [$id])->getRow();
    }

    public function storeData($data)
    {
        $currentUserData = $this->getCurrentUserData();
        
        // If the user is not from System Section, auto-fill the Reporter Employee ID with the logged-in employee's ID
        if (!$currentUserData['isSystemSection']) {
            // Set the employee no as the logged-in user's employee ID
            $data['employee_no'] = $currentUserData['employeeCode'];
        }

        $insertData = [
            'tt_empno_rep'      => isset($data['employee_no']) && $data['employee_no'] !== '' ? $data['employee_no'] : null,
            'tt_empname_rep'    => isset($data['employee_name']) && $data['employee_name'] !== '' ? $data['employee_name'] : null,
            'tt_sectioncode_rep'=> isset($data['section_code']) && $data['section_code'] !== '' ? $data['section_code'] : null,
            'tt_category'       => isset($data['team']) && $data['team'] !== '' ? $data['team'] : null,
            'tt_case'           => isset($data['case_content']) && $data['case_content'] !== '' ? $data['case_content'] : null,
            'tt_action'         => isset($data['handle_content']) && $data['handle_content'] !== '' ? $data['handle_content'] : null,
            'tt_pic_system'     => isset($data['pic_system']) && $data['pic_system'] !== '' ? $data['pic_system'] : null,
            'tt_check_date'     => date('Y-m-d H:i:s'),
            'tt_finish_date'    => isset($data['finish_date']) && $data['finish_date'] !== '' ? date('Y-m-d H:i:s', strtotime($data['finish_date'])) : null,
            'tt_assetno'        => isset($data['asset_no']) && $data['asset_no'] !== '' ? $data['asset_no'] : null,
            'tt_categoryequip'  => isset($data['category']) && $data['category'] !== '' ? $data['category'] : null,
            'tt_status'         => 1, // 1 for active
            'tt_lastuser'       => session()->get('user_info')['em_emplcode'],
            'tt_lastupdate'     => date('Y-m-d H:i:s')
        ];
                
        try {
            $this->db_sysinfra->table('t_ticketing')->insert($insertData);
            return [
                'status' => true,
                'message' => 'Ticket created successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error creating ticket: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error occurred while saving ticket data: ' . $e->getMessage()
            ];
        }
    }

    public function updateData($data)
    {
        // Check if user can edit this ticket
        if (!$this->canUserEditTicket($data['ticket_id'])) {
            return [
                'status' => false,
                'message' => 'You do not have permission to edit this ticket. You can only edit your own tickets.'
            ];
        }

        $currentUserData = $this->getCurrentUserData();
        
        // If the user is not from System Section, ensure they can only edit their own tickets
        if (!$currentUserData['isSystemSection']) {
            // Force the employee data to be the current user's data (prevent tampering)
            $data['employee_no'] = $currentUserData['employeeCode'];
            $data['employee_name'] = $currentUserData['employeeName'];
            $data['section_code'] = $currentUserData['sectionCode'];
        }

        $updateData = [
            'tt_empno_rep'      => isset($data['employee_no']) && $data['employee_no'] !== '' ? $data['employee_no'] : null,
            'tt_empname_rep'    => isset($data['employee_name']) && $data['employee_name'] !== '' ? $data['employee_name'] : null,
            'tt_sectioncode_rep'=> isset($data['section_code']) && $data['section_code'] !== '' ? $data['section_code'] : null,
            'tt_category'       => isset($data['team']) && $data['team'] !== '' ? $data['team'] : null,
            'tt_case'           => isset($data['case_content']) && $data['case_content'] !== '' ? $data['case_content'] : null,
            'tt_action'         => isset($data['handle_content']) && $data['handle_content'] !== '' ? $data['handle_content'] : null,
            'tt_pic_system'     => isset($data['pic_system']) && $data['pic_system'] !== '' ? $data['pic_system'] : null,
            'tt_finish_date'    => isset($data['finish_date']) && $data['finish_date'] !== '' ? date('Y-m-d H:i:s', strtotime($data['finish_date'])) : null,
            'tt_assetno'        => isset($data['asset_no']) && $data['asset_no'] !== '' ? $data['asset_no'] : null,
            'tt_categoryequip'  => isset($data['category']) && $data['category'] !== '' ? $data['category'] : null,
            'tt_lastuser'       => session()->get('user_info')['em_emplcode'],
            'tt_lastupdate'     => date('Y-m-d H:i:s')
        ];
                
        try {
            $this->db_sysinfra->table('t_ticketing')
                ->where('tt_id', $data['ticket_id'])
                ->update($updateData);
            return [
                'status'  => true,
                'message' => 'Ticket updated successfully.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating ticket: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Error occurred while updating ticket data: ' . $e->getMessage()
            ];
        }
    }

    public function deleteData($id)
    {
        // Check if user can edit this ticket (delete permission same as edit)
        if (!$this->canUserEditTicket($id)) {
            return [
                'status' => false,
                'message' => 'You do not have permission to delete this ticket. You can only delete your own tickets.'
            ];
        }

        try {
            $this->db_sysinfra->table('t_ticketing')
                ->where('tt_id', $id)
                ->update([
                    'tt_status'     => 25,
                    'tt_lastuser'   => session()->get('user_info')['em_emplcode'],
                    'tt_lastupdate' => date('Y-m-d H:i:s')
                ]);
            return [
                'status' => true,
                'message' => 'Ticket has been marked as deleted successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error marking ticket as deleted: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Failed to delete ticket'
            ];
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

        // Searching filter
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

    public function getSystemEmployees()
    {
        $query = "
            SELECT emp.em_emplcode, emp.em_emplname, sec.sec_section, sec.sec_team, sec.sec_sectioncode as em_sectioncode, pos.pm_positionname 
            FROM tbmst_employee emp 
            LEFT JOIN tbmst_section sec ON emp.em_sectioncode = sec.sec_sectioncode 
            LEFT JOIN tbmst_position pos ON emp.em_positioncode = pos.pm_code 
            WHERE emp.em_emplstatus < 200 
            and sec.sec_section = 'System Section'
            order by em_sectioncode asc, sec.sec_section asc
        ";

        return $this->db_postgree->query($query)->getResult();
    }

    public function getSectionName($secId)
    {
        return $this->db_postgree->query("
            select sec_section from tbmst_section ts where sec_sectioncode = ? and sec_status = 1
        ", [$secId])->getRow();
    }

    public function getPicName($empNo)
    {
        return $this->db_postgree->query("
            select em_emplname from tbmst_employee where em_emplcode = ? and em_emplstatus < 200
        ", [$empNo])->getRow();
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

    public function getCategories()
    {
        $query = "
            SELECT equipmentcat
            FROM m_equipmentcat
            WHERE ec_status <> 25
            ORDER BY equipmentcat ASC
        ";
                
        return $this->db_sysinfra->query($query)->getResult();
    }
    
}