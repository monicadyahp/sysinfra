<?php

namespace App\Controllers\Transaction;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use CodeIgniter\Session\Session;
use App\Models\M_Auth;
use App\Models\M_MenuAccess;

class ChangePass extends BaseController
{
    protected $session;
    
    protected $M_Auth;
    protected $M_MenuAccess;

    public function __construct()
    {
        $this->M_Auth = new M_Auth();
        $this->M_MenuAccess = new M_MenuAccess();
        $this->session = session();
    }


    public function index()
    {
        if (session()->get('login')) {
            $data['users'] = $this->M_MenuAccess->get_users_change();
            $data['apps'] = $this->M_MenuAccess->get_apps();
    
            // Retrieve the user menu from the session
            $usermenu = session()->get("usermenu");
    
            // Inisialisasi variabel agar tidak error jika menu tidak ditemukan
            $activeMenuGroup = null;
            $activeMenuName = null;
    
            // Find the active menu group and menu name
            if (!empty($usermenu)) {
                foreach ($usermenu as $menu) {
                    if ($menu->umn_path === "transaction/ChangePass") { // Ubah path sesuai yang diinginkan
                        $activeMenuGroup = $menu->umg_groupname;
                        $activeMenuName = $menu->umn_menuname;
                        break; // Stop the loop once the active menu is found
                    }
                }
            }
    
            $data["active_menu_group"] = $activeMenuGroup;
            $data["active_menu_name"] = $activeMenuName;
    
            return view('transaction/ChangePass', $data);
        }
    
        return view('transaction/ChangePass'); // Pastikan view ini tersedia
    }
    

   
}
