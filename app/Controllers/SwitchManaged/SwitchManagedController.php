<?php

namespace App\Controllers\SwitchManaged;

use App\Controllers\BaseController;
use App\Models\switchmanaged\SwitchManagedModel;
use CodeIgniter\I18n\Time;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SwitchManagedController extends BaseController

{
    protected $SwitchManagedModel;
    protected $db;
    protected $dbCommon; // For tbmst_employee and tbua_useraccess

    public function __construct()
    {
        $this->SwitchManagedModel = new SwitchManagedModel();
        $this->db = \Config\Database::connect('jinsystem'); // Your main database connection
        $this->dbCommon = \Config\Database::connect('jincommon'); // Common database for user info
        helper('session');
    }

    // Helper function to calculate age
    private function calculateAge(?string $receivedDateValue): ?float
    {
        if (empty($receivedDateValue)) {
            return null;
        }

        try {
            $receiveDate = new Time($receivedDateValue);
            $today = Time::now();

            $start = new \DateTime($receiveDate->toDateString());
            $end = new \DateTime($today->toDateString());
            $interval = $start->diff($end);

            $totalMonths = ($interval->y * 12) + $interval->m;
            $totalMonths += $interval->d / (int)date('t', $end->getTimestamp());

            return round($totalMonths / 12, 1);
        } catch (\Exception $e) {
            log_message('error', 'Error calculating age: ' . $e->getMessage());
            return null;
        }
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $usermenu = session()->get("usermenu");
        $activeMenuGroup = 'Master';
        $activeMenuName = 'Switch Managed';

        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstSwitchManaged") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }

        $data["active_menu_group"] = $activeMenuGroup;
        $data["active_menu_name"] = $activeMenuName;

        return view('master/SwitchManaged/index', $data);
    }

    public function getDataSwitchManaged()
    {
        try {
            $query = $this->db->table('public.tbmst_switch_managed')
                               ->select('sm_id AS id, sm_id_switch AS id_switch, sm_asset_no AS asset_no, sm_asset_name AS asset_name,
                                         sm_received_date AS received_date, sm_age AS age, sm_ip AS ip, sm_location AS location,
                                         sm_lastupdate AS last_update, sm_lastuser AS last_user_id_from_db')
                               ->where('sm_status', 1) // BARIS INI HARUS ADA
                               ->orderBy('sm_lastupdate', 'DESC');

            $data = $query->get()->getResultArray();

            $dbCommon = \Config\Database::connect('jincommon');

            $userIds = array_filter(array_unique(array_column($data, 'last_user_id_from_db')), 'is_numeric');

            $employeeNames = [];
            $userAccessNames = [];

            if (!empty($userIds)) {
                $employeeQuery = $dbCommon->table('tbmst_employee')
                                         ->select('em_emplname, em_emplcode')
                                         ->whereIn('em_emplcode', $userIds)
                                         ->get();
                foreach ($employeeQuery->getResultArray() as $emp) {
                    $employeeNames[$emp['em_emplcode']] = $emp['em_emplname'];
                }

                $userAccessIds = array_diff($userIds, array_keys($employeeNames));
                if (!empty($userAccessIds)) {
                    $userAccessQuery = $dbCommon->table('tbua_useraccess')
                                                 ->select('ua_username, ua_userid')
                                                 ->whereIn('ua_userid', $userAccessIds)
                                                 ->get();
                    foreach ($userAccessQuery->getResultArray() as $user) {
                        $userAccessNames[$user['ua_userid']] = $user['ua_username'];
                    }
                }
            }

            foreach ($data as &$row) {
                $userIdFromDb = $row['last_user_id_from_db'];
                if (is_numeric($userIdFromDb)) {
                    $row['last_user'] = $employeeNames[$userIdFromDb] ?? ($userAccessNames[$userIdFromDb] ?? $userIdFromDb);
                } else {
                    $row['last_user'] = $userIdFromDb ?? 'N/A';
                }
                unset($row['last_user_id_from_db']);

                $row['age'] = $this->calculateAge($row['received_date']);
            }

            if (empty($data)) {
                return $this->response->setJSON([]);
            }

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching Switch Managed data: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['error' => true, 'message' => 'Could not retrieve data: ' . $e->getMessage()]);
        }
    }

    public function add()
    {
        $post = $this->request->getPost();

        $inputValues = array_filter($post, function($value, $key) {
            return !in_array($key, ['asset_no_sourced_from_finder', 'age']);
        }, ARRAY_FILTER_USE_BOTH);

        $filledInputCount = 0;
        foreach ($inputValues as $value) {
            if (is_string($value) && trim($value) !== '') {
                $filledInputCount++;
            } elseif (!is_string($value) && !empty($value)) {
                $filledInputCount++;
            }
        }

        if ($filledInputCount === 0) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => ['general' => 'Minimal satu kolom harus diisi.'],
                'error' => 'Minimal satu kolom harus diisi.'
            ]);
        }

        $idSwitch = !empty($post['id_switch']) ? (int)trim($post['id_switch']) : null;
        $assetNo = !empty($post['asset_no']) ? strtoupper(trim($post['asset_no'])) : null;
        $assetName = !empty($post['asset_name']) ? strtoupper(trim($post['asset_name'])) : null;
        $receivedDateValue = !empty($post['received_date']) ? $post['received_date'] : null;
        $ip = !empty($post['ip']) ? trim($post['ip']) : null;
        $location = !empty($post['location']) ? $post['location'] : null;

        $age = $this->calculateAge($receivedDateValue);

        $data = [
            'sm_id_switch'    => $idSwitch,
            'sm_asset_no'     => $assetNo,
            'sm_asset_name'   => $assetName,
            'sm_received_date' => $receivedDateValue,
            'sm_age'           => $age,
            'sm_ip'           => $ip,
            'sm_location'     => $location,
            // sm_lastuser will be set by the model's beforeInsert callback
        ];

        $rules = [
            'id_switch'    => 'permit_empty|integer',
            'asset_no'     => 'permit_empty|alpha_numeric_punct',
            'asset_name'   => 'permit_empty|alpha_numeric_punct',
            'received_date' => 'permit_empty|valid_date',
            'ip'           => 'permit_empty|valid_ip',
            'location'     => 'permit_empty|alpha_numeric_punct',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        try {
            $duplicateCheck = $this->SwitchManagedModel->checkDuplicate(
                $idSwitch,
                $assetNo
            );

            if ($duplicateCheck['duplicate_id_switch']) {
                return $this->response->setJSON(['status' => false, 'error' => 'ID Switch sudah ada.']);
            }
            if ($duplicateCheck['duplicate_asset_no']) {
                return $this->response->setJSON(['status' => false, 'error' => 'Asset Number sudah ada.']);
            }

            $insertID = $this->SwitchManagedModel->insert($data);
            if ($insertID === false) {
                return $this->response->setJSON(['status' => false, 'error' => 'Gagal menambahkan Switch Managed. Data mungkin tidak valid.']);
            }
            return $this->response->setJSON(['status' => true, 'message' => 'Record added successfully.', 'id' => $insertID]);
        } catch (\Exception $e) {
            log_message('error', 'Error adding Switch Managed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['status' => false, 'error' => 'Terjadi kesalahan server saat menambahkan Switch Managed: ' . $e->getMessage()]);
        }
    }

    public function edit()
    {
        $id = $this->request->getPost('id');
        $row = $this->SwitchManagedModel->find($id);

        if (!$row) {
            return $this->response->setStatusCode(404)
                                 ->setJSON(['status' => false, 'message' => 'Data not found']);
        }

        $receivedDateDisplay = $row['sm_received_date'];
        $ageDisplay = $this->calculateAge($receivedDateDisplay);


        $lastUserDisplayName = 'N/A';
        if (!empty($row['sm_lastuser'])) {
            $userIdFromDb = $row['sm_lastuser'];
            $userFromEmployee = $this->dbCommon->table('tbmst_employee')
                                                ->select('em_emplname')
                                                ->where('em_emplcode', $userIdFromDb)
                                                ->get()
                                                ->getRowArray();
            if ($userFromEmployee) {
                $lastUserDisplayName = $userFromEmployee['em_emplname'];
            } else {
                $userFromUserAccess = $this->dbCommon->table('tbua_useraccess')
                                                     ->select('ua_username')
                                                     ->where('ua_userid', $userIdFromDb)
                                                     ->get()
                                                     ->getRowArray();
                if ($userFromUserAccess) {
                    $lastUserDisplayName = $userFromUserAccess['ua_username'];
                } else {
                    $lastUserDisplayName = $userIdFromDb;
                }
            }
        }
        $row['sm_lastuser_display'] = $lastUserDisplayName;

        $data = [
            'id'             => $row['sm_id'],
            'id_switch'      => $row['sm_id_switch'],
            'asset_no'       => $row['sm_asset_no'],
            'asset_name'     => $row['sm_asset_name'],
            'received_date'  => $receivedDateDisplay,
            'age'            => $ageDisplay,
            'ip'             => $row['sm_ip'],
            'location'       => $row['sm_location'],
            'last_update'    => $row['sm_lastupdate'],
            'last_user'      => $row['sm_lastuser_display'],
        ];

        $isFinderSourced = $this->isAssetFromFinder(
            $row['sm_asset_no'] ?? '',
            $row['sm_asset_name'] ?? '',
            $row['sm_received_date'] ?? ''
        );
        $data['asset_no_sourced_from_finder'] = $isFinderSourced ? 1 : 0;

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    /**
     * Memeriksa apakah data Switch Managed cocok persis dengan entri di m_itequipment
     * berdasarkan Asset Number, Equipment Name, dan Receive Date.
     */
    private function isAssetFromFinder(string $assetNoFromSwitchManaged, string $assetNameFromSwitchManaged, string $receiveDateFromSwitchManaged): bool
    {
        $builder = $this->db->table('public.m_itequipment');

        $assetNoFromSwitchManaged = strtoupper(trim($assetNoFromSwitchManaged));
        $assetNameFromSwitchManaged = strtoupper(trim($assetNameFromSwitchManaged));
        $receiveDateFromSwitchManaged = $receiveDateFromSwitchManaged ? substr($receiveDateFromSwitchManaged, 0, 10) : '';

        $builder->where('UPPER(TRIM(e_assetno))', $assetNoFromSwitchManaged);
        $builder->where('UPPER(TRIM(e_equipmentname))', $assetNameFromSwitchManaged);

        if (empty($receiveDateFromSwitchManaged)) {
            $builder->groupStart()
                        ->where('e_receivedate IS NULL')
                        ->orWhere('TRIM(CAST(e_receivedate AS TEXT)) = \'\'')
                    ->groupEnd();
        } else {
            $builder->where("TO_CHAR(e_receivedate, 'YYYY-MM-DD')", $receiveDateFromSwitchManaged);
        }

        return (bool)$builder->countAllResults();
    }

    public function update()
    {
        $post = $this->request->getPost();
        $sm_id = $post['id'];

        $inputValues = array_filter($post, function($value, $key) {
            return !in_array($key, ['id', 'asset_no_sourced_from_finder']);
        }, ARRAY_FILTER_USE_BOTH);

        $filledInputCount = 0;
        foreach ($inputValues as $value) {
            if (is_string($value) && trim($value) !== '') {
                $filledInputCount++;
            } elseif (!is_string($value) && !empty($value)) {
                $filledInputCount++;
            }
        }

        if ($filledInputCount === 0) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => ['general' => 'Minimal satu kolom harus diisi.'],
                'error' => 'Minimal satu kolom harus diisi.'
            ]);
        }

        $idSwitch = !empty($post['id_switch']) ? (int)trim($post['id_switch']) : null;
        $assetNo = !empty($post['asset_no']) ? strtoupper(trim($post['asset_no'])) : null;
        $assetName = !empty($post['asset_name']) ? strtoupper(trim($post['asset_name'])) : null;
        $receivedDateValue = !empty($post['received_date']) ? $post['received_date'] : null;
        $ip = !empty($post['ip']) ? trim($post['ip']) : null;
        $location = !empty($post['location']) ? $post['location'] : null;

        $age = $this->calculateAge($receivedDateValue);

        $data = [
            'sm_id_switch'    => $idSwitch,
            'sm_asset_no'     => $assetNo,
            'sm_asset_name'   => $assetName,
            'sm_received_date' => $receivedDateValue,
            'sm_age'           => $age,
            'sm_ip'           => $ip,
            'sm_location'     => $location,
            // sm_lastuser will be set by the model's beforeUpdate callback
        ];

        $rules = [
            'id_switch'    => 'permit_empty|integer',
            'asset_no'     => 'permit_empty|alpha_numeric_punct',
            'asset_name'   => 'permit_empty|alpha_numeric_punct',
            'received_date' => 'permit_empty|valid_date',
            'ip'           => 'permit_empty|valid_ip',
            'location'     => 'permit_empty|alpha_numeric_punct',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        try {
            $duplicateCheck = $this->SwitchManagedModel->checkDuplicate(
                $idSwitch,
                $assetNo,
                $sm_id
            );

            if ($duplicateCheck['duplicate_id_switch']) {
                return $this->response->setJSON(['status' => false, 'error' => 'ID Switch sudah ada.']);
            }
            if ($duplicateCheck['duplicate_asset_no']) {
                return $this->response->setJSON(['status' => false, 'error' => 'Asset Number sudah ada.']);
            }

            $this->SwitchManagedModel->update($sm_id, $data);
            return $this->response->setJSON(['status' => true, 'message' => 'Record updated successfully.']);
        } catch (\Exception $e) {
            log_message('error', 'Error updating Switch Managed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['status' => false, 'error' => 'Terjadi kesalahan server saat memperbarui Switch Managed: ' . $e->getMessage()]);
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id'); // Ini adalah sm_id dari tabel utama
        try {
            // Fetch the main record using sm_id
            $mainRecord = $this->SwitchManagedModel->find($id);

            if ($mainRecord) {
                // Pastikan Anda meneruskan sm_id (primary key) ke fungsi delete detail
                // YANG ADA DI TABEL UTAMA sm_id
                $this->SwitchManagedModel->deleteSwitchDetailPortsByHeaderId($mainRecord['sm_id']);
            }

            // Lanjutkan dengan menghapus record utama
            $this->SwitchManagedModel->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Record deleted successfully.']);
        } catch (\Exception $e) {
            log_message('error', 'Delete Switch Managed error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function checkDuplicate()
    {
        $post = $this->request->getPost();
        $id = isset($post['id']) ? (int)$post['id'] : null;
        $idSwitch = isset($post['id_switch']) ? (int)trim($post['id_switch']) : null;
        $assetNo = isset($post['asset_no']) ? trim($post['asset_no']) : null;

        $duplicateResult = $this->SwitchManagedModel->checkDuplicate($idSwitch, $assetNo, $id);

        return $this->response->setJSON([
            'existIdSwitch' => $duplicateResult['duplicate_id_switch'],
            'existAssetNo' => $duplicateResult['duplicate_asset_no']
        ]);
    }

    public function getEquipmentData()
    {
        try {
            $search = $this->request->getGet('search');
            $data = $this->SwitchManagedModel->getEquipmentDataForFinder($search);

            if (empty($data)) {
                return $this->response->setJSON([]);
            }

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching Equipment data for Switch Managed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['error' => true, 'message' => 'Could not retrieve Equipment data: ' . $e->getMessage()]);
        }
    }

    /**
     * Export all switch managed data and their port details to Excel.
     */
    public function exportExcel()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        try {
            // Fetch all main switch records
            $mainSwitchesQuery = $this->db->table('public.tbmst_switch_managed')
                                         ->select('sm_id, sm_id_switch, sm_asset_no, sm_asset_name,
                                                   sm_received_date, sm_age, sm_ip, sm_location') // Menghapus sm_lastupdate, sm_lastuser
                                         ->orderBy('sm_id_switch', 'ASC') // Order by ID Switch for better grouping
                                         ->get();
            $mainSwitches = $mainSwitchesQuery->getResultArray();

            // Prepare user and VLAN data for lookup
            // Bagian ini dihapus karena kolom 'Last User' tidak lagi disertakan dalam Excel
            // $dbCommon = \Config\Database::connect('jincommon');
            // $allUserIds = [];
            // foreach ($mainSwitches as $switch) {
            //     if (is_numeric($switch['sm_lastuser'])) {
            //         $allUserIds[] = $switch['sm_lastuser'];
            //     }
            // }
            // $employeeNames = [];
            // $userAccessNames = [];
            // if (!empty($allUserIds)) {
            //     $employeeQuery = $dbCommon->table('tbmst_employee')
            //                              ->select('em_emplname, em_emplcode')
            //                              ->whereIn('em_emplcode', array_unique($allUserIds))
            //                              ->get();
            //     foreach ($employeeQuery->getResultArray() as $emp) {
            //         $employeeNames[$emp['em_emplcode']] = $emp['em_emplname'];
            //     }
            //     $userAccessIdsToFetch = array_diff(array_unique($allUserIds), array_keys($employeeNames));
            //     if (!empty($userAccessIdsToFetch)) {
            //         $userAccessQuery = $dbCommon->table('tbua_useraccess')
            //                                      ->select('ua_username, ua_userid')
            //                                      ->whereIn('ua_userid', $userAccessIdsToFetch)
            //                                      ->get();
            //         foreach ($userAccessQuery->getResultArray() as $user) {
            //             $userAccessNames[$user['ua_userid']] = $user['ua_username'];
            //         }
            //     }
            // }

            // Data VLAN masih dibutuhkan untuk kolom 'VLAN Name'
            $vlanData = $this->db->table('public.tbmst_vlan')
                                 ->select('tv_id_vlan, tv_name')
                                 ->get()
                                 ->getResultArray();
            $vlanNames = array_column($vlanData, 'tv_name', 'tv_id_vlan');


            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Switch Managed');

            // Header for the entire report
            $sheet->setCellValue('A1', 'SWITCH MANAGED REPORT');
            $sheet->mergeCells('A1:O1'); // Disesuaikan: 9 kolom utama + 6 kolom detail = 15 kolom total (A-O)
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 18],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Empty row for spacing
            $sheet->getRowDimension(2)->setRowHeight(15);


            // Set main header row
            $mainHeaders = [
                'No.', 'ID', 'ID Switch', 'Asset No', 'Asset Name', 'Received Date', 'Age (Years)',
                'IP', 'Location', // Menghapus 'Last Update (Main)', 'Last User (Main)'
                'No. (Port)', 'Port', 'Type', 'VLAN ID', 'VLAN Name', 'Status' // Menghapus 'Last Update (Detail)', 'Last User (Detail)'
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
            $sheet->getStyle('A' . $headerStartRow . ':O' . $headerStartRow)->applyFromArray($headerStyle); // Disesuaikan rentang ke 'O'


            // Define alternating row styles (base styles)
            $styleEvenRow = [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFF0F0F0']], // Very light gray
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
            ];

            $styleOddRow = [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFFFFFFF']], // White
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
            ];

            // Define column colors for differentiation
            $mainColumnFill = ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFE8F5E9']]; // Light Green for main data
            $detailColumnFill = ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFDAE5FF']]; // Very Light Blue for detail data
            $statusInactiveFill = ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFFFD9B3']]; // Light orange for Inactive status


            $rowNum = $headerStartRow + 1; // Start data from row after headers
            $mainSwitchNo = 1;

            foreach ($mainSwitches as $mainSwitch) {
                // Baris ini dihapus karena sm_lastuser tidak lagi diambil dari database
                // $mainSwitchUserId = $mainSwitch['sm_lastuser'];
                // $mainSwitch['last_user_display'] = $employeeNames[$mainSwitchUserId] ?? ($userAccessNames[$mainSwitchUserId] ?? $mainSwitchUserId);

                // Format dates (hanya sm_received_date yang relevan)
                $mainSwitch['sm_received_date'] = $mainSwitch['sm_received_date'] ? (new Time($mainSwitch['sm_received_date']))->toDateString() : '';
                // Baris ini dihapus karena sm_lastupdate tidak lagi diambil dari database
                // $mainSwitch['sm_lastupdate'] = $mainSwitch['sm_lastupdate'] ? (new Time($mainSwitch['sm_lastupdate']))->toDateTimeString() : '';

                // Fetch detail ports for the current main switch
                $detailPortsQuery = $this->db->table('public.tbmst_switch_managed_detail')
                                             ->select('smd_id, smd_port, smd_type, smd_vlan_id, smd_vlan_name, smd_status') // Menghapus smd_lastupdate, smd_lastuser
                                             ->where('smd_header_id_switch', $mainSwitch['sm_id'])
                                             ->orderBy('smd_port', 'ASC')
                                             ->get();
                $detailPorts = $detailPortsQuery->getResultArray();

                $numDetails = count($detailPorts);
                $startMergeRow = $rowNum;

                if ($numDetails === 0) {
                    // Case: No details for this main switch
                    $rowData = [
                        $mainSwitchNo,
                        $mainSwitch['sm_id'],
                        $mainSwitch['sm_id_switch'],
                        $mainSwitch['sm_asset_no'],
                        $mainSwitch['sm_asset_name'],
                        $mainSwitch['sm_received_date'],
                        $this->calculateAge($mainSwitch['sm_received_date']),
                        $mainSwitch['sm_ip'],
                        $mainSwitch['sm_location'],
                        // Menghapus 'Last Update (Main)' dan 'Last User (Main)'
                        '', '', '', '', '', '', // Kolom kosong untuk data detail (sekarang 6 kolom)
                    ];
                    $sheet->fromArray($rowData, NULL, 'A' . $rowNum);

                    // Apply alternating row style
                    $styleToApply = ($rowNum % 2 === 0) ? $styleEvenRow : $styleOddRow;
                    $sheet->getStyle('A' . $rowNum . ':O' . $rowNum)->applyFromArray($styleToApply); // Disesuaikan rentang ke 'O'

                    // Apply specific column background colors
                    $sheet->getStyle('A' . $rowNum . ':I' . $rowNum)->applyFromArray(['fill' => $mainColumnFill]); // Disesuaikan ke 'I'
                    $sheet->getStyle('J' . $rowNum . ':O' . $rowNum)->applyFromArray(['fill' => $detailColumnFill]); // Disesuaikan ke 'J-O'

                    $rowNum++;
                } else {
                    $detailNo = 1;
                    foreach ($detailPorts as $detail) {
                        // Baris ini dihapus karena smd_lastuser tidak lagi diambil dari database
                        // $detailUserId = $detail['smd_lastuser'];
                        // $detail['last_user_display'] = $employeeNames[$detailUserId] ?? ($userAccessNames[$detailUserId] ?? $detailUserId);

                        // Resolve VLAN Name from lookup if smd_vlan_name is empty or null
                        $resolvedVlanName = $detail['smd_vlan_name'];
                        if (empty($resolvedVlanName) && !empty($detail['smd_vlan_id'])) {
                            $resolvedVlanName = $vlanNames[$detail['smd_vlan_id']] ?? '';
                        }
                        $detail['resolved_vlan_name'] = strtoupper($resolvedVlanName);

                        // Baris ini dihapus karena smd_lastupdate tidak lagi diambil dari database
                        // $detail['smd_lastupdate'] = $detail['smd_lastupdate'] ? (new Time($detail['smd_lastupdate']))->toDateTimeString() : '';


                        $rowData = [
                            $mainSwitchNo, // Nomor switch utama (akan digabung)
                            $mainSwitch['sm_id'],
                            $mainSwitch['sm_id_switch'],
                            $mainSwitch['sm_asset_no'],
                            $mainSwitch['sm_asset_name'],
                            $mainSwitch['sm_received_date'],
                            $this->calculateAge($mainSwitch['sm_received_date']),
                            $mainSwitch['sm_ip'],
                            $mainSwitch['sm_location'],
                            // Menghapus 'Last Update (Main)' dan 'Last User (Main)'
                            // Kolom Detail
                            $detailNo++, // Ini menjadi 'No. (Port)'
                            $detail['smd_port'],
                            $detail['smd_type'],
                            $detail['smd_vlan_id'],
                            $detail['resolved_vlan_name'],
                            $detail['smd_status'] == 1 ? 'Active' : 'Inactive',
                            // Menghapus smd_lastupdate, smd_lastuser
                        ];
                        $sheet->fromArray($rowData, NULL, 'A' . $rowNum);

                        // Apply alternating row style
                        $styleToApply = ($rowNum % 2 === 0) ? $styleEvenRow : $styleOddRow;
                        $sheet->getStyle('A' . $rowNum . ':O' . $rowNum)->applyFromArray($styleToApply); // Disesuaikan rentang ke 'O'

                        // Apply specific column background colors
                        $sheet->getStyle('A' . $rowNum . ':I' . $rowNum)->applyFromArray(['fill' => $mainColumnFill]); // Disesuaikan ke 'I'
                        $sheet->getStyle('J' . $rowNum . ':O' . $rowNum)->applyFromArray(['fill' => $detailColumnFill]); // Disesuaikan ke 'J-O'

                        // Apply special style for 'Inactive' status in detail
                        if ($detail['smd_status'] == 0) {
                            $sheet->getStyle('O' . $rowNum)->applyFromArray($statusInactiveFill); // Disesuaikan ke 'O'
                        }

                        $rowNum++;
                    }

                    // Merge cells for main switch data across its detail rows
                    $endMergeRow = $rowNum - 1;
                    $mergeColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I']; // Kolom A sampai I untuk data utama
                    foreach ($mergeColumns as $col) {
                        $sheet->mergeCells($col . $startMergeRow . ':' . $col . $endMergeRow);
                        $sheet->getStyle($col . $startMergeRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        // Re-apply original cell styles to merged cells to maintain borders and background
                        $sheet->getStyle($col . $startMergeRow)->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]]);
                        $sheet->getStyle($col . $startMergeRow)->applyFromArray(['fill' => $mainColumnFill]);
                    }
                }
                $mainSwitchNo++;
            }

            // Set column widths
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Add auto filter to header
            $sheet->setAutoFilter('A' . $headerStartRow . ':' . $sheet->getHighestColumn() . $headerStartRow);

            $filename = 'Switch_Managed_Report_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Error exporting Switch Managed data to Excel: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['error' => true, 'message' => 'Could not export data to Excel: ' . $e->getMessage()]);
        }
    }

    // --- NEW: Methods for Switch Managed Detail ---

    /**
     * Get detail ports for a specific switch (by sm_id_switch).
     */
    public function getSwitchDetailPorts($sm_id_switch)
    {
        if (empty($sm_id_switch) || !is_numeric($sm_id_switch) || (int)$sm_id_switch <= 0) {
            log_message('error', 'Invalid sm_id_switch provided to getSwitchDetailPorts: ' . $sm_id_switch);
            return $this->response->setJSON([]);
        }

        try {
            $data = $this->SwitchManagedModel->getSwitchDetailPortsByHeaderId((int)$sm_id_switch);

            // Populate last_user display names
            $userIds = array_filter(array_unique(array_column($data, 'smd_lastuser')), 'is_numeric');
            $employeeNames = [];
            $userAccessNames = [];
            if (!empty($userIds)) {
                $employeeQuery = $this->dbCommon->table('tbmst_employee')
                                                 ->select('em_emplname, em_emplcode')
                                                 ->whereIn('em_emplcode', $userIds)
                                                 ->get();
                foreach ($employeeQuery->getResultArray() as $emp) {
                    $employeeNames[$emp['em_emplcode']] = $emp['em_emplname'];
                }
                $userAccessIds = array_diff($userIds, array_keys($employeeNames));
                if (!empty($userAccessIds)) {
                    $userAccessQuery = $this->dbCommon->table('tbua_useraccess')
                                                     ->select('ua_username, ua_userid')
                                                     ->whereIn('ua_userid', $userAccessIds)
                                                     ->get();
                    foreach ($userAccessQuery->getResultArray() as $user) {
                        $userAccessNames[$user['ua_userid']] = $user['ua_username'];
                    }
                }
            }

            foreach ($data as &$row) {
                $userIdFromDb = $row['smd_lastuser'];
                if (is_numeric($userIdFromDb)) {
                    $row['last_user_display'] = $employeeNames[$userIdFromDb] ?? ($userAccessNames[$userIdFromDb] ?? $userIdFromDb);
                } else {
                    $row['last_user_display'] = $userIdFromDb ?? 'N/A';
                }

                // Resolve VLAN Name from lookup if smd_vlan_name is empty or null
                $resolvedVlanName = $row['smd_vlan_name'];
                if (empty($resolvedVlanName) && !empty($row['smd_vlan_id'])) {
                    $vlanData = $this->db->table('public.tbmst_vlan')
                                         ->select('tv_name')
                                         ->where('tv_id_vlan', $row['smd_vlan_id'])
                                         ->get()
                                         ->getRowArray();
                   if ($vlanData) {
                       $resolvedVlanName = $vlanData['tv_name'];
                   }
                }
                $row['smd_vlan_name'] = $resolvedVlanName; // Update with resolved name
            }

            if (empty($data)) {
                return $this->response->setJSON([]);
            }
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching switch detail ports for sm_id_switch ' . $sm_id_switch . ': ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => true, 'message' => 'Could not retrieve switch detail ports: ' . $e->getMessage()]);
        }
    }

    /**
     * Get count of detail ports for a specific switch (by sm_id_switch).
     */
    public function countSwitchDetailPorts($sm_id_switch)
    {
        try {
            $count = $this->SwitchManagedModel->countSwitchDetailPortsByHeaderId((int)$sm_id_switch);
            // Optionally fetch other data from the main switch record if needed for display
            // $mainSwitchData = $this->SwitchManagedModel->select('sm_asset_name')->find($sm_id_switch);

            return $this->response->setJSON(['status' => true, 'count' => $count]);
        } catch (\Exception $e) {
            log_message('error', 'Error counting switch detail ports: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => false, 'error' => 'Could not count switch detail ports']);
        }
    }

    /**
     * Add a new port detail to tbmst_switch_managed_detail.
     */
    public function addSwitchDetailPort()
    {
        $post = $this->request->getPost();

        // Server-side validation rules for detail fields
        $rules = [
            'header_id_switch' => 'permit_empty|integer',
            'port'             => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[28]',
            'type'             => 'permit_empty|in_list[ethernet,SFP]',
            'vlan_id'          => 'permit_empty|integer',
            'vlan_name'        => 'permit_empty|string|max_length[250]',
            'status'           => 'permit_empty|integer|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $headerIdSwitch = (int) $post['header_id_switch'];
        $port = !empty($post['port']) ? (int) $post['port'] : null; // Gunakan null jika kosong
        $vlanId = !empty($post['vlan_id']) ? (int)$post['vlan_id'] : null;
        $vlanName = !empty($post['vlan_name']) ? trim($post['vlan_name']) : null;
        $type = !empty($post['type']) ? trim($post['type']) : null;
        $status = (int) ($post['status'] ?? 1); // Default to Active if not provided

        // --- NONAKTIFKAN SEMENTARA Pengecekan duplikasi Port per Switch ---
        // $isDuplicate = $this->SwitchManagedModel->checkDuplicateSwitchDetailPort($headerIdSwitch, $port);
        // if ($isDuplicate) {
        //     return $this->response->setJSON(['status' => false, 'errors' => ['port' => 'Port number ' . $port . ' already exists for this switch.']]);
        // }
        // --- AKHIR NONAKTIFKAN Pengecekan duplikasi ---

        $data = [
            'smd_header_id_switch' => $headerIdSwitch,
            'smd_port'             => $port,
            'smd_type'             => $type,
            'smd_vlan_id'          => $vlanId,
            'smd_vlan_name'        => $vlanName,
            'smd_status'           => $status,
            // 'smd_lastuser' and 'smd_lastupdate' will be set by the model callback
        ];

        try {
            $insertID = $this->SwitchManagedModel->addSwitchDetailPort($data);
            return $this->response->setJSON(['status' => true, 'message' => 'Detail port added successfully.', 'id' => $insertID]);
        } catch (\Exception $e) {
            log_message('error', 'Error adding switch detail port: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => false, 'error' => 'Failed to add detail port: ' . $e->getMessage()]);
        }
    }

    /**
     * Edit a specific port detail record.
     */
    public function editSwitchDetailPort()
    {
        $smd_id = $this->request->getPost('smd_id');
        $row = $this->SwitchManagedModel->getSwitchDetailPortById((int)$smd_id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Detail port not found']);
        }

        $lastUserDisplayName = 'N/A';
        if (!empty($row['smd_lastuser'])) {
            $userIdFromDb = $row['smd_lastuser'];
            $userFromEmployee = $this->dbCommon->table('tbmst_employee')
                                                ->select('em_emplname')
                                                ->where('em_emplcode', $userIdFromDb)
                                                ->get()
                                                ->getRowArray();
            if ($userFromEmployee) {
                $lastUserDisplayName = $userFromEmployee['em_emplname'];
            } else {
                $userFromUserAccess = $this->dbCommon->table('tbua_useraccess')
                                                     ->select('ua_username')
                                                     ->where('ua_userid', $userIdFromDb)
                                                     ->get()
                                                     ->getRowArray();
                if ($userFromUserAccess) {
                    $lastUserDisplayName = $userFromUserAccess['ua_username'];
                } else {
                    $lastUserDisplayName = $userIdFromDb;
                }
            }
        }
        $row['last_user_display'] = $lastUserDisplayName;

        // Populate VLAN Name if VLAN ID exists
        $vlanNameDisplay = $row['smd_vlan_name'] ?? '';
        if (empty($vlanNameDisplay) && !empty($row['smd_vlan_id'])) {
             $vlanData = $this->db->table('public.tbmst_vlan')
                                  ->select('tv_name')
                                  ->where('tv_id_vlan', $row['smd_vlan_id'])
                                  ->get()
                                  ->getRowArray();
             if ($vlanData) {
                 $vlanNameDisplay = $vlanData['tv_name'];
             }
        }
        $row['smd_vlan_name_display'] = $vlanNameDisplay;


        $response_data = [
            'smd_id'             => $row['smd_id'],
            'smd_header_id_switch' => $row['smd_header_id_switch'],
            'smd_port'           => $row['smd_port'],
            'smd_type'           => $row['smd_type'],
            'smd_vlan_id'        => $row['smd_vlan_id'],
            'smd_vlan_name'      => $row['smd_vlan_name_display'], // Use the resolved name
            'smd_status'         => $row['smd_status'],
            'smd_lastupdate'     => $row['smd_lastupdate'],
            'smd_lastuser'       => $row['last_user_display'],
        ];

        return $this->response->setJSON(['status' => true, 'data' => $response_data]);
    }

    /**
     * Update an existing port detail record.
     */
    public function updateSwitchDetailPort()
    {
        $post = $this->request->getPost();
        $smd_id = $post['smd_id'];

        // ... (aturan validasi lainnya) ...

        $headerIdSwitch = (int) $post['header_id_switch'];
        $port = !empty($post['port']) ? (int) $post['port'] : null; // Gunakan null jika kosong
        $vlanId = !empty($post['vlan_id']) ? (int)$post['vlan_id'] : null;
        $vlanName = !empty($post['vlan_name']) ? trim($post['vlan_name']) : null;
        $type = !empty($post['type']) ? trim($post['type']) : null;
        $status = (int) ($post['status'] ?? 1);

        // --- NONAKTIFKAN SEMENTARA Pengecekan duplikasi Port per Switch ---
        // $isDuplicate = $this->SwitchManagedModel->checkDuplicateSwitchDetailPort($headerIdSwitch, $port, (int)$smd_id);
        // if ($isDuplicate) {
        //     return $this->response->setJSON(['status' => false, 'errors' => ['port' => 'Port number ' . $port . ' already exists for this switch.']]);
        // }
        // --- AKHIR NONAKTIFKAN Pengecekan duplikasi ---

        $data = [
            'smd_header_id_switch' => $headerIdSwitch,
            'smd_port'             => $port,
            'smd_type'             => $type,
            'smd_vlan_id'          => $vlanId,
            'smd_vlan_name'        => $vlanName,
            'smd_status'           => $status,
            // 'smd_lastuser' and 'smd_lastupdate' will be set by the model callback
        ];

        try {
            $this->SwitchManagedModel->updateSwitchDetailPort($smd_id, $data);
            return $this->response->setJSON(['status' => true, 'message' => 'Detail port updated successfully.']);
        } catch (\Exception $e) {
            log_message('error', 'Error updating switch detail port: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => false, 'error' => 'Failed to update detail port: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a port detail record.
     */
    public function deleteSwitchDetailPort()
    {
        $smd_id = $this->request->getPost('smd_id');
        try {
            $this->SwitchManagedModel->deleteSwitchDetailPort((int)$smd_id);
            return $this->response->setJSON(['status' => true, 'message' => 'Detail port deleted successfully.']);
        } catch (\Exception $e) {
            log_message('error', 'Delete switch detail port error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get VLAN data from tbmst_vlan for search modal.
     */
    public function getVlanData()
    {
        try {
            $search = $this->request->getGet('search');
            $data = $this->SwitchManagedModel->getVlanDataForFinder($search);

            if (empty($data)) {
                return $this->response->setJSON([]);
            }

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching VLAN data: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => true, 'message' => 'Could not retrieve VLAN data: ' . $e->getMessage()]);
        }
    }

    /**
     * Export a single switch managed record and its port details to Excel.
     */
    public function exportExcelById(int $sm_id)
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        try {
            // Fetch main switch record by sm_id
            $mainSwitch = $this->db->table('public.tbmst_switch_managed')
                                   ->select('sm_id, sm_id_switch, sm_asset_no, sm_asset_name,
                                             sm_received_date, sm_age, sm_ip, sm_location') // Removed sm_lastupdate, sm_lastuser
                                   ->where('sm_id', $sm_id)
                                   ->get()
                                   ->getRowArray();

            if (!$mainSwitch) {
                return $this->response->setStatusCode(404)
                                     ->setJSON(['error' => true, 'message' => 'Switch Managed record not found for the given ID.']);
            }

            // Fetch detail ports for the current main switch
            // Fetch detail ports for the current main switch
            $detailPortsQuery = $this->db->table('public.tbmst_switch_managed_detail')
                ->select('smd_id, smd_header_id_switch, smd_port, smd_type, smd_vlan_id, smd_vlan_name, smd_status') // **Tambahkan smd_header_id_switch**
                ->where('smd_header_id_switch', $mainSwitch['sm_id'])
                ->orderBy('smd_port', 'ASC')
                ->get();
            $detailPorts = $detailPortsQuery->getResultArray();

            // Prepare VLAN data for lookup (only VLAN data is needed as user fields are removed)
            // Removed code for fetching employeeNames and userAccessNames as 'Last User' is no longer in the Excel
            $vlanData = $this->db->table('public.tbmst_vlan')
                                 ->select('tv_id_vlan, tv_name')
                                 ->get()
                                 ->getResultArray();
            $vlanNames = array_column($vlanData, 'tv_name', 'tv_id_vlan');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Switch Config - ID ' . $mainSwitch['sm_id_switch']);

            // Header for the entire report
            $sheet->setCellValue('A1', 'SWITCH MANAGED CONFIGURATION REPORT');
            $sheet->mergeCells('A1:H1'); // Adjusted range: A-H (8 columns for main data)
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 18],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Empty row for spacing
            $sheet->getRowDimension(2)->setRowHeight(15);

            // Main Switch Details Header
            $sheet->setCellValue('A3', 'Main Switch Details');
            $sheet->mergeCells('A3:H3'); // Adjusted range: A-H
            $sheet->getStyle('A3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8F5E9']], // Light Green
            ]);

            // Main Switch Data Headers
            $mainHeaders = [
                'ID', 'ID Switch', 'Asset No', 'Asset Name', 'Received Date', 'Age (Years)',
                'IP', 'Location' // Removed 'Last Update', 'Last User'
            ];
            $mainHeaderStartRow = 4;
            $sheet->fromArray($mainHeaders, NULL, 'A' . $mainHeaderStartRow);

            // Apply style to main header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCDE8F3']], // Light Blue
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
            ];
            $sheet->getStyle('A' . $mainHeaderStartRow . ':H' . $mainHeaderStartRow)->applyFromArray($headerStyle); // Adjusted range to 'H'

            // Populate main switch data
            // Removed logic to get and display 'last_user_display' and 'sm_lastupdate' as they are no longer selected
            $mainSwitch['sm_received_date'] = $mainSwitch['sm_received_date'] ? (new Time($mainSwitch['sm_received_date']))->toDateString() : '';

            $mainRowData = [
                $mainSwitch['sm_id'],
                $mainSwitch['sm_id_switch'],
                $mainSwitch['sm_asset_no'],
                $mainSwitch['sm_asset_name'],
                $mainSwitch['sm_received_date'],
                $this->calculateAge($mainSwitch['sm_received_date']),
                $mainSwitch['sm_ip'],
                $mainSwitch['sm_location'],
            ];
            $mainDataRowStart = $mainHeaderStartRow + 1;
            $sheet->fromArray($mainRowData, NULL, 'A' . $mainDataRowStart);
            $sheet->getStyle('A' . $mainDataRowStart . ':H' . $mainDataRowStart)->applyFromArray([ // Adjusted range to 'H'
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
            ]);


            // Empty row for spacing between main and detail
            $sheet->getRowDimension($mainDataRowStart + 1)->setRowHeight(15);


            // Port Details Header
            $portHeaderStartRow = $mainDataRowStart + 3;
            $sheet->setCellValue('A' . $portHeaderStartRow, 'Port Configurations');
            $sheet->mergeCells('A' . $portHeaderStartRow . ':G' . $portHeaderStartRow); // Adjusted range for port headers (A-G for port data)
            $sheet->getStyle('A' . $portHeaderStartRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDAE5FF']], // Very Light Blue
            ]);

            // Port Data Headers
            $portHeaders = [
                'No.', 'ID Detail', 'Header ID', 'Port', 'Type', 'VLAN ID', 'VLAN Name', 'Status' // Header ID ditambahkan
            ];
            $portDataHeaderRow = $portHeaderStartRow + 1;
            $sheet->fromArray($portHeaders, NULL, 'A' . $portDataHeaderRow);

            // Apply style to port header
            $sheet->getStyle('A' . $portDataHeaderRow . ':H' . $portDataHeaderRow)->applyFromArray($headerStyle); // Rentang disesuaikan ke 'H'

            $rowNum = $portDataHeaderRow + 1;
            $detailNo = 1;
            $statusInactiveFill = ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFFFD9B3']]; // Light orange for Inactive status


            if (empty($detailPorts)) {
                // If no ports, add a single row indicating no data
                $rowData = [
                    '', '', '', '', '', '', '', 'No Ports Configured' // Sekarang 8 kolom
                ];
                $sheet->fromArray($rowData, NULL, 'A' . $rowNum);
                $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray([ // Rentang disesuaikan ke 'H'
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
                    'font' => ['italic' => true, 'color' => ['argb' => 'FF808080']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->mergeCells('A' . $rowNum . ':H' . $rowNum); // Rentang disesuaikan ke 'H'
                $rowNum++;
            } else {
                foreach ($detailPorts as $detail) {
                    // Resolve VLAN Name from lookup if smd_vlan_name is empty or null
                    $resolvedVlanName = $detail['smd_vlan_name'];
                    if (empty($resolvedVlanName) && !empty($detail['smd_vlan_id'])) {
                        $resolvedVlanName = $vlanNames[$detail['smd_vlan_id']] ?? '';
                    }
                    $detail['resolved_vlan_name'] = strtoupper($resolvedVlanName);

                    $rowData = [
                        $detailNo++,
                        $detail['smd_id'],
                        $detail['smd_header_id_switch'], // Header ID ditambahkan di sini
                        $detail['smd_port'],
                        $detail['smd_type'],
                        $detail['smd_vlan_id'],
                        $detail['resolved_vlan_name'],
                        $detail['smd_status'] == 1 ? 'Active' : 'Inactive',
                    ];
                    $sheet->fromArray($rowData, NULL, 'A' . $rowNum);

                    $styleToApply = ($detailNo % 2 === 0) ? ['fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFF0F0F0']]] : ['fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFFFFFFF']]];
                    $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray($styleToApply); // Rentang disesuaikan ke 'H'
                    $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]]); // Rentang disesuaikan ke 'H'

                    // Apply special style for 'Inactive' status in detail
                    if ($detail['smd_status'] == 0) {
                        $sheet->getStyle('H' . $rowNum)->applyFromArray($statusInactiveFill); // Disesuaikan ke 'H'
                    }

                    $rowNum++;
                }
            }


            // Set column widths
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'Switch_Configuration_ID_' . $mainSwitch['sm_id_switch'] . '_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Error exporting Switch Managed data to Excel by ID: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['error' => true, 'message' => 'Could not export data to Excel: ' . $e->getMessage()]);
        }
    }
    
}