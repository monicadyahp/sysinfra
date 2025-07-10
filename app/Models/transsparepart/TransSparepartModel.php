<?php

namespace App\Models\transsparepart;

use CodeIgniter\Model;

class TransSparepartModel extends Model {
    protected $db_postgree;
    protected $db_sysinfra;
    
    public function __construct()
    {
        $this->db_postgree = db_connect('jincommon');
        $this->db_sysinfra = db_connect('jinsystem');
    }
    
    public function getData()
    {
        // Karena `t_equipmentmovement` tidak memiliki kolom `tea_status`,
        // hapus klausa WHERE yang merujuk padanya.
        // Ini akan menampilkan semua data dari t_equipmentmovement.
        return $this->db_sysinfra->query("SELECT * FROM t_equipmentmovement ORDER BY tea_id DESC")->getResult();
    }

    public function getDataById($id)
    {
        // Karena `t_equipmentmovement` tidak memiliki kolom `tea_status`,
        // hapus klausa WHERE yang merujuk padanya.
        return $this->db_sysinfra->query("
            SELECT *
            FROM t_equipmentmovement
            WHERE tea_id = ?
        ", [$id])->getRow();
    }
    
    public function storeData($data)
    {
        // Check duplicate asset number
        // Anda mungkin ingin mempertimbangkan juga apakah tea_assetno adalah primary key di t_equipmentmovement
        // Jika ya, database akan menanganinya otomatis. Jika bukan, dan Anda tidak ingin duplikasi assetNo,
        // maka pengecekan ini diperlukan.
        $existing = $this->db_sysinfra->table('t_equipmentmovement')
            ->where('tea_assetno', $data['assetNo'])
            ->countAllResults();

        if ($existing > 0) {
            return [
                'status' => false,
                'message' => 'Asset number already exists in equipment movement.' // Pesan lebih spesifik
            ];
        }

        $insertData = [
            'tea_assetno'          => isset($data['assetNo']) && $data['assetNo'] !== '' ? $data['assetNo'] : null,
            'tea_transactiondate'  => isset($data['tsdate']) && $data['tsdate'] !== '' ? $data['tsdate'] : date('Y-m-d'),
            'tea_pcname'           => isset($data['pcname']) && $data['pcname'] !== '' ? $data['pcname'] : null,
            'tea_ipaddress'        => isset($data['ipaddress']) && $data['ipaddress'] !== '' ? $data['ipaddress'] : null,
            'tea_fromlocation'     => isset($data['fromlocation']) && $data['fromlocation'] !== '' ? $data['fromlocation'] : null,
            'tea_tolocation'       => isset($data['tolocation']) && $data['tolocation'] !== '' ? $data['tolocation'] : null,
            'tea_fromuser'         => isset($data['fromuser']) && $data['fromuser'] !== '' ? $data['fromuser'] : null,
            'tea_touser'           => isset($data['touser']) && $data['touser'] !== '' ? $data['touser'] : null,
            'tea_category'         => isset($data['category']) && $data['category'] !== '' ? $data['category'] : null,
            'tea_purpose'          => isset($data['purpose']) && $data['purpose'] !== '' ? $data['purpose'] : null,
            'tea_returnoldequip'   => isset($data['return']) && $data['return'] !== '' ? $data['return'] : null,
            'tea_serialnumber'     => isset($data['serialnumber']) && $data['serialnumber'] !== '' ? $data['serialnumber'] : null,
            // Baris 'tea_status' dihilangkan karena tidak ada di skema DB yang diberikan
            'tea_lastuser'         => session()->get('user_info')['em_emplcode'],
            'tea_lastupdate'       => date('Y-m-d H:i:s')
        ];

        try {
            $this->db_sysinfra->table('t_equipmentmovement')->insert($insertData);
            return [
                'status' => true,
                'message' => 'Data saved successfully.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'An error occurred while saving data: ' . $e->getMessage()
            ];
        }
    }
    
    public function updateData($data)
    {
        // Check duplicate asset number (exclude current ID)
        $builder = $this->db_sysinfra->table('t_equipmentmovement');
        $existing = $builder->where('tea_assetno', $data['assetNo'])
                            ->where('tea_id !=', $data['id'])
                            ->countAllResults();
        
        if ($existing > 0) {
            return [
                'status' => false,
                'message' => 'Asset number already exists in another record'
            ];
        }
            
        $updateData = [
            'tea_assetno'          => isset($data['assetNo']) && $data['assetNo'] !== '' ? $data['assetNo'] : null,
            'tea_transactiondate'  => isset($data['tsdate']) && $data['tsdate'] !== '' ? $data['tsdate'] : date('Y-m-d'),
            'tea_pcname'           => isset($data['pcname']) && $data['pcname'] !== '' ? $data['pcname'] : null,
            'tea_ipaddress'        => isset($data['ipaddress']) && $data['ipaddress'] !== '' ? $data['ipaddress'] : null,
            'tea_fromlocation'     => isset($data['fromlocation']) && $data['fromlocation'] !== '' ? $data['fromlocation'] : null,
            'tea_tolocation'       => isset($data['tolocation']) && $data['tolocation'] !== '' ? $data['tolocation'] : null,
            'tea_fromuser'         => isset($data['fromuser']) && $data['fromuser'] !== '' ? $data['fromuser'] : null,
            'tea_touser'           => isset($data['touser']) && $data['touser'] !== '' ? $data['touser'] : null,
            'tea_category'         => isset($data['category']) && $data['category'] !== '' ? $data['category'] : null,
            'tea_purpose'          => isset($data['purpose']) && $data['purpose'] !== '' ? $data['purpose'] : null,
            'tea_returnoldequip'   => isset($data['return']) && $data['return'] !== '' ? $data['return'] : null,
            'tea_serialnumber'     => isset($data['serialnumber']) && $data['serialnumber'] !== '' ? $data['serialnumber'] : null,
            'tea_lastuser'         => session()->get('user_info')['em_emplcode'],
            'tea_lastupdate'       => date('Y-m-d H:i:s')
        ];
        
        try {
            $affectedRows = $builder->where('tea_id', $data['id'])
                ->update($updateData);
            
            if ($affectedRows > 0) {
                return [
                    'status' => true,
                    'message' => 'Data updated successfully.'
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'No changes made or record not found.'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating data: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'An error occurred while updating data: ' . $e->getMessage()
            ];
        }
    }

    public function deleteData($id)
    {
        try {
            // Karena tidak ada kolom tea_status untuk soft delete, lakukan hard delete
            $this->db_sysinfra->table('t_equipmentmovement')
                ->where('tea_id', $id)
                ->delete();

            return [
                'status' => true,
                'message' => 'Sparepart data have been deleted successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting Sparepart: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Failed to delete Sparepart: ' . $e->getMessage()
            ];
        }
    }
    
    public function getSections()
    {
        return $this->db_postgree->query("
            SELECT 
                MIN(sec_sectioncode) AS sec_sectioncode,
                sec_section
            FROM 
                tbmst_section 
            WHERE 
                sec_status <> 25 
            GROUP BY
                sec_section
            ORDER BY
                sec_section ASC;
        ")->getResult();
    }

    public function getAssetNo($assetNo)
    {
        if (empty($assetNo)) {
            return null;
        }
                
        $query = "
                SELECT 
                    e_assetno, 
                    e_equipmentid, 
                    e_serialnumber, 
                    e_equipmentname
                FROM m_itequipment
                WHERE e_assetno = ?
        ";
                
        return $this->db_sysinfra->query($query, [$assetNo])->getRow();
    }

    public function searchAssetNo($search = null)
    {
        // Untuk serverSide: false, kita tidak perlu memproses parameter DataTables seperti draw, start, length, order.
        // Kita hanya perlu mengambil semua data yang cocok dengan pencarian (jika ada) dan filter kustom.
        
        $builder = $this->db_sysinfra->table('m_itequipment e');
        $builder->select('e.e_id, e.e_assetno, e.e_equipmentid, e.e_serialnumber, e.e_equipmentname, e.e_receivedate, e.e_status');
        
        // Filter awal: Pastikan equipmentid tidak NULL dan statusnya 'Active'
        $builder->where('e.e_equipmentid IS NOT NULL');
        $builder->where('e.e_status', 'Active'); // Sesuai dengan data contoh Anda

        // Jika ada pencarian dari input search DataTables
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('LOWER(e.e_assetno)', strtolower($search))
                    ->orLike('LOWER(CAST(e.e_equipmentid AS TEXT))', strtolower($search)) 
                    ->orLike('LOWER(e.e_serialnumber)', strtolower($search))
                    ->orLike('LOWER(e.e_equipmentname)', strtolower($search))
                    ->groupEnd();
        }

        // --- PENTING: Keputusan tentang aset yang sudah ada di t_equipmentmovement
        // Jika Anda ingin menampilkan *semua* data m_itequipment: pastikan blok `NOT EXISTS` di bawah ini DIKOMENTARI.
        // Jika Anda ingin hanya menampilkan aset yang *belum* tercatat di t_equipmentmovement: hapus komentar blok di bawah ini.
        /*
        $builder->whereNotIn('e.e_assetno', function($subquery) {
            $subquery->select('CAST(tea_assetno AS TEXT)')
                     ->from('t_equipmentmovement')
                     ->where('tea_assetno IS NOT NULL');
        });
        */
        
        // Tambahkan pengurutan default
        $builder->orderBy('e.e_assetno', 'ASC');

        return $builder->get()->getResultArray(); // Mengembalikan array untuk DataTables frontend
    }

    public function getEmployeeById($employeeId)
    {
        return $this->db_postgree->table('tbmst_employee')
            ->select('em_emplcode, em_emplname, em_email, em_sectioncode')
            ->where('em_emplcode', $employeeId)
            ->where('em_emplstatus', 1)
            ->get()
            ->getRowArray();
    }
    
    public function searchEmployees()
    {
        
        $query = "
            SELECT 
                emp.em_emplcode,
                emp.em_emplname,
                sec.sec_section,
                sec.sec_sectioncode AS em_sectioncode,
                pos.pm_positionname
            FROM 
                tbmst_employee emp
            LEFT JOIN 
                tbmst_section sec ON emp.em_sectioncode = sec.sec_sectioncode
            LEFT JOIN
                tbmst_position pos ON emp.em_positioncode = pos.pm_code
            WHERE 
                emp.em_emplstatus < 200
        ";
        
        return $this->db_postgree->query($query)->getResult();
    }

}