<?php

namespace App\Controllers;

use App\Models\M_Auth;
use App\Models\M_MenuAccess; // Make sure this model exists and is correctly configured

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

        // If the current time minus the last login time is more than 2 hours (7200 seconds), then
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
            'username'     => $username, // Using input username for DB connection
            'password'     => $password, // Using input password for DB connection
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

        try {
            // Attempt to connect to the database with provided credentials
            $db_common = \Config\Database::connect($db_sysinfra);
            // This line will throw an exception if credentials are bad
            $db_common->query("SELECT 1"); 

            // If connection is successful, retrieve user and employee info
            // Fetch user data from tbua_useraccess using the username
            $userAccessInfo = $db_common->table("tbua_useraccess")
                                         ->where("ua_username", $username)
                                         ->get()
                                         ->getRowArray(); // Use getRowArray() to easily put it into session

            if (empty($userAccessInfo)) {
                return $this->response->setJSON([
                    "success"   => false,
                    "error"     => "User account not found or inactive. Please contact the administrator.",
                ]);
            }

            // Authenticate user's password (if not already done by DB connection)
            // If your DB connection *itself* handles authentication (as implied by passing username/password to it)
            // and the SELECT 1 query above validates credentials, then this part is sufficient.
            // Otherwise, you'd need explicit password hashing/verification here.

            // Set basic login session flags
            session()->set('db_sysinfra', $db_sysinfra); // Keep DB connection details if needed elsewhere
            session()->set("login", true);
            session()->set("username", $username);
            session()->set("last_visited", time());
            
            // --- CRITICAL CHANGE: Set 'user_info' with 'ua_userid' and 'em_emplcode' ---
            session()->set('user_info', [
                'ua_userid' => $userAccessInfo['ua_userid'], // Assuming 'ua_userid' exists in $userAccessInfo
                'ua_username' => $userAccessInfo['ua_username'],
                'em_emplcode' => $userAccessInfo['ua_emplcode'] ?? null // Include employee code if available and relevant
            ]);
            // --- END CRITICAL CHANGE ---

            // Ambil menu user dari model M_Auth (menggunakan instance db_common)
            // Note: M_Auth::__construct uses db_connect('jincommon'), so it might not need $db_common instance passed.
            // If M_Auth->get_menu also connects to 'jincommon', passing $db_common here might be redundant or problematic.
            // Let's assume M_Auth is configured to use 'jincommon' connection properly.
            $usermenu = $this->M_Auth->get_menu($username);
            
            // Simpan usermenu kedalam session
            session()->set("usermenu", $usermenu);

            // Jika tidak ada menu, kembalikan error
            if (empty($usermenu)) {
                // Destroy newly created session as there are no menus
                session()->destroy(); 
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

        } catch (\Exception $e) {
            // Handle database connection or query errors during login
            log_message('error', 'Login Error: ' . $e->getMessage());
            return $this->response->setJSON([
                "success"   => false,
                "error"     => "Invalid username or password, or database connection error.",
            ]);
        }
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