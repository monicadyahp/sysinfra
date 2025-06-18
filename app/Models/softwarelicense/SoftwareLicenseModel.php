<?php

namespace App\Models\softwarelicense;

use CodeIgniter\Model;

class SoftwareLicenseModel extends Model
{
    // Properti untuk tabel t_license
    protected $table             = 't_license';
    protected $primaryKey        = 'tl_id';
    protected $useAutoIncrement = true;
    protected $allowedFields     = [
        'tl_id',
        'tl_type',
        'tl_license_type_category', // Add this line
        'tl_refnumber',
        'tl_po_number',
        'tl_licensepartner',
        'tl_orderdate',
        'tl_startdate',
        'tl_enddate',
        'tl_productname',
        'tl_productqty',
        'tl_productdesc',
        'tl_productkey',
        'tl_organization',
        'tl_last_update',
        'tl_last_user',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'tl_last_update';
    protected $updatedField  = 'tl_last_update';
    protected $deletedField  = null; // Set to null to prevent CodeIgniter from looking for 'deleted_at'

    // Tambahkan properti untuk interaksi dengan tabel t_licensedetail
    protected $licensedDetailTable = 't_licensedetail';
    protected $licensedDetailPrimaryKey = 'ld_id';

    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect('jinsystem');
    }

    /**
     * Mengambil daftar PC berlisensi berdasarkan tl_id.
     * Menggunakan Query Builder secara eksplisit untuk tabel t_licensedetail.
     */
    public function getLicensedPcsByLicenseId(int $tl_id)
    {
        return $this->db->table($this->licensedDetailTable)
                        ->where('tl_id', $tl_id)
                        ->get()
                        ->getResultArray();
    }

    /**
     * Menghitung jumlah PC berlisensi berdasarkan tl_id.
     */
    public function countLicensedPcsByLicenseId(int $tl_id): int
    {
        return $this->db->table($this->licensedDetailTable)
                        ->where('tl_id', $tl_id)
                        ->countAllResults();
    }

    /**
     * Menambahkan PC baru ke t_licensedetail.
     */
    public function addLicensedPc(array $data)
    {
        $insertData = [
            'tl_id'           => $data['tl_id'],
            'ld_pcnama'       => $data['ld_pcnama'],
            'ld_assetno'      => $data['ld_assetno'],
            'ld_pc_id'        => $data['ld_pc_id'] ?? null, // Pastikan ini null jika tidak ada e_id
            'ld_po_number'    => $data['ld_po_number'] ?? null, // Perubahan: Include ld_po_number
            'ld_status'       => $data['ld_status'] ?? 1, // Perubahan: Include ld_status
            'ld_lastuser'     => $data['ld_lastuser'],
            'ld_lastupdate'   => $data['ld_lastupdate'],
            'ld_serialnumber' => $data['ld_serialnumber'],
            // DUA BARIS INI UNTUK MENYIMPAN DATA EMPLOYEE KE t_licensedetail
            'ld_employee_code' => $data['ld_employee_code'] ?? null,
            'ld_position_code' => $data['ld_position_code'] ?? null,            
        ];

        $this->db->table($this->licensedDetailTable)->insert($insertData);
        return $this->db->insertID();
    }

    /**
     * Mengambil satu record PC berlisensi dari t_licensedetail.
     */
    public function getLicensedPcById(int $ld_id)
    {
        return $this->db->table($this->licensedDetailTable)
                        ->select('ld_id, tl_id, ld_pcnama, ld_assetno, ld_pc_id, ld_po_number, ld_status, ld_lastuser, ld_lastupdate, ld_serialnumber, ld_employee_code, ld_position_code') // Tambah ini
                        ->where($this->licensedDetailPrimaryKey, $ld_id)
                        ->get()
                        ->getRowArray();
    }

