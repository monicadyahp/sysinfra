<?= $this->extend("main/template") ?>
<?= $this->section("content") ?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Master Printer Location</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">          
        <!-- Button trigger modal -->          
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPrinterLocationModal">          
            <span class="btn-label">            
                <i class="fa fa-plus"></i>          
            </span>            
            Add New Printer Location          
        </button>      
    </p>      
    <div class="card-datatable table-responsive pt-0">          
        <table class="datatables-basic table table-bordered" id="tabelPrinterLocationList">          
            <thead class="table-light">            
                <tr>              
                    <th>Action</th>              
                    <th>Location Name</th>              
                </tr>            
            </thead>          
        </table>      
    </div>      

    <!-- Add Modal -->      
    <div class="modal fade" id="addPrinterLocationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addPrinterLocationModalLabel" aria-hidden="true">          
        <div class="modal-dialog">            
            <div class="modal-content">              
                <div class="modal-header">                
                    <h5 class="modal-title" id="addPrinterLocationModalLabel">Add New Printer Location</h5>                
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>              
                </div>              
                <div class="modal-body">                
                    <form id="addPrinterLocationForm">                  
                        <div class="mb-3">                    
                            <label for="printerLocationName" class="form-label">Location Name</label>                    
                            <input type="text" class="form-control" id="printerLocationName" name="printerLocationName" placeholder="Type Printer Location name">                  
                        </div>                  
                        <div class="modal-footer">                    
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>                    
                            <button type="submit" class="btn btn-primary" id="savePrinterLocation">Submit Printer Location</button>                  
                        </div>                  
                    </form>              
                </div>              
            </div>          
        </div>      
    </div>      

    <!-- Edit Modal -->      
    <div class="modal fade" id="editPrinterLocationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editPrinterLocationModalLabel" aria-hidden="true">          
        <div class="modal-dialog">            
            <div class="modal-content">              
                <div class="modal-header">                
                    <h5 class="modal-title" id="editPrinterLocationModalLabel">Edit Printer Location Data</h5>                
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>              
                </div>              
                <div class="modal-body">                
                    <form id="editPrinterLocationForm">                  
                        <input type="hidden" id="edit_oldPrinterLocationName" name="oldPrinterLocationName">                  
                        <div class="mb-3">                    
                            <label for="edit_printerLocationName" class="form-label">Location Name</label>                    
                            <input type="text" class="form-control" id="edit_printerLocationName" name="printerLocationName">                  
                        </div>                  
                        <div class="modal-footer">                    
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>                    
                            <button type="submit" class="btn btn-primary" id="updatePrinterLocation">Update Printer Location</button>                  
                        </div>                  
                    </form>              
                </div>              
            </div>          
        </div>      
    </div>      

    <!-- Modal Delete Confirmation -->
    <div class="modal fade" id="deletePrinterLocationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deletePrinterLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePrinterLocationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to deactivate this Printer Location? This action cannot be undone.</p>
                    <p><strong>Location Name:</strong> <span id="delete_printerLocationName"></span></p>
                    <input type="hidden" id="delete_printerLocationNameInput">
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
            let printerLocationList = []; // Store all location names for duplicate checking
            
            // Initialize DataTable
            var table = $("#tabelPrinterLocationList").DataTable({
                pageLength: 10,
                order: [],
                autoWidth: false,
                ajax: {
                    url: base_url + "MstPrinterLocation/getData", // Ubah URL
                    dataSrc: function(json) {
                        // Store all location names in lowercase for duplicate checking
                        printerLocationList = json.map(item => item.mploc_name.toLowerCase()); // Ubah nama kolom
                        return json;
                    },
                    beforeSend: function() {
                        let spinner = `
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                            </div>`;

                        $('#tabelPrinterLocationList tbody').html(spinner);
                    },
                },
                columns: [
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-printerlocation="${row.mploc_name}">
                                        <i class="fa fa-pen-to-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-printerlocation="${row.mploc_name}">
                                        <i class="fa fa-trash-can"></i>
                                    </a>
                                </div>
                            `;
                        },
                        className: "text-center"
                    },
                    { data: 'mploc_name' }, // Ubah nama kolom
                ]
            });

            // Check for duplicate location names (case-insensitive)
            function isDuplicate(printerLocationName, originalName = '') { // Ubah nama parameter
                const nameToCheck = printerLocationName.toLowerCase().trim();
                
                // If editing, skip if same as original (case-insensitive)
                if (originalName && originalName.toLowerCase().trim() === nameToCheck) {
                    return false;
                }

                return printerLocationList.includes(nameToCheck);
            }

            // Function to show delete confirmation
            function showDeleteConfirmation(printerLocationName) { // Ubah nama parameter
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this Printer Location!", // Ubah pesan
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deletePrinterLocation(printerLocationName); // Ubah nama fungsi
                    }
                });
            }

            // Show loading animation during delete
            function deletePrinterLocation(printerLocationName) { // Ubah nama fungsi dan parameter
                Swal.fire({
                    title: 'Processing...',
                    text: 'Deactivating Printer Location...', // Ubah pesan
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: base_url + "MstPrinterLocation/delete", // Ubah URL
                    type: 'POST',
                    data: { printerLocationName: printerLocationName }, // Ubah nama parameter
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
                            text: 'Failed to deactivate Printer Location', // Ubah pesan
                            icon: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }

            // Handle Add Form Submission
            $('#addPrinterLocationForm').on('submit', function(e) { // Ubah ID form
                e.preventDefault();

                // Reset validation
                $('#printerLocationName').removeClass('is-invalid'); // Ubah ID input
                $('.invalid-feedback').remove();

                const printerLocationName = $('#printerLocationName').val().trim(); // Ubah ID input dan nama variabel

                // Validate
                if (!printerLocationName) {
                    $('#printerLocationName').addClass('is-invalid'); // Ubah ID input
                    $('#printerLocationName').after('<div class="invalid-feedback">Printer Location name is required</div>'); // Ubah ID input dan pesan
                    return;
                }

                if (isDuplicate(printerLocationName)) { // Ubah nama fungsi
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Printer Location', // Ubah pesan
                        text: 'This Printer Location already exists.', // Ubah pesan
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                const formData = {
                    printerLocationName: printerLocationName // Ubah nama parameter
                };

                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "MstPrinterLocation/store", // Ubah URL
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

                $('#addPrinterLocationModal').modal('hide'); // Ubah ID modal
                $('#addPrinterLocationForm')[0].reset(); // Ubah ID form
            });

            // Handle Delete Button Click
            $('#tabelPrinterLocationList').on('click', '.delete-btn', function() { // Ubah ID tabel
                const printerLocationName = $(this).data('printerlocation'); // Ubah data attribute
                showDeleteConfirmation(printerLocationName); // Ubah nama fungsi
            });

            // Handle Edit Button Click
            $('#tabelPrinterLocationList').on('click', '.edit-btn', function() { // Ubah ID tabel
                const printerLocationName = $(this).data('printerlocation'); // Ubah data attribute
                
                $.ajax({
                    url: base_url + "MstPrinterLocation/getPrinterLocationById", // Ubah URL
                    type: 'POST',
                    data: { printerLocationName: printerLocationName }, // Ubah nama parameter
                    success: function(response) {
                        if (response.status) {
                            // Store original location name for comparison
                            $('#edit_oldPrinterLocationName').val(response.data.mploc_name); // Ubah ID input dan nama kolom
                            // Fill the new name field
                            $('#edit_printerLocationName').val(response.data.mploc_name); // Ubah ID input dan nama kolom
                            // Show the modal
                            $('#editPrinterLocationModal').modal('show'); // Ubah ID modal
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
            $('#editPrinterLocationForm').on('submit', function(e) { // Ubah ID form
                e.preventDefault();

                // Reset validation
                $('#edit_printerLocationName').removeClass('is-invalid'); // Ubah ID input
                $('.invalid-feedback').remove();

                const oldPrinterLocationName = $('#edit_oldPrinterLocationName').val(); // Ubah ID input
                const newPrinterLocationName = $('#edit_printerLocationName').val().trim(); // Ubah ID input dan nama variabel

                // Validate
                if (!newPrinterLocationName) {
                    $('#edit_printerLocationName').addClass('is-invalid'); // Ubah ID input
                    $('#edit_printerLocationName').after('<div class="invalid-feedback">Printer Location name cannot be empty</div>'); // Ubah ID input dan pesan
                    return;
                }

                if (newPrinterLocationName === oldPrinterLocationName) {
                    // Prevent editing if name hasn't changed
                    return;
                }

                if (isDuplicate(newPrinterLocationName, oldPrinterLocationName)) { // Ubah nama fungsi
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Printer Location', // Ubah pesan
                        text: 'This Printer Location name already exists. Please use a different name.', // Ubah pesan
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                const formData = {
                    oldPrinterLocationName: oldPrinterLocationName, // Ubah nama parameter
                    printerLocationName: newPrinterLocationName // Ubah nama parameter
                };

                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "MstPrinterLocation/update", // Ubah URL
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

                $('#editPrinterLocationModal').modal('hide'); // Ubah ID modal
                $('#editPrinterLocationForm')[0].reset(); // Ubah ID form
            });
        });
    </script>
</div>
<?= $this->endSection() ?>
