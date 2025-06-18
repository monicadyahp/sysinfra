<?php

namespace App\Controllers\MstCategory;

use App\Controllers\BaseController;
use App\Models\mstcategory\MstCategoryModel;

class MstCategoryController extends BaseController
{
    protected $MstCategoryModel;

    public function __construct()
    {
        $this->MstCategoryModel = new MstCategoryModel(); // Initialize the model in the constructor
    }

    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }

        // Retrieve the user menu from the session
        $usermenu = session()->get("usermenu");

        // Find the active menu group and menu name
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "MstCategory") {
                $activeMenuGroup    = $menu->umg_groupname;
                $activeMenuName     = $menu->umn_menuname;
                break; // Stop the loop once the active menu is found
            }
        }
        $data["active_menu_group"]  = $activeMenuGroup ?? 'Transaction';
        $data["active_menu_name"]   = $activeMenuName ?? 'Master Category';
        
        return view('master/MstCategory/index', $data); // Send data to view
    }

    public function getDataCategory()
    {
        $data = $this->MstCategoryModel->getDataCategory();
        return $this->response->setJSON($data);
    }

    public function store()
    {
        // Get data from POST request
        $data = $this->request->getPost();
        
        // Store data using the model
        $result = $this->MstCategoryModel->storeCategory($data);

        // Return the result as JSON
        return $this->response->setJSON($result);
    }

    public function edit()
    {
        $categoryName = $this->request->getPost('categoryName');
        
        if (!$categoryName) {
            return $this->response->setJSON(['status' => false, 'message' => 'Category name not found']);
        }

        // Get data from model
        $data = $this->MstCategoryModel->getDataById($categoryName);

        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Data not found']);
        }
    }

    public function update()
    {
        $data = $this->request->getPost();
        
        if (!session()->get('login')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Your session has expired. Please login again.']);
        }

        if (isset($data['oldCategoryName']) && isset($data['categoryName'])) {
            $result = $this->MstCategoryModel->updateCategory($data);
            return $this->response->setJSON($result);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Invalid data: Missing required fields']);
    }

    public function delete()
    {
        $categoryName = $this->request->getPost('categoryName');
        
        // Make sure category name is not empty
        if (empty($categoryName)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Category name not found']);
        }

        // Delete data
        $result = $this->MstCategoryModel->deleteCategory($categoryName);
        return $this->response->setJSON($result);
    }
}