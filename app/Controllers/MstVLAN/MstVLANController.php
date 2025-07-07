<?php

namespace App\Controllers\MstVLAN;

use App\Controllers\BaseController;
use App\Models\mstvlan\MstVLANModel;

class MstVLANController extends BaseController
{
    protected $MstVLANModel;

    public function __construct()
    {
        $this->MstVLANModel = new MstVLANModel();
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $usermenu = session()->get("usermenu");
        $activeMenuGroup = 'Master';
        $activeMenuName = 'VLAN';

        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstVLAN") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }

        $data = [
            "active_menu_group" => $activeMenuGroup,
            "active_menu_name" => $activeMenuName
        ];

        return view('master/MstVLAN/index', $data);
    }

    public function getData()
    {
        $vlans = $this->MstVLANModel->getData();
        
        // Format the data for DataTables
        $formattedData = [];
        foreach ($vlans as $vlan) {
            $formattedData[] = [
                'id' => $vlan->mv_id,
                'vlan_id' => $vlan->mv_vlanid,
                'name' => $vlan->mv_name
            ];
        }

        return $this->response->setJSON($formattedData);
    }

    public function getVLANById()
    {
        $id = $this->request->getPost('id');
        $vlan = $this->MstVLANModel->getVLANById($id);

        if (!$vlan) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'VLAN not found'
            ]);
        }

        $data = [
            'id' => $vlan->mv_id,
            'vlan_id' => $vlan->mv_vlanid,
            'name' => $vlan->mv_name
        ];

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function store()
    {
        $data = $this->request->getPost();
        
        // Validate input
        if (empty($data['vlan_id']) && empty($data['name'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Either VLAN ID or VLAN Name must be filled.'
            ]);
        }

        $result = $this->MstVLANModel->storeData($data);
        return $this->response->setJSON($result);
    }

    public function update()
    {
        $data = $this->request->getPost();
        
        // Validate input
        if (empty($data['id'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'VLAN ID is required.'
            ]);
        }

        if (empty($data['vlan_id']) && empty($data['name'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Either VLAN ID or VLAN Name must be filled.'
            ]);
        }

        $result = $this->MstVLANModel->updateData($data);
        return $this->response->setJSON($result);
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        if (empty($id)) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID not found']);
        }
        
        $result = $this->MstVLANModel->deleteData($id);
        return $this->response->setJSON($result);
    }
}