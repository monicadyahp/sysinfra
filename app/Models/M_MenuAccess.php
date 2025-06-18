<?php

namespace App\Models;

use CodeIgniter\Model;

class M_MenuAccess extends Model
{

    protected $db_postgree;

    public function __construct()
    {
        parent::__construct();
        $this->db_postgree = \Config\Database::connect('jincommon');
    }
    
    public function get_employee()
    {
        return $this->db_postgree->query("SELECT * FROM tbmst_employee 
            LEFT JOIN tbmst_section ON sec_sectioncode = em_sectioncode WHERE em_emplstatus < 200 ORDER BY em_emplcode")->getResult();
    }

    public function get_users()
    {
        return $this->db_postgree->query("SELECT ua_userid AS value, ua_username AS text FROM tbua_useraccess")->getResult();
    }

    public function get_users_change()
    {
        return $this->db_postgree->query("SELECT ua.ua_username AS value, ua.ua_username AS text
                                            FROM tbua_useraccess ua
                                            WHERE ua.ua_username IN (SELECT rolname FROM pg_roles)
                                            ORDER BY ua.ua_username ASC")->getResult();
    }

    public function get_apps()
    {
        return $this->db_postgree->query("SELECT appcode AS value, appname AS text FROM m_application")->getResult();
    }

    public function get_groupnames($app)
    {
        return $this->db_postgree->query("SELECT groupcode AS value, groupname AS text FROM m_menugroup WHERE groupappcode = $app")->getResult();
    }

    public function get_menus($group)
    {
        return $this->db_postgree->query("SELECT mn_menucode AS value, mn_menuname AS text FROM m_menuname WHERE mn_groupcode = $group")->getResult();
    }

    public function get_useraccess($user, $menu)
    {
        return $this->db_postgree->query("SELECT * FROM tbua_usermenusetup WHERE ums_userid = $user AND ums_menuid = $menu")->getRow();
    }

    public function save_access($data)
    {
        if (!session()->get('login')) {
            return array('status' => '', 'message' => 'Your session has expired. Please login again.');
        }

        $data["ums_add"] = isset($data["ums_add"]) ? 1 : 0;
        $data["ums_edit"] = isset($data["ums_edit"]) ? 1 : 0;
        $data["ums_delete"] = isset($data["ums_delete"]) ? 1 : 0;
        $data["ums_generate"] = isset($data["ums_generate"]) ? 1 : 0;
        $data["ums_post"] = isset($data["ums_post"]) ? 1 : 0;
        $data["ums_print"] = isset($data["ums_print"]) ? 1 : 0;
        $data["ums_view"] = 1;
        $data["ums_designreport"] = 0;
        $data["ums_lastuser"] = session()->get('username');
        $data["ums_lastupdate"] = date("Y-m-d H:i:s");

        if (
            $data["ums_add"] == 0 &&
            $data["ums_edit"] == 0 &&
            $data["ums_delete"] == 0 &&
            $data["ums_generate"] == 0 &&
            $data["ums_post"] == 0 &&
            $data["ums_print"] == 0
        ) {
            $this->db_postgree->table("tbua_usermenusetup")
                ->where("ums_userid", $data["ums_userid"])->where("ums_menuid", $data["ums_menuid"])
                ->delete();
        } else {
            $check = $this->db_postgree->table("tbua_usermenusetup")
                ->where("ums_userid", $data["ums_userid"])->where("ums_menuid", $data["ums_menuid"])
                ->get()->getNumRows();

            if ($check) {
                $this->db_postgree->table("tbua_usermenusetup")
                    ->where("ums_userid", $data["ums_userid"])->where("ums_menuid", $data["ums_menuid"])
                    ->update($data);
            } else {
                $this->db_postgree->table("tbua_usermenusetup")->insert($data);
            }
        }

        return array('status' => 'success', 'message' => '');
    }

    public function get_user($id)
    {
        return $this->db_postgree->query("SELECT * FROM tbua_useraccess WHERE ua_userid = $id")->getRow();
    }

    public function get_app($id)
    {
        return $this->db_postgree->query("SELECT * FROM m_application WHERE appcode = $id")->getRow();
    }

    public function get_group($id)
    {
        return $this->db_postgree->query("SELECT b.appname AS add_appname, a.* FROM m_menugroup a LEFT JOIN m_application b ON appcode = groupappcode
            WHERE groupcode = $id")->getRow();
    }

    public function get_menu($id)
    {
        return $this->db_postgree->query("SELECT b.groupname AS add_groupname, a.* FROM m_menuname a LEFT JOIN m_menugroup b ON groupcode = mn_groupcode
            WHERE mn_menucode = $id")->getRow();
    }

    public function save_app($data)
    {
        if (!session()->get('login')) {
            return array('status' => '', 'message' => 'Your session has expired. Please login again.');
        }

        $id = $data["appcode"];
        unset($data["appcode"]);
        $data["appisactive"] = isset($data["appisactive"]) ? 1 : 0;
        $data["appusername"] = session()->get('username');
        $data["applastupdate"] = date("Y-m-d H:i:s");

        if (empty($id)) {
            $this->db_postgree->table("m_application")->insert($data);
        } else {
            $this->db_postgree->table("m_application")->where("appcode", $id)->update($data);
        }

        return array('status' => 'success', 'message' => '');
    }

    public function save_group($data)
    {
        if (!session()->get('login')) {
            return array('status' => '', 'message' => 'Your session has expired. Please login again.');
        }

        $id = $data["groupcode"];
        unset($data["groupcode"]);
        $data["grouplastuser"] = session()->get('username');
        $data["grouplastupdate"] = date("Y-m-d H:i:s");

        if (empty($id)) {
            $this->db_postgree->table("m_menugroup")->insert($data);
        } else {
            $this->db_postgree->table("m_menugroup")->where("groupcode", $id)->update($data);
        }

        return array('status' => 'success', 'message' => '');
    }

    public function save_menu($data)
    {
        if (!session()->get('login')) {
            return array('status' => '', 'message' => 'Your session has expired. Please login again.');
        }

        $id = $data["mn_menucode"];
        unset($data["mn_menucode"]);
        $data["mn_isactive"] = isset($data["mn_isactive"]) ? 1 : 0;
        $data["mn_lastuser"] = session()->get('username');
        $data["mn_lastupdate"] = date("Y-m-d H:i:s");

        if (empty($id)) {
            $this->db_postgree->table("m_menuname")->insert($data);
        } else {
            $this->db_postgree->table("m_menuname")->where("mn_menucode", $id)->update($data);
        }

        return array('status' => 'success', 'message' => '');
    }
}
