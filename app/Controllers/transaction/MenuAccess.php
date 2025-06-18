<?php

namespace App\Controllers\Transaction;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use CodeIgniter\Session\Session;
use App\Models\M_Auth;
use App\Models\M_MenuAccess;


class MenuAccess extends BaseController
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
            $data['employee'] = $this->M_MenuAccess->get_employee();
            $data['users'] = $this->M_MenuAccess->get_users();
            $data['apps'] = $this->M_MenuAccess->get_apps();
            
            // Ambil user menu dari session
            $usermenu = session()->get("usermenu");

            // Cek menu yang aktif
            $activeMenuGroup = "";
            $activeMenuName = "";

            if (!empty($usermenu)) {
                foreach ($usermenu as $menu) {
                    if ($menu->umn_path === "transaction/MenuAccess") {
                        $activeMenuGroup = $menu->umg_groupname;
                        $activeMenuName = $menu->umn_menuname;
                        break;
                    }
                }
            }

            $data["active_menu_group"] = $activeMenuGroup;
            $data["active_menu_name"] = $activeMenuName;

            return view('transaction/MenuAccess', $data);
        }

        return view('transaction/MenuAccess');
    }

    
}
