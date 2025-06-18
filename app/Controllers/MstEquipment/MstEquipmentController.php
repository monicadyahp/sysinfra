<?php

namespace App\Controllers\MstEquipment;

use App\Controllers\BaseController;
use App\Models\mstequipment\MstEquipmentModel;

class MstEquipmentController extends BaseController
{
    protected $MstEquipmentModel;

    public function __construct()
    {
        $this->MstEquipmentModel = new MstEquipmentModel();
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $usermenu = session()->get("usermenu");

        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstEquipment") {
                $activeMenuGroup    = $menu->umg_groupname;
                $activeMenuName     = $menu->umn_menuname;
                break;
            }
        }

        $data["active_menu_group"]  = $activeMenuGroup;
        $data["active_menu_name"]   = $activeMenuName;
        $data['cat'] = $this->MstEquipmentModel->getDataCat();
        return view('master/MstEquipment/index', $data);
    }

    public function getDataMfg()
    {
        $data = $this->MstEquipmentModel->getDataEquipment();
        return $this->response->setJSON($data);
    }

    public function store()
    {
        $data = $this->request->getPost();

        if (empty($data)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data kosong!']);
        }

        $assetNo = $data['assetNo'];
        $acceptanceData = $this->MstEquipmentModel->checkAssetInAcceptance($assetNo);

        if ($acceptanceData) {
            $data['model'] = $acceptanceData['ea_model'];
            $data['receiveDate'] = $acceptanceData['ea_datereceived'];
            $data['serial_number'] = $acceptanceData['ea_mfgno'];
            $data['equipmentId'] = $acceptanceData['ea_id'];
        }

        // Simpan nama user dari session ke kolom e_lastuser
        $data['last_user'] = session()->get('username');

        $result = $this->MstEquipmentModel->storeMaster($data);
        return $this->response->setJSON($result);
    }

    public function edit()
    {
        $id = $this->request->getPost('id');
    
        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak ditemukan']);
        }
    
        $data = $this->MstEquipmentModel->getDataByid($id);
    
        if ($data) {
            // Menentukan apakah kolom harus readonly atau tidak
            $isReadonly = $this->MstEquipmentModel->isAssetAccepted($data['e_assetno']);  // Tentukan apakah data diambil dari Asset Number
            
            return $this->response->setJSON([
                'status' => true, 
                'data' => $data, 
                'isReadonly' => $isReadonly  // Kembalikan status apakah readonly
            ]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
    }
    
    public function update()
    {
        if (!session()->get('login')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi telah berakhir. Silakan login kembali.']);
        }

        $data = $this->request->getPost();

        if (empty($data['id'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak ditemukan.']);
        }

        // Simpan nama user dari session ke kolom e_lastuser
        $data['last_user'] = session()->get('username');

        log_message('info', 'Update data: ' . json_encode($data));

        $result = $this->MstEquipmentModel->updateMaster($data);

        if ($result['status']) {
            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil diperbarui.']);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => $result['message']
            ]);
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');

        if (!$id) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'ID tidak valid!'
            ]);
        }

        $deleted = $this->MstEquipmentModel->deleteData($id);

        if ($deleted) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data berhasil dihapus!'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal menghapus data atau data tidak ditemukan!'
            ]);
        }
    }

    public function checkAssetInAcceptance()
    {
        $assetNo = $this->request->getPost('assetNo');
        $data = $this->MstEquipmentModel->checkAssetInAcceptance($assetNo);
    
        if ($data) {
            return $this->response->setJSON([
                'status' => true,
                'data' => [
                    'ea_model' => $data['ea_model'],
                    'ea_datereceived' => $data['ea_datereceived'],
                    'ea_mfgno' => $data['ea_mfgno'],
                    'ea_id' => $data['ea_id']
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Asset not found in TBTFA_EquipmentAcceptance'
            ]);
        }
    }

    public function checkDuplicate()
    {
        $id = $this->request->getPost('id');
        $assetNo = $this->request->getPost('assetNo');
        $serialNumber = $this->request->getPost('serial_number');

        if ($this->MstEquipmentModel->isDuplicateOnUpdate($id, $assetNo, $serialNumber)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Nomor Asset atau Serial Number sudah terdaftar!'
            ]);
        }

        return $this->response->setJSON(['status' => true]);
    }

    public function getAssetNumbers()
    {
        try {
            $data = $this->MstEquipmentModel->getAssetNumbers();
            
            // Return empty array if no data found to prevent DataTables errors
            if (empty($data)) {
                return $this->response->setJSON([]);
            }
            
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error in getAssetNumbers controller: ' . $e->getMessage());
            return $this->response->setJSON([])
                                 ->setStatusCode(500);
        }
    }
}