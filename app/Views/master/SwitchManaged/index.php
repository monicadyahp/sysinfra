---
<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<style>
    /* Readonly style */
    input[readonly], textarea[readonly], select[readonly] {
        background-color: #e9ecef; /* Light gray to indicate readonly */
        cursor: not-allowed;
    }
    /* force uppercase input */
    #add_asset_no, #edit_asset_no,
    #add_asset_name, #edit_asset_name,
    #add_vlan_name, #edit_vlan_name
    {
        text-transform: uppercase;
    }

    .card-datatable.table-responsive {
        overflow-x: auto;
        width: 100%;
    }

    /* Adjust column widths for better display in tables */
    #tabelSwitchManaged th:nth-child(1), #tabelSwitchManaged td:nth-child(1) { width: 5%; } /* No. */
    #tabelSwitchManaged th:nth-child(2), #tabelSwitchManaged td:nth-child(2) { width: 15%; } /* Action */
    #tabelSwitchManaged th:nth-child(3), #tabelSwitchManaged td:nth-child(3) { width: 5%; }  /* ID */
    #tabelSwitchManaged th:nth-child(4), #tabelSwitchManaged td:nth-child(4) { width: 10%; } /* ID Switch */
    #tabelSwitchManaged th:nth-child(5), #tabelSwitchManaged td:nth-child(5) { width: 10%; } /* Asset No */
    #tabelSwitchManaged th:nth-child(6), #tabelSwitchManaged td:nth-child(6) { width: 15%; } /* Asset Name */
    /* ... adjust other columns as needed ... */

    /* Modal dialog styling for wider modals */
    #addSwitchManagedModal .modal-dialog,
    #editSwitchManagedModal .modal-dialog {
        max-width: 90%; /* Use more width */
        width: auto !important;
    }
    #addSwitchManagedModal .modal-content,
    #editSwitchManagedModal .modal-content {
        height: auto;
        display: flex;
        flex-direction: column;
    }
    #addSwitchManagedModal .modal-body,
    #editSwitchManagedModal .modal-body {
        flex-grow: 1;
        overflow-y: auto;
    }

    /* Custom DataTables controls layout */
    .dataTables_wrapper .top {
        padding: 0 1.25rem; /* Menambahkan padding horizontal pada div 'top' */
    }

    .dataTables_wrapper .dataTables_length {
        display: flex;
        align-items: center;
        gap: 10px; /* Space between "Show entries" and Status dropdown */
    }
    .dataTables_wrapper .dataTables_length label {
        margin-bottom: 0; /* Remove default margin from label */
    }
    .dataTables_wrapper .dataTables_filter {
        margin-left: auto; /* Push search to the right */
    }

    /* Adjust padding for the search input to align with length dropdown */
    .dataTables_wrapper .dataTables_filter label {
        margin-right: 0.5rem; /* Add some space to the right of "Search:" label */
    }
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5rem; /* Add some space to the left of the search input */
    }

    /* Styles for detail modal */
    #switchDetailListModal .modal-dialog {
        max-width: 95%; /* Even wider for detail */
        width: auto !important;
    }

    #switchDetailListModal .modal-body .table-responsive {
        overflow-x: auto;
        margin-left: 0;
        margin-right: 0;
        max-height: calc(100vh - 350px); /* Adjust based on header/footer size */
    }

    /* Styles for detail overview table */
    #switchMainDetailOverviewTable {
        width: 100%;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        border-collapse: collapse;
        color: #4a5568;
    }
    #switchMainDetailOverviewTable thead th,
    #switchMainDetailOverviewTable tbody td {
        padding: 0.8rem 1rem;
        border: 1px solid #e2e8f0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        vertical-align: top;
    }
    #switchMainDetailOverviewTable thead th {
        background-color: rgb(221, 231, 241);
        text-align: left;
        font-weight: 600;
        color: #6e6b7b;
        font-size: 0.8125rem;
        text-transform: uppercase;
    }
    #switchMainDetailOverviewTable tbody tr {
        background-color: #fff;
    }
    #switchMainDetailOverviewTable tbody tr:hover {
        background-color: #f5f5f5;
    }

    /* Detail table styling */
    #switchDetailTable th, #switchDetailTable td {
        white-space: nowrap;
    }

    /* Overlicensed row style (if applicable, or rename to 'status-alert-row') */
    .overlicensed-row { /* Reusing for any status alert, like 'inactive' detail port */
        background-color: #ffe0b2 !important; /* Light orange */
        color: #d35400; /* Darker orange text */
        font-weight: bold;
    }
    .overlicensed-row td {
        color: #d35400;
    }
</style>


