<?php

namespace App\Controllers\master_assy;

use App\Controllers\BaseController;

use App\Models\master_assy\M_MstMachineAssy;

class MstMachineAssy extends BaseController
{
    protected $M_MstMachineAssy;

    public function __construct()
    {
        $this->M_MstMachineAssy = new M_MstMachineAssy();
    }

    public function index()
    {
        if (!session()->get("login")) {
            return redirect()->to("/");
        }

        // Retrieve the user menu from the session
        $usermenu = session()->get("usermenu");

        // Find the active menu group and menu name
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstMachineAssy") {
                $activeMenuGroup    = $menu->umg_groupname;
                $activeMenuName     = $menu->umn_menuname;
                break; // Stop the loop once the active menu is found
            }
        }

        $data["active_menu_group"]  = $activeMenuGroup;
        $data["active_menu_name"]   = $activeMenuName;
        $data["machines"]   =$this->M_MstMachineAssy->getMachines();
        // var_dump($data);die();

        return view("master_assy/assy_machine/main", $data);
    }

    public function get_data()
    {
        $data = $this->M_MstMachineAssy->get_data();
        return $this->response->setJSON($data);
    }

    public function cek_data()
    {
        // Check if it's an AJAX request
        if ($this->request->isAJAX()) {
            // Get the data from POST
            $code   = $this->request->getPost("code");
            $pole    = $this->request->getPost("pole");
            $spm    = $this->request->getPost("spm");
            $stroke    = $this->request->getPost("stroke");
            $qty    = $this->request->getPost("qty");
            // Call the model method to check if the data exists
            $exists = $this->M_MstMachineAssy->cek_data($code, $pole, $spm, $stroke, $qty);
            // var_dump($group, $desc);die();

            // Return JSON response
            return $this->response->setJSON(["exists" => $exists]);
        } else {
            // Handle invalid requests, if needed
            return $this->response->setStatusCode(400)->setBody("Bad request");
        }
    }

    public function update_data()
    {
        $emplcode = session()->get("user_info")["EM_EmplCode"];
        // var_dump($emplcode, $_POST);die();
        $update = $this->M_MstMachineAssy->update_data($emplcode, $_POST);

        if ($update) {
            $response = [
                "success" => true,
                "message" => "Data successfully updated!",
            ];

            // Return JSON response
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(["success" => false, "error" => "Update data failed!"]);
        }
    }

    public function update_status_data()
    {
        $emplcode = session()->get("user_info")["EM_EmplCode"];

        // Check if it's a POST request
        if ($this->request->isAJAX() && $this->request->getPost("id") && $this->request->getPost("new_status")) {
            $id     = $this->request->getPost("id");
            $new_status = $this->request->getPost("new_status");
            // Call the model method to update status
            $update = $this->M_MstMachineAssy->update_status_data($id, $new_status, $emplcode);

            // Prepare response
            if ($update) {
                $response = [
                    "success" => true,
                    "message" => "Status updated successfully!"
                ];
            } else {
                $response = [
                    "success" => false,
                    "message" => "Failed to update status."
                ];
            }

            // Return JSON response
            return $this->response->setJSON($response);
        } else {
            // Handle invalid requests, if needed
            return $this->response->setStatusCode(400)->setBody("Bad request");
        }
    }
}
