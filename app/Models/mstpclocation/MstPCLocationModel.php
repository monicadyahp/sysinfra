<?php

namespace App\Models\MstPCLocation;

use CodeIgniter\Model;

class MstPCLocationModel extends Model
{
    protected $db_sysinfra;

    public function __construct()
    {
        $this->db_sysinfra = db_connect('jinsystem');
    }

    public function getData()
    {
        return $this->db_sysinfra->query("
            SELECT * FROM m_pclocation 
            WHERE mpl_status != 25 
            ORDER BY mpl_lastupdate DESC
        ")->getResult();
    }

    public function getLocationById($locationName)
    {
        return $this->db_sysinfra->table('m_pclocation')
            ->where('LOWER(mpl_name)', strtolower($locationName))
            ->get()
            ->getRowArray();
    }

    public function storeData($data)
    {
        $locationName = isset($data['locationName']) && $data['locationName'] !== '' ? trim($data['locationName']) : null;

        if (!$locationName) {
            return [
                'status' => false,
                'message' => 'Location name is required.'
            ];
        }

        // --- START PERBAIKAN ---
        // Ambil info user dari session, pastikan tidak null sebelum mengakses array offset
        $userInfo = session()->get('user_info');
        $lastUser = isset($userInfo['em_emplcode']) ? $userInfo['em_emplcode'] : null; // Menggunakan null jika tidak ada
        // Anda juga bisa memberikan nilai default seperti 0 atau 9999 (untuk user unknown)
        // $lastUser = isset($userInfo['em_emplcode']) ? $userInfo['em_emplcode'] : 0; 
        // --- END PERBAIKAN ---

        // Periksa apakah location dengan nama yang sama sudah ada (termasuk yang nonaktif)
        $existingLocation = $this->db_sysinfra->table('m_pclocation')
            ->where('LOWER(mpl_name)', strtolower($locationName))
            ->get()
            ->getRowArray();

        if ($existingLocation) {
            // Jika location sudah ada dan masih aktif
            if ($existingLocation['mpl_status'] == 1) {
                return [
                    'status' => false,
                    'message' => 'Location name is already active.'
                ];
            }
            
            // Jika location sudah ada tapi nonaktif (status 25), timpa dengan data baru
            if ($existingLocation['mpl_status'] == 25) {
                try {
                    $updateData = [
                        'mpl_name' => $locationName, // Timpa dengan data baru dari input
                        'mpl_status' => 1,
                        'mpl_lastupdate' => date("Y-m-d H:i:s"),
                        'mpl_lastuser' => $lastUser, // Menggunakan variabel $lastUser yang sudah divalidasi
                    ];

                    $this->db_sysinfra->table('m_pclocation')
                        ->where('LOWER(mpl_name)', strtolower($locationName))
                        ->update($updateData);
                        
                    return [
                        'status' => true,
                        'message' => 'Location has been successfully saved.'
                    ];
                } catch (\Exception $e) {
                    return [
                        'status' => false,
                        'message' => 'Error occurred while saving Location: ' . $e->getMessage()
                    ];
                }
            }
        }

        // Jika tidak ada location dengan nama yang sama, buat baru
        try {
            $insertData = [
                'mpl_name' => $locationName,
                'mpl_status' => 1,
                'mpl_lastupdate' => date("Y-m-d H:i:s"),
                'mpl_lastuser' => $lastUser, // Menggunakan variabel $lastUser yang sudah divalidasi
            ];

            $this->db_sysinfra->table('m_pclocation')->insert($insertData);
            return [
                'status' => true,
                'message' => 'Location has been successfully saved.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Error occurred while saving Location: ' . $e->getMessage()
            ];
        }
    }

    public function updateData($data)
    {
        $locationName = isset($data['locationName']) && $data['locationName'] !== '' ? trim($data['locationName']) : null;
        $oldLocationName = isset($data['oldLocationName']) && $data['oldLocationName'] !== '' ? trim($data['oldLocationName']) : null;

        if (!$locationName || !$oldLocationName) {
            return [
                'status' => false,
                'message' => 'Location name is required.'
            ];
        }

        // --- START PERBAIKAN ---
        $userInfo = session()->get('user_info');
        $lastUser = isset($userInfo['em_emplcode']) ? $userInfo['em_emplcode'] : null; // Menggunakan null jika tidak ada
        // --- END PERBAIKAN ---

        // Cek apakah location baru sudah ada (semua status, bukan hanya aktif)
        $existingLocation = $this->db_sysinfra->table('m_pclocation')
            ->where('LOWER(mpl_name)', strtolower($locationName))
            ->get()
            ->getRowArray();

        // Jika location baru sudah ada dan itu bukan location yang sedang diedit
        if ($existingLocation && strtolower($existingLocation['mpl_name']) !== strtolower($oldLocationName)) {
            if ($existingLocation['mpl_status'] == 1) {
                return [
                    'status' => false,
                    'message' => 'Location name already exists and is active.'
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Location name already exists (inactive). Please choose a different name.'
                ];
            }
        }

        // Cek location lama
        $oldLocation = $this->db_sysinfra->table('m_pclocation')
            ->where('LOWER(mpl_name)', strtolower($oldLocationName))
            ->get()
            ->getRowArray();

        if ($oldLocation) {
            try {
                $updateData = [
                    'mpl_name' => $locationName,
                    'mpl_lastupdate' => date('Y-m-d H:i:s'),
                    'mpl_lastuser' => $lastUser, // Menggunakan variabel $lastUser yang sudah divalidasi
                ];

                $this->db_sysinfra->table('m_pclocation')
                    ->where('LOWER(mpl_name)', strtolower($oldLocationName))
                    ->update($updateData);

                return [
                    'status'  => true,
                    'message' => 'Location has been successfully updated.'
                ];
            } catch (\Exception $e) {
                return [
                    'status'  => false,
                    'message' => 'Error occurred while updating Location: ' . $e->getMessage()
                ];
            }
        }

        return [
            'status'  => false,
            'message' => 'Error: Old location data not found.'
        ];
    }

    public function deleteData($locationName)
    {
        // --- START PERBAIKAN ---
        $userInfo = session()->get('user_info');
        $lastUser = isset($userInfo['em_emplcode']) ? $userInfo['em_emplcode'] : null; // Menggunakan null jika tidak ada
        // --- END PERBAIKAN ---

        try {
            $location = $this->db_sysinfra->table('m_pclocation')
                ->where('LOWER(mpl_name)', strtolower($locationName))
                ->where('mpl_status', 1) // Hanya cari yang aktif untuk dihapus
                ->get()
                ->getRowArray();

            if (!$location) {
                return [
                    'status' => false,
                    'message' => 'Active location not found.'
                ];
            }

            $this->db_sysinfra->table('m_pclocation')
                ->where('LOWER(mpl_name)', strtolower($locationName))
                ->where('mpl_status', 1)
                ->update([
                    'mpl_status'     => 25,
                    'mpl_lastupdate' => date("Y-m-d H:i:s"),
                    'mpl_lastuser'   => $lastUser, // Menggunakan variabel $lastUser yang sudah divalidasi
                ]);

            return [
                'status' => true,
                'message' => 'Location has been successfully deactivated.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Error occurred while deactivating Location: ' . $e->getMessage()
            ];
        }
    }
}