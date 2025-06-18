<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>
<div class="card">
<div class="card-header">
        <h4 class="card-title">Sparepart</h4>
    </div>    
<p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDetailDetModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add Transaction Sparepart
        </button>
    </p>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelEmovement">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>ID</th>
                    <th>No Asset</th>
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
                    <h5 class="modal-title" id="addDetailDetModalLabel">Add Transaction Sparepart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addDataForm">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col">
                                    <label for="assetNo" class="form-label">Asset Number</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control assetNo" id="assetNo" name="assetNo" 
                                            placeholder="Cari nomor asset">
                                        <button class="btn btn-link search-asset-btn" type="button" 
                                                style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="category" name="category" readonly 
                                           style="background-color: #f2f2f2; cursor: not-allowed; opacity: 0.8;">
                                </div>
                                <div class="col">
                                    <label for="pcname" class="form-label">PC Name</label>
                                    <input type="text" class="form-control" id="pcname" name="pcname">
                                </div>
                                <div class="col">
                                    <label for="ipaddress" class="form-label">IP Address</label>
                                    <input type="text" class="form-control" id="ipaddress" name="ipaddress" 
                                        pattern="^[0-9.]*$" 
                                        onkeypress="return /[0-9.]/.test(event.key)" 
                                        title="Only numbers and dots allowed">
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col">
                                    <label for="fromlocation" class="form-label">From Location</label>
                                    <select class="form-select" id="fromlocation" name="fromlocation">
                                        <option value="">--Pilih Lokasi--</option>
                                        <?php foreach ($sections as $section): ?>
                                            <option value="<?= $section->sec_section ?>" data-id="<?= $section->sec_sectioncode ?>">
                                                <?= $section->sec_section ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="tolocation" class="form-label">To Location</label>
                                    <select class="form-select" id="tolocation" name="tolocation">
                                        <option value="">--Pilih Lokasi--</option>
                                        <?php foreach ($sections as $section): ?>
                                            <option value="<?= $section->sec_section ?>" data-id="<?= $section->sec_sectioncode ?>">
                                                <?= $section->sec_section ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="fromuser" class="form-label">From User</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="fromuser" name="fromuser" placeholder="Cari user">
                                        <input type="hidden" id="fromuser_id" name="fromuser_id">
                                        <button class="btn btn-link search-fromuser-btn" type="button" 
                                                style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="touser" class="form-label">To User</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="touser" name="touser" placeholder="Cari user">
                                        <input type="hidden" id="touser_id" name="touser_id">
                                        <button class="btn btn-link search-touser-btn" type="button" 
                                                style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px; width: 77%;">
                                <div class="col">
                                    <label for="serial_number" class="form-label">Serial Number</label>
                                    <input type="text" class="form-control" id="serial_number" name="serialnumber" 
                                           readonly style="background-color: #f2f2f2; cursor: not-allowed; opacity: 0.8;">
                                </div>
                                <div class="col">
                                    <label for="return" class="form-label">Return</label>
                                    <select class="form-select" id="return" name="return">
                                        <option value=""></option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="tsdate" class="form-label">Transaction Date</label>
                                    <input type="date" class="form-control" id="tsdate" name="tsdate">
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px; width: 51%;">
                                <div class="col">
                                    <label for="purpose" class="form-label">Purpose</label>
                                    <input type="text" class="form-control" id="purpose" name="purpose" style="width: 100%;">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="save">Save </button>
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
                    <h5 class="modal-title" id="editDetailDetModalLabel">Edit Transaction Sparepart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editDataForm">
                        <input type="hidden" id="edit_tea_id" name="id">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col">
                                    <label for="edit_assetNo" class="form-label">Asset Number</label>
                                    <input type="number" class="form-control" id="edit_assetNo" name="assetNo"
                                        readonly style="background-color: #f2f2f2; cursor: not-allowed; opacity: 0.8;">
                                </div>
                                <div class="col">
                                    <label for="edit_category" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="edit_category" name="category"
                                           readonly style="background-color: #f2f2f2; cursor: not-allowed; opacity: 0.8;">
                                </div>
                                <div class="col">
                                    <label for="edit_pcname" class="form-label">PC Name</label>
                                    <input type="text" class="form-control" id="edit_pcname" name="pcname">
                                </div>
                                <div class="col">
                                    <label for="edit_ipaddress" class="form-label">IP Address</label>
                                    <input type="text" class="form-control" id="edit_ipaddress" name="ipaddress"
                                           pattern="^[0-9.]*$" 
                                           onkeypress="return /[0-9.]/.test(event.key)" 
                                           title="Only numbers and dots allowed">
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col">
                                    <label for="edit_fromlocation" class="form-label">From Location</label>
                                    <select class="form-select" id="edit_fromlocation" name="fromlocation">
                                        <?php foreach ($sections as $section): ?>
                                            <option value="<?= $section->sec_section ?>" data-id="<?= $section->sec_sectioncode ?>">
                                                <?= $section->sec_section ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="edit_tolocation" class="form-label">To Location</label>
                                    <select class="form-select" id="edit_tolocation" name="tolocation">
                                        <?php foreach ($sections as $section): ?>
                                            <option value="<?= $section->sec_section ?>" data-id="<?= $section->sec_sectioncode ?>">
                                                <?= $section->sec_section ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="edit_fromuser" class="form-label">From User</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="edit_fromuser" name="fromuser" placeholder="Select user">
                                        <input type="hidden" id="edit_fromuser_id" name="fromuser_id">
                                        <button class="btn btn-link search-edit-fromuser-btn" type="button" 
                                                style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="edit_touser" class="form-label">To User</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="edit_touser" name="touser" placeholder="Select user">
                                        <input type="hidden" id="edit_touser_id" name="touser_id">
                                        <button class="btn btn-link search-edit-touser-btn" type="button" 
                                                style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px; width: 77%;">
                                <div class="col">
                                    <label for="edit_serial_number" class="form-label">Serial Number</label>
                                    <input type="text" class="form-control" id="edit_serial_number" name="serialnumber" 
                                           readonly style="background-color: #f2f2f2; cursor: not-allowed; opacity: 0.8;">
                                </div>
                                <div class="col">
                                    <label for="edit_return" class="form-label">Return</label>
                                    <select class="form-select" id="edit_return" name="return">
                                        <option value=""></option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="edit_tsdate" class="form-label">Transaction Date</label>
                                    <input type="date" class="form-control" id="edit_tsdate" name="tsdate">
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px; width: 51%;">
                                <div class="col">
                                    <label for="edit_purpose" class="form-label">Purpose</label>
                                    <input type="text" class="form-control" id="edit_purpose" name="purpose" style="width: 100%;">
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
                                <th style="width: 15%;">Kind</th>
                                <th style="width:25%;">Serial Number</th>
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

    <!-- From User Modal -->
    <div class="modal fade" id="fromUserModal" tabindex="-1" aria-labelledby="fromUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fromUserModalLabel">From User Data</h5>
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
                    <h5 class="modal-title" id="toUserModalLabel">To User Data</h5>
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
                    
                    /* Hide spinner for Chrome, Safari, Edge, Opera */
                    input[type=number]::-webkit-inner-spin-button, 
                    input[type=number]::-webkit-outer-spin-button { 
                        -webkit-appearance: none;
                        margin: 0;}
                    
                    /* Hide spinner for Firefox */
                    input[type=number] {
                        -moz-appearance: textfield;
                    }
                </style>
            `);

            // Function to get field label text
            function getFieldLabel(fieldId) {
                // Find the label for this field
                const labelElement = document.querySelector(`label[for="${fieldId}"]`);
                if (labelElement) {
                    return labelElement.textContent;
                }
                // Default fallback labels for fields that might not have a direct label
                const fieldLabels = {
                    'assetNo': 'Asset Number',
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

            // Function to validate form - includes from/to user validation
            function validateForm(formId) {
                let isValid = true;
                const form = document.getElementById(formId);
                
                // Remove any existing error messages and red borders
                const errorMessages = form.querySelectorAll('.error-message');
                errorMessages.forEach(error => error.remove());
                
                const inputs = form.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid');
                });
                
                // Required fields (add or remove field names as needed)
                const requiredFields = ['assetNo', 'category', 'pcname', 'ipaddress', 'fromlocation', 'tolocation', 'fromuser', 'touser', 'tsdate', 'serial_number', 'purpose', 'return'];
                
                // Check if the form ID is for editing, if so modify the field names
                const prefix = formId === 'editDataForm' ? 'edit_' : '';
                
                // Validate each required field
                requiredFields.forEach(field => {
                    const fieldName = prefix + field;
                    const input = form.querySelector(`#${fieldName}`);
                    
                    if (input) {
                        const value = input.value.trim();
                        
                        // Skip validation for readonly fields in edit form
                        if ((formId === 'editDataForm' && (field === 'category' || field === 'serial_number')) || 
                            (formId === 'addDataForm' && (field === 'category' || field === 'serial_number') && value !== '')) {
                            return;
                        }
                        
                        if (value === '') {
                            isValid = false;
                            input.classList.add('is-invalid');
                            
                            // Get field label for custom error message
                            const fieldLabel = getFieldLabel(fieldName);
                            
                            // Create error message
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-danger mt-1';
                            errorDiv.textContent = `${fieldLabel} is required`;
                            
                            // Find the correct parent for the error message
                            // For asset number, use the parent of the input group
                            let parent;
                            if (field === 'assetNo' || field === 'fromuser' || field === 'touser') {
                                // Get the input-group's parent (which is the column)
                                parent = input.closest('.input-group') ? input.closest('.input-group').parentElement : input.parentElement;
                            } else {
                                parent = input.parentElement;
                            }
                            
                            // Insert error message after the input or input group
                            parent.appendChild(errorDiv);
                        }
                    }
                });
                
                // IP Address validation (simple check)
                const ipField = form.querySelector(`#${prefix}ipaddress`);
                if (ipField && ipField.value.trim() !== '') {
                    const ipRegex = /^(\d{1,3}\.){3}\d{1,3}$/;
                    if (!ipRegex.test(ipField.value.trim())) {
                        isValid = false;
                        ipField.classList.add('is-invalid');
                        
                        // Check if error message already exists
                        let errorDiv = ipField.parentElement.querySelector('.error-message');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-danger mt-1';
                            ipField.parentElement.appendChild(errorDiv);
                        }
                        errorDiv.textContent = 'Please enter a valid IP Address format (e.g., 192.168.1.1)';
                    }
                }
                
                // Check if From User and To User are the same
                const fromUser = form.querySelector(`#${prefix}fromuser`).value;
                const toUser = form.querySelector(`#${prefix}touser`).value;
                
                if (fromUser && toUser && fromUser === toUser) {
                    isValid = false;
                    
                    // Add validation error to To User field
                    const toUserField = form.querySelector(`#${prefix}touser`);
                    toUserField.classList.add('is-invalid');
                    
                    // Find the correct parent for the error message
                    const parent = toUserField.closest('.input-group').parentElement;
                    
                    // Create error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message text-danger mt-1';
                    errorDiv.textContent = 'From User and To User cannot be the same';
                    
                    // Insert error message
                    parent.appendChild(errorDiv);
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
                        console.log('Server response:', xhr.responseText);
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
                    { data: 'tea_serialnumber' }, 
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
                            
                            return `${day} / ${month} / ${year}`;
                        }
                    },
                    { data: 'tea_pcname' }, 
                    { data: 'tea_ipaddress' }, 
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
            
            // Initialize Asset Number modal with search functionality
            $('.search-asset-btn, .assetNo').on('click', function() {
                // Only show the modal if it's the Add form (not Edit form)
                if ($('#edit_assetNo').is(':visible') && $('#edit_assetNo').is('[readonly]')) {
                    return; // Don't show modal if it's the edit form
                }
                $('#assetNoModal').modal('show');
            });

            $('#assetNoModal').on('shown.bs.modal', function() {
                if ($.fn.DataTable.isDataTable('#assetNoTable')) {
                    tblAssetNo.clear().destroy(); 
                }

                tblAssetNo = $("#assetNoTable").DataTable({
                    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                    pageLength: 5,
                    processing: true,
                    order: [],
                    autoWidth: false,
                    dom: 't<"bottom"ip>', // Only show table, pagination and info
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
                    ajax: {
                        url: base_url + "TransSparepart/getAvailableAssets", // Updated to use the new endpoint
                        dataSrc: "",
                        beforeSend: function() {
                            let spinner = `
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Loading...
                                </div>`;
                            $('#assetNoTable tbody').html(`<tr><td colspan="4">${spinner}</td></tr>`); 
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTables AJAX error:', error, thrown);
                            console.log('Server response:', xhr.responseText);
                            $('#assetNoTable tbody').html('<tr><td colspan="4" class="text-center">Error loading data. Please try again.</td></tr>');
                        }
                    },
                    columns: [
                        { data: 'e_assetno', width: '15%' },
                        { data: 'e_kind', width: '15%' },
                        { data: 'e_serialnumber', width: '25%' },
                        { data: 'e_model', width: '45%' }
                    ]
                });

                // Set up length change
                $('#assetNoLength').on('change', function() {
                    const newLength = $(this).val();
                    tblAssetNo.page.len(newLength).draw();
                });
                
                // Set up search with debounce
                let searchTimeout;
                $('#searchAssetNo').on('keyup', function() {
                    clearTimeout(searchTimeout);
                    const searchValue = this.value;
                    
                    searchTimeout = setTimeout(function() {
                        tblAssetNo.search(searchValue).draw();
                    }, 300);
                });

                $('#assetNoTable tbody').on('click', 'tr', function() {
                    var data = tblAssetNo.row(this).data();
                    if (!data) return;
                    
                    // Only for Add form, as Edit form has these fields readonly
                    if ($('#addDetailDetModal').hasClass('show')) {
                        $('#assetNo').val(data.e_assetno); 
                        $('#category').val(data.e_kind); 
                        $('#serial_number').val(data.e_serialnumber);
                        
                        // Remove any error messages and validation styling
                        $('#assetNo').removeClass('is-invalid');
                        const assetErrorMessage = $('#assetNo').parent().find('.error-message');
                        if (assetErrorMessage.length) {
                            assetErrorMessage.remove();
                        }
                    }
                    
                    $('#assetNoModal').modal('hide');
                });
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

            // Function to initialize user table
            function initializeUserTable(tableId, modalId, searchInputId, lengthSelectId, selectedInput, selectedInputId, otherUserInput) {
                const table = $(tableId).DataTable({
                    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                    pageLength: 5,
                    processing: true,
                    searching: true,
                    ordering: true,
                    paging: true,
                    info: true,
                    dom: 't<"bottom"ip>', // Only show table, pagination and info
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
                        { width: '10%' }, // Employee ID
                        { width: '30%' }, // Name
                        { width: '25%' }, // Position
                        { width: '35%' }  // Section
                    ]
                });
                
                // Set up length change
                $(lengthSelectId).on('change', function() {
                    const newLength = $(this).val();
                    table.page.len(newLength).draw();
                });
                
                // Set up search with debounce
                let searchTimeout;
                $(searchInputId).on('keyup', function() {
                    clearTimeout(searchTimeout);
                    const searchValue = this.value;
                    
                    searchTimeout = setTimeout(function() {
                        table.search(searchValue).draw();
                    }, 300);
                });
                
                // Load initial data
                loadUserData(tableId, otherUserInput);
                
                // Handle row selection
                $(tableId + ' tbody').on('click', 'tr', function() {
                    const data = table.row(this).data();
                    if (!data) return;
                    
                    const employeeId = data[0];
                    const employeeName = data[1];
                    
                    $(selectedInput).val(employeeName);
                    $(selectedInputId).val(employeeId);
                    
                    // Remove validation errors if any
                    $(selectedInput).removeClass('is-invalid');
                    const errorMessage = $(selectedInput).closest('.input-group').parent().find('.error-message');
                    if (errorMessage.length) {
                        errorMessage.remove();
                    }
                    
                    // Check if the selected user is the same as the other user field
                    const otherUserValue = $(otherUserInput).val();
                    if (otherUserValue && otherUserValue === employeeName) {
                        // Show validation error
                        $(selectedInput).addClass('is-invalid');
                        
                        // Find the correct parent for the error message
                        let parent = $(selectedInput).closest('.input-group').parent();
                        
                        // Create error message
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message text-danger mt-1';
                        errorDiv.textContent = 'From User and To User cannot be the same';
                        
                        // Insert error message
                        parent.append(errorDiv);
                    }
                    
                    // Close the modal
                    $(modalId).modal('hide');
                    
                    // Reload the other user table to filter out this selection
                    const otherTableId = tableId === '#fromUserTable' ? '#toUserTable' : '#fromUserTable';
                    if ($.fn.DataTable.isDataTable(otherTableId)) {
                        loadUserData(otherTableId, selectedInput);
                    }
                });
                
                return table;
            }

            // Initialize From User Modal and Table
            $('.search-fromuser-btn, #fromuser').on('click', function() {
                $('#fromUserModal').modal('show');
            });
            
            $('#fromUserModal').on('shown.bs.modal', function() {
                if ($.fn.DataTable.isDataTable('#fromUserTable')) {
                    // Reload data to apply current filters
                    loadUserData('#fromUserTable', '#touser');
                } else {
                    // Initialize table
                    tblFromUser = initializeUserTable(
                        '#fromUserTable', 
                        '#fromUserModal', 
                        '#searchFromUser', 
                        '#fromUserLength',
                        '#fromuser', 
                        '#fromuser_id', 
                        '#touser'
                    );
                }
            });
            
            // Initialize To User Modal and Table
            $('.search-touser-btn, #touser').on('click', function() {
                $('#toUserModal').modal('show');
            });
            
            $('#toUserModal').on('shown.bs.modal', function() {
                if ($.fn.DataTable.isDataTable('#toUserTable')) {
                    // Reload data to apply current filters
                    loadUserData('#toUserTable', '#fromuser');
                } else {
                    // Initialize table
                    tblToUser = initializeUserTable(
                        '#toUserTable', 
                        '#toUserModal', 
                        '#searchToUser', 
                        '#toUserLength',
                        '#touser', 
                        '#touser_id', 
                        '#fromuser'
                    );
                }
            });// For Edit form - From User
            $('.search-edit-fromuser-btn, #edit_fromuser').on('click', function() {
                $('#fromUserModal').modal('show');
                
                // When the modal is shown for edit form
                $('#fromUserModal').off('shown.bs.modal').on('shown.bs.modal', function() {
                    if ($.fn.DataTable.isDataTable('#fromUserTable')) {
                        tblFromUser.clear().destroy();
                    }
                    
                    tblFromUser = initializeUserTable(
                        '#fromUserTable', 
                        '#fromUserModal', 
                        '#searchFromUser', 
                        '#fromUserLength',
                        '#edit_fromuser', 
                        '#edit_fromuser_id', 
                        '#edit_touser'
                    );
                    
                    // When selecting from this modal while edit form is active,
                    // update the edit form fields instead
                    $('#fromUserTable tbody').off('click', 'tr').on('click', 'tr', function() {
                        const data = tblFromUser.row(this).data();
                        if (!data) return;
                        
                        const employeeId = data[0];
                        const employeeName = data[1];
                        
                        $('#edit_fromuser').val(employeeName);
                        $('#edit_fromuser_id').val(employeeId);
                        
                        // Remove validation errors if any
                        $('#edit_fromuser').removeClass('is-invalid');
                        const errorMessage = $('#edit_fromuser').closest('.input-group').parent().find('.error-message');
                        if (errorMessage.length) {
                            errorMessage.remove();
                        }
                        
                        // Check if the selected user is the same as the other user field
                        if ($('#edit_touser').val() === employeeName) {
                            // Show validation error
                            $('#edit_fromuser').addClass('is-invalid');
                            
                            // Find the correct parent for the error message
                            let parent = $('#edit_fromuser').closest('.input-group').parent();
                            
                            // Create error message
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-danger mt-1';
                            errorDiv.textContent = 'From User and To User cannot be the same';
                            
                            // Insert error message
                            parent.append(errorDiv);
                        }
                        
                        // Close the modal
                        $('#fromUserModal').modal('hide');
                        
                        // Reload to user table to filter out this selection
                        if ($.fn.DataTable.isDataTable('#toUserTable')) {
                            loadUserData('#toUserTable', '#edit_fromuser');
                        }
                    });
                });
            });
            
            // For Edit form - To User
            $('.search-edit-touser-btn, #edit_touser').on('click', function() {
                $('#toUserModal').modal('show');
                
                // When the modal is shown for edit form
                $('#toUserModal').off('shown.bs.modal').on('shown.bs.modal', function() {
                    if ($.fn.DataTable.isDataTable('#toUserTable')) {
                        tblToUser.clear().destroy();
                    }
                    
                    tblToUser = initializeUserTable(
                        '#toUserTable', 
                        '#toUserModal', 
                        '#searchToUser', 
                        '#toUserLength',
                        '#edit_touser', 
                        '#edit_touser_id', 
                        '#edit_fromuser'
                    );
                    
                    // When selecting from this modal while edit form is active,
                    // update the edit form fields instead
                    $('#toUserTable tbody').off('click', 'tr').on('click', 'tr', function() {
                        const data = tblToUser.row(this).data();
                        if (!data) return;
                        
                        const employeeId = data[0];
                        const employeeName = data[1];
                        
                        $('#edit_touser').val(employeeName);
                        $('#edit_touser_id').val(employeeId);
                        
                        // Remove validation errors if any
                        $('#edit_touser').removeClass('is-invalid');
                        const errorMessage = $('#edit_touser').closest('.input-group').parent().find('.error-message');
                        if (errorMessage.length) {
                            errorMessage.remove();
                        }
                        
                        // Check if the selected user is the same as the other user field
                        if ($('#edit_fromuser').val() === employeeName) {
                            // Show validation error
                            $('#edit_touser').addClass('is-invalid');
                            
                            // Find the correct parent for the error message
                            let parent = $('#edit_touser').closest('.input-group').parent();
                            
                            // Create error message
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-danger mt-1';
                            errorDiv.textContent = 'From User and To User cannot be the same';
                            
                            // Insert error message
                            parent.append(errorDiv);
                        }
                        
                        // Close the modal
                        $('#toUserModal').modal('hide');
                        
                        // Reload from user table to filter out this selection
                        if ($.fn.DataTable.isDataTable('#fromUserTable')) {
                            loadUserData('#fromUserTable', '#edit_touser');
                        }
                    });
                });
            });

            // Add styling and effects for better visibility
            $('.assetNo').on('focus', function() {
                $(this).attr('placeholder', '');
            }).on('blur', function() {
                if (!$(this).val()) {
                    $(this).attr('placeholder', 'Cari nomor asset');
                }
            });

            $('#fromuser, #touser, #edit_fromuser, #edit_touser').on('focus', function() {
                $(this).attr('placeholder', '');
            }).on('blur', function() {
                if (!$(this).val()) {
                    $(this).attr('placeholder', 'Cari user');
                }
            });

            // Add this to clear validation errors when modal is opened
            $('#addDetailDetModal').on('show.bs.modal', function() {
                const form = document.getElementById('addDataForm');
                const errorMessages = form.querySelectorAll('.error-message');
                errorMessages.forEach(error => error.remove());
                
                const inputs = form.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid');
                });
            });

            $('#editDetailDetModal').on('show.bs.modal', function() {
                const form = document.getElementById('editDataForm');
                const errorMessages = form.querySelectorAll('.error-message');
                errorMessages.forEach(error => error.remove());
                
                const inputs = form.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid');
                });
            });

            $('#addDetailDetModal').on('hidden.bs.modal', function () {
                // Reset all input fields in the form
                $('#addDataForm')[0].reset();
            });

            $('#editDetailDetModal').on('hidden.bs.modal', function () {
                // Reset all input fields in the form
                $('#editDataForm')[0].reset();
            });

            // Add validation to check for same user in From User and To User fields
            $('#fromuser').on('change', function() {
                const fromUser = $(this).val();
                
                if (fromUser && fromUser === $('#touser').val()) {
                    // Show validation error
                    $('#touser').addClass('is-invalid');
                    
                    // Find the correct parent for the error message
                    let parent = $('#touser').closest('.input-group').parent();
                    
                    // Remove any existing error message
                    parent.find('.error-message').remove();
                    
                    // Add new error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message text-danger mt-1';
                    errorDiv.textContent = 'From User and To User cannot be the same';
                    parent.append(errorDiv);
                } else {
                    // Remove validation error if exists
                    $('#touser').removeClass('is-invalid');
                    $('#touser').closest('.input-group').parent().find('.error-message').remove();
                }
                
                // Reload to user table if it's initialized
                if ($.fn.DataTable.isDataTable('#toUserTable')) {
                    loadUserData('#toUserTable', '#fromuser');
                }
            });

            $('#touser').on('change', function() {
                const toUser = $(this).val();
                
                if (toUser && toUser === $('#fromuser').val()) {
                    // Show validation error
                    $(this).addClass('is-invalid');
                    
                    // Find the correct parent for the error message
                    let parent = $(this).closest('.input-group').parent();
                    
                    // Remove any existing error message
                    parent.find('.error-message').remove();
                    
                    // Add new error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message text-danger mt-1';
                    errorDiv.textContent = 'From User and To User cannot be the same';
                    parent.append(errorDiv);
                } else {
                    // Remove validation error if exists
                    $(this).removeClass('is-invalid');
                    $(this).closest('.input-group').parent().find('.error-message').remove();
                }
                
                // Reload from user table if it's initialized
                if ($.fn.DataTable.isDataTable('#fromUserTable')) {
                    loadUserData('#fromUserTable', '#touser');
                }
            });

            // Same for edit form
            $('#edit_fromuser').on('change', function() {
                const fromUser = $(this).val();
                
                if (fromUser && fromUser === $('#edit_touser').val()) {
                    // Show validation error
                    $('#edit_touser').addClass('is-invalid');
                    
                    // Find the correct parent for the error message
                    let parent = $('#edit_touser').closest('.input-group').parent();
                    
                    // Remove any existing error message
                    parent.find('.error-message').remove();
                    
                    // Add new error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message text-danger mt-1';
                    errorDiv.textContent = 'From User and To User cannot be the same';
                    parent.append(errorDiv);
                } else {
                    // Remove validation error if exists
                    $('#edit_touser').removeClass('is-invalid');
                    $('#edit_touser').closest('.input-group').parent().find('.error-message').remove();
                }
                
                // Reload to user table if it's initialized
                if ($.fn.DataTable.isDataTable('#toUserTable')) {
                    loadUserData('#toUserTable', '#edit_fromuser');
                }
            });

            $('#edit_touser').on('change', function() {
                const toUser = $(this).val();
                
                if (toUser && toUser === $('#edit_fromuser').val()) {
                    // Show validation error
                    $(this).addClass('is-invalid');
                    
                    // Find the correct parent for the error message
                    let parent = $(this).closest('.input-group').parent();
                    
                    // Remove any existing error message
                    parent.find('.error-message').remove();
                    
                    // Add new error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message text-danger mt-1';
                    errorDiv.textContent = 'From User and To User cannot be the same';
                    parent.append(errorDiv);
                } else {
                    // Remove validation error if exists
                    $(this).removeClass('is-invalid');
                    $(this).closest('.input-group').parent().find('.error-message').remove();
                }
                
                // Reload from user table if it's initialized
                if ($.fn.DataTable.isDataTable('#fromUserTable')) {
                    loadUserData('#fromUserTable', '#edit_touser');
                }
            });

            // Form submission with validation
            $('#addDataForm').on('submit', function(e) {
                e.preventDefault(); // Prevent form submission
                
                // Validate the form
                if (!validateForm('addDataForm')) {
                    return false;
                }
                
                // Capture form data
                var formData = {
                    assetNo: $('#assetNo').val(),
                    category: $('#category').val(),
                    pcname: $('#pcname').val(),
                    ipaddress: $('#ipaddress').val(),
                    fromlocation: $('#fromlocation').val(),
                    tolocation: $('#tolocation').val(),
                    fromuser: $('#fromuser').val(),
                    touser: $('#touser').val(),
                    tsdate: $('#tsdate').val(),
                    purpose: $('#purpose').val(),
                    return: $('#return').val(),
                    serialnumber: $('#serial_number').val()
                };
                
                // Send data to controller via AJAX
                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "TransSparepart/add",
                    type: 'POST',
                    data: formData,
                }).done(function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',showConfirmButton: false,
                            text: response.message,
                            timer: 1500
                        }).then(function(result) {
                            table.ajax.reload();
                        });
                        
                        // Close the modal
                        $('#addDetailDetModal').modal('hide');

                        // Clear the form fields
                        $('#addDataForm')[0].reset();
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    let message;
                    try {
                        message = JSON.parse(jqXHR.responseText);
                        Swal.fire("Error!", message.code + '<br>' + message.message, "error");
                    } catch (e) {
                        Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                    }
                }).always(function() {
                    $('#wait_screen').hide();
                });
            });

            $('#tabelEmovement').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                
                // Get data for editing
                $.ajax({
                    url: base_url + "TransSparepart/edit",
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.status) {
                            // Fill the form with data
                            $('#edit_tea_id').val(response.data.tea_id);
                            $('#edit_assetNo').val(response.data.tea_assetno);
                            $('#edit_category').val(response.data.tea_category);
                            $('#edit_pcname').val(response.data.tea_pcname);
                            $('#edit_tsdate').val(response.data.tea_transactiondate);
                            $('#edit_ipaddress').val(response.data.tea_ipaddress);
                            
                            // Set select options for from location and to location
                            if (response.data.tea_fromlocation) {
                                $('#edit_fromlocation').val(response.data.tea_fromlocation);
                            }
                            
                            if (response.data.tea_tolocation) {
                                $('#edit_tolocation').val(response.data.tea_tolocation);
                            }
                            
                            // Set user fields
                            $('#edit_fromuser').val(response.data.tea_fromuser);
                            $('#edit_touser').val(response.data.tea_touser);
                            
                            $('#edit_purpose').val(response.data.tea_purpose);
                            $('#edit_return').val(response.data.tea_returnoldequip);
                            $('#edit_serial_number').val(response.data.tea_serialnumber);
                            
                            // Show modal
                            $('#editDetailDetModal').modal('show');
                        } else {
                            Swal.fire("Error!", response.message, "error");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                    }
                });
            });

            $('#editDataForm').on('submit', function(e) {
                e.preventDefault(); // Prevent form submission
                
                // Validate the form
                if (!validateForm('editDataForm')) {
                    return false;
                }
                
                // Capture form data
                var formData = {
                    id: $('#edit_tea_id').val(),
                    assetNo: $('#edit_assetNo').val(),
                    category: $('#edit_category').val(),
                    pcname: $('#edit_pcname').val(),
                    ipaddress: $('#edit_ipaddress').val(),
                    fromlocation: $('#edit_fromlocation').val(),
                    tolocation: $('#edit_tolocation').val(),
                    fromuser: $('#edit_fromuser').val(),
                    touser: $('#edit_touser').val(),
                    tsdate: $('#edit_tsdate').val(),
                    purpose: $('#edit_purpose').val(),
                    return: $('#edit_return').val(),
                    serialnumber: $('#edit_serial_number').val()
                };
                
                // Send data to controller via AJAX
                $('#wait_screen').show();
                $.ajax({
                    url: base_url + "TransSparepart/update",
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
                        });
                        
                        // Close the modal
                        $('#editDetailDetModal').modal('hide');

                        // Clear the form fields
                        $('#editDataForm')[0].reset();
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    let message;
                    try {
                        message = JSON.parse(jqXHR.responseText);
                        Swal.fire("Error!", message.code + '<br>' + message.message, "error");
                    } catch (e) {
                        Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                    }
                }).always(function() {
                    $('#wait_screen').hide();
                });
            });

            $('#tabelEmovement').on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                
                // Confirm deletion using SweetAlert2
                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: `Data dengan id: ${id} akan dihapus secara permanen!`,
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonText: "Batal",
                    confirmButtonText: "Hapus",
                    confirmButtonColor: "#d33",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Perform deletion via AJAX
                        $.ajax({
                            url: base_url + "TransSparepart/delete",
                            type: 'POST',
                            data: {
                                id: id
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Berhasil!",
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        // Refresh DataTable
                                        table.ajax.reload();
                                    });
                                } else {
                                    Swal.fire("Error!", response.message, "error");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                                Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                            }
                        });
                    }
                });
            });

            // IP Address validation for both add and edit forms
            const setupIpAddressValidation = () => {
                const ipFields = document.querySelectorAll('#ipaddress, #edit_ipaddress');
                ipFields.forEach(field => {
                    if (field) {
                        field.addEventListener('input', function(e) {
                            // Remove any characters that aren't numbers or dots
                            this.value = this.value.replace(/[^0-9.]/g, '');
                        });
                    }
                });
            };

            // Call the setup function when DOM is loaded
            setupIpAddressValidation();
        });
    </script>
</div>
<?= $this->endSection() ?>