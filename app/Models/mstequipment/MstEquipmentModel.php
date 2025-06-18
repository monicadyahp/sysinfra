<?php

namespace App\Models\mstequipment;

use CodeIgniter\Model;

class MstEquipmentModel extends Model
{
    protected $db_sysinfra;

    public function __construct()
    {
        // Gunakan koneksi ke database "jinsystem" karena tabel-tabel system (misalnya m_equipmentcat) berada di sini.
        $this->db_sysinfra = db_connect('jinsystem');
    }

    //tambahan monic
    public function getDataEquipment()
    {
        return $this->db_sysinfra
            ->query("SELECT * 
                    FROM m_itequipment 
                    ORDER BY e_lastupdate DESC")
            ->getResult();
    }

    public function getDataCat()
    {
        return $this->db_sysinfra->query("select * from m_equipmentcat")->getResult();
    }

    public function getDataChart()
    {
        return $this->db_sysinfra->query(
            "SELECT e_status, COUNT(*) AS total FROM m_itequipment GROUP BY e_status ORDER BY e_status"
        )->getResultArray();
    }

    public function getDataChartDt($status = null)
    {
        return $this->db_sysinfra->query(
            "select e_kind, count(*) as total from m_itequipment mi where e_status = '$status' GROUP BY e_kind ORDER BY e_kind"
        )->getResultArray();
    }

    //tambahan monic
    public function storeMaster($data)
    {
        // Periksa apakah Asset No. atau Serial No. sudah ada
        if ($this->isDuplicate($data['assetNo'], $data['serial_number'])) {
            return ['status' => false, 'message' => 'Asset No. atau Serial No. sudah terdaftar!'];
        }

        $insertData = [
            'e_assetno' => !empty($data['assetNo']) ? $data['assetNo'] : null,
            'e_equipmentid' => !empty($data['equipmentId']) ? $data['equipmentId'] : null,
            'e_kind' => !empty($data['kind']) ? $data['kind'] : null,
            'e_brand' => !empty($data['brand']) ? $data['brand'] : null,
            'e_model' => !empty($data['model']) ? $data['model'] : null,
            'e_equipmentname' => !empty($data['equipmentName']) ? $data['equipmentName'] : null,
            'e_serialnumber' => !empty($data['serial_number']) ? $data['serial_number'] : null,
            'e_receivedate' => !empty($data['receiveDate']) ? $data['receiveDate'] : null,
            'e_status' => !empty($data['status']) ? $data['status'] : null,
            'e_disposedate' => !empty($data['disposeDate']) ? $data['disposeDate'] : null,
            'e_lastupdate' => date("Y-m-d H:i:s"),
            'e_lastuser' => session()->has('user_info') ? session()->get('user_info')['upper_name'] : 'Unknown'
        ];

        try {
            $this->db_sysinfra->table('m_itequipment')->insert($insertData);
            return ['status' => true, 'message' => 'Data berhasil disimpan.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'value too long for type') !== false) {
                return ['status' => false, 'message' => 'Data terlalu panjang untuk beberapa kolom. Pastikan data sesuai dengan batasan kolom.'];
            }
            return ['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
        
    }

    public function getDataByid($id)
    {
        $data = $this->db_sysinfra->table('m_itequipment')
            ->where('e_id', $id)
            ->get()
            ->getRowArray();

        if ($data && isset($data['e_receivedate'])) {
            $data['e_receivedate'] = date('Y-m-d', strtotime($data['e_receivedate']));
        }

        return $data;
    }

    public function updateMaster($data)
    {
        $builder = $this->db_sysinfra->table('m_itequipment');

        try {
            $this->db_sysinfra->transStart(); // Mulai transaksi

            // Cek duplikasi sebelum update
            if ($this->isDuplicateOnUpdate($data['id'], $data['assetNo'], $data['serial_number'])) {
                return ['status' => false, 'message' => 'Asset No. atau Serial No. sudah digunakan!'];
            }

            $updateData = [
                'e_assetno' => $data['assetNo'] ?? null,
                'e_equipmentid' => $data['equipmentId'] ?? null,
                'e_kind' => $data['kind'] ?? null,
                'e_brand' => $data['brand'] ?? null,
                'e_model' => $data['model'] ?? null,
                'e_equipmentname' => $data['equipmentName'] ?? null,
                'e_serialnumber' => $data['serial_number'] ?? null,
                'e_receivedate' => $data['receiveDate'] ?? null,
                'e_status' => $data['status'] ?? null,
                'e_disposedate' => $data['disposeDate'] ?? null,
                'e_lastupdate' => date("Y-m-d H:i:s"),
                'e_lastuser' => session()->has('user_info') ? session()->get('user_info')['upper_name'] : 'Unknown'
            ];

            $builder->where('e_id', $data['id'])->update($updateData);

            $this->db_sysinfra->transComplete(); // Selesai transaksi

            if ($this->db_sysinfra->transStatus() === false) {
                throw new \Exception('Update gagal!');
            }

            return ['status' => true, 'message' => 'Data berhasil diperbarui.'];
        } catch (\Exception $e) {
            log_message('error', 'Error updating data: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    //tambahan monic
    public function deleteData($id)
    {
        try {
            $builder = $this->db_sysinfra->table('m_itequipment');
            $builder->where('e_id', $id)->delete();
    
            return $this->db_sysinfra->affectedRows() > 0; // True jika ada data yang terhapus
        } catch (\Exception $e) {
            log_message('error', 'Error menghapus data: ' . $e->getMessage());
            return false;
        }
    }
    
    //tambahan monic
    public function isDuplicate($assetNo, $serialNumber)
    {
        $query = $this->db_sysinfra->table('m_itequipment')
            ->where('e_assetno', $assetNo)
            ->orWhere('e_serialnumber', $serialNumber)
            ->countAllResults();

        return $query > 0; // Jika lebih dari 0, berarti duplikasi ditemukan
    }

    //tambahan monic
    public function checkAssetInAcceptance($assetNo)
    {
        return $this->db_sysinfra->table('tbtfa_equipmentacceptance')
            ->select('ea_assetnumber, ea_model, ea_datereceived, ea_mfgno, ea_id')
            ->where('ea_assetnumber', $assetNo)
            ->get()
            ->getRowArray();
    }

    // tambahan monic
    public function isAssetAccepted($assetNo)
    {
        return $this->db_sysinfra->table('tbtfa_equipmentacceptance')
            ->where('ea_assetnumber', $assetNo)
            ->countAllResults() > 0;
    }

    // tambahan monic
    public function isDuplicateOnUpdate($id, $assetNo, $serialNumber)
    {
        return $this->db_sysinfra->table('m_itequipment')
            ->where('e_id !=', $id) // Pastikan tidak mengecek ID yang sama
            ->groupStart()
                ->where('e_assetno', $assetNo)
                ->orWhere('e_serialnumber', $serialNumber)
            ->groupEnd()
            ->countAllResults() > 0;
    }

    public function getAssetNumbers()
    {
        try {
            // Mapping the columns according to your requirements
            $query = $this->db_sysinfra->table('tbtfa_equipmentacceptance')
                ->select('
                    ea_assetnumber as asset_no, 
                    ea_id as equipment_id, 
                    ea_machineno as serial_number, 
                    ea_datereceived as receive_date,
                    ea_productdescription as model
                ')
                ->where('ea_assetnumber IS NOT NULL')
                ->orderBy('ea_assetnumber', 'DESC');
                    
            $result = $query->get()->getResultArray();
            
            // Add debug log
            log_message('info', 'Asset numbers query result count: ' . count($result));
            
            // Handle case where result is empty
            if (empty($result)) {
                log_message('info', 'No asset numbers found in tbtfa_equipmentacceptance');
                return [];
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching asset numbers: ' . $e->getMessage());
            return [];
        }
    }

}