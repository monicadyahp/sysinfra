<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<div class="card">
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <!-- Button trigger modal for adding new ticket -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTicketModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add New Ticket
        </button>
    </p>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelTicketing">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>ID</th>
                    <th>Employee</th>
                    <th>Section</th>
                    <th>Asset No</th>
                    <th>Category</th>
                    <th>Team</th>
                    <th>Case</th>
                    <th>Handle</th>
                    <th>PIC System</th>
                    <th>Create Date</th>
                    <th>Finish Date</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
        
    <!-- Add Ticket Modal -->
    <div class="modal fade" id="addTicketModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTicketModalLabel">Add New Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTicketForm">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="employee_no" class="form-label">Reporter Employee ID <span class="text-danger">*</span></label>
                                                 
                                <?php if($isSystemSection): ?>
                                <!-- For System Section: Use regular input with search button -->
                                <div class="input-group">
                                    <input type="text" class="form-control" id="employee_no" name="employee_no"
                                         placeholder="Type or search reporter employee ID">
                                    <button class="btn btn-link search-employee-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="employee_no_error"></div>
                                                 
                                <?php else: ?>
                                <!-- For Non-System Section: Use form-info display -->
                                <div class="form-info d-flex align-items-center p-2 bg-light rounded border">
                                    <div>
                                        <span><?= $currentUserData['employeeCode'] ?></span>
                                    </div>
                                </div>
                                <input type="hidden" id="employee_no" name="employee_no" value="<?= $currentUserData['employeeCode'] ?>">
                                <div class="error-message text-danger mt-1" id="employee_no_error"></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-info d-flex align-items-center p-2 bg-light rounded border">
                                    <div>
                                        <div><strong>Employee Name:</strong> <span id="employee_name_display"><?= $isSystemSection ? '-' : $currentUserData['employeeName'] ?></span></div>
                                        <div><strong>Section:</strong> <span id="section_name_display"><?= $isSystemSection ? '-' : $currentUserData['sectionName'] ?></span></div>
                                    </div>
                                </div>
                                <input type="hidden" id="employee_name" name="employee_name" value="<?= $isSystemSection ? '' : $currentUserData['employeeName'] ?>">
                                <input type="hidden" id="section_name" name="section_name" value="<?= $isSystemSection ? '' : $currentUserData['sectionName'] ?>">
                                <input type="hidden" id="section_code" name="section_code" value="<?= $isSystemSection ? '' : $currentUserData['sectionCode'] ?>">
                            </div>
                        </div>
                                         
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="asset_no" class="form-label">Asset No</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="asset_no" name="asset_no" placeholder="Type or search Asset No">
                                    <button class="btn btn-link search-asset-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="asset_no_error"></div>
                            </div>
                        </div>
                                         
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">--Select Category--</option>
                                    <!-- Options will be populated dynamically based on the server response -->
                                </select>
                                <div class="error-message text-danger mt-1" id="category_error"></div>
                            </div>
                        </div>
                                         
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="case_content" class="form-label">Case <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="case_content" name="case_content" rows="4" placeholder="Describe the issue"></textarea>
                                <div class="error-message text-danger mt-1" id="case_content_error"></div>
                            </div>
                        </div>
                                         
                        <?php if($isSystemSection): ?>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="handle_content" class="form-label">Handle</label>
                                <textarea class="form-control" id="handle_content" name="handle_content" rows="4"
                                         placeholder="Describe the handle taken or to be taken"></textarea>
                                <div class="error-message text-danger mt-1" id="handle_content_error"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                                         
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Team <span class="text-danger">*</span></label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="team" id="team_sdo" value="1" checked>
                                        <label class="form-check-label" for="team_sdo">
                                            Development Team
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="team" id="team_infra" value="2">
                                        <label class="form-check-label" for="team_infra">
                                            Infrastructure Team
                                        </label>
                                    </div>
                                </div>
                                <div class="error-message text-danger mt-1" id="team_error"></div>
                            </div>
                        </div>
                                         
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="pic_system" class="form-label">PIC System <span class="text-danger">*</span></label>
                                <select class="form-select" id="pic_system" name="pic_system">
                                    <option value="">--Select PIC--</option>
                                    <!-- Options will be populated dynamically based on selected team -->
                                </select>
                                <div class="error-message text-danger mt-1" id="pic_system_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submit-btn">Submit Ticket</button>
                </div>
            </div>
        </div>
    </div>
        
    <!-- Edit Ticket Modal -->
    <div class="modal fade" id="editTicketModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTicketModalLabel">Edit Ticket Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTicketForm">
                        <input type="hidden" id="edit_ticket_id" name="ticket_id">
                        <input type="hidden" id="edit_create_date" name="create_date">
                                         
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_employee_no" class="form-label">Reporter Employee ID <span class="text-danger">*</span></label>
                                                 
                                <?php if($isSystemSection): ?>
                                <!-- For System Section: Use regular input with search button -->
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_employee_no" name="employee_no"
                                         placeholder="Type or search reporter employee ID">
                                    <button class="btn btn-link edit-search-employee-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_employee_no_error"></div>
                                                 
                                <?php else: ?>
                                <!-- For Non-System Section: Use regular input but readonly for employee field -->
                                <div class="form-info d-flex align-items-center p-2 bg-light rounded border">
                                    <div>
                                        <span id="edit_employee_no_display"></span>
                                    </div>
                                </div>
                                <input type="hidden" id="edit_employee_no" name="employee_no">
                                <div class="error-message text-danger mt-1" id="edit_employee_no_error"></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Modify the employee info display in Edit Ticket Modal -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-info d-flex align-items-center p-2 bg-light rounded border">
                                    <div>
                                        <div><strong>Employee Name:</strong> <span id="edit_employee_name_display">-</span></div>
                                        <div><strong>Section:</strong> <span id="edit_section_name_display">-</span></div>
                                    </div>
                                </div>
                                <input type="hidden" id="edit_employee_name" name="employee_name">
                                <input type="hidden" id="edit_section_name" name="section_name">
                                <input type="hidden" id="edit_section_code" name="section_code">
                            </div>
                        </div>
                                         
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_asset_no" class="form-label">Asset No</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_asset_no" name="asset_no" placeholder="Type or search Asset No">
                                    <button class="btn btn-link search-asset-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_asset_no_error"></div>
                            </div>
                        </div>
                                         
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_category" class="form-label">Category</label>
                                <select class="form-select" id="edit_category" name="category">
                                    <option value="">--Select Category--</option>
                                    <!-- Options will be populated dynamically based on the server response -->
                                </select>
                                <div class="error-message text-danger mt-1" id="edit_category_error"></div>
                            </div>
                        </div>
                                         
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_case_content" class="form-label">Case <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="edit_case_content" name="case_content" rows="4" placeholder="Describe the issue"></textarea>
                                <div class="error-message text-danger mt-1" id="edit_case_content_error"></div>
                            </div>
                        </div>
                                         
                        <?php if($isSystemSection): ?>
                        <div class="row mb-3" id="edit_handle_section">
                            <div class="col-md-12">
                                <label for="edit_handle_content" class="form-label">Handle</label>
                                <textarea class="form-control" id="edit_handle_content" name="handle_content" rows="4"
                                         placeholder="Describe the handle taken or to be taken"></textarea>
                                <div class="error-message text-danger mt-1" id="edit_handle_content_error"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                                         
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Team <span class="text-danger">*</span></label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="team" id="edit_team_sdo" value="1">
                                        <label class="form-check-label" for="edit_team_sdo">
                                            Development Team
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="team" id="edit_team_infra" value="2">
                                        <label class="form-check-label" for="edit_team_infra">
                                            Infrastructure Team
                                        </label>
                                    </div>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_team_error"></div>
                            </div>
                        </div>
                                         
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_pic_system" class="form-label">PIC System <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_pic_system" name="pic_system">
                                    <option value="">--Select PIC--</option>
                                    <!-- Options will be populated dynamically based on selected team -->
                                </select>
                                <div class="error-message text-danger mt-1" id="edit_pic_system_error"></div>
                            </div>
                        </div>
                                         
                        <?php if($isSystemSection): ?>
                        <div class="row mb-3" id="edit_finish_date_section">
                            <div class="col-md-12">
                                <label for="edit_finish_date" class="form-label">Finish Date</label>
                                <input type="date" class="form-control" id="edit_finish_date" name="finish_date">
                                <div class="error-message text-danger mt-1" id="edit_finish_date_error"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <?= $isSystemSection ? 'Cancel' : 'Close' ?>
                    </button>
                    <button type="button" class="btn btn-primary" id="update-btn">Update Ticket</button>
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
                        margin: 0;}
                                         
                    /* Hide spinner for Firefox */
                    input[type=number] {
                        -moz-appearance: textfield;
                    }
                </style>
            `);
                 
        let base_url = '<?= base_url() ?>';
        let isEditMode = false; // Flag untuk melacak form mana yang aktif
                 
        function addCustomControls() {
            // Find the DataTables length control element
            const lengthControl = $('#tabelTicketing_length');
                         
            // Create the status dropdown HTML
            const statusDropdown = `
                <div class="status-filter-wrapper" style="display: inline-block; margin-left: 20px;">
                    <label style="font-weight: normal;">
                        Status:
                        <select id="statusFilter" class="form-select form-select-sm" style="display: inline-block; width: auto;">
                            <option value="all">All</option>
                            <option value="outstanding" selected>Outstanding</option>
                            <option value="finish">Finish</option>
                        </select>
                    </label>
                </div>
            `;
                         
            // Insert the dropdown after the length control
            lengthControl.append(statusDropdown);
        }
                 
        // Initialize DataTable dengan pagination yang ditingkatkan
        var table = $("#tabelTicketing").DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            order: [],
            autoWidth: false,
            ajax: {
                url: base_url + "TransTicketing/getData",
                dataSrc: function(json) {
                    return json;
                },
                beforeSend: function() {
                    let spinner = `
                        <div class="align-items-center justify-content-center gap-2">
                            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                        </div>`;
                    $('#tabelTicketing tbody').html(`<tr><td colspan="12">${spinner}</td></tr>`);
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                    $('#tabelTicketing tbody').html('<tr><td colspan="12" class="text-center">Error loading data. Please try again.</td></tr>');
                }
            },
            columns: [
                {
                    data: null,
                    className: 'text-center',
                    render: function(data, type, row) {
                        let buttons = '';
                        
                        // Check if current user can edit this ticket
                        if (row.can_edit) {
                            buttons = `
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.tt_id}">
                                        <i class="fa fa-pen-to-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.tt_id}">
                                        <i class="fa fa-trash-can"></i>
                                    </a>
                                </div>
                            `;
                        } else {
                            buttons = `
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:;" class="btn btn-icon btn-outline-info view-btn" data-id="${row.tt_id}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            `;
                        }
                                                 
                        return buttons;
                    }
                },
                { data: 'tt_id' },
                { data: 'tt_empno_rep' },
                { data: 'section_name' },
                { data: 'tt_assetno', render: function(data) { return data ? data : '-'; } },
                { data: 'tt_categoryequip', render: function(data) { return data ? data : '-'; } },
                { data: 'tt_category' },
                { data: 'tt_case', render: function(data) { return data ? data : '-'; } },
                { data: 'tt_action', render: function(data) { return data ? data : '-'; } },
                { data: 'tt_pic_system', render: function(data) { return data ? data : '-'; } },
                { data: 'tt_check_date', render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') return data;
                    if (!data) return '-';
                    const date = new Date(data);
                    if (isNaN(date)) return data;
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${day}/${month}/${year}`;
                }},
                { data: 'tt_finish_date', render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') return data;
                    if (!data) return '-';
                    const date = new Date(data);
                    if (isNaN(date)) return data;
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${day}/${month}/${year}`;
                }}
            ],
            drawCallback: function() {
                // Only add custom controls once
                if (!$('#statusFilter').length) {
                    addCustomControls();
                }
            }
        });

        const isSystemSection = <?= json_encode($isSystemSection) ?>;
        const currentUserData = <?= json_encode($currentUserData) ?>;
                 
        // Set up the forms for non-System Section users
        if (!isSystemSection) {
            // For add form - Set current user data when form is opened
            $('#addTicketModal').on('show.bs.modal', function() {
                // Set current user data to form fields
                $('#employee_no').val(currentUserData.employeeCode);
                $('#employee_name').val(currentUserData.employeeName);
                $('#section_name').val(currentUserData.sectionName);
                $('#section_code').val(currentUserData.sectionCode);
                                 
                // Update display spans
                $('#employee_name_display').text(currentUserData.employeeName || '-');
                $('#section_name_display').text(currentUserData.sectionName || '-');
            });
                         
            // For edit form - Make sure we don't overwrite with current user data unless it's a new ticket
            $('#editTicketModal').on('show.bs.modal', function() {
                // Wait for the ticket data to be loaded
                const id = $('#edit_ticket_id').val();
                                 
                if (!id) {
                    // If it's a new ticket, set current user data
                    $('#edit_employee_no').val(currentUserData.employeeCode);
                    $('#edit_employee_name').val(currentUserData.employeeName);
                    $('#edit_section_name').val(currentUserData.sectionName);
                    $('#edit_section_code').val(currentUserData.sectionCode);
                                         
                    // Update display spans
                    $('#edit_employee_name_display').text(currentUserData.employeeName || '-');
                    $('#edit_section_name_display').text(currentUserData.sectionName || '-');
                }
                                 
                // Ensure the field is readonly
                $('#edit_employee_no').attr('readonly', true);
            });
        }
                 
        // Modify the edit behavior for System Section users too
        if (isSystemSection) {
            // For edit form - load ticket data normally, just ensure we can edit
            $('#editTicketModal').on('show.bs.modal', function() {
                // Ensure the search button is visible
                $('.edit-search-employee-btn').show();
            });
        }
                  
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            // Get current status filter value from the dropdown
            const currentStatusFilter = $('#statusFilter').val();
                         
            // If "All" is selected, show all records
            if (currentStatusFilter === 'all') {
                return true;
            }
                         
            // Column index 10 contains the finish date
            const finishDate = data[11]; //Buat menambah atau mengurangi kolom datatable tabelticketing
                         
            // Check if record matches the selected filter
            if (currentStatusFilter === 'finish') {
                return finishDate !== '-'; // Show only records with finish date
            } else if (currentStatusFilter === 'outstanding') {
                return finishDate === '-'; // Show only records without finish date
            }
                         
            // Default behavior: show all records
            return true;
        });
                 
        // Add event handler for the status dropdown after it's created
        $(document).on('change', '#statusFilter', function() {
            const status = $(this).val();
                         
            if (status === 'finish') {
                table.order([11, 'desc']); //ini mengikuti jumlah kolom datatable tabelticketing
            } else {
                table.order([0, 'asc']); // Default sorting, misalnya kolom pertama ascending
            }
                         
            table.draw();
        });

        // Fungsi untuk memuat Category dinamis
        function loadCategoryOptions(targetSelector, selectedCategory = null) {
            // Show loading state
            $(targetSelector).html('<option value="">Loading Categories...</option>');
                         
            $.ajax({
                url: base_url + 'TransTicketing/getCategories',
                type: 'GET',
                data: { search: '' },
                dataType: 'json',
                success: function(response) {
                                         
                    // Reset dropdown
                    $(targetSelector).empty();
                    $(targetSelector).append('<option value="">--Select Category--</option>');
                                         
                    if (response && response.length > 0) {
                        // Populate dropdown with Category options
                        response.forEach(category => {
                            const selected = (selectedCategory && category.equipmentcat === selectedCategory) ? 'selected' : '';
                            $(targetSelector).append(
                                `<option value="${category.equipmentcat}" ${selected}>${category.equipmentcat}</option>`
                            );
                        });
                                                 
                        // Jika ada selectedCategory, atur nilai dropdown
                        if (selectedCategory) {
                            $(targetSelector).val(selectedCategory);
                        }
                    } else {
                        $(targetSelector).append('<option value="">No Categories available</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading category options:', error);
                    $(targetSelector).html('<option value="">Error loading category options</option>');
                }
            });
        }

        $('#addTicketModal').on('show.bs.modal', function () {
            $('#category').val('');
            loadCategoryOptions('#category');
            const selectedTeam = $('input[name="team"]:checked').val();
            loadPicSystemUsers(selectedTeam, '#pic_system');
        });
                  
        $('#editTicketModal').on('show.bs.modal', function () {
            $('#edit_category').val('');
            // loadCategoryOptions akan dipanggil setelah data tiket diambil
        });
                       
        // Fungsi untuk memuat PIC dinamis berdasarkan kategori yang dipilih
        function loadPicSystemUsers(team, targetSelector, selectedId = null) {
            // Show loading state
            $(targetSelector).html('<option value="">Loading PIC users...</option>');
                         
            $.ajax({
                url: base_url + 'TransTicketing/getSystemEmployees',
                type: 'GET',
                data: {
                    search: '',
                    team: team
                },
                dataType: 'json',
                success: function(response) {
                                         
                    // Reset dropdown
                    $(targetSelector).empty();
                    $(targetSelector).append('<option value="">--Select PIC--</option>');
                                         
                    // Filter PIC based on team if needed
                    let picUsers = response;
                                         
                    // Populate dropdown with data PIC
                    if (picUsers && picUsers.length > 0) {
                        picUsers.forEach(user => {
                            const selected = (selectedId && user.em_emplcode == selectedId) ? 'selected' : '';
                            let teamInfo = user.sec_team ? ` - ${user.sec_team}` : '';
                            $(targetSelector).append(
                                `<option value="${user.em_emplcode}" ${selected}>${user.em_emplname} (${user.em_emplcode})</option>`
                            );
                        });
                    } else {
                        $(targetSelector).append('<option value="">No PIC users available for this team</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading PIC users:', error);
                    $(targetSelector).html('<option value="">Error loading PIC users</option>');
                }
            });
        }
                 
        // Kategori berubah pada form tambah
        $('input[name="team"]').on('change', function() {
            const team = $('input[name="team"]:checked').val();
            loadPicSystemUsers(team, '#pic_system');
        });
                 
        // Kategori berubah pada form edit
        $('#editTicketModal input[name="team"]').on('change', function() {
            const team = $('#editTicketModal input[name="team"]:checked').val();
            const savedPicValue = $('#edit_pic_system').data('saved-value');
            loadPicSystemUsers(team, '#edit_pic_system', savedPicValue);
        });
                 
        // Handle manual employee ID input
        $('#employee_no, #edit_employee_no').on('change', function() {
            const employeeId = $(this).val();
            const isEditMode = $(this).attr('id') === 'edit_employee_no';
            const prefix = isEditMode ? 'edit_' : '';
                         
            if (!employeeId) {
                $(`#${prefix}employee_name`).val('');
                $(`#${prefix}section_name`).val('');
                $(`#${prefix}section_code`).val('');
                $(`#${prefix}employee_name_display`).text('-');
                $(`#${prefix}section_name_display`).text('-');
                return;
            }
                         
            // Fetch employee details by ID
            $.ajax({
                url: base_url + 'TransTicketing/getEmployees',
                type: 'GET',
                data: { employeeId: employeeId },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        // Menggunakan em_emplname dari tabel tbmst_employee
                        $(`#${prefix}employee_name`).val(response.data.em_emplname);
                        $(`#${prefix}section_name`).val(response.data.sec_section);
                        $(`#${prefix}section_code`).val(response.data.em_sectioncode);
                                                 
                        // Update display spans
                        $(`#${prefix}employee_name_display`).text(response.data.em_emplname);
                        $(`#${prefix}section_name_display`).text(response.data.sec_section);
                                                 
                        // Clear validation errors if any
                        $(`#${prefix}employee_no_error`).text('');
                        $(`#${prefix}employee_no`).removeClass('is-invalid');
                    } else {
                        // Show validation error
                        $(`#${prefix}employee_name`).val('');
                        $(`#${prefix}section_name`).val('');
                        $(`#${prefix}section_code`).val('');
                        $(`#${prefix}employee_name_display`).text('-');
                        $(`#${prefix}section_name_display`).text('-');
                        $(`#${prefix}employee_no_error`).text('Employee not found');
                        $(`#${prefix}employee_no`).addClass('is-invalid');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching employee details:', error);
                    $(`#${prefix}employee_no_error`).text('Error fetching employee details');
                    $(`#${prefix}employee_no`).addClass('is-invalid');
                }
            });
        });
                 
        // Employee search buttons click
        $('.search-employee-btn, .edit-search-employee-btn').on('click', function() {
            // Set flag based on which button was clicked
            isEditMode = $(this).hasClass('edit-search-employee-btn');
            $('#employeeModal').modal('show');
                         
            // Reset search input dan data
            $('#searchEmployee').val('');
            initEmployeeDataTable();
        });
                 
        // Inisialisasi DataTable untuk pencarian karyawan
        let employeeDataTable = null;
        function initEmployeeDataTable() {
            // Destroy table if it already exists
            if (employeeDataTable) {
                employeeDataTable.destroy();
            }
                         
            // Create new DataTable
            employeeDataTable = $('#employeeTable').DataTable({
                processing: true,
                serverSide: false,
                paging: true,
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                searching: false, // Using custom search
                ordering: true,
                order: [[1, 'asc']], // Default sort by name
                ajax: {
                    url: base_url + 'TransTicketing/searchEmployees',
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
                dom: 't<"bottom"ip>' // Simpler DOM structure
            });
                         
            // Set up length change
            $('#employeeLength').on('change', function() {
                const newLength = $(this).val();
                employeeDataTable.page.len(newLength).draw();
            });
                         
            // Add search functionality with debounce
            let searchTimeout;
            $('#searchEmployee').off('keyup').on('keyup', function() {
                clearTimeout(searchTimeout);
                const searchValue = this.value;
                                 
                searchTimeout = setTimeout(function() {
                    employeeDataTable.ajax.reload();
                }, 300);
            });
        }
                 
        // Handle employee selection dari DataTable
        $('#employeeTable tbody').on('click', 'tr', function() {
            if (employeeDataTable) {
                const data = employeeDataTable.row(this).data();
                if (!data) return;
                                 
                if (isEditMode) {
                    // Edit form
                    $('#edit_employee_no').val(data.em_emplcode);
                    $('#edit_employee_name').val(data.em_emplname);
                    $('#edit_section_name').val(data.sec_section);
                    $('#edit_section_code').val(data.em_sectioncode);
                                         
                    // Update display spans
                    $('#edit_employee_name_display').text(data.em_emplname);
                    $('#edit_section_name_display').text(data.sec_section);
                                         
                    // Clear any validation errors
                    $('#edit_employee_no_error').text('');
                    $('#edit_employee_no').removeClass('is-invalid');
                } else {
                    // Add form
                    $('#employee_no').val(data.em_emplcode);
                    $('#employee_name').val(data.em_emplname);
                    $('#section_name').val(data.sec_section);
                    $('#section_code').val(data.em_sectioncode);
                                         
                    // Update display spans
                    $('#employee_name_display').text(data.em_emplname);
                    $('#section_name_display').text(data.sec_section);
                                         
                    // Clear any validation errors
                    $('#employee_no_error').text('');
                    $('#employee_no').removeClass('is-invalid');
                }
                                 
                $('#employeeModal').modal('hide');
            }
        });
                 
        // Asset No search button click handlers
        $('.search-asset-btn').on('click', function() {
            // Determine if we're in edit mode based on which modal is visible
            isEditMode = $('#editTicketModal').is(':visible');
            $('#assetNoModal').modal('show');
                         
            // Reset search input and initialize asset table
            $('#searchAssetNo').val('');
            initAssetNoDataTable();
        });

        // Initialize DataTable for asset search
        let assetNoDataTable = null;
        function initAssetNoDataTable() {
            // Destroy table if it already exists
            if (assetNoDataTable) {
                assetNoDataTable.destroy();
            }
                         
            // Create new DataTable
            assetNoDataTable = $('#assetNoTable').DataTable({
                processing: true,
                serverSide: false,
                paging: true,
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                searching: false, // Using custom search
                ordering: true,
                order: [[0, 'asc']], // Default sort by asset no
                ajax: {
                    url: base_url + 'TransTicketing/searchAssetNo',
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
                        $('#assetNoTable tbody').html('<tr><td colspan="4" class="text-center">Error loading data. Please try again.</td></tr>');
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
                dom: 't<"bottom"ip>' // Simpler DOM structure
            });
                         
            // Set up length change
            $('#assetNoLength').on('change', function() {
                const newLength = $(this).val();
                assetNoDataTable.page.len(newLength).draw();
            });
                         
            // Add search functionality with debounce
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
                                 
                if (isEditMode) {
                    $('#edit_asset_no').val(data.asset_no);
                    $('#edit_category').val('');
                } else {
                    $('#asset_no').val(data.asset_no);
                    $('#category').val('');
                }
                                 
                $('#assetNoModal').modal('hide');
            }
        });
                 
        // Handle manual asset no input
        $('#asset_no').on('change', function() {
            const assetNo = $(this).val();
                         
            $('#category').val('');
                         
            if (!assetNo) {
                return;
            }
                         
            // Fetch asset details by asset no
            $.ajax({
                url: base_url + 'TransTicketing/getAssetNo',
                type: 'GET',
                data: { assetNo: assetNo },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        // Tidak perlu mengatur display text
                    } else {
                        $('#category').val('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching asset details:', error);
                    $('#category').val('');
                }
            });
        });
                 
        $('#edit_asset_no').on('change', function() {
            const assetNo = $(this).val();
                         
            $('#edit_category').val('');
                         
            if (!assetNo) {
                return;
            }
                         
            // Fetch asset details by asset no
            $.ajax({
                url: base_url + 'TransTicketing/getAssetNo',
                type: 'GET',
                data: { assetNo: assetNo },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        // Tidak perlu mengatur display text
                    } else {
                        $('#edit_category').val('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching asset details:', error);
                    $('#edit_category').val('');
                }
            });
        });

        // Reset asset search when modal is hidden
        $('#assetNoModal').on('hidden.bs.modal', function() {
            $('#searchAssetNo').val('');
            if (assetNoDataTable) {
                assetNoDataTable.destroy();
                assetNoDataTable = null;
            }
        });
                 
        // Edit button click handler (also handles view)
       $('#tabelTicketing').on('click', '.edit-btn, .view-btn', function() {
            const id = $(this).data('id');
            const isViewOnly = $(this).hasClass('view-btn');
            
            // Reset form
            document.getElementById('editTicketForm').reset();
            $('#edit_employee_name_display').text('-');
            $('#edit_section_name_display').text('-');
            
            // Show modal with loading state
            $('#edit_ticket_id_display').text(id);
            $('#edit_ticket_id').val(id);
            
            // Update modal title and button visibility based on view/edit
            if (isViewOnly) {
                $('#editTicketModalLabel').text('View Ticket Details');
                $('#update-btn').hide(); // Hide update button for view mode
                // Change Cancel button text to Close for view mode
                $('.modal-footer .btn-secondary').text('Close');
            } else {
                $('#editTicketModalLabel').text('Edit Ticket Data');
                $('#update-btn').show(); // Show update button for edit mode
                // Reset Cancel button text for edit mode
                $('.modal-footer .btn-secondary').text('Cancel');
            }
            
            $('#editTicketModal').modal('show');
            
            // Get ticket data
            $.ajax({
                url: base_url + 'TransTicketing/getTicketById',
                type: 'GET',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.data) {
                        const ticket = response.data;
                        
                        // Populate form fields
                        $('#edit_employee_no').val(ticket.tt_empno_rep);
                        $('#edit_employee_name').val(ticket.reporter_name);
                        $('#edit_section_name').val(ticket.section_name);
                        $('#edit_section_code').val(ticket.tt_sectioncode_rep);
                        
                        // Update display spans
                        $('#edit_employee_name_display').text(ticket.reporter_name);
                        $('#edit_section_name_display').text(ticket.section_name);
                        
                        // For non-system section, show employee no in display
                        if (!isSystemSection) {
                            $('#edit_employee_no_display').text(ticket.tt_empno_rep);
                        }
                        
                        // Populate asset fields
                        $('#edit_asset_no').val(ticket.tt_assetno);
                        
                        // Load kategori dengan kategori yang dipilih
                        loadCategoryOptions('#edit_category', ticket.tt_categoryequip);
                        
                        $('#edit_case_content').val(ticket.tt_case);
                        $('#edit_handle_content').val(ticket.tt_action);
                        
                        // Set team radio button
                        let teamValue = '1'; // Default ke SDO
                        if (ticket.tt_category === '1') {
                            $('#edit_team_sdo').prop('checked', true);
                            teamValue = '1';
                        } else {
                            $('#edit_team_infra').prop('checked', true);
                            teamValue = '2';
                        }
                        
                        // Save the PIC value for later selection
                        $('#edit_pic_system').data('saved-value', ticket.tt_pic_system);
                        
                        // Load PIC options based on team
                        loadPicSystemUsers(teamValue, '#edit_pic_system', ticket.tt_pic_system);
                        
                        // Format dates
                        const createDate = new Date(ticket.tt_check_date);
                        const day = String(createDate.getDate()).padStart(2, '0');
                        const month = String(createDate.getMonth() + 1).padStart(2, '0');
                        const year = createDate.getFullYear();
                        const formattedCreateDate = `${day}/${month}/${year}`;
                        $('#edit_create_date').val(formattedCreateDate);
                        
                        // Set min date constraint for finish date
                        const minDateStr = createDate.toISOString().split('T')[0];
                        $('#edit_finish_date').attr('min', minDateStr);
                        
                        if (ticket.tt_finish_date) {
                            const finishDate = new Date(ticket.tt_finish_date);
                            // Ensure finish date is not before create date
                            if (finishDate >= createDate) {
                                const formattedDate = finishDate.toISOString().split('T')[0];
                                $('#edit_finish_date').val(formattedDate);
                            } else {
                                $('#edit_finish_date').val(minDateStr);
                            }
                        } else {
                            $('#edit_finish_date').val('');
                        }
                        
                        // Check if current user can edit this ticket (for non-system users)
                        const canCurrentUserEdit = isSystemSection || (ticket.tt_empno_rep == currentUserData.employeeCode);
                        
                        // If it's view only mode OR user is non-system section viewing someone else's ticket
                        if (isViewOnly || (!isSystemSection && !canCurrentUserEdit)) {
                            // FORCEFULLY hide update button for view mode
                            $('#update-btn').hide().addClass('d-none');
                            
                            // Change modal footer button text
                            $('.modal-footer .btn-secondary').text('Close');
                            
                            // For view mode, make all fields readonly but keep them visible and readable
                            $('#editTicketForm input[type="text"], #editTicketForm input[type="date"], #editTicketForm select, #editTicketForm textarea').prop('readonly', true);
                            $('#editTicketForm input[type="radio"]').prop('disabled', true);
                            $('#editTicketForm select').prop('disabled', true);
                            
                            // Hide ALL search buttons in view mode
                            $('.search-asset-btn, .edit-search-employee-btn').hide().addClass('d-none');
                            $('#editTicketModal .search-asset-btn').hide().addClass('d-none');
                            
                            // Additional styling for view mode
                            $('#edit_asset_no').prop('disabled', true).addClass('bg-light');
                            $('#edit_case_content').addClass('bg-light');
                            
                            // Additional restrictions for non-system section users in view mode
                            if (!isSystemSection) {
                                // Disable case field specifically for non-system users
                                $('#edit_case_content').prop('disabled', true);
                            }
                        } else {
                            // Enable form fields for edit mode (system section or non-system editing own ticket)
                            $('#update-btn').show().removeClass('d-none');
                            $('.modal-footer .btn-secondary').text('Cancel');
                            
                            $('#editTicketForm input, #editTicketForm select, #editTicketForm textarea').prop('readonly', false);
                            $('#editTicketForm input[type="radio"]').prop('disabled', false);
                            $('#editTicketForm select').prop('disabled', false);
                            
                            // Show search buttons for edit mode
                            $('.search-asset-btn, .edit-search-employee-btn').show().removeClass('d-none');
                            $('#editTicketModal .search-asset-btn').show().removeClass('d-none');
                            
                            // Remove view mode styling
                            $('#edit_asset_no, #edit_case_content').removeClass('bg-light').prop('disabled', false);
                            
                            // For non-system section in edit mode, keep some fields readonly
                            if (!isSystemSection) {
                                $('#edit_employee_no').prop('readonly', true);
                                $('#edit_handle_content').prop('readonly', true);
                                $('#edit_finish_date').prop('readonly', true);
                                $('.edit-search-employee-btn').hide().addClass('d-none');
                            }
                        }
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to load ticket details',
                        });
                        $('#editTicketModal').modal('hide');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching ticket details:', error);
                    $('#editTicketModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load ticket details. Please try again.'
                    });
                }
            });
        });
                 
        // Add ticket form submission
        $('#submit-btn').on('click', function() {
            // Validate form
            if (!validateForm('addTicketForm')) {
                return;
            }
                         
            // Get form data
            const formData = new FormData(document.getElementById('addTicketForm'));
                         
            // Disable submit button and show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
                         
            // Send AJAX request
            $.ajax({
                url: base_url + 'TransTicketing/store',
                type: 'POST',
                data: Object.fromEntries(formData),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                                                 
                        // Reset form and close modal
                        document.getElementById('addTicketForm').reset();
                        $('#employee_name_display').text('-');
                        $('#section_name_display').text('-');
                        $('#addTicketModal').modal('hide');
                                                 
                        // Refresh the table
                        table.ajax.reload();
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to create ticket'
                        });
                    }
                                         
                    // Re-enable submit button
                    $('#submit-btn').prop('disabled', false).text('Submit Ticket');
                }
            });
        });
                 
        // Update ticket form submission
        $('#update-btn').on('click', function() {
            // Validate form
            if (!validateForm('editTicketForm')) {
                return;
            }
                         
            // Get form data
            const formData = new FormData(document.getElementById('editTicketForm'));
                         
            // Disable update button and show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                         
            // Send AJAX request
            $.ajax({
                url: base_url + 'TransTicketing/update',
                type: 'POST',
                data: Object.fromEntries(formData),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                                                 
                        // Close modal
                        $('#editTicketModal').modal('hide');
                                                 
                        // Refresh the table
                        table.ajax.reload();
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update ticket'
                        });
                    }
                                         
                    // Re-enable update button
                    $('#update-btn').prop('disabled', false).text('Update Ticket');
                },
                error: function(xhr, status, error) {
                    console.error('Error updating ticket:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the ticket. Please try again.'
                    });
                                         
                    // Re-enable update button
                    $('#update-btn').prop('disabled', false).text('Update Ticket');
                }
            });
        });
                 
        // Delete button click handler
        $('#tabelTicketing').on('click', '.delete-btn', function() {
            const id = $(this).data('id');
                         
            // Show confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "This ticketing will be marked as deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request to delete
                    $.ajax({
                        url: base_url + 'TransTicketing/delete',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                                                 
                                // Refresh the datatable
                                table.ajax.reload();
                            } else {
                                // Show error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete ticket',
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
                         
            // Clear previous error messages
            form.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                         
            // Check if it's add or edit form
            const prefix = formId === 'editTicketForm' ? 'edit_' : '';
                         
            // Validate employee (only if field exists and is visible)
            const employeeNoField = form.querySelector(`#${prefix}employee_no`);
            if (employeeNoField && !employeeNoField.value) {
                document.getElementById(`${prefix}employee_no_error`).textContent = 'Employee is required';
                employeeNoField.classList.add('is-invalid');
                isValid = false;
            }
                         
            // Validate PIC System (only if field exists and is visible)
            const picSystemField = form.querySelector(`#${prefix}pic_system`);
            if (picSystemField && !picSystemField.value) {
                document.getElementById(`${prefix}pic_system_error`).textContent = 'PIC System is required';
                picSystemField.classList.add('is-invalid');
                isValid = false;
            }
                         
            // Validate Case (only if field exists and is visible)
            const caseContentField = form.querySelector(`#${prefix}case_content`);
            if (caseContentField && !caseContentField.value) {
                document.getElementById(`${prefix}case_content_error`).textContent = 'Case is required';
                caseContentField.classList.add('is-invalid');
                isValid = false;
            }

            // Validate Team (only if field exists and is visible)
            const teamFields = form.querySelectorAll(`input[name="team"]`);
            if (teamFields.length > 0) {
                const teamChecked = form.querySelector(`input[name="team"]:checked`);
                if (!teamChecked) {
                    document.getElementById(`${prefix}team_error`).textContent = 'Team selection is required';
                    isValid = false;
                }
            }
                         
            // Validate Finish Date is not before Create Date (for edit form only and if System Section)
            if (prefix === 'edit_' && isSystemSection) {
                const finishDateField = form.querySelector('#edit_finish_date');
                const createDateStr = form.querySelector('#edit_create_date').value;
                                 
                if (finishDateField && finishDateField.value) {
                    // Parse create date (dd/mm/yyyy format)
                    const parts = createDateStr.split('/');
                    if (parts.length === 3) {
                        const createDate = new Date(parts[2], parts[1] - 1, parts[0]);
                        const finishDate = new Date(finishDateField.value);
                                                 
                        if (finishDate < createDate) {
                            document.getElementById('edit_finish_date_error').textContent = 'Finish date cannot be earlier than create date';
                            finishDateField.classList.add('is-invalid');
                            isValid = false;
                        }
                    }
                }
            }
                         
            return isValid;
        }
                 
        // Reset forms when modals are hidden
        $('#addTicketModal').on('hidden.bs.modal', function() {
            document.getElementById('addTicketForm').reset();
            $('#addTicketForm .error-message').text('');
            $('#addTicketForm .is-invalid').removeClass('is-invalid');
                         
            // Reset form-info classes
            $('.form-info').removeClass('border-danger');
                         
            // Reset display spans
            $('#employee_name_display').text('-');
            $('#section_name_display').text('-');
                         
            // Reset PIC selection - set default to SDO
            $('#team_sdo').prop('checked', true);
            $('#pic_system').val('');
        });
                 
        $('#editTicketModal').on('hidden.bs.modal', function() {
            $('#editTicketForm .error-message').text('');
            $('#editTicketForm .is-invalid').removeClass('is-invalid');
            
            // Reset form-info classes
            $('.form-info').removeClass('border-danger');
            
            // Reset display spans
            $('#edit_employee_name_display').text('-');
            $('#edit_section_name_display').text('-');
            
            // Remove temporary fields
            $('#temp_handle_section, #temp_finish_date_section').remove();
            
            // Re-enable all form fields (in case they were disabled for view mode)
            $('#editTicketForm input, #editTicketForm select, #editTicketForm textarea').prop('disabled', false);
            $('#editTicketForm input, #editTicketForm select, #editTicketForm textarea').prop('readonly', false);
            
            // Remove any added styling classes
            $('#edit_asset_no, #edit_case_content').removeClass('bg-light');
            
            // Show search buttons and remove d-none class
            $('.search-asset-btn, .edit-search-employee-btn').show().removeClass('d-none');
            $('#editTicketModal .search-asset-btn').show().removeClass('d-none');
            
            // Reset update button visibility and remove d-none class
            $('#update-btn').show().removeClass('d-none');
            
            // Reset modal footer button text
            $('.modal-footer .btn-secondary').text(isSystemSection ? 'Cancel' : 'Close');
        });
                 
        // Reset employee search when modal is hidden
        $('#employeeModal').on('hidden.bs.modal', function() {
            $('#searchEmployee').val('');
            if (employeeDataTable) {
                employeeDataTable.destroy();
                employeeDataTable = null;
            }
        });
                 
        // Handle finish date change to validate against create date
        $('#edit_finish_date').on('change', function() {
            const finishDate = new Date($(this).val());
            const createDateStr = $('#edit_create_date').val();
            const parts = createDateStr.split('/');
                         
            if (parts.length === 3 && $(this).val()) {
                const createDate = new Date(parts[2], parts[1] - 1, parts[0]);
                                 
                if (finishDate < createDate) {
                    $('#edit_finish_date_error').text('Finish date cannot be earlier than create date');
                    $(this).addClass('is-invalid');
                    // Set value to min date
                    const minDateStr = createDate.toISOString().split('T')[0];
                    $(this).val(minDateStr);
                } else {
                    $('#edit_finish_date_error').text('');
                    $(this).removeClass('is-invalid');
                }
            }
        });
    });
    </script>
</div>

<?= $this->endSection() ?>