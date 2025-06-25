<?php

namespace App\Controllers\SoftwareLicense;

use App\Controllers\BaseController;
use App\Models\softwarelicense\SoftwareLicenseModel;
use CodeIgniter\I18n\Time;
use Config\Database;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SoftwareLicenseController extends BaseController
{
    protected $SoftwareLicenseModel;
    protected $db; // Untuk koneksi 'jinsystem'
    protected $dbCommon; // Tambahkan properti untuk koneksi 'jincommon'

    public function __construct()
    {
        $this->SoftwareLicenseModel = new SoftwareLicenseModel();
        $this->db = Database::connect('jinsystem'); // Koneksi ke 'jinsystem'
        $this->dbCommon = Database::connect('jincommon'); // Tambahkan koneksi ke 'jincommon'
        helper('session'); // Pastikan helper session dimuat
    }

    /** * Return next id dari sequence
     */
    public function getNextId()
    {
        // Tetap mengambil MAX(tl_id) karena ini adalah ID baru, tidak terkait dengan status
        $row = $this->db
            ->query("SELECT COALESCE(MAX(tl_id),0)+1 AS id FROM t_license")
            ->getRow();
        return $this->response->setJSON(['id' => $row->id]);
    }

    // Fungsi untuk menampilkan halaman utama
    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $usermenu = session()->get("usermenu");
        $activeMenuGroup = 'Transaction';
        $activeMenuName = 'Software License Transaction';

        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "SoftwareLicense") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }

        $data["active_menu_group"] = $activeMenuGroup;
        $data["active_menu_name"] = $activeMenuName;
        
        return view('master/SoftwareLicense/index', $data); // <--- UBAH KE PATH BARU INI
    }

    // Fungsi untuk mengambil data PO yang sudah di-join (tidak ada perubahan terkait status di sini)
    public function getPOData()
    {
        try {
            $query = $this->db->query("
                SELECT 
                    p.pos_potosupplierno AS po_number,
                    p.pos_suppliername AS license_partner,
                    p.pos_podate AS order_date,
                    d.posd_partname AS product_name,
                    d.posd_quantity AS product_qty
                FROM 
                    tb_mrp_potosup p
                LEFT JOIN 
                    tb_mrp_potosupdet d
                ON 
                    p.pos_potosupplierno = d.posd_potosupplierno
            ");
            
            $data = $query->getResultArray();
            
            if (empty($data)) {
                return $this->response->setJSON([]);
            }
            
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching PO data: ' . $e->getMessage());
            try {
                $query = $this->db->query("
                    SELECT 
                        p.pos_potosupplierno AS po_number,
                        p.pos_suppliername AS license_partner,
                        p.pos_podate AS order_date,
                        d.posd_partname AS product_name,
                        d.posd_quantity AS product_qty
                    FROM 
                        public.tb_mrp_potosup p
                    LEFT JOIN 
                        public.tb_mrp_potosupdet d
                    ON 
                        p.pos_potosupplierno = d.posd_potosupplierno
                ");
                $data = $query->getResultArray();
                return $this->response->setJSON($data);
            } catch (\Exception $e2) {
                log_message('error', 'Error fetching PO data (second attempt): ' . $e2->getMessage());
                return $this->response->setStatusCode(500)
                                     ->setJSON(['error' => true, 'message' => 'Could not retrieve data']);
            }
        }
    }

    // Fungsi untuk mengambil data dari t_license
    public function getDataSoftwareLicense()
    {
        try {
            $query = $this->db->query("
                SELECT 
                        tl_id             AS id,
                        tl_type           AS type,
                        tl_license_type_category AS license_category,
                        UPPER(tl_refnumber) AS ref_number,
                        tl_po_number      AS po_number,
                        UPPER(tl_licensepartner) AS license_partner,
                        tl_orderdate      AS order_date,
                        tl_startdate      AS start_date,
                        tl_enddate        AS end_date,
                        UPPER(tl_productname)     AS product_name,
                        tl_productqty     AS product_qty,
                        tl_productdesc    AS product_desc,
                        UPPER(tl_productkey) AS product_key,
                        tl_organization   AS organization,
                        tl_last_update    AS last_update,
                        tl_last_user      AS last_user
                    FROM t_license
                    WHERE tl_status = 1 -- TAMBAHKAN KONDISI INI: Hanya tampilkan data dengan status 1
                    ORDER BY tl_last_update DESC
                ");
            
            $data = $query->getResultArray();
            
            if (empty($data)) {
                return $this->response->setJSON([]);
            }
            
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching software license data: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['error' => true, 'message' => 'Could not retrieve data']);
        }
    }
    
    // --- START PERBAIKAN PADA add() METHOD ---
    public function add()
    {
        $post = $this->request->getPost();

        // Validasi input (tidak berubah)
        $rules = [
            'po_number' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'PO Number is required.',
                    'numeric'  => 'PO Number must be a number.',
                ],
            ],
            'ref_num_subs_id' => [
                'rules' => 'permit_empty|alpha_numeric',
                'errors' => [
                    'alpha_numeric' => 'Ref. Number / Subs ID can only contain letters and numbers.'
                ]
            ],
            'product_key' => [
                'rules' => 'permit_empty|alpha_numeric_punct',
                'errors' => [
                    'alpha_numeric_punct' => 'Product Key can only contain letters, numbers, and common punctuation.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $isPoFinderSourced = $this->request->getPost('po_sourced_from_finder') === '1';
        
        $data = [
            'tl_type'            => $post['license_type'] ?? null,
            'tl_license_type_category' => $post['license_type_category'] ?? null,
            'tl_refnumber'       => empty($post['ref_num_subs_id']) ? null : strtoupper($post['ref_num_subs_id']),
            'tl_po_number'       => $post['po_number'],
            'tl_licensepartner'  => empty($post['license_partner']) ? null : strtoupper($post['license_partner']),
            'tl_orderdate'       => empty($post['order_date']) ? null : $post['order_date'],
            'tl_startdate'       => empty($post['start_date']) ? null : $post['start_date'],
            'tl_enddate'         => empty($post['end_date']) ? null : $post['end_date'],
            'tl_productname'     => empty($post['product_name']) ? null : strtoupper($post['product_name']),
            'tl_productqty'      => empty($post['product_qty']) ? null : $post['product_qty'],
            'tl_productdesc'     => $post['product_desc'],
            'tl_productkey'      => empty($post['product_key']) ? null : strtoupper($post['product_key']),
            'tl_organization'    => $post['organization'],
            'tl_last_user'       => session()->get('user_id') ?? 1,
            'tl_status'          => 1, // TAMBAHKAN INI: Set status default saat penambahan data
        ];
        
        try {
            $insertID = $this->SoftwareLicenseModel->insert($data);

            if ($isPoFinderSourced) {
                $finderSourcedIds = session()->get('finder_sourced_po_ids') ?? [];
                $finderSourcedIds[] = $insertID;
                session()->set('finder_sourced_po_ids', array_unique($finderSourcedIds));
            }

            // Setelah insert, fetch kembali data lengkap dari record yang baru dengan semua alias yang sesuai
            $newlyInsertedLicense = $this->db->table('t_license')
                ->select("
                    tl_id AS id,
                    tl_type AS type,
                    tl_license_type_category AS license_category,
                    UPPER(tl_refnumber) AS ref_number,
                    tl_po_number AS po_number,
                    UPPER(tl_licensepartner) AS license_partner,
                    tl_orderdate AS order_date,
                    tl_startdate AS start_date,
                    tl_enddate AS end_date,
                    UPPER(tl_productname) AS product_name,
                    tl_productqty AS product_qty,
                    tl_productdesc AS product_desc,
                    UPPER(tl_productkey) AS product_key,
                    tl_organization AS organization,
                    tl_last_update AS last_update,
                    tl_last_user AS last_user_id_from_db,
                    tl_status AS status_data_int -- Tambahkan alias untuk tl_status
                ")
                ->where('tl_id', $insertID)
                ->get()
                ->getRowArray();

            $lastUserDisplayName = 'N/A';
            $userIdFromDb = $newlyInsertedLicense['last_user_id_from_db'];

            // Coba ambil nama user dari `tbmst_employee`
            $userFromEmployee = $this->dbCommon->table('tbmst_employee')
                                                ->select('em_emplname')
                                                ->where('em_emplcode', $userIdFromDb)
                                                ->get()
                                                ->getRowArray();
            if ($userFromEmployee) {
                $lastUserDisplayName = $userFromEmployee['em_emplname'];
            } else {
                $loggedInUserId = session()->get('user_id');
                $loggedInUserName = session()->get('user_name');

                if ($loggedInUserId == $userIdFromDb && !empty($loggedInUserName)) {
                    $lastUserDisplayName = $loggedInUserName;
                } else {
                    $lastUserDisplayName = $userIdFromDb;
                }
            }
            
            $newlyInsertedLicense['last_user'] = $lastUserDisplayName;
            unset($newlyInsertedLicense['last_user_id_from_db']);

            return $this->response->setJSON([
                'status' => true, 
                'message' => 'Record added successfully.', 
                'new_license_id' => $insertID, 
                'product_qty' => $newlyInsertedLicense['product_qty'],
                'new_license_data' => $newlyInsertedLicense // Kirim seluruh objek data
            ]);
        } catch(\Exception $e) {
            log_message('error',$e->getMessage());
            return $this->response->setJSON(['status'=>false,'error'=>$e->getMessage()]);
        }
    }

    public function edit()
    {
        $id = $this->request->getPost('id');
        $row = $this->SoftwareLicenseModel
                                         ->where('tl_id', $id)
                                         ->where('tl_status', 1) // TAMBAHKAN KONDISI INI
                                         ->first();
        
        if (!$row) {
            return $this->response
                                         ->setStatusCode(404)
                                         ->setJSON(['status'=>false,'message'=>'Data not found']);
        }
        
        $isPoFinderSourced = false;
        $finderSourcedIds = session()->get('finder_sourced_po_ids') ?? [];
        if (in_array($id, $finderSourcedIds)) {
            $isPoFinderSourced = true;
        }

        // Map kolom ke key yang dipakai di JS (termasuk semua yang dibutuhkan DataTable)
        $data = [
            'id'                  => $row['tl_id'],
            'license_id'          => $row['tl_id'],
            'type'                => $row['tl_type'],
            'license_category'    => $row['tl_license_type_category'],
            'ref_number'          => $row['tl_refnumber'],
            'po_number'           => $row['tl_po_number'],
            'license_partner'     => $row['tl_licensepartner'],
            'order_date'          => substr($row['tl_orderdate'], 0, 10),
            'start_date'          => substr($row['tl_startdate'], 0, 10),
            'end_date'            => substr($row['tl_enddate'], 0, 10),
            'product_name'        => $row['tl_productname'],
            'product_qty'         => $row['tl_productqty'],
            'product_desc'        => $row['tl_productdesc'],
            'product_key'         => $row['tl_productkey'],
            'organization'        => $row['tl_organization'],
            'po_sourced_from_finder' => $isPoFinderSourced ? 1 : 0,
            'last_update'         => $row['tl_last_update'],
            'last_user'           => $row['tl_last_user'],
        ];

        // Resolusi nama user untuk data lisensi utama di modal edit
        $lastUserDisplayName = 'N/A';
        if (!empty($data['last_user'])) {
            $userIdOrEmployeeCode = $data['last_user'];
            
            // Coba cari di tbmst_employee (sesuai yang berhasil di "Detail PC")
            $userFromEmployee = $this->dbCommon->table('tbmst_employee')
                                                ->select('em_emplname')
                                                ->where('em_emplcode', $userIdOrEmployeeCode)
                                                ->get()
                                                ->getRowArray();

            if ($userFromEmployee) {
                $lastUserDisplayName = $userFromEmployee['em_emplname'];
            } else {
                $loggedInUserId = session()->get('user_id');
                $loggedInUserName = session()->get('user_name'); 

                if ($loggedInUserId == $userIdOrEmployeeCode && !empty($loggedInUserName)) {
                    $lastUserDisplayName = $loggedInUserName;
                } else {
                    $lastUserDisplayName = $userIdOrEmployeeCode;
                }
            }
        }
        $data['last_user'] = $lastUserDisplayName;
        
        return $this->response->setJSON(['status'=>true,'data'=>$data]);
    }


    public function update()
    {
        $post = $this->request->getPost();
        $tl_id = $post['td_id'];
        
        log_message('debug', 'tl_id saat update: ' . $tl_id);
        
        if (empty($tl_id)) {
            log_message('error', 'tl_id is empty. Cannot update.');
            return $this->response
                                         ->setStatusCode(400)
                                         ->setJSON(['status'=>false,'error'=>'tl_id is missing.']);
        }

        // Validasi input
        $rules = [
            'po_number' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'PO Number is required.',
                    'numeric'  => 'PO Number must be a number.',
                ],
            ],
            'ref_num_subs_id' => [
                'rules' => 'permit_empty|alpha_numeric',
                'errors' => [
                    'alpha_numeric' => 'Ref. Number / Subs ID can only contain letters and numbers.'
                ]
            ],
            'product_key' => [
                'rules' => 'permit_empty|alpha_numeric_punct',
                'errors' => [
                    'alpha_numeric_punct' => 'Product Key can only contain letters, numbers, and common punctuation.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }
        
        $data = [
            'tl_type'            => $post['license_type'] ?? null,
            'tl_license_type_category' => $post['license_type_category'] ?? null,
            'tl_refnumber'       => empty($post['ref_num_subs_id']) ? null : strtoupper($post['ref_num_subs_id']),
            'tl_po_number'       => $post['po_number'],
            'tl_licensepartner'  => empty($post['license_partner']) ? null : strtoupper($post['license_partner']),
            'tl_orderdate'       => empty($post['order_date']) ? null : $post['order_date'],
            'tl_productname'     => empty($post['product_name']) ? null : strtoupper($post['product_name']),
            'tl_productqty'      => empty($post['product_qty']) ? null : $post['product_qty'],
            'tl_startdate'       => empty($post['start_date']) ? null : $post['start_date'],
            'tl_enddate'         => empty($post['end_date']) ? null : $post['end_date'],
            'tl_productdesc'     => $post['product_desc'],
            'tl_productkey'      => empty($post['product_key']) ? null : strtoupper($post['product_key']),
            'tl_organization'    => $post['organization'],
            'tl_last_update'     => Time::now(),
            'tl_last_user'       => session()->get('user_id') ?? 1,
            // 'tl_status' tidak perlu di-update di sini karena ini untuk soft delete, bukan update biasa
        ];
        
        try {
            $this->SoftwareLicenseModel->update($tl_id, $data);
            return $this->response->setJSON(['status'=>true]);
        } catch(\Exception $e) {
            log_message('error', $e->getMessage());
            return $this->response
                                         ->setStatusCode(500)
                                         ->setJSON(['status'=>false,'error'=>$e->getMessage()]);
        }
    }

    /**
     * Mengubah status tl_status menjadi 25 untuk soft delete.
     */
    public function delete()
    {
        $id = $this->request->getPost('id');
        try {
            // Panggil metode delete() yang sudah kita override di model
            // Ini akan mengubah tl_status menjadi 25, BUKAN menghapus fisik.
            $this->SoftwareLicenseModel->delete($id);
            
            // Juga lakukan soft delete untuk semua PC yang terlisensi di bawah lisensi ini
            $this->SoftwareLicenseModel->softDeleteLicensedPcsByLicenseId($id); // UBAH INI

            return $this->response->setJSON(['status' => true]);
        } catch (\Exception $e) {
            log_message('error', 'Soft delete error for license: '.$e->getMessage());
            return $this->response
                                         ->setStatusCode(500)
                                         ->setJSON(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Cek duplikat di t_license untuk PO Number, Product Key, Ref. Num / Subs ID
     * Saat edit, kirim juga td_id (pk lama) untuk EXCLUDE dirinya sendiri.
     * Hanya memeriksa data yang tl_status = 1.
     */
    public function checkDuplicate()
    {
        $post = $this->request->getPost();
        $id  = isset($post['td_id']) ? (int)$post['td_id'] : null;

        $po  = trim($post['po_number']);
        $ref = trim($post['ref_num_subs_id']);
        $key = trim($post['product_key']);

        // Tambahkan kondisi tl_status = 1 untuk semua pengecekan duplikasi
        $bpo = $this->db->table('t_license')->where('tl_po_number', $po)->where('tl_status', 1); // TAMBAHKAN INI
        if ($id) $bpo->where('tl_id !=', $id);
        $existPO = (bool)$bpo->countAllResults();

        if ($key === '') {
            $existKey = false;
        } else {
            $bkey = $this->db->table('t_license')->where('tl_productkey', $key)->where('tl_status', 1); // TAMBAHKAN INI
            if ($id) $bkey->where('tl_id !=', $id);
            $existKey = (bool)$bkey->countAllResults();
        }

        $bref = $this->db->table('t_license')->where('tl_refnumber', $ref)->where('tl_status', 1); // TAMBAHKAN INI
        if ($id) $bref->where('tl_id !=', $id);
        $existRef = (bool)$bref->countAllResults();

        return $this->response->setJSON([
            'existPO'  => $existPO,
            'existKey' => $existKey,
            'existRef' => $existRef
        ]);
    }

    /**
     * Get licensed PCs for a specific software license (tl_id)
     * Hanya mengambil PC yang ld_status = 1.
     */
    public function getLicensedPcs($tl_id)
    {
        // Tambahkan validasi untuk tl_id agar tidak null atau 0
        if (empty($tl_id) || !is_numeric($tl_id) || (int)$tl_id <= 0) {
            log_message('error', 'Invalid tl_id provided to getLicensedPcs: ' . $tl_id);
            return $this->response->setJSON([]); // Return empty array for invalid ID
        }

        try {
            $data = $this->db->table('t_licensedetail td')
                ->select('td.ld_id, td.tl_id, td.ld_pcnama, td.ld_assetno, td.ld_pc_id, td.ld_po_number, td.ld_status, td.ld_lastuser, td.ld_lastupdate, td.ld_serialnumber, td.ld_employee_code, td.ld_position_code')
                ->where('td.tl_id', (int)$tl_id)
                ->where('td.ld_status', 1) // TAMBAHKAN KONDISI INI: Hanya tampilkan PC dengan status 1
                ->orderBy('td.ld_lastupdate DESC')
                ->get()
                ->getResultArray();

            // Fetch employee and position names from jincommon
            if (!empty($data)) {
                $employeeCodes = array_filter(array_column($data, 'ld_employee_code'));
                $positionCodes = array_filter(array_column($data, 'ld_position_code'));

                $employees = [];
                if (!empty($employeeCodes)) {
                    $employeeQuery = $this->dbCommon->table('tbmst_employee')
                                             ->select('em_emplcode, em_emplname')
                                             ->whereIn('em_emplcode', $employeeCodes)
                                             ->get()
                                             ->getResultArray();
                    foreach ($employeeQuery as $emp) {
                        $employees[$emp['em_emplcode']] = $emp['em_emplname'];
                    }
                }

                $positions = [];
                if (!empty($positionCodes)) {
                    $positionQuery = $this->dbCommon->table('tbmst_position')
                                             ->select('pm_code, pm_positionname')
                                             ->whereIn('pm_code', $positionCodes)
                                             ->get()
                                             ->getResultArray();
                    foreach ($positionQuery as $pos) {
                        $positions[$pos['pm_code']] = $pos['pm_positionname'];
                    }
                }

                // Merge employee and position names into the main data array
                foreach ($data as &$row) {
                    $row['em_emplname'] = $employees[$row['ld_employee_code']] ?? null;
                    $row['pm_positionname'] = $positions[$row['ld_position_code']] ?? null;
                }
            }

            if (empty($data)) {
                return $this->response->setJSON([]);
            }
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching licensed PC data for tl_id ' . $tl_id . ': ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => true, 'message' => 'Could not retrieve licensed PC data: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Get count of licensed PCs for a specific software license (tl_id)
     * Hanya menghitung PC yang ld_status = 1.
     */
    public function countLicensedPcs($tl_id)
    {
        try {
            $count = $this->SoftwareLicenseModel->countLicensedPcsByLicenseId($tl_id);
            $licenseData = $this->SoftwareLicenseModel->select('tl_productqty, tl_status')->find($tl_id); // Ambil juga tl_status
            
            $productQty = $licenseData ? (float)$licenseData['tl_productqty'] : 0.0;
            
            // Tambahkan pengecekan status lisensi utama
            if (!$licenseData || $licenseData['tl_status'] == 25) { // Jika lisensi utama sudah "dihapus"
                return $this->response->setJSON(['status' => true, 'count' => 0, 'product_qty' => 0, 'license_status' => 25]);
            }

            return $this->response->setJSON(['status' => true, 'count' => $count, 'product_qty' => $productQty, 'license_status' => 1]);
        } catch (\Exception $e) {
            log_message('error', 'Error counting licensed PCs: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['status' => false, 'error' => 'Could not count licensed PCs']);
        }
    }

    /**
     * Add a new licensed PC to t_licensedetail
     */
    public function addLicensedPc()
    {
        $post = $this->request->getPost();

        $rules = [
            'asset_no' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Asset Number is required.',
                ],
            ],
            'pc_id' => [
                'rules'  => 'permit_empty|integer',
                'errors' => [
                    'integer' => 'Asset ID must be a number.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        if (empty($post['asset_no'])) {
            return $this->response->setJSON(['status' => false, 'errors' => ['asset_no' => 'Asset Number is required.']]);
        }

        $tl_id = $post['tl_id'];
        $asset_no = strtoupper($post['asset_no']);

        // Panggil checkDuplicateLicensedPcAssetNo dari model yang sudah dimodifikasi
        $isDuplicate = $this->SoftwareLicenseModel->checkDuplicateLicensedPcAssetNo(
            $tl_id,
            $asset_no
        );

        if ($isDuplicate) {
            return $this->response->setJSON(['status' => false, 'errors' => ['asset_no' => 'This Asset Number is already licensed (active status) for this software.']]); // Ubah pesan error
        }

        $data = [
            'tl_id'          => $tl_id,
            'ld_pcnama'      => strtoupper($post['pc_name'] ?? null),
            'ld_assetno'     => $asset_no,
            'ld_pc_id'       => ($post['pc_id'] === '' || $post['pc_id'] === null) ? null : (int)$post['pc_id'],
            'ld_po_number'   => $post['po_number'] ?? null,
            'ld_status'      => 1, // Pastikan selalu 1 saat menambahkan (aktif)
            'ld_lastuser'    => session()->get('user_id') ?? 1,
            'ld_lastupdate'  => Time::now()->toDateTimeString(),
            'ld_serialnumber'=> strtoupper($post['serial_number'] ?? null),
            'ld_employee_code' => ($post['employee_code'] === '' || $post['employee_code'] === null) ? null : (int)$post['employee_code'],
            'ld_position_code' => ($post['position_code'] === '' || $post['position_code'] === null) ? null : (int)$post['position_code'],
        ];

        try {
            $insertID = $this->SoftwareLicenseModel->addLicensedPc($data);
            return $this->response->setJSON(['status' => true, 'id' => $insertID]);
        } catch (\Exception $e) {
            log_message('error', 'Error adding licensed PC: ' . $e->getMessage());
            return $this->response->setJSON(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Edit a licensed PC record
     * Hanya mengambil yang ld_status = 1.
     */
    public function editLicensedPc()
    {
        $ld_id = $this->request->getPost('ld_id');
        $row = $this->SoftwareLicenseModel->getLicensedPcById($ld_id); // getLicensedPcById sudah filter status 1

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Licensed PC data not found or is inactive.']); // Ubah pesan
        }

        $employeeName = null;
        $positionName = null;

        $ld_employee_code = $row['ld_employee_code'] ?? null;
        $ld_position_code = $row['ld_position_code'] ?? null;

        if (!empty($ld_employee_code)) {
            $employee = $this->dbCommon->table('tbmst_employee')
                                             ->select('em_emplname')
                                             ->where('em_emplcode', $ld_employee_code)
                                             ->get()
                                             ->getRowArray();
            if ($employee) {
                $employeeName = $employee['em_emplname'];
            }
        }

        if (!empty($ld_position_code)) {
            $position = $this->dbCommon->table('tbmst_position')
                                             ->select('pm_positionname')
                                             ->where('pm_code', $ld_position_code)
                                             ->get()
                                             ->getRowArray();
            if ($position) {
                $positionName = $position['pm_positionname'];
            }
        }

        $isFinderSourced = $this->SoftwareLicenseModel->isLicensedPcFromFinder(
            $row['ld_assetno'],
            $row['ld_pcnama'],
            $row['ld_serialnumber'],
            $row['ld_pc_id']
        );

        $response_data = [
            'ld_id'              => $row['ld_id'],
            'tl_id'              => $row['tl_id'],
            'ld_pcnama'          => $row['ld_pcnama'],
            'ld_assetno'         => $row['ld_assetno'],
            'ld_pc_id'           => $row['ld_pc_id'],
            'ld_po_number'       => $row['ld_po_number'],
            'ld_status'          => $row['ld_status'], // Kirim status saat ini
            'ld_lastuser'        => $row['ld_lastuser'],
            'ld_lastupdate'      => $row['ld_lastupdate'],
            'ld_serialnumber'    => $row['ld_serialnumber'],
            'ld_employee_code'   => $ld_employee_code,
            'ld_position_code'   => $ld_position_code,
            'em_emplname'        => $employeeName,
            'pm_positionname'    => $positionName,
            'is_finder_sourced'  => $isFinderSourced
        ];

        return $this->response->setJSON(['status' => true, 'data' => $response_data]);
    }

    /**
     * Update a licensed PC record
     */
    public function updateLicensedPc()
    {
        $post = $this->request->getPost();
        $ld_id = $post['ld_id'];

           $rules = [
            'asset_no' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Asset Number is required.',
                ],
            ],
            'pc_id' => [
                'rules'  => 'permit_empty|integer',
                'errors' => [
                    'integer' => 'Asset ID must be a number.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        if (empty($post['asset_no'])) {
            return $this->response->setJSON(['status' => false, 'errors' => ['asset_no' => 'Asset Number is required.']]);
        }
        
        $tl_id = $post['tl_id'];
        $asset_no = strtoupper($post['asset_no']);

        // Panggil checkDuplicateLicensedPcAssetNo dari model yang sudah dimodifikasi
        $isDuplicate = $this->SoftwareLicenseModel->checkDuplicateLicensedPcAssetNo(
            $tl_id,
            $asset_no,
            (int)$ld_id
        );

        if ($isDuplicate) {
            return $this->response->setJSON(['status' => false, 'errors' => ['asset_no' => 'This Asset Number is already licensed (active status) for this software.']]); // Ubah pesan error
        }

        $data = [
            'tl_id'          => $tl_id,
            'ld_pcnama'      => strtoupper($post['pc_name'] ?? null),
            'ld_assetno'     => $asset_no,
            'ld_pc_id'       => ($post['pc_id'] === '' || $post['pc_id'] === null) ? null : (int)$post['pc_id'],
            'ld_po_number'   => $post['po_number'] ?? null,
            'ld_status'      => 1, // Pastikan ini diatur ke 1 saat diupdate (tetap aktif)
            'ld_lastuser'    => session()->get('user_id') ?? 1,
            'ld_lastupdate'  => Time::now()->toDateTimeString(),
            'ld_serialnumber'=> strtoupper($post['serial_number'] ?? null),
            'ld_employee_code' => ($post['employee_code'] === '' || $post['employee_code'] === null) ? null : (int)$post['employee_code'],
            'ld_position_code' => ($post['position_code'] === '' || $post['position_code'] === null) ? null : (int)$post['position_code'],
        ];
        
        try {
            $this->SoftwareLicenseModel->updateLicensedPc($ld_id, $data);
            return $this->response->setJSON(['status' => true]);
            // Tambahkan ini agar tidak ada data yang difetch ulang di backend setelah updateLicensedPc
        } catch (\Exception $e) {
            log_message('error', 'Error updating licensed PC: ' . $e->getMessage());
            return $this->response->setJSON(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Soft delete a licensed PC record
     */
    public function deleteLicensedPc()
    {
        $ld_id = $this->request->getPost('ld_id');
        try {
            $this->SoftwareLicenseModel->softDeleteLicensedPc($ld_id); // UBAH INI
            return $this->response->setJSON(['status' => true]);
        } catch (\Exception $e) {
            log_message('error', 'Soft delete licensed PC error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                         ->setJSON(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get Equipment data for search modal (from m_itequipment)
     * (Tidak ada perubahan terkait status di sini).
     */
    public function getEquipmentData()
    {
        try {
            $search = $this->request->getGet('search');
            $builder = $this->db->table('m_itequipment'); 

            $builder->select('e_id, e_assetno, e_equipmentname, e_equipmentid, e_serialnumber, e_brand, e_model'); 

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
            
            $data = $builder->orderBy('e_assetno', 'ASC')
                                             ->get()
                                             ->getResultArray();
            
            if (empty($data)) {
                return $this->response->setJSON([]);
            }
            
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching Equipment data: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                             ->setJSON(['error' => true, 'message' => 'Could not retrieve Equipment data: ' . $e->getMessage()]);
        }
    }

    /**
     * Get Employee data for search modal (from jincommon db).
     * (Tidak ada perubahan terkait status di sini).
     */
    public function getEmployeeData()
    {
        try {
            $search = $this->request->getGet('search');
            $builder = $this->dbCommon->table('tbmst_employee te');
            $builder->select('te.em_emplcode, te.em_emplname, tp.pm_positionname, tp.pm_code');
            $builder->join('tbmst_position tp', 'tp.pm_code = te.em_positioncode', 'left');

            if (!empty($search)) {
                $searchTerm = strtoupper(trim($search));
                $builder->groupStart()
                    ->like('UPPER(TRIM(te.em_emplcode::text))', $searchTerm)
                    ->orLike('UPPER(TRIM(te.em_emplname))', $searchTerm)
                    ->orLike('UPPER(TRIM(tp.pm_positionname))', $searchTerm)
                    ->groupEnd();
            }

            $data = $builder->orderBy('te.em_emplname', 'ASC')
                                 ->get()
                                 ->getResultArray();

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching Employee data: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => true, 'message' => 'Could not retrieve Employee data: ' . $e->getMessage()]);
        }
    }

    /**
     * Export all software license data and their licensed PCs to Excel.
     * Hanya mengambil data yang tl_status = 1 dan ld_status = 1.
     */
    public function exportExcel()
    {
        try {
            // Get all active software licenses
            $licensesQuery = $this->db->query("
                SELECT
                    tl_id           AS id,
                    tl_type         AS type,
                    tl_license_type_category AS license_category,
                    UPPER(tl_refnumber) AS ref_number,
                    tl_po_number    AS po_number,
                    UPPER(tl_licensepartner) AS license_partner,
                    tl_orderdate    AS order_date,
                    tl_startdate    AS start_date,
                    tl_enddate      AS end_date,
                    UPPER(tl_productname)     AS product_name,
                    tl_productdesc  AS product_desc,
                    tl_productqty   AS product_qty,
                    UPPER(tl_productkey) AS product_key,
                    tl_organization AS organization
                FROM t_license
                WHERE tl_status = 1 -- TAMBAHKAN KONDISI INI
                ORDER BY tl_id ASC
            ");
            $licenses = $licensesQuery->getResultArray();

            // Prepare employee and position data for lookup
            $employeeData = $this->dbCommon->table('tbmst_employee')
                ->select('em_emplcode, em_emplname')
                ->get()
                ->getResultArray();
            $employees = array_column($employeeData, 'em_emplname', 'em_emplcode');

            $positionData = $this->dbCommon->table('tbmst_position')
                ->select('pm_code, pm_positionname')
                ->get()
                ->getResultArray();
            $positions = array_column($positionData, 'pm_positionname', 'pm_code');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Software Licenses');

            $sheet->setCellValue('A3', 'List Software License');
            $sheet->mergeCells('A3:S3'); 
            $sheet->getStyle('A3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $headerStartRow = 5;
            $dataStartRow = 6;
            
            $headers = [
                'No.', 'License ID', 'License Type', 'License Category', 'License Partner',
                'Product Name', 'PO Number', 'Ref. Number / Subs ID', 'Product Key',
                'Product Desc', 'Product Qty', 'Organization',
                'No.',
                'PC Asset Name', 'PC Asset Number', 'PC Asset ID', 'PC Asset Serial Number',
                'User', 'Position'
            ];
            $sheet->fromArray($headers, NULL, 'A' . $headerStartRow);

            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'C7D9FE'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC'],
                    ],
                ],
            ];
            $sheet->getStyle('A' . $headerStartRow . ':' . $sheet->getHighestColumn() . $headerStartRow)->applyFromArray($headerStyle);

            $styleEvenRow = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'F0F0F0'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC'],
                    ],
                ],
            ];

            $styleOddRow = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFFFFF'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC'],
                    ],
                ],
            ];

            $greenColumnStyle = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'D1FEB8'],
                ],
            ];

            $defaultColumnStyle = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'DAE5FF'],
                ],
            ];

            $redCellStyle = [
                'font' => [
                    'color' => ['rgb' => '000000'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FF8E88'],
                ],
            ];

            $rowNum = $dataStartRow;
            $nomorUrutLisensi = 1;

            foreach ($licenses as $license) {
                // HANYA AMBIL PC DENGAN ld_status = 1 UNTUK EXPORT
                $licensedPcsQuery = $this->db->table('t_licensedetail')
                    ->select('ld_pcnama, ld_assetno, ld_pc_id, ld_serialnumber, ld_employee_code, ld_position_code, ld_status')
                    ->where('tl_id', $license['id'])
                    ->where('ld_status', 1) // TAMBAHKAN KONDISI INI
                    ->orderBy('ld_pcnama', 'ASC')
                    ->get();
                $licensedPcs = $licensedPcsQuery->getResultArray();

                $numLicensedPcs = count($licensedPcs);
                $initialRowNum = $rowNum;

                $mainLicenseData = [
                    $license['id'],
                    $license['type'],
                    $license['license_category'],
                    $license['license_partner'],
                    $license['product_name'],
                    $license['po_number'],
                    $license['ref_number'],
                    $license['product_key'],
                    (string)$license['product_desc'],
                    (float)$license['product_qty'],
                    (string)$license['organization'],
                ];

                if ($numLicensedPcs === 0) {
                    $rowData = array_merge([$nomorUrutLisensi], $mainLicenseData, ['', '', '', '', '', '', '']); // Sesuaikan jumlah kolom kosong PC jika ada perubahan
                    $sheet->fromArray($rowData, NULL, 'A' . $rowNum);

                    $styleToApply = ($rowNum % 2 === 0) ? $styleEvenRow : $styleOddRow;
                    $sheet->getStyle('A' . $rowNum . ':' . $sheet->getHighestColumn() . $rowNum)->applyFromArray($styleToApply);

                    $sheet->getStyle('A' . $rowNum . ':' . 'L' . $rowNum)->applyFromArray($defaultColumnStyle);
                    $sheet->getStyle('M' . $rowNum . ':' . 'S' . $rowNum)->applyFromArray($greenColumnStyle);

                    if ((float)$license['product_qty'] < 3) {
                        $sheet->getStyle('K' . $rowNum)->applyFromArray($redCellStyle);
                    }
                    $rowNum++;
                } else {
                    $nomorUrutPc = 1;

                    foreach ($licensedPcs as $pcIndex => $pc) {
                        $pcData = [
                            $nomorUrutPc++,
                            (string)$pc['ld_pcnama'],
                            (string)$pc['ld_assetno'],
                            (string)$pc['ld_pc_id'],
                            (string)$pc['ld_serialnumber'],
                            $employees[$pc['ld_employee_code']] ?? '',
                            $positions[$pc['ld_position_code']] ?? '',
                        ];

                        if ($pcIndex === 0) {
                            $rowData = array_merge([$nomorUrutLisensi], $mainLicenseData, $pcData);
                        } else {
                            $emptyLicenseData = array_fill(0, count($mainLicenseData) + 1, '');
                            $rowData = array_merge($emptyLicenseData, $pcData);
                        }
                        $sheet->fromArray($rowData, NULL, 'A' . $rowNum);

                        $styleToApply = ($rowNum % 2 === 0) ? $styleEvenRow : $styleOddRow;
                        $sheet->getStyle('A' . $rowNum . ':' . $sheet->getHighestColumn() . $rowNum)->applyFromArray($styleToApply);

                        $sheet->getStyle('A' . $rowNum . ':' . 'L' . $rowNum)->applyFromArray($defaultColumnStyle);
                        $sheet->getStyle('M' . $rowNum . ':' . 'S' . $rowNum)->applyFromArray($greenColumnStyle);

                        if ($pcIndex === 0 && (float)$license['product_qty'] < 3) {
                            $sheet->getStyle('K' . $rowNum)->applyFromArray($redCellStyle);
                        }
                        $rowNum++;
                    }

                    if ($numLicensedPcs > 1) {
                        $startMergeRow = $initialRowNum;
                        $endMergeRow = $rowNum - 1;

                        $sheet->mergeCells('A' . $startMergeRow . ':' . 'A' . $endMergeRow);
                        $sheet->getStyle('A' . $startMergeRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle('A' . $startMergeRow)->applyFromArray($defaultColumnStyle);

                        $mergeCols = [
                            'B' => $defaultColumnStyle,
                            'C' => $defaultColumnStyle,
                            'D' => $defaultColumnStyle,
                            'E' => $defaultColumnStyle,
                            'F' => $defaultColumnStyle,
                            'G' => $defaultColumnStyle,
                            'H' => $defaultColumnStyle,
                            'I' => $defaultColumnStyle,
                            'J' => $defaultColumnStyle,
                            'L' => $defaultColumnStyle,
                        ];

                        foreach ($mergeCols as $col => $style) {
                            $sheet->mergeCells($col . $startMergeRow . ':' . $col . $endMergeRow);
                            $sheet->getStyle($col . $startMergeRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getStyle($col . $startMergeRow)->applyFromArray($style);
                        }
                        
                        $sheet->mergeCells('K' . $startMergeRow . ':' . 'K' . $endMergeRow);
                        $sheet->getStyle('K' . $startMergeRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        
                        if ((float)$license['product_qty'] < 3) {
                            $sheet->getStyle('K' . $startMergeRow)->applyFromArray($redCellStyle);
                        } else {
                            $sheet->getStyle('K' . $startMergeRow)->applyFromArray($defaultColumnStyle);
                        }
                    }
                }
                $nomorUrutLisensi++;
            }

            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $sheet->setAutoFilter('A' . $headerStartRow . ':' . $sheet->getHighestColumn() . $headerStartRow);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Software_License_Report_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Error exporting Excel: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => true, 'message' => 'Failed to export Excel: ' . $e->getMessage()]);
        }
    }

    /**
     * Export a single software license record and its licensed PCs to Excel.
     * Hanya mengambil data yang tl_status = 1 dan ld_status = 1.
     */
    public function exportExcelById(int $tl_id)
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        try {
            // Fetch main software license record by tl_id, only if tl_status = 1
            $license = $this->db->table('t_license')
                                 ->select('tl_id AS id, tl_type AS type, tl_license_type_category AS license_category,
                                             UPPER(tl_refnumber) AS ref_number, tl_po_number AS po_number,
                                             UPPER(tl_licensepartner) AS license_partner, tl_orderdate AS order_date,
                                             tl_startdate AS start_date, tl_enddate AS end_date,
                                             UPPER(tl_productname) AS product_name, tl_productdesc AS product_desc,
                                             tl_productqty AS product_qty, UPPER(tl_productkey) AS product_key,
                                             tl_organization AS organization, tl_status AS status_data_int') // Tambahkan tl_status
                                 ->where('tl_id', $tl_id)
                                 ->where('tl_status', 1) // TAMBAHKAN KONDISI INI
                                 ->get()
                                 ->getRowArray();

            if (!$license) {
                return $this->response->setStatusCode(404)
                                     ->setJSON(['error' => true, 'message' => 'Software License record not found or is inactive for the given ID.']); // Ubah pesan
            }

            // Fetch licensed PCs for the current main license, only if ld_status = 1
            $licensedPcsQuery = $this->db->table('t_licensedetail')
                                         ->select('ld_pcnama, ld_assetno, ld_pc_id, ld_serialnumber, ld_employee_code, ld_position_code, ld_status')
                                         ->where('tl_id', $license['id'])
                                         ->where('ld_status', 1) // TAMBAHKAN KONDISI INI
                                         ->orderBy('ld_pcnama', 'ASC')
                                         ->get();
            $licensedPcs = $licensedPcsQuery->getResultArray();

            // Prepare employee and position data for lookup
            $employeeData = $this->dbCommon->table('tbmst_employee')
                ->select('em_emplcode, em_emplname')
                ->get()
                ->getResultArray();
            $employees = array_column($employeeData, 'em_emplname', 'em_emplcode');

            $positionData = $this->dbCommon->table('tbmst_position')
                ->select('pm_code, pm_positionname')
                ->get()
                ->getResultArray();
            $positions = array_column($positionData, 'pm_positionname', 'pm_code');


            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('License ID ' . $license['id']);

            $sheet->setCellValue('A1', 'SOFTWARE LICENSE CONFIGURATION REPORT');
            $sheet->mergeCells('A1:L1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 18],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(30);

            $sheet->getRowDimension(2)->setRowHeight(15);

            $sheet->setCellValue('A3', 'Main License Details');
            $sheet->mergeCells('A3:L3');
            $sheet->getStyle('A3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'C7D9FE']],
            ]);

            $mainHeaders = [
                'ID', 'License Type', 'License Category', 'Ref. Number / Subs ID', 'PO Number',
                'License Partner', 'Order Date', 'Start Date', 'End Date', 'Product Name',
                'Product Qty', 'Product Desc', 'Product Key', 'Organization'
            ];
            $mainHeaderStartRow = 4;
            $sheet->fromArray($mainHeaders, NULL, 'A' . $mainHeaderStartRow);

            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCDE8F3']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
            ];
            $sheet->getStyle('A' . $mainHeaderStartRow . ':N' . $mainHeaderStartRow)->applyFromArray($headerStyle);

            $mainRowData = [
                $license['id'],
                $license['type'],
                $license['license_category'],
                $license['ref_number'],
                $license['po_number'],
                $license['license_partner'],
                $license['order_date'] ? (new Time($license['order_date']))->toDateString() : '',
                $license['start_date'] ? (new Time($license['start_date']))->toDateString() : '',
                $license['end_date'] ? (new Time($license['end_date']))->toDateString() : '',
                $license['product_name'],
                (float)$license['product_qty'],
                $license['product_desc'],
                $license['product_key'],
                $license['organization']
            ];
            $mainDataRowStart = $mainHeaderStartRow + 1;
            $sheet->fromArray($mainRowData, NULL, 'A' . $mainDataRowStart);
            $sheet->getStyle('A' . $mainDataRowStart . ':N' . $mainDataRowStart)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
            ]);

            if ((float)$license['product_qty'] < 3) {
                $redCellStyle = [
                    'font' => ['color' => ['rgb' => '000000']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF8E88']],
                ];
                $sheet->getStyle('K' . $mainDataRowStart)->applyFromArray($redCellStyle);
            }

            $sheet->getRowDimension($mainDataRowStart + 1)->setRowHeight(15);

            $pcHeaderStartRow = $mainDataRowStart + 3;
            $sheet->setCellValue('A' . $pcHeaderStartRow, 'Licensed PCs');
            $sheet->mergeCells('A' . $pcHeaderStartRow . ':H' . $pcHeaderStartRow);
            $sheet->getStyle('A' . $pcHeaderStartRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'D1FEB8']],
            ]);

            $pcHeaders = [
                'No.', 'PC Asset Name', 'PC Asset Number', 'PC Asset ID', 'PC Asset Serial Number',
                'User', 'Position', 'Status'
            ];
            $pcDataHeaderRow = $pcHeaderStartRow + 1;
            $sheet->fromArray($pcHeaders, NULL, 'A' . $pcDataHeaderRow);

            $sheet->getStyle('A' . $pcDataHeaderRow . ':H' . $pcDataHeaderRow)->applyFromArray($headerStyle);

            $rowNum = $pcDataHeaderRow + 1;
            $pcNo = 1;
            $overlicensedFill = ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFE0B2']];

            $productQty = (float)$license['product_qty'];

            if (empty($licensedPcs)) {
                $rowData = [
                    '', '', '', '', '', '', '', 'No Active PCs Licensed' // Ubah pesan
                ];
                $sheet->fromArray($rowData, NULL, 'A' . $rowNum);
                $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
                    'font' => ['italic' => true, 'color' => ['argb' => 'FF808080']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->mergeCells('A' . $rowNum . ':H' . $rowNum);
                $rowNum++;
            } else {
                foreach ($licensedPcs as $pcIndex => $pc) {
                    $rowData = [
                        $pcNo++,
                        $pc['ld_pcnama'],
                        $pc['ld_assetno'],
                        $pc['ld_pc_id'],
                        $pc['ld_serialnumber'],
                        $employees[$pc['ld_employee_code']] ?? '',
                        $positions[$pc['ld_position_code']] ?? '',
                        $pc['ld_status'] == 1 ? 'Active' : 'Inactive (Status: ' . $pc['ld_status'] . ')' // Tambahkan keterangan status
                    ];
                    $sheet->fromArray($rowData, NULL, 'A' . $rowNum);

                    $styleToApply = ($pcIndex % 2 === 0) ? ['fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFF0F0F0']]] : ['fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFFFFFFF']]];
                    $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray($styleToApply);
                    $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]]);

                    // Apply special style for overlicensed PCs (if their index exceeds product_qty)
                    // and for Inactive status (which would be 25 now for soft-delete)
                    if ($pcIndex >= $productQty || $pc['ld_status'] == 25) { // Ganti dari 0 ke 25
                        $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray($overlicensedFill);
                    }

                    $rowNum++;
                }
            }

            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'Software_License_ID_' . $license['id'] . '_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Error exporting Software License data to Excel by ID: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['error' => true, 'message' => 'Could not export data to Excel: ' . $e->getMessage()]);
        }
    }

}