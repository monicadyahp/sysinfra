<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<style>
/* Anda mungkin perlu menyesuaikan gaya ini untuk tata letak baru */
.card-datatable.table-responsive {
    overflow-x: auto;
    width: 100%;
}

/* Container untuk tabel detail, yang sekarang ada di dalam modal */
/* .handover-detail-container { 
    overflow-x: auto;
    width: 100%;
    margin-top: 1.5rem;
} */

#handoverDetailTable {
    min-width: 1000px; /* Pertahankan ini agar tabel detail tidak terlalu sempit */
}

.mb-3.action-buttons {
    margin-bottom: 1rem !important;
}

/* Sesuaikan lebar modal detail agar lebih besar */
#handoverDetailModal .modal-dialog {
    max-width: 90vw; /* Atur lebar maksimal modal */
    width: 90vw; /* Atur lebar modal */
}

/* Atur lebar modal search agar lebih besar */
#employeeModal .modal-dialog,
#assetModal .modal-dialog,
#serialModal .modal-dialog {
    max-width: 70vw;
    width: 70vw;
}

</style>

<div class="card">
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHandoverModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add New Handover
        </button>
    </p>
    
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="handoverTable">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Record No</th>
                    <th>Request Date</th>
                    <th>Employee</th>
                    <th>Section</th>
                    <th>Purpose</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    
    <div class="modal fade" id="handoverDetailModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="handoverDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 80vw; width: 80vw;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="handoverDetailModalLabel">Equipment Requested for Record No. <span id="detailRecordNoDisplay"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4 action-buttons">
                        <div class="row mb-3">
                            <div class="col-md-12 d-flex gap-3">
                                <button type="button" class="btn btn-primary add-asset-btn">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add by Asset No
                                </button>
                                        
                                <button type="button" class="btn btn-primary add-serial-btn">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add by Serial Number
                                </button>
                                
                                <button type="button" class="btn btn-primary add-equipment-name-btn">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add by Name
                                </button>

                                <button type="button" class="btn btn-primary export-pdf-btn">
                                    <span class="btn-label">
                                        <i class="fa fa-file-pdf"></i> </span>
                                    Export PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="datatables-basic table table-bordered" id="handoverDetailTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Action</th>
                                    <th>Asset No</th>
                                    <th>Serial Number</th>
                                    <th>Equipment Name</th>
                                    <th>Category</th>
                                    <th>Delivered Date</th>
                                    <th>Delivered SIC</th>
                                    <th>Returned Date</th>
                                    <th>Returned SIC</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="addHandoverModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addHandoverModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addHandoverModalLabel">Add New Handover</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addHandoverForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="record_no" class="form-label">Record No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="record_no" name="record_no" placeholder="Enter Record No">
                                <div class="error-message text-danger mt-1" id="record_no_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="request_date" class="form-label">Request Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="request_date" name="request_date" value="<?= date('Y-m-d') ?>">
                                <div class="error-message text-danger mt-1" id="request_date_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="employee_no" class="form-label">Handover Employee ID <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="employee_no" name="employee_no" placeholder="Type or search handover employee ID">
                                    <button class="btn btn-link search-employee-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="employee_no_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-info d-flex align-items-center p-2 bg-light rounded border">
                                    <div>
                                        <div><strong>Employee Name:</strong> <span id="employee_name_display">-</span></div>
                                        <div><strong>Section:</strong> <span id="section_name_display">-</span></div>
                                    </div>
                                </div>
                                <input type="hidden" id="employee_name" name="employee_name">
                                <input type="hidden" id="section_name" name="section_name">
                                <input type="hidden" id="section_code" name="section_code">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="purpose_content" class="form-label">Purpose <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="purpose_content" name="purpose_content" rows="4" placeholder="Describe the purpose"></textarea>
                                <div class="error-message text-danger mt-1" id="purpose_content_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="reason_content" class="form-label">Reason</span></label>
                                <textarea class="form-control" id="reason_content" name="reason_content" rows="4" placeholder="Describe the reason"></textarea>
                                <div class="error-message text-danger mt-1" id="reason_content_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submit-handover-btn">Submit Handover</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="editHandoverModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editHandoverModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editHandoverModalLabel">Edit Handover Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editHandoverForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_record_no" class="form-label">Record No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_record_no" name="record_no" placeholder="Enter Record No">
                                <div class="error-message text-danger mt-1" id="edit_record_no_error"></div>
                                <input type="hidden" id="edit_original_record_no" name="original_record_no">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_request_date" class="form-label">Request Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_request_date" name="request_date">
                                <div class="error-message text-danger mt-1" id="edit_request_date_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_employee_no" class="form-label">Handover Employee ID <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_employee_no" name="employee_no" placeholder="Type or search handover employee ID">
                                    <button class="btn btn-link edit-search-employee-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_employee_no_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-info d-flex align-items-center p-2 bg-light rounded border">
                                    <div>
                                        <div><strong>Employee Name:</strong> <span id="edit_employee_name_display">-</span></div>
                                        <div><strong>Section:</strong> <span id="edit_section_name_display">-</span></div>
                                    </div>
                                </div>
                                <input type="hidden" id="edit_employee_name" name="employee_name">
                                <input type="hidden" id="edit_section_name" name="section_name">
                                <input type="hidden" id="edit_section_code" name="section_code">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_purpose_content" class="form-label">Purpose <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="edit_purpose_content" name="purpose_content" rows="4" placeholder="Describe the purpose"></textarea>
                                <div class="error-message text-danger mt-1" id="edit_purpose_content_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_reason_content" class="form-label">Reason</span></label>
                                <textarea class="form-control" id="edit_reason_content" name="reason_content" rows="4" placeholder="Describe the reason"></textarea>
                                <div class="error-message text-danger mt-1" id="edit_reason_content_error"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="update-handover-btn">Update Handover</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="addHandoverDetailModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addHandoverDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addHandoverDetailModalLabel">Add New Equipment Requested</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addHandoverDetailForm">
                        <input type="hidden" id="detail_recordno" name="recordno">
                        <input type="hidden" id="detail_add_type" name="add_type" value="asset">
                        
                        <div id="asset_number_section" class="row mb-3">
                            <div class="col-md-12">
                                <label for="asset_no" class="form-label">Asset No <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="asset_no" name="asset_no" placeholder="Type or search asset number">
                                    <button class="btn btn-link search-asset-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="asset_no_error"></div>
                            </div>
                            
                            <input type="hidden" id="serial_number" name="serial_number">
                        </div>
                        
                        <div id="serial_number_section" class="row mb-3" style="display: none;">
                            <div class="col-md-12">
                                <label for="serial_number_input" class="form-label">Serial Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="serial_number_input" name="serial_number" placeholder="Type or search serial number">
                                    <button class="btn btn-link search-serial-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="serial_number_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="equipment_name" class="form-label equipment-name-label">Equipment Name</label>
                                <input type="text" class="form-control" id="equipment_name" name="equipment_name" placeholder="Enter equipment name">
                                <div class="error-message text-danger mt-1" id="equipment_name_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="equipment_category" class="form-label">Category</label>
                                <select class="form-select" id="equipment_category" name="equipment_category">
                                    <option value="">--Select Category--</option>
                                    </select>
                                <div class="error-message text-danger mt-1" id="equipment_category_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="delivered_date" class="form-label">Delivered Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="delivered_date" name="delivered_date">
                                <div class="error-message text-danger mt-1" id="delivered_date_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="delivered_sic" class="form-label">Delivered SIC <span class="text-danger">*</span></label>
                                <select class="form-select" id="delivered_sic" name="delivered_sic">
                                    <option value="">--Select SIC--</option>
                                    </select>
                                <input type="hidden" id="delivered_sic_empno" name="delivered_sic_empno">
                                <input type="hidden" id="delivered_sic_name" name="delivered_sic_name">
                                <div class="error-message text-danger mt-1" id="delivered_sic_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="returned_date" class="form-label">Returned Date</label>
                                <input type="date" class="form-control" id="returned_date" name="returned_date">
                            </div>
                            <div class="col-md-6">
                                <label for="returned_sic" class="form-label">Returned SIC</label>
                                <select class="form-select" id="returned_sic" name="returned_sic">
                                    <option value="">--Select SIC--</option>
                                    </select>
                                <input type="hidden" id="returned_sic_empno" name="returned_sic_empno">
                                <input type="hidden" id="returned_sic_name" name="returned_sic_name">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submit-handover-detail-btn">Submit Detail</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="editHandoverDetailModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editHandoverDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editHandoverDetailModalLabel">Edit Equipment Requested</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editHandoverDetailForm">
                        <input type="hidden" id="edit_detail_id" name="id">
                        <input type="hidden" id="edit_detail_recordno" name="recordno">
                        <input type="hidden" id="edit_detail_type" name="detail_type" value="asset">
                        
                        <div id="edit_asset_number_section" class="row mb-3" style="display: none;">
                            <div class="col-md-12">
                                <label for="edit_asset_no" class="form-label">Asset No <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_asset_no" name="asset_no" placeholder="Type or search asset number">
                                    <button class="btn btn-link edit-search-asset-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_asset_no_error"></div>
                            </div>
                            
                            <input type="hidden" id="edit_serial_number" name="serial_number">
                        </div>
                        
                        <div id="edit_serial_number_section" class="row mb-3" style="display: none;">
                            <div class="col-md-12">
                                <label for="edit_serial_number_input" class="form-label">Serial Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="edit_serial_number_input" name="serial_number" placeholder="Type or search serial number">
                                    <button class="btn btn-link edit-search-serial-btn" type="button"
                                    style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="error-message text-danger mt-1" id="edit_serial_number_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_equipment_name" class="form-label edit-equipment-name-label">Equipment Name</label>
                                <input type="text" class="form-control" id="edit_equipment_name" name="equipment_name" placeholder="Enter Equipment Name">
                                <div class="error-message text-danger mt-1" id="edit_equipment_name_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="edit_equipment_category" class="form-label">Category</label>
                                <select class="form-select" id="edit_equipment_category" name="equipment_category">
                                    <option value="">--Select Category--</option>
                                    </select>
                                <div class="error-message text-danger mt-1" id="edit_equipment_category_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_delivered_date" class="form-label">Delivered Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_delivered_date" name="delivered_date">
                                <div class="error-message text-danger mt-1" id="edit_delivered_date_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_delivered_sic" class="form-label">Delivered SIC <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_delivered_sic" name="delivered_sic">
                                    <option value="">--Select SIC--</option>
                                    </select>
                                <input type="hidden" id="edit_delivered_sic_empno" name="delivered_sic_empno">
                                <input type="hidden" id="edit_delivered_sic_name" name="delivered_sic_name">
                                <div class="error-message text-danger mt-1" id="edit_delivered_sic_error"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_returned_date" class="form-label">Returned Date</label>
                                <input type="date" class="form-control" id="edit_returned_date" name="returned_date">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_returned_sic" class="form-label">Returned SIC</label>
                                <select class="form-select" id="edit_returned_sic" name="returned_sic">
                                    <option value="">--Select SIC--</option>
                                    </select>
                                <input type="hidden" id="edit_returned_sic_empno" name="returned_sic_empno">
                                <input type="hidden" id="edit_returned_sic_name" name="returned_sic_name">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="update-handover-detail-btn">Update Detail</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">Select Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="employeeLength" class="form-label mb-0">Show</label>
                        </div>
                        <div class="col-auto">
                            <select id="employeeLength" class="form-select form-select-sm">
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
                                <input type="text" class="form-control" id="searchEmployee">
                            </div>
                        </div>
                    </div>
                    <table id="employeeTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Employee ID</th>
                                <th style="width: 30%;">Name</th>
                                <th style="width: 25%;">Position</th>
                                <th style="width: 35%;">Section</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="assetModal" tabindex="-1" aria-labelledby="assetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assetModalLabel">Select Equipment by Asset No</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="assetLength" class="form-label mb-0">Show</label>
                        </div>
                        <div class="col-auto">
                            <select id="assetLength" class="form-select form-select-sm">
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
                                <input type="text" class="form-control" id="searchAsset">
                            </div>
                        </div>
                    </div>
                    <table id="assetTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Asset No</th>
                                <th style="width: 20%;">Equipment ID</th>
                                <th style="width: 30%;">Serial Number</th>
                                <th style="width: 40%;">Model</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="serialModal" tabindex="-1" aria-labelledby="serialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serialModalLabel">Select Equipment by Serial Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="serialLength" class="form-label mb-0">Show</label>
                        </div>
                        <div class="col-auto">
                            <select id="serialLength" class="form-select form-select-sm">
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
                                <input type="text" class="form-control" id="searchSerial">
                            </div>
                        </div>
                    </div>
                    <table id="serialTable" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Asset No</th>
                                <th style="width: 20%;">Equipment ID</th>
                                <th style="width: 30%;">Serial Number</th>
                                <th style="width: 40%;">Model</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let base_url = '<?= base_url() ?>';
    let isEditMode = false; // Flag to track which form is active (for employee search)
    let currentHandoverRecord = null; // To track the selected handover record for details
    
    // Load initial data for dropdowns/modals once
    loadEquipmentCategories(); // Memuat kategori untuk form Add dan Edit Detail
    loadSystemEmployees('#delivered_sic'); // Memuat SIC untuk form Add Detail
    loadSystemEmployees('#returned_sic'); // Memuat SIC untuk form Add Detail
    loadSystemEmployees('#edit_delivered_sic'); // Memuat SIC untuk form Edit Detail
    loadSystemEmployees('#edit_returned_sic'); // Memuat SIC untuk form Edit Detail

    // Initialize DataTable for Handover Table
    var handoverTable = $('#handoverTable').DataTable({
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
        order: [],
        autoWidth: false,
        ajax: {
            url: base_url + 'TransHandover/getHandoverData',
            dataSrc: function (json) {
                return json;
            }
        },
        columns: [
            {
                data: null,
                className: 'text-center',
                render: function (data, type, row) {
                    return `
                        <div class="d-flex justify-content-center gap-2">
                            <a href="javascript:;" class="btn btn-icon btn-outline-info view-details-btn" data-id="<span class="math-inline">\{row\.th\_recordno\}"\>
<i class\="fa fa\-eye"\></i\>
</a\>
<a href\="javascript\:;" class\="btn btn\-icon btn\-outline\-primary edit\-handover\-btn" data\-id\="</span>{row.th_recordno}">
                                <i class="fa fa-pen-to-square"></i>
                            </a>
                            <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-handover-btn" data-id="${row.th_recordno}">
                                <i class="fa fa-trash-can"></i>
                            </a>
                        </div>
                    `;
                }
            },
            { data: 'th_recordno' },
            {    
                data: 'th_requestdate',
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        return data;
                    }
                    
                    if (!data) {
                        return '';
                    }
                    
                    const date = new Date(data);
                    if (isNaN(date)) {
                        return data;
                    }
                    
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    
                    return `<span class="math-inline">\{day\}/</span>{month}/${year}`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `${row.th_empno_rep} - ${row.th_empname_rep}`;
                }
            },
            { data: 'section_name' },
            { data: 'th_purpose' },
            {
                data: 'th_reason',
                render: function(data, type, row) {
                    return data ? data : '-';
                }
            },
        ]
    });
    
    // Initialize DataTable for Equipment Requested Table (detail modal)
    var handoverDetailTable = $('#handoverDetailTable').DataTable({
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
        order: [],
        autoWidth: false,
        ajax: {
            url: base_url + 'TransHandover/getHandoverDetailData',
            data: function(d) {
                d.requestNo = currentHandoverRecord; // Mengirim recordNo yang sedang aktif
                return d;
            },
            dataSrc: function (json) {
                // Tombol "Add detail" dan "Export PDF" di dalam modal detail
                $('.add-asset-btn, .add-serial-btn, .add-equipment-name-btn, .export-pdf-btn').prop('disabled', !currentHandoverRecord);
                return json || [];
            }
        },
        columns: [
            {
                data: null,
                className: 'text-center',
                render: function (data, type, row) {
                    return `
                        <div class="d-flex justify-content-center gap-2">
                            <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-detail-btn"
                                data-id="<span class="math-inline">\{row\.hd\_id\}"\>
<i class\="fa fa\-pen\-to\-square"\></i\>
</a\>
<a href\="javascript\:;" class\="btn btn\-icon btn\-outline\-danger delete\-detail\-btn"
data\-id\="</span>{row.hd_id}">
                                <i class="fa fa-trash-can"></i>
                            </a>
                        </div>
                    `;
                }
            },
            {
                data: 'hd_assetno',
                render: function(data, type, row) {
                    return data ? data : '-';
                }
            },
            {
                data: 'hd_serialnumber',
                render: function(data, type, row) {
                    return data ? data : '-';
                }
            },
            {
                data: 'hd_equipmentname',
                render: function(data, type, row) {
                    return data ? data : '-';
                }
            },
            {
                data: 'hd_category',
                render: function(data, type, row) {
                    return data ? data : '-';
                }
            },
            {
                data: 'hd_delivereddate',
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        return data;
                    }
                    
                    if (!data) {
                        return '-';
                    }
                    
                    const date = new Date(data);
                    if (isNaN(date)) {
                        return data;
                    }
                    
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    
                    return `<span class="math-inline">\{day\}/</span>{month}/${year}`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.hd_deliveredempno_rep && row.hd_deliveredname_rep) {
                        return `${row.hd_deliveredname_rep}`;
                    }
                    return '-';
                }
            },
            {
                data: 'hd_returneddate',
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        return data;
                    }
                    if (!data) {
                        return '-';
                    }
                    
                    const date = new Date(data);
                    if (isNaN(date)) {
                        return data;
                    }
                    
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    
                    return `<span class="math-inline">\{day\}/</span>{month}/${year}`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.hd_returnedempno_rep && row.hd_returnedname_rep) {
                        return `${row.hd_returnedname_rep}`;
                    }
                    return '-';
                }
            }
        ]
    });
    
    // View details button click handler (di tabel utama)
    $('#handoverTable').on('click', '.view-details-btn', function(e) {
        e.stopPropagation(); // Mencegah event row click lainnya
        const recordNo = $(this).data('id');
        currentHandoverRecord = recordNo;
        
        // Set the record number in the modal title
        $('#detailRecordNoDisplay').text(recordNo);
        
        // Enable the add detail buttons
        $('.add-asset-btn, .add-serial-btn, .add-equipment-name-btn, .export-pdf-btn').prop('disabled', false);
        
        // Reload the handoverDetailTable with the selected handover's details
        handoverDetailTable.ajax.reload();
        
        // Show the detail modal
        $('#handoverDetailModal').modal('show');
    });

    // Add input event handler to clear error state when typing in record_no field
    $('#record_no').on('input', function() {
        // Clear the error message and remove invalid styling when user types
        $('#record_no_error').text('');
        $(this).removeClass('is-invalid');
    });
    
    // Add input event handler for edit_record_no as well
    $('#edit_record_no').on('input', function() {
        // Clear the error message and remove invalid styling when user types
        $('#edit_record_no_error').text('');
        $(this).removeClass('is-invalid');
    });
    
    // Check if record number already exists when editing
    $('#edit_record_no').on('change', function() {
        const newRecordNo = $(this).val();
        const originalRecordNo = $('#edit_original_record_no').val();
        
        // Only check if the number is being changed
        if (newRecordNo && newRecordNo !== originalRecordNo) {
            $.ajax({
                url: base_url + 'TransHandover/checkRecordNoExists', // Menggunakan checkRecordNoExists
                type: 'GET',
                data: { recordNo: newRecordNo },
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.exists) {
                        // Record number already exists
                        $('#edit_record_no_error').text('Record no already exists. Please use a different record no.');
                        $('#edit_record_no').addClass('is-invalid');
                    } else {
                        // Record number is available
                        $('#edit_record_no_error').text('');
                        $('#edit_record_no').removeClass('is-invalid');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking record number:', error);
                }
            });
        }
    });
    
    // Reset add forms when modals are hidden
    $('#addHandoverModal').on('hidden.bs.modal', function() {
        document.getElementById('addHandoverForm').reset();
        $('#addHandoverForm .error-message').text('');
        $('#addHandoverForm .is-invalid').removeClass('is-invalid');
        
        // Reset display spans
        $('#employee_name_display').text('-');
        $('#section_name_display').text('-');
    });
    
    // Reset edit forms when modals are hidden
    $('#editHandoverModal').on('hidden.bs.modal', function() {
        $('#editHandoverForm .error-message').text('');
        $('#editHandoverForm .is-invalid').removeClass('is-invalid');
        
        // Reset display spans
        $('#edit_employee_name_display').text('-');
        $('#edit_section_name_display').text('-');
    });
    
    // Edit handover button click handler
    $('#handoverTable').on('click', '.edit-handover-btn', function(e) {
        e.stopPropagation(); // Prevent row selection
        const id = $(this).data('id');
        
        // Reset form
        document.getElementById('editHandoverForm').reset();
        $('#edit_employee_name_display').text('-');
        $('#edit_section_name_display').text('-');
        $('#edit_record_no_error').text('');
        $('#edit_record_no').removeClass('is-invalid');
        
        // Show modal with loading state
        $('#editHandoverModal').modal('show');
        
        // Get handover data
        $.ajax({
            url: base_url + 'TransHandover/getHandoverById',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const handover = response.data;
                    
                    // Populate form fields
                    $('#edit_record_no').val(handover.th_recordno);
                    $('#edit_original_record_no').val(handover.th_recordno);
                    
                    // Format date for date input (YYYY-MM-DD)
                    if (handover.th_requestdate) {
                        const requestDate = new Date(handover.th_requestdate);
                        const formattedDate = requestDate.toISOString().split('T')[0];
                        $('#edit_request_date').val(formattedDate);
                    }
                    
                    $('#edit_employee_no').val(handover.th_empno_rep);
                    $('#edit_employee_name').val(handover.th_empname_rep);
                    $('#edit_section_code').val(handover.th_sectioncode_rep);
                    $('#edit_section_name').val(handover.section_name);
                    
                    // Update display spans
                    $('#edit_employee_name_display').text(handover.th_empname_rep);
                    $('#edit_section_name_display').text(handover.section_name);
                    
                    $('#edit_purpose_content').val(handover.th_purpose);
                    $('#edit_reason_content').val(handover.th_reason);
                    
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load Equipment Requested',});
                    $('#editHandoverModal').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching Equipment Requested:', error);
                $('#editHandoverModal').modal('hide');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load Equipment Requested. Please try again.'
                });
            }
        });
    });
    
    // Handle manual employee ID input
    $('#employee_no, #edit_employee_no').on('change', function() {
        const employeeId = $(this).val();
        const isEditMode = $(this).attr('id') === 'edit_employee_no';
        const prefix = isEditMode ? 'edit_' : '';
        
        if (!employeeId) {
            $(`#${prefix}employee_name`).val('');
            $(`#${prefix}section_name`).val('');
            $(`#${prefix}section_code`).val('');
            $(`#${prefix}employee_name_display`).text('-');
            $(`#${prefix}section_name_display`).text('-');
            return;
        }
        
        // Fetch employee details by ID
        $.ajax({
            url: base_url + 'TransHandover/getEmployeeDetails', // Menggunakan getEmployeeDetails
            type: 'GET',
            data: { employeeId: employeeId },
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    // Fill employee info
                    $(`#${prefix}employee_name`).val(response.data.em_emplname);
                    $(`#${prefix}section_name`).val(response.data.sec_section);
                    $(`#${prefix}section_code`).val(response.data.em_sectioncode);
                    
                    // Update display spans
                    $(`#${prefix}employee_name_display`).text(response.data.em_emplname);
                    $(`#${prefix}section_name_display`).text(response.data.sec_section);
                    
                    // Clear validation errors if any
                    $(`#${prefix}employee_no_error`).text('');
                    $(`#${prefix}employee_no`).removeClass('is-invalid');
                } else {
                    // Show validation error
                    $(`#${prefix}employee_name`).val('');
                    $(`#${prefix}section_name`).val('');
                    $(`#${prefix}section_code`).val('');
                    $(`#${prefix}employee_name_display`).text('-');
                    $(`#${prefix}section_name_display`).text('-');
                    $(`#${prefix}employee_no_error`).text('Employee not found');
                    $(`#${prefix}employee_no`).addClass('is-invalid');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching employee details:', error);
                $(`#${prefix}employee_no_error`).text('Error fetching employee details');
                $(`#${prefix}employee_no`).addClass('is-invalid');
            }
        });
    });
    
    // Employee search buttons click
    $('.search-employee-btn, .edit-search-employee-btn').on('click', function() {
        // Set flag based on which button was clicked
        isEditMode = $(this).hasClass('edit-search-employee-btn');
        $('#employeeModal').modal('show');
        
        // Reset search input and data
        $('#searchEmployee').val('');
        initEmployeeDataTable();
    });
    
    // Reset employee search when modal is hidden
    $('#employeeModal').on('hidden.bs.modal', function() {
        $('#searchEmployee').val('');
        if (employeeDataTable) {
            employeeDataTable.destroy();
            employeeDataTable = null;
        }
    });
    
    // Initialize DataTable for employee search
    let employeeDataTable = null;
    function initEmployeeDataTable() {
        // Destroy table if it already exists
        if (employeeDataTable) {
            employeeDataTable.destroy();
        }
        
        // Create new DataTable
        employeeDataTable = $('#employeeTable').DataTable({
            processing: true,
            serverSide: false,
            paging: true,
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            searching: false, // Using custom search
            ordering: true,
            order: [[1, 'asc']], // Default sort by name
            ajax: {
                url: base_url + 'TransHandover/searchEmployees',
                type: 'GET',
                data: function(d) {
                    d.search = $('#searchEmployee').val() || '';
                    return d;
                },
                dataSrc: function(json) {
                    return json;
                }
            },
            columns: [
                { data: 'em_emplcode', width: '10%' },
                { data: 'em_emplname', width: '30%' },
                { data: 'pm_positionname', width: '25%' },
                { data: 'sec_section', width: '35%' }
            ],
            columnDefs: [
                {
                    targets: '_all',
                    defaultContent: '-'
                }
            ],
            dom: 't<"bottom"ip>' // Simpler DOM structure
        });
        
        // Set up length change
        $('#employeeLength').on('change', function() {
            const newLength = $(this).val();
            employeeDataTable.page.len(newLength).draw();
        });
        
        // Add search functionality with debounce
        let searchTimeout;
        $('#searchEmployee').off('keyup').on('keyup', function() {
            clearTimeout(searchTimeout);
            
            searchTimeout = setTimeout(function() {
                employeeDataTable.ajax.reload();
            }, 300);
        });
    }
    
    // Handle employee selection from DataTable
    $('#employeeTable tbody').on('click', 'tr', function() {
        if (employeeDataTable) {
            const data = employeeDataTable.row(this).data();
            if (!data) return;
            
            if (isEditMode) {
                // Edit form
                $('#edit_employee_no').val(data.em_emplcode);
                $('#edit_employee_name').val(data.em_emplname);
                $('#edit_section_name').val(data.sec_section);
                $('#edit_section_code').val(data.em_sectioncode);
                
                // Update display spans
                $('#edit_employee_name_display').text(data.em_emplname);
                $('#edit_section_name_display').text(data.sec_section);
                
                // Clear any validation errors
                $('#edit_employee_no_error').text('');
                $('#edit_employee_no').removeClass('is-invalid');
            } else {
                // Add form
                $('#employee_no').val(data.em_emplcode);
                $('#employee_name').val(data.em_emplname);
                $('#section_name').val(data.sec_section);
                $('#section_code').val(data.em_sectioncode);
                
                // Update display spans
                $('#employee_name_display').text(data.em_emplname);
                $('#section_name_display').text(data.sec_section);
                
                // Clear any validation errors
                $('#employee_no_error').text('');
                $('#employee_no').removeClass('is-invalid');
            }
            
            $('#employeeModal').modal('hide');
        }
    });
    
    // Validate handover form
    function validateHandoverForm(formId) {
        let isValid = true;
        const form = document.getElementById(formId);
        
        // Clear previous error messages that aren't already set by other validations
        form.querySelectorAll('.error-message:empty').forEach(el => el.textContent = '');
        
        // Check if it's add or edit form
        const prefix = formId === 'editHandoverForm' ? 'edit_' : '';
        
        // Validate record number (for both add and edit forms)
        const recordNoField = form.querySelector(`#${prefix}record_no`);
        const recordNoError = document.getElementById(`${prefix}record_no_error`);
        
        if (!recordNoField.value) {
            recordNoError.textContent = 'Record number is required';
            recordNoField.classList.add('is-invalid');
            isValid = false;
        } else if (recordNoError.textContent.includes('already exists')) {
            // If there's already an error about existing record, maintain it
            recordNoField.classList.add('is-invalid');
            isValid = false;
        }
        
        // Validate request date
        const requestDateField = form.querySelector(`#${prefix}request_date`);
        if (!requestDateField.value) {
            document.getElementById(`${prefix}request_date_error`).textContent = 'Request date is required';
            requestDateField.classList.add('is-invalid');
            isValid = false;
        }
        
        const employeeNoField = form.querySelector(`#${prefix}employee_no`);
        if (!employeeNoField.value) {
            document.getElementById(`${prefix}employee_no_error`).textContent = 'Employee is required';
            employeeNoField.classList.add('is-invalid');
            isValid = false;
        }
        
        const purposeContentField = form.querySelector(`#${prefix}purpose_content`); // Diubah dari case_content
        if (!purposeContentField.value) {
            document.getElementById(`${prefix}purpose_content_error`).textContent = 'Purpose is required';
            purposeContentField.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }
    
    // Add handover form submission
    $('#submit-handover-btn').on('click', function() {
        // Validate form
        if (!validateHandoverForm('addHandoverForm')) {
            return;
        }
        
        // Get form data
        const formData = new FormData(document.getElementById('addHandoverForm'));
        
        // Disable submit button and show loading state
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
        
        // Send AJAX request
        $.ajax({
            url: base_url + 'TransHandover/storeHandover',
            type: 'POST',
            data: Object.fromEntries(formData),
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    // Reset form and close modal
                    document.getElementById('addHandoverForm').reset();
                    $('#addHandoverModal').modal('hide');
                    
                    // Refresh the handover table
                    handoverTable.ajax.reload();
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to create handover'
                    });
                }
                
                // Re-enable submit button
                $('#submit-handover-btn').prop('disabled', false).text('Submit Handover');
            },
            error: function(xhr, status, error) {
                console.error('Error creating handover:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while creating the handover. Please try again.'
                });
                
                // Re-enable submit button
                $('#submit-handover-btn').prop('disabled', false).text('Submit Handover');
            }
        });
    });
    
    // Update handover form submission
    $('#update-handover-btn').on('click', function() {
        // Validate form
        if (!validateHandoverForm('editHandoverForm')) {
            return;
        }
        
        // Get form data
        const formData = new FormData(document.getElementById('editHandoverForm'));
        
        // Disable update button and show loading state
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
        
        // Send AJAX request
        $.ajax({
            url: base_url + 'TransHandover/updateHandover',
            type: 'POST',
            data: Object.fromEntries(formData),
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    // Close modal
                    $('#editHandoverModal').modal('hide');
                    
                    // Refresh the table
                    handoverTable.ajax.reload();
                    
                    // If the current selected record was updated, refresh the detail table too
                    if (currentHandoverRecord === $('#edit_original_record_no').val()) {
                        // Update currentHandoverRecord if record number changed
                        currentHandoverRecord = $('#edit_record_no').val();
                        handoverDetailTable.ajax.reload();
                    }
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to update handover'
                    });
                }
                
                // Re-enable update button
                $('#update-handover-btn').prop('disabled', false).text('Update Handover');
            },
            error: function(xhr, status, error) {
                console.error('Error updating handover:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the handover. Please try again.'
                });
                
                // Re-enable update button
                $('#update-handover-btn').prop('disabled', false).text('Update Handover');
            }
        });
    });
    
    // Delete handover button click handler
    $('#handoverTable').on('click', '.delete-handover-btn', function(e) {
        e.stopPropagation(); // Prevent row selection
        const id = $(this).data('id');
        
        // Show confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "This handover will be marked as deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX request to delete
                $.ajax({
                    url: base_url + 'TransHandover/deleteHandover',
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
                            
                            // Refresh the handover table
                            handoverTable.ajax.reload();
                            
                            // Clear detail table if the deleted handover was selected
                            if (currentHandoverRecord === id) {
                                currentHandoverRecord = null;
                                handoverDetailTable.clear().draw();
                                // Disable add detail buttons and export PDF button
                                $('.add-asset-btn, .add-serial-btn, .add-equipment-name-btn, .export-pdf-btn').prop('disabled', true);
                            }
                        } else {
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to delete handover',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error deleting handover:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the handover. Please try again.',
                        });
                    }
                });
            }
        });
    });
    
    // Function to handle changes in add type (asset or serial or equipment name)
    function handleAddTypeChange(prefix, isAssetType, isSerialNumberType, isEquipmentNameType) {
        // Clear all input values and errors in asset and serial sections
        $(`#${prefix}asset_no`).val('');
        $(`#${prefix}serial_number`).val('');
        $(`#${prefix}serial_number_input`).val('');
        $(`#${prefix}equipment_name`).val('');

        $(`#${prefix}asset_no_error`).text('');
        $(`#${prefix}serial_number_error`).text('');
        $(`#${prefix}equipment_name_error`).text('');

        $(`#${prefix}asset_no`).removeClass('is-invalid');
        $(`#${prefix}serial_number_input`).removeClass('is-invalid');
        $(`#${prefix}equipment_name`).removeClass('is-invalid');


        if (isAssetType) {
            $(`#${prefix}asset_number_section`).show();
            $(`#${prefix}serial_number_section`).hide();
            $(`#${prefix}equipment_name`).prop('readonly', true).css('background-color', '#e9ecef').attr('placeholder', '');
            $(`.${prefix}equipment-name-label`).html('Equipment Name');
        } else if (isSerialNumberType) {
            $(`#${prefix}asset_number_section`).hide();
            $(`#${prefix}serial_number_section`).show();
            $(`#${prefix}equipment_name`).prop('readonly', true).css('background-color', '#e9ecef').attr('placeholder', '');
            $(`.${prefix}equipment-name-label`).html('Equipment Name');
        } else if (isEquipmentNameType) {
            $(`#${prefix}asset_number_section`).hide();
            $(`#${prefix}serial_number_section`).hide();
            $(`#${prefix}equipment_name`).prop('readonly', false).css('background-color', '').attr('placeholder', 'Enter equipment name');
            $(`.${prefix}equipment-name-label`).html('Equipment Name <span class="text-danger">*</span>');
        }
    }
    
    // Function to load equipment categories
    function loadEquipmentCategories() {
        $.ajax({
            url: base_url + 'TransHandover/getEquipmentCategories', // Menggunakan getEquipmentCategories
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // Reset category dropdown for add form
                $('#equipment_category').empty();
                $('#equipment_category').append('<option value="">--Select Category--</option>');
                
                // Reset category dropdown for edit form
                $('#edit_equipment_category').empty();
                $('#edit_equipment_category').append('<option value="">--Select Category--</option>');
                
                // Populate dropdowns with category data
                if (response && response.length > 0) {
                    response.forEach(category => {
                        $('#equipment_category').append(
                            `<option value="<span class="math-inline">\{category\.equipmentcat\}"\></span>{category.equipmentcat}</option>`
                        );
                        $('#edit_equipment_category').append(
                            `<option value="<span class="math-inline">\{category\.equipmentcat\}"\></span>{category.equipmentcat}</option>`
                        );
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading equipment categories:', error);
            }
        });
    }
    
    // Function to load system employee dropdowns
    function loadSystemEmployees(targetSelector, selectedId = null) {
        // Show loading state
        $(targetSelector).html('<option value="">Loading employees...</option>');
        
        $.ajax({
            url: base_url + 'TransHandover/searchSystemEmployees', // Menggunakan searchSystemEmployees
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // Reset dropdown
                $(targetSelector).empty();
                $(targetSelector).append('<option value="">--Select SIC--</option>');
                
                // Populate dropdown with SIC data
                if (response && response.length > 0) {
                    response.forEach(user => {
                        const selected = (selectedId && user.em_emplcode == selectedId) ? 'selected' : '';
                        $(targetSelector).append(
                            `<option value="<span class="math-inline">\{user\.em\_emplcode\}" data\-name\="</span>{user.em_emplname}" <span class="math-inline">\{selected\}\></span>{user.em_emplname} (${user.em_emplcode})</option>`
                        );
                    });
                } else {
                    $(targetSelector).append('<option value="">No employees available</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading employees:', error);
                $(targetSelector).html('<option value="">Error loading employees</option>');
            }
        });
    }

    // Set current date as default delivered date for add form
    $('#addHandoverDetailModal').on('show.bs.modal', function() {
        const now = new Date();
        const today = now.toISOString().split('T')[0];
        $('#delivered_date').val(today);
        $('#returned_date').attr('min', today); // Set min date for returned date
    });

    // Update returned date minimum when delivered date is manually changed in add form
    $('#delivered_date').on('change', function() {
        const deliveredDate = $(this).val();
        $('#returned_date').attr('min', deliveredDate);
    });
    
    // Update returned date minimum when delivered date is manually changed in edit form
    $('#edit_delivered_date').on('change', function() {
        const deliveredDate = $(this).val();
        $('#edit_returned_date').attr('min', deliveredDate);
        // Reset the returned date if it is earlier than the new delivered date
        const returnedDate = $('#edit_returned_date').val();
        if (returnedDate && new Date(returnedDate) < new Date(deliveredDate)) {
            $('#edit_returned_date').val(deliveredDate);
        }
    });

    // Add by Asset No Button Click Handler
    $('.add-asset-btn').on('click', function() {
        // Reset form and adjust visibility
        document.getElementById('addHandoverDetailForm').reset();
        $('#detail_recordno').val(currentHandoverRecord);
        $('#detail_add_type').val('asset');
        handleAddTypeChange('', true, false, false); // prefix empty for add form
        $('#addHandoverDetailModalLabel').text('Add New Equipment Requested by Asset No');
        
        $('#addHandoverDetailModal').modal('show');
    });
    
    // Add by Serial Number Button Click Handler
    $('.add-serial-btn').on('click', function() {
        // Reset form and adjust visibility
        document.getElementById('addHandoverDetailForm').reset();
        $('#detail_recordno').val(currentHandoverRecord);
        $('#detail_add_type').val('serial');
        handleAddTypeChange('', false, true, false); // prefix empty for add form
        $('#addHandoverDetailModalLabel').text('Add New Equipment Requested by Serial Number');
        
        $('#addHandoverDetailModal').modal('show');
    });

    $('.add-equipment-name-btn').on('click', function() {
        // Reset form and adjust visibility
        document.getElementById('addHandoverDetailForm').reset();
        $('#detail_recordno').val(currentHandoverRecord);
        $('#detail_add_type').val('equipment');
        handleAddTypeChange('', false, false, true); // prefix empty for add form
        $('#addHandoverDetailModalLabel').text('Add New Equipment Requested by Name');
        
        $('#addHandoverDetailModal').modal('show');
    });

    // Handle delivered SIC selection (add form)
    $('#delivered_sic').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const empNo = selectedOption.val();
        const empName = selectedOption.data('name');
        
        $('#delivered_sic_empno').val(empNo);
        $('#delivered_sic_name').val(empName);
    });
    
    // Handle returned SIC selection (add form)
    $('#returned_sic').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const empNo = selectedOption.val();
        const empName = selectedOption.data('name');
        
        $('#returned_sic_empno').val(empNo);
        $('#returned_sic_name').val(empName);
    });
    
    // Asset number input change handler (add form)
    $('#asset_no').on('input', function() {
        $('#asset_no_error').text('');
        $(this).removeClass('is-invalid');
    });
    
    // Serial number input change handler (add form)
    $('#serial_number_input').on('input', function() { // Gunakan serial_number_input
        $('#serial_number_error').text('');
        $(this).removeClass('is-invalid');
    });
    
    // Equipment name input change handler (add form)
    $('#equipment_name').on('input', function() {
        $('#equipment_name_error').text('');
        $(this).removeClass('is-invalid');
    });

    // Handle manual asset number input (add form)
    $('#asset_no').on('change', function() {
        const assetNo = $(this).val();
        if (!assetNo) {
            $('#equipment_name').val('');
            $('#serial_number').val(''); // Clear hidden serial for asset
            $('#asset_no_error').text('');
            return;
        }
        $.ajax({
            url: base_url + 'TransHandover/getEquipmentByAssetNo',
            type: 'GET',
            data: { assetNo: assetNo },
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const equipment = response.data;
                    $('#equipment_name').val(equipment.e_equipmentname); // Sesuaikan dengan kolom m_itequipment
                    $('#serial_number').val(equipment.e_serialnumber || ''); // Sesuaikan dengan kolom m_itequipment
                    $('#asset_no_error').text('');
                    $('#asset_no').removeClass('is-invalid');
                } else {
                    $('#equipment_name').val('');
                    $('#serial_number').val('');
                    $('#asset_no_error').text('Equipment not found with this asset no.');
                    $('#asset_no').addClass('is-invalid');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching equipment details by asset no:', error);
                $('#asset_no_error').text('Error fetching equipment details.');
                $('#asset_no').addClass('is-invalid');
            }
        });
    });

    // Handle manual serial number input (add form)
    $('#serial_number_input').on('change', function() { // Gunakan serial_number_input
        const serialNumber = $(this).val();
        if (!serialNumber) {
            $('#equipment_name').val('');
            $('#serial_number_error').text('');
            return;
        }
        $.ajax({
            url: base_url + 'TransHandover/getEquipmentBySerialNumber',
            type: 'GET',
            data: { serialNumber: serialNumber },
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const equipment = response.data;
                    $('#equipment_name').val(equipment.e_equipmentname); // Sesuaikan dengan kolom m_itequipment
                    $('#asset_no').val(equipment.e_assetno || ''); // Sesuaikan dengan kolom m_itequipment
                    $('#serial_number_error').text('');
                    $('#serial_number_input').removeClass('is-invalid');
                } else {
                    $('#equipment_name').val('');
                    $('#asset_no').val('');
                    $('#serial_number_error').text('Equipment not found with this serial number.');
                    $('#serial_number_input').addClass('is-invalid');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching equipment details by serial number:', error);
                $('#serial_number_error').text('Error fetching equipment details.');
                $('#serial_number_input').addClass('is-invalid');
            }
        });
    });
    
    // Search asset button click handler (add form)
    $('.search-asset-btn').on('click', function() {
        $('#assetModal').modal('show');
        $('#searchAsset').val(''); // Clear search input
        window.isEditAssetSearch = false; // Flag to indicate add mode search
        initAssetDataTable();
    });
    
    // Edit asset search button handler (edit form)
    $('.edit-search-asset-btn').on('click', function() {
        $('#assetModal').modal('show');
        $('#searchAsset').val(''); // Clear search input
        window.isEditAssetSearch = true; // Flag to indicate edit mode search
        initAssetDataTable();
    });
    
    // Initialize DataTable for asset search
    let assetDataTable = null;
    function initAssetDataTable() {
        if (assetDataTable) {
            assetDataTable.destroy();
        }
        assetDataTable = $('#assetTable').DataTable({
            processing: true,
            serverSide: false,
            paging: true,
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            searching: false,
            ordering: true,
            order: [[0, 'asc']],
            ajax: {
                url: base_url + 'TransHandover/searchAssets', // Menggunakan searchAssets
                type: 'GET',
                data: function(d) {
                    d.search = $('#searchAsset').val() || '';
                    return d;
                },
                dataSrc: function(json) {
                    return json || [];
                }
            },
            columns: [
                { data: 'e_assetno', width: '20%', render: function(data) { return data ? data : '-'; } }, // Sesuaikan dengan kolom m_itequipment
                { data: 'e_equipmentid', width: '20%', render: function(data) { return data ? data : '-'; } }, // Sesuaikan dengan kolom m_itequipment
                { data: 'e_serialnumber', width: '30%', render: function(data) { return data ? data : '-'; } }, // Sesuaikan dengan kolom m_itequipment
                { data: 'e_model', width: '30%', render: function(data) { return data ? data : '-'; } }, // Sesuaikan dengan kolom m_itequipment (atau e_equipmentname jika itu yang diinginkan)
            ],
            columnDefs: [{ targets: '_all', defaultContent: '-' }],
            dom: 't<"bottom"ip>'
        });
        $('#assetLength').off('change').on('change', function() {
            assetDataTable.page.len($(this).val()).draw();
        });
        $('#searchAsset').off('keyup').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => assetDataTable.ajax.reload(), 300);
        });
    }
    
    // Handle asset selection from DataTable
    $('#assetTable tbody').on('click', 'tr', function() {
        if (assetDataTable) {
            const data = assetDataTable.row(this).data();
            if (!data) return;
            
            if (window.isEditAssetSearch) {
                $('#edit_asset_no').val(data.e_assetno);
                $('#edit_equipment_name').val(data.e_equipmentname || data.e_model); // Prefer e_equipmentname, fallback to e_model
                $('#edit_serial_number').val(data.e_serialnumber || '');
                $('#edit_asset_no_error').text('').removeClass('is-invalid');
            } else {
                $('#asset_no').val(data.e_assetno);
                $('#equipment_name').val(data.e_equipmentname || data.e_model);
                $('#serial_number').val(data.e_serialnumber || '');
                $('#asset_no_error').text('').removeClass('is-invalid');
                $('#equipment_name_error').text('').removeClass('is-invalid');
            }
            $('#assetModal').modal('hide');
        }
    });
    
    // Serial search button handlers
    $('.search-serial-btn, .edit-search-serial-btn').on('click', function() {
        window.isEditSerialSearch = $(this).hasClass('edit-search-serial-btn');
        $('#serialModal').modal('show');
        $('#searchSerial').val('');
        initSerialDataTable();
    });
    
    // Initialize DataTable for serial search
    let serialDataTable = null;
    function initSerialDataTable() {
        if (serialDataTable) {
            serialDataTable.destroy();
        }
        serialDataTable = $('#serialTable').DataTable({
            processing: true,
            serverSide: false,
            paging: true,
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            searching: false,
            ordering: true,
            order: [[2, 'asc']], // Order by Serial Number
            ajax: {
                url: base_url + 'TransHandover/searchEquipmentBySerialNumber',
                type: 'GET',
                data: function(d) {
                    d.search = $('#searchSerial').val() || '';
                    return d;
                },
                dataSrc: function(json) {
                    return json || [];
                }
            },
            columns: [
                { data: 'e_assetno', width: '20%', render: function(data) { return data ? data : '-'; } },
                { data: 'e_equipmentid', width: '20%', render: function(data) { return data ? data : '-'; } },
                { data: 'e_serialnumber', width: '30%', render: function(data) { return data ? data : '-'; } },
                { data: 'e_model', width: '30%', render: function(data) { return data ? data : '-'; } }, // Atau e_equipmentname
            ],
            columnDefs: [{ targets: '_all', defaultContent: '-' }],
            dom: 't<"bottom"ip>'
        });
        $('#serialLength').off('change').on('change', function() {
            serialDataTable.page.len($(this).val()).draw();
        });
        $('#searchSerial').off('keyup').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => serialDataTable.ajax.reload(), 300);
        });
    }
    
    // Handle serial selection from DataTable
    $('#serialTable tbody').on('click', 'tr', function() {
        if (serialDataTable) {
            const data = serialDataTable.row(this).data();
            if (!data) return;

            if (window.isEditSerialSearch) {
                $('#edit_serial_number_input').val(data.e_serialnumber);
                $('#edit_equipment_name').val(data.e_equipmentname || data.e_model);
                $('#edit_asset_no').val(data.e_assetno || ''); // Clear asset number for serial mode
                $('#edit_serial_number_error').text('').removeClass('is-invalid');
            } else {
                $('#serial_number_input').val(data.e_serialnumber);
                $('#equipment_name').val(data.e_equipmentname || data.e_model);
                $('#asset_no').val(data.e_assetno || ''); // Clear asset number for serial mode
                $('#serial_number_error').text('').removeClass('is-invalid');
                $('#equipment_name_error').text('').removeClass('is-invalid');
            }
            $('#serialModal').modal('hide');
        }
    });

    $('#equipment_category').on('change', function() {
        $('#equipment_category_error').text('');
        $(this).removeClass('is-invalid');
    });
    
    $('#edit_equipment_category').on('change', function() {
        $('#edit_equipment_category_error').text('');
        $(this).removeClass('is-invalid');
    });
    
    // Edit detail button click handler
    $('#handoverDetailTable').on('click', '.edit-detail-btn', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        
        document.getElementById('editHandoverDetailForm').reset();
        $('#edit_asset_number_section').hide();
        $('#edit_serial_number_section').hide();
        $('.edit-equipment-name-label').html('Equipment Name');
        
        $('#edit_detail_id').val(id);
        
        // Load initial data for dropdowns in edit modal (SIC & Categories)
        // Ensure dropdowns are populated before setting values
        loadSystemEmployees('#edit_delivered_sic');
        loadSystemEmployees('#edit_returned_sic');
        loadEquipmentCategories();

        $('#editHandoverDetailModal').modal('show');
        
        $.ajax({
            url: base_url + 'TransHandover/getHandoverDetailById',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const detail = response.data;
                    $('#edit_detail_id').val(detail.hd_id);
                    $('#edit_detail_recordno').val(detail.hd_recordno);
                    
                    // Determine type and show/hide sections
                    if (detail.hd_assetno && detail.hd_assetno !== '-') { // Cek juga nilai '-'
                        $('#edit_detail_type').val('asset');
                        handleAddTypeChange('edit_', true, false, false);
                        $('#edit_asset_no').val(detail.hd_assetno);
                        $('#edit_serial_number').val(detail.hd_serialnumber || ''); // Keep hidden serial for asset mode
                        $('#editHandoverDetailModalLabel').text('Edit Equipment Requested by Asset No');
                    } else if (detail.hd_serialnumber && detail.hd_serialnumber !== '-') { // Cek juga nilai '-'
                        $('#edit_detail_type').val('serial');
                        handleAddTypeChange('edit_', false, true, false);
                        $('#edit_serial_number_input').val(detail.hd_serialnumber);
                        $('#editHandoverDetailModalLabel').text('Edit Equipment Requested by Serial Number');
                    } else {
                        $('#edit_detail_type').val('equipment');
                        handleAddTypeChange('edit_', false, false, true);
                        $('#editHandoverDetailModalLabel').text('Edit Equipment Requested by Name');
                    }
                    
                    $('#edit_equipment_name').val(detail.hd_equipmentname);
                    $('#edit_equipment_category').val(detail.hd_category);
                    
                    // Populate SIC dropdowns after they are loaded
                    $('#edit_delivered_sic').val(detail.hd_deliveredempno_rep).trigger('change'); // Trigger change to update hidden fields
                    $('#edit_returned_sic').val(detail.hd_returnedempno_rep).trigger('change'); // Trigger change to update hidden fields
                    
                    // Set hidden SIC employee number/name directly
                    $('#edit_delivered_sic_empno').val(detail.hd_deliveredempno_rep);
                    $('#edit_delivered_sic_name').val(detail.hd_deliveredname_rep);
                    $('#edit_returned_sic_empno').val(detail.hd_returnedempno_rep);
                    $('#edit_returned_sic_name').val(detail.hd_returnedname_rep);
                    
                    if (detail.hd_delivereddate) {
                        const deliveredDate = new Date(detail.hd_delivereddate);
                        const formattedDeliveredDate = deliveredDate.toISOString().split('T')[0];
                        $('#edit_delivered_date').val(formattedDeliveredDate);
                        $('#edit_returned_date').attr('min', formattedDeliveredDate);
                    } else {
                        $('#edit_delivered_date').val(''); // Clear if null
                    }
                    
                    if (detail.hd_returneddate) {
                        const returnedDate = new Date(detail.hd_returneddate);
                        const formattedReturnedDate = returnedDate.toISOString().split('T')[0];
                        $('#edit_returned_date').val(formattedReturnedDate);
                    } else {
                        $('#edit_returned_date').val(''); // Clear if null
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load detail information',
                    });
                    $('#editHandoverDetailModal').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching detail information:', error);
                $('#editHandoverDetailModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load detail information. Please try again.'
                });
            }
        });
    });
    
    // Handle edit delivered SIC selection
    $('#edit_delivered_sic').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const empNo = selectedOption.val();
        const empName = selectedOption.data('name');
        
        $('#edit_delivered_sic_empno').val(empNo);
        $('#edit_delivered_sic_name').val(empName);
    });
    
    // Handle edit returned SIC selection
    $('#edit_returned_sic').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const empNo = selectedOption.val();
        const empName = selectedOption.data('name');
        
        $('#edit_returned_sic_empno').val(empNo);
        $('#edit_returned_sic_name').val(empName);
    });
    
    // Validate detail form
    function validateDetailForm(formId) {
        let isValid = true;
        const form = document.getElementById(formId);

        form.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        const prefix = formId === 'editHandoverDetailForm' ? 'edit_' : '';
        const addType = $(`#${prefix}detail_add_type`).val(); // Get current add/detail type

        if (addType === 'asset') {
            const assetNoField = form.querySelector(`#${prefix}asset_no`);
            if (!assetNoField.value) {
                document.getElementById(`${prefix}asset_no_error`).textContent = 'Asset number is required.';
                assetNoField.classList.add('is-invalid');
                isValid = false;
            }
        } else if (addType === 'serial') {
            const serialNumberField = form.querySelector(`#${prefix}serial_number_input`);
            if (!serialNumberField.value) {
                document.getElementById(`${prefix}serial_number_error`).textContent = 'Serial number is required.';
                serialNumberField.classList.add('is-invalid');
                isValid = false;
            }
        } else if (addType === 'equipment') {
            const equipmentNameField = form.querySelector(`#${prefix}equipment_name`);
            if (!equipmentNameField.value) {
                document.getElementById(`${prefix}equipment_name_error`).textContent = 'Equipment name is required.';
                equipmentNameField.classList.add('is-invalid');
                isValid = false;
            }
        }

        const deliveredDateField = form.querySelector(`#${prefix}delivered_date`);
        if (!deliveredDateField.value) {
            document.getElementById(`${prefix}delivered_date_error`).textContent = 'Delivered date is required.';
            deliveredDateField.classList.add('is-invalid');
            isValid = false;
        }
        
        const deliveredSicField = form.querySelector(`#${prefix}delivered_sic`);
        if (!deliveredSicField.value) {
            document.getElementById(`${prefix}delivered_sic_error`).textContent = 'Delivered SIC is required.';
            deliveredSicField.classList.add('is-invalid');
            isValid = false;
        }

        return isValid;
    }
    
    // Add handover detail form submission
    $('#submit-handover-detail-btn').on('click', function() {
        if (!validateDetailForm('addHandoverDetailForm')) {
            return;
        }
        
        const formData = new FormData(document.getElementById('addHandoverDetailForm'));
        const formDataObj = Object.fromEntries(formData);
        
        const addType = $('#detail_add_type').val();
        if (addType === 'serial') {
            formDataObj.serial_number = $('#serial_number_input').val();
            formDataObj.asset_no = ''; // Clear asset_no if adding by serial
        } else if (addType === 'equipment') {
            formDataObj.asset_no = '';
            formDataObj.serial_number = '';
        } else { // asset type
            formDataObj.serial_number = $('#serial_number').val(); // Ensure hidden serial is sent for asset type
        }
        
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
        
        $.ajax({
            url: base_url + 'TransHandover/storeHandoverDetail',
            type: 'POST',
            data: formDataObj,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    Swal.fire({ icon: 'success', title: 'Success', text: response.message, showConfirmButton: false, timer: 1500 });
                    document.getElementById('addHandoverDetailForm').reset();
                    $('#addHandoverDetailModal').modal('hide');
                    handoverDetailTable.ajax.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to create detail' });
                }
                $('#submit-handover-detail-btn').prop('disabled', false).text('Submit Detail');
            },
            error: function(xhr, status, error) {
                console.error('Error creating detail:', error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while creating the detail. Please try again.' });
                $('#submit-handover-detail-btn').prop('disabled', false).text('Submit Detail');
            }
        });
    });
    
    // Update Equipment Requested form submission
    $('#update-handover-detail-btn').on('click', function() {
        if (!validateDetailForm('editHandoverDetailForm')) {
            return;
        }
        
        const formData = new FormData(document.getElementById('editHandoverDetailForm'));
        const formDataObj = Object.fromEntries(formData);
        
        const detailType = $('#edit_detail_type').val();
        if (detailType === 'serial') {
            formDataObj.serial_number = $('#edit_serial_number_input').val(); // Use correct field for serial
            formDataObj.asset_no = ''; // Clear asset_no if editing by serial
        } else if (detailType === 'equipment') {
            formDataObj.asset_no = '';
            formDataObj.serial_number = '';
        } else { // asset type
            formDataObj.serial_number = $('#edit_serial_number').val(); // Ensure hidden serial is sent for asset type
        }
        
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
        
        $.ajax({
            url: base_url + 'TransHandover/updateHandoverDetail',
            type: 'POST',
            data: formDataObj,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    Swal.fire({ icon: 'success', title: 'Success', text: response.message, showConfirmButton: false, timer: 1500 });
                    $('#editHandoverDetailModal').modal('hide');
                    handoverDetailTable.ajax.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to update detail' });
                }
                $('#update-handover-detail-btn').prop('disabled', false).text('Update Detail');
            },
            error: function(xhr, status, error) {
                console.error('Error updating detail:', error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while updating the detail. Please try again.' });
                $('#update-handover-detail-btn').prop('disabled', false).text('Update Detail');
            }
        });
    });
    
    // Delete detail button click handler
    $('#handoverDetailTable').on('click', '.delete-detail-btn', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This equipment requested will be marked as deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + 'TransHandover/deleteHandoverDetail',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message, showConfirmButton: false, timer: 1500 });
                            handoverDetailTable.ajax.reload();
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to delete detail' });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error deleting detail:', error);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while deleting the detail. Please try again.' });
                    }
                });
            }
        });
    });

    // Reset modals
    $('#addHandoverDetailModal').on('hidden.bs.modal', function() {
        document.getElementById('addHandoverDetailForm').reset();
        $('#addHandoverDetailForm .error-message').text('');
        $('#addHandoverDetailForm .is-invalid').removeClass('is-invalid');
        // Clear hidden serial number for add form
        $('#serial_number').val('');
        // Ensure manual input fields are cleared
        $('#asset_no').val('');
        $('#serial_number_input').val('');
        $('#equipment_name').val('');
        // Reset dynamic labels/placeholders
        $('.equipment-name-label').html('Equipment Name');
        $('#equipment_name').prop('readonly', true).css('background-color', '#e9ecef').attr('placeholder', '');
    });
    
    $('#editHandoverDetailModal').on('hidden.bs.modal', function() {
        $('#editHandoverDetailForm .error-message').text('');
        $('#editHandoverDetailForm .is-invalid').removeClass('is-invalid');
        // Clear hidden serial number for edit form
        $('#edit_serial_number').val('');
        // Ensure manual input fields are cleared
        $('#edit_asset_no').val('');
        $('#edit_serial_number_input').val('');
        $('#edit_equipment_name').val('');
        // Reset dynamic labels/placeholders
        $('.edit-equipment-name-label').html('Equipment Name');
        $('#edit_equipment_name').prop('readonly', true).css('background-color', '#e9ecef').attr('placeholder', '');
    });
    
    // Reset employee search when modal is hidden
    $('#employeeModal').on('hidden.bs.modal', function() {
        $('#searchEmployee').val('');
        if (employeeDataTable) {
            employeeDataTable.destroy();
            employeeDataTable = null;
        }
    });
    
    // Reset asset search when modal is hidden
    $('#assetModal').on('hidden.bs.modal', function() {
        $('#searchAsset').val('');
        if (assetDataTable) {
            assetDataTable.destroy();
            assetDataTable = null;
        }
    });

    // Reset serial search when modal is hidden
    $('#serialModal').on('hidden.bs.modal', function() {
        $('#searchSerial').val('');
        if (serialDataTable) {
            serialDataTable.destroy();
            serialDataTable = null;
        }
    });

    // Klik tombol Export PDF
    $('.export-pdf-btn').on('click', function() {
        // Pastikan ada record yang dipilih
        if (!currentHandoverRecord) {
            Swal.fire({
                icon: 'warning',
                title: 'No Handover Selected',
                text: 'Please select a handover record first.',
            });
            return;
        }
        // Redirect ke URL export PDF, passing record number sebagai parameter
        window.open(base_url + 'TransHandover/export_pdf?recordNo=' + encodeURIComponent(currentHandoverRecord), '_blank');
    });
});
</script>

<?= $this->endSection() ?>
