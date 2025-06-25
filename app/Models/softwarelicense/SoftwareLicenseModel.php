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
        'tl_license_type_category',
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
        'tl_status', // BARIS INI DITAMBAHKAN
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
                         ->where('ld_status', 1) // TAMBAHKAN KONDISI INI: Hanya tampilkan yang berstatus 1 (aktif)
                         ->get()
                         ->getResultArray();
    }

    /**
     * Menghitung jumlah PC berlisensi berdasarkan tl_id.
     * Hanya menghitung yang berstatus 1 (aktif).
     */
    public function countLicensedPcsByLicenseId(int $tl_id): int
    {
        return $this->db->table($this->licensedDetailTable)
                         ->where('tl_id', $tl_id)
                         ->where('ld_status', 1) // TAMBAHKAN KONDISI INI: Hanya hitung yang berstatus 1 (aktif)
                         ->countAllResults();
    }

    /**
     * Menambahkan PC baru ke t_licensedetail.
     * Default status adalah 1.
     */
    public function addLicensedPc(array $data)
    {
        $insertData = [
            'tl_id'            => $data['tl_id'],
            'ld_pcnama'        => $data['ld_pcnama'],
            'ld_assetno'       => $data['ld_assetno'],
            'ld_pc_id'         => $data['ld_pc_id'] ?? null,
            'ld_po_number'     => $data['ld_po_number'] ?? null,
            'ld_status'        => $data['ld_status'] ?? 1, // Pastikan ini diset 1 secara default jika tidak dikirim
            'ld_lastuser'      => $data['ld_lastuser'],
            'ld_lastupdate'    => $data['ld_lastupdate'],
            'ld_serialnumber'  => $data['ld_serialnumber'],
            'ld_employee_code' => $data['ld_employee_code'] ?? null,
            'ld_position_code' => $data['ld_position_code'] ?? null,
        ];

        $this->db->table($this->licensedDetailTable)->insert($insertData);
        return $this->db->insertID();
    }

    /**
     * Mengambil satu record PC berlisensi dari t_licensedetail.
     * Hanya mengambil yang berstatus 1 (aktif).
     */
    public function getLicensedPcById(int $ld_id)
    {
        return $this->db->table($this->licensedDetailTable)
                            ->select('ld_id, tl_id, ld_pcnama, ld_assetno, ld_pc_id, ld_po_number, ld_status, ld_lastuser, ld_lastupdate, ld_serialnumber, ld_employee_code, ld_position_code')
                            ->where($this->licensedDetailPrimaryKey, $ld_id)
                            ->where('ld_status', 1) // TAMBAHKAN KONDISI INI: Hanya ambil yang berstatus 1 (aktif)
                            ->get()
                            ->getRowArray();
    }

    /**
     * Mengupdate record PC berlisensi di t_licensedetail.
     */
    public function updateLicensedPc(int $ld_id, array $data)
    {
        $updateData = [
            'tl_id'            => $data['tl_id'],
            'ld_pcnama'        => $data['ld_pcnama'],
            'ld_assetno'       => $data['ld_assetno'],
            'ld_pc_id'         => $data['ld_pc_id'] ?? null,
            'ld_po_number'     => $data['ld_po_number'] ?? null,
            'ld_status'        => $data['ld_status'] ?? 1, // Pastikan ini diset 1 secara default jika tidak dikirim
            'ld_lastuser'      => $data['ld_lastuser'],
            'ld_lastupdate'    => $data['ld_lastupdate'],
            'ld_serialnumber'  => $data['ld_serialnumber'],
            'ld_employee_code' => $data['ld_employee_code'] ?? null,
            'ld_position_code' => $data['ld_position_code'] ?? null,
        ];
        
        return $this->db->table($this->licensedDetailTable)
                         ->where($this->licensedDetailPrimaryKey, $ld_id)
                         ->update($updateData);
    }

    /**
     * Melakukan soft delete (mengubah status menjadi 25) record PC berlisensi dari t_licensedetail.
     * BUKAN MENGHAPUS FISIK.
     */
    public function softDeleteLicensedPc(int $ld_id) // UBAH NAMA METHOD DARI deleteLicensedPc
    {
        $updateData = [
            'ld_status'     => 25, // Set status menjadi 25 (tidak muncul)
            'ld_lastuser'   => session()->get('user_id') ?? 1, // Update last user
            'ld_lastupdate' => \CodeIgniter\I18n\Time::now()->toDateTimeString(), // Update last update
        ];
        return $this->db->table($this->licensedDetailTable)
                         ->where($this->licensedDetailPrimaryKey, $ld_id)
                         ->update($updateData);
    }

    /**
     * Melakukan soft delete (mengubah status menjadi 25) semua record PC berlisensi dari t_licensedetail berdasarkan tl_id.
     * BUKAN MENGHAPUS FISIK.
     */
    public function softDeleteLicensedPcsByLicenseId(int $tl_id) // UBAH NAMA METHOD DARI deleteLicensedPcsByLicenseId
    {
        $updateData = [
            'ld_status'     => 25, // Set status menjadi 25 (tidak muncul)
            'ld_lastuser'   => session()->get('user_id') ?? 1, // Update last user
            'ld_lastupdate' => \CodeIgniter\I18n\Time::now()->toDateTimeString(), // Update last update
        ];
        return $this->db->table($this->licensedDetailTable)
                         ->where('tl_id', $tl_id)
                         ->update($updateData);
    }

    /**
     * Menimpa metode delete bawaan Model CodeIgniter untuk melakukan soft delete.
     * Mengubah status tl_status menjadi 25.
     */
    public function delete($id = null, bool $purge = false)
    {
        if ($id === null) {
            return false;
        }

        $updateData = [
            'tl_status'      => 25, // Set status menjadi 25 (tidak muncul)
            'tl_last_user'   => session()->get('user_id') ?? 1, // Update last user
            'tl_last_update' => \CodeIgniter\I18n\Time::now(), // Update last update
        ];

        return $this->update($id, $updateData); // Panggil metode update() dari base model
    }


    /**
     * Mengecek duplikasi asset_no di t_licensedetail untuk tl_id tertentu.
     * Hanya memeriksa yang berstatus 1 (aktif).
     */
    public function checkDuplicateLicensedPcAssetNo(int $tl_id, string $asset_no, ?int $ld_id = null)
    {
        $builder = $this->db->table($this->licensedDetailTable)
                             ->where('tl_id', $tl_id)
                             ->where('ld_assetno', $asset_no)
                             ->where('ld_status', 1); // TAMBAHKAN KONDISI INI: Hanya cek duplikasi pada data aktif

        if ($ld_id !== null) {
            $builder->where($this->licensedDetailPrimaryKey . ' !=', $ld_id);
        }

        return (bool)$builder->countAllResults();
    }

    /**
     * Memeriksa apakah data PC di t_licensedetail cocok persis dengan entri di m_itequipment
     * berdasarkan Asset Number, Asset Name, Serial Number, dan Asset ID.
     * (Tidak ada perubahan di sini terkait status).
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