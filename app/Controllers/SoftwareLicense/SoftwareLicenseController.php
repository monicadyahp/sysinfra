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
        
        // BARIS INI YANG HARUS DIUBAH:
        // return view('transaction/SoftwareLicense/index', $data);
        return view('master/SoftwareLicense/index', $data); // <--- UBAH KE PATH BARU INI
    }

    // Fungsi untuk mengambil data PO yang sudah di-join
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
                        tl_id            AS id,
                        tl_type          AS type,
                        tl_license_type_category AS license_category,
                        UPPER(tl_refnumber) AS ref_number,
                        tl_po_number     AS po_number,
                        UPPER(tl_licensepartner) AS license_partner,
                        tl_orderdate     AS order_date,
                        tl_startdate     AS start_date,
                        tl_enddate       AS end_date,
                        UPPER(tl_productname)    AS product_name,
                        tl_productqty    AS product_qty,
                        tl_productdesc   AS product_desc,
                        UPPER(tl_productkey) AS product_key,
                        tl_organization  AS organization,
                        tl_last_update   AS last_update,
                        tl_last_user    AS last_user
                    FROM t_license
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
            'tl_last_user'       => session()->get('user_id') ?? 1, // Pastikan ini adalah ID user yang valid di DB Anda
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
                    tl_last_user AS last_user_id_from_db
                ") // Gunakan alias yang jelas untuk tl_last_user
                ->where('tl_id', $insertID)
                ->get()
                ->getRowArray();

            $lastUserDisplayName = 'N/A'; // Default value jika tidak ditemukan
            $userIdFromDb = $newlyInsertedLicense['last_user_id_from_db']; // Ambil nilai tl_last_user dari hasil fetch dengan alias baru

            // Coba ambil nama user dari `tbmst_employee`
            $userFromEmployee = $this->dbCommon->table('tbmst_employee')
                                                ->select('em_emplname')
                                                ->where('em_emplcode', $userIdFromDb)
                                                ->get()
                                                ->getRowArray();
            if ($userFromEmployee) {
                $lastUserDisplayName = $userFromEmployee['em_emplname'];
            } else {
                // Fallback terakhir jika tidak ditemukan di DB atau jika user_id berasal dari sesi
                // Ini bisa jadi berguna jika `tl_last_user` di DB menyimpan sesuatu yang tidak ada di `tbmst_employee`
                // atau jika Anda ingin menampilkan nama dari sesi jika user yang sama yang sedang login
                $loggedInUserId = session()->get('user_id');
                $loggedInUserName = session()->get('user_name'); // Asumsi ini ada dan berisi nama lengkap

                if ($loggedInUserId == $userIdFromDb && !empty($loggedInUserName)) {
                    $lastUserDisplayName = $loggedInUserName;
                } else {
                    $lastUserDisplayName = $userIdFromDb; // Gunakan ID/kode sebagai fallback jika nama tidak ditemukan
                }
            }
            
            $newlyInsertedLicense['last_user'] = $lastUserDisplayName; // Set alias yang benar untuk frontend
            unset($newlyInsertedLicense['last_user_id_from_db']); // Hapus kolom sementara

            // Kirim data lengkap yang baru di-fetch ke frontend
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
            'type'                => $row['tl_type'], // Pastikan ada 'type'
            'license_category'    => $row['tl_license_type_category'], // Pastikan ada 'license_category'
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
            'last_update'          => $row['tl_last_update'],
            'last_user'            => $row['tl_last_user'], // Ini masih ID/kode
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
                // Fallback terakhir jika tidak ditemukan di DB atau jika user_id berasal dari sesi
                $loggedInUserId = session()->get('user_id');
                $loggedInUserName = session()->get('user_name'); 

                if ($loggedInUserId == $userIdOrEmployeeCode && !empty($loggedInUserName)) {
                    $lastUserDisplayName = $loggedInUserName;
                } else {
                    $lastUserDisplayName = $userIdOrEmployeeCode; // Fallback ke ID/kode jika nama tidak ditemukan
                }
            }
        }
        $data['last_user'] = $lastUserDisplayName; // Update kolom last_user dengan nama yang ditemukan
        
        return $this->response->setJSON(['status'=>true,'data'=>$data]);
    }


    public function update()
    {
        $post = $this->request->getPost();
        $tl_id = $post['td_id']; // Menggunakan nama variabel yang lebih sesuai
        
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
        
        // Kita tidak perlu memodifikasi tl_po_sourced_from_finder di sini
        // karena itu hanya flag di sesi/frontend untuk UI read-only.
        // Data di DB tetap diupdate sesuai input, asalkan validasinya lewat.

        $data = [
            'tl_type'            => $post['license_type'] ?? null,
            'tl_license_type_category' => $post['license_type_category'] ?? null,
            'tl_refnumber'       => empty($post['ref_num_subs_id']) ? null : strtoupper($post['ref_num_subs_id']),
            'tl_po_number'       => $post['po_number'], // Ini akan diupdate meskipun readonly di UI
            'tl_licensepartner'  => empty($post['license_partner']) ? null : strtoupper($post['license_partner']), // Ini akan diupdate meskipun readonly di UI
            'tl_orderdate'       => empty($post['order_date']) ? null : $post['order_date'], // Ini akan diupdate meskipun readonly di UI
            'tl_productname'     => empty($post['product_name']) ? null : strtoupper($post['product_name']), // Ini akan diupdate meskipun readonly di UI
            'tl_productqty'      => empty($post['product_qty']) ? null : $post['product_qty'], // Ini akan diupdate meskipun readonly di UI
            'tl_startdate'       => empty($post['start_date']) ? null : $post['start_date'],
            'tl_enddate'         => empty($post['end_date']) ? null : $post['end_date'],
            'tl_productdesc'     => $post['product_desc'],
            'tl_productkey'      => empty($post['product_key']) ? null : strtoupper($post['product_key']),
            'tl_organization'    => $post['organization'],
            'tl_last_update'     => Time::now(),
            'tl_last_user'       => session()->get('user_id') ?? 1,
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
     * Delete one record by ID
     */
    public function delete()
    {
        $id = $this->request->getPost('id');
        try {
            $this->SoftwareLicenseModel->deleteLicensedPcsByLicenseId($id);
            $this->SoftwareLicenseModel->delete($id);
            return $this->response->setJSON(['status' => true]);
        } catch (\Exception $e) {
            log_message('error', 'Delete error: '.$e->getMessage());
            return $this->response
                                                ->setStatusCode(500)
                                                ->setJSON(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Cek duplikat di t_license untuk PO Number, Product Key, Ref. Num / Subs ID
     * Saat edit, kirim juga td_id (pk lama) untuk EXCLUDE dirinya sendiri.
     */
    public function checkDuplicate()
    {
        $post = $this->request->getPost();
        $id  = isset($post['td_id']) ? (int)$post['td_id'] : null;

        $po  = trim($post['po_number']);
        $ref = trim($post['ref_num_subs_id']);
        $key = trim($post['product_key']);

        $bpo = $this->db->table('t_license')->where('tl_po_number', $po);
        if ($id) $bpo->where('tl_id !=', $id);
        $existPO = (bool)$bpo->countAllResults();

        if ($key === '') {
            $existKey = false;
        } else {
            $bkey = $this->db->table('t_license')->where('tl_productkey', $key);
            if ($id) $bkey->where('tl_id !=', $id);
            $existKey = (bool)$bkey->countAllResults();
        }

        $bref = $this->db->table('t_license')->where('tl_refnumber', $ref);
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
                    // Ensure default null if not found
                    $row['em_emplname'] = $employees[$row['ld_employee_code']] ?? null;
                    $row['pm_positionname'] = $positions[$row['ld_position_code']] ?? null;
                }
            }
            // End of new approach

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
     */
    public function countLicensedPcs($tl_id)
    {
        try {
            $count = $this->SoftwareLicenseModel->countLicensedPcsByLicenseId($tl_id);
            $licenseData = $this->SoftwareLicenseModel->select('tl_productqty')->find($tl_id);
            // Ensure productQty is always a number, default to 0 if null or not found
            $productQty = $licenseData ? (float)$licenseData['tl_productqty'] : 0.0; // Explicitly cast to float
            
            return $this->response->setJSON(['status' => true, 'count' => $count, 'product_qty' => $productQty]);
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
            'pc_id' => [ // Aturan validasi untuk Asset ID (ld_pc_id)
                'rules'  => 'permit_empty|integer', // Membolehkan kosong, tapi jika diisi harus integer
                'errors' => [
                    'integer' => 'Asset ID must be a number.', // Pesan error jika bukan angka
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


        $isDuplicate = $this->SoftwareLicenseModel->checkDuplicateLicensedPcAssetNo(
            $tl_id,
            $asset_no
        );

        if ($isDuplicate) {
            return $this->response->setJSON(['status' => false, 'errors' => ['asset_no' => 'This Asset Number is already licensed for this software.']]);
        }

        $data = [
            'tl_id'          => $tl_id,
            'ld_pcnama'      => strtoupper($post['pc_name'] ?? null),
            'ld_assetno'     => $asset_no,
            'ld_pc_id'       => ($post['pc_id'] === '' || $post['pc_id'] === null) ? null : (int)$post['pc_id'],
            'ld_po_number'   => $post['po_number'] ?? null,
            'ld_status'      => $post['ld_status'] ?? 1,
            'ld_lastuser'    => session()->get('user_id') ?? 1,
            'ld_lastupdate'  => Time::now()->toDateTimeString(),
            'ld_serialnumber'=> strtoupper($post['serial_number'] ?? null),
            'ld_employee_code' => ($post['employee_code'] === '' || $post['employee_code'] === null) ? null : (int)$post['employee_code'], // Tambah ini
            'ld_position_code' => ($post['position_code'] === '' || $post['position_code'] === null) ? null : (int)$post['position_code'], // Tambah ini
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
     */
    public function editLicensedPc()
    {
        $ld_id = $this->request->getPost('ld_id');
        $row = $this->SoftwareLicenseModel->getLicensedPcById($ld_id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Licensed PC data not found']);
        }

        // Ambil employee name dan position name jika employee_code dan position_code ada
        $employeeName = null;
        $positionName = null;

        // Pastikan ld_employee_code dan ld_position_code diambil dari $row yang sudah ada
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

        // Panggil fungsi baru di model untuk memeriksa kecocokan di m_itequipment
        $isFinderSourced = $this->SoftwareLicenseModel->isLicensedPcFromFinder(
            $row['ld_assetno'],
            $row['ld_pcnama'],
            $row['ld_serialnumber'],
            $row['ld_pc_id']
        );

        // Kirim data kembali ke frontend bersama status isFinderSourced
        $response_data = [
            'ld_id'              => $row['ld_id'],
            'tl_id'              => $row['tl_id'],
            'ld_pcnama'          => $row['ld_pcnama'],
            'ld_assetno'         => $row['ld_assetno'],
            'ld_pc_id'           => $row['ld_pc_id'],
            'ld_po_number'       => $row['ld_po_number'],
            'ld_status'          => $row['ld_status'],
            'ld_lastuser'        => $row['ld_lastuser'],
            'ld_lastupdate'      => $row['ld_lastupdate'],
            'ld_serialnumber'    => $row['ld_serialnumber'],
            'ld_employee_code'   => $ld_employee_code,    // Pastikan ini yang dikirim
            'ld_position_code'   => $ld_position_code,    // Pastikan ini yang dikirim
            'em_emplname'        => $employeeName,            // Data nama employee
            'pm_positionname'    => $positionName,            // Data nama position
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
            'pc_id' => [ // Aturan validasi untuk Asset ID (ld_pc_id)
                'rules'  => 'permit_empty|integer', // Membolehkan kosong, tapi jika diisi harus integer
                'errors' => [
                    'integer' => 'Asset ID must be a number.', // Pesan error jika bukan angka
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

        $isDuplicate = $this->SoftwareLicenseModel->checkDuplicateLicensedPcAssetNo(
            $tl_id,
            $asset_no,
            (int)$ld_id
        );

        if ($isDuplicate) {
            return $this->response->setJSON(['status' => false, 'errors' => ['asset_no' => 'This Asset Number is already licensed for this software.']]);
        }

        $data = [
            'tl_id'          => $tl_id,
            'ld_pcnama'      => strtoupper($post['pc_name'] ?? null),
            'ld_assetno'     => $asset_no,
            'ld_pc_id'       => ($post['pc_id'] === '' || $post['pc_id'] === null) ? null : (int)$post['pc_id'],
            'ld_po_number'   => $post['po_number'] ?? null,
            'ld_status'      => $post['ld_status'] ?? 1,
            'ld_lastuser'    => session()->get('user_id') ?? 1,
            'ld_lastupdate'  => Time::now()->toDateTimeString(),
            'ld_serialnumber'=> strtoupper($post['serial_number'] ?? null),
            'ld_employee_code' => ($post['employee_code'] === '' || $post['employee_code'] === null) ? null : (int)$post['employee_code'], // Tambah ini
            'ld_position_code' => ($post['position_code'] === '' || $post['position_code'] === null) ? null : (int)$post['position_code'], // Tambah ini
        ];
        
        try {
            $this->SoftwareLicenseModel->updateLicensedPc($ld_id, $data);
            return $this->response->setJSON(['status' => true]);
        } catch (\Exception $e) {
            log_message('error', 'Error updating licensed PC: ' . $e->getMessage());
            return $this->response->setJSON(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Delete a licensed PC record
     */
    public function deleteLicensedPc()
    {
        $ld_id = $this->request->getPost('ld_id');
        try {
            $this->SoftwareLicenseModel->deleteLicensedPc($ld_id);
            return $this->response->setJSON(['status' => true]);
        } catch (\Exception $e) {
            log_message('error', 'Delete licensed PC error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                            ->setJSON(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get Equipment data for search modal (from m_itequipment)
     */
    public function getEquipmentData()
    {
        try {
            $search = $this->request->getGet('search');
            $builder = $this->db->table('m_itequipment'); 

            // PASTIKAN SEMUA KOLOM YANG DIBUTUHKAN DISELECT!
            // Termasuk e_id, e_equipmentname, e_equipmentid, e_serialnumber
            $builder->select('e_id, e_assetno, e_equipmentname, e_equipmentid, e_serialnumber, e_brand, e_model'); 

            if (!empty($search)) {
                $searchTerm = strtoupper(trim($search)); // Normalisasi search term
                $builder->groupStart()
                        ->like('UPPER(TRIM(e_assetno))', $searchTerm)
                        ->orLike('UPPER(TRIM(e_equipmentname))', $searchTerm)
                        ->orLike('UPPER(TRIM(e_serialnumber))', $searchTerm)
                        ->orLike('UPPER(TRIM(e_brand))', $searchTerm)
                        ->orLike('UPPER(TRIM(e_model))', $searchTerm)
                        ->orLike('CAST(e_equipmentid AS TEXT)', $searchTerm) // Convert INT to TEXT for LIKE comparison
                        ->groupEnd();
            }
            
            $data = $builder->orderBy('e_assetno', 'ASC')
                                            ->get()
                                            ->getResultArray();
            
            // Debugging: Log data yang diambil dari m_itequipment
            // log_message('debug', 'Data from getEquipmentData: ' . json_encode($data));

            if (empty($data)) {
                return $this->response->setJSON([]);
            }
            
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching Equipment data: ' . $e->getMessage());
            // Return lebih detail untuk debugging frontend
            return $this->response->setStatusCode(500)
                                            ->setJSON(['error' => true, 'message' => 'Could not retrieve Equipment data: ' . $e->getMessage()]);
        }
    }

    public function getEmployeeData()
    {
        try {
            $search = $this->request->getGet('search');
            // Gunakan $this->dbCommon untuk tbmst_employee dan tbmst_position
            $builder = $this->dbCommon->table('tbmst_employee te');
            $builder->select('te.em_emplcode, te.em_emplname, tp.pm_positionname, tp.pm_code'); // Pilih juga pm_code untuk disimpan
            $builder->join('tbmst_position tp', 'tp.pm_code = te.em_positioncode', 'left');

            if (!empty($search)) {
                $searchTerm = strtoupper(trim($search));
                $builder->groupStart()
                    ->like('UPPER(TRIM(te.em_emplcode::text))', $searchTerm) // Cast to text for like on integer
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
     */
    /**
     * Export all software license data and their licensed PCs to Excel.
     */
    public function exportExcel()
    {
        try {
            // Get all software licenses
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
                    UPPER(tl_productname)    AS product_name,
                    tl_productdesc   AS product_desc,
                    tl_productqty    AS product_qty,
                    UPPER(tl_productkey) AS product_key,
                    tl_organization  AS organization
                FROM t_license
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

            // --- Mulai penambahan untuk header "List Software License" dan 3 baris kosong ---
            // Baris 1: Kosong
            // Baris 2: Kosong
            // Baris 3: Tulis "List Software License" di sel A3 dan gabungkan sel-selnya
            $sheet->setCellValue('A3', 'List Software License');
            // Gabungkan sel dari A3 hingga kolom terakhir dari header Anda (misalnya, 'S3')
            $sheet->mergeCells('A3:S3'); 
            $sheet->getStyle('A3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            // Baris 4: Kosong (ini akan menjadi baris kosong antara judul dan header tabel)

            // Atur posisi baris header dan data Anda untuk dimulai setelah baris judul dan kosong
            $headerStartRow = 5; // Header akan dimulai dari baris 5
            $dataStartRow = 6;  // Data akan dimulai dari baris 6
            // --- Akhir penambahan untuk header "List Software License" ---

            // Set header row
            $headers = [
                'No.', 'License ID', 'License Type', 'License Category', 'License Partner',
                'Product Name', 'PO Number', 'Ref. Number / Subs ID', 'Product Key',
                'Product Desc', 'Product Qty', 'Organization',
                'No.', // Header "No." untuk PC
                'PC Asset Name', 'PC Asset Number', 'PC Asset ID', 'PC Asset Serial Number',
                'User', 'Position'
            ];
            $sheet->fromArray($headers, NULL, 'A' . $headerStartRow);

            // Apply style to header
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'C7D9FE'], // Light Blue header
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC'],
                    ],
                ],
            ];
            $sheet->getStyle('A' . $headerStartRow . ':' . $sheet->getHighestColumn() . $headerStartRow)->applyFromArray($headerStyle);

            // Define alternating row styles (base styles)
            $styleEvenRow = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'F0F0F0'], // Very light gray
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC'], // Light gray border
                    ],
                ],
            ];

            $styleOddRow = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFFFFF'], // White
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
                    'color' => ['argb' => 'D1FEB8'], // Light Green
                ],
            ];

            $defaultColumnStyle = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'DAE5FF'], // Very Light Blue (for remaining columns)
                ],
            ];

            // Ganti warna kolom dan font untuk data product qty kurang dari 3 ( > 3 )
            $redCellStyle = [
                'font' => [
                    'color' => ['rgb' => '000000'], // black font
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FF8E88'], // Red background (ARGB format)
                ],
            ];
            // --- Akhir definisi $redCellStyle ---

            $rowNum = $dataStartRow; // Mulai data dari baris yang ditentukan setelah judul dan baris kosong
            $nomorUrutLisensi = 1; // Inisialisasi nomor urut untuk lisensi (yang di-merge)

            foreach ($licenses as $license) {
                $licensedPcsQuery = $this->db->table('t_licensedetail')
                    ->select('ld_pcnama, ld_assetno, ld_pc_id, ld_serialnumber, ld_employee_code, ld_position_code')
                    ->where('tl_id', $license['id'])
                    ->orderBy('ld_pcnama', 'ASC')
                    ->get();
                $licensedPcs = $licensedPcsQuery->getResultArray();

                $numLicensedPcs = count($licensedPcs);
                $initialRowNum = $rowNum; // Store the starting row for this license

                // Data utama lisensi
                $mainLicenseData = [
                    $license['id'],
                    $license['type'],
                    $license['license_category'],
                    $license['license_partner'],
                    $license['product_name'],
                    $license['po_number'],
                    $license['ref_number'],
                    $license['product_key'],
                    (string)$license['product_desc'], // Ensure string for PhpSpreadsheet
                    (float)$license['product_qty'], // Ensure numeric
                    (string)$license['organization'], // Ensure string
                ];

                if ($numLicensedPcs === 0) {
                    $rowData = array_merge([$nomorUrutLisensi], $mainLicenseData, ['', '', '', '', '', '']);
                    $sheet->fromArray($rowData, NULL, 'A' . $rowNum);

                    // Apply alternating row style first
                    $styleToApply = ($rowNum % 2 === 0) ? $styleEvenRow : $styleOddRow;
                    $sheet->getStyle('A' . $rowNum . ':' . $sheet->getHighestColumn() . $rowNum)->applyFromArray($styleToApply);

                    // Apply specific column colors over the row style
                    // License Data Columns (A-L)
                    $sheet->getStyle('A' . $rowNum)->applyFromArray($defaultColumnStyle); // No.
                    $sheet->getStyle('B' . $rowNum)->applyFromArray($defaultColumnStyle); // License ID
                    $sheet->getStyle('C' . $rowNum)->applyFromArray($defaultColumnStyle); // License Type
                    $sheet->getStyle('D' . $rowNum)->applyFromArray($defaultColumnStyle); // License Category
                    $sheet->getStyle('E' . $rowNum)->applyFromArray($defaultColumnStyle); // License Partner
                    $sheet->getStyle('F' . $rowNum)->applyFromArray($defaultColumnStyle); // Product Name
                    $sheet->getStyle('G' . $rowNum)->applyFromArray($defaultColumnStyle); // PO Number
                    $sheet->getStyle('H' . $rowNum)->applyFromArray($defaultColumnStyle); // Ref. Number / Subs ID
                    $sheet->getStyle('I' . $rowNum)->applyFromArray($defaultColumnStyle); // Product Key
                    $sheet->getStyle('J' . $rowNum)->applyFromArray($defaultColumnStyle); // Product Desc
                    $sheet->getStyle('K' . $rowNum)->applyFromArray($defaultColumnStyle); // Product Qty
                    $sheet->getStyle('L' . $rowNum)->applyFromArray($defaultColumnStyle); // Organization

                    // PC Data Columns (M-S) - these will be empty, but we apply the style for consistency if needed
                    $sheet->getStyle('M' . $rowNum . ':' . 'S' . $rowNum)->applyFromArray($greenColumnStyle);

                    // ** Perubahan: Terapkan gaya sel merah lengkap jika Product Qty < 3 **
                    if ((float)$license['product_qty'] < 3) {
                        $sheet->getStyle('K' . $rowNum)->applyFromArray($redCellStyle);
                    }
                    // ** Akhir Perubahan **

                    $rowNum++;
                } else {
                    $nomorUrutPc = 1;

                    foreach ($licensedPcs as $pcIndex => $pc) {
                        $pcData = [
                            $nomorUrutPc++,
                            (string)$pc['ld_pcnama'], // Ensure string
                            (string)$pc['ld_assetno'], // Ensure string
                            (string)$pc['ld_pc_id'],   // Ensure string
                            (string)$pc['ld_serialnumber'], // Ensure string
                            $employees[$pc['ld_employee_code']] ?? '',
                            $positions[$pc['ld_position_code']] ?? '',
                        ];

                        if ($pcIndex === 0) {
                            $rowData = array_merge([$nomorUrutLisensi], $mainLicenseData, $pcData);
                        } else {
                            // Kosongkan sel lisensi untuk baris PC lanjutan
                            $emptyLicenseData = array_fill(0, count($mainLicenseData) + 1, '');
                            $rowData = array_merge($emptyLicenseData, $pcData);
                        }
                        $sheet->fromArray($rowData, NULL, 'A' . $rowNum);

                        // Apply alternating row style first
                        $styleToApply = ($rowNum % 2 === 0) ? $styleEvenRow : $styleOddRow;
                        $sheet->getStyle('A' . $rowNum . ':' . $sheet->getHighestColumn() . $rowNum)->applyFromArray($styleToApply);

                        // Apply specific column colors over the row style
                        // License Data Columns (A-L)
                        // Perhatikan bahwa untuk baris PC lanjutan, kolom A-L akan kosong,
                        // tetapi kita tetap menerapkan gaya default untuk konsistensi border.
                        $sheet->getStyle('A' . $rowNum)->applyFromArray($defaultColumnStyle); 
                        $sheet->getStyle('B' . $rowNum)->applyFromArray($defaultColumnStyle); 
                        $sheet->getStyle('C' . $rowNum)->applyFromArray($defaultColumnStyle); 
                        $sheet->getStyle('D' . $rowNum)->applyFromArray($defaultColumnStyle); 
                        $sheet->getStyle('E' . $rowNum)->applyFromArray($defaultColumnStyle); 
                        $sheet->getStyle('F' . $rowNum)->applyFromArray($defaultColumnStyle); 
                        $sheet->getStyle('G' . $rowNum)->applyFromArray($defaultColumnStyle); 
                        $sheet->getStyle('H' . $rowNum)->applyFromArray($defaultColumnStyle); 
                        $sheet->getStyle('I' . $rowNum)->applyFromArray($defaultColumnStyle); 
                        $sheet->getStyle('J' . $rowNum)->applyFromArray($defaultColumnStyle); 
                        $sheet->getStyle('K' . $rowNum)->applyFromArray($defaultColumnStyle); 
                        $sheet->getStyle('L' . $rowNum)->applyFromArray($defaultColumnStyle); 

                        // PC Data Columns (M-S)
                        $sheet->getStyle('M' . $rowNum . ':' . 'S' . $rowNum)->applyFromArray($greenColumnStyle);

                        // ** Perubahan: Terapkan gaya sel merah lengkap jika Product Qty < 3 (hanya untuk baris pertama lisensi) **
                        if ($pcIndex === 0 && (float)$license['product_qty'] < 3) {
                            $sheet->getStyle('K' . $rowNum)->applyFromArray($redCellStyle);
                        }
                        // ** Akhir Perubahan **

                        $rowNum++;
                    }

                    // After all PCs for this license are added, merge cells for license details
                    if ($numLicensedPcs > 1) {
                        $startMergeRow = $initialRowNum;
                        $endMergeRow = $rowNum - 1;

                        // Merge cells for 'No.' column (A)
                        $sheet->mergeCells('A' . $startMergeRow . ':' . 'A' . $endMergeRow);
                        $sheet->getStyle('A' . $startMergeRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        // Re-apply style to the merged cell to ensure it retains its color
                        $sheet->getStyle('A' . $startMergeRow)->applyFromArray($defaultColumnStyle);

                        // Merge cells for columns B through L (License ID to Organization)
                        // Re-apply styles to the top-left cell of the merged block for these columns
                        $mergeCols = [
                            'B' => $defaultColumnStyle,    // License ID
                            'C' => $defaultColumnStyle,    // License Type
                            'D' => $defaultColumnStyle,    // License Category
                            'E' => $defaultColumnStyle,    // License Partner
                            'F' => $defaultColumnStyle,     // Product Name
                            'G' => $defaultColumnStyle,    // PO Number
                            'H' => $defaultColumnStyle,    // Ref. Number / Subs ID
                            'I' => $defaultColumnStyle,     // Product Key
                            'J' => $defaultColumnStyle,    // Product Desc
                            'L' => $defaultColumnStyle     // Organization
                        ];

                        foreach ($mergeCols as $col => $style) {
                            $sheet->mergeCells($col . $startMergeRow . ':' . $col . $endMergeRow);
                            $sheet->getStyle($col . $startMergeRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getStyle($col . $startMergeRow)->applyFromArray($style);
                        }
                        
                        // Khusus untuk kolom K (Product Qty), kita perlu menggabungkan dan menerapkan gaya merah
                        // jika Product Qty < 3.
                        $sheet->mergeCells('K' . $startMergeRow . ':' . 'K' . $endMergeRow);
                        $sheet->getStyle('K' . $startMergeRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        
                        // ** Perubahan: Terapkan gaya sel merah lengkap jika Product Qty < 3 setelah merge **
                        if ((float)$license['product_qty'] < 3) {
                            $sheet->getStyle('K' . $startMergeRow)->applyFromArray($redCellStyle);
                        } else {
                            $sheet->getStyle('K' . $startMergeRow)->applyFromArray($defaultColumnStyle);
                        }
                        // ** Akhir Perubahan **
                    }
                }
                $nomorUrutLisensi++;
            }

            // Set column widths
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Add auto filter to header
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
     */
    public function exportExcelById(int $tl_id)
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        try {
            // Fetch main software license record by tl_id
            $license = $this->db->table('t_license')
                                ->select('tl_id AS id, tl_type AS type, tl_license_type_category AS license_category,
                                          UPPER(tl_refnumber) AS ref_number, tl_po_number AS po_number,
                                          UPPER(tl_licensepartner) AS license_partner, tl_orderdate AS order_date,
                                          tl_startdate AS start_date, tl_enddate AS end_date,
                                          UPPER(tl_productname) AS product_name, tl_productdesc AS product_desc,
                                          tl_productqty AS product_qty, UPPER(tl_productkey) AS product_key,
                                          tl_organization AS organization') // Removed tl_last_update, tl_last_user
                                ->where('tl_id', $tl_id)
                                ->get()
                                ->getRowArray();

            if (!$license) {
                return $this->response->setStatusCode(404)
                                      ->setJSON(['error' => true, 'message' => 'Software License record not found for the given ID.']);
            }

            // Fetch licensed PCs for the current main license
            $licensedPcsQuery = $this->db->table('t_licensedetail')
                                         ->select('ld_pcnama, ld_assetno, ld_pc_id, ld_serialnumber, ld_employee_code, ld_position_code, ld_status') // Removed ld_lastupdate, ld_lastuser
                                         ->where('tl_id', $license['id'])
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

            // Header for the entire report
            $sheet->setCellValue('A1', 'SOFTWARE LICENSE CONFIGURATION REPORT');
            $sheet->mergeCells('A1:L1'); // Adjusted range (12 columns for main data)
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 18],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Empty row for spacing
            $sheet->getRowDimension(2)->setRowHeight(15);

            // Main License Details Header
            $sheet->setCellValue('A3', 'Main License Details');
            $sheet->mergeCells('A3:L3'); // Adjusted range
            $sheet->getStyle('A3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'C7D9FE']], // Light Blue
            ]);

            // Main License Data Headers
            $mainHeaders = [
                'ID', 'License Type', 'License Category', 'Ref. Number / Subs ID', 'PO Number',
                'License Partner', 'Order Date', 'Start Date', 'End Date', 'Product Name',
                'Product Qty', 'Product Desc', 'Product Key', 'Organization'
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
            $sheet->getStyle('A' . $mainHeaderStartRow . ':N' . $mainHeaderStartRow)->applyFromArray($headerStyle); // Adjusted range to 'N' for 14 columns

            // Populate main license data
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
            $sheet->getStyle('A' . $mainDataRowStart . ':N' . $mainDataRowStart)->applyFromArray([ // Adjusted range to 'N'
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
            ]);

            // Apply special style if Product Qty is less than 3
            if ((float)$license['product_qty'] < 3) {
                $redCellStyle = [
                    'font' => ['color' => ['rgb' => '000000']], // black font
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF8E88']], // Red background (ARGB format)
                ];
                $sheet->getStyle('K' . $mainDataRowStart)->applyFromArray($redCellStyle); // Column K is Product Qty
            }


            // Empty row for spacing between main and detail
            $sheet->getRowDimension($mainDataRowStart + 1)->setRowHeight(15);

            // Licensed PCs Header
            $pcHeaderStartRow = $mainDataRowStart + 3;
            $sheet->setCellValue('A' . $pcHeaderStartRow, 'Licensed PCs');
            $sheet->mergeCells('A' . $pcHeaderStartRow . ':H' . $pcHeaderStartRow); // Adjusted range for PC headers (A-H)
            $sheet->getStyle('A' . $pcHeaderStartRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'D1FEB8']], // Light Green
            ]);

            // PC Data Headers
            $pcHeaders = [
                'No.', 'PC Asset Name', 'PC Asset Number', 'PC Asset ID', 'PC Asset Serial Number',
                'User', 'Position', 'Status' // Removed Last Update, Last User
            ];
            $pcDataHeaderRow = $pcHeaderStartRow + 1;
            $sheet->fromArray($pcHeaders, NULL, 'A' . $pcDataHeaderRow);

            // Apply style to PC header
            $sheet->getStyle('A' . $pcDataHeaderRow . ':H' . $pcDataHeaderRow)->applyFromArray($headerStyle); // Adjusted range to 'H' for 8 columns

            $rowNum = $pcDataHeaderRow + 1;
            $pcNo = 1;
            $overlicensedFill = ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFE0B2']]; // Light orange for overlicensed/inactive

            $productQty = (float)$license['product_qty']; // Ensure this is a float

            if (empty($licensedPcs)) {
                // If no PCs, add a single row indicating no data
                $rowData = [
                    '', '', '', '', '', '', '', 'No PCs Licensed' // Now 8 columns
                ];
                $sheet->fromArray($rowData, NULL, 'A' . $rowNum);
                $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray([ // Adjusted range to 'H'
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
                    'font' => ['italic' => true, 'color' => ['argb' => 'FF808080']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->mergeCells('A' . $rowNum . ':H' . $rowNum); // Adjusted range to 'H'
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
                        $pc['ld_status'] == 1 ? 'Active' : 'Inactive'
                    ];
                    $sheet->fromArray($rowData, NULL, 'A' . $rowNum);

                    // Apply alternating row style
                    $styleToApply = ($pcIndex % 2 === 0) ? ['fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFF0F0F0']]] : ['fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFFFFFFF']]];
                    $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray($styleToApply); // Adjusted range to 'H'
                    $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]]]); // Adjusted range to 'H'

                    // Apply special style for overlicensed PCs (if their index exceeds product_qty)
                    // and for Inactive status
                    if ($pcIndex >= $productQty || $pc['ld_status'] == 0) {
                        $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->applyFromArray($overlicensedFill);
                    }

                    $rowNum++;
                }
            }

            // Set column widths
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