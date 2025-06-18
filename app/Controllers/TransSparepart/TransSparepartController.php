<?php

namespace App\Controllers\TransSparepart;

use App\Controllers\BaseController;
use App\Models\transsparepart\TransSparepartModel;
use App\Models\mstequipment\MstEquipmentModel;

class TransSparepartController extends BaseController
{
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
        $data['sections'] = $this->TransSparepartModel->getActiveSections(); // Get active sections
        
        return view('transaction/TransSparepart/index', $data);
    }

    public function getData()
    {
        $data = $this->TransSparepartModel->getData();
        return $this->response->setJSON($data);
    }

    public function getAvailableAssets() 
    {
        $data = $this->TransSparepartModel->getAvailableEquipment();
        return $this->response->setJSON($data);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $result = $this->TransSparepartModel->storeData($data);
        return $this->response->setJSON($result);
    }

    public function edit()
    {
        $id = $this->request->getPost('id');
        
        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'id tidak ditemukan']);
        }
        $data = $this->TransSparepartModel->getDataByid($id);

        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
    }

    public function update()
    {
        $data = $this->request->getPost();
        if (!session()->get('login')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Your session has expired. Please login again.']);
        }

        if (isset($data['id'])) {
            $result = $this->TransSparepartModel->updateData($data);

            if ($result) {
                return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil diperbarui']);
            } else {
                return $this->response->setJSON(['status' => false, 'message' => 'Data gagal diperbarui']);
            }
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Invalid data']);
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        if (empty($id)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Id tidak ditemukan']);
        }
        
        $deleted = $this->TransSparepartModel->deleteData($id);
        if ($deleted) {
            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus data']);
        }
    }
    
    // Add method to search for employees
    public function searchEmployees()
    {
        $search = $this->request->getGet('search') ?? '';
        $exclude = $this->request->getGet('exclude') ?? '';
        
        $employees = $this->TransSparepartModel->searchEmployees($search);
        
        // Filter out excluded employee if needed
        if (!empty($exclude)) {
            $filteredEmployees = array_filter($employees, function($employee) use ($exclude) {
                return $employee->em_emplname !== $exclude;
            });
            return $this->response->setJSON(array_values($filteredEmployees)); // Reset array keys
        }
        
        return $this->response->setJSON($employees);
    }
}