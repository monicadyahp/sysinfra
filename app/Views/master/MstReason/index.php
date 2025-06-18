<?= $this->extend("main/template") ?>
<?= $this->section("content") ?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Master Reason</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReasonModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add Reason
        </button>
    </p>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelReason">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Reason Name</th>
                    <th>Last Update</th>
                    <th>Last User</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addReasonModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addReasonModalLabel">Add Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addReasonForm">
                        <div class="mb-3">
                            <label for="reasonName" class="form-label">Reason Name</label>
                            <input type="text" class="form-control" id="reasonName" name="reasonName" required>
                            <!-- Error message -->
                            <div id="reasonNameError" class="invalid-feedback" style="display:none;">Reason Name is required</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="saveReason">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editReasonModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReasonModalLabel">Edit Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editReasonForm">
                        <input type="hidden" id="edit_oldReasonName" name="oldReason">
                        <div class="mb-3">
                            <label for="edit_reasonName" class="form-label">Reason Name</label>
                            <input type="text" class="form-control" id="edit_reasonName" name="reasonName" required>
                            <div id="edit_reasonNameError" class="invalid-feedback" style="display:none;">Reason Name is required</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="updateReason">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let base_url = '<?= base_url() ?>';
            let reasonList = [];
            // let reasonList = json.map(item => item.td_reason.toLowerCase());
            
            // Initialize DataTable
            var table = $("#tabelReason").DataTable({
                pageLength: 10,
                order: [],
                autoWidth: false,
                ajax: {
                    url: base_url + "MstReason/getDataReason",
                    dataSrc: function(json) {
                        // Gunakan nama kolom td_reason sesuai database
                        reasonList = json.map(item => item.td_reason.toLowerCase());
                        return json;
                    },
                    beforeSend: function() {
                        let spinner = `
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                            </div>`;
                        $('#tabelReason tbody').html(spinner);
                    },
                },
                columns: [
                    { 
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center gap-2">   
                                    <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-reason="${row.td_reason}">
                                        <i class="fa fa-pen-to-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-reason="${row.td_reason}">
                                        <i class="fa fa-trash-can"></i>
                                    </a>
                                </div>
                            `;
                        },
                        className: "text-center"
                    },
                    { data: 'td_reason' },
                    { 
                        data: 'td_lastupdate',
                        render: function(data) {
                            if (data) {
                                const date = new Date(data);
                                return  date.getDate().toString().padStart(2, '0') + ' / ' + 
                                        (date.getMonth() + 1).toString().padStart(2, '0') + ' / ' + 
                                        date.getFullYear();
                            }
                            return '';
                        }
                    },
                    { data: 'td_lastuser' }
                ]
            });
            
            // Utility to check duplicate in client-side list
            function isDuplicate(reasonName, originalName = '') {
                const nameToCheck = reasonName.toLowerCase();
                if (originalName && originalName.toLowerCase() === nameToCheck) {
                    return false;
                }
                return reasonList.includes(nameToCheck);
            }

            // Handle Add Form Submission with Client-Side Validation
            $('#addReasonForm').on('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                $('.invalid-feedback').hide();
                $('.form-control').removeClass('is-invalid');
                
                let isValid = true;
                const reasonName = $('#reasonName').val().trim();
                
                if (reasonName === '') {
                    isValid = false;
                    $('#reasonName').addClass('is-invalid');
                    $('#reasonNameError').text('Reason Name is required').show();
                }
                
                if (!isValid) {
                    Swal.fire("Info!", "Semua Kolom Wajib Diisi", "info");
                    return;
                }
                
                if (isDuplicate(reasonName)) {
                    Swal.fire("Info!", "Reason already exists", "info");
                    return;
                }
                
                var formData = { reasonName: reasonName };
                
                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "MstReason/add",
                    type: 'POST',
                    data: formData,
                }).done(function(response) {
                    if (response.status) {
                        Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500 })
                            .then(() => { table.ajax.reload(); });
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }).fail(function(jqXHR) {
                    let message = JSON.parse(jqXHR.responseText);
                    Swal.fire("Error!", message.code + '<br>' + message.message, "error");
                }).always(function() {
                    $('#wait_screen').hide();
                });
                
                $('#addReasonModal').modal('hide');
                $('#addReasonForm')[0].reset();
            });
            
            // Handle Edit Button Click
            $('#tabelReason').on('click', '.edit-btn', function() {
                var reasonName = $(this).data('reason');
                $.ajax({
                    url: base_url + "MstReason/edit",
                    type: 'POST',
                    data: { reasonName: reasonName },
                    success: function(response) {
                        if (response.status) {
                            $('#edit_oldReasonName').val(response.data.td_reason);
                            $('#edit_reasonName').val(response.data.td_reason);
                            $('#editReasonModal').modal('show');
                        } else {
                            Swal.fire("Error!", response.message, "error");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire("Error!", "An error occurred on the server.", "error");
                    }
                });
            });
            
            // Handle Edit Form Submission with Duplicate Check and Validation
            $('#editReasonForm').on('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                $('.invalid-feedback').hide();
                $('.form-control').removeClass('is-invalid');
                
                const oldReason = $('#edit_oldReasonName').val();
                const newReason = $('#edit_reasonName').val().trim();
                
                let isValid = true;
                if (newReason === '') {
                    isValid = false;
                    $('#edit_reasonName').addClass('is-invalid');
                    $('#edit_reasonNameError').text('Reason Name is required').show();
                }
                
                if (!isValid) {
                    Swal.fire("Info!", "Semua Kolom Wajib Diisi", "info");
                    return;
                }
                
                if (isDuplicate(newReason, oldReason)) {
                    Swal.fire("Info!", "Reason already exists", "info");
                    return;
                }
                
                var formData = {
                    oldReason: oldReason,
                    reasonName: newReason
                };
                
                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "MstReason/update",
                    type: 'POST',
                    data: formData,
                }).done(function(response) {
                    if(response.status) {
                        Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500 })
                            .then(function() { table.ajax.reload(); });
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                    $('#wait_screen').hide();
                }).fail(function(jqXHR) {
                    let message = JSON.parse(jqXHR.responseText);
                    Swal.fire("Error!", message.code + '<br>' + message.message, "error");
                    $('#wait_screen').hide();
                });
                
                $('#editReasonModal').modal('hide');
                $('#editReasonForm')[0].reset();
            });
            
            // Handle Delete Button Click
            $('#tabelReason').on('click', '.delete-btn', function() {
                var reasonName = $(this).data('reason');
                
                Swal.fire({
                    title: "Are you sure?",
                    text: "This reason will be marked as deleted!",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: base_url + "MstReason/delete",
                            type: 'POST',
                            data: { reasonName: reasonName },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(function() { table.ajax.reload(); });
                                } else {
                                    Swal.fire("Error!", response.message, "error");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                                Swal.fire("Error!", "An error occurred on the server.", "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
<?= $this->endSection() ?>