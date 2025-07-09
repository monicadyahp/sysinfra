<?php

namespace App\Controllers\TransSwitchManaged;

use App\Controllers\BaseController;
use App\Models\transswitchmanaged\TransSwitchManagedModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransSwitchManagedController extends BaseController
{
    protected $TransSwitchManagedModel;

    public function __construct()
    {
        $this->TransSwitchManagedModel = new TransSwitchManagedModel();
        helper('session');
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $usermenu = session()->get("usermenu");
        $activeMenuGroup = 'Transaction';
        $activeMenuName = 'Switch Managed';

        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "TransSwitchManaged") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }

        $data["active_menu_group"] = $activeMenuGroup;
        $data["active_menu_name"] = $activeMenuName;

        return view('transaction/TransSwitchManaged/index', $data);
    }

    public function getData()
    {
        $statusFilter = $this->request->getGet('status');
        $switches = $this->TransSwitchManagedModel->getData($statusFilter);

        // Format the data for DataTables
        $formattedData = [];
        foreach ($switches as $switch) {
            // Get user display name
            $lastUser = $this->TransSwitchManagedModel->getUserDisplayName($switch->tsm_lastuser);

            $formattedData[] = [
                'tsm_id' => $switch->tsm_id,
                'tsm_assetno' => $switch->tsm_assetno,
                'tsm_assetname' => $switch->tsm_assetname,
                'tsm_receivedate' => $switch->tsm_receivedate,
                'tsm_ipaddress' => $switch->tsm_ipaddress,
                'tsm_location' => $switch->tsm_location,
                'tsm_port' => $switch->tsm_port,
                'tsm_lastupdate' => $switch->tsm_lastupdate,
                'tsm_lastuser' => $lastUser
            ];
        }

        return $this->response->setJSON($formattedData);
    }

    public function getSwitchManagedById()
    {
        $id = $this->request->getGet('id');

        if (empty($id)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Switch ID is required'
            ]);
        }

        $switch = $this->TransSwitchManagedModel->getSwitchManagedById($id);

        if ($switch) {
            // Get user display name
            $lastUser = $this->TransSwitchManagedModel->getUserDisplayName($switch->tsm_lastuser);

            $formattedSwitch = [
                'tsm_id' => $switch->tsm_id,
                'tsm_assetno' => $switch->tsm_assetno,
                'tsm_assetname' => $switch->tsm_assetname,
                'tsm_receivedate' => $switch->tsm_receivedate,
                'tsm_ipaddress' => $switch->tsm_ipaddress,
                'tsm_location' => $switch->tsm_location,
                'tsm_port' => $switch->tsm_port,
                'tsm_lastupdate' => $switch->tsm_lastupdate,
                'tsm_lastuser' => $lastUser
            ];

            return $this->response->setJSON([
                'status' => true,
                'data' => $formattedSwitch
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Switch not found'
            ]);
        }
    }

    public function store()
    {
        $data = $this->request->getPost();

        // Validate input
        if (empty($data['receive_date'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Receive Date is required.'
            ]);
        }

        // Validate port count
        if (empty($data['port_count']) || !is_numeric($data['port_count'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Port Count is required and must be a number.'
            ]);
        }

        $portCount = (int)$data['port_count'];
        if ($portCount < 1 ) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Max port must be more than 1.'
            ]);
        }

        // Validasi IP address di sisi server
        // Dapatkan detail IP dari master tanpa peduli status awal
        // Validasi IP address di sisi server
        $ipAddress = $data['ip'] ?? null;
        if (!empty($ipAddress)) {
            // PERBAIKAN DI SINI: Panggil method dari instance model
            $ipRecord = $this->TransSwitchManagedModel->getIPAddressByIP($ipAddress); // <-- Pastikan ada $this->TransSwitchManagedModel->

            if (!$ipRecord) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Selected IP Address is not found in IP Master. Please ensure it exists.'
                ]);
            }

            // Menurut definisi baru: status 1 = 'Unused', status 0 = 'Used', status 25 = 'Soft Deleted'
            // Hanya izinkan pemilihan jika statusnya adalah 'Unused' (1)
            if ($ipRecord->mip_status == 0) { // Jika statusnya 0 (Used)
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Selected IP Address is already in use. Please select an unused IP.'
                ]);
            } else if ($ipRecord->mip_status == 25) { // Jika statusnya 25 (Soft Deleted)
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Selected IP Address is soft-deleted and cannot be used.'
                ]);
            }
        }

        // Validasi IP address sebelum menyimpan
        // Validasi IP address di sisi server
        $ipAddress = $data['ip'] ?? null;
        if (!empty($ipAddress)) {
            $ipRecord = $this->TransSwitchManagedModel->getIPAddressByIP($ipAddress);

            if (!$ipRecord) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Selected IP Address is not found in IP Master. Please ensure it exists.'
                ]);
            }

            // Menurut definisi baru: status 1 = 'Unused', status 0 = 'Used', status 25 = 'Soft Deleted'
            // Hanya izinkan pemilihan jika statusnya adalah 'Unused' (1)
            if ($ipRecord->mip_status == 0) { // Jika statusnya 0 (Used)
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Selected IP Address is already in use. Please select an unused IP.'
                ]);
            } else if ($ipRecord->mip_status == 25) { // Jika statusnya 25 (Soft Deleted)
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Selected IP Address is soft-deleted and cannot be used.'
                ]);
            }
            // Tidak perlu memanggil isIpUsedByAnyActiveDevice di sini,
            // karena kita sudah memvalidasi mip_status di atas.
            // isIpUsedByAnyActiveDevice digunakan oleh handleIPStatusUpdate untuk pelepasan IP lama.
        }

        $result = $this->TransSwitchManagedModel->storeData($data);
        return $this->response->setJSON($result);
    }

    public function update()
    {
        $data = $this->request->getPost();

        // Validate input
        if (empty($data['tsm_id']) || empty($data['receive_date'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Switch ID and Receive Date are required.'
            ]);
        }

        // Validate port count
        if (empty($data['port_count']) || !is_numeric($data['port_count'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Port Count is required and must be a number.'
            ]);
        }

        $portCount = (int)$data['port_count'];
        if ($portCount < 1) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Max port must be more than 1.'
            ]);
        }

        // Get old switch data to compare IP address
        $oldSwitch = $this->TransSwitchManagedModel->getSwitchManagedById($data['tsm_id']);
        $oldIPAddress = $oldSwitch ? $oldSwitch->tsm_ipaddress : '';
        $newIPAddress = $data['ip'] ?? '';

        // Only validate if there's a change in IP address
        // Validate IP address availability and status if there's a change
        // Validasi IP address di sisi server jika ada perubahan IP
        // Validasi IP address jika ada perubahan
        // Validasi IP address jika ada perubahan
        $ipAddress = $data['ip'] ?? null;
        if (!empty($ipAddress) && $ipAddress !== $oldIPAddress) {
            // PERBAIKAN DI SINI: Panggil method dari instance model
            $ipRecord = $this->TransSwitchManagedModel->getIPAddressByIP($ipAddress); // <-- Pastikan ada $this->TransSwitchManagedModel->

            if (!$ipRecord) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Selected IP Address is not found in IP Master. Please ensure it exists.'
                ]);
            }

            // Menurut definisi baru: status 1 = 'Unused', status 0 = 'Used', status 25 = 'Soft Deleted'
            // Hanya izinkan pemilihan jika statusnya adalah 'Unused' (1)
            // KECUALI jika IP yang dipilih adalah IP LAMA dari switch ini.
            if ($ipRecord->mip_status == 0 && $ipAddress !== $oldIPAddress) { // Jika statusnya 0 (Used) DAN ini BUKAN IP lama
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Selected IP Address is already in use. Please select an unused IP.'
                ]);
            } else if ($ipRecord->mip_status == 25) { // Jika statusnya 25 (Soft Deleted)
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Selected IP Address is soft-deleted and cannot be used.'
                ]);
            }
        }

        $result = $this->TransSwitchManagedModel->updateData($data);
        return $this->response->setJSON($result);
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        
        if (empty($id)) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID not found']);
        }

        $result = $this->TransSwitchManagedModel->deleteData($id);
        return $this->response->setJSON($result);
    }

    public function searchAssetNo()
    {
        $assets = $this->TransSwitchManagedModel->searchAssetNo();

        // Process the data to ensure display_asset_no is used consistently
        $processedData = [];
        foreach ($assets as $asset) {
            $processedAsset = (object)[
                'e_assetno' => $asset->display_asset_no,
                'e_equipmentid' => $asset->e_equipmentid,
                'e_serialnumber' => $asset->e_serialnumber,
                'e_equipmentname' => $asset->e_equipmentname,
                'e_receivedate' => $asset->e_receivedate,
                'original_assetno' => $asset->e_assetno,
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

        $assetDetails = $this->TransSwitchManagedModel->getAssetNo($assetNo);

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

    // IP Address methods
    public function getIPAddresses()
    {
        $ipAddress = $this->request->getGet('ipAddress');
                            
        if (empty($ipAddress)) {
            return $this->response->setJSON(['status' => false, 'message' => 'IP Address is required']);
        }
                            
        $ip = $this->TransSwitchManagedModel->getIPAddressByIP($ipAddress);
                            
        return $this->response->setJSON([
            'status' => ($ip !== null),
            'data' => $ip
        ]);
    }

    public function searchIPAddresses()
    {
        $statusFilter = $this->request->getGet('status'); // Pastikan ini mengambil parameter 'status' dari URL GET
        $ipAddresses = $this->TransSwitchManagedModel->searchIPAddresses($statusFilter);
        
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
            $result = $this->TransSwitchManagedModel->updateIPStatus($ipAddress, $status);
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

    public function getSwitchDetails()
    {
        $id = $this->request->getGet('id');
        
        if (empty($id)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Switch ID is required'
            ]);
        }
        
        $details = $this->TransSwitchManagedModel->getSwitchManagedFullDetails($id);
        
        if ($details) {
            // Format switch data
            $switch = $details['switch'];
            $formattedSwitch = [
                'tsm_id' => $switch->tsm_id,
                'tsm_assetno' => $switch->tsm_assetno,
                'tsm_assetname' => $switch->tsm_assetname,
                'tsm_receivedate' => $switch->tsm_receivedate,
                'tsm_age' => $switch->tsm_age,
                'tsm_ipaddress' => $switch->tsm_ipaddress,
                'tsm_location' => $switch->tsm_location,
                'tsm_port' => $switch->tsm_port,
                'tsm_lastuser' => $this->TransSwitchManagedModel->getUserDisplayName($switch->tsm_lastuser),
                'tsm_lastupdate' => $switch->tsm_lastupdate
            ];
            
            // Format ports data
            $formattedPorts = [];
            foreach ($details['ports'] as $port) {
                $formattedPorts[] = [
                    'tsd_id' => $port['tsd_id'],
                    'tsd_port' => $port['tsd_port'],
                    'tsd_type' => $port['tsd_type'],
                    'tsd_vlanid' => $port['tsd_vlanid'],
                    'tsd_vlanname' => $port['tsd_vlanname'],
                    'tsd_status' => $port['tsd_status'],
                    'tsd_lastuser' => $this->TransSwitchManagedModel->getUserDisplayName($port['tsd_lastuser']),
                    'tsd_lastupdate' => $port['tsd_lastupdate']
                ];
            }
            
            return $this->response->setJSON([
                'status' => true,
                'data' => [
                    'switch' => $formattedSwitch,
                    'ports' => $formattedPorts
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Switch not found'
            ]);
        }
    }

    public function getVlanData()
    {
        try {            
            // Gunakan method model yang baru
            $data = $this->TransSwitchManagedModel->getVlanData();

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching VLAN data: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => true, 'message' => 'Could not retrieve VLAN data: ' . $e->getMessage()]);
        }
    }

    public function getVlanById()
    {
        $vlanId = $this->request->getGet('vlanId');
        
        if (empty($vlanId)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'VLAN ID is required'
            ]);
        }
        
        try {
            $vlanData = $this->TransSwitchManagedModel->getVlanById($vlanId);
            
            if ($vlanData) {
                return $this->response->setJSON([
                    'status' => true,
                    'data' => $vlanData
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'VLAN not found'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error fetching VLAN by ID: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => false, 'message' => 'Could not retrieve VLAN data']);
        }
    }

    public function getlocations()
    {
        $locations = $this->TransSwitchManagedModel->getlocations();
        return $this->response->setJSON($locations);
    }

    public function getSwitchDetailPortData()
    {
        $tsd_switchid = $this->request->getGet('tsd_switchid');
        
        if (empty($tsd_switchid) || !is_numeric($tsd_switchid) || (int)$tsd_switchid <= 0) {
            log_message('error', 'Invalid tsd_switchid provided to getSwitchDetailPortData: ' . $tsd_switchid);
            return $this->response->setJSON([]);
        }

        try {
            $data = $this->TransSwitchManagedModel->getSwitchDetailPortsByHeaderId((int)$tsd_switchid);

            // Format data and populate user display names
            $formattedData = [];
            foreach ($data as $row) {
                $lastUser = $this->TransSwitchManagedModel->getUserDisplayName($row->tsd_lastuser);

                // Resolve VLAN Name from lookup if tsd_vlanname is empty or null
                $resolvedVlanName = $row->tsd_vlanname;
                if (empty($resolvedVlanName) && !empty($row->tsd_vlanid)) {
                    $vlanData = $this->TransSwitchManagedModel->getVlanData();
                    foreach ($vlanData as $vlan) {
                        if ($vlan['mv_vlanid'] == $row->tsd_vlanid) {
                            $resolvedVlanName = $vlan['mv_name'];
                            break;
                        }
                    }
                }

                $formattedData[] = [
                    'tsd_id' => $row->tsd_id,
                    'tsd_switchid' => $row->tsd_switchid,
                    'tsd_port' => $row->tsd_port,
                    'tsd_type' => $row->tsd_type,
                    'tsd_vlanid' => $row->tsd_vlanid,
                    'tsd_vlanname' => $resolvedVlanName,
                    'tsd_status' => $row->tsd_status,
                    'tsd_lastupdate' => $row->tsd_lastupdate,
                    'tsd_lastuser' => $row->tsd_lastuser,
                    'last_user_display' => $lastUser
                ];
            }

            return $this->response->setJSON($formattedData);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching switch detail ports for tsd_switchid ' . $tsd_switchid . ': ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => true, 'message' => 'Could not retrieve switch detail ports: ' . $e->getMessage()]);
        }
    }

    public function countSwitchDetailPorts()
    {
        $tsd_switchid = $this->request->getGet('tsd_switchid');
        
        if (empty($tsd_switchid) || !is_numeric($tsd_switchid)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid Switch ID']);
        }

        try {
            $count = $this->TransSwitchManagedModel->countSwitchDetailPortsByHeaderId((int)$tsd_switchid);
            return $this->response->setJSON(['status' => true, 'count' => $count]);
        } catch (\Exception $e) {
            log_message('error', 'Error counting switch detail ports: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => false, 'error' => 'Could not count switch detail ports']);
        }
    }

    public function getAvailablePorts()
    {
        $tsd_switchid = $this->request->getGet('tsd_switchid');
        
        if (empty($tsd_switchid)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Switch ID is required'
            ]);
        }

        try {
            $availablePorts = $this->TransSwitchManagedModel->getAvailablePortsForSwitch($tsd_switchid);
            
            return $this->response->setJSON([
                'status' => true,
                'data' => $availablePorts
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting available ports: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error getting available ports'
            ]);
        }
    }

    public function storeSwitchDetailPort()
    {
        $data = $this->request->getPost();

        // Basic validation like switch managed methods
        if (empty($data['header_id_switch'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Switch ID is required.'
            ]);
        }

        if (empty($data['vlan_id'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'VLAN ID is required.'
            ]);
        }

        // Validate VLAN ID is numeric
        if (!is_numeric($data['vlan_id'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'VLAN ID must be a number.'
            ]);
        }

        // Validate port if provided
        if (!empty($data['port']) && (!is_numeric($data['port']) || (int)$data['port'] < 1)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Port number must be greater than 0.'
            ]);
        }

        // Validate status
        if (!isset($data['status']) || !in_array($data['status'], ['0', '1'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Status is required and must be either Active or Inactive.'
            ]);
        }

        // Validate type if provided
        if (!empty($data['type']) && !in_array($data['type'], ['ethernet', 'SFP'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Type must be either ethernet or SFP.'
            ]);
        }

        $portData = [
            'tsd_switchid' => $data['header_id_switch'],
            'tsd_port' => !empty($data['port']) ? $data['port'] : null,
            'tsd_type' => !empty($data['type']) ? $data['type'] : null,
            'tsd_vlanid' => $data['vlan_id'],
            'tsd_vlanname' => !empty($data['vlan_name']) ? $data['vlan_name'] : null,
            'tsd_status' => $data['status']
        ];

        $result = $this->TransSwitchManagedModel->storeSwitchDetailPort($portData);
        return $this->response->setJSON($result);
    }

    public function getSwitchDetailPortById()
    {
        $tsd_id = $this->request->getPost('tsd_id');
        
        if (empty($tsd_id)) {
            return $this->response->setJSON([
                'status' => false, 
                'message' => 'Port Detail ID is required'
            ]);
        }

        $row = $this->TransSwitchManagedModel->getSwitchDetailPortById((int)$tsd_id);

        if (!$row) {
            return $this->response->setJSON([
                'status' => false, 
                'message' => 'Detail port not found'
            ]);
        }

        $lastUserDisplayName = $this->TransSwitchManagedModel->getUserDisplayName($row->tsd_lastuser);

        // Populate VLAN Name if VLAN ID exists but VLAN Name is empty
        $vlanNameDisplay = $row->tsd_vlanname ?? '';
        if (empty($vlanNameDisplay) && !empty($row->tsd_vlanid)) {
            $vlanData = $this->TransSwitchManagedModel->getVlanData();
            foreach ($vlanData as $vlan) {
                if ($vlan['mv_vlanid'] == $row->tsd_vlanid) {
                    $vlanNameDisplay = $vlan['mv_name'];
                    break;
                }
            }
        }

        $response_data = [
            'tsd_id' => $row->tsd_id,
            'tsd_switchid' => $row->tsd_switchid,
            'tsd_port' => $row->tsd_port,
            'tsd_type' => $row->tsd_type,
            'tsd_vlanid' => $row->tsd_vlanid,
            'tsd_vlanname' => $vlanNameDisplay,
            'tsd_status' => $row->tsd_status,
            'tsd_lastupdate' => $row->tsd_lastupdate,
            'tsd_lastuser' => $lastUserDisplayName
        ];

        return $this->response->setJSON(['status' => true, 'data' => $response_data]);
    }

    public function updateSwitchDetailPort()
    {
        $data = $this->request->getPost();
        
        // Basic validation like switch managed methods
        if (empty($data['tsd_id'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Port Detail ID is required.'
            ]);
        }

        if (empty($data['header_id_switch'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Switch ID is required.'
            ]);
        }

        if (empty($data['vlan_id'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'VLAN ID is required.'
            ]);
        }

        // Validate VLAN ID is numeric
        if (!is_numeric($data['vlan_id'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'VLAN ID must be a number.'
            ]);
        }

        // Validate port if provided
        if (!empty($data['port']) && (!is_numeric($data['port']) || (int)$data['port'] < 1)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Port number must be greater than 0.'
            ]);
        }

        // Validate status
        if (!isset($data['status']) || !in_array($data['status'], ['0', '1'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Status is required and must be either Active or Inactive.'
            ]);
        }

        // Validate type if provided
        if (!empty($data['type']) && !in_array($data['type'], ['ethernet', 'SFP'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Type must be either ethernet or SFP.'
            ]);
        }

        $portData = [
            'tsd_switchid' => $data['header_id_switch'],
            'tsd_port' => !empty($data['port']) ? $data['port'] : null,
            'tsd_type' => !empty($data['type']) ? $data['type'] : null,
            'tsd_vlanid' => $data['vlan_id'],
            'tsd_vlanname' => !empty($data['vlan_name']) ? $data['vlan_name'] : null,
            'tsd_status' => $data['status']
        ];

        $result = $this->TransSwitchManagedModel->updateSwitchDetailPort((int)$data['tsd_id'], $portData);
        return $this->response->setJSON($result);
    }

    public function deleteSwitchDetailPort()
    {
        $tsd_id = $this->request->getPost('tsd_id');
        
        if (empty($tsd_id)) {
            return $this->response->setJSON([
                'status' => false, 
                'message' => 'Port Detail ID is required'
            ]);
        }

        $result = $this->TransSwitchManagedModel->deleteSwitchDetailPort((int)$tsd_id);
        return $this->response->setJSON($result);
    }

    public function exportXLSX()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        try {
            $switches = $this->TransSwitchManagedModel->getData();
            
            // Filter only active/inactive switches
            $switches = array_filter($switches, function($item) {
                return $item->tsm_status == 0 || $item->tsm_status == 1;
            });

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("Switch Management System")
                ->setTitle("Switch Data Export")
                ->setDescription("Export of Switch data in XLSX format");

            // Set title
            $sheet->mergeCells('B2:N2');
            $sheet->setCellValue('B2', 'Switch Managed Data with Port Details');
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('B2:N2')->applyFromArray($titleStyle);

            // Define and set headers
            $headers = [
                'B4' => 'ID',
                'C4' => 'Asset No',
                'D4' => 'Asset Name',
                'E4' => 'Asset Age',
                'F4' => 'Max Port',
                'G4' => 'IP Address',
                'H4' => 'Location',
                'I4' => 'Status',
                'J4' => 'Port Number',
                'K4' => 'Port Type',
                'L4' => 'VLAN ID',
                'M4' => 'VLAN Name',
                'N4' => 'Last User'
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
            $sheet->getStyle('B4:N4')->applyFromArray($headerStyle);

            // Add data
            $row = 5;
            foreach ($switches as $switch) {
                $lastUser = $this->TransSwitchManagedModel->getUserDisplayName($switch->tsm_lastuser);
                $switchReceiveDate = $switch->tsm_receivedate ?? '-';
                $switchAge = ($switchReceiveDate !== '-') ? $this->calculateAge($switchReceiveDate) : '-';

                // Get switch port details
                $ports = $this->TransSwitchManagedModel->getSwitchDetailPortsByHeaderId($switch->tsm_id);

                if (!empty($ports)) {
                    $startRow = $row;
                    foreach ($ports as $port) {
                        // Switch basic data (only on first port row)
                        if ($port === $ports[0]) {
                            $sheet->setCellValue('B' . $row, $switch->tsm_id);
                            $sheet->setCellValue('C' . $row, $switch->tsm_assetno ?? '-');
                            $sheet->setCellValue('D' . $row, $switch->tsm_assetname ?? '-');
                            $sheet->setCellValue('E' . $row, $switchAge);
                            $sheet->setCellValue('F' . $row, $switch->tsm_port ?? '-');
                            $sheet->setCellValue('G' . $row, $switch->tsm_ipaddress ?? '-');
                            $sheet->setCellValue('H' . $row, $switch->tsm_location ?? '-');
                            $sheet->setCellValue('I' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                            $sheet->setCellValue('N' . $row, $lastUser);
                        }

                        // Port data
                        $sheet->setCellValue('J' . $row, $port['tsd_port'] ?? '-');
                        $sheet->setCellValue('K' . $row, $port['tsd_type'] ?? '-');
                        $sheet->setCellValue('L' . $row, $port['tsd_vlanid'] ?? '-');
                        $sheet->setCellValue('M' . $row, $port['tsd_vlanname'] ?? '-');

                        $row++;
                    }

                    // Merge cells for switch basic data if there are multiple ports
                    if (count($ports) > 1) {
                        $endRow = $startRow + count($ports) - 1;
                        $mergeColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'N'];
                        foreach ($mergeColumns as $col) {
                            $sheet->mergeCells($col . $startRow . ':' . $col . $endRow);
                            $sheet->getStyle($col . $startRow . ':' . $col . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        }
                    }
                } else {
                    // Switch without ports
                    $sheet->setCellValue('B' . $row, $switch->tsm_id);
                    $sheet->setCellValue('C' . $row, $switch->tsm_assetno ?? '-');
                    $sheet->setCellValue('D' . $row, $switch->tsm_assetname ?? '-');
                    $sheet->setCellValue('E' . $row, $switchAge);
                    $sheet->setCellValue('F' . $row, $switch->tsm_port ?? '-');
                    $sheet->setCellValue('G' . $row, $switch->tsm_ipaddress ?? '-');
                    $sheet->setCellValue('H' . $row, $switch->tsm_location ?? '-');
                    $sheet->setCellValue('I' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                    $sheet->setCellValue('J' . $row, '-');
                    $sheet->setCellValue('K' . $row, '-');
                    $sheet->setCellValue('L' . $row, '-');
                    $sheet->setCellValue('M' . $row, '-');
                    $sheet->setCellValue('N' . $row, $lastUser);
                    $row++;
                }
            }

            // Auto-size columns
            foreach (range('B', 'N') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Style data rows
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ];
            $sheet->getStyle('B5:N' . ($row - 1))->applyFromArray($dataStyle);

            // Create writer and output
            $writer = new Xlsx($spreadsheet);

            $filename = 'Switch_Managed_Data_' . date('Y-m-d_H-i-s') . '.xlsx';

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
            $switches = $this->TransSwitchManagedModel->getData();
            
            // Filter only active/inactive switches
            $switches = array_filter($switches, function($item) {
                return $item->tsm_status == 0 || $item->tsm_status == 1;
            });
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Define headers
            $headers = [
                'ID', 'Asset No', 'Asset Name', 'Asset Age', 'Max Port',
                'IP Address', 'Location', 'Status',
                'Port Number', 'Port Type', 'VLAN ID', 'VLAN Name', 'Last User'
            ];
            
            // Set headers
            $headerColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
            for ($i = 0; $i < count($headers); $i++) {
                $sheet->setCellValue($headerColumns[$i] . '1', $headers[$i]);
            }
            
            // Add data
            $row = 2;
            foreach ($switches as $switch) {
                $lastUser = $this->TransSwitchManagedModel->getUserDisplayName($switch->tsm_lastuser);
                $switchReceiveDate = $switch->tsm_receivedate ?? '-';
                $switchAge = ($switchReceiveDate !== '-') ? $this->calculateAge($switchReceiveDate) : '-';
                
                // Get switch port details
                $ports = $this->TransSwitchManagedModel->getSwitchDetailPortsByHeaderId($switch->tsm_id);

                if (!empty($ports)) {
                    foreach ($ports as $index => $port) {
                        // Switch basic data (only on first port row)
                        if ($index === 0) {
                            $sheet->setCellValue('A' . $row, $switch->tsm_id);
                            $sheet->setCellValue('B' . $row, $switch->tsm_assetno ?? '-');
                            $sheet->setCellValue('C' . $row, $switch->tsm_assetname ?? '-');
                            $sheet->setCellValue('D' . $row, $switchAge);
                            $sheet->setCellValue('E' . $row, $switch->tsm_port ?? '-');
                            $sheet->setCellValue('F' . $row, $switch->tsm_ipaddress ?? '-');
                            $sheet->setCellValue('G' . $row, $switch->tsm_location ?? '-');
                            $sheet->setCellValue('H' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                            $sheet->setCellValue('M' . $row, $lastUser);
                        } else {
                            // Empty cells for subsequent rows
                            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'M'] as $col) {
                                $sheet->setCellValue($col . $row, '');
                            }
                        }

                        // Port data
                        $sheet->setCellValue('I' . $row, $port['tsd_port'] ?? '-');
                        $sheet->setCellValue('J' . $row, $port['tsd_type'] ?? '-');
                        $sheet->setCellValue('K' . $row, $port['tsd_vlanid'] ?? '-');
                        $sheet->setCellValue('L' . $row, $port['tsd_vlanname'] ?? '-');

                        $row++;
                    }
                } else {
                    // Switch without ports
                    $sheet->setCellValue('A' . $row, $switch->tsm_id);
                    $sheet->setCellValue('B' . $row, $switch->tsm_assetno ?? '-');
                    $sheet->setCellValue('C' . $row, $switch->tsm_assetname ?? '-');
                    $sheet->setCellValue('D' . $row, $switchAge);
                    $sheet->setCellValue('E' . $row, $switch->tsm_port ?? '-');
                    $sheet->setCellValue('F' . $row, $switch->tsm_ipaddress ?? '-');
                    $sheet->setCellValue('G' . $row, $switch->tsm_location ?? '-');
                    $sheet->setCellValue('H' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                    $sheet->setCellValue('I' . $row, '-');
                    $sheet->setCellValue('J' . $row, '-');
                    $sheet->setCellValue('K' . $row, '-');
                    $sheet->setCellValue('L' . $row, '-');
                    $sheet->setCellValue('M' . $row, $lastUser);
                    $row++;
                }
            }
            
            // Create CSV writer
            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            
            $filename = 'Switch_Managed_Data_' . date('Y-m-d_H-i-s') . '.csv';
            
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
            $switches = $this->TransSwitchManagedModel->getData();
            
            // Filter only active/inactive switches
            $switches = array_filter($switches, function($item) {
                return $item->tsm_status == 0 || $item->tsm_status == 1;
            });

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("Switch Management System")
                ->setTitle("Switch Data Export")
                ->setDescription("Export of Switch data in ODS format");

            // Set title
            $sheet->mergeCells('B2:N2');
            $sheet->setCellValue('B2', 'Switch Managed Data with Port Details');
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('B2:N2')->applyFromArray($titleStyle);

            // Define and set headers
            $headers = [
                'B4' => 'ID',
                'C4' => 'Asset No',
                'D4' => 'Asset Name',
                'E4' => 'Asset Age',
                'F4' => 'Max Port',
                'G4' => 'IP Address',
                'H4' => 'Location',
                'I4' => 'Status',
                'J4' => 'Port Number',
                'K4' => 'Port Type',
                'L4' => 'VLAN ID',
                'M4' => 'VLAN Name',
                'N4' => 'Last User'
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
            
            $sheet->getStyle('B4:N4')->applyFromArray($headerStyle);

            // Add data (same logic as XLSX)
            $row = 5;
            foreach ($switches as $switch) {
                $lastUser = $this->TransSwitchManagedModel->getUserDisplayName($switch->tsm_lastuser);
                $switchReceiveDate = $switch->tsm_receivedate ?? '-';
                $switchAge = ($switchReceiveDate !== '-') ? $this->calculateAge($switchReceiveDate) : '-';

                // Get switch port details
                $ports = $this->TransSwitchManagedModel->getSwitchDetailPortsByHeaderId($switch->tsm_id);

                if (!empty($ports)) {
                    $startRow = $row;
                    foreach ($ports as $port) {
                        // Switch basic data (only on first port row)
                        if ($port === $ports[0]) {
                            $sheet->setCellValue('B' . $row, $switch->tsm_id);
                            $sheet->setCellValue('C' . $row, $switch->tsm_assetno ?? '-');
                            $sheet->setCellValue('D' . $row, $switch->tsm_assetname ?? '-');
                            $sheet->setCellValue('E' . $row, $switchAge);
                            $sheet->setCellValue('F' . $row, $switch->tsm_port ?? '-');
                            $sheet->setCellValue('G' . $row, $switch->tsm_ipaddress ?? '-');
                            $sheet->setCellValue('H' . $row, $switch->tsm_location ?? '-');
                            $sheet->setCellValue('I' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                            $sheet->setCellValue('N' . $row, $lastUser);
                        }

                        // Port data
                        $sheet->setCellValue('J' . $row, $port['tsd_port'] ?? '-');
                        $sheet->setCellValue('K' . $row, $port['tsd_type'] ?? '-');
                        $sheet->setCellValue('L' . $row, $port['tsd_vlanid'] ?? '-');
                        $sheet->setCellValue('M' . $row, $port['tsd_vlanname'] ?? '-');

                        $row++;
                    }

                    // Merge cells for switch basic data if there are multiple ports
                    if (count($ports) > 1) {
                        $endRow = $startRow + count($ports) - 1;
                        $mergeColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'N'];
                        foreach ($mergeColumns as $col) {
                            $sheet->mergeCells($col . $startRow . ':' . $col . $endRow);
                            $sheet->getStyle($col . $startRow . ':' . $col . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        }
                    }
                } else {
                    // Switch without ports
                    $sheet->setCellValue('B' . $row, $switch->tsm_id);
                    $sheet->setCellValue('C' . $row, $switch->tsm_assetno ?? '-');
                    $sheet->setCellValue('D' . $row, $switch->tsm_assetname ?? '-');
                    $sheet->setCellValue('E' . $row, $switchAge);
                    $sheet->setCellValue('F' . $row, $switch->tsm_port ?? '-');
                    $sheet->setCellValue('G' . $row, $switch->tsm_ipaddress ?? '-');
                    $sheet->setCellValue('H' . $row, $switch->tsm_location ?? '-');
                    $sheet->setCellValue('I' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                    $sheet->setCellValue('J' . $row, '-');
                    $sheet->setCellValue('K' . $row, '-');
                    $sheet->setCellValue('L' . $row, '-');
                    $sheet->setCellValue('M' . $row, '-');
                    $sheet->setCellValue('N' . $row, $lastUser);
                    $row++;
                }
            }

            // Auto-size columns
            foreach (range('B', 'N') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Style data rows
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ];
            $sheet->getStyle('B5:N' . ($row - 1))->applyFromArray($dataStyle);

            // Create ODS writer
            $writer = new Ods($spreadsheet);

            $filename = 'Switch_Managed_Data_' . date('Y-m-d_H-i-s') . '.ods';

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
                'message' => 'No Switch records selected for export.'
            ]);
        }

        try {
            // Get selected switch data
            $allSwitches = $this->TransSwitchManagedModel->getData();
            $switches = array_filter($allSwitches, function($item) use ($selectedIds) {
                return in_array($item->tsm_id, $selectedIds) && ($item->tsm_status == 0 || $item->tsm_status == 1);
            });

            if (empty($switches)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'No valid Switch records found for export.'
                ]);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("Switch Management System")
                ->setTitle("Selected Switch Data Export")
                ->setDescription("Export of selected Switch data in XLSX format");

            // Set title
            $sheet->mergeCells('B2:N2');
            $selectedCount = count($switches);
            $sheet->setCellValue('B2', "Selected Switch Data ({$selectedCount} records) with Port Details");
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('B2:N2')->applyFromArray($titleStyle);

            // Define and set headers
            $headers = [
                'B4' => 'ID',
                'C4' => 'Asset No',
                'D4' => 'Asset Name',
                'E4' => 'Asset Age',
                'F4' => 'Max Port',
                'G4' => 'IP Address',
                'H4' => 'Location',
                'I4' => 'Status',
                'J4' => 'Port Number',
                'K4' => 'Port Type',
                'L4' => 'VLAN ID',
                'M4' => 'VLAN Name',
                'N4' => 'Last User'
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
            $sheet->getStyle('B4:N4')->applyFromArray($headerStyle);

            // Add data (same logic as exportXLSX but for selected data only)
            $row = 5;
            foreach ($switches as $switch) {
                $lastUser = $this->TransSwitchManagedModel->getUserDisplayName($switch->tsm_lastuser);
                $switchReceiveDate = $switch->tsm_receivedate ?? '-';
                $switchAge = ($switchReceiveDate !== '-') ? $this->calculateAge($switchReceiveDate) : '-';

                // Get switch port details
                $ports = $this->TransSwitchManagedModel->getSwitchDetailPortsByHeaderId($switch->tsm_id);

                if (!empty($ports)) {
                    $startRow = $row;
                    foreach ($ports as $port) {
                        // Switch basic data (only on first port row)
                        if ($port === $ports[0]) {
                            $sheet->setCellValue('B' . $row, $switch->tsm_id);
                            $sheet->setCellValue('C' . $row, $switch->tsm_assetno ?? '-');
                            $sheet->setCellValue('D' . $row, $switch->tsm_assetname ?? '-');
                            $sheet->setCellValue('E' . $row, $switchAge);
                            $sheet->setCellValue('F' . $row, $switch->tsm_port ?? '-');
                            $sheet->setCellValue('G' . $row, $switch->tsm_ipaddress ?? '-');
                            $sheet->setCellValue('H' . $row, $switch->tsm_location ?? '-');
                            $sheet->setCellValue('I' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                            $sheet->setCellValue('N' . $row, $lastUser);
                        }

                        // Port data
                        $sheet->setCellValue('J' . $row, $port['tsd_port'] ?? '-');
                        $sheet->setCellValue('K' . $row, $port['tsd_type'] ?? '-');
                        $sheet->setCellValue('L' . $row, $port['tsd_vlanid'] ?? '-');
                        $sheet->setCellValue('M' . $row, $port['tsd_vlanname'] ?? '-');

                        $row++;
                    }

                    // Merge cells for switch basic data if there are multiple ports
                    if (count($ports) > 1) {
                        $endRow = $startRow + count($ports) - 1;
                        $mergeColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'N'];
                        foreach ($mergeColumns as $col) {
                            $sheet->mergeCells($col . $startRow . ':' . $col . $endRow);
                            $sheet->getStyle($col . $startRow . ':' . $col . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        }
                    }
                } else {
                    // Switch without ports
                    $sheet->setCellValue('B' . $row, $switch->tsm_id);
                    $sheet->setCellValue('C' . $row, $switch->tsm_assetno ?? '-');
                    $sheet->setCellValue('D' . $row, $switch->tsm_assetname ?? '-');
                    $sheet->setCellValue('E' . $row, $switchAge);
                    $sheet->setCellValue('F' . $row, $switch->tsm_port ?? '-');
                    $sheet->setCellValue('G' . $row, $switch->tsm_ipaddress ?? '-');
                    $sheet->setCellValue('H' . $row, $switch->tsm_location ?? '-');
                    $sheet->setCellValue('I' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                    $sheet->setCellValue('J' . $row, '-');
                    $sheet->setCellValue('K' . $row, '-');
                    $sheet->setCellValue('L' . $row, '-');
                    $sheet->setCellValue('M' . $row, '-');
                    $sheet->setCellValue('N' . $row, $lastUser);
                    $row++;
                }
            }

            // Auto-size columns
            foreach (range('B', 'N') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Style data rows
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ];
            $sheet->getStyle('B5:N' . ($row - 1))->applyFromArray($dataStyle);

            // Create writer and output
            $writer = new Xlsx($spreadsheet);

            $filename = 'Switch_Selected_Data_(' . count($switches) . '_records)_' . date('Y-m-d_H-i-s') . '.xlsx';

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
                'message' => 'No Switch records selected for export.'
            ]);
        }

        try {
            // Get selected switch data
            $allSwitches = $this->TransSwitchManagedModel->getData();
            $switches = array_filter($allSwitches, function($item) use ($selectedIds) {
                return in_array($item->tsm_id, $selectedIds) && ($item->tsm_status == 0 || $item->tsm_status == 1);
            });

            if (empty($switches)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'No valid Switch records found for export.'
                ]);
            }
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Define headers
            $headers = [
                'ID', 'Asset No', 'Asset Name', 'Asset Age', 'Max Port',
                'IP Address', 'Location', 'Status',
                'Port Number', 'Port Type', 'VLAN ID', 'VLAN Name', 'Last User'
            ];
            
            // Set headers
            $headerColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
            for ($i = 0; $i < count($headers); $i++) {
                $sheet->setCellValue($headerColumns[$i] . '1', $headers[$i]);
            }
            
            // Add data (similar logic as CSV but for selected data only)
            $row = 2;
            foreach ($switches as $switch) {
                $lastUser = $this->TransSwitchManagedModel->getUserDisplayName($switch->tsm_lastuser);
                $switchReceiveDate = $switch->tsm_receivedate ?? '-';
                $switchAge = ($switchReceiveDate !== '-') ? $this->calculateAge($switchReceiveDate) : '-';
                
                // Get switch port details
                $ports = $this->TransSwitchManagedModel->getSwitchDetailPortsByHeaderId($switch->tsm_id);

                if (!empty($ports)) {
                    foreach ($ports as $index => $port) {
                        // Switch basic data (only on first port row)
                        if ($index === 0) {
                            $sheet->setCellValue('A' . $row, $switch->tsm_id);
                            $sheet->setCellValue('B' . $row, $switch->tsm_assetno ?? '-');
                            $sheet->setCellValue('C' . $row, $switch->tsm_assetname ?? '-');
                            $sheet->setCellValue('D' . $row, $switchAge);
                            $sheet->setCellValue('E' . $row, $switch->tsm_port ?? '-');
                            $sheet->setCellValue('F' . $row, $switch->tsm_ipaddress ?? '-');
                            $sheet->setCellValue('G' . $row, $switch->tsm_location ?? '-');
                            $sheet->setCellValue('H' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                            $sheet->setCellValue('M' . $row, $lastUser);
                        } else {
                            // Empty cells for subsequent rows
                            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'M'] as $col) {
                                $sheet->setCellValue($col . $row, '');
                            }
                        }

                        // Port data
                        $sheet->setCellValue('I' . $row, $port['tsd_port'] ?? '-');
                        $sheet->setCellValue('J' . $row, $port['tsd_type'] ?? '-');
                        $sheet->setCellValue('K' . $row, $port['tsd_vlanid'] ?? '-');
                        $sheet->setCellValue('L' . $row, $port['tsd_vlanname'] ?? '-');

                        $row++;
                    }
                } else {
                    // Switch without ports
                    $sheet->setCellValue('A' . $row, $switch->tsm_id);
                    $sheet->setCellValue('B' . $row, $switch->tsm_assetno ?? '-');
                    $sheet->setCellValue('C' . $row, $switch->tsm_assetname ?? '-');
                    $sheet->setCellValue('D' . $row, $switchAge);
                    $sheet->setCellValue('E' . $row, $switch->tsm_port ?? '-');
                    $sheet->setCellValue('F' . $row, $switch->tsm_ipaddress ?? '-');
                    $sheet->setCellValue('G' . $row, $switch->tsm_location ?? '-');
                    $sheet->setCellValue('H' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                    $sheet->setCellValue('I' . $row, '-');
                    $sheet->setCellValue('J' . $row, '-');
                    $sheet->setCellValue('K' . $row, '-');
                    $sheet->setCellValue('L' . $row, '-');
                    $sheet->setCellValue('M' . $row, $lastUser);
                    $row++;
                }
            }
            
            // Create CSV writer
            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            
            $filename = 'Switch_Selected_Data_(' . count($switches) . '_records)_' . date('Y-m-d_H-i-s') . '.csv';
            
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
                'message' => 'No Switch records selected for export.'
            ]);
        }

        try {
            // Get selected switch data
            $allSwitches = $this->TransSwitchManagedModel->getData();
            $switches = array_filter($allSwitches, function($item) use ($selectedIds) {
                return in_array($item->tsm_id, $selectedIds) && ($item->tsm_status == 0 || $item->tsm_status == 1);
            });

            if (empty($switches)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'No valid Switch records found for export.'
                ]);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("Switch Management System")
                ->setTitle("Selected Switch Data Export")
                ->setDescription("Export of selected Switch data in ODS format");

            // Set title
            $sheet->mergeCells('B2:N2');
            $selectedCount = count($switches);
            $sheet->setCellValue('B2', "Selected Switch Data ({$selectedCount} records) with Port Details");
            $titleStyle = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('B2:N2')->applyFromArray($titleStyle);

            // Define and set headers
            $headers = [
                'B4' => 'ID',
                'C4' => 'Asset No',
                'D4' => 'Asset Name',
                'E4' => 'Asset Age',
                'F4' => 'Max Port',
                'G4' => 'IP Address',
                'H4' => 'Location',
                'I4' => 'Status',
                'J4' => 'Port Number',
                'K4' => 'Port Type',
                'L4' => 'VLAN ID',
                'M4' => 'VLAN Name',
                'N4' => 'Last User'
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
            
            $sheet->getStyle('B4:N4')->applyFromArray($headerStyle);

            // Add data (same logic as ODS)
            $row = 5;
            foreach ($switches as $switch) {
                $lastUser = $this->TransSwitchManagedModel->getUserDisplayName($switch->tsm_lastuser);
                $switchReceiveDate = $switch->tsm_receivedate ?? '-';
                $switchAge = ($switchReceiveDate !== '-') ? $this->calculateAge($switchReceiveDate) : '-';

                // Get switch port details
                $ports = $this->TransSwitchManagedModel->getSwitchDetailPortsByHeaderId($switch->tsm_id);

                if (!empty($ports)) {
                    $startRow = $row;
                    foreach ($ports as $port) {
                        // Switch basic data (only on first port row)
                        if ($port === $ports[0]) {
                            $sheet->setCellValue('B' . $row, $switch->tsm_id);
                            $sheet->setCellValue('C' . $row, $switch->tsm_assetno ?? '-');
                            $sheet->setCellValue('D' . $row, $switch->tsm_assetname ?? '-');
                            $sheet->setCellValue('E' . $row, $switchAge);
                            $sheet->setCellValue('F' . $row, $switch->tsm_port ?? '-');
                            $sheet->setCellValue('G' . $row, $switch->tsm_ipaddress ?? '-');
                            $sheet->setCellValue('H' . $row, $switch->tsm_location ?? '-');
                            $sheet->setCellValue('I' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                            $sheet->setCellValue('N' . $row, $lastUser);
                        }

                        // Port data
                        $sheet->setCellValue('J' . $row, $port['tsd_port'] ?? '-');
                        $sheet->setCellValue('K' . $row, $port['tsd_type'] ?? '-');
                        $sheet->setCellValue('L' . $row, $port['tsd_vlanid'] ?? '-');
                        $sheet->setCellValue('M' . $row, $port['tsd_vlanname'] ?? '-');

                        $row++;
                    }

                    // Merge cells for switch basic data if there are multiple ports
                    if (count($ports) > 1) {
                        $endRow = $startRow + count($ports) - 1;
                        $mergeColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'N'];
                        foreach ($mergeColumns as $col) {
                            $sheet->mergeCells($col . $startRow . ':' . $col . $endRow);
                            $sheet->getStyle($col . $startRow . ':' . $col . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        }
                    }
                } else {
                    // Switch without ports
                    $sheet->setCellValue('B' . $row, $switch->tsm_id);
                    $sheet->setCellValue('C' . $row, $switch->tsm_assetno ?? '-');
                    $sheet->setCellValue('D' . $row, $switch->tsm_assetname ?? '-');
                    $sheet->setCellValue('E' . $row, $switchAge);
                    $sheet->setCellValue('F' . $row, $switch->tsm_port ?? '-');
                    $sheet->setCellValue('G' . $row, $switch->tsm_ipaddress ?? '-');
                    $sheet->setCellValue('H' . $row, $switch->tsm_location ?? '-');
                    $sheet->setCellValue('I' . $row, $switch->tsm_status == 1 ? 'Active' : 'Inactive');
                    $sheet->setCellValue('J' . $row, '-');
                    $sheet->setCellValue('K' . $row, '-');
                    $sheet->setCellValue('L' . $row, '-');
                    $sheet->setCellValue('M' . $row, '-');
                    $sheet->setCellValue('N' . $row, $lastUser);
                    $row++;
                }
            }

            // Auto-size columns
            foreach (range('B', 'N') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Style data rows
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ];
            $sheet->getStyle('B5:N' . ($row - 1))->applyFromArray($dataStyle);

            // Create ODS writer
            $writer = new Ods($spreadsheet);

            $filename = 'Switch_Selected_Data_(' . count($switches) . '_records)_' . date('Y-m-d_H-i-s') . '.ods';

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

    // Helper method for calculating age
    private function calculateAge($receiveDate)
    {
        if (empty($receiveDate)) {
            return '-';
        }

        try {
            $receive = new \DateTime($receiveDate);
            $today = new \DateTime();

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
}