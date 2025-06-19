<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<style>
    /* Readonly style to indicate non-editable fields */
    input[readonly], textarea[readonly], select[readonly] {
        background-color: #e9ecef; /* Light gray background */
        cursor: not-allowed;        /* Not-allowed cursor */
    }
    /* Force uppercase input for VLAN Name consistency */
    #add_name, #edit_name {
        text-transform: uppercase;
    }

    /* Styles for responsive table containers */
    .card-datatable.table-responsive {
        overflow-x: auto; /* Enable horizontal scrolling for small screens */
        width: 100%;       /* Full width */
    }

    /* Adjust column widths for better display in the DataTable */
    #tabelVlan th:nth-child(1), #tabelVlan td:nth-child(1) { width: 5%; }  /* No. column */
    #tabelVlan th:nth-child(2), #tabelVlan td:nth-child(2) { width: 15%; } /* Action column */
    #tabelVlan th:nth-child(3), #tabelVlan td:nth-child(3) { width: 5%; }    /* ID (PK) */
    #tabelVlan th:nth-child(4), #tabelVlan td:nth-child(4) { width: 10%; }    /* VLAN ID (inputted) */
    #tabelVlan th:nth-child(5), #tabelVlan td:nth-child(5) { width: 30%; }  /* VLAN Name column */
    #tabelVlan th:nth-child(6), #tabelVlan td:nth-child(6) { width: 20%; }  /* Last Update column */
    #tabelVlan th:nth-child(7), #tabelVlan td:nth-child(7) { width: 15%; }  /* Last User column */


    /* Modal dialog styling for a more compact VLAN form */
    #addVlanModal .modal-dialog,
    #editVlanModal .modal-dialog {
        max-width: 60%; /* Use 60% of screen width */
        width: auto !important; /* Override inline width for responsiveness */
    }
    #addVlanModal .modal-content,
    #editVlanModal .modal-content {
        height: auto;           /* Auto height based on content */
        display: flex;          /* Use flexbox for layout */
        flex-direction: column; /* Stack children vertically */
    }
    #addVlanModal .modal-body,
    #editVlanModal .modal-body {
        flex-grow: 1;        /* Allow modal body to grow */
        overflow-y: auto;    /* Enable vertical scrolling if content overflows */
    }

    /* Custom DataTables controls layout for search and pagination */
    .dataTables_wrapper .top {
        padding: 0 1.25rem; /* Horizontal padding for the top section */
    }
    .dataTables_wrapper .dataTables_length {
        display: flex;           /* Use flexbox for length dropdown and other elements */
        align-items: center;    /* Vertically align items */
        gap: 10px;               /* Space between elements */
    }
    .dataTables_wrapper .dataTables_length label {
        margin-bottom: 0; /* Remove default margin from label */
    }
    .dataTables_wrapper .dataTables_filter {
        margin-left: auto; /* Push the search input to the right */
    }
    .dataTables_wrapper .dataTables_filter label {
        margin-right: 0.5rem; /* Space to the right of "Search:" label */
    }
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5rem; /* Space to the left of the search input */
    }
</style>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">VLAN Management</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addVlanModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add VLAN
        </button>
        <button type="button" class="btn btn-success me-2" id="exportVlanExcelBtn">
            <span class="btn-label">
                <i class="fa fa-file-excel"></i>
            </span>
            Export Excel
        </button>
    </p>

    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelVlan">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>Action</th>
                    <th>ID</th>
                    <th>VLAN ID</th>
                    <th>VLAN Name</th>
                    <th>Last Updated</th>
                    <th>Last User</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>

    <div class="modal fade" id="addVlanModal" tabindex="-1" aria-labelledby="addVlanModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVlanModalLabel">Add VLAN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addVlanForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="add_vlan_id" class="form-label">VLAN ID</label>
                                <input type="number" class="form-control" id="add_vlan_id" name="vlan_id" min="1">
                                <div class="invalid-feedback" id="add_vlan_id_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_name" class="form-label">VLAN Name</label>
                                <input type="text" class="form-control" id="add_name" name="name" maxlength="250">
                                <div class="invalid-feedback" id="add_name_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveVlan">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editVlanModal" tabindex="-1" aria-labelledby="editVlanModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVlanModalLabel">Edit VLAN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editVlanForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <input type="hidden" id="edit_id" name="id"> <div class="col-md-6">
                                <label for="edit_vlan_id" class="form-label">VLAN ID</label>
                                <input type="number" class="form-control" id="edit_vlan_id" name="vlan_id" min="1">
                                <div class="invalid-feedback" id="edit_vlan_id_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_name" class="form-label">VLAN Name</label>
                                <input type="text" class="form-control" id="edit_name" name="name" maxlength="250">
                                <div class="invalid-feedback" id="edit_name_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="updateVlan">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
