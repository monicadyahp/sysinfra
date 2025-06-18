<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class C_Main extends BaseController
{
    public function index()
    {
        if (!session()->get("login")) {
            return redirect()->to("/");
        }

        // Retrieve the user menu from the session
        $usermenu = session()->get("usermenu");

        // Find the active menu group and menu name
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstMachine") {
                $activeMenuGroup    = $menu->umg_groupname;
                $activeMenuName     = $menu->umn_menuname;
                break; // Stop the loop once the active menu is found
            }
        }

        $data["active_menu_group"]  = $activeMenuGroup;
        $data["active_menu_name"]   = $activeMenuName;

        return view("main/dashboard", $data);
    }

    public function open_menu($menupath)
    {
        return redirect()->to(base_url($menupath));
    }
}
