<?php

namespace App\Models\MstPrinterLocation; // Sesuaikan namespace

use CodeIgniter\Model;

class MstPrinterLocationModel extends Model // Ubah nama kelas
{
    protected $db_sysinfra; // Sesuaikan nama koneksi database jika berbeda

    public function __construct()
    {
        // Pastikan 'jinsystem' adalah nama grup koneksi database yang benar untuk tabel m_printerlocation
        $this->db_sysinfra = db_connect('jinsystem'); 
    }

    public function getData()
    {
        return $this->db_sysinfra->query("
            SELECT * FROM m_printerlocation 
            WHERE mploc_status != 25 
            ORDER BY mploc_lastupdate DESC
        ")->getResult();
    }

    public function getPrinterLocationById($printerLocationName) // Ubah nama fungsi dan parameter
    {
        return $this->db_sysinfra->table('m_printerlocation')
            ->where('LOWER(mploc_name)', strtolower($printerLocationName)) // Ubah nama kolom
            ->get()
            ->getRowArray();
    }

    public function storeData($data)
    {
        $printerLocationName = isset($data['printerLocationName']) && $data['printerLocationName'] !== '' ? trim($data['printerLocationName']) : null; // Ubah nama variabel

        if (!$printerLocationName) {
            return [
                'status' => false,
                'message' => 'Printer Location name is required.'
            ];
        }

        $userInfo = session()->get('user_info');
        $lastUser = isset($userInfo['em_emplcode']) ? $userInfo['em_emplcode'] : null;

        $existingLocation = $this->db_sysinfra->table('m_printerlocation')
            ->where('LOWER(mploc_name)', strtolower($printerLocationName)) // Ubah nama kolom
            ->get()
            ->getRowArray();

        if ($existingLocation) {
            if ($existingLocation['mploc_status'] == 1) { // Ubah nama kolom
                return [
                    'status' => false,
                    'message' => 'Printer Location name is already active.'
                ];
            }
            
            if ($existingLocation['mploc_status'] == 25) { // Ubah nama kolom
                try {
                    $updateData = [
                        'mploc_name' => $printerLocationName, // Ubah nama kolom
                        'mploc_status' => 1, // Ubah nama kolom
                        'mploc_lastupdate' => date("Y-m-d H:i:s"), // Ubah nama kolom
                        'mploc_lastuser' => $lastUser, // Ubah nama kolom
                    ];

                    $this->db_sysinfra->table('m_printerlocation')
                        ->where('LOWER(mploc_name)', strtolower($printerLocationName)) // Ubah nama kolom
                        ->update($updateData);
                        
                    return [
                        'status' => true,
                        'message' => 'Printer Location has been successfully saved.'
                    ];
                } catch (\Exception $e) {
                    return [
                        'status' => false,
                        'message' => 'Error occurred while saving Printer Location: ' . $e->getMessage()
                    ];
                }
            }
        }

        try {
            $insertData = [
                'mploc_name' => $printerLocationName, // Ubah nama kolom
                'mploc_status' => 1, // Ubah nama kolom
                'mploc_lastupdate' => date("Y-m-d H:i:s"), // Ubah nama kolom
                'mploc_lastuser' => $lastUser, // Ubah nama kolom
            ];

            $this->db_sysinfra->table('m_printerlocation')->insert($insertData);
            return [
                'status' => true,
                'message' => 'Printer Location has been successfully saved.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Error occurred while saving Printer Location: ' . $e->getMessage()
            ];
        }
    }

    public function updateData($data)
    {
        $printerLocationName = isset($data['printerLocationName']) && $data['printerLocationName'] !== '' ? trim($data['printerLocationName']) : null; // Ubah nama variabel
        $oldPrinterLocationName = isset($data['oldPrinterLocationName']) && $data['oldPrinterLocationName'] !== '' ? trim($data['oldPrinterLocationName']) : null; // Ubah nama variabel

        if (!$printerLocationName || !$oldPrinterLocationName) {
            return [
                'status' => false,
                'message' => 'Printer Location name is required.'
            ];
        }

        $userInfo = session()->get('user_info');
        $lastUser = isset($userInfo['em_emplcode']) ? $userInfo['em_emplcode'] : null;

        $existingLocation = $this->db_sysinfra->table('m_printerlocation')
            ->where('LOWER(mploc_name)', strtolower($printerLocationName)) // Ubah nama kolom
            ->get()
            ->getRowArray();

        if ($existingLocation && strtolower($existingLocation['mploc_name']) !== strtolower($oldPrinterLocationName)) { // Ubah nama kolom
            if ($existingLocation['mploc_status'] == 1) { // Ubah nama kolom
                return [
                    'status' => false,
                    'message' => 'Printer Location name already exists and is active.'
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Printer Location name already exists (inactive). Please choose a different name.'
                ];
            }
        }

        $oldLocation = $this->db_sysinfra->table('m_printerlocation')
            ->where('LOWER(mploc_name)', strtolower($oldPrinterLocationName)) // Ubah nama kolom
            ->get()
            ->getRowArray();

        if ($oldLocation) {
            try {
                $updateData = [
                    'mploc_name' => $printerLocationName, // Ubah nama kolom
                    'mploc_lastupdate' => date('Y-m-d H:i:s'), // Ubah nama kolom
                    'mploc_lastuser' => $lastUser, // Ubah nama kolom
                ];

                $this->db_sysinfra->table('m_printerlocation')
                    ->where('LOWER(mploc_name)', strtolower($oldPrinterLocationName)) // Ubah nama kolom
                    ->update($updateData);

                return [
                    'status'  => true,
                    'message' => 'Printer Location has been successfully updated.'
                ];
            } catch (\Exception $e) {
                return [
                    'status'  => false,
                    'message' => 'Error occurred while updating Printer Location: ' . $e->getMessage()
                ];
            }
        }

        return [
            'status'  => false,
            'message' => 'Error: Old printer location data not found.'
        ];
    }

    public function deleteData($printerLocationName) // Ubah nama fungsi dan parameter
    {
        $userInfo = session()->get('user_info');
        $lastUser = isset($userInfo['em_emplcode']) ? $userInfo['em_emplcode'] : null;

        try {
            $location = $this->db_sysinfra->table('m_printerlocation')
                ->where('LOWER(mploc_name)', strtolower($printerLocationName)) // Ubah nama kolom
                ->where('mploc_status', 1) // Ubah nama kolom
                ->get()
                ->getRowArray();

            if (!$location) {
                return [
                    'status' => false,
                    'message' => 'Active printer location not found.'
                ];
            }

            $this->db_sysinfra->table('m_printerlocation')
                ->where('LOWER(mploc_name)', strtolower($printerLocationName)) // Ubah nama kolom
                ->where('mploc_status', 1) // Ubah nama kolom
                ->update([
                    'mploc_status'  => 25, // Ubah nama kolom
                    'mploc_lastupdate' => date("Y-m-d H:i:s"), // Ubah nama kolom
                    'mploc_lastuser'  => $lastUser, // Ubah nama kolom
                ]);

            return [
                'status' => true,
                'message' => 'Printer Location has been successfully deactivated.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Error occurred while deactivating Printer Location: ' . $e->getMessage()
            ];
        }
    }
}
