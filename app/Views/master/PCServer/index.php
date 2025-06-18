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
    #add_hdd, #edit_hdd,
    #add_ram, #edit_ram,
    #add_vga, #edit_vga,
    #add_ethernet, #edit_ethernet,
    #add_remark, #edit_remark
    {
        text-transform: uppercase;
    }

    .card-datatable.table-responsive {
        overflow-x: auto;
        width: 100%;
    }

    /* Adjust column widths for better display in tables */
    #tabelPCServer th:nth-child(1), #tabelPCServer td:nth-child(1) { width: 5%; } /* No. */
    #tabelPCServer th:nth-child(2), #tabelPCServer td:nth-child(2) { width: 10%; } /* Action */
    #tabelPCServer th:nth-child(3), #tabelPCServer td:nth-child(3) { width: 5%; }  /* ID */
    #tabelPCServer th:nth-child(4), #tabelPCServer td:nth-child(4) { width: 10%; } /* Asset No */
    #tabelPCServer th:nth-child(5), #tabelPCServer td:nth-child(5) { width: 15%; } /* Asset Name */
    /* ... adjust other columns as needed ... */

    /* Modal dialog styling for wider modals */
    #addPCServerModal .modal-dialog,
    #editPCServerModal .modal-dialog {
        max-width: 90%; /* Use more width */
        width: auto !important;
    }
    #addPCServerModal .modal-content,
    #editPCServerModal .modal-content {
        height: auto;
        /* REMOVED: min-height: 80vh; */
        /* REMOVED: max-height: 95vh; */
        display: flex;
        flex-direction: column;
    }
    #addPCServerModal .modal-body,
    #editPCServerModal .modal-body {
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
</style>


