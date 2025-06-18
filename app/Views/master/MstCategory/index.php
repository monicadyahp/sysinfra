<?= $this->extend("main/template") ?>
<?= $this->section("content") ?>
<div class="card">
    <div class="card-header">
            <h4 class="card-title">Master Category</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add Category
        </button>
    </p>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelCategory">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Category Name</th>
                    <th>Last Update</th>
                    <th>Last User</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addCategoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" name="categoryName" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="saveCategory">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editCategoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" id="edit_oldCategoryName" name="oldCategoryName">
                        <div class="mb-3">
                            <label for="edit_categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="edit_categoryName" name="categoryName" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="updateCategory">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let base_url = '<?= base_url() ?>';
            let categoryList = []; // Store all categories for duplicate checking

            // Initialize DataTable
            var table = $("#tabelCategory").DataTable({
                pageLength: 10,
                order: [],
                autoWidth: false,
                ajax: {
                    url: base_url + "MstCategory/getDataCategory",
                    dataSrc: function(json) {
                        // Store category names for duplicate checking
                        categoryList = json.map(item => item.equipmentcat.toLowerCase());
                        return json;
                    },
                    beforeSend: function() {
                        let spinner = `
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                            </div>`;
                        $('#tabelCategory tbody').html(spinner);
                    },
                },
                columns: [
                    { 
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center gap-2">   
                                    <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-category="${row.equipmentcat}">
                                        <i class="fa fa-pen-to-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-category="${row.equipmentcat}">
                                        <i class="fa fa-trash-can"></i>
                                    </a>
                                </div>
                            `;
                        },
                        className: "text-center"
                    },
                    { data: 'equipmentcat' },
                    { 
                        data: 'ec_lastupdate',
                        render: function(data) {
                            if (data) {
                                const date = new Date(data);
                                // Format: DD / MM / YYYY
                                return  date.getDate().toString().padStart(2, '0') + ' / ' + 
                                       (date.getMonth() + 1).toString().padStart(2, '0') + ' / ' + 
                                       date.getFullYear();
                            }
                            return '';
                        }
                    },
                    { 
                        data: 'es_lastuser',
                        // Display the employee code as is without any special formatting
                        render: function(data) {
                            return data || '';
                        }
                    }
                ]
            });

            // Check for duplicate categories
            function isDuplicate(categoryName, originalName = '') {
                // If we're editing, exclude the original name from duplicate check
                const nameToCheck = categoryName.toLowerCase();
                if (originalName && originalName.toLowerCase() === nameToCheck) {
                    return false; // Not a duplicate if it's the same as original
                }
                return categoryList.includes(nameToCheck);
            }

            // Handle Add Form Submission
            $('#addCategoryForm').on('submit', function(e) {
                e.preventDefault();
                
                const categoryName = $('#categoryName').val();
                
                // Check for duplicates before submitting
                if (isDuplicate(categoryName)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Category',
                        text: 'This category name already exists. Please use a different name.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                
                var formData = {
                    categoryName: categoryName
                };
                
                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "MstCategory/add",
                    type: 'POST',
                    data: formData,
                }).done(function(response) {
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
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    let message;
                    try {
                        message = JSON.parse(jqXHR.responseText);
                        Swal.fire("Error!", message.code + '<br>' + message.message, "error");
                    } catch (e) {
                        Swal.fire("Error!", "An unexpected error occurred", "error");
                    }
                }).always(function() {
                    $('#wait_screen').hide();
                });
                
                $('#addCategoryModal').modal('hide');
                $('#addCategoryForm')[0].reset();
            });

            // Handle Edit Button Click
            $('#tabelCategory').on('click', '.edit-btn', function() {
                var categoryName = $(this).data('category');
                
                $.ajax({
                    url: base_url + "MstCategory/edit",
                    type: 'POST',
                    data: { categoryName: categoryName },
                    success: function(response) {
                        if (response.status) {
                            // Store original category name for update reference
                            $('#edit_oldCategoryName').val(response.data.equipmentcat);
                            $('#edit_categoryName').val(response.data.equipmentcat);
                            
                            $('#editCategoryModal').modal('show');
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
            $('#editCategoryForm').on('submit', function(e) {
                e.preventDefault();
                
                const oldCategoryName = $('#edit_oldCategoryName').val();
                const newCategoryName = $('#edit_categoryName').val();
                
                // Check for duplicates before submitting
                if (isDuplicate(newCategoryName, oldCategoryName)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Category',
                        text: 'This category name already exists. Please use a different name.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                
                var formData = {
                    oldCategoryName: oldCategoryName,
                    categoryName: newCategoryName
                };
                
                console.log('Sending update with data:', formData);
                
                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "MstCategory/update",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
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
                        $('#wait_screen').hide();
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.error("Update error:", textStatus, errorThrown);
                        let message;
                        try {
                            message = JSON.parse(xhr.responseText);
                            Swal.fire("Error!", message.code + '<br>' + message.message, "error");
                        } catch (e) {
                            Swal.fire("Error!", "An unexpected error occurred", "error");
                        }
                        $('#wait_screen').hide();
                    }
                });
                
                $('#editCategoryModal').modal('hide');
                $('#editCategoryForm')[0].reset();
            });

            // Handle Delete Button Click
            $('#tabelCategory').on('click', '.delete-btn', function() {
                var categoryName = $(this).data('category');
                
                Swal.fire({
                    title: "Are you sure?",
                    text: "This category will be marked as deleted!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: base_url + "MstCategory/delete",
                            type: 'POST',
                            data: { categoryName: categoryName },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(function() {
                                        table.ajax.reload();
                                    });
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
</div>
<?= $this->endSection() ?>