<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\M_MenuAccess;

class C_MenuAccess extends BaseController
{
    protected $M_MenuAccess;

    public function __construct()
    {
        $this->M_MenuAccess = new M_MenuAccess();
    }

    public function get_employee()
    {
        $data = $this->M_MenuAccess->get_employee();
        return $this->response->setJSON($data);
    }

    public function get_users()
    {
        $data = $this->M_MenuAccess->get_users();
        return $this->response->setJSON($data);
    }

    public function get_users_change()
    {
        $data = $this->M_MenuAccess->get_users_change();
        return $this->response->setJSON($data);
    }

    public function get_apps()
    {
        $data = $this->M_MenuAccess->get_apps();
        return $this->response->setJSON($data);
    }

    public function get_groupnames()
    {
        $app = $this->request->getGet('app');
        $data = empty($app) ? [] : $this->M_MenuAccess->get_groupnames($app);
        return $this->response->setJSON($data);
    }

    public function get_menus()
    {
        $group = $this->request->getGet('group');
        $data = empty($group) ? [] : $this->M_MenuAccess->get_menus($group);
        return $this->response->setJSON($data);
    }

    public function get_useraccess()
    {
        $user = $this->request->getPost('user');
        $menu = $this->request->getPost('menu');
        $data = $this->M_MenuAccess->get_useraccess($user, $menu);
        return $this->response->setJSON($data);
    }

    public function save_access()
    {
        $data = $this->request->getPost();
        $result = $this->M_MenuAccess->save_access($data);
        return $this->response->setJSON($result);
    }

    public function get_user()
    {
        $id = $this->request->getGet('id');
        $data = $this->M_MenuAccess->get_user($id);
        return $this->response->setJSON($data);
    }

    public function get_app()
    {
        $id = $this->request->getGet('id');
        $data = $this->M_MenuAccess->get_app($id);
        return $this->response->setJSON($data);
    }

    public function get_group()
    {
        $id = $this->request->getGet('id');
        $data = $this->M_MenuAccess->get_group($id);
        return $this->response->setJSON($data);
    }

    public function get_menu()
    {
        $id = $this->request->getGet('id');
        $data = $this->M_MenuAccess->get_menu($id);
        return $this->response->setJSON($data);
    }

    public function save_app()
    {
        $data = $this->request->getPost();
        $result = $this->M_MenuAccess->save_app($data);
        return $this->response->setJSON($result);
    }

    public function save_group()
    {
        $data = $this->request->getPost();
        $result = $this->M_MenuAccess->save_group($data);
        return $this->response->setJSON($result);
    }

    public function save_menu()
    {
        $data = $this->request->getPost();
        $result = $this->M_MenuAccess->save_menu($data);
        return $this->response->setJSON($result);
    }

    public function changePassword()
    {
        $menuAccessModel = new M_MenuAccess(); 
        $data['users'] = $menuAccessModel->get_users_change(); 

        return view('transaction/ChangePass', $data);
    }
}
