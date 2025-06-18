<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>
<head>
<style>
input[readonly], textarea[readonly] {
    background-color: #e0e0e0; /* Darker gray color for readonly fields */
    cursor: not-allowed; /* Optional: change the cursor to indicate that the field is not editable */
}
/* Tambahkan CSS untuk asset-number-container */
.asset-number-container {
    display: flex;
    gap: 10px;
}
</style>
</head>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Disposal</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDetailDetModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add Disposal
        </button>
    </p>
    <div class="card-body">
        <table id="tabelDisposal" class="display table table-striped table-hover" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 5%">Action</th>
                    <th style="width: 5%">ID</th>
                    <th style="width: 5%">No Asset</th>
                    <th>Category</th>
                    <th>Reason</th>
                    <th>Decision Date</th>
                    <th>Dispose Date</th>
                    <th>Last Update</th>
                    <th>Last User</th>
                    <th style="width: 10%">Serial Number</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>      
    </div>

        <!-- Modal Add -->
        <div class="modal fade" id="addDetailDetModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addDetailDetModalLabel" aria-hidden="true">
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
                                            <button type="button" class="btn btn-outline-secondary" id="assetNoSearchBtn" data-bs-toggle="modal" data-bs-target="#assetNoModal">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                        <div id="assetNoError" class="invalid-feedback" style="display:none;">Asset Number is required</div>
                                    </div>
                                    <div class="col">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category">
                                            <option value="">-- Pilih Category --</option>
                                            <?php foreach ($cat as $mst): ?>
                                                <option value="<?= $mst->equipmentcat; ?>">
                                                    <?= $mst->equipmentcat; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div id="categoryError" class="invalid-feedback" style="display:none;">Category is required</div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px;">
                                <div class="col">
                                    <label for="reason" class="form-label">Reason</label>
                                    <select class="form-select" id="reason" name="reason">
                                        <option value="">-- Select Reason --</option>
                                        <?php foreach ($reasonList as $r): ?>
                                            <option value="<?= $r->td_id ?>"><?= $r->td_reason ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div id="reasonError" class="invalid-feedback" style="display:none;">Reason is required</div>
                                </div>
                                    <div class="col">
                                        <label for="serial_number" class="form-label">Serial Number</label>
                                        <input type="text" class="form-control" id="serial_number" name="serial_number">
                                        <div id="serial_number_Error" class="invalid-feedback" style="display:none;">Serial Number is required</div>
                                    </div>
                                    <div class="col">
                                        <label for="decisionDate" class="form-label">Decision Date</label>
                                        <input type="date" class="form-control" id="decisionDate" name="decisionDate" value="<?= date('Y-m-d'); ?>">
                                        <div id="decisionDateError" class="invalid-feedback" style="display:none;">Decision Date is required</div>
                                    </div>
                                    <div class="col">
                                        <label for="disposeDate" class="form-label">Dispose Date</label>
                                        <input type="date" class="form-control" id="disposeDate" name="disposeDate" value="<?= date('Y-m-d'); ?>">
                                        <div id="disposeDateError" class="invalid-feedback" style="display:none;">Dispose Date is required</div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="save">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editDetailDetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editDetailDetModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDetailDetModalLabel">Edit Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editDataForm">
                            <input type="hidden" id="edit_td_id" name="td_id">
                            <div class="col-md-12">
                                <div class="row" style="margin-bottom: 20px;">
                                    <div class="col">
                                        <label for="edit_assetNo" class="form-label">Asset Number</label>
                                        <div class="asset-number-container">
                                            <input type="number" class="form-control" id="edit_assetNo" name="edit_assetNo">
                                            <button type="button" class="btn btn-outline-secondary" id="editAssetNoSearchBtn" data-bs-toggle="modal" data-bs-target="#assetNoModal">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                        <div id="edit_assetNoError" class="invalid-feedback" style="display: none;">Asset Number is required</div>
                                    </div>
                                    <div class="col">
                                        <label for="edit_category" class="form-label">Category</label>
                                        <select class="form-select" id="edit_category" name="edit_category">
                                            <option value="">-- Pilih Category --</option>
                                            <?php foreach ($cat as $mst): ?>
                                                <option value="<?= $mst->equipmentcat; ?>">
                                                    <?= $mst->equipmentcat; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div id="edit_categoryError" class="invalid-feedback" style="display:none;">Category is required</div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px;">
                                <div class="col">
                                    <label for="edit_reason" class="form-label">Reason</label>
                                    <select class="form-select" id="edit_reason" name="edit_reason">
                                        <option value="">-- Select Reason --</option>
                                        <?php foreach ($reasonList as $r): ?>
                                            <option value="<?= $r->td_id ?>"><?= $r->td_reason ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div id="edit_reasonError" class="invalid-feedback" style="display:none;">Reason is required</div>
                                </div>
                                    <div class="col">
                                        <label for="edit_serial_number" class="form-label">Serial Number</label>
                                        <input type="text" class="form-control" id="edit_serial_number" name="edit_serial_number">
                                        <div id="edit_serial_number_Error" class="invalid-feedback" style="display:none;">Serial Number is required</div>
                                    </div>
                                    <div class="col">
                                        <label for="edit_decisionDate" class="form-label">Decision Date</label>
                                        <input type="date" class="form-control" id="edit_decisionDate" name="edit_decisionDate">
                                        <div id="edit_decisionDateError" class="invalid-feedback" style="display:none;">Decision Date is required</div>
                                    </div>
                                    <div class="col">
                                        <label for="edit_disposeDate" class="form-label">Dispose Date</label>
                                        <input type="date" class="form-control" id="edit_disposeDate" name="edit_disposeDate">
                                        <div id="edit_disposeDateError" class="invalid-feedback" style="display:none;">Dispose Date is required</div>
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

        <!-- Modal for Asset No Data -->
        <div class="modal fade" id="assetNoModal" tabindex="-1" aria-labelledby="assetNoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assetNoModalLabel">Asset Number Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table id="assetNoTable" class="table table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Asset No</th>
                                    <th style="width: 15%;">Equipment ID</th>
                                    <th style="width: 25%;">Serial Number</th>
                                    <th style="width: 45%;">Receive Date</th>
                                    <th style="width: 45%;">Model</th>
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
        // 08-04 monica
        $(document).ready(function () {
        let base_url = '<?= base_url() ?>';

        window.skipResetEdit = false;

        // Hanya reset form Edit kalau bukan sedang switch ke modal Asset Number
        $('#editDetailDetModal').on('hidden.bs.modal', function () {
            if (window.skipResetEdit) {
                window.skipResetEdit = false;
                return;
            }
            // reset‐only‐when‐really‐closing
            $('#editDataForm')[0].reset();
            $('.form-control, .form-select').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        });

        // Fungsi untuk load asset numbers dan populate dropdown untuk modal Edit Data
        function loadEditAssetNumbersDropdown() {
            $.ajax({
                url: base_url + "TransDisposal/getAssetNumbers",
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

        // Panggil fungsi load dropdown untuk modal Edit Data ketika document sudah siap
        loadEditAssetNumbersDropdown();

        // Event handler: ketika salah satu item dropdown (modal Edit) diklik, update input edit_assetNo
        $(document).on('click', '.edit-asset-no-item', function (e) {
            e.preventDefault();
            let selectedAssetNo = $(this).text();
            $("#edit_assetNo").val(selectedAssetNo).trigger('blur');
        });

        // 22-04 Set flag untuk membedakan asal pencarian asset
       // Button trigger for modal (no disabling needed after click)
       $('#assetNoSearchBtn').on('click', function() {
            window.currentAssetSearch = "add"; // Set the current modal state for asset number modal
            $('#addDetailDetModal').modal('hide');  // Hide Add Data modal
            $('#assetNoModal').modal('show');      // Show Asset Number Modal
        });

        //22-04
        $('#editAssetNoSearchBtn').on('click', function() {
            window.currentAssetSearch = "edit";
            window.skipResetEdit = true;      // <<-- tambahkan ini
            $('#editDetailDetModal').modal('hide');
            $('#assetNoModal').modal('show');
        });

        // 11-04
        function getLocalDate() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            return yyyy + '-' + mm + '-' + dd;
        }

        //14-04
        $('#addDetailDetModal').on('shown.bs.modal', function () {
            var decisionDate = $('#decisionDate').val();
            $("#disposeDate").attr('min', decisionDate);
            if (!$('#disposeDate').val()) {
                var today = getLocalDate();
                var defaultDisposeDate = (today < decisionDate ? decisionDate : today);
                $('#disposeDate').val(defaultDisposeDate);
            }
        });

        //14-04
        // A. Fungsi untuk mengupdate minimum nilai Dispose Date
        function updateDisposeMin() {
            var decisionVal = $('#decisionDate').val();
            if (decisionVal) {
                $("#disposeDate").attr('min', decisionVal);
                var currentDispose = $('#disposeDate').val();
                if (!currentDispose || currentDispose < decisionVal) {
                    $('#disposeDate').val(decisionVal);
                }
            }
        }

        // ============================================
        // B. Delegated event listener untuk #decisionDate
        $(document).on('input change', '#decisionDate', function () {
            updateDisposeMin();
        });

        // ============================================
        // C. Saat modal "Add Data" terbuka, set nilai awal untuk Dispose Date

        // 11-04 Event handler untuk saat user selesai mengetik Nomor Asset (misalnya saat blur)
        $('#assetNo').on('blur', function() {
            var assetNo = $(this).val().trim();

            if(assetNo === ""){
                // Jika kosong, pastikan Serial Number dikosongkan, dan Decision Date diset ke tanggal hari ini (waktu lokal)
                $('#serial_number').val('').prop('readonly', false).css('background-color', '#ffffff');
                var today = getLocalDate();
                $('#decisionDate').val(today).prop('readonly', false).css('background-color', '#ffffff');
                return;
            }
            
            $.ajax({
                url: base_url + "MstEquipment/checkAssetInAcceptance",
                type: 'POST',
                data: { assetNo: assetNo },
                success: function(response) {
                    if(response.status) {
                        // Data asset ditemukan
                        var receiveDate = response.data.ea_datereceived;
                        var formattedDate = new Date(receiveDate).toISOString().split('T')[0];
                        
                        // Set nilai dan readonly untuk Serial Number & Decision Date
                        $('#serial_number').val(response.data.ea_mfgno)
                            .prop('readonly', true)
                            .css('background-color', '#e0e0e0');
                        $('#decisionDate').val(formattedDate)
                            .prop('readonly', true)
                            .css('background-color', '#e0e0e0');
                        
                        // Atur minimum Dispose Date berdasarkan Decision Date
                        $("#disposeDate").attr('min', formattedDate);
                    } else {
                        // Data asset tidak ditemukan
                        $('#serial_number').val('').prop('readonly', false).css('background-color', '#ffffff');
                        var today = getLocalDate();
                        $('#decisionDate')
                            .val(today)
                            .prop('readonly', false)
                            .css('background-color', '#ffffff');
                        // Hapus minimum pada Dispose Date, sehingga user bebas memilih tanggal
                        $("#disposeDate").removeAttr('min');
                    }
                }
            });
        });

        //11-04
        $('#edit_decisionDate').on('change', function(){
            var newDecisionDate = $(this).val(); // Format YYYY-MM-DD
            $("#edit_disposeDate").attr('min', newDecisionDate);
            
            var currentDisposeDate = $('#edit_disposeDate').val();
            if (currentDisposeDate && currentDisposeDate < newDecisionDate) {
                $('#edit_disposeDate').val(newDecisionDate);
            }
        });

        // Set minimum date for Dispose Date inputs
        var decisionDateVal = $("#decisionDate").val();
        if (decisionDateVal && decisionDateVal.trim() !== "") {
            $("#disposeDate, #edit_disposeDate").attr('min', decisionDateVal);
        } else {
            var today = new Date().toISOString().split('T')[0];
            $("#disposeDate, #edit_disposeDate").attr('min', today);
        }

                  // Initialize DataTable untuk TransDisposal
                  var table = $("#tabelDisposal").DataTable({
                    scrollX: true,         // <<-- tarik scrollbar horizontal
                    scrollCollapse: true,  // <<-- agar tabel melebar sesuai kontainer
                    autoWidth: false,      // <<-- matikan auto‐sizing kolom
                    pageLength: 10,
                    order: [[7, "desc"]],
                    ajax: {
                        url: "<?= site_url('TransDisposal/getDataDisposal') ?>",
                        dataSrc: "",
                        beforeSend: function() {
                            $('#tabelDisposal tbody').html('<tr><td colspan="10" class="text-center">Loading...</td></tr>');
                        },
                        error: function(xhr, error, thrown) {
                            console.error("AJAX Error:", error, thrown);
                            Swal.fire({ icon: 'error', title: 'AJAX Error', text: 'Failed to fetch data.' });
                        }
                    },
                    columns: [
                        {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.td_id}">
                                        <i class="fa fa-pen-to-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.td_id}">
                                        <i class="fa fa-trash-can"></i>
                                    </a>
                                </div>`;
                            }
                        },
                        { data: 'td_id' },
                        { data: 'td_assetno' },
                        { data: 'td_category' },
                        { data: 'td_reason' },
                        { data: 'td_decisiondate' },
                        { data: 'td_disposedate' },
                        { data: 'td_lastupdate' },
                        { data: 'td_lastuser' },
                        { data: 'td_serialnumber' }
                    ]
                });
      

                  // Inisialisasi DataTable untuk modal Asset No (tidak diubah)
                  var tblAssetNo;
                    $('#assetNoModal').on('shown.bs.modal', function () {
                        if ($.fn.DataTable.isDataTable('#assetNoTable')) {
                        tblAssetNo.destroy();
                        }
                        tblAssetNo = $("#assetNoTable").DataTable({
                        ajax: {
                            url: base_url + "/TransDisposal/getAssetNumbers",
                            dataSrc: "",
                            error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Error fetching asset numbers:', textStatus, errorThrown);
                            alert('Error: Could not load asset numbers.');
                            }
                        },
                        columns: [
                            { data: 'asset_no' },
                            { data: 'equipment_id' },
                            { data: 'serial_number' },
                            { data: 'receive_date' },
                            { data: 'model' }
                        ]
                        });
                    });         

                    // 22-04 Event handler ketika memilih Asset Number di modal "Asset Number Data"
                    // Handle selection of Asset Number from Asset Number Modal
                    // 22-04 Event handler ketika memilih Asset Number di modal "Asset Number Data"
                    $('#assetNoTable').on('click', 'tbody tr', function() {
                        var data = tblAssetNo.row(this).data();  // Ambil data asset yang dipilih

                        if (window.currentAssetSearch === "add") {
                            // Saat modal Add Data terbuka, update field dengan data asset yang dipilih
                            $('#assetNo').val(data.asset_no)   // Set Nomor Asset
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');  // Jadikan readonly
                            $('#serial_number').val(data.serial_number)  // Set Serial Number
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');  // Jadikan readonly
                            $('#decisionDate').val(data.receive_date.split(' ')[0])  // Set Decision Date
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');  // Jadikan readonly
                            // Set Dispose Date ke tanggal hari ini
                            $('#disposeDate').val(new Date().toISOString().split('T')[0])  // Set Dispose Date ke tanggal hari ini
                                .attr('min', data.receive_date.split(' ')[0]);  // Set min untuk Dispose Date berdasarkan Decision Date

                            // Menyembunyikan modal Asset Number dan menampilkan modal Add Data
                            $('#assetNoModal').modal('hide');  // Sembunyikan modal Asset Number
                            $('#addDetailDetModal').modal('show');  // Tampilkan modal Add Data
                        } else if (window.currentAssetSearch === "edit") {
                            // Saat modal Edit Data terbuka, update field dengan data asset yang dipilih
                            $('#edit_assetNo').val(data.asset_no)   // Set Nomor Asset untuk Edit
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');  // Jadikan readonly
                            $('#edit_serial_number').val(data.serial_number)  // Set Serial Number untuk Edit
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');  // Jadikan readonly
                            $('#edit_decisionDate').val(data.receive_date.split(' ')[0])  // Set Decision Date untuk Edit
                                .prop('readonly', true)
                                .css('background-color', '#e0e0e0');  // Jadikan readonly
                            // Set Dispose Date ke tanggal hari ini
                            $('#edit_disposeDate').val(new Date().toISOString().split('T')[0])  // Set Dispose Date ke tanggal hari ini
                                .attr('min', data.receive_date.split(' ')[0]);  // Set min untuk Dispose Date berdasarkan Decision Date

                            // Menyembunyikan modal Asset Number dan menampilkan modal Edit Data
                            $('#assetNoModal').modal('hide');  // Sembunyikan modal Asset Number
                            $('#editDetailDetModal').modal('show');  // Tampilkan modal Edit Data
                        }
                    });

            $('#addDetailDetModal').on('hidden.bs.modal', function () {
                // Reset form
                $('#addDataForm')[0].reset();

                // Reset input dan tombol search
                $('#assetNo').prop('readonly', false).css('background-color', '');
                $('#assetNoSearchBtn').prop('disabled', false);
                $('#serial_number').prop('readonly', false).css('background-color', '');
                $('#decisionDate').prop('readonly', false).css('background-color', '');
                $("#disposeDate").removeAttr('min');
                
                // Remove 'is-invalid' class and hide error messages
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                
                // Reset background colors to default
                $('#assetNo').css('background-color', '');
                $('#category').css('background-color', '');
                $('#reason').css('background-color', '');
                $('#serial_number').css('background-color', '');
                $('#decisionDate').css('background-color', '');
                $('#disposeDate').css('background-color', '');
            });

            //17-04
            $('#addDataForm').on('submit', function (e) {
                e.preventDefault();

                // Clear previous errors
                $('.invalid-feedback').hide();
                $('.form-control, .form-select').removeClass('is-invalid');

                let isValid = true;
                let emptyFields = []; // Array to collect names of empty fields

                // Check Asset Number
                if ($('#assetNo').val().trim() === '') {
                    isValid = false;
                    $('#assetNo').addClass('is-invalid');
                    $('#assetNoError').show();
                    emptyFields.push("Asset Number");
                }

                // Check Category
                if ($('#category').val() === '') {
                    isValid = false;
                    $('#category').addClass('is-invalid');
                    $('#categoryError').show();
                    emptyFields.push("Category");
                }

                // Check Reason
                if ($('#reason').val() === '') {
                    isValid = false;
                    $('#reason').addClass('is-invalid');
                    $('#reasonError').show();
                    emptyFields.push("Reason");
                }

                // Check Serial Number
                if ($('#serial_number').val().trim() === '') {
                    isValid = false;
                    $('#serial_number').addClass('is-invalid');
                    $('#serial_number_Error').show();
                    emptyFields.push("Serial Number");
                }

                // Check Decision Date
                if ($('#decisionDate').val() === '') {
                    isValid = false;
                    $('#decisionDate').addClass('is-invalid');
                    $('#decisionDateError').show();
                    emptyFields.push("Decision Date");
                }

                // Check Dispose Date
                if ($('#disposeDate').val() === '') {
                    isValid = false;
                    $('#disposeDate').addClass('is-invalid');
                    $('#disposeDateError').show();
                    emptyFields.push("Dispose Date");
                }

                // If any validation failed, show detailed SweetAlert and stop submission
                if (!isValid) {
                    // Create the detailed message with list of empty fields
                    let detailMessage = "Column " + emptyFields.join(", ") + " required to fill!";
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'Harap isi semua kolom!',
                        text: detailMessage,
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Lakukan pengecekan duplikat dari data di DataTable
                var duplicateAsset = false;
                var duplicateSerial = false;

                table.rows().every(function () {
                    var data = this.data();
                    // Bandingkan dengan value form
                    if (data.td_assetno == $('#assetNo').val()) {
                        duplicateAsset = true;
                    }
                    if (data.td_serialnumber == $('#serial_number').val()) {
                        duplicateSerial = true;
                    }
                });

                // Jika salah satu atau kedua field duplikat, tampilkan SweetAlert dulu
                if (duplicateAsset || duplicateSerial) {
                    var duplicateMsg = "";
                    if (duplicateAsset) {
                        duplicateMsg += "Nomor Asset is already exist. ";
                    }
                    if (duplicateSerial) {
                        duplicateMsg += "Serial Number is already exist.";
                    }
                    Swal.fire({
                        title: "Info!",
                        text: duplicateMsg,
                        icon: "info",
                        confirmButtonText: "OK"
                    }).then(function () {
                        if (duplicateAsset) {
                            $("#assetNo").addClass("is-invalid").css("background-color", "#f8d7da");
                            $("#assetNoError").text("Nomor Asset is already exist").show();
                        }
                        if (duplicateSerial) {
                            $("#serial_number").addClass("is-invalid").css("background-color", "#f8d7da");
                            $("#serial_number_Error").text("Serial Number is already exist").show();
                        }
                    });
                    return; // Hentikan proses submit jika ada duplikat
                }

                // Jika tidak ada duplikat, lanjutkan pengiriman data via AJAX
                var formData = {
                    assetNo: $('#assetNo').val(),
                    category: $('#category').val(),
                    reason: $('#reason').val(),
                    serial_number: $('#serial_number').val(),
                    decisionDate: $('#decisionDate').val(),
                    disposeDate: $('#disposeDate').val()
                };

                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "TransDisposal/add",
                    type: 'POST',
                    data: formData,
                }).done(function (response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 1500
                        }).then(() => {
                            table.ajax.reload();
                        });
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }).fail(function (jqXHR) {
                    let message = JSON.parse(jqXHR.responseText);
                    Swal.fire("Error!", message.code + '<br>' + message.message, "error");
                }).always(function () {
                    $('#wait_screen').hide();
                    $('#addDetailDetModal').modal('hide');
                });
            });
        
            //14-04
            // Saat user mengetik ulang di input Nomor Asset, hapus error styling dan pesan error-nya
            $('#assetNo').on('input', function(){
                $(this).removeClass('is-invalid').css('background-color', '');
                $('#assetNoError').hide();
            });

            // Saat user mengetik ulang di input Serial Number, hapus error styling dan pesan error-nya
            $('#serial_number').on('input', function(){
                $(this).removeClass('is-invalid').css('background-color', '');
                $('#serial_number_Error').hide();
            });

            $('#assetNo, #serial_number').on('input', function(){
                $(this).removeClass('is-invalid').css('background-color', '');
                var errorId = $(this).attr('id') + "Error";
                $("#" + errorId).hide();
            });
            
    //11-04
    $('#editDetailDetModal').on('shown.bs.modal', function () {
        var decisionDate = $('#edit_decisionDate').val();
        $("#edit_disposeDate").attr('min', decisionDate);
        
        var currentDisposeDate = $('#edit_disposeDate').val();
        if (currentDisposeDate && currentDisposeDate < decisionDate) {
            $('#edit_disposeDate').val(decisionDate);
        }
    });

    // Handling Edit button click
    $('#tabelDisposal').on('click', '.edit-btn', function () {
        var id = $(this).data('id');
        $.ajax({
            url: base_url + "TransDisposal/edit",
            type: 'POST',
            data: { id: id },
            success: function (response) {
                if (response.status) {
                    // Isi form Edit dengan data dari record DataTable
                    $('#edit_td_id').val(response.data.td_id);
                    $('#edit_assetNo').val(response.data.td_assetno);
                    $('#edit_category').val(response.data.td_category); // Tetap pertahankan
                    $('#edit_reason').val(response.data.td_reason);       // Tetap pertahankan
                    $('#edit_decisionDate').val(response.data.td_decisiondate);
                    $('#edit_disposeDate').val(response.data.td_disposedate);
                    $('#edit_serial_number').val(response.data.td_serialnumber);
                    
                    // Simpan nilai asli untuk Category dan Reason
                    window.originalEditCategory = response.data.td_category;
                    window.originalEditReason = response.data.td_reason;
                    
                    // Jika data sudah dipilih dari modal asset sebelumnya, (flag readonly true)
                    if (response.data.readonly === true) {
                        $('#edit_assetNo').prop('readonly', true).css('background-color', '#e0e0e0');
                        $('#edit_serial_number').prop('readonly', true).css('background-color', '#e0e0e0');
                        $('#edit_decisionDate').prop('readonly', true).css('background-color', '#e0e0e0');
                    } else {
                        $('#edit_assetNo').prop('readonly', false).css('background-color', '');
                        $('#edit_serial_number').prop('readonly', false).css('background-color', '');
                        $('#edit_decisionDate').prop('readonly', false).css('background-color', '');
                    }
                    $('#editDetailDetModal').modal('show');
                } else {
                    Swal.fire("Error!", response.message, "error");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                Swal.fire("Error!", "Server error.", "error");
            }
        });
    });

        // 17-04 Menangani submit form untuk edit data
        // Menangani submit form untuk edit data
        $('#editDataForm').on('submit', function (e) {
            e.preventDefault();

            // Clear previous errors
            $('.invalid-feedback').hide();
            $('.form-control, .form-select').removeClass('is-invalid');

            let isValid = true;
            let emptyFields = []; // Array to collect names of empty fields

            // Check Asset Number
            if ($('#edit_assetNo').val().trim() === '') {
                isValid = false;
                $('#edit_assetNo').addClass('is-invalid');
                $('#edit_assetNoError').show();
                emptyFields.push("Asset Number");
            }

            // Check Category
            if ($('#edit_category').val() === '') {
                isValid = false;
                $('#edit_category').addClass('is-invalid');
                $('#edit_categoryError').show();
                emptyFields.push("Category");
            }

            // Check Reason
            if ($('#edit_reason').val() === '') {
                isValid = false;
                $('#edit_reason').addClass('is-invalid');
                $('#edit_reasonError').show();
                emptyFields.push("Reason");
            }

            // Check Serial Number
            if ($('#edit_serial_number').val().trim() === '') {
                isValid = false;
                $('#edit_serial_number').addClass('is-invalid');
                $('#edit_serial_number_Error').show();
                emptyFields.push("Serial Number");
            }

            // Check Decision Date
            if ($('#edit_decisionDate').val() === '') {
                isValid = false;
                $('#edit_decisionDate').addClass('is-invalid');
                $('#edit_decisionDateError').show();
                emptyFields.push("Decision Date");
            }

            // Check Dispose Date
            if ($('#edit_disposeDate').val() === '') {
                isValid = false;
                $('#edit_disposeDate').addClass('is-invalid');
                $('#edit_disposeDateError').show();
                emptyFields.push("Dispose Date");
            }

            // If any validation failed, show detailed SweetAlert and stop submission
            if (!isValid) {
                // Create the detailed message with list of empty fields
                let detailMessage = "Column " + emptyFields.join(", ") + " required to fill!";
                
                Swal.fire({
                    icon: 'info',
                    title: 'Harap isi semua kolom!',
                    text: detailMessage,
                    confirmButtonText: 'OK'
                });
                return false;
            }

                // Continue with duplicate checking and form submission as before
                // --- Duplicate check pada Number Asset & Serial Number ---
                // Ambil nilai baru dari form edit
                var newAsset = $('#edit_assetNo').val().trim();
                var newSerial = $('#edit_serial_number').val().trim();
                var duplicateAsset = false;
                var duplicateSerial = false;

                // Loop melalui seluruh data dari DataTable
                table.rows().every(function () {
                    var data = this.data();
                    // Abaikan baris yang sedang di-edit (dengan id yang sama)
                    if (data.td_id == $('#edit_td_id').val()) {
                        return;
                    }
                    // Cek apakah Nomor Asset sudah ada
                    if (data.td_assetno == newAsset) {
                        duplicateAsset = true;
                    }
                    // Cek apakah Serial Number sudah ada
                    if (data.td_serialnumber == newSerial) {
                        duplicateSerial = true;
                    }
                });

                // Tampilkan pesan peringatan serta ubah styling input sesuai kondisi duplikasi
                if (duplicateAsset && duplicateSerial) {
                    $("#edit_assetNo").addClass("is-invalid");
                    $("#edit_serial_number").addClass("is-invalid");
                    $("#edit_assetNoError").text("Number Asset already exists!").show();
                    $("#edit_serial_number_Error").text("Serial Number already exists!").show();
                    Swal.fire("Info!", "Number Asset & Serial Number already exists!", "info");
                    return;
                } else if (duplicateAsset) {
                    $("#edit_assetNo").addClass("is-invalid");
                    $("#edit_assetNoError").text("Number Asset already exists!").show();
                    Swal.fire("Info!", "Number Asset already exists!", "info");
                    return;
                } else if (duplicateSerial) {
                    $("#edit_serial_number").addClass("is-invalid");
                    $("#edit_serial_number_Error").text("Serial Number already exists!").show();
                    Swal.fire("Info!", "Serial Number already exists!", "info");
                    return;
                }
                // --- End duplicate check ---

                // Jika tidak ada duplikasi, teruskan dengan proses update data
                var formData = {
                    id: $('#edit_td_id').val(),
                    assetNo: $('#edit_assetNo').val(),
                    category: $('#edit_category').val(),
                    reason: $('#edit_reason').val(), // Dropdown value
                    serial_number: $('#edit_serial_number').val(),
                    decisionDate: $('#edit_decisionDate').val(),
                    disposeDate: $('#edit_disposeDate').val()
                };

                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "TransDisposal/update",
                    type: "POST",
                    data: formData,
                }).done(function (response) {
                    if (response.status) {
                        Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500 })
                            .then(function () {
                                table.ajax.reload();
                            });
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }).fail(function (jqXHR) {
                    let message = JSON.parse(jqXHR.responseText);
                    Swal.fire("Error!", message.code + '<br>' + message.message, "error");
                }).always(function () {
                    $('#wait_screen').hide();
                    $('#editDetailDetModal').modal('hide');
                });
            });

            // Real-time validation feedback for Add form inputs
            $('#assetNo, #category, #reason, #serial_number, #decisionDate, #disposeDate').on('input change', function() {
                $(this).removeClass('is-invalid');
                const fieldId = $(this).attr('id');
                if (fieldId === 'serial_number') {
                    $('#serial_number_Error').hide(); // Using original error ID from your HTML
                } else {
                    $(`#${fieldId}Error`).hide();
                }
            });

            // Real-time validation feedback for Edit form inputs
            $('#edit_assetNo, #edit_category, #edit_reason, #edit_serial_number, #edit_decisionDate, #edit_disposeDate').on('input change', function() {
                $(this).removeClass('is-invalid');
                const fieldId = $(this).attr('id');
                if (fieldId === 'edit_serial_number') {
                    $('#edit_serial_number_Error').hide(); // Using original error ID from your HTML
                } else {
                    $(`#${fieldId}Error`).hide();
                }
            });

    // Handle delete button click
    $('#tabelDisposal').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: `Data with ID: ${id} will be permanently deleted!`,
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: base_url + "TransDisposal/delete",
                        type: 'POST',
                        data: { id: id },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire("Deleted!", response.message, "success")
                                    .then(() => {
                                        table.ajax.reload(); // Refresh data table
                                    });
                            } else {
                                Swal.fire("Error!", response.message, "error");
                            }
                        },
                        error: function(xhr) {
                            console.error('Error occurred:', xhr);
                            Swal.fire("Error!", "Server error occurred.", "error");
                        }
                    });
                }
            });
        });

          // Utility: Client-side duplicate checking for Reason dropdown (jika diperlukan)
          function isDuplicate(reasonName, originalName = '') {
            const nameToCheck = reasonName.toLowerCase();
            if (originalName && originalName.toLowerCase() === nameToCheck) {
            return false;
            }
            return reasonList.includes(nameToCheck);
            }

        // Handle Add Form Submission dengan validasi
        $('#addReasonForm').on('submit', function(e) {
            e.preventDefault();
            $('.invalid-feedback').hide();
            $('.form-control, .form-select').removeClass('is-invalid');
            
            let isValid = true;
            const reasonName = $('#reasonName').val().trim();
            if (reasonName === '') {
            isValid = false;
            $('#reasonName').addClass('is-invalid');
            $('#reasonNameError').text('Reason Name is required').show();
            }
            
            if (!isValid) {
            Swal.fire("Harap isi semua kolom!", "All fields are required to fill!", "info");
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
                $('#edit_oldReasonName').val(response.data.disposereason);
                $('#edit_reason').val(response.data.disposereason);
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

        // Handle Edit Form Submission dengan validasi
        $('#editReasonForm').on('submit', function(e) {
            e.preventDefault();
                $('.invalid-feedback').hide();
                $('.form-control, .form-select').removeClass('is-invalid');
                
                const oldReason = $('#edit_oldReasonName').val();
                const newReason = $('#edit_reason').val().trim();
                    
                let isValid = true;
                if (newReason === '') {
                    isValid = false;
                    $('#edit_reason').addClass('is-invalid');
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
                    if (response.status) {
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

    // Event handler untuk edit_assetNo pada klik atau blur, untuk memperbarui field terkait
    $('#edit_assetNo').on('blur', function() {
        var assetNo = $(this).val().trim();
        if (assetNo === "") {
            return;
        }
            
            $.ajax({
                url: base_url + "MstEquipment/checkAssetInAcceptance",
                type: 'POST',
                data: { assetNo: assetNo },
                success: function(response) {
                    if (response.status) {
                        // Jika asset ditemukan di acceptance, update field terkait
                        var receiveDate = response.data.ea_datereceived;
                        var formattedDate = new Date(receiveDate).toISOString().split('T')[0];
                        
                        $('#edit_serial_number').val(response.data.ea_mfgno)
                            .prop('readonly', true)
                            .css('background-color', '#e0e0e0');
                        $('#edit_decisionDate').val(formattedDate)
                            .prop('readonly', true)
                            .css('background-color', '#e0e0e0');
                        
                        // Nonaktifkan dropdown jika readonly
                        $("#editAssetNoDropdownButton").attr("disabled", true);
                        
                        // Set minimum untuk Dispose Date
                        $("#edit_disposeDate").attr('min', formattedDate);
                    }
                }
            });
        });
    });

    $("#disposeDate").on('focus', function() {
        var decisionDate = $("#decisionDate").val();
        if(decisionDate){
            $(this).attr('min', decisionDate);
            var currentVal = $(this).val();
            if(!currentVal || currentVal < decisionDate) {
                $(this).val(decisionDate);
            }
        }
    });

    $("#edit_disposeDate").on('focus', function() {
        var decisionDate = $("#edit_decisionDate").val();
        if(decisionDate){
            $(this).attr('min', decisionDate);
            var currentVal = $(this).val();
            if(!currentVal || currentVal < decisionDate) {
                $(this).val(decisionDate);
            }
        }
    });

    // 22-04 Ketika modal "Asset Number Data" ditutup, pastikan modal "Add Data" tetap terbuka
    $('#assetNoModal').on('hidden.bs.modal', function () {
        if (window.currentAssetSearch === "add") {
            $('#addDetailDetModal').modal('show');  // Menampilkan modal Add Data jika tidak memilih asset
        } else if (window.currentAssetSearch === "edit") {
            $('#editDetailDetModal').modal('show');  // Menampilkan modal Edit Data
        }
    });


    // 22-04 Event handler ketika tombol Close "X" pada modal Asset Number Data
    $('#assetNoModal .btn-close').on('click', function () {
        if (window.currentAssetSearch === "add") {
            $('#addDetailDetModal').modal('show');  // Menampilkan modal Add Data kembali
        } else if (window.currentAssetSearch === "edit") {
            $('#editDetailDetModal').modal('show');  // Menampilkan modal Edit Data kembali
        }
    });


    </script>
</div>
<?= $this->endSection() ?>
