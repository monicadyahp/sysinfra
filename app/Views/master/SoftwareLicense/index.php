<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<style>
    /* Readonly style */
    input[readonly], textarea[readonly] {
        background-color: #e9ecef; /* Light gray to indicate readonly */
        cursor: not-allowed;
    }
    /* Spacing at the bottom to prevent the DataTable from overlapping with the modal */
    .card {
        margin-bottom: 2rem;
    }
    /* force uppercase input */
    #ref_num_subs_id,
    #edit_ref_num_subs_id,
    #product_key,
    #edit_product_key,
    #license_partner,
    #edit_license_partner,
    #product_name,
    #edit_product_name,
    #add_pc_asset_name,
    #add_pc_asset_no, 
    #add_pc_serial_number,
    #add_pc_asset_id, 
    #edit_pc_asset_name,
    #edit_pc_asset_no, 
    #edit_pc_serial_number,
    #edit_pc_asset_id
    {
        text-transform: uppercase;
    }
    /* product-desc full width */
    #product_desc,
    #edit_product_desc {
        width: 100%;
    }

    .card-datatable.table-responsive {
        overflow-x: auto;
        width: 100%;
    }

    /* Styles for licensedPcTable rows that are overlicensed */
    .overlicensed-row {
        background-color:rgb(206, 206, 203) !important; /* Light orange, or choose a distinct color */
        color:rgb(105, 105, 105); /* Darker orange text */
        font-weight: bold;
    }
    /* Ensure the text is visible on the row */
    .overlicensed-row td {
        color:rgb(105, 105, 105);
    }

    #licensedPcTable th,
    #licensedPcTable td {
        white-space: nowrap; /* Mencegah teks wrapping */
    }

    /* Atur lebar kolom dalam persentase untuk flexibilitas */
    #licensedPcTable thead th:nth-child(1),
    #licensedPcTable tbody td:nth-child(1) { width: 10%; } /* Action */
    #licensedPcTable thead th:nth-child(2),
    #licensedPcTable tbody td:nth-child(2) { width: 15%; } /* Asset Name */
    #licensedPcTable thead th:nth-child(3),
    #licensedPcTable tbody td:nth-child(3) { width: 10%; } /* Asset No */
    #licensedPcTable thead th:nth-child(4),
    #licensedPcTable tbody td:nth-child(4) { width: 10%; } /* Asset ID */
    #licensedPcTable thead th:nth-child(5),
    #licensedPcTable tbody td:nth-child(5) { width: 15%; } /* Serial Number */

    .mb-3.action-buttons {
        margin-bottom: 1rem !important;
    }

    /* Styles for editLicensedPcTable (similar to licensedPcTable) */
    #editLicensedPcTable th,
    #editLicensedPcTable td {
        white-space: nowrap; /* Prevent text wrapping */
    }

    /* Adjust column widths for flexibility in editLicensedPcTable */
    #editLicensedPcTable thead th:nth-child(1),
    #editLicensedPcTable tbody td:nth-child(1) { width: 10%; } /* Action */
    #editLicensedPcTable thead th:nth-child(2),
    #editLicensedPcTable tbody td:nth-child(2) { width: 15%; } /* Asset Name */
    #editLicensedPcTable thead th:nth-child(3),
    #editLicensedPcTable tbody td:nth-child(3) { width: 10%; } /* Asset No */
    #editLicensedPcTable thead th:nth-child(4),
    #editLicensedPcTable tbody td:nth-child(4) { width: 10%; } /* Asset ID */
    #editLicensedPcTable thead th:nth-child(5),
    #editLicensedPcTable tbody td:nth-child(5) { width: 15%; } /* Serial Number */

    /* Styles for editLicensedPcInfoInModal */
    #editLicensedPcInfoInModal {
        margin-top: 5px;
        margin-left: 1rem;
        font-weight: bold;
        display: block !important;
    }

    /* Style for the license info text */
    #licensedPcInfoInModal { /* Changed ID to match new placement in modal */
        margin-top: 5px; /* Adjust as needed for spacing */
        font-weight: bold;
        padding-left: 30px; /* Align with button padding */
    }

        /* Styles for the single row license detail table */
        #licenseDetailOverviewTable {
        width: 100%;
        margin-bottom: 1rem; /* Tambahkan sedikit jarak di bawah tabel */
        font-size: 0.9rem; /* Ukuran font sesuai dengan datatable standar Bootstrap/Datatables */
        border-collapse: collapse; /* Untuk tampilan border yang rapi */
        /* Tambahkan beberapa gaya dari datatables-basic table */
        color: #4a5568; /* Warna teks umum */
    }
    #licenseDetailOverviewTable thead th,
    #licenseDetailOverviewTable tbody td {
        padding: 0.8rem 1rem; /* Sesuaikan padding agar lebih mirip datatable */
        border: 1px solid #e2e8f0; /* Border warna abu-abu muda, seperti datatable */
        white-space: nowrap; /* Mencegah wrap pada satu baris */
        overflow: hidden; /* Sembunyikan overflow */
        text-overflow: ellipsis; /* Tampilkan elipsis jika teks terlalu panjang */
        max-width: 150px; /* Batasi lebar kolom agar tidak terlalu melebar */
        vertical-align: top; /* Agar konten sejajar di atas jika ada wrapping yang tidak terduga */
    }
    #licenseDetailOverviewTable thead th {
        background-color:rgb(221, 231, 241); /* Background header yang lebih terang, seperti datatable */
        text-align: left;
        font-weight: 600; /* Tebal sedikit */
        color: #6e6b7b; /* Warna teks header */
        font-size: 0.8125rem; /* Ukuran font header */
        text-transform: uppercase; /* Uppercase seperti header datatable */
    }
    #licenseDetailOverviewTable tbody tr {
        background-color: #fff;
    }
    /* Hover effect untuk baris tabel detail */
    #licenseDetailOverviewTable tbody tr:hover {
        background-color: #f5f5f5; /* Sedikit terang saat di-hover */
    }

    /* Ensure the responsive container still works */
    #licensedPcsListModal .modal-body .table-responsive {
        overflow-x: auto;
        margin-left: 0; /* Pastikan tidak ada indentasi tambahan */
        margin-right: 0; /* Pastikan tidak ada indentasi tambahan */
    }

    /* Style for the license info text - pastikan sesuai posisi baru */
    #licensedPcInfoInModal {
        margin-top: 5px; /* Adjust as needed for spacing */
        margin-left: 1rem; /* Tambahkan margin kiri agar sejajar dengan isi tabel */
        font-weight: bold;
        display: block !important; /* Pastikan selalu tampil jika ada info */
    }

        /* --- TAMBAHKAN KODE CSS INI DI BAWAH --- */
        #editDetailDetModal .modal-dialog {
        max-width: 90%; /* Menggunakan 90% lebar viewport, Anda bisa sesuaikan */
        width: auto !important; /* Biarkan lebar menyesuaikan konten */
        margin: 1.75rem auto; /* Atur margin agar tidak terlalu mepet ke atas/bawah */
    }

    #editDetailDetModal .modal-content {
        height: auto; /* Biarkan tinggi menyesuaikan konten */
        min-height: 80vh; /* Setidaknya 80% dari tinggi viewport */
        max-height: 95vh; /* Maksimal 95% dari tinggi viewport */
        display: flex; /* Untuk flexbox layout di modal-content */
        flex-direction: column; /* Konten diatur secara vertikal */
    }

    #editDetailDetModal .modal-body {
        flex-grow: 1; /* Biarkan modal-body mengisi sisa ruang yang tersedia */
        overflow-y: auto; /* Aktifkan scrolling hanya pada modal-body jika kontennya melebihi */
        padding-top: 0; /* Kurangi padding atas jika perlu */
        padding-bottom: 0; /* Kurangi padding bawah jika perlu */
    }

    /* Jika ada bagian lain yang perlu di-scroll di dalam modal-body, pastikan mereka memiliki tinggi yang terbatas */
    #editDetailDetModal .modal-body .table-responsive {
        /* Properti ini sudah ada, pastikan tidak ada tinggi tetap yang menghalangi */
        height: auto; /* Biarkan tinggi tabel responsif menyesuaikan */
        overflow-y: auto; /* Pastikan scrolling Y aktif untuk tabel jika perlu */
        max-height: calc(100vh - 400px); /* Contoh: 100vh - (tinggi header modal + tinggi footer modal + tinggi form di atas datatable + padding) */
        /* Anda perlu menyesuaikan '400px' ini berdasarkan seberapa banyak ruang yang diambil oleh elemen lain di dalam modal-body */
    }
</style>

