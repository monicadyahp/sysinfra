<?php

namespace App\Models\transsparepart;

use CodeIgniter\Model;

class TransSparepartModel extends Model
{
    protected $db_sysinfra;

    public function __construct()
    {
        parent::__construct();
        $this->db_sysinfra = db_connect('jinsystem'); // Paksa koneksi ke database "jinsystem"
    }
    
    public function getData()
    {
        return $this->db_sysinfra->query("SELECT * FROM t_equipmentmovement ORDER BY tea_id DESC")->getResult();
    }

    public function storeData($data)
    {
        $insertData = [
            'tea_assetno'          => isset($data['assetNo']) && $data['assetNo'] !== '' ? $data['assetNo'] : null,
            'tea_transactiondate'  => isset($data['tsdate']) && $data['tsdate'] !== '' ? $data['tsdate'] : null,
            'tea_pcname'           => isset($data['pcname']) && $data['pcname'] !== '' ? $data['pcname'] : null,
            'tea_ipaddress'        => isset($data['ipaddress']) && $data['ipaddress'] !== '' ? $data['ipaddress'] : null,
            'tea_fromlocation'     => isset($data['fromlocation']) && $data['fromlocation'] !== '' ? $data['fromlocation'] : null,
            'tea_tolocation'       => isset($data['tolocation']) && $data['tolocation'] !== '' ? $data['tolocation'] : null,
            'tea_fromuser'         => isset($data['fromuser']) && $data['fromuser'] !== '' ? $data['fromuser'] : null,
            'tea_touser'           => isset($data['touser']) && $data['touser'] !== '' ? $data['touser'] : null,
            'tea_category'         => isset($data['category']) && $data['category'] !== '' ? $data['category'] : null,
            'tea_purpose'          => isset($data['purpose']) && $data['purpose'] !== '' ? $data['purpose'] : null,
            'tea_returnoldequip'   => isset($data['return']) && $data['return'] !== '' ? $data['return'] : null,
            'tea_serialnumber'     => isset($data['serialnumber']) && $data['serialnumber'] !== '' ? $data['serialnumber'] : null
        ];
        try {
            $this->db_sysinfra->table('t_equipmentmovement')->insert($insertData);
            return [
                'status' => true,
                'message' => 'Data berhasil disimpan.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ];
        }
    }

    public function getDataByid($id)
    {
        return $this->db_sysinfra->table('t_equipmentmovement')
            ->where('tea_id', $id)
            ->get()
            ->getRowArray();
    }

    public function updateData($data)
    {
        $builder = $this->db_sysinfra->table('t_equipmentmovement');

        try {
            $builder->where('tea_id', $data['id'])
                ->update([
                    'tea_assetno'          => isset($data['assetNo']) && $data['assetNo'] !== '' ? $data['assetNo'] : null,
                    'tea_transactiondate'  => isset($data['tsdate']) && $data['tsdate'] !== '' ? $data['tsdate'] : null,
                    'tea_pcname'           => isset($data['pcname']) && $data['pcname'] !== '' ? $data['pcname'] : null,
                    'tea_ipaddress'        => isset($data['ipaddress']) && $data['ipaddress'] !== '' ? $data['ipaddress'] : null,
                    'tea_fromlocation'     => isset($data['fromlocation']) && $data['fromlocation'] !== '' ? $data['fromlocation'] : null,
                    'tea_tolocation'       => isset($data['tolocation']) && $data['tolocation'] !== '' ? $data['tolocation'] : null,
                    'tea_fromuser'         => isset($data['fromuser']) && $data['fromuser'] !== '' ? $data['fromuser'] : null,
                    'tea_touser'           => isset($data['touser']) && $data['touser'] !== '' ? $data['touser'] : null,
                    'tea_category'         => isset($data['category']) && $data['category'] !== '' ? $data['category'] : null,
                    'tea_purpose'          => isset($data['purpose']) && $data['purpose'] !== '' ? $data['purpose'] : null,
                    'tea_returnoldequip'   => isset($data['return']) && $data['return'] !== '' ? $data['return'] : null,
                    'tea_serialnumber'     => isset($data['serialnumber']) && $data['serialnumber'] !== '' ? $data['serialnumber'] : null
                ]);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error updating data: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteData($id)
    {
        $builder = $this->db_sysinfra->table('t_equipmentmovement');

        try {
            $builder->where('tea_id', $id)
                ->delete();
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error deleting data: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all active sections with distinct section names only
     */
    public function getActiveSections()
    {
        // Gunakan koneksi ke database "jincommon" untuk mengambil data section
        $dbCommon = db_connect('jincommon');
        return $dbCommon->query("
            SELECT 
                MIN(sec_sectioncode) as sec_sectioncode, 
                sec_section, 
                MIN(sec_department) as sec_department, 
                MIN(sec_division) as sec_division,
                NULL as sec_team,
                CONCAT(
                    CASE WHEN MIN(sec_division) IS NOT NULL AND MIN(sec_division) != '' 
                        THEN MIN(sec_division) || ' - ' ELSE '' END,
                    CASE WHEN MIN(sec_department) IS NOT NULL AND MIN(sec_department) != '' 
                        THEN MIN(sec_department) || ' - ' ELSE '' END,
                    sec_section
                ) AS full_section_name
            FROM 
                tbmst_section 
            WHERE 
                sec_status = 1 
            GROUP BY 
                sec_section
            ORDER BY 
                full_section_name ASC
        ")->getResult();
    }
        
    /**
     * Search employees by name or employee code, with option to exclude a specific employee
     */
    public function searchEmployees($search = '', $exclude = '')
    {
        $search = '%' . $search . '%';
        
        $query = "
            SELECT 
                em_emplcode, 
                em_emplname, 
                COALESCE(em_email, '') as em_email,
                sec.sec_section,
                pos.pm_positionname
            FROM 
                tbmst_employee emp
            LEFT JOIN 
                tbmst_section sec ON emp.em_sectioncode = sec.sec_sectioncode
            LEFT JOIN 
                tbmst_position pos ON emp.em_positioncode = pos.pm_code
            WHERE 
                em_emplstatus = 1 
                AND (
                    em_emplname ILIKE ? 
                    OR CAST(em_emplcode AS VARCHAR) ILIKE ?
                )
        ";
        
        // Add exclusion filter if needed
        if (!empty($exclude)) {
            $query .= " AND em_emplname != ?";
            $params = [$search, $search, $exclude];
        } else {
            $params = [$search, $search];
        }
        
        $query .= " ORDER BY em_emplname ASC LIMIT 100";
        
        return $this->db_sysinfra->query($query, $params)->getResult();
    }
    
    /**
     * Get available equipment for selection (not in movement or has been returned)
     */
    public function getAvailableEquipment()
    {
        return $this->db_sysinfra->query("
            SELECT e.* FROM m_itequipment e
            WHERE e.e_assetno NOT IN (
                SELECT tea_assetno FROM t_equipmentmovement 
                WHERE tea_returnoldequip IS NULL OR tea_returnoldequip = 0
            )
            ORDER BY e.e_id ASC
        ")->getResult();
    }
}