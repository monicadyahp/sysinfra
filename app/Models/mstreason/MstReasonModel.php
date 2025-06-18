<?php

namespace App\Models\mstreason;

use CodeIgniter\Model;

class MstReasonModel extends Model
{
    protected $db_sysinfra;

    public function __construct()
    {
        // Gunakan koneksi ke database "jinsystem" karena tabel sistem (termasuk tbmst_disposereason) ada di sana.
        $this->db_sysinfra = db_connect('jinsystem');
    }
    
    // Ambil data dari tabel tbmst_disposereason
    public function getDataReason()
    {
        return $this->db_sysinfra->query("
            SELECT *
            FROM tbmst_disposereason
            WHERE td_status <> 25
            ORDER BY td_lastupdate DESC
        ")->getResult();
    }

    public function storeReason($data)
    {
        $insertData = [
            'td_reason'     => !empty($data['reasonName']) ? $data['reasonName'] : null,
            'td_status'     => 1,
            'td_lastupdate' => date("Y-m-d H:i:s"),
            'td_lastuser'   => 1
        ];        
        
        try {
            $this->db_sysinfra->table('tbmst_disposereason')->insert($insertData);
            return [
                'status'  => true,
                'message' => 'Reason has been successfully saved.'
            ];
        } catch (\Exception $e) {
            return [
                'status'  => false,
                'message' => 'Error saving reason: ' . $e->getMessage()
            ];
        }
    }

    public function getDataById($reasonName)
    {
        return $this->db_sysinfra->table('tbmst_disposereason')
            ->where('td_reason', $reasonName)
            ->get()
            ->getRowArray();
    }

    public function updateReason($data)
    {
        $builder = $this->db_sysinfra->table('tbmst_disposereason');
        
        try {
            $builder->where('td_reason', $data['oldReason'])
                ->update([
                    'td_reason'     => !empty($data['reasonName']) ? $data['reasonName'] : null,
                    'td_status'     => 1,
                    'td_lastupdate' => date("Y-m-d H:i:s"),
                    'td_lastuser'   => 1
                ]);

            return [
                'status'  => true,
                'message' => 'Reason has been successfully updated.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating reason: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Error updating reason: ' . $e->getMessage()
            ];
        }
    }

    public function deleteReason($reasonName)
    {
        $builder = $this->db_sysinfra->table('tbmst_disposereason');
        
        try {
            $builder->where('td_reason', $reasonName)
                ->update([
                    'td_status'     => 25,
                    'td_lastupdate' => date("Y-m-d H:i:s"),
                    'td_lastuser'   => 1
                ]);
            return [
                'status'  => true,
                'message' => 'Reason has been successfully deleted.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting reason: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Error deleting reason: ' . $e->getMessage()
            ];
        }
    }
}