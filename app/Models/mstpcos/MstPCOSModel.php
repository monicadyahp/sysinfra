<?php

namespace App\Models\MstPCOS;

use CodeIgniter\Model;

class MstPCOSModel extends Model
{
    protected $db_sysinfra;

    public function __construct()
    {
        $this->db_sysinfra = db_connect('db_sysinfra');
    }

    public function getData()
    {
        return $this->db_sysinfra->query("
            SELECT *
            FROM m_pcos 
            WHERE mpo_status != 25 
            ORDER BY mpo_lastupdate DESC
        ")->getResult();
    }

    public function getOSById($osName)
    {
        return $this->db_sysinfra->table('m_pcos')
            ->where('LOWER(mpo_osname)', strtolower($osName))
            ->get()
            ->getRowArray();
    }

    public function storeData($data)
    {
        $osName = isset($data['osName']) && $data['osName'] !== '' ? trim($data['osName']) : null;

        if (!$osName) {
            return [
                'status' => false,
                'message' => 'OS name is required.'
            ];
        }

        // Periksa apakah OS dengan nama yang sama sudah ada (termasuk yang nonaktif)
        $existingOS = $this->db_sysinfra->table('m_pcos')
            ->where('LOWER(mpo_osname)', strtolower($osName))
            ->get()
            ->getRowArray();

        if ($existingOS) {
            // Jika OS sudah ada dan masih aktif
            if ($existingOS['mpo_status'] == 1) {
                return [
                    'status' => false,
                    'message' => 'OS name is already active.'
                ];
            }
            
            // Jika OS sudah ada tapi nonaktif (status 25), timpa dengan data baru
            if ($existingOS['mpo_status'] == 25) {
                try {
                    $updateData = [
                        'mpo_osname' => $osName, // Timpa dengan data baru dari input
                        'mpo_status' => 1,
                        'mpo_lastupdate' => date("Y-m-d H:i:s"),
                        'mpo_lastuser' => session()->get('user_info')['em_emplcode'],
                    ];

                    $this->db_sysinfra->table('m_pcos')
                        ->where('LOWER(mpo_osname)', strtolower($osName))
                        ->update($updateData);
                        
                    return [
                        'status' => true,
                        'message' => 'OS has been successfully saved.'
                    ];
                } catch (\Exception $e) {
                    return [
                        'status' => false,
                        'message' => 'Error occurred while saving OS: ' . $e->getMessage()
                    ];
                }
            }
        }

        // Jika tidak ada OS dengan nama yang sama, buat baru
        try {
            $insertData = [
                'mpo_osname'    => $osName,
                'mpo_status'    => 1,
                'mpo_lastupdate' => date("Y-m-d H:i:s"),
                'mpo_lastuser'   => session()->get('user_info')['em_emplcode'],
            ];

            $this->db_sysinfra->table('m_pcos')->insert($insertData);
            return [
                'status'  => true,
                'message' => 'OS has been successfully saved.'
            ];
        } catch (\Exception $e) {
            return [
                'status'  => false,
                'message' => 'Error occurred while saving OS: ' . $e->getMessage()
            ];
        }
    }

    public function updateData($data)
    {
        $osName = isset($data['osName']) && $data['osName'] !== '' ? trim($data['osName']) : null;
        $oldOSName = isset($data['oldOSName']) && $data['oldOSName'] !== '' ? trim($data['oldOSName']) : null;

        if (!$osName || !$oldOSName) {
            return [
                'status' => false,
                'message' => 'OS name is required.'
            ];
        }

        // Cek apakah OS baru sudah ada (semua status, bukan hanya aktif)
        $existingOS = $this->db_sysinfra->table('m_pcos')
            ->where('LOWER(mpo_osname)', strtolower($osName))
            ->get()
            ->getRowArray();

        // Jika OS baru sudah ada dan itu bukan OS yang sedang diedit
        if ($existingOS && strtolower($existingOS['mpo_osname']) !== strtolower($oldOSName)) {
            if ($existingOS['mpo_status'] == 1) {
                return [
                    'status' => false,
                    'message' => 'OS name already exists and is active.'
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'OS name already exists (inactive). Please choose a different name.'
                ];
            }
        }

        // Cek OS lama
        $oldOS = $this->db_sysinfra->table('m_pcos')
            ->where('LOWER(mpo_osname)', strtolower($oldOSName))
            ->get()
            ->getRowArray();

        if ($oldOS) {
            try {
                $updateData = [
                    'mpo_osname'    => $osName,
                    'mpo_lastupdate' => date('Y-m-d H:i:s'),
                    'mpo_lastuser'   => session()->get('user_info')['em_emplcode'],
                ];

                $this->db_sysinfra->table('m_pcos')
                    ->where('LOWER(mpo_osname)', strtolower($oldOSName))
                    ->update($updateData);
                    
                return [
                    'status'  => true,
                    'message' => 'OS has been successfully updated.'
                ];
            } catch (\Exception $e) {
                return [
                    'status'  => false,
                    'message' => 'Error occurred while updating OS: ' . $e->getMessage()
                ];
            }
        }

        return [
            'status'  => false,
            'message' => 'Error: Old OS data not found.'
        ];
    }

    public function deleteData($osName)
    {
        try {
            $os = $this->db_sysinfra->table('m_pcos')
                ->where('LOWER(mpo_osname)', strtolower($osName))
                ->where('mpo_status', 1) // Hanya cari yang aktif untuk dihapus
                ->get()
                ->getRowArray();

            if (!$os) {
                return [
                    'status' => false,
                    'message' => 'Active OS not found.'
                ];
            }

            $this->db_sysinfra->table('m_pcos')
                ->where('LOWER(mpo_osname)', strtolower($osName))
                ->where('mpo_status', 1)
                ->update([
                    'mpo_status'     => 25,
                    'mpo_lastupdate' => date("Y-m-d H:i:s"),
                    'mpo_lastuser'   => session()->get('user_info')['em_emplcode'],
                ]);
                
            return [
                'status' => true,
                'message' => 'OS has been successfully deactivated.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Error occurred while deactivating OS: ' . $e->getMessage()
            ];
        }
    }
}