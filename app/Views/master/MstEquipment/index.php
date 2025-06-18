<?= $this->extend("main/template") ?>
<?= $this->section("content") ?>
<head>
    <style>
        input[readonly], textarea[readonly] {
            background-color: #e0e0e0; /* Light gray color */
            cursor: not-allowed; /* Optional: change the cursor to indicate that the field is not editable */
        }
        .is-invalid {
            border: 1px solid red;  /* Red border for empty fields */
        }
        .asset-number-container {
            display: flex;
            gap: 10px;
        }
    </style>
</head>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Master Equipment</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDetailDetModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add Master
        </button>
    </p>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelEquipment">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>ID</th>
                    <th>No Asset</th>
                    <th>Equipment Name</th>
                    <th>Kind</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Serial Number</th>
                    <th>Receive Date</th>
                    <th>Status</th>
                    <th>Last Update</th>
                    <th>Last User</th>
                    <th>Dispose Date</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addDetailDetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addDetailDetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDetailDetModalLabel">Add Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addDataForm">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom: 20px;">
                            <div class="col">
                                <label for="assetNo" class="form-label">Asset Number</label>
                                <div class="asset-number-container">
                                    <input type="number" class="form-control" id="assetNo" name="assetNo">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#assetNoModal">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div id="assetNoError" class="invalid-feedback" style="display: none;">Nomor Asset is required</div>
                            </div>
                                <div class="col">
                                    <label for="equipmentId" class="form-label">Id Equipment</label>
                                    <input type="number" class="form-control" id="equipmentId" name="equipmentId">
                                    <div id="equipmentIdError" class="invalid-feedback" style="display: none;">Equipment ID is required</div>
                                </div>
                                <div class="col">
                                    <label for="kind" class="form-label">Category</label>
                                    <select class="form-select" id="kind">
                                        <option value="">-- Select Category --</option>
                                        <?php foreach ($cat as $mst): ?>
                                            <option value="<?= $mst->equipmentcat; ?>">
                                                <?= $mst->equipmentcat; ?>
                               <?php endforeach; ?>
                                    </select>
                                    <div id="kindError" class="invalid-feedback" style="display: none;">Kategori is required</div>
                                </div>
                                <div class="col">
                                    <label for="brand" class="form-label">Brand</label>
                                    <input type="text" class="form-control" id="brand" name="brand">
                                    <div id="brandError" class="invalid-feedback" style="display: none;">Brand is required</div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col">
                                    <label for="serial_number" class="form-label">Serial Number</label>
                                    <input type="text" class="form-control" id="serial_number" name="serial_number">
                                    <div id="serial_numberError" class="invalid-feedback" style="display: none;">Serial Number is required</div>
                                </div>
                                <div class="col">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status">
                                        <option value=""></option>
                                                               </option>
                              <option value="Active">Active</option>
                                        <option value="Dispose">Dispose</option>
                                        <option value="Broken">Broken</option>
                                        <option value="Unused">Unused</option>
                                        <option value="Waiting Dispose">Waiting Dispose</option>
                                    </select>
                                    <div id="statusError" class="invalid-feedback" style="display: none;">Status is required</div>
                                </div>
                                <div class="col">
                                    <label for="receiveDate" class="form-label">Receive Date</label>
                                    <input type="date" class="form-control" id="receiveDate" name="receiveDate">
                                    <div id="receiveDateError" class="invalid-feedback" style="display: none;">Receive Date is required</div>
                                </div>
                                <div class="col">
                                    <label for="disposeDate" class="form-label">Dispose Date</label>
                                    <input type="date" class="form-control" id="disposeDate" name="disposeDate">
                                    <div id="disposeDateError" class="invalid-feedback" style="display: none;">Dispose Date is required</div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col" >
                                    <label for="model" class="form-label">Model</label>
                                    <input type="textarea" class="form-control" id="model" name="model" style="width: 100%">
                                    <div id="modelError" class="invalid-feedback" style="display: none;">Model is required</div>
                                </div>
                                <div class="col" >
                                    <label for="equipmentName" class="form-label">Equipment Name</label>
                                    <input type="textarea" class="form-control" id="equipmentName" name="equipmentName" style="width: 100%">
                                    <div id="equipmentNameError" class="invalid-feedback" style="display: none;">Equipment Name is required</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="cancelButton" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="save">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editDetailDetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editDetailDetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDetailDetModalLabel">Edit Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editDataForm">
                        <input type="hidden" id="edit_e_id" name="e_id">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom: 20px;">
                            <div class="col">
                                <label for="edit_assetNo" class="form-label">Nomor Asset</label>
                                <div class="asset-number-container">
                                    <input type="number" class="form-control" id="edit_assetNo" name="edit_assetNo">
                                    <button type="button" class="btn btn-outline-secondary" id="editAssetNoSearchBtn" data-bs-toggle="modal" data-bs-target="#assetNoModal">
                                        <i class="fa fa-search"></i>
                                    </button>

                                </div>
                                <div id="edit_assetNoError" class="invalid-feedback" style="display: none;">Nomor Asset is required</div>
                            </div>
                                <div class="col">
                                    <label for="edit_equipmentId" class="form-label">Id Equipment</label>
                                    <input type="number" class="form-control" id="edit_equipmentId" name="edit_equipmentId">
                                    <div id="edit_equipmentIdError" class="invalid-feedback" style="display: none;">Equipment ID is required</div>
                                </div>
                                <div class="col">
                                    <label for="edit_kind" class="form-label">Category</label>
                                    <select class="form-select" id="edit_kind">
                                        <?php foreach ($cat as $mst): ?>
                                            <option value="<?= $mst->equipmentcat; ?>">
                                                <?= $mst->equipmentcat; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div id="edit_kindError" class="invalid-feedback" style="display: none;">Category is required</div>
                                </div>
                                <div class="col">
                                    <label for="edit_brand" class="form-label">Brand</label>
                                    <input type="text" class="form-control" id="edit_brand" name="edit_brand">
                                    <div id="edit_brandError" class="invalid-feedback" style="display: none;">Brand is required</div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col">
                                    <label for="edit_serial_number" class="form-label">Serial Number</label>
                                    <input type="text" class="form-control" id="edit_serial_number" name="edit_serial_number">
                                    <div id="edit_serial_numberError" class="invalid-feedback" style="display: none;">Serial Number is required</div>
                                </div>
                                <div class="col">
                                    <label for="edit_status" class="form-label">Status</label>
                                    <select class="form-select" id="edit_status">
                                        <option value=""></option>
                                        <option value="Active">Active</option>
                                        <option value="Dispose">Dispose</option>
                                        <option value="Broken">Broken</option>
                                        <option value="Unused">Unused</option>
                                        <option value="Waiting Dispose">Waiting Dispose</option>
                                    </select>
                                    <div id="edit_statusError" class="invalid-feedback" style="display: none;">Status is required</div>
                                </div>
                                <div class="col">
                                    <label for="edit_receiveDate" class="form-label">Receive Date</label>
                                    <input type="date" class="form-control" id="edit_receiveDate" name="edit_receiveDate">
                                    <div id="edit_receiveDateError" class="invalid-feedback" style="display: none;">Receive Date is required</div>
                                </div>
                                <div class="col">
                                    <label for="edit_disposeDate" class="form-label">Dispose Date</label>
                                    <input type="date" class="form-control" id="edit_disposeDate" name="edit_disposeDate">
                                    <div id="edit_disposeDateError" class="invalid-feedback" style="display: none;">
                                        (opsional) format tanggal tidak valid
                                        </div>
                                        <div id="edit_equipment_nameError" class="invalid-feedback" style="display: none;">
                                        (opsional) panjang maksimal 50 karakter
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col">
                                    <label for="edit_model" class="form-label">Model</label>
                                    <input type="textarea" class="form-control" id="edit_model" name="edit_model" style="width: 100%">
                                    <div id="edit_modelError" class="invalid-feedback" style="display: none;">Model is required</div>
                                </div>
                                <div class="col">
                                    <label for="edit_equipment_name" class="form-label">Equipment Name</label>
                                    <input type="textarea" class="form-control" id="edit_equipment_name" name="edit_equipment_name" style="width: 100%">
                                    <div id="edit_equipment_nameError" class="invalid-feedback" style="display: none;">Equipment Name is required</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="update">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset No Modal -->
    <div class="modal fade" id="assetNoModal" tabindex="-1" aria-labelledby="assetNoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assetNoModalLabel">Asset Number Data</h5>
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
                            <th style="width: 15%;">Asset No</th>
                            <th style="width: 15%;">Equipment ID</th>
                            <th style="width: 20%;">Serial Number</th>
                            <th style="width: 20%;">Receive Date</th>
                            <th style="width: 30%;">Model</th>
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
            // 09-04 monic
            const today = new Date().toISOString().split('T')[0]; // Get current date in YYYY-MM-DD format
            $('#disposeDate').attr('min', today).val(today);  // Set min and default value for Dispose Date (Add modal) // Set the min attribute for disposeDate input (Add modal)
            $('#edit_disposeDate').attr('min', today);  // Set the min attribute for edit form disposeDate input

            // Other existing code remains unchanged
            let base_url = '<?= base_url() ?>';

            //17-04
            // Fungsi untuk load asset numbers dan populate dropdown menu
            function loadAssetNumbersDropdown() {
                $.ajax({
                    url: base_url + "MstEquipment/getAssetNumbers",
                    type: "GET",
                    dataType: 'json',
                    success: function (assets) {
                        let dropdownMenu = $("#assetNoDropdownMenu");
                        dropdownMenu.empty(); // Bersihkan menu terlebih dahulu
                        $.each(assets, function (index, asset) {
                            // Pastikan asset_no tidak null atau kosong
                            if (asset.asset_no != null && asset.asset_no !== "") {
                                dropdownMenu.append(
                                    `<li><a class="dropdown-item asset-no-item" href="#">${asset.asset_no}</a></li>`
                                );
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading asset numbers:', error);
                    }
                });
            }

            // 17-04 Panggil fungsi loadAssetNumbersDropdown saat document sudah siap
                loadAssetNumbersDropdown();

            // 17-04 Event handler: ketika salah satu item dropdown diklik, update field Nomor Asset
            $(document).on('click', '.asset-no-item', function (e) {
                e.preventDefault();
                let selectedAssetNo = $(this).text();
                $("#assetNo").val(selectedAssetNo).trigger('blur');
            });

            //17-04
            // Fungsi untuk load asset numbers dan populate dropdown untuk modal Edit Data
            function loadEditAssetNumbersDropdown() {
                $.ajax({
                    url: base_url + "MstEquipment/getAssetNumbers",
                    type: "GET",
                    dataType: 'json',
                    success: function (assets) {
                        let dropdownMenu = $("#editAssetNoDropdownMenu");
                        dropdownMenu.empty(); // Bersihkan isi dropdown
                        $.each(assets, function (index, asset) {
                            if (asset.asset_no != null && asset.asset_no !== "") {
                                dropdownMenu.append(
                                    `<li><a class="dropdown-item edit-asset-no-item" href="#">${asset.asset_no}</a></li>`
                                );
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading asset numbers for edit:', error);
                    }
                });
            }

            // 17-04 Panggil fungsi load dropdown untuk modal Edit Data ketika document sudah siap
            loadEditAssetNumbersDropdown();

            // 17-04 Event handler: ketika salah satu item dropdown (modal Edit) diklik, update input edit_assetNo
            $(document).on('click', '.edit-asset-no-item', function (e) {
                e.preventDefault();
                let selectedAssetNo = $(this).text();
                $("#edit_assetNo").val(selectedAssetNo).trigger('blur');
            });

            //  17-04 ===============================================
            // Contoh bagian kode untuk memanggil modal Edit Data
            // (Pastikan ini berada pada callback AJAX ketika klik tombol edit)
            $('#tabelEquipment').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: base_url + "MstEquipment/edit",
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response.status) {
                            var isReadonly = response.isReadonly; // Ambil status readonly (true jika data berasal dari acceptance)

                            // Set field pada modal Edit Data
                            $('#edit_e_id').val(response.data.e_id).prop('readonly', true);
                            $('#edit_assetNo')
                                .val(response.data.e_assetno)
                                .prop('readonly', isReadonly)
                                .css('background-color', isReadonly ? '#e0e0e0' : '#ffffff');

                            // Tambahkan ini tepat setelah Anda populate field edit modal 
                            $('#editAssetNoSearchBtn').prop('disabled', false);

                            $('#edit_equipmentId').val(response.data.e_equipmentid).prop('readonly', isReadonly);
                            $('#edit_kind').val(response.data.e_kind).prop('readonly', false);
                            $('#edit_brand').val(response.data.e_brand).prop('readonly', false);
                            $('#edit_serial_number').val(response.data.e_serialnumber).prop('readonly', isReadonly);
                            $('#edit_status').val(response.data.e_status).prop('readonly', false);
                            $('#edit_disposeDate').val(response.data.e_disposedate).prop('readonly', false);
                            $('#edit_model').val(response.data.e_model).prop('readonly', isReadonly);
                            $('#edit_equipment_name').val(response.data.e_equipmentname).prop('readonly', false);

                            if (response.data.e_receivedate) {
                                let formattedDate = formatDate(response.data.e_receivedate);
                                $('#edit_receiveDate')
                                    .val(formattedDate)
                                    .prop('readonly', isReadonly);
                                // Update min untuk Dispose Date berdasarkan Receive Date
                                $('#edit_disposeDate').attr('min', formattedDate);
                            }

                            // Tampilkan modal Edit Data
                            $('#editDetailDetModal').modal('show');
                        } else {
                            swal("Info!", response.message, {
                                icon: "info",
                                buttons: {
                                    confirm: { className: "btn btn-danger" },
                                },
                            });
                        }
                    },
                    error: function() {
                        swal("Error!", "Terjadi kesalahan pada server.", {
                            icon: "error",
                            buttons: {
                                confirm: { className: "btn btn-danger" },
                            },
                        });
                    }
                });

            });
            //  17-04 ===============================================
            
            // Helper: Fungsi untuk mengonversi format tanggal (YYYY-MM-DD)
            function formatDate(dateString) {
                let parts = dateString.split('/');
                if (parts.length === 3) {
                    let month = parts[0].padStart(2, '0');
                    let day = parts[1].padStart(2, '0');
                    let year = parts[2];
                    return `${year}-${month}-${day}`;
                }
                return dateString;
            }
            
            var table = $("#tabelEquipment").DataTable({
                pageLength: 10,
                order: [[10, 'desc']],
                autoWidth: false,
                ajax: {
                    url: base_url + "MstEquipment/getDataMfg",
                    dataSrc: "",
                    beforeSend: function() {
                        let spinner = 
                            `<div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                            </div>`;
                        $('#tabelEquipment tbody').html(spinner);
                    },
                },
                columns: [
                    { 
                        data: null,
                        className: 'text-center', 
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center gap-2">   
                                    <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.e_id}">
                                        <i class="fa fa-pen-to-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.e_id}">
                                        <i class="fa fa-trash-can"></i>
                                    </a>
                                </div>
                            `;
                        },
                        className: "text-center"
                    },
                    { data: 'e_equipmentid' }, 
                    { data: 'e_assetno' }, 
                    { data: 'e_equipmentname' }, 
                    { data: 'e_kind' }, 
                    { data: 'e_brand' }, 
                    { data: 'e_model' }, 
                    { data: 'e_serialnumber' }, 
                    { data: 'e_receivedate' }, 
                    { data: 'e_status' }, 
                    { data: 'e_lastupdate' }, 
                    { data: 'e_lastuser' }, 
                    { data: 'e_disposedate' }
                ],
                initComplete: function () {
                    this.api()
                        .columns()
                        .every(function () {
                            var column = this;
                            var select = $(
                                '<select class="form-select"><option value=""></option></select>'
                            )
                                .appendTo($(column.footer()).empty())
                                .on("change", function () {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column.search(val ? "^" + val + "$" : "", true, false).draw();
                                });

                            column.data().unique().sort().each(function (d) {
                                select.append('<option value="' + d + '">' + d + '</option>');
                            });
                        });
                },
            });

            function populateAddDataFields(data) {
                // Set Asset No.
                $('#assetNo')
                .val(data.asset_no)
                .prop('readonly', true)
                .css('background-color', '#d0d0d0');
                // Set Equipment ID dan Serial Number
                $('#equipmentId')
                .val(data.equipment_id)
                .prop('readonly', false)
                .css('background-color', '#e0e0e0');
                $('#serial_number')
                .val(data.serial_number)
                .prop('readonly', false)
                .css('background-color', '#e0e0e0');
                
                // Ambil Receive Date dari data asset
                let receiveDate = new Date(data.receive_date);
                let formattedDate = receiveDate.toISOString().split('T')[0];
                $('#receiveDate')
                .val(formattedDate)
                .prop('readonly', true)  // Karena diambil dari data asset, buat readonly
                .css('background-color', '#e0e0e0');
                // Set min untuk Dispose Date sesuai Receive Date
                $('#disposeDate').attr('min', formattedDate);
                
                // Set Model
                $('#model')
                .val(data.model)
                .prop('readonly', false)
                .css('background-color', '#e0e0e0');

                // Simpan data asset ke variabel global agar nantinya bisa dibandingkan di validasi submit
                window.selectedAssetData = data;
            }

            // Initialize Asset Number DataTable
            var assetNoTable = $("#assetNoTable").DataTable({
                processing: true,
                searching: true,
                serverSide: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                language: {
                    search: "",
                    searchPlaceholder: "Search...",
                    processing: "Loading data...",
                    emptyTable: "No asset data available",
                    zeroRecords: "No matching assets found"
                },
                dom: 'rt<"bottom"lp><"clear">',
                ajax: {
                    url: base_url + "MstEquipment/getAssetNumbers",
                    type: "GET",
                    dataSrc: function(json) {
                        // For debugging
                        console.log("Asset data received:", json);
                        
                        // Check if the data is in the expected format
                        if (!Array.isArray(json)) {
                            console.error("Expected array but received:", typeof json);
                            return [];
                        }
                        
                        // Format date values for display
                        json.forEach(function(item) {
                            if (item.receive_date) {
                                let date = new Date(item.receive_date);
                                item.receive_date = date.toISOString().split('T')[0];
                            }
                        });
                        
                        return json;
                    },
                    error: function(xhr, error, thrown) {
                        console.error("DataTable error:", error, thrown);
                        console.error("Server response:", xhr.responseText);
                        
                        // Show a more user-friendly error message in the table
                        $('#assetNoTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                },
                columns: [
                    { 
                        data: 'asset_no',
                        render: function(data) {
                            return data !== null && data !== undefined ? data : 'N/A';
                        }
                    },
                    { 
                        data: 'equipment_id',
                        render: function(data) {
                            return data !== null && data !== undefined ? data : 'N/A';
                        }
                    },
                    { 
                        data: 'serial_number',
                        render: function(data) {
                            return data !== null && data !== undefined ? data : 'N/A';
                        }
                    },
                    { 
                        data: 'receive_date',
                        render: function(data) {
                            return data !== null && data !== undefined ? data : 'N/A';
                        }
                    },
                    { 
                        data: 'model',
                        render: function(data) {
                            return data !== null && data !== undefined ? data : 'N/A';
                        }
                    }
                ]
            });

            // Connect length dropdown to datatable
            $('#assetNoLength').on('change', function() {
                assetNoTable.page.len(parseInt($(this).val())).draw();
            });

            // Connect search box to datatable
            $('#searchAssetNo').on('keyup', function() {
                assetNoTable.search(this.value).draw();
            });

            //11-04
            // Jika Receive Date pada modal Add Data diubah,
            // update secara otomatis atribut "min" pada Dispose Date dan koreksi nilainya jika perlu.
            $('#receiveDate').on('change', function () {
                var receiveDate = $(this).val();
                $('#disposeDate').attr('min', receiveDate);
                // Jika dispose date sudah terisi namun kurang dari tanggal receive, maka update.
                if ($('#disposeDate').val() && $('#disposeDate').val() < receiveDate) {
                    $('#disposeDate').val(receiveDate);
                }
            });

            // Jika Receive Date pada modal Edit Data diubah,
            // update secara otomatis atribut "min" pada Dispose Date dan koreksi nilainya jika perlu.
            $('#edit_receiveDate').on('change', function () {
                var receiveDate = $(this).val();
                $('#edit_disposeDate').attr('min', receiveDate);
                if ($('#edit_disposeDate').val() && $('#edit_disposeDate').val() < receiveDate) {
                    $('#edit_disposeDate').val(receiveDate);
                }
            });

            // 11-04 1. Saat user memilih asset dari modal "Asset Number Data"
            // 22-04Ketika sebuah baris diklik di modal Asset Number Data
            // When an item in the "Asset Number Data" modal is clicked
            // Ketika sebuah baris diklik di modal Asset Number Data
            $('#assetNoTable tbody').on('click', 'tr', function() {
                var data = assetNoTable.row(this).data(); // Ambil data aset yang dipilih

                // Tentukan modal mana yang sedang terbuka dan update sesuai
                const sourceModal = $('#assetNoModal').data('source-modal');
                
                if (sourceModal === 'add') {
                    // Jika modal Add Data terbuka, update field di modal Add
                    $('#assetNo').val(data.asset_no).trigger('blur'); // Set Nomor Asset
                    $('#serial_number').val(data.serial_number); // Set Serial Number
                    $('#receiveDate').val(data.receive_date.split(' ')[0]); // Set Receive Date
                    $('#disposeDate').val(new Date().toISOString().split('T')[0]); // Set Dispose Date ke tanggal hari ini

                    // Update min untuk Dispose Date berdasarkan Receive Date jika diperlukan
                    $('#disposeDate').attr('min', $('#receiveDate').val()); 

                    // Sembunyikan modal Asset Number dan tampilkan modal Add Data
                    $('#assetNoModal').modal('hide');
                    $('#addDetailDetModal').modal('show');
                } else if (sourceModal === 'edit') {
                    // Jika modal Edit Data terbuka, update field di modal Edit
                    $('#edit_assetNo').val(data.asset_no).trigger('blur'); // Set Nomor Asset
                    $('#edit_serial_number').val(data.serial_number); // Set Serial Number
                    $('#edit_receiveDate').val(data.receive_date.split(' ')[0]); // Set Receive Date
                    $('#edit_disposeDate').val(new Date().toISOString().split('T')[0]); // Set Dispose Date ke tanggal hari ini

                    // Update min untuk Dispose Date berdasarkan Receive Date jika diperlukan
                    $('#edit_disposeDate').attr('min', $('#edit_receiveDate').val()); 

                    // Sembunyikan modal Asset Number dan tampilkan modal Edit Data
                    $('#assetNoModal').modal('hide');
                    $('#editDetailDetModal').modal('show');
                }
            });


            // Helper function to format date for input fields
            function formatDateForInput(dateString) {
                if (!dateString) return '';
                
                let date = new Date(dateString);
                return date.toISOString().split('T')[0]; // Format as YYYY-MM-DD
            }



            // Store the modal source when opening Asset Number Modal
            // Triggered when 'Search' button is clicked in both Add and Edit modals
            $('.btn-outline-secondary[data-bs-toggle="modal"][data-bs-target="#assetNoModal"]').on('click', function() {
                const isAddModalVisible = $('#addDetailDetModal').is(':visible');
                const isEditModalVisible = $('#editDetailDetModal').is(':visible');

                if (isAddModalVisible) {
                    $('#assetNoModal').data('source-modal', 'add'); // Store which modal was open
                } else if (isEditModalVisible) {
                    $('#assetNoModal').data('source-modal', 'edit');
                }
            });



            // Handle close button on Asset Number modal
            $('#assetNoModal').on('hide.bs.modal', function(e) {
                // Determine which modal was open before
                if ($('#addDetailDetModal').hasClass('show')) {
                    // The Add Data modal was open, keep it open
                    return;
                } else if ($('#editDetailDetModal').hasClass('show')) {
                    // The Edit Data modal was open, keep it open
                    return;
                }
                
                // If neither modal is showing, check which one was last open
                var lastOpenModal = $(this).data('source-modal');
                if (lastOpenModal === 'add') {
                    // Re-open the Add Data modal
                    setTimeout(function() {
                        $('#addDetailDetModal').modal('show');
                    }, 200); // Small delay to ensure the current modal closes first
                } else if (lastOpenModal === 'edit') {
                    // Re-open the Edit Data modal
                    setTimeout(function() {
                        $('#editDetailDetModal').modal('show');
                    }, 200);
                }
            });

            // Update the search button click handlers
            // This code is already correct in your file
            $('.btn-outline-secondary[data-bs-toggle="modal"][data-bs-target="#assetNoModal"]').on('click', function() {
                // Store which modal is currently open
                if ($('#addDetailDetModal').hasClass('show')) {
                    $('#assetNoModal').data('source-modal', 'add');
                } else if ($('#editDetailDetModal').hasClass('show')) {
                    $('#assetNoModal').data('source-modal', 'edit');
                }
            });

            // tambahan monic
            // 11-04 Bagian untuk menangani event "blur" pada input assetNo
            $('#assetNo').on('blur', function() {
                var assetNo = $(this).val();

                $.ajax({
                    url: base_url + "MstEquipment/checkAssetInAcceptance",
                    type: 'POST',
                    data: { assetNo: assetNo },
                    success: function(response) {
                        if (response.status) {
                            // Jika asset ditemukan, isi nilai dan buat readonly
                            var receiveDate = response.data.ea_datereceived;
                            var formattedDate = new Date(receiveDate).toISOString().split('T')[0];

                            $('#model')
                                .val(response.data.ea_model)
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');

                            $('#receiveDate')
                                .val(formattedDate)
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');

                            $('#serial_number')
                                .val(response.data.ea_mfgno)
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');

                            $('#equipmentId')
                                .val(response.data.ea_id)
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');

                            // Update atribut min pada Dispose Date berdasarkan Receive Date
                            $('#disposeDate').attr('min', formattedDate);

                        } else {
                            // Jika tidak ditemukan, atur nilai default (hari ini)
                            const today = new Date().toISOString().split('T')[0];
                            $('#model, #serial_number, #equipmentId')
                                .val('')
                                .prop('readonly', false)
                                .css('background-color', '#ffffff');

                            $('#receiveDate')
                                .val(today)
                                .prop('readonly', false)
                                .css('background-color', '#ffffff');

                            $('#disposeDate').attr('min', today);
                        }
                    }
                });
            });

            // Same for edit form
            // Handler untuk edit form (modal Edit Data)
            $('#edit_assetNo').on('blur', function() {
                var assetNo = $(this).val();
                var assetNo = $(this).val().trim(); // Ambil nilai dari input Asset Number
                
                $.ajax({
                    url: base_url + "MstEquipment/checkAssetInAcceptance",
                    type: 'POST',
                    data: { assetNo: assetNo },
                    success: function(response) {
                        if (response.status) {
                            var receiveDate = response.data.ea_datereceived;
                            var formattedDate = new Date(receiveDate).toISOString().split('T')[0]; // Format YYYY-MM-DD

                            // Jika asset ditemukan di acceptance, update field terkait dan buat readonly
                            $('#edit_model')
                                .val(response.data.ea_model)
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');
                            $('#edit_receiveDate')
                                .val(formattedDate)
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');
                            $('#edit_serial_number')
                                .val(response.data.ea_mfgno)
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0'); 
                            $('#edit_equipmentId')
                                .val(response.data.ea_id)
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');
                        }
                        // Catatan: Tidak perlu mengambil tindakan "else"
                        // agar jika data tidak ditemukan, field tidak direset.
                    }
                });
            });

            // 14-04 2. Saat modal "Add Data" ditampilkan
            $('#addDetailDetModal').on('show.bs.modal', function () {
                if ($.trim($('#assetNo').val()) === "") {
                    // Reset form dan hapus styling error:
                    $('#addDataForm')[0].reset();
                    $('#addDataForm input, #addDataForm select').prop('readonly', false).css('background-color', '');
                    $('#addDataForm .invalid-feedback').hide();
                    $('#addDataForm input, #addDataForm select').removeClass('is-invalid');

                    // Set nilai Receive Date dan Dispose Date ke tanggal hari ini.
                    setTimeout(function() {
                        const today = new Date().toISOString().split('T')[0];
                        $('#receiveDate').val(today).trigger('change');
                        $('#disposeDate').val(today);
                    }, 100);
                } else {
                    // Jika sudah ada isian, jangan reset nilai field.
                    $('#addDataForm .invalid-feedback').hide();
                    $('#addDataForm input, #addDataForm select').removeClass('is-invalid');
                }
            });
            
            // 11-04 tambahan monic            
            $('#cancelButton').on('click', function() {
                $('#addDataForm')[0].reset(); // Reset form
                $('#addDataForm input, #addDataForm select').removeClass('is-invalid'); // Menghapus class is-invalid yang menandakan error
            });
            // 11-04
            $('#addDetailDetModal').on('hidden.bs.modal', function () {
                // Reset form agar saat modal dibuka kembali tampil bersih
                $("#addDataForm")[0].reset();
                $("#addDataForm input, #addDataForm select").removeClass('is-invalid').css('background-color', '');
                $("#addDataForm .invalid-feedback").hide();
            });

            // Tambahkan DI SINI (setelah handler addDetailDetModal.on('hidden.bs.modal', ...))
            $('#editDetailDetModal').on('hidden.bs.modal', function () {
            // Hapus semua class is-invalid dan sembunyikan feedback di form Edit
            $('#editDataForm input, #editDataForm select').removeClass('is-invalid');
            $('#editDataForm .invalid-feedback').hide();
            });

            // 17-04 tambahan monic
            $('#addDataForm').on('submit', function(e) {
                e.preventDefault(); // cegah submit default

                // Bersihkan styling error
                $('#addDataForm input, #addDataForm select').removeClass('is-invalid');
                $('#addDataForm .invalid-feedback').hide();

                // Ambil nilai input dari form
                var formData = {
                    assetNo: $('#assetNo').val().trim(),
                    equipmentId: $('#equipmentId').val().trim(),
                    kind: $('#kind').val().trim(),
                    brand: $('#brand').val().trim(),
                    serial_number: $('#serial_number').val().trim(),
                    status: $('#status').val().trim(),
                    receiveDate: $('#receiveDate').val().trim(),
                    disposeDate: $('#disposeDate').val().trim(),
                    model: $('#model').val().trim(),
                    equipmentName: $('#equipmentName').val().trim()
                };

                var isValid = true;
                $.each(formData, function(key, value) {
                    if (!value) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + 'Error').show();
                        isValid = false;
                    }
                });
                if (!isValid) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Harap Isi Semua Kolom!',
                        text: 'All field are required to fill.',
                        confirmButtonClass: "btn btn-warning"
                    });
                    return;
                }

                // 17-04 --- Validasi Duplikasi berdasarkan data dari DataTable tampilan MstEquipment (master data) ---
                var assetDuplicate = false;
                var serialDuplicate = false;

                // Dapatkan data master equipment dari DataTable utama
                var masterData = table.rows().data().toArray();
                $.each(masterData, function(index, row) {
                    if (assetNoInput === row.e_assetno) {
                        assetDuplicate = true;
                    }
                    if (serialInput === row.e_serialnumber) {
                        serialDuplicate = true;
                    }
                });

                if (assetDuplicate && !serialDuplicate) {
                    $("#assetNo").addClass("is-invalid");
                    $("#assetNoError").text("Nomor Asset is already exist!").show();
                    Swal.fire("Info!", "Nomor Asset sudah terdaftar!", "info");
                    return;
                } else if (serialDuplicate && !assetDuplicate) {
                    $("#serial_number").addClass("is-invalid");
                    $("#serial_numberError").text("Serial Number is already exist!").show();
                    Swal.fire("Info!", "Serial Number sudah terdaftar!", "info");
                    return;
                } else if (assetDuplicate && serialDuplicate) {
                    $("#assetNo").addClass("is-invalid");
                    $("#serial_number").addClass("is-invalid");
                    $("#assetNoError").text("Nomor Asset is already exist!").show();
                    $("#serial_numberError").text("Serial Number is already exist!").show();
                    Swal.fire("Info!", "Nomor Asset & Serial Number sudah terdaftar!", "info");
                    return;
                }

                // Jika tidak ada duplikasi, lanjutkan kirim data via AJAX
                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "MstEquipment/add",
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
                        }).then(function(result) {
                            table.ajax.reload();
                            $('#addDetailDetModal').modal('hide');
                            $('#addDataForm')[0].reset();
                            $('#addDataForm input, #addDataForm select').removeClass('is-invalid');
                            $('#addDataForm .invalid-feedback').hide();
                        });
                    } else {
                        Swal.fire("Info!", response.message, "info");
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    let message = JSON.parse(jqXHR.responseText);
                    Swal.fire("Info!", message.code + '<br>' + message.message, "info");
                }).always(function() {
                    $('#wait_screen').hide();
                });
            });

        // Gunakan satu event handler untuk submit form addDataForm
        // 17-04 Hapus event handler pertama pada addDataForm dan gunakan hanya satu handler
        $('#addDataForm').off('submit').on('submit', function(e) {
            e.preventDefault();

            // Bersihkan styling error sebelumnya
            $('#addDataForm input, #addDataForm select').removeClass('is-invalid');
            $('#addDataForm .invalid-feedback').hide();

            // Ambil nilai input dari form
            var formData = {
                assetNo: $('#assetNo').val().trim(),
                equipmentId: $('#equipmentId').val().trim(),
                kind: $('#kind').val().trim(),
                brand: $('#brand').val().trim(),
                serial_number: $('#serial_number').val().trim(),
                status: $('#status').val().trim(),
                receiveDate: $('#receiveDate').val().trim(),
                disposeDate: $('#disposeDate').val().trim(),
                model: $('#model').val().trim(),
                equipmentName: $('#equipmentName').val().trim()
            };

            // Validasi semua kolom wajib diisi
            var isValid = true;
            var emptyFields = [];

            // ======== TAMBAH START ========
            // definisikan field yang wajib
            var requiredKeys = [
                'assetNo',
                'equipmentId',
                'kind',
                'brand',
                'serial_number',
                'status',
                'receiveDate',
                'model'
            ];

            // validasi satu per satu
            var emptyFields = [];
            requiredKeys.forEach(function(key) {
                if (!formData[key]) {
                    $('#' + key).addClass('is-invalid');
                    $('#' + key + 'Error').show();
                    // untuk menampilkan nama kolom friendly
                    var fieldLabel = $('#' + key).closest('.col').find('label').text();
                    emptyFields.push(fieldLabel);
                    isValid = false;
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'info',
                    title: 'Harap Isi Semua Kolom!',
                    text: 'Column ' + emptyFields.join(', ') + ' required to fill!',
                    confirmButtonClass: "btn btn-warning"
                });
                return;
            }
            // ======== TAMBAH END ========

            // Jika ada kolom yang kosong, tampilkan SweetAlert dan hentikan proses
            if (!isValid) {
                Swal.fire({
                    icon: 'info',
                    title: 'Harap Isi Semua Kolom!',
                    text: 'Column ' + emptyFields.join(', ') + ' required to fill!',
                    confirmButtonClass: "btn btn-warning"
                });
                return;
            }

            // Validasi duplikasi Nomor Asset dan Serial Number
            var assetDuplicate = false;
            var serialDuplicate = false;
            
            // Dapatkan data dari DataTable
            var masterData = table.rows().data().toArray();
            $.each(masterData, function(index, row) {
                if (formData.assetNo === row.e_assetno) {
                    assetDuplicate = true;
                }
                if (formData.serial_number === row.e_serialnumber) {
                    serialDuplicate = true;
                }
            });

            // Tangani berbagai skenario duplikasi
            if (assetDuplicate && serialDuplicate) {
                $("#assetNo, #serial_number").addClass("is-invalid");
                $("#assetNoError").text("Nomor Asset is already exist!").show();
                $("#serial_numberError").text("Serial Number is already exist!").show();
                Swal.fire("Info!", "Nomor Asset & Serial Number sudah terdaftar!", "info");
                return;
            } else if (assetDuplicate) {
                $("#assetNo").addClass("is-invalid");
                $("#assetNoError").text("Nomor Asset is already exist!").show();
                Swal.fire("Info!", "Nomor Asset sudah terdaftar!", "info");
                return;
            } else if (serialDuplicate) {
                $("#serial_number").addClass("is-invalid");
                $("#serial_numberError").text("Serial Number is already exist!").show();
                Swal.fire("Info!", "Serial Number sudah terdaftar!", "info");
                return;
            }

            // Jika lolos semua validasi, kirim data via AJAX
            $('#wait_screen').show();
            $.ajax({
                url: base_url + "MstEquipment/add",
                type: 'POST',
                data: formData
            }).done(function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        table.ajax.reload(); // reload DataTable
                        $('#addDetailDetModal').modal('hide');
                        $('#addDataForm')[0].reset();
                        $('#addDataForm input, #addDataForm select').removeClass('is-invalid');
                        $('#addDataForm .invalid-feedback').hide();
                    });
                } else {
                    Swal.fire("Info!", response.message, "info");
                }
            }).fail(function(jqXHR) {
                var message = JSON.parse(jqXHR.responseText);
                Swal.fire("Info!", message.code + '<br>' + message.message, "info");
            }).always(function() {
                $('#wait_screen').hide();
            });
        });

            $('#tabelEquipment').on('click', '.edit-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: base_url + "MstEquipment/edit",
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response.status) {
                            var isReadonly = response.isReadonly; // Ambil status readonly

                            // Set input fields berdasarkan data dari server
                            $('#edit_e_id').val(response.data.e_id).prop('readonly', true);
                            
                            $('#edit_assetNo').val(response.data.e_assetno).prop('readonly', isReadonly);
                            $('#edit_equipmentId').val(response.data.e_equipmentid).prop('readonly', isReadonly);
                            $('#edit_kind').val(response.data.e_kind).prop('readonly', false);
                            $('#edit_brand').val(response.data.e_brand).prop('readonly', false);
                            $('#edit_serial_number').val(response.data.e_serialnumber).prop('readonly', isReadonly);
                            $('#edit_status').val(response.data.e_status).prop('readonly', false);
                            $('#edit_disposeDate').val(response.data.e_disposedate).prop('readonly', false);
                            $('#edit_model').val(response.data.e_model).prop('readonly', isReadonly);
                            $('#edit_equipment_name').val(response.data.e_equipmentname).prop('readonly', false);

                            if (response.data.e_receivedate) {
                                let formattedDate = formatDate(response.data.e_receivedate);
                                $('#edit_receiveDate').val(formattedDate).prop('readonly', isReadonly);
                                // Update minimum Dispose Date berdasarkan Receive Date
                                $('#edit_disposeDate').attr('min', formattedDate);
                            }

                            // Atur warna latar belakang berdasarkan readonly
                            if (isReadonly) {
                                $('#edit_assetNo, #edit_equipmentId, #edit_serial_number, #edit_receiveDate, #edit_model').css('background-color', '#e0e0e0');
                            } else {
                                $('#edit_assetNo, #edit_equipmentId, #edit_serial_number, #edit_receiveDate, #edit_model').css('background-color', '#ffffff');
                            }

                            $('#editDetailDetModal').modal('show');
                        } else {
                            swal("Info!", response.message, {
                                icon: "info",
                                buttons: {
                                    confirm: { className: "btn btn-danger" },
                                },
                            });
                        }
                    },
                    error: function() {
                        swal("Error!", "Terjadi kesalahan pada server.", {
                            icon: "error",
                            buttons: {
                                confirm: { className: "btn btn-danger" },
                            },
                        });
                    }
                });
            });

            // Fungsi untuk mengonversi format MM/DD/YYYY menjadi YYYY-MM-DD
            function formatDate(dateString) {
                let parts = dateString.split('/');
                if (parts.length === 3) {
                    let month = parts[0].padStart(2, '0');
                    let day = parts[1].padStart(2, '0');
                    let year = parts[2];
                    return `${year}-${month}-${day}`;
                }
                return dateString; // Jika format sudah benar, kembalikan tanpa perubahan
            }
            
            // 09-04 Store the names of the fields that are required
            const requiredFields = [
                { id: 'edit_assetNo', name: 'Nomor Asset' },
                { id: 'edit_equipmentId', name: 'Equipment ID' },
                { id: 'edit_kind', name: 'Category' },
                { id: 'edit_brand', name: 'Brand' },
                { id: 'edit_serial_number', name: 'Serial Number' },
                { id: 'edit_status', name: 'Status' },
                { id: 'edit_receiveDate', name: 'Receive Date' },
                { id: 'edit_disposeDate', name: 'Dispose Date' },
                { id: 'edit_model', name: 'Model' },
                { id: 'edit_equipment_name', name: 'Equipment Name' }
            ];

            // 17-04 Function to validate form
            function validateForm() {
                let isValid = true;
                let emptyFields = [];

                // Reset any previous validation states
                $('#editDataForm input, #editDataForm select').removeClass('is-invalid');
                $('#editDataForm .invalid-feedback').hide();

                // Definisikan semua field yang wajib diisi
                const requiredFields = [
                    { id: 'edit_assetNo',      name: 'Nomor Asset'   },
                    { id: 'edit_equipmentId',   name: 'Equipment ID'  },
                    { id: 'edit_kind',          name: 'Category'      },
                    { id: 'edit_brand',         name: 'Brand'         },
                    { id: 'edit_serial_number', name: 'Serial Number' },
                    { id: 'edit_status',        name: 'Status'        },
                    { id: 'edit_receiveDate',   name: 'Receive Date'  },
                    { id: 'edit_model',         name: 'Model'         }
                ];

                // Loop through required fields and check for empty values
                requiredFields.forEach(field => {
                    const value = $('#' + field.id).val();
                    if (!value || value.trim() === "") {
                        $('#' + field.id).addClass('is-invalid');
                        $('#' + field.id + 'Error').show();
                        emptyFields.push(field.name);
                        isValid = false;
                    }
                });

                if (!isValid) {
                    // Display SweetAlert with the names of the empty fields
                    Swal.fire({
                        icon: 'info',
                        title: 'Harap Isi Semua Kolom!',
                        text: `Column ${emptyFields.join(', ')} required to fill!`,
                        confirmButtonText: "OK"
                    });
                }
                return isValid;
            }

            // 09-04 tambahan monic untuk validasi form edit
            $('#editDataForm').on('submit', function (e) {
                e.preventDefault();

                // Validasi kolom-kolom wajib
                if (!validateForm()) {
                    return; // Hentikan jika validasi gagal
                }

                // Kumpulkan data form
                // SESUDAH perbaikan
                let formData = {
                    id: $('#edit_e_id').val(),
                    assetNo: $('#edit_assetNo').val().trim(),
                    serial_number: $('#edit_serial_number').val().trim(),
                    equipmentId: $('#edit_equipmentId').val().trim(),
                    kind: $('#edit_kind').val().trim(),
                    brand: $('#edit_brand').val().trim(),
                    status: $('#edit_status').val().trim(),
                    receiveDate: $('#edit_receiveDate').val().trim(),
                    disposeDate: $('#edit_disposeDate').val().trim(),         // ← ditambahkan
                    model: $('#edit_model').val().trim(),
                    equipmentName: $('#edit_equipment_name').val().trim()     // ← ditambahkan
                };

                // Validasi duplikasi dengan mengecualikan record yang sedang di-edit
                var assetDuplicate = false;
                var serialDuplicate = false;
                var currentId = $('#edit_e_id').val();
                
                // Periksa duplikasi dari data yang ada
                table.rows().every(function() {
                    var data = this.data();
                    // Abaikan baris untuk ID yang sama (record yang sedang di-edit)
                    if (data.e_id != currentId) {
                        if (formData.assetNo === data.e_assetno) {
                            assetDuplicate = true;
                        }
                        if (formData.serial_number === data.e_serialnumber) {
                            serialDuplicate = true;
                        }
                    }
                });

                // Tangani berbagai skenario duplikasi
                if (assetDuplicate && serialDuplicate) {
                    $("#edit_assetNo, #edit_serial_number").addClass("is-invalid");
                    $("#edit_assetNoError").text("Nomor Asset is already exist!").show();
                    $("#edit_serial_numberError").text("Serial Number is already exist!").show();
                    Swal.fire("Info!", "Nomor Asset & Serial Number sudah terdaftar!", "info");
                    return;
                } else if (assetDuplicate) {
                    $("#edit_assetNo").addClass("is-invalid");
                    $("#edit_assetNoError").text("Nomor Asset is already exist!").show();
                    Swal.fire("Info!", "Nomor Asset sudah terdaftar!", "info");
                    return;
                } else if (serialDuplicate) {
                    $("#edit_serial_number").addClass("is-invalid");
                    $("#edit_serial_numberError").text("Serial Number is already exist!").show();
                    Swal.fire("Info!", "Serial Number sudah terdaftar!", "info");
                    return;
                }

                // Jika lolos semua validasi, kirim data via AJAX
                $('#wait_screen').show();

                $.ajax({
                    url: base_url + "MstEquipment/update",
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.status) {
                            // Jika update sukses, tampilkan SweetAlert dan tutup modal
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                table.ajax.reload();
                                $('#editDetailDetModal').modal('hide');
                            });
                        } else {
                            // Jika error dari server, tampilkan pesan
                            Swal.fire({
                                icon: 'info',
                                title: 'Info!',
                                text: response.message,
                                confirmButtonText: "OK"
                            });
                        }
                    },
                    error: function (xhr) {
                        let errorMsg = 'Terjadi kesalahan saat mengupdate data!';
                        try {
                            let response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {}
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg,
                            confirmButtonText: "OK"
                        });
                    }
                }).always(function () {
                    $('#wait_screen').hide();
                });
            });

            $(document).on("click", ".delete-btn", function () {
                var id = $(this).data("id");
                var base_url = "<?= base_url() ?>";

                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Data dengan ID: " + id + " akan dihapus secara permanen!",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Hapus",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: base_url + "/MstEquipment/delete", // pastikan URL sesuai dengan route controller
                            type: "POST",
                            data: { id: id },
                            dataType: "json",
                            success: function (response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: "Berhasil!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonColor: "#3085d6"
                                    }).then(() => {
                                        // Reload tabel setelah penghapusan berhasil
                                        $('#tabelEquipment').DataTable().ajax.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.message,
                                        icon: "error",
                                        confirmButtonColor: "#d33"
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error("Error:", error);
                                Swal.fire({
                                    title: "Error!",
                                    text: "Terjadi kesalahan pada server.",
                                    icon: "error",
                                    confirmButtonColor: "#d33"
                                });
                            }
                        });
                    }
                });
            });

        });

        // 17-04 Tambahkan di bagian document ready
        // Event handler untuk input/select pada form Add Data
        $('#addDataForm input, #addDataForm select').on('input change', function() {
            $(this).removeClass('is-invalid');
            let errorId = $(this).attr('id') + 'Error';
            $('#' + errorId).hide();
        });

        // Event handler untuk input/select pada form Edit Data
        $('#editDataForm input, #editDataForm select').on('input change', function() {
            $(this).removeClass('is-invalid');
            let idParts = $(this).attr('id').split('_');
            let errorId = '';
            
            // Menangani perbedaan penamaan id error pada form edit
            if (idParts.length > 1) {
                // Format error ID untuk element dengan prefix 'edit_'
                errorId = idParts[0] + '_' + idParts[1] + 'Error';
            } else {
                errorId = idParts[0] + 'Error';
            }
            
            $('#' + errorId).hide();
        });

        //22-04
        // Fungsi untuk memeriksa apakah "Asset Number" sudah ada di dalam modal "Asset Number Data"
        // Fungsi untuk memeriksa apakah "Asset Number" sudah ada di dalam modal "Asset Number Data"
        function checkAssetNumberExistence(assetNo) {
            let assetExists = false;

            // Loop melalui setiap baris di tabel Asset Number Data
            $('#assetNoTable tbody tr').each(function() {
                let row = $(this);
                let assetData = row.find('td').eq(0).text(); // Nomor Asset berada di kolom pertama
                if (assetData.trim() === assetNo.trim()) {
                    assetExists = true;
                }
            });

            return assetExists;
        }

        // 22-04 Event handler untuk tombol "Edit" pada DataTable
        $('#tabelDisposal').on('click', '.edit-btn', function () {
            var id = $(this).data('id');

            $.ajax({
                url: base_url + "TransDisposal/edit",
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.status) {
                        // Isi form modal edit dengan data yang diambil
                        $('#edit_td_id').val(response.data.td_id);
                        $('#edit_assetNo').val(response.data.td_assetno).trigger('blur'); // Set Nomor Asset
                        $('#edit_category').val(response.data.td_category);
                        $('#edit_reason').val(response.data.td_reason);
                        $('#edit_serial_number').val(response.data.td_serialnumber);
                        $('#edit_decisionDate').val(response.data.td_decisiondate);
                        $('#edit_disposeDate').val(response.data.td_disposedate);

                        // Periksa apakah Nomor Asset yang ada di form Edit sudah ada di modal Asset Number Data
                        var assetNo = response.data.td_assetno;
                        if (checkAssetNumberExistence(assetNo)) {
                            // Jika asset number sudah ada, nonaktifkan tombol Search
                            $('#editAssetNoSearchBtn').prop('disabled', true);
                        } else {
                            $('#editAssetNoSearchBtn').prop('disabled', false); // Aktifkan kembali tombol Search
                        }

                        // Tampilkan modal Edit Data
                        $('#editDetailDetModal').modal('show');
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                },
                error: function () {
                    Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                }
            });
        });

        //22-04
        // Fungsi untuk menonaktifkan tombol "Search" di modal Edit Data saat modal dibuka
        $('#editDetailDetModal').on('shown.bs.modal', function () {
        });

        // 22-04 Event handler: ketika tombol "Search" di klik untuk membuka modal Asset Number Data
        // Event handler: ketika tombol "Search" di klik untuk membuka modal Asset Number Data
        $('#editAssetNoSearchBtn').on('click', function() {
            window.currentAssetSearch = "edit"; // Set flag untuk menandakan pencarian dari modal edit
            $('#editDetailDetModal').modal('hide');  // Sembunyikan modal Edit
            $('#assetNoModal').modal('show'); // Tampilkan modal Asset Number Data
        });



        //22-04
        // Ketika modal Asset Number Data ditutup, periksa modal mana yang terbuka
        // 22-04: When Asset Number Data modal is closed, return to the previous modal
        // When the "Asset Number Data" modal is hidden, reopen the correct modal
        $('#assetNoModal').on('hidden.bs.modal', function() {
            const sourceModal = $(this).data('source-modal');

            if (sourceModal === 'add') {
                // If Add Data modal was open, reopen Add Data modal
                $('#addDetailDetModal').modal('show');
            } else if (sourceModal === 'edit') {
                // If Edit Data modal was open, reopen Edit Data modal
                $('#editDetailDetModal').modal('show');
            }
        });



    </script>
</div>
<?= $this->endSection() ?>"