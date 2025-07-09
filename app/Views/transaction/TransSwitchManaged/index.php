<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Trans Switch Managed</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addSwitchManagedModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add New Switch Managed
        </button>
    </p>

    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelSwitchManaged">
            <thead class="table-light">
                <tr>
                    <th width="5%">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllHeader" title="Select/Deselect All">
                        </div>
                    </th>
                    <th width="15%">Action</th>
                    <th>ID</th>
                    <th>Asset No</th>
                    <th>Asset Name</th>
                    <th>Asset Age</th>
                    <th>Max Port</th>
                    <th>IP Address</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Add Switch Managed Modal -->
    <div class="modal fade" id="addSwitchManagedModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addSwitchManagedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSwitchManagedModalLabel">Add New Switch Managed Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addSwitchManagedForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="add_asset_no" class="form-label">Asset No</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="add_asset_no" name="asset_no" placeholder="Type or search Asset No">
                                    <button type="button" class="btn btn-link search-asset-btn"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="add_asset_no_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_asset_name" class="form-label">Asset Name</label>
                                <input type="text" class="form-control" id="add_asset_name" name="asset_name" placeholder="Type Asset Name">
                                <div class="invalid-feedback" id="add_asset_name_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_receive_date" class="form-label">Receive Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="add_receive_date" name="receive_date">
                                <div class="invalid-feedback" id="add_receive_date_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_port_count" class="form-label">Max Port <span class="text-danger">*</span></label>
                                <input type="number" class="form-control no-spinner" id="add_port_count" name="port_count" placeholder="Type Max Port" min="1">
                                <div class="invalid-feedback" id="add_port_count_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_ip" class="form-label">IP Address</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="add_ip" name="ip" placeholder="Type or search IP Address">
                                    <button type="button" class="btn btn-link search-ip-btn"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="add_ip_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_location" class="form-label">Location</label>
                                <select class="form-select" id="add_location" name="location">
                                    <option value="">--Select Location--</option>
                                </select>
                                <div class="invalid-feedback" id="add_location_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveSwitchManaged">Submit Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Switch Managed Modal -->
    <div class="modal fade" id="editSwitchManagedModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editSwitchManagedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSwitchManagedModalLabel">Edit Switch Managed Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSwitchManagedForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <input type="hidden" id="edit_id" name="tsm_id">
                            
                            <div class="col-md-6">
                                <label for="edit_asset_no" class="form-label">Asset No</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_asset_no" name="asset_no" placeholder="Type or search Asset No">
                                    <button type="button" class="btn btn-link search-asset-btn"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="edit_asset_no_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_asset_name" class="form-label">Asset Name</label>
                                <input type="text" class="form-control" id="edit_asset_name" name="asset_name" placeholder="Type Asset Name">
                                <div class="invalid-feedback" id="edit_asset_name_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_receive_date" class="form-label">Receive Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_receive_date" name="receive_date">
                                <div class="invalid-feedback" id="edit_receive_date_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_port_count" class="form-label">Max Port <span class="text-danger">*</span></label>
                                <input type="number" class="form-control no-spinner" id="edit_port_count" name="port_count" placeholder="Type Max Port" min="1">
                                <div class="invalid-feedback" id="edit_port_count_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_ip" class="form-label">IP Address</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_ip" name="ip" placeholder="Type or search IP Address">
                                    <button type="button" class="btn btn-link search-ip-btn"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="edit_ip_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_location" class="form-label">Location</label>
                                <select class="form-select" id="edit_location" name="location">
                                    <option value="">--Select Location--</option>
                                </select>
                                <div class="invalid-feedback" id="edit_location_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="updateSwitchManaged">Update Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Asset Search Modal -->
    <div class="modal fade" id="assetSearchModal" tabindex="-1" aria-labelledby="assetSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assetSearchModalLabel">Select Asset No</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="assetTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Asset No</th>
                                <th>Equipment ID</th>
                                <th>Serial Number</th>
                                <th>Receive Date</th>
                                <th>Equipment Name</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Switch Details Modal -->
    <div class="modal fade" id="switchDetailModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="switchDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 80vw;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="switchDetailModalLabel">Switch Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">                    
                    <!-- Action Section -->
                    <div class="mb-4 action-buttons" style="padding-left: 15px; padding-right: 15px;">
                        <div class="row mb-3">
                            <div class="col-md-12 d-flex gap-3">
                                <button type="button" class="btn btn-primary add-port-detail-btn" style="width: 220px;" data-bs-toggle="modal" data-bs-target="#addSwitchPortDetailModal">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add Port Detail
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="datatables-basic table table-bordered" id="switchDetailTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 15%;">Action</th>
                                    <th style="width: 8%;">ID</th>
                                    <th style="width: 10%;">Port</th>
                                    <th style="width: 17%;">Type</th>
                                    <th style="width: 12%;">VLAN ID</th>
                                    <th style="width: 28%;">VLAN Name</th>
                                    <th style="width: 10%;">Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Switch Port Detail Modal -->
    <div class="modal fade" id="addSwitchPortDetailModal" tabindex="-1" aria-labelledby="addSwitchPortDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSwitchPortDetailModalLabel">Add New Switch Port</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addSwitchPortDetailForm">
                        <input type="hidden" id="add_detail_header_id" name="header_id_switch">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="add_port" class="form-label">Port</label>
                                <select class="form-select" id="add_port" name="port">
                                    <option value="">--Select Port--</option>
                                    <!-- Options will be populated dynamically -->
                                </select>
                                <div class="invalid-feedback" id="add_port_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_type" class="form-label">Type</label>
                                <select class="form-select" id="add_type" name="type">
                                    <option value="">--Select Type--</option>
                                    <option value="ethernet">Ethernet</option>
                                    <option value="SFP">SFP</option>
                                </select>
                                <div class="invalid-feedback" id="add_type_error"></div>
                            </div>
                            <div class="col-md-3"> <label for="add_vlan_id" class="form-label">VLAN ID <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="add_vlan_id" name="vlan_id" placeholder="Type VLAN ID">
                                    <button type="button" class="btn btn-link search-vlan-btn"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="add_vlan_id_error"></div>
                                <input type="hidden" id="add_vlan_name_hidden" name="vlan_name"> </div>
                            <div class="col-md-3"> <label for="add_vlan_name" class="form-label">VLAN Name</label>
                                <input type="text" class="form-control" id="add_vlan_name" name="vlan_name_display" placeholder="VLAN Name (Auto-filled)" readonly>
                                </div>
                            <div class="col-md-6">
                                <label for="add_detail_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="add_detail_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback" id="add_detail_status_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submit-port-detail-btn">Submit Port</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Switch Port Detail Modal -->
    <div class="modal fade" id="editSwitchPortDetailModal" tabindex="-1" aria-labelledby="editSwitchPortDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSwitchPortDetailModalLabel">Edit Switch Port</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSwitchPortDetailForm">
                        <input type="hidden" id="edit_tsd_id" name="tsd_id">
                        <input type="hidden" id="edit_detail_header_id" name="header_id_switch">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_port" class="form-label">Port</label>
                                <select class="form-select" id="edit_port" name="port">
                                    <option value="">--Select Port--</option>
                                    <!-- Options will be populated dynamically -->
                                </select>
                                <div class="invalid-feedback" id="edit_port_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_type" class="form-label">Type</label>
                                <select class="form-select" id="edit_type" name="type">
                                    <option value="">--Select Type--</option>
                                    <option value="ethernet">Ethernet</option>
                                    <option value="SFP">SFP</option>
                                </select>
                                <div class="invalid-feedback" id="edit_type_error"></div>
                            </div>
                            <div class="col-md-3"> <label for="edit_vlan_id" class="form-label">VLAN ID <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_vlan_id" name="vlan_id" placeholder="Type VLAN ID">
                                    <button type="button" class="btn btn-link search-vlan-btn" data-bs-toggle="modal" data-bs-target="#vlanSearchModal"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="edit_vlan_id_error"></div>
                                <input type="hidden" id="edit_vlan_name_hidden" name="vlan_name"> </div>
                            <div class="col-md-3"> <label for="edit_vlan_name" class="form-label">VLAN Name</label>
                                <input type="text" class="form-control" id="edit_vlan_name" name="vlan_name_display" placeholder="VLAN Name (Auto-filled)" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_detail_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_detail_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback" id="edit_detail_status_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="update-port-detail-btn">Update Port</button>
                </div>
            </div>
        </div>
    </div>

    <!-- VLAN Search Modal -->
    <div class="modal fade" id="vlanSearchModal" tabindex="-1" aria-labelledby="vlanSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vlanSearchModalLabel">Select VLAN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="vlanTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>VLAN ID</th>
                                <th>VLAN Name</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Menyembunyikan spinner pada input number */
    .no-spinner::-webkit-outer-spin-button,
    .no-spinner::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .no-spinner[type=number] {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    /* Readonly style with subtle appearance */
    input[readonly], textarea[readonly], select[readonly] {
        background-color: #f8f9fa !important;
        cursor: not-allowed;
        color: #6c757d !important;
    }
    
    .card-datatable.table-responsive {
        overflow-x: auto;
        width: 100%;
    }

    /* Styles for detail modal */
    #switchDetailModal .modal-dialog {
        max-width: 95%;
        width: auto !important;
    }

    #switchDetailModal .modal-body .table-responsive {
        overflow-x: auto;
        margin-left: 0;
        margin-right: 0;
        max-height: calc(100vh - 350px);
    }

    /* Detail table styling */
    #switchDetailTable th, #switchDetailTable td {
        white-space: nowrap;
    }

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

    /* Custom styling for auto-filled fields */
    .auto-filled-field {
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        font-style: italic;
    }

    /* Make required asterisk more visible */
    .text-danger {
        color: #dc3545 !important;
        font-weight: bold;
    }

    /* Custom style for VLAN name field to make it more subtle */
    #add_vlan_name, #edit_vlan_name {
        background-color: #f8f9fa !important;
        border-color: #dee2e6 !important;
        color: #6c757d !important;
        font-style: italic;
    }

    /* Override validation styling for readonly fields */
    #add_vlan_name.is-invalid, #edit_vlan_name.is-invalid {
        border-color: #dee2e6 !important;
        background-color: #f8f9fa !important;
    }

    /* Filter wrapper styling */
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

