<?php

namespace App\Controllers\TransPC;

use App\Controllers\BaseController;
use App\Models\transpc\TransPCModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use DateTime;

class TransPCController extends BaseController
{
    protected $TransPCModel;

    public function __construct()
    {
        $this->TransPCModel = new TransPCModel();
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }
                                        
        $usermenu = session()->get("usermenu");
                                      
        $activeMenuGroup = 'Transaction';
        $activeMenuName = 'PC';
                                        
        if ($usermenu) { // Tambahkan pengecekan null untuk $usermenu
            foreach ($usermenu as $menu) {
                if (isset($menu->umn_path) && $menu->umn_path === "TransPC") { // Tambahkan isset
                    $activeMenuGroup = $menu->umg_groupname ?? 'Transaction'; // Fallback jika groupname tidak ada
                    $activeMenuName = $menu->umn_menuname ?? 'PC'; // Fallback jika menuname tidak ada
                    break;
                }
            }
        }
                                        
        $data = [
            "active_menu_group" => $activeMenuGroup,
            "active_menu_name" => $activeMenuName
        ];
                                        
        return view('transaction/TransPC/index', $data);
    }

    public function getData()
    {
        try {
            $statusFilter = $this->request->getGet('status');
            $typeFilter = $this->request->getGet('type');
            
            // Perbaikan: Pastikan getData() dari model selalu mengembalikan array
            $pcs = $this->TransPCModel->getData($statusFilter, $typeFilter);
            
            if (!is_array($pcs)) {
                // Ini seharusnya tidak terjadi jika model ditangani dengan baik, tapi sebagai fallback
                throw new \Exception("Model returned non-array data.");
            }

            $formattedData = [];
            foreach ($pcs as $pc) {
                // Pastikan properti objek ada sebelum diakses, gunakan null coalescing operator untuk nilai default
                $formattedData[] = [
                    'tpc_id'            => $pc->tpc_id ?? null,
                    'tpc_type'          => $pc->tpc_type ?? null,
                    'tpc_name'          => $pc->tpc_name ?? null,
                    'tpc_assetno'       => $pc->tpc_assetno ?? null,
                    'tpc_pcreceivedate' => $pc->tpc_pcreceivedate ?? null,
                    'tpc_osname'        => $pc->tpc_osname ?? null,
                    'tpc_ipaddress'     => $pc->tpc_ipaddress ?? null,
                    'tpc_user'          => $pc->tpc_user ?? null,
                    'tpc_location'      => $pc->tpc_location ?? null,
                    'tpc_status'        => $pc->tpc_status ?? null,
                    // tpc_lastuser tidak dikirim langsung, tapi display name-nya
                    'tpc_lastuser'      => $this->getUserDisplayName($pc->tpc_lastuser ?? null), // Pastikan mengirim nilai yang valid
                    'tpc_lastupdate'    => $pc->tpc_lastupdate ?? null,
                ];
            }
                                        
            return $this->response->setJSON($formattedData);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getData: ' . $e->getMessage());
            // Return empty array and 500 status code
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error loading data. Please try again.']);
        }
    }


    // Fungsi-fungsi lain (getPCById, store, update, delete, dll.) tidak diubah kecuali penambahan try-catch
    // dan penyesuaian untuk null coalescing operator untuk akses properti.
    // Tambahkan try-catch di setiap fungsi controller jika belum ada untuk menangani error model
    // atau masalah lain yang mungkin muncul saat pemrosesan.

    public function getPCById()
    {
        try {
            $id = $this->request->getGet('id');
                                            
            if (empty($id)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'PC ID is required'
                ]);
            }
                                            
            $pc = $this->TransPCModel->getPCById($id);
                                
            if ($pc) {
                $formattedPC = [
                    'tpc_id'            => $pc->tpc_id ?? null,
                    'tpc_type'          => $pc->tpc_type ?? null,
                    'tpc_name'          => $pc->tpc_name ?? null,
                    'tpc_assetno'       => $pc->tpc_assetno ?? null,
                    'tpc_pcreceivedate' => $pc->tpc_pcreceivedate ?? null,
                    'tpc_osname'        => $pc->tpc_osname ?? null,
                    'tpc_ipaddress'     => $pc->tpc_ipaddress ?? null,
                    'tpc_user'          => $pc->tpc_user ?? null,
                    'tpc_location'      => $pc->tpc_location ?? null,
                    'tpc_status'        => $pc->tpc_status ?? null,
                    'tpc_lastupdate'    => $pc->tpc_lastupdate ?? null,
                    'tpc_lastuser'      => $this->getUserDisplayName($pc->tpc_lastuser ?? null)
                ];
                                             
                return $this->response->setJSON([
                    'status' => true,
                    'data' => $formattedPC
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'PC not found'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getPCById: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error retrieving PC details.']);
        }
    }

    public function store()
    {
        try {
            $data = $this->request->getPost();
            
            // Validate input
            if (empty($data['pc_type']) || empty($data['pc_receive_date']) || !isset($data['pcstatus'])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'PC Type, PC Receive Date, and Status are required.'
                ]);
            }
                                        
            $result = $this->TransPCModel->storeData($data);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::store: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error saving PC data.']);
        }
    }

    public function update()
    {
        try {
            $data = $this->request->getPost();
            
            // Validate input
            if (empty($data['tpc_id']) || empty($data['pc_type']) || empty($data['pc_receive_date']) || !isset($data['pcstatus'])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'PC ID, PC Type, PC Receive Date, and Status are required.'
                ]);
            }
                                        
            $result = $this->TransPCModel->updateData($data);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::update: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error updating PC data.']);
        }
    }

    public function delete()
    {
        try {
            $id = $this->request->getPost('id');
            if (empty($id)) {
                return $this->response->setJSON(['status' => false, 'message' => 'ID not found']);
            }
                                            
            $result = $this->TransPCModel->deleteData($id);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::delete: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error deleting PC data.']);
        }
    }

    public function searchAssetNo()
    {
        try {
            $assetNo = $this->TransPCModel->searchAssetNo();
            
            // Process the data to ensure display_asset_no is used consistently
            $processedData = [];
            foreach ($assetNo as $asset) {
                $processedAsset = (object)[
                    'e_assetno'         => $asset->e_assetno ?? null,
                    'e_equipmentid'     => $asset->e_equipmentid ?? null,
                    'e_serialnumber'    => $asset->e_serialnumber ?? null,
                    'e_equipmentname'   => $asset->e_equipmentname ?? null,
                    'e_receivedate'     => $asset->e_receivedate ?? null,
                    'display_asset_no'  => $asset->display_asset_no ?? null // Already handled by SQL COALESCE
                ];
                $processedData[] = $processedAsset;
            }
            
            return $this->response->setJSON($processedData);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::searchAssetNo: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([]); // Return empty array on error
        }
    }

    public function getAssetNo()
    {
        try {
            $assetNo = $this->request->getGet('assetNo');

            if (empty($assetNo)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Asset No is required'
                ]);
            }

            $assetDetails = $this->TransPCModel->getAssetNo($assetNo);

            if ($assetDetails) {
                return $this->response->setJSON([
                    'status' => true,
                    'data' => [
                        'asset_name' => $assetDetails->e_equipmentname ?? null,
                        'equipment_id' => $assetDetails->e_equipmentid ?? null,
                        'receive_date' => $assetDetails->formatted_receive_date ?? ($assetDetails->e_receivedate ? date('Y-m-d', strtotime($assetDetails->e_receivedate)) : null),
                        'display_asset_no' => $assetDetails->display_asset_no ?? $assetDetails->e_assetno ?? null,
                        'original_assetno' => $assetDetails->e_assetno ?? null
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Asset not found or already in use'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getAssetNo: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error retrieving asset details.']);
        }
    }

    public function getIPAddresses()
    {
        try {
            $ipAddress = $this->request->getGet('ipAddress');
                                                            
            if (empty($ipAddress)) {
                return $this->response->setJSON(['status' => false, 'message' => 'IP Address is required']);
            }
                                                            
            $ip = $this->TransPCModel->getIPAddressByIP($ipAddress);
                                                            
            return $this->response->setJSON([
                'status' => ($ip !== null),
                'data' => $ip
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getIPAddresses: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error retrieving IP address.']);
        }
    }

    public function searchIPAddresses()
    {
        try {
            $ipAddresses = $this->TransPCModel->searchIPAddresses();
            
            // Pastikan data yang dikembalikan adalah array
            if (!is_array($ipAddresses)) {
                // Log kesalahan jika model mengembalikan data non-array meskipun sudah diharapkan array
                log_message('error', 'TransPCModel::searchIPAddresses did not return an array as expected.');
                // Dalam kasus ini, kita akan mengembalikan array kosong agar DataTables tidak error
                return $this->response->setJSON([]);
            }

            // DataTables biasanya mengharapkan array of objects/arrays sebagai 'data'
            // Jika model sudah mengembalikan format yang benar, langsung kirim.
            return $this->response->setJSON($ipAddresses);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::searchIPAddresses: ' . $e->getMessage());
            // Mengembalikan JSON dengan pesan error dan status 500
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error loading IP addresses. Please try again.']);
        }
    }


    public function updateIPStatus()
    {
        $ipAddress = $this->request->getPost('ipAddress');
        $status = $this->request->getPost('status');
        
        if (empty($ipAddress)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'IP Address is required'
            ]);
        }
        
        try {
            $result = $this->TransPCModel->updateIPStatus($ipAddress, $status);
            return $this->response->setJSON([
                'status' => $result,
                'message' => $result ? 'IP status updated successfully' : 'Failed to update IP status'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error updating IP status: ' . $e->getMessage()
            ]);
        }
    }

    public function getEmployees()
    {
        try {
            $employeeId = $this->request->getGet('employeeId');
                                                            
            if (empty($employeeId)) {
                return $this->response->setJSON(['status' => false, 'message' => 'Employee ID is required']);
            }
                                                            
            $employee = $this->TransPCModel->getEmployeeById($employeeId);
                                                            
            if ($employee) {
                return $this->response->setJSON(['status' => true, 'data' => $employee]);
            } else {
                return $this->response->setJSON(['status' => false, 'message' => 'Employee not found']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getEmployees: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error retrieving employee data.']);
        }
    }

    public function searchEmployees()
    {
        try {
            $employees = $this->TransPCModel->searchEmployees();
            return $this->response->setJSON($employees);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::searchEmployees: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([]); // Return empty array on error
        }
    }

    public function getLocations()
    {
        try {
            $locations = $this->TransPCModel->getLocations();
            return $this->response->setJSON($locations);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getLocations: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([]); // Return empty array on error
        }
    }

    public function getOSList()
    {
        try {
            $osList = $this->TransPCModel->getOSList();
            return $this->response->setJSON($osList);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getOSList: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([]); // Return empty array on error
        }
    }

    private function getUserDisplayName($userId)
    {
        if (empty($userId)) {
            return 'N/A';
        }

        $dbCommon = \Config\Database::connect('jincommon');
        
        try {
            // First, try to get username from tbua_useraccess using ua_userid
            $userAccess = $dbCommon->table('tbua_useraccess')
                                    ->select('ua_username')
                                    ->where('ua_userid', $userId) // Use ua_userid for lookup
                                    ->get()
                                    ->getRow();

            if ($userAccess) {
                return $userAccess->ua_username;
            }

            // Fallback: If not found in useraccess by ua_userid, try employee table using em_emplcode
            $employee = $dbCommon->table('tbmst_employee')
                                 ->select('em_emplname')
                                 ->where('em_emplcode', $userId) // Try matching with em_emplcode
                                 ->get()
                                 ->getRow();

            if ($employee) {
                return $employee->em_emplname;
            }

            return $userId; // Return ID if not found in either table
        } catch (\Exception $e) {
            log_message('error', 'Error in getUserDisplayName for userId ' . $userId . ': ' . $e->getMessage());
            return 'Error User'; // Return a more informative error string
        }
    }

    private function calculateAge($receiveDate)
    {
        if (empty($receiveDate)) {
            return '-';
        }

        try {
            $receive = new DateTime($receiveDate);
            $today = new DateTime();

            if ($receive > $today) {
                return 'Future date';
            }

            $diff = $today->diff($receive);
            $years = $diff->y;
            $months = $diff->m;
            $days = $diff->d;

            if ($years == 0 && $months == 0) {
                if ($days == 0) {
                    return "Today";
                } else if ($days == 1) {
                    return "1 day";
                } else {
                    return $days . " days";
                }
            } else if ($years == 0) {
                if ($months == 1) {
                    return "1 month";
                } else {
                    return $months . " months";
                }
            } else if ($months == 0) {
                if ($years == 1) {
                    return "1 year";
                } else {
                    return $years . " years";
                }
            } else {
                $result = $years . " year" . ($years > 1 ? "s" : "");
                $result .= " " . $months . " month" . ($months > 1 ? "s" : "");
                return $result;
            }
        } catch (\Exception $e) {
            log_message('error', 'Error calculating age for date ' . $receiveDate . ': ' . $e->getMessage());
            return 'Invalid date';
        }
    }

    public function getPCDetails()
    {
        try {
            $id = $this->request->getGet('id');
            
            if (empty($id)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'PC ID is required'
                ]);
            }
            
            $details = $this->TransPCModel->getPCFullDetails($id);
            
            if ($details) {
                // Format PC data
                $pc = $details['pc'];
                $formattedPC = [
                    'tpc_id'            => $pc->tpc_id ?? null,
                    'tpc_type'          => $pc->tpc_type ?? null,
                    'tpc_name'          => $pc->tpc_name ?? null,
                    'tpc_assetno'       => $pc->tpc_assetno ?? null,
                    'tpc_pcreceivedate' => $pc->tpc_pcreceivedate ?? null,
                    'tpc_osname'        => $pc->tpc_osname ?? null,
                    'tpc_ipaddress'     => $pc->tpc_ipaddress ?? null,
                    'tpc_user'          => $pc->tpc_user ?? null,
                    'tpc_location'      => $pc->tpc_location ?? null,
                    'tpc_status'        => $pc->tpc_status ?? null,
                    'tpc_lastuser'      => $this->getUserDisplayName($pc->tpc_lastuser ?? null),
                    'tpc_lastupdate'    => $pc->tpc_lastupdate ?? null
                ];
                
                // Format specs data
                $formattedSpecs = null;
                if ($details['specs']) {
                    $specs = $details['specs'];
                    $formattedSpecs = [
                        'tps_id'        => $specs->tps_id ?? null,
                        'tps_processor' => $specs->tps_processor ?? null,
                        'tps_ram'       => $specs->tps_ram ?? null,
                        'tps_storage'   => $specs->tps_storage ?? null,
                        'tps_vga'       => $specs->tps_vga ?? null,
                        'tps_ethernet'  => $specs->tps_ethernet ?? null,
                        'tps_lastuser'  => $this->getUserDisplayName($specs->tps_lastuser ?? null),
                        'tps_lastupdate' => $specs->tps_lastupdate ?? null
                    ];
                }
                
                // Format equipment data
                $formattedEquipment = [];
                foreach ($details['equipment'] as $equipment) {
                    $formattedEquipment[] = [
                        'tpi_id'          => $equipment->tpi_id ?? null,
                        'tpi_type'        => $equipment->tpi_type ?? null,
                        'tpi_assetno'     => $equipment->tpi_assetno ?? null,
                        'tpi_receivedate' => $equipment->tpi_receivedate ?? null,
                        'tpi_lastuser'    => $this->getUserDisplayName($equipment->tpi_lastuser ?? null),
                        'tpi_lastupdate'  => $equipment->tpi_lastupdate ?? null
                    ];
                }

                // Format Server VM data
                $formattedServerVM = [];
                foreach ($details['servervm'] as $vm) {
                    $formattedServerVM[] = [
                        'tpv_id'        => $vm->tpv_id ?? null,
                        'tpv_name'      => $vm->tpv_name ?? null,
                        'tpv_processor' => $vm->tpv_processor ?? null,
                        'tpv_ram'       => $vm->tpv_ram ?? null,
                        'tpv_storage'   => $vm->tpv_storage ?? null,
                        'tpv_vga'       => $vm->tpv_vga ?? null,
                        'tpv_ethernet'  => $vm->tpv_ethernet ?? null,
                        'tpv_ipaddress' => $vm->tpv_ipaddress ?? null,
                        'tpv_services'  => $vm->tpv_services ?? null,
                        'tpv_remark'    => $vm->tpv_remark ?? null,
                        'tpv_status'    => $vm->tpv_status ?? null,
                        'tpv_lastuser'  => $this->getUserDisplayName($vm->tpv_lastuser ?? null),
                        'tpv_lastupdate' => $vm->tpv_lastupdate ?? null
                    ];
                }
                
                return $this->response->setJSON([
                    'status' => true,
                    'data' => [
                        'pc' => $formattedPC,
                        'specs' => $formattedSpecs,
                        'equipment' => $formattedEquipment,
                        'servervm' => $formattedServerVM
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'PC not found'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getPCDetails: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error retrieving PC full details.']);
        }
    }

    public function getSpecsByPC()
    {
        try {
            $pcId = $this->request->getGet('pcId');
            $specs = $this->TransPCModel->getPCSpecs($pcId);
            
            if ($specs) {
                return $this->response->setJSON([
                    'status' => true,
                    'data' => [
                        'tps_id'    => $specs->tps_id ?? null,
                        'processor' => $specs->tps_processor ?? null,
                        'ram'       => $specs->tps_ram ?? null,
                        'storage'   => $specs->tps_storage ?? null,
                        'vga'       => $specs->tps_vga ?? null,
                        'ethernet'  => $specs->tps_ethernet ?? null // Add ethernet here
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'No specifications found'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getSpecsByPC: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error retrieving PC specs.']);
        }
    }

    public function saveSpecs() // This function is not used, updatePCSpecs is used instead
    {
        // This function seems to be redundant. updatePCSpecs is likely intended to be used for both insert/update.
        // Consider removing this if not explicitly used, or rename updatePCSpecs to savePCSpecs if it handles both.
        return $this->response->setJSON(['status' => false, 'message' => 'This method is deprecated or not intended for direct use.']);
    }

    public function getEquipmentById()
    {
        try {
            $eqptId = $this->request->getGet('id');
            $equipment = $this->TransPCModel->getPCEquipmentById($eqptId);
            
            if ($equipment) {
                return $this->response->setJSON([
                    'status' => true,
                    'data' => [
                        'tpi_id'          => $equipment->tpi_id ?? null,
                        'tpi_type'        => $equipment->tpi_type ?? null,
                        'tpi_pcid'        => $equipment->tpi_pcid ?? null,
                        'tpi_assetno'     => $equipment->tpi_assetno ?? null,
                        'tpi_receivedate' => $equipment->tpi_receivedate ?? null
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Equipment not found'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getEquipmentById: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error retrieving equipment details.']);
        }
    }

    public function saveEquipment() // This function is not used, storePCEquipment/updatePCEquipment are used
    {
        // This function seems to be redundant. storePCEquipment/updatePCEquipment are likely intended to be used.
        return $this->response->setJSON(['status' => false, 'message' => 'This method is deprecated or not intended for direct use.']);
    }


    public function deleteEquipment()
    {
        try {
            $eqptId = $this->request->getPost('id');
            $result = $this->TransPCModel->deletePCEquipment($eqptId);
            
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::deleteEquipment: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error deleting equipment.']);
        }
    }

    // PC Specifications CRUD Methods
    public function updatePCSpecs()
    {
        try {
            $data = $this->request->getPost();
            
            // Validasi field yang diperlukan
            if (empty($data['pc_id'])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'PC ID is required.'
                ]);
            }
            
            $model = new TransPCModel(); // Re-instantiate model (or use $this->TransPCModel if already loaded)
            
            // Cek apakah spesifikasi sudah ada
            $existingSpecs = $model->getPCSpecs($data['pc_id']);

            // Jika spesifikasi sudah ada: lakukan UPDATE
            // Jika belum ada: lakukan INSERT sebagai UPDATE (logic di model updatePCSpecs)
            $result = $model->updatePCSpecs($data, !empty($existingSpecs));
            
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::updatePCSpecs: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error saving PC specifications.']);
        }
    }

    // PC IT Equipment CRUD Methods
    public function storePCEquipment()
    {
         try {
             $data = $this->request->getPost();
             
             // Validate required fields
             if (empty($data['pc_id']) || !isset($data['equipment_type']) || $data['equipment_type'] === '') { // Added explicit check for empty string
                 return $this->response->setJSON([
                     'status' => false,
                     'message' => 'PC ID and Equipment Type are required.'
                 ]);
             }
             
             $result = $this->TransPCModel->storePCEquipment($data);
             return $this->response->setJSON($result);
         } catch (\Exception $e) {
             log_message('error', 'Error in TransPCController::storePCEquipment: ' . $e->getMessage());
             return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error storing PC equipment.']);
         }
    }
 


    public function updatePCEquipment()
    {
        try {
            $data = $this->request->getPost();
            
            // Validate required fields
            if (empty($data['equipment_id']) || empty($data['pc_id']) || !isset($data['equipment_type']) || $data['equipment_type'] === '') { // Added explicit check for empty string
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Equipment ID, PC ID, and Equipment Type are required.'
                ]);
            }
            
            $result = $this->TransPCModel->updatePCEquipment($data);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::updatePCEquipment: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error updating PC equipment.']);
        }
    }

    public function deletePCEquipment()
    {
        try {
            $id = $this->request->getPost('id');
            
            if (empty($id)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Equipment ID is required.'
                ]);
            }
            
            $result = $this->TransPCModel->deletePCEquipment($id);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::deletePCEquipment: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error deleting PC equipment.']);
        }
    }

    public function getPCEquipmentById()
    {
        try {
            $id = $this->request->getGet('id');
            
            if (empty($id)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Equipment ID is required.'
                ]);
            }
            
            $equipment = $this->TransPCModel->getPCEquipmentById($id);
            
            if ($equipment) {
                // Get user display name
                $lastUser = $this->getUserDisplayName($equipment->tpi_lastuser ?? null);
                
                $formattedEquipment = [
                    'tpi_id'          => $equipment->tpi_id ?? null,
                    'tpi_type'        => $equipment->tpi_type ?? null,
                    'tpi_pcid'        => $equipment->tpi_pcid ?? null,
                    'tpi_assetno'     => $equipment->tpi_assetno ?? null,
                    'tpi_receivedate' => $equipment->tpi_receivedate ?? null,
                    'tpi_status'      => $equipment->tpi_status ?? null,
                    'tpi_lastuser'    => $lastUser,
                    'tpi_lastupdate'  => $equipment->tpi_lastupdate ?? null
                ];
                
                return $this->response->setJSON([
                    'status' => true,
                    'data' => $formattedEquipment
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Equipment not found.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getPCEquipmentById: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error retrieving equipment details.']);
        }
    }

    public function storePCServerVM()
    {
        try {
            $data = $this->request->getPost();
            
            if (empty($data['pc_id']) || empty($data['vm_name'])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'PC ID and VM Name are required.'
                ]);
            }
            
            $result = $this->TransPCModel->storePCServerVM($data);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::storePCServerVM: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error storing Server VM.']);
        }
    }

    public function updatePCServerVM()
    {
        try {
            $data = $this->request->getPost();
            
            if (empty($data['vm_id']) || empty($data['pc_id']) || empty($data['vm_name'])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'VM ID, PC ID, and VM Name are required.'
                ]);
            }
            
            $result = $this->TransPCModel->updatePCServerVM($data);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::updatePCServerVM: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error updating Server VM.']);
        }
    }

    public function deletePCServerVM()
    {
        try {
            $id = $this->request->getPost('id');
            
            if (empty($id)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'VM ID is required.'
                ]);
            }
            
            $result = $this->TransPCModel->deletePCServerVM($id);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::deletePCServerVM: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error deleting Server VM.']);
        }
    }

    public function getPCServerVMById()
    {
        try {
            $id = $this->request->getGet('id');
            
            if (empty($id)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'VM ID is required.'
                ]);
            }
            
            $serverVM = $this->TransPCModel->getPCServerVMById($id);
            
            if ($serverVM) {
                // Get user display name
                $lastUser = $this->getUserDisplayName($serverVM->tpv_lastuser ?? null);
                
                $formattedServerVM = [
                    'tpv_id'        => $serverVM->tpv_id ?? null,
                    'tpv_pcid'      => $serverVM->tpv_pcid ?? null,
                    'tpv_name'      => $serverVM->tpv_name ?? null,
                    'tpv_processor' => $serverVM->tpv_processor ?? null,
                    'tpv_ram'       => $serverVM->tpv_ram ?? null,
                    'tpv_storage'   => $serverVM->tpv_storage ?? null,
                    'tpv_vga'       => $serverVM->tpv_vga ?? null,
                    'tpv_ethernet'  => $serverVM->tpv_ethernet ?? null,
                    'tpv_ipaddress' => $serverVM->tpv_ipaddress ?? null,
                    'tpv_services'  => $serverVM->tpv_services ?? null,
                    'tpv_remark'    => $serverVM->tpv_remark ?? null,
                    'tpv_status'    => $serverVM->tpv_status ?? null,
                    'tpv_lastuser'  => $lastUser,
                    'tpv_lastupdate' => $serverVM->tpv_lastupdate ?? null
                ];
                
                return $this->response->setJSON([
                    'status' => true,
                    'data' => $formattedServerVM
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Server VM not found.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in TransPCController::getPCServerVMById: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Error retrieving Server VM details.']);
        }
    }

    public function exportXLSX()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        try {
            $pcs = $this->TransPCModel->getData('All'); // Get all data
            // Filtering status should be done in model's getData if possible for efficiency
            // If the status filtering needs to happen *after* fetching all, keep this:
            $pcs = array_filter($pcs, function($item) {
                return ($item->tpc_status == 0 || $item->tpc_status == 1);
            });

            if (empty($pcs)) {
                // Handle case where no data is found after filtering
                throw new \Exception("No active or inactive PC data available for export.");
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("PC Management System")
                ->setTitle("PC Data Export")
                ->setDescription("Export of PC data in XLSX format");

            // Set title
            $sheet->mergeCells('B2:AC2'); // Adjusted to new max column 'AC'
            $sheet->setCellValue('B2', 'PC Data with Specifications, Equipment & Server VM');
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('B2:AC2')->applyFromArray($titleStyle);

            // Define and set headers with new columns
            $headers = [
                'B4'  => 'ID',
                'C4'  => 'Type',
                'D4'  => 'PC Name',
                'E4'  => 'Asset No',
                'F4'  => 'Asset Age',
                'G4'  => 'OS Name',
                'H4'  => 'IP Address',
                'I4'  => 'User',
                'J4'  => 'Location',
                'K4'  => 'Status',
                'L4'  => 'Processor',
                'M4'  => 'RAM',
                'N4'  => 'Storage',
                'O4'  => 'VGA',
                'P4'  => 'Ethernet',
                'Q4'  => 'VM Name',
                'R4'  => 'VM Processor',
                'S4'  => 'VM RAM',
                'T4'  => 'VM Storage',
                'U4'  => 'VM VGA',
                'V4'  => 'VM Ethernet',
                'W4'  => 'VM IP',
                'X4'  => 'VM Services',
                'Y4'  => 'VM Remark',
                'Z4'  => 'Equipment Type',
                'AA4' => 'Equipment Asset',
                'AB4' => 'Equipment Age',
                'AC4' => 'Last User'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Style headers
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('B4:AC4')->applyFromArray($headerStyle);

            // Add data
            $row = 5;
            foreach ($pcs as $pc) {
                // Ensure tpc_id is not null before using it
                $pcId = $pc->tpc_id ?? null;
                if ($pcId === null) {
                    log_message('warning', 'Skipping PC record with null tpc_id during export.');
                    continue; // Skip this record if ID is null
                }

                $lastUser = $this->getUserDisplayName($pc->tpc_lastuser ?? null);
                $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
                $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';

                // Get PC type display name
                $pcTypeNames = [
                    1 => 'Client',
                    2 => 'Server'
                ];
                $pcTypeName = $pcTypeNames[$pc->tpc_type ?? 0] ?? 'Unknown';

                // Get PC specifications with new ethernet field
                $specs = $this->TransPCModel->getPCSpecs($pcId); // Pass $pcId
                $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
                $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
                $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
                $vga = $specs ? ($specs->tps_vga ?? '-') : '-';
                $ethernet = $specs ? ($specs->tps_ethernet ?? '-') : '-';

                // Get IT Equipment and Server VM using the PC ID
                $equipment = $this->TransPCModel->getPCEquipment($pcId);
                $serverVM = $this->TransPCModel->getPCServerVM($pcId);

                // Calculate total rows needed for this PC
                $equipmentCount = $equipment ? count($equipment) : 0;
                $vmCount = $serverVM ? count($serverVM) : 0;
                $maxRows = max($equipmentCount, $vmCount, 1); // At least 1 row

                $startRow = $row;
                
                // Fill data for each row
                for ($i = 0; $i < $maxRows; $i++) {
                    // PC basic data (only on first row)
                    if ($i == 0) {
                        $sheet->setCellValue('B' . $row, $pc->tpc_id);
                        $sheet->setCellValue('C' . $row, $pcTypeName);
                        $sheet->setCellValue('D' . $row, $pc->tpc_name ?? '-');
                        $sheet->setCellValue('E' . $row, $pc->tpc_assetno ?? '-');
                        $sheet->setCellValue('F' . $row, $pcAge);
                        $sheet->setCellValue('G' . $row, $pc->tpc_osname ?? '-');
                        $sheet->setCellValue('H' . $row, $pc->tpc_ipaddress ?? '-');
                        $sheet->setCellValue('I' . $row, $pc->tpc_user ?? '-');
                        $sheet->setCellValue('J' . $row, $pc->tpc_location ?? '-');
                        $sheet->setCellValue('K' . $row, ($pc->tpc_status == 1 ? 'Active' : ($pc->tpc_status == 0 ? 'Inactive' : 'Unknown'))); // Handle status 0/1 correctly
                        $sheet->setCellValue('L' . $row, $processor);
                        $sheet->setCellValue('M' . $row, $ram);
                        $sheet->setCellValue('N' . $row, $storage);
                        $sheet->setCellValue('O' . $row, $vga);
                        $sheet->setCellValue('P' . $row, $ethernet);
                        $sheet->setCellValue('AC' . $row, $lastUser);
                    } else {
                        // Clear PC-specific columns for merged rows
                        $clearPccolumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'AC'];
                        foreach ($clearPccolumns as $col) {
                            $sheet->setCellValue($col . $row, '');
                        }
                    }

                    // Server VM data with new fields
                    if ($i < $vmCount && isset($serverVM[$i])) {
                        $vm = $serverVM[$i];
                        $sheet->setCellValue('Q' . $row, $vm->tpv_name ?? '-');
                        $sheet->setCellValue('R' . $row, $vm->tpv_processor ?? '-');
                        $sheet->setCellValue('S' . $row, $vm->tpv_ram ?? '-');
                        $sheet->setCellValue('T' . $row, $vm->tpv_storage ?? '-');
                        $sheet->setCellValue('U' . $row, $vm->tpv_vga ?? '-');
                        $sheet->setCellValue('V' . $row, $vm->tpv_ethernet ?? '-');
                        $sheet->setCellValue('W' . $row, $vm->tpv_ipaddress ?? '-');
                        $sheet->setCellValue('X' . $row, $vm->tpv_services ?? '-');
                        $sheet->setCellValue('Y' . $row, $vm->tpv_remark ?? '-');
                    } else {
                        $sheet->setCellValue('Q' . $row, '-');
                        $sheet->setCellValue('R' . $row, '-');
                        $sheet->setCellValue('S' . $row, '-');
                        $sheet->setCellValue('T' . $row, '-');
                        $sheet->setCellValue('U' . $row, '-');
                        $sheet->setCellValue('V' . $row, '-');
                        $sheet->setCellValue('W' . $row, '-');
                        $sheet->setCellValue('X' . $row, '-');
                        $sheet->setCellValue('Y' . $row, '-');
                    }

                    // Equipment data
                    if ($i < $equipmentCount && isset($equipment[$i])) {
                        $eq = $equipment[$i];
                        $eqType = '';
                        switch($eq->tpi_type ?? 0) { // Add null coalescing for safety
                            case 1: $eqType = 'Monitor'; break;
                            case 2: $eqType = 'Printer'; break;
                            case 3: $eqType = 'Scanner'; break;
                            default: $eqType = 'Unknown'; break;
                        }
                        $eqAge = $eq->tpi_receivedate ? $this->calculateAge($eq->tpi_receivedate) : '-';
                        
                        $sheet->setCellValue('Z' . $row, $eqType);
                        $sheet->setCellValue('AA' . $row, $eq->tpi_assetno ?? '-');
                        $sheet->setCellValue('AB' . $row, $eqAge);
                    } else {
                        $sheet->setCellValue('Z' . $row, '-');
                        $sheet->setCellValue('AA' . $row, '-');
                        $sheet->setCellValue('AB' . $row, '-');
                    }

                    $row++;
                }

                // Merge cells for PC basic data if there are multiple rows
                if ($maxRows > 1) {
                    $endRow = $startRow + $maxRows - 1;
                    
                    // Merge PC basic data columns including new ethernet column
                    $mergeColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'AC'];
                    foreach ($mergeColumns as $col) {
                        $sheet->mergeCells($col . $startRow . ':' . $col . $endRow);
                        // Center align merged cells vertically
                        $sheet->getStyle($col . $startRow . ':' . $col . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }
                }
            }

            // Auto-size columns
            $columns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC'];
            foreach ($columns as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Style data rows
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ];
            // Ensure $row - 1 is not less than 4 (header row)
            $lastDataRow = max(4, $row - 1);
            $sheet->getStyle('B5:AC' . $lastDataRow)->applyFromArray($dataStyle);

            // Create writer and output
            $writer = new Xlsx($spreadsheet);

            $filename = 'PC_Data_Complete_' . date('Y-m-d_H-i-s') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Error exporting XLSX: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ]);
        }
    }

    public function exportCSV()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        try {
            $pcs = $this->TransPCModel->getData('All');
            $pcs = array_filter($pcs, function($item) {
                return ($item->tpc_status == 0 || $item->tpc_status == 1);
            });
            
            if (empty($pcs)) {
                throw new \Exception("No active or inactive PC data available for export.");
            }
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Define headers with new columns
            $headers = [
                'ID', 'Type', 'PC Name', 'Asset No', 'Asset Age',
                'OS Name', 'IP Address', 'User', 'Location', 'Status',
                'Processor', 'RAM', 'Storage', 'VGA', 'Ethernet',
                'VM Name', 'VM Processor', 'VM RAM', 'VM Storage', 'VM VGA', 'VM Ethernet',
                'VM IP', 'VM Services', 'VM Remark',
                'Equipment Type', 'Equipment Asset', 'Equipment Age', 'Last User'
            ];
            
            // Set headers
            $sheet->fromArray($headers, NULL, 'A1'); // Simpler way to set headers
            
            // Add data
            $row = 2;
            foreach ($pcs as $pc) {
                $pcId = $pc->tpc_id ?? null;
                if ($pcId === null) {
                    log_message('warning', 'Skipping PC record with null tpc_id during CSV export.');
                    continue;
                }

                $lastUser = $this->getUserDisplayName($pc->tpc_lastuser ?? null);
                $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
                $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';
                
                // Get PC type display name
                $pcTypeNames = [
                    1 => 'Client',
                    2 => 'Server'
                ];
                $pcTypeName = $pcTypeNames[$pc->tpc_type ?? 0] ?? 'Unknown';

                // Get PC specifications
                $specs = $this->TransPCModel->getPCSpecs($pcId);
                $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
                $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
                $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
                $vga = $specs ? ($specs->tps_vga ?? '-') : '-';
                $ethernet = $specs ? ($specs->tps_ethernet ?? '-') : '-'; // Retrieve ethernet

                // Get IT Equipment and Server VM
                $equipment = $this->TransPCModel->getPCEquipment($pcId);
                $serverVM = $this->TransPCModel->getPCServerVM($pcId);

                // Calculate total rows needed for this PC
                $equipmentCount = $equipment ? count($equipment) : 0;
                $vmCount = $serverVM ? count($serverVM) : 0;
                $maxRows = max($equipmentCount, $vmCount, 1); // At least 1 row
                
                // Fill data for each row
                for ($i = 0; $i < $maxRows; $i++) {
                    $rowData = [];
                    // PC basic data (only on first row, empty on subsequent rows for CSV readability)
                    if ($i == 0) {
                        $rowData[] = $pc->tpc_id ?? null;
                        $rowData[] = $pcTypeName;
                        $rowData[] = $pc->tpc_name ?? '-';
                        $rowData[] = $pc->tpc_assetno ?? '-';
                        $rowData[] = $pcAge;
                        $rowData[] = $pc->tpc_osname ?? '-';
                        $rowData[] = $pc->tpc_ipaddress ?? '-';
                        $rowData[] = $pc->tpc_user ?? '-';
                        $rowData[] = $pc->tpc_location ?? '-';
                        $rowData[] = ($pc->tpc_status == 1 ? 'Active' : ($pc->tpc_status == 0 ? 'Inactive' : 'Unknown'));
                        $rowData[] = $processor;
                        $rowData[] = $ram;
                        $rowData[] = $storage;
                        $rowData[] = $vga;
                        $rowData[] = $ethernet; // Add Ethernet
                    } else {
                        // Empty cells for merged columns to keep CSV structure clean (optional for CSV, but good for consistency)
                        for ($j = 0; $j < 15; $j++) { // Columns up to Ethernet
                            $rowData[] = '';
                        }
                    }

                    // Server VM data with new structure
                    if ($i < $vmCount && isset($serverVM[$i])) {
                        $vm = $serverVM[$i];
                        $rowData[] = $vm->tpv_name ?? '-';
                        $rowData[] = $vm->tpv_processor ?? '-';
                        $rowData[] = $vm->tpv_ram ?? '-';
                        $rowData[] = $vm->tpv_storage ?? '-';
                        $rowData[] = $vm->tpv_vga ?? '-';
                        $rowData[] = $vm->tpv_ethernet ?? '-';
                        $rowData[] = $vm->tpv_ipaddress ?? '-';
                        $rowData[] = $vm->tpv_services ?? '-';
                        $rowData[] = $vm->tpv_remark ?? '-';
                    } else {
                        for ($j = 0; $j < 9; $j++) { // 9 VM related columns
                            $rowData[] = '-';
                        }
                    }

                    // Equipment data
                    if ($i < $equipmentCount && isset($equipment[$i])) {
                        $eq = $equipment[$i];
                        $eqType = '';
                        switch($eq->tpi_type ?? 0) {
                            case 1: $eqType = 'Monitor'; break;
                            case 2: $eqType = 'Printer'; break;
                            case 3: $eqType = 'Scanner'; break;
                            default: $eqType = 'Unknown'; break;
                        }
                        $eqAge = $eq->tpi_receivedate ? $this->calculateAge($eq->tpi_receivedate) : '-';
                        
                        $rowData[] = $eqType;
                        $rowData[] = $eq->tpi_assetno ?? '-';
                        $rowData[] = $eqAge;
                    } else {
                        $rowData[] = '-'; // Equipment Type
                        $rowData[] = '-'; // Equipment Asset
                        $rowData[] = '-'; // Equipment Age
                    }

                    if ($i == 0) {
                        $rowData[] = $lastUser;
                    } else {
                        $rowData[] = '';
                    }

                    $sheet->fromArray($rowData, NULL, 'A' . $row);
                    $row++;
                }
            }
            
            // Create CSV writer
            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            
            $filename = 'PC_Data_Complete_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            log_message('error', 'Error exporting CSV: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ]);
        }
    }

    public function exportODS()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        try {
            $pcs = $this->TransPCModel->getData('All');
            $pcs = array_filter($pcs, function($item) {
                return ($item->tpc_status == 0 || $item->tpc_status == 1);
            });

            if (empty($pcs)) {
                throw new \Exception("No active or inactive PC data available for export.");
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("PC Management System")
                ->setTitle("PC Data Export")
                ->setDescription("Export of PC data in ODS format");

            // Set title
            $sheet->mergeCells('B2:AC2');
            $sheet->setCellValue('B2', 'PC Data with Specifications, Equipment & Server VM');
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('B2:AC2')->applyFromArray($titleStyle);

            // Define and set headers with new columns
            $headers = [
                'B4'  => 'ID',
                'C4'  => 'Type',
                'D4'  => 'PC Name',
                'E4'  => 'Asset No',
                'F4'  => 'Asset Age',
                'G4'  => 'OS Name',
                'H4'  => 'IP Address',
                'I4'  => 'User',
                'J4'  => 'Location',
                'K4'  => 'Status',
                'L4'  => 'Processor',
                'M4'  => 'RAM',
                'N4'  => 'Storage',
                'O4'  => 'VGA',
                'P4'  => 'Ethernet',
                'Q4'  => 'VM Name',
                'R4'  => 'VM Processor',
                'S4'  => 'VM RAM',
                'T4'  => 'VM Storage',
                'U4'  => 'VM VGA',
                'V4'  => 'VM Ethernet',
                'W4'  => 'VM IP',
                'X4'  => 'VM Services',
                'Y4'  => 'VM Remark',
                'Z4'  => 'Equipment Type',
                'AA4' => 'Equipment Asset',
                'AB4' => 'Equipment Age',
                'AC4' => 'Last User'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Style headers
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            
            $sheet->getStyle('B4:AC4')->applyFromArray($headerStyle);

            // Add data
            $row = 5;
            foreach ($pcs as $pc) {
                $pcId = $pc->tpc_id ?? null;
                if ($pcId === null) {
                    log_message('warning', 'Skipping PC record with null tpc_id during ODS export.');
                    continue;
                }

                $lastUser = $this->getUserDisplayName($pc->tpc_lastuser ?? null);
                $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
                $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';

                // Get PC type display name
                $pcTypeNames = [
                    1 => 'Client',
                    2 => 'Server'
                ];
                $pcTypeName = $pcTypeNames[$pc->tpc_type ?? 0] ?? 'Unknown';

                // Get PC specifications with new ethernet field
                $specs = $this->TransPCModel->getPCSpecs($pcId);
                $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
                $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
                $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
                $vga = $specs ? ($specs->tps_vga ?? '-') : '-';
                $ethernet = $specs ? ($specs->tps_ethernet ?? '-') : '-';

                // Get IT Equipment and Server VM
                $equipment = $this->TransPCModel->getPCEquipment($pcId);
                $serverVM = $this->TransPCModel->getPCServerVM($pcId);

                // Calculate total rows needed for this PC
                $equipmentCount = $equipment ? count($equipment) : 0;
                $vmCount = $serverVM ? count($serverVM) : 0;
                $maxRows = max($equipmentCount, $vmCount, 1); // At least 1 row

                $startRow = $row;
                
                // Fill data for each row
                for ($i = 0; $i < $maxRows; $i++) {
                    // PC basic data
                    if ($i == 0) {
                        $sheet->setCellValue('B' . $row, $pc->tpc_id);
                        $sheet->setCellValue('C' . $row, $pcTypeName);
                        $sheet->setCellValue('D' . $row, $pc->tpc_name ?? '-');
                        $sheet->setCellValue('E' . $row, $pc->tpc_assetno ?? '-');
                        $sheet->setCellValue('F' . $row, $pcAge);
                        $sheet->setCellValue('G' . $row, $pc->tpc_osname ?? '-');
                        $sheet->setCellValue('H' . $row, $pc->tpc_ipaddress ?? '-');
                        $sheet->setCellValue('I' . $row, $pc->tpc_user ?? '-');
                        $sheet->setCellValue('J' . $row, $pc->tpc_location ?? '-');
                        $sheet->setCellValue('K' . $row, ($pc->tpc_status == 1 ? 'Active' : ($pc->tpc_status == 0 ? 'Inactive' : 'Unknown')));
                        $sheet->setCellValue('L' . $row, $processor);
                        $sheet->setCellValue('M' . $row, $ram);
                        $sheet->setCellValue('N' . $row, $storage);
                        $sheet->setCellValue('O' . $row, $vga);
                        $sheet->setCellValue('P' . $row, $ethernet);
                        $sheet->setCellValue('AC' . $row, $lastUser);
                    } else {
                        // Clear PC-specific columns for merged rows
                        $clearPccolumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'AC'];
                        foreach ($clearPccolumns as $col) {
                            $sheet->setCellValue($col . $row, '');
                        }
                    }

                    // Server VM data with new structure
                    if ($i < $vmCount && isset($serverVM[$i])) {
                        $vm = $serverVM[$i];
                        $sheet->setCellValue('Q' . $row, $vm->tpv_name ?? '-');
                        $sheet->setCellValue('R' . $row, $vm->tpv_processor ?? '-');
                        $sheet->setCellValue('S' . $row, $vm->tpv_ram ?? '-');
                        $sheet->setCellValue('T' . $row, $vm->tpv_storage ?? '-');
                        $sheet->setCellValue('U' . $row, $vm->tpv_vga ?? '-');
                        $sheet->setCellValue('V' . $row, $vm->tpv_ethernet ?? '-');
                        $sheet->setCellValue('W' . $row, $vm->tpv_ipaddress ?? '-');
                        $sheet->setCellValue('X' . $row, $vm->tpv_services ?? '-');
                        $sheet->setCellValue('Y' . $row, $vm->tpv_remark ?? '-');
                    } else {
                        $sheet->setCellValue('Q' . $row, '-');
                        $sheet->setCellValue('R' . $row, '-');
                        $sheet->setCellValue('S' . $row, '-');
                        $sheet->setCellValue('T' . $row, '-');
                        $sheet->setCellValue('U' . $row, '-');
                        $sheet->setCellValue('V' . $row, '-');
                        $sheet->setCellValue('W' . $row, '-');
                        $sheet->setCellValue('X' . $row, '-');
                        $sheet->setCellValue('Y' . $row, '-');
                    }

                    // Equipment data
                    if ($i < $equipmentCount && isset($equipment[$i])) {
                        $eq = $equipment[$i];
                        $eqType = '';
                        switch($eq->tpi_type ?? 0) {
                            case 1: $eqType = 'Monitor'; break;
                            case 2: $eqType = 'Printer'; break;
                            case 3: $eqType = 'Scanner'; break;
                            default: $eqType = 'Unknown'; break;
                        }
                        $eqAge = $eq->tpi_receivedate ? $this->calculateAge($eq->tpi_receivedate) : '-';
                        
                        $sheet->setCellValue('Z' . $row, $eqType);
                        $sheet->setCellValue('AA' . $row, $eq->tpi_assetno ?? '-');
                        $sheet->setCellValue('AB' . $row, $eqAge);
                    } else {
                        $sheet->setCellValue('Z' . $row, '-');
                        $sheet->setCellValue('AA' . $row, '-');
                        $sheet->setCellValue('AB' . $row, '-');
                    }

                    $row++;
                }

                // Merge cells for PC basic data if there are multiple rows
                if ($maxRows > 1) {
                    $endRow = $startRow + $maxRows - 1;
                    
                    // Merge PC basic data columns
                    $mergeColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'AC'];
                    foreach ($mergeColumns as $col) {
                        $sheet->mergeCells($col . $startRow . ':' . $col . $endRow);
                        // Center align merged cells vertically
                        $sheet->getStyle($col . $startRow . ':' . $col . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }
                }
            }

            // Auto-size columns
            foreach (range('B', 'AC') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Style data rows
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ];
            $lastDataRow = max(4, $row - 1);
            $sheet->getStyle('B5:AC' . $lastDataRow)->applyFromArray($dataStyle);

            // Create ODS writer
            $writer = new Ods($spreadsheet);

            $filename = 'PC_Selected_Data_(' . count($pcs) . '_records)_' . date('Y-m-d_H-i-s') . '.ods';

            header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Error exporting selected ODS: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ]);
        }
    }


    public function exportSelectedXLSX()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $selectedIds = $this->request->getPost('selected_ids');
        
        if (!$selectedIds || empty($selectedIds)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No PC records selected for export.'
            ]);
        }

        try {
            // Get selected PC data
            $allPCs = $this->TransPCModel->getData('All'); // Get all data
            $pcs = array_filter($allPCs, function($item) use ($selectedIds) {
                // Ensure tpc_id is treated as integer for comparison with selectedIds which are integers
                return in_array($item->tpc_id, array_map('intval', $selectedIds)) && ($item->tpc_status == 0 || $item->tpc_status == 1);
            });

            if (empty($pcs)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'No valid PC records found for export.'
                ]);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("PC Management System")
                ->setTitle("Selected PC Data Export")
                ->setDescription("Export of selected PC data in XLSX format");

            // Set title
            // Adjusted to max column 'W' from the original code
            $sheet->mergeCells('B2:AC2');
            $selectedCount = count($pcs);
            $sheet->setCellValue('B2', "Selected PC Data ({$selectedCount} records) with Specifications, Equipment & Server VM");
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('B2:AC2')->applyFromArray($titleStyle);

            // Define and set headers
            $headers = [
                'B4' => 'ID',
                'C4' => 'Type',
                'D4' => 'PC Name',
                'E4' => 'Asset No',
                'F4' => 'Asset Age',
                'G4' => 'OS Name',
                'H4' => 'IP Address',
                'I4' => 'User',
                'J4' => 'Location',
                'K4' => 'Status',
                'L4' => 'Processor',
                'M4' => 'RAM',
                'N4' => 'Storage',
                'O4' => 'VGA',
                // Added Ethernet column here as it's missing in your current selected export headers
                'P4' => 'Ethernet', 
                'Q4' => 'VM Name', // Changed from VM Type to VM Name for consistency with full export
                'R4' => 'VM Processor', // Added new VM related columns
                'S4' => 'VM RAM',
                'T4' => 'VM Storage',
                'U4' => 'VM VGA',
                'V4' => 'VM Ethernet',
                'W4' => 'VM IP',
                'X4' => 'VM Services',
                'Y4' => 'VM Remark',
                'Z4' => 'Equipment Type',
                'AA4' => 'Equipment Asset',
                'AB4' => 'Equipment Age',
                'AC4' => 'Last User'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Style headers
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            // Adjust header style range to match new full headers
            $sheet->getStyle('B4:AC4')->applyFromArray($headerStyle);

            // Add data (same logic as original export but for selected data only)
            $row = 5;
            foreach ($pcs as $pc) {
                $pcId = $pc->tpc_id ?? null;
                if ($pcId === null) {
                    log_message('warning', 'Skipping selected PC record with null tpc_id during XLSX export.');
                    continue;
                }

                $lastUser = $this->getUserDisplayName($pc->tpc_lastuser ?? null);
                $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
                $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';

                // Get PC type display name
                $pcTypeNames = [
                    1 => 'Client',
                    2 => 'Server'
                ];
                $pcTypeName = $pcTypeNames[$pc->tpc_type ?? 0] ?? 'Unknown';

                // Get PC specifications
                $specs = $this->TransPCModel->getPCSpecs($pcId);
                $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
                $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
                $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
                $vga = $specs ? ($specs->tps_vga ?? '-') : '-';
                $ethernet = $specs ? ($specs->tps_ethernet ?? '-') : '-'; // Make sure ethernet is retrieved

                // Get IT Equipment and Server VM
                $equipment = $this->TransPCModel->getPCEquipment($pcId);
                $serverVM = $this->TransPCModel->getPCServerVM($pcId);

                // Calculate total rows needed for this PC
                $equipmentCount = $equipment ? count($equipment) : 0;
                $vmCount = $serverVM ? count($serverVM) : 0;
                $maxRows = max($equipmentCount, $vmCount, 1); // At least 1 row

                $startRow = $row;
                
                // Fill data for each row
                for ($i = 0; $i < $maxRows; $i++) {
                    // PC basic data (only on first row)
                    if ($i == 0) {
                        $sheet->setCellValue('B' . $row, $pc->tpc_id);
                        $sheet->setCellValue('C' . $row, $pcTypeName);
                        $sheet->setCellValue('D' . $row, $pc->tpc_name ?? '-');
                        $sheet->setCellValue('E' . $row, $pc->tpc_assetno ?? '-');
                        $sheet->setCellValue('F' . $row, $pcAge);
                        $sheet->setCellValue('G' . $row, $pc->tpc_osname ?? '-');
                        $sheet->setCellValue('H' . $row, $pc->tpc_ipaddress ?? '-');
                        $sheet->setCellValue('I' . $row, $pc->tpc_user ?? '-');
                        $sheet->setCellValue('J' . $row, $pc->tpc_location ?? '-');
                        $sheet->setCellValue('K' . $row, ($pc->tpc_status == 1 ? 'Active' : ($pc->tpc_status == 0 ? 'Inactive' : 'Unknown')));
                        $sheet->setCellValue('L' . $row, $processor);
                        $sheet->setCellValue('M' . $row, $ram);
                        $sheet->setCellValue('N' . $row, $storage);
                        $sheet->setCellValue('O' . $row, $vga);
                        $sheet->setCellValue('P' . $row, $ethernet); // Add Ethernet here
                        $sheet->setCellValue('AC' . $row, $lastUser);
                    } else {
                        // Clear PC-specific columns for merged rows
                        $clearPccolumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'AC'];
                        foreach ($clearPccolumns as $col) {
                            $sheet->setCellValue($col . $row, '');
                        }
                    }

                    // Server VM data (if exists for this index)
                    if ($i < $vmCount && isset($serverVM[$i])) {
                        $vm = $serverVM[$i];
                        // Changed from VM Type to VM Name for consistency
                        $sheet->setCellValue('Q' . $row, $vm->tpv_name ?? '-'); 
                        $sheet->setCellValue('R' . $row, $vm->tpv_processor ?? '-');
                        $sheet->setCellValue('S' . $row, $vm->tpv_ram ?? '-');
                        $sheet->setCellValue('T' . $row, $vm->tpv_storage ?? '-');
                        $sheet->setCellValue('U' . $row, $vm->tpv_vga ?? '-');
                        $sheet->setCellValue('V' . $row, $vm->tpv_ethernet ?? '-');
                        $sheet->setCellValue('W' . $row, $vm->tpv_ipaddress ?? '-');
                        $sheet->setCellValue('X' . $row, $vm->tpv_services ?? '-');
                        $sheet->setCellValue('Y' . $row, $vm->tpv_remark ?? '-');
                    } else {
                        $sheet->setCellValue('Q' . $row, '-');
                        $sheet->setCellValue('R' . $row, '-');
                        $sheet->setCellValue('S' . $row, '-');
                        $sheet->setCellValue('T' . $row, '-');
                        $sheet->setCellValue('U' . $row, '-');
                        $sheet->setCellValue('V' . $row, '-');
                        $sheet->setCellValue('W' . $row, '-');
                        $sheet->setCellValue('X' . $row, '-');
                        $sheet->setCellValue('Y' . $row, '-');
                    }

                    // Equipment data (if exists for this index)
                    if ($i < $equipmentCount && isset($equipment[$i])) {
                        $eq = $equipment[$i];
                        $eqType = '';
                        switch($eq->tpi_type ?? 0) {
                            case 1: $eqType = 'Monitor'; break;
                            case 2: $eqType = 'Printer'; break;
                            case 3: $eqType = 'Scanner'; break;
                            default: $eqType = 'Unknown'; break;
                        }
                        $eqAge = $eq->tpi_receivedate ? $this->calculateAge($eq->tpi_receivedate) : '-';
                        
                        $sheet->setCellValue('Z' . $row, $eqType);
                        $sheet->setCellValue('AA' . $row, $eq->tpi_assetno ?? '-');
                        $sheet->setCellValue('AB' . $row, $eqAge);
                    } else {
                        $sheet->setCellValue('Z' . $row, '-');
                        $sheet->setCellValue('AA' . $row, '-');
                        $sheet->setCellValue('AB' . $row, '-');
                    }

                    $row++;
                }

                // Merge cells for PC basic data if there are multiple rows
                if ($maxRows > 1) {
                    $endRow = $startRow + $maxRows - 1;
                    
                    // Merge PC basic data columns
                    $mergeColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'AC']; // Added P for Ethernet
                    foreach ($mergeColumns as $col) {
                        $sheet->mergeCells($col . $startRow . ':' . $col . $endRow);
                        // Center align merged cells vertically
                        $sheet->getStyle($col . $startRow . ':' . $col . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }
                }
            }

            // Auto-size columns
            // Adjusted to include all new columns up to AC
            foreach (range('B', 'AC') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Style data rows
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ];
            $lastDataRow = max(4, $row - 1);
            $sheet->getStyle('B5:AC' . $lastDataRow)->applyFromArray($dataStyle);

            // Create writer and output
            $writer = new Xlsx($spreadsheet);

            $filename = 'PC_Selected_Data_(' . count($pcs) . '_records)_' . date('Y-m-d_H-i-s') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Error exporting selected XLSX: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ]);
        }
    }

    public function exportSelectedCSV()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $selectedIds = $this->request->getPost('selected_ids');
        
        if (!$selectedIds || empty($selectedIds)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No PC records selected for export.'
            ]);
        }

        try {
            // Get selected PC data
            $allPCs = $this->TransPCModel->getData('All');
            $pcs = array_filter($allPCs, function($item) use ($selectedIds) {
                return in_array($item->tpc_id, array_map('intval', $selectedIds)) && ($item->tpc_status == 0 || $item->tpc_status == 1);
            });

            if (empty($pcs)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'No valid PC records found for export.'
                ]);
            }
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Define headers (Adjusted to match full export headers for consistency)
            $headers = [
                'ID', 'Type', 'PC Name', 'Asset No', 'Asset Age',
                'OS Name', 'IP Address', 'User', 'Location', 'Status',
                'Processor', 'RAM', 'Storage', 'VGA', 'Ethernet', // Added Ethernet
                'VM Name', 'VM Processor', 'VM RAM', 'VM Storage', 'VM VGA', 'VM Ethernet', // Changed 'VM Type' to VM Name and added new VM fields
                'VM IP', 'VM Services', 'VM Remark',
                'Equipment Type', 'Equipment Asset', 'Equipment Age', 'Last User'
            ];
            
            // Set headers
            $sheet->fromArray($headers, NULL, 'A1');
            
            // Add data (similar logic as XLSX but for CSV)
            $row = 2;
            foreach ($pcs as $pc) {
                $pcId = $pc->tpc_id ?? null;
                if ($pcId === null) {
                    log_message('warning', 'Skipping selected PC record with null tpc_id during CSV export.');
                    continue;
                }

                $lastUser = $this->getUserDisplayName($pc->tpc_lastuser ?? null);
                $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
                $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';
                
                // Get PC type display name
                $pcTypeNames = [
                    1 => 'Client',
                    2 => 'Server'
                ];
                $pcTypeName = $pcTypeNames[$pc->tpc_type ?? 0] ?? 'Unknown';

                // Get PC specifications
                $specs = $this->TransPCModel->getPCSpecs($pcId);
                $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
                $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
                $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
                $vga = $specs ? ($specs->tps_vga ?? '-') : '-';
                $ethernet = $specs ? ($specs->tps_ethernet ?? '-') : '-'; // Retrieve ethernet

                // Get IT Equipment and Server VM
                $equipment = $this->TransPCModel->getPCEquipment($pcId);
                $serverVM = $this->TransPCModel->getPCServerVM($pcId);

                // Calculate total rows needed for this PC
                $equipmentCount = $equipment ? count($equipment) : 0;
                $vmCount = $serverVM ? count($serverVM) : 0;
                $maxRows = max($equipmentCount, $vmCount, 1); // At least 1 row
                
                // Fill data for each row
                for ($i = 0; $i < $maxRows; $i++) {
                    $rowData = [];
                    // PC basic data (only on first row, empty on subsequent rows for CSV readability)
                    if ($i == 0) {
                        $rowData[] = $pc->tpc_id ?? null;
                        $rowData[] = $pcTypeName;
                        $rowData[] = $pc->tpc_name ?? '-';
                        $rowData[] = $pc->tpc_assetno ?? '-';
                        $rowData[] = $pcAge;
                        $rowData[] = $pc->tpc_osname ?? '-';
                        $rowData[] = $pc->tpc_ipaddress ?? '-';
                        $rowData[] = $pc->tpc_user ?? '-';
                        $rowData[] = $pc->tpc_location ?? '-';
                        $rowData[] = ($pc->tpc_status == 1 ? 'Active' : ($pc->tpc_status == 0 ? 'Inactive' : 'Unknown'));
                        $rowData[] = $processor;
                        $rowData[] = $ram;
                        $rowData[] = $storage;
                        $rowData[] = $vga;
                        $rowData[] = $ethernet; // Add Ethernet
                    } else {
                        // Empty cells for subsequent rows, adjusted for new ethernet column
                        for ($j = 0; $j < 15; $j++) { // Columns up to Ethernet
                            $rowData[] = '';
                        }
                    }

                    // Server VM data
                    if ($i < $vmCount && isset($serverVM[$i])) {
                        $vm = $serverVM[$i];
                        $rowData[] = $vm->tpv_name ?? '-'; // Changed from VM Type to VM Name
                        $rowData[] = $vm->tpv_processor ?? '-';
                        $rowData[] = $vm->tpv_ram ?? '-';
                        $rowData[] = $vm->tpv_storage ?? '-';
                        $rowData[] = $vm->tpv_vga ?? '-';
                        $rowData[] = $vm->tpv_ethernet ?? '-';
                        $rowData[] = $vm->tpv_ipaddress ?? '-';
                        $rowData[] = $vm->tpv_services ?? '-';
                        $rowData[] = $vm->tpv_remark ?? '-';
                    } else {
                        for ($j = 0; $j < 9; $j++) { // 9 VM related columns
                            $rowData[] = '-';
                        }
                    }

                    // Equipment data
                    if ($i < $equipmentCount && isset($equipment[$i])) {
                        $eq = $equipment[$i];
                        $eqType = '';
                        switch($eq->tpi_type ?? 0) {
                            case 1: $eqType = 'Monitor'; break;
                            case 2: $eqType = 'Printer'; break;
                            case 3: $eqType = 'Scanner'; break;
                            default: $eqType = 'Unknown'; break;
                        }
                        $eqAge = $eq->tpi_receivedate ? $this->calculateAge($eq->tpi_receivedate) : '-';
                        
                        $rowData[] = $eqType;
                        $rowData[] = $eq->tpi_assetno ?? '-';
                        $rowData[] = $eqAge;
                    } else {
                        $rowData[] = '-';
                        $rowData[] = '-';
                        $rowData[] = '-';
                    }

                    if ($i == 0) {
                        $rowData[] = $lastUser;
                    } else {
                        $rowData[] = '';
                    }

                    $sheet->fromArray($rowData, NULL, 'A' . $row);
                    $row++;
                }
            }
            
            // Create CSV writer
            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            
            $filename = 'PC_Selected_Data_(' . count($pcs) . '_records)_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            log_message('error', 'Error exporting selected CSV: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ]);
        }
    }

    public function exportSelectedODS()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $selectedIds = $this->request->getPost('selected_ids');
        
        if (!$selectedIds || empty($selectedIds)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No PC records selected for export.'
            ]);
        }

        try {
            // Get selected PC data
            $allPCs = $this->TransPCModel->getData('All');
            $pcs = array_filter($allPCs, function($item) use ($selectedIds) {
                return in_array($item->tpc_id, array_map('intval', $selectedIds)) && ($item->tpc_status == 0 || $item->tpc_status == 1);
            });

            if (empty($pcs)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'No valid PC records found for export.'
                ]);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("PC Management System")
                ->setTitle("Selected PC Data Export")
                ->setDescription("Export of selected PC data in ODS format");

            // Set title
            // Adjusted to max column 'AC' to be consistent with full export for proper merging
            $sheet->mergeCells('B2:AC2');
            $selectedCount = count($pcs);
            $sheet->setCellValue('B2', "Selected PC Data ({$selectedCount} records) with Specifications, Equipment & Server VM");
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('B2:AC2')->applyFromArray($titleStyle);

            // Define and set headers
            $headers = [
                'B4' => 'ID',
                'C4' => 'Type',
                'D4' => 'PC Name',
                'E4' => 'Asset No',
                'F4' => 'Asset Age',
                'G4' => 'OS Name',
                'H4' => 'IP Address',
                'I4' => 'User',
                'J4' => 'Location',
                'K4' => 'Status',
                'L4' => 'Processor',
                'M4' => 'RAM',
                'N4' => 'Storage',
                'O4' => 'VGA',
                'P4' => 'Ethernet', // Added Ethernet
                'Q4' => 'VM Name', // Changed 'VM Type' to VM Name
                'R4' => 'VM Processor',
                'S4' => 'VM RAM',
                'T4' => 'VM Storage',
                'U4' => 'VM VGA',
                'V4' => 'VM Ethernet',
                'W4' => 'VM IP',
                'X4' => 'VM Services',
                'Y4' => 'VM Remark',
                'Z4' => 'Equipment Type',
                'AA4' => 'Equipment Asset',
                'AB4' => 'Equipment Age',
                'AC4' => 'Last User'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Style headers
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            
            $sheet->getStyle('B4:AC4')->applyFromArray($headerStyle); // Adjusted range

            // Add data (same logic as XLSX)
            $row = 5;
            foreach ($pcs as $pc) {
                $pcId = $pc->tpc_id ?? null;
                if ($pcId === null) {
                    log_message('warning', 'Skipping selected PC record with null tpc_id during ODS export.');
                    continue;
                }

                $lastUser = $this->getUserDisplayName($pc->tpc_lastuser ?? null);
                $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
                $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';

                // Get PC type display name
                $pcTypeNames = [
                    1 => 'Client',
                    2 => 'Server'
                ];
                $pcTypeName = $pcTypeNames[$pc->tpc_type ?? 0] ?? 'Unknown';

                // Get PC specifications
                $specs = $this->TransPCModel->getPCSpecs($pcId);
                $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
                $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
                $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
                $vga = $specs ? ($specs->tps_vga ?? '-') : '-';
                $ethernet = $specs ? ($specs->tps_ethernet ?? '-') : '-'; // Retrieve ethernet

                // Get IT Equipment and Server VM
                $equipment = $this->TransPCModel->getPCEquipment($pcId);
                $serverVM = $this->TransPCModel->getPCServerVM($pcId);

                // Calculate total rows needed for this PC
                $equipmentCount = $equipment ? count($equipment) : 0;
                $vmCount = $serverVM ? count($serverVM) : 0;
                $maxRows = max($equipmentCount, $vmCount, 1); // At least 1 row

                $startRow = $row;
                
                // Fill data for each row
                for ($i = 0; $i < $maxRows; $i++) {
                    // PC basic data (only on first row)
                    if ($i == 0) {
                        $sheet->setCellValue('B' . $row, $pc->tpc_id);
                        $sheet->setCellValue('C' . $row, $pcTypeName);
                        $sheet->setCellValue('D' . $row, $pc->tpc_name ?? '-');
                        $sheet->setCellValue('E' . $row, $pc->tpc_assetno ?? '-');
                        $sheet->setCellValue('F' . $row, $pcAge);
                        $sheet->setCellValue('G' . $row, $pc->tpc_osname ?? '-');
                        $sheet->setCellValue('H' . $row, $pc->tpc_ipaddress ?? '-');
                        $sheet->setCellValue('I' . $row, $pc->tpc_user ?? '-');
                        $sheet->setCellValue('J' . $row, $pc->tpc_location ?? '-');
                        $sheet->setCellValue('K' . $row, ($pc->tpc_status == 1 ? 'Active' : ($pc->tpc_status == 0 ? 'Inactive' : 'Unknown')));
                        $sheet->setCellValue('L' . $row, $processor);
                        $sheet->setCellValue('M' . $row, $ram);
                        $sheet->setCellValue('N' . $row, $storage);
                        $sheet->setCellValue('O' . $row, $vga);
                        $sheet->setCellValue('P' . $row, $ethernet); // Add Ethernet
                        $sheet->setCellValue('AC' . $row, $lastUser);
                    } else {
                        // Clear PC-specific columns for merged rows
                        $clearPccolumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'AC'];
                        foreach ($clearPccolumns as $col) {
                            $sheet->setCellValue($col . $row, '');
                        }
                    }

                    // Server VM data (if exists for this index)
                    if ($i < $vmCount && isset($serverVM[$i])) {
                        $vm = $serverVM[$i];
                        $sheet->setCellValue('Q' . $row, $vm->tpv_name ?? '-'); // Changed from VM Type to VM Name
                        $sheet->setCellValue('R' . $row, $vm->tpv_processor ?? '-');
                        $sheet->setCellValue('S' . $row, $vm->tpv_ram ?? '-');
                        $sheet->setCellValue('T' . $row, $vm->tpv_storage ?? '-');
                        $sheet->setCellValue('U' . $row, $vm->tpv_vga ?? '-');
                        $sheet->setCellValue('V' . $row, $vm->tpv_ethernet ?? '-');
                        $sheet->setCellValue('W' . $row, $vm->tpv_ipaddress ?? '-');
                        $sheet->setCellValue('X' . $row, $vm->tpv_services ?? '-');
                        $sheet->setCellValue('Y' . $row, $vm->tpv_remark ?? '-');
                    } else {
                        $sheet->setCellValue('Q' . $row, '-');
                        $sheet->setCellValue('R' . $row, '-');
                        $sheet->setCellValue('S' . $row, '-');
                        $sheet->setCellValue('T' . $row, '-');
                        $sheet->setCellValue('U' . $row, '-');
                        $sheet->setCellValue('V' . $row, '-');
                        $sheet->setCellValue('W' . $row, '-');
                        $sheet->setCellValue('X' . $row, '-');
                        $sheet->setCellValue('Y' . $row, '-');
                    }

                    // Equipment data (if exists for this index)
                    if ($i < $equipmentCount && isset($equipment[$i])) {
                        $eq = $equipment[$i];
                        $eqType = '';
                        switch($eq->tpi_type ?? 0) {
                            case 1: $eqType = 'Monitor'; break;
                            case 2: $eqType = 'Printer'; break;
                            case 3: $eqType = 'Scanner'; break;
                            default: $eqType = 'Unknown'; break;
                        }
                        $eqAge = $eq->tpi_receivedate ? $this->calculateAge($eq->tpi_receivedate) : '-';
                        
                        $sheet->setCellValue('Z' . $row, $eqType);
                        $sheet->setCellValue('AA' . $row, $eq->tpi_assetno ?? '-');
                        $sheet->setCellValue('AB' . $row, $eqAge);
                    } else {
                        $sheet->setCellValue('Z' . $row, '-');
                        $sheet->setCellValue('AA' . $row, '-');
                        $sheet->setCellValue('AB' . $row, '-');
                    }

                    $row++;
                }

                // Merge cells for PC basic data if there are multiple rows
                if ($maxRows > 1) {
                    $endRow = $startRow + $maxRows - 1;
                    
                    // Merge PC basic data columns
                    $mergeColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'AC']; // Added P for Ethernet
                    foreach ($mergeColumns as $col) {
                        $sheet->mergeCells($col . $startRow . ':' . $col . $endRow);
                        // Center align merged cells vertically
                        $sheet->getStyle($col . $startRow . ':' . $col . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }
                }
            }

            // Auto-size columns
            foreach (range('B', 'AC') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Style data rows
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ];
            $lastDataRow = max(4, $row - 1);
            $sheet->getStyle('B5:AC' . $lastDataRow)->applyFromArray($dataStyle);

            // Create ODS writer
            $writer = new Ods($spreadsheet);

            $filename = 'PC_Selected_Data_(' . count($pcs) . '_records)_' . date('Y-m-d_H-i-s') . '.ods';

            header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Error exporting selected ODS: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ]);
        }
    }
}