<div class="card">
    <div class="card-header">
        <h4 class="card-title">PC Server Management</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addPCServerModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add PC Server
        </button>
        </p>

    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelPCServer">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%">No.</th> <th style="width: 15%">Action</th>
                    <th>ID</th>
                    <th>Asset No</th>
                    <th>Asset Name</th>
                    <th>Location</th>
                    <th>Receive Date</th>
                    <th>Age (Years)</th>
                    <th>HDD</th>
                    <th>RAM</th>
                    <th>VGA</th>
                    <th>Ethernet</th>
                    <th>Remark</th>
                    <th>Status</th>
                    <th>Last Update</th>
                    <th>Last User</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="modal fade" id="addPCServerModal" tabindex="-1" aria-labelledby="addPCServerModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPCServerModalLabel">Add PC Server</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addPCServerForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <input type="hidden" id="add_asset_no_sourced_from_finder" name="asset_no_sourced_from_finder" value="0">

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
                            <div class="col-md-4">
                                <label for="add_location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="add_location" name="location">
                            </div>
                            <div class="col-md-4">
                                <label for="add_receive_date" class="form-label">Receive Date</label>
                                <input type="date" class="form-control" id="add_receive_date" name="receive_date">
                                <div class="invalid-feedback" id="add_receive_date_error"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="add_age" class="form-label">Age (Years)</label>
                                <input type="text" class="form-control bg-light" id="add_age" name="age" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="add_hdd" class="form-label">HDD</label>
                                <input type="text" class="form-control" id="add_hdd" name="hdd">
                            </div>
                            <div class="col-md-4">
                                <label for="add_ram" class="form-label">RAM</label>
                                <input type="text" class="form-control" id="add_ram" name="ram">
                            </div>
                            <div class="col-md-4">
                                <label for="add_vga" class="form-label">VGA</label>
                                <input type="text" class="form-control" id="add_vga" name="vga">
                            </div>
                            <div class="col-md-4">
                                <label for="add_ethernet" class="form-label">Ethernet</label>
                                <input type="text" class="form-control" id="add_ethernet" name="ethernet">
                            </div>
                            <div class="col-md-8">
                                <label for="add_status" class="form-label">Status</label>
                                <select class="form-select" id="add_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="add_remark" class="form-label">Remark</label>
                                <textarea class="form-control" id="add_remark" name="remark" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="savePCServer">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPCServerModal" tabindex="-1" aria-labelledby="editPCServerModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPCServerModalLabel">Edit PC Server</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPCServerForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <input type="hidden" id="edit_id" name="id">
                            <input type="hidden" id="edit_asset_no_sourced_from_finder" name="asset_no_sourced_from_finder" value="0">

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
                            <div class="col-md-4">
                                <label for="edit_location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="edit_location" name="location">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_receive_date" class="form-label">Receive Date</label>
                                <input type="date" class="form-control" id="edit_receive_date" name="receive_date">
                                <div class="invalid-feedback" id="edit_receive_date_error"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_age" class="form-label">Age (Years)</label>
                                <input type="text" class="form-control bg-light" id="edit_age" name="age" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_hdd" class="form-label">HDD</label>
                                <input type="text" class="form-control" id="edit_hdd" name="hdd">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_ram" class="form-label">RAM</label>
                                <input type="text" class="form-control" id="edit_ram" name="ram">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_vga" class="form-label">VGA</label>
                                <input type="text" class="form-control" id="edit_vga" name="vga">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_ethernet" class="form-label">Ethernet</label>
                                <input type="text" class="form-control" id="edit_ethernet" name="ethernet">
                            </div>
                            <div class="col-md-8">
                                <label for="edit_status" class="form-label">Status</label>
                                <select class="form-select" id="edit_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="edit_remark" class="form-label">Remark</label>
                                <textarea class="form-control" id="edit_remark" name="remark" rows="1"></textarea>
                            </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="updatePCServer">Update</button>
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
                                <th>Receive Date</th> </tr>
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
    const base_url = '<?= base_url('MstPCServer') ?>';

    let equipmentTable;
    let currentCallingModal = '';
    let tabelPCServer; // Declare tabelPCServer globally

    // Auto-uppercase inputs
    $('#add_asset_no, #edit_asset_no,' +
        '#add_asset_name, #edit_asset_name,' +
        '#add_hdd, #edit_hdd,' +
        '#add_ram, #edit_ram,' +
        '#add_vga, #edit_vga,' +
        '#add_ethernet, #edit_ethernet,' +
        '#add_remark, #edit_remark')
        .on('blur keyup', function(){
            this.value = this.value.toUpperCase();
        });

    // --- Main PC Server DataTable Initialization ---
    // Function to initialize or re-initialize DataTable
    function initializePCServerDataTable(statusFilter = 'All') {
        if ($.fn.DataTable.isDataTable('#tabelPCServer')) {
            tabelPCServer.destroy();
        }

        tabelPCServer = $('#tabelPCServer').DataTable({
            scrollX: true,
            pageLength: 10,
            order: [[14, 'desc']], // Order by Last Update descending (index changed due to new "No." column)
            ajax: {
                url: base_url + "/getDataPCServer",
                data: function(d) {
                    d.status = statusFilter; // Pass the selected status filter to the server
                },
                dataSrc: "",
                error: function(xhr, status, error) {
                    console.error("Error fetching PC Server data:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load PC Server data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error),
                    });
                }
            },
            columns: [
                {
                    data: null, // Kolom "No."
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row, meta) {
                        return meta.row + 1; // Menghasilkan nomor urut
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex justify-content-center gap-2">
                                <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.id}" title="Edit PC Server">
                                    <i class="fa fa-pen-to-square"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.id}" title="Delete PC Server">
                                    <i class="fa fa-trash-can"></i>
                                </a>
                            </div>`;
                    }
                },
                { data: 'id' },
                { data: 'asset_no', render: d => d ? d.toUpperCase() : '' },
                { data: 'asset_name', render: d => d ? d.toUpperCase() : '' },
                { data: 'location' },
                {
                    data: 'receive_date',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }) : '';
                    }
                },
                { data: 'age' }, // Ini akan menampilkan nilai 'age' yang sudah dihitung server
                { data: 'hdd', render: d => d ? d.toUpperCase() : '' },
                { data: 'ram', render: d => d ? d.toUpperCase() : '' },
                { data: 'vga', render: d => d ? d.toUpperCase() : '' },
                { data: 'ethernet', render: d => d ? d.toUpperCase() : '' },
                { data: 'remark' },
                {
                    data: 'status',
                    render: function(data) {
                        return data == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
                    }
                },
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
                "sSearch": "Search:",
                "oPaginate": {
                    "sNext": '<i class="fa fa-angle-right"></i>',
                    "sPrevious": '<i class="fa fa-angle-left"></i>'
                }
            },
            dom: '<"top d-flex justify-content-between align-items-center"<"dataTables_length"l<"status-filter-wrapper ms-2"><"export-excel-button-wrapper ms-2">><"dataTables_filter"f>>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
            initComplete: function() {
                const api = this.api();
                const $statusFilter = `
                    <label for="statusFilter">Status:
                        <select id="statusFilter" class="form-select form-select-sm">
                            <option value="All">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </label>
                `;
                $('.status-filter-wrapper').append($statusFilter);

                // Add Export Excel Button
                const $exportButton = `
                    <button type="button" class="btn btn-success btn-sm" id="exportExcelBtn">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </button>
                `;
                $('.export-excel-button-wrapper').append($exportButton);

                $('#statusFilter').val(statusFilter);

                $('#statusFilter').on('change', function() {
                    const selectedStatus = $(this).val();
                    initializePCServerDataTable(selectedStatus);
                });

                // Export Excel button click handler
                $('#exportExcelBtn').on('click', function() {
                    window.location.href = base_url + '/exportExcel';
                });
            }
        });
    }

    // Initialize DataTable on page load with 'All' status
    initializePCServerDataTable('All');


    // --- Add PC Server Logic ---
    $('#addPCServerForm').on('submit', function(e) {
        e.preventDefault();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').text('').hide();

        $('#savePCServer').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: base_url + '/add',
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
                        $('#addPCServerModal').modal('hide');
                        tabelPCServer.ajax.reload();
                    });
                } else {
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
                        Swal.fire('Error', 'Gagal menambahkan PC Server. Mohon coba lagi.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText);
                let errorMessage = 'Request failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : error);
                Swal.fire('Error', errorMessage, 'error');
            },
            complete: function() {
                $('#savePCServer').prop('disabled', false).text('Save');
                checkFormInputs('#addPCServerForm', '#savePCServer');
            }
        });
    });

    // Calculate age on receive date change
    $('#add_receive_date').on('change', function() {
        const receiveDateStr = $(this).val();
        if (receiveDateStr) {
            const today = new Date();
            const rDate = new Date(receiveDateStr);

            let years = today.getFullYear() - rDate.getFullYear();
            let months = today.getMonth() - rDate.getMonth();
            let days = today.getDate() - rDate.getDate();

            if (days < 0) {
                months--;
                const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                days += prevMonth.getDate();
            }

            if (months < 0) {
                years--;
                months += 12;
            }

            const totalMonths = (years * 12) + months + (days / (new Date(rDate.getFullYear(), rDate.getMonth() + 1, 0).getDate()));
            const ageInYears = totalMonths / 12;

            $('#add_age').val(ageInYears.toFixed(1)); // Format to one decimal place
        } else {
            $('#add_age').val('');
        }
    });

    function checkAddFormInputs() {
        let filled = false;
        $('#addPCServerForm input:not([type="hidden"]), #addPCServerForm select, #addPCServerForm textarea').each(function() {
            if ($(this).val() && $(this).val().trim() !== '') {
                filled = true;
                return false;
            }
        });
        $('#savePCServer').prop('disabled', !filled);
    }

    function checkEditFormInputs() {
        let filled = false;
        $('#editPCServerForm input:not([type="hidden"]), #editPCServerForm select, #editPCServerForm textarea').each(function() {
            if ($(this).val() && $(this).val().trim() !== '') {
                filled = true;
                return false;
            }
        });
        $('#updatePCServer').prop('disabled', !filled);
    }

    function checkFormInputs(formId, submitBtnId) {
        let filled = false;
        $(`${formId} input:not([type="hidden"]):not([readonly]), ${formId} textarea:not([readonly]), ${formId} select:not([readonly])`).each(function() {
            if ($(this).val() && $(this).val().trim() !== '') {
                filled = true;
                return false;
            }
        });
        // Periksa juga input readonly jika memiliki nilai, agar tombol submit tidak disabled jika hanya field readonly yang terisi
        $(`${formId} input[readonly], ${formId} textarea[readonly], ${formId} select[readonly]`).each(function() {
            if ($(this).val() && $(this).val().trim() !== '') {
                filled = true;
                return false;
            }
        });

        $(`${submitBtnId}`).prop('disabled', !filled);
    }

    $('#addPCServerModal').on('shown.bs.modal', function() {
        checkFormInputs('#addPCServerForm', '#savePCServer');
    });

    $('#addPCServerForm input, #addPCServerForm select, #addPCServerForm textarea').on('keyup change', function() {
        checkFormInputs('#addPCServerForm', '#savePCServer');
    });

    $('#editPCServerModal').on('shown.bs.modal', function() {
        checkFormInputs('#editPCServerForm', '#updatePCServer');
    });

    $('#editPCServerForm input, #editPCServerForm select, #editPCServerForm textarea').on('keyup change', function() {
        checkFormInputs('#editPCServerForm', '#updatePCServer');
    });

    $('#addPCServerModal').on('hidden.bs.modal', function() {
        if ($('#equipmentSearchModal').hasClass('show')) return;
        $('#addPCServerForm')[0].reset();
        $('#addPCServerForm').find('.is-invalid').removeClass('is-invalid');
        $('#addPCServerForm').find('.invalid-feedback').text('').hide();
        // Pastikan untuk mereset readonly dan bg-light saat modal ditutup
        $('#add_asset_no, #add_asset_name, #add_receive_date').prop('readonly', false).removeClass('bg-light');
        $('#add_asset_no_sourced_from_finder').val('0'); // Reset hidden flag
        $('#add_age').val('');
        $('#savePCServer').prop('disabled', true).text('Save');
    });

    $('#tabelPCServer').on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.ajax({
            url: base_url + '/edit',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.status) {
                    const d = response.data;
                    $('#edit_id').val(d.id);
                    $('#edit_asset_no').val(d.asset_no);
                    $('#edit_asset_name').val(d.asset_name);
                    $('#edit_location').val(d.location);
                    $('#edit_receive_date').val(d.receive_date);
                    $('#edit_age').val(d.age); // Age is already calculated on the server
                    $('#edit_hdd').val(d.hdd);
                    $('#edit_ram').val(d.ram);
                    $('#edit_vga').val(d.vga);
                    $('#edit_ethernet').val(d.ethernet);
                    $('#edit_remark').val(d.remark);
                    $('#edit_status').val(d.status);

                    // --- START: MODIFIKASI READONLY DI MODAL EDIT ---
                    // Gunakan flag asset_no_sourced_from_finder dari response
                    if (d.asset_no_sourced_from_finder == 1) {
                        $('#edit_asset_no').prop('readonly', true).addClass('bg-light');
                        $('#edit_asset_name').prop('readonly', true).addClass('bg-light');
                        $('#edit_receive_date').prop('readonly', true).addClass('bg-light');
                        $('#edit_asset_no_sourced_from_finder').val('1'); // Pastikan hidden field juga di-set
                    } else {
                        $('#edit_asset_no').prop('readonly', false).removeClass('bg-light');
                        $('#edit_asset_name').prop('readonly', false).removeClass('bg-light');
                        $('#edit_receive_date').prop('readonly', false).removeClass('bg-light');
                        $('#edit_asset_no_sourced_from_finder').val('0'); // Pastikan hidden field juga di-reset
                    }
                    // --- END: MODIFIKASI READONLY DI MODAL EDIT ---

                    $('#editPCServerForm').find('.is-invalid').removeClass('is-invalid');
                    $('#editPCServerForm').find('.invalid-feedback').text('').hide();

                    checkFormInputs('#editPCServerForm', '#updatePCServer');

                    $('#editPCServerModal').modal('show');
                } else {
                    Swal.fire('Error', response.message || 'Data not found', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });

    $('#editPCServerForm').on('submit', function(e) {
        e.preventDefault();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').text('').hide();

        $('#updatePCServer').prop('disabled', true).text('Memperbarui...');


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
                        $('#editPCServerModal').modal('hide');
                        tabelPCServer.ajax.reload();
                    });
                } else {
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
                        Swal.fire('Error', 'Gagal memperbarui PC Server. Mohon coba lagi.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText);
                let errorMessage = 'Request failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : error);
                Swal.fire('Error', errorMessage, 'error');
            },
            complete: function() {
                $('#updatePCServer').prop('disabled', false).text('Update');
                checkFormInputs('#editPCServerForm', '#updatePCServer');
            }
        });
    });

    // Calculate age on receive date change for edit modal
    $('#edit_receive_date').on('change', function() {
        const receiveDateStr = $(this).val();
        if (receiveDateStr) {
            const today = new Date();
            const rDate = new Date(receiveDateStr);

            let years = today.getFullYear() - rDate.getFullYear();
            let months = today.getMonth() - rDate.getMonth();
            let days = today.getDate() - rDate.getDate();

            if (days < 0) {
                months--;
                const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0); // Last day of previous month
                days += prevMonth.getDate();
            }

            if (months < 0) {
                years--;
                months += 12;
            }

            const totalMonths = (years * 12) + months + (days / (new Date(rDate.getFullYear(), rDate.getMonth() + 1, 0).getDate()));
            const ageInYears = totalMonths / 12;

            $('#edit_age').val(ageInYears.toFixed(1)); // Format to one decimal place
        } else {
            $('#edit_age').val('');
        }
    });

    $('#editPCServerModal').on('hidden.bs.modal', function() {
        if ($('#equipmentSearchModal').hasClass('show')) return;
        $('#editPCServerForm')[0].reset();
        $('#editPCServerForm').find('.is-invalid').removeClass('is-invalid');
        $('#editPCServerForm').find('.invalid-feedback').text('').hide();
        // Pastikan untuk mereset readonly dan bg-light saat modal ditutup
        $('#edit_asset_no, #edit_asset_name, #edit_receive_date').prop('readonly', false).removeClass('bg-light');
        $('#edit_asset_no_sourced_from_finder').val('0'); // Reset hidden flag
        $('#edit_age').val('');
        $('#updatePCServer').prop('disabled', true).text('Update');
    });


    // --- Delete PC Server Logic ---
    $('#tabelPCServer').on('click', '.delete-btn', function() {
        const id = $(this).data('id');
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
                    url: base_url + '/delete',
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
                            tabelPCServer.ajax.reload();
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

    // --- Asset No Finder Logic ---
    $('.search-equipment-btn').on('click', function() {
        currentCallingModal = $(this).closest('.modal').attr('id');
        if (currentCallingModal === 'addPCServerModal') {
            $('#addPCServerModal').modal('hide');
        } else if (currentCallingModal === 'editPCServerModal') {
            $('#editPCServerModal').modal('hide');
        }

        if ($.fn.DataTable.isDataTable('#equipmentTable')) {
            equipmentTable.destroy();
        }
        equipmentTable = $('#equipmentTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '<?= base_url('MstPCServer/getEquipmentData') ?>',
                dataSrc: function(json) {
                    return json || [];
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching Equipment data:', error);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load Equipment data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error) });
                    return [];
                }
            },
            columns: [
                { data: 'e_assetno', render: d => d ? d.toUpperCase() : '' },
                { data: 'e_equipmentname', render: d => d ? d.toUpperCase() : '' },
                { data: 'e_equipmentid' },
                { data: 'e_serialnumber', render: d => d ? d.toUpperCase() : '' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return (row.e_brand || '') + ' / ' + (row.e_model || '');
                    }
                },
                { // Kolom baru: Receive Date
                    data: 'e_receivedate',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }) : '';
                    }
                }
            ],
            language: {
                "sLengthMenu": "Show _MENU_ entries",
                "sSearch": "Search:"
            },
            paging: true,
            lengthChange: true,
            searching: true
        });

        setTimeout(() => {
            $('#equipmentSearchModal').modal('show');
        }, 200);
    });

    $('#equipmentTable tbody').on('click', 'tr', function() {
        if (!equipmentTable) return;
        const data = equipmentTable.row(this).data();
        if (!data) return;

        // Populate fields and set readonly based on the calling modal
        if (currentCallingModal === 'addPCServerModal') {
            $('#add_asset_no').val(data.e_assetno).prop('readonly', true).addClass('bg-light');
            $('#add_asset_name').val(data.e_equipmentname).prop('readonly', true).addClass('bg-light');
            // Pastikan format tanggal sesuai dengan input type="date" (YYYY-MM-DD)
            $('#add_receive_date').val(data.e_receivedate ? data.e_receivedate.split(' ')[0] : '').prop('readonly', true).addClass('bg-light');

            // Recalculate age for the Add modal
            const receiveDateStr = $('#add_receive_date').val();
            if (receiveDateStr) {
                const today = new Date();
                const rDate = new Date(receiveDateStr);

                let years = today.getFullYear() - rDate.getFullYear();
                let months = today.getMonth() - rDate.getMonth();
                let days = today.getDate() - rDate.getDate();

                if (days < 0) {
                    months--;
                    const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                    days += prevMonth.getDate();
                }

                if (months < 0) {
                    years--;
                    months += 12;
                }

                const totalMonths = (years * 12) + months + (days / (new Date(rDate.getFullYear(), rDate.getMonth() + 1, 0).getDate()));
                const ageInYears = totalMonths / 12;
                $('#add_age').val(ageInYears.toFixed(1));
            } else {
                $('#add_age').val('');
            }
            $('#add_asset_no_sourced_from_finder').val('1'); // Set flag
            $('#add_asset_no').removeClass('is-invalid');
            $('#add_asset_no_error').text('').hide();

        } else if (currentCallingModal === 'editPCServerModal') {
            $('#edit_asset_no').val(data.e_assetno).prop('readonly', true).addClass('bg-light');
            $('#edit_asset_name').val(data.e_equipmentname).prop('readonly', true).addClass('bg-light');
            // Pastikan format tanggal sesuai dengan input type="date" (YYYY-MM-DD)
            $('#edit_receive_date').val(data.e_receivedate ? data.e_receivedate.split(' ')[0] : '').prop('readonly', true).addClass('bg-light');

            // Recalculate age for the Edit modal
            const receiveDateStr = $('#edit_receive_date').val();
            if (receiveDateStr) {
                const today = new Date();
                const rDate = new Date(receiveDateStr);

                let years = today.getFullYear() - rDate.getFullYear();
                let months = today.getMonth() - rDate.getMonth();
                let days = today.getDate() - rDate.getDate();

                if (days < 0) {
                    months--;
                    const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                    days += prevMonth.getDate();
                }

                if (months < 0) {
                    years--;
                    months += 12;
                }

                const totalMonths = (years * 12) + months + (days / (new Date(rDate.getFullYear(), rDate.getMonth() + 1, 0).getDate()));
                const ageInYears = totalMonths / 12;
                $('#edit_age').val(ageInYears.toFixed(1));
            } else {
                $('#edit_age').val('');
            }
            $('#edit_asset_no_sourced_from_finder').val('1'); // Set flag
            $('#edit_asset_no').removeClass('is-invalid');
            $('#edit_asset_no_error').text('').hide();
        }

        $('#equipmentSearchModal').modal('hide');
    });

    $('#equipmentSearchModal').on('hidden.bs.modal', function() {
        if (currentCallingModal === 'addPCServerModal') {
            $('#addPCServerModal').modal('show');
        } else if (currentCallingModal === 'editPCServerModal') {
            $('#editPCServerModal').modal('show');
        }
        currentCallingModal = '';
    });
});
</script>
<?= $this->endSection() ?>