<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Transaction PC</h4>
    </div>  
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <!-- Button trigger modal for adding new PC -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPCModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add New PC
        </button>
    </p>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelPC">
            <thead class="table-light">
                <tr>
                    <th width="5%">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllHeader" title="Select/Deselect All">
                        </div>
                    </th>
                    <th width="15%">Action</th>
                    <th>ID</th>
                    <th>Type</th>
                    <th>PC Name</th>
                    <th>Asset No</th>
                    <th>Asset Age</th>
                    <th>OS Name</th>
                    <th>IP Address</th>
                    <th>User</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
             
    <!-- Add PC Modal -->
    <div class="modal fade" id="addPCModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addPCModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPCModalLabel">Add New PC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPCForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pc_type" class="form-label">PC Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="pc_type" name="pc_type" required>
                                    <option value="1" selected>Client</option>
                                    <option value="2">Server</option>
                                </select>
                                <div class="error-message text-danger mt-1" id="pc_type_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="pc_name" class="form-label">PC Name</label>
                                <input type="text" class="form-control" id="pc_name" name="pc_name" placeholder="Type PC Name">
                                <div class="error-message text-danger mt-1" id="pc_name_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pc_assetno" class="form-label">PC Asset No</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="pc_assetno" name="pc_assetno" placeholder="Type or search PC Asset No">
                                    <button class="btn btn-link search-asset-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="pc_assetno_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="pc_receive_date" class="form-label">PC Receive Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="pc_receive_date" name="pc_receive_date" required>
                                <div class="error-message text-danger mt-1" id="pc_receive_date_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="os_name" class="form-label">OS Name</label>
                                <select class="form-select" id="os_name" name="os_name">
                                    <option value="">--Select OS--</option>
                                    <!-- Options will be populated dynamically -->
                                </select>
                                <div class="error-message text-danger mt-1" id="os_name_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="ip_address" class="form-label">PC IP Address</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="ip_address" name="ip_address" placeholder="Type or search PC IP Address">
                                    <button class="btn btn-link search-ip-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="ip_address_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <select class="form-select" id="location" name="location">
                                    <option value="">--Select Location--</option>
                                    <!-- Options will be populated dynamically -->
                                </select>
                                <div class="error-message text-danger mt-1" id="location_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="pc_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="pc_status" name="pcstatus" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="error-message text-danger mt-1" id="pc_status_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="user" class="form-label">User</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="user" name="user" placeholder="Type or search User">
                                    <button class="btn btn-link search-user-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="user_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submit-btn">Submit PC</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit PC Modal -->
    <div class="modal fade" id="editPCModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editPCModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPCModalLabel">Edit PC Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPCForm">
                        <input type="hidden" id="edit_tpc_id" name="tpc_id">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_pc_type" class="form-label">PC Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_pc_type" name="pc_type" required>
                                    <option value="1">Client</option>
                                    <option value="2">Server</option>
                                </select>
                                <div class="error-message text-danger mt-1" id="edit_pc_type_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_pc_name" class="form-label">PC Name</label>
                                <input type="text" class="form-control" id="edit_pc_name" name="pc_name" placeholder="Type PC Name">
                                <div class="error-message text-danger mt-1" id="edit_pc_name_error"></div>
                            </div>
                        </div>

                        <!-- Asset No & PC Receive Date -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_pc_assetno" class="form-label">PC Asset No</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_pc_assetno" name="pc_assetno" placeholder="Type or search PC Asset No">
                                    <button class="btn btn-link search-asset-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_pc_assetno_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_pc_receive_date" class="form-label">PC Receive Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_pc_receive_date" name="pc_receive_date" required>
                                <div class="error-message text-danger mt-1" id="edit_pc_receive_date_error"></div>
                            </div>
                        </div>

                        <!-- OS Name & IP Address -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_os_name" class="form-label">OS Name</label>
                                <select class="form-select" id="edit_os_name" name="os_name">
                                    <option value="">--Select OS--</option>
                                    <!-- Options will be populated dynamically -->
                                </select>
                                <div class="error-message text-danger mt-1" id="edit_os_name_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_ip_address" class="form-label">PC IP Address</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_ip_address" name="ip_address" placeholder="Type or search PC IP Address">
                                    <button class="btn btn-link search-ip-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_ip_address_error"></div>
                            </div>
                        </div>

                        <!-- Location & Status -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_location" class="form-label">Location</label>
                                <select class="form-select" id="edit_location" name="location">
                                    <option value="">--Select Location--</option>
                                    <!-- Options will be populated dynamically -->
                                </select>
                                <div class="error-message text-danger mt-1" id="edit_location_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_pc_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_pc_status" name="pcstatus">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="error-message text-danger mt-1" id="edit_pc_status_error"></div>
                            </div>
                        </div>

                        <!-- User -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_user" class="form-label">User</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_user" name="user" placeholder="Type or search User">
                                    <button class="btn btn-link search-user-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_user_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="update-btn">Update PC</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Search Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">Select Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="employeeTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Employee ID</th>
                                <th style="width: 30%;">Name</th>
                                <th style="width: 25%;">Position</th>
                                <th style="width: 35%;">Section</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- PC Detail Modal -->
    <div class="modal fade" id="pcDetailModal" tabindex="-1" aria-labelledby="pcDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 80vw;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pcDetailModalLabel">PC Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- PC Specifications -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary mb-0"><i class="fa fa-microchip me-2"></i>PC Specifications</h6>
                                <button type="button" class="btn btn-sm btn-outline-warning" id="manageSpecsBtn" style="display: none;">
                                    <i class="fa fa-edit me-1"></i><span id="specsButtonText">Edit Specs</span>
                                </button>
                            </div>
                            <div id="pc_specs_content">
                                <div class="alert alert-info text-center">
                                    <i class="fa fa-info-circle me-2"></i>No specifications data available
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Server VM Section (only shown for servers) -->
                    <div class="row mb-4" id="server_vm_section" style="display: none;">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary mb-0"><i class="fa fa-server me-2"></i>Server VM Management</h6>
                                <button type="button" class="btn btn-sm btn-outline-info" id="addServerVMBtn">
                                    <i class="fa fa-plus me-1"></i>Add VM
                                </button>
                            </div>
                            <div id="server_vm_content">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-bordered" id="serverVMTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 8%;">Action</th>
                                                <th style="width: 4%;">ID</th>
                                                <th style="width: 15%;">VM Name</th>
                                                <th style="width: 12%;">Processor</th>
                                                <th style="width: 10%;">RAM</th>
                                                <th style="width: 10%;">Storage</th>
                                                <th style="width: 10%;">VGA</th>
                                                <th style="width: 10%;">Ethernet</th>
                                                <th style="width: 10%;">IP Address</th>
                                                <th style="width: 11%;">Services</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Server VM data will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- IT Equipment -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary mb-0"><i class="fa fa-desktop me-2"></i>IT Equipment</h6>
                                <button type="button" class="btn btn-sm btn-outline-success" id="addEquipmentBtn">
                                    <i class="fa fa-plus me-1"></i>Add Equipment
                                </button>
                            </div>
                            <div id="pc_equipment_content">
                                <div class="alert alert-info text-center">
                                    <i class="fa fa-info-circle me-2"></i>No equipment data available
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset No Modal -->
    <div class="modal fade" id="assetNoModal" tabindex="-1" aria-labelledby="assetNoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assetNoModalLabel">Select Asset No</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="assetNoTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Asset No</th>
                                <th style="width: 15%;">Equipment ID</th>
                                <th style="width: 20%;">Serial Number</th>
                                <th style="width: 20%;">Receive Date</th>
                                <th style="width: 30%;">Equipment Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be dynamically added here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- IP Address Search Modal -->
    <div class="modal fade" id="ipAddressModal" tabindex="-1" aria-labelledby="ipAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ipAddressModalLabel">Select IP Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="ipAddressTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 15%;">VLAN ID</th>
                                <th style="width: 25%;">VLAN Name</th>
                                <th style="width: 25%;">IP Address</th>
                                <th style="width: 15%;">Status</th> </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- PC Specifications Modal -->
    <div class="modal fade" id="pcSpecsModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="pcSpecsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pcSpecsModalLabel">Add PC Specifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="pcSpecsForm">
                        <input type="hidden" id="specs_pc_id" name="pc_id">
                        <input type="hidden" id="specs_id" name="spec_id">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="processor" class="form-label">Processor</label>
                                <input type="text" class="form-control" id="processor" name="processor" placeholder="Enter processor details">
                                <div class="error-message text-danger mt-1" id="processor_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="ram" class="form-label">RAM</label>
                                <input type="text" class="form-control" id="ram" name="ram" placeholder="Enter RAM details">
                                <div class="error-message text-danger mt-1" id="ram_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="storage" class="form-label">Storage</label>
                                <input type="text" class="form-control" id="storage" name="storage" placeholder="Enter storage details">
                                <div class="error-message text-danger mt-1" id="storage_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="vga" class="form-label">VGA</label>
                                <input type="text" class="form-control" id="vga" name="vga" placeholder="Enter VGA details">
                                <div class="error-message text-danger mt-1" id="vga_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="ethernet" class="form-label">Ethernet</label>
                                <input type="text" class="form-control" id="ethernet" name="ethernet" placeholder="Enter Ethernet details">
                                <div class="error-message text-danger mt-1" id="ethernet_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitSpecsBtn">Submit Specifications</button>
                </div>
            </div>
        </div>
    </div>

    <!-- PC Equipment Modal -->
    <div class="modal fade" id="pcEquipmentModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="pcEquipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pcEquipmentModalLabel">Add New IT Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="pcEquipmentForm">
                        <input type="hidden" id="equipment_pc_id" name="pc_id">
                        <input type="hidden" id="equipment_id" name="equipment_id">

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="equipment_type" class="form-label">Equipment Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="equipment_type" name="equipment_type" required>
                                    <option value="">--Select Type--</option>
                                    <option value="1">Monitor</option>
                                    <option value="2">Printer</option>
                                    <option value="3">Scanner</option>
                                </select>
                                <div class="error-message text-danger mt-1" id="equipment_type_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="equipment_asset_no" class="form-label">Equipment Asset No</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="equipment_asset_no" name="asset_no" placeholder="Type or search Equipment Asset No">
                                    <button class="btn btn-link search-equipment-asset-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="equipment_asset_no_error"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="equipment_receive_date" class="form-label">Receive Date</label>
                                <input type="date" class="form-control" id="equipment_receive_date" name="receive_date">
                                <div class="error-message text-danger mt-1" id="equipment_receive_date_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="submitEquipmentBtn">Submit Equipment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- PC Server VM Modal -->
     <div class="modal fade" id="pcServerVMModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="pcServerVMModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pcServerVMModalLabel">Add New Server VM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="pcServerVMForm">
                        <input type="hidden" id="vm_pc_id" name="pc_id">
                        <input type="hidden" id="vm_id" name="vm_id">

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="vm_name" class="form-label">VM Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="vm_name" name="vm_name" placeholder="Enter VM Name" required>
                                <div class="error-message text-danger mt-1" id="vm_name_error"></div>
                            </div>
                        </div>

                        <!-- VM Hardware Specifications -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="vm_processor" class="form-label">Processor</label>
                                <input type="text" class="form-control" id="vm_processor" name="vm_processor" placeholder="Enter VM processor details">
                                <div class="error-message text-danger mt-1" id="vm_processor_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="vm_ram" class="form-label">RAM</label>
                                <input type="text" class="form-control" id="vm_ram" name="vm_ram" placeholder="Enter VM RAM details">
                                <div class="error-message text-danger mt-1" id="vm_ram_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="vm_storage" class="form-label">Storage</label>
                                <input type="text" class="form-control" id="vm_storage" name="vm_storage" placeholder="Enter VM storage details">
                                <div class="error-message text-danger mt-1" id="vm_storage_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="vm_vga" class="form-label">VGA</label>
                                <input type="text" class="form-control" id="vm_vga" name="vm_vga" placeholder="Enter VM VGA details">
                                <div class="error-message text-danger mt-1" id="vm_vga_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="vm_ethernet" class="form-label">Ethernet</label>
                                <input type="text" class="form-control" id="vm_ethernet" name="vm_ethernet" placeholder="Enter VM Ethernet details">
                                <div class="error-message text-danger mt-1" id="vm_ethernet_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="vm_ip_address" class="form-label">VM IP Address</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="vm_ip_address" name="vm_ip_address" placeholder="Type or search VM IP Address">
                                    <button class="btn btn-link search-vm-ip-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="vm_ip_address_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="vm_services" class="form-label">Services</label>
                                <textarea class="form-control" id="vm_services" name="vm_services" rows="3" placeholder="Enter services description"></textarea>
                                <div class="error-message text-danger mt-1" id="vm_services_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="vm_remark" class="form-label">Remark</label>
                                <textarea class="form-control" id="vm_remark" name="vm_remark" rows="2" placeholder="Enter additional remarks"></textarea>
                                <div class="error-message text-danger mt-1" id="vm_remark_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitServerVMBtn">Submit VM</button>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
    $(document).ready(function () {
        document.head.insertAdjacentHTML('beforeend', `
            <style>
                .is-invalid {
                    border-color: #dc3545 !important;
                    padding-right: calc(1.5em + 0.75rem);
                    background-repeat: no-repeat;
                    background-position: right calc(0.375em + 0.1875rem) center;
                    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
                }
                
                .error-message {
                    font-size: 0.875em;
                    margin-top: 0.25rem;
                }
                
                /* Equipment card styling */
                .equipment-card {
                    border: 2px solid #e3f2fd;
                    border-radius: 12px;
                    padding: 1.25rem;
                    margin-bottom: 1rem;
                    position: relative;
                    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    transition: all 0.3s ease;
                }

                .equipment-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
                    border-color: #2196f3;
                }

                .equipment-actions {
                    position: absolute;
                    top: 0.75rem;
                    right: 0.75rem;
                }

                .equipment-header {
                    display: flex;
                    align-items: center;
                    margin-bottom: 1rem;
                    padding-bottom: 0.75rem;
                    border-bottom: 1px solid #e3f2fd;
                }

                .equipment-icon {
                    width: 48px;
                    height: 48px;
                    background: linear-gradient(135deg, #2196f3, #1976d2);
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 1rem;
                    color: white;
                    font-size: 1.5rem;
                }

                .equipment-title {
                    font-weight: 600;
                    color: #1565c0;
                    margin-bottom: 0.25rem;
                }

                .equipment-subtitle {
                    color: #757575;
                    font-size: 0.875rem;
                }

                .specs-table th {
                    background-color: #f8f9fa;
                    font-weight: 600;
                    width: 25%;
                    vertical-align: middle;
                }

                .specs-table td {
                    vertical-align: middle;
                }

                /* Filter styling */
                .filter-wrapper {
                    display: inline-block;
                    margin-left: 20px;
                }
                
                .filter-wrapper label {
                    font-weight: normal;
                    margin-bottom: 0;
                }
                
                .filter-wrapper select {
                    display: inline-block;
                    width: auto;
                    min-width: 120px;
                }

                /* Export buttons styling */
                .export-buttons {
                    display: inline-flex;
                    gap: 0.5rem;
                    margin-left: 1rem;
                }
                
                .export-buttons .btn {
                    padding: 0.25rem 0.5rem;
                    font-size: 0.875rem;
                    line-height: 1.25;
                }

            </style>
        `);

        let base_url = '<?= base_url() ?>';
        let isEditMode = false;
        let previousModal = null;
        let isSearchModalOpen = false;
        let savedFormData = {};
        let tabelPC;
        let currentPCId = null;
        let currentPCSpecs = null;
        let currentPCType = null;
        let isSpecsEditMode = false;
        let serverVMDataTable = null;
        let assetNoDataTable = null;
        let employeeDataTable = null;
        let ipAddressDataTable = null;
        let selectedPCIds = [];
        let isFromPCDetailsModal = false;
        let shouldReturnToPCDetails = false;
        
        // Function to get PC type display name
        function getPCTypeDisplay(type) {
            const types = {
                1: 'Client', 
                2: 'Server',
            };
            return types[type] || 'Unknown';
        }

        // Function to get equipment type display name
        function getEquipmentTypeDisplay(type) {
            const types = {
                1: 'Monitor',
                2: 'Printer',
                3: 'Scanner'
            };
            return types[type] || 'Unknown';
        }

        // Function to get equipment icon
        function getEquipmentIcon(type) {
            const icons = {
                1: 'fa fa-desktop',
                2: 'fa fa-print',
                3: 'fa fa-expand'
            };
            return icons[type] || 'fa fa-desktop';
        }

        // Function to get VM type display name
        function getVMTypeDisplay(type) {
            const types = {
                1: 'VM',
                2: 'Non-VM'
            };
            return types[type] || 'Unknown';
        }
        
        // Pastikan fungsi restoreFormData dan saveFormData memegang nilai IP
        // Fungsi saveFormData()
        function saveFormData() {
            const formData = {};
            
            if (previousModal === '#addPCModal') {
                // Save add PC form data
                formData.pc_type = $('#pc_type').val();
                formData.pc_name = $('#pc_name').val();
                formData.pc_assetno = $('#pc_assetno').val();
                formData.pc_receive_date = $('#pc_receive_date').val();
                formData.pc_status = $('#pc_status').val();
                formData.os_name = $('#os_name').val();
                formData.ip_address = $('#ip_address').val(); // PASTIKAN INI TERMASUK
                formData.user = $('#user').val();
                formData.location = $('#location').val();
            } else if (previousModal === '#editPCModal') {
                // Save edit PC form data
                formData.edit_tpc_id = $('#edit_tpc_id').val();
                formData.edit_pc_type = $('#edit_pc_type').val();
                formData.edit_pc_name = $('#edit_pc_name').val();
                formData.edit_pc_status = $('#edit_pc_status').val();
                formData.edit_pc_assetno = $('#edit_pc_assetno').val();
                formData.edit_pc_receive_date = $('#edit_pc_receive_date').val();
                formData.edit_os_name = $('#edit_os_name').val();
                formData.edit_ip_address = $('#edit_ip_address').val(); // PASTIKAN INI TERMASUK
                formData.edit_user = $('#edit_user').val();
                formData.edit_location = $('#edit_location').val();
            }
            
            return formData;
        }


        // Function to restore form data after closing search modal
        function restoreFormData(formData) {
            if (previousModal === '#addPCModal') {
                // Restore add PC form data
                $('#pc_type').val(formData.pc_type || '1');
                $('#pc_name').val(formData.pc_name || '');
                $('#pc_assetno').val(formData.pc_assetno || '');
                $('#pc_receive_date').val(formData.pc_receive_date || '');
                $('#pc_status').val(formData.pc_status || '1');
                $('#os_name').val(formData.os_name || '');
                $('#ip_address').val(formData.ip_address || '');
                $('#user').val(formData.user || '');
                $('#location').val(formData.location || '');
                
                // Trigger age calculation after restoration
                if (formData.pc_receive_date) {
                    setTimeout(function() {
                        $('#pc_receive_date').trigger('change');
                    }, 50);
                }
            } else if (previousModal === '#editPCModal') {
                // Restore edit PC form data
                $('#edit_tpc_id').val(formData.edit_tpc_id || '');
                $('#edit_pc_type').val(formData.edit_pc_type || '1');
                $('#edit_pc_name').val(formData.edit_pc_name || '');
                $('#edit_pc_status').val(formData.edit_pc_status || '1');
                $('#edit_pc_assetno').val(formData.edit_pc_assetno || '');
                $('#edit_pc_receive_date').val(formData.edit_pc_receive_date || '');
                $('#edit_os_name').val(formData.edit_os_name || '');
                $('#edit_ip_address').val(formData.edit_ip_address || '');
                $('#edit_user').val(formData.edit_user || '');
                $('#edit_location').val(formData.edit_location || '');
                
                // Trigger age calculation after restoration
                if (formData.edit_pc_receive_date) {
                    setTimeout(function() {
                        $('#edit_pc_receive_date').trigger('change');
                    }, 50);
                }
            }
        }

        // Function to restore equipment form data
        function restoreEquipmentFormData(formData, assetData) {
            $('#equipment_pc_id').val(formData.pc_id || '');
            $('#equipment_id').val(formData.equipment_id || '');
            $('#equipment_type').val(formData.equipment_type || '');
            $('#equipment_asset_no').val(formData.asset_no || '');
            $('#equipment_receive_date').val(formData.receive_date || '');
            
            // Display equipment ID if available
            if (assetData && assetData.e_equipmentid) {
                displayEquipmentIDForEquipment(assetData.e_equipmentid);
            }
            
            // Trigger age calculation if receive date exists
            if (formData.receive_date) {
                $('#equipment_receive_date').trigger('change');
            }
        }

        // Function to calculate age from receive date
        function calculateAge(receiveDate) {
            if (!receiveDate) {
                return '-';
            }
            
            try {
                const receiveDateObj = new Date(receiveDate);
                const today = new Date();
                
                if (receiveDateObj > today) {
                    return 'Future date';
                }
                
                let years = today.getFullYear() - receiveDateObj.getFullYear();
                let months = today.getMonth() - receiveDateObj.getMonth();
                let days = today.getDate() - receiveDateObj.getDate();
                
                if (days < 0) {
                    months--;
                    const lastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                    days += lastMonth.getDate();
                }
                
                if (months < 0) {
                    years--;
                    months += 12;
                }
                
                if (years === 0 && months === 0) {
                    if (days === 0) {
                        return "Today";
                    } else if (days === 1) {
                        return "1 day";
                    } else {
                        return days + " days";
                    }
                } else if (years === 0) {
                    if (months === 1) {
                        return "1 month";
                    } else {
                        return months + " months";
                    }
                } else if (months === 0) {
                    if (years === 1) {
                        return "1 year";
                    } else {
                        return years + " years";
                    }
                } else {
                    let result = "";
                    if (years === 1) {
                        result = "1 year";
                    } else {
                        result = years + " years";
                    }
                    
                    if (months === 1) {
                        result += " 1 month";
                    } else {
                        result += " " + months + " months";
                    }
                    
                    return result;
                }
                
            } catch (error) {
                console.error('Error calculating age:', error);
                return 'Invalid date';
            }
        }
        
        // Function to display equipment ID
        function displayEquipmentID(assetData, isEditMode) {
            const equipmentDisplayId = isEditMode ? 'edit_equipment_id_display' : 'equipment_id_display';
            const targetInput = isEditMode ? $('#edit_pc_assetno') : $('#pc_assetno');
            
            // Remove existing display if it exists
            $(`#${equipmentDisplayId}`).remove();
            
            // Create equipment ID display if equipment ID exists
            if (assetData && assetData.equipment_id) {
                // Find the parent column div to append reliably
                const parentColumn = targetInput.closest('.col-md-6');
                
                // Append to the parent column (below the input group)
                parentColumn.append(`
                    <div class="mt-1">
                        <small class="text-muted" id="${equipmentDisplayId}">
                            Equipment ID: ${assetData.equipment_id}
                        </small>
                    </div>
                `);
            }
        }

        // Function to display equipment ID for equipment forms
        function displayEquipmentIDForEquipment(equipmentId) {
            // Remove existing display
            $('#equipment_equipment_id_display').remove();
            
            // Find the parent column div
            const parentColumn = $('#equipment_asset_no').closest('.col-md-6');
            
            // Append to the parent column (below the input group)
            parentColumn.append(`
                <div class="mt-1">
                    <small class="text-muted" id="equipment_equipment_id_display">Equipment ID: ${equipmentId}</small>
                </div>
            `);
        }

        // Function to filter Server VM data
        function filterServerVMData(filterType) {
            if (!window.originalServerVMData || !serverVMDataTable) {
                return;
            }
            
            let filteredData = window.originalServerVMData;
            
            // Apply filter based on selected type
            if (filterType !== 'All') {
                filteredData = window.originalServerVMData.filter(function(item) {
                    return item.tpv_type == filterType;
                });
            }
            
            // Clear and reload DataTable with filtered data
            serverVMDataTable.clear();
            serverVMDataTable.rows.add(filteredData);
            serverVMDataTable.draw();
        }

        // Function to update selected IDs array
        // Function to update selected IDs array
        function updateSelectedIds() {
            selectedPCIds = [];
            $('.pc-checkbox:checked').each(function() {
                selectedPCIds.push(parseInt($(this).val()));
            });
            updateSelectedCount();
        }

        // Function to update selected count display
        function updateSelectedCount() {
            const hasSelection = selectedPCIds.length > 0;
            $('#exportSelectedCSV, #exportSelectedODS, #exportSelectedXLSX').toggleClass('disabled', !hasSelection);
        }

        // Function to export selected data
        function exportSelectedData(format) {
            if (selectedPCIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one PC record to export.',
                    showConfirmButton: true
                });
                return;
            }

            Swal.fire({
                title: 'Confirm Export',
                html: `Are you sure you want to export <strong>${selectedPCIds.length}</strong> selected PC record(s) to ${format} format?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Yes, export to ${format}!`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $('<form>', {
                        method: 'POST',
                        action: base_url + `TransPC/exportSelected${format}`
                    });

                    selectedPCIds.forEach(id => {
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'selected_ids[]',
                            value: id
                        }));
                    });

                    $('body').append(form);
                    form.submit();
                    form.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Export Started',
                        text: `Your ${format} export for ${selectedPCIds.length} records has been initiated.`,
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        }
        
        // Initialize DataTable with filters
        function initializePCDataTable(statusFilter = 'All', typeFilter = 'All') {
            if (window.currentStatusFilter !== undefined) {
                statusFilter = window.currentStatusFilter;
            }
            if (window.currentTypeFilter !== undefined) {
                typeFilter = window.currentTypeFilter;
            }
            
            if ($.fn.DataTable.isDataTable('#tabelPC')) {
                tabelPC.destroy();
                window.pcFiltersInitialized = false;
                $('#statusFilterWrapper, #typeFilterWrapper, #exportButtonsWrapper').remove();
            }

            tabelPC = $('#tabelPC').DataTable({
                scrollX: true,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], 
                order: [[2, 'desc']], 
                autoWidth: false,
                ajax: {
                    url: base_url + "TransPC/getData",
                    data: function(d) {
                        d.status = statusFilter;
                        d.type = typeFilter;
                    },
                    dataSrc: function(json) {
                        if (json && json.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error Loading Data',
                                text: json.error,
                                showConfirmButton: true
                            });
                            return [];
                        }
                        return json;
                    },
                    beforeSend: function() {
                        let spinner = `
                            <div class="align-items-center justify-content-center gap-2">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                            </div>`;
                        $('#tabelPC tbody').html(`<tr><td colspan="12">${spinner}</td></tr>`);
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error, thrown, xhr.responseText);
                        let errorMessage = 'Error loading data. Please try again.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response && response.error) {
                                errorMessage = response.error;
                            } else if (response && response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) { }
                        $('#tabelPC tbody').html(`<tr><td colspan="12" class="text-center">${errorMessage}</td></tr>`);
                    }
                },
                columns: [
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        width: '5%',
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="form-check-input pc-checkbox" value="${row.tpc_id}" data-id="${row.tpc_id}">`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        width: '15%',
                        render: function(data, type, row) {
                            let buttons = `
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:;" class="btn btn-icon btn-outline-info detail-btn" data-id="${row.tpc_id}" title="View Details">
                                        <i class="fa fa-desktop"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.tpc_id}" title="Edit PC">
                                        <i class="fa fa-pen-to-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.tpc_id}" title="Delete PC">
                                        <i class="fa fa-trash-can"></i>
                                    </a>
                                </div>
                            `;
                            
                            return buttons;
                        }
                    },
                    { data: 'tpc_id' },
                    { 
                        data: 'tpc_type', 
                        render: function(data) {
                            const typeName = getPCTypeDisplay(data);
                            const badgeClass = data == 1 ? 'bg-primary' : 'bg-secondary';
                            return `<span class="badge ${badgeClass}">${typeName}</span>`;
                        } 
                    },
                    { data: 'tpc_name', render: function(data) { return data ? data : '-'; } },
                    { data: 'tpc_assetno', render: function(data) { return data ? data : '-'; } },
                    { 
                        data: 'tpc_pcreceivedate', 
                        render: function(data, type, row) {
                            if (!data) return '-';
                            return calculateAge(data);
                        },
                        orderable: true
                    },
                    { data: 'tpc_osname', render: function(data) { return data ? data : '-'; } },
                    { data: 'tpc_ipaddress', render: function(data) { return data ? data : '-'; } },
                    { data: 'tpc_user', render: function(data) { return data ? data : '-'; } },
                    { data: 'tpc_location', render: function(data) { return data ? data : '-'; } },
                    { data: 'tpc_status',className: 'text-center', render: function(data, type, row) {
                        const statusText = data == 1 ? 'Active' : 'Inactive';
                        const statusClass = data == 1 ? 'bg-success text-white' : 'bg-danger text-white';
                        return `<span class="badge ${statusClass}">${statusText}</span>`;} 
                    },
                ],
                drawCallback: function() {
                    if (!window.pcFiltersInitialized) {
                        window.pcFiltersInitialized = true;
                        
                        const lengthControl = $('#tabelPC_length');
                        
                        const statusFilterHtml = `
                            <div class="filter-wrapper" id="statusFilterWrapper" style="display: inline-block; margin-left: 20px;">
                                <label style="font-weight: normal;">
                                    Status:
                                    <select id="statusFilter" class="form-select form-select-sm" style="display: inline-block; width: auto; min-width: 120px;">
                                        <option value="All">All</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </label>
                            </div>
                        `;
                        
                        const typeFilterHtml = `
                            <div class="filter-wrapper" id="typeFilterWrapper" style="display: inline-block; margin-left: 20px;">
                                <label style="font-weight: normal;">
                                    Type:
                                    <select id="typeFilter" class="form-select form-select-sm" style="display: inline-block; width: auto; min-width: 120px;">
                                        <option value="All">All</option>
                                        <option value="1">Client</option>
                                        <option value="2">Server</option>
                                    </select>
                                </label>
                            </div>
                        `;
                        
                        const exportButtons = `
                            <div class="export-buttons" id="exportButtonsWrapper" style="display: inline-flex; gap: 0.5rem; margin-left: 1rem;">
                                <div class="dropdown">
                                    <button class="btn btn-success dropdown-toggle btn-sm" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-file-export"></i> Export
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                        <li><h6 class="dropdown-header">Export All Data</h6></li>
                                        <li><a class="dropdown-item" href="#" id="exportAllCSV">
                                            <i class="fa fa-file-csv me-2"></i>All to CSV
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" id="exportAllODS">
                                            <i class="fa fa-file-alt me-2"></i>All to ODS
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" id="exportAllXLSX">
                                            <i class="fa fa-file-excel me-2"></i>All to XLSX
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><h6 class="dropdown-header">Export Selected Data</h6></li>
                                        <li><a class="dropdown-item" href="#" id="exportSelectedCSV">
                                            <i class="fa fa-file-csv me-2"></i>Selected to CSV
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" id="exportSelectedODS">
                                            <i class="fa fa-file-alt me-2"></i>Selected to ODS
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" id="exportSelectedXLSX">
                                            <i class="fa fa-file-excel me-2"></i>Selected to XLSX
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        `;
                        
                        lengthControl.append(statusFilterHtml);
                        lengthControl.append(typeFilterHtml);
                        lengthControl.append(exportButtons);
                        
                        $('#statusFilter').on('change', function() {
                            const selectedStatus = $(this).val();
                            const selectedType = $('#typeFilter').val();
                            selectedPCIds = []; 
                            updateSelectedCount();
                            window.currentStatusFilter = selectedStatus;
                            window.currentTypeFilter = selectedType;
                            initializePCDataTable(selectedStatus, selectedType);
                        });
                        
                        $('#typeFilter').on('change', function() {
                            const selectedType = $(this).val();
                            const selectedStatus = $('#statusFilter').val();
                            selectedPCIds = []; 
                            updateSelectedCount();
                            window.currentStatusFilter = selectedStatus;
                            window.currentTypeFilter = selectedType;
                            initializePCDataTable(selectedStatus, selectedType);
                        });

                        $('#exportAllCSV').on('click', function(e) {
                            e.preventDefault();
                            const originalHtml = $(this).html();
                            $(this).html('<i class="fa fa-spinner fa-spin me-2"></i>Exporting...');
                            window.location.href = base_url + 'TransPC/exportCSV';
                            const self = this;
                            setTimeout(() => {
                                $(self).html(originalHtml);
                            }, 2000);
                        });

                        $('#exportAllODS').on('click', function(e) {
                            e.preventDefault();
                            const originalHtml = $(this).html();
                            $(this).html('<i class="fa fa-spinner fa-spin me-2"></i>Exporting...');
                            window.location.href = base_url + 'TransPC/exportODS';
                            const self = this;
                            setTimeout(() => {
                                $(self).html(originalHtml);
                            }, 2000);
                        });

                        $('#exportAllXLSX').on('click', function(e) {
                            e.preventDefault();
                            const originalHtml = $(this).html();
                            $(this).html('<i class="fa fa-spinner fa-spin me-2"></i>Exporting...');
                            window.location.href = base_url + 'TransPC/exportXLSX';
                            const self = this;
                            setTimeout(() => {
                                $(self).html(originalHtml);
                            }, 2000);
                        });

                        $('#exportSelectedCSV').on('click', function(e) {
                            e.preventDefault();
                            exportSelectedData('CSV');
                        });

                        $('#exportSelectedODS').on('click', function(e) {
                            e.preventDefault();
                            exportSelectedData('ODS');
                        });

                        $('#exportSelectedXLSX').on('click', function(e) {
                            e.preventDefault();
                            exportSelectedData('XLSX');
                        });
                    }
                    
                    setTimeout(function() {
                        $('#statusFilter').val(statusFilter);
                        $('#typeFilter').val(typeFilter);
                        window.currentStatusFilter = statusFilter;
                        window.currentTypeFilter = typeFilter;
                    }, 100);
                }
            });

            $('#tabelPC').on('change', '.pc-checkbox', function() {
                updateSelectedIds();
            });
        }


        // Handle header checkbox for select/deselect all
        $('#selectAllHeader').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.pc-checkbox').prop('checked', isChecked);
            updateSelectedIds();
        });


        $(document).on('change', '.pc-checkbox', function() {
            updateSelectedIds();
            
            const totalCheckboxes = $('.pc-checkbox').length;
            const checkedCheckboxes = $('.pc-checkbox:checked').length;
            
            if (checkedCheckboxes === 0) {
                $('#selectAllHeader').prop('indeterminate', false).prop('checked', false);
            } else if (checkedCheckboxes === totalCheckboxes) {
                $('#selectAllHeader').prop('indeterminate', false).prop('checked', true);
            } else {
                $('#selectAllHeader').prop('indeterminate', true).prop('checked', false);
            }
        });

        // Reset header checkbox when table is reloaded/filtered
        $('#tabelPC').on('draw.dt', function() {
            $('#selectAllHeader').prop('checked', false).prop('indeterminate', false);
            selectedPCIds = [];
            updateSelectedCount();
        });

        // Initialize DataTable for asset search (simplified)
        function initAssetNoDataTable() {
            if (assetNoDataTable) {
                assetNoDataTable.destroy();
            }
            
            assetNoDataTable = $('#assetNoTable').DataTable({
                processing: true,
                serverSide: false,
                paging: true,
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                searching: true,
                ordering: true,
                order: [[0, 'asc']],
                ajax: {
                    url: base_url + 'TransPC/searchAssetNo',
                    type: 'GET',
                    dataSrc: function(json) {
                        return json || [];
                    }
                },
                columns: [
                    { 
                        data: 'e_assetno', 
                        width: '15%', 
                        render: function(data, type, row) { 
                            const displayText = data ? data : '-';
                            const isFromEquipmentId = row.original_assetno === null || row.original_assetno === '';
                            
                            if (isFromEquipmentId) {
                                return `${displayText} <small class="text-muted">(ID)</small>`;
                            }
                            return displayText;
                        } 
                    },
                    { data: 'e_equipmentid', width: '15%', render: function(data) { return data ? data : '-'; } },
                    { data: 'e_serialnumber', width: '20%', render: function(data) { return data ? data : '-'; } },
                    { 
                        data: 'e_receivedate', 
                        width: '20%', 
                        render: function(data) { 
                            return data ? new Date(data).toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }) : '-'; 
                        } 
                    },
                    { data: 'e_equipmentname', width: '30%', render: function(data) { return data ? data : '-'; } },
                ],
                columnDefs: [
                    {
                        targets: '_all',
                        defaultContent: '-'
                    }
                ]
            });
        }


        // Initialize DataTable for employee search (simplified)
        function initEmployeeDataTable() {
            if (employeeDataTable) {
                employeeDataTable.destroy();
            }
            
            employeeDataTable = $('#employeeTable').DataTable({
                processing: true,
                serverSide: false,
                paging: true,
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                searching: true,
                ordering: true,
                order: [[1, 'asc']],
                ajax: {
                    url: base_url + 'TransPC/searchEmployees',
                    type: 'GET',
                    dataSrc: function(json) {
                        return json || [];
                    }
                },
                columns: [
                    { data: 'em_emplcode', width: '10%' },
                    { data: 'em_emplname', width: '30%' },
                    { data: 'pm_positionname', width: '25%' },
                    { data: 'sec_section', width: '35%' }
                ],
                columnDefs: [
                    {
                        targets: '_all',
                        defaultContent: '-'
                    }
                ]
            });
        }

        // Initialize DataTable for IP Address search (modified to show status and allow filtering)
        function initIPAddressDataTable() {
            if (ipAddressDataTable) {
                ipAddressDataTable.destroy();
                $('#ipAddressTable tbody').html(''); // Clear previous table body content
                // Hapus juga elemen filter kustom yang mungkin ada dari inisialisasi sebelumnya
                $('#ipStatusFilterWrapper').remove(); 
            }
            
            ipAddressDataTable = $('#ipAddressTable').DataTable({
                processing: true,
                serverSide: false,
                paging: true,
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                searching: true, 
                ordering: true,
                order: [[2, 'asc']], // Default sort by IP Address (column index 2)
                ajax: {
                    url: base_url + 'TransPC/searchIPAddresses',
                    type: 'GET',
                    dataSrc: function(json) {
                        if (json && json.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error Loading IP Data',
                                text: json.error,
                                showConfirmButton: true
                            });
                            return [];
                        }
                        return json || [];
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables IP AJAX error:', error, thrown, xhr.responseText);
                        let errorMessage = 'Error loading IP data. Please try again.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response && response.error) {
                                errorMessage = response.error;
                            } else if (response && response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) { }
                        $('#ipAddressTable tbody').html(`<tr><td colspan="4" class="text-center text-danger">${errorMessage}</td></tr>`);
                    }
                },
                columns: [
                    { data: 'mip_vlanid', width: '15%', defaultContent: '-' },
                    { data: 'mip_vlanname', width: '25%', defaultContent: '-' },
                    { data: 'mip_ipadd', width: '25%', defaultContent: '-' }, 
                    { 
                        data: 'mip_status', 
                        width: '15%', 
                        className: 'text-center',
                        render: function(data, type, row) {
                            const statusText = data == 1 ? 'Used' : 'Unused';
                            const badgeClass = data == 1 ? 'bg-danger text-white' : 'bg-success text-white';
                            return `<span class="badge ${badgeClass}">${statusText}</span>`;
                        }
                    },
                ],
                columnDefs: [
                    { targets: '_all', defaultContent: '-' }
                ],
                initComplete: function() {
                    const api = this.api();
                    const filterColumn = api.column(3); 

                    // Cari elemen filter bawaan DataTables
                    let filterContainer = $(this).closest('.dataTables_wrapper').find('.dataTables_filter');
                    
                    // Buat elemen filter kustom baru dan tambahkan ke dalam filter container bawaan
                    let customFilterHtml = `
                        <div id="ipStatusFilterWrapper" class="d-inline-flex align-items-center ms-2">
                            <label style="font-weight: normal; margin-bottom: 0;">Status:</label>
                            <select id="ipStatusFilter" class="form-select form-select-sm ms-1" style="width: auto;">
                                <option value="">Show All</option>
                                <option value="0">Unused</option>
                                <option value="1">Used</option>
                            </select>
                        </div>
                    `;
                    filterContainer.append(customFilterHtml);
                    
                    // Bind event listener ke filter kustom
                    $('#ipStatusFilter').on('change', function() {
                        const val = $.fn.dataTable.util.escapeRegex($(this).val());
                        filterColumn.search(val ? '^' + val + '$' : '', true, false).draw();
                    });
                }
            });
        }

        // Function to load Server VM data into DataTable
        // Function to load Server VM data into DataTable
        function loadServerVMData(serverVMData) {
            const tableContainer = $('#server_vm_content');

            // Clear any previous error messages or content
            tableContainer.empty();

            // Re-create the table structure every time to ensure a clean slate
            tableContainer.html(`
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" id="serverVMTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 8%;">Action</th>
                                <th style="width: 4%;">ID</th>
                                <th style="width: 15%;">VM Name</th>
                                <th style="width: 12%;">Processor</th>
                                <th style="width: 10%;">RAM</th>
                                <th style="width: 10%;">Storage</th>
                                <th style="width: 10%;">VGA</th>
                                <th style="width: 10%;">Ethernet</th>
                                <th style="width: 10%;">IP Address</th>
                                <th style="width: 11%;">Services</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            `);

            // Destroy existing DataTable instance if it exists on the newly created table
            if ($.fn.DataTable.isDataTable('#serverVMTable')) {
                $('#serverVMTable').DataTable().destroy();
            }

            if (serverVMData && Array.isArray(serverVMData) && serverVMData.length > 0) {
                try {
                    serverVMDataTable = $('#serverVMTable').DataTable({
                        data: serverVMData,
                        pageLength: 5,
                        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                        ordering: true,
                        searching: true,
                        info: true,
                        paging: true,
                        scrollX: true,
                        autoWidth: false,
                        responsive: false,
                        destroy: true, // Allow reinitialization
                        processing: false,
                        deferRender: false,
                        language: {
                            processing: "Processing...",
                            emptyTable: "No VM data available",
                            info: "Showing _START_ to _END_ of _TOTAL_ entries",
                            infoEmpty: "Showing 0 to 0 of 0 entries",
                            lengthMenu: "Show _MENU_ entries"
                        },
                        columnDefs: [
                            { targets: 0, width: '80px', orderable: false, className: 'text-center' },
                            { targets: 1, width: '40px', className: 'text-center' },
                            { targets: 2, width: '120px' },
                            { targets: [3, 4, 5, 6, 7], width: '90px' },
                            { targets: 8, width: '100px' },
                            { targets: 9, width: '150px' }
                        ],
                        columns: [
                            {
                                data: null,
                                render: function(data, type, row) {
                                    return `
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-warning edit-servervm-btn" data-id="${row.tpv_id}" title="Edit VM">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger delete-servervm-btn" data-id="${row.tpv_id}" title="Delete VM">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    `;
                                }
                            },
                            { data: 'tpv_id', render: function(data) { return data || '-'; } },
                            { data: 'tpv_name', render: function(data) { return data || '-'; } },
                            { data: 'tpv_processor', render: function(data) { return data || '-'; } },
                            { data: 'tpv_ram', render: function(data) { return data || '-'; } },
                            { data: 'tpv_storage', render: function(data) { return data || '-'; } },
                            { data: 'tpv_vga', render: function(data) { return data || '-'; } },
                            { data: 'tpv_ethernet', render: function(data) { return data || '-'; } },
                            { data: 'tpv_ipaddress', render: function(data) { return data || '-'; } },
                            { data: 'tpv_services', render: function(data) { return data || '-'; } }
                        ],
                        initComplete: function() {
                            // Adjust columns after data is loaded
                            this.api().columns.adjust().draw();
                        }
                    });
                } catch (error) {
                    console.error('Error initializing VM DataTable:', error);
                    // Fallback: Show manual table if DataTable fails
                    let manualTableHtml = `
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 8%;">Action</th>
                                        <th style="width: 4%;">ID</th>
                                        <th style="width: 15%;">VM Name</th>
                                        <th style="width: 12%;">Processor</th>
                                        <th style="width: 10%;">RAM</th>
                                        <th style="width: 10%;">Storage</th>
                                        <th style="width: 10%;">VGA</th>
                                        <th style="width: 10%;">Ethernet</th>
                                        <th style="width: 10%;">IP Address</th>
                                        <th style="width: 11%;">Services</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    serverVMData.forEach(item => {
                        manualTableHtml += `
                            <tr>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-warning edit-servervm-btn" data-id="${item.tpv_id}" title="Edit VM">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger delete-servervm-btn" data-id="${item.tpv_id}" title="Delete VM">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">${item.tpv_id || '-'}</td>
                                <td>${item.tpv_name || '-'}</td>
                                <td>${item.tpv_processor || '-'}</td>
                                <td>${item.tpv_ram || '-'}</td>
                                <td>${item.tpv_storage || '-'}</td>
                                <td>${item.tpv_vga || '-'}</td>
                                <td>${item.tpv_ethernet || '-'}</td>
                                <td>${item.tpv_ipaddress || '-'}</td>
                                <td>${item.tpv_services || '-'}</td>
                            </tr>
                        `;
                    });

                    manualTableHtml += `
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-warning mt-2">
                            <i class="fa fa-exclamation-triangle me-2"></i>DataTable initialization failed, showing static table. Some features may be limited.
                        </div>
                    `;

                    $('#server_vm_content').html(manualTableHtml);
                }
            } else {
                // Show empty message with proper table structure
                tableContainer.html(`
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="serverVMTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 8%;">Action</th>
                                    <th style="width: 4%;">ID</th>
                                    <th style="width: 15%;">VM Name</th>
                                    <th style="width: 12%;">Processor</th>
                                    <th style="width: 10%;">RAM</th>
                                    <th style="width: 10%;">Storage</th>
                                    <th style="width: 10%;">VGA</th>
                                    <th style="width: 10%;">Ethernet</th>
                                    <th style="width: 10%;">IP Address</th>
                                    <th style="width: 11%;">Services</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="10" class="text-center text-muted">
                                        <i class="fa fa-info-circle me-2"></i>No VM data available for this server
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                `);
            }
        }
        // Load locations 
        function loadLocations(targetSelector, selectedLocation = null) {
            $(targetSelector).html('<option value="">Loading Locations...</option>');
                            
            $.ajax({
                url: base_url + 'TransPC/getLocations',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $(targetSelector).empty();
                    $(targetSelector).append('<option value="">--Select Location--</option>');
                                            
                    if (response && response.length > 0) {
                        response.forEach(location => {
                            const selected = (selectedLocation && location.mpl_name === selectedLocation) ? 'selected' : '';
                            $(targetSelector).append(
                                `<option value="${location.mpl_name}" ${selected}>${location.mpl_name}</option>`
                            );
                        });
                                                            
                        if (selectedLocation) {
                            $(targetSelector).val(selectedLocation);
                        }
                    } else {
                        $(targetSelector).append('<option value="">No Locations available</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading locations:', error);
                    $(targetSelector).html('<option value="">Error loading locations</option>');
                }
            });
        }

        // Load OS list
        function loadOSList(targetSelector, selectedOS = null) {
            $(targetSelector).html('<option value="">Loading OS...</option>');
                            
            $.ajax({
                url: base_url + 'TransPC/getOSList',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $(targetSelector).empty();
                    $(targetSelector).append('<option value="">--Select OS--</option>');
                                            
                    if (response && response.length > 0) {
                        response.forEach(os => {
                            const selected = (selectedOS && os.mpo_osname === selectedOS) ? 'selected' : '';
                            $(targetSelector).append(
                                `<option value="${os.mpo_osname}" ${selected}>${os.mpo_osname}</option>`
                            );
                        });
                                                            
                        if (selectedOS) {
                            $(targetSelector).val(selectedOS);
                        }
                    } else {
                        $(targetSelector).append('<option value="">No OS available</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading OS list:', error);
                    $(targetSelector).html('<option value="">Error loading OS</option>');
                }
            });
        }

        // Load PC details function
       function loadPCDetails(pcId, callback) {
            // Validate PC ID before making the request
            if (!pcId || pcId === 'null' || pcId === 'undefined') {
                console.error('Invalid PC ID provided to loadPCDetails:', pcId);
                $('#pc_specs_content').html(`
                    <div class="alert alert-danger text-center">
                        <i class="fa fa-exclamation-triangle me-2"></i>Error: PC ID is missing
                    </div>
                `);
                $('#pc_equipment_content').html(`
                    <div class="alert alert-danger text-center">
                        <i class="fa fa-exclamation-triangle me-2"></i>Error: PC ID is missing
                    </div>
                `);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid PC ID',
                    text: 'PC ID is missing. Please close this modal and try again.',
                    showConfirmButton: true
                });
                return;
            }
            
            // Clear any existing timeout
            if (window.loadPCDetailsTimeout) {
                clearTimeout(window.loadPCDetailsTimeout);
            }
            
            
            $.ajax({
                url: base_url + 'TransPC/getPCDetails',
                type: 'GET',
                data: { id: pcId },
                dataType: 'json',
                timeout: 30000,
                beforeSend: function() {
                    $('#pc_specs_content').html(`
                        <div class="alert alert-info text-center">
                            <i class="fa fa-spinner fa-spin me-2"></i>Loading specifications...
                        </div>
                    `);
                    $('#pc_equipment_content').html(`
                        <div class="alert alert-info text-center">
                            <i class="fa fa-spinner fa-spin me-2"></i>Loading equipment...
                        </div>
                    `);
                    // Only show loading for VM section if it's visible (server type)
                    if ($('#server_vm_section').is(':visible')) {
                        $('#server_vm_content').html(`
                            <div class="alert alert-info text-center">
                                <i class="fa fa-spinner fa-spin me-2"></i>Loading VM data...
                            </div>
                        `);
                    }
                },
                success: function(response) {
                    
                    try {
                        // Handle error responses from server
                        if (response.status === false) {
                            throw new Error(response.message || 'Server returned error status');
                        }
                        
                        // Check if response has the expected structure
                        if (!response || (!response.data && !response.pc)) {
                            throw new Error('Empty or invalid response received');
                        }
                        
                        // Handle different response formats
                        let data;
                        if (response.status && response.data) {
                            data = response.data;
                        } else if (response.pc || response.specs || response.equipment || response.servervm) {
                            data = response;
                        } else {
                            throw new Error('Invalid response structure');
                        }
                        
                        const pc = data.pc || {};
                        const specs = data.specs || null;
                        const equipment = data.equipment || [];
                        const serverVM = data.servervm || [];
                        
                        if (!pc.tpc_id) {
                            throw new Error('Invalid PC data - missing ID');
                        }
                        
                        const pcName = pc.tpc_name ? pc.tpc_name : `PC ID:${pc.tpc_id}`;
                        $('#pcDetailModalLabel').text(`PC Details - ${pcName}`);
                        
                        currentPCSpecs = specs;
                        currentPCType = pc.tpc_type;
                        
                        // Handle Server VM section
                        if (pc.tpc_type == 2) {
                            $('#server_vm_section').show();
                            // Load VM data immediately after showing the section
                            loadServerVMData(serverVM);
                        } else {
                            $('#server_vm_section').hide();
                        }
                        
                        $('#manageSpecsBtn').show();
                        
                        // Update specs content (same as before)
                        if (specs && specs.tps_id) {
                            $('#manageSpecsBtn').removeClass('btn-outline-primary').addClass('btn-outline-warning');
                            $('#specsButtonText').text('Edit Specs');
                            
                            $('#pc_specs_content').html(`
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-bordered specs-table">
                                            <tr><th>Processor:</th><td>${specs.tps_processor || '-'}</td></tr>
                                            <tr><th>RAM:</th><td>${specs.tps_ram || '-'}</td></tr>
                                            <tr><th>Storage:</th><td>${specs.tps_storage || '-'}</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-bordered specs-table">
                                            <tr><th>VGA:</th><td>${specs.tps_vga || '-'}</td></tr>
                                            <tr><th>Ethernet:</th><td>${specs.tps_ethernet || '-'}</td></tr>
                                        </table>
                                    </div>
                                </div>
                            `);
                        } else {
                            $('#manageSpecsBtn').removeClass('btn-outline-warning').addClass('btn-outline-primary');
                            $('#specsButtonText').text('Add Specs');
                            
                            $('#pc_specs_content').html(`
                                <div class="alert alert-info text-center">
                                    <i class="fa fa-info-circle me-2"></i>No specifications data available
                                </div>
                            `);
                        }

                        // Update equipment content (same as before)
                        if (equipment && Array.isArray(equipment) && equipment.length > 0) {
                            let equipmentHtml = '<div class="row">';
                            
                            equipment.forEach(item => {
                                const typeIcon = getEquipmentIcon(item.tpi_type);
                                const typeName = getEquipmentTypeDisplay(item.tpi_type);
                                const age = item.tpi_receivedate ? calculateAge(item.tpi_receivedate) : '-';
                                
                                equipmentHtml += `
                                    <div class="col-md-6 mb-3">
                                        <div class="equipment-card">
                                            <div class="equipment-actions">
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-warning edit-equipment-btn" data-id="${item.tpi_id}" title="Edit Equipment">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger delete-equipment-btn" data-id="${item.tpi_id}" title="Delete Equipment">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="equipment-header">
                                                <div class="equipment-icon">
                                                    <i class="${typeIcon}"></i>
                                                </div>
                                                <div>
                                                    <div class="equipment-title">${typeName}</div>
                                                    <div class="equipment-subtitle">ID: ${item.tpi_id}</div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Asset No:</small>
                                                    <strong>${item.tpi_assetno || '-'}</strong>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Age:</small>
                                                    <strong>${age}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            
                            equipmentHtml += '</div>';
                            $('#pc_equipment_content').html(equipmentHtml);
                        } else {
                            $('#pc_equipment_content').html(`
                                <div class="alert alert-info text-center">
                                    <i class="fa fa-info-circle me-2"></i>No equipment data available for this PC
                                </div>
                            `);
                        }
                        
                        if (typeof callback === 'function') {
                            callback();
                        }
                        
                    } catch (error) {
                        console.error('Error processing PC details response:', error);
                        console.error('Response data:', response);
                        
                        const errorMessage = error.message || 'Unknown error processing data';
                        
                        $('#pc_specs_content').html(`
                            <div class="alert alert-danger text-center">
                                <i class="fa fa-exclamation-triangle me-2"></i>Error: ${errorMessage}
                            </div>
                        `);
                        $('#pc_equipment_content').html(`
                            <div class="alert alert-danger text-center">
                                <i class="fa fa-exclamation-triangle me-2"></i>Error: ${errorMessage}
                            </div>
                        `);
                        
                        if ($('#server_vm_section').is(':visible')) {
                            $('#server_vm_content').html(`
                                <div class="alert alert-danger text-center">
                                    <i class="fa fa-exclamation-triangle me-2"></i>Error: ${errorMessage}
                                </div>
                            `);
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Processing Error',
                            html: `
                                <p>Failed to process PC details data.</p>
                                <p><strong>Error:</strong> ${errorMessage}</p>
                                <p><small>Please check the console for more details.</small></p>
                            `,
                            showConfirmButton: true
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error loading PC details:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    
                    let errorMessage = 'Failed to load PC details';
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMessage = errorResponse.message;
                        }
                    } catch (e) {
                        // Use default message if parsing fails
                    }
                    
                    $('#pc_specs_content').html(`
                        <div class="alert alert-danger text-center">
                            <i class="fa fa-exclamation-triangle me-2"></i>Network Error: ${errorMessage}
                        </div>
                    `);
                    $('#pc_equipment_content').html(`
                        <div class="alert alert-danger text-center">
                            <i class="fa fa-exclamation-triangle me-2"></i>Network Error: ${errorMessage}
                        </div>
                    `);
                    
                    if ($('#server_vm_section').is(':visible')) {
                        $('#server_vm_content').html(`
                            <div class="alert alert-danger text-center">
                                <i class="fa fa-exclamation-triangle me-2"></i>Network Error: ${errorMessage}
                            </div>
                        `);
                    }
                    
                    if (xhr.status !== 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            html: `
                                <p>${errorMessage}</p>
                                <p><strong>Status:</strong> ${xhr.status}</p>
                                <p><small>Please try again or contact support if the problem persists.</small></p>
                            `,
                            showConfirmButton: true
                        });
                    }
                }
            });
        }
        
        // Form validation function
        function validateForm(formId) {
            let isValid = true;
            const form = document.getElementById(formId);
                    
            form.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    
            const prefix = formId === 'editPCForm' ? 'edit_' : '';
            
            // Validate PC Type (required)
            const pcTypeField = form.querySelector(`#${prefix}pc_type`);
            if (pcTypeField && (pcTypeField.value === '' || pcTypeField.value === null)) {
                document.getElementById(`${prefix}pc_type_error`).textContent = 'PC Type is required';
                pcTypeField.classList.add('is-invalid');
                isValid = false;
            }
                    
            // Validate PC Receive Date (required)
            const pcReceiveDateField = form.querySelector(`#${prefix}pc_receive_date`);
            if (pcReceiveDateField && !pcReceiveDateField.value.trim()) {
                document.getElementById(`${prefix}pc_receive_date_error`).textContent = 'PC Receive Date is required';
                pcReceiveDateField.classList.add('is-invalid');
                isValid = false;
            }

            // Validate Status (required)
            const statusField = form.querySelector(`#${prefix}pc_status`);
            if (statusField && statusField.value === '') {
                document.getElementById(`${prefix}pc_status_error`).textContent = 'Status is required';
                statusField.classList.add('is-invalid');
                isValid = false;
            }

            // Validate IP format if provided
            const ipField = form.querySelector(`#${prefix}ip_address`);
            const ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            
            if (ipField && ipField.value.trim() && !ipRegex.test(ipField.value.trim())) {
                document.getElementById(`${prefix}ip_address_error`).textContent = 'Please type a valid IP address';
                ipField.classList.add('is-invalid');
                isValid = false;
            }
                    
            return isValid;
        }

        // Equipment validation
        function validateEquipmentForm() {
            let isValid = true;
            
            // Clear previous errors
            $('#pcEquipmentForm .error-message').text('');
            $('#pcEquipmentForm .is-invalid').removeClass('is-invalid');
            
            // Validate equipment type
            const equipmentType = $('#equipment_type').val();
            if (!equipmentType) {
                $('#equipment_type_error').text('Equipment Type is required');
                $('#equipment_type').addClass('is-invalid');
                isValid = false;
            }
            
            return isValid;
        }

        // Server VM validation
        function validateServerVMForm() {
            let isValid = true;
            
            // Clear previous errors
            $('#pcServerVMForm .error-message').text('');
            $('#pcServerVMForm .is-invalid').removeClass('is-invalid');
            
            // Validate VM name (required instead of type)
            const vmName = $('#vm_name').val();
            if (!vmName || vmName.trim() === '') {
                $('#vm_name_error').text('VM Name is required');
                $('#vm_name').addClass('is-invalid');
                isValid = false;
            }

            // Validate IP format if provided
            const ipField = $('#vm_ip_address');
            const ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            
            if (ipField.val().trim() && !ipRegex.test(ipField.val().trim())) {
                $('#vm_ip_address_error').text('Please enter a valid IP address');
                ipField.addClass('is-invalid');
                isValid = false;
            }
            
            return isValid;
        }

        // Initialize DataTable on page load
        initializePCDataTable('All', 'All');

        // Load dropdowns when modals are shown
        $('#addPCModal').on('show.bs.modal', function () {
            loadLocations('#location');
            loadOSList('#os_name');
        });
        
        // Asset search button click handlers
        $('.search-asset-btn, .search-equipment-asset-btn').off('click').on('click', function() {
            const isEquipmentSearch = $(this).hasClass('search-equipment-asset-btn');
            
            if (isEquipmentSearch) {
                // Equipment asset search
                isSearchModalOpen = true;
                
                // Save equipment form data
                savedFormData.equipment = {
                    pc_id: $('#equipment_pc_id').val(),
                    equipment_id: $('#equipment_id').val(),
                    equipment_type: $('#equipment_type').val(),
                    asset_no: $('#equipment_asset_no').val(),
                    receive_date: $('#equipment_receive_date').val()
                };
                
                previousModal = '#pcEquipmentModal';
                
                $('#pcEquipmentModal').modal('hide');
                $('#pcEquipmentModal').one('hidden.bs.modal', function() {
                    $('#assetNoModal').modal('show');
                    initAssetNoDataTable();
                });
            } else {
                // PC asset search (existing logic)
                isEditMode = $('#editPCModal').is(':visible');
                isSearchModalOpen = true;
                
                // Store which modal was open before search
                if ($('#addPCModal').hasClass('show')) {
                    previousModal = '#addPCModal';
                } else if ($('#editPCModal').hasClass('show')) {
                    previousModal = '#editPCModal';
                }
                
                // Save current form data before hiding modal
                if (previousModal) {
                    savedFormData = saveFormData();
                    
                    // Hide the current modal
                    $(previousModal).modal('hide');
                    
                    // Wait for the modal to be completely hidden before showing the search modal
                    $(previousModal).one('hidden.bs.modal', function() {
                        $('#assetNoModal').modal('show');
                    });
                } else {
                    $('#assetNoModal').modal('show');
                }
                
                initAssetNoDataTable();
            }
        });

        // User search button click handlers
        $('.search-user-btn').off('click').on('click', function() {
            isEditMode = $('#editPCModal').is(':visible');
            isSearchModalOpen = true;
            
            // Store which modal was open before search
            if ($('#addPCModal').hasClass('show')) {
                previousModal = '#addPCModal';
            } else if ($('#editPCModal').hasClass('show')) {
                previousModal = '#editPCModal';
            }
            
            // Save current form data before hiding modal
            if (previousModal) {
                savedFormData = saveFormData();
                
                // Hide the current modal
                $(previousModal).modal('hide');
                
                // Wait for the modal to be completely hidden before showing the search modal
                $(previousModal).one('hidden.bs.modal', function() {
                    $('#employeeModal').modal('show');
                });
            } else {
                $('#employeeModal').modal('show');
            }
            
            initEmployeeDataTable();
        });

        // IP Address search button click handlers
        $('.search-ip-btn, .search-vm-ip-btn').off('click').on('click', function() {
            const isVMIPSearch = $(this).hasClass('search-vm-ip-btn');
            
            if (isVMIPSearch) {
                // VM IP search
                isSearchModalOpen = true;
                
                // Save VM form data
                savedFormData.vm = {
                    pc_id: $('#vm_pc_id').val(),
                    vm_id: $('#vm_id').val(),
                    vm_type: $('#vm_type').val(),
                    vm_ip_address: $('#vm_ip_address').val(),
                    vm_services: $('#vm_services').val(),
                    vm_remark: $('#vm_remark').val()
                };
                
                previousModal = '#pcServerVMModal';
                
                $('#pcServerVMModal').modal('hide');
                $('#pcServerVMModal').one('hidden.bs.modal', function() {
                    $('#ipAddressModal').modal('show');
                    initIPAddressDataTable();
                });
            } else {
                // PC IP search
                isEditMode = $('#editPCModal').is(':visible');
                isSearchModalOpen = true;
                
                // Store which modal was open before search
                if ($('#addPCModal').hasClass('show')) {
                    previousModal = '#addPCModal';
                } else if ($('#editPCModal').hasClass('show')) {
                    previousModal = '#editPCModal';
                }
                
                // Save current form data before hiding modal
                if (previousModal) {
                    savedFormData = saveFormData();
                    
                    // Hide the current modal
                    $(previousModal).modal('hide');
                    
                    // Wait for the modal to be completely hidden before showing the search modal
                    $(previousModal).one('hidden.bs.modal', function() {
                        $('#ipAddressModal').modal('show');
                    });
                } else {
                    $('#ipAddressModal').modal('show');
                }
                
                initIPAddressDataTable();
            }
        });
        
        // Auto-fill asset details when asset no is entered manually
        $('#pc_assetno, #edit_pc_assetno').on('blur', function() {
            const assetNo = $(this).val().trim();
            const isEditMode = $(this).attr('id').startsWith('edit_');
            
            if (assetNo) {
                $.ajax({
                    url: base_url + 'TransPC/getAssetNo',
                    type: 'GET',
                    data: { assetNo: assetNo },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data) {
                            // Use correct field name from API response
                            const receiveDate = response.data.receive_date || '';
                            
                            if (isEditMode) {
                                $('#edit_pc_receive_date').val(receiveDate);
                                // Trigger age calculation for edit mode
                                if (receiveDate) {
                                    $('#edit_pc_receive_date').trigger('change');
                                }
                            } else {
                                $('#pc_receive_date').val(receiveDate);
                                // Trigger age calculation for add mode
                                if (receiveDate) {
                                    $('#pc_receive_date').trigger('change');
                                }
                            }
                            
                            // Display equipment ID
                            displayEquipmentID(response.data, isEditMode);
                        } else {
                            // Clear displays and show error
                            const equipmentDisplayId = isEditMode ? 'edit_equipment_id_display' : 'equipment_id_display';
                            $(`#${equipmentDisplayId}`).remove();
                            
                            const ageDisplayId = isEditMode ? 'edit_pc_age_display' : 'pc_age_display';
                            $(`#${ageDisplayId}`).remove();
                            
                            // Show error message
                            Swal.fire({
                                icon: 'warning',
                                title: 'Asset Not Available',
                                text: 'This asset is not found or already in use by another PC.',
                                showConfirmButton: true
                            });
                            
                            // Clear the field
                            $(this).val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching asset details:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error checking asset availability. Please try again.',
                            showConfirmButton: true
                        });
                    }
                });
            } else {
                // Clear equipment ID display if asset no is empty
                const equipmentDisplayId = isEditMode ? 'edit_equipment_id_display' : 'equipment_id_display';
                $(`#${equipmentDisplayId}`).remove();
                
                // Clear age display if asset no is empty
                const ageDisplayId = isEditMode ? 'edit_pc_age_display' : 'pc_age_display';
                $(`#${ageDisplayId}`).remove();
            }
        });

        // Auto-fill equipment asset details when asset no is entered
        $('#equipment_asset_no').on('blur', function() {
            const assetNo = $(this).val().trim();
            
            if (assetNo) {
                $.ajax({
                    url: base_url + 'TransPC/getAssetNo',
                    type: 'GET',
                    data: { assetNo: assetNo },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data) {
                            // Use correct field name from API response
                            const receiveDate = response.data.receive_date || '';
                            $('#equipment_receive_date').val(receiveDate);
                            
                            // Display equipment ID and trigger age calculation
                            if (response.data.equipment_id) {
                                displayEquipmentIDForEquipment(response.data.equipment_id);
                            }
                            
                            // Trigger age calculation
                            if (receiveDate) {
                                $('#equipment_receive_date').trigger('change');
                            }
                        } else {
                            // Clear displays and show error
                            $('#equipment_equipment_id_display').remove();
                            $('#equipment_age_display').remove();
                            
                            // Show error message
                            Swal.fire({
                                icon: 'warning',
                                title: 'Asset Not Available',
                                text: 'This asset is not found or already in use.',
                                showConfirmButton: true
                            });
                            
                            // Clear the field
                            $(this).val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching asset details:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error checking asset availability. Please try again.',
                            showConfirmButton: true
                        });
                    }
                });
            } else {
                // Clear displays if asset no is empty
                $('#equipment_equipment_id_display').remove();
                $('#equipment_age_display').remove();
            }
        });

        // Handle manual employee input for user field
        $('#user, #edit_user').on('blur', function() {
            const userInput = $(this).val().trim();
            if (userInput && !isNaN(userInput)) {
                // If it's just a number, try to get employee details
                $.ajax({
                    url: base_url + 'TransPC/getEmployees',
                    type: 'GET',
                    data: { employeeId: userInput },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data) {
                            const userValue = `${response.data.em_emplcode} - ${response.data.em_emplname}`;
                            $('#user, #edit_user').filter(function() {
                                return $(this).val() === userInput;
                            }).val(userValue);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching employee details:', error);
                    }
                });
            }
        });
        
        // Age display for PC receive date
        $('#pc_receive_date, #edit_pc_receive_date').on('change', function() {
            const receiveDate = $(this).val();
            const isEditMode = $(this).attr('id').startsWith('edit_');
            
            if (receiveDate) {
                const age = calculateAge(receiveDate);
                
                // Create or update age display
                const ageDisplayId = isEditMode ? 'edit_pc_age_display' : 'pc_age_display';
                let ageDisplay = $(`#${ageDisplayId}`);
                
                if (ageDisplay.length === 0) {
                    // Create simple age display
                    const targetElement = isEditMode ? $('#edit_pc_receive_date') : $('#pc_receive_date');
                    targetElement.parent().append(`
                        <div class="mt-1">
                            <small class="text-muted" id="${ageDisplayId}">Age: ${age}</small>
                        </div>
                    `);
                } else {
                    // Update existing age display
                    ageDisplay.text(`Age: ${age}`);
                }
            } else {
                // Clear age display if no date
                const ageDisplayId = isEditMode ? 'edit_pc_age_display' : 'pc_age_display';
                $(`#${ageDisplayId}`).remove();
            }
        });

        // Age calculation for equipment receive date
        $('#equipment_receive_date').on('change', function() {
            const receiveDate = $(this).val();
            
            if (receiveDate) {
                const age = calculateAge(receiveDate);
                
                // Create or update age display
                let ageDisplay = $('#equipment_age_display');
                
                if (ageDisplay.length === 0) {
                    // Create simple age display
                    $(this).parent().append(`
                        <div class="mt-1">
                            <small class="text-muted" id="equipment_age_display">Age: ${age}</small>
                        </div>
                    `);
                } else {
                    // Update existing age display
                    ageDisplay.text(`Age: ${age}`);
                }
            } else {
                // Clear age display if no date
                $('#equipment_age_display').remove();
            }
        });
        
        // Handle IP Address blur - mark as used (1)
        $('#ip_address, #edit_ip_address').on('blur', function() {
            const ipInput = $(this).val().trim();
            const fieldId = $(this).attr('id');
            const errorElementId = `${fieldId}_error`;
            
            if (ipInput) {
                // Cek format IP dulu
                const ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
                if (!ipRegex.test(ipInput)) {
                    $(`#${errorElementId}`).text('Invalid IP address format');
                    $(`#${fieldId}`).addClass('is-invalid');
                    return;
                }

                // Cek ketersediaan di server
                $.ajax({
                    url: base_url + 'TransPC/getIPAddresses',
                    type: 'GET',
                    data: { ipAddress: ipInput },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data) {
                            // IP tersedia
                            $(`#${errorElementId}`).text('');
                            $(`#${fieldId}`).removeClass('is-invalid');
                        } else {
                            // IP tidak tersedia
                            $(`#${errorElementId}`).text('IP Address not available');
                            $(`#${fieldId}`).addClass('is-invalid');
                        }
                    },
                    error: function(xhr, status, error) {
                        $(`#${errorElementId}`).text('Error checking IP');
                        $(`#${fieldId}`).addClass('is-invalid');
                    }
                });
            } else {
                // Reset jika field kosong
                $(`#${errorElementId}`).text('');
                $(`#${fieldId}`).removeClass('is-invalid');
            }
        });

        // VM IP Address validation (similar to PC IP)
        $('#vm_ip_address').on('blur', function() {
            const ipInput = $(this).val().trim();
            const fieldId = $(this).attr('id');
            const errorElementId = `${fieldId}_error`;
            
            if (ipInput) {
                // Validate IP format first
                const ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
                
                if (!ipRegex.test(ipInput)) {
                    $(`#${errorElementId}`).text('Please enter a valid IP address');
                    $(`#${fieldId}`).addClass('is-invalid');
                    return;
                }
                
                // Check if IP address is available (status 0)
                $.ajax({
                    url: base_url + 'TransPC/getIPAddresses',
                    type: 'GET',
                    data: { ipAddress: ipInput },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data) {
                            // IP is available, mark it as used (1)
                            $.ajax({
                                url: base_url + 'TransPC/updateIPStatus',
                                type: 'POST',
                                data: { 
                                    ipAddress: ipInput,
                                    status: 1 // Mark as used
                                },
                                dataType: 'json',
                                success: function(updateResponse) {
                                    if (updateResponse.status) {
                                        $(`#${errorElementId}`).text('');
                                        $(`#${fieldId}`).removeClass('is-invalid');
                                    } else {
                                        $(`#${errorElementId}`).text('Failed to update IP status');
                                        $(`#${fieldId}`).addClass('is-invalid');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error updating IP status:', error);
                                    $(`#${errorElementId}`).text('Error updating IP status');
                                    $(`#${fieldId}`).addClass('is-invalid');
                                }
                            });
                        } else {
                            // IP not found or already in use
                            $(`#${errorElementId}`).text('IP Address not available');
                            $(`#${fieldId}`).addClass('is-invalid');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error checking IP address:', error);
                        $(`#${errorElementId}`).text('Error checking IP address');
                        $(`#${fieldId}`).addClass('is-invalid');
                    }
                });
            } else {
                // Clear error if field is empty
                $(`#${errorElementId}`).text('');
                $(`#${fieldId}`).removeClass('is-invalid');
            }
        });
        
        // Edit button click handler
        $('#tabelPC').on('click', '.edit-btn', function() {
            const id = $(this).data('id');

            //reset form
            document.getElementById('editPCForm').reset();
            $('#editPCModalLabel').text('Edit PC Data');
            $('#update-btn').show();
            $('.modal-footer .btn-secondary').text('Cancel');
            $('#edit_tpc_id').val(id);
            $('#editPCModal').modal('show');
            
            // Get PC data
            $.ajax({
                url: base_url + 'TransPC/getPCById',
                type: 'GET',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        const pc = response.data;
                        
                        // Fill form fields
                        $('#edit_pc_type').val(pc.tpc_type);
                        $('#edit_pc_name').val(pc.tpc_name);
                        $('#edit_pc_assetno').val(pc.tpc_assetno);
                        $('#edit_pc_receive_date').val(pc.tpc_pcreceivedate ? pc.tpc_pcreceivedate.split(' ')[0] : '');
                        $('#edit_pc_status').val(pc.tpc_status);
                        $('#edit_ip_address').val(pc.tpc_ipaddress);
                        $('#edit_user').val(pc.tpc_user);
                        
                        // Load dropdown options
                        loadLocations('#edit_location', pc.tpc_location);
                        loadOSList('#edit_os_name', pc.tpc_osname);
                        
                        // Trigger age calculation with a slight delay to ensure form is fully populated
                        if (pc.tpc_pcreceivedate) {
                            setTimeout(function() {
                                $('#edit_pc_receive_date').trigger('change');
                            }, 100);
                        }
                        
                        // Get and display equipment ID if asset no exists - with delay to ensure form is ready
                        if (pc.tpc_assetno) {
                            setTimeout(function() {
                                $.ajax({
                                    url: base_url + 'TransPC/getAssetNo',
                                    type: 'GET',
                                    data: { assetNo: pc.tpc_assetno },
                                    dataType: 'json',
                                    success: function(assetResponse) {
                                        if (assetResponse.status && assetResponse.data && assetResponse.data.equipment_id) {
                                            displayEquipmentID(assetResponse.data, true);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error fetching asset details for equipment ID:', error);
                                    }
                                });
                            }, 200);
                        }
                        
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to load PC details',
                        });
                        $('#editPCModal').modal('hide');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching PC details:', error);
                    $('#editPCModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load PC details. Please try again.'
                    });
                }
            });
        });

        // Delete button click handler
        $('#tabelPC').on('click', '.delete-btn', function() {
            const id = $(this).data('id');
                            
            Swal.fire({
                title: 'Are you sure?',
                text: "This PC will be marked as deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: base_url + 'TransPC/delete',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                                                                
                                tabelPC.ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete PC',
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting data:', xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting data. Please try again.',
                            });
                        }
                    });
                }
            });
        });

        // Detail button click handler (modified to store PC ID)
        $('#tabelPC').off('click', '.detail-btn').on('click', '.detail-btn', function() {
            const id = $(this).data('id');
            
            // Set the global PC ID
            currentPCId = id;
            
            // Reset flags
            isFromPCDetailsModal = false;
            shouldReturnToPCDetails = false;
            
            // Show loading state
            $('#pcDetailModal').modal('show');
            $('#pcDetailModalLabel').text(`PC Details - Loading...`);
            
            // Load initial details
            loadPCDetails(id);
        });

        // Add PC form submission
        $('#submit-btn').on('click', function() {
            if (!validateForm('addPCForm')) {
                return;
            }
                            
            const formData = new FormData(document.getElementById('addPCForm'));
                            
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
                            
            $.ajax({
                url: base_url + 'TransPC/store',
                type: 'POST',
                data: Object.fromEntries(formData),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                                                                        
                        document.getElementById('addPCForm').reset();
                        $('#addPCModal').modal('hide');
                                                                        
                        tabelPC.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to create PC'
                        });
                    }
                                                            
                    $('#submit-btn').prop('disabled', false).text('Submit PC');
                },
                error: function(xhr, status, error) {
                    console.error('Error creating PC:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while creating the PC. Please try again.'
                    });
                                                            
                    $('#submit-btn').prop('disabled', false).text('Submit PC');
                }
            });
        });

        // Update PC form submission
        $('#update-btn').on('click', function() {
            if (!validateForm('editPCForm')) {
                return;
            }
                            
            const formData = new FormData(document.getElementById('editPCForm'));
                            
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                            
            $.ajax({
                url: base_url + 'TransPC/update',
                type: 'POST',
                data: Object.fromEntries(formData),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                                                                        
                        $('#editPCModal').modal('hide');
                                                                        
                        tabelPC.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update PC'
                        });
                    }
                                                            
                    $('#update-btn').prop('disabled', false).text('Update PC');
                },
                error: function(xhr, status, error) {
                    console.error('Error updating PC:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the PC. Please try again.'
                    });
                                                            
                    $('#update-btn').prop('disabled', false).text('Update PC');
                }
            });
        });
        
        // PC Specifications Management - Only Edit Mode
        $('#manageSpecsBtn').off('click').on('click', function() {
            // Ensure we have a valid PC ID
            const pcId = currentPCId;
            
            if (!pcId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PC ID is missing. Please close this modal and try again.',
                    showConfirmButton: true
                });
                return;
            }
            
            isFromPCDetailsModal = true;
            shouldReturnToPCDetails = true;
            
            isSpecsEditMode = currentPCSpecs !== null && currentPCSpecs !== undefined;
            
            if (isSpecsEditMode) {
                $('#pcSpecsModalLabel').text('Edit PC Specifications');
                $('#submitSpecsBtn').text('Update Specifications');
            } else {
                $('#pcSpecsModalLabel').text('Add PC Specifications');
                $('#submitSpecsBtn').text('Add Specifications');
            }
            
            // Set PC ID in the form
            $('#specs_pc_id').val(pcId);
            $('#specs_id').val(isSpecsEditMode ? currentPCSpecs.tps_id : '');
            
            if (isSpecsEditMode) {
                $('#processor').val(currentPCSpecs.tps_processor || '');
                $('#ram').val(currentPCSpecs.tps_ram || '');
                $('#storage').val(currentPCSpecs.tps_storage || '');
                $('#vga').val(currentPCSpecs.tps_vga || '');
                $('#ethernet').val(currentPCSpecs.tps_ethernet || '');
            } else {
                $('#processor').val('');
                $('#ram').val('');
                $('#storage').val('');
                $('#vga').val('');
                $('#ethernet').val('');
            }
            
            $('#pcDetailModal').modal('hide');
            $('#pcDetailModal').one('hidden.bs.modal', function() {
                $('#pcSpecsModal').modal('show');
            });
        });

        // Submit PC Specifications
        $('#submitSpecsBtn').off('click').on('click', function() {
            // Ensure we have a valid PC ID
            const pcId = $('#specs_pc_id').val() || currentPCId;
            
            if (!pcId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PC ID is missing. Please close this modal and try again.',
                    showConfirmButton: true
                });
                return;
            }
            
            const formData = new FormData(document.getElementById('pcSpecsForm'));
            
            // Ensure PC ID is set in form data
            formData.set('pc_id', pcId);
            
            const url = base_url + 'TransPC/updatePCSpecs';
            
            $(this).prop('disabled', true).text('Saving...');

            $.ajax({
                url: url,
                type: 'POST',
                data: Object.fromEntries(formData),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        $('#pcSpecsModal').modal('hide');
                        
                        // FIXED: Ensure PC ID is preserved when returning to PC Details
                        $('#pcSpecsModal').one('hidden.bs.modal', function() {
                            // Store the PC ID before any operations
                            const preservedPCId = pcId;
                            currentPCId = preservedPCId; // Ensure global variable is set
                            
                            setTimeout(function() {
                                $('#pcDetailModal').modal('show');
                                $('#pcDetailModalLabel').text('PC Details - Loading...');
                                
                                // Load fresh data after modal is visible
                                setTimeout(function() {
                                    loadPCDetails(preservedPCId, function() {
                                    });
                                    isFromPCDetailsModal = false;
                                    shouldReturnToPCDetails = false;
                                }, 100);
                            }, 100);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to save specifications',
                            showConfirmButton: true
                        });
                    }
                    
                    const buttonText = isSpecsEditMode ? 'Update Specifications' : 'Add Specifications';
                    $('#submitSpecsBtn').prop('disabled', false).text(buttonText);
                },
                error: function(xhr, status, error) {
                    console.error('Error saving PC specs:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving specifications. Please try again.',
                        showConfirmButton: true
                    });
                    
                    const buttonText = isSpecsEditMode ? 'Update Specifications' : 'Add Specifications';
                    $('#submitSpecsBtn').prop('disabled', false).text(buttonText);
                }
            });
        });

        // Add Equipment Button
        $('#addEquipmentBtn').off('click').on('click', function() {
            // Ensure we have a valid PC ID
            const pcId = currentPCId;
            
            if (!pcId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PC ID is missing. Please close this modal and try again.',
                    showConfirmButton: true
                });
                return;
            }
            
            isFromPCDetailsModal = true;
            shouldReturnToPCDetails = true;
            
            document.getElementById('pcEquipmentForm').reset();
            $('#equipment_pc_id').val(pcId);
            $('#equipment_id').val('');
            
            $('#pcEquipmentModalLabel').text('Add New IT Equipment');
            $('#submitEquipmentBtn').text('Submit Equipment');
            
            $('#pcDetailModal').modal('hide');
            $('#pcDetailModal').one('hidden.bs.modal', function() {
                $('#pcEquipmentModal').modal('show');
            });
        });

        // Submit PC Equipment
        $('#submitEquipmentBtn').off('click').on('click', function() {
            if (!validateEquipmentForm()) {
                return;
            }
            
            // Ensure we have a valid PC ID
            const pcId = $('#equipment_pc_id').val() || currentPCId;
            if (!pcId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PC ID is missing. Please close this modal and try again.',
                    showConfirmButton: true
                });
                return;
            }
            
            const formData = new FormData(document.getElementById('pcEquipmentForm'));
            
            // Ensure PC ID is set in form data
            formData.set('pc_id', pcId);
            
            const isEditMode = $('#equipment_id').val() !== '';
            const url = isEditMode ? 
                base_url + 'TransPC/updatePCEquipment' : 
                base_url + 'TransPC/storePCEquipment';
            
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            
            $.ajax({
                url: url,
                type: 'POST',
                data: Object.fromEntries(formData),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        $('#pcEquipmentModal').modal('hide');
                        
                        // FIXED: Ensure PC ID is preserved when returning to PC Details
                        $('#pcEquipmentModal').one('hidden.bs.modal', function() {
                            // Store the PC ID before any operations
                            const preservedPCId = pcId;
                            currentPCId = preservedPCId; // Ensure global variable is set
                            setTimeout(function() {
                                $('#pcDetailModal').modal('show');
                                $('#pcDetailModalLabel').text('PC Details - Loading...');
                                
                                // Load fresh data after modal is visible
                                setTimeout(function() {
                                    loadPCDetails(preservedPCId, function() {
                                        console.log('PC Details loaded successfully after equipment update');
                                    });
                                    isFromPCDetailsModal = false;
                                    shouldReturnToPCDetails = false;
                                }, 100);
                            }, 100);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to save equipment'
                        });
                    }
                    
                    $('#submitEquipmentBtn').prop('disabled', false).text(isEditMode ? 'Update Equipment' : 'Save Equipment');
                },
                error: function(xhr, status, error) {
                    console.error('Error saving PC equipment:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving equipment. Please try again.'
                    });
                    
                    $('#submitEquipmentBtn').prop('disabled', false).text(isEditMode ? 'Update Equipment' : 'Save Equipment');
                }
            });
        });

        // Edit equipment button handler
        $(document).off('click', '.edit-equipment-btn').on('click', '.edit-equipment-btn', function() {
            const equipmentId = $(this).data('id');
            
            // Ensure we have a valid PC ID
            const pcId = currentPCId;
            
            if (!pcId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PC ID is missing. Please close this modal and try again.',
                    showConfirmButton: true
                });
                return;
            }
            
            isFromPCDetailsModal = true;
            shouldReturnToPCDetails = true;
            
            $.ajax({
                url: base_url + 'TransPC/getPCEquipmentById',
                type: 'GET',
                data: { id: equipmentId },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        const equipment = response.data;
                        
                        $('#equipment_pc_id').val(pcId);
                        $('#equipment_id').val(equipment.tpi_id);
                        $('#equipment_type').val(equipment.tpi_type);
                        $('#equipment_asset_no').val(equipment.tpi_assetno || '');
                        $('#equipment_receive_date').val(equipment.tpi_receivedate || '');
                        
                        if (equipment.tpi_assetno) {
                            $.ajax({
                                url: base_url + 'TransPC/getAssetNo',
                                type: 'GET',
                                data: { assetNo: equipment.tpi_assetno },
                                dataType: 'json',
                                success: function(assetResponse) {
                                    if (assetResponse.status && assetResponse.data && assetResponse.data.equipment_id) {
                                        displayEquipmentIDForEquipment(assetResponse.data.equipment_id);
                                    }
                                }
                            });
                        }
                        
                        if (equipment.tpi_receivedate) {
                            $('#equipment_receive_date').trigger('change');
                        }
                        
                        $('#pcEquipmentModalLabel').text('Edit IT Equipment');
                        $('#submitEquipmentBtn').text('Update Equipment');
                        
                        $('#pcDetailModal').modal('hide');
                        $('#pcDetailModal').one('hidden.bs.modal', function() {
                            $('#pcEquipmentModal').modal('show');
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load equipment details'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading equipment details:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load equipment details'
                    });
                }
            });
        });

        // Delete equipment button handler
        $(document).on('click', '.delete-equipment-btn', function() {
            const equipmentId = $(this).data('id');
            
            // Store PC ID before deletion
            const pcId = currentPCId;
            
            if (!pcId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PC ID is missing. Please close this modal and try again.',
                    showConfirmButton: true
                });
                return;
            }
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This equipment will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading indicator in the equipment section while deleting
                    $('#pc_equipment_content').html(`
                        <div class="alert alert-info text-center">
                            <i class="fa fa-spinner fa-spin me-2"></i>Deleting equipment...
                        </div>
                    `);
                    
                    $.ajax({
                        url: base_url + 'TransPC/deletePCEquipment',
                        type: 'POST',
                        data: { id: equipmentId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                
                                // Reload PC details while modal is still visible
                                console.log('Reloading PC Details after equipment deletion with ID:', pcId);
                                currentPCId = pcId; // Ensure global variable is preserved
                                
                                // Reload the PC details data immediately
                                loadPCDetails(pcId, function() {
                                    console.log('PC Details reloaded successfully after equipment deletion');
                                });
                                
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete equipment'
                                });
                                
                                // Restore the previous state if deletion failed
                                loadPCDetails(pcId);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting equipment:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting equipment'
                            });
                            
                            // Restore the previous state if deletion failed
                            loadPCDetails(pcId);
                        }
                    });
                }
            });
        });
        
        // Add Server VM Button
        $('#addServerVMBtn').off('click').on('click', function() {
            // Ensure we have a valid PC ID
            const pcId = currentPCId;
            
            if (!pcId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PC ID is missing. Please close this modal and try again.',
                    showConfirmButton: true
                });
                return;
            }
            
            isFromPCDetailsModal = true;
            shouldReturnToPCDetails = true;
            
            document.getElementById('pcServerVMForm').reset();
            $('#vm_pc_id').val(pcId);
            $('#vm_id').val('');
            
            $('#pcServerVMModalLabel').text('Add New Server VM');
            $('#submitServerVMBtn').text('Submit Server VM');
            
            $('#pcDetailModal').modal('hide');
            $('#pcDetailModal').one('hidden.bs.modal', function() {
                $('#pcServerVMModal').modal('show');
            });
        });

        // Submit Server VM
        $('#submitServerVMBtn').off('click').on('click', function() {
            if (!validateServerVMForm()) {
                return;
            }
            
            // Ensure we have a valid PC ID
            const pcId = $('#vm_pc_id').val() || currentPCId;
            
            if (!pcId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PC ID is missing. Please close this modal and try again.',
                    showConfirmButton: true
                });
                return;
            }
            
            const formData = new FormData(document.getElementById('pcServerVMForm'));
            
            // Ensure PC ID is set in form data
            formData.set('pc_id', pcId);
            
            const isEditMode = $('#vm_id').val() !== '';
            const url = isEditMode ? 
                base_url + 'TransPC/updatePCServerVM' : 
                base_url + 'TransPC/storePCServerVM';
            
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            
            $.ajax({
                url: url,
                type: 'POST',
                data: Object.fromEntries(formData),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        $('#pcServerVMModal').modal('hide');
                        
                        // Ensure PC ID is preserved when returning to PC Details
                        $('#pcServerVMModal').one('hidden.bs.modal', function() {
                            // Store the PC ID before any operations
                            const preservedPCId = pcId;
                            currentPCId = preservedPCId; // Ensure global variable is set
                            
                            setTimeout(function() {
                                $('#pcDetailModal').modal('show');
                                $('#pcDetailModalLabel').text('PC Details - Loading...');
                                
                                // Load fresh data after modal is visible
                                setTimeout(function() {
                                    loadPCDetails(preservedPCId, function() {
                                        console.log('PC Details loaded successfully after VM update');
                                    });
                                    isFromPCDetailsModal = false;
                                    shouldReturnToPCDetails = false;
                                }, 100);
                            }, 100);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to save Server VM'
                        });
                    }
                    
                    $('#submitServerVMBtn').prop('disabled', false).text(isEditMode ? 'Update VM' : 'Save VM');
                },
                error: function(xhr, status, error) {
                    console.error('Error saving Server VM:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving Server VM. Please try again.'
                    });
                    
                    $('#submitServerVMBtn').prop('disabled', false).text(isEditMode ? 'Update VM' : 'Save VM');
                }
            });
        });

        // Edit Server VM button handler
        $(document).off('click', '.edit-servervm-btn').on('click', '.edit-servervm-btn', function() {
            const vmId = $(this).data('id');
            
            // Ensure we have a valid PC ID
            const pcId = currentPCId;
            
            if (!pcId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PC ID is missing. Please close this modal and try again.',
                    showConfirmButton: true
                });
                return;
            }
            
            isFromPCDetailsModal = true;
            shouldReturnToPCDetails = true;
            
            $.ajax({
                url: base_url + 'TransPC/getPCServerVMById',
                type: 'GET',
                data: { id: vmId },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        const vm = response.data;
                        
                        $('#vm_pc_id').val(pcId); 
                        $('#vm_id').val(vm.tpv_id);
                        $('#vm_name').val(vm.tpv_name || '');
                        $('#vm_processor').val(vm.tpv_processor || '');
                        $('#vm_ram').val(vm.tpv_ram || '');
                        $('#vm_storage').val(vm.tpv_storage || '');
                        $('#vm_vga').val(vm.tpv_vga || '');
                        $('#vm_ethernet').val(vm.tpv_ethernet || '');
                        $('#vm_ip_address').val(vm.tpv_ipaddress || '');
                        $('#vm_services').val(vm.tpv_services || '');
                        $('#vm_remark').val(vm.tpv_remark || '');
                        
                        $('#pcServerVMModalLabel').text('Edit Server VM');
                        $('#submitServerVMBtn').text('Update Server VM');
                        
                        $('#pcDetailModal').modal('hide');
                        $('#pcDetailModal').one('hidden.bs.modal', function() {
                            $('#pcServerVMModal').modal('show');
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load Server VM details'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Server VM details:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load Server VM details'
                    });
                }
            });
        });

        // Delete Server VM button handler
        $(document).on('click', '.delete-servervm-btn', function() {
            const vmId = $(this).data('id');
            
            // Store PC ID before deletion
            const pcId = currentPCId;
            if (!pcId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PC ID is missing. Please close this modal and try again.',
                    showConfirmButton: true
                });
                return;
            }
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This Server VM will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading indicator in the VM section while deleting
                    if ($('#server_vm_section').is(':visible')) {
                        $('#server_vm_content').html(`
                            <div class="alert alert-info text-center">
                                <i class="fa fa-spinner fa-spin me-2"></i>Deleting VM...
                            </div>
                        `);
                    }
                    
                    $.ajax({
                        url: base_url + 'TransPC/deletePCServerVM',
                        type: 'POST',
                        data: { id: vmId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                
                                currentPCId = pcId; // Ensure global variable is preserved
                                
                                // Reload the PC details data immediately
                                loadPCDetails(pcId, function() {
                                    console.log('PC Details reloaded successfully after VM deletion');
                                });
                                
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete Server VM'
                                });
                                
                                // Restore the previous state if deletion failed
                                loadPCDetails(pcId);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting Server VM:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting Server VM'
                            });
                            
                            // Restore the previous state if deletion failed
                            loadPCDetails(pcId);
                        }
                    });
                }
            });
        });
        
        // Handle asset selection from DataTable
        $('#assetNoTable tbody').off('click', 'tr').on('click', 'tr', function() {
            if (assetNoDataTable) {
                const data = assetNoDataTable.row(this).data();
                if (!data) return;
                
                // Use the display_asset_no as the selected value
                const selectedAssetNo = data.display_asset_no || data.e_assetno;
                
                if (previousModal === '#pcEquipmentModal') {
                    // Equipment asset selection
                    savedFormData.equipment.asset_no = selectedAssetNo;
                    const assetDetails = {
                        e_equipmentid: data.e_equipmentid
                    };
                    
                    restoreEquipmentFormData(savedFormData.equipment, assetDetails);
                    // Use the correct field mapping from API response structure
                    savedFormData.equipment.receive_date = data.e_receivedate ? 
                        data.e_receivedate.split(' ')[0] : '';
                    
                    // Hide asset modal and show equipment modal
                    $('#assetNoModal').modal('hide');
                    
                    $('#assetNoModal').one('hidden.bs.modal', function() {
                        $('#pcEquipmentModal').modal('show');
                        
                        // Restore equipment form data after modal is shown
                        setTimeout(function() {
                            restoreEquipmentFormData(savedFormData.equipment, data);
                            isSearchModalOpen = false;
                            previousModal = null;
                            savedFormData = {};
                        }, 100);
                    });
                } else {
                    // Update saved form data with selected asset
                    if (isEditMode) {
                        savedFormData.edit_pc_assetno = selectedAssetNo;
                        savedFormData.edit_pc_receive_date = data.e_receivedate ? 
                            data.e_receivedate.split(' ')[0] : '';
                    } else {
                        savedFormData.pc_assetno = selectedAssetNo;
                        savedFormData.pc_receive_date = data.e_receivedate ? 
                            data.e_receivedate.split(' ')[0] : '';
                    }
                    
                    // Hide asset modal and show previous modal
                    $('#assetNoModal').modal('hide');
                    
                    // Show the previous modal after asset modal is hidden
                    if (previousModal) {
                        $('#assetNoModal').one('hidden.bs.modal', function() {
                            $(previousModal).modal('show');
                            
                            // Restore form data after modal is shown
                            setTimeout(function() {
                                restoreFormData(savedFormData);
                                
                                // Trigger age calculation after restore for both add and edit modes
                                if (isEditMode && savedFormData.edit_pc_receive_date) {
                                    $('#edit_pc_receive_date').trigger('change');
                                } else if (!isEditMode && savedFormData.pc_receive_date) {
                                    $('#pc_receive_date').trigger('change');
                                }
                                
                                // Display equipment ID after restoring form data
                                if (data.e_equipmentid) {
                                    const equipmentData = { equipment_id: data.e_equipmentid };
                                    displayEquipmentID(equipmentData, isEditMode);
                                }
                                
                                isSearchModalOpen = false;
                                previousModal = null;
                                savedFormData = {};
                            }, 100);
                        });
                    }
                }
            }
        });

        // Handle employee selection from DataTable
        $('#employeeTable tbody').off('click', 'tr').on('click', 'tr', function() {
            if (employeeDataTable) {
                const data = employeeDataTable.row(this).data();
                if (!data) return;
                
                const userValue = `${data.em_emplcode} - ${data.em_emplname}`;
                
                // Update saved form data with selected employee
                if (isEditMode) {
                    savedFormData.edit_user = userValue;
                } else {
                    savedFormData.user = userValue;
                }
                
                // Hide employee modal and show previous modal
                $('#employeeModal').modal('hide');
                
                // Show the previous modal after employee modal is hidden
                if (previousModal) {
                    $('#employeeModal').one('hidden.bs.modal', function() {
                        $(previousModal).modal('show');
                        
                        // Restore form data after modal is shown
                        setTimeout(function() {
                            restoreFormData(savedFormData);
                            isSearchModalOpen = false;
                            previousModal = null;
                            savedFormData = {};
                        }, 100);
                    });
                }
            }
        });

        // Handle IP address selection from DataTable
        $('#ipAddressTable tbody').off('click', 'tr').on('click', 'tr', function() {
            if (ipAddressDataTable) {
                const data = ipAddressDataTable.row(this).data();

                console.log("Data IP yang diklik:", data); // Tetap biarkan ini untuk debugging
                console.log("Status IP (mip_status):", data.mip_status, "Tipe:", typeof data.mip_status); // Tetap biarkan ini untuk debugging

                if (!data || data.mip_status === undefined || data.mip_ipadd === undefined) {
                    console.error("Data baris IP tidak lengkap:", data);
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Error',
                        text: 'Terjadi masalah saat mengambil data IP. Mohon coba lagi.',
                        showConfirmButton: true
                    });
                    return;
                }

                // Validasi status IP address
                if (parseInt(data.mip_status) !== 0) { // HANYA izinkan memilih yang statusnya 0 (Unused)
                    Swal.fire({
                        icon: 'warning',
                        title: 'IP Not Available',
                        text: `Alamat IP yang dipilih (${data.mip_ipadd}) saat ini berstatus ${parseInt(data.mip_status) == 1 ? 'Used' : 'Tidak Tersedia'}. Mohon pilih IP yang belum terpakai (Unused).`,
                        showConfirmButton: true
                    });
                    return; // Hentikan proses jika IP tidak tersedia
                }

                // --- BAGIAN YANG DIPERBAIKI: MEMASTIKAN NILAI IP DISET KE INPUT YANG TEPAT ---

                // Simpan nilai IP yang dipilih ke savedFormData untuk skenario PC dan VM
                // Ini memastikan data tersedia saat restoreFormData dipanggil atau saat langsung mengisi
                if (previousModal === '#addPCModal') {
                    savedFormData.ip_address = data.mip_ipadd;
                } else if (previousModal === '#editPCModal') {
                    savedFormData.edit_ip_address = data.mip_ipadd;
                } else if (previousModal === '#pcServerVMModal') {
                    // Untuk VM, kita sudah punya savedFormData.vm, jadi langsung update properti vm_ip_address
                    savedFormData.vm.vm_ip_address = data.mip_ipadd;
                }

                // Sembunyikan modal IP Address
                $('#ipAddressModal').modal('hide');

                // Setelah modal IP Address tersembunyi, tampilkan kembali modal sebelumnya
                $('#ipAddressModal').one('hidden.bs.modal', function() {
                    if (previousModal) {
                        $(previousModal).modal('show');
                        
                        // Setelah modal sebelumnya ditampilkan, baru isi nilai input IP-nya
                        // Gunakan setTimeout untuk memastikan elemen input sudah dirender dan siap diisi
                        setTimeout(function() {
                            if (previousModal === '#addPCModal') {
                                $('#ip_address').val(savedFormData.ip_address || ''); // Isi nilai IP Address
                                $('#ip_address').trigger('blur'); // Pemicu event blur untuk validasi
                            } else if (previousModal === '#editPCModal') {
                                $('#edit_ip_address').val(savedFormData.edit_ip_address || ''); // Isi nilai IP Address
                                $('#edit_ip_address').trigger('blur'); // Pemicu event blur untuk validasi
                            } else if (previousModal === '#pcServerVMModal') {
                                restoreVMFormData(savedFormData.vm); // restoreVMFormData akan mengisi vm_ip_address
                                $('#vm_ip_address').trigger('blur'); // Pemicu event blur
                            }

                            // Reset flag dan variabel setelah operasi selesai
                            isSearchModalOpen = false;
                            previousModal = null;
                            savedFormData = {}; // Bersihkan savedFormData setelah digunakan
                        }, 100); // Sedikit delay untuk memastikan modal siap
                    }
                });
                // --- AKHIR BAGIAN YANG DIPERBAIKI ---
            }
        });

        // Reset forms when modals are hidden
        $('#addPCModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            // Only reset if not opened by search modal
            if (!isSearchModalOpen) {
                document.getElementById('addPCForm').reset();
                $('#addPCForm .error-message').text('');
                $('#addPCForm .is-invalid').removeClass('is-invalid');
                // Clear age displays
                $('#pc_age_display').remove();
                // Clear equipment ID display
                $('#equipment_id_display').remove();
            }
        });

        $('#editPCModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            // Only reset if not opened by search modal
            if (!isSearchModalOpen) {
                $('#editPCForm .error-message').text('');
                $('#editPCForm .is-invalid').removeClass('is-invalid');
                
                // Re-enable all form fields
                $('#editPCForm input, #editPCForm select, #editPCForm textarea').prop('disabled', false);
                $('#editPCForm input, #editPCForm select, #editPCForm textarea').prop('readonly', false);
                
                // Show all search buttons
                $('.search-asset-btn, .search-user-btn, .search-ip-btn').show();
                
                // Reset update button visibility
                $('#update-btn').show();
                
                // Reset modal footer button text
                $('.modal-footer .btn-secondary').text('Cancel');
                
                // Clear age displays
                $('#edit_pc_age_display').remove();
                // Clear equipment ID display
                $('#edit_equipment_id_display').remove();
                
                // Reset loading flag
                isLoadingSections = false;
            }
        });

        // Reset PC Detail Modal when hidden
        $('#pcDetailModal').on('hidden.bs.modal', function() {
            
            // Reset VM type filter when modal is closed
            if (serverVMDataTable) {
                try {
                    $('#vmTypeFilter').val('All');
                    serverVMDataTable.column(2).search('').draw();
                } catch (e) {
                    console.log('VM DataTable filter reset error (expected if table was destroyed):', e.message);
                }
            }
            
            // Don't clear currentPCId immediately - only clear if we're not returning to it
            if (!shouldReturnToPCDetails && !isFromPCDetailsModal) {
                currentPCId = null;
                currentPCSpecs = null;
                currentPCType = null;
            } else {
                console.log('Preserving PC ID data for modal transition');
            }
            
            // Destroy VM DataTable to ensure clean state
            if (serverVMDataTable) {
                try {
                    serverVMDataTable.destroy();
                    serverVMDataTable = null;
                    console.log('VM DataTable destroyed successfully');
                } catch (e) {
                    console.log('VM DataTable destroy error (may already be destroyed):', e.message);
                    serverVMDataTable = null;
                }
            }
        });

        // Reset equipment modal when hidden
        $('#pcEquipmentModal').on('hidden.bs.modal', function() {
            // Only reset if not opened by search modal
            if (!isSearchModalOpen) {
                $('#pcEquipmentForm .error-message').text('');
                $('#pcEquipmentForm .is-invalid').removeClass('is-invalid');
                
                // Clear age and equipment ID displays
                $('#equipment_age_display').remove();
                $('#equipment_equipment_id_display').remove();
            }
        });

        // Reset Server VM modal when hidden
        $('#pcServerVMModal').on('hidden.bs.modal', function() {
            $('#pcServerVMForm .error-message').text('');
            $('#pcServerVMForm .is-invalid').removeClass('is-invalid');
            
            // Reset all form fields including new ones
            if (!isSearchModalOpen) {
                $('#vm_name').val('');
                $('#vm_processor').val('');
                $('#vm_ram').val('');
                $('#vm_storage').val('');
                $('#vm_vga').val('');
                $('#vm_ethernet').val('');
                $('#vm_ip_address').val('');
                $('#vm_services').val('');
                $('#vm_remark').val('');
            }
        });

        // Update saved VM form data structure to include new fields
        function saveVMFormData() {
            return {
                pc_id: $('#vm_pc_id').val(),
                vm_id: $('#vm_id').val(),
                vm_name: $('#vm_name').val(),
                vm_processor: $('#vm_processor').val(),
                vm_ram: $('#vm_ram').val(),
                vm_storage: $('#vm_storage').val(),
                vm_vga: $('#vm_vga').val(),
                vm_ethernet: $('#vm_ethernet').val(),
                vm_ip_address: $('#vm_ip_address').val(),
                vm_services: $('#vm_services').val(),
                vm_remark: $('#vm_remark').val()
            };
        }

        // Fungsi restoreVMFormData() (ini sudah benar)
        function restoreVMFormData(formData) {
            $('#vm_pc_id').val(formData.pc_id || '');
            $('#vm_id').val(formData.vm_id || '');
            $('#vm_name').val(formData.vm_name || '');
            $('#vm_processor').val(formData.vm_processor || '');
            $('#vm_ram').val(formData.vm_ram || '');
            $('#vm_storage').val(formData.vm_storage || '');
            $('#vm_vga').val(formData.vm_vga || '');
            $('#vm_ethernet').val(formData.vm_ethernet || '');
            $('#vm_ip_address').val(formData.vm_ip_address || ''); // Ini sudah mengisi dengan benar
            $('#vm_services').val(formData.vm_services || '');
            $('#vm_remark').val(formData.vm_remark || '');
        }


        // Reset asset search when modal is hidden
        $('#assetNoModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            $('#searchAssetNo').val('');
            if (assetNoDataTable) {
                assetNoDataTable.destroy();
                assetNoDataTable = null;
            }
            
            // If modal was closed without selection and there was a previous modal, show it and restore data
            if (previousModal && isSearchModalOpen) {
                $(previousModal).modal('show');
                
                // Restore form data after modal is shown
                setTimeout(function() {
                    if (previousModal === '#pcEquipmentModal') {
                        restoreEquipmentFormData(savedFormData.equipment, null);
                    } else {
                        restoreFormData(savedFormData);
                    }
                    isSearchModalOpen = false;
                    previousModal = null;
                    savedFormData = {};
                }, 100);
            }
        });

        // Reset employee search when modal is hidden
        $('#employeeModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            $('#searchEmployee').val('');
            if (employeeDataTable) {
                employeeDataTable.destroy();
                employeeDataTable = null;
            }
            
            // If modal was closed without selection and there was a previous modal, show it and restore data
            if (previousModal && isSearchModalOpen) {
                $(previousModal).modal('show');
                
                // Restore form data after modal is shown
                setTimeout(function() {
                    restoreFormData(savedFormData);
                    isSearchModalOpen = false;
                    previousModal = null;
                    savedFormData = {};
                }, 100);
            }
        });

        // Reset IP address search when modal is hidden
        $('#ipAddressModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            $('#searchIPAddress').val('');
            if (ipAddressDataTable) {
                ipAddressDataTable.destroy();
                ipAddressDataTable = null;
            }
            
            // If modal was closed without selection and there was a previous modal, show it and restore data
            if (previousModal && isSearchModalOpen) {
                $(previousModal).modal('show');
                
                // Restore form data after modal is shown
                setTimeout(function() {
                    if (previousModal === '#pcServerVMModal') {
                        // Restore VM form data
                        $('#vm_pc_id').val(savedFormData.vm.pc_id || '');
                        $('#vm_id').val(savedFormData.vm.vm_id || '');
                        $('#vm_type').val(savedFormData.vm.vm_type || '');
                        $('#vm_ip_address').val(savedFormData.vm.vm_ip_address || '');
                        $('#vm_services').val(savedFormData.vm.vm_services || '');
                        $('#vm_remark').val(savedFormData.vm.vm_remark || '');
                    } else {
                        restoreFormData(savedFormData);
                    }
                    isSearchModalOpen = false;
                    previousModal = null;
                    savedFormData = {};
                }, 100);
            }
        });

        // Initialize search modals when they appear
        $('#assetNoModal').off('show.bs.modal').on('show.bs.modal', function() {
            $('#searchAssetNo').val('');
            if (assetNoDataTable && !isSearchModalOpen) {
                assetNoDataTable.ajax.reload();
            }
        });

        $('#employeeModal').off('show.bs.modal').on('show.bs.modal', function() {
            $('#searchEmployee').val('');
            if (employeeDataTable && !isSearchModalOpen) {
                employeeDataTable.ajax.reload();
            }
        });

        $('#ipAddressModal').off('show.bs.modal').on('show.bs.modal', function() {
            $('#searchIPAddress').val('');
            // **PENTING:** Memastikan filter dropdown IP disetel ulang ke "Show All" setiap kali modal dibuka
            // dan DataTables dimuat ulang untuk menampilkan semua opsi IP
            if ($('#ipStatusFilter').length) {
                $('#ipStatusFilter').val('').trigger('change'); 
            } else if (ipAddressDataTable && !isSearchModalOpen) {
                ipAddressDataTable.ajax.reload();
            }
        });

        // Handle close buttons for search modals
        $('#employeeModal .btn-close, #assetNoModal .btn-close, #ipAddressModal .btn-close').off('click').on('click', function() {
            const modalId = $(this).closest('.modal').attr('id');
            
            // Hide the current search modal
            $(`#${modalId}`).modal('hide');
            
            // Show the previous modal after search modal is hidden and restore data
            if (previousModal && isSearchModalOpen) {
                $(`#${modalId}`).one('hidden.bs.modal', function() {
                    $(previousModal).modal('show');
                    
                    // Restore form data after modal is shown
                    setTimeout(function() {
                        if (previousModal === '#pcEquipmentModal') {
                            restoreEquipmentFormData(savedFormData.equipment, null);
                        } else if (previousModal === '#pcServerVMModal') {
                            // Restore VM form data
                            $('#vm_pc_id').val(savedFormData.vm.pc_id || '');
                            $('#vm_id').val(savedFormData.vm.vm_id || '');
                            $('#vm_type').val(savedFormData.vm.vm_type || '');
                            $('#vm_ip_address').val(savedFormData.vm.vm_ip_address || '');
                            $('#vm_services').val(savedFormData.vm.vm_services || '');
                            $('#vm_remark').val(savedFormData.vm.vm_remark || '');
                        } else {
                            restoreFormData(savedFormData);
                        }
                        isSearchModalOpen = false;
                        previousModal = null;
                        savedFormData = {};
                    }, 100);
                });
            }
        });

        // Handle modal cancel buttons to return to PC Details with fresh data
        $('#pcSpecsModal .btn-secondary, #pcSpecsModal .btn-close').off('click').on('click', function() {
            const pcId = $('#specs_pc_id').val() || currentPCId;
            
            if (shouldReturnToPCDetails && pcId) {
                $('#pcSpecsModal').modal('hide');
                $('#pcSpecsModal').one('hidden.bs.modal', function() {
                    currentPCId = pcId; // Preserve PC ID
                    setTimeout(function() {
                        $('#pcDetailModal').modal('show');
                        setTimeout(function() {
                            loadPCDetails(pcId);
                            isFromPCDetailsModal = false;
                            shouldReturnToPCDetails = false;
                        }, 100);
                    }, 100);
                });
            } else {
                $('#pcSpecsModal').modal('hide');
            }
        });

        $('#pcEquipmentModal .btn-secondary, #pcEquipmentModal .btn-close').off('click').on('click', function() {
            const pcId = $('#equipment_pc_id').val() || currentPCId;
            
            if (shouldReturnToPCDetails && pcId) {
                $('#pcEquipmentModal').modal('hide');
                $('#pcEquipmentModal').one('hidden.bs.modal', function() {
                    currentPCId = pcId; // Preserve PC ID
                    setTimeout(function() {
                        $('#pcDetailModal').modal('show');
                        setTimeout(function() {
                            loadPCDetails(pcId);
                            isFromPCDetailsModal = false;
                            shouldReturnToPCDetails = false;
                        }, 100);
                    }, 100);
                });
            } else {
                $('#pcEquipmentModal').modal('hide');
            }
        });

        $('#pcServerVMModal .btn-secondary, #pcServerVMModal .btn-close').off('click').on('click', function() {
            const pcId = $('#vm_pc_id').val() || currentPCId;
            
            if (shouldReturnToPCDetails && pcId) {
                $('#pcServerVMModal').modal('hide');
                $('#pcServerVMModal').one('hidden.bs.modal', function() {
                    currentPCId = pcId; // Preserve PC ID
                    setTimeout(function() {
                        $('#pcDetailModal').modal('show');
                        setTimeout(function() {
                            loadPCDetails(pcId);
                            isFromPCDetailsModal = false;
                            shouldReturnToPCDetails = false;
                        }, 100);
                    }, 100);
                });
            } else {
                $('#pcServerVMModal').modal('hide');
            }
        });

        // Handle modal close buttons (X) to return to PC Details with fresh data
        $('#pcSpecsModal .btn-close, #pcEquipmentModal .btn-close, #pcServerVMModal .btn-close').off('click').on('click', function() {
            const currentModal = $(this).closest('.modal');
            const modalId = currentModal.attr('id');
            
            let pcId;
            if (modalId === 'pcSpecsModal') {
                pcId = $('#specs_pc_id').val() || currentPCId;
            } else if (modalId === 'pcEquipmentModal') {
                pcId = $('#equipment_pc_id').val() || currentPCId;
            } else if (modalId === 'pcServerVMModal') {
                pcId = $('#vm_pc_id').val() || currentPCId;
            }
            
            if (shouldReturnToPCDetails && pcId) {
                currentModal.modal('hide');
                currentModal.one('hidden.bs.modal', function() {
                    currentPCId = pcId; // Preserve PC ID
                    // Show loading state first
                    $('#pcDetailModal').modal('show');
                    $('#pcDetailModalLabel').text('PC Details - Loading...');
                    
                    // Small delay to ensure modal is visible before loading data
                    setTimeout(function() {
                        // Reload the PC details data to ensure fresh information
                        loadPCDetails(pcId);
                        // Reset flags
                        isFromPCDetailsModal = false;
                        shouldReturnToPCDetails = false;
                    }, 200);
                });
            } else {
                currentModal.modal('hide');
            }
        });

        // Reset flags when modals are hidden without return
        $('#pcSpecsModal, #pcEquipmentModal, #pcServerVMModal').on('hidden.bs.modal', function() {
            const modalId = $(this).attr('id');
            
            // Only reset if not returning to PC Details
            if (!shouldReturnToPCDetails) {
                isFromPCDetailsModal = false;
                shouldReturnToPCDetails = false;
            } else {
                console.log('Preserving modal flags for return to PC Details');
            }
        });

        $('#pcDetailModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            
            // Reset VM type filter when modal is closed
            if (serverVMDataTable) {
                try {
                    $('#vmTypeFilter').val('All');
                    serverVMDataTable.column(2).search('').draw();
                } catch (e) {
                    console.log('VM DataTable filter reset error (expected if table was destroyed):', e.message);
                }
            }
            
            // Don't clear currentPCId immediately - only clear if we're not returning to it
            if (!shouldReturnToPCDetails && !isFromPCDetailsModal) {
                currentPCId = null;
                currentPCSpecs = null;
                currentPCType = null;
            } else {
                console.log('Preserving PC ID data for modal transition');
            }
            
            // Destroy VM DataTable to ensure clean state
            if (serverVMDataTable) {
                try {
                    serverVMDataTable.destroy();
                    serverVMDataTable = null;
                    console.log('VM DataTable destroyed successfully');
                } catch (e) {
                    console.log('VM DataTable destroy error (may already be destroyed):', e.message);
                    serverVMDataTable = null;
                }
            }
        });

        // Reset search modal handling for equipment and VM modals
        $('.search-equipment-asset-btn').off('click').on('click', function() {
            const isEquipmentSearch = $(this).hasClass('search-equipment-asset-btn');
            
            if (isEquipmentSearch) {
                // Equipment asset search
                isSearchModalOpen = true;
                
                // Save equipment form data
                savedFormData.equipment = {
                    pc_id: $('#equipment_pc_id').val(),
                    equipment_id: $('#equipment_id').val(),
                    equipment_type: $('#equipment_type').val(),
                    asset_no: $('#equipment_asset_no').val(),
                    receive_date: $('#equipment_receive_date').val()
                };
                
                previousModal = '#pcEquipmentModal';
                
                $('#pcEquipmentModal').modal('hide');
                $('#pcEquipmentModal').one('hidden.bs.modal', function() {
                    $('#assetNoModal').modal('show');
                    initAssetNoDataTable();
                });
            }
        });

        $('.search-vm-ip-btn').off('click').on('click', function() {
            const isVMIPSearch = $(this).hasClass('search-vm-ip-btn');
            
            if (isVMIPSearch) {
                // VM IP search
                isSearchModalOpen = true;
                
                // Save VM form data
                savedFormData.vm = saveVMFormData();
                
                previousModal = '#pcServerVMModal';
                
                $('#pcServerVMModal').modal('hide');
                $('#pcServerVMModal').one('hidden.bs.modal', function() {
                    $('#ipAddressModal').modal('show');
                    initIPAddressDataTable();
                });
            }
        });
    });
    </script>

<?= $this->endSection() ?>