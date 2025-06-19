<?php

namespace App\Controllers\MstVlan;

use App\Controllers\BaseController;
use App\Models\mstvlan\MstVlanModel;
use CodeIgniter\I18n\Time;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MstVlanController extends BaseController
{
    protected $MstVlanModel;
    protected $db;
    protected $dbCommon; // Essential to fetch username from tbua_useraccess or tbmst_employee

    public function __construct()
    {
        $this->MstVlanModel = new MstVlanModel();
        $this->db = \Config\Database::connect('jinsystem');
        $this->dbCommon = \Config\Database::connect('jincommon'); // Connect to jincommon database
        helper('session');
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $usermenu = session()->get("usermenu");
        $activeMenuGroup = 'Master';
        $activeMenuName = 'VLAN';

        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstVlan") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }

        $data["active_menu_group"] = $activeMenuGroup;
        $data["active_menu_name"] = $activeMenuName;

        return view('master/MstVlan/index', $data);
    }

    public function getDataVlan()
    {
        try {
            // Select tv_id (auto-increment PK) as 'id', tv_id_vlan (user-inputtable) as 'vlan_id'
            // tv_lastuser is selected as 'last_user_id_from_db' because it's an integer ID that needs resolution
            $query = $this->db->table('public.tbmst_vlan')
                             ->select('tv_id AS id, tv_id_vlan AS vlan_id, tv_name AS name,
                                     tv_lastupdate AS last_update, tv_lastuser AS last_user_id_from_db');

            $query->orderBy('tv_lastupdate', 'DESC');

            $data = $query->get()->getResultArray();

            // Collect unique user IDs for lookup, only numeric values
            $userIds = array_filter(array_unique(array_column($data, 'last_user_id_from_db')), 'is_numeric');

            $employeeNames = [];
            if (!empty($userIds)) {
                // Try to find user names in tbmst_employee first
                $employeeQuery = $this->dbCommon->table('public.tbmst_employee')
                                             ->select('em_emplname, em_emplcode')
                                             ->whereIn('em_emplcode', $userIds)
                                             ->get();
                foreach ($employeeQuery->getResultArray() as $emp) {
                    $employeeNames[$emp['em_emplcode']] = $emp['em_emplname'];
                }
            }

            $userAccessNames = [];
            // Get user IDs that were not found in tbmst_employee table
            $userAccessIds = array_diff($userIds, array_keys($employeeNames));
            if (!empty($userAccessIds)) {
                // Then try to find user names in tbua_useraccess
                $userAccessQuery = $this->dbCommon->table('public.tbua_useraccess')
                                             ->select('ua_username, ua_userid')
                                             ->whereIn('ua_userid', $userAccessIds)
                                             ->get();
                foreach ($userAccessQuery->getResultArray() as $user) {
                    $userAccessNames[$user['ua_userid']] = $user['ua_username'];
                }
            }

            // Populate the 'last_user' display name for each row
            foreach ($data as &$row) {
                if (is_numeric($row['last_user_id_from_db'])) {
                    // Prioritize employee name, then user access name, fallback to raw ID if no match
                    $row['last_user'] = $employeeNames[$row['last_user_id_from_db']]
                                            ?? ($userAccessNames[$row['last_user_id_from_db']]
                                            ?? $row['last_user_id_from_db']);
                } else {
                    $row['last_user'] = 'N/A'; // Default for non-numeric or null user IDs
                }
                unset($row['last_user_id_from_db']); // Remove the raw ID field

                // Format last_update date
                if ($row['last_update']) {
                    try {
                        $lastUpdateObj = new Time($row['last_update']);
                        $row['last_update'] = $lastUpdateObj->toDateTimeString();
                    } catch (\Exception $e) {
                        $row['last_update'] = null;
                        log_message('error', 'Error formatting last_update for VLAN: ' . $e->getMessage());
                    }
                } else {
                    $row['last_update'] = null;
                }
            }

            if (empty($data)) {
                return $this->response->setJSON([]);
            }

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching VLAN data: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['error' => true, 'message' => 'Could not retrieve data: ' . $e->getMessage()]);
        }
    }

    public function add()
    {
        $post = $this->request->getPost();

        $vlanIdInput = !empty($post['vlan_id']) ? (int)trim($post['vlan_id']) : null;
        $vlanName = !empty($post['name']) ? strtoupper(trim($post['name'])) : null;

        // Server-side validation: at least one of VLAN ID or VLAN Name must be filled
        $errors = [];
        if (empty($vlanIdInput) && empty($vlanName)) {
            $errors['general'] = 'Either VLAN ID or VLAN Name must be filled.';
            return $this->response->setJSON([
                'status' => false,
                'errors' => $errors,
                'error' => 'Please fill in at least one of VLAN ID or VLAN Name.'
            ]);
        }

        // Check for duplicates only if the respective field is filled
        if (!empty($vlanIdInput)) {
            $isDuplicateVlanId = $this->MstVlanModel->checkDuplicateVlanId($vlanIdInput);
            if ($isDuplicateVlanId) {
                $errors['vlan_id'] = 'This VLAN ID already exists.';
            }
        }
        
        if (!empty($vlanName)) {
            $isDuplicateVlanName = $this->MstVlanModel->checkDuplicateName($vlanName);
            if ($isDuplicateVlanName) {
                $errors['name'] = 'This VLAN Name already exists.';
            }
        }

        if (!empty($errors)) {
            return $this->response->setJSON(['status' => false, 'errors' => $errors, 'error' => 'Duplicate input detected or invalid input.']);
        }

        // --- Store the INTEGER user ID from session ---
        $loggedInUserId = session()->get('user_id'); // Assuming 'user_id' in session contains ua_userid or em_emplcode
        // Fallback to a default integer ID if session 'user_id' is not available or not numeric.
        // It's critical that '1' (or your chosen default) actually exists in tbmst_employee or tbua_useraccess.
        $lastUser = is_numeric($loggedInUserId) ? (int)$loggedInUserId : 1; // Default to 1 (like PCServer example)
        // End of storing INTEGER user ID

        // Prepare data for insertion, explicitly setting null if empty
        $data = [
            'tv_id_vlan' => $vlanIdInput, // Will be null if empty($vlanIdInput)
            'tv_name'    => $vlanName,    // Will be null if empty($vlanName)
            'tv_lastuser' => $lastUser,
        ];

        // Define validation rules only for the fields that are expected to be present
        $rules = [];
        if (!empty($vlanIdInput)) {
            // If VLAN ID is provided, it must be an integer and greater than 0
            $rules['vlan_id'] = 'integer|greater_than[0]';
        }
        if (!empty($vlanName)) {
            // If VLAN Name is provided, it must conform to these rules
            $rules['name'] = 'alpha_numeric_punct|max_length[250]';
        }

        // Apply validation if any rule exists
        // Note: The 'required' rule is NOT used here as the "at least one" check
        // already handles the overall presence requirement.
        if (!empty($rules) && !$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }
        
        try {
            $insertID = $this->MstVlanModel->insert($data);
            if ($insertID === false) {
                return $this->response->setJSON(['status' => false, 'error' => 'Failed to add VLAN. Data might be invalid.']);
            }
            return $this->response->setJSON(['status' => true, 'message' => 'Record added successfully.', 'id' => $insertID]);
        } catch (\Exception $e) {
            log_message('error', 'Error adding VLAN: ' . $e->getMessage());
            // Improved duplicate error handling from database constraints
            if (strpos($e->getMessage(), 'tbmst_vlan_tv_id_vlan_key') !== false) {
                return $this->response->setJSON(['status' => false, 'errors' => ['vlan_id' => 'This VLAN ID already exists.'], 'error' => 'Duplicate VLAN ID.']);
            }
            if (strpos($e->getMessage(), 'tbmst_vlan_tv_name_key') !== false) { // Assuming a unique constraint on tv_name
                return $this->response->setJSON(['status' => false, 'errors' => ['name' => 'This VLAN Name already exists.'], 'error' => 'Duplicate VLAN Name.']);
            }
            // Generic duplicate constraint error if the specific key name is unknown
            if (strpos($e->getMessage(), 'duplicate key value violates unique constraint') !== false) {
                return $this->response->setJSON(['status' => false, 'error' => 'A VLAN with the same ID or Name already exists.']);
            }
            return $this->response->setStatusCode(500)
                                 ->setJSON(['status' => false, 'error' => 'Server error occurred while adding VLAN: ' . $e->getMessage()]);
        }
    }

    public function edit()
    {
        $id = $this->request->getPost('id');
        $row = $this->MstVlanModel->find($id);

        if (!$row) {
            return $this->response->setStatusCode(404)
                                 ->setJSON(['status' => false, 'message' => 'Data not found']);
        }

        // --- Resolve last_user_id_from_db to a display name for edit modal ---
        $lastUserDisplayName = 'N/A';
        if (!empty($row['tv_lastuser']) && is_numeric($row['tv_lastuser'])) {
            $lastUserValue = (int)$row['tv_lastuser'];

            // Try to find in tbmst_employee
            $userFromEmployee = $this->dbCommon->table('public.tbmst_employee')
                                               ->select('em_emplname')
                                               ->where('em_emplcode', $lastUserValue)
                                               ->get()
                                               ->getRowArray();
            if ($userFromEmployee) {
                $lastUserDisplayName = $userFromEmployee['em_emplname'];
            } else {
                // Try to find in tbua_useraccess if not in tbmst_employee
                $userFromUserAccess = $this->dbCommon->table('public.tbua_useraccess')
                                                     ->select('ua_username')
                                                     ->where('ua_userid', $lastUserValue)
                                                     ->get()
                                                     ->getRowArray();
                if ($userFromUserAccess) {
                    $lastUserDisplayName = $userFromUserAccess['ua_username'];
                } else {
                    $lastUserDisplayName = 'ID: ' . $lastUserValue; // Fallback to ID if no name found
                }
            }
        } else if (!empty($row['tv_lastuser'])) {
            // If it's not numeric (e.g., legacy string data or error), display as is
            $lastUserDisplayName = $row['tv_lastuser'];
        }
        // End of resolving last_user_id_from_db

        $data = [
            'id'           => $row['tv_id'],
            'vlan_id'      => $row['tv_id_vlan'],
            'name'         => $row['tv_name'],
            'last_update'  => $row['tv_lastupdate'],
            'last_user'    => $lastUserDisplayName, // Pass the resolved name to the view
        ];

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function update()
    {
        $post = $this->request->getPost();
        $id = $post['id'];

        $vlanIdInput = !empty($post['vlan_id']) ? (int)trim($post['vlan_id']) : null;
        $vlanName = !empty($post['name']) ? strtoupper(trim($post['name'])) : null;

        // Server-side validation: at least one of VLAN ID or VLAN Name must be filled for update
        $errors = [];
        if (empty($vlanIdInput) && empty($vlanName)) {
            $errors['general'] = 'Either VLAN ID or VLAN Name must be filled.';
            return $this->response->setJSON([
                'status' => false,
                'errors' => $errors,
                'error' => 'Please fill in at least one of VLAN ID or VLAN Name.'
            ]);
        }

        // Check for duplicates only if the respective field is filled, excluding the current record
        if (!empty($vlanIdInput)) {
            $isDuplicateVlanId = $this->MstVlanModel->checkDuplicateVlanId($vlanIdInput, $id);
            if ($isDuplicateVlanId) {
                $errors['vlan_id'] = 'This VLAN ID already exists.';
            }
        }
        
        if (!empty($vlanName)) {
            $isDuplicateVlanName = $this->MstVlanModel->checkDuplicateName($vlanName, $id);
            if ($isDuplicateVlanName) {
                $errors['name'] = 'This VLAN Name already exists.';
            }
        }

        if (!empty($errors)) {
            return $this->response->setJSON(['status' => false, 'errors' => $errors, 'error' => 'Duplicate input detected or invalid input.']);
        }

        // --- Store the INTEGER user ID from session ---
        $loggedInUserId = session()->get('user_id'); // Assuming 'user_id' in session contains ua_userid or em_emplcode
        // Fallback to a default integer ID if session 'user_id' is not available or not numeric.
        $lastUser = is_numeric($loggedInUserId) ? (int)$loggedInUserId : 1; // Default to 1 (like PCServer example)
        // End of storing INTEGER user ID

        // Prepare data for update, explicitly setting null if empty
        $data = [
            'tv_id_vlan' => $vlanIdInput, // Will be null if empty($vlanIdInput)
            'tv_name'    => $vlanName,    // Will be null if empty($vlanName)
            'tv_lastuser' => $lastUser,
        ];

        // Define validation rules only for the fields that are expected to be present
        $rules = [];
        if (!empty($vlanIdInput)) {
            $rules['vlan_id'] = 'integer|greater_than[0]';
        }
        if (!empty($vlanName)) {
            $rules['name'] = 'alpha_numeric_punct|max_length[250]';
        }

        if (!empty($rules) && !$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        try {
            $this->MstVlanModel->update($id, $data);
            return $this->response->setJSON(['status' => true, 'message' => 'Record updated successfully.']);
        } catch (\Exception $e) {
            log_message('error', 'Error updating VLAN: ' . $e->getMessage());
            // Improved duplicate error handling from database constraints
            if (strpos($e->getMessage(), 'tbmst_vlan_tv_id_vlan_key') !== false) {
                return $this->response->setJSON(['status' => false, 'errors' => ['vlan_id' => 'This VLAN ID already exists.'], 'error' => 'Duplicate VLAN ID.']);
            }
            if (strpos($e->getMessage(), 'tbmst_vlan_tv_name_key') !== false) { // Assuming a unique constraint on tv_name
                return $this->response->setJSON(['status' => false, 'errors' => ['name' => 'This VLAN Name already exists.'], 'error' => 'Duplicate VLAN Name.']);
            }
            // Generic duplicate constraint error if the specific key name is unknown
            if (strpos($e->getMessage(), 'duplicate key value violates unique constraint') !== false) {
                 return $this->response->setJSON(['status' => false, 'error' => 'A VLAN with the same ID or Name already exists.']);
            }
            return $this->response->setStatusCode(500)
                                 ->setJSON(['status' => false, 'error' => 'Server error occurred while updating VLAN: ' . $e->getMessage()]);
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        try {
            $this->MstVlanModel->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Record deleted successfully.']);
        } catch (\Exception $e) {
            log_message('error', 'Delete VLAN error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Checks for duplicate VLAN names.
     * @param string $vlanName The VLAN name to check.
     * @param int|null $id The auto-increment primary key (tv_id) of the current record being edited.
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function checkDuplicateName()
    {
        $post = $this->request->getPost();
        $id = isset($post['id']) ? (int)$post['id'] : null;
        $vlanName = trim($post['name']);

        $isDuplicate = $this->MstVlanModel->checkDuplicateName($vlanName, $id);

        return $this->response->setJSON(['existName' => $isDuplicate]);
    }

    /**
     * Checks for duplicate user-inputted VLAN IDs (tv_id_vlan).
     * @param int $vlanIdInput The user-input VLAN ID (tv_id_vlan) to check.
     * @param int|null $id The auto-increment primary key (tv_id) of the current record being edited.
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function checkDuplicateVlanId()
    {
        $post = $this->request->getPost();
        $id = isset($post['id']) ? (int)$post['id'] : null;
        $vlanIdInput = (int)trim($post['vlan_id']);

        $isDuplicate = $this->MstVlanModel->checkDuplicateVlanId($vlanIdInput, $id);

        return $this->response->setJSON(['existVlanId' => $isDuplicate]);
    }

    /**
     * Export all VLAN data to Excel.
     */
    public function exportExcel()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        try {
            // Fetch all VLAN records. Exclude 'tv_lastuser' and 'tv_lastupdate' as requested.
            $vlanData = $this->db->table('public.tbmst_vlan')
                                 ->select('tv_id AS id, tv_id_vlan AS vlan_id, tv_name AS name')
                                 ->orderBy('tv_id_vlan', 'ASC') // Order by VLAN ID for better readability
                                 ->get()
                                 ->getResultArray();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('VLAN Report');

            // Header for the entire report
            $sheet->setCellValue('A1', 'VLAN MANAGEMENT REPORT');
            $sheet->mergeCells('A1:C1'); // Merge across 3 columns (No., VLAN ID, VLAN Name)
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 18],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Empty row for spacing
            $sheet->getRowDimension(2)->setRowHeight(15);

            // Set main header row
            $mainHeaders = [
                'No.', 'VLAN ID', 'VLAN Name'
            ];
            $headerStartRow = 4;
            $sheet->fromArray($mainHeaders, NULL, 'A' . $headerStartRow);

            // Apply style to header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCDE8F3']], // Light Blue
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
            ];
            $sheet->getStyle('A' . $headerStartRow . ':C' . $headerStartRow)->applyFromArray($headerStyle); // Apply style to A-C

            // Define alternating row styles
            $styleEvenRow = [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFF0F0F0']], // Very light gray
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
            ];
            $styleOddRow = [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFFFFFFF']], // White
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
            ];

            $rowNum = $headerStartRow + 1; // Start data from row after headers
            $recordNo = 1;

            foreach ($vlanData as $vlan) {
                $rowData = [
                    $recordNo++,
                    $vlan['vlan_id'],
                    $vlan['name']
                ];
                $sheet->fromArray($rowData, NULL, 'A' . $rowNum);

                // Apply alternating row style
                $styleToApply = ($rowNum % 2 === 0) ? $styleEvenRow : $styleOddRow;
                $sheet->getStyle('A' . $rowNum . ':C' . $rowNum)->applyFromArray($styleToApply); // Apply style to A-C

                $rowNum++;
            }

            // Set column widths
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Add auto filter to header
            $sheet->setAutoFilter('A' . $headerStartRow . ':' . $sheet->getHighestColumn() . $headerStartRow);

            $filename = 'VLAN_Report_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Error exporting VLAN data to Excel: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                  ->setJSON(['error' => true, 'message' => 'Could not export data to Excel: ' . $e->getMessage()]);
        }
    }
}