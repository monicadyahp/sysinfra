<?php

namespace App\Controllers\TransDisposal;

use App\Controllers\BaseController;
use App\Models\transdisposal\TransDisposalModel;
use App\Models\mstequipment\MstEquipmentModel;
use App\Models\mstreason\MstReasonModel;

class TransDisposalController extends BaseController
{
    protected $TransDisposalModel;
    protected $MstEquipmentModel;

    public function __construct()
    {
        $this->TransDisposalModel = new TransDisposalModel();
        $this->MstEquipmentModel  = new MstEquipmentModel();
        $this->MstReasonModel     = new MstReasonModel(); // Inisialisasi model master reason
    }

    // 08-04 monic
    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $usermenu = session()->get("usermenu");
        $activeMenuGroup = 'Transaction';
        $activeMenuName = 'Disposal Transaction';

        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "TransDisposal") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }
        
        $data['reasonList'] = $this->MstReasonModel->getDataReason();
        $data["active_menu_group"] = $activeMenuGroup;
        $data["active_menu_name"] = $activeMenuName;
        $data['cat'] = $this->MstEquipmentModel->getDataCat();

        return view('transaction/TransDisposal/index', $data);
    }

    // 14-04 monic
    public function getDataDisposal()
    {
        try {
            $data = $this->TransDisposalModel->getDataDisposal();
            // Jangan mengembalikan {"status": false, ...} karena DataTables mengharapkan array JSON.
            // Jika tidak ada data, kembalikan array kosong.
            if (empty($data)) {
                return $this->response->setJSON([]);
            }
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataDisposal: ' . $e->getMessage());
            return $this->response->setJSON([]);
        }
    }
                
    public function store()
    {
        $data = $this->request->getPost();
        $result = $this->TransDisposalModel->storeDisposal($data);
        return $this->response->setJSON($result);
    }

    public function edit()
    {
        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'id tidak ditemukan']);
        }
        $data = $this->TransDisposalModel->getDataByid($id);
        if ($data) {
            // Tambahkan flag readonly, jadi TRUE jika asset tersebut ditemukan di asset acceptance
            $data['readonly'] = $this->TransDisposalModel->isAssetFromModal($data['td_assetno']);
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
            $result = $this->TransDisposalModel->updateDisposal($data);
            if ($result !== false) {
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
        $deleted = $this->TransDisposalModel->deleteData($id);
        if ($deleted) {
            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus data']);
        }
    }


    public function getAssetNumbers()
    {
        try {
            $data = $this->TransDisposalModel->getAssetNumbers();
            if (!empty($data)) {
                return $this->response->setJSON($data);
            } else {
                return $this->response->setJSON(['status' => false, 'message' => 'No asset numbers found']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error fetching asset numbers: ' . $e->getMessage());
            return $this->response->setJSON(['status' => false, 'message' => 'An error occurred while fetching asset numbers.']);
        }
    }

    public function checkUnique()
    {
        $data = $this->request->getPost();
        $assetNo = isset($data['assetNo']) ? $data['assetNo'] : null;
        $serialNumber = isset($data['serial_number']) ? $data['serial_number'] : null;
        $duplicate = $this->TransDisposalModel->checkDuplicate($assetNo, $serialNumber);
        if ($duplicate) {
            return $this->response->setJSON(['status' => false, 'message' => 'Number Asset / Serial Number sudah ada']);
        } else {
            return $this->response->setJSON(['status' => true]);
        }
    }

    public function checkUniqueEdit()
    {
        $data = $this->request->getPost();
        $id = isset($data['id']) ? $data['id'] : null;
        $assetNo = isset($data['assetNo']) ? $data['assetNo'] : null;
        $serialNumber = isset($data['serial_number']) ? $data['serial_number'] : null;
        $duplicate = $this->TransDisposalModel->checkDuplicateUpdate($assetNo, $serialNumber, $id);
        if ($duplicate) {
            return $this->response->setJSON(['status' => false, 'message' => 'Number Asset / Serial Number sudah ada']);
        } else {
            return $this->response->setJSON(['status' => true]);
        }
    }

}