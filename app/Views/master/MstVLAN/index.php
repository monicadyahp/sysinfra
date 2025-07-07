<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">VLAN Management</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVlanModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add New VLAN
        </button>
    </p>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelVlan">
            <thead class="table-light">
                <tr>
                    <th width="8%">Action</th>
                    <th>ID</th>
                    <th>VLAN ID</th>
                    <th>VLAN Name</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <!-- Add VLAN Modal -->
    <div class="modal fade" id="addVlanModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addVlanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVlanModalLabel">Add New VLAN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addVlanForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="vlan_id" class="form-label">VLAN ID</label>
                                <input type="text" class="form-control" id="vlan_id" name="vlan_id" placeholder="Type VLAN ID">
                                <div class="error-message text-danger mt-1" id="vlan_id_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">VLAN Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Type VLAN Name">
                                <div class="error-message text-danger mt-1" id="name_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submit-btn">Submit VLAN</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit VLAN Modal -->
    <div class="modal fade" id="editVlanModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editVlanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVlanModalLabel">Edit VLAN Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editVlanForm">
                        <input type="hidden" id="edit_id" name="id">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_vlan_id" class="form-label">VLAN ID</label>
                                <input type="text" class="form-control" id="edit_vlan_id" name="vlan_id" placeholder="Type VLAN ID">
                                <div class="error-message text-danger mt-1" id="edit_vlan_id_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_name" class="form-label">VLAN Name</label>
                                <input type="text" class="form-control" id="edit_name" name="name" placeholder="Type VLAN Name">
                                <div class="error-message text-danger mt-1" id="edit_name_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="update-btn">Update VLAN</button>
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
        </style>
    `);

    let base_url = '<?= base_url() ?>';
    let tabelVlan;
    
    // Initialize DataTable
    function initializeVlanDataTable() {
        if ($.fn.DataTable.isDataTable('#tabelVlan')) {
            tabelVlan.destroy();
        }

        tabelVlan = $('#tabelVlan').DataTable({
            scrollX: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], 
            order: [[1, 'desc']], // Order by ID descending
            autoWidth: false,
            ajax: {
                url: base_url + "MstVLAN/getData",
                dataSrc: function(json) {
                    return json;
                },
                beforeSend: function() {
                    let spinner = `
                        <div class="align-items-center justify-content-center gap-2">
                            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                        </div>`;
                    $('#tabelVlan tbody').html(`<tr><td colspan="4">${spinner}</td></tr>`);
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                    $('#tabelVlan tbody').html('<tr><td colspan="4" class="text-center">Error loading data. Please try again.</td></tr>');
                }
            },
            columns: [
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    width: '8%',
                    render: function(data, type, row) {
                        let buttons = `
                            <div class="d-flex justify-content-center gap-2">
                                <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.id}" title="Edit VLAN">
                                    <i class="fa fa-pen-to-square"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.id}" title="Delete VLAN">
                                    <i class="fa fa-trash-can"></i>
                                </a>
                            </div>
                        `;
                        
                        return buttons;
                    }
                },
                { data: 'id' },
                { data: 'vlan_id', render: function(data) { return data ? data : '-'; } },
                { data: 'name', render: function(data) { return data ? data : '-'; } }
            ],
            drawCallback: function() {
                // No export functionality
            }
        });
    }

    // Form validation function
    function validateForm(formId) {
        let isValid = true;
        const form = document.getElementById(formId);
                
        form.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
        const prefix = formId === 'editVlanForm' ? 'edit_' : '';
        
        // Validate at least one field is filled
        const vlanIdField = form.querySelector(`#${prefix}vlan_id`);
        const nameField = form.querySelector(`#${prefix}name`);
        
        if ((!vlanIdField.value || vlanIdField.value.trim() === '') && 
            (!nameField.value || nameField.value.trim() === '')) {
            // Show error message but don't mark specific fields as invalid
            Swal.fire({
                icon: 'warning',
                title: 'Input Required',
                text: 'Either VLAN ID or VLAN Name must be filled.'
            });
            isValid = false;
        }
                
        return isValid;
    }

    // Initialize DataTable on page load
    initializeVlanDataTable();

    // Edit button click handler
    $('#tabelVlan').on('click', '.edit-btn', function() {
        const id = $(this).data('id');

        // Reset form
        document.getElementById('editVlanForm').reset();
        $('#editVlanModalLabel').text('Edit VLAN Data');
        $('#update-btn').show();
        $('.modal-footer .btn-secondary').text('Cancel');
        $('#edit_id').val(id);
        $('#editVlanModal').modal('show');
        
        // Get VLAN data using getVLANById route
        $.ajax({
            url: base_url + 'MstVLAN/getVLANById',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const vlan = response.data;
                    
                    // Fill form fields
                    $('#edit_vlan_id').val(vlan.vlan_id);
                    $('#edit_name').val(vlan.name);
                    
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load VLAN details',
                    });
                    $('#editVlanModal').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching VLAN details:', error);
                $('#editVlanModal').modal('hide');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load VLAN details. Please try again.'
                });
            }
        });
    });

    // Delete button click handler
    $('#tabelVlan').on('click', '.delete-btn', function() {
        const id = $(this).data('id');
                        
        Swal.fire({
            title: 'Are you sure?',
            text: "This VLAN will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + 'MstVLAN/delete',
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
                                                                            
                            tabelVlan.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to delete VLAN',
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
    
    // Add VLAN form submission
    $('#submit-btn').on('click', function() {
        if (!validateForm('addVlanForm')) {
            return;
        }
                        
        const formData = new FormData(document.getElementById('addVlanForm'));
                        
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
                        
        $.ajax({
            url: base_url + 'MstVLAN/store',
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
                                                                    
                    document.getElementById('addVlanForm').reset();
                    $('#addVlanModal').modal('hide');
                                                                    
                    tabelVlan.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to create VLAN'
                    });
                }
                                                        
                $('#submit-btn').prop('disabled', false).text('Submit VLAN');
            },
            error: function(xhr, status, error) {
                console.error('Error creating VLAN:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while creating the VLAN. Please try again.'
                });
                                                        
                $('#submit-btn').prop('disabled', false).text('Submit VLAN');
            }
        });
    });

    // Update VLAN form submission
    $('#update-btn').on('click', function() {
        if (!validateForm('editVlanForm')) {
            return;
        }
                        
        const formData = new FormData(document.getElementById('editVlanForm'));
                        
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                        
        $.ajax({
            url: base_url + 'MstVLAN/update',
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
                                                                    
                    $('#editVlanModal').modal('hide');
                                                                    
                    tabelVlan.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to update VLAN'
                    });
                }
                                                        
                $('#update-btn').prop('disabled', false).text('Update VLAN');
            },
            error: function(xhr, status, error) {
                console.error('Error updating VLAN:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the VLAN. Please try again.'
                });
                                                        
                $('#update-btn').prop('disabled', false).text('Update VLAN');
            }
        });
    });

    // Client-side duplicate check for VLAN ID
    $('#vlan_id').on('blur', function() {
        const vlanId = $(this).val().trim();
        const vlanIdInput = $(this);

        if (vlanId === '') {
            vlanIdInput.removeClass('is-invalid');
            $('#vlan_id_error').text('').hide();
            return;
        }

        // Check if VLAN ID already exists in current DataTable data
        if (tabelVlan && tabelVlan.data()) {
            const currentData = tabelVlan.data().toArray();
            const isDuplicate = currentData.some(function(row) {
                return row.vlan_id && row.vlan_id.toString() === vlanId;
            });

            if (isDuplicate) {
                vlanIdInput.addClass('is-invalid');
                $('#vlan_id_error').text('This VLAN ID already exists.').show();
            } else {
                vlanIdInput.removeClass('is-invalid');
                $('#vlan_id_error').text('').hide();
            }
        }
    });

    $('#edit_vlan_id').on('blur', function() {
        const vlanId = $(this).val().trim();
        const currentId = $('#edit_id').val();
        const vlanIdInput = $(this);

        if (vlanId === '') {
            vlanIdInput.removeClass('is-invalid');
            $('#edit_vlan_id_error').text('').hide();
            return;
        }

        // Check if VLAN ID already exists in current DataTable data (excluding current record)
        if (tabelVlan && tabelVlan.data()) {
            const currentData = tabelVlan.data().toArray();
            const isDuplicate = currentData.some(function(row) {
                return row.vlan_id && row.vlan_id.toString() === vlanId && row.id.toString() !== currentId;
            });

            if (isDuplicate) {
                vlanIdInput.addClass('is-invalid');
                $('#edit_vlan_id_error').text('This VLAN ID already exists.').show();
            } else {
                vlanIdInput.removeClass('is-invalid');
                $('#edit_vlan_id_error').text('').hide();
            }
        }
    });

    // Client-side duplicate check for VLAN Name
    $('#name').on('blur', function() {
        const vlanName = $(this).val().trim();
        const nameInput = $(this);

        if (vlanName === '') {
            nameInput.removeClass('is-invalid');
            $('#name_error').text('').hide();
            return;
        }

        // Check if VLAN Name already exists in current DataTable data
        if (tabelVlan && tabelVlan.data()) {
            const currentData = tabelVlan.data().toArray();
            const isDuplicate = currentData.some(function(row) {
                return row.name && row.name === vlanName;
            });

            if (isDuplicate) {
                nameInput.addClass('is-invalid');
                $('#name_error').text('This VLAN Name already exists.').show();
            } else {
                nameInput.removeClass('is-invalid');
                $('#name_error').text('').hide();
            }
        }
    });

    $('#edit_name').on('blur', function() {
        const vlanName = $(this).val().trim();
        const currentId = $('#edit_id').val();
        const nameInput = $(this);

        if (vlanName === '') {
            nameInput.removeClass('is-invalid');
            $('#edit_name_error').text('').hide();
            return;
        }

        // Check if VLAN Name already exists in current DataTable data (excluding current record)
        if (tabelVlan && tabelVlan.data()) {
            const currentData = tabelVlan.data().toArray();
            const isDuplicate = currentData.some(function(row) {
                return row.name && row.name === vlanName && row.id.toString() !== currentId;
            });

            if (isDuplicate) {
                nameInput.addClass('is-invalid');
                $('#edit_name_error').text('This VLAN Name already exists.').show();
            } else {
                nameInput.removeClass('is-invalid');
                $('#edit_name_error').text('').hide();
            }
        }
    });

    // Reset forms when modals are hidden
    $('#addVlanModal').on('hidden.bs.modal', function() {
        document.getElementById('addVlanForm').reset();
        $('#addVlanForm .error-message').text('');
        $('#addVlanForm .is-invalid').removeClass('is-invalid');
    });

    $('#editVlanModal').on('hidden.bs.modal', function() {
        $('#editVlanForm .error-message').text('');
        $('#editVlanForm .is-invalid').removeClass('is-invalid');
    });
});
</script>

<?= $this->endSection() ?>