<?php

namespace App\Models\master_assy;

use CodeIgniter\Model;

date_default_timezone_set('Asia/Jakarta');
$currentDateTime = date('Y-m-d H:i:s');

class M_MstMachineAssy extends Model
{
    // Declare a class property for currentDateTime
    protected $currentDateTime;

    // Constructor to initialize currentDateTime
    public function __construct()
    {
        parent::__construct();  // Call the parent constructor if needed

        // Set the time zone to Indonesian WIB and initialize currentDateTime
        date_default_timezone_set('Asia/Jakarta');
        $this->currentDateTime = date('Y-m-d H:i:s');
    }

    public function get_data()
    {
        $query = "select * from m_mchassy_setting WHERE mas_status = 1
                    ORDER BY mas_id DESC";

        return $this->db->query($query)->getResult();
    }

    public function getMachines()
    {
        $query = "select mcode, mseries, mname from m_machine where mlocation in ('handmade', 'clean room', 'Auto 1', 'Auto', 'Auto 2')";

        return $this->db->query($query)->getResultArray();
    }

    public function cek_data($code, $pole, $spm, $stroke, $qty)
    {
        // var_dump($code, $pole);die();
        // Query to check if the document exists
        $query = "select * from m_mchassy_setting WHERE mas_mchid = ? and mas_pole = ? ";
        // Executing the raw SQL query with a parameter binding
        $result = $this->db->query($query, [$code, $pole])->getRow();
        // Return true if any row is found, false otherwise
        return ($result !== null);
    }

    public function update_data($emplcode, $POST)
    {
        // var_dump($POST);die();
        $code   = $POST["code"];
        $pole    = $POST["pole"];
        $spm    = $POST["spm"];
        $stroke    = $POST["stroke"];
        $qty    = $POST["qty"];
        $tipe    = $POST["tipe"];

        $q = "select mcode, mseries, mname 
            from m_machine 
            where mlocation in ('handmade', 'clean room', 'Auto 1', 'Auto', 'Auto 2') 
            and mcode = ?";
        $result = $this->db->query($q, [$code])->getRow();

        // var_dump($result->mname);die();
        if ($tipe == "add") {
            $query = "INSERT INTO m_mchassy_setting (mas_mchcode, mas_pole, mas_spm, mas_pcs_stroke, mas_extqty, mas_lastuser, mas_lastupdate, mas_status, mas_mchid)
             VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)";

            $params = [$result->mname, $pole, $spm, $stroke, $qty, $emplcode, $this->currentDateTime, $code];
        } else if ($tipe == "edit") {
            $id = $POST["id"];
            // var_dump($group, $desc, $tipe, $this->currentDateTime, $emplcode, $id);die();

            $query = "  UPDATE m_mchassy_setting
                        SET mas_spm = ?, 
                            mas_pcs_stroke = ?,
                            mas_extqty = ?,
                            mas_lastuser = ?,
                            mas_lastupdate = ?
                        WHERE mas_id = ?";

            $params = [$spm, $stroke, $qty, $emplcode, $this->currentDateTime, $id];
        }

        $result = $this->db->query($query, $params);
        // var_dump($this->db->error());
        return $result;
    }

    public function update_status_data($id, $new_status, $emplcode)
    {
        $query = "  UPDATE m_mchassy_setting
                    SET mas_status      = ?,
                        mas_lastuser    = ?, 
                        mas_lastupdate  = ?
                    WHERE mas_id        = ?";

        $params = [$new_status, $emplcode, $this->currentDateTime, $id];

        $this->db->query($query, $params);

        return $this->db->affectedRows() > 0;
    }
}
