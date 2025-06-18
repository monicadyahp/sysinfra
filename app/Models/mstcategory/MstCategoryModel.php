<?php

namespace App\Models\mstcategory;

use CodeIgniter\Model;

class MstCategoryModel extends Model
{
    protected $db_sysinfra;

    public function __construct()
    {
        // Koneksi dari database ‘jinsystem’ untuk membuat query pada tabel m_equipmentcat
        $this->db_sysinfra = db_connect('jinsystem');
    }
    
    public function getDataCategory()
    {
        return $this->db_sysinfra->query("
            SELECT m.*
            FROM m_equipmentcat m
            WHERE ec_status <> 25 ORDER BY ec_lastupdate DESC
        ")->getResult();
    }

    public function storeCategory($data)
    {
        $insertData = [
            'equipmentcat' => !empty($data['categoryName']) ? $data['categoryName'] : null,
            'ec_status' => 1,
            'ec_lastupdate' => date("Y-m-d H:i:s"),
            // 08-04 tambahan monic
            'es_lastuser' => 1
        ];
        
        try {
            $this->db_sysinfra->table('m_equipmentcat')->insert($insertData);
            return [
                'status' => true,
                'message' => 'Category has been successfully saved.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Error saving category: ' . $e->getMessage()
            ];
        }
    }

    public function getDataById($categoryName)
    {
        return $this->db_sysinfra->table('m_equipmentcat')
            ->where('equipmentcat', $categoryName)
            ->get()
            ->getRowArray();
    }

    public function updateCategory($data)
    {
        $builder = $this->db_sysinfra->table('m_equipmentcat');

        try {
            $builder->where('equipmentcat', $data['oldCategoryName'])
                ->update([
                    'equipmentcat' => !empty($data['categoryName']) ? $data['categoryName'] : null,
                    'ec_status' => 1,
                    'ec_lastupdate' => date("Y-m-d H:i:s"),
                    // 08-04 tambahan monic
                    'es_lastuser' => 1
                ]);

            return [
                'status' => true,
                'message' => 'Category has been successfully updated.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating category: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error updating category: ' . $e->getMessage()
            ];
        }
    }

    public function deleteCategory($categoryName)
    {
        $builder = $this->db_sysinfra->table('m_equipmentcat');

        try {
            $builder->where('equipmentcat', $categoryName)
            ->update([
                'ec_status' => 25,
                'ec_lastupdate' => date("Y-m-d H:i:s"),
                'es_lastuser' => session()->get('user_info')['ua_userid'] ?? null
            ]);
            return [
                'status' => true,
                'message' => 'Category has been successfully deleted.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting category: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error deleting category: ' . $e->getMessage()
            ];
        }
    }
}