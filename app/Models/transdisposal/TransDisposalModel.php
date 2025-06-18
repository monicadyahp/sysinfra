<?php

namespace App\Models\transdisposal;

use CodeIgniter\Model;

class TransDisposalModel extends Model
{
    protected $table = 't_dispose';
    protected $primaryKey = 'td_id';
    protected $allowedFields = [
        'td_assetno', 'td_category', 'td_reason', 'td_decisiondate', 
        'td_disposedate', 'td_serialnumber', 'td_lastupdate', 'td_lastuser'
    ];

    public function __construct()
    {
        parent::__construct();
        // Paksa koneksi ke database 'jinsystem'
        $this->db = db_connect('jinsystem');
    }

    // Method to get active sections (example)
    public function getActiveSections()
    {
        // Example query: Adjust based on your actual requirements
        try {
            // Replace with actual query to fetch active sections
            $sections = $this->db->table('jinsystem')  // Replace with actual table name
                ->where('is_active', 1)  // Example condition for active sections
                ->get()
                ->getResult();
    
            return $sections;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching active sections: ' . $e->getMessage());
            return [];
        }
    }

    // 14-04 monic
    public function getDataDisposal()
    {
        try {
            $builder = $this->db->table($this->table);
            $builder->select("t_dispose.td_id,
                            t_dispose.td_assetno,
                            t_dispose.td_category,
                            tbmst_disposereason.td_reason, 
                            t_dispose.td_decisiondate,
                            t_dispose.td_disposedate,
                            t_dispose.td_lastupdate,
                            t_dispose.td_lastuser,
                            t_dispose.td_serialnumber");
                            //14-04 biar td_reason tetep varchar
                            $builder->join('tbmst_disposereason', "CASE WHEN t_dispose.td_reason ~ '^[0-9]+$' THEN t_dispose.td_reason::integer ELSE NULL END = tbmst_disposereason.td_id", 'left', false);
                            // kode sebelumnya dihapus, perlu diganti dengan:
                            $builder->orderBy('td_lastupdate', 'DESC');
            $data = $builder->get()->getResultArray();
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching data from t_dispose: ' . $e->getMessage());
            return [];
        }
    }
                

    // Store disposal menyimpan data
    public function storeDisposal($data)
    {
        $session    = session();
        $user_info  = $session->get('user_info');
        $lastUser   = isset($user_info['upper_name']) ? $user_info['upper_name'] : 'Unknown';

        $insertData = [
            'td_assetno'      => !empty($data['assetNo']) ? $data['assetNo'] : null,
            'td_category'     => !empty($data['category']) ? $data['category'] : null,
            'td_reason'       => !empty($data['reason']) ? $data['reason'] : null,
            'td_decisiondate' => !empty($data['decisionDate']) ? $data['decisionDate'] : null,
            'td_disposedate'  => !empty($data['disposeDate'])  ? $data['disposeDate']  : null,
            'td_serialnumber' => !empty($data['serial_number']) ? $data['serial_number'] : null,
            'td_lastupdate'   => date("Y-m-d H:i:s"),
            'td_lastuser'     => $lastUser
        ];

        try {
            $this->insert($insertData);
            return ['status' => true, 'message' => 'Data berhasil disimpan.'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    // Fungsi untuk mengambil data berdasarkan ID
    public function getDataByid($id)
    {
        return $this->where('td_id', $id)->first();
    }

    // Fungsi untuk memperbarui data
    public function updateDisposal($data)
    {
        try {
            $this->update($data['id'], [
                'td_assetno'      => !empty($data['assetNo']) ? $data['assetNo'] : null,
                'td_category'     => !empty($data['category']) ? $data['category'] : null,
                'td_reason'       => !empty($data['reason']) ? $data['reason'] : null,
                'td_decisiondate' => !empty($data['decisionDate']) ? $data['decisionDate'] : null,
                'td_disposedate'  => !empty($data['disposeDate']) ? $data['disposeDate'] : null,
                'td_serialnumber' => !empty($data['serial_number']) ? $data['serial_number'] : null,
                'td_lastupdate'   => date("Y-m-d H:i:s"),
                'td_lastuser'     => session()->get('user_info')['upper_name'] ?? 'Unknown'
            ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error updating data: ' . $e->getMessage());
            return false;
        }
    }

    // Fungsi untuk menghapus data
    public function deleteData($id)
    {
        try {
            $this->delete($id);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error deleting data: ' . $e->getMessage());
            return false;
        }
    }

    // Di dalam TransDisposalModel.php
    public function getAssetNumbers()
    {
        try {
            return $this->db->table('tbtfa_equipmentacceptance')
                ->select('
                    ea_assetnumber AS asset_no, 
                    ea_id AS equipment_id, 
                    ea_machineno AS serial_number, 
                    ea_datereceived AS receive_date,
                    ea_productdescription AS model
                ')
                ->where('ea_assetnumber IS NOT NULL')
                ->orderBy('ea_assetnumber', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching asset numbers: ' . $e->getMessage());
            return [];
        }
    }

    public function checkDuplicate($assetNo, $serialNumber)
    {
        return $this->where('td_assetno', $assetNo)
                    ->orWhere('td_serialnumber', $serialNumber)
                    ->first();
    }

    public function checkDuplicateUpdate($assetNo, $serialNumber, $id)
    {
        return $this->where('td_id !=', $id)
                    ->groupStart()
                        ->where('td_assetno', $assetNo)
                        ->orWhere('td_serialnumber', $serialNumber)
                    ->groupEnd()
                    ->first();
    }

    public function isAssetFromModal($assetNo)
    {
        $builder = $this->db->table('tbtfa_equipmentacceptance');
        $builder->where('ea_assetnumber', $assetNo);
        return $builder->countAllResults() > 0;
    }
}