<div class="card">
    <div class="card-header">
            <h4 class="card-title">Software License</h4>
    </div>
    <p class="demo" style="padding-left: 30px; padding-top: 12px;">
    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addDetailDetModal">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            Add Software License
        </button>
        <a href="<?= base_url('SoftwareLicense/exportExcel') ?>" class="btn btn-success" id="exportExcelBtn">
            <span class="btn-label">
                <i class="fa fa-file-excel"></i>
            </span>
            Export Excel
        </a>
    </p>

    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelSoftwareLicense">
            <thead class="table-light">
                <tr>
                    <th style="width: 15%">Action</th> <th>ID</th>
                    <th>Type</th>
                    <th>License Category</th> <th>Ref. Number / Subs ID</th>
                    <th>PO Number</th>
                    <th>License Partner</th>
                    <th>Order Date</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Product Name</th>
                    <th>Product Qty</th>
                    <th>Product Desc</th>
                    <th>Product Key</th>
                    <th>Organization</th>
                    <th>Last Update</th>
                    <th>Last User</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        </div>

        <div class="modal fade" id="addDetailDetModal" tabindex="-1" aria-labelledby="addDetailDetModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDetailDetModalLabel">Add Software License</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addDataForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12"> <label for="license_id" class="form-label" style="display: none;">License ID</label>
                                <input type="hidden" class="form-control bg-light" id="license_id" name="license_id" readonly required>
                                <input type="hidden" id="add_po_sourced_from_finder" name="po_sourced_from_finder" value="0">
                            </div>
                            <div class="col-md-4">
                                <label for="license_type_category" class="form-label">License Type Category</label>
                                <select class="form-select" id="license_type_category" name="license_type_category">
                                    <option value="" disabled selected>Select Category</option>
                                    <option value="Perpetual License">Perpetual License</option>
                                    <option value="Subscription License">Subscription License</option>
                                    <option value="Perpetual + Subscription">Perpetual + Subscription</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="license_type" class="form-label">License Type</label>
                                <select class="form-select" id="license_type" name="license_type">
                                    <option value="" disabled selected>Select License Type</option>
                                    <option value="-">-</option>
                                    <option value="Operating System">Operating System</option>
                                    <option value="Application">Application</option>
                                    <option value="Utility">Utility</option>
                                    <option value="Developer Tools">Developer Tools</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="ref_num_subs_id" class="form-label">Ref. Num / Subs ID</label>
                                <input type="text" class="form-control" id="ref_num_subs_id" name="ref_num_subs_id">
                                <div class="invalid-feedback" id="ref_num_subs_id_error"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="po_number" class="form-label">PO Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="po_number" name="po_number" required>
                                    <button type="button" class="btn btn-outline-secondary search-po-btn" data-bs-toggle="modal" data-bs-target="#poNumberListModal">
                                        <i class="fa fa-search"></i> </button>
                                </div>
                                <div class="invalid-feedback" id="po_number_error"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="license_partner" class="form-label">License Partner</label>
                                <input type="text" class="form-control" id="license_partner" name="license_partner">
                            </div>
                            <div class="col-md-4">
                                <label for="order_date" class="form-label">Order Date</label>
                                <input type="date" class="form-control" id="order_date" name="order_date">
                            </div>
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>
                            <div class="col-md-4">
                                <label for="product_name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="product_name" name="product_name">
                            </div>
                            <div class="col-md-4">
                                <label for="product_qty" class="form-label">Product Qty</label>
                                <input type="number" class="form-control" id="product_qty" name="product_qty">
                            </div>
                            <div class="col-md-4">
                                <label for="organization" class="form-label">Organization</label>
                                <select class="form-select" id="organization" name="organization">
                                    <option value="" disabled>Select Organization</option>
                                    <option value="PT JST Indonesia" selected>PT JST Indonesia</option>
                                    <option value="PT JST Batam">PT JST Batam</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="product_key" class="form-label">Product Key</label>
                                <input type="text" class="form-control" id="product_key" name="product_key">
                                <div class="invalid-feedback" id="product_key_error"></div>
                            </div>
                            <div class="col-md-12">
                                <label for="product_desc" class="form-label">Product Desc</label>
                                <textarea class="form-control" id="product_desc" name="product_desc" rows="1"></textarea>
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

        <div class="modal fade" id="editDetailDetModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="editDetailDetModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editDetailDetModalLabel">Edit Software License Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="editDataForm">
                        <div class="modal-body">
                            <div class="row g-3">
                                <input type="hidden" id="edit_td_id" name="td_id">

                                <div class="col-md-4" style="display: none;"> <label for="edit_license_id" class="form-label">License ID</label>
                                    <input type="text" class="form-control bg-light" id="edit_license_id" name="license_id" readonly> 
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_license_type_category" class="form-label">License Type Category</label> <select class="form-select" id="edit_license_type_category" name="license_type_category">
                                        <option value="" disabled selected>Select Category</option>
                                        <option value="Perpetual License">Perpetual License</option>
                                        <option value="Subscription License">Subscription License</option>
                                        <option value="Perpetual + Subscription">Perpetual + Subscription</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_license_type" class="form-label">License Type</label>
                                    <select class="form-select" id="edit_license_type" name="license_type">
                                        <option value="" disabled selected>Select License Type</option>
                                        <option value="-">-</option>
                                        <option>Operating System</option>
                                        <option>Application</option>
                                        <option>Utility</option>
                                        <option>Developer Tools</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_ref_num_subs_id" class="form-label">Ref. Num / Subs ID</label>
                                    <input type="text" class="form-control" id="edit_ref_num_subs_id" name="ref_num_subs_id">
                                    <div class="invalid-feedback" id="edit_ref_num_subs_id_error"></div>
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_po_number" class="form-label">PO Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="edit_po_number" name="po_number" required>
                                        <button class="btn btn-outline-secondary edit-search-po-btn" type="button"
                                                    data-bs-toggle="modal" data-bs-target="#poNumberListModal">
                                                    <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="edit_po_number_error"></div>
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_license_partner" class="form-label">License Partner</label>
                                    <input type="text" class="form-control" id="edit_license_partner" name="license_partner">
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_order_date" class="form-label">Order Date</label>
                                    <input type="date" class="form-control" id="edit_order_date" name="order_date">
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="edit_start_date" name="start_date">
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="edit_end_date" name="end_date">
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_product_name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="edit_product_name" name="product_name">
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_product_qty" class="form-label">Product Qty</label>
                                    <input type="number" class="form-control" id="edit_product_qty" name="product_qty">
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_organization" class="form-label">Organization</label>
                                    <select class="form-select" id="edit_organization" name="organization">
                                        <option value="" disabled>Select Organization</option>
                                        <option value="PT JST Indonesia" selected>PT JST Indonesia</option>
                                        <option value="PT JST Batam">PT JST Batam</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_product_key" class="form-label">Product Key</label>
                                    <input type="text" class="form-control" id="edit_product_key" name="product_key">
                                    <div class="invalid-feedback" id="edit_product_key_error"></div>
                                </div>

                                <div class="col-md-12">
                                    <label for="edit_product_desc" class="form-label">Product Desc</label>
                                    <textarea class="form-control" id="edit_product_desc" name="product_desc" rows="1"></textarea>
                                </div>
                            </div>
                                </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Close
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="update">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                    <div class="modal-body pt-0">
                                        <hr class="my-4">
                                        <h5 class="mb-3">Licensed PCs</h5>
                                        <div class="mb-4 action-buttons" style="padding-left: 15px; padding-right: 15px;">
                                            <div class="row mb-3">
                                                <div class="col-md-12 d-flex gap-3 flex-column">
                                                    <!-- <button type="button" class="btn btn-primary add-pc-to-edit-modal-btn" style="width: 220px;" data-bs-toggle="modal" data-bs-target="#addLicensedPcModal">
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        Add PC
                                                    </button> -->
                                                    <span id="editLicensedPcInfoInModal" style="display: none;"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="datatables-basic table table-bordered" id="editLicensedPcTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <!-- <th>Action</th> -->
                                                        <th>Asset Name</th>
                                                        <th>Asset Number</th>
                                                        <th>Asset ID</th>
                                                        <th>Serial Number</th>
                                                        <th>User</th>
                                                        <th>Position</th>
                                                        <th>Last Update</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="poNumberListModal" tabindex="-1" aria-labelledby="poNumberListModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="poNumberListModalLabel">PO Number List</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table id="poNumberTable" class="table table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Product Name</th>
                                    <th>Order Date</th>
                                    <th>License Partner</th>
                                    <th>Product Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="licensedPcsListModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="licensedPcsListModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="licensedPcsListModalLabel">Licensed PCs for Software ID: <span id="modalLicenseIdDisplay"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" id="licenseDetailOverviewTable" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Action</th> <th>ID</th>
                                    <th>Type</th>
                                    <th>License Category</th>
                                    <th>Ref. Number / Subs ID</th>
                                    <th>PO Number</th>
                                    <th>License Partner</th>
                                    <th>Order Date</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Product Name</th>
                                    <th>Product Qty</th>
                                    <th>Product Desc</th>
                                    <th>Product Key</th>
                                    <th>Organization</th>
                                    <th>Last Update</th>
                                    <th>Last User</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                        <div class="mb-4 action-buttons" style="padding-left: 30px; padding-right: 30px;">
                            <div class="row mb-3">
                                <div class="col-md-9 d-flex gap-3 flex-column">
                                    <button type="button" class="btn btn-primary add-pc-btn" style="width: 220px;" data-bs-toggle="modal" data-bs-target="#addLicensedPcModal">
                                        <span class="btn-label">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                        Add PC
                                    </button>
                                    <span id="licensedPcInfoInModal" style="display: none;"></span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                        <table class="datatables-basic table table-bordered" id="licensedPcTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Action</th>
                                    <th>Asset Name</th>
                                    <th>Asset Number</th>
                                    <th>Asset ID</th>
                                    <th>Serial Number</th>
                                    <th>User</th> <th>Position</th> <th>Last Update</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addLicensedPcModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="addLicensedPcModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLicensedPcModalLabel">Add Licensed PC</h5>
                    <button type="button" class="btn-close" id="closeAddLicensedPcModalHeader" aria-label="Close"></button>
                </div>
                    <div class="modal-body">
                        <form id="addLicensedPcForm">
                            <input type="hidden" id="licensed_pc_license_id" name="tl_id">
                            <input type="hidden" id="add_pc_e_id" name="e_id">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="add_pc_asset_no" class="form-label">Asset Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="add_pc_asset_no" name="asset_no" placeholder="Enter Asset Number">
                                        <button class="btn btn-outline-secondary search-pc-asset-no-btn" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                    <div class="error-message text-danger mt-1" id="add_pc_asset_no_error"></div>
                                </div>
                                <div class="col-md-12">
                                    <label for="add_pc_asset_name" class="form-label">Asset Name</label>
                                    <input type="text" class="form-control" id="add_pc_asset_name" name="pc_name">
                                </div>
                                <div class="col-md-12">
                                    <label for="add_pc_serial_number" class="form-label">Serial Number</label>
                                    <input type="text" class="form-control" id="add_pc_serial_number" name="serial_number">
                                </div>
                                <div class="col-md-12">
                                    <label for="add_pc_asset_id" class="form-label">Asset ID</label>
                                    <input type="text" class="form-control" id="add_pc_asset_id" name="pc_id">
                                    <div class="error-message text-danger mt-1" id="add_pc_asset_id_error"></div> </div>

                                <div class="col-md-12">
                                    <label for="add_pc_employee_name" class="form-label">User</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="add_pc_employee_name" name="employee_name" placeholder="Select User" readonly>
                                        <input type="hidden" id="add_pc_employee_code" name="employee_code">
                                        <input type="hidden" id="add_pc_position_code" name="position_code">
                                        <button class="btn btn-outline-secondary search-employee-btn" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                </div>
                                </form>
                                </div>
                                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelAddLicensedPcModalFooter">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submit-licensed-pc-btn">Submit PC</button>
                </div>
            </div>
        </div>
        </div>

        
        <div class="modal fade" id="editLicensedPcModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editLicensedPcModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editLicensedPcModalLabel">Edit Licensed PC</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editLicensedPcForm">
                            <input type="hidden" id="edit_licensed_pc_id" name="ld_id">
                            <input type="hidden" id="edit_licensed_pc_license_id" name="tl_id">
                            <input type="hidden" id="edit_pc_e_id" name="e_id">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="edit_pc_asset_no" class="form-label">Asset Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="edit_pc_asset_no" name="asset_no" placeholder="Enter Asset Number">
                                        <button class="btn btn-outline-secondary search-pc-asset-no-btn" type="button"
                                        style="background-color: white; border: 1px solid #ced4da; border-left: none; color: #6c757d; padding: 0.375rem 0.75rem; border-radius: 0 0.25rem 0.25rem 0; box-shadow: none;">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                    <div class="error-message text-danger mt-1" id="edit_pc_asset_no_error"></div>
                                </div>
                                <div class="col-md-12">
                                    <label for="edit_pc_asset_name" class="form-label">Asset Name</label>
                                    <input type="text" class="form-control" id="edit_pc_asset_name" name="pc_name">
                                </div>
                                <div class="col-md-12">
                                    <label for="edit_pc_serial_number" class="form-label">Serial Number</label>
                                    <input type="text" class="form-control" id="edit_pc_serial_number" name="serial_number">
                                </div>
                                <div class="col-md-12">
                                    <label for="edit_pc_asset_id" class="form-label">Asset ID</label>
                                    <input type="text" class="form-control" id="edit_pc_asset_id" name="pc_id">
                                    <div class="error-message text-danger mt-1" id="edit_pc_asset_id_error"></div> </div>

                                <div class="col-md-12">
                                    <label for="edit_pc_employee_name" class="form-label">User</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="edit_pc_employee_name" name="employee_name" placeholder="Select User" readonly>
                                        <input type="hidden" id="edit_pc_employee_code" name="employee_code">
                                        <input type="hidden" id="edit_pc_position_code" name="position_code">
                                        <button class="btn btn-outline-secondary search-employee-btn" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="update-licensed-pc-btn">Update PC</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="pcSearchModal" tabindex="-1" aria-labelledby="pcSearchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pcSearchModalLabel">Select PC</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-auto">
                                <label for="pcLength" class="form-label mb-0">Show</label>
                            </div>
                            <div class="col-auto">
                                <select id="pcLength" class="form-select form-select-sm">
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
                                    <input type="text" class="form-control" id="searchPc">
                                </div>
                            </div>
                        </div>
                        <table id="pcTable" class="table table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Asset No</th>
                                    <th>PC Name</th>
                                    <th>Asset ID</th>
                                    <th>Serial Number</th>
                                    <th>Brand / Model</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="employeeSearchModal" tabindex="-1" aria-labelledby="employeeSearchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="employeeSearchModalLabel">Select Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-auto">
                                <label for="employeeLength" class="form-label mb-0">Show</label>
                            </div>
                            <div class="col-auto">
                                <select id="employeeLength" class="form-select form-select-sm">
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
                                    <th>Employee Code</th>
                                    <th>Employee Name</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>