<div class="card">
    <div class="card-header">
        <h4 class="card-title">Switch Managed Configuration</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addSwitchManagedModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add Switch Managed
        </button>
        </p>

    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelSwitchManaged">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%">No.</th>
                    <th style="width: 15%">Action</th> 
                    <th>ID</th>
                    <th>ID Switch</th>
                    <th>Asset No</th>
                    <th>Asset Name</th>
                    <th>Received Date</th>
                    <th>Age (Years)</th>
                    <th>IP</th>
                    <th>Location</th>
                    <th>Last Update</th>
                    <th>Last User</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="modal fade" id="addSwitchManagedModal" tabindex="-1" aria-labelledby="addSwitchManagedModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSwitchManagedModalLabel">Add Switch Managed Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addSwitchManagedForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <input type="hidden" id="add_asset_no_sourced_from_finder" name="asset_no_sourced_from_finder" value="0">

                            <div class="col-md-6">
                                <label for="add_id_switch" class="form-label">ID Switch </label>
                                <input type="number" class="form-control" id="add_id_switch" name="id_switch" min="1">
                                <div class="invalid-feedback" id="add_id_switch_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_asset_no" class="form-label">Asset No</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="add_asset_no" name="asset_no">
                                    <button type="button" class="btn btn-outline-secondary search-equipment-btn" data-bs-toggle="modal" data-bs-target="#equipmentSearchModal">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="add_asset_no_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_asset_name" class="form-label">Asset Name</label>
                                <input type="text" class="form-control" id="add_asset_name" name="asset_name">
                                <div class="invalid-feedback" id="add_asset_name_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_received_date" class="form-label">Received Date</label>
                                <input type="date" class="form-control" id="add_received_date" name="received_date">
                                <div class="invalid-feedback" id="add_received_date_error"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="add_age" class="form-label">Age (Years)</label>
                                <input type="text" class="form-control bg-light" id="add_age" name="age" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="add_ip" class="form-label">IP</label>
                                <input type="text" class="form-control" id="add_ip" name="ip"
                                       pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                                       title="Please enter a valid IPv4 address (e.g., 192.168.1.1)">
                                <div class="invalid-feedback" id="add_ip_error"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="add_location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="add_location" name="location">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveSwitchManaged">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSwitchManagedModal" tabindex="-1" aria-labelledby="editSwitchManagedModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSwitchManagedModalLabel">Edit Switch Managed Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSwitchManagedForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <input type="hidden" id="edit_id" name="id">
                            <input type="hidden" id="edit_asset_no_sourced_from_finder" name="asset_no_sourced_from_finder" value="0">

                            <div class="col-md-6">
                                <label for="edit_id_switch" class="form-label">ID Switch </label>
                                <input type="number" class="form-control" id="edit_id_switch" name="id_switch" min="1">
                                <div class="invalid-feedback" id="edit_id_switch_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_asset_no" class="form-label">Asset No</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_asset_no" name="asset_no">
                                    <button type="button" class="btn btn-outline-secondary search-equipment-btn" data-bs-toggle="modal" data-bs-target="#equipmentSearchModal">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="edit_asset_no_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_asset_name" class="form-label">Asset Name</label>
                                <input type="text" class="form-control" id="edit_asset_name" name="asset_name">
                                <div class="invalid-feedback" id="edit_asset_name_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_received_date" class="form-label">Received Date</label>
                                <input type="date" class="form-control" id="edit_received_date" name="received_date">
                                <div class="invalid-feedback" id="edit_received_date_error"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_age" class="form-label">Age (Years)</label>
                                <input type="text" class="form-control bg-light" id="edit_age" name="age" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_ip" class="form-label">IP</label>
                                <input type="text" class="form-control" id="edit_ip" name="ip"
                                       pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                                       title="Please enter a valid IPv4 address (e.g., 192.168.1.1)">
                                <div class="invalid-feedback" id="edit_ip_error"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="edit_location" name="location">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="updateSwitchManaged">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="equipmentSearchModal" tabindex="-1" aria-labelledby="equipmentSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentSearchModalLabel">Select Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="equipmentTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Asset No</th>
                                <th>Equipment Name</th>
                                <th>Asset ID</th>
                                <th>Serial Number</th>
                                <th>Brand / Model</th>
                                <th>Receive Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="switchDetailListModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="switchDetailListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="switchDetailListModalLabel">Switch Details for ID Switch: <span id="modalIdSwitchDisplay"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" id="switchMainDetailOverviewTable" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Action</th>
                                    <th>ID</th>
                                    <th>ID Switch</th>
                                    <th>Asset No</th>
                                    <th>Asset Name</th>
                                    <th>Received Date</th>
                                    <th>Age (Years)</th>
                                    <th>IP</th>
                                    <th>Location</th>
                                    <th>Last Update</th>
                                    <th>Last User</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Port Configurations</h5>
                    <div class="mb-4 action-buttons" style="padding-left: 15px; padding-right: 15px;">
                        <div class="row mb-3">
                            <div class="col-md-9 d-flex gap-3 flex-column">
                                <button type="button" class="btn btn-primary add-port-detail-btn" style="width: 220px;" data-bs-toggle="modal" data-bs-target="#addSwitchPortDetailModal">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add Port Detail
                                </button>
                                <!-- <span id="portDetailInfoInModal" style="display: none;"></span> -->
                            </div>
                            <div id="customDetailStatusFilterWrapper" style="display: none;">
                            <label for="filterDetailStatus" class="form-label mb-0 me-2">Status:</label>
                            <select id="filterDetailStatus" class="form-select form-select-sm" style="width: auto;">
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table class="datatables-basic table table-bordered" id="switchDetailTable">
                        <thead class="table-light">
                            <tr>
                                <th>Action</th>
                                <th>ID Detail</th>
                                <th>Header ID</th> <th>Port</th>
                                <th>Type</th>
                                <th>VLAN ID</th>
                                <th>VLAN Name</th>
                                <th>Status</th>
                                <th>Last Update</th>
                                <th>Last User</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addSwitchPortDetailModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="addSwitchPortDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSwitchPortDetailModalLabel">Add Switch Port Detail</h5>
                    <button type="button" class="btn-close" id="closeAddPortDetailModalHeader" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addSwitchPortDetailForm">
                        <input type="hidden" id="add_detail_header_id_switch" name="header_id_switch">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="add_port" class="form-label">Port </label>
                                <select class="form-select" id="add_port" name="port">
                                    <option value="">Select Port</option> <?php for ($i = 1; $i <= 28; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="invalid-feedback" id="add_port_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_type" class="form-label">Type </label>
                                <select class="form-select" id="add_type" name="type">
                                    <option value="">Select Type</option> <option value="ethernet">Ethernet</option>
                                    <option value="SFP">SFP</option>
                                </select>
                                <div class="invalid-feedback" id="add_type_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_vlan_id" class="form-label">VLAN ID</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="add_vlan_id_display" placeholder="Select VLAN ID" readonly>
                                    <input type="hidden" id="add_vlan_id" name="vlan_id"> <button type="button" class="btn btn-outline-secondary search-vlan-btn" data-bs-toggle="modal" data-bs-target="#vlanSearchModal">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="add_vlan_id_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_vlan_name" class="form-label">VLAN Name</label>
                                <input type="text" class="form-control" id="add_vlan_name" name="vlan_name" readonly>
                            </div>
                            <div class="col-md-12">
                                <label for="add_detail_status" class="form-label">Status</label>
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
                    <button type="button" class="btn btn-secondary" id="cancelAddPortDetailModalFooter">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submit-port-detail-btn">Save Port</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSwitchPortDetailModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editSwitchPortDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSwitchPortDetailModalLabel">Edit Switch Port Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSwitchPortDetailForm">
                        <input type="hidden" id="edit_smd_id" name="smd_id">
                        <input type="hidden" id="edit_detail_header_id_switch" name="header_id_switch">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_port" class="form-label">Port </label>
                                <select class="form-select" id="edit_port" name="port">
                                    <option value="" selected disabled>Select Port</option>
                                    <?php for ($i = 1; $i <= 28; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="invalid-feedback" id="edit_port_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_type" class="form-label">Type </label>
                                <select class="form-select" id="edit_type" name="type">
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="ethernet">Ethernet</option>
                                    <option value="SFP">SFP</option>
                                </select>
                                <div class="invalid-feedback" id="edit_type_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_vlan_id" class="form-label">VLAN ID</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_vlan_id_display" placeholder="Select VLAN ID" readonly>
                                    <input type="hidden" id="edit_vlan_id" name="vlan_id"> <button type="button" class="btn btn-outline-secondary search-vlan-btn" data-bs-toggle="modal" data-bs-target="#vlanSearchModal">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="edit_vlan_id_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_vlan_name" class="form-label">VLAN Name</label>
                                <input type="text" class="form-control" id="edit_vlan_name" name="vlan_name" readonly>
                            </div>
                            <div class="col-md-12">
                                <label for="edit_detail_status" class="form-label">Status </label>
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
                        <tbody>
                            </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
$(document).ready(function() {
    const base_url = '<?= base_url('MstSwitchManaged') ?>';

    let equipmentTable;
    let currentCallingModal = ''; // For main add/edit modal (Switch Managed)
    let tabelSwitchManaged;

    let selectedSwitchId = null; // Stores sm_id_switch of the currently viewed main switch
    let selectedSwitchAssetNo = null; // Stores sm_asset_no for display in detail modal title
    let switchDetailTable; // DataTable for the detail ports
    let switchMainDetailOverviewTable; // DataTable for the single main switch record in detail modal

    let currentCallingDetailModal = ''; // For add/edit detail modal (Switch Port Detail)
    let vlanTable; // DataTable for VLAN search modal

    // Regex for basic IPv4 validation
    const ipv4Pattern = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;

    // Auto-uppercase inputs
    $('#add_asset_no, #edit_asset_no,' +
        '#add_asset_name, #edit_asset_name,' +
        '#add_vlan_name, #edit_vlan_name')
        .on('blur keyup', function(){
            this.value = this.value.toUpperCase();
        });

    // --- Main Switch Managed DataTable Initialization ---
    function initializeSwitchManagedDataTable() {
        if ($.fn.DataTable.isDataTable('#tabelSwitchManaged')) {
            tabelSwitchManaged.destroy();
        }

        tabelSwitchManaged = $('#tabelSwitchManaged').DataTable({
            scrollX: true,
            pageLength: 10,
            order: [[10, 'desc']], // Order by Last Update descending
            ajax: {
                url: base_url + "/getDataSwitchManaged",
                dataSrc: "",
                error: function(xhr, status, error) {
                    console.error("Error fetching Switch Managed data:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load Switch Managed data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error),
                    });
                }
            },
            columns: [
                {
                    data: null, // No.
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        // Using row.id which maps to sm_id from the database
                        const switchManagedId = row.id;
                        return `
                            <div class="d-flex justify-content-center gap-2">
                                <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.id}" title="Edit Switch Managed">
                                    <i class="fa fa-pen-to-square"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-info view-detail-btn" data-id_switch="${row.id_switch}" data-asset_no="${row.asset_no}" title="View Port Details">
                                    <i class="fa fa-network-wired"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-success print-excel-by-id-btn" data-id="${switchManagedId}" title="Print Excel">
                                    <i class="fa fa-file-excel"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.id}" title="Delete Switch Managed">
                                    <i class="fa fa-trash-can"></i>
                                </a>
                            </div>`;
                    }
                },
                { data: 'id' },
                { data: 'id_switch' },
                { data: 'asset_no', render: d => d ? d.toUpperCase() : '' },
                { data: 'asset_name', render: d => d ? d.toUpperCase() : '' },
                {
                    data: 'received_date',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }) : '';
                    }
                },
                { data: 'age' },
                { data: 'ip' },
                { data: 'location' },
                {
                    data: 'last_update',
                    render: function(data) {
                        return data ? new Date(data).toLocaleString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '';
                    }
                },
                { data: 'last_user' }
            ],
            language: {
                "sLengthMenu": "Show _MENU_ entries",
                "sSearch": "Search:"
            },
            dom: '<"top d-flex justify-content-between align-items-center"<"dataTables_length"l<"export-excel-button-wrapper ms-2">><"dataTables_filter"f>>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
            initComplete: function() {
                const $exportButton = `
                    <button type="button" class="btn btn-success btn-sm" id="exportExcelBtn">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </button>
                `;
                $('.export-excel-button-wrapper').append($exportButton);

                $('#exportExcelBtn').on('click', function() {
                    window.location.href = base_url + '/exportExcel';
                });
            }
        });
    }

    // Initialize main DataTable on page load
    initializeSwitchManagedDataTable();


    // --- IP Validation Logic ---
    $('#add_ip').on('input', function() {
        if ($(this).val() && !ipv4Pattern.test($(this).val())) {
            $(this).addClass('is-invalid');
            $('#add_ip_error').text('Format IP tidak valid. Contoh: 192.168.1.1').show();
        } else {
            $(this).removeClass('is-invalid');
            $('#add_ip_error').text('').hide();
        }
        checkFormInputs('#addSwitchManagedForm', '#saveSwitchManaged');
    });

    $('#edit_ip').on('input', function() {
        if ($(this).val() && !ipv4Pattern.test($(this).val())) {
            $(this).addClass('is-invalid');
            $('#edit_ip_error').text('Format IP tidak valid. Contoh: 192.168.1.1').show();
        } else {
            $(this).removeClass('is-invalid');
            $('#edit_ip_error').text('').hide();
        }
        checkFormInputs('#editSwitchManagedForm', '#updateSwitchManaged');
    });

    // --- Age Calculation (client-side for display) ---
    $('#add_received_date').on('change', function() {
        const receivedDateStr = $(this).val();
        if (receivedDateStr) {
            const today = new Date();
            const rDate = new Date(receivedDateStr);
            let years = today.getFullYear() - rDate.getFullYear();
            let months = today.getMonth() - rDate.getMonth();
            let days = today.getDate() - rDate.getDate();
            if (days < 0) { months--; const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0); days += prevMonth.getDate(); }
            if (months < 0) { years--; months += 12; }
            const totalMonths = (years * 12) + months + (days / (new Date(rDate.getFullYear(), rDate.getMonth() + 1, 0).getDate()));
            const ageInYears = totalMonths / 12;
            $('#add_age').val(ageInYears.toFixed(1));
        } else {
            $('#add_age').val('');
        }
    });

    $('#edit_received_date').on('change', function() {
        const receivedDateStr = $(this).val();
        if (receivedDateStr) {
            const today = new Date();
            const rDate = new Date(receivedDateStr);
            let years = today.getFullYear() - rDate.getFullYear();
            let months = today.getMonth() - rDate.getMonth();
            let days = today.getDate() - rDate.getDate();
            if (days < 0) { months--; const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0); days += prevMonth.getDate(); }
            if (months < 0) { years--; months += 12; }
            const totalMonths = (years * 12) + months + (days / (new Date(rDate.getFullYear(), rDate.getMonth() + 1, 0).getDate()));
            const ageInYears = totalMonths / 12;
            $('#edit_age').val(ageInYears.toFixed(1));
        } else {
            $('#edit_age').val('');
        }
    });

    function checkFormInputs(formId, submitBtnId) {
        let filled = false;
        let isValidIp = true;

        $(`${formId} input:not([type="hidden"]):not([readonly]), ${formId} textarea:not([readonly]), ${formId} select:not([readonly])`).each(function() {
            if ($(this).val() && $(this).val().trim() !== '') {
                filled = true;
            }
            if ($(this).attr('id') === 'add_ip' || $(this).attr('id') === 'edit_ip') {
                if ($(this).val() && !ipv4Pattern.test($(this).val())) {
                    isValidIp = false;
                }
            }
        });

        // Baris ini dihapus karena id_switch tidak lagi required untuk mengaktifkan tombol save
        // if ($(formId).find('#add_id_switch').val() || $(formId).find('#edit_id_switch').val()) {
        //     filled = true;
        // }

        $(`${submitBtnId}`).prop('disabled', !(filled && isValidIp));
    }

    // --- Modal show/hide and form reset handlers for main modals ---
    $('#addSwitchManagedModal').on('shown.bs.modal', function() {
        checkFormInputs('#addSwitchManagedForm', '#saveSwitchManaged');
    });

    $('#addSwitchManagedForm input, #addSwitchManagedForm select, #addSwitchManagedForm textarea').on('keyup change', function() {
        checkFormInputs('#addSwitchManagedForm', '#saveSwitchManaged');
    });

    $('#editSwitchManagedModal').on('shown.bs.modal', function() {
        checkFormInputs('#editSwitchManagedForm', '#updateSwitchManaged');
    });

    $('#editSwitchManagedForm input, #editSwitchManagedForm select, #editSwitchManagedForm textarea').on('keyup change', function() {
        checkFormInputs('#editSwitchManagedForm', '#updateSwitchManaged');
    });

    $('#addSwitchManagedModal').on('hidden.bs.modal', function() {
        if ($('#equipmentSearchModal').hasClass('show')) return;
        $('#addSwitchManagedForm')[0].reset();
        $('#addSwitchManagedForm').find('.is-invalid').removeClass('is-invalid');
        $('#addSwitchManagedForm').find('.invalid-feedback').text('').hide();
        $('#add_asset_no, #add_asset_name, #add_received_date').prop('readonly', false).removeClass('bg-light');
        $('#add_asset_no_sourced_from_finder').val('0');
        $('#add_age').val('');
        $('#saveSwitchManaged').prop('disabled', true).text('Save');
    });

    $('#editSwitchManagedModal').on('hidden.bs.modal', function() {
        if ($('#equipmentSearchModal').hasClass('show')) return;
        $('#editSwitchManagedForm')[0].reset();
        $('#editSwitchManagedForm').find('.is-invalid').removeClass('is-invalid');
        $('#editSwitchManagedForm').find('.invalid-feedback').text('').hide();
        $('#edit_asset_no, #edit_asset_name, #edit_received_date').prop('readonly', false).removeClass('bg-light');
        $('#edit_asset_no_sourced_from_finder').val('0');
        $('#edit_age').val('');
        $('#updateSwitchManaged').prop('disabled', true).text('Update');
    });

    // --- CRUD Operations for Main Switch Managed ---
    $('#addSwitchManagedForm').on('submit', function(e) {
        e.preventDefault();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').text('').hide();

        const addIpInput = $('#add_ip');
        if (addIpInput.val() && !ipv4Pattern.test(addIpInput.val())) {
            addIpInput.addClass('is-invalid');
            $('#add_ip_error').text('Format IP tidak valid. Contoh: 192.168.1.1').show();
            $('#saveSwitchManaged').prop('disabled', false);
            return;
        }

        $('#saveSwitchManaged').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: base_url + '/add',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false
                    }).then(() => {
                        $('#addSwitchManagedModal').modal('hide');
                        tabelSwitchManaged.ajax.reload();
                    });
                } else {
                    if (response.errors) {
                        for (const field in response.errors) {
                            if (response.errors.hasOwnProperty(field)) {
                                const fieldId = `add_${field.replace('sm_', '')}`;
                                $(`#${fieldId}`).addClass('is-invalid');
                                $(`#${fieldId}_error`).text(response.errors[field]).show();
                            }
                        }
                    } else if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                    } else {
                        Swal.fire('Error', 'Gagal menambahkan Switch Managed. Mohon coba lagi.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Request failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : error);
                Swal.fire('Error', errorMessage, 'error');
            },
            complete: function() {
                $('#saveSwitchManaged').prop('disabled', false).text('Save');
                checkFormInputs('#addSwitchManagedForm', '#saveSwitchManaged');
            }
        });
    });

    $('#tabelSwitchManaged').on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.ajax({
            url: base_url + '/edit',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.status) {
                    const d = response.data;
                    $('#edit_id').val(d.id);
                    $('#edit_id_switch').val(d.id_switch);
                    $('#edit_asset_no').val(d.asset_no);
                    $('#edit_asset_name').val(d.asset_name);
                    $('#edit_received_date').val(d.received_date);
                    $('#edit_age').val(d.age);
                    $('#edit_ip').val(d.ip);
                    $('#edit_location').val(d.location);

                    if (d.asset_no_sourced_from_finder == 1) {
                        $('#edit_asset_no, #edit_asset_name, #edit_received_date').prop('readonly', true).addClass('bg-light');
                    } else {
                        $('#edit_asset_no, #edit_asset_name, #edit_received_date').prop('readonly', false).removeClass('bg-light');
                    }
                    $('#edit_asset_no_sourced_from_finder').val(d.asset_no_sourced_from_finder);

                    $('#editSwitchManagedForm').find('.is-invalid').removeClass('is-invalid');
                    $('#editSwitchManagedForm').find('.invalid-feedback').text('').hide();
                    checkFormInputs('#editSwitchManagedForm', '#updateSwitchManaged');
                    $('#editSwitchManagedModal').modal('show');
                } else {
                    Swal.fire('Error', response.message || 'Data not found', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });

    $('#editSwitchManagedForm').on('submit', function(e) {
        e.preventDefault();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').text('').hide();

        const editIpInput = $('#edit_ip');
        if (editIpInput.val() && !ipv4Pattern.test(editIpInput.val())) {
            editIpInput.addClass('is-invalid');
            $('#edit_ip_error').text('Format IP tidak valid. Contoh: 192.168.1.1').show();
            $('#updateSwitchManaged').prop('disabled', false);
            return;
        }

        $('#updateSwitchManaged').prop('disabled', true).text('Memperbarui...');

        $.ajax({
            url: base_url + '/update',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success', title: 'Updated!', text: response.message, timer: 1500, showConfirmButton: false
                    }).then(() => {
                        $('#editSwitchManagedModal').modal('hide');
                        tabelSwitchManaged.ajax.reload();
                    });
                } else {
                    if (response.errors) {
                        for (const field in response.errors) {
                            if (response.errors.hasOwnProperty(field)) {
                                const fieldId = `edit_${field.replace('sm_', '')}`;
                                $(`#${fieldId}`).addClass('is-invalid');
                                $(`#${fieldId}_error`).text(response.errors[field]).show();
                            }
                        }
                    } else if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                    } else {
                        Swal.fire('Error', 'Gagal memperbarui Switch Managed. Mohon coba lagi.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Request failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : error);
                Swal.fire('Error', errorMessage, 'error');
            },
            complete: function() {
                $('#updateSwitchManaged').prop('disabled', false).text('Update');
                checkFormInputs('#editSwitchManagedForm', '#updateSwitchManaged');
            }
        });
    });

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
                            Swal.fire('Error', response.error || 'Delete failed', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
                    }
                });
            }
        });
    });

    // --- Equipment Finder Logic ---
    $('.search-equipment-btn').on('click', function() {
        currentCallingModal = $(this).closest('.modal').attr('id');
        if (currentCallingModal === 'addSwitchManagedModal') {
            $('#addSwitchManagedModal').modal('hide');
        } else if (currentCallingModal === 'editSwitchManagedModal') {
            $('#editSwitchManagedModal').modal('hide');
        }

        if ($.fn.DataTable.isDataTable('#equipmentTable')) {
            equipmentTable.destroy();
        }
        equipmentTable = $('#equipmentTable').DataTable({
            processing: true, serverSide: false,
            ajax: {
                url: base_url + "/getEquipmentData", dataSrc: function(json) { return json || []; },
                error: function(xhr, status, error) { console.error('Error fetching Equipment data:', error); Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load Equipment data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error) }); return []; }
            },
            columns: [
                { data: 'e_assetno', render: d => d ? d.toUpperCase() : '' },
                { data: 'e_equipmentname', render: d => d ? d.toUpperCase() : '' },
                { data: 'e_equipmentid' },
                { data: 'e_serialnumber', render: d => d ? d.toUpperCase() : '' },
                { data: null, render: function(data, type, row) { return (row.e_brand || '') + ' / ' + (row.e_model || ''); } },
                { data: 'e_receivedate', render: function(data) { return data ? new Date(data).toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }) : ''; } }
            ],
            language: { "sLengthMenu": "Show _MENU_ entries", "sSearch": "Search:" },
            paging: true, lengthChange: true, searching: true
        });

        setTimeout(() => { $('#equipmentSearchModal').modal('show'); }, 200);
    });

    $('#equipmentTable tbody').on('click', 'tr', function() {
        if (!equipmentTable) return;
        const data = equipmentTable.row(this).data();
        if (!data) return;

        if (currentCallingModal === 'addSwitchManagedModal') {
            $('#add_asset_no').val(data.e_assetno).prop('readonly', true).addClass('bg-light');
            $('#add_asset_name').val(data.e_equipmentname).prop('readonly', true).addClass('bg-light');
            $('#add_received_date').val(data.e_receivedate ? data.e_receivedate.split(' ')[0] : '').prop('readonly', true).addClass('bg-light');
            const receivedDateStr = $('#add_received_date').val();
            if (receivedDateStr) {
                const today = new Date(); const rDate = new Date(receivedDateStr);
                let years = today.getFullYear() - rDate.getFullYear(); let months = today.getMonth() - rDate.getMonth(); let days = today.getDate() - rDate.getDate();
                if (days < 0) { months--; const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0); days += prevMonth.getDate(); }
                if (months < 0) { years--; months += 12; }
                const totalMonths = (years * 12) + months + (days / (new Date(rDate.getFullYear(), rDate.getMonth() + 1, 0).getDate()));
                const ageInYears = totalMonths / 12;
                $('#add_age').val(ageInYears.toFixed(1));
            } else { $('#add_age').val(''); }
            $('#add_asset_no_sourced_from_finder').val('1');
            $('#add_asset_no').removeClass('is-invalid');
            $('#add_asset_no_error').text('').hide();

        } else if (currentCallingModal === 'editSwitchManagedModal') {
            $('#edit_asset_no').val(data.e_assetno).prop('readonly', true).addClass('bg-light');
            $('#edit_asset_name').val(data.e_equipmentname).prop('readonly', true).addClass('bg-light');
            $('#edit_received_date').val(data.e_receivedate ? data.e_receivedate.split(' ')[0] : '').prop('readonly', true).addClass('bg-light');
            const receivedDateStr = $('#edit_received_date').val();
            if (receivedDateStr) {
                const today = new Date(); const rDate = new Date(receivedDateStr);
                let years = today.getFullYear() - rDate.getFullYear(); let months = today.getMonth() - rDate.getMonth(); let days = today.getDate() - rDate.getDate();
                if (days < 0) { months--; const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0); days += prevMonth.getDate(); }
                if (months < 0) { years--; months += 12; }
                const totalMonths = (years * 12) + months + (days / (new Date(rDate.getFullYear(), rDate.getMonth() + 1, 0).getDate()));
                const ageInYears = totalMonths / 12;
                $('#edit_age').val(ageInYears.toFixed(1));
            } else { $('#edit_age').val(''); }
            $('#edit_asset_no_sourced_from_finder').val('1');
            $('#edit_asset_no').removeClass('is-invalid');
            $('#edit_asset_no_error').text('').hide();
        }

        $('#equipmentSearchModal').modal('hide');
    });

    $('#equipmentSearchModal').on('hidden.bs.modal', function() {
        if (currentCallingModal === 'addSwitchManagedModal') {
            $('#addSwitchManagedModal').modal('show');
        } else if (currentCallingModal === 'editSwitchManagedModal') {
            $('#editSwitchManagedModal').modal('show');
        }
        currentCallingModal = '';
    });

    // --- NEW: View Port Details Button Click Handler ---
    $('#tabelSwitchManaged').on('click', '.view-detail-btn', function() {
        const rowData = tabelSwitchManaged.row($(this).parents('tr')).data();
        // === PERBAIKI BARIS INI ===
        // selectedSwitchId harus mengambil ID utama dari tabel tbmst_switch_managed, yaitu 'id'
        selectedSwitchId = rowData.id; // Menggunakan sm_id (primary key) untuk details
        selectedSwitchAssetNo = rowData.asset_no; // Tetap ambil asset_no untuk tampilan

        // Perbarui teks modal display agar lebih informatif
        // $('#modalIdSwitchDisplay').text(selectedSwitchAssetNo + ' (ID Master: ' + (selectedSwitchId || 'N/A') + ')');
        // Perbarui teks modal display
        $('#modalIdSwitchDisplay').text(selectedSwitchId || 'N/A');
        // Perbarui judul modal keseluruhan
        $('#switchDetailListModalLabel').text('Switch Details for ID Header: ' + (selectedSwitchId || 'N/A'));
        $('#add_detail_header_id_switch').val(selectedSwitchId); // Set hidden input untuk Add Detail form

        // Populate and draw the single-row overview table with main switch data
        switchMainDetailOverviewTable.clear().rows.add([rowData]).draw();

        // Panggil loadSwitchDetailPorts hanya jika selectedSwitchId valid
        if (selectedSwitchId) {
            loadSwitchDetailPorts(selectedSwitchId); // Load the detail ports
            $('.add-port-detail-btn').prop('disabled', false); // Aktifkan tombol Add Port Detail
            $('#switchDetailListModal').modal('show'); // Tampilkan modal detail
        } else {
            Swal.fire('Error', 'Switch ID (ID Utama) tidak ditemukan untuk entri ini. Detail Port tidak dapat ditampilkan.', 'error');
            $('.add-port-detail-btn').prop('disabled', true); // Pastikan tombol nonaktif
        }
    });

    $('#tabelSwitchManaged').on('click', '.print-excel-by-id-btn', function() {
        const id = $(this).data('id');
        if (id) {
            Swal.fire({
                title: 'Generating Excel Report...',
                text: 'Please wait, your report is being generated.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            window.location.href = base_url + '/exportExcelById/' + id;
            // Close Swal after a short delay (give time for file download to initiate)
            setTimeout(() => {
                Swal.close();
            }, 2000);
        } else {
            Swal.fire('Error', 'Switch ID (ID Utama) not found for this entry.', 'error');
        }
    });

    // --- NEW: Initialize Switch Detail Overview DataTable ---
    switchMainDetailOverviewTable = $('#switchMainDetailOverviewTable').DataTable({
        info: false, paging: false, searching: false, ordering: false,
        columns: [
            {
                data: null, className: 'text-center', orderable: false,
                render: function(data, type, row) {
                    // These buttons apply to the main switch record
                    return `
                        <div class="d-flex justify-content-center gap-2">
                            <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-main-switch-btn" data-id="${row.id}" title="Edit Switch Managed">
                                <i class="fa fa-pen-to-square"></i>
                            </a>
                            <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-main-switch-btn" data-id="${row.id}" title="Delete Switch Managed">
                                <i class="fa fa-trash-can"></i>
                            </a>
                        </div>`;
                }
            },
            { data: 'id' },
            { data: 'id_switch' },
            { data: 'asset_no' },
            { data: 'asset_name' },
            { data: 'received_date', render: d => d ? new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }) : '' },
            { data: 'age' },
            { data: 'ip' },
            { data: 'location' },
            { data: 'last_update', render: d => d ? new Date(d).toLocaleString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '' },
            { data: 'last_user' }
        ],
        data: [], // Starts empty, filled on demand
        language: { "sEmptyTable": "No switch data available.", "sZeroRecords": "No matching records found" }
    });

    // NEW: Handle click on Edit/Delete buttons in switchMainDetailOverviewTable (re-triggering main table logic)
    $('#switchMainDetailOverviewTable').on('click', '.edit-main-switch-btn', function() {
        const id = $(this).data('id');
        // Close detail modal before opening main edit modal
        $('#switchDetailListModal').modal('hide');
        // Trigger the original edit button click in the main table
        $(`#tabelSwitchManaged .edit-btn[data-id="${id}"]`).trigger('click');
    });

    $('#switchMainDetailOverviewTable').on('click', '.delete-main-switch-btn', function() {
        const id = $(this).data('id');
        // Close detail modal before triggering main delete logic
        $('#switchDetailListModal').modal('hide');
        // Trigger the original delete button click in the main table
        $(`#tabelSwitchManaged .delete-btn[data-id="${id}"]`).trigger('click');
    });


    // --- NEW: Initialize Switch Detail Ports DataTable ---
    switchDetailTable = $('#switchDetailTable').DataTable({
        scrollX: true,
        pageLength: 5,
        autoWidth: false,
        processing: true,
        serverSide: false, // All data fetched at once
        ajax: {
            url: base_url + "/getSwitchDetailPorts/0", // Dummy URL, actual ID will be passed
            dataSrc: "",
            error: function(xhr, status, error) {
                console.error("Error fetching Switch Port Detail data:", error);
                Swal.fire({
                    icon: 'error', title: 'Error', text: 'Failed to load Port Details: ' + (xhr.responseJSON ? xhr.responseJSON.message : error)
                });
                return [];
            }
        },
        columns: [
            {
                data: null, className: 'text-center', orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="d-flex justify-content-center gap-2">
                            <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-detail-port-btn" data-id="${row.smd_id}" title="Edit Port Detail">
                                <i class="fa fa-pen-to-square"></i>
                            </a>
                            <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-detail-port-btn" data-id="${row.smd_id}" data-header_id_switch="${row.smd_header_id_switch}" title="Delete Port Detail">
                                <i class="fa fa-trash-can"></i>
                            </a>
                        </div>`;
                }
            },
            { data: 'smd_id', title: 'ID Detail' },
            { data: 'smd_header_id_switch', title: 'Header ID' },
            { data: 'smd_port', title: 'Port' },
            { data: 'smd_type', title: 'Type' },
            { data: 'smd_vlan_id', title: 'VLAN ID' },
            { data: 'smd_vlan_name', title: 'VLAN Name', render: d => d ? d.toUpperCase() : '' },
            // Perhatikan perubahan pada bagian 'data' dan 'render'
            {
                data: 'smd_status', // Tetap gunakan data asli (1 atau 0) untuk filter
                title: 'Status',
                // Gunakan 'render' untuk tampilan HTML
                render: function(data, type, row) {
                    // Jika type adalah 'filter' atau 'sort', kembalikan data mentah (1 atau 0)
                    // Ini memastikan DataTables memfilter dan mengurutkan berdasarkan nilai asli.
                    if (type === 'filter' || type === 'sort') {
                        return data;
                    }
                    // Untuk tampilan normal, kembalikan HTML badge
                    return data == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
                }
            },
            {
                data: 'smd_lastupdate', title: 'Last Update',
                render: function(data) {
                    return data ? new Date(data).toLocaleString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '';
                }
            },
            { data: 'last_user_display', title: 'Last User' }
        ],
        order: [[2, 'asc']], // order by Header ID (index 2) ascending

        rowCallback: function(row, data, index) {
            // Apply a class if status is Inactive
            if (data.smd_status == 0) {
                $(row).addClass('overlicensed-row'); // Reusing this class for inactive status
            } else {
                $(row).removeClass('overlicensed-row');
            }
        },
        language: { "sSearch": "Search:", "sEmptyTable": "No port details configured for this switch.", "sZeroRecords": "No matching records found" },
        // **PENTING: DOM string yang disederhanakan.**
        // Kita hanya akan menyisakan 'f' (filter/search) yang akan dibuat DataTables secara otomatis di sisi kanan.
        // Untuk sisi kiri, kita akan membuat kontainer kustom sepenuhnya.
        dom: '<"top d-flex justify-content-between align-items-center"<"custom-left-controls">f>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
        initComplete: function() {
            const api = this.api();

            // 1. Temukan kontainer kustom di sisi kiri yang kita definisikan di 'dom'
            const $customLeftControls = $(this).closest('.dataTables_wrapper').find('.custom-left-controls').first();

            // 2. Buat elemen "Show entries" secara manual
            const $lengthDropdownHtml = `
                <div class="dataTables_length" id="switchDetailTable_length">
                    <label>Show 
                        <select name="switchDetailTable_length" aria-controls="switchDetailTable" class="form-select form-select-sm">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        entries
                    </label>
                </div>
            `;
            const $lengthDropdown = $($lengthDropdownHtml);

            // 3. Buat elemen dropdown Status secara manual
            const $statusFilterHtml = `
                <div class="status-filter-wrapper d-flex align-items-center ms-2">
                    <label class="form-label mb-0 me-2">Status:</label>
                    <select id="filterDetailStatus" class="form-select form-select-sm" style="width: auto;">
                        <option value="">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            `;
            const $statusFilter = $($statusFilterHtml);

            // 4. Masukkan kedua elemen ke dalam kontainer kustom di sisi kiri
            $customLeftControls.append($lengthDropdown);
            $customLeftControls.append($statusFilter);

            // 5. Sambungkan event listener untuk "Show entries" yang baru kita buat
            $lengthDropdown.find('select').on('change', function() {
                api.page.len($(this).val()).draw();
            });

            // 6. Sambungkan event listener untuk dropdown Status
            $statusFilter.find('#filterDetailStatus').on('change', function() {
                const statusValue = $(this).val();
                // Kolom 'smd_status' adalah kolom ke-8 (index 7).
                if (statusValue === "") {
                    api.column(7).search('').draw(); // Tampilkan semua jika "All" dipilih
                } else {
                    api.column(7).search(`^${statusValue}$`, true, false).draw(); // Filter exact match untuk 1 atau 0
                }
            });

            // Opsional: Atur nilai awal Show Entries sesuai pageLength yang dikonfigurasi
            $lengthDropdown.find('select').val(api.page.len());
        }
    });

    // Function to load Switch Detail Ports
    function loadSwitchDetailPorts(id_switch) {
        if (!id_switch) {
            console.error("No Switch ID provided to loadSwitchDetailPorts.");
            switchDetailTable.clear().draw();
            $('#portDetailInfoInModal').text('Switch ID is missing.').show();
            return;
        }

        // Fetch count of ports
        // $.ajax({
        //     url: base_url + '/countSwitchDetailPorts/' + id_switch,
        //     type: 'GET',
        //     success: function(response) {
        //         if (response.status) {
        //             const currentPortCount = response.count;
        //             let infoText = `Total Ports: ${currentPortCount} configured.`;
        //             $('#portDetailInfoInModal').html(infoText).show();
        //             $('.add-port-detail-btn').prop('disabled', false); // Enable Add Port button
        //         } else {
        //             $('#portDetailInfoInModal').text(`Error loading port count.`).show();
        //             $('.add-port-detail-btn').prop('disabled', false);
        //             Swal.fire('Error', response.error || 'Failed to get port count.', 'error');
        //         }
        //     },
        //     error: function(xhr, status, error) {
        //         $('#portDetailInfoInModal').text(`Error loading port count.`).show();
        //         $('.add-port-detail-btn').prop('disabled', false);
        //         Swal.fire('Error', 'Request failed to get port count: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
        //     }
        // });

        // Load the actual port detail data
        switchDetailTable.ajax.url(base_url + "/getSwitchDetailPorts/" + id_switch).load(function() {
            switchDetailTable.rows().invalidate().draw(); // Invalidate and redraw to apply rowCallback
            console.log("Switch Port Detail data loaded and redrawn successfully for ID Switch: ", id_switch);
        });
    }

    // --- NEW: Add Port Detail Modal Handlers ---
    $('#switchDetailListModal').on('click', '.add-port-detail-btn', function() {
        currentCallingDetailModal = 'addSwitchPortDetailModal';
        $('#add_detail_header_id_switch').val(selectedSwitchId); // Ensure header_id is set
        $('#addSwitchPortDetailForm')[0].reset();
        $('#addSwitchPortDetailForm').find('.is-invalid').removeClass('is-invalid');
        $('#addSwitchPortDetailForm').find('.invalid-feedback').text('').hide();

        // Remove 'required' attribute from all inputs and selects in the add modal
        $('#addSwitchPortDetailForm input, #addSwitchPortDetailForm select').removeAttr('required');

        // Reset VLAN fields for Add modal
        $('#add_vlan_id_display').val('').prop('readonly', true).removeClass('bg-light'); // Clear and make readonly as it's for display
        $('#add_vlan_id').val(''); // Clear hidden input
        $('#add_vlan_name').val('').prop('readonly', true).removeClass('bg-light'); // Clear and make readonly

        $('#add_detail_status').val('1'); // Default to Active
        $('#add_port').val(''); // Reset selected port
        
        // Menampilkan kembali switchDetailListModal di belakang addSwitchPortDetailModal
        $('#switchDetailListModal').modal('show'); 
        $('#addSwitchPortDetailModal').modal('show');
    });

    // Fix for overlapping modals (VLAN search and parent modal)
    $('#addSwitchPortDetailModal').on('hidden.bs.modal', function() {
        if ($('#vlanSearchModal').hasClass('show')) return; // If VLAN search is still open, do nothing here

        // Resume interaction with the parent modal (switchDetailListModal)
        setTimeout(() => {
            $('#switchDetailListModal').modal('show');
            loadSwitchDetailPorts(selectedSwitchId); // Re-load details in the main detail modal to update counts/list
        }, 100);
    });

    // Handle "X" and "Cancel" buttons for Add Port Detail Modal
    $('#closeAddPortDetailModalHeader, #cancelAddPortDetailModalFooter').on('click', function() {
        // This will trigger the 'hidden.bs.modal' event handler above
        $('#addSwitchPortDetailModal').modal('hide');
    });

    // Submit Add Port Detail Form
    $('#submit-port-detail-btn').on('click', function() {
        const headerId = $('#add_detail_header_id_switch').val();
        if (!headerId) {
            Swal.fire('Error', 'Switch ID is missing. Cannot add port detail.', 'error');
            return;
        }

        // === START: KODE UNTUK VALIDASI MINIMAL 1 KOLOM (Port, Type, VLAN ID, VLAN Name) ===
        let isAtLeastOneCoreFieldFilled = false;
        // Bersihkan semua indikator error dan class 'is-invalid' terlebih dahulu
        $('#addSwitchPortDetailForm input, #addSwitchPortDetailForm select').removeClass('is-invalid');
        $('#addSwitchPortDetailForm .invalid-feedback').text('').hide();

        const portValue = $('#add_port').val();
        const typeValue = $('#add_type').val();
        const vlanIdValue = $('#add_vlan_id').val(); // Ini adalah hidden input
        const vlanNameValue = $('#add_vlan_name').val(); // Ini adalah readonly input

        // Periksa apakah Port terisi (dan valid)
        if (portValue !== '' && (parseInt(portValue) >= 1 && parseInt(portValue) <= 28)) {
            isAtLeastOneCoreFieldFilled = true;
        } else if (portValue !== '') { // Jika ada nilai tapi tidak valid, tandai error
            $('#add_port').addClass('is-invalid');
            $('#add_port_error').text('Port must be between 1 and 28.').show();
            return; // Hentikan eksekusi jika Port invalid
        }

        // Periksa apakah Type terisi
        if (typeValue !== '') {
            isAtLeastOneCoreFieldFilled = true;
        }

        // Periksa apakah VLAN ID atau VLAN Name terisi
        if (vlanIdValue !== '' || vlanNameValue.trim() !== '') {
            isAtLeastOneCoreFieldFilled = true;
        }

        // Validasi khusus untuk VLAN ID jika vlan_id_display punya nilai tapi vlan_id (hidden) kosong
        if ($('#add_vlan_id_display').val().trim() !== '' && vlanIdValue === '') {
            $('#add_vlan_id_display').addClass('is-invalid');
            $('#add_vlan_id_error').text('VLAN ID must be selected from finder if VLAN Name is used.').show();
            return; // Hentikan eksekusi jika VLAN ID tidak valid
        }

        // Jika tidak ada satu pun dari field inti yang terisi, tampilkan error
        if (!isAtLeastOneCoreFieldFilled) {
            Swal.fire('Error', 'Please fill at least one of the following fields: Port, Type, VLAN ID, or VLAN Name.', 'error');
            return;
        }
        // === END: KODE UNTUK VALIDASI MINIMAL 1 KOLOM ===
        
        $.ajax({
            url: base_url + '/addSwitchDetailPort',
            type: 'POST',
            data: $('#addSwitchPortDetailForm').serialize(),
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false
                    }).then(() => {
                        $('#addSwitchPortDetailModal').modal('hide');
                        // Will trigger loadSwitchDetailPorts via hidden.bs.modal handler
                    });
                } else {
                    if (response.errors) {
                        for (const field in response.errors) {
                            if (response.errors.hasOwnProperty(field)) {
                                const fieldId = `add_${field.replace('smd_', '')}`; // e.g., smd_port -> port
                                $(`#${fieldId}`).addClass('is-invalid');
                                $(`#${fieldId}_error`).text(response.errors[field]).show();
                            }
                        }
                    } else if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                    } else {
                        Swal.fire('Error', 'Failed to add port detail.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });

    // --- NEW: Edit Port Detail Modal Handlers ---
    $('#switchDetailTable').on('click', '.edit-detail-port-btn', function() {
        currentCallingDetailModal = 'editSwitchPortDetailModal';
        const smd_id = $(this).data('id');

        // Menampilkan kembali switchDetailListModal di belakang editSwitchPortDetailModal
        $('#switchDetailListModal').modal('show'); 
        
        $.ajax({
            url: base_url + '/editSwitchDetailPort',
            type: 'POST',
            data: { smd_id: smd_id },
            success: function(response) {
                if (response.status) {
                    const d = response.data;
                    $('#edit_smd_id').val(d.smd_id);
                    $('#edit_detail_header_id_switch').val(d.smd_header_id_switch);
                    $('#edit_port').val(d.smd_port);
                    $('#edit_type').val(d.smd_type);
                    // Populate display field for VLAN ID
                    $('#edit_vlan_id_display').val(d.smd_vlan_id);
                    $('#edit_vlan_id').val(d.smd_vlan_id); // Hidden field
                    $('#edit_vlan_name').val(d.smd_vlan_name); // Populated with resolved name
                    $('#edit_detail_status').val(d.smd_status);

                    // Clear previous invalid states
                    $('#editSwitchPortDetailForm').find('.is-invalid').removeClass('is-invalid');
                    $('#editSwitchPortDetailForm').find('.invalid-feedback').text('').hide();

                    $('#editSwitchPortDetailModal').modal('show');
                } else {
                    Swal.fire('Error', response.message || 'Detail port data not found', 'error');
                    setTimeout(() => $('#switchDetailListModal').modal('show'), 100);
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
                setTimeout(() => $('#switchDetailListModal').modal('show'), 100);
            }
        });
    });

    $('#editSwitchPortDetailModal').on('hidden.bs.modal', function() {
        if ($('#vlanSearchModal').hasClass('show')) return;
        setTimeout(() => {
            $('#switchDetailListModal').modal('show');
            loadSwitchDetailPorts(selectedSwitchId); // Re-load details
        }, 100);
    });

    // Submit Edit Port Detail Form
    $('#update-port-detail-btn').on('click', function() {
        const smd_id = $('#edit_smd_id').val();
        const headerId = $('#edit_detail_header_id_switch').val();

        // Client-side validation for port, type, status, vlan_id
        let isValid = true;
        $('#editSwitchPortDetailForm input, #editSwitchPortDetailForm select').each(function() {
            const field = $(this);
            const fieldName = field.attr('name');
            const errorElement = $(`#edit_${fieldName}_error`);

            // Clear previous errors for this field
            field.removeClass('is-invalid');
            if (errorElement.length) errorElement.text('').hide();

            if (field.is(':required') && !field.val()) {
                field.addClass('is-invalid');
                if (errorElement.length) errorElement.text(`${fieldName.replace('_', ' ').toUpperCase()} is required.`).show();
                isValid = false;
            } else if (field.attr('type') === 'number' && field.val() !== '' && isNaN(field.val())) {
                field.addClass('is-invalid');
                if (errorElement.length) errorElement.text(`${fieldName.replace('_', ' ').toUpperCase()} must be a number.`).show();
                isValid = false;
            } else if (fieldName === 'port' && field.val() !== '' && (parseInt(field.val()) < 1 || parseInt(field.val()) > 28)) {
                field.addClass('is-invalid');
                if (errorElement.length) errorElement.text('Port must be between 1 and 28.').show();
                isValid = false;
            }
        });

        // Specific validation for vlan_id (which is a hidden field now) if it's required (but it's permit_empty)
        if ($('#edit_vlan_id').val() === '' && $('#edit_vlan_id_display').val() !== '') { // Use _display for user input check
            $('#edit_vlan_id_display').addClass('is-invalid');
            $('#edit_vlan_id_error').text('VLAN ID must be selected from finder if VLAN Name is filled.').show();
            isValid = false;
        }

        if (!isValid) return;

        $.ajax({
            url: base_url + '/updateSwitchDetailPort',
            type: 'POST',
            data: $('#editSwitchPortDetailForm').serialize(),
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success', title: 'Updated!', text: response.message, timer: 1500, showConfirmButton: false
                    }).then(() => {
                        $('#editSwitchPortDetailModal').modal('hide');
                        // Will trigger loadSwitchDetailPorts via hidden.bs.modal
                    });
                } else {
                    if (response.errors) {
                        for (const field in response.errors) {
                            if (response.errors.hasOwnProperty(field)) {
                                const fieldId = `edit_${field.replace('smd_', '')}`;
                                $(`#${fieldId}`).addClass('is-invalid');
                                $(`#${fieldId}_error`).text(response.errors[field]).show();
                            }
                        }
                    } else if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                    } else {
                        Swal.fire('Error', 'Failed to update port detail.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });

    // Delete Port Detail Handler
    $('#switchDetailTable').on('click', '.delete-detail-port-btn', function() {
        const smd_id = $(this).data('id');
        const header_id_switch = $(this).data('header_id_switch');
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
                    data: { smd_id: smd_id },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success', title: 'Deleted!', text: response.message, timer: 1500, showConfirmButton: false
                            });
                            loadSwitchDetailPorts(header_id_switch); // Reload the detail table
                        } else {
                            Swal.fire('Error', response.error || 'Delete failed', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
                    }
                });
            }
        });
    });

    // --- NEW: VLAN Search Modal Handlers ---
    $('.search-vlan-btn').on('click', function() {
        // Capture the ID of the modal that opened this VLAN finder
        currentCallingDetailModal = $(this).closest('.modal').attr('id');

        // Hide the parent detail modal before opening the VLAN search modal
        // This is crucial to prevent overlapping and ensure correct focus management.
        if (currentCallingDetailModal === 'addSwitchPortDetailModal') {
            $('#addSwitchPortDetailModal').modal('hide');
        } else if (currentCallingDetailModal === 'editSwitchPortDetailModal') {
            $('#editSwitchPortDetailModal').modal('hide');
        } else if (currentCallingDetailModal === 'switchDetailListModal') {
            // If the VLAN search is initiated directly from the main detail list modal (unlikely for now, but good practice)
            $('#switchDetailListModal').modal('hide');
        }

        // Destroy and reinitialize VLAN table
        if ($.fn.DataTable.isDataTable('#vlanTable')) {
            vlanTable.destroy();
        }
        vlanTable = $('#vlanTable').DataTable({
            processing: true, serverSide: false,
            ajax: {
                url: base_url + "/getVlanData", dataSrc: function(json) { return json || []; },
                error: function(xhr, status, error) { console.error('Error fetching VLAN data:', error); Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load VLAN data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error) }); return []; }
            },
            columns: [
                { data: 'tv_id_vlan' },
                { data: 'tv_name', render: d => d ? d.toUpperCase() : '' }
            ],
            language: { "sLengthMenu": "Show _MENU_ entries", "sSearch": "Search:" },
            paging: true, lengthChange: true, searching: true
        });

        // Small delay to ensure the parent modal is fully hidden before showing the new one
        setTimeout(() => { $('#vlanSearchModal').modal('show'); }, 100);
    });

    // ... kode Anda sebelumnya ...

    $('#vlanTable tbody').on('click', 'tr', function() {
        if (!vlanTable) return;
        const data = vlanTable.row(this).data();
        if (!data) return;

        // Ambil tv_id_vlan dan tv_name. Gunakan string kosong jika null/undefined
        const vlanId = data.tv_id_vlan !== null && data.tv_id_vlan !== undefined ? data.tv_id_vlan : '';
        const vlanName = data.tv_name !== null && data.tv_name !== undefined ? data.tv_name : '';

        // Determine the target modal based on which one called the VLAN search
        if (currentCallingDetailModal === 'addSwitchPortDetailModal') {
            // Set VLAN ID (hidden field)
            $('#add_vlan_id').val(vlanId);

            // Set VLAN ID display (visible field)
            $('#add_vlan_id_display').val(vlanId);

            // Set VLAN Name (visible, readonly field)
            $('#add_vlan_name').val(vlanName);

            // Clear any previous validation errors
            $('#add_vlan_id_display').removeClass('is-invalid');
            $('#add_vlan_id_error').text('').hide();
            $('#add_vlan_name').removeClass('is-invalid'); // Ensure vlan_name also clears invalid state
        } else if (currentCallingDetailModal === 'editSwitchPortDetailModal') {
            // Set VLAN ID (hidden field)
            $('#edit_vlan_id').val(vlanId);

            // Set VLAN ID display (visible field)
            $('#edit_vlan_id_display').val(vlanId);

            // Set VLAN Name (visible, readonly field)
            $('#edit_vlan_name').val(vlanName);

            // Clear any previous validation errors
            $('#edit_vlan_id_display').removeClass('is-invalid');
            $('#edit_vlan_id_error').text('').hide();
            $('#edit_vlan_name').removeClass('is-invalid'); // Ensure vlan_name also clears invalid state
        }

        // Tutup modal pencarian VLAN
        $('#vlanSearchModal').modal('hide');
    });

