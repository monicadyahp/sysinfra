<?php

namespace App\Controllers\PCServer;

use App\Controllers\BaseController;
use App\Models\pcserver\PCServerModel;
use CodeIgniter\I18n\Time;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PCServerController extends BaseController
{
    protected $PCServerModel;
    protected $db;
    protected $dbCommon;

    public function __construct()
    {
        $this->PCServerModel = new PCServerModel();
        $this->db = \Config\Database::connect('jinsystem');
        $this->dbCommon = \Config\Database::connect('jincommon');
        helper('session');
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $usermenu = session()->get("usermenu");
        $activeMenuGroup = 'Master';
        $activeMenuName = 'PC Server';

        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstPCServer") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }

        $data["active_menu_group"] = $activeMenuGroup;
        $data["active_menu_name"] = $activeMenuName;

        return view('master/PCServer/index', $data);
    }

    public function getDataPCServer()
    {
        try {
            $statusFilter = $this->request->getGet('status');

            $query = $this->db->table('public.t_pcserver')
                             ->select('srv_id AS id, srv_asset_no AS asset_no, srv_asset_name AS asset_name,
                                     srv_location AS location, srv_receive_date AS receive_date, srv_age AS age,
                                     srv_hdd AS hdd, srv_ram AS ram, srv_vga AS vga, srv_ethernet AS ethernet,
                                     srv_remark AS remark, srv_status AS status,
                                     srv_lastupdate AS last_update, srv_lastuser AS last_user_id_from_db');

            if ($statusFilter !== null && $statusFilter !== 'All') {
                $query->where('srv_status', (int)$statusFilter);
            }

            $query->orderBy('srv_lastupdate', 'DESC');

            $data = $query->get()->getResultArray();

            $dbCommon = \Config\Database::connect('jincommon');

            $userIds = array_filter(array_unique(array_column($data, 'last_user_id_from_db')), 'is_numeric');

            $employeeNames = [];
            if (!empty($userIds)) {
                $employeeQuery = $dbCommon->table('tbmst_employee')
                                         ->select('em_emplname, em_emplcode')
                                         ->whereIn('em_emplcode', $userIds)
                                         ->get();
                foreach ($employeeQuery->getResultArray() as $emp) {
                    $employeeNames[$emp['em_emplcode']] = $emp['em_emplname'];
                }
            }

            $userAccessNames = [];
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

            foreach ($data as &$row) {
                if (is_numeric($row['last_user_id_from_db'])) {
                    $row['last_user'] = $employeeNames[$row['last_user_id_from_db']] ?? ($userAccessNames[$row['last_user_id_from_db']] ?? $row['last_user_id_from_db']);
                } else {
                    $row['last_user'] = $row['last_user_id_from_db'] ?? 'N/A';
                }
                unset($row['last_user_id_from_db']);

                if ($row['receive_date']) {
                    try {
                        $receiveDateObj = new Time($row['receive_date']);
                        $today = Time::now();

                        $start = new \DateTime($receiveDateObj->toDateString());
                        $end = new \DateTime($today->toDateString());
                        $interval = $start->diff($end);

                        $totalMonths = ($interval->y * 12) + $interval->m;
                        $totalMonths += $interval->d / (int)date('t', $end->getTimestamp());

                        $row['age'] = round($totalMonths / 12, 1);
                    } catch (\Exception $e) {
                        $row['age'] = null;
                        log_message('error', 'Error calculating age for row with receive_date ' . $row['receive_date'] . ': ' . $e->getMessage());
                    }
                } else {
                    $row['age'] = null;
                }
            }

            if (empty($data)) {
                return $this->response->setJSON([]);
            }

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching PC Server data: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['error' => true, 'message' => 'Could not retrieve data: ' . $e->getMessage()]);
        }
    }

    public function add()
    {
        $post = $this->request->getPost();

        $inputValues = array_filter($post, function($value, $key) {
            return !in_array($key, ['asset_no_sourced_from_finder', 'status']);
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

        $assetNo = !empty($post['asset_no']) ? strtoupper(trim($post['asset_no'])) : null;
        $assetName = !empty($post['asset_name']) ? strtoupper(trim($post['asset_name'])) : null;
        $location = !empty($post['location']) ? $post['location'] : null;
        $hdd = !empty($post['hdd']) ? strtoupper(trim($post['hdd'])) : null;
        $ram = !empty($post['ram']) ? strtoupper(trim($post['ram'])) : null;
        $vga = !empty($post['vga']) ? strtoupper(trim($post['vga'])) : null;
        $ethernet = !empty($post['ethernet']) ? strtoupper(trim($post['ethernet'])) : null;
        $remark = !empty($post['remark']) ? $post['remark'] : null;

        $receiveDateValue = !empty($post['receive_date']) ? $post['receive_date'] : null;
        $age = null;

        if ($receiveDateValue) {
            try {
                $receiveDate = new Time($receiveDateValue);
                $today = Time::now();

                $start = new \DateTime($receiveDate->toDateString());
                $end = new \DateTime($today->toDateString());
                $interval = $start->diff($end);

                $totalMonths = ($interval->y * 12) + $interval->m;
                $totalMonths += $interval->d / (int)date('t', $end->getTimestamp());

                $age = round($totalMonths / 12, 1);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => false,
                    'errors' => ['receive_date' => 'Format tanggal terima tidak valid.'],
                    'error' => 'Format tanggal terima tidak valid.'
                ]);
            }
        }

        $loggedInUserName = session()->get('user_name');
        $lastUser = $loggedInUserName ?? 'postgres';

        $data = [
            'srv_asset_no'       => $assetNo,
            'srv_asset_name'     => $assetName,
            'srv_location'       => $location,
            'srv_receive_date'   => $receiveDateValue,
            'srv_age'            => $age,
            'srv_hdd'            => $hdd,
            'srv_ram'            => $ram,
            'srv_vga'            => $vga,
            'srv_ethernet'       => $ethernet,
            'srv_remark'         => $remark,
            'srv_status'         => (int)($post['status'] ?? 0),
            'srv_lastuser'       => $lastUser,
        ];

        $rules = [
            'asset_no'       => 'permit_empty|alpha_numeric_punct',
            'receive_date'   => 'permit_empty|valid_date',
            'asset_name'     => 'permit_empty|alpha_numeric_punct',
            'location'       => 'permit_empty|alpha_numeric_punct',
            'hdd'            => 'permit_empty|alpha_numeric_punct',
            'ram'            => 'permit_empty|alpha_numeric_punct',
            'vga'            => 'permit_empty|alpha_numeric_punct',
            'ethernet'       => 'permit_empty|alpha_numeric_punct',
            'remark'         => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        try {
            $insertID = $this->PCServerModel->insert($data);
            if ($insertID === false) {
                return $this->response->setJSON(['status' => false, 'error' => 'Gagal menambahkan PC Server. Data mungkin tidak valid.']);
            }
            return $this->response->setJSON(['status' => true, 'message' => 'Record added successfully.', 'id' => $insertID]);
        } catch (\Exception $e) {
            log_message('error', 'Error adding PC Server: ' . $e->getMessage());
            if (strpos($e->getMessage(), 'duplicate key value violates unique constraint') !== false) {
                return $this->response->setJSON(['status' => false, 'error' => 'Asset Number sudah ada.']);
            }
            return $this->response->setStatusCode(500)
                                 ->setJSON(['status' => false, 'error' => 'Terjadi kesalahan server saat menambahkan PC Server: ' . $e->getMessage()]);
        }
    }


    public function edit()
    {
        $id = $this->request->getPost('id');
        $row = $this->PCServerModel->find($id);

        if (!$row) {
            return $this->response->setStatusCode(404)
                                 ->setJSON(['status' => false, 'message' => 'Data not found']);
        }

        $receiveDateDisplay = $row['srv_receive_date'];
        $ageDisplay = null;
        if ($receiveDateDisplay) {
            try {
                $receiveDateObj = new Time($receiveDateDisplay);
                $today = Time::now();

                $start = new \DateTime($receiveDateObj->toDateString());
                $end = new \DateTime($today->toDateString());
                $interval = $start->diff($end);

                $totalMonths = ($interval->y * 12) + $interval->m;
                $totalMonths += $interval->d / (int)date('t', $end->getTimestamp());

                $ageDisplay = round($totalMonths / 12, 1);
            } catch (\Exception $e) {
                $ageDisplay = null;
                log_message('error', 'Error calculating age for edit modal: ' . $e->getMessage());
            }
        }
        $row['srv_age'] = $ageDisplay;

        $lastUserDisplayName = 'N/A';
        if (!empty($row['srv_lastuser'])) {
            $lastUserValue = $row['srv_lastuser'];
            if (is_numeric($lastUserValue)) {
                $userFromEmployee = $this->dbCommon->table('tbmst_employee')
                                                   ->select('em_emplname')
                                                   ->where('em_emplcode', $lastUserValue)
                                                   ->get()
                                                   ->getRowArray();
                if ($userFromEmployee) {
                    $lastUserDisplayName = $userFromEmployee['em_emplname'];
                } else {
                    $userFromUserAccess = $this->dbCommon->table('tbua_useraccess')
                                                         ->select('ua_username')
                                                         ->where('ua_userid', $lastUserValue)
                                                         ->get()
                                                         ->getRowArray();
                    if ($userFromUserAccess) {
                        $lastUserDisplayName = $userFromUserAccess['ua_username'];
                    } else {
                        $lastUserDisplayName = $lastUserValue;
                    }
                }
            } else {
                $lastUserDisplayName = $lastUserValue;
            }
        }
        $row['srv_lastuser_display'] = $lastUserDisplayName;

        $data = [
            'id'             => $row['srv_id'],
            'asset_no'       => $row['srv_asset_no'],
            'asset_name'     => $row['srv_asset_name'],
            'location'       => $row['srv_location'],
            'receive_date'   => $row['srv_receive_date'],
            'age'            => $row['srv_age'],
            'hdd'            => $row['srv_hdd'],
            'ram'            => $row['srv_ram'],
            'vga'            => $row['srv_vga'],
            'ethernet'       => $row['srv_ethernet'],
            'remark'         => $row['srv_remark'],
            'status'         => $row['srv_status'],
            'last_update'    => $row['srv_lastupdate'],
            'last_user'      => $row['srv_lastuser_display'],
            // 'original_asset_no' => $row['srv_asset_no'] // Not needed here
        ];

        // --- START: MODIFIKASI UNTUK isFinderSourced ---
        // Panggil fungsi penentu isFinderSourced dengan semua data yang relevan
        $isFinderSourced = $this->isAssetFromFinder(
            $row['srv_asset_no'] ?? '',
            $row['srv_asset_name'] ?? '',
            $row['srv_receive_date'] ?? ''
        );
        $data['asset_no_sourced_from_finder'] = $isFinderSourced ? 1 : 0;
        // --- END: MODIFIKASI UNTUK isFinderSourced ---

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    /**
     * Memeriksa apakah data PC di t_pcserver cocok persis dengan entri di m_itequipment
     * berdasarkan Asset Number, Equipment Name, dan Receive Date.
     * Menggunakan e_equipmentname (Asset Name) dan e_receivedate (Receive Date).
     *
     * @param string $assetNoFromPCServer
     * @param string $assetNameFromPCServer
     * @param string $receiveDateFromPCServer (format YYYY-MM-DD HH:MM:SS atau YYYY-MM-DD)
     * @return bool
     */
    private function isAssetFromFinder(string $assetNoFromPCServer, string $assetNameFromPCServer, string $receiveDateFromPCServer): bool
    {
        $builder = $this->db->table('public.m_itequipment');

        // Normalisasi input untuk perbandingan yang konsisten
        $assetNoFromPCServer = strtoupper(trim($assetNoFromPCServer));
        $assetNameFromPCServer = strtoupper(trim($assetNameFromPCServer));
        // Receive date dari t_pcserver mungkin dalam format 'YYYY-MM-DD HH:MM:SS', ambil hanya tanggalnya
        $receiveDateFromPCServer = $receiveDateFromPCServer ? substr($receiveDateFromPCServer, 0, 10) : '';

        $builder->where('UPPER(TRIM(e_assetno))', $assetNoFromPCServer);
        $builder->where('UPPER(TRIM(e_equipmentname))', $assetNameFromPCServer);

        // --- Perbaikan di sini: Gunakan fungsi DATE_TRUNC atau TO_CHAR untuk kolom TIMESTAMP ---
        if (empty($receiveDateFromPCServer)) {
            $builder->groupStart()
                        ->where('e_receivedate IS NULL')
                        ->orWhere('TRIM(CAST(e_receivedate AS TEXT)) = \'\'') // If empty string can be stored
                    ->groupEnd();
        } else {
            // Gunakan TO_CHAR untuk mengonversi TIMESTAMP ke TEXT dengan format tanggal, lalu bandingkan
            $builder->where("TO_CHAR(e_receivedate, 'YYYY-MM-DD')", $receiveDateFromPCServer);
        }

        return (bool)$builder->countAllResults();
    }


    public function update()
    {
        $post = $this->request->getPost();
        $srv_id = $post['id'];

        $inputValues = array_filter($post, function($value, $key) {
            return !in_array($key, ['id', 'asset_no_sourced_from_finder', 'status']);
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

        $assetNo = !empty($post['asset_no']) ? strtoupper(trim($post['asset_no'])) : null;
        $assetName = !empty($post['asset_name']) ? strtoupper(trim($post['asset_name'])) : null;
        $location = !empty($post['location']) ? $post['location'] : null;
        $hdd = !empty($post['hdd']) ? strtoupper(trim($post['hdd'])) : null;
        $ram = !empty($post['ram']) ? strtoupper(trim($post['ram'])) : null;
        $vga = !empty($post['vga']) ? strtoupper(trim($post['vga'])) : null;
        $ethernet = !empty($post['ethernet']) ? strtoupper(trim($post['ethernet'])) : null;
        $remark = !empty($post['remark']) ? $post['remark'] : null;

        $receiveDateValue = !empty($post['receive_date']) ? $post['receive_date'] : null;
        $age = null;

        if ($receiveDateValue) {
            try {
                $receiveDate = new Time($receiveDateValue);
                $today = Time::now();

                $start = new \DateTime($receiveDate->toDateString());
                $end = new \DateTime($today->toDateString());
                $interval = $start->diff($end);

                $totalMonths = ($interval->y * 12) + $interval->m;
                $totalMonths += $interval->d / (int)date('t', $end->getTimestamp());

                $age = round($totalMonths / 12, 1);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => false,
                    'errors' => ['receive_date' => 'Format tanggal terima tidak valid.'],
                    'error' => 'Format tanggal terima tidak valid.'
                ]);
            }
        }

        $loggedInUserName = session()->get('user_name');
        $lastUser = $loggedInUserName ?? 'postgres';

        $data = [
            'srv_asset_no'       => $assetNo,
            'srv_asset_name'     => $assetName,
            'srv_location'       => $location,
            'srv_receive_date'   => $receiveDateValue,
            'srv_age'            => $age,
            'srv_hdd'            => $hdd,
            'srv_ram'            => $ram,
            'srv_vga'            => $vga,
            'srv_ethernet'       => $ethernet,
            'srv_remark'         => $remark,
            'srv_status'         => (int)($post['status'] ?? 0),
            'srv_lastuser'       => $lastUser,
        ];

        $rules = [
            'asset_no'       => 'permit_empty|alpha_numeric_punct',
            'receive_date'   => 'permit_empty|valid_date',
            'asset_name'     => 'permit_empty|alpha_numeric_punct',
            'location'       => 'permit_empty|alpha_numeric_punct',
            'hdd'            => 'permit_empty|alpha_numeric_punct',
            'ram'            => 'permit_empty|alpha_numeric_punct',
            'vga'            => 'permit_empty|alpha_numeric_punct',
            'ethernet'       => 'permit_empty|alpha_numeric_punct',
            'remark'         => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        try {
            $this->PCServerModel->update($srv_id, $data);
            return $this->response->setJSON(['status' => true, 'message' => 'Record updated successfully.']);
        } catch (\Exception $e) {
            log_message('error', 'Error updating PC Server: ' . $e->getMessage());
            if (strpos($e->getMessage(), 'duplicate key value violates unique constraint') !== false) {
                return $this->response->setJSON(['status' => false, 'error' => 'Asset Number sudah ada.']);
            }
            return $this->response->setStatusCode(500)
                                 ->setJSON(['status' => false, 'error' => 'Terjadi kesalahan server saat memperbarui PC Server: ' . $e->getMessage()]);
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        try {
            $this->PCServerModel->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Record deleted successfully.']);
        } catch (\Exception $e) {
            log_message('error', 'Delete PC Server error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function checkDuplicate()
    {
        $post = $this->request->getPost();
        $id = isset($post['id']) ? (int)$post['id'] : null;
        $assetNo = trim($post['asset_no']);

        $isDuplicate = $this->PCServerModel->checkDuplicateAssetNo($assetNo, $id);

        return $this->response->setJSON(['existAssetNo' => $isDuplicate]);
    }

    public function getEquipmentData()
    {
        try {
            $search = $this->request->getGet('search');
            $builder = $this->db->table('public.m_itequipment');

            $builder->select('e_id, e_assetno, e_equipmentname, e_equipmentid, e_serialnumber, e_brand, e_model, e_receivedate');

            if (!empty($search)) {
                $searchTerm = strtoupper(trim($search));
                $builder->groupStart()
                            ->like('UPPER(TRIM(e_assetno))', $searchTerm)
                            ->orLike('UPPER(TRIM(e_equipmentname))', $searchTerm)
                            ->orLike('UPPER(TRIM(e_serialnumber))', $searchTerm)
                            ->orLike('UPPER(TRIM(e_brand))', $searchTerm)
                            ->orLike('UPPER(TRIM(e_model))', $searchTerm)
                            ->orLike('CAST(e_equipmentid AS TEXT)', $searchTerm)
                            ->groupEnd();
            }

            $data = $builder->orderBy('e_assetno', 'ASC')->get()->getResultArray();

            if (empty($data)) {
                return $this->response->setJSON([]);
            }

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching Equipment data for PCServer: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['error' => true, 'message' => 'Could not retrieve Equipment data: ' . $e->getMessage()]);
        }
    }

    public function exportExcel()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        try {
            $query = $this->db->table('public.t_pcserver')
                             ->select('srv_id AS id, srv_asset_no AS asset_no, srv_asset_name AS asset_name,
                                     srv_location AS location, srv_receive_date AS receive_date, srv_age AS age,
                                     srv_hdd AS hdd, srv_ram AS ram, srv_vga AS vga, srv_ethernet AS ethernet,
                                     srv_remark AS remark, srv_status AS status,
                                     srv_lastupdate AS last_update, srv_lastuser AS last_user_id_from_db')
                             ->orderBy('srv_lastupdate', 'DESC')
                             ->get();
            $data = $query->getResultArray();

            $dbCommon = \Config\Database::connect('jincommon');

            $userIds = array_filter(array_unique(array_column($data, 'last_user_id_from_db')), 'is_numeric');

            $employeeNames = [];
            if (!empty($userIds)) {
                $employeeQuery = $dbCommon->table('tbmst_employee')
                                         ->select('em_emplname, em_emplcode')
                                         ->whereIn('em_emplcode', $userIds)
                                         ->get();
                foreach ($employeeQuery->getResultArray() as $emp) {
                    $employeeNames[$emp['em_emplcode']] = $emp['em_emplname'];
                }
            }

            $userAccessNames = [];
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

            foreach ($data as &$row) {
                if (is_numeric($row['last_user_id_from_db'])) {
                    $row['last_user'] = $employeeNames[$row['last_user_id_from_db']] ?? ($userAccessNames[$row['last_user_id_from_db']] ?? $row['last_user_id_from_db']);
                } else {
                    $row['last_user'] = $row['last_user_id_from_db'] ?? 'N/A';
                }
                unset($row['last_user_id_from_db']);

                if ($row['receive_date']) {
                    try {
                        $receiveDateObj = new Time($row['receive_date']);
                        $today = Time::now();
                        $start = new \DateTime($receiveDateObj->toDateString());
                        $end = new \DateTime($today->toDateString());
                        $interval = $start->diff($end);
                        $totalMonths = ($interval->y * 12) + $interval->m;
                        $totalMonths += $interval->d / (int)date('t', $end->getTimestamp());
                        $row['age'] = round($totalMonths / 12, 1);
                    } catch (\Exception $e) {
                        $row['age'] = null;
                        log_message('error', 'Error calculating age for export: ' . $e->getMessage());
                    }
                } else {
                    $row['age'] = null;
                }

                $row['status'] = $row['status'] == 1 ? 'Active' : 'Inactive';
                $row['receive_date'] = $row['receive_date'] ? (new Time($row['receive_date']))->toDateString() : '';
                $row['last_update'] = $row['last_update'] ? (new Time($row['last_update']))->toDateTimeString() : '';
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('B2', 'List PC Server');
            $sheet->mergeCells('B2:N2');
            $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $headers = [
                'No.', 'ID', 'Asset No', 'Asset Name', 'Location', 'Receive Date',
                'Age (Years)', 'HDD', 'RAM', 'VGA', 'Ethernet',
                'Remark', 'Status', 'Last Update', 'Last User'
            ];
            $sheet->fromArray($headers, NULL, 'A4');

            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FF000000'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'rotation' => 90,
                    'startColor' => ['argb' => 'FFCDE8F3'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle('A4:' . $sheet->getHighestColumn() . '4')->applyFromArray($headerStyle);


            $rowNum = 5;
            $no = 1;
            foreach ($data as $row) {
                $sheet->setCellValue('A' . $rowNum, $no++);
                $sheet->setCellValue('B' . $rowNum, $row['id']);
                $sheet->setCellValue('C' . $rowNum, $row['asset_no']);
                $sheet->setCellValue('D' . $rowNum, $row['asset_name']);
                $sheet->setCellValue('E' . $rowNum, $row['location']);
                $sheet->setCellValue('F' . $rowNum, $row['receive_date']);
                $sheet->setCellValue('G' . $rowNum, $row['age']);
                $sheet->setCellValue('H' . $rowNum, $row['hdd']);
                $sheet->setCellValue('I' . $rowNum, $row['ram']);
                $sheet->setCellValue('J' . $rowNum, $row['vga']);
                $sheet->setCellValue('K' . $rowNum, $row['ethernet']);
                $sheet->setCellValue('L' . $rowNum, $row['remark']);
                $sheet->setCellValue('M' . $rowNum, $row['status']);
                $sheet->setCellValue('N' . $rowNum, $row['last_update']);
                $sheet->setCellValue('O' . $rowNum, $row['last_user']);

                $sheet->getStyle('A' . $rowNum . ':' . $sheet->getHighestColumn() . $rowNum)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                $rowNum++;
            }

            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'PC_Server_Data_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Error exporting PC Server data to Excel: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['error' => true, 'message' => 'Could not export data to Excel: ' . $e->getMessage()]);
        }
    }
}