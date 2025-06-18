<?php

namespace App\Controllers\TransTicketing;

use App\Controllers\BaseController;
use App\Models\transticketing\TransTicketingModel;

class TransTicketingController extends BaseController
{
    protected $TransTicketingModel;

    public function __construct()
    {
        $this->TransTicketingModel = new TransTicketingModel();
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }
                
        $usermenu = session()->get("usermenu");
            
        $activeMenuGroup = 'Transaction';
        $activeMenuName = 'Ticketing';
                
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "TransTicketing") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }

        // Get current user data from model
        $currentUserData = $this->TransTicketingModel->getCurrentUserData();
                
        $data = [
            "active_menu_group" => $activeMenuGroup,
            "active_menu_name" => $activeMenuName,
            "currentUserData" => $currentUserData,
            "isSystemSection" => $currentUserData['isSystemSection']
        ];
                
        return view('transaction/TransTicketing/index', $data);
    }

    public function getData()
    {
        $tickets = $this->TransTicketingModel->getData();
        $currentUserData = $this->TransTicketingModel->getCurrentUserData();

        // Format the data for DataTables
        $formattedData = [];
        foreach ($tickets as $ticket) {
            $sectionName = $this->TransTicketingModel->getSectionName($ticket->tt_sectioncode_rep);
            $picName = $this->TransTicketingModel->getPicName($ticket->tt_pic_system);

            // Use null-safe access or default values to avoid errors
            $sectionNameValue = $sectionName ? $sectionName->sec_section : '-';
            $picNameValue = $picName ? $picName->em_emplname : '-';

            // Determine status text based on status code (only 1 and 25)
            $statusText = 'Unknown';
            $statusBadge = 'bg-secondary';

            switch ($ticket->tt_status) {
                case 1:
                    $statusText = 'Active';
                    $statusBadge = 'bg-success';
                    break;
                case 25:
                    $statusText = 'Deleted';
                    $statusBadge = 'bg-danger';
                    break;
            }

            // Check if current user can edit this ticket
            $canEdit = $this->TransTicketingModel->canUserEditTicket($ticket->tt_id);

            $formattedData[] = [
                'tt_id' => $ticket->tt_id,
                'tt_empno_rep' => $ticket->tt_empno_rep,
                'reporter_name' => $ticket->tt_empname_rep,
                'section_name' => $sectionNameValue,
                'tt_category' => $ticket->tt_category,
                'tt_case' => $ticket->tt_case,
                'tt_action' => $ticket->tt_action,
                'tt_pic_system' => $picNameValue,
                'tt_check_date' => $ticket->tt_check_date,
                'tt_finish_date' => $ticket->tt_finish_date,
                'tt_assetno' => $ticket->tt_assetno,
                'tt_categoryequip' => $ticket->tt_categoryequip,
                'tt_status' => $ticket->tt_status,
                'status_text' => $statusText,
                'status_badge' => $statusBadge,
                'can_edit' => $canEdit
            ];
        }

        return $this->response->setJSON($formattedData);
    }
    
    public function getTicketById()
    {
        $id = $this->request->getGet('id');
                
        if (empty($id)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Ticket ID is required'
            ]);
        }
                
        $ticket = $this->TransTicketingModel->getTicketById($id);
        
        if ($ticket) {
            // Determine status text based on status code (only 1 and 25)
            $statusText = 'Unknown';
            $statusBadge = 'bg-secondary';
                        
            switch ($ticket->tt_status) {
                case 1:
                    $statusText = 'Active';
                    $statusBadge = 'bg-success';
                    break;
                case 25:
                    $statusText = 'Deleted';
                    $statusBadge = 'bg-danger';
                    break;
            }
                        
            $formattedTicket = [
                'tt_id' => $ticket->tt_id,
                'tt_empno_rep' => $ticket->tt_empno_rep,
                'reporter_name' => $ticket->tt_empname_rep,
                'tt_sectioncode_rep' => $ticket->tt_sectioncode_rep,
                'section_name' => $ticket->tt_sectioncode_rep,
                'tt_category' => $ticket->tt_category,
                'tt_case' => $ticket->tt_case,
                'tt_action' => $ticket->tt_action,
                'tt_pic_system' => $ticket->tt_pic_system,
                'pic_name' => $ticket->tt_pic_system,
                'tt_check_date' => $ticket->tt_check_date,
                'tt_finish_date' => $ticket->tt_finish_date,
                'tt_assetno' => $ticket->tt_assetno,
                'tt_categoryequip' => $ticket->tt_categoryequip,
                'tt_status' => $ticket->tt_status,
                'status_text' => $statusText,
                'status_badge' => $statusBadge
            ];
                        
            return $this->response->setJSON([
                'status' => true,
                'data' => $formattedTicket
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Ticket not found'
            ]);
        }
    }

    public function store()
    {
        $data = $this->request->getPost();
                
        // Validate input
        if (empty($data['employee_no']) || empty($data['pic_system']) || empty($data['case_content']) || empty($data['team'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'All required fields must be filled out.'
            ]);
        }
                
        // Convert numeric team value to string name
        if (isset($data['team'])) {
            if ($data['team'] == '1') {
                $data['team'] = 1;
            } else if ($data['team'] == '2') {
                $data['team'] = 2;
            }
        }
                
        $result = $this->TransTicketingModel->storeData($data);
        return $this->response->setJSON($result);
    }

    public function update()
    {
        $data = $this->request->getPost();
                
        // Validate input
        if (empty($data['ticket_id']) || empty($data['employee_no']) || empty($data['pic_system']) || empty($data['case_content']) || empty($data['team'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'All required fields must be filled out.'
            ]);
        }
                
        // Convert numeric team value to string name
        if (isset($data['team'])) {
            if ($data['team'] == '1') {
                $data['team'] = 1;
            } else if ($data['team'] == '2') {
                $data['team'] = 2;
            }
        }
                
        $result = $this->TransTicketingModel->updateData($data);
        return $this->response->setJSON($result);
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        if (empty($id)) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID not found']);
        }
                
        $result = $this->TransTicketingModel->deleteData($id);
        return $this->response->setJSON($result);
    }

    public function getEmployees()
    {
        $employeeId = $this->request->getGet('employeeId');
                
        if (empty($employeeId)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Employee ID is required']);
        }
                
        $employee = $this->TransTicketingModel->getEmployeeById($employeeId);
                
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
                
        $employees = $this->TransTicketingModel->searchEmployees($search, $exclude);
        return $this->response->setJSON($employees);
    }

    public function getSystemEmployees()
    {
                
        $employees = $this->TransTicketingModel->getSystemEmployees();
                
        return $this->response->setJSON($employees);
    }

    public function getAssetNo()
    {
        $assetNo = $this->request->getGet('assetNo');
                         
        if (empty($assetNo)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Asset No is required']);
        }
                         
        $asset = $this->TransTicketingModel->getAssetNo($assetNo);
                         
        if ($asset) {
            return $this->response->setJSON(['status' => true, 'data' => $asset]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Asset not found']);
        }
    }

    public function searchAssetNo()
    {
        $search = $this->request->getGet('search') ?? '';
                
        $assetNo = $this->TransTicketingModel->searchAssetNo($search);
                
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

    public function getCategories()
    {
        $category = $this->TransTicketingModel->getCategories();
                
        if ($category) {
            return $this->response->setJSON($category);
        } else {
            return $this->response->setJSON([]);
        }
    }
    
}