<script>
$(document).ready(function() {
    const base_url = '<?= base_url() ?>';

    let selectedSoftwareLicenseId = null;
    let selectedSoftwareLicenseQty = 0;
    let licensedPcTable;
    let editLicensedPcTable; // Tambahkan ini
    let licenseDetailOverviewTable;
    let poNumberTable;
    let pcTable;

    let callingModalForPcSearch = '';
    let employeeTable;
    let callingModalForEmployeeSearch = '';
    let currentMainModal = '';
    let isAnotherModalOpenOnTop = false;
    let currentLicensedPcsAjaxRequest = null;
    let currentLicensedPcsCountAjaxRequest = null;
    let currentEquipmentDataAjaxRequest = null;
    let currentEmployeeDataAjaxRequest = null;

    let poFieldsModifiedViaFinderInEdit = false; // <-- NEW: Flag untuk melacak perubahan PO di modal edit

    // Auto-upper saat user selesai mengetik
    $('#ref_num_subs_id, #edit_ref_num_subs_id,' +
        ' #product_key, #edit_product_key,' +
        ' #license_partner, #edit_license_partner,' +
        ' #product_name, #edit_product_name,' +
        ' #add_pc_asset_name, #add_pc_asset_no, #add_pc_serial_number,' +
        ' #edit_pc_asset_name, #edit_pc_asset_no, #edit_pc_serial_number')
        .on('blur keyup', function(){
            this.value = this.value.toUpperCase();
        });

    // Load PO data list (hanya untuk referensi, tidak digunakan untuk validasi duplikasi)
    let poNumberList = [];
    $.ajax({
        url: base_url + "/SoftwareLicense/getPOData",
        type: "GET",
        success: function(data) {
            poNumberList = data.map(item => item.po_number);
        },
        error: function(xhr, status, error) {
            console.error("Gagal load PO data list:", error);
        }
    });

    // ====================================================================
    // PENTING: INISIALISASI DATATABLES DILAKUKAN DI AWAL ready() FUNCTION
    // ====================================================================

    // Initialize DataTable for Software License (tabel utama)
    var table = $('#tabelSoftwareLicense').DataTable({
        scrollX: true,
        pageLength: 10,
        // Set the default order for the main table to "Last Update" (index 15) in descending order
        order: [[15, 'desc']],
        ajax: {
            url: base_url + "/SoftwareLicense/getDataSoftwareLicense",
            dataSrc: "",
            error: function(xhr, status, error) {
                console.error("Error fetching main software license data:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load Software License data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error),
                });
            }
        },
        columns: [
            {
                data: null,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                            <div class="d-flex justify-content-center gap-2">
                                <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-btn" data-id="${row.id}" title="Edit Software License">
                                    <i class="fa fa-pen-to-square"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-info view-licensed-pc-btn" data-id="${row.id}" data-qty="${row.product_qty}" title="View Licensed PCs">
                                    <i class="fa fa-computer"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-success print-excel-by-id-btn" data-id="${row.id}" title="Print Excel for this License">
                                    <i class="fa fa-file-excel"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-btn" data-id="${row.id}" title="Delete Software License">
                                    <i class="fa fa-trash-can"></i>
                                </a>
                            </div>`;
                }
            },
            { data: 'id' },
            { data: 'type' },
            { data: 'license_category' },
            {
                data: 'ref_number',
                render: function(d) {
                    return d ? d.toUpperCase() : '';
                }
            },
            { data: 'po_number' },
            {
                data: 'license_partner',
                render: d => d ? d.toUpperCase() : ''
            },
            { // Perubahan di sini untuk Order Date
                data: 'order_date',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit'
                    }) : '';
                }
            },
            { // Perubahan di sini untuk Start Date
                data: 'start_date',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit'
                    }) : '';
                }
            },
            { // Perubahan di sini untuk End Date
                data: 'end_date',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit'
                    }) : '';
                }
            },
            {
                data: 'product_name',
                render: d => d ? d.toUpperCase() : ''
            },
            {
                data: 'product_qty',
                render: function(data) {
                    // Cek apakah data adalah null, undefined, atau string kosong
                    if (data === null || typeof data === 'undefined' || data === '') {
                        return ''; // Kembalikan string kosong jika tidak ada data
                    }
                    // Coba konversi data ke float (untuk numeric(18,2))
                    const parsedData = parseFloat(data);
                    // Cek apakah hasil konversi adalah NaN
                    if (isNaN(parsedData)) {
                        return ''; // Kembalikan string kosong jika hasilnya NaN
                    }
                    return parsedData;
                }
            },
            { data: 'product_desc' },
            {
                data: 'product_key',
                render: function(d){
                    return d ? d.toUpperCase() : '';
                }
            },
            { data: 'organization' },
            { data: 'last_update' },
            { data: 'last_user' }
        ],
        language: {
            "sLengthMenu": "Show _MENU_ entries",
            "sSearch": "Search:"
        }
    });

    // Initialize Licensed PC DataTable
    licensedPcTable = $('#licensedPcTable').DataTable({
        scrollX: true,
        pageLength: 5,
        autoWidth: false,
        processing: true,
        serverSide: false, // Set to false if you are fetching all data at once
        ajax: {
            url: base_url + "/SoftwareLicense/getLicensedPcs/0", // Dummy URL, will be updated on click
            dataSrc: "",
            error: function(xhr, status, error) {
                // Check if the error is an abort
                if (status === "abort") {
                    console.warn("Ajax request for licensed PC data aborted.");
                    return; // Ignore aborted requests
                }
                console.error('Error fetching licensed PC data:', error);
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    $('#licensedPcTable_wrapper .dataTables_empty').text('Error: ' + xhr.responseJSON.message);
                } else {
                    $('#licensedPcTable_wrapper .dataTables_empty').text('Error loading data.');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load Licensed PC data. Please try again. ' + (xhr.responseJSON ? xhr.responseJSON.message : ''),
                });
            }
        },
        columns: [
            {
                data: null,
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="d-flex justify-content-center gap-2">
                            <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-licensed-pc-btn" data-id="${row.ld_id}" title="Edit Licensed PC">
                                <i class="fa fa-pen-to-square"></i>
                            </a>
                            <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-licensed-pc-btn" data-id="${row.ld_id}" title="Delete Licensed PC">
                                <i class="fa fa-trash-can"></i>
                            </a>
                        </div>`;
                }
            },
            { data: 'ld_pcnama', render: d => d ? d.toUpperCase() : '' },
            { data: 'ld_assetno', render: d => d ? d.toUpperCase() : '' },
            { data: 'ld_pc_id', render: d => d ? d.toString() : '' },
            { data: 'ld_serialnumber', render: d => d ? d.toUpperCase() : '' },
            {
                data: 'em_emplname',
                defaultContent: '',
                render: d => d ? d.toUpperCase() : ''
            },
            {
                data: 'pm_positionname',
                defaultContent: '',
                render: d => d ? d.toUpperCase() : ''
            },
            { // Kolom Last Update (index 7)
                data: 'ld_lastupdate',
                render: function(data) {
                    return data ? new Date(data).toLocaleString('id-ID', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    }) : '';
                }
            }
        ],
        // Pengurutan berdasarkan kolom Last Update (indeks 7) secara menurun
        order: [[7, 'desc']],
        rowCallback: function(row, data, index) {
            const currentLicensedCount = licensedPcTable.rows({ filter: 'applied' }).count();
            const currentLicenseQty = selectedSoftwareLicenseQty;
            const lastUpdateCell = $('td:eq(7)', row); // Get the Last Update cell (index 7)

            $(row).removeClass('overlicensed-row');
            lastUpdateCell.removeClass('latest-update-cell'); // Pastikan menghapus kelas sebelumnya
            if (currentLicensedCount > currentLicenseQty) {
                const numOverlicensed = currentLicensedCount - currentLicenseQty;
                if (index < numOverlicensed) {
                    $(row).addClass('overlicensed-row');

                    if (index === currentLicenseQty) {
                        lastUpdateCell.addClass('latest-update-cell');
                    }
                }
            }
        },
        language: {
            "sLengthMenu": "Show _MENU_ entries",
            "sSearch": "Search:",
            "sEmptyTable": "No PCs licensed for this software.",
            "sZeroRecords": "No matching records found"
        }
    });

    // Initialize Edit Licensed PC DataTable (editLicensedPcTable)
    editLicensedPcTable = $('#editLicensedPcTable').DataTable({
        scrollX: true,
        pageLength: 5,
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + "/SoftwareLicense/getLicensedPcs/0", // Dummy URL, will be updated on click
            dataSrc: "",
            error: function(xhr, status, error) {
                if (status === "abort") {
                    console.warn("Ajax request for edit licensed PC data aborted.");
                    return;
                }
                console.error('Error fetching licensed PC data for edit modal:', error);
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    $('#editLicensedPcTable_wrapper .dataTables_empty').text('Error: ' + xhr.responseJSON.message);
                } else {
                    $('#editLicensedPcTable_wrapper .dataTables_empty').text('Error loading data.');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load Licensed PC data for edit. Please try again. ' + (xhr.responseJSON ? xhr.responseJSON.message : ''),
                });
            }
        },
        columns: [

            { data: 'ld_pcnama', render: d => d ? d.toUpperCase() : '' },
            { data: 'ld_assetno', render: d => d ? d.toUpperCase() : '' },
            { data: 'ld_pc_id', render: d => d ? d.toString() : '' },
            { data: 'ld_serialnumber', render: d => d ? d.toUpperCase() : '' },
            {
                data: 'em_emplname',
                defaultContent: '',
                render: d => d ? d.toUpperCase() : ''
            },
            {
                data: 'pm_positionname',
                defaultContent: '',
                render: d => d ? d.toUpperCase() : ''
            },
            {
                data: 'ld_lastupdate',
                render: function(data) {
                    return data ? new Date(data).toLocaleString('id-ID', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    }) : '';
                }
            }
        ],
        // Pengurutan berdasarkan kolom Last Update (indeks 7) secara menurun
        order: [[6, 'desc']], // Ubah indeks kolom pengurutan dari 7 menjadi 6 (karena kolom action hilang)
        rowCallback: function(row, data, index) {
            // Apply overlicensed-row class based on live data (from this table itself)
            const currentLicensedCount = editLicensedPcTable.rows({ filter: 'applied' }).count();
            const currentLicenseQty = selectedSoftwareLicenseQty; // Use the globally updated quantity

            $(row).removeClass('overlicensed-row');
            if (currentLicensedCount > currentLicenseQty && index >= currentLicenseQty) {
                $(row).addClass('overlicensed-row');
            }
        },
        language: {
            "sLengthMenu": "Show _MENU_ entries",
            "sSearch": "Search:",
            "sEmptyTable": "No PCs licensed for this software.",
            "sZeroRecords": "No matching records found"
        }
    });

    // Initialize License Detail Overview DataTable (NEW)
    licenseDetailOverviewTable = $('#licenseDetailOverviewTable').DataTable({
        info: false,
        paging: false,
        searching: false,
        ordering: false,
        columns: [
            {   // NEW: Kolom Action
                data: null,
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    // Tombol ini akan memicu edit/delete untuk LISENSI UTAMA (row.id)
                    // Jadi, fungsionalitasnya sama seperti tombol di tabel utama
                    return `
                        <div class="d-flex justify-content-center gap-2">
                            <a href="javascript:;" class="btn btn-icon btn-outline-primary edit-main-license-btn" data-id="${row.id}" title="Edit Software License">
                                <i class="fa fa-pen-to-square"></i>
                            </a>
                            <a href="javascript:;" class="btn btn-icon btn-outline-danger delete-main-license-btn" data-id="${row.id}" title="Delete Software License">
                                <i class="fa fa-trash-can"></i>
                            </a>
                        </div>`;
                }
            },
            { data: 'id' },
            { data: 'type' },
            { data: 'license_category' },
            { data: 'ref_number', render: d => d ? d.toUpperCase() : '' },
            { data: 'po_number' },
            { data: 'license_partner', render: d => d ? d.toUpperCase() : '' },
            { data: 'order_date', render: d => d ? new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }) : '' },
            { data: 'start_date', render: d => d ? new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }) : '' },
            { data: 'end_date', render: d => d ? new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }) : '' },
            { data: 'product_name', render: d => d ? d.toUpperCase() : '' },
            { data: 'product_qty', render: d => (d === null || typeof d === 'undefined' || d === '') ? '' : parseFloat(d) },
            { data: 'product_desc' },
            { data: 'product_key', render: d => d ? d.toUpperCase() : '' },
            { data: 'organization' },
            { data: 'last_update', render: d => d ? new Date(d).toLocaleString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '' },
            { data: 'last_user' }
        ],
        data: [],
        language: {
            "sEmptyTable": "No license detail available.",
            "sZeroRecords": "No matching records found"
        }
    });

    // NEW: Event listener untuk tombol Edit di licenseDetailOverviewTable
    $('#licenseDetailOverviewTable').on('click', '.edit-main-license-btn', function() {
        const id = $(this).data('id');
        // Memicu event click pada tombol edit di tabel utama
        // Ini akan menggunakan logika edit yang sudah ada
        $(`#tabelSoftwareLicense .edit-btn[data-id="${id}"]`).trigger('click');
        // Tutup modal licensedPcsListModal setelah memicu edit
        $('#licensedPcsListModal').modal('hide');
    });

    // NEW: Event listener untuk tombol Delete di licenseDetailOverviewTable
    $('#licenseDetailOverviewTable').on('click', '.delete-main-license-btn', function() {
        const id = $(this).data('id');
        // Memicu event click pada tombol delete di tabel utama
        // Ini akan menggunakan logika delete yang sudah ada
        $(`#tabelSoftwareLicense .delete-btn[data-id="${id}"]`).trigger('click');
        // Tutup modal licensedPcsListModal setelah memicu delete
        $('#licensedPcsListModal').modal('hide');
    });

    // ====================================================================
    // END INISIALISASI DATATABLES
    // ====================================================================

    // NEW: Handle change event for License Type Category in Add Modal
    $('#license_type_category').on('change', function() {
        const selectedCategory = $(this).val();
        const endDateField = $('#end_date');
        if (selectedCategory === 'Perpetual License') {
            endDateField.val('').prop('readonly', true);
        } else {
            endDateField.prop('readonly', false);
        }
    });

    // NEW: Handle change event for License Type Category in Edit Modal
    $('#edit_license_type_category').on('change', function() {
        const selectedCategory = $(this).val();
        const editEndDateField = $('#edit_end_date');
        if (selectedCategory === 'Perpetual License') {
            editEndDateField.val('').prop('readonly', true);
        } else {
            editEndDateField.prop('readonly', false);
        }
    });

    // NEW: Handle click on "Licensed PC" button in the main table
    $('#tabelSoftwareLicense').on('click', '.view-licensed-pc-btn', function() {
        const rowData = table.row($(this).parents('tr')).data(); // Dapatkan seluruh data baris
        selectedSoftwareLicenseId = rowData.id;
        selectedSoftwareLicenseQty = parseFloat(rowData.product_qty); // Pastikan ini float

        // Isi dan gambar tabel detail lisensi tunggal
        licenseDetailOverviewTable.clear().rows.add([rowData]).draw(); // Isi data baris yang dipilih
        $('#modalLicenseIdDisplay').text(selectedSoftwareLicenseId);
        $('#licensed_pc_license_id').val(selectedSoftwareLicenseId);
        loadLicensedPcs(selectedSoftwareLicenseId);
        isAnotherModalOpenOnTop = false; // Reset status saat modal ini dibuka langsung
        $('#licensedPcsListModal').modal('show'); // Show the modal after initiating data load
    });
    // MODIFIKASI SELESAI

    // NEW: Handle click on the new "Print Excel" button in the main table
    $('#tabelSoftwareLicense').on('click', '.print-excel-by-id-btn', function() {
        const id = $(this).data('id');
        if (id) {
            Swal.fire({
                title: 'Generating Excel Report...',
                text: 'Please wait, your report is being generated.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            window.location.href = base_url + '/SoftwareLicense/exportExcelById/' + id;
            // Close Swal after a short delay (give time for file download to initiate)
            setTimeout(() => {
                Swal.close();
            }, 2000); // Adjust delay if download is very slow to start
        } else {
            Swal.fire('Error', 'Software License ID not found for this entry.', 'error');
        }
    });

    $('#tabelSoftwareLicense').on('click', '.edit-btn', function () {
        var id = $(this).data('id');
        poFieldsModifiedViaFinderInEdit = false; // Reset flag saat modal edit dibuka

        // PENTING: Set selectedSoftwareLicenseId dan selectedSoftwareLicenseQty di sini
        // saat modal edit utama dibuka dari tabel.
        const rowData = table.row($(this).parents('tr')).data();
        selectedSoftwareLicenseId = rowData.id;
        selectedSoftwareLicenseQty = parseFloat(rowData.product_qty);
        console.log("Edit button clicked. Setting selectedSoftwareLicenseId to:", selectedSoftwareLicenseId);
        console.log("Setting selectedSoftwareLicenseQty to:", selectedSoftwareLicenseQty);

        $.ajax({
            url: base_url + '/SoftwareLicense/edit',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.status) {
                    const d = response.data;
                    $('#edit_td_id').val(d.id);
                    $('#edit_license_id').val(d.license_id);
                    $('#edit_license_type').val(d.license_type);
                    $('#edit_license_type_category').val(d.license_type_category);
                    $('#edit_ref_num_subs_id').val(d.ref_num_subs_id);

                    // PO fields initial state from DB
                    $('#edit_po_number').val(d.po_number);
                    $('#edit_license_partner').val(d.license_partner);
                    $('#edit_order_date').val(d.order_date); // Pastikan format tanggal sudah benar
                    $('#edit_product_name').val(d.product_name);
                    $('#edit_product_qty').val(parseFloat(d.product_qty).toFixed(0));

                    // Menerapkan readonly jika data berasal dari finder, TAPI JANGAN DISABLE TOMBOL FINDERNYA
                    if (d.po_sourced_from_finder == 1) {
                        $('#edit_po_number, #edit_license_partner, #edit_order_date, #edit_product_name, #edit_product_qty')
                            .prop('readonly', true).addClass('bg-light');
                        // Tombol finder tetap ENABLED
                        $('.edit-search-po-btn').prop('disabled', false);
                    } else { // Jika data PO diinput manual
                        $('#edit_po_number, #edit_license_partner, #edit_order_date, #edit_product_name, #edit_product_qty')
                            .prop('readonly', false).removeClass('bg-light');
                        $('.edit-search-po-btn').prop('disabled', false);
                    }

                    $('#edit_start_date').val(d.start_date);
                    $('#edit_end_date').val(d.end_date);
                    $('#edit_product_desc').val(d.product_desc);
                    $('#edit_product_key').val(d.product_key);
                    $('#edit_organization').val(d.organization);

                    if (d.license_type_category === 'Perpetual License') {
                        $('#edit_end_date').val('').prop('readonly', true);
                    } else {
                        $('#edit_end_date').prop('readonly', false);
                    }

                    // Clear any previous invalid states
                    $('#edit_po_number, #edit_ref_num_subs_id, #edit_product_key')
                        .removeClass('is-invalid');
                    $('#edit_po_number_error, #edit_product_key_error, #edit_ref_num_subs_id_error').text('').hide();

                    // PENTING: Pastikan selectedSoftwareLicenseId dan selectedSoftwareLicenseQty di-update
                    // dengan data yang baru di-fetch dari server.
                    selectedSoftwareLicenseId = d.id; 
                    selectedSoftwareLicenseQty = parseFloat(d.product_qty);
                    console.log("Edit data loaded. Updated selectedSoftwareLicenseId to:", selectedSoftwareLicenseId);
                    console.log("Updated selectedSoftwareLicenseQty to:", selectedSoftwareLicenseQty);

                    loadEditLicensedPcs(d.id, d.product_qty); // Panggil fungsi untuk memuat data PC

                    $('#editDetailDetModal').modal('show');
                } else {
                    Swal.fire('Error', response.message || 'Data not found', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });


    // Pastikan tombol "Add PC" awalnya tidak disabled (atau atur di loadLicensedPcs)
    // $('.add-pc-btn').prop('disabled', false); // Initial state can be set here if needed

    // Event saat modal Add Licensed PC mencoba tampil (dari dalam licensedPcsListModal)
    // Event saat modal Add Licensed PC mencoba tampil (dari dalam licensedPcsListModal atau editDetailDetModal)
    $('#addLicensedPcModal').on('show.bs.modal', function(event) {
        // Set flag bahwa modal lain terbuka di atas parent modal (licensedPcsListModal atau editDetailDetModal)
        isAnotherModalOpenOnTop = true;

        // PENTING: Pastikan #licensed_pc_license_id diisi dari selectedSoftwareLicenseId
        const licenseId = selectedSoftwareLicenseId; // Ambil dari variabel global
        if (!licenseId) {
            event.preventDefault(); // Mencegah modal terbuka
            Swal.fire({
                icon: 'warning',
                title: 'Error',
                text: 'License ID for Licensed PC is missing. Please open the main license first.',
            });
            return;
        }
        // Set nilai ke hidden input field
        $('#licensed_pc_license_id').val(licenseId); 
        console.log("addLicensedPcModal is about to show. licensed_pc_license_id set to:", $('#licensed_pc_license_id').val());

        // Selalu reset form saat modal dibuka, terlepas dari dari mana ia dibuka
        $('#addLicensedPcForm')[0].reset();

        // Hapus kelas is-invalid dan sembunyikan pesan error untuk semua field
        $('#add_pc_asset_no, #add_pc_asset_id').removeClass('is-invalid'); 
        $('#add_pc_asset_no_error, #add_pc_asset_id_error').text('').hide();

        // Atur kembali properti readonly untuk semua kolom
        $('#add_pc_asset_no').prop('readonly', false).removeClass('bg-light');
        $('#add_pc_asset_name').prop('readonly', false).removeClass('bg-light');
        $('#add_pc_serial_number').prop('readonly', false).removeClass('bg-light');
        $('#add_pc_asset_id').prop('readonly', false).removeClass('bg-light');

        // Kolom "User" tetap readonly karena ada tombol searchnya
        $('#add_pc_employee_name').prop('readonly', true);

        // Pastikan hidden fields kosong juga
        $('#add_pc_e_id').val('');
        $('#add_pc_employee_code').val('');
        $('#add_pc_position_code').val('');

        callingModalForPcSearch = 'addLicensedPcModal';
    });

    // NEW: Handle click on "Add PC" button in the Edit Software License Modal
    $('.add-pc-to-edit-modal-btn').on('click', function() {
        // Ini akan memicu event 'show.bs.modal' pada #addLicensedPcModal
        // Penting: kita harus memastikan selectedSoftwareLicenseId sudah diset dengan benar
        // sebelum #addLicensedPcModal ditampilkan.
        // Karena tombol ini ada di dalam editDetailDetModal, selectedSoftwareLicenseId
        // seharusnya sudah diset oleh event .edit-btn di atas.
        isAnotherModalOpenOnTop = true; // Set flag karena modal ini juga membuka modal di atasnya

        // Reset form di sini juga untuk memastikan bersih saat dibuka dari modal edit
        $('#addLicensedPcForm')[0].reset();

        // Hapus kelas is-invalid dan sembunyikan pesan error untuk semua field
        $('#add_pc_asset_no, #add_pc_asset_id').removeClass('is-invalid');
        $('#add_pc_asset_no_error, #add_pc_asset_id_error').text('').hide();

        // Atur kembali properti readonly untuk semua kolom (jika sebelumnya diubah oleh finder)
        $('#add_pc_asset_no').prop('readonly', false).removeClass('bg-light');
        $('#add_pc_asset_name').prop('readonly', false).removeClass('bg-light');
        $('#add_pc_serial_number').prop('readonly', false).removeClass('bg-light');
        $('#add_pc_asset_id').prop('readonly', false).removeClass('bg-light');

        // Kolom "User" tetap readonly karena ada tombol searchnya
        $('#add_pc_employee_name').prop('readonly', true);

        // Pastikan hidden fields kosong juga
        $('#add_pc_e_id').val('');
        $('#add_pc_employee_code').val('');
        $('#add_pc_position_code').val('');

        // Set the hidden field for the licensed PC form
        $('#licensed_pc_license_id').val(selectedSoftwareLicenseId);

        // Langsung tampilkan modal Add Licensed PC. Event 'show.bs.modal' akan menangani sisanya.
        // Tidak perlu setTimeout di sini karena tidak ada modal lain yang ditutup.
        $('#addLicensedPcModal').modal('show');
    });

    // Event saat modal Add Licensed PC mencoba tampil (dari dalam licensedPcsListModal atau editDetailDetModal)
    // $('#addLicensedPcModal').on('show.bs.modal', function(event) {
    //     // Set flag bahwa modal lain terbuka di atas parent modal (licensedPcsListModal atau editDetailDetModal)
    //     // isAnotherModalOpenOnTop = true; // Ini sudah diset di event listener di atas

    //     const licenseId = $('#licensed_pc_license_id').val();
    //     if (!licenseId) {
    //         event.preventDefault();
    //         Swal.fire({
    //             icon: 'warning',
    //             title: 'Error',
    //             text: 'License ID for Licensed PC is missing. This should not happen.',
    //         });
    //         return;
    //     }

    //     // ... (kode yang sudah ada di addLicensedPcModal.on('show.bs.modal')) ...
    //     // Selalu reset form saat modal dibuka, terlepas dari dari mana ia dibuka
    //     // $('#addLicensedPcForm')[0].reset(); // Sudah dilakukan di listener add-pc-to-edit-modal-btn dan view-licensed-pc-btn

    //     // Hapus kelas is-invalid dan sembunyikan pesan error untuk semua field
    //     // $('#add_pc_asset_no, #add_pc_asset_id').removeClass('is-invalid');
    //     // $('#add_pc_asset_no_error, #add_pc_asset_id_error').text('').hide();

    //     // Atur kembali properti readonly untuk semua kolom (jika sebelumnya diubah oleh finder)
    //     // $('#add_pc_asset_no').prop('readonly', false).removeClass('bg-light');
    //     // $('#add_pc_asset_name').prop('readonly', false).removeClass('bg-light');
    //     // $('#add_pc_serial_number').prop('readonly', false).removeClass('bg-light');
    //     // $('#add_pc_asset_id').prop('readonly', false).removeClass('bg-light');

    //     // Kolom "User" tetap readonly karena ada tombol searchnya
    //     // $('#add_pc_employee_name').prop('readonly', true);

    //     // Pastikan hidden fields kosong juga
    //     // $('#add_pc_e_id').val('');
    //     // $('#add_pc_employee_code').val('');
    //     // $('#add_pc_position_code').val('');

    //     callingModalForPcSearch = 'addLicensedPcModal';
    // });

    // Handle click on "Finder" button in Add Licensed PC Modal (for Asset)
    $('#addLicensedPcModal .search-pc-asset-no-btn').on('click', function() {
        callingModalForPcSearch = 'addLicensedPcModal';
        // Jangan hide licensedPcsListModal jika sudah stacked
        // $('#addLicensedPcModal').modal('hide'); // Hapus baris ini jika Anda ingin addLicensedPcModal tetap terbuka
        // Biarkan modal ini menutup secara otomatis jika ada data-bs-dismiss atau kita akan kontrol manual

        // Kita akan menggunakan setTimeout untuk membuka pcSearchModal
        // Ini adalah workaround untuk masalah fokus modal berlapis
        setTimeout(() => {
            $('#pcSearchModal').modal('show');
        }, 100); // Sedikit delay
    });

    // Handle click on "Finder" button in Edit Licensed PC Modal (for Asset)
    $('#editLicensedPcModal .search-pc-asset-no-btn').on('click', function() {
        callingModalForPcSearch = 'editLicensedPcModal';
        $('#editLicensedPcModal').modal('hide');
        $('#pcSearchModal').modal('show');
    });

    // Handle click on "Finder" button in Add Licensed PC Modal (for Employee)
    $('#addLicensedPcModal .search-employee-btn').on('click', function() {
        callingModalForEmployeeSearch = 'addLicensedPcModal';
        // Jangan hide licensedPcsListModal jika sudah stacked
        // $('#addLicensedPcModal').modal('hide'); // Hapus baris ini juga jika Anda ingin addLicensedPcModal tetap terbuka
        setTimeout(() => {
            $('#employeeSearchModal').modal('show');
        }, 100); // Sedikit delay
    });

    // Handle click on "Finder" button in Edit Licensed PC Modal (for Employee)
    $('#editLicensedPcModal .search-employee-btn').on('click', function() {
        callingModalForEmployeeSearch = 'editLicensedPcModal';
        $('#editLicensedPcModal').modal('hide');
        $('#employeeSearchModal').modal('show');
    });

    // Event ketika modal Select PC tampil
    $('#pcSearchModal').on('shown.bs.modal', function() {
        if ($.fn.DataTable.isDataTable('#pcTable')) {
            pcTable.destroy();
        }

        // Abort previous equipment data request
        if (currentEquipmentDataAjaxRequest) {
            currentEquipmentDataAjaxRequest.abort();
        }

        pcTable = $('#pcTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: base_url + "/SoftwareLicense/getEquipmentData",
                dataSrc: function(json) {
                    return json || [];
                },
                beforeSend: function(xhr) {
                    currentEquipmentDataAjaxRequest = xhr; // Store the current request
                },
                error: function(xhr, status, error) {
                    if (status === "abort") {
                        console.warn("Ajax request for Equipment data aborted.");
                        return; // Ignore aborted requests
                    }
                    console.error('Error fetching Equipment data:', error);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load Equipment data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error) });
                    return [];
                },
                complete: function() {
                    currentEquipmentDataAjaxRequest = null; // Clear the request once completed
                }
            },
            columns: [
                { data: 'e_assetno' },
                { data: 'e_equipmentname' },
                { data: 'e_equipmentid' },
                { data: 'e_serialnumber' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return row.e_brand + ' / ' + row.e_model;
                    }
                }
            ],
            language: {
                "sLengthMenu": "Show _MENU_ entries",
                "sSearch": "Search:",
                "sEmptyTable": "No data available in table",
                "sZeroRecords": "No matching records found"
            },
            paging: true,
            pageLength: 10,
            lengthChange: true,
        });

        $('#searchPc').off('keyup').on('keyup', function () {
            pcTable.search(this.value).draw();
        });
        $('#pcLength').off('change').on('change', function() {
            pcTable.page.len($(this).val()).draw();
        });
    });

    // *** FUNGSI PENTING: Pilih PC dari search modal dan isi ke form sebelumnya ***
    $('#pcTable tbody').on('click', 'tr', function() {
        if (!pcTable) return;
        const data = pcTable.row(this).data();
        if (!data) return;

        if (callingModalForPcSearch === 'addLicensedPcModal') {
            $('#add_pc_asset_no').val(data.e_assetno).prop('readonly', true).addClass('bg-light'); // Set readonly and add bg-light
            $('#add_pc_asset_name').val(data.e_equipmentname).prop('readonly', true).addClass('bg-light'); // Set readonly and add bg-light
            $('#add_pc_serial_number').val(data.e_serialnumber).prop('readonly', true).addClass('bg-light'); // Set readonly and add bg-light
            $('#add_pc_asset_id').val(data.e_equipmentid).prop('readonly', true).addClass('bg-light'); // Set readonly and add bg-light
            $('#add_pc_e_id').val(data.e_id || '');
        } else if (callingModalForPcSearch === 'editLicensedPcModal') {
            $('#edit_pc_asset_no').val(data.e_assetno).prop('readonly', true).addClass('bg-light'); // Set readonly and add bg-light
            $('#edit_pc_asset_name').val(data.e_equipmentname).prop('readonly', true).addClass('bg-light'); // Set readonly and add bg-light
            $('#edit_pc_serial_number').val(data.e_serialnumber).prop('readonly', true).addClass('bg-light'); // Set readonly and add bg-light
            $('#edit_pc_asset_id').val(data.e_equipmentid).prop('readonly', true).addClass('bg-light'); // Set readonly and add bg-light
            $('#edit_pc_e_id').val(data.e_id || '');
        }

        $('#pcSearchModal').modal('hide');
    });

    // Event saat modal "Select PC" ditutup
    $('#pcSearchModal').on('hidden.bs.modal', function () {
        if (callingModalForPcSearch === 'addLicensedPcModal') {
            $('#addLicensedPcModal').modal('show'); // Ini yang memastikan addLicensedPcModal muncul kembali
        } else if (callingModalForPcSearch === 'editLicensedPcModal') {
            $('#editLicensedPcModal').modal('show');
        }
        callingModalForPcSearch = ''; // Reset context
    });

    // Event ketika modal Employee Search tampil
    $('#employeeSearchModal').on('shown.bs.modal', function() {
        if ($.fn.DataTable.isDataTable('#employeeTable')) {
            employeeTable.destroy();
        }

        // Abort previous employee data request
        if (currentEmployeeDataAjaxRequest) {
            currentEmployeeDataAjaxRequest.abort();
        }

        employeeTable = $('#employeeTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: base_url + '/SoftwareLicense/getEmployeeData',
                dataSrc: function(json) {
                    return json || [];
                },
                beforeSend: function(xhr) {
                    currentEmployeeDataAjaxRequest = xhr; // Store the current request
                },
                error: function(xhr, status, error) {
                    if (status === "abort") {
                        console.warn("Ajax request for Employee data aborted.");
                        return; // Ignore aborted requests
                    }
                    console.error('Error fetching Employee data:', error);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load Employee data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error) });
                    return [];
                },
                complete: function() {
                    currentEmployeeDataAjaxRequest = null; // Clear the request once completed
                }
            },
            columns: [
                { data: 'em_emplcode' },
                { data: 'em_emplname' },
                { data: 'pm_positionname' }
            ],
            language: {
                "sLengthMenu": "Show _MENU_ entries",
                "sSearch": "Search:"
            },
            paging: true,
            pageLength: 10,
            lengthChange: true
        });

        $('#searchEmployee').off('keyup').on('keyup', function () {
            employeeTable.search(this.value).draw();
        });
        $('#employeeLength').off('change').on('change', function() {
            employeeTable.page.len($(this).val()).draw();
        });
    });

    // Handle row selection from the employee modal
    $('#employeeTable tbody').on('click', 'tr', function() {
        if (!employeeTable) return;
        const data = employeeTable.row(this).data();
        if (!data) return;

        if (callingModalForEmployeeSearch === 'addLicensedPcModal') {
            $('#add_pc_employee_name').val(data.em_emplname).prop('readonly', true);
            $('#add_pc_employee_code').val(data.em_emplcode);
            $('#add_pc_position_code').val(data.pm_code || null); // Assuming pm_code for position
        } else if (callingModalForEmployeeSearch === 'editLicensedPcModal') {
            $('#edit_pc_employee_name').val(data.em_emplname).prop('readonly', true);
            $('#edit_pc_employee_code').val(data.em_emplcode);
            $('#edit_pc_position_code').val(data.pm_code || null); // Assuming pm_code for position
        }

        $('#employeeSearchModal').modal('hide');
    });

    // Event saat modal "Select Employee" ditutup
    $('#employeeSearchModal').on('hidden.bs.modal', function() {
        if (callingModalForEmployeeSearch === 'addLicensedPcModal') {
            $('#addLicensedPcModal').modal('show'); // Ini yang memastikan addLicensedPcModal muncul kembali
        } else if (callingModalForEmployeeSearch === 'editLicensedPcModal') {
            $('#editLicensedPcModal').modal('show');
        }
        callingModalForEmployeeSearch = ''; // Reset context
    });

    // Handle Delete button click for Software License
    $('#tabelSoftwareLicense').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the record and all associated licensed PCs!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + '/SoftwareLicense/delete',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Record has been deleted.',
                                icon: 'success',
                                timer: 1500, // Menutup otomatis setelah 1.5 detik
                                showConfirmButton: false // Menghilangkan tombol "Ok"
                            });
                            table.ajax.reload();
                            if (selectedSoftwareLicenseId === id) {
                                selectedSoftwareLicenseId = null;
                                selectedSoftwareLicenseQty = 0;
                            }
                        } else {
                            Swal.fire('Error', response.error || 'Delete failed', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
                    }
                });
            }
        });
    });

    // Saat modal Add Data tampil, fetch next ID
    $('#addDetailDetModal').on('show.bs.modal', function(){
        $.get(base_url + '/SoftwareLicense/getNextId', function(res){
            $('#license_id').val(res.id);
        }).fail(function(xhr, status, error) {
            console.error('Error fetching next ID:', error);
            Swal.fire('Error', 'Failed to get next ID. Please try again.', 'error');
        });
        $('#end_date').val('').prop('readonly', false);
        $('#license_type_category').val('');
    });

    // Handle Add Data form submit
    // Handle Add Data form submit
    $('#addDataForm').on('submit', function(e) {
        e.preventDefault();
        // Hapus kelas is-invalid dan sembunyikan pesan error sebelumnya
        $('#po_number, #product_key, #ref_num_subs_id').removeClass('is-invalid');
        $('#po_number_error, #product_key_error, #ref_num_subs_id_error').text('').hide();

        $.ajax({
            url: base_url + '/SoftwareLicense/add',
            type: 'POST',
            data: $('#addDataForm').serialize(),
            success: function(resp) {
                if (resp.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'The record has been added.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#addDetailDetModal').modal('hide');
                        table.ajax.reload();

                        // --- START PERBAIKAN DI SINI ---
                        // Pastikan respons dari backend menyertakan new_license_data yang lengkap
                        if (resp.new_license_id && resp.new_license_data) {
                            selectedSoftwareLicenseId = resp.new_license_id;
                            // Gunakan product_qty dari new_license_data karena itu yang paling akurat dari DB
                            selectedSoftwareLicenseQty = parseFloat(resp.new_license_data.product_qty); 
                            
                            $('#modalLicenseIdDisplay').text(selectedSoftwareLicenseId);
                            $('#licensed_pc_license_id').val(selectedSoftwareLicenseId);

                            // PENTING: Gunakan new_license_data dari respons server!
                            const newLicenseData = resp.new_license_data;

                            // Isi dan gambar tabel detail lisensi tunggal
                            // DataTables akan menggunakan render function yang sudah Anda definisikan untuk format tanggal dan nama user
                            licenseDetailOverviewTable.clear().rows.add([newLicenseData]).draw();

                            // Gunakan setTimeout untuk memastikan modal sebelumnya benar-benar tertutup
                            setTimeout(() => {
                                $('#licensedPcsListModal').modal('show');
                                // Panggil loadLicensedPcs di sini setelah modal induk tampil,
                                // agar info count PC terupdate dan tabel PC di bawahnya dimuat.
                                loadLicensedPcs(selectedSoftwareLicenseId);
                            }, 500); // Beri sedikit delay (misal 500ms) untuk transisi modal
                        } else {
                            console.warn("New license ID or full license data not found in response after add.");
                            Swal.fire('Warning', 'Software license added, but could not open Licensed PCs modal (missing ID or full data).', 'warning');
                        }
                        // --- END PERBAIKAN ---
                    });
                } else {
                    // Periksa apakah ada errors dari validasi
                    if (resp.errors) {
                        if (resp.errors.po_number) {
                            $('#po_number').addClass('is-invalid');
                            $('#po_number_error').text(resp.errors.po_number).show();
                        }
                        if (resp.errors.product_key) {
                            $('#product_key').addClass('is-invalid');
                            $('#product_key_error').text(resp.errors.product_key).show();
                        }
                        if (resp.errors.ref_num_subs_id) {
                            $('#ref_num_subs_id').addClass('is-invalid');
                            $('#ref_num_subs_id_error').text(resp.errors.ref_num_subs_id).show();
                        }
                        // Tampilkan pesan error umum jika ada error validasi lain yang tidak spesifik
                        if (!resp.errors.po_number && !resp.errors.product_key && !resp.errors.ref_num_subs_id) {
                            Swal.fire('Error', 'Validation failed: ' + Object.values(resp.errors).join(', '), 'error');
                        }
                    } else if (resp.error) {
                        Swal.fire('Error', resp.error, 'error');
                    } else {
                        Swal.fire('Error','Save failed','error');
                    }
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error','Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error),'error');
            }
        });
    });

    // Handle Update Data form submit
    $('#editDataForm').on('submit', function(e) {
        e.preventDefault();
        // Hapus kelas is-invalid dan sembunyikan pesan error sebelumnya
        $('#edit_po_number, #edit_product_key, #edit_ref_num_subs_id').removeClass('is-invalid');
        $('#edit_po_number_error, #edit_product_key_error, #edit_ref_num_subs_id_error').text('').hide();

        // Pastikan `tl_id` diambil dari hidden field di form edit
        const tl_id_for_update = $('#edit_td_id').val();
        const product_qty_for_update = $('#edit_product_qty').val(); // Ambil qty terbaru

        $.ajax({
            url: base_url + '/SoftwareLicense/update',
            type: 'POST',
            data: $('#editDataForm').serialize(),
            success: function(resp) {
                if (resp.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'The record has been updated.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Tutup modal editDetailDetModal
                        $('#editDetailDetModal').modal('hide');
                        // Reload tabel utama di halaman
                        table.ajax.reload();

                        // --- MODIFIKASI UNTUK KEMBALI KE MODAL Licensed PCs ---
                        // selectedSoftwareLicenseId seharusnya sudah terisi.
                        // Sekarang kita juga menggunakan tl_id dari form update untuk konsistensi.
                        if (selectedSoftwareLicenseId !== null) { // Pastikan ID masih ada
                            // Fetch ulang data lisensi yang baru diupdate
                            $.ajax({
                                url: base_url + '/SoftwareLicense/edit', // Gunakan endpoint edit untuk fetch data lengkap
                                type: 'POST',
                                data: { id: selectedSoftwareLicenseId }, // Gunakan ID yang sudah di-set
                                success: function(responseFetch) {
                                    if (responseFetch.status) {
                                        const updatedLicenseData = responseFetch.data;
                                        // PENTING: Update selectedSoftwareLicenseQty dengan yang paling baru dari server
                                        selectedSoftwareLicenseQty = parseFloat(updatedLicenseData.product_qty); 
                                        
                                        // Update tabel detail lisensi di modal Licensed PCs
                                        licenseDetailOverviewTable.clear().rows.add([updatedLicenseData]).draw();

                                        // Buka kembali modal Licensed PCs dan refresh tabel PC di dalamnya
                                        setTimeout(() => {
                                            $('#licensedPcsListModal').modal('show');
                                            // Panggil loadLicensedPcs dengan ID lisensi yang benar dan qty terbaru
                                            loadLicensedPcs(selectedSoftwareLicenseId); 
                                        }, 100); 
                                    } else {
                                        Swal.fire('Error', responseFetch.message || 'Failed to fetch updated license data for display. Returning to Licensed PCs list.', 'error');
                                        // Jika gagal fetch, setidaknya kembali ke modal sebelumnya dengan data yang mungkin belum 100% terbaru
                                        setTimeout(() => {
                                            $('#licensedPcsListModal').modal('show');
                                            loadLicensedPcs(selectedSoftwareLicenseId); 
                                        }, 100);
                                    }
                                },
                                error: function(xhrFetch, statusFetch, errorFetch) {
                                    Swal.fire('Error', 'Failed to get updated license data for display. Returning to Licensed PCs list. Error: ' + (xhrFetch.responseJSON ? xhrFetch.responseJSON.message : errorFetch), 'error');
                                    // Jika AJAX fetch gagal, setidaknya kembali ke modal sebelumnya
                                    setTimeout(() => {
                                        $('#licensedPcsListModal').modal('show');
                                        loadLicensedPcs(selectedSoftwareLicenseId); 
                                    }, 100);
                                }
                            });
                        } else {
                            console.warn("selectedSoftwareLicenseId is null after update, cannot reopen Licensed PCs modal.");
                            // Jika ID hilang, fallback: mungkin buka modal utama saja tanpa detail.
                            // Atur ulang selectedSoftwareLicenseId ke null.
                            selectedSoftwareLicenseId = null;
                            selectedSoftwareLicenseQty = 0;
                        }
                        // --- END MODIFIKASI ---
                    });
                } else {
                    // Penanganan error validasi atau error umum dari respons server
                    if (resp.errors) {
                        if (resp.errors.po_number) {
                            $('#edit_po_number').addClass('is-invalid');
                            $('#edit_po_number_error').text(resp.errors.po_number).show();
                        }
                        if (resp.errors.product_key) {
                            $('#edit_product_key').addClass('is-invalid');
                            $('#edit_product_key_error').text(resp.errors.product_key).show();
                        }
                        if (resp.errors.ref_num_subs_id) {
                            $('#edit_ref_num_subs_id').addClass('is-invalid');
                            $('#edit_ref_num_subs_id_error').text(resp.errors.ref_num_subs_id).show();
                        }
                        if (!resp.errors.po_number && !resp.errors.product_key && !resp.errors.ref_num_subs_id) {
                            Swal.fire('Error', 'Validation failed: ' + Object.values(resp.errors).join(', '), 'error');
                        }
                    } else if (resp.error) {
                        Swal.fire('Error', resp.error, 'error');
                    } else {
                        Swal.fire('Error','Update failed','error');
                    }
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error','Gagal update: ' + (xhr.responseJSON ? xhr.responseJSON.message : error),'error');
            }
        });
    });

    // Load PO data saat PO Number modal ditampilkan
    $('#poNumberListModal').on('shown.bs.modal', function() {
        if ($.fn.DataTable.isDataTable('#poNumberTable')) {
            poNumberTable.destroy();
        }
        poNumberTable = $('#poNumberTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: base_url + "/SoftwareLicense/getPOData",
                dataSrc: function(json) {
                    return json || [];
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching PO data:', error);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load PO data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error) });
                    return [];
                }
            },
            columns: [
                { data: 'po_number' },
                { data: 'product_name' },
                { data: 'order_date',
                render: function(data) {
                    return data ? new Date(data).toLocaleString('id-ID', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                    }) : '';
                }
                },
                { data: 'license_partner' },
                { data: 'product_qty' }
            ],
            language: {
                "sLengthMenu": "Show _MENU_ entries",
                "sSearch": "Search:",
                "sEmptyTable": "No data available in table",
                "sZeroRecords": "No matching records found"
            },
            paging: true,
            pageLength: 10,
            lengthChange: true
        });
    });

    // Pilih PO dari modal
    $('#poNumberTable tbody').on('click', 'tr', function() {
        if (!poNumberTable) return;
        const data = poNumberTable.row(this).data();
        if (!data) return;

        if (currentMainModal === 'add') {
            $('#po_number').val(data.po_number).prop('readonly', true).addClass('bg-light');
            $('#license_partner').val(data.license_partner).prop('readonly', true).addClass('bg-light');
            $('#order_date').val(data.order_date ? data.order_date.split(' ')[0] : '').prop('readonly', true).addClass('bg-light');
            $('#product_name').val(data.product_name).prop('readonly', true).addClass('bg-light');
            $('#product_qty').val(data.product_qty).prop('readonly', true).addClass('bg-light');
            $('#add_po_sourced_from_finder').val('1'); 
        } else if (currentMainModal === 'edit') {
            // Ketika user memilih dari finder di modal edit, field PO akan diisi
            // dan langsung dijadikan readonly, tetapi tombol finder tetap aktif
            $('#edit_po_number').val(data.po_number).prop('readonly', true).addClass('bg-light');
            $('#edit_license_partner').val(data.license_partner).prop('readonly', true).addClass('bg-light');
            $('#edit_order_date').val(data.order_date ? data.order_date.split(' ')[0] : '').prop('readonly', true).addClass('bg-light');
            $('#edit_product_name').val(data.product_name).prop('readonly', true).addClass('bg-light');
            $('#edit_product_qty').val(data.product_qty).prop('readonly', true).addClass('bg-light');
            
            // Tandai bahwa field PO di modal edit telah diubah melalui finder
            poFieldsModifiedViaFinderInEdit = true; // <-- NEW: Set flag ini
        }
        $('#poNumberListModal').modal('hide');
        if (currentMainModal === 'add') {
            $('#addDetailDetModal').modal('show');
        } else if (currentMainModal === 'edit') {
            $('#editDetailDetModal').modal('show');
        }
    });

    // Ganti modal aktif (untuk PO modal)
    $('#addDetailDetModal').find('.search-po-btn').on('click', function() {
        currentMainModal = 'add';
    });
    $('#editDetailDetModal').find('.edit-search-po-btn').on('click', function() {
        currentMainModal = 'edit';
    });

    // Reset form saat modal Add Data ditutup
    // Reset form saat modal Add Data ditutup
    $('#addDetailDetModal').on('hidden.bs.modal', function () {
        if ($('#poNumberListModal').hasClass('show')) return;
        $('#addDataForm')[0].reset();
        $('#po_number, #license_partner, #order_date, #product_name, #product_qty')
            .prop('readonly', false).removeClass('bg-light');
        $('#po_number, #product_key, #ref_num_subs_id')
            .removeClass('is-invalid');
        $('#po_number_error, #product_key_error, #ref_num_subs_id_error')
            .text('').hide();
        $('#addDataForm').removeClass('was-validated');
        $('#end_date').val('').prop('readonly', false);
        currentMainModal = '';
        $('#add_po_sourced_from_finder').val('0'); 
    });

    // Validasi dan reset validasi saat modal Edit ditutup
    // Validasi dan reset validasi saat modal Edit ditutup
    $('#editDetailDetModal').on('hidden.bs.modal', function () {
        // Hanya jika tidak ada modal lain yang terbuka di atasnya (seperti poNumberListModal)
        // DAN kita memiliki ID lisensi yang sedang dikerjakan.
        // `isAnotherModalOpenOnTop` seharusnya sudah diatur false oleh handler `handleCloseAddLicensedPcModal`
        // saat ia selesai.
        if (!$('#poNumberListModal').hasClass('show') && selectedSoftwareLicenseId !== null) {
            // Ini akan memastikan modal Licensed PCs kembali terbuka jika editDetailDetModal ditutup manual.
            // Jika penutupan berasal dari proses update sukses, logika di atas sudah menangani pembukaan.
            setTimeout(() => {
                $('#licensedPcsListModal').modal('show');
                loadLicensedPcs(selectedSoftwareLicenseId); // Pastikan data di-refresh
            }, 100);
        }

        // Reset form dan validasi seperti biasa
        $('#editDataForm').removeClass('was-validated');
        $('#edit_po_number, #edit_license_partner, #edit_order_date, #edit_product_name, #edit_product_qty')
            .prop('readonly', false).removeClass('bg-light');
        $('.edit-search-po-btn').prop('disabled', false); 

        $('#edit_po_number, #edit_product_key, #edit_ref_num_subs_id')
            .removeClass('is-invalid');
        $('#edit_po_number_error, #edit_product_key_error, #edit_ref_num_subs_id_error')
            .text('').hide();
        $('#edit_end_date').val('').prop('readonly', false);
        currentMainModal = '';
        poFieldsModifiedViaFinderInEdit = false; // Reset flag
    });

    // Reopen modal form jika PO modal ditutup tanpa pilih
    $('#poNumberListModal').on('hidden.bs.modal', function() {
        // Hapus `setTimeout` sebelumnya karena kadang bisa menyebabkan masalah
        // Kita akan langsung mencoba membuka modal induk.

        if (currentMainModal === 'add') {
            // Dapatkan instance modal secara langsung
            const addModalElement = document.getElementById('addDetailDetModal');
            const addModalInstance = bootstrap.Modal.getInstance(addModalElement);
            if (addModalInstance) {
                addModalInstance.show();
            } else {
                // Fallback jika instance tidak ditemukan (misal jika modal belum diinisialisasi)
                new bootstrap.Modal(addModalElement).show();
            }
            console.log("PO Number List closed. Returning to Add Software License Modal.");
        } else if (currentMainModal === 'edit') {
            // Dapatkan instance modal secara langsung
            const editModalElement = document.getElementById('editDetailDetModal');
            const editModalInstance = bootstrap.Modal.getInstance(editModalElement);
            if (editModalInstance) {
                editModalInstance.show();
            } else {
                // Fallback jika instance tidak ditemukan
                new bootstrap.Modal(editModalElement).show();
            }
            console.log("PO Number List closed. Returning to Edit Software License Modal.");
        }
        currentMainModal = ''; // Reset context setelah digunakan
    });


    // =========================================================================================
    // MODIFIKASI KRUSIAL DI SINI!
    // NEW: Saat `licensedPcsListModal` ditutup, reset selectedSoftwareLicenseId
    $('#licensedPcsListModal').on('hidden.bs.modal', function () {
        if (!isAnotherModalOpenOnTop) { // Hanya reset jika tidak ada modal lain yang terbuka di atasnya
            selectedSoftwareLicenseId = null;
            selectedSoftwareLicenseQty = 0;
            console.log("licensedPcsListModal fully closed. selectedSoftwareLicenseId reset to null.");
        } else {
            console.log("licensedPcsListModal hidden, but another modal is still open on top. Not resetting selectedSoftwareLicenseId.");
            isAnotherModalOpenOnTop = false; // Reset flag setelah event hidden ini selesai
        }
        $('#licensedPcInfoInModal').hide(); // Hide the info text
    });

    // Fungsi loadLicensedPcs sekarang akan mengambil data employee dan position
    function loadLicensedPcs(tl_id) {
        // Abort previous requests if they are still active
        if (currentLicensedPcsCountAjaxRequest) {
            currentLicensedPcsCountAjaxRequest.abort();
        }
        if (currentLicensedPcsAjaxRequest) {
            currentLicensedPcsAjaxRequest.abort();
        }

        licensedPcTable.clear().draw(); // Clear existing data before loading new data

        // Fetch count first
        currentLicensedPcsCountAjaxRequest = $.ajax({
            url: base_url + '/SoftwareLicense/countLicensedPcs/' + tl_id,
            type: 'GET',
            success: function(response) {
                currentLicensedPcsCountAjaxRequest = null; // Clear request on success
                if (response.status) {
                    const currentCount = response.count;
                    // PASTIKAN liveProductQty DIPARSE SEBAGAI ANGKA
                    const liveProductQty = parseFloat(response.product_qty); // Menggunakan parseFloat untuk numeric(18,2)

                    selectedSoftwareLicenseQty = liveProductQty; // Update the global quantity

                    let infoText = `ID No: ${tl_id} (${currentCount}/${liveProductQty} PCs Licensed)`;
                    const excess = currentCount - liveProductQty;
                    if (excess > 0) {
                        infoText += ` <span class="text-danger">(Overlicensed by ${excess} PCs!)</span>`;
                    } else if (excess < 0) {
                        // Tambahkan logika untuk menampilkan berapa banyak lagi lisensi yang bisa digunakan
                        infoText += ` <span class="text-success">(Available: ${Math.abs(excess)} PCs)</span>`;
                    }
                    $('#licensedPcInfoInModal').html(infoText).show();
                    $('.add-pc-btn').prop('disabled', false);

                    // Load the actual licensed PC data AFTER count is successful
                    currentLicensedPcsAjaxRequest = licensedPcTable.ajax.url(base_url + "/SoftwareLicense/getLicensedPcs/" + tl_id).load(function() {
                        currentLicensedPcsAjaxRequest = null; // Clear request on success
                        licensedPcTable.rows().invalidate().draw(); // Invalidate and redraw to apply rowCallback
                        console.log("Licensed PC data loaded and redrawn successfully for ID: ", tl_id);
                    }, false); // Set second parameter to false to not redraw immediately, let the callback handle it.

                } else {
                    $('#licensedPcInfoInModal').text(`ID No: ${tl_id} (Error loading count)`).show();
                    $('.add-pc-btn').prop('disabled', false);
                    Swal.fire('Error', response.error || 'Failed to get license count.', 'error');
                }
            },
            error: function(xhr, status, error) {
                currentLicensedPcsCountAjaxRequest = null; // Clear request on error
                if (status === "abort") {
                    console.warn("Ajax request for licensed PC count aborted.");
                    return; // Ignore aborted requests
                }
                $('#licensedPcInfoInModal').text(`ID No: ${tl_id} (Error loading count)`).show();
                $('.add-pc-btn').prop('disabled', false);
                Swal.fire('Error', 'Request failed to get license count: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    }

    // NEW: Function to load licensed PCs for the Edit Modal
    function loadEditLicensedPcs(tl_id, product_qty) {
        // Abort previous requests if they are still active
        if (currentLicensedPcsCountAjaxRequest) {
            currentLicensedPcsCountAjaxRequest.abort();
        }
        if (currentLicensedPcsAjaxRequest) {
            currentLicensedPcsAjaxRequest.abort();
        }

        editLicensedPcTable.clear().draw(); // Clear existing data before loading new data

        currentLicensedPcsCountAjaxRequest = $.ajax({
            url: base_url + '/SoftwareLicense/countLicensedPcs/' + tl_id,
            type: 'GET',
            success: function(response) {
                currentLicensedPcsCountAjaxRequest = null;
                if (response.status) {
                    const currentCount = response.count;
                    // Use product_qty passed to this function, which should be fresh from the main license data
                    const liveProductQty = parseFloat(product_qty);

                    // Update selectedSoftwareLicenseQty if needed (though it should be set by the main table click)
                    selectedSoftwareLicenseQty = liveProductQty;

                    let infoText = `ID No: ${tl_id} (${currentCount}/${liveProductQty} PCs Licensed)`;
                    const excess = currentCount - liveProductQty;
                    if (excess > 0) {
                        infoText += ` <span class="text-danger">(Overlicensed by ${excess} PCs!)</span>`;
                    } else if (excess < 0) {
                        infoText += ` <span class="text-success">(Available: ${Math.abs(excess)} PCs)</span>`;
                    }
                    $('#editLicensedPcInfoInModal').html(infoText).show();
                    $('.add-pc-to-edit-modal-btn').prop('disabled', false); // Enable Add PC button

                    currentLicensedPcsAjaxRequest = editLicensedPcTable.ajax.url(base_url + "/SoftwareLicense/getLicensedPcs/" + tl_id).load(function() {
                        currentLicensedPcsAjaxRequest = null;
                        editLicensedPcTable.rows().invalidate().draw(); // Invalidate and redraw to apply rowCallback
                        console.log("Licensed PC data loaded and redrawn successfully for Edit Modal, ID: ", tl_id);
                    }, false); // Set second parameter to false to not redraw immediately, let the callback handle it.

                } else {
                    $('#editLicensedPcInfoInModal').text(`ID No: ${tl_id} (Error loading count)`).show();
                    $('.add-pc-to-edit-modal-btn').prop('disabled', false);
                    Swal.fire('Error', response.error || 'Failed to get license count for edit modal.', 'error');
                }
            },
            error: function(xhr, status, error) {
                currentLicensedPcsCountAjaxRequest = null;
                if (status === "abort") {
                    console.warn("Ajax request for licensed PC count (edit modal) aborted.");
                    return;
                }
                $('#editLicensedPcInfoInModal').text(`ID No: ${tl_id} (Error loading count)`).show();
                $('.add-pc-to-edit-modal-btn').prop('disabled', false);
                Swal.fire('Error', 'Request failed to get license count for edit modal: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    }

        // Event ketika modal Licensed PCs List tampil
        $('#licensedPcsListModal').on('shown.bs.modal', function () {
            console.log('licensedPcsListModal shown. selectedSoftwareLicenseId:', selectedSoftwareLicenseId); // Tambahkan log ini
            if (selectedSoftwareLicenseId !== null) {
                loadLicensedPcs(selectedSoftwareLicenseId);
            } else {
                console.warn('licensedPcsListModal shown, but selectedSoftwareLicenseId is null.');
            }
        });


        // Fungsi handler umum untuk menutup AddLicensedPcModal dan membuka LicensedPcsListModal
        function handleCloseAddLicensedPcModal(shouldReopenParent = true) { // Tambahkan parameter opsional
            console.log("Closing addLicensedPcModal...");
            const addLicensedPcModalElement = document.getElementById('addLicensedPcModal');
            const addLicensedPcModalInstance = bootstrap.Modal.getInstance(addLicensedPcModalElement);

            // Pastikan untuk menghapus event listener 'hidden.bs.modal.reopen' yang lama
            // agar tidak ada duplikasi jika fungsi ini dipanggil berkali-kali.
            $(addLicensedPcModalElement).off('hidden.bs.modal.reopen');

            // Tambahkan event listener yang akan dieksekusi HANYA SEKALI setelah modal benar-benar tersembunyi
            $(addLicensedPcModalElement).one('hidden.bs.modal.reopen', function () {
                console.log("addLicensedPcModal is hidden. selectedSoftwareLicenseId:", selectedSoftwareLicenseId, "shouldReopenParent:", shouldReopenParent);

                if (shouldReopenParent && selectedSoftwareLicenseId !== null) {
                    setTimeout(() => {
                        // Prioritaskan membuka kembali licensedPcsListModal jika itu yang pertama kali membuka addLicensedPcModal
                        if (document.getElementById('licensedPcsListModal').classList.contains('show') || !document.getElementById('editDetailDetModal').classList.contains('show')) {
                            // Jika licensedPcsListModal sudah terbuka (di belakang) atau editDetailDetModal TIDAK terbuka,
                            // maka kita berasumsi licensedPcsListModal adalah parent yang ingin kita tampilkan.
                            console.log("Attempting to show licensedPcsListModal and refresh data...");
                            const licensedPcsListModalElement = document.getElementById('licensedPcsListModal');
                            const licensedPcsListModalInstance = bootstrap.Modal.getInstance(licensedPcsListModalElement);
                            if (licensedPcsListModalInstance) {
                                licensedPcsListModalInstance.show();
                            } else {
                                new bootstrap.Modal(licensedPcsListModalElement).show();
                            }
                            loadLicensedPcs(selectedSoftwareLicenseId); // Refresh data di licensedPcTable
                        } else if (document.getElementById('editDetailDetModal').classList.contains('show')) {
                            console.log("Attempting to show editDetailDetModal and refresh data...");
                            const editDetailDetModalElement = document.getElementById('editDetailDetModal');
                            const editDetailDetModalInstance = bootstrap.Modal.getInstance(editDetailDetModalElement);
                            if (editDetailDetModalInstance) {
                                editDetailDetModalInstance.show();
                            } else {
                                new bootstrap.Modal(editDetailDetModalElement).show();
                            }
                            const currentProductQty = $('#edit_product_qty').val();
                            loadEditLicensedPcs(selectedSoftwareLicenseId, currentProductQty); // Refresh data di editLicensedPcTable
                        } else {
                            console.log("No detectable parent modal to reopen after addLicensedPcModal. This might be an unexpected state.");
                        }
                    }, 100); // Sedikit delay untuk transisi mulus
                } else if (selectedSoftwareLicenseId === null) {
                    console.log("selectedSoftwareLicenseId is null, cannot show parent modal after addLicensedPcModal.");
                } else {
                    console.log("Parent modal explicitly prevented from reopening.");
                }
                isAnotherModalOpenOnTop = false; // Reset flag setelah logika pembukaan parent selesai
            });

            // Sembunyikan modal addLicensedPcModal
            if (addLicensedPcModalInstance) {
                addLicensedPcModalInstance.hide();
            } else {
                // Fallback jika instance tidak dapat ditemukan
                $('#addLicensedPcModal').modal('hide');
            }
        }


    // Fungsi handler umum untuk menutup EditLicensedPcModal dan membuka LicensedPcsListModal
    function handleCloseEditLicensedPcModal() {
        console.log("Closing editLicensedPcModal...");
        const editLicensedPcModalElement = document.getElementById('editLicensedPcModal');
        const editLicensedPcModalInstance = bootstrap.Modal.getInstance(editLicensedPcModalElement);

        if (editLicensedPcModalInstance) {
            $(editLicensedPcModalElement).off('hidden.bs.modal.reopen'); // Hapus event listener sebelumnya

            $(editLicensedPcModalElement).one('hidden.bs.modal.reopen', function () {
                console.log("editLicensedPcModal is hidden. selectedSoftwareLicenseId:", selectedSoftwareLicenseId);
                if (selectedSoftwareLicenseId !== null) {
                    console.log("Attempting to show licensedPcsListModal...");
                    setTimeout(() => {
                        const licensedPcsListModalElement = document.getElementById('licensedPcsListModal');
                        const licensedPcsListModalInstance = bootstrap.Modal.getInstance(licensedPcsListModalElement);

                        if (licensedPcsListModalInstance) {
                            licensedPcsListModalInstance.show();
                        } else {
                            new bootstrap.Modal(licensedPcsListModalElement).show();
                        }
                        // Pastikan data di licenseDetailOverviewTable dan licensedPcTable di-refresh
                        // PENTING: Lakukan fetch ulang data lisensi utama dari server untuk memastikan `Last Update` dan `Last User` akurat
                        $.ajax({
                            url: base_url + '/SoftwareLicense/edit', // Menggunakan endpoint edit untuk fetch data lengkap
                            type: 'POST',
                            data: { id: selectedSoftwareLicenseId },
                            success: function(responseFetch) {
                                if (responseFetch.status) {
                                    const updatedLicenseData = responseFetch.data;
                                    selectedSoftwareLicenseQty = parseFloat(updatedLicenseData.product_qty);
                                    licenseDetailOverviewTable.clear().rows.add([updatedLicenseData]).draw();
                                    loadLicensedPcs(selectedSoftwareLicenseId); // Refresh tabel PC juga
                                } else {
                                    console.error('Failed to re-fetch license data for modal reopening:', responseFetch.message);
                                    loadLicensedPcs(selectedSoftwareLicenseId); // Setidaknya refresh tabel PC
                                }
                            },
                            error: function(xhrFetch, statusFetch, errorFetch) {
                                console.error('AJAX error re-fetching license data for modal reopening:', errorFetch);
                                loadLicensedPcs(selectedSoftwareLicenseId); // Setidaknya refresh tabel PC
                            }
                        });
                    }, 100);
                } else {
                    console.log("selectedSoftwareLicenseId is null, cannot show licensedPcsListModal.");
                }
            });

            editLicensedPcModalInstance.hide();
        } else {
            console.warn("Could not get Bootstrap modal instance for editLicensedPcModal. Trying jQuery hide.");
            $('#editLicensedPcModal').modal('hide');
            $('#editLicensedPcModal').one('hidden.bs.modal.reopen-fallback', function () {
                if (selectedSoftwareLicenseId !== null) {
                    setTimeout(() => {
                        $('#licensedPcsListModal').modal('show');
                        loadLicensedPcs(selectedSoftwareLicenseId);
                    }, 100);
                }
            });
        }
    }

    // Event listener untuk tombol "X" di header modal Add Licensed PC
    $('#closeAddLicensedPcModalHeader').on('click', function() {
        handleCloseAddLicensedPcModal(true); // Pastikan true
    });

    // Event listener untuk tombol "Cancel" di footer modal Add Licensed PC
    $('#cancelAddLicensedPcModalFooter').on('click', function() {
        handleCloseAddLicensedPcModal(true); // Pastikan true
    });
    
    // Handle Add Licensed PC form submission
    $('#submit-licensed-pc-btn').on('click', function() {
        const licenseId = $('#licensed_pc_license_id').val();
        if (!licenseId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'License ID is missing. Please select a software license first.',
            });
            return;
        }

        // --- TAMBAHKAN VALIDASI SISI KLIEN UNTUK ASSET ID (pc_id) ---
        const assetIdField = $('#add_pc_asset_id');
        const assetIdValue = assetIdField.val();
        const isNumeric = /^\d*$/.test(assetIdValue);

        if (assetIdValue !== '' && !isNumeric) {
            assetIdField.addClass('is-invalid');
            $('#add_pc_asset_id_error').text('Asset ID must be a number.').show();
            return;
        } else {
            assetIdField.removeClass('is-invalid');
            $('#add_pc_asset_id_error').text('').hide();
        }
        // --- AKHIR VALIDASI SISI KLIEN ---

        const assetNoField = $('#add_pc_asset_no');
        if (!assetNoField.val()) {
            assetNoField.addClass('is-invalid');
            $('#add_pc_asset_no_error').text('Asset Number is required.').show();
            return;
        } else {
            assetNoField.removeClass('is-invalid');
            $('#add_pc_asset_no_error').text('').hide();
        }

        const formData = $('#addLicensedPcForm').serializeArray();
        formData.push({name: 'tl_id', value: licenseId});
        formData.push({name: 'ld_status', value: 1});

        // Add employee_code and position_code to formData
        formData.push({name: 'employee_code', value: $('#add_pc_employee_code').val() || null});
        formData.push({name: 'position_code', value: $('#add_pc_position_code').val() || null});

        $.ajax({
            url: base_url + '/SoftwareLicense/addLicensedPc',
            type: 'POST',
            data: $.param(formData),
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Licensed PC has been added.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Panggil fungsi penutup modal dengan parameter true untuk memastikan parent dibuka kembali
                        // Ini akan memicu refresh di licensedPcsListModal
                        handleCloseAddLicensedPcModal(true); 
                    });
                } else {
                    if (response.errors) {
                        if (response.errors.asset_no) {
                            $('#add_pc_asset_no').addClass('is-invalid');
                            $('#add_pc_asset_no_error').text(response.errors.asset_no).show();
                        }
                        if (response.errors.pc_id) {
                            $('#add_pc_asset_id').addClass('is-invalid');
                            $('#add_pc_asset_id_error').text(response.errors.pc_id).show();
                        }
                    } else if (response.error) {
                        Swal.fire('Error', response.error || 'Failed to add licensed PC.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });

    // Handle Edit Licensed PC button click (from within licensedPcsListModal)
    $('#licensedPcTable').on('click', '.edit-licensed-pc-btn', function() {
        const ld_id = $(this).data('id');
        // PENTING: Set `isAnotherModalOpenOnTop` menjadi true sebelum membuka modal baru.
        isAnotherModalOpenOnTop = true; // Flag ini harus diatur di sini!

        callingModalForPcSearch = 'editLicensedPcModal';
        callingModalForEmployeeSearch = 'editLicensedPcModal';

        // Sembunyikan dulu licensedPcsListModal untuk transisi yang lebih baik
        $('#licensedPcsListModal').modal('hide'); 
        
        // Membuka editLicensedPcModal (ini akan mengisi form di sana)
        $.ajax({
            url: base_url + '/SoftwareLicense/editLicensedPc',
            type: 'POST',
            data: { ld_id: ld_id },
            success: function(response) {
                if (response.status) {
                    const d = response.data;
                    $('#edit_licensed_pc_id').val(d.ld_id);
                    $('#edit_licensed_pc_license_id').val(d.tl_id);
                    $('#edit_pc_e_id').val(d.ld_pc_id || '');

                    // Set readonly and add bg-light based on is_finder_sourced
                    if (d.is_finder_sourced == 1) {
                        $('#edit_pc_asset_no').val(d.ld_assetno).prop('readonly', true).addClass('bg-light');
                        $('#edit_pc_asset_name').val(d.ld_pcnama).prop('readonly', true).addClass('bg-light');
                        $('#edit_pc_serial_number').val(d.ld_serialnumber).prop('readonly', true).addClass('bg-light');
                        $('#edit_pc_asset_id').val(d.ld_pc_id).prop('readonly', true).addClass('bg-light');
                    } else {
                        $('#edit_pc_asset_no').val(d.ld_assetno).prop('readonly', false).removeClass('bg-light');
                        $('#edit_pc_asset_name').val(d.ld_pcnama).prop('readonly', false).removeClass('bg-light');
                        $('#edit_pc_serial_number').val(d.ld_serialnumber).prop('readonly', false).removeClass('bg-light');
                        $('#edit_pc_asset_id').val(d.ld_pc_id).prop('readonly', false).removeClass('bg-light');
                    }

                    $('#edit_pc_employee_name').val(d.em_emplname || '').prop('readonly', true);
                    $('#edit_pc_employee_code').val(d.ld_employee_code || '');
                    $('#edit_pc_position_code').val(d.ld_position_code || '');

                    // Clear any previous invalid states for edit modal
                    $('#edit_pc_asset_no').removeClass('is-invalid');
                    $('#edit_pc_asset_no_error').text('').hide();
                    $('#edit_pc_asset_id').removeClass('is-invalid'); // Clear for asset ID
                    $('#edit_pc_asset_id_error').text('').hide(); // Clear for asset ID error message

                    $('#editLicensedPcModal').modal('show');
                } else {
                    Swal.fire('Error', response.message || 'Data not found', 'error');
                    // Jika gagal memuat data PC, pastikan kembali ke modal parent.
                    setTimeout(() => $('#licensedPcsListModal').modal('show'), 100); 
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
                // Jika AJAX gagal, pastikan kembali ke modal parent.
                setTimeout(() => $('#licensedPcsListModal').modal('show'), 100);
            }
        });
    });

    // Handle Update Licensed PC form submission
    $('#update-licensed-pc-btn').on('click', function() {
        const ld_id = $('#edit_licensed_pc_id').val();
        const tl_id = $('#edit_licensed_pc_license_id').val();

        // Validasi sisi klien untuk asset_no (tidak berubah)
        const assetNoField = $('#edit_pc_asset_no');
        if (!assetNoField.val()) {
            assetNoField.addClass('is-invalid');
            $('#edit_pc_asset_no_error').text('Asset Number is required.').show();
            return;
        } else {
            assetNoField.removeClass('is-invalid');
            $('#edit_pc_asset_no_error').text('').hide();
        }
        
        // Validasi sisi klien untuk pc_id (tidak berubah)
        const assetIdField = $('#edit_pc_asset_id');
        const assetIdValue = assetIdField.val();
        const isNumeric = /^\d*$/.test(assetIdValue);

        if (assetIdValue !== '' && !isNumeric) {
            assetIdField.addClass('is-invalid');
            $('#edit_pc_asset_id_error').text('Asset ID must be a number.').show();
            return;
        } else {
            assetIdField.removeClass('is-invalid');
            $('#edit_pc_asset_id_error').text('').hide();
        }

        const formData = $('#editLicensedPcForm').serializeArray();
        formData.push({name: 'tl_id', value: tl_id}); // Pastikan tl_id terkirim
        formData.push({name: 'ld_status', value: 1}); // Atau sesuaikan status jika ada dropdown

        // Add employee_code and position_code to formData
        formData.push({name: 'employee_code', value: $('#edit_pc_employee_code').val() || null});
        formData.push({name: 'position_code', value: $('#edit_pc_position_code').val() || null});

        $.ajax({
            url: base_url + '/SoftwareLicense/updateLicensedPc',
            type: 'POST',
            data: $.param(formData),
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Licensed PC has been updated.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Tutup modal editLicensedPcModal
                        $('#editLicensedPcModal').modal('hide');
                        // Reload tabel PC di dalam Licensed Pcs List Modal
                        loadLicensedPcs(tl_id); // Panggil fungsi untuk me-refresh data PC
                        // Flag isAnotherModalOpenOnTop akan di-reset oleh handleCloseEditLicensedPcModal
                        // yang dipanggil saat modal ditutup.
                    });
                } else {
                    if (response.errors) {
                        if (response.errors.asset_no) {
                            $('#edit_pc_asset_no').addClass('is-invalid');
                            $('#edit_pc_asset_no_error').text(response.errors.asset_no).show();
                        }
                        if (response.errors.pc_id) {
                            $('#edit_pc_asset_id').addClass('is-invalid');
                            $('#edit_pc_asset_id_error').text(response.errors.pc_id).show();
                        }
                    } else if (response.error) {
                        Swal.fire('Error', response.error || 'Failed to update licensed PC.', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });

    // Event listener untuk tombol "X" di header modal Edit Licensed PC
    $('#editLicensedPcModal .btn-close').on('click', function() {
        // Ini akan memicu `handleCloseEditLicensedPcModal`
        handleCloseEditLicensedPcModal();
    });

    // Event listener untuk tombol "Cancel" di footer modal Edit Licensed PC
    $('#editLicensedPcModal .btn-secondary[data-bs-dismiss="modal"]').on('click', function() {
        // Ini akan memicu `handleCloseEditLicensedPcModal`
        handleCloseEditLicensedPcModal();
    });

    // Handle Delete Licensed PC button click
    $('#licensedPcTable').on('click', '.delete-licensed-pc-btn', function() {
        const ld_id = $(this).data('id');
        const tl_id = selectedSoftwareLicenseId;

        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently remove this PC from the license!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + '/SoftwareLicense/deleteLicensedPc',
                    type: 'POST',
                    data: { ld_id: ld_id },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Licensed PC has been removed.',
                                icon: 'success',
                                timer: 1500, // Menutup otomatis setelah 1.5 detik
                                showConfirmButton: false // Menghilangkan tombol "Ok"
                            });
                            loadLicensedPcs(tl_id);
                        } else {
                            Swal.fire('Error', response.error || 'Delete failed', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
                    }
                });
            }
        });
    });

    // Reset modals on close
    $('#addLicensedPcModal').on('hidden.bs.modal', function () {
        // Reset konteks finder saat modal Add Licensed PC ditutup.
        callingModalForPcSearch = '';
        callingModalForEmployeeSearch = '';

        $('#add_pc_asset_no').prop('readonly', false).removeClass('bg-light');
        $('#add_pc_asset_name').prop('readonly', false).removeClass('bg-light');
        $('#add_pc_serial_number').prop('readonly', false).removeClass('bg-light');
        $('#add_pc_asset_id').prop('readonly', false).removeClass('bg-light'); // Pastikan ini juga direset
        // Clear any validation messages
        $('#add_pc_asset_id').removeClass('is-invalid');
        $('#add_pc_asset_id_error').text('').hide();

        // Hanya User yang readonly
        $('#add_pc_employee_name').prop('readonly', true);
    });

    $('#editLicensedPcModal').on('hidden.bs.modal', function () {
        if (callingModalForPcSearch === '' && callingModalForEmployeeSearch === '') {
            $('#editLicensedPcForm')[0].reset();
            $('#edit_pc_asset_no').removeClass('is-invalid');
            $('#edit_pc_asset_no_error').text('').hide();
            $('#edit_pc_asset_id').removeClass('is-invalid'); // Clear for asset ID
            $('#edit_pc_asset_id_error').text('').hide(); // Clear for asset ID error message
            // Semua field, termasuk Asset Number, tidak readonly saat modal ditutup
            $('#edit_pc_asset_no').prop('readonly', false).removeClass('bg-light');
            $('#edit_pc_asset_name').prop('readonly', false).removeClass('bg-light');
            $('#edit_pc_serial_number').prop('readonly', false).removeClass('bg-light');
            $('#edit_pc_asset_id').prop('readonly', false).removeClass('bg-light'); // Pastikan ini juga direset
            // Hanya User yang readonly
            $('#edit_pc_employee_name').prop('readonly', true);

            $('#edit_pc_e_id').val('');
            $('#edit_pc_employee_code').val('');
            $('#edit_pc_position_code').val('');
        }
    });
});
</script>
<?= $this->endSection() ?>
