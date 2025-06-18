<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">PC Client Management</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <!-- Button trigger modal for adding new PC Client -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPCClientModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add New PC Client
        </button>
    </p>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelPCClient">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>ID</th>
                    <th>PC Name</th>
                    <th>Asset No</th>
                    <th>Monitor Asset No</th>
                    <th>IP Before</th>
                    <th>IP After</th>
                    <th>IT Equipment</th>
                    <th>User</th>
                    <th>Area</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
             
    <!-- Add PC Client Modal -->
    <div class="modal fade" id="addPCClientModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addPCClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPCClientModalLabel">Add New PC Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPCClientForm">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="pc_name" class="form-label">PC Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pc_name" name="pc_name" placeholder="Enter PC Name">
                                <div class="error-message text-danger mt-1" id="pc_name_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pc_assetno" class="form-label">Asset No <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="pc_assetno" name="pc_assetno" placeholder="Type or search Asset No">
                                    <button class="btn btn-link search-asset-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="pc_assetno_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="monitor_assetno" class="form-label">Monitor Asset No</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="monitor_assetno" name="monitor_assetno" placeholder="Type or search Monitor Asset No">
                                    <button class="btn btn-link search-monitor-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="monitor_assetno_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ip_before" class="form-label">IP Before</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="ip_before" name="ip_before" placeholder="Type or search IP Before">
                                    <button class="btn btn-link search-ip-before-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="ip_before_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="ip_after" class="form-label">IP After 
                                    <span class="text-warning" id="ip_after_required" style="display:none;">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="ip_after" name="ip_after" placeholder="Type or search IP After">
                                    <button class="btn btn-link search-ip-after-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="ip_after_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="it_equipment" class="form-label">IT Equipment</label>
                                <textarea class="form-control" id="it_equipment" name="it_equipment" rows="3" placeholder="Enter IT Equipment details"></textarea>
                                <div class="error-message text-danger mt-1" id="it_equipment_error"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user" class="form-label">User <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="user" name="user" placeholder="Type or search User">
                                    <button class="btn btn-link search-user-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="user_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="area" class="form-label">Area <span class="text-danger">*</span></label>
                                <select class="form-select" id="area" name="area">
                                    <option value="">--Select Area--</option>
                                    <!-- Options will be populated dynamically -->
                                </select>
                                <div class="error-message text-danger mt-1" id="area_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submit-btn">Submit PC Client</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit PC Client Modal -->
    <div class="modal fade" id="editPCClientModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editPCClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPCClientModalLabel">Edit PC Client Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPCClientForm">
                        <input type="hidden" id="edit_tpc_id" name="tpc_id">

                        <!-- PC Name -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_pc_name" class="form-label">PC Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_pc_name" name="pc_name" placeholder="Enter PC Name" required>
                                <div class="error-message text-danger mt-1" id="edit_pc_name_error"></div>
                            </div>
                        </div>

                        <!-- Asset No & Monitor Asset No -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_pc_assetno" class="form-label">Asset No <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_pc_assetno" name="pc_assetno" placeholder="Type or search Asset No" required>
                                    <button class="btn btn-link search-asset-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_pc_assetno_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_monitor_assetno" class="form-label">Monitor Asset No</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_monitor_assetno" name="monitor_assetno" placeholder="Type or search Monitor Asset No">
                                    <button class="btn btn-link search-monitor-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_monitor_assetno_error"></div>
                            </div>
                        </div>

                        <!-- IP Before & IP After -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_ip_before" class="form-label">IP Before</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_ip_before" name="ip_before" placeholder="Type or search IP Before">
                                    <button class="btn btn-link search-ip-before-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_ip_before_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_ip_after" class="form-label">IP After 
                                    <span class="text-warning" id="edit_ip_after_required" style="display:none;">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_ip_after" name="ip_after" placeholder="Type or search IP After">
                                    <button class="btn btn-link search-ip-after-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_ip_after_error"></div>
                            </div>
                        </div>

                        <!-- IT Equipment -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_it_equipment" class="form-label">IT Equipment</label>
                                <textarea class="form-control" id="edit_it_equipment" name="it_equipment" rows="3" placeholder="Enter IT Equipment details"></textarea>
                                <div class="error-message text-danger mt-1" id="edit_it_equipment_error"></div>
                            </div>
                        </div>

                        <!-- User & Area - Both Required -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_user" class="form-label">User <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_user" name="user" placeholder="Type or search User" required>
                                    <button class="btn btn-link search-user-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_user_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_area" class="form-label">Area <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_area" name="area" required>
                                    <option value="">--Select Area--</option>
                                    <!-- Options will be populated dynamically -->
                                </select>
                                <div class="error-message text-danger mt-1" id="edit_area_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="update-btn">Update PC Client</button>
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
                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="employeeLength" class="form-label mb-0">Show</label>
                        </div>
                        <div class="col-auto">
                            <select id="employeeLength" class="form-select form-select-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <span>entries</span>
                        </div>
                        <div class="col-md-4 ms-auto">
                            <div class="input-group">
                                <span class="input-group-text border-0">Search:</span>
                                <input type="text" class="form-control" id="searchEmployee">
                            </div>
                        </div>
                    </div>
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

    <!-- Asset No Modal -->
    <div class="modal fade" id="assetNoModal" tabindex="-1" aria-labelledby="assetNoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assetNoModalLabel">Select Asset No</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="assetNoLength" class="form-label mb-0">Show</label>
                        </div>
                        <div class="col-auto">
                            <select id="assetNoLength" class="form-select form-select-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <span>entries</span>
                        </div>
                        <div class="col-md-4 ms-auto">
                            <div class="input-group">
                                <span class="input-group-text border-0">Search:</span>
                                <input type="text" class="form-control" id="searchAssetNo">
                            </div>
                        </div>
                    </div>
                    <table id="assetNoTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Asset No</th>
                                <th style="width: 20%;">Equipment ID</th>
                                <th style="width: 30%;">Serial Number</th>
                                <th style="width: 40%;">Model</th>
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
                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="ipAddressLength" class="form-label mb-0">Show</label>
                        </div>
                        <div class="col-auto">
                            <select id="ipAddressLength" class="form-select form-select-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <span>entries</span>
                        </div>
                        <div class="col-md-4 ms-auto">
                            <div class="input-group">
                                <span class="input-group-text border-0">Search:</span>
                                <input type="text" class="form-control" id="searchIPAddress">
                            </div>
                        </div>
                    </div>
                    <table id="ipAddressTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 15%;">VLAN ID</th>
                                <th style="width: 25%;">VLAN Name</th>
                                <th style="width: 25%;">IP Address</th>
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
                                                              
                    /* Hide spinner for Chrome, Safari, Edge, Opera */
                    input[type=number]::-webkit-inner-spin-button, 
                    input[type=number]::-webkit-outer-spin-button {
                        -webkit-appearance: none;
                        margin: 0;
                    }
                                                              
                    /* Hide spinner for Firefox */
                    input[type=number] {
                        -moz-appearance: textfield;
                    }
                </style>
            `);
                          
        let base_url = '<?= base_url('MstPCClient') ?>';        let isEditMode = false; // Flag untuk melacak form mana yang aktif
        let currentAssetType = ''; // Flag untuk melacak tipe asset yang sedang dipilih
        let currentIPType = ''; // Flag untuk melacak tipe IP yang sedang dipilih
                          
        // Initialize DataTable
        var table = $("#tabelPCClient").DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            order: [],
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
                    $('#tabelPCClient tbody').html(`<tr><td colspan="11">${spinner}</td></tr>`);
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                    $('#tabelPCClient tbody').html('<tr><td colspan="11" class="text-center">Error loading data. Please try again.</td></tr>');
                }
            },
            columns: [
                {
                    data: null,
                    className: 'text-center',
                    render: function(data, type, row) {
                        let buttons = `
                            <div class="d-flex justify-content-center gap-2">
                                <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.tpc_id}" title="Edit PC Client">
                                    <i class="fa fa-pen-to-square"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.tpc_id}" title="Delete PC Client">
                                    <i class="fa fa-trash-can"></i>
                                </a>
                            </div>
                        `;
                        
                        return buttons;
                    }
                },
                { data: 'tpc_id' },
                { data: 'tpc_name', render: function(data) { return data ? data : '-'; } },
                { data: 'tpc_assetno', render: function(data) { return data ? data : '-'; } },
                { data: 'tpc_monitorassetno', render: function(data) { return data ? data : '-'; } },
                { data: 'tpc_ipbefore', render: function(data) { return data ? data : '-'; } },
                { data: 'tpc_ipafter', render: function(data) { return data ? data : '-'; } },
                { data: 'tpc_itequipment', render: function(data) { return data ? data : '-'; } },
                { data: 'tpc_user', render: function(data) { return data ? data : '-'; } },
                { data: 'tpc_area', render: function(data) { return data ? data : '-'; } },
            ]
        });

        // Load sections for area dropdown
        function loadSections(targetSelector, selectedSection = null) {
            $(targetSelector).html('<option value="">Loading Sections...</option>');
                                      
            $.ajax({
                url: base_url + '/getAreas',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $(targetSelector).empty();
                    $(targetSelector).append('<option value="">--Select Area--</option>');
                                                      
                    if (response && response.length > 0) {
                        response.forEach(section => {
                            const selected = (selectedSection && section.sec_teamnaming === selectedSection) ? 'selected' : '';
                            $(targetSelector).append(
                                `<option value="${section.sec_teamnaming}" ${selected}>${section.sec_teamnaming}</option>`
                            );
                        });
                                                              
                        if (selectedSection) {
                            $(targetSelector).val(selectedSection);
                        }
                    } else {
                        $(targetSelector).append('<option value="">No Sections available</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading sections:', error);
                    $(targetSelector).html('<option value="">Error loading sections</option>');
                }
            });
        }

        // Load sections when modals are shown
        $('#addPCClientModal').on('show.bs.modal', function () {
            loadSections('#area');
        });

        // Asset search button click handlers - PC Asset
        $('.search-asset-btn').on('click', function() {
            currentAssetType = 'pc';
            isEditMode = $('#editPCClientModal').is(':visible');
            $('#assetNoModal').modal('show');
                                      
            $('#searchAssetNo').val('');
            initAssetNoDataTable();
        });

        // Asset search button click handlers - Monitor Asset
        $('.search-monitor-btn').on('click', function() {
            currentAssetType = 'monitor';
            isEditMode = $('#editPCClientModal').is(':visible');
            $('#assetNoModal').modal('show');
                                      
            $('#searchAssetNo').val('');
            initAssetNoDataTable();
        });

        // User search button click handlers
        $('.search-user-btn').on('click', function() {
            isEditMode = $('#editPCClientModal').is(':visible');
            $('#employeeModal').modal('show');
                                      
            $('#searchEmployee').val('');
            initEmployeeDataTable();
        });

        // Initialize DataTable for asset search
        let assetNoDataTable = null;
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
                searching: false,
                ordering: true,
                order: [[0, 'asc']],
                ajax: {
                    url: base_url + '/searchAssetNo',
                    type: 'GET',
                    data: function(d) {
                        d.search = $('#searchAssetNo').val() || '';
                        return d;
                    },
                    dataSrc: function(json) {
                        return json;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('Error loading asset data:', error);
                        $('#assetNoTable tbody').html('<tr><td colspan="5" class="text-center">Error loading data. Please try again.</td></tr>');
                    }
                },
                columns: [
                    { data: 'asset_no', width: '15%' },
                    { data: 'ea_id', width: '15%', render: function(data) { return data ? data : '-'; } },
                    { data: 'ea_machineno', width: '30%', render: function(data) { return data ? data : '-'; } },
                    { data: 'ea_model', width: '40%', render: function(data) { return data ? data : '-'; } },
                ],
                columnDefs: [
                    {
                        targets: '_all',
                        defaultContent: '-'
                    }
                ],
                dom: 't<"bottom"ip>'
            });
                                      
            $('#assetNoLength').on('change', function() {
                const newLength = $(this).val();
                assetNoDataTable.page.len(newLength).draw();
            });
                                      
            let searchTimeout;
            $('#searchAssetNo').off('keyup').on('keyup', function() {
                clearTimeout(searchTimeout);
                const searchValue = this.value;
                                                  
                searchTimeout = setTimeout(function() {
                    assetNoDataTable.ajax.reload();
                }, 300);
            });
        }

        // Handle asset selection from DataTable
        $('#assetNoTable tbody').on('click', 'tr', function() {
            if (assetNoDataTable) {
                const data = assetNoDataTable.row(this).data();
                if (!data) return;
                                                  
                if (currentAssetType === 'pc') {
                    if (isEditMode) {
                        $('#edit_pc_assetno').val(data.asset_no);
                    } else {
                        $('#pc_assetno').val(data.asset_no);
                    }
                } else if (currentAssetType === 'monitor') {
                    if (isEditMode) {
                        $('#edit_monitor_assetno').val(data.asset_no);
                    } else {
                        $('#monitor_assetno').val(data.asset_no);
                    }
                }
                                                  
                $('#assetNoModal').modal('hide');
            }
        });

        // Handle manual asset no input
        $('#asset_no').on('change', function() {
            const assetNo = $(this).val();
                         
            $('#category').val(''); // This `category` element doesn't appear to exist in your current form, might be legacy.
                                    // You might want to remove this line if it's not used.
                         
            if (!assetNo) {
                return;
            }
                         
            // Fetch asset details by asset no
            $.ajax({
                url: base_url + '/getAssetNo', // **Corrected to use the new base_url (which already includes MstPCClient)**
                type: 'GET',
                data: { assetNo: assetNo },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        // You might want to update some form fields here with the fetched asset data,
                        // similar to how the search modal populates fields.
                    } else {
                        $('#category').val(''); // Still refers to potential non-existent element
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching asset details:', error);
                    $('#category').val(''); // Still refers to potential non-existent element
                }
            });
        });
                 
        $('#edit_asset_no').on('change', function() {
            const assetNo = $(this).val();
                         
            $('#edit_category').val(''); // This `edit_category` element doesn't appear to exist in your current form, might be legacy.
                                        // You might want to remove this line if it's not used.
                         
            if (!assetNo) {
                return;
            }
                         
            // Fetch asset details by asset no
            $.ajax({
                url: base_url + '/getAssetNo', // **Corrected to use the new base_url**
                type: 'GET',
                data: { assetNo: assetNo },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        // You might want to update some form fields here with the fetched asset data,
                        // similar to how the search modal populates fields.
                    } else {
                        $('#edit_category').val(''); // Still refers to potential non-existent element
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching asset details:', error);
                    $('#edit_category').val(''); // Still refers to potential non-existent element
                }
            });
        });

        // Initialize DataTable for employee search
        let employeeDataTable = null;
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
                searching: false,
                ordering: true,
                order: [[1, 'asc']],
                ajax: {
                    url: base_url + '/searchEmployees',
                    type: 'GET',
                    data: function(d) {
                        d.search = $('#searchEmployee').val() || '';
                        return d;
                    },
                    dataSrc: function(json) {
                        return json;
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
                ],
                dom: 't<"bottom"ip>'
            });
                                      
            $('#employeeLength').on('change', function() {
                const newLength = $(this).val();
                employeeDataTable.page.len(newLength).draw();
            });
                                      
            let searchTimeout;
            $('#searchEmployee').off('keyup').on('keyup', function() {
                clearTimeout(searchTimeout);
                const searchValue = this.value;
                                                  
                searchTimeout = setTimeout(function() {
                    employeeDataTable.ajax.reload();
                }, 300);
            });
        }

        // Handle employee selection from DataTable
        $('#employeeTable tbody').on('click', 'tr', function() {
            if (employeeDataTable) {
                const data = employeeDataTable.row(this).data();
                if (!data) return;
                                                  
                const userValue = `${data.em_emplcode} - ${data.em_emplname}`;
                                                  
                if (isEditMode) {
                    $('#edit_user').val(userValue);
                } else {
                    $('#user').val(userValue);
                }
                                                  
                $('#employeeModal').modal('hide');
            }
        });

        // Edit button click handler
        $('#tabelPCClient').on('click', '.edit-btn', function() {
            const id = $(this).data('id');

            //reset form
            document.getElementById('editPCClientForm').reset();
            $('#editPCClientModalLabel').text('Edit PC Client Data');
            $('#update-btn').show();
            $('.modal-footer .btn-secondary').text('Cancel');
            $('#edit_tpc_id').val(id);
            $('#editPCClientModal').modal('show');
            
            // Get PC Client data
            $.ajax({
                url: base_url + '/getPCClientById',
                type: 'GET',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        const pcClient = response.data;
                        
                        // Fill form fields
                        $('#edit_pc_name').val(pcClient.tpc_name);
                        $('#edit_pc_assetno').val(pcClient.tpc_assetno);
                        $('#edit_monitor_assetno').val(pcClient.tpc_monitorassetno);
                        $('#edit_ip_before').val(pcClient.tpc_ipbefore);
                        $('#edit_ip_after').val(pcClient.tpc_ipafter);
                        $('#edit_it_equipment').val(pcClient.tpc_itequipment);
                        $('#edit_user').val(pcClient.tpc_user);
        
                        loadSections('#edit_area', pcClient.tpc_area);
                        
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to load PC Client details',
                        });
                        $('#editPCClientModal').modal('hide');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching PC Client details:', error);
                    $('#editPCClientModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load PC Client details. Please try again.'
                    });
                }
            });
        });

        // IP Address search button click handlers - IP Before
        $('.search-ip-before-btn').on('click', function() {
            currentIPType = 'before';
            isEditMode = $('#editPCClientModal').is(':visible');
            $('#ipAddressModal').modal('show');
                                      
            $('#searchIPAddress').val('');
            initIPAddressDataTable();
        });

        // IP Address search button click handlers - IP After
        $('.search-ip-after-btn').on('click', function() {
            currentIPType = 'after';
            isEditMode = $('#editPCClientModal').is(':visible');
            
            // Get the current IP Before value to exclude
            const ipBefore = isEditMode ? $('#edit_ip_before').val() : $('#ip_before').val();
            const excludeIPs = ipBefore ? [ipBefore] : [];
            
            $('#ipAddressModal').modal('show');
            $('#searchIPAddress').val('');
            initIPAddressDataTable(excludeIPs);
        });

        // Initialize DataTable for IP Address search
        let ipAddressDataTable = null;
        function initIPAddressDataTable(excludeIPs = []) {
            if (ipAddressDataTable) {
                ipAddressDataTable.destroy();
            }
            
            ipAddressDataTable = $('#ipAddressTable').DataTable({
                processing: true,
                serverSide: false,
                paging: true,
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                searching: false,
                ordering: true,
                order: [[0, 'asc']],
                ajax: {
                    url: base_url + '/searchIPAddresses',
                    type: 'GET',
                    data: function(d) {
                        d.search = $('#searchIPAddress').val() || '';
                        d.excludeIPs = excludeIPs;
                        return d;
                    },
                    dataSrc: function(json) {
                        return json;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('Error loading IP address data:', error);
                        $('#ipAddressTable tbody').html('<tr><td colspan="4" class="text-center">Error loading data. Please try again.</td></tr>');
                    }
                },
                columns: [
                    { data: 'mip_vlanid', width: '15%', render: function(data) { return data ? data : '-'; } },
                    { data: 'mip_vlanname', width: '25%', render: function(data) { return data ? data : '-'; } },
                    { data: 'mip_ipadd', width: '25%' },
                ],
                columnDefs: [
                    {
                        targets: '_all',
                        defaultContent: '-'
                    }
                ],
                dom: 't<"bottom"ip>'
            });
            
            $('#ipAddressLength').on('change', function() {
                const newLength = $(this).val();
                ipAddressDataTable.page.len(newLength).draw();
            });
            
            let searchTimeout;
            $('#searchIPAddress').off('keyup').on('keyup', function() {
                clearTimeout(searchTimeout);
                const searchValue = this.value;
                
                searchTimeout = setTimeout(function() {
                    ipAddressDataTable.ajax.reload();
                }, 300);
            });
        }

        // Handle IP address selection from DataTable
        $('#ipAddressTable tbody').on('click', 'tr', function() {
            if (ipAddressDataTable) {
                const data = ipAddressDataTable.row(this).data();
                if (!data) return;
                                                  
                if (currentIPType === 'before') {
                    if (isEditMode) {
                        $('#edit_ip_before').val(data.mip_ipadd);
                    } else {
                        $('#ip_before').val(data.mip_ipadd);
                    }
                } else if (currentIPType === 'after') {
                    if (isEditMode) {
                        $('#edit_ip_after').val(data.mip_ipadd);
                    } else {
                        $('#ip_after').val(data.mip_ipadd);
                    }
                }
                                                  
                $('#ipAddressModal').modal('hide');
            }
        });

        // Handle IP Before blur - mark as unused (0)
        $('#ip_before, #edit_ip_before').on('blur', function() {
            const ipInput = $(this).val().trim();
            const fieldId = $(this).attr('id');
            const prefix = fieldId.includes('edit_') ? 'edit_' : '';
            const errorElementId = `${fieldId}_error`;
            
            if (ipInput) {
                // Check if IP address exists
                $.ajax({
                    url: base_url + '/getIPAddresses',
                    type: 'GET',
                    data: { ipAddress: ipInput },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data) {
                            // IP exists, mark it as unused (0)
                            $.ajax({
                                url: base_url + '/updateIPStatus',
                                type: 'POST',
                                data: { 
                                    ipAddress: ipInput,
                                    status: 0 // Mark as unused
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
                            // IP not found
                            $(`#${errorElementId}`).text('IP Address not found');
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

        // Handle IP After blur - mark as used (1)
        $('#ip_after, #edit_ip_after').on('blur', function() {
            const ipInput = $(this).val().trim();
            const fieldId = $(this).attr('id');
            const prefix = fieldId.includes('edit_') ? 'edit_' : '';
            const errorElementId = `${fieldId}_error`;
            
            if (ipInput) {
                // Check if IP address is available (status 0)
                $.ajax({
                    url: base_url + '/getIPAddresses',
                    type: 'GET',
                    data: { ipAddress: ipInput },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data) {
                            // IP is available, mark it as used (1)
                            $.ajax({
                                url: base_url + '/updateIPStatus',
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

        $('#ip_before, #edit_ip_before').on('input', function() {
            const hasValue = $(this).val().trim() !== '';
            const requiredSpan = $(this).closest('.modal').find('#ip_after_required');
            
            if (hasValue) {
                requiredSpan.show();
            } else {
                requiredSpan.hide();
            }
        });

        // Add PC Client form submission
        $('#submit-btn').on('click', function() {
            if (!validateForm('addPCClientForm')) {
                return;
            }
                                      
            const formData = new FormData(document.getElementById('addPCClientForm'));
                                      
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
                                      
            $.ajax({
                url: base_url + '/store',
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
                                                                          
                        document.getElementById('addPCClientForm').reset();
                        $('#addPCClientModal').modal('hide');
                                                                          
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to create PC Client'
                        });
                    }
                                                              
                    $('#submit-btn').prop('disabled', false).text('Submit PC Client');
                },
                error: function(xhr, status, error) {
                    console.error('Error creating PC Client:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while creating the PC Client. Please try again.'
                    });
                                                              
                    $('#submit-btn').prop('disabled', false).text('Submit PC Client');
                }
            });
        });

        // Update PC Client form submission
        $('#update-btn').on('click', function() {
            if (!validateForm('editPCClientForm')) {
                return;
            }
                                      
            const formData = new FormData(document.getElementById('editPCClientForm'));
                                      
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                                      
            $.ajax({
                url: base_url + '/update',
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
                                                                          
                        $('#editPCClientModal').modal('hide');
                                                                          
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update PC Client'
                        });
                    }
                                                              
                    $('#update-btn').prop('disabled', false).text('Update PC Client');
                },
                error: function(xhr, status, error) {
                    console.error('Error updating PC Client:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the PC Client. Please try again.'
                    });
                                                              
                    $('#update-btn').prop('disabled', false).text('Update PC Client');
                }
            });
        });

        // Delete button click handler
        $('#tabelPCClient').on('click', '.delete-btn', function() {
            const id = $(this).data('id');
                                      
            Swal.fire({
                title: 'Are you sure?',
                text: "This PC Client will be marked as deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: base_url + '/delete',
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
                                                                                                  
                                table.ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete PC Client',
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

        // Form validation function
        function validateForm(formId) {
            let isValid = true;
            const form = document.getElementById(formId);
                                      
            form.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                                      
            const prefix = formId === 'editPCClientForm' ? 'edit_' : '';
                                      
            // Validate PC Name
            const pcNameField = form.querySelector(`#${prefix}pc_name`);
            if (pcNameField && !pcNameField.value.trim()) {
                document.getElementById(`${prefix}pc_name_error`).textContent = 'PC Name is required';
                pcNameField.classList.add('is-invalid');
                isValid = false;
            }

            //Validate Asset No
            const assetNoField = form.querySelector(`#${prefix}pc_assetno`);
            if (assetNoField && !assetNoField.value.trim()) {
                document.getElementById(`${prefix}pc_assetno_error`).textContent = 'Asset No is required';
                assetNoField.classList.add('is-invalid');
                isValid = false;
            }

            //Validate User
            const userField = form.querySelector(`#${prefix}user`);
            if (userField && !userField.value.trim()) {
                document.getElementById(`${prefix}user_error`).textContent = 'User is required';
                userField.classList.add('is-invalid');
                isValid = false;
            }

            //Validate Area
            const areaField = form.querySelector(`#${prefix}area`);
            if (areaField && !areaField.value.trim()) {
                document.getElementById(`${prefix}area_error`).textContent = 'Area is required';
                areaField.classList.add('is-invalid');
                isValid = false;
            }

            // IP After required if IP Before is filled
            const ipBeforeField = form.querySelector(`#${prefix}ip_before`);
            const ipAfterField = form.querySelector(`#${prefix}ip_after`);
            
            if (ipBeforeField && ipBeforeField.value.trim() && 
                ipAfterField && !ipAfterField.value.trim()) {
                document.getElementById(`${prefix}ip_after_error`).textContent = 'IP After is required when IP Before is provided';
                ipAfterField.classList.add('is-invalid');
                isValid = false;
            }

            // Validate IP format if provided
            const ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            
            if (ipBeforeField && ipBeforeField.value.trim() && !ipRegex.test(ipBeforeField.value.trim())) {
                document.getElementById(`${prefix}ip_before_error`).textContent = 'Please enter a valid IP address';
                ipBeforeField.classList.add('is-invalid');
                isValid = false;
            }
            
            if (ipAfterField && ipAfterField.value.trim() && !ipRegex.test(ipAfterField.value.trim())) {
                document.getElementById(`${prefix}ip_after_error`).textContent = 'Please enter a valid IP address';
                ipAfterField.classList.add('is-invalid');
                isValid = false;
            }
                                      
            return isValid;
        }

        // Reset forms when modals are hidden
        $('#addPCClientModal').on('hidden.bs.modal', function() {
            document.getElementById('addPCClientForm').reset();
            $('#addPCClientForm .error-message').text('');
            $('#addPCClientForm .is-invalid').removeClass('is-invalid');
        });
                          
        $('#editPCClientModal').on('hidden.bs.modal', function() {
            $('#editPCClientForm .error-message').text('');
            $('#editPCClientForm .is-invalid').removeClass('is-invalid');
            
            // Re-enable all form fields (hapus logic view mode)
            $('#editPCClientForm input, #editPCClientForm select, #editPCClientForm textarea').prop('disabled', false);
            $('#editPCClientForm input, #editPCClientForm select, #editPCClientForm textarea').prop('readonly', false);
            
            // Show all search buttons
            $('.search-asset-btn, .search-monitor-btn, .search-user-btn, .search-ip-before-btn, .search-ip-after-btn').show();
            
            // Reset update button visibility
            $('#update-btn').show();
            
            // Reset modal footer button text
            $('.modal-footer .btn-secondary').text('Cancel');
            
            // Reset loading flag
            isLoadingSections = false;
        });

        // Reset asset search when modal is hidden
        $('#assetNoModal').on('hidden.bs.modal', function() {
            $('#searchAssetNo').val('');
            if (assetNoDataTable) {
                assetNoDataTable.destroy();
                assetNoDataTable = null;
            }
        });

        // Reset employee search when modal is hidden
        $('#employeeModal').on('hidden.bs.modal', function() {
            $('#searchEmployee').val('');
            if (employeeDataTable) {
                employeeDataTable.destroy();
                employeeDataTable = null;
            }
        });

        // Reset IP address search when modal is hidden
        $('#ipAddressModal').on('hidden.bs.modal', function() {
            $('#searchIPAddress').val('');
            if (ipAddressDataTable) {
                ipAddressDataTable.destroy();
                ipAddressDataTable = null;
            }
        });

        // Initialize search modals when they appear
        $('#assetNoModal').on('show.bs.modal', function() {
            $('#searchAssetNo').val('');
            if (assetNoDataTable) {
                assetNoDataTable.ajax.reload();
            }
        });

        $('#employeeModal').on('show.bs.modal', function() {
            $('#searchEmployee').val('');
            if (employeeDataTable) {
                employeeDataTable.ajax.reload();
            }
        });

        $('#ipAddressModal').on('show.bs.modal', function() {
            $('#searchIPAddress').val('');
            if (ipAddressDataTable) {
                ipAddressDataTable.ajax.reload();
            }
        });

        // Handle manual employee input for user field
        $('#user, #edit_user').on('blur', function() {
            const userInput = $(this).val().trim();
            if (userInput && !isNaN(userInput)) {
                // If it's just a number, try to get employee details
                $.ajax({
                    url: base_url + '/getEmployees',
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
    });
    </script>
</div>

<?= $this->endSection() ?>