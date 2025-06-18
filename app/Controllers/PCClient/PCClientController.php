<?php

namespace App\Controllers\PCClient;

use App\Controllers\BaseController;
use App\Models\pcclient\PCClientModel; // Corrected model path for clarity

class PCClientController extends BaseController
{
    protected $PCClientModel;

    public function __construct()
    {
        $this->PCClientModel = new PCClientModel(); // Simpler instantiation with the use statement
        helper('session');
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }
                         
        $usermenu = session()->get("usermenu");
                 
        $activeMenuGroup = 'Master'; // Already correct
        $activeMenuName = 'PC Client'; // Already correct
                         
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstPCClient") { // **Changed: from "TransPCClient" to "MstPCClient"**
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }
                         
        $data = [
            "active_menu_group" => $activeMenuGroup,
            "active_menu_name" => $activeMenuName
        ];
                         
        return view('master/PCClient/index', $data); // **Changed: from 'transaction/TransPCClient/index' to 'master/PCClient/index'**
    }

    public function getData()
    {
        $pcClients = $this->PCClientModel->getData();
                 
        // Format the data for DataTables
        $formattedData = [];
        foreach ($pcClients as $pcClient) {
            // Determine status text based on status code
            $statusText = 'Unknown';
            $statusBadge = 'bg-secondary';
                                     
            switch ($pcClient->tpc_status) {
                case 1:
                    $statusText = 'Active';
                    $statusBadge = 'bg-success';
                    break;
                case 25:
                    $statusText = 'Deleted';
                    $statusBadge = 'bg-danger';
                    break;
            }
                                     
            $formattedData[] = [
                'tpc_id' => $pcClient->tpc_id,
                'tpc_name' => $pcClient->tpc_name,
                'tpc_assetno' => $pcClient->tpc_assetno,
                'tpc_monitorassetno' => $pcClient->tpc_monitorassetno,
                'tpc_ipbefore' => $pcClient->tpc_ipbefore,
                'tpc_ipafter' => $pcClient->tpc_ipafter,
                'tpc_itequipment' => $pcClient->tpc_itequipment,
                'tpc_user' => $pcClient->tpc_user,
                'tpc_area' => $pcClient->tpc_area,
                'tpc_status' => $pcClient->tpc_status,
                'status_text' => $statusText,
                'status_badge' => $statusBadge,
                'tpc_lastupdate' => $pcClient->tpc_lastupdate,
            ];
        }
                         
        return $this->response->setJSON($formattedData);
    }

    public function getPCClientById()
    {
        $id = $this->request->getGet('id');
                         
        if (empty($id)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'PC Client ID is required'
            ]);
        }
                         
        $pcClient = $this->PCClientModel->getPCClientById($id);
         
        if ($pcClient) {
            // Determine status text based on status code
            $statusText = 'Unknown';
            $statusBadge = 'bg-secondary';
                                     
            switch ($pcClient->tpc_status) {
                case 1:
                    $statusText = 'Active';
                    $statusBadge = 'bg-success';
                    break;
                case 25:
                    $statusText = 'Deleted';
                    $statusBadge = 'bg-danger';
                    break;
            }
                                     
            $formattedPCClient = [
                'tpc_id' => $pcClient->tpc_id,
                'tpc_name' => $pcClient->tpc_name,
                'tpc_assetno' => $pcClient->tpc_assetno,
                'tpc_monitorassetno' => $pcClient->tpc_monitorassetno,
                'tpc_ipbefore' => $pcClient->tpc_ipbefore,
                'tpc_ipafter' => $pcClient->tpc_ipafter,
                'tpc_itequipment' => $pcClient->tpc_itequipment,
                'tpc_user' => $pcClient->tpc_user,
                'tpc_area' => $pcClient->tpc_area,
                'tpc_status' => $pcClient->tpc_status,
                'status_text' => $statusText,
                'status_badge' => $statusBadge,
                'tpc_lastupdate' => $pcClient->tpc_lastupdate
            ];
                                     
            return $this->response->setJSON([
                'status' => true,
                'data' => $formattedPCClient
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'PC Client not found'
            ]);
        }
    }

    public function store()
    {
        $data = $this->request->getPost();
        log_message('debug', 'Received data for store: ' . json_encode($data)); // Add this line
    
        // Validate input
        if (empty($data['pc_name']) || empty($data['pc_assetno']) || empty($data['user']) || empty($data['area'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'PC Name, Asset No, User, and Area are required fields.'
            ]);
        }

        $result = $this->PCClientModel->storeData($data);
        return $this->response->setJSON($result);
    }

    public function update()
    {
        $data = $this->request->getPost();

        // Validate input for update:
        // tpc_id is required for update to identify the record.
        // pc_name, pc_assetno, user, and area are explicitly marked as required in the UI.
        if (empty($data['tpc_id']) || empty($data['pc_name']) || empty($data['pc_assetno']) || empty($data['user']) || empty($data['area'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'All required fields must be filled out for update.'
            ]);
        }

        $result = $this->PCClientModel->updateData($data);
        return $this->response->setJSON($result);
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        if (empty($id)) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID not found']);
        }
                         
        $result = $this->PCClientModel->deleteData($id);
        return $this->response->setJSON($result);
    }

    public function getAssetNo()
    {
        $assetNo = $this->request->getGet('assetNo');
                         
        if (empty($assetNo)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Asset No is required']);
        }
                         
        $assetNo = $this->PCClientModel->getAssetNo($assetNo);
                         
        if ($assetNo) {
            return $this->response->setJSON(['status' => true, 'data' => $assetNo]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Asset No not found or already in use']);
        }
    }

    public function searchAssetNo()
    {
        $search = $this->request->getGet('search') ?? '';
                         
        $assetNo = $this->PCClientModel->searchAssetNo($search);
                         
        // Format data for DataTables
        $formattedAssetNo = [];
        foreach ($assetNo as $asset) {
            $formattedAssetNo[] = [
                'asset_no' => $asset->ea_assetnumber ?? '',
                'ea_id' => $asset->ea_id ?? '',
                'ea_machineno' => $asset->ea_machineno ?? '',
                'ea_model' => $asset->ea_model ?? '',
            ];
        }
                         
        return $this->response->setJSON($formattedAssetNo);
    }

    public function getIPAddresses()
    {
        $ipAddress = $this->request->getGet('ipAddress');
                         
        if (empty($ipAddress)) {
            return $this->response->setJSON(['status' => false, 'message' => 'IP Address is required']);
        }
                         
        $ip = $this->PCClientModel->getIPAddressByIP($ipAddress);
                         
        if ($ip) {
            return $this->response->setJSON(['status' => true, 'data' => $ip]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'IP Address not found or already in use']);
        }
    }

    public function SearchIPAddresses()
    {
        $search = $this->request->getGet('search') ?? '';
        $excludeIPs = $this->request->getGet('excludeIPs') ?? [];
        
        // Convert string to array if needed
        if (is_string($excludeIPs) && !empty($excludeIPs)) {
            $excludeIPs = explode(',', $excludeIPs);
        }
        
        $ipAddresses = $this->PCClientModel->searchIPAddresses($search, $excludeIPs);
        
        // Format data for DataTables
        $formattedIPs = [];
        foreach ($ipAddresses as $ip) {
            $formattedIPs[] = [
                'mip_id' => $ip->mip_id ?? '',
                'mip_vlanid' => $ip->mip_vlanid ?? '',
                'mip_vlanname' => $ip->mip_vlanname ?? '',
                'mip_ipadd' => $ip->mip_ipadd ?? '',
            ];
        }
        
        return $this->response->setJSON($formattedIPs);
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
            $result = $this->PCClientModel->updateIPStatus($ipAddress, $status);
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
                         
        $employee = $this->PCClientModel->getEmployeeById($employeeId);
                         
        if ($employee) {
            return $this->response->setJSON(['status' => true, 'data' => $employee]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Employee not found']);
        }
    }

    public function searchEmployees()
    {
        $search = $this->request->getGet('search') ?? '';
        $exclude = $this->request->getGet('exclude') ?? '';
                         
        $employees = $this->PCClientModel->searchEmployees($search, $exclude);
        return $this->response->setJSON($employees);
    }

    public function getAreas()
    {
        $sections = $this->PCClientModel->getAreas();
        return $this->response->setJSON($sections);
    }
    
}