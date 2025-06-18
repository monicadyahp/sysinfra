<?php

namespace App\Models\master_assy;

use CodeIgniter\Model;

date_default_timezone_set('Asia/Jakarta');
$currentDateTime = date('Y-m-d H:i:s');

class M_MstAdjustAssy extends Model
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
        $query = "select * from m_assy_adjust WHERE mad_status = 1
                    ORDER BY mad_id DESC";

        return $this->db->query($query)->getResult();
    }

    public function cek_data($group, $desc)
    {
        // Query to check if the document exists
        $query = "select * from m_assy_adjust WHERE mad_grp = ? AND mad_desc = ?";
        // Executing the raw SQL query with a parameter binding
        $result = $this->db->query($query, [$group, $desc])->getRow();
        // Return true if any row is found, false otherwise
        return ($result !== null);
    }

    public function update_data($emplcode, $POST)
    {
        $group   = $POST["group"];
        $desc    = $POST["desc"];
        $tipe    = $POST["tipe"];
        // var_dump($group, $desc, $tipe, $this->currentDateTime, $emplcode);die();
        if ($tipe == "add") {
            $query = "INSERT INTO m_assy_adjust (mad_grp, mad_desc, mad_lastuser, mad_lastupdate, mad_status) VALUES (?, ?, ?, ?, 1)";

            $params = [$group, $desc, $emplcode, $this->currentDateTime];
        } else if ($tipe == "edit") {
            $id = $POST["id"];

            $query = "  UPDATE m_assy_adjust
                        SET mad_grp = ?, 
                            mad_desc = ?, 
                            mad_lastuser = ?, 
                            mad_lastupdate = ?
                        WHERE mad_id = ?";

            $params = [$group, $desc, $emplcode, $this->currentDateTime, $id];
        }

        $result = $this->db->query($query, $params);
        // var_dump($this->db->error());
        return $result;
    }

    public function update_status_data($id, $new_status, $emplcode)
    {
        $query = "  UPDATE m_assy_adjust
                    SET mad_status      = ?,
                        mad_lastuser    = ?, 
                        mad_lastupdate  = ?
                    WHERE mad_id        = ?";

        $params = [$new_status, $emplcode, $this->currentDateTime, $id];

        $this->db->query($query, $params);

        return $this->db->affectedRows() > 0;
    }
}
