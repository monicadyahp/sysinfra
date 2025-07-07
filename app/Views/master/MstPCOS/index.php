<?= $this->extend("main/template") ?> 
<?= $this->section("content") ?> 
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Master PC OS</h4>
    </div>  
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">         
        <!-- Button trigger modal -->         
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOSModal">             
            <span class="btn-label">                 
                <i class="fa fa-plus"></i>             
            </span>             
            Add New OS         
        </button>     
    </p>     
    <div class="card-datatable table-responsive pt-0">         
        <table class="datatables-basic table table-bordered" id="tabelOSList">             
            <thead class="table-light">                 
                <tr>                     
                    <th>Action</th>                     
                    <th>OS Name</th>                     
                </tr>             
            </thead>         
        </table>     
    </div>      

    <!-- Add Modal -->     
    <div class="modal fade" id="addOSModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addOSModalLabel" aria-hidden="true">         
        <div class="modal-dialog">             
            <div class="modal-content">                 
                <div class="modal-header">                     
                    <h5 class="modal-title" id="addOSModalLabel">Add New OS</h5>                     
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>                 
                </div>                 
                <div class="modal-body">                     
                    <form id="addOSForm">                         
                        <div class="mb-3">                             
                            <label for="osName" class="form-label">OS Name</label>                             
                            <input type="text" class="form-control" id="osName" name="osName" placeholder="Type OS name">                         
                        </div>                         
                        <div class="modal-footer">                             
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>                             
                            <button type="submit" class="btn btn-primary" id="saveOS">Submit OS</button>                         
                        </div>                     
                    </form>                 
                </div>             
            </div>         
        </div>     
    </div>      

    <!-- Edit Modal -->     
    <div class="modal fade" id="editOSModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editOSModalLabel" aria-hidden="true">         
        <div class="modal-dialog">             
            <div class="modal-content">                 
                <div class="modal-header">                     
                    <h5 class="modal-title" id="editOSModalLabel">Edit OS Data</h5>                     
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>                 
                </div>                 
                <div class="modal-body">                     
                    <form id="editOSForm">                         
                        <input type="hidden" id="edit_oldOSName" name="oldOSName">                         
                        <div class="mb-3">                             
                            <label for="edit_osName" class="form-label">OS Name</label>                             
                            <input type="text" class="form-control" id="edit_osName" name="osName">                         
                        </div>                         
                        <div class="modal-footer">                             
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>                             
                            <button type="submit" class="btn btn-primary" id="updateOS">Update OS</button>                         
                        </div>                     
                    </form>                 
                </div>             
            </div>         
        </div>     
    </div>      

    <!-- Modal Delete Confirmation -->
    <div class="modal fade" id="deleteOSModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteOSModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteOSModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to deactivate this OS? This action cannot be undone.</p>
                    <p><strong>OS Name:</strong> <span id="delete_osName"></span></p>
                    <input type="hidden" id="delete_osNameInput">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">Yes, Deactivate</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let base_url = '<?= base_url() ?>';
            let osList = []; // Store all OS names for duplicate checking
            let currentDeleteOS = null;
            
            // Initialize DataTable
            var table = $("#tabelOSList").DataTable({
                pageLength: 10,
                order: [],
                autoWidth: false,
                ajax: {
                    url: base_url + "MstPCOS/getData",
                    dataSrc: function(json) {
                        // Store all OS names in lowercase for duplicate checking
                        osList = json.map(item => item.mpo_osname.toLowerCase());
                        return json;
                    },
                    beforeSend: function() {
                        let spinner = `
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                            </div>`;

                        $('#tabelOSList tbody').html(spinner);
                    },
                },
                columns: [
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-os="${row.mpo_osname}">
                                        <i class="fa fa-pen-to-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-os="${row.mpo_osname}">
                                        <i class="fa fa-trash-can"></i>
                                    </a>
                                </div>
                            `;
                        },
                        className: "text-center"
                    },
                    { data: 'mpo_osname' },
                ]
            });

            // Check for duplicate OS names (case-insensitive)
            function isDuplicate(osName, originalName = '') {
                const nameToCheck = osName.toLowerCase().trim();
                
                // If editing, skip if same as original (case-insensitive)
                if (originalName && originalName.toLowerCase().trim() === nameToCheck) {
                    return false;
                }

                return osList.includes(nameToCheck);
            }

            // Function to show delete confirmation
            function showDeleteConfirmation(osName) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this OS!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteOS(osName);
                    }
                });
            }

            // Show loading animation during delete
            function deleteOS(osName) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Deactivating OS...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: base_url + "MstPCOS/delete",
                    type: 'POST',
                    data: { osName: osName },
                    success: function(response) {
                        Swal.close();
                        if (response.status) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to deactivate OS',
                            icon: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }

            // Handle Add Form Submission
            $('#addOSForm').on('submit', function(e) {
                e.preventDefault();

                // Reset validation
                $('#osName').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                const osName = $('#osName').val().trim();

                // Validate
                if (!osName) {
                    $('#osName').addClass('is-invalid');
                    $('#osName').after('<div class="invalid-feedback">OS name is required</div>');
                    return;
                }

                if (isDuplicate(osName)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate OS',
                        text: 'This OS already exists.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                const formData = {
                    osName: osName
                };

                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "MstPCOS/store",
                    type: 'POST',
                    data: formData,
                }).done(function(response) {
                    $('#wait_screen').hide();
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            showConfirmButton: false,
                            text: response.message,
                            timer: 1500
                        }).then(function() {
                            table.ajax.reload();
                        });
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }).fail(function() {
                    Swal.fire("Error!", "An unexpected error occurred", "error");
                    $('#wait_screen').hide();
                });

                $('#addOSModal').modal('hide');
                $('#addOSForm')[0].reset();
            });

            // Handle Delete Button Click
            $('#tabelOSList').on('click', '.delete-btn', function() {
                const osName = $(this).data('os');
                showDeleteConfirmation(osName);
            });

            // Handle Edit Button Click
            $('#tabelOSList').on('click', '.edit-btn', function() {
                const osName = $(this).data('os');
                
                $.ajax({
                    url: base_url + "MstPCOS/getOSById",
                    type: 'POST',
                    data: { osName: osName },
                    success: function(response) {
                        if (response.status) {
                            // Store original OS name for comparison
                            $('#edit_oldOSName').val(response.data.mpo_osname);
                            // Fill the new name field
                            $('#edit_osName').val(response.data.mpo_osname);
                            // Show the modal
                            $('#editOSModal').modal('show');
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

            // Handle Edit Form Submission
            $('#editOSForm').on('submit', function(e) {
                e.preventDefault();

                // Reset validation
                $('#edit_osName').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                const oldOSName = $('#edit_oldOSName').val();
                const newOSName = $('#edit_osName').val().trim();

                // Validate
                if (!newOSName) {
                    $('#edit_osName').addClass('is-invalid');
                    $('#edit_osName').after('<div class="invalid-feedback">OS name cannot be empty</div>');
                    return;
                }

                if (newOSName === oldOSName) {
                    // Prevent editing if name hasn't changed
                    return;
                }

                if (isDuplicate(newOSName, oldOSName)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate OS',
                        text: 'This OS name already exists. Please use a different name.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                const formData = {
                    oldOSName: oldOSName,
                    osName: newOSName
                };

                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "MstPCOS/update",
                    type: 'POST',
                    data: formData,
                }).done(function(response) {
                    $('#wait_screen').hide();
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            showConfirmButton: false,
                            text: response.message,
                            timer: 1500
                        }).then(function() {
                            table.ajax.reload();
                        });
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }).fail(function() {
                    Swal.fire("Error!", "An unexpected error occurred", "error");
                    $('#wait_screen').hide();
                });

                $('#editOSModal').modal('hide');
                $('#editOSForm')[0].reset();
            });
        });
    </script> 
</div> 
<?= $this->endSection() ?>
