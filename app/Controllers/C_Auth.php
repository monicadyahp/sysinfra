<?php

namespace App\Controllers;

use App\Models\M_Auth;
use App\Models\M_MenuAccess;

class C_Auth extends BaseController
{
    protected $M_Auth;
    protected $M_MenuAccess;

    public function __construct()
    {
        $this->M_Auth = new M_Auth();
        $this->M_MenuAccess = new M_MenuAccess();
    }

    public function index()
    {
        // Check if session exists and is valid
        if (session()->get("login")) {
            $this->check_session_expiration();
        }

        echo view("auth/login");
    }

    // to check session based on last time visit
    private function check_session_expiration()
    {
        // Check last login time
        $lastVisited = session()->get("last_visited");

        // If the current time minus the last login time is more than 2 hours, then
        if (!$lastVisited || time() - $lastVisited > 7200) {
            // Session expired, destroy, and redirect to login
            session()->destroy();
            return redirect()->to("/");
        } else {
            // If not, update last activity time and redirect to controller admin
            // Update last activity time
            session()->set("last_visited", time());
        }
    }

    public function login()
    {
        $username   = $this->request->getPost("username");
        $password   = $this->request->getPost("password");

        // Menggunakan koneksi untuk tabel common
        $db_sysinfra = [
            'DSN'          => '',
            'hostname'     => '127.0.0.1',
            'username'     => $username,
            'password'     => $password,
            'database'     => 'jincommon', // Pastikan ini diubah dari 'jincommen'
            'schema'       => 'public',
            'DBDriver'     => 'Postgre',
            'DBPrefix'     => '',
            'pConnect'     => false,
            'DBDebug'      => (ENVIRONMENT !== 'production'),
            'charset'      => 'utf8',
            'swapPre'      => '',
            'failover'     => [],
            'port'         => 5432,
            'dateFormat'   => [
                'date'     => 'Y-m-d',
                'datetime' => 'Y-m-d H:i:s',
                'time'     => 'H:i:s',
            ],
        ];

        $db_common = db_connect($db_sysinfra);
        $db_common->query("SELECT 1"); // Jika terjadi error, maka password tidak valid
        session()->set('db_sysinfra', $db_sysinfra);
        session()->set("login", true);
        session()->set("username", $username);
        session()->set("last_visited", time());

        // Ambil menu user dari model M_Auth
        $usermenu = $this->M_Auth->get_menu($username);
        // Simpan usermenu kedalam session
        session()->set("usermenu", $usermenu);

        // Jika tidak ada menu, kembalikan error
        if (empty($usermenu)) {
            return $this->response->setJSON([
                "success"   => false,
                "error"     => "No menus assigned to your account. Please contact the administrator.",
            ]);
        }

        // Tentukan redireksi berdasarkan menu pertama (atau khusus jika username 'postgres', dll.)
        if ($username === 'postgres') {
            $redirectUrl = base_url('/MstEquipment');
        } else {
            $firstMenu = $usermenu[0];
            $redirectUrl = base_url($firstMenu->umn_path);
        }
        
        // Simpan pengaturan hak akses tiap menu kedalam session
        foreach ($usermenu as $menu) {
            $session_name = "menu_" . strval($menu->umn_menucode);
            $session_val = [
                "add"       => $menu->ums_add,
                "edit"      => $menu->ums_edit,
                "delete"    => $menu->ums_delete,
                "generate"  => $menu->ums_generate,
                "post"      => $menu->ums_post,
                "print"     => $menu->ums_print
            ];
            session()->set($session_name, $session_val);
        }

        return $this->response->setJSON([
            "status"        => "success",
            "message"       => "Login Success!",
            "redirect_url"  => $redirectUrl
        ]);
    }

    public function logout()
    {
        session()->destroy(); // Hapus semua session
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Logged out successfully',
            'redirect' => base_url('/')
        ]);
    }
    

    public function changePassword()
    {
        if (session()->get('login')) {
            $data['employee'] = $this->M_MenuAccess->get_employee();
            $data['users'] = $this->M_MenuAccess->get_users_change();
            $data['apps'] = $this->M_MenuAccess->get_apps();
            return view('transaction/ChangePass', $data);
        }
        return view('transaction/ChangePass'); // Pastikan ada view bernama forgot_password.php
    }

  
}