// ... sisa kode Anda ...

    $('#vlanSearchModal').on('hidden.bs.modal', function() {
        // Reopen the correct parent modal after VLAN search is closed
        if (currentCallingDetailModal === 'addSwitchPortDetailModal') {
            // TIDAK PERLU MERESET LAGI DI SINI, KARENA DATA SUDAH DIISI DI ATAS
            // Cukup tampilkan modal induk
            $('#addSwitchPortDetailModal').modal('show');
        } else if (currentCallingDetailModal === 'editSwitchPortDetailModal') {
            // TIDAK PERLU MERESET LAGI DI SINI
            $('#editSwitchPortDetailModal').modal('show');
        } else if (currentCallingDetailModal === 'switchDetailListModal') {
            $('#switchDetailListModal').modal('show');
        }
        currentCallingDetailModal = ''; // Reset context
    });

    // Reset VLAN fields and readonly state when add/edit detail modals are shown
    $('#addSwitchPortDetailModal').on('shown.bs.modal', function() {
        // HAPUS LOGIKA RESET INI JIKA ANDA INGIN MEMPERTAHANKAN DATA SETELAH PEMILIHAN VLAN
        // KARENA INI AKAN MENGHAPUS NILAI YANG BARU SAJA DISET DARI VLAN FINDER JIKA VLAN ID KOSONG
        /*
        if ($(this).find('#add_vlan_id').val() === '') { // Hanya mereset jika VLAN ID masih kosong
            $('#add_vlan_id_display').val('').prop('readonly', true).removeClass('bg-light');
            $('#add_vlan_name').val('').prop('readonly', true).removeClass('bg-light');
        }
        */
        // Biarkan bagian ini jika Anda ingin menghilangkan pesan error sebelumnya
        $('#add_vlan_id_error').text('').hide();
        $('#add_vlan_id_display').removeClass('is-invalid');
    });

    $('#editSwitchPortDetailModal').on('shown.bs.modal', function() {
        // HAPUS LOGIKA RESET INI JUGA
        /*
        if ($(this).find('#edit_vlan_id').val() === '') { // Hanya mereset jika VLAN ID masih kosong
            $('#edit_vlan_id_display').val('').prop('readonly', true).removeClass('bg-light');
            $('#edit_vlan_name').val('').prop('readonly', true).removeClass('bg-light');
        }
        */
        // Biarkan bagian ini jika Anda ingin menghilangkan pesan error sebelumnya
        $('#edit_vlan_id_error').text('').hide();
        $('#edit_vlan_id_display').removeClass('is-invalid');
    });

});
</script>
<?= $this->endSection() ?>