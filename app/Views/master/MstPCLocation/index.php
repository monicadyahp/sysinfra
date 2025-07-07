<?= $this->extend("main/template") ?> 
<?= $this->section("content") ?> 

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Master PC Location</h4>
    </div>  
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">         
        <!-- Button trigger modal -->         
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">             
            <span class="btn-label">                 
                <i class="fa fa-plus"></i>             
            </span>             
            Add New Location         
        </button>     
    </p>     
    <div class="card-datatable table-responsive pt-0">         
        <table class="datatables-basic table table-bordered" id="tabelLocationList">             
            <thead class="table-light">                 
                <tr>                     
                    <th>Action</th>                     
                    <th>Location Name</th>                     
                </tr>             
            </thead>         
        </table>     
    </div>      

    <!-- Add Modal -->     
    <div class="modal fade" id="addLocationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLocationModalLabel" aria-hidden="true">         
        <div class="modal-dialog">             
            <div class="modal-content">                 
                <div class="modal-header">                     
                    <h5 class="modal-title" id="addLocationModalLabel">Add New Location</h5>                     
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>                 
                </div>                 
                <div class="modal-body">                     
                    <form id="addLocationForm">                         
                        <div class="mb-3">                             
                            <label for="locationName" class="form-label">Location Name</label>                             
                            <input type="text" class="form-control" id="locationName" name="locationName" placeholder="Type Location name">                         
                        </div>                         
                        <div class="modal-footer">                             
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>                             
                            <button type="submit" class="btn btn-primary" id="saveLocation">Submit Location</button>                         
                        </div>                     
                    </form>                 
                </div>             
            </div>         
        </div>     
    </div>      

    <!-- Edit Modal -->     
    <div class="modal fade" id="editLocationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editLocationModalLabel" aria-hidden="true">         
        <div class="modal-dialog">             
            <div class="modal-content">                 
                <div class="modal-header">                     
                    <h5 class="modal-title" id="editLocationModalLabel">Edit Location Data</h5>                     
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>                 
                </div>                 
                <div class="modal-body">                     
                    <form id="editLocationForm">                         
                        <input type="hidden" id="edit_oldLocationName" name="oldLocationName">                         
                        <div class="mb-3">                             
                            <label for="edit_locationName" class="form-label">Location Name</label>                             
                            <input type="text" class="form-control" id="edit_locationName" name="locationName">                         
                        </div>                         
                        <div class="modal-footer">                             
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>                             
                            <button type="submit" class="btn btn-primary" id="updateLocation">Update Location</button>                         
                        </div>                     
                    </form>                 
                </div>             
            </div>         
        </div>     
    </div>      

    <!-- Modal Delete Confirmation -->
    <div class="modal fade" id="deleteLocationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteLocationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to deactivate this Location? This action cannot be undone.</p>
                    <p><strong>Location Name:</strong> <span id="delete_locationName"></span></p>
                    <input type="hidden" id="delete_locationNameInput">
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
            let locationList = []; // Store all location names for duplicate checking
            
            // Initialize DataTable
            var table = $("#tabelLocationList").DataTable({
                pageLength: 10,
                order: [],
                autoWidth: false,
                ajax: {
                    url: base_url + "MstPCLocation/getData",
                    dataSrc: function(json) {
                        // Store all location names in lowercase for duplicate checking
                        locationList = json.map(item => item.mpl_name.toLowerCase());
                        return json;
                    },
                    beforeSend: function() {
                        let spinner = `
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                            </div>`;

                        $('#tabelLocationList tbody').html(spinner);
                    },
                },
                columns: [
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-location="${row.mpl_name}">
                                        <i class="fa fa-pen-to-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-location="${row.mpl_name}">
                                        <i class="fa fa-trash-can"></i>
                                    </a>
                                </div>
                            `;
                        },
                        className: "text-center"
                    },
                    { data: 'mpl_name' },
                ]
            });

            // Check for duplicate location names (case-insensitive)
            function isDuplicate(locationName, originalName = '') {
                const nameToCheck = locationName.toLowerCase().trim();
                
                // If editing, skip if same as original (case-insensitive)
                if (originalName && originalName.toLowerCase().trim() === nameToCheck) {
                    return false;
                }

                return locationList.includes(nameToCheck);
            }

            // Function to show delete confirmation
            function showDeleteConfirmation(locationName) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this Location!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteLocation(locationName);
                    }
                });
            }

            // Show loading animation during delete
            function deleteLocation(locationName) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Deactivating Location...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: base_url + "MstPCLocation/delete",
                    type: 'POST',
                    data: { locationName: locationName },
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
                            text: 'Failed to deactivate Location',
                            icon: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }

            // Handle Add Form Submission
            $('#addLocationForm').on('submit', function(e) {
                e.preventDefault();

                // Reset validation
                $('#locationName').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                const locationName = $('#locationName').val().trim();

                // Validate
                if (!locationName) {
                    $('#locationName').addClass('is-invalid');
                    $('#locationName').after('<div class="invalid-feedback">Location name is required</div>');
                    return;
                }

                if (isDuplicate(locationName)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Location',
                        text: 'This Location already exists.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                const formData = {
                    locationName: locationName
                };

                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "MstPCLocation/store",
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

                $('#addLocationModal').modal('hide');
                $('#addLocationForm')[0].reset();
            });

            // Handle Delete Button Click
            $('#tabelLocationList').on('click', '.delete-btn', function() {
                const locationName = $(this).data('location');
                showDeleteConfirmation(locationName);
            });

            // Handle Edit Button Click
            $('#tabelLocationList').on('click', '.edit-btn', function() {
                const locationName = $(this).data('location');
                
                $.ajax({
                    url: base_url + "MstPCLocation/getLocationById",
                    type: 'POST',
                    data: { locationName: locationName },
                    success: function(response) {
                        if (response.status) {
                            // Store original location name for comparison
                            $('#edit_oldLocationName').val(response.data.mpl_name);
                            // Fill the new name field
                            $('#edit_locationName').val(response.data.mpl_name);
                            // Show the modal
                            $('#editLocationModal').modal('show');
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
            $('#editLocationForm').on('submit', function(e) {
                e.preventDefault();

                // Reset validation
                $('#edit_locationName').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                const oldLocationName = $('#edit_oldLocationName').val();
                const newLocationName = $('#edit_locationName').val().trim();

                // Validate
                if (!newLocationName) {
                    $('#edit_locationName').addClass('is-invalid');
                    $('#edit_locationName').after('<div class="invalid-feedback">Location name cannot be empty</div>');
                    return;
                }

                if (newLocationName === oldLocationName) {
                    // Prevent editing if name hasn't changed
                    return;
                }

                if (isDuplicate(newLocationName, oldLocationName)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Location',
                        text: 'This Location name already exists. Please use a different name.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                const formData = {
                    oldLocationName: oldLocationName,
                    locationName: newLocationName
                };

                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "MstPCLocation/update",
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

                $('#editLocationModal').modal('hide');
                $('#editLocationForm')[0].reset();
            });
        });
    </script> 
</div> 
<?= $this->endSection() ?>
