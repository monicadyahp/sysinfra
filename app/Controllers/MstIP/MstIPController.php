<?php

namespace App\Controllers\MstIP;

use App\Controllers\BaseController;
use App\Models\MstIP\MstIPModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use Config\Database;

class MstIPController extends BaseController
{
    protected $MstIPModel;
    protected $db;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->MstIPModel = new MstIPModel();
        $this->db = Database::connect('jinsystem');
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        $usermenu = session()->get("usermenu");
        $activeMenuGroup = 'Master';
        $activeMenuName = 'Master IP';

        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstIP") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }

        $data["active_menu_group"] = $activeMenuGroup;
        $data["active_menu_name"] = $activeMenuName;

        return view('master/MstIP/index', $data);
    }

    /**
     * Get all IP Master data for DataTable.
     */
    public function getData()
    {
        try {
            $statusFilter = $this->request->getGet('status');

            $query = $this->MstIPModel->orderBy('mip_lastupdate', 'DESC');

            // --- Logika Filter yang Sudah Kita Sepakati ---
            if ($statusFilter !== null && $statusFilter !== 'All') {
                if ($statusFilter === 'Used') {
                    // 'Used': mip_lastuser BUKAN 0 (dan bukan NULL) AND mip_status = 0
                    $query->where('mip_lastuser !=', 0);
                    $query->where('mip_status', 0);
                    // Tambahan untuk menangani NULL pada mip_lastuser jika ada
                    $query->where('mip_lastuser IS NOT NULL'); 
                } else { // 'Unused'
                    // 'Unused': mip_lastuser = 0 (atau NULL) OR mip_status = 1
                    $query->groupStart()
                            ->where('mip_lastuser', 0)
                            ->orWhere('mip_status', 1)
                            ->orWhere('mip_lastuser IS NULL') // Tambahan untuk NULL
                        ->groupEnd();
                }
            }
            
            $data = $query->findAll();

            foreach ($data as &$row) {
                // Pastikan mip_lastuser diubah ke integer atau 0 jika NULL/kosong
                $row['mip_lastuser'] = (int) $row['mip_lastuser'];
                $row['mip_status'] = (int) $row['mip_status'];

                // --- Logika Penentuan Teks Status (USED/UNUSED) ---
                if ($row['mip_lastuser'] !== 0 && $row['mip_status'] === 0) {
                    $row['mip_status_text'] = 'Used';
                } else {
                    $row['mip_status_text'] = 'Unused';
                }
                $row['mip_ipadd'] = trim($row['mip_ipadd']);
            }

            // DEBUGGING: Log data yang akan dikirim ke frontend
            // log_message('debug', 'Data sent for status filter "' . $statusFilter . '": ' . json_encode($data));

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching IP Master data: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                                 ->setJSON(['error' => true, 'message' => 'Could not retrieve data: ' . $e->getMessage()]);
        }
    }

    /**
     * Get detail of an IP Master record for modal.
     */
    public function getDetail()
    {
        $id = $this->request->getPost('id');

        if (empty($id)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => false, 'message' => 'ID is missing.']);
        }

        $row = $this->MstIPModel->find($id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Data not found.']);
        }

        // Pastikan mip_lastuser diubah ke integer atau 0 jika NULL/kosong
        $row['mip_lastuser'] = (int) $row['mip_lastuser'];
        $row['mip_status'] = (int) $row['mip_status'];

        $formattedData = [
            'mip_id'           => $row['mip_id'],
            'mip_vlanid'       => $row['mip_vlanid'],
            'mip_vlanname'     => $row['mip_vlanname'],
            'mip_ipadd'        => $row['mip_ipadd'],
            'mip_lastuser'     => $row['mip_lastuser'],
            // Tentukan mip_status_text berdasarkan logika yang diinginkan
            'mip_status_text'  => ($row['mip_lastuser'] !== 0 && $row['mip_status'] === 0) ? 'Used' : 'Unused',
            'mip_status_raw'   => $row['mip_status'], 
            'mip_lastuser_raw' => $row['mip_lastuser'], 
        ];

        if (isset($row['mip_lastupdate']) && !empty($row['mip_lastupdate'])) {
            try {
                $timeString = trim($row['mip_lastupdate']);
                $time = \CodeIgniter\I18n\Time::parse($timeString);
                $formattedData['mip_lastupdate'] = $time->toLocalizedString('id-ID', [
                    'year' => 'numeric', 'month' => '2-digit', 'day' => '2-digit',
                    'hour' => '2-digit', 'minute' => '2-digit', 'second' => '2-digit'
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Error parsing or formatting mip_lastupdate for ID ' . $id . ': ' . $e->getMessage());
                $formattedData['mip_lastupdate'] = 'Parsing Error: ' . $e->getMessage() . ' (Raw: ' . $row['mip_lastupdate'] . ')';
            }
        } else {
            $formattedData['mip_lastupdate'] = '-';
        }

        return $this->response->setJSON(['status' => true, 'data' => $formattedData]);
    }

    /**
     * Toggle the status of an IP Master record.
     */
    public function toggleStatus()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request.']);
        }

        $id = $this->request->getPost('id');
        $currentMipStatus = (int) $this->request->getPost('currentMipStatus'); // Pastikan integer
        $currentMipLastUser = (int) $this->request->getPost('currentMipLastUser'); // Pastikan integer

        if (empty($id) || !isset($currentMipStatus) || !isset($currentMipLastUser)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Missing ID or current status/last user.']);
        }

        $row = $this->MstIPModel->find($id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Data not found.']);
        }

        // Tentukan status logis saat ini (Used/Unused) berdasarkan definisi Anda
        $isCurrentlyUsedLogical = ($currentMipLastUser !== 0 && $currentMipStatus === 0);
        
        $newMipStatus;
        $newMipLastUser;

        if ($isCurrentlyUsedLogical) {
            // Jika saat ini dianggap 'Used' secara logis, ubah ke 'Unused'
            $newMipStatus = 1; // Set mip_status ke 1
            $newMipLastUser = 0; // Set mip_lastuser ke 0
        } else {
            // Jika saat ini dianggap 'Unused' secara logis, ubah ke 'Used'
            $newMipStatus = 0; // Set mip_status ke 0
            $newMipLastUser = session()->get('user_id'); // Set mip_lastuser ke ID pengguna yang login
            if (empty($newMipLastUser)) {
                 $newMipLastUser = 1; // Fallback jika session()->get('user_id') kosong, misalnya 1
                 log_message('warning', 'session()->get("user_id") is empty, defaulting mip_lastuser to 1.');
            }
        }

        try {
            $this->MstIPModel->update($id, [
                'mip_status' => $newMipStatus,
                'mip_lastupdate' => date('Y-m-d H:i:s'),
                'mip_lastuser' => $newMipLastUser,
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully.']);

        } catch (\Exception $e) {
            log_message('error', 'Error toggling IP Master status for ID ' . $id . ': ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to update status: ' . $e->getMessage()]);
        }
    }
}