    /**
     * Mengupdate record PC berlisensi di t_licensedetail.
     */
    public function updateLicensedPc(int $ld_id, array $data)
    {
        $updateData = [
            'tl_id'           => $data['tl_id'],
            'ld_pcnama'       => $data['ld_pcnama'],
            'ld_assetno'      => $data['ld_assetno'],
            'ld_pc_id'        => $data['ld_pc_id'] ?? null, // Pastikan ini null jika tidak ada e_id
            'ld_po_number'    => $data['ld_po_number'] ?? null, // Perubahan: Include ld_po_number
            'ld_status'       => $data['ld_status'] ?? 1, // Perubahan: Include ld_status
            'ld_lastuser'     => $data['ld_lastuser'],
            'ld_lastupdate'   => $data['ld_lastupdate'],
            'ld_serialnumber' => $data['ld_serialnumber'],
            // DUA BARIS INI UNTUK MENYIMPAN DATA EMPLOYEE KE t_licensedetail
            'ld_employee_code' => $data['ld_employee_code'] ?? null,
            'ld_position_code' => $data['ld_position_code'] ?? null,            
        ];
        
        return $this->db->table($this->licensedDetailTable)
                        ->where($this->licensedDetailPrimaryKey, $ld_id)
                        ->update($updateData);
    }

    /**
     * Menghapus record PC berlisensi dari t_licensedetail.
     */
    public function deleteLicensedPc(int $ld_id)
    {
        return $this->db->table($this->licensedDetailTable)
                        ->where($this->licensedDetailPrimaryKey, $ld_id)
                        ->delete();
    }

    /**
     * Menghapus semua record PC berlisensi dari t_licensedetail berdasarkan tl_id.
     */
    public function deleteLicensedPcsByLicenseId(int $tl_id)
    {
        return $this->db->table($this->licensedDetailTable)
                        ->where('tl_id', $tl_id)
                        ->delete();
    }

    /**
     * Mengecek duplikasi asset_no di t_licensedetail untuk tl_id tertentu.
     */
    public function checkDuplicateLicensedPcAssetNo(int $tl_id, string $asset_no, ?int $ld_id = null)
    {
        $builder = $this->db->table($this->licensedDetailTable)
                            ->where('tl_id', $tl_id)
                            ->where('ld_assetno', $asset_no);

        if ($ld_id !== null) {
            $builder->where($this->licensedDetailPrimaryKey . ' !=', $ld_id);
        }

        return (bool)$builder->countAllResults();
    }

    /**
     * Memeriksa apakah data PC di t_licensedetail cocok persis dengan entri di m_itequipment
     * berdasarkan Asset Number, Asset Name, Serial Number, dan Asset ID.
     */
    public function isLicensedPcFromFinder(string $ldAssetNo, string $ldPcNama, string $ldSerialNumber, $ldPcId): bool
    {
        $builder = $this->db->table('m_itequipment');

        // Normalisasi input untuk perbandingan yang konsisten
        $ldAssetNo = strtoupper(trim($ldAssetNo));
        $ldPcNama = strtoupper(trim($ldPcNama));
        $ldSerialNumber = strtoupper(trim($ldSerialNumber));
        
        // Convert $ldPcId to null if it's empty string, 0, or already null
        $ldPcId = ($ldPcId === null || $ldPcId === '' || $ldPcId === 0) ? null : (int)$ldPcId;

        $builder->where('UPPER(TRIM(e_assetno))', $ldAssetNo);
        $builder->where('UPPER(TRIM(e_equipmentname))', $ldPcNama);
        $builder->where('UPPER(TRIM(e_serialnumber))', $ldSerialNumber);

        // Kritis: Kondisi untuk e_equipmentid
        if ($ldPcId !== null) {
            // Jika ld_pc_id dari t_licensedetail memiliki nilai (bukan null/kosong/0),
            // maka harus cocok dengan e_equipmentid di m_itequipment.
            $builder->where('e_equipmentid', $ldPcId);
        } else {
            // Jika ld_pc_id dari t_licensedetail adalah null/kosong/0,
            // maka di m_itequipment, e_equipmentid juga harus null/kosong/0 untuk dianggap "cocok".
            // Ini untuk menangani kasus data manual yang tidak memiliki e_equipmentid dari m_itequipment.
            $builder->groupStart()
                        ->where('e_equipmentid IS NULL')
                        ->orWhere('e_equipmentid', 0) // Tambahkan ini jika 0 adalah representasi NULL di DB Anda
                        ->groupEnd();
        }
        
        return (bool)$builder->countAllResults() > 0;
    }
}