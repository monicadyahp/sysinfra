<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<style>
    .status-unused { 
        color: #28a745; 
        font-weight: bold; 
    }
    
    .status-used { 
        color: #dc3545; 
        font-weight: bold; 
    }
    
    /* Switch Styles */
    .switch {
        position: relative;
        display: inline-block;
        width: 34px;
        height: 20px;
    }
    
    .switch input { 
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #28a745; /* Unused */
        transition: .4s;
        border-radius: 34px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background-color: #dc3545; /* Used */
    }
    
    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }
    
    input:checked + .slider:before {
        transform: translateX(14px);
    }

    /* Status filter styling */
    .status-filter-wrapper {
        display: inline-block;
        margin-left: 20px;
    }
    
    .status-filter-wrapper label {
        font-weight: normal;
        margin-bottom: 0;
    }
    
    #statusFilter {
        display: inline-block;
        width: auto;
        min-width: 120px;
    }
</style>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Master IP Address</h4>
    </div>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelIPAdd">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>ID</th>
                    <th>IP Address</th>
                    <th>VLAN ID</th>
                    <th>VLAN Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    const base_url = '<?= base_url() ?>';
    let table;
    let currentFilterStatus = 'Unused'; // Default filter status
    
    function initializeDataTable() {
        table = $('#tabelIPAdd').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            order: [[1, 'desc']], // Order by ID descending
            autoWidth: false,
            ajax: {
                url: base_url + 'MstIPAdd/getData',
                type: 'GET',
                data: function(d) {
                    d.status = currentFilterStatus; // Send filter status to server
                },
                dataSrc: function(json) {
                    return json;
                },
                beforeSend: function() {
                    let spinner = `
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                        </div>`;
                    $('#tabelIPAdd tbody').html(`<tr><td colspan="6">${spinner}</td></tr>`);
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                    $('#tabelIPAdd tbody').html('<tr><td colspan="6" class="text-center">Error loading data. Please try again.</td></tr>');
                }
            },
            columns: [
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data) {
                        const isChecked = (data.mip_status_text === 'Used') ? 'checked' : '';
                        return `
                            <label class="switch">
                                <input type="checkbox" class="status-toggle" 
                                    data-id="${data.mip_id}" 
                                    data-current-mip-status="${data.mip_status_raw}" 
                                    data-current-mip-lastuser="${data.mip_lastuser_raw}"
                                    ${isChecked}>
                                <span class="slider"></span>
                            </label>
                        `;
                    }
                },
                { data: 'mip_id' },
                { data: 'mip_ipadd' },
                { data: 'mip_vlanid', render: function(data) { return data ? data : '-'; } },
                { data: 'mip_vlanname', render: function(data) { return data ? data : '-'; } },
                { 
                    data: 'mip_status_text',
                    render: function(data) {
                        const statusClass = data === 'Used' ? 'status-used' : 'status-unused';
                        return `<span class="${statusClass}">${data}</span>`;
                    }
                }
            ],
            initComplete: function() {
                // Find the DataTables length control element
                const lengthControl = $('#tabelIPAdd_length');
                
                // Create the status filter dropdown HTML
                const statusFilterHtml = `
                    <div class="status-filter-wrapper" style="display: inline-block; margin-left: 20px;">
                        <label style="font-weight: normal;">
                            Status:
                            <select id="statusFilter" class="form-select form-select-sm" style="display: inline-block; width: auto; min-width: 120px;">
                                <option value="All">All</option>
                                <option value="Used">Used</option>
                                <option value="Unused" selected>Unused</option>
                            </select>
                        </label>
                    </div>
                `;
                
                // Insert the dropdown after the length control
                lengthControl.append(statusFilterHtml);
                
                // Set initial value and bind change event
                $('#statusFilter').val(currentFilterStatus).on('change', function() {
                    currentFilterStatus = $(this).val();
                    table.ajax.reload();
                });
            }
        });
    }
    
    initializeDataTable();
    
    // Handle status toggle change
    $('#tabelIPAdd').on('change', '.status-toggle', function() {
        const id = $(this).data('id');
        const currentStatus = $(this).data('current-mip-status');
        const currentUser = $(this).data('current-mip-lastuser');
        
        Swal.fire({
            title: 'Confirm Status Change',
            text: "Are you sure you want to change the status?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                $(this).prop('disabled', true);
                
                $.ajax({
                    url: base_url + 'MstIPAdd/toggleStatus',
                    method: 'POST',
                    data: {
                        id: id,
                        currentMipStatus: currentStatus,
                        currentMipLastUser: currentUser
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            table.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to update status'
                            });
                            table.ajax.reload(); // Revert the switch
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating status:', error);
                        let errorMessage = 'Could not update status';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                        table.ajax.reload();
                    },
                    complete: function() {
                        // Re-enable the toggle
                        $('.status-toggle').prop('disabled', false);
                    }
                });
            } else {
                // Revert switch if canceled
                table.ajax.reload();
            }
        });
    });
});
</script>

<?= $this->endSection() ?>