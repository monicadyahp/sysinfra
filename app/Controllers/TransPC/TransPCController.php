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
                         
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "TransPC") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
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
        $statusFilter = $this->request->getGet('status');
        $typeFilter = $this->request->getGet('type');
        $pcs = $this->TransPCModel->getData($statusFilter, $typeFilter);
                 
        // Format the data for DataTables
        $formattedData = [];
        foreach ($pcs as $pc) {
            // Get user display name
            $lastUser = $this->getUserDisplayName($pc->tpc_lastuser);

            $formattedData[] = [
                'tpc_id' => $pc->tpc_id,
                'tpc_type' => $pc->tpc_type,
                'tpc_name' => $pc->tpc_name,
                'tpc_assetno' => $pc->tpc_assetno,
                'tpc_pcreceivedate' => $pc->tpc_pcreceivedate,
                'tpc_osname' => $pc->tpc_osname,
                'tpc_ipaddress' => $pc->tpc_ipaddress,
                'tpc_user' => $pc->tpc_user,
                'tpc_location' => $pc->tpc_location,
                'tpc_status' => $pc->tpc_status,
                'tpc_lastupdate' => $pc->tpc_lastupdate,
                'tpc_lastuser' => $lastUser
            ];
        }
                         
        return $this->response->setJSON($formattedData);
    }

    public function getPCById()
    {
        $id = $this->request->getGet('id');
                         
        if (empty($id)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'PC ID is required'
            ]);
        }
                         
        $pc = $this->TransPCModel->getPCById($id);
         
        if ($pc) {
            // Get user display name
            $lastUser = $this->getUserDisplayName($pc->tpc_lastuser);

            $formattedPC = [
                'tpc_id' => $pc->tpc_id,
                'tpc_type' => $pc->tpc_type,
                'tpc_name' => $pc->tpc_name,
                'tpc_assetno' => $pc->tpc_assetno,
                'tpc_pcreceivedate' => $pc->tpc_pcreceivedate,
                'tpc_osname' => $pc->tpc_osname,
                'tpc_ipaddress' => $pc->tpc_ipaddress,
                'tpc_user' => $pc->tpc_user,
                'tpc_location' => $pc->tpc_location,
                'tpc_status' => $pc->tpc_status,
                'tpc_lastupdate' => $pc->tpc_lastupdate,
                'tpc_lastuser' => $lastUser
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
    }

    public function store()
    {
        $data = $this->request->getPost();
        
        // Validate input
        if (empty($data['pc_type']) || empty($data['pc_receive_date']) || !isset($data['pcstatus'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'PC Type, PC Receive Date, and Status are required.'
            ]);
        }

        // Sebelum menyimpan data, tambahkan validasi IP
        if (!empty($data['ip_address'])) {
            $ipAvailable = $this->TransPCModel->getIPAddressByIP($data['ip_address']);
            
            if (!$ipAvailable) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Selected IP Address is not available'
                ]);
            }
        }
                        
        $result = $this->TransPCModel->storeData($data);
        return $this->response->setJSON($result);
    }

    public function update()
    {
        $data = $this->request->getPost();
                        
        // Validate input
        if (empty($data['tpc_id']) || empty($data['pc_type']) || empty($data['pc_receive_date']) || !isset($data['pcstatus'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'PC ID, PC Type, PC Receive Date, and Status are required.'
            ]);
        }

        // Dapatkan data PC lama untuk validasi IP
        $oldPC = $this->TransPCModel->getPCById($data['tpc_id']);
        $oldIPAddress = $oldPC ? $oldPC->tpc_ipaddress : '';
        $newIPAddress = $data['ip_address'] ?? '';

        // Hanya validasi jika ada perubahan IP
        if (!empty($newIPAddress) && $newIPAddress !== $oldIPAddress) {
            $ipAvailable = $this->TransPCModel->getIPAddressByIP($newIPAddress);
            
            if (!$ipAvailable) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Selected IP Address is not available'
                ]);
            }
        }

                        
        $result = $this->TransPCModel->updateData($data);
        return $this->response->setJSON($result);
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        if (empty($id)) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID not found']);
        }
                         
        $result = $this->TransPCModel->deleteData($id);
        return $this->response->setJSON($result);
    }

    public function searchAssetNo()
    {
        $assetNo = $this->TransPCModel->searchAssetNo();
        
        // Process the data to ensure display_asset_no is used consistently
        $processedData = [];
        foreach ($assetNo as $asset) {
            $processedAsset = (object)[
                'e_assetno' => $asset->display_asset_no, // Use display_asset_no as primary identifier
                'e_equipmentid' => $asset->e_equipmentid,
                'e_serialnumber' => $asset->e_serialnumber,
                'e_equipmentname' => $asset->e_equipmentname,
                'e_receivedate' => $asset->e_receivedate,
                'original_assetno' => $asset->e_assetno, // Keep original for reference
                'display_asset_no' => $asset->display_asset_no
            ];
            $processedData[] = $processedAsset;
        }
        
        return $this->response->setJSON($processedData);
    }

    public function getAssetNo()
    {
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
                    'asset_name' => $assetDetails->e_equipmentname,
                    'equipment_id' => $assetDetails->e_equipmentid,
                    'receive_date' => $assetDetails->formatted_receive_date ?? 
                        ($assetDetails->e_receivedate ? date('Y-m-d', strtotime($assetDetails->e_receivedate)) : null),
                    'display_asset_no' => $assetDetails->display_asset_no ?? $assetDetails->e_assetno,
                    'original_assetno' => $assetDetails->e_assetno
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Asset not found or already in use'
            ]);
        }
    }

    public function getIPAddresses()
    {
        $ipAddress = $this->request->getGet('ipAddress');
                            
        if (empty($ipAddress)) {
            return $this->response->setJSON(['status' => false, 'message' => 'IP Address is required']);
        }
                            
        $ip = $this->TransPCModel->getIPAddressByIP($ipAddress);
                            
        return $this->response->setJSON([
            'status' => ($ip !== null),
            'data' => $ip
        ]);
    }

    public function SearchIPAddresses()
    {
        
        $ipAddresses = $this->TransPCModel->searchIPAddresses();
        
        return $this->response->setJSON($ipAddresses);
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
    }

    public function searchEmployees()
    {
                         
        $employees = $this->TransPCModel->searchEmployees();
        return $this->response->setJSON($employees);
    }

    public function getlocations()
    {
        $locations = $this->TransPCModel->getlocations();
        return $this->response->setJSON($locations);
    }

    public function getOSList()
    {
        $osList = $this->TransPCModel->getOSList();
        return $this->response->setJSON($osList);
    }

    private function getUserDisplayName($userId)
    {
        if (empty($userId)) {
            return 'N/A';
        }

        // Connect to common database
        $dbCommon = \Config\Database::connect('db_postgree');

        if (is_numeric($userId)) {
            // Try employee table first
            $employee = $dbCommon->table('tbmst_employee')
                                ->select('em_emplname')
                                ->where('em_emplcode', $userId)
                                ->get()
                                ->getRow();

            if ($employee) {
                return $employee->em_emplname;
            }

            // Try user access table
            $userAccess = $dbCommon->table('tbua_useraccess')
                                   ->select('ua_username')
                                   ->where('ua_userid', $userId)
                                   ->get()
                                   ->getRow();

            if ($userAccess) {
                return $userAccess->ua_username;
            }

            return $userId;
        } else {
            return $userId;
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
            return 'Invalid date';
        }
    }

    public function getPCDetails()
    {
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
                'tpc_id' => $pc->tpc_id,
                'tpc_type' => $pc->tpc_type,
                'tpc_name' => $pc->tpc_name,
                'tpc_assetno' => $pc->tpc_assetno,
                'tpc_pcreceivedate' => $pc->tpc_pcreceivedate,
                'tpc_osname' => $pc->tpc_osname,
                'tpc_ipaddress' => $pc->tpc_ipaddress,
                'tpc_user' => $pc->tpc_user,
                'tpc_location' => $pc->tpc_location,
                'tpc_status' => $pc->tpc_status,
                'tpc_lastuser' => $this->getUserDisplayName($pc->tpc_lastuser),
                'tpc_lastupdate' => $pc->tpc_lastupdate
            ];
            
            // Format specs data
            $formattedSpecs = null;
            if ($details['specs']) {
                $specs = $details['specs'];
                $formattedSpecs = [
                    'tps_id' => $specs->tps_id,
                    'tps_processor' => $specs->tps_processor,
                    'tps_ram' => $specs->tps_ram,
                    'tps_storage' => $specs->tps_storage,
                    'tps_vga' => $specs->tps_vga,
                    'tps_ethernet' => $specs->tps_ethernet,
                    'tps_lastuser' => $this->getUserDisplayName($specs->tps_lastuser),
                    'tps_lastupdate' => $specs->tps_lastupdate
                ];
            }
            
            // Format equipment data
            $formattedEquipment = [];
            foreach ($details['equipment'] as $equipment) {
                $formattedEquipment[] = [
                    'tpi_id' => $equipment->tpi_id,
                    'tpi_type' => $equipment->tpi_type,
                    'tpi_assetno' => $equipment->tpi_assetno,
                    'tpi_receivedate' => $equipment->tpi_receivedate,
                    'tpi_lastuser' => $this->getUserDisplayName($equipment->tpi_lastuser),
                    'tpi_lastupdate' => $equipment->tpi_lastupdate
                ];
            }

            // Format Server VM data
            $formattedServerVM = [];
            foreach ($details['servervm'] as $vm) {
                $formattedServerVM[] = [
                    'tpv_id' => $vm->tpv_id,
                    'tpv_name' => $vm->tpv_name,
                    'tpv_processor' => $vm->tpv_processor,
                    'tpv_ram' => $vm->tpv_ram,
                    'tpv_storage' => $vm->tpv_storage,
                    'tpv_vga' => $vm->tpv_vga,
                    'tpv_ethernet' => $vm->tpv_ethernet,
                    'tpv_ipaddress' => $vm->tpv_ipaddress,
                    'tpv_services' => $vm->tpv_services,
                    'tpv_remark' => $vm->tpv_remark,
                    'tpv_status' => $vm->tpv_status,
                    'tpv_lastuser' => $this->getUserDisplayName($vm->tpv_lastuser),
                    'tpv_lastupdate' => $vm->tpv_lastupdate
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
    }

    public function getSpecsByPC()
    {
        $pcId = $this->request->getGet('pcId');
        $specs = $this->TransPCModel->getPCSpecs($pcId);
        
        if ($specs) {
            return $this->response->setJSON([
                'status' => true,
                'data' => [
                    'tps_id' => $specs->tps_id,
                    'processor' => $specs->tps_processor,
                    'ram' => $specs->tps_ram,
                    'storage' => $specs->tps_storage,
                    'vga' => $specs->tps_vga
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No specifications found'
            ]);
        }
    }

    public function saveSpecs()
    {
        $data = $this->request->getPost();
        
        $saveData = [
            'tps_id' => $data['tps_id'] ?? null,
            'tps_pcid' => $data['tps_pcid'],
            'processor' => $data['processor'],
            'ram' => $data['ram'],
            'storage' => $data['storage'],
            'vga' => $data['vga']
        ];
        
        $result = $this->TransPCModel->savePCSpecs($saveData);
        
        if ($result) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'PC specifications saved successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to save PC specifications'
            ]);
        }
    }

    public function getEquipmentById()
    {
        $eqptId = $this->request->getGet('id');
        $equipment = $this->TransPCModel->getEquipmentById($eqptId);
        
        if ($equipment) {
            return $this->response->setJSON([
                'status' => true,
                'data' => [
                    'tpi_id' => $equipment->tpi_id,
                    'tpi_type' => $equipment->tpi_type,
                    'tpi_assetno' => $equipment->tpi_assetno,
                    'tpi_receivedate' => $equipment->tpi_receivedate
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Equipment not found'
            ]);
        }
    }

    public function saveEquipment()
    {
        $data = $this->request->getPost();
        
        $saveData = [
            'tpi_id' => $data['tpi_id'] ?? null,
            'tpi_pcid' => $data['tpi_pcid'],
            'type' => $data['type'],
            'assetno' => $data['assetno'],
            'receive_date' => $data['receive_date']
        ];
        
        $result = $this->TransPCModel->saveEquipment($saveData);
        
        if ($result) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Equipment saved successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to save equipment'
            ]);
        }
    }

    public function deleteEquipment()
    {
        $eqptId = $this->request->getPost('id');
        $result = $this->TransPCModel->deleteEquipment($eqptId);
        
        if ($result) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Equipment deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to delete equipment'
            ]);
        }
    }

    // PC Specifications CRUD Methods
    public function updatePCSpecs()
    {
        $data = $this->request->getPost();
        
        // Validasi field yang diperlukan
        if (empty($data['pc_id'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'PC ID is required.'
            ]);
        }
        
        $model = new TransPCModel();
        
        // Cek apakah spesifikasi sudah ada
        $existingSpecs = $model->getPCSpecs($data['pc_id']);

        // Jika spesifikasi sudah ada: lakukan UPDATE
        // Jika belum ada: lakukan INSERT sebagai UPDATE
        $result = $model->updatePCSpecs($data, !empty($existingSpecs));
        
        return $this->response->setJSON($result);
    }

    // PC IT Equipment CRUD Methods
    public function storePCEquipment()
    {
        $data = $this->request->getPost();
        
        // Validate required fields
        if (empty($data['pc_id']) || !isset($data['equipment_type'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'PC ID and Equipment Type are required.'
            ]);
        }
        
        $result = $this->TransPCModel->storePCEquipment($data);
        return $this->response->setJSON($result);
    }

    public function updatePCEquipment()
    {
        $data = $this->request->getPost();
        
        // Validate required fields
        if (empty($data['equipment_id']) || empty($data['pc_id']) || !isset($data['equipment_type'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Equipment ID, PC ID, and Equipment Type are required.'
            ]);
        }
        
        $result = $this->TransPCModel->updatePCEquipment($data);
        return $this->response->setJSON($result);
    }

    public function deletePCEquipment()
    {
        $id = $this->request->getPost('id');
        
        if (empty($id)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Equipment ID is required.'
            ]);
        }
        
        $result = $this->TransPCModel->deletePCEquipment($id);
        return $this->response->setJSON($result);
    }

    public function getPCEquipmentById()
    {
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
            $lastUser = $this->getUserDisplayName($equipment->tpi_lastuser);
            
            $formattedEquipment = [
                'tpi_id' => $equipment->tpi_id,
                'tpi_type' => $equipment->tpi_type,
                'tpi_pcid' => $equipment->tpi_pcid,
                'tpi_assetno' => $equipment->tpi_assetno,
                'tpi_receivedate' => $equipment->tpi_receivedate,
                'tpi_status' => $equipment->tpi_status,
                'tpi_lastuser' => $lastUser,
                'tpi_lastupdate' => $equipment->tpi_lastupdate
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
    }

    public function storePCServerVM()
    {
        $data = $this->request->getPost();
        
        if (empty($data['pc_id']) || empty($data['vm_name'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'PC ID and VM Name are required.'
            ]);
        }
        
        $result = $this->TransPCModel->storePCServerVM($data);
        return $this->response->setJSON($result);
    }

    public function updatePCServerVM()
    {
        $data = $this->request->getPost();
        
        if (empty($data['vm_id']) || empty($data['pc_id']) || empty($data['vm_name'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'VM ID, PC ID, and VM Name are required.'
            ]);
        }
        
        $result = $this->TransPCModel->updatePCServerVM($data);
        return $this->response->setJSON($result);
    }

    public function deletePCServerVM()
    {
        $id = $this->request->getPost('id');
        
        if (empty($id)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'VM ID is required.'
            ]);
        }
        
        $result = $this->TransPCModel->deletePCServerVM($id);
        return $this->response->setJSON($result);
    }

    public function getPCServerVMById()
    {
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
            $lastUser = $this->getUserDisplayName($serverVM->tpv_lastuser);
            
            $formattedServerVM = [
                'tpv_id' => $serverVM->tpv_id,
                'tpv_pcid' => $serverVM->tpv_pcid,
                'tpv_name' => $serverVM->tpv_name,
                'tpv_processor' => $serverVM->tpv_processor,
                'tpv_ram' => $serverVM->tpv_ram,
                'tpv_storage' => $serverVM->tpv_storage,
                'tpv_vga' => $serverVM->tpv_vga,
                'tpv_ethernet' => $serverVM->tpv_ethernet,
                'tpv_ipaddress' => $serverVM->tpv_ipaddress,
                'tpv_services' => $serverVM->tpv_services,
                'tpv_remark' => $serverVM->tpv_remark,
                'tpv_status' => $serverVM->tpv_status,
                'tpv_lastuser' => $lastUser,
                'tpv_lastupdate' => $serverVM->tpv_lastupdate
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
    }

    public function exportXLSX()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        try {
            $pcs = $this->TransPCModel->getData('All');
            $pcs = array_filter($pcs, function($item) {
                return $item->tpc_status == 0 || $item->tpc_status == 1;
            });

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("PC Management System")
                ->setTitle("PC Data Export")
                ->setDescription("Export of PC data in XLSX format");

            // Set title
            $sheet->mergeCells('B2:Y2');
            $sheet->setCellValue('B2', 'PC Data with Specifications, Equipment & Server VM');
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('B2:Y2')->applyFromArray($titleStyle);

            // Define and set headers with new columns
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
                'P4' => 'Ethernet',
                'Q4' => 'VM Name',
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
            $sheet->getStyle('B4:AC4')->applyFromArray($headerStyle);

            // Add data
            $row = 5;
            foreach ($pcs as $pc) {
                $lastUser = $this->getUserDisplayName($pc->tpc_lastuser);
                $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
                $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';

                // Get PC type display name
                $pcTypeNames = [
                    1 => 'Client',
                    2 => 'Server'
                ];
                $pcTypeName = $pcTypeNames[$pc->tpc_type] ?? 'Unknown';

                // Get PC specifications with new ethernet field
                $specs = $this->TransPCModel->getPCSpecs($pc->tpc_id);
                $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
                $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
                $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
                $vga = $specs ? ($specs->tps_vga ?? '-') : '-';
                $ethernet = $specs ? ($specs->tps_ethernet ?? '-') : '-';

                // Get IT Equipment and Server VM
                $equipment = $this->TransPCModel->getPCEquipment($pc->tpi_pcid);
                $serverVM = $this->TransPCModel->getPCServerVM($pc->tpv_pcid);

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
                        $sheet->setCellValue('K' . $row, $pc->tpc_status == 1 ? 'Active' : 'Inactive');
                        $sheet->setCellValue('L' . $row, $processor);
                        $sheet->setCellValue('M' . $row, $ram);
                        $sheet->setCellValue('N' . $row, $storage);
                        $sheet->setCellValue('O' . $row, $vga);
                        $sheet->setCellValue('P' . $row, $ethernet);
                        $sheet->setCellValue('AC' . $row, $lastUser);
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
                        switch($eq->tpi_type) {
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
            $sheet->getStyle('B5:AC' . ($row - 1))->applyFromArray($dataStyle);

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
                return $item->tpc_status == 0 || $item->tpc_status == 1;
            });
            
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
            $headerColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB'];
            for ($i = 0; $i < count($headers); $i++) {
                $sheet->setCellValue($headerColumns[$i] . '1', $headers[$i]);
            }
            
            // Add data
            $row = 2;
            foreach ($pcs as $pc) {
                $lastUser = $this->getUserDisplayName($pc->tpc_lastuser);
                $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
                $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';
                
                // Get PC type display name
                $pcTypeNames = [
                    1 => 'Client',
                    2 => 'Server'
                ];
                $pcTypeName = $pcTypeNames[$pc->tpc_type] ?? 'Unknown';

                // Get PC specifications 
                $specs = $this->TransPCModel->getPCSpecs($pc->tpc_id);
                $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
                $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
                $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
                $vga = $specs ? ($specs->tps_vga ?? '-') : '-';
                $ethernet = $specs ? ($specs->tps_ethernet ?? '-') : '-';

                // Get IT Equipment and Server VM
                $equipment = $this->TransPCModel->getPCEquipment($pc->tpi_pcid);
                $serverVM = $this->TransPCModel->getPCServerVM($pc->tpv_pcid);

                // Calculate total rows needed for this PC
                $equipmentCount = $equipment ? count($equipment) : 0;
                $vmCount = $serverVM ? count($serverVM) : 0;
                $maxRows = max($equipmentCount, $vmCount, 1); // At least 1 row
                
                // Fill data for each row
                for ($i = 0; $i < $maxRows; $i++) {
                    // PC basic data
                    if ($i == 0) {
                        $sheet->setCellValue('A' . $row, $pc->tpc_id);
                        $sheet->setCellValue('B' . $row, $pcTypeName);
                        $sheet->setCellValue('C' . $row, $pc->tpc_name ?? '-');
                        $sheet->setCellValue('D' . $row, $pc->tpc_assetno ?? '-');
                        $sheet->setCellValue('E' . $row, $pcAge);
                        $sheet->setCellValue('F' . $row, $pc->tpc_osname ?? '-');
                        $sheet->setCellValue('G' . $row, $pc->tpc_ipaddress ?? '-');
                        $sheet->setCellValue('H' . $row, $pc->tpc_user ?? '-');
                        $sheet->setCellValue('I' . $row, $pc->tpc_location ?? '-');
                        $sheet->setCellValue('J' . $row, $pc->tpc_status == 1 ? 'Active' : 'Inactive');
                        $sheet->setCellValue('K' . $row, $processor);
                        $sheet->setCellValue('L' . $row, $ram);
                        $sheet->setCellValue('M' . $row, $storage);
                        $sheet->setCellValue('N' . $row, $vga);
                        $sheet->setCellValue('O' . $row, $ethernet);
                        $sheet->setCellValue('AB' . $row, $lastUser);
                    } else {
                        // Empty cells for subsequent rows
                        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'AB'] as $col) {
                            $sheet->setCellValue($col . $row, '');
                        }
                    }

                    // Server VM data with new structure
                    if ($i < $vmCount && isset($serverVM[$i])) {
                        $vm = $serverVM[$i];
                        $sheet->setCellValue('P' . $row, $vm->tpv_name ?? '-');
                        $sheet->setCellValue('Q' . $row, $vm->tpv_processor ?? '-');
                        $sheet->setCellValue('R' . $row, $vm->tpv_ram ?? '-');
                        $sheet->setCellValue('S' . $row, $vm->tpv_storage ?? '-');
                        $sheet->setCellValue('T' . $row, $vm->tpv_vga ?? '-');
                        $sheet->setCellValue('U' . $row, $vm->tpv_ethernet ?? '-');
                        $sheet->setCellValue('V' . $row, $vm->tpv_ipaddress ?? '-');
                        $sheet->setCellValue('W' . $row, $vm->tpv_services ?? '-');
                        $sheet->setCellValue('X' . $row, $vm->tpv_remark ?? '-');
                    } else {
                        $sheet->setCellValue('P' . $row, '-');
                        $sheet->setCellValue('Q' . $row, '-');
                        $sheet->setCellValue('R' . $row, '-');
                        $sheet->setCellValue('S' . $row, '-');
                        $sheet->setCellValue('T' . $row, '-');
                        $sheet->setCellValue('U' . $row, '-');
                        $sheet->setCellValue('V' . $row, '-');
                        $sheet->setCellValue('W' . $row, '-');
                        $sheet->setCellValue('X' . $row, '-');
                    }

                    // Equipment data
                    if ($i < $equipmentCount && isset($equipment[$i])) {
                        $eq = $equipment[$i];
                        $eqType = '';
                        switch($eq->tpi_type) {
                            case 1: $eqType = 'Monitor'; break;
                            case 2: $eqType = 'Printer'; break;
                            case 3: $eqType = 'Scanner'; break;
                            default: $eqType = 'Unknown'; break;
                        }
                        $eqAge = $eq->tpi_receivedate ? $this->calculateAge($eq->tpi_receivedate) : '-';
                        
                        $sheet->setCellValue('Y' . $row, $eqType);
                        $sheet->setCellValue('Z' . $row, $eq->tpi_assetno ?? '-');
                        $sheet->setCellValue('AA' . $row, $eqAge);
                    } else {
                        $sheet->setCellValue('Y' . $row, '-');
                        $sheet->setCellValue('Z' . $row, '-');
                        $sheet->setCellValue('AA' . $row, '-');
                    }

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
                return $item->tpc_status == 0 || $item->tpc_status == 1;
            });

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
                'P4' => 'Ethernet',
                'Q4' => 'VM Name',
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
            
            $sheet->getStyle('B4:AC4')->applyFromArray($headerStyle);

            // Add data
            $row = 5;
            foreach ($pcs as $pc) {
                $lastUser = $this->getUserDisplayName($pc->tpc_lastuser);
                $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
                $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';

                // Get PC type display name
                $pcTypeNames = [
                    1 => 'Client',
                    2 => 'Server'
                ];
                $pcTypeName = $pcTypeNames[$pc->tpc_type] ?? 'Unknown';

                // Get PC specifications with new ethernet field
                $specs = $this->TransPCModel->getPCSpecs($pc->tpc_id);
                $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
                $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
                $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
                $vga = $specs ? ($specs->tps_vga ?? '-') : '-';
                $ethernet = $specs ? ($specs->tps_ethernet ?? '-') : '-';

                // Get IT Equipment and Server VM
                $equipment = $this->TransPCModel->getPCEquipment($pc->tpi_pcid);
                $serverVM = $this->TransPCModel->getPCServerVM($pc->tpv_pcid);

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
                        $sheet->setCellValue('K' . $row, $pc->tpc_status == 1 ? 'Active' : 'Inactive');
                        $sheet->setCellValue('L' . $row, $processor);
                        $sheet->setCellValue('M' . $row, $ram);
                        $sheet->setCellValue('N' . $row, $storage);
                        $sheet->setCellValue('O' . $row, $vga);
                        $sheet->setCellValue('P' . $row, $ethernet);
                        $sheet->setCellValue('AC' . $row, $lastUser);
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
                        switch($eq->tpi_type) {
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
            $sheet->getStyle('B5:AC' . ($row - 1))->applyFromArray($dataStyle);

            // Create ODS writer
            $writer = new Ods($spreadsheet);

            $filename = 'PC_Data_Complete_' . date('Y-m-d_H-i-s') . '.ods';

            header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Error exporting ODS: ' . $e->getMessage());
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
            $allPCs = $this->TransPCModel->getData('All');
            $pcs = array_filter($allPCs, function($item) use ($selectedIds) {
                return in_array($item->tpc_id, $selectedIds) && ($item->tpc_status == 0 || $item->tpc_status == 1);
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
            $sheet->mergeCells('B2:T2');
            $selectedCount = count($pcs);
            $sheet->setCellValue('B2', "Selected PC Data ({$selectedCount} records) with Specifications, Equipment & Server VM");
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('B2:T2')->applyFromArray($titleStyle);

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
                'P4' => 'VM Type',
                'Q4' => 'VM IP',
                'R4' => 'VM Services',
                'S4' => 'VM Remark',
                'T4' => 'Equipment Type',
                'U4' => 'Equipment Asset',
                'V4' => 'Equipment Age',
                'W4' => 'Last User'
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
            $sheet->getStyle('B4:W4')->applyFromArray($headerStyle);

            // Add data (same logic as original export but for selected data only)
            $row = 5;
            foreach ($pcs as $pc) {
                $lastUser = $this->getUserDisplayName($pc->tpc_lastuser);
                $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
                $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';

                // Get PC type display name
                $pcTypeNames = [
                    1 => 'Client',
                    2 => 'Server'
                ];
                $pcTypeName = $pcTypeNames[$pc->tpc_type] ?? 'Unknown';

                // Get PC specifications
                $specs = $this->TransPCModel->getPCSpecs($pc->tpc_id);
                $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
                $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
                $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
                $vga = $specs ? ($specs->tps_vga ?? '-') : '-';

                // Get IT Equipment and Server VM
                $equipment = $this->TransPCModel->getPCEquipment($pc->tpi_pcid);
                $serverVM = $this->TransPCModel->getPCServerVM($pc->tpv_pcid);

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
                        $sheet->setCellValue('K' . $row, $pc->tpc_status == 1 ? 'Active' : 'Inactive');
                        $sheet->setCellValue('L' . $row, $processor);
                        $sheet->setCellValue('M' . $row, $ram);
                        $sheet->setCellValue('N' . $row, $storage);
                        $sheet->setCellValue('O' . $row, $vga);
                        $sheet->setCellValue('W' . $row, $lastUser);
                    }

                    // Server VM data (if exists for this index)
                    if ($i < $vmCount && isset($serverVM[$i])) {
                        $vm = $serverVM[$i];
                        $vmType = $vm->tpv_type == 1 ? 'VM' : 'Non-VM';
                        $sheet->setCellValue('P' . $row, $vmType);
                        $sheet->setCellValue('Q' . $row, $vm->tpv_ipaddress ?? '-');
                        $sheet->setCellValue('R' . $row, $vm->tpv_services ?? '-');
                        $sheet->setCellValue('S' . $row, $vm->tpv_remark ?? '-');
                    } else {
                        $sheet->setCellValue('P' . $row, '-');
                        $sheet->setCellValue('Q' . $row, '-');
                        $sheet->setCellValue('R' . $row, '-');
                        $sheet->setCellValue('S' . $row, '-');
                    }

                    // Equipment data (if exists for this index)
                    if ($i < $equipmentCount && isset($equipment[$i])) {
                        $eq = $equipment[$i];
                        $eqType = '';
                        switch($eq->tpi_type) {
                            case 1: $eqType = 'Monitor'; break;
                            case 2: $eqType = 'Printer'; break;
                            case 3: $eqType = 'Scanner'; break;
                            default: $eqType = 'Unknown'; break;
                        }
                        $eqAge = $eq->tpi_receivedate ? $this->calculateAge($eq->tpi_receivedate) : '-';
                        
                        $sheet->setCellValue('T' . $row, $eqType);
                        $sheet->setCellValue('U' . $row, $eq->tpi_assetno ?? '-');
                        $sheet->setCellValue('V' . $row, $eqAge);
                    } else {
                        $sheet->setCellValue('T' . $row, '-');
                        $sheet->setCellValue('U' . $row, '-');
                        $sheet->setCellValue('V' . $row, '-');
                    }

                    $row++;
                }

                // Merge cells for PC basic data if there are multiple rows
                if ($maxRows > 1) {
                    $endRow = $startRow + $maxRows - 1;
                    
                    // Merge PC basic data columns
                    $mergeColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'W'];
                    foreach ($mergeColumns as $col) {
                        $sheet->mergeCells($col . $startRow . ':' . $col . $endRow);
                        // Center align merged cells vertically
                        $sheet->getStyle($col . $startRow . ':' . $col . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }
                }
            }

            // Auto-size columns
            foreach (range('B', 'W') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Style data rows
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ];
            $sheet->getStyle('B5:W' . ($row - 1))->applyFromArray($dataStyle);

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
            return in_array($item->tpc_id, $selectedIds) && ($item->tpc_status == 0 || $item->tpc_status == 1);
        });

        if (empty($pcs)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No valid PC records found for export.'
            ]);
        }
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Define headers
        $headers = [
            'ID', 'Type', 'PC Name', 'Asset No', 'Asset Age',
            'OS Name', 'IP Address', 'User', 'Location', 'Status',
            'Processor', 'RAM', 'Storage', 'VGA',
            'VM Type', 'VM IP', 'VM Services', 'VM Remark',
            'Equipment Type', 'Equipment Asset', 'Equipment Age', 'Last User'
        ];
        
        // Set headers
        $headerColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V'];
        for ($i = 0; $i < count($headers); $i++) {
            $sheet->setCellValue($headerColumns[$i] . '1', $headers[$i]);
        }
        
        // Add data (similar logic as XLSX but for CSV)
        $row = 2;
        foreach ($pcs as $pc) {
            $lastUser = $this->getUserDisplayName($pc->tpc_lastuser);
            $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
            $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';
            
            // Get PC type display name
            $pcTypeNames = [
                1 => 'Client',
                2 => 'Server'
            ];
            $pcTypeName = $pcTypeNames[$pc->tpc_type] ?? 'Unknown';

            // Get PC specifications
            $specs = $this->TransPCModel->getPCSpecs($pc->tpc_id);
            $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
            $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
            $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
            $vga = $specs ? ($specs->tps_vga ?? '-') : '-';

            // Get IT Equipment and Server VM
            $equipment = $this->TransPCModel->getPCEquipment($pc->tpi_pcid);
            $serverVM = $this->TransPCModel->getPCServerVM($pc->tpv_pcid);

            // Calculate total rows needed for this PC
            $equipmentCount = $equipment ? count($equipment) : 0;
            $vmCount = $serverVM ? count($serverVM) : 0;
            $maxRows = max($equipmentCount, $vmCount, 1); // At least 1 row
            
            // Fill data for each row
            for ($i = 0; $i < $maxRows; $i++) {
                // PC basic data (only on first row, empty on subsequent rows for CSV readability)
                if ($i == 0) {
                    $sheet->setCellValue('A' . $row, $pc->tpc_id);
                    $sheet->setCellValue('B' . $row, $pcTypeName);
                    $sheet->setCellValue('C' . $row, $pc->tpc_name ?? '-');
                    $sheet->setCellValue('D' . $row, $pc->tpc_assetno ?? '-');
                    $sheet->setCellValue('E' . $row, $pcAge);
                    $sheet->setCellValue('F' . $row, $pc->tpc_osname ?? '-');
                    $sheet->setCellValue('G' . $row, $pc->tpc_ipaddress ?? '-');
                    $sheet->setCellValue('H' . $row, $pc->tpc_user ?? '-');
                    $sheet->setCellValue('I' . $row, $pc->tpc_location ?? '-');
                    $sheet->setCellValue('J' . $row, $pc->tpc_status == 1 ? 'Active' : 'Inactive');
                    $sheet->setCellValue('K' . $row, $processor);
                    $sheet->setCellValue('L' . $row, $ram);
                    $sheet->setCellValue('M' . $row, $storage);
                    $sheet->setCellValue('N' . $row, $vga);
                    $sheet->setCellValue('V' . $row, $lastUser);
                } else {
                    // Empty cells for subsequent rows
                    foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'V'] as $col) {
                        $sheet->setCellValue($col . $row, '');
                    }
                }

                // Server VM data
                if ($i < $vmCount && isset($serverVM[$i])) {
                    $vm = $serverVM[$i];
                    $vmType = $vm->tpv_type == 1 ? 'VM' : 'Non-VM';
                    $sheet->setCellValue('O' . $row, $vmType);
                    $sheet->setCellValue('P' . $row, $vm->tpv_ipaddress ?? '-');
                    $sheet->setCellValue('Q' . $row, $vm->tpv_services ?? '-');
                    $sheet->setCellValue('R' . $row, $vm->tpv_remark ?? '-');
                } else {
                    $sheet->setCellValue('O' . $row, '-');
                    $sheet->setCellValue('P' . $row, '-');
                    $sheet->setCellValue('Q' . $row, '-');
                    $sheet->setCellValue('R' . $row, '-');
                }

                // Equipment data
                if ($i < $equipmentCount && isset($equipment[$i])) {
                    $eq = $equipment[$i];
                    $eqType = '';
                    switch($eq->tpi_type) {
                        case 1: $eqType = 'Monitor'; break;
                        case 2: $eqType = 'Printer'; break;
                        case 3: $eqType = 'Scanner'; break;
                        default: $eqType = 'Unknown'; break;
                    }
                    $eqAge = $eq->tpi_receivedate ? $this->calculateAge($eq->tpi_receivedate) : '-';
                    
                    $sheet->setCellValue('S' . $row, $eqType);
                    $sheet->setCellValue('T' . $row, $eq->tpi_assetno ?? '-');
                    $sheet->setCellValue('U' . $row, $eqAge);
                } else {
                    $sheet->setCellValue('S' . $row, '-');
                    $sheet->setCellValue('T' . $row, '-');
                    $sheet->setCellValue('U' . $row, '-');
                }

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
                return in_array($item->tpc_id, $selectedIds) && ($item->tpc_status == 0 || $item->tpc_status == 1);
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
            $sheet->mergeCells('B2:W2');
            $selectedCount = count($pcs);
            $sheet->setCellValue('B2', "Selected PC Data ({$selectedCount} records) with Specifications, Equipment & Server VM");
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('B2:W2')->applyFromArray($titleStyle);

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
                'P4' => 'VM Type',
                'Q4' => 'VM IP',
                'R4' => 'VM Services',
                'S4' => 'VM Remark',
                'T4' => 'Equipment Type',
                'U4' => 'Equipment Asset',
                'V4' => 'Equipment Age',
                'W4' => 'Last User'
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
            
            $sheet->getStyle('B4:W4')->applyFromArray($headerStyle);

            // Add data (same logic as XLSX)
            $row = 5;
            foreach ($pcs as $pc) {
                $lastUser = $this->getUserDisplayName($pc->tpc_lastuser);
                $pcReceiveDate = $pc->tpc_pcreceivedate ?? '-';
                $pcAge = ($pcReceiveDate !== '-') ? $this->calculateAge($pcReceiveDate) : '-';

                // Get PC type display name
                $pcTypeNames = [
                    1 => 'Client',
                    2 => 'Server'
                ];
                $pcTypeName = $pcTypeNames[$pc->tpc_type] ?? 'Unknown';

                // Get PC specifications
                $specs = $this->TransPCModel->getPCSpecs($pc->tpc_id);
                $processor = $specs ? ($specs->tps_processor ?? '-') : '-';
                $ram = $specs ? ($specs->tps_ram ?? '-') : '-';
                $storage = $specs ? ($specs->tps_storage ?? '-') : '-';
                $vga = $specs ? ($specs->tps_vga ?? '-') : '-';

                // Get IT Equipment and Server VM
                $equipment = $this->TransPCModel->getPCEquipment($pc->tpi_pcid);
                $serverVM = $this->TransPCModel->getPCServerVM($pc->tpv_pcid);

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
                        $sheet->setCellValue('K' . $row, $pc->tpc_status == 1 ? 'Active' : 'Inactive');
                        $sheet->setCellValue('L' . $row, $processor);
                        $sheet->setCellValue('M' . $row, $ram);
                        $sheet->setCellValue('N' . $row, $storage);
                        $sheet->setCellValue('O' . $row, $vga);
                        $sheet->setCellValue('W' . $row, $lastUser);
                    }

                    // Server VM data (if exists for this index)
                    if ($i < $vmCount && isset($serverVM[$i])) {
                        $vm = $serverVM[$i];
                        $vmType = $vm->tpv_type == 1 ? 'VM' : 'Non-VM';
                        $sheet->setCellValue('P' . $row, $vmType);
                        $sheet->setCellValue('Q' . $row, $vm->tpv_ipaddress ?? '-');
                        $sheet->setCellValue('R' . $row, $vm->tpv_services ?? '-');
                        $sheet->setCellValue('S' . $row, $vm->tpv_remark ?? '-');
                    } else {
                        $sheet->setCellValue('P' . $row, '-');
                        $sheet->setCellValue('Q' . $row, '-');
                        $sheet->setCellValue('R' . $row, '-');
                        $sheet->setCellValue('S' . $row, '-');
                    }

                    // Equipment data (if exists for this index)
                    if ($i < $equipmentCount && isset($equipment[$i])) {
                        $eq = $equipment[$i];
                        $eqType = '';
                        switch($eq->tpi_type) {
                            case 1: $eqType = 'Monitor'; break;
                            case 2: $eqType = 'Printer'; break;
                            case 3: $eqType = 'Scanner'; break;
                            default: $eqType = 'Unknown'; break;
                        }
                        $eqAge = $eq->tpi_receivedate ? $this->calculateAge($eq->tpi_receivedate) : '-';
                        
                        $sheet->setCellValue('T' . $row, $eqType);
                        $sheet->setCellValue('U' . $row, $eq->tpi_assetno ?? '-');
                        $sheet->setCellValue('V' . $row, $eqAge);
                    } else {
                        $sheet->setCellValue('T' . $row, '-');
                        $sheet->setCellValue('U' . $row, '-');
                        $sheet->setCellValue('V' . $row, '-');
                    }

                    $row++;
                }

                // Merge cells for PC basic data if there are multiple rows
                if ($maxRows > 1) {
                    $endRow = $startRow + $maxRows - 1;
                    
                    // Merge PC basic data columns
                    $mergeColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'W'];
                    foreach ($mergeColumns as $col) {
                        $sheet->mergeCells($col . $startRow . ':' . $col . $endRow);
                        // Center align merged cells vertically
                        $sheet->getStyle($col . $startRow . ':' . $col . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }
                }
            }

            // Auto-size columns
            foreach (range('B', 'W') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Style data rows
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ];
            $sheet->getStyle('B5:W' . ($row - 1))->applyFromArray($dataStyle);

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