<?php
// app/Controllers/MstIPAdd/MstIPAddController.php

namespace App\Controllers\MstIPAdd;

use App\Controllers\BaseController;
use App\Models\mstipadd\MstIPAddModel;
use CodeIgniter\I18n\Time; // *** PERBAIKAN: Menambahkan use statement untuk Time ***

class MstIPAddController extends BaseController
{
    protected $MstIPAddModel;

    public function __construct()
    {
        $this->MstIPAddModel = new MstIPAddModel();
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $usermenu = session()->get("usermenu");
        $activeMenuGroup = 'Master';
        $activeMenuName = 'IP Address';

        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstIPAdd") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }

        $data = [
            "active_menu_group" => $activeMenuGroup,
            "active_menu_name" => $activeMenuName
        ];

        return view('master/MstIPAdd/index', $data);
    }

    public function getData()
    {
        $statusFilter = $this->request->getGet('status');
        
        try {
            $data = $this->MstIPAddModel->getData($statusFilter);
            $formattedData = [];

            foreach ($data as $row) {
                $lastUser = (int)$row->mip_lastuser;
                $status = (int)$row->mip_status;

                // Logika penentuan teks status:
                // 'Used' jika mip_lastuser BUKAN 0 (dan bukan NULL) AND mip_status = 0
                // 'Unused' jika mip_lastuser = 0 (atau NULL) OR mip_status = 1
                $statusText = ($lastUser !== 0 && $status === 0) ? 'Used' : 'Unused';

                $formattedData[] = [
                    'mip_id'          => $row->mip_id,
                    'mip_ipadd'       => $row->mip_ipadd,
                    'mip_vlanid'      => $row->mip_vlanid,
                    'mip_vlanname'    => $row->mip_vlanname,
                    'mip_status_text' => $statusText,
                    'mip_lastupdate'  => $this->formatDateTime($row->mip_lastupdate),
                    'mip_lastuser'    => $row->mip_lastuser,
                    'mip_status_raw'  => $status,
                    'mip_lastuser_raw' => $lastUser
                ];
            }

            return $this->response->setJSON($formattedData);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching IP Address data: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON([
                                     'status' => false, 
                                     'message' => 'Failed to load data: ' . $e->getMessage()
                                 ]);
        }
    }

    public function toggleStatus()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request.']);
        }

        $id = $this->request->getPost('id');
        $currentMipStatus = (int)$this->request->getPost('currentMipStatus');
        $currentMipLastUser = (int)$this->request->getPost('currentMipLastUser');

        if (empty($id)) {
            return $this->response->setStatusCode(400)
                                 ->setJSON([
                                     'success' => false, 
                                     'message' => 'ID is required'
                                 ]);
        }

        try {
            $result = $this->MstIPAddModel->toggleStatus($id, $currentMipStatus, $currentMipLastUser);
            
            return $this->response->setJSON([
                'success' => $result['status'],
                'message' => $result['message']
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error toggling status: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON([
                                     'success' => false, 
                                     'message' => 'Failed to update status: ' . $e->getMessage()
                                 ]);
        }
    }

    private function formatDateTime($dateString)
    {
        if (empty($dateString)) {
            return '-'; // Return hyphen for empty date strings
        }
        
        try {
            $time = Time::parse($dateString);
            return $time->toLocalizedString('id-ID', [
                'year' => 'numeric', 'month' => '2-digit', 'day' => '2-digit',
                'hour' => '2-digit', 'minute' => '2-digit', 'second' => '2-digit'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error formatting date: ' . $e->getMessage() . ' (Raw: ' . $dateString . ')');
            return 'Parsing Error'; // Or return original string for debugging
        }
    }
}