$(document).ready(function() {
    // Base URL for AJAX requests
    const base_url = '<?= base_url('MstVlan') ?>';

    let tabelVlan; // Global variable for the DataTable instance

    // Function to force input values to uppercase
    $('#add_name, #edit_name').on('blur keyup', function(){
        this.value = this.value.toUpperCase();
    });

    /**
     * Initializes or re-initializes the VLAN DataTable.
     * Destroys any existing DataTable instance before creating a new one.
     */
    function initializeVlanDataTable() {
        if ($.fn.DataTable.isDataTable('#tabelVlan')) {
            tabelVlan.destroy(); // Destroy existing DataTable instance
        }

        tabelVlan = $('#tabelVlan').DataTable({
            scrollX: true, // Enable horizontal scrolling for overflow content
            pageLength: 10, // Default number of rows per page
            order: [[5, 'desc']], // Order by 'Last Updated' column (index 5) in descending order

            // AJAX configuration for fetching data from the server
            ajax: {
                url: base_url + "/getDataVlan", // Controller method to get VLAN data
                dataSrc: "", // Data is returned directly as an array (not nested under a key)
                error: function(xhr, status, error) {
                    // Log and display error if data fetching fails
                    console.error("Error fetching VLAN data:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load VLAN data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error),
                    });
                }
            },
            // Column definitions for DataTable
            columns: [
                {
                    data: null, // "No." (Row Number) column
                    orderable: false, // Not sortable
                    className: 'text-center', // Center align text
                    render: function (data, type, row, meta) {
                        return meta.row + 1; // Display row number starting from 1
                    }
                },
                {
                    data: null, // "Action" column (Edit/Delete buttons)
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex justify-content-center gap-2">
                                <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.id}" title="Edit VLAN">
                                    <i class="fa fa-pen-to-square"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.id}" title="Delete VLAN">
                                    <i class="fa fa-trash-can"></i>
                                </a>
                            </div>`;
                    }
                },
                { data: 'id' }, // Auto-increment PK (tv_id)
                { data: 'vlan_id' }, // User-input VLAN ID (tv_id_vlan)
                { data: 'name', render: d => d ? d.toUpperCase() : '' }, // VLAN Name, always uppercase
                {
                    data: 'last_update', // Last Update timestamp
                    render: function(data) {
                        // Format date and time for display
                        return data ? new Date(data).toLocaleString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '';
                    }
                },
                { data: 'last_user' } // Last User name (from tv_lastuser VARCHAR)
            ],
            // Language settings for DataTables controls
            language: {
                "sLengthMenu": "Show _MENU_ entries",
                "sSearch": "Search:",
                "oPaginate": {
                    "sNext": '<i class="fa fa-angle-right"></i>', // Custom next page icon
                    "sPrevious": '<i class="fa fa-angle-left"></i>' // Custom previous page icon
                }
            },
            // DOM manipulation for custom layout of DataTables controls
            dom: '<"top d-flex justify-content-between align-items-center"<"dataTables_length"l><"dataTables_filter"f>>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
            initComplete: function() {
                // No additional custom elements (like status filter or export button)
                // are added for this simpler VLAN management module.
            }
        });
    }

    // Initialize the DataTable when the page loads
    initializeVlanDataTable();

    /**
     * Checks if at least one of the VLAN ID or VLAN Name fields is filled and no validation errors are present
     * to enable/disable the submit button.
     * @param {string} formId The ID of the form (e.g., '#addVlanForm').
     * @param {string} submitBtnId The ID of the submit button (e.g., '#saveVlan').
     */
    function checkFormInputs(formId, submitBtnId) {
        let isVlanIdFilled = $(`${formId} input[name="vlan_id"]`).val().trim() !== '';
        let isNameFilled = $(`${formId} input[name="name"]`).val().trim() !== '';

        let isVlanIdInvalid = $(`${formId} #add_vlan_id`).hasClass('is-invalid') || $(`${formId} #edit_vlan_id`).hasClass('is-invalid');
        let isNameInvalid = $(`${formId} #add_name`).hasClass('is-invalid') || $(`${formId} #edit_name`).hasClass('is-invalid');

        // The button is enabled if (VLAN ID is filled OR VLAN Name is filled) AND (VLAN ID is NOT invalid) AND (VLAN Name is NOT invalid)
        const shouldEnable = (isVlanIdFilled || isNameFilled) && !isVlanIdInvalid && !isNameInvalid;
        $(`${submitBtnId}`).prop('disabled', !shouldEnable);
    }


    // --- Add VLAN Logic ---
    $('#addVlanForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Clear previous validation feedback
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').text('').hide();

        // Perform client-side check for "at least one filled"
        const vlanIdInput = $('#add_vlan_id').val().trim();
        const vlanNameInput = $('#add_name').val().trim();

        if (vlanIdInput === '' && vlanNameInput === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Input Required',
                text: 'Either VLAN ID or VLAN Name must be filled.',
            });
            $('#saveVlan').prop('disabled', true); // Keep button disabled
            return; // Stop the submission
        }

        // Disable save button and change text to indicate saving
        $('#saveVlan').prop('disabled', true).text('Saving...');

        $.ajax({
            url: base_url + '/add', // AJAX endpoint for adding VLAN
            type: 'POST',
            data: $(this).serialize(), // Serialize form data
            success: function(response) {
                if (response.status) {
                    // Show success message using SweetAlert2
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Hide modal and reload DataTable on success
                        $('#addVlanModal').modal('hide');
                        tabelVlan.ajax.reload();
                    });
                } else {
                    // Handle validation errors or server-side errors
                    if (response.errors) {
                        for (const field in response.errors) {
                            if (response.errors.hasOwnProperty(field)) {
                                if (field === 'general') {
                                    Swal.fire('Warning', response.errors[field], 'warning');
                                } else {
                                    $(`#add_${field}`).addClass('is-invalid');
                                    $(`#add_${field}_error`).text(response.errors[field]).show();
                                }
                            }
                        }
                    } else if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                    } else {
                        Swal.fire('Error', 'Failed to add VLAN. Please try again.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX request errors
                console.error("AJAX Error:", status, error, xhr.responseText);
                let errorMessage = 'Request failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : error);
                Swal.fire('Error', errorMessage, 'error');
            },
            complete: function() {
                // Re-enable save button and reset text after request completes
                $('#saveVlan').prop('disabled', false).text('Save');
                checkFormInputs('#addVlanForm', '#saveVlan'); // Re-check button state
            }
        });
    });

    // Event listener for when the Add modal is shown
    $('#addVlanModal').on('shown.bs.modal', function() {
        checkFormInputs('#addVlanForm', '#saveVlan'); // Initial check for button state
        $('#add_vlan_id').focus(); // Set focus to the VLAN ID input field
    });

    // Event listener for input changes in the Add form to enable/disable save button
    $('#addVlanForm input').on('keyup change', function() {
        checkFormInputs('#addVlanForm', '#saveVlan');
    });

    // Event listener for when the Add modal is hidden
    $('#addVlanModal').on('hidden.bs.modal', function() {
        $('#addVlanForm')[0].reset(); // Reset form fields
        $('#addVlanForm').find('.is-invalid').removeClass('is-invalid'); // Clear validation styles
        $('#addVlanForm').find('.invalid-feedback').text('').hide(); // Clear validation messages
        $('#saveVlan').prop('disabled', true).text('Save'); // Reset save button state
    });

    // --- Edit VLAN Logic ---
    $('#tabelVlan').on('click', '.edit-btn', function() {
        const id = $(this).data('id'); // Get the auto-increment PK of the VLAN to edit
        $.ajax({
            url: base_url + '/edit', // AJAX endpoint for fetching edit data
            type: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.status) {
                    const d = response.data;
                    // Populate edit form fields with fetched data
                    $('#edit_id').val(d.id); // Populate hidden PK
                    $('#edit_vlan_id').val(d.vlan_id); // Populate user-input VLAN ID
                    $('#edit_name').val(d.name);
                    // No need to populate last_update and last_user fields as they are not editable.
                    // If you want to display them in the modal, you'll need to add elements for them in the modal HTML.

                    // Clear previous validation feedback for the edit form
                    $('#editVlanForm').find('.is-invalid').removeClass('is-invalid');
                    $('#editVlanForm').find('.invalid-feedback').text('').hide();

                    checkFormInputs('#editVlanForm', '#updateVlan'); // Initial check for button state
                    $('#editVlanModal').modal('show'); // Show the edit modal
                } else {
                    Swal.fire('Error', response.message || 'Data not found', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });

    $('#editVlanForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Clear previous validation feedback
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').text('').hide();

        // Perform client-side check for "at least one filled"
        const vlanIdInput = $('#edit_vlan_id').val().trim();
        const vlanNameInput = $('#edit_name').val().trim();

        if (vlanIdInput === '' && vlanNameInput === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Input Required',
                text: 'Either VLAN ID or VLAN Name must be filled.',
            });
            $('#updateVlan').prop('disabled', true); // Keep button disabled
            return; // Stop the submission
        }

        // Disable update button and change text to indicate updating
        $('#updateVlan').prop('disabled', true).text('Updating...');

        $.ajax({
            url: base_url + '/update', // AJAX endpoint for updating VLAN
            type: 'POST',
            data: $(this).serialize(), // Serialize form data
            success: function(response) {
                if (response.status) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Hide modal and reload DataTable on success
                        $('#editVlanModal').modal('hide');
                        tabelVlan.ajax.reload();
                    });
                } else {
                    // Handle validation errors or server-side errors
                    if (response.errors) {
                        for (const field in response.errors) {
                            if (response.errors.hasOwnProperty(field)) {
                                if (field === 'general') {
                                    Swal.fire('Warning', response.errors[field], 'warning');
                                } else {
                                    $(`#edit_${field}`).addClass('is-invalid');
                                    $(`#edit_${field}_error`).text(response.errors[field]).show();
                                }
                            }
                        }
                    } else if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                    } else {
                        Swal.fire('Error', 'Failed to update VLAN. Please try again.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX request errors
                console.error("AJAX Error:", status, error, xhr.responseText);
                let errorMessage = 'Request failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : error);
                Swal.fire('Error', errorMessage, 'error');
            },
            complete: function() {
                // Re-enable update button and reset text
                $('#updateVlan').prop('disabled', false).text('Update');
                checkFormInputs('#editVlanForm', '#updateVlan'); // Re-check button state
            }
        });
    });

    // Event listener for when the Edit modal is shown
    $('#editVlanModal').on('shown.bs.modal', function() {
        checkFormInputs('#editVlanForm', '#updateVlan'); // Initial check for button state
        $('#edit_vlan_id').focus(); // Set focus to the VLAN ID input field
    });

    // Event listener for input changes in the Edit form to enable/disable update button
    $('#editVlanForm input').on('keyup change', function() {
        checkFormInputs('#editVlanForm', '#updateVlan');
    });

    // Event listener for when the Edit modal is hidden
    $('#editVlanModal').on('hidden.bs.modal', function() {
        $('#editVlanForm')[0].reset(); // Reset form fields
        $('#editVlanForm').find('.is-invalid').removeClass('is-invalid'); // Clear validation styles
        $('#editVlanForm').find('.invalid-feedback').text('').hide(); // Clear validation messages
        $('#updateVlan').prop('disabled', true).text('Update'); // Reset update button state
    });

    // --- Delete VLAN Logic ---
    $('#tabelVlan').on('click', '.delete-btn', function() {
        const id = $(this).data('id'); // Get the auto-increment PK of the VLAN to delete
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) { // If user confirms deletion
                $.ajax({
                    url: base_url + '/delete', // AJAX endpoint for deleting VLAN
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            tabelVlan.ajax.reload(); // Reload DataTable to reflect changes
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

    // --- Duplicate Check for VLAN Name on blur (when input loses focus) ---
    $('#add_name').on('blur', function() {
        const vlanName = $(this).val().trim();
        const nameInput = $(this); // Reference to VLAN Name input

        if (vlanName !== '') {
            $.ajax({
                url: base_url + '/checkDuplicateName', // AJAX endpoint for name duplicate check
                type: 'POST',
                data: { name: vlanName }, // Send the VLAN name
                success: function(response) {
                    if (response.existName) {
                        // If duplicate exists, show error and disable save button
                        nameInput.addClass('is-invalid');
                        $('#add_name_error').text('This VLAN Name already exists.').show();
                        $('#saveVlan').prop('disabled', true);
                    } else {
                        // If no duplicate, clear error and re-check form inputs
                        nameInput.removeClass('is-invalid');
                        $('#add_name_error').text('').hide();
                        checkFormInputs('#addVlanForm', '#saveVlan');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Duplicate name check error:", error);
                }
            });
        } else {
            // If input is empty, clear validation and re-check form inputs
            nameInput.removeClass('is-invalid');
            $('#add_name_error').text('').hide();
            checkFormInputs('#addVlanForm', '#saveVlan');
        }
    });

    $('#edit_name').on('blur', function() {
        const vlanName = $(this).val().trim();
        const id = $('#edit_id').val(); // Get current auto-increment PK for edit mode
        const nameInput = $(this); // Reference to VLAN Name input

        if (vlanName !== '') {
            $.ajax({
                url: base_url + '/checkDuplicateName',
                type: 'POST',
                data: { name: vlanName, id: id }, // Send VLAN name and PK ID
                success: function(response) {
                    if (response.existName) {
                        // If duplicate exists, show error and disable update button
                        nameInput.addClass('is-invalid');
                        $('#edit_name_error').text('This VLAN Name already exists.').show();
                        $('#updateVlan').prop('disabled', true);
                    } else {
                        // If no duplicate, clear error and re-check form inputs
                        nameInput.removeClass('is-invalid');
                        $('#edit_name_error').text('').hide();
                        checkFormInputs('#editVlanForm', '#updateVlan');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Duplicate name check error:", error);
                }
            });
        } else {
            // If input is empty, clear validation and re-check form inputs
            nameInput.removeClass('is-invalid');
            $('#edit_name_error').text('').hide();
            checkFormInputs('#editVlanForm', '#updateVlan');
        }
    });

    // --- Duplicate Check for VLAN ID on blur (when input loses focus) ---
    $('#add_vlan_id').on('blur', function() {
        const vlanId = $(this).val().trim();
        const vlanIdInput = $(this); // Reference to VLAN ID input

        // Only perform check if VLAN ID is filled and is a valid number
        if (vlanId === '' || isNaN(vlanId)) {
            vlanIdInput.removeClass('is-invalid');
            $('#add_vlan_id_error').text('').hide();
            checkFormInputs('#addVlanForm', '#saveVlan');
            return; // Exit if empty or not a number
        }

        $.ajax({
            url: base_url + '/checkDuplicateVlanId', // AJAX endpoint for VLAN ID duplicate check
            type: 'POST',
            data: { vlan_id: vlanId }, // Send the VLAN ID
            success: function(response) {
                if (response.existVlanId) {
                    // If duplicate VLAN ID exists, show error and disable save button
                    vlanIdInput.addClass('is-invalid');
                    $('#add_vlan_id_error').text('This VLAN ID already exists.').show();
                    $('#saveVlan').prop('disabled', true);
                } else {
                    // If no duplicate, clear error and re-check form inputs
                    vlanIdInput.removeClass('is-invalid');
                    $('#add_vlan_id_error').text('').hide();
                    checkFormInputs('#addVlanForm', '#saveVlan');
                }
            },
            error: function(xhr, status, error) {
                console.error("Duplicate VLAN ID check error:", error);
            }
        });
    });

    $('#edit_vlan_id').on('blur', function() {
        const vlanId = $(this).val().trim();
        const id = $('#edit_id').val(); // Get current auto-increment PK for edit mode
        const vlanIdInput = $(this); // Reference to VLAN ID input

        // Only perform check if VLAN ID is filled and is a valid number
        if (vlanId === '' || isNaN(vlanId)) {
            vlanIdInput.removeClass('is-invalid');
            $('#edit_vlan_id_error').text('').hide();
            checkFormInputs('#editVlanForm', '#updateVlan');
            return; // Exit if empty or not a number
        }

        $.ajax({
            url: base_url + '/checkDuplicateVlanId',
            type: 'POST',
            data: { vlan_id: vlanId, id: id }, // Send VLAN ID and PK ID
            success: function(response) {
                if (response.existVlanId) {
                    // If duplicate VLAN ID exists, show error and disable update button
                    vlanIdInput.addClass('is-invalid');
                    $('#edit_vlan_id_error').text('This VLAN ID already exists.').show();
                    $('#updateVlan').prop('disabled', true);
                } else {
                    // If no duplicate, clear error and re-check form inputs
                    vlanIdInput.removeClass('is-invalid');
                    $('#edit_vlan_id_error').text('').hide();
                    checkFormInputs('#editVlanForm', '#updateVlan');
                }
            },
            error: function(xhr, status, error) {
                console.error("Duplicate VLAN ID check error:", error);
            }
        });
    });

    // Handle click for the new "Export Excel" button
    $('#exportVlanExcelBtn').on('click', function() {
        Swal.fire({
            title: 'Generating Excel Report...',
            text: 'Please wait, your report is being generated.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        window.location.href = base_url + '/exportExcel';
        // Close Swal after a short delay (give time for file download to initiate)
        setTimeout(() => {
            Swal.close();
        }, 2000); // Adjust delay if download is very slow to start
    });
    
}); // Ini adalah penutup dari $(document).ready(function() { ... });


</script>
<?= $this->endSection() ?>