<script>
$(document).ready(function() {
    // ============================
    // VARIABLE DECLARATIONS
    // ============================
    const base_url = '<?= base_url('TransSwitchManaged') ?>';
    
    let assetTable;
    let vlanTable;
    let ipAddressTable;
    let currentCallingModal = '';
    let currentCallingDetailModal = '';
    let tabelSwitchManaged;
    let switchDetailTable;
    let selectedSwitchId = null;
    let selectedSwitchIds = [];
    let isSearchModalOpen = false;
    let savedFormData = {};
    let modalActionType = null;
    let isFromVlanSearch = false;

    // Regex for basic IPv4 validation
    const ipv4Pattern = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;

    // ============================
    // UTILITY FUNCTIONS
    // ============================
    
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

    // Function to update selected IDs array
    function updateSelectedIds() {
        selectedSwitchIds = [];
        $('.switch-checkbox:checked').each(function() {
            selectedSwitchIds.push(parseInt($(this).val()));
        });
        updateSelectedCount();
    }

    // Function to update selected count display
    function updateSelectedCount() {
        const hasSelection = selectedSwitchIds.length > 0;
        $('#exportSelectedCSV, #exportSelectedODS, #exportSelectedXLSX').toggleClass('disabled', !hasSelection);
    }

    // Function to export selected data
    function exportSelectedData(format) {
        if (selectedSwitchIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'Please select at least one Switch record to export.',
                showConfirmButton: true
            });
            return;
        }

        // Show confirmation dialog
        Swal.fire({
            title: 'Confirm Export',
            html: `Are you sure you want to export <strong>${selectedSwitchIds.length}</strong> selected Switch record(s) to ${format} format?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, export to ${format}!`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a form to submit selected IDs
                const form = $('<form>', {
                    method: 'POST',
                    action: base_url + `/exportSelected${format}`
                });

                // Add selected IDs as hidden inputs
                selectedSwitchIds.forEach(id => {
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'selected_ids[]',
                        value: id
                    }));
                });

                // Submit form
                $('body').append(form);
                form.submit();
                form.remove();

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Export Started',
                    text: `Your ${format} export for ${selectedSwitchIds.length} records has been initiated.`,
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }

    // ============================
    // FORM DATA MANAGEMENT
    // ============================
    
    // Function to save form data before opening search modal
    function saveFormData() {
        const formData = {};
        
        if (currentCallingModal === '#addSwitchManagedModal') {
            formData.asset_no = $('#add_asset_no').val();
            formData.asset_name = $('#add_asset_name').val();
            formData.receive_date = $('#add_receive_date').val();
            formData.port_count = $('#add_port_count').val();
            formData.ip = $('#add_ip').val();
            formData.location = $('#add_location').val();
        } else if (currentCallingModal === '#editSwitchManagedModal') {
            formData.edit_id = $('#edit_id').val();
            formData.edit_asset_no = $('#edit_asset_no').val();
            formData.edit_asset_name = $('#edit_asset_name').val();
            formData.edit_receive_date = $('#edit_receive_date').val();
            formData.edit_port_count = $('#edit_port_count').val();
            formData.edit_ip = $('#edit_ip').val();
            formData.edit_location = $('#edit_location').val();
        }
        
        return formData;
    }

    // Function to restore form data after closing search modal
    function restoreFormData(formData) {
        if (currentCallingModal === '#addSwitchManagedModal') {
            $('#add_asset_no').val(formData.asset_no || '');
            $('#add_asset_name').val(formData.asset_name || '');
            $('#add_receive_date').val(formData.receive_date || '');
            $('#add_port_count').val(formData.port_count || '');
            $('#add_ip').val(formData.ip || ''); // <--- BARIS INI PENTING
            $('#add_location').val(formData.location || '');
        } else if (currentCallingModal === '#editSwitchManagedModal') {
            $('#edit_id').val(formData.edit_id || '');
            $('#edit_asset_no').val(formData.edit_asset_no || '');
            $('#edit_asset_name').val(formData.edit_asset_name || '');
            $('#edit_receive_date').val(formData.edit_receive_date || '');
            $('#edit_port_count').val(formData.edit_port_count || '');
            $('#edit_ip').val(formData.edit_ip || ''); // <--- BARIS INI PENTING
            $('#edit_location').val(formData.edit_location || '');
        }
    }

    // ============================
    // PORT MANAGEMENT FUNCTIONS
    // ============================
    
    // Function to populate port options based on switch max port
    function populatePortOptions(selectElement, switchId, excludePortId = null) {
        $.ajax({
            url: base_url + '/getAvailablePorts',
            type: 'GET',
            data: { tsd_switchid: switchId }, // Updated to use tsd_switchid parameter
            dataType: 'json',
            success: function(response) {
                $(selectElement).empty();
                $(selectElement).append('<option value="">--Select Port--</option>');
                
                if (response.status && response.data && response.data.length > 0) {
                    response.data.forEach(function(port) {
                        $(selectElement).append(`<option value="${port}">${port}</option>`);
                    });
                    
                    // If editing, add the current port to options and select it
                    if (excludePortId !== null) {
                        const currentOption = $(selectElement).find(`option[value="${excludePortId}"]`);
                        if (currentOption.length === 0) {
                            $(selectElement).append(`<option value="${excludePortId}">${excludePortId}</option>`);
                        }
                        $(selectElement).val(excludePortId);
                        
                        // Sort options numerically
                        const options = $(selectElement).find('option').not(':first').sort(function(a, b) {
                            return parseInt(a.value) - parseInt(b.value);
                        });
                        $(selectElement).find('option').not(':first').remove();
                        $(selectElement).append(options);
                        $(selectElement).val(excludePortId);
                    }
                } else {
                    $(selectElement).append('<option value="">No available ports</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading available ports:', error);
                $(selectElement).html('<option value="">Error loading ports</option>');
            }
        });
    }

    function countSwitchDetailPorts(tsd_switchid) {
        return $.ajax({
            url: base_url + '/countSwitchDetailPorts',
            type: 'GET',
            data: { tsd_switchid: tsd_switchid }, // Updated to use tsd_switchid parameter
            dataType: 'json'
        });
    }

    // ============================
    // DATATABLE INITIALIZATION
    // ============================
    
    // Initialize DataTable with filters and export buttons
    function initializeSwitchManagedDataTable() {
        if ($.fn.DataTable.isDataTable('#tabelSwitchManaged')) {
            tabelSwitchManaged.destroy();
        }

        tabelSwitchManaged = $('#tabelSwitchManaged').DataTable({
            scrollX: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], 
            order: [[2, 'asc']], // Order by ID ascending
            autoWidth: false,
            ajax: {
                url: base_url + "/getData",
                dataSrc: function(json) {
                    return json;
                },
                beforeSend: function() {
                    let spinner = `
                        <div class="align-items-center justify-content-center gap-2">
                            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                        </div>`;
                    $('#tabelSwitchManaged tbody').html(`<tr><td colspan="9">${spinner}</td></tr>`);
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                $('#tabelSwitchManaged tbody').html('<tr><td colspan="9" class="text-center">Error loading data. Please try again.</td></tr>');
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
                        return `<input type="checkbox" class="form-check-input switch-checkbox" value="${row.tsm_id}" data-id="${row.tsm_id}">`;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    width: '15%',
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex justify-content-center gap-2">
                                <a href="javascript:;" class="btn btn-icon btn-outline-info view-detail-btn" data-id="${row.tsm_id}" title="View Port Details">
                                    <i class="fa fa-network-wired"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.tsm_id}" title="Edit Switch">
                                    <i class="fa fa-pen-to-square"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.tsm_id}" title="Delete Switch">
                                    <i class="fa fa-trash-can"></i>
                                </a>
                            </div>`;
                    }
                },
                { data: 'tsm_id' },
                { data: 'tsm_assetno', render: d => d ? d : '-' },
                { data: 'tsm_assetname', render: d => d ? d : '-' },
                { 
                    data: 'tsm_receivedate',
                    render: function(data, type, row) {
                        if (!data) return '-';
                        return calculateAge(data);
                    }
                },
                { data: 'tsm_port', render: d => d ? d : '-' },
                { data: 'tsm_ipaddress', render: d => d ? d : '-' },
                { data: 'tsm_location', render: d => d ? d : '-' },
            ],
            language: {
                "sLengthMenu": "Show _MENU_ entries",
                "sSearch": "Search:"
            },
            // Ganti drawCallback dengan initComplete
            initComplete: function() {
                // Initialize export buttons after table is completely initialized
                initializeExportButtons();
            }
        });

        $('#tabelSwitchManaged').on('change', '.switch-checkbox', function() {
            updateSelectedIds();
        });
    }

    // Fungsi terpisah untuk initialize export buttons
    function initializeExportButtons() {
        // Check if export buttons already exist
        if ($('#exportButtonsWrapper').length > 0) {
            return; // Already initialized
        }

        const lengthControl = $('#tabelSwitchManaged_length');
        
        // Create export buttons with enhanced functionality
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
        
        lengthControl.append(exportButtons);
        
        // Bind export event handlers
        bindExportHandlers();
    }

    // Fungsi terpisah untuk bind export handlers
    function bindExportHandlers() {
        // Export All event handlers
        $('#exportAllCSV').on('click', function(e) {
            e.preventDefault();
            const originalHtml = $(this).html();
            $(this).html('<i class="fa fa-spinner fa-spin me-2"></i>Exporting...');
            window.location.href = base_url + '/exportCSV';
            const self = this;
            setTimeout(() => {
                $(self).html(originalHtml);
            }, 2000);
        });

        $('#exportAllODS').on('click', function(e) {
            e.preventDefault();
            const originalHtml = $(this).html();
            $(this).html('<i class="fa fa-spinner fa-spin me-2"></i>Exporting...');
            window.location.href = base_url + '/exportODS';
            const self = this;
            setTimeout(() => {
                $(self).html(originalHtml);
            }, 2000);
        });

        $('#exportAllXLSX').on('click', function(e) {
            e.preventDefault();
            const originalHtml = $(this).html();
            $(this).html('<i class="fa fa-spinner fa-spin me-2"></i>Exporting...');
            window.location.href = base_url + '/exportXLSX';
            const self = this;
            setTimeout(() => {
                $(self).html(originalHtml);
            }, 2000);
        });

        // Export Selected event handlers
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

    // Initialize DataTable for asset search
    function initAssetDataTable() {
        if (assetTable) {
            assetTable.destroy();
        }
        
        assetTable = $('#assetTable').DataTable({
            processing: true,
            serverSide: false,
            paging: true,
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            searching: true,
            ordering: true,
            order: [[0, 'asc']],
            ajax: {
                url: base_url + '/searchAssetNo',
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

    // Initialize DataTable for IP Address search
    function initIPAddressDataTable(statusFilter = 'All') { // <--- UBAH BARIS INI
        if (ipAddressTable) {
            ipAddressTable.destroy();
            // Penting: Hapus konten tbody agar tidak ada spinner lama menumpuk
            $('#ipAddressTable tbody').html('');  // Ini sudah bagus
            // Hapus elemen filter kustom yang mungkin ada dari inisialisasi sebelumnya
            $('#ipAddressTable_filter #ipStatusFilterWrapper').remove();
            // --- SISIPKAN INI UNTUK MEMASTIKAN THEAD KOSONG DAN SIAP SEBELUM INISIALISASI ULANG ---
            $('#ipAddressTable thead').empty().append(`
                <tr>
                    <th style="width: 15%;">VLAN ID</th>
                    <th style="width: 25%;">VLAN Name</th>
                    <th style="width: 25%;">IP Address</th>
                    <th style="width: 15%;">Status</th>
                </tr>
            `);
            // --- AKHIR SISIPAN ---
        }
        
        ipAddressTable = $('#ipAddressTable').DataTable({
            processing: true, // Pastikan ini true untuk menunjukkan loading
            serverSide: false, // Penting: False, karena DataTables yang melakukan sorting/paging lokal
            paging: true,
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            searching: true, // Mengaktifkan fitur pencarian DataTables
            ordering: true,
            order: [[2, 'asc']], // Default sort by IP Address (column index 2: mip_ipadd)
            ajax: {
                url: base_url + '/searchIPAddresses',
                type: 'GET',
                data: function(d) {
                    // Kirim filter status ke backend. DataTables akan menambahkan parameter lain secara otomatis
                    d.status = statusFilter; 
                },
                dataSrc: function(json) {
                    // SANGAT PENTING UNTUK DEBUGGING: log data mentah
                    console.log("Raw JSON received by DataTables (IP Address Modal):", json);

                    if (json && json.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Loading IP Data',
                            text: json.error,
                            showConfirmButton: true
                        });
                        return [];
                    }
                    // Jika backend mengembalikan array langsung, ini sudah benar
                    return json || []; 
                },
                beforeSend: function() {
                    // Tampilkan spinner saat loading dimulai
                    $('#ipAddressTable tbody').html(`<tr><td colspan="4" class="text-center">
                        <div class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></div> Loading...
                    </td></tr>`);
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables IP AJAX error (frontend):', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error,
                        thrown: thrown
                    });
                    let errorMessage = 'Error loading IP data. Please try again.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response && response.error) {
                            errorMessage = response.error;
                        } else if (response && response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) { /* fallback to generic error message */ }
                    $('#ipAddressTable tbody').html(`<tr><td colspan="4" class="text-center text-danger">${errorMessage}</td></tr>`);
                }
            },
            columns: [
                { data: 'mip_vlanid', title: 'VLAN ID', width: '15%', defaultContent: '-' },
                { data: 'mip_vlanname', title: 'VLAN Name', width: '25%', defaultContent: '-' },
                { 
                    // PASTIKAN ini 'mip_ipadd' agar cocok dengan kolom DB dan hasil query
                    data: 'mip_ipadd', 
                    title: 'IP Address', 
                    width: '25%', 
                    defaultContent: '-' 
                }, 
                { 
                    data: 'mip_status', 
                    title: 'Status', 
                    width: '15%', 
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            // Menurut definisi baru Anda: 1 = Unused (hijau), 0 = Used (merah), 25 = Soft Deleted (gelap)
                            const statusText = data == 1 ? 'Unused' : (data == 0 ? 'Used' : 'Soft Deleted');
                            let badgeClass;
                            if (data == 1) { // Unused
                                badgeClass = 'bg-success text-white'; // Hijau untuk Unused
                            } else if (data == 0) { // Used
                                badgeClass = 'bg-danger text-white'; // Merah untuk Used
                            } else { // Soft Deleted (25) atau status lainnya
                                badgeClass = 'bg-dark text-white'; // Warna lain untuk soft-deleted
                            }
                            return `<span class="badge ${badgeClass}">${statusText}</span>`;
                        }
                        return data; // For filtering and sorting, use raw data value
                    }
                },
            ],
            columnDefs: [
                { targets: '_all', defaultContent: '-' }
            ],
            initComplete: function() {
                const api = this.api();
                const filterColumn = api.column(3); // Kolom status ada di indeks 3 (0-indexed)

                let filterContainer = $(this).closest('.dataTables_wrapper').find('.dataTables_filter');
                
                // Hapus filter lama jika ada untuk menghindari duplikasi
                filterContainer.find('#ipStatusFilterWrapper').remove();

                let customFilterHtml = `
                    <div id="ipStatusFilterWrapper" class="d-inline-flex align-items-center ms-2">
                        <label style="font-weight: normal; margin-bottom: 0;">Status:</label>
                        <select id="ipStatusFilter" class="form-select form-select-sm ms-1" style="width: auto;">
                            <option value="All">Show All</option>
                            <option value="0">Unused</option>
                            <option value="1">Used</option>
                        </select>
                    </div>
                `;
                filterContainer.append(customFilterHtml);
                
                // Set nilai filter awal yang benar
                $('#ipStatusFilter').val(statusFilter); 
                
                // Gunakan .off().on() untuk mencegah binding event berulang jika fungsi dipanggil ulang
                $('#ipStatusFilter').off('change').on('change', function() { 
                    const val = $.fn.dataTable.util.escapeRegex($(this).val());
                    if (val === 'All') {
                        // Gunakan regex kosong untuk menghapus filter di kolom
                        filterColumn.search('').draw(); 
                    } else {
                        // Terapkan filter eksak untuk nilai '0' atau '1'
                        filterColumn.search('^' + val + '$', true, false).draw(); 
                    }
                });

                // Adjust columns after initialization
                api.columns.adjust().draw();
            }
        });
    }

    // Initialize DataTable for VLAN search
    function initVlanDataTable() {
        // Properly destroy existing table first
        if (vlanTable && $.fn.DataTable.isDataTable('#vlanTable')) {
            vlanTable.destroy();
            $('#vlanTable').empty(); // Clear the table content
        }
        
        vlanTable = $('#vlanTable').DataTable({
            processing: true,
            serverSide: false,
            paging: true,
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            searching: true,
            ordering: true,
            order: [[0, 'asc']],
            info: true,
            lengthChange: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: base_url + '/getVlanData',
                type: 'GET',
                dataSrc: function(json) {
                    console.log('VLAN data received:', json);
                    return json || [];
                },
                error: function(xhr, status, error) {
                    console.error('Error loading VLAN data:', error);
                    return [];
                }
            },
            columns: [
                { 
                    data: 'mv_vlanid', 
                    title: 'VLAN ID',
                    width: '50%',
                    render: function(data, type, row) { 
                        return data ? data : '-'; 
                    } 
                },
                { 
                    data: 'mv_name', 
                    title: 'VLAN Name',
                    width: '50%',
                    render: function(data, type, row) { 
                        return data ? data : '-'; 
                    } 
                }
            ],
            columnDefs: [
                {
                    targets: '_all',
                    defaultContent: '-'
                }
            ],
            dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>t<"row"<"col-sm-6"i><"col-sm-6"p>>',
            initComplete: function(settings, json) {
                console.log('VLAN DataTable initialization complete');
            },
            drawCallback: function(settings) {
                console.log('VLAN DataTable draw complete');
            }
        });
    }

    // ============================
    // MODAL AND DROPDOWN LOADERS
    // ============================
    
    // Load locations
    function loadLocations(targetSelector, selectedLocation = null) {
        $(targetSelector).html('<option value="">Loading Locations...</option>');
                        
        $.ajax({
            url: base_url + '/getLocations',
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

    // ============================
    // FORM VALIDATION
    // ============================
    
    // Form validation function
    function validateForm(formId) {
        let isValid = true;
        const form = document.getElementById(formId);
                
        // Clear all previous errors
        form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
        const prefix = formId === 'editSwitchManagedForm' ? 'edit_' : 'add_';
        
        // Validate Receive Date (required)
        const receiveDateField = form.querySelector(`#${prefix}receive_date`);
        const receiveDateError = document.getElementById(`${prefix}receive_date_error`);
        
        if (receiveDateField && !receiveDateField.value.trim()) {
            receiveDateError.textContent = 'Receive Date is required';
            receiveDateError.style.display = 'block';
            receiveDateField.classList.add('is-invalid');
            isValid = false;
        }

        // Validate Max Port (required)
        const portCountField = form.querySelector(`#${prefix}port_count`);
        const portCountError = document.getElementById(`${prefix}port_count_error`);
        
        if (portCountField && !portCountField.value.trim()) {
            portCountError.textContent = 'Max Port is required';
            portCountError.style.display = 'block';
            portCountField.classList.add('is-invalid');
            isValid = false;
        } else if (portCountField && portCountField.value.trim()) {
            const portCount = parseInt(portCountField.value);
            if (isNaN(portCount) || portCount < 1) {
                portCountError.textContent = 'Max port must be more than 1';
                portCountError.style.display = 'block';
                portCountField.classList.add('is-invalid');
                isValid = false;
            }
        }

        // Validate IP format if provided
        const ipField = form.querySelector(`#${prefix}ip`);
        const ipError = document.getElementById(`${prefix}ip_error`);
        
        if (ipField && ipField.value.trim()) {
            if (!ipv4Pattern.test(ipField.value.trim())) {
                ipError.textContent = 'Please type a valid IP address';
                ipError.style.display = 'block';
                ipField.classList.add('is-invalid');
                isValid = false;
            }
            // Check if IP has validation error from server (IP not available)
            else if (ipField.classList.contains('is-invalid')) {
                isValid = false; // Keep the server-side validation error
            }
        }
                
        return isValid;
    }

    // Update validation function for port details
    function validatePortDetailForm(formId) {
        let isValid = true;
        const form = document.getElementById(formId);
                
        // Clear all previous errors
        form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
        const prefix = formId === 'editSwitchPortDetailForm' ? 'edit_' : 'add_';
        
        // Validate VLAN ID (required)
        const vlanIdField = form.querySelector(`#${prefix}vlan_id`);
        const vlanIdError = document.getElementById(`${prefix}vlan_id_error`);
        
        if (vlanIdField && !vlanIdField.value.trim()) {
            vlanIdError.textContent = 'VLAN ID is required';
            vlanIdError.style.display = 'block';
            vlanIdField.classList.add('is-invalid');
            isValid = false;
        } else if (vlanIdField && vlanIdField.value.trim() && !/^\d+$/.test(vlanIdField.value.trim())) {
            vlanIdError.textContent = 'VLAN ID must be a number';
            vlanIdError.style.display = 'block';
            vlanIdField.classList.add('is-invalid');
            isValid = false;
        }

        // Validate Status (required)
        const statusField = form.querySelector(`#${prefix}detail_status`);
        const statusError = document.getElementById(`${prefix}detail_status_error`);
        
        if (statusField && statusField.value === '') {
            statusError.textContent = 'Status is required';
            statusError.style.display = 'block';
            statusField.classList.add('is-invalid');
            isValid = false;
        }

        // Validate port range if provided
        const portField = form.querySelector(`#${prefix}port`);
        const portError = document.getElementById(`${prefix}port_error`);
        
        if (portField && portField.value !== '' && (parseInt(portField.value) < 1)) {
            portError.textContent = 'Port must be more than 1';
            portError.style.display = 'block';
            portField.classList.add('is-invalid');
            isValid = false;
        }
                
        return isValid;
    }

    // ============================
    // INITIALIZATION AND EVENT HANDLERS
    // ============================
    
    // Initialize main DataTable on page load
    initializeSwitchManagedDataTable();

    // Handle header checkbox for select/deselect all
    $('#selectAllHeader').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.switch-checkbox').prop('checked', isChecked);
        updateSelectedIds();
    });

    $(document).on('change', '.switch-checkbox', function() {
        updateSelectedIds();
        
        const totalCheckboxes = $('.switch-checkbox').length;
        const checkedCheckboxes = $('.switch-checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#selectAllHeader').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#selectAllHeader').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#selectAllHeader').prop('indeterminate', true).prop('checked', false);
        }
    });

    $('#tabelSwitchManaged').on('draw.dt', function() {
        $('#selectAllHeader').prop('checked', false).prop('indeterminate', false);
        selectedSwitchIds = [];
        updateSelectedCount();
    });

    // ============================
    // MODAL SHOW/HIDE HANDLERS
    // ============================
    
    $('#addSwitchManagedModal').on('shown.bs.modal', function() {
        loadLocations('#add_location');
    });

    $('#addSwitchManagedModal').on('hidden.bs.modal', function() {
        if (!isSearchModalOpen) {
            $('#addSwitchManagedForm')[0].reset();
            $('#addSwitchManagedForm').find('.is-invalid').removeClass('is-invalid');
            $('#addSwitchManagedForm').find('.invalid-feedback').each(function() {
                $(this).text('').hide();
            });
        }
    });

    $('#editSwitchManagedModal').on('hidden.bs.modal', function() {
        if (!isSearchModalOpen) {
            $('#editSwitchManagedForm').find('.is-invalid').removeClass('is-invalid');
            $('#editSwitchManagedForm').find('.invalid-feedback').each(function() {
                $(this).text('').hide();
            });
        }
    });

    // ============================
    // SEARCH MODAL HANDLERS
    // ============================
    
    // Asset search button click handlers to handle both add and edit modals
    $(document).off('click', '.search-asset-btn').on('click', '.search-asset-btn', function() {
        isSearchModalOpen = true;
        
        // Determine which modal is currently open
        if ($('#addSwitchManagedModal').hasClass('show')) {
            currentCallingModal = '#addSwitchManagedModal';
        } else if ($('#editSwitchManagedModal').hasClass('show')) {
            currentCallingModal = '#editSwitchManagedModal';
        }
        
        console.log('Asset search clicked, calling modal:', currentCallingModal);
        
        if (currentCallingModal) {
            savedFormData = saveFormData();
            $(currentCallingModal).modal('hide');
            
            $(currentCallingModal).one('hidden.bs.modal.asset', function() {
                $('#assetSearchModal').modal('show');
            });
        } else {
            $('#assetSearchModal').modal('show');
        }
    });

    $('#assetSearchModal').off('shown.bs.modal').on('shown.bs.modal', function() {
        console.log('Asset search modal shown, initializing table...');
        initAssetDataTable();
    });

    $('#assetSearchModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
        console.log('Asset search modal closed, returning to:', currentCallingModal);
        
        // Properly destroy the asset table
        if (assetTable && $.fn.DataTable.isDataTable('#assetTable')) {
            assetTable.destroy();
            assetTable = null;
        }
        
        // Clear the table HTML
        $('#assetTable').empty();
        
        // Always return to the calling modal if it exists
        if (currentCallingModal) {
            setTimeout(function() {
                $(currentCallingModal).modal('show');
                
                // Restore form data and reset flags after modal is shown
                $(currentCallingModal).one('shown.bs.modal.restored', function() {
                    if (Object.keys(savedFormData).length > 0) {
                        restoreFormData(savedFormData);
                    }
                    
                    // Reset flags
                    isSearchModalOpen = false;
                    currentCallingModal = '';
                    savedFormData = {};
                });
            }, 150);
        } else {
            // Reset flags even if no calling modal
            isSearchModalOpen = false;
            currentCallingModal = '';
            savedFormData = {};
        }
    });

    // IP Address search button click handlers to handle both add and edit modals
    $(document).off('click', '.search-ip-btn').on('click', '.search-ip-btn', function() {
        isSearchModalOpen = true;
        
        // Determine which modal is currently open
        if ($('#addSwitchManagedModal').hasClass('show')) {
            currentCallingModal = '#addSwitchManagedModal';
        } else if ($('#editSwitchManagedModal').hasClass('show')) {
            currentCallingModal = '#editSwitchManagedModal';
        }
        
        console.log('IP search clicked, calling modal:', currentCallingModal);
        
        if (currentCallingModal) {
            savedFormData = saveFormData();
            $(currentCallingModal).modal('hide');
            
            $(currentCallingModal).one('hidden.bs.modal.ip', function() {
                $('#ipAddressModal').modal('show');
            });
        } else {
            $('#ipAddressModal').modal('show');
        }
    });

    // IP Address Search Modal show/hide handlers
    // IP Address Search Modal show/hide handlers
    $('#ipAddressModal').off('shown.bs.modal').on('shown.bs.modal', function() {
        console.log('IP address modal shown, initializing table...');
        // Panggil initIPAddressDataTable dengan status filter default 'All'
        // Jika ada kebutuhan filter spesifik saat pertama kali dibuka, sesuaikan di sini
        initIPAddressDataTable('All'); 
    });

    $('#ipAddressModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
        console.log('IP address modal closed, returning to:', currentCallingModal);
        
        // Properly destroy the IP address table
        if (ipAddressTable && $.fn.DataTable.isDataTable('#ipAddressTable')) {
            ipAddressTable.destroy();
            ipAddressTable = null;
        }
        
        // Clear the table HTML
        $('#ipAddressTable').empty();
        
        // Always return to the calling modal if it exists
        if (currentCallingModal) {
            setTimeout(function() {
                $(currentCallingModal).modal('show');
                
                // Restore form data and reset flags after modal is shown
                $(currentCallingModal).one('shown.bs.modal.restored', function() {
                    if (Object.keys(savedFormData).length > 0) {
                        restoreFormData(savedFormData);
                    }
                    
                    // Reset flags
                    isSearchModalOpen = false;
                    currentCallingModal = '';
                    savedFormData = {};
                });
            }, 150);
        } else {
            // Reset flags even if no calling modal
            isSearchModalOpen = false;
            currentCallingModal = '';
            savedFormData = {};
        }
    });

    // VLAN Search Modal Handlers
    $(document).on('click', '.search-vlan-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $parentModal = $(this).closest('.modal');
        
        // Set the calling modal context
        if ($parentModal.attr('id') === 'addSwitchPortDetailModal') {
            currentCallingDetailModal = 'addSwitchPortDetailModal';
        } else if ($parentModal.attr('id') === 'editSwitchPortDetailModal') {
            currentCallingDetailModal = 'editSwitchPortDetailModal';
        }
        
        // Mark that we're going to VLAN search
        modalActionType = 'search';
        
        // Hide parent modal first
        $parentModal.modal('hide');
        
        // Show VLAN modal after parent is hidden
        $parentModal.one('hidden.bs.modal.vlan', function() {
            // Show VLAN modal
            $('#vlanSearchModal').modal('show');
        });
    });

    // ============================
    // SELECTION HANDLERS FOR SEARCH MODALS
    // ============================
    
    // Handle asset selection from DataTable
    $('#assetTable').off('click', 'tbody tr').on('click', 'tbody tr', function() {
        if (assetTable) {
            const data = assetTable.row(this).data();
            if (!data) return;
            
            console.log('Asset selected:', data);
            console.log('Current calling modal:', currentCallingModal);
            
            const selectedAssetNo = data.display_asset_no || data.e_assetno;
            const assetName = data.e_equipmentname || '';
            const receiveDate = data.e_receivedate ? data.e_receivedate.split(' ')[0] : '';
            
            if (currentCallingModal === '#addSwitchManagedModal') {
                savedFormData.asset_no = selectedAssetNo;
                savedFormData.asset_name = assetName;
                savedFormData.receive_date = receiveDate;
            } else if (currentCallingModal === '#editSwitchManagedModal') {
                savedFormData.edit_asset_no = selectedAssetNo;
                savedFormData.edit_asset_name = assetName;
                savedFormData.edit_receive_date = receiveDate;
            }
            
            console.log('Updated savedFormData:', savedFormData);
            
            $('#assetSearchModal').modal('hide');
        }
    });

    // Handle IP address selection from DataTable - Fixed
    $('#ipAddressTable').off('click', 'tbody tr').on('click', 'tbody tr', function() {
        if (ipAddressTable) {
            const data = ipAddressTable.row(this).data();
            if (!data) return;

            console.log("Data IP yang diklik:", data);
            console.log("Status IP (mip_status):", data.mip_status, "Tipe:", typeof data.mip_status);

            // --- SISIPKAN KODE INI ---
            // Validasi status IP address: HANYA izinkan memilih yang statusnya 0 (Unused)
            // Validasi status IP address: HANYA izinkan memilih yang statusnya 1 (Unused)
            if (parseInt(data.mip_status) !== 1) { // Jika status BUKAN 1 (Unused)
                let statusDisplay = '';
                if (data.mip_status == 0) { // Status 0 = Used (menurut definisi baru Anda)
                    statusDisplay = 'Used';
                } else if (data.mip_status == 25) { // Status 25 = Soft Deleted
                    statusDisplay = 'Soft Deleted';
                } else { // Status lain yang tidak diharapkan
                    statusDisplay = 'unknown';
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'IP Not Available',
                    html: `Alamat IP yang dipilih (<strong>${data.mip_ipadd}</strong>) saat ini berstatus <strong>${statusDisplay}</strong>. Mohon pilih IP yang **Unused** (status 1).`,
                    showConfirmButton: true
                });
                return; // Hentikan proses jika IP tidak tersedia
            }
            // --- AKHIR SISIPAN KODE ---

            if (currentCallingModal === '#addSwitchManagedModal') {
            savedFormData.ip = data.mip_ipadd; // Pastikan ini sudah benar
            } else if (currentCallingModal === '#editSwitchManagedModal') {
                savedFormData.edit_ip = data.mip_ipadd; // Pastikan ini sudah benar
            }

            console.log('Updated savedFormData:', savedFormData);

            $('#ipAddressModal').modal('hide');
        }
    });

    // ============================
    // MODAL CLOSE EVENT HANDLERS
    // ============================

    // Asset Search Modal close handlers
    $('#assetSearchModal').on('hidden.bs.modal', function() {
        console.log('Asset search modal closed, returning to:', currentCallingModal);
        
        // Properly destroy the asset table
        if (assetTable && $.fn.DataTable.isDataTable('#assetTable')) {
            assetTable.destroy();
            assetTable = null;
        }
        
        // Clear the table HTML
        $('#assetTable').empty();
        
        // Always return to the calling modal if it exists
        if (currentCallingModal) {
            setTimeout(function() {
                $(currentCallingModal).modal('show');
                
                // Restore form data and reset flags after modal is shown
                $(currentCallingModal).one('shown.bs.modal.restored', function() {
                    if (Object.keys(savedFormData).length > 0) {
                        restoreFormData(savedFormData);
                    }
                    // --- SISIPKAN KODE INI ---
                    // Pemicu event blur setelah form di-restore
                    if (currentCallingModal === '#addSwitchManagedModal') {
                        $('#add_ip').trigger('blur');
                    } else if (currentCallingModal === '#editSwitchManagedModal') {
                        $('#edit_ip').trigger('blur');
                    }
                    // --- AKHIR SISIPAN ---
                    
                    // Reset flags
                    isSearchModalOpen = false;
                    currentCallingModal = '';
                    savedFormData = {};
                });
            }, 150);
        } else {
            // Reset flags even if no calling modal
            isSearchModalOpen = false;
            currentCallingModal = '';
            savedFormData = {};
        }
    });

    // IP Address Search Modal close handlers
    $('#ipAddressModal').on('hidden.bs.modal', function() {
        console.log('IP address modal closed, returning to:', currentCallingModal);
        
        // Properly destroy the IP address table
        if (ipAddressTable && $.fn.DataTable.isDataTable('#ipAddressTable')) {
            ipAddressTable.destroy();
            ipAddressTable = null;
        }
        
        // Clear the table HTML
        $('#ipAddressTable').empty();
        
        // Always return to the calling modal if it exists
        if (currentCallingModal) {
            setTimeout(function() {
                $(currentCallingModal).modal('show');
                
                // Restore form data and reset flags after modal is shown
                $(currentCallingModal).one('shown.bs.modal.restored', function() {
                    if (Object.keys(savedFormData).length > 0) {
                        restoreFormData(savedFormData);
                    }
                    // --- SISIPKAN KODE INI ---
                    // Pemicu event blur setelah form di-restore
                    if (currentCallingModal === '#addSwitchManagedModal') {
                        $('#add_ip').trigger('blur');
                    } else if (currentCallingModal === '#editSwitchManagedModal') {
                        $('#edit_ip').trigger('blur');
                    }
                    // --- AKHIR SISIPAN ---
                    
                    // Reset flags
                    isSearchModalOpen = false;
                    currentCallingModal = '';
                    savedFormData = {};
                });
            }, 150);
        } else {
            // Reset flags even if no calling modal
            isSearchModalOpen = false;
            currentCallingModal = '';
            savedFormData = {};
        }
    });

    // ============================
    // AUTO-FILL HANDLERS
    // ============================
    
    // Auto-fill asset details when asset no is entered manually
    $('#add_asset_no, #edit_asset_no').on('blur', function() {
        const assetNo = $(this).val().trim();
        const isEditMode = $(this).attr('id').startsWith('edit_');
        
        if (assetNo) {
            $.ajax({
                url: base_url + '/getAssetNo',
                type: 'GET',
                data: { assetNo: assetNo },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        const receiveDate = response.data.receive_date || '';
                        const assetName = response.data.asset_name || '';
                        
                        if (isEditMode) {
                            $('#edit_asset_name').val(assetName);
                            $('#edit_receive_date').val(receiveDate);
                        } else {
                            $('#add_asset_name').val(assetName);
                            $('#add_receive_date').val(receiveDate);
                        }
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Asset Not Available',
                            text: 'This asset is not found or already in use by another switch.',
                            showConfirmButton: true
                        });
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
        }
    });

    // IP Validation Logic
    $('#add_ip').on('input', function() {
        if ($(this).val() && !ipv4Pattern.test($(this).val())) {
            $(this).addClass('is-invalid');
            $('#add_ip_error').text('Invalid IP format').show();
        } else {
            $(this).removeClass('is-invalid');
            $('#add_ip_error').text('').hide();
        }
    });

    $('#edit_ip').on('input', function() {
        if ($(this).val() && !ipv4Pattern.test($(this).val())) {
            $(this).addClass('is-invalid');
            $('#edit_ip_error').text('Invalid IP format').show();
        } else {
            $(this).removeClass('is-invalid');
            $('#edit_ip_error').text('').hide();
        }
    });

    // Max Count validation
    $('#add_port_count, #edit_port_count').on('input', function() {
        const portCount = parseInt($(this).val());
        const fieldId = $(this).attr('id');
        const errorElementId = `${fieldId}_error`;
        
        if (isNaN(portCount) || portCount < 1) {
            $(this).addClass('is-invalid');
            $(`#${errorElementId}`).text('Max count must be more than 1 ').show();
        } else {
            $(this).removeClass('is-invalid');
            $(`#${errorElementId}`).text('').hide();
        }
    });

    // Handle IP Address blur - check availability
    $('#add_ip, #edit_ip').on('blur', function() {
        const ipInput = $(this).val().trim();
        const fieldId = $(this).attr('id');
        const errorElementId = `${fieldId}_error`;
        const errorElement = $(`#${errorElementId}`);
        
        // Clear previous errors first
        errorElement.text('').hide();
        $(`#${fieldId}`).removeClass('is-invalid');
        
        if (ipInput) {
            // Check IP format first
            if (!ipv4Pattern.test(ipInput)) {
                errorElement.text('Please type a valid IP address');
                errorElement.show();
                $(`#${fieldId}`).addClass('is-invalid');
                return;
            }

            // Check availability on server
            $.ajax({
                url: base_url + '/getIPAddresses',
                type: 'GET',
                data: { ipAddress: ipInput },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        const ipDetails = response.data;
                        // Menurut definisi baru: 1 = Unused (tersedia), 0 = Used (tidak tersedia)
                        if (ipDetails.mip_status == 1) { // Jika IP tersedia (Unused)
                            errorElement.text('').hide();
                            $(`#${fieldId}`).removeClass('is-invalid');
                        } else if (ipDetails.mip_status == 0) { // Jika IP terpakai (Used)
                            errorElement.text('IP Address is currently used by another device. Please select an unused IP.'); // Ini pesan yang akan muncul jika IP 0 (Used)
                            errorElement.show();
                            $(`#${fieldId}`).addClass('is-invalid');
                        } else if (ipDetails.mip_status == 25) { // Jika IP soft-deleted
                            errorElement.text('IP Address is soft-deleted and cannot be used.');
                            errorElement.show();
                            $(`#${fieldId}`).addClass('is-invalid');
                        } else { // Status tidak dikenal
                            errorElement.text('IP Address has an unknown status and is not available.'); // Pesan lebih umum
                            errorElement.show();
                            $(`#${fieldId}`).addClass('is-invalid');
                        }
                    } else {
                        // IP not found in database at all
                        errorElement.text('IP Address not found in IP Master database.');
                        errorElement.show();
                        $(`#${fieldId}`).addClass('is-invalid');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking IP availability:', error);
                    errorElement.text('Error checking IP address availability');
                    errorElement.show();
                    $(`#${fieldId}`).addClass('is-invalid');
                }
            });
        }
    });

    // ============================
    // CRUD OPERATIONS FOR MAIN SWITCH
    // ============================
    
    // Add Switch form submission
    $('#addSwitchManagedForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear all previous validation states
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').each(function() {
            $(this).text('').hide();
        });

        // Validate form
        if (!validateForm('addSwitchManagedForm')) {
            // Focus on first invalid field
            const firstInvalidField = $(this).find('.is-invalid').first();
            if (firstInvalidField.length) {
                firstInvalidField.focus();
            }
            return;
        }

        $('#saveSwitchManaged').prop('disabled', true).text('Saving...');

        $.ajax({
            url: base_url + '/store',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success', 
                        title: 'Success!', 
                        text: response.message, 
                        timer: 1500, 
                        showConfirmButton: false
                    }).then(() => {
                        $('#addSwitchManagedModal').modal('hide');
                        tabelSwitchManaged.ajax.reload();
                    });
                } else {
                    // Handle server-side validation errors
                    if (response.errors) {
                        for (const field in response.errors) {
                            if (response.errors.hasOwnProperty(field)) {
                                const fieldId = `add_${field}`;
                                const errorElementId = `${fieldId}_error`;
                                
                                $(`#${fieldId}`).addClass('is-invalid');
                                $(`#${errorElementId}`).text(response.errors[field]).show();
                            }
                        }
                        
                        // Focus on first invalid field
                        const firstInvalidField = $('#addSwitchManagedForm').find('.is-invalid').first();
                        if (firstInvalidField.length) {
                            firstInvalidField.focus();
                        }
                    } else if (response.message) {
                        Swal.fire('Error', response.message, 'error');
                    } else {
                        Swal.fire('Error', 'Failed to add Managed Switch. Please try again.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Request failed: ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += xhr.responseJSON.message;
                } else {
                    errorMessage += error;
                }
                Swal.fire('Error', errorMessage, 'error');
            },
            complete: function() {
                $('#saveSwitchManaged').prop('disabled', false).text('Submit Configuration');
            }
        });
    });

    // Edit button click handler
    $('#tabelSwitchManaged').on('click', '.edit-btn', function() {
        const id = $(this).data('id');

        $('#editSwitchManagedForm')[0].reset();
        $('#editSwitchManagedModalLabel').text('Edit Switch Managed Configuration');
        $('#updateSwitchManaged').show();
        $('.modal-footer .btn-secondary').text('Cancel');
        $('#edit_id').val(id);
        $('#editSwitchManagedModal').modal('show');
        
        $.ajax({
            url: base_url + '/getSwitchManagedById',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const sw = response.data;
                    
                    $('#edit_asset_no').val(sw.tsm_assetno);
                    $('#edit_asset_name').val(sw.tsm_assetname);
                    $('#edit_receive_date').val(sw.tsm_receivedate ? sw.tsm_receivedate.split(' ')[0] : '');
                    $('#edit_port_count').val(sw.tsm_port);
                    $('#edit_ip').val(sw.tsm_ipaddress);
                    
                    loadLocations('#edit_location', sw.tsm_location);
                    
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load switch details',
                    });
                    $('#editSwitchManagedModal').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching switch details:', error);
                $('#editSwitchManagedModal').modal('hide');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load switch details. Please try again.'
                });
            }
        });
    });

    // Update form submission
    $('#editSwitchManagedForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear all previous validation states
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').each(function() {
            $(this).text('').hide();
        });

        // Validate form
        if (!validateForm('editSwitchManagedForm')) {
            // Focus on first invalid field
            const firstInvalidField = $(this).find('.is-invalid').first();
            if (firstInvalidField.length) {
                firstInvalidField.focus();
            }
            return;
        }

        $('#updateSwitchManaged').prop('disabled', true).text('Updating...');

        $.ajax({
            url: base_url + '/update',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success', 
                        title: 'Updated!', 
                        text: response.message, 
                        timer: 1500, 
                        showConfirmButton: false
                    }).then(() => {
                        $('#editSwitchManagedModal').modal('hide');
                        tabelSwitchManaged.ajax.reload();
                        });
                } else {
                    // Handle server-side validation errors
                    if (response.errors) {
                        for (const field in response.errors) {
                            if (response.errors.hasOwnProperty(field)) {
                                const fieldId = `edit_${field}`;
                                const errorElementId = `${fieldId}_error`;
                                
                                $(`#${fieldId}`).addClass('is-invalid');
                                $(`#${errorElementId}`).text(response.errors[field]).show();
                            }
                        }
                        
                        // Focus on first invalid field
                        const firstInvalidField = $('#editSwitchManagedForm').find('.is-invalid').first();
                        if (firstInvalidField.length) {
                            firstInvalidField.focus();
                        }
                    } else if (response.message) {
                        Swal.fire('Error', response.message, 'error');
                    } else {
                        Swal.fire('Error', 'Failed to update Managed Switch. Please try again.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Request failed: ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += xhr.responseJSON.message;
                } else {
                    errorMessage += error;
                }
                Swal.fire('Error', errorMessage, 'error');
            },
            complete: function() {
                $('#updateSwitchManaged').prop('disabled', false).text('Update Configuration');
            }
        });
    });

    // Delete button click handler
    $('#tabelSwitchManaged').on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this! This will also delete all associated port configurations.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + '/delete',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success', title: 'Deleted!', text: response.message, timer: 1500, showConfirmButton: false
                            });
                            tabelSwitchManaged.ajax.reload();
                        } else {
                            Swal.fire('Error', response.message || 'Delete failed', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
                    }
                });
            }
        });
    });

    // ============================
    // PORT DETAIL MODAL OPERATIONS
    // ============================
    
    // View Detail button click handler
    $('#tabelSwitchManaged').on('click', '.view-detail-btn', function() {
        const id = $(this).data('id');
        selectedSwitchId = id;
        
        // Get data from the current DataTable row for efficiency
        const rowData = tabelSwitchManaged.row($(this).closest('tr')).data();
        let modalTitle = 'Switch Details - ID: ' + id;
        
        if (rowData) {
            // Use asset name if available, otherwise use asset no, otherwise use ID
            if (rowData.tsm_assetname && rowData.tsm_assetname.trim() !== '' && rowData.tsm_assetname !== '-') {
                modalTitle = 'Switch Details - ' + rowData.tsm_assetname;
            } else if (rowData.tsm_assetno && rowData.tsm_assetno.toString().trim() !== '' && rowData.tsm_assetno !== '-') {
                modalTitle = 'Switch Details - Asset No: ' + rowData.tsm_assetno;
            } else {
                modalTitle = 'Switch Details - ID: ' + id;
            }
        }
        
        $('#switchDetailModalLabel').text(modalTitle);
        $('#add_detail_header_id').val(id);

        // Load switch port details and show modal
        loadSwitchDetailPorts(id);
        $('#switchDetailModal').modal('show');
    });

    // Initialize Switch Detail Ports DataTable with proper status filtering
    function loadSwitchDetailPorts(tsd_switchid, statusFilter = 'All') {
        // Ensure that the DataTable is not reinitialized if it already exists
        if (switchDetailTable) {
            // Update the AJAX URL and reload the data - use GET parameter instead of route parameter
            switchDetailTable.ajax.url(base_url + '/getSwitchDetailPortData?tsd_switchid=' + tsd_switchid).load(function() {
                // Apply the status filter after data is loaded
                setTimeout(function() {
                    // Apply the filter based on the selected status
                    if (statusFilter !== 'All') {
                        switchDetailTable.column(6).search('^' + statusFilter + '$', true, false).draw();
                    } else {
                        switchDetailTable.column(6).search('').draw();
                    }

                    // Update the status filter dropdown with the current filter value
                    $('#portStatusFilter').val(statusFilter);
                    window.currentPortStatusFilter = statusFilter;
                }, 100);
            });
            return;
        }

        // Initialize new DataTable if it doesn't exist yet
        switchDetailTable = $('#switchDetailTable').DataTable({
            scrollX: true,
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            processing: true,
            serverSide: false,
            ajax: {
                url: base_url + '/getSwitchDetailPortData?tsd_switchid=' + tsd_switchid, // Updated URL with GET parameter
                dataSrc: "",
                beforeSend: function() {
                    let spinner = `
                        <div class="align-items-center justify-content-center gap-2">
                            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                        </div>`;
                    $('#switchDetailTable tbody').html(`<tr><td colspan="7">${spinner}</td></tr>`);
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                    $('#switchDetailTable tbody').html('<tr><td colspan="7" class="text-center">Error loading data. Please try again.</td></tr>');
                }
            },
            columns: [
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    width: '15%',
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex justify-content-center gap-2">
                                <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-detail-port-btn" data-id="${row.tsd_id}" title="Edit Port Detail">
                                    <i class="fa fa-pen-to-square"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-detail-port-btn" data-id="${row.tsd_id}" title="Delete Port Detail">
                                    <i class="fa fa-trash-can"></i>
                                </a>
                            </div>`;
                    }
                },
                {
                    data: 'tsd_id',
                    title: 'ID',
                    width: '8%',
                    className: 'text-center'
                },
                {
                    data: 'tsd_port',
                    title: 'Port',
                    width: '10%',
                    className: 'text-center',
                    render: function(data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'tsd_type',
                    title: 'Type',
                    width: '17%',
                    render: function(data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'tsd_vlanid',
                    title: 'VLAN ID',
                    width: '12%',
                    className: 'text-center',
                    render: function(data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'tsd_vlanname',
                    title: 'VLAN Name',
                    width: '28%',
                    render: function(data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'tsd_status',
                    title: 'Status',
                    width: '10%',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
                        }
                        return data; // For filtering and sorting
                    }
                }
            ],
            order: [[2, 'asc']], // Order by Port
            language: {
                "sSearch": "Search:",
                "sEmptyTable": "No port details configured for this switch.",
                "sZeroRecords": "No matching records found",
                "sLengthMenu": "Show _MENU_ entries"
            },
            drawCallback: function() {
                // Initialize filters only once after the first draw
                if (!window.switchDetailFiltersInitialized) {
                    window.switchDetailFiltersInitialized = true;

                    const lengthControl = $('#switchDetailTable_length');

                    // Create the status filter dropdown dynamically
                    const statusFilterHtml = `
                        <div class="filter-wrapper" id="switchDetailStatusFilterWrapper" style="display: inline-block; margin-left: 20px;">
                            <label style="font-weight: normal;">
                                Status:
                                <select id="portStatusFilter" class="form-select form-select-sm" style="display: inline-block; width: auto; min-width: 120px;">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </label>
                        </div>
                    `;
                    lengthControl.append(statusFilterHtml);

                    // Bind the filter event to update the table on status change
                    $('#portStatusFilter').on('change', function() {
                        const filterValue = $(this).val();
                        window.currentPortStatusFilter = filterValue;

                        // Apply the filter to the Status column
                        if (filterValue === '') {
                            switchDetailTable.column(6).search('').draw(); // Clear filter
                        } else {
                            switchDetailTable.column(6).search('^' + filterValue + '$', true, false).draw(); // Apply filter
                        }
                    });
                }

                // Apply initial filter and set dropdown value
                setTimeout(function() {
                    // Apply the saved filter or default to 'All'
                    const filterToApply = window.currentPortStatusFilter || 'All';
                    $('#portStatusFilter').val(filterToApply === 'All' ? '' : filterToApply);
                    window.currentPortStatusFilter = filterToApply;

                    // Only apply the filter if it's not 'All'
                    if (filterToApply !== 'All' && filterToApply !== '') {
                        switchDetailTable.column(6).search('^' + filterToApply + '$', true, false).draw();
                    } else {
                        switchDetailTable.column(6).search('').draw();
                    }
                }, 100);
            }
        });
    }

    // ============================
    // PORT DETAIL FORM HANDLERS
    // ============================
    
    // Add Port Detail Modal Handlers
    $('#addSwitchPortDetailModal').off('shown.bs.modal').on('shown.bs.modal', function() {
        const switchId = $('#add_detail_header_id').val();
        
        // Only populate ports if not already populated or if switch ID changed
        const currentPortOptions = $('#add_port option').length;
        const lastSwitchId = $(this).data('last-switch-id');
        
        if (switchId && (currentPortOptions <= 1 || lastSwitchId !== switchId)) {
            // Store the current switch ID
            $(this).data('last-switch-id', switchId);
            // Populate available ports when modal is shown
            populatePortOptions('#add_port', switchId);
        }
        
        // Clear VLAN validation errors
        $('#add_vlan_id_error').text('').hide();
        $('#add_vlan_id').removeClass('is-invalid');
        $('#add_detail_status_error').text('').hide();
        $('#add_detail_status').removeClass('is-invalid');
        
        // Restore VLAN data if coming from VLAN search
        if (isFromVlanSearch && window.selectedVlanData && window.selectedVlanData.targetModal === 'addSwitchPortDetailModal') {
            $('#add_vlan_id').val(window.selectedVlanData.vlanId);
            $('#add_vlan_name').val(window.selectedVlanData.vlanName);
            $('#add_vlan_name_display').text(window.selectedVlanData.vlanName || '');
            
            console.log('Restored VLAN data in ADD modal:', window.selectedVlanData.vlanId, window.selectedVlanData.vlanName);
            
            // Reset flags after restoration
            setTimeout(function() {
                isFromVlanSearch = false;
                currentCallingDetailModal = '';
                window.selectedVlanData = null;
            }, 500);
        } else if (!isFromVlanSearch) {
            // Only clear if not from VLAN search
            $('#add_vlan_name_display').text('');
        }
    });

    // Submit Add Port Detail Form
    $('#submit-port-detail-btn').off('click').on('click', function() {
        const headerId = $('#add_detail_header_id').val();
        if (!headerId) {
            Swal.fire('Error', 'Switch ID is missing. Cannot add port detail.', 'error');
            return;
        }

        // Clear previous validation errors
        $('#addSwitchPortDetailForm input, #addSwitchPortDetailForm select').removeClass('is-invalid');
        $('#addSwitchPortDetailForm .invalid-feedback').text('').hide();

        // Validate form with new validation function
        if (!validatePortDetailForm('addSwitchPortDetailForm')) {
            // Focus on first invalid field
            const firstInvalidField = $('#addSwitchPortDetailForm').find('.is-invalid').first();
            if (firstInvalidField.length) {
                firstInvalidField.focus();
            }
            return;
        }

        // Set action type to save
        modalActionType = 'save';

        // Disable button during submission
        $(this).prop('disabled', true).text('Saving...');
        
        $.ajax({
            url: base_url + '/storeSwitchDetailPort',
            type: 'POST',
            data: $('#addSwitchPortDetailForm').serialize(),
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success', 
                        title: 'Success!', 
                        text: response.message, 
                        timer: 1500, 
                        showConfirmButton: false
                    }).then(() => {
                        $('#addSwitchPortDetailModal').modal('hide');
                        
                        // Wait for modal to close, then show switch detail modal and reload data
                        $('#addSwitchPortDetailModal').one('hidden.bs.modal.success', function() {
                            $('#switchDetailModal').modal('show');
                            
                            // Reload fresh data after modal is shown
                            $('#switchDetailModal').one('shown.bs.modal.reload', function() {
                                if (switchDetailTable) {
                                    switchDetailTable.ajax.reload(null, false);
                                }
                            });
                        });
                    });
                } else {
                    // Reset action type on error so cancel behavior works
                    modalActionType = null;
                    
                    if (response.errors) {
                        for (const field in response.errors) {
                            if (response.errors.hasOwnProperty(field)) {
                                const fieldId = `add_${field}`;
                                $(`#${fieldId}`).addClass('is-invalid');
                                $(`#${fieldId}_error`).text(response.errors[field]).show();
                            }
                        }
                        
                        // Focus on first invalid field
                        const firstInvalidField = $('#addSwitchPortDetailForm').find('.is-invalid').first();
                        if (firstInvalidField.length) {
                            firstInvalidField.focus();
                        }
                    } else if (response.message) {
                        Swal.fire('Error', response.message, 'error');
                    } else {
                        Swal.fire('Error', 'Failed to add port detail.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                // Reset action type on error so cancel behavior works
                modalActionType = null;
                console.error('Add port error:', error, xhr.responseText);
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            },
            complete: function() {
                $('#submit-port-detail-btn').prop('disabled', false).text('Submit Port');
            }
        });
    });

    // Edit Port Detail Modal Handlers
    $('#switchDetailTable').off('click', '.edit-detail-port-btn').on('click', '.edit-detail-port-btn', function() {
        const tsd_id = $(this).data('id');

        $.ajax({
            url: base_url + '/getSwitchDetailPortById',
            type: 'POST',
            data: { tsd_id: tsd_id },
            success: function(response) {
                if (response.status) {
                    const d = response.data;
                    
                    // Populate form fields
                    $('#edit_tsd_id').val(d.tsd_id);
                    $('#edit_detail_header_id').val(d.tsd_switchid);
                    $('#edit_type').val(d.tsd_type || '');
                    $('#edit_vlan_id').val(d.tsd_vlanid || '');
                    $('#edit_vlan_name').val(d.tsd_vlanname || '');
                    $('#edit_vlan_name_display').text(d.tsd_vlanname || ''); // Show VLAN name in display div
                    $('#edit_detail_status').val(d.tsd_status);

                    // Populate available ports for the switch including current port
                    populatePortOptions('#edit_port', d.tsd_switchid, d.tsd_port);

                    // Clear validation errors
                    $('#editSwitchPortDetailForm').find('.is-invalid').removeClass('is-invalid');
                    $('#editSwitchPortDetailForm').find('.invalid-feedback').text('').hide();

                    modalActionType = null; // Reset action type after handling
                        
                        modalActionType = null; // Reset action type

                        // Hide switch detail modal and show edit modal
                        $('#switchDetailModal').modal('hide');
                        
                        // Show edit modal after switch detail is hidden
                        $('#switchDetailModal').one('hidden.bs.modal.editport', function() {
                            $('#editSwitchPortDetailModal').modal('show');
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Detail port data not found', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Edit port fetch error:', error, xhr.responseText);
                    Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
                }
            });
        });

        $('#addSwitchPortDetailModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            console.log('Add port modal hidden, action type:', modalActionType);
            
            // Reset form when modal is hidden (unless it's from VLAN search)
            if (!isFromVlanSearch && modalActionType !== 'search') {
                $('#addSwitchPortDetailForm')[0].reset();
                $('#addSwitchPortDetailForm').find('.is-invalid').removeClass('is-invalid');
                $('#addSwitchPortDetailForm').find('.invalid-feedback').text('').hide();
                $('#add_vlan_name').val('');
                
                // Clear stored switch ID
                $(this).removeData('last-switch-id');
            }
            
            // Only restore switch detail modal if action was 'cancel' or null (not save or VLAN search)
            if (modalActionType === 'cancel' || (modalActionType === null && !isFromVlanSearch)) {
                // Clean up VLAN search flags if we're truly canceling
                if (!isFromVlanSearch) {
                    currentCallingDetailModal = '';
                    window.selectedVlanData = null;
                }
                
                // Show switch detail modal without reloading data
                setTimeout(() => {
                    $('#switchDetailModal').modal('show');
                }, 150);
            } else if (modalActionType === 'save') {
                // For save action, the success callback will handle showing switch detail modal
                currentCallingDetailModal = '';
                window.selectedVlanData = null;
            }
            
            // Reset action type after handling
            modalActionType = null;
        });

        // Add event handlers for Cancel buttons to set action type
        $('#addSwitchPortDetailModal').off('click', '[data-bs-dismiss="modal"]').on('click', '[data-bs-dismiss="modal"]', function() {
            modalActionType = 'cancel';
        });

        $('#editSwitchPortDetailModal').off('click', '[data-bs-dismiss="modal"]').on('click', '[data-bs-dismiss="modal"]', function() {
            modalActionType = 'cancel';
        });

        // Submit Edit Port Detail Form
        $('#update-port-detail-btn').off('click').on('click', function() {
            const tsd_id = $('#edit_tsd_id').val();
            const headerId = $('#edit_detail_header_id').val();

            if (!tsd_id || !headerId) {
                Swal.fire('Error', 'Missing required data. Cannot update port detail.', 'error');
                return;
            }

            // Clear previous validation errors
            $('#editSwitchPortDetailForm input, #editSwitchPortDetailForm select').removeClass('is-invalid');
            $('#editSwitchPortDetailForm .invalid-feedback').text('').hide();

            // Validate form with new validation function
            if (!validatePortDetailForm('editSwitchPortDetailForm')) {
                // Focus on first invalid field
                const firstInvalidField = $('#editSwitchPortDetailForm').find('.is-invalid').first();
                if (firstInvalidField.length) {
                    firstInvalidField.focus();
                }
                return;
            }

            // Set action type to save
            modalActionType = 'save';

            // Disable button during submission
            $(this).prop('disabled', true).text('Updating...');

            $.ajax({
                url: base_url + '/updateSwitchDetailPort',
                type: 'POST',
                data: $('#editSwitchPortDetailForm').serialize(),
                success: function(response) {
                    console.log('Update port response:', response);
                    
                    if (response.status) {
                        Swal.fire({
                            icon: 'success', 
                            title: 'Updated!', 
                            text: response.message, 
                            timer: 1500, 
                            showConfirmButton: false
                        }).then(() => {
                            $('#editSwitchPortDetailModal').modal('hide');
                            
                            // Wait for modal to close, then show switch detail modal and reload data
                            $('#editSwitchPortDetailModal').one('hidden.bs.modal.success', function() {
                                $('#switchDetailModal').modal('show');
                                
                                // Reload fresh data after modal is shown
                                $('#switchDetailModal').one('shown.bs.modal.reload', function() {
                                    if (switchDetailTable) {
                                        switchDetailTable.ajax.reload(null, false);
                                    }
                                });
                            });
                        });
                    } else {
                        // Reset action type on error so cancel behavior works
                        modalActionType = null;
                        
                        if (response.errors) {
                            for (const field in response.errors) {
                                if (response.errors.hasOwnProperty(field)) {
                                    const fieldId = `edit_${field}`;
                                    $(`#${fieldId}`).addClass('is-invalid');
                                    $(`#${fieldId}_error`).text(response.errors[field]).show();
                                }
                            }
                            
                            // Focus on first invalid field
                            const firstInvalidField = $('#editSwitchPortDetailForm').find('.is-invalid').first();
                            if (firstInvalidField.length) {
                                firstInvalidField.focus();
                            }
                        } else if (response.message) {
                            Swal.fire('Error', response.message, 'error');
                        } else {
                            Swal.fire('Error', 'Failed to update port detail.', 'error');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    // Reset action type on error so cancel behavior works
                    modalActionType = null;
                    console.error('Update port error:', error, xhr.responseText);
                    Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
                },
                complete: function() {
                    $('#update-port-detail-btn').prop('disabled', false).text('Update Port');
                }
            });
        });

        // Delete Port Detail Handler
        $('#switchDetailTable').off('click', '.delete-detail-port-btn').on('click', '.delete-detail-port-btn', function() {
            const tsd_id = $(this).data('id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: base_url + '/deleteSwitchDetailPort',
                        type: 'POST',
                        data: { tsd_id: tsd_id },
                        success: function(response) {
                            
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success', 
                                    title: 'Deleted!', 
                                    text: response.message, 
                                    timer: 1500, 
                                    showConfirmButton: false
                                });
                                // Reload the table data
                                if (switchDetailTable) {
                                    switchDetailTable.ajax.reload(null, false); // Keep current page
                                }
                            } else {
                                Swal.fire('Error', response.message || 'Delete failed', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
                        }
                    });
                }
            });
        });

        // Handle VLAN selection from DataTable
        $(document).on('click', '#vlanTable tbody tr', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (!vlanTable) {
                console.log('VLAN table not initialized');
                return;
            }
            
            const data = vlanTable.row(this).data();
            if (!data) {
                console.log('No data found for clicked row');
                return;
            }

            console.log('VLAN selected:', data);

            // Get VLAN ID and Name, use empty string if null/undefined
            const vlanId = data.mv_vlanid !== null && data.mv_vlanid !== undefined ? data.mv_vlanid : '';
            const vlanName = data.mv_name !== null && data.mv_name !== undefined ? data.mv_name : '';

            // Store selected values globally to preserve them
            window.selectedVlanData = {
                vlanId: vlanId,
                vlanName: vlanName,
                targetModal: currentCallingDetailModal
            };

            console.log('Stored VLAN data:', window.selectedVlanData);

            // Update form fields based on calling modal
            if (currentCallingDetailModal === 'addSwitchPortDetailModal') {
                $('#add_vlan_id').val(vlanId);
                $('#add_vlan_name_hidden').val(vlanName); // Mengisi hidden input
                $('#add_vlan_name').val(vlanName || ''); // Mengisi visible input
                $('#add_vlan_id').removeClass('is-invalid');
                $('#add_vlan_id_error').text('').hide();
            }   else if (currentCallingDetailModal === 'editSwitchPortDetailModal') {
                $('#edit_vlan_id').val(vlanId);
                $('#edit_vlan_name_hidden').val(vlanName); // Mengisi hidden input
                $('#edit_vlan_name').val(vlanName || ''); // Mengisi visible input
                $('#edit_vlan_id').removeClass('is-invalid');
                $('#edit_vlan_id_error').text('').hide();
            }

            // Mark that we're returning from VLAN search
            isFromVlanSearch = true;

            // Close VLAN search modal
            $('#vlanSearchModal').modal('hide');
        });

       $('#vlanSearchModal').on('show.bs.modal', function() {
            console.log('VLAN modal showing...');
        });

        $('#vlanSearchModal').on('shown.bs.modal', function() {
            console.log('VLAN modal shown, initializing DataTable...');
            
            // Initialize DataTable after modal is fully shown
            setTimeout(function() {
                initVlanDataTable();
            }, 100);
        });

        $('#vlanSearchModal').on('hidden.bs.modal', function() {
            console.log('VLAN modal closed, returning to:', currentCallingDetailModal);
            
            // Properly destroy the VLAN table
            if (vlanTable && $.fn.DataTable.isDataTable('#vlanTable')) {
                vlanTable.destroy();
                vlanTable = null;
            }
            
            // Clear the table HTML
            $('#vlanTable').empty();
            
            // Reopen the correct parent modal after VLAN search is closed
            if (currentCallingDetailModal === 'addSwitchPortDetailModal') {
                setTimeout(function() {
                    $('#addSwitchPortDetailModal').modal('show');
                }, 150);
            } else if (currentCallingDetailModal === 'editSwitchPortDetailModal') {
                setTimeout(function() {
                    $('#editSwitchPortDetailModal').modal('show');
                }, 150);
            }
        });

        // Add this function to handle VLAN ID manual input and auto-fill VLAN Name
        function handleVlanIdInput(vlanIdField, vlanNameField, errorField) {
            const vlanId = $(vlanIdField).val().trim();
            
            // Clear previous errors
            $(errorField).text('').hide();
            $(vlanIdField).removeClass('is-invalid');
            $(vlanNameField).val(''); // Clear VLAN name field
            
            if (vlanId && vlanId !== '') {
                // Check if it's a valid number
                if (!/^\d+$/.test(vlanId)) {
                    $(errorField).text('VLAN ID must be a number').show();
                    $(vlanIdField).addClass('is-invalid');
                    return;
                }
                
                // Fetch VLAN details from server
                $.ajax({
                    url: base_url + '/getVlanByID',
                    type: 'GET',
                    data: { vlanId: vlanId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data) {
                            // Auto-fill VLAN name
                            $(vlanNameField).val(response.data.mv_name || '');
                            $(errorField).text('').hide();
                            $(vlanIdField).removeClass('is-invalid');
                        } else {
                            // VLAN not found - show warning but don't make it invalid
                            $(vlanNameField).val(''); 
                            console.log('VLAN ID ' + vlanId + ' not found in database');
                        }
                        },
                    error: function(xhr, status, error) {
                        console.error('Error checking VLAN ID:', error);
                        $(vlanNameField).val('');
                    }
                });
            }
        }

        // Add VLAN ID input handlers for both add and edit forms
        $('#add_vlan_id').on('input', function() {
            const vlanId = $(this).val().trim();
            const errorField = '#add_vlan_id_error';
            
            // Clear previous errors
            $(errorField).text('').hide();
            $(this).removeClass('is-invalid');
            
            // Only validate if there's a value
            if (vlanId && vlanId !== '') {
                if (!/^\d+$/.test(vlanId)) {
                    $(errorField).text('VLAN ID must be a number').show();
                    $(this).addClass('is-invalid');
                }
            }
        });

        $('#edit_vlan_id').on('input', function() {
            const vlanId = $(this).val().trim();
            const errorField = '#edit_vlan_id_error';
            
            // Clear previous errors
            $(errorField).text('').hide();
            $(this).removeClass('is-invalid');
            
            // Only validate if there's a value
            if (vlanId && vlanId !== '') {
                if (!/^\d+$/.test(vlanId)) {
                    $(errorField).text('VLAN ID must be a number').show();
                    $(this).addClass('is-invalid');
                }
            }
        });

        // Auto-fill VLAN name when VLAN ID is entered manually
        $('#add_vlan_id').on('blur', function() {
            const vlanId = $(this).val().trim();
            if (vlanId && /^\d+$/.test(vlanId)) {
                $.ajax({
                    url: base_url + '/getVlanById',
                    type: 'GET',
                    data: { vlanId: vlanId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data) {
                            const vlanName = response.data.mv_name || '';
                            $('#add_vlan_name').val(vlanName);
                            $('#add_vlan_name_display').text(vlanName);
                        } else {
                            $('#add_vlan_name').val('');
                            $('#add_vlan_name_display').text('');
                        }
                    },
                    error: function() {
                        $('#add_vlan_name').val('');
                        $('#add_vlan_name_display').text('');
                    }
                });
            } else {
                $('#add_vlan_name').val('');
                $('#add_vlan_name_display').text('');
            }
        });

        $('#edit_vlan_id').on('blur', function() {
            const vlanId = $(this).val().trim();
            if (vlanId && /^\d+$/.test(vlanId)) {
                $.ajax({
                    url: base_url + '/getVlanById',
                    type: 'GET',
                    data: { vlanId: vlanId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data) {
                            const vlanName = response.data.mv_name || '';
                            $('#edit_vlan_name').val(vlanName);
                            $('#edit_vlan_name_display').text(vlanName);
                        } else {
                            $('#edit_vlan_name').val('');
                            $('#edit_vlan_name_display').text('');
                        }
                    },
                    error: function() {
                        $('#edit_vlan_name').val('');
                        $('#edit_vlan_name_display').text('');
                    }
                });
            } else {
                $('#edit_vlan_name').val('');
                $('#edit_vlan_name_display').text('');
            }
        });

        $('#editSwitchPortDetailModal').on('shown.bs.modal', function() {
            console.log('Edit modal shown, isFromVlanSearch:', isFromVlanSearch);
            console.log('Stored VLAN data:', window.selectedVlanData);
            
            $('#edit_vlan_id_error').text('').hide();
            $('#edit_vlan_id').removeClass('is-invalid');
            $('#edit_detail_status_error').text('').hide();
            $('#edit_detail_status').removeClass('is-invalid');
            
            // Restore VLAN data if coming from VLAN search
            if (isFromVlanSearch && window.selectedVlanData && window.selectedVlanData.targetModal === 'editSwitchPortDetailModal') {
                $('#edit_vlan_id').val(window.selectedVlanData.vlanId);
                $('#edit_vlan_name').val(window.selectedVlanData.vlanName);
                $('#edit_vlan_name_display').text(window.selectedVlanData.vlanName || '');
                
                console.log('Restored VLAN data in EDIT modal:', window.selectedVlanData.vlanId, window.selectedVlanData.vlanName);
                
                // Reset flags after restoration
                setTimeout(function() {
                    isFromVlanSearch = false;
                    currentCallingDetailModal = '';
                    window.selectedVlanData = null;
                }, 500);
            }
            // Don't clear VLAN name display for edit modal - it should show existing data
        });

        $('#editSwitchPortDetailModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            console.log('Edit port modal hidden, action type:', modalActionType);
            
            // Only restore if action was 'cancel' (not save or VLAN search)
            if (modalActionType === 'cancel' || (modalActionType === null && !isFromVlanSearch)) {
                // Clean up VLAN search flags if we're truly canceling
                if (!isFromVlanSearch) {
                    currentCallingDetailModal = '';
                    window.selectedVlanData = null;
                }
                
                // Show switch detail modal without reloading data
                setTimeout(() => {
                    $('#switchDetailModal').modal('show');
                }, 150);
            } else if (modalActionType === 'save') {
                // For save action, we'll handle the reload in the success callback
                currentCallingDetailModal = '';
                window.selectedVlanData = null;
            }
            
            // Reset action type after handling
            modalActionType = null;
        });

        // ============================
        // EXPORT FUNCTIONS
        // ============================
        
        // Function to export selected data
        function exportSelectedData(format) {
            if (selectedSwitchIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one Switch record to export.',
                    showConfirmButton: true
                });
                return;
            }

            // Show confirmation dialog
            Swal.fire({
                title: 'Confirm Export',
                html: `Are you sure you want to export <strong>${selectedSwitchIds.length}</strong> selected Switch record(s) to ${format} format?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Yes, export to ${format}!`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form to submit selected IDs
                    const form = $('<form>', {
                        method: 'POST',
                        action: base_url + `/exportSelected${format}`
                    });

                    // Add selected IDs as hidden inputs
                    selectedSwitchIds.forEach(id => {
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'selected_ids[]',
                            value: id
                        }));
                    });

                    // Submit form
                    $('body').append(form);
                    form.submit();
                    form.remove();

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Export Started',
                        text: `Your ${format} export for ${selectedSwitchIds.length} records has been initiated.`,
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        }
    });

    </script>

<?= $this->endSection() ?>