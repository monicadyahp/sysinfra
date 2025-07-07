<?php

namespace App\Controllers\MstPCOS;

use App\Controllers\BaseController;
use App\Models\mstpcos\MstPCOSModel;

class MstPCOSController extends BaseController
{
    protected $MstPCOSModel;

    public function __construct()
    {
        $this->MstPCOSModel = new MstPCOSModel();
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }
                         
        $usermenu = session()->get("usermenu");
                 
        $activeMenuGroup = 'Master';
        $activeMenuName = 'PC OS';
                         
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstPCOS") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }
                         
        $data = [
            "active_menu_group" => $activeMenuGroup,
            "active_menu_name" => $activeMenuName
        ];
                         
        return view('master/MstPCOS/index', $data);
    }

    public function getData()
    {
        $data = $this->MstPCOSModel->getData();
        return $this->response->setJSON($data);
    }

    public function getOSbyId()
    {
        $osName = $this->request->getPost('osName');
        
        if (!$osName) {
            return $this->response->setJSON(['status' => false, 'message' => 'OS name not found']);
        }

        $data = $this->MstPCOSModel->getOSById($osName);

        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Data not found']);
        }
    }

    public function store()
    {
        $data = $this->request->getPost();
        
        $result = $this->MstPCOSModel->storeData($data);
        
        return $this->response->setJSON($result);
    }

    public function update()
    {
        $data = $this->request->getPost();

        if (isset($data['oldOSName']) && isset($data['osName'])) {
            $result = $this->MstPCOSModel->updateData($data);
            return $this->response->setJSON($result);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Invalid data: Missing required fields']);
    }

    public function delete()
    {
        $osName = $this->request->getPost('osName');
        
        if (empty($osName)) {
            return $this->response->setJSON(['status' => false, 'message' => 'OS name not found']);
        }

        $result = $this->MstPCOSModel->deleteData($osName);
        return $this->response->setJSON($result);
    }
}
