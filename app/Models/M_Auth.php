<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Auth extends Model
{
    protected $db_postgree;

    public function __construct()
    {
        // Gunakan koneksi 'jincommon'
        $this->db_postgree = db_connect('jincommon');
    }

    public function get_menu($username)
    {
        $query = "  SELECT e.ua_username, b.groupname umg_groupname, b.groupclass umg_class, b.groupcode umg_groupcode, groupicon, c.mn_menuname umn_menuname, c.mn_path umn_path, c.mn_menucode umn_menucode, e.ua_rolescommon rolescommon, e.ua_rolespps rolespps, e.ua_rolesmorpics rolesmorpics, e.ua_rolesjinsystem rolesjinsystem, ua_roleshrpa roleshrpa, ums_add, ums_edit, ums_delete, ums_generate, ums_post, ums_print
                    FROM m_application a
                    INNER JOIN m_menugroup b
                        ON a.appcode                = b.groupappcode
                    INNER JOIN m_menuname c 
                        ON b.groupcode              = c.mn_groupcode
                    INNER JOIN tbua_usermenusetup d
                        ON c.mn_menucode            = d.ums_menuid
                    INNER JOIN tbua_useraccess e
                        ON d.ums_userid             = e.ua_userid
                    WHERE a.appname                 = 'SYSINFRA'
                        AND e.ua_username           = ? 
                    ORDER BY e.ua_username, b.groupname, c.mn_menuname";

        return $this->db_postgree->query($query, [$username])->getResult();
    }

    public function get_user_info($db_common_sql, $username)
    {
        $user = $this->db_postgree->table("tbua_useraccess")->where("ua_username", $username)->get()->getRowArray();
        $userinfo = $db_common_sql->table('TBMST_Employee a')
            ->select('a.EM_EmplCode, a.EM_EmplName, b.SM_SexName, b.SM_SexNick1, b.SM_SexNick2, a.EM_Email, a.EM_PositionCode, a.EM_SectionCode, s.sec_departmentnaming, s.sec_sectionnaming, s.sec_teamnaming, s.sec_department')
            ->join('TBMST_Section s', 'a.EM_SectionCode = s.SEC_SECTIONCODE', 'inner')
            ->join('TBMST_Sex b', 'a.EM_SexCode = b.SM_SexCode', 'inner')
            ->where('a.EM_EmplCode', $user['ua_emplcode'])
            ->where('a.EM_EmplStatus <', 200)
            ->get()
            ->getRowArray();

        return $userinfo;
    }
}