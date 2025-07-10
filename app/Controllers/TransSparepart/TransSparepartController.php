<?php

namespace App\Controllers\TransSparepart;

use App\Controllers\BaseController;
use App\Models\transsparepart\TransSparepartModel;
use App\Models\mstequipment\MstEquipmentModel;

class TransSparepartController extends BaseController {
    protected $TransSparepartModel;
    protected $MstEquipmentModel;
    
    public function __construct()
    {
        $this->TransSparepartModel = new TransSparepartModel();
        $this->MstEquipmentModel = new MstEquipmentModel();
    }
    
    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }
        
        $usermenu = session()->get("usermenu");
        
        $activeMenuGroup = 'Transaction';
        $activeMenuName = 'Equipment Movement';
        
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "TransSparepart") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }
        
        $data["active_menu_group"] = $activeMenuGroup;
        $data["active_menu_name"] = $activeMenuName;
        $data['cat'] = $this->MstEquipmentModel->getDataCat();
        $data['sections'] = $this->TransSparepartModel->getSections();
        
        return view('transaction/TransSparepart/index', $data);
    }
    
    public function getData()
    {
        $data = $this->TransSparepartModel->getData();
        return $this->response->setJSON($data);
    }
    
    public function store()
    {
        $data = $this->request->getPost();
        
        // Ensure the user is logged in
        if (!session()->get('login')) {
            return $this->response->setJSON([
                'status' => false, 
                'message' => 'Your session has expired. Please login again.'
            ]);
        }
        
        // Add current user to the data
        $data['last_user'] = session()->get('username');
        
        // If transaction date is empty, set it to today
        if (empty($data['tsdate'])) {
            $data['tsdate'] = date('Y-m-d');
        }
        
        $result = $this->TransSparepartModel->storeData($data);
        return $this->response->setJSON($result);
    }
    
    public function getSparepartById()
    {
        $id = $this->request->getPost('id');
        
        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID not found']);
        }
        
        $data = $this->TransSparepartModel->getDataByid($id);
        
        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Data not found']);
        }
    }
    
    public function update()
    {
        $data = $this->request->getPost();
        
        if (!session()->get('login')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Your session has expired. Please login again.']);
        }
        
        // Add current user to the data
        $data['last_user'] = session()->get('username');
        
        // If transaction date is empty, set it to today
        if (empty($data['tsdate'])) {
            $data['tsdate'] = date('Y-m-d');
        }
        
        if (isset($data['id'])) {
            $result = $this->TransSparepartModel->updateData($data);
            
            // Return the result directly since updateData now returns array with status and message
            return $this->response->setJSON($result);
        }
        
        return $this->response->setJSON(['status' => false, 'message' => 'Invalid data']);
    }
    
    public function delete()
    {
        $id = $this->request->getPost('id');
        
        if (empty($id)) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID not found']);
        }
        
        $deleted = $this->TransSparepartModel->deleteData($id);
        
        if ($deleted) {
            return $this->response->setJSON(['status' => true, 'message' => 'Data deleted successfully']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Failed to delete data']);
        }
    }

    public function getAssetNo()
    {
        $assetNo = $this->request->getGet('assetNo');
        
        if (empty($assetNo)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Asset No is required']);
        }
        
        $asset = $this->TransSparepartModel->getAssetNo($assetNo);
        
        if ($asset) {
            return $this->response->setJSON(['status' => true, 'data' => $asset]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Asset No not found']);
        }
    }

    public function searchAssetNo()
    {
        $search = $this->request->getGet('search') ?? null; // Ambil parameter 'search' dari AJAX DataTables
        $data = $this->TransSparepartModel->searchAssetNo($search); // Teruskan parameter search ke Model
        return $this->response->setJSON($data);
    }

    public function getEmployees()
    {
        $employeeId = $this->request->getGet('employeeId');
        
        if (empty($employeeId)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Employee ID is required']);
        }
        
        $employee = $this->TransSparepartModel->getEmployeeById($employeeId);
        
        if ($employee) {
            return $this->response->setJSON(['status' => true, 'data' => $employee]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Employee not found']);
        }
    }
    
    public function searchEmployees()
    {
        
        $employees = $this->TransSparepartModel->searchEmployees();
        
        return $this->response->setJSON($employees);
    }
}