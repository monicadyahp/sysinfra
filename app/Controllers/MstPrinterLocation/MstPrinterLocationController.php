<?php

namespace App\Controllers\MstPrinterLocation;

use App\Controllers\BaseController;
use App\Models\mstprinterlocation\MstPrinterLocationModel; // Sesuaikan namespace model

class MstPrinterLocationController extends BaseController
{
    protected $MstPrinterLocationModel;

    public function __construct()
    {
        $this->MstPrinterLocationModel = new MstPrinterLocationModel();
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }
                                
        $usermenu = session()->get("usermenu");
                            
        $activeMenuGroup = 'Master';
        $activeMenuName = 'Printer Location'; // Ubah nama menu
                                
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstPrinterLocation") { // Ubah path
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }
                                
        $data = [
            "active_menu_group" => $activeMenuGroup,
            "active_menu_name" => $activeMenuName
        ];
                                
        return view('master/MstPrinterLocation/index', $data); // Ubah path view
    }

    public function getData()
    {
        $data = $this->MstPrinterLocationModel->getData();
        return $this->response->setJSON($data);
    }

    public function getPrinterLocationById() // Ubah nama fungsi
    {
        $printerLocationName = $this->request->getPost('printerLocationName'); // Ubah nama parameter
        
        if (!$printerLocationName) {
            return $this->response->setJSON(['status' => false, 'message' => 'Printer Location name not found']);
        }

        $data = $this->MstPrinterLocationModel->getPrinterLocationById($printerLocationName); // Ubah nama fungsi model

        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Data not found']);
        }
    }

    public function store()
    {
        $data = $this->request->getPost();
        
        $result = $this->MstPrinterLocationModel->storeData($data);
        
        return $this->response->setJSON($result);
    }

    public function update()
    {
        $data = $this->request->getPost();

        if (isset($data['oldPrinterLocationName']) && isset($data['printerLocationName'])) { // Ubah nama parameter
            $result = $this->MstPrinterLocationModel->updateData($data);
            return $this->response->setJSON($result);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Invalid data: Missing required fields']);
    }

    public function delete()
    {
        $printerLocationName = $this->request->getPost('printerLocationName'); // Ubah nama parameter
        
        if (empty($printerLocationName)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Printer Location name not found']);
        }

        $result = $this->MstPrinterLocationModel->deleteData($printerLocationName);
        return $this->response->setJSON($result);
    }
}
