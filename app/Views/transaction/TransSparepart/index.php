<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<div class="card">
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDetailDetModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add New Sparepart
        </button>
    </p>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelEmovement">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>ID</th>
                    <th>Asset No</th>
                    <th>Serial Number</th>
                    <th>Transaction Date</th>
                    <th>PC Name</th>
                    <th>IP Address</th>
                    <th>From Location</th>
                    <th>To Location</th>
                    <th>From User</th>
                    <th>To User</th>
                    <th>Category</th>
                    <th>Return</th>
                    <th>Purpose</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        
    </div>
    
    <!-- Modal Add -->
    <div class="modal fade" id="addDetailDetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addDetailDetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDetailDetModalLabel">Add New Sparepart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form id="addDataForm">
                    <div class="col-md-12">
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col">
                                <label for="assetNo" class="form-label">Asset No <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control assetNo" id="assetNo" name="assetNo" placeholder="Type asset no" readonly>
                                    <button class="btn btn-link search-asset-btn" type="button"
                                            style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col">
                                <label for="pcname" class="form-label">PC Name</label>
                                <input type="text" class="form-control" id="pcname" name="pcname" placeholder="Auto-filled" readonly>
                            </div>
                            <div class="col">
                                <label for="serial_number" class="form-label">Serial Number</label>
                                <input type="text" class="form-control" id="serial_number" name="serialnumber" 
                                    placeholder="Auto-filled" readonly>
                            </div>
                            <div class="col">
                                <label for="category" class="form-label">Category <span style="color: red;">*</span></label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">--Select Category--</option>
                                    <?php foreach ($cat as $mst): ?>
                                        <option value="<?= $mst->equipmentcat; ?>">
                                            <?= $mst->equipmentcat; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col">
                                <label for="ipaddress" class="form-label">IP Address</label>
                                <input type="text" class="form-control" id="ipaddress" name="ipaddress"
                                    pattern="^[0-9.]*$"
                                    onkeypress="return /[0-9.]/.test(event.key)"
                                    placeholder="Type IP Address"
                                    title="Only numbers and dots allowed">
                            </div>
                            <div class="col">
                                <label for="fromlocation" class="form-label">From Location <span style="color: red;">*</span></label>
                                <select class="form-select" id="fromlocation" name="fromlocation">
                                    <option value="">--Select Location--</option>
                                    <?php foreach ($sections as $section): ?>
                                        <option value="<?= $section->sec_section ?>" data-id="<?= $section->sec_sectioncode ?>">
                                            <?= $section->sec_section ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="tolocation" class="form-label">To Location <span style="color: red;">*</span></label>
                                <select class="form-select" id="tolocation" name="tolocation">
                                    <option value="">--Select Location--</option>
                                    <?php foreach ($sections as $section): ?>
                                        <option value="<?= $section->sec_section ?>" data-id="<?= $section->sec_sectioncode ?>">
                                            <?= $section->sec_section ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="fromuser" class="form-label">From User <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="fromuser" name="fromuser" placeholder="Type or select user">
                                    <input type="hidden" id="fromuser_id" name="fromuser_id">
                                    <button class="btn btn-link search-fromuser-btn" type="button"
                                            style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col">
                                <label for="touser" class="form-label">To User <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="touser" name="touser" placeholder="Type or select user">
                                    <input type="hidden" id="touser_id" name="touser_id">
                                    <button class="btn btn-link search-touser-btn" type="button"
                                            style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col">
                                <label for="return" class="form-label">Return <span style="color: red;">*</span></label>
                                <select class="form-select" id="return" name="return">
                                    <option value="">--Select Return--</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="tsdate" class="form-label">Transaction Date <span style="color: red;">*</span></label>
                                <input type="date" class="form-control" id="tsdate" name="tsdate">
                            </div>
                            <div class="col">
                                <label for="purpose" class="form-label">Purpose <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="purpose" name="purpose" style="width: 100%;" 
                                    placeholder="Descripe the purpose">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="save">Submit Sparepart</button>
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
                    <h5 class="modal-title" id="editDetailDetModalLabel">Edit Sparepart Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form id="editDataForm">
                    <input type="hidden" id="edit_tea_id" name="id">
                    <div class="col-md-12">
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col">
                                <label for="edit_assetNo" class="form-label">Asset No <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_assetNo" name="assetNo" placeholder="Type asset no" readonly>
                                    <button class="btn btn-link search-edit-asset-btn" type="button"
                                            style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col">
                                <label for="edit_pcname" class="form-label">PC Name</label>
                                <input type="text" class="form-control" id="edit_pcname" name="pcname" placeholder="Auto-filled" readonly>
                            </div>
                            <div class="col">
                                <label for="edit_serial_number" class="form-label">Serial Number</label>
                                <input type="text" class="form-control" id="edit_serial_number" name="serialnumber" 
                                    placeholder="Auto-filled" readonly>
                            </div>
                            <div class="col">
                                <label for="edit_category" class="form-label">Category <span style="color: red;">*</span></label>
                                <select class="form-select" id="edit_category" name="category">
                                    <option value="">--Select Category--</option>
                                    <?php foreach ($cat as $mst): ?>
                                        <option value="<?= $mst->equipmentcat; ?>">
                                            <?= $mst->equipmentcat; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col">
                                <label for="edit_ipaddress" class="form-label">IP Address</label>
                                <input type="text" class="form-control" id="edit_ipaddress" name="ipaddress"
                                    pattern="^[0-9.]*$"
                                    onkeypress="return /[0-9.]/.test(event.key)"
                                    placeholder="Type IP Address"
                                    title="Only numbers and dots allowed">
                            </div>
                            <div class="col">
                                <label for="edit_fromlocation" class="form-label">From Location <span style="color: red;">*</span></label>
                                <select class="form-select" id="edit_fromlocation" name="fromlocation">
                                    <option value="">--Select Location--</option>
                                    <?php foreach ($sections as $section): ?>
                                        <option value="<?= $section->sec_section ?>" data-id="<?= $section->sec_sectioncode ?>">
                                            <?= $section->sec_section ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="edit_tolocation" class="form-label">To Location <span style="color: red;">*</span></label>
                                <select class="form-select" id="edit_tolocation" name="tolocation">
                                    <option value="">--Select Location--</option>
                                    <?php foreach ($sections as $section): ?>
                                        <option value="<?= $section->sec_section ?>" data-id="<?= $section->sec_sectioncode ?>">
                                            <?= $section->sec_section ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="edit_fromuser" class="form-label">From User <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_fromuser" name="fromuser" placeholder="Type or select user">
                                    <input type="hidden" id="edit_fromuser_id" name="fromuser_id">
                                    <button class="btn btn-link search-edit-fromuser-btn" type="button"
                                            style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col">
                                <label for="edit_touser" class="form-label">To User <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_touser" name="touser" placeholder="Type or select user">
                                    <input type="hidden" id="edit_touser_id" name="touser_id">
                                    <button class="btn btn-link search-edit-touser-btn" type="button"
                                            style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col">
                                <label for="edit_return" class="form-label">Return <span style="color: red;">*</span></label>
                                <select class="form-select" id="edit_return" name="return">
                                    <option value="">--Select Return--</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="edit_tsdate" class="form-label">Transaction Date <span style="color: red;">*</span></label>
                                <input type="date" class="form-control" id="edit_tsdate" name="tsdate">
                            </div>
                            <div class="col">
                                <label for="edit_purpose" class="form-label">Purpose <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="edit_purpose" name="purpose" style="width: 100%;"
                                    placeholder="Describe the purpose">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="update">Update Sparepart</button>
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
                    <h5 class="modal-title" id="assetNoModalLabel">Select Asset No</h5>
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
                                <th style="width: 20%;">Asset No</th>
                                <th style="width: 20%;">Equipment ID</th>
                                <th style="width: 30%;">Serial Number</th>
                                <th style="width: 30%;">Equipment Name</th>
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
    
    <!-- From User Modal -->
    <div class="modal fade" id="fromUserModal" tabindex="-1" aria-labelledby="fromUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fromUserModalLabel">Select From User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="fromUserLength" class="form-label mb-0">Show</label>
                        </div>
                        <div class="col-auto">
                            <select id="fromUserLength" class="form-select form-select-sm">
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
                                <input type="text" class="form-control" id="searchFromUser">
                            </div>
                        </div>
                    </div>
                    <table id="fromUserTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Employee ID</th>
                                <th style="width: 30%;">Name</th>
                                <th style="width: 25%;">Position</th>
                                <th style="width: 35%;">Section</th>
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
    
    <!-- To User Modal -->
    <div class="modal fade" id="toUserModal" tabindex="-1" aria-labelledby="toUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="toUserModalLabel">Select To User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="toUserLength" class="form-label mb-0">Show</label>
                        </div>
                        <div class="col-auto">
                            <select id="toUserLength" class="form-select form-select-sm">
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
                                <input type="text" class="form-control" id="searchToUser">
                            </div>
                        </div>
                    </div>
                    <table id="toUserTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Employee ID</th>
                                <th style="width: 30%;">Name</th>
                                <th style="width: 25%;">Position</th>
                                <th style="width: 35%;">Section</th>
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
            let base_url = '<?= base_url() ?>';
            let assetNumberList = []; // Store all asset numbers but not for validation
            let modalSource = ''; // To track which form is opening the asset modal
            // Variables to track modal state and save form data
            let previousModal = null;
            let isSearchModalOpen = false;
            let savedFormData = {};

           

            // Function to restore form data after closing search modal
            function restoreFormData(formData) {
                if (previousModal === '#addDetailDetModal') {
                    $('#assetNo').val(formData.assetNo || '');
                    $('#pc_id').val(formData.pc_id || ''); // <-- Tambahkan ini
                    $('#category').val(formData.category || '');
                    $('#pcname').val(formData.pcname || ''); 
                    $('#ipaddress').val(formData.ipaddress || '');
                    $('#fromlocation').val(formData.fromlocation || '');
                    $('#tolocation').val(formData.tolocation || '');
                    $('#fromuser').val(formData.fromuser || '');
                    $('#fromuser_id').val(formData.fromuser_id || '');
                    $('#touser').val(formData.touser || '');
                    $('#touser_id').val(formData.touser_id || '');
                    $('#serial_number').val(formData.serial_number || ''); 
                    $('#return').val(formData.return || '');
                    $('#tsdate').val(formData.tsdate || '');
                    $('#purpose').val(formData.purpose || '');
                } else if (previousModal === '#editDetailDetModal') {
                    $('#edit_tea_id').val(formData.edit_tea_id || '');
                    $('#edit_assetNo').val(formData.edit_assetNo || '');
                    $('#edit_pc_id').val(formData.edit_pc_id || ''); // <-- Tambahkan ini
                    $('#edit_category').val(formData.edit_category || '');
                    $('#edit_pcname').val(formData.edit_pcname || ''); 
                    $('#edit_ipaddress').val(formData.edit_ipaddress || '');
                    $('#edit_fromlocation').val(formData.edit_fromlocation || '');
                    $('#edit_tolocation').val(formData.edit_tolocation || '');
                    $('#edit_fromuser').val(formData.edit_fromuser || '');
                    $('#edit_fromuser_id').val(formData.edit_fromuser_id || '');
                    $('#edit_touser').val(formData.edit_touser || '');
                    $('#edit_touser_id').val(formData.edit_touser_id || '');
                    $('#edit_serial_number').val(formData.edit_serial_number || ''); 
                    $('#edit_return').val(formData.edit_return || '');
                    $('#edit_tsdate').val(formData.edit_tsdate || '');
                    $('#edit_purpose').val(formData.edit_purpose || '');
                }
            }
            
            // Add this custom CSS for validation
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
                    
                    /* Jika Anda masih memiliki input type=number lain yang membutuhkan ini, biarkan. */
                    /* Jika tidak, bisa dihapus atau hanya simpan yang relevan */
                    /* Contoh: jika hanya ingin menyembunyikan spinner untuk input Max Port saja: */
                    .no-spinner::-webkit-outer-spin-button,
                    .no-spinner::-webkit-inner-spin-button {
                        -webkit-appearance: none;
                        margin: 0;
                    }

                    .no-spinner[type=number] {
                        -moz-appearance: textfield;
                        appearance: textfield; /* Tambahkan ini agar bekerja di browser modern */
                    }
                </style>
            `);
            
            // Set today's date as default for Transaction Date fields
            const today = new Date().toISOString().split('T')[0];
            $('#tsdate').val(today);
            
            // Function to get field label text
            function getFieldLabel(fieldId) {
                // Find the label for this field
                const labelElement = document.querySelector(`label[for="${fieldId}"]`);
                if (labelElement) {
                    // Get the text content and remove the asterisk and any surrounding whitespace
                    return labelElement.textContent.replace(/\s*\*\s*$/, '').trim();
                }
                
                // Default fallback labels for fields that might not have a direct label
                const fieldLabels = {
                    'assetNo': 'Asset No',
                    'category': 'Category',
                    'pcname': 'PC Name',
                    'ipaddress': 'IP Address',
                    'fromlocation': 'From Location',
                    'tolocation': 'To Location',
                    'fromuser': 'From User',
                    'touser': 'To User',
                    'tsdate': 'Transaction Date',
                    'serial_number': 'Serial Number',
                    'purpose': 'Purpose',
                    'return': 'Return'
                };
                
                // Remove "edit_" prefix if present
                const baseFieldId = fieldId.replace('edit_', '');
                return fieldLabels[baseFieldId] || 'This field';
            }

            // Function to handle the removal of the old "Equipment ID" display
            // This function will now simply remove the existing display if it exists.
            function cleanupEquipmentIDDisplay(isEditMode) {
                const equipmentDisplayId = isEditMode ? 'edit_equipment_id_display' : 'equipment_id_display';
                $(`#${equipmentDisplayId}`).remove(); // Ensure any old display is removed
            }

            function checkAssetNumberExists(assetNo, formPrefix = '') {
                return $.ajax({
                    url: base_url + 'TransSparepart/getData',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response) {
                            const exists = response.some(item => item.tea_assetno == assetNo);
                            return exists;
                        }
                        return false;
                    },
                    error: function() {
                        return false;
                    }
                });
            }
            
            // Function to validate form - includes from/to user validation
            function validateForm(formId) {
                let isValid = true;
                const form = document.getElementById(formId);
                const requiredFields = form.querySelectorAll('[required]');
                const prefix = formId === 'addDataForm' ? '' : 'edit_';

                // Cek semua field yang wajib diisi
                requiredFields.forEach(field => {
                    const id = field.id;
                    const fieldId = prefix + id;
                    const value = $(`#${fieldId}`).val().trim();

                    if (!value) {
                        $(`#${fieldId}`).addClass('is-invalid');
                        const parent = $(`#${fieldId}`).closest('.input-group').parent();

                        if (!parent.find('.error-message').length) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-danger mt-1';
                            errorDiv.textContent = 'This field is required';
                            parent.append(errorDiv);
                        }

                        isValid = false;
                    } else {
                        $(`#${fieldId}`).removeClass('is-invalid');
                        const parent = $(`#${fieldId}`).closest('.input-group').parent();
                        const errorDiv = parent.find('.error-message');
                        if (errorDiv.length) {
                            errorDiv.remove();
                        }
                    }
                });

                // Cek duplikasi Asset Number untuk form Add
                if (formId === 'addDataForm') {
                    const assetNo = $('#assetNo').val();
                    const fromUser = $('#fromuser').val();
                    const toUser = $('#touser').val();

                    // Cek duplikasi di server via AJAX
                    checkAssetNumberExists(assetNo).then(exists => {
                        if (exists) {
                            $('#assetNo').addClass('is-invalid');
                            const parent = $('#assetNo').closest('.input-group').parent();
                            if (!parent.find('.error-message').length) {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'error-message text-danger mt-1';
                                errorDiv.textContent = 'Asset number already exists';
                                parent.append(errorDiv);
                            }
                            isValid = false;
                        }
                    });

                    // Cek apakah From User dan To User sama
                    if (fromUser && toUser && fromUser === toUser) {
                        $('#touser').addClass('is-invalid');
                        $('#fromuser').addClass('is-invalid');
                        const parent = $('#touser').closest('.input-group').parent();
                        const parentFrom = $('#fromuser').closest('.input-group').parent();

                        if (!parent.find('.error-message').length) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-danger mt-1';
                            errorDiv.textContent = 'From User and To User cannot be the same';
                            parent.append(errorDiv);
                        }

                        if (!parentFrom.find('.error-message').length) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-danger mt-1';
                            errorDiv.textContent = 'From User and To User cannot be the same';
                            parentFrom.append(errorDiv);
                        }

                        isValid = false;
                    }
                }

                return isValid;
            }
            
            var table =$("#tabelEmovement").DataTable({
                pageLength: 10,
                order: [],
                autoWidth: false,
                ajax: {
                    url: base_url + "TransSparepart/getData",
                    dataSrc: function(json) {
                        // Store asset numbers but not for validation
                        assetNumberList = json.map(item => item.tea_assetno);
                        return json;
                    },
                    beforeSend: function() {
                        let spinner = `
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                            </div>`;
                        $('#tabelEmovement tbody').html(`<tr><td colspan="14">${spinner}</td></tr>`);
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error, thrown);
                        $('#tabelEmovement tbody').html('<tr><td colspan="14" class="text-center">Error loading data. Please try again.</td></tr>');
                    }
                },
                columns: [
                    {
                         data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center gap-2">
                                       <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.tea_id}">
                                        <i class="fa fa-pen-to-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.tea_id}">
                                        <i class="fa fa-trash-can"></i>
                                    </a>
                                </div>
                            `;
                        }
                    },
                    { data: 'tea_id' },
                    { data: 'tea_assetno' },
                    {
                        data: 'tea_serialnumber',
                        render: function(data, type, row) {
                            return data ? data : '-';
                        }
                    },
                    {
                         data: 'tea_transactiondate',
                        render: function(data, type, row) {
                            // For ordering and type detection, return the original data
                            if (type === 'sort' || type === 'type') {
                                return data;
                            }
                            
                            // If data is empty or null, return empty string
                            if (!data) {
                                return '';
                            }
                            
                            // Parse the date and format it as dd/mm/yyyy
                            const date = new Date(data);
                            if (isNaN(date)) {
                                return data; // Return original if date is invalid
                            }
                            
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0'); // +1 because getMonth() returns 0-11
                            const year = date.getFullYear();
                            
                            return `${day}/${month}/${year}`;
                        }
                    },
                    {
                        data: 'tea_pcname',
                        render: function(data, type, row) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'tea_ipaddress',
                        render: function(data, type, row) {
                            return data ? data : '-';
                        }
                    },
                    { data: 'tea_fromlocation' },
                    { data: 'tea_tolocation' },
                    { data: 'tea_fromuser' },
                    { data: 'tea_touser' },
                    { data: 'tea_category' },
                    {
                         data: 'tea_returnoldequip',
                        render: function(data, type, row) {
                            if (data === null) {
                                return '';
                             }
                            return data == 1 ? 'Yes' : 'No';
                        }
                    },
                    { data: 'tea_purpose' }
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
            
            var tblAssetNo;
            
            // Function to load Asset No data with filter
            function loadAssetNoData(search = '') {
                if ($.fn.DataTable.isDataTable('#assetNoTable')) {
                    tblAssetNo.clear().destroy();
                    // Penting: Hapus konten tbody agar tidak ada spinner lama menumpuk
                    $('#assetNoTable tbody').html('');
                    // Memastikan thead bersih dan siap sebelum inisialisasi ulang
                    $('#assetNoTable thead').empty().append(`
                        <tr>
                            <th style="width: 20%;">Asset No</th>
                            <th style="width: 20%;">Equipment ID</th>
                            <th style="width: 30%;">Serial Number</th>
                            <th style="width: 30%;">Equipment Name</th>
                        </tr>
                    `);
                }

                tblAssetNo = $("#assetNoTable").DataTable({
                    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                    pageLength: 5,
                    processing: true,
                    serverSide: false, // <--- UBAH INI MENJADI FALSE
                    order: [[0, 'asc']], // Default order by Asset No
                    autoWidth: false,
                    dom: 't<"bottom"ip>', // Hanya tampilkan tabel, info, dan paginasi
                    ajax: {
                        url: base_url + "TransSparepart/searchAssetNo",
                        type: 'GET',
                        // Ketika serverSide: false, DataTables akan menambahkan parameter search secara otomatis.
                        // Fungsi data: ini hanya perlu jika Anda punya parameter kustom lain,
                        // atau ingin mengirim nilai search dari input manual yang terpisah dari DataTables default search box.
                        // Untuk saat ini, bisa dihilangkan atau biarkan kosong jika DataTables default search digunakan.
                        // Contoh jika ingin mengirim nilai search dari input searchAssetNo
                        data: function(d) {
                            d.search = $('#searchAssetNo').val(); // Mengirim nilai input search ke backend
                        },
                        dataSrc: function(json) {
                            // SANGAT PENTING UNTUK DEBUGGING: log data mentah
                            console.log("Raw JSON received by DataTables (Asset No Modal):", json);
                            
                            // Jika serverSide: false, model harus mengembalikan array data langsung
                            return json || []; 
                        },
                        beforeSend: function() {
                            let spinner = `
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                                </div>`;
                            $('#assetNoTable tbody').html(`<tr><td colspan="4">${spinner}</td></tr>`);
                        },
                        error: function(xhr, error, thrown) {
                            console.error('Asset No DataTables AJAX error (frontend):', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                responseText: xhr.responseText,
                                error: error,
                                thrown: thrown
                            });
                            let errorMessage = 'Error loading data. Please try again.';
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response && response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) { /* fallback to generic error message */ }
                            $('#assetNoTable tbody').html(`<tr><td colspan="4" class="text-center text-danger">${errorMessage}</td></tr>`);
                        }
                    },
                    columns: [
                        // Pastikan 'data' key di sini cocok dengan nama kolom yang dikembalikan oleh model
                        { data: 'e_assetno' }, // Pastikan ini ada di hasil query
                        { data: 'e_equipmentid' }, // Pastikan ini ada di hasil query
                        { data: 'e_serialnumber' }, // Pastikan ini ada di hasil query
                        { data: 'e_equipmentname' } // Pastikan ini ada di hasil query
                    ],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    initComplete: function() {
                        const api = this.api();
                        // Custom search box for assetTable (re-bind to ensure it works)
                        $('#searchAssetNo').off('keyup change').on('keyup change', function() {
                            // Memanggil fungsi pencarian DataTables
                            api.search(this.value).draw();
                        });
                    }
                });
            }
            
            // For Asset Number field - Allow manual input with auto-completion
            $('#assetNo').off('input').on('input', function() {
                const assetNo = $(this).val().trim();
                
                // Clear equipment ID display first
                $('#equipment_id_display').remove();
                
                // If asset number is empty, clear serial number field only
                if (!assetNo) {
                    $('#serial_number').val('');
                    return;
                }
                
                // Only proceed if there's a valid asset number entered
                if (!isNaN(assetNo)) {
                    // Make AJAX request to get asset details by asset number
                    $.ajax({
                        url: base_url + "TransSparepart/getAssetNo",
                        type: 'GET',
                        data: { assetNo: assetNo },
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.status) {
                                // Auto-fill serial number field
                                $('#serial_number').val(response.data.e_serialnumber || '');
                                
                                // Display equipment ID
                                displayEquipmentID(response.data, false);
                                
                                // Remove validation errors if any
                                $('#serial_number').removeClass('is-invalid');
                                const serialErrorMessage = $('#serial_number').parent().find('.error-message');
                                if (serialErrorMessage.length) {
                                    serialErrorMessage.remove();
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching asset details:', error);
                        }
                    });
                }
            });
            
            // For Edit Asset Number field - Allow manual input with auto-completion
            $('#edit_assetNo').off('input').on('input', function() {
                const assetNo = $(this).val().trim();
                
                // Clear equipment ID display first
                $('#edit_equipment_id_display').remove();
                
                // If asset number is empty, clear serial number field only
                if (!assetNo) {
                    $('#edit_serial_number').val('');
                    return;
                }
                
                // Only proceed if there's a valid asset number entered
                if (!isNaN(assetNo)) {
                    // Make AJAX request to get asset details by asset number
                    $.ajax({
                        url: base_url + "TransSparepart/getAssetNo",
                        type: 'GET',
                        data: { assetNo: assetNo },
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.status) {
                                // Auto-fill serial number field
                                $('#edit_serial_number').val(response.data.e_serialnumber || '');
                                
                                // Display equipment ID
                                displayEquipmentID(response.data, true);
                                
                                // Remove validation errors if any
                                $('#edit_serial_number').removeClass('is-invalid');
                                const serialErrorMessage = $('#edit_serial_number').parent().find('.error-message');
                                if (serialErrorMessage.length) {
                                    serialErrorMessage.remove();
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching asset details:', error);
                        }
                    });
                }
            });
            
            
            
            // User search functionality - variables
            var tblFromUser, tblToUser;
            
            // Function to load user data with filter
            function loadUserData(tableId, excludeUserField) {
                const table = $(tableId).DataTable();
                const excludeUser = $(excludeUserField).val();
                
                // Show loading indicator
                $(tableId + ' tbody').html(`
                    <tr>
                        <td colspan="4" class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                            </div>
                        </td>
                    </tr>
                `);
                
                // Make AJAX request
                $.ajax({
                    url: base_url + 'TransSparepart/searchEmployees',
                    type: 'GET',
                    data: {
                         search: $(tableId === '#fromUserTable' ? '#searchFromUser' : '#searchToUser').val() || '',
                        exclude: excludeUser
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Clear existing data
                        table.clear();
                        
                        // Add new data
                        if (response && response.length > 0) {
                            // Filter out the excluded user
                            const filteredResponse = excludeUser
                                 ? response.filter(employee => employee.em_emplname !== excludeUser)
                                : response;
                            
                            if (filteredResponse.length > 0) {
                                filteredResponse.forEach(function(employee) {
                                    table.row.add([
                                        employee.em_emplcode,
                                        employee.em_emplname,
                                        employee.pm_positionname || '',
                                        employee.sec_section || ''
                                    ]);
                                });
                                // Draw the table
                                table.draw();
                            } else {
                                $(tableId + ' tbody').html(`
                                    <tr>
                                        <td colspan="4" class="text-center">No available employees</td>
                                    </tr>
                                `);
                            }
                        } else {
                            // No results found
                            $(tableId + ' tbody').html(`
                                <tr>
                                    <td colspan="4" class="text-center">No employees found</td>
                                </tr>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error searching employees:', xhr.responseText);
                        $(tableId + ' tbody').html(`
                            <tr>
                                <td colspan="4" class="text-center">Error searching employees. Please try again.</td>
                            </tr>
                        `);
                    }
                });
            }
            
            // For From User field - Allow manual input with auto-completion
            $('#fromuser').on('input', function() {
                const employeeId = $(this).val().trim();
                
                // Only proceed if there's a valid employee ID entered
                if (employeeId && !isNaN(employeeId)) {
                    // Make AJAX request to get employee details by ID
                    $.ajax({
                        url: base_url + "TransSparepart/getEmployees",
                        type: 'GET',
                        data: { employeeId: employeeId },
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.status) {
                                // Auto-fill the employee name
                                $('#fromuser').val(response.data.em_emplname || '');
                                $('#fromuser_id').val(response.data.em_emplcode || '');
                                
                                // Remove validation errors if any
                                $('#fromuser').removeClass('is-invalid');
                                const userErrorMessage = $('#fromuser').closest('.input-group').parent().find('.error-message');
                                if (userErrorMessage.length) {
                                    userErrorMessage.remove();
                                }
                                
                                // Check if From User and To User are the same
                                if ($('#touser').val() === response.data.em_emplname) {
                                    // Show validation error
                                    $('#fromuser').addClass('is-invalid');
                                    
                                    // Find the correct parent for the error message
                                    let parent = $('#fromuser').closest('.input-group').parent();
                                    
                                    // Create error message
                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'error-message text-danger mt-1';
                                    errorDiv.textContent = 'From User and To User cannot be the same';
                                    
                                    // Insert error message
                                    parent.append(errorDiv);
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching employee details:', error);
                        }
                    });
                }
            });
            
            // For To User field - Allow manual input with auto-completion
            $('#touser').on('input', function() {
                const employeeId = $(this).val().trim();
                
                // Only proceed if there's a valid employee ID entered
                if (employeeId && !isNaN(employeeId)) {
                    // Make AJAX request to get employee details by ID
                    $.ajax({
                        url: base_url + "TransSparepart/getEmployees",
                        type: 'GET',
                        data: { employeeId: employeeId },
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.status) {
                                // Auto-fill the employee name
                                $('#touser').val(response.data.em_emplname || '');
                                $('#touser_id').val(response.data.em_emplcode || '');
                                
                                // Remove validation errors if any
                                $('#touser').removeClass('is-invalid');
                                const userErrorMessage = $('#touser').closest('.input-group').parent().find('.error-message');
                                if (userErrorMessage.length) {
                                    userErrorMessage.remove();
                                }
                                
                                // Check if From User and To User are the same
                                if ($('#fromuser').val() === response.data.em_emplname) {
                                    // Show validation error
                                    $('#touser').addClass('is-invalid');
                                    
                                    // Find the correct parent for the error message
                                    let parent = $('#touser').closest('.input-group').parent();
                                    
                                    // Create error message
                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'error-message text-danger mt-1';
                                    errorDiv.textContent = 'From User and To User cannot be the same';
                                    
                                    // Insert error message
                                    parent.append(errorDiv);
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching employee details:', error);
                        }
                    });
                }
            });
            
            // Also add the same functionality for edit form fields
            $('#edit_fromuser').on('input', function() {
                const employeeId = $(this).val().trim();
                
                if (employeeId && !isNaN(employeeId)) {
                    $.ajax({
                        url: base_url + "TransSparepart/getEmployees",
                        type: 'GET',
                        data: { employeeId: employeeId },
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.status) {
                                $('#edit_fromuser').val(response.data.em_emplname || '');
                                $('#edit_fromuser_id').val(response.data.em_emplcode || '');
                                
                                $('#edit_fromuser').removeClass('is-invalid');
                                const userErrorMessage = $('#edit_fromuser').closest('.input-group').parent().find('.error-message');
                                if (userErrorMessage.length) {
                                    userErrorMessage.remove();
                                }
                                
                                if ($('#edit_touser').val() === response.data.em_emplname) {
                                    $('#edit_fromuser').addClass('is-invalid');
                                    let parent = $('#edit_fromuser').closest('.input-group').parent();
                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'error-message text-danger mt-1';
                                    errorDiv.textContent = 'From User and To User cannot be the same';
                                    parent.append(errorDiv);
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching employee details:', error);
                        }
                    });
                }
            });
            
            $('#edit_touser').on('input', function() {
                const employeeId = $(this).val().trim();
                
                if (employeeId && !isNaN(employeeId)) {
                    $.ajax({
                        url: base_url + "TransSparepart/getEmployees",
                        type: 'GET',
                        data: { employeeId: employeeId },
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.status) {
                                $('#edit_touser').val(response.data.em_emplname || '');
                                $('#edit_touser_id').val(response.data.em_emplcode || '');
                                
                                $('#edit_touser').removeClass('is-invalid');
                                const userErrorMessage = $('#edit_touser').closest('.input-group').parent().find('.error-message');
                                if (userErrorMessage.length) {
                                    userErrorMessage.remove();
                                }
                                
                                if ($('#edit_fromuser').val() === response.data.em_emplname) {
                                    $('#edit_touser').addClass('is-invalid');
                                    let parent = $('#edit_touser').closest('.input-group').parent();
                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'error-message text-danger mt-1';
                                    errorDiv.textContent = 'From User and To User cannot be the same';
                                    parent.append(errorDiv);
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching employee details:', error);
                        }
                    });
                }
            });
                       
            // From User modal setup
            $('.search-fromuser-btn, .search-edit-fromuser-btn').on('click', function() {
                // Track which form opened the modal
                modalSource = $(this).hasClass('search-edit-fromuser-btn') ? 'edit' : 'add';
                isSearchModalOpen = true;
                
                // Store which modal was open before search
                if ($('#addDetailDetModal').hasClass('show')) {
                    previousModal = '#addDetailDetModal';
                } else if ($('#editDetailDetModal').hasClass('show')) {
                    previousModal = '#editDetailDetModal';
                }
                
                // Save current form data before hiding modal
                if (previousModal) {
                    savedFormData = saveFormData();
                    
                    // Hide the current modal
                    $(previousModal).modal('hide');
                    
                    // Wait for the modal to be completely hidden before showing the search modal
                    $(previousModal).one('hidden.bs.modal', function() {
                        $('#fromUserModal').modal('show');
                    });
                } else {
                    $('#fromUserModal').modal('show');
                }
});
            
            $('#fromUserModal').on('shown.bs.modal', function() {
                if ($.fn.DataTable.isDataTable('#fromUserTable')) {
                    tblFromUser.clear().destroy();
                }
                
                tblFromUser = $("#fromUserTable").DataTable({
                    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                    pageLength: 5,
                    processing: true,
                    order: [],
                    autoWidth: false,
                    dom: 't<"bottom"ip>',
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    columns: [
                        { width: '10%' },
                        { width: '30%' },
                        { width: '25%' },
                        { width: '35%' }
                    ]
                });
                
                // Load initial user data
                if (modalSource === 'edit') {
                    loadUserData('#fromUserTable', '#edit_touser');
                } else {
                    loadUserData('#fromUserTable', '#touser');
                }
                
                // Set up length change
                $('#fromUserLength').on('change', function() {
                    const newLength = $(this).val();
                    tblFromUser.page.len(newLength).draw();
                });
                
                // Set up search with debounce
                let searchTimeout;
                $('#searchFromUser').on('keyup', function() {
                    clearTimeout(searchTimeout);
                    const searchValue = this.value;
                    
                    searchTimeout = setTimeout(function() {
                        // Make AJAX request with search term
                        $.ajax({
                            url: base_url + 'TransSparepart/searchEmployees',
                            type: 'GET',
                            data: {
                                 search: searchValue,
                                exclude: modalSource === 'edit' ? $('#edit_touser').val() : $('#touser').val()
                            },
                            dataType: 'json',
                            success: function(response) {
                                // Clear existing data
                                tblFromUser.clear();
                                
                                // Add new data
                                if (response && response.length > 0) {
                                    response.forEach(function(employee) {
                                        tblFromUser.row.add([
                                            employee.em_emplcode,
                                            employee.em_emplname,
                                            employee.pm_positionname || '',
                                            employee.sec_section || ''
                                        ]);
                                    });
                                    // Draw the table
                                    tblFromUser.draw();
                                } else {
                                    $('#fromUserTable tbody').html(`
                                        <tr>
                                            <td colspan="4" class="text-center">No employees found</td>
                                        </tr>
                                    `);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error searching employees:', xhr.responseText);
                                $('#fromUserTable tbody').html(`
                                    <tr>
                                        <td colspan="4" class="text-center">Error searching employees. Please try again.</td>
                                    </tr>
                                `);
                            }
                        });
                    }, 300);
                });
                
                // Handle row click to select user
               $('#fromUserTable tbody').on('click', 'tr', function() {
                    const data = tblFromUser.row(this).data();
                    if (!data) return;
                    
                    const employeeId = data[0];
                    const employeeName = data[1];
                    
                    // Update saved form data with selected from user
                    if (modalSource === 'edit') {
                        savedFormData.edit_fromuser = employeeName;
                        savedFormData.edit_fromuser_id = employeeId;
                    } else {
                        savedFormData.fromuser = employeeName;
                        savedFormData.fromuser_id = employeeId;
                    }
                    
                    // Hide from user modal and show previous modal
                    $('#fromUserModal').modal('hide');
                    
                    // Show the previous modal after from user modal is hidden
                    if (previousModal) {
                        $('#fromUserModal').one('hidden.bs.modal', function() {
                            $(previousModal).modal('show');
                            
                            // Restore form data after modal is shown
                            setTimeout(function() {
                                restoreFormData(savedFormData);
                                
                                // Handle validation for same user check
                                const prefix = modalSource === 'edit' ? 'edit_' : '';
                                const fromUserField = $(`#${prefix}fromuser`);
                                const toUserField = $(`#${prefix}touser`);
                                
                                // Check if From User and To User are the same
                                if (fromUserField.val() === toUserField.val() && fromUserField.val() !== '') {
                                    // Show validation error
                                    fromUserField.addClass('is-invalid');
                                    
                                    // Find the correct parent for the error message
                                    let parent = fromUserField.closest('.input-group').parent();
                                    
                                    // Create error message if it doesn't exist
                                    if (!parent.find('.error-message').length) {
                                        const errorDiv = document.createElement('div');
                                        errorDiv.className = 'error-message text-danger mt-1';
                                        errorDiv.textContent = 'From User and To User cannot be the same';
                                        parent.append(errorDiv);
                                    }
                                } else {
                                    // Remove validation errors if any
                                    fromUserField.removeClass('is-invalid');
                                    const userErrorMessage = fromUserField.closest('.input-group').parent().find('.error-message');
                                    if (userErrorMessage.length) {
                                        userErrorMessage.remove();
                                    }
                                }
                                
                                isSearchModalOpen = false;
                                previousModal = null;
                                savedFormData = {};
                            }, 100);
                        });
                    }
                });
            });
            
            // To User modal setup
            $('.search-touser-btn, .search-edit-touser-btn').on('click', function() {
                // Track which form opened the modal
                modalSource = $(this).hasClass('search-edit-touser-btn') ? 'edit' : 'add';
                isSearchModalOpen = true;
                
                // Store which modal was open before search
                if ($('#addDetailDetModal').hasClass('show')) {
                    previousModal = '#addDetailDetModal';
                } else if ($('#editDetailDetModal').hasClass('show')) {
                    previousModal = '#editDetailDetModal';
                }
                
                // Save current form data before hiding modal
                if (previousModal) {
                    savedFormData = saveFormData();
                    
                    // Hide the current modal
                    $(previousModal).modal('hide');
                    
                    // Wait for the modal to be completely hidden before showing the search modal
                    $(previousModal).one('hidden.bs.modal', function() {
                        $('#toUserModal').modal('show');
                    });
                } else {
                    $('#toUserModal').modal('show');
                }
            });
            
            $('#toUserModal').on('shown.bs.modal', function() {
                if ($.fn.DataTable.isDataTable('#toUserTable')) {
                    tblToUser.clear().destroy();
                }
                
                tblToUser = $("#toUserTable").DataTable({
                    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                    pageLength: 5,
                    processing: true,
                    order: [],
                    autoWidth: false,
                    dom: 't<"bottom"ip>',
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    columns: [
                        { width: '10%' },
                        { width: '30%' },
                        { width: '25%' },
                        { width: '35%' }
                    ]
                });
                
                // Load initial user data - based on which form opened the modal
                if (modalSource === 'edit') {
                    loadUserData('#toUserTable', '#edit_fromuser');
                } else {
                    loadUserData('#toUserTable', '#fromuser');
                }
                
                // Set up length change
                $('#toUserLength').on('change', function() {
                    const newLength = $(this).val();
                    tblToUser.page.len(newLength).draw();
                });
                
                // Set up search with debounce
                let searchTimeout;
                $('#searchToUser').on('keyup', function() {
                    clearTimeout(searchTimeout);
                    const searchValue = this.value;
                    
                    searchTimeout = setTimeout(function() {
                        // Make AJAX request with search term
                        $.ajax({
                            url: base_url + 'TransSparepart/searchEmployees',
                            type: 'GET',
                            data: {
                                 search: searchValue,
                                exclude: modalSource === 'edit' ? $('#edit_fromuser').val() : $('#fromuser').val()
                            },
                            dataType: 'json',
                            success: function(response) {
                                // Clear existing data
                                tblToUser.clear();
                                
                                // Add new data
                                if (response && response.length > 0) {
                                    response.forEach(function(employee) {
                                        tblToUser.row.add([
                                            employee.em_emplcode,
                                            employee.em_emplname,
                                            employee.pm_positionname || '',
                                            employee.sec_section || ''
                                        ]);
                                    });
                                    // Draw the table
                                    tblToUser.draw();
                                } else {
                                    $('#toUserTable tbody').html(`
                                        <tr>
                                            <td colspan="4" class="text-center">No employees found</td>
                                        </tr>
                                    `);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error searching employees:', xhr.responseText);
                                $('#toUserTable tbody').html(`
                                    <tr>
                                        <td colspan="4" class="text-center">Error searching employees. Please try again.</td>
                                    </tr>
                                `);
                            }
                        });
                    }, 300);
                });
                
                // Handle row click to select user
                $('#toUserTable tbody').on('click', 'tr', function() {
                    const data = tblToUser.row(this).data();
                    if (!data) return;
                    
                    const employeeId = data[0];
                    const employeeName = data[1];
                    
                    // Update saved form data with selected to user
                    if (modalSource === 'edit') {
                        savedFormData.edit_touser = employeeName;
                        savedFormData.edit_touser_id = employeeId;
                    } else {
                        savedFormData.touser = employeeName;
                        savedFormData.touser_id = employeeId;
                    }
                    
                    // Hide to user modal and show previous modal
                    $('#toUserModal').modal('hide');
                    
                    // Show the previous modal after to user modal is hidden
                    if (previousModal) {
                        $('#toUserModal').one('hidden.bs.modal', function() {
                            $(previousModal).modal('show');
                            
                            // Restore form data after modal is shown
                            setTimeout(function() {
                                restoreFormData(savedFormData);
                                
                                // Handle validation for same user check
                                const prefix = modalSource === 'edit' ? 'edit_' : '';
                                const fromUserField = $(`#${prefix}fromuser`);
                                const toUserField = $(`#${prefix}touser`);
                                
                                // Check if From User and To User are the same
                                if (fromUserField.val() === toUserField.val() && toUserField.val() !== '') {
                                    // Show validation error
                                    toUserField.addClass('is-invalid');
                                    
                                    // Find the correct parent for the error message
                                    let parent = toUserField.closest('.input-group').parent();
                                    
                                    // Create error message if it doesn't exist
                                    if (!parent.find('.error-message').length) {
                                        const errorDiv = document.createElement('div');
                                        errorDiv.className = 'error-message text-danger mt-1';
                                        errorDiv.textContent = 'From User and To User cannot be the same';
                                        parent.append(errorDiv);
                                    }
                                } else {
                                    // Remove validation errors if any
                                    toUserField.removeClass('is-invalid');
                                    const userErrorMessage = toUserField.closest('.input-group').parent().find('.error-message');
                                    if (userErrorMessage.length) {
                                        userErrorMessage.remove();
                                    }
                                }
                                
                                isSearchModalOpen = false;
                                previousModal = null;
                                savedFormData = {};
                            }, 100);
                        });
                    }
                });
            });
            
            // Add form submission
            $('#addDataForm').on('submit', function(e) {
                e.preventDefault();

                // Validate the form first
                if (!validateForm('addDataForm')) {
                    return false;
                }

                // Create form data object
                var formData = $(this).serialize();

                // Send AJAX request
                $.ajax({
                    url: base_url + 'TransSparepart/store',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        // Disable the submit button and show loading state
                        $('#save').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

                        // Clear any previous error messages from the assetNo field
                        $('#assetNo').removeClass('is-invalid');
                        const assetErrorMessage = $('#assetNo').closest('.input-group').parent().find('.error-message');
                        if (assetErrorMessage.length) {
                            assetErrorMessage.remove();
                        }
                    },
                    success: function(response) {
                        if (response.status) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                // Reset form and close modal
                                $('#addDataForm')[0].reset();
                                $('#addDetailDetModal').modal('hide');

                                // Refresh the datatable
                                table.ajax.reload();
                            });
                        } else {
                            // Show error message
                            if (response.message === 'Asset number already exists') {
                                // Mark the assetNo field as invalid
                                $('#assetNo').addClass('is-invalid');

                                // Find the correct parent for the error message
                                let parent = $('#assetNo').closest('.input-group').parent();

                                // Create error message if it doesn't exist
                                if (!parent.find('.error-message').length) {
                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'error-message text-danger mt-1';
                                    errorDiv.textContent = 'This asset number already exists';
                                    parent.append(errorDiv);
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to save data',
                                });
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error submitting form:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while saving data. Please try again.',
                        });
                    },
                    complete: function() {
                        // Re-enable the submit button and restore text
                        $('#save').prop('disabled', false).text('Submit Sparepart');
                    }
                });
            });
            
            // Edit button click handler
            $('#tabelEmovement').off('click', '.edit-btn').on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                
                // Clear equipment ID display first
                $('#edit_equipment_id_display').remove();
                
                // Send AJAX request to get data
                $.ajax({
                    url: base_url + 'TransSparepart/getSparepartById',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        
                        if (response.status) {
                            const data = response.data;
                            
                            // Populate form fields
                            $('#edit_tea_id').val(data.tea_id);
                            $('#edit_assetNo').val(data.tea_assetno);
                            $('#edit_category').val(data.tea_category);
                            $('#edit_pcname').val(data.tea_pcname);
                            $('#edit_ipaddress').val(data.tea_ipaddress);
                            $('#edit_fromlocation').val(data.tea_fromlocation);
                            $('#edit_tolocation').val(data.tea_tolocation);
                            $('#edit_fromuser').val(data.tea_fromuser);
                            $('#edit_touser').val(data.tea_touser);
                            $('#edit_serial_number').val(data.tea_serialnumber);
                            $('#edit_return').val(data.tea_returnoldequip);
                            $('#edit_purpose').val(data.tea_purpose);
                            
                            // If asset no exists, fetch and display equipment ID
                            if (data.tea_assetno) {
                                $.ajax({
                                    url: base_url + 'TransSparepart/getAssetNo',
                                    type: 'GET',
                                    data: { assetNo: data.tea_assetno },
                                    dataType: 'json',
                                    success: function(assetResponse) {
                                        if (assetResponse.status && assetResponse.data && assetResponse.data.e_equipmentid) {
                                            displayEquipmentID(assetResponse.data, true);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error fetching asset details for equipment ID:', error);
                                    }
                                });
                            }
                            
                            // Format date for input field (YYYY-MM-DD)
                            if (data.tea_transactiondate) {
                                const date = new Date(data.tea_transactiondate);
                                if (!isNaN(date)) {
                                    const year = date.getFullYear();
                                    const month = String(date.getMonth() + 1).padStart(2, '0');
                                    const day = String(date.getDate()).padStart(2, '0');
                                    $('#edit_tsdate').val(`${year}-${month}-${day}`);
                                } else {
                                    $('#edit_tsdate').val('');
                                }
                            } else {
                                $('#edit_tsdate').val('');
                            }
                            
                            // Show modal
                            $('#editDetailDetModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to load data',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading data:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while loading data. Please try again.',
                        });
                    }
                });
            });
            
            // Update form submission
            $('#editDataForm').on('submit', function(e) {
                e.preventDefault();

                // Validate the form first
                if (!validateForm('editDataForm')) {
                    return false;
                }

                // Create form data object
                var formData = $(this).serialize();

                // Send AJAX request
                $.ajax({
                    url: base_url + 'TransSparepart/update',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        // Disable the submit button and show loading state
                        $('#update').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');

                        // Clear any previous error messages from the assetNo field
                        $('#edit_assetNo').removeClass('is-invalid');
                        const assetErrorMessage = $('#edit_assetNo').closest('.input-group').parent().find('.error-message');
                        if (assetErrorMessage.length) {
                            assetErrorMessage.remove();
                        }
                    },
                    success: function(response) {
                        if (response.status) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                // Close modal
                                $('#editDetailDetModal').modal('hide');

                                // Refresh the datatable
                                table.ajax.reload();
                            });
                        } else {
                            // Show error message
                            if (response.message === 'Asset number already exists in another record') {
                                // Mark the assetNo field as invalid
                                $('#edit_assetNo').addClass('is-invalid');

                                // Find the correct parent for the error message
                                let parent = $('#edit_assetNo').closest('.input-group').parent();

                                // Create error message if it doesn't exist
                                if (!parent.find('.error-message').length) {
                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'error-message text-danger mt-1';
                                    errorDiv.textContent = 'This asset number already exists in another record';
                                    parent.append(errorDiv);
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to update data',
                                });
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating data:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating data. Please try again.',
                        });
                    },
                    complete: function() {
                        // Re-enable the submit button and restore text
                        $('#update').prop('disabled', false).text('Update Sparepart');
                    }
                });
            });

            
            // Delete button click handler
            $('#tabelEmovement').on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                
                // Show confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This sparepart will be marked as deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request to delete
                        $.ajax({
                            url: base_url + 'TransSparepart/delete',
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status) {
                                    // Show success message
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    
                                    // Refresh the datatable
                                    table.ajax.reload();
                                } else {
                                    // Show error message
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message || 'Failed to delete data',
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

            // Function to save form data before opening search modal (pastikan ini ada dan benar)
            // Function to save form data before opening search modal
            function saveFormData() {
                const formData = {};
                
                if (previousModal === '#addDetailDetModal') {
                    formData.assetNo = $('#assetNo').val();
                    // formData.pc_id = $('#pc_id').val(); // Dihapus karena kolom PC ID dihilangkan
                    formData.category = $('#category').val();
                    formData.pcname = $('#pcname').val(); 
                    formData.ipaddress = $('#ipaddress').val();
                    formData.fromlocation = $('#fromlocation').val();
                    formData.tolocation = $('#tolocation').val();
                    formData.fromuser = $('#fromuser').val();
                    formData.fromuser_id = $('#fromuser_id').val();
                    formData.touser = $('#touser').val();
                    formData.touser_id = $('#touser_id').val();
                    formData.serial_number = $('#serial_number').val(); 
                    formData.return = $('#return').val();
                    formData.tsdate = $('#tsdate').val();
                    formData.purpose = $('#purpose').val();
                } else if (previousModal === '#editDetailDetModal') {
                    formData.edit_tea_id = $('#edit_tea_id').val();
                    formData.edit_assetNo = $('#edit_assetNo').val();
                    // formData.edit_pc_id = $('#edit_pc_id').val(); // Dihapus karena kolom PC ID dihilangkan
                    formData.edit_category = $('#edit_category').val();
                    formData.edit_pcname = $('#edit_pcname').val(); 
                    formData.edit_ipaddress = $('#edit_ipaddress').val();
                    formData.edit_fromlocation = $('#edit_fromlocation').val();
                    formData.edit_tolocation = $('#edit_tolocation').val();
                    formData.edit_fromuser = $('#edit_fromuser').val();
                    formData.edit_fromuser_id = $('#edit_fromuser_id').val();
                    formData.edit_touser = $('#edit_touser').val();
                    formData.edit_touser_id = $('#edit_touser_id').val();
                    formData.edit_serial_number = $('#edit_serial_number').val(); 
                    formData.edit_return = $('#edit_return').val();
                    formData.edit_tsdate = $('#edit_tsdate').val();
                    formData.edit_purpose = $('#edit_purpose').val();
                }
                
                return formData;
            }

            function restoreFormData(formData) {
                if (previousModal === '#addDetailDetModal') {
                    $('#assetNo').val(formData.assetNo || '');
                    // $('#pc_id').val(formData.pc_id || ''); // Dihapus karena kolom PC ID dihilangkan
                    $('#category').val(formData.category || '');
                    $('#pcname').val(formData.pcname || ''); 
                    $('#ipaddress').val(formData.ipaddress || '');
                    $('#fromlocation').val(formData.fromlocation || '');
                    $('#tolocation').val(formData.tolocation || '');
                    $('#fromuser').val(formData.fromuser || '');
                    $('#fromuser_id').val(formData.fromuser_id || '');
                    $('#touser').val(formData.touser || '');
                    $('#touser_id').val(formData.touser_id || '');
                    $('#serial_number').val(formData.serial_number || ''); 
                    $('#return').val(formData.return || '');
                    $('#tsdate').val(formData.tsdate || '');
                    $('#purpose').val(formData.purpose || '');
                } else if (previousModal === '#editDetailDetModal') {
                    $('#edit_tea_id').val(formData.edit_tea_id || '');
                    $('#edit_assetNo').val(formData.edit_assetNo || '');
                    // $('#edit_pc_id').val(formData.edit_pc_id || ''); // Dihapus karena kolom PC ID dihilangkan
                    $('#edit_category').val(formData.edit_category || '');
                    $('#edit_pcname').val(formData.edit_pcname || ''); 
                    $('#edit_ipaddress').val(formData.edit_ipaddress || '');
                    $('#edit_fromlocation').val(formData.edit_fromlocation || '');
                    $('#edit_tolocation').val(formData.edit_tolocation || '');
                    $('#edit_fromuser').val(formData.edit_fromuser || '');
                    $('#edit_fromuser_id').val(formData.edit_fromuser_id || '');
                    $('#edit_touser').val(formData.edit_touser || '');
                    $('#edit_touser_id').val(formData.edit_touser_id || '');
                    $('#edit_serial_number').val(formData.edit_serial_number || ''); 
                    $('#edit_return').val(formData.edit_return || '');
                    $('#edit_tsdate').val(formData.edit_tsdate || '');
                    $('#edit_purpose').val(formData.purpose || '');
                }
            }

            // Handle row click to select asset
            // Handle row click to select asset
            // Handle row click to select asset
            $('#assetNoTable tbody').off('click', 'tr').on('click', 'tr', function() {
                const data = tblAssetNo.row(this).data();
                if (!data) {
                    console.log("No data found for clicked row in assetNoTable.");
                    return;
                }

                console.log('Asset selected for Sparepart Form:', data);
                console.log('Calling modal (previousModal):', previousModal);

                // Mengambil nilai dari objek 'data'
                // Gunakan || '' untuk memastikan nilai string kosong jika data null/undefined
                const assetNoValue = data.e_assetno || '';
                const pcNameValue = data.e_equipmentname || '';
                const serialNumberValue = data.e_serialnumber || '';
                // Tidak perlu lagi mengambil equipmentIdValue jika tidak ditampilkan di form utama

                if (previousModal === '#addDetailDetModal') {
                    $('#assetNo').val(assetNoValue);
                    $('#pcname').val(pcNameValue);
                    $('#serial_number').val(serialNumberValue);
                    // Hapus pemanggilan cleanupEquipmentIDDisplay dari sini, karena elemen sudah tidak ada.
                    // Jika Anda masih memiliki elemen hidden untuk Equipment ID yang tidak ingin dihapus, biarkan saja.
                } else if (previousModal === '#editDetailDetModal') {
                    $('#edit_assetNo').val(assetNoValue);
                    $('#edit_pcname').val(pcNameValue);
                    $('#edit_serial_number').val(serialNumberValue);
                    // Hapus pemanggilan cleanupEquipmentIDDisplay dari sini.
                }

                // Sembunyikan modal assetNo
                $('#assetNoModal').modal('hide');

                // Setelah assetNoModal tersembunyi, tampilkan kembali modal sebelumnya
                if (previousModal) {
                    $('#assetNoModal').one('hidden.bs.modal', function() {
                        $(previousModal).modal('show');
                        // Penting: Tandai modal utama bahwa penampilannya karena seleksi dari modal pencarian,
                        // agar handler `hidden.bs.modal` pada modal utama tidak mereset form.
                        $(previousModal).data('fromSearchSelection', true); 
                        isSearchModalOpen = false;
                        // `previousModal` dan `savedFormData` akan direset oleh handler hidden modal utama
                    });
                } else {
                    // Jika tidak ada previousModal (misal modal search dibuka langsung)
                    isSearchModalOpen = false;
                }
            });

            // Perbaiki juga di bagian Auto-fill asset details when asset no is entered manually
            // Fungsi ini akan dipanggil saat pengguna mengetik Asset No secara manual
            // Kita perlu memastikan PC Name, Serial Number juga terisi.
            $('#assetNo').off('input').on('input', function() {
                const assetNo = $(this).val().trim();
                // Bersihkan semua field terkait yang harus diisi otomatis
                $('#pcname').val('');
                $('#serial_number').val('');
                // Hapus elemen display Equipment ID jika masih ada
                // $('#equipment_id_display').remove(); // Hapus baris ini jika fungsi cleanupEquipmentIDDisplay sudah tidak ada

                if (assetNo) {
                    $.ajax({
                        url: base_url + "TransSparepart/getAssetNo",
                        type: 'GET',
                        data: { assetNo: assetNo },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status && response.data) {
                                $('#assetNo').val(response.data.e_assetno || ''); // Bisa tetap update Asset No jika ada koreksi
                                $('#pcname').val(response.data.e_equipmentname || '');
                                $('#serial_number').val(response.data.e_serialnumber || '');
                            } else {
                                // Jika tidak ditemukan, kosongkan field terkait dan tampilkan pesan
                                $('#pcname').val('');
                                $('#serial_number').val('');
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Asset Not Found',
                                    text: 'Asset No tidak ditemukan atau tidak aktif.',
                                    showConfirmButton: true
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching asset details on manual input:', error);
                            $('#pcname').val('');
                            $('#serial_number').val('');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error mengambil detail aset. Silakan coba lagi.',
                                showConfirmButton: true
                            });
                        }
                    });
                }
            });

            $('#edit_assetNo').off('input').on('input', function() {
                const assetNo = $(this).val().trim();
                // Bersihkan semua field terkait yang harus diisi otomatis
                $('#edit_pcname').val('');
                $('#edit_serial_number').val('');
                // Hapus elemen display Equipment ID jika masih ada
                // $('#edit_equipment_id_display').remove(); // Hapus baris ini jika fungsi cleanupEquipmentIDDisplay sudah tidak ada

                if (assetNo) {
                    $.ajax({
                        url: base_url + "TransSparepart/getAssetNo",
                        type: 'GET',
                        data: { assetNo: assetNo },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status && response.data) {
                                $('#edit_assetNo').val(response.data.e_assetno || '');
                                $('#edit_pcname').val(response.data.e_equipmentname || '');
                                $('#edit_serial_number').val(response.data.e_serialnumber || '');
                            } else {
                                // Jika tidak ditemukan, kosongkan field terkait dan tampilkan pesan
                                $('#edit_pcname').val('');
                                $('#edit_serial_number').val('');
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Asset Not Found',
                                    text: 'Asset No tidak ditemukan atau tidak aktif.',
                                    showConfirmButton: true
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching asset details on manual input:', error);
                            $('#edit_pcname').val('');
                            $('#edit_serial_number').val('');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error mengambil detail aset. Silakan coba lagi.',
                                showConfirmButton: true
                            });
                        }
                    });
                }
            });

            // Pastikan handler hidden.bs.modal untuk assetNoModal tidak mengganggu pengisian data
            // dan hanya mereset state yang diperlukan.
            $('#assetNoModal').on('hidden.bs.modal', function() {
                $('#searchAssetNo').val('');
                if ($.fn.DataTable.isDataTable('#assetNoTable')) {
                    tblAssetNo.clear().destroy();
                }
                // HANYA reset `previousModal` dan `savedFormData` jika `isSearchModalOpen` false
                // Ini mencegah mereka direset jika modal utama masih ingin menggunakan data tersimpan
                // (misalnya jika modal search diklik tapi tidak ada seleksi).
                if (!isSearchModalOpen) { // Jika modal search ditutup secara langsung (bukan karena seleksi)
                     previousModal = null; 
                     savedFormData = {};
                }
                // isSearchModalOpen akan direset oleh handler klik baris (jika seleksi)
                // atau oleh handler close modal utama (jika di-cancel dari modal search)
            });

            // Perbaiki bagian ini di handler modal utama Anda (addDetailDetModal & editDetailDetModal)
            // agar mereka selalu menampilkan diri kembali dan mereset `isSearchModalOpen` dan `previousModal`
            // setelah *semua* interaksi dengan modal search selesai.

            // Reset form when add modal is closed
            $('#addDetailDetModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                console.log('Add modal hidden. From search selection:', $(this).data('fromSearchSelection'));
                // Reset form HANYA JIKA BUKAN dari proses pemilihan aset melalui search modal
                if (!$(this).data('fromSearchSelection')) { 
                    $('#addDataForm')[0].reset();
                    const today = new Date().toISOString().split('T')[0];
                    $('#tsdate').val(today);
                    // Hapus elemen display Equipment ID jika masih ada
                    // $('#equipment_id_display').remove(); // Hapus baris ini jika fungsi cleanupEquipmentIDDisplay sudah tidak ada
                    $('#addDataForm .is-invalid').removeClass('is-invalid');
                    $('#addDataForm .error-message').remove();
                }
                // Pastikan flag reset setelah semua proses selesai
                $(this).removeData('fromSearchSelection'); // Hapus data flag ini
                isSearchModalOpen = false;
                previousModal = null;
                savedFormData = {};
            });

            // Reset form when edit modal is closed
            $('#editDetailDetModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                console.log('Edit modal hidden. From search selection:', $(this).data('fromSearchSelection'));
                if (!$(this).data('fromSearchSelection')) {
                    // $('#edit_equipment_id_display').remove(); // Hapus baris ini jika fungsi cleanupEquipmentIDDisplay sudah tidak ada
                    $('#editDataForm .is-invalid').removeClass('is-invalid');
                    $('#editDataForm .error-message').remove();
                }
                $(this).removeData('fromSearchSelection'); // Hapus data flag ini
                isSearchModalOpen = false;
                previousModal = null;
                savedFormData = {};
            });

            // Trigger asset number modal
            $('.search-asset-btn, .search-edit-asset-btn').on('click', function() {
                modalSource = $(this).hasClass('search-edit-asset-btn') ? 'edit' : 'add';
                isSearchModalOpen = true;

                if ($('#addDetailDetModal').hasClass('show')) {
                    previousModal = '#addDetailDetModal';
                } else if ($('#editDetailDetModal').hasClass('show')) {
                    previousModal = '#editDetailDetModal';
                }

                if (previousModal) {
                    savedFormData = saveFormData();
                    $(previousModal).modal('hide');
                    $(previousModal).one('hidden.bs.modal', function() {
                        $('#assetNoModal').modal('show');
                        loadAssetNoData(); // Load data when modal is shown
                    });
                } else {
                    $('#assetNoModal').modal('show');
                    loadAssetNoData(); // Load data when modal is shown
                }
            });

            // Reset asset search when modal is hidden
            $('#assetNoModal').on('hidden.bs.modal', function() {
                $('#searchAssetNo').val('');
                if ($.fn.DataTable.isDataTable('#assetNoTable')) {
                    tblAssetNo.clear().destroy();
                }
                
                if (previousModal && isSearchModalOpen) {
                    $(previousModal).modal('show');
                    setTimeout(function() {
                        restoreFormData(savedFormData);
                        isSearchModalOpen = false;
                        previousModal = null;
                        savedFormData = {};
                    }, 100);
                }
            });

            // Modified Reset from user search when modal is hidden
            $('#fromUserModal').on('hidden.bs.modal', function() {
                // Reset search functionality (keep existing code)
                $('#searchFromUser').val('');
                if ($.fn.DataTable.isDataTable('#fromUserTable')) {
                    tblFromUser.clear().destroy();
                }
                
                // If modal was closed without selection and there was a previous modal, show it and restore data
                if (previousModal && isSearchModalOpen) {
                    $(previousModal).modal('show');
                    
                    // Restore form data after modal is shown
                    setTimeout(function() {
                        restoreFormData(savedFormData);
                        isSearchModalOpen = false;
                        previousModal = null;
                        savedFormData = {};
                    }, 100);
                }
            });

            // Modified Reset to user search when modal is hidden
            $('#toUserModal').on('hidden.bs.modal', function() {
                // Reset search functionality (keep existing code)
                $('#searchToUser').val('');
                if ($.fn.DataTable.isDataTable('#toUserTable')) {
                    tblToUser.clear().destroy();
                }
                
                // If modal was closed without selection and there was a previous modal, show it and restore data
                if (previousModal && isSearchModalOpen) {
                    $(previousModal).modal('show');
                    
                    // Restore form data after modal is shown
                    setTimeout(function() {
                        restoreFormData(savedFormData);
                        isSearchModalOpen = false;
                        previousModal = null;
                        savedFormData = {};
                    }, 100);
                }
            });

            // Handle close button clicks specifically
            $('#assetNoModal .btn-close, #fromUserModal .btn-close, #toUserModal .btn-close').on('click', function() {
                const modalId = $(this).closest('.modal').attr('id');
                
                // Hide the current search modal
                $(`#${modalId}`).modal('hide');
                
                // Show the previous modal after search modal is hidden and restore data
                if (previousModal && isSearchModalOpen) {
                    $(`#${modalId}`).one('hidden.bs.modal', function() {
                        $(previousModal).modal('show');
                        
                        // Restore form data after modal is shown
                        setTimeout(function() {
                            restoreFormData(savedFormData);
                            isSearchModalOpen = false;
                            previousModal = null;
                            savedFormData = {};
                        }, 100);
                    });
                }
            });
            
            // Reset form when add modal is closed
            $('#addDetailDetModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                // Only reset if not opened by search modal
                if (!isSearchModalOpen) {
                    $('#addDataForm')[0].reset();

                    // Reset today's date as default for Transaction Date
                    const today = new Date().toISOString().split('T')[0];
                    $('#tsdate').val(today);

                    // Clear equipment ID display
                    $('#equipment_id_display').remove();

                    // Remove validation errors for Asset No
                    const assetNoField = $('#assetNo');
                    assetNoField.removeClass('is-invalid');
                    const assetErrorMessage = assetNoField.closest('.input-group').parent().find('.error-message');
                    if (assetErrorMessage.length) {
                        assetErrorMessage.remove();
                    }

                    // Remove validation errors for other fields
                    const inputs = document.querySelectorAll('#addDataForm .is-invalid');
                    inputs.forEach(input => {
                        input.classList.remove('is-invalid');
                    });

                    const errorMessages = document.querySelectorAll('#addDataForm .error-message');
                    errorMessages.forEach(error => error.remove());
                }
            });

            $('#editDetailDetModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                // Only reset if not opened by search modal
                if (!isSearchModalOpen) {
                    // Clear equipment ID display
                    $('#edit_equipment_id_display').remove();

                    // Remove validation errors for Asset No
                    const editAssetNoField = $('#edit_assetNo');
                    editAssetNoField.removeClass('is-invalid');
                    const editAssetErrorMessage = editAssetNoField.closest('.input-group').parent().find('.error-message');
                    if (editAssetErrorMessage.length) {
                        editAssetErrorMessage.remove();
                    }

                    // Remove validation errors for other fields
                    const inputs = document.querySelectorAll('#editDataForm .is-invalid');
                    inputs.forEach(input => {
                        input.classList.remove('is-invalid');
                    });

                    const errorMessages = document.querySelectorAll('#editDataForm .error-message');
                    errorMessages.forEach(error => error.remove());
                }
            });
        });
    </script>
</div>
<?= $this->endSection() ?>