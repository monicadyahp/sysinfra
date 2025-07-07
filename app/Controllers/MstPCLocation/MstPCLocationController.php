<?php

namespace App\Controllers\MstPCLocation;

use App\Controllers\BaseController;
use App\Models\mstpclocation\MstPCLocationModel;

class MstPCLocationController extends BaseController
{
    protected $MstPCLocationModel;

    public function __construct()
    {
        $this->MstPCLocationModel = new MstPCLocationModel();
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }
                         
        $usermenu = session()->get("usermenu");
                 
        $activeMenuGroup = 'Master';
        $activeMenuName = 'PC Client Location';
                         
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstPCLocation") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }
                         
        $data = [
            "active_menu_group" => $activeMenuGroup,
            "active_menu_name" => $activeMenuName
        ];
                         
        return view('master/MstPCLocation/index', $data);
    }

    public function getData()
    {
        $data = $this->MstPCLocationModel->getData();
        return $this->response->setJSON($data);
    }

    public function getLocationById()
    {
        $locationName = $this->request->getPost('locationName');
        
        if (!$locationName) {
            return $this->response->setJSON(['status' => false, 'message' => 'Location name not found']);
        }

        $data = $this->MstPCLocationModel->getLocationById($locationName);

        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Data not found']);
        }
    }

    public function store()
    {
        $data = $this->request->getPost();
        
        $result = $this->MstPCLocationModel->storeData($data);
        
        return $this->response->setJSON($result);
    }

    public function update()
    {
        $data = $this->request->getPost();

        if (isset($data['oldLocationName']) && isset($data['locationName'])) {
            $result = $this->MstPCLocationModel->updateData($data);
            return $this->response->setJSON($result);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Invalid data: Missing required fields']);
    }

    public function delete()
    {
        $locationName = $this->request->getPost('locationName');
        
        if (empty($locationName)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Location name not found']);
        }

        $result = $this->MstPCLocationModel->deleteData($locationName);
        return $this->response->setJSON($result);
    }
}
