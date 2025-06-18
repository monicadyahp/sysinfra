<?php

namespace App\Controllers\MstReason;

use App\Controllers\BaseController;
use App\Models\mstreason\MstReasonModel;

class MstReasonController extends BaseController
{
    protected $MstReasonModel;

    public function __construct()
    {
        $this->MstReasonModel = new MstReasonModel(); // Initialize the model
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        // Retrieve user menu
        $usermenu = session()->get("usermenu");
        $activeMenuGroup = 'Transaction';
        $activeMenuName = 'Master Reason';

        // Cari menu yang sesuai (sesuaikan kondisi jika perlu)
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstReason") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }
        $data["active_menu_group"] = $activeMenuGroup;
        $data["active_menu_name"]  = $activeMenuName;
        
        return view('master/MstReason/index', $data);
    }

    public function getDataReason()
    {
        $data = $this->MstReasonModel->getDataReason();
        return $this->response->setJSON($data);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $result = $this->MstReasonModel->storeReason($data);
        return $this->response->setJSON($result);
    }

    public function edit()
    {
        $reasonName = $this->request->getPost('reasonName');
        
        if (!$reasonName) {
            return $this->response->setJSON(['status' => false, 'message' => 'Reason not found']);
        }

        $data = $this->MstReasonModel->getDataById($reasonName);

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
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Your session has expired. Please login again.'
            ]);
        }

        if (isset($data['oldReason']) && isset($data['reasonName'])) {
            $result = $this->MstReasonModel->updateReason($data);
            return $this->response->setJSON($result);
        }

        return $this->response->setJSON([
            'status'  => false,
            'message' => 'Invalid data: Missing required fields'
        ]);
    }

    public function delete()
    {
        $reasonName = $this->request->getPost('reasonName');
        
        if (empty($reasonName)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Reason not found']);
        }

        $result = $this->MstReasonModel->deleteReason($reasonName);
        return $this->response->setJSON($result);
    }
}