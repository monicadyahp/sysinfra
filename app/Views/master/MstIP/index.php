<?= $this->extend("main/template") ?>
<?= $this->section('content') ?>

<style>
    /* Styling untuk status */
    .status-used {
        color: #28a745; /* Green */
        font-weight: bold;
    }
    .status-unused {
        color: #dc3545; /* Red */
        font-weight: bold;
    }

    /* Style for readonly input fields */
    .form-control[readonly] {
        background-color: #e9ecef; /* Light gray to indicate readonly */
        cursor: default; /* Change cursor to default */
    }

    /* Style for custom filter dropdown to align with DataTables' elements */
    .dataTables_wrapper .dataTables_filter {
        display: flex;
        align-items: center;
        gap: 10px; /* Space between search and custom filter */
    }

    .dataTables_wrapper .dataTables_length {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Adjust placement of the custom filter */
    .dataTables_wrapper .dataTables_length label {
        margin-bottom: 0; /* Remove default margin */
    }
    .custom-status-filter {
        display: flex;
        align-items: center;
        margin-left: 10px; /* Adjust spacing as needed */
    }
    .custom-status-filter label {
        margin-right: 5px;
        margin-bottom: 0;
    }
    .custom-status-filter select {
        width: auto; /* Adjust width of the select box */
    }

    /* Flexbox for top content to align elements */
    .dataTables_wrapper .row:first-child {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap; /* Allow wrapping on smaller screens */
    }

    /* Styling untuk toggle switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 34px;
        height: 20px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        /* Default background untuk Unused (jika mip_status_text adalah 'Unused') -> Merah */
        background-color: #dc3545; 
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50%;
    }

    /* Jika input checked (berarti statusnya Used) -> Hijau */
    input:checked + .slider {
        background-color: #28a745; /* Used (Green) */
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    /* Jika input checked (berarti statusnya Used) -> Geser ke kanan */
    input:checked + .slider:before {
        -webkit-transform: translateX(14px); 
        -ms-transform: translateX(14px);
        transform: translateX(14px);
    }
</style>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Master IP Address</h4>
    </div>

    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered" id="tabelMstIP">
            <thead class="table-light">
                <tr>
                    <th style="width: 10%">Action</th>
                    <th>ID</th>
                    <th>VLAN ID</th>
                    <th>VLAN Name</th>
                    <th>IP Address</th>
                    <th>Status</th>
                    <th>Action Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail IP Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ID:</label>
                            <input type="text" class="form-control" id="detail_mip_id" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">VLAN ID:</label>
                            <input type="text" class="form-control" id="detail_mip_vlanid" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">VLAN Name:</label>
                            <input type="text" class="form-control" id="detail_mip_vlanname" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">IP Address:</label>
                            <input type="text" class="form-control" id="detail_mip_ipadd" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status:</label>
                            <input type="text" class="form-control" id="detail_mip_status_text" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Update:</label>
                            <input type="text" class="form-control" id="detail_mip_lastupdate" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last User:</label>
                            <input type="text" class="form-control" id="detail_mip_lastuser" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
$(document).ready(function() {
    const base_url = '<?= base_url() ?>';
    let dataTableInstance; // Declare a variable to hold the DataTable instance
    let currentFilterState = 'Unused'; // Tambahkan variabel untuk melacak status filter yang aktif

    // Function to initialize or reload DataTable with a status filter
    function initializeDataTable(statusFilter = 'Unused') {
        currentFilterState = statusFilter;

        if (dataTableInstance) {
            dataTableInstance.destroy();
            $('#tabelMstIP tbody').empty(); // Kosongkan tbody
        }

        dataTableInstance = $('#tabelMstIP').DataTable({
            scrollX: true,
            pageLength: 10,
            order: [[1, 'asc']],
            destroy: true, 
            ajax: {
                url: base_url + "/MstIP/getData",
                data: function(d) {
                    d.status = statusFilter;
                },
                dataSrc: "",
                error: function(xhr, status, error) {
                    console.error("Error fetching IP Master data:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load IP Master data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error),
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
                            <div class="d-flex justify-content-center">
                                <a href="javascript:;" class="btn btn-icon btn-outline-info detail-btn" data-id="${row.mip_id}" title="View IP Data">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </div>`;
                    }
                },
                { data: 'mip_id' },
                { data: 'mip_vlanid' },
                { data: 'mip_vlanname' },
                { data: 'mip_ipadd' },
                {
                    // Kolom Status dengan warna
                    data: 'mip_status_text', // Menggunakan teks yang sudah diformat dari controller
                    render: function(data, type, row) {
                        // Logika kelas CSS berdasarkan mip_status_text
                        const statusClass = (data === 'Used') ? 'status-used' : 'status-unused';
                        return `<span class="${statusClass}">${data}</span>`;
                    }
                },
                {
                    // Kolom Toggle Status (switch)
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        // Perhatikan bahwa row.mip_lastuser bisa berupa string kosong jika dari database
                        // Konversi ke int untuk perbandingan yang konsisten
                        const currentMipLastUser = parseInt(row.mip_lastuser) || 0;
                        const currentMipStatus = parseInt(row.mip_status) || 0;

                        // Checkbox harus checked jika mip_status_text adalah 'Used'
                        // Logika ini harus sama dengan di controller
                        const isChecked = (currentMipLastUser !== 0 && currentMipStatus === 0) ? 'checked' : '';
                        
                        // Kirim mip_status dan mip_lastuser mentah ke frontend untuk digunakan pada toggle
                        return `
                            <label class="switch">
                                <input type="checkbox" class="status-toggle" data-id="${row.mip_id}" data-current-mip-status="${row.mip_status}" data-current-mip-lastuser="${row.mip_lastuser}" ${isChecked}>
                                <span class="slider"></span>
                            </label>
                        `;
                    }
                }
            ],
            language: {
                "sLengthMenu": "Show _MENU_ entries",
                "sSearch": "Search:"
            },
            initComplete: function() {
                var api = this.api();
                var wrapper = $(api.table().container());

                if (wrapper.find('.custom-status-filter').length === 0) {
                    var statusFilterHtml = `
                        <div class="custom-status-filter">
                            <label for="statusFilter">Status:</label>
                            <select id="statusFilter" class="form-select form-select-sm">
                                <option value="All">All</option>
                                <option value="Used">Used</option>
                                <option value="Unused">Unused</option>
                            </select>
                        </div>`;

                    wrapper.find('.dataTables_length').append(statusFilterHtml);

                    $('#statusFilter').on('change', function() {
                        var selectedStatus = $(this).val();
                        initializeDataTable(selectedStatus);
                    });
                }
                
                $('#statusFilter').val(currentFilterState); 
            }
        });
    }

    initializeDataTable('Unused'); // Inisialisasi awal dengan filter 'Unused'

    // Handle Detail button click
    $('#tabelMstIP').on('click', '.detail-btn', function() {
        var id = $(this).data('id');

        $.ajax({
            url: base_url + '/MstIP/getDetail',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.status) {
                    const d = response.data;
                    $('#detail_mip_id').val(d.mip_id);
                    $('#detail_mip_vlanid').val(d.mip_vlanid);
                    $('#detail_mip_vlanname').val(d.mip_vlanname);
                    $('#detail_mip_ipadd').val(d.mip_ipadd);
                    $('#detail_mip_status_text').val(d.mip_status_text);
                    $('#detail_mip_lastupdate').val(d.mip_lastupdate);
                    $('#detail_mip_lastuser').val(d.mip_lastuser);

                    $('#detailModal').modal('show');
                } else {
                    Swal.fire('Error', response.message || 'Data not found', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });

    // Handle Status Toggle click
    $('#tabelMstIP').on('change', '.status-toggle', function() {
        const mip_id = $(this).data('id');
        const currentMipStatus = parseInt($(this).data('current-mip-status')) || 0;
        const currentMipLastUser = parseInt($(this).data('current-mip-lastuser')) || 0;
        const $thisToggle = $(this);

        const isCurrentlyUsedLogical = (currentMipLastUser !== 0 && currentMipStatus === 0);
        const newLogicalStatusText = isCurrentlyUsedLogical ? 'Unused' : 'Used';

        Swal.fire({
            title: 'Konfirmasi Perubahan Status',
            text: `Anda yakin ingin mengubah status IP Address ini menjadi "${newLogicalStatusText}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + '/MstIP/toggleStatus',
                    type: 'POST',
                    data: {
                        id: mip_id,
                        currentMipStatus: currentMipStatus,
                        currentMipLastUser: currentMipLastUser
                    },
                    success: function(response) {
                        if (response.success) {
                            // SWEETALERT YANG DIPERBARUI
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: false, // TIDAK ADA TOMBOL "OK"
                                timer: 3000, // TUTUP OTOMATIS SETELAH 3 DETIK
                                timerProgressBar: true // MENAMPILKAN PROGRESS BAR
                            });
                            initializeDataTable(currentFilterState); // Reload DataTables
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                            $thisToggle.prop('checked', isCurrentlyUsedLogical);
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'Gagal mengubah status: ' + (xhr.responseJSON ? xhr.responseJSON.message : error),
                            'error'
                        );
                        $thisToggle.prop('checked', isCurrentlyUsedLogical);
                    }
                });
            } else {
                $thisToggle.prop('checked', isCurrentlyUsedLogical);
            }
        });
    });
});
</script>

<?= $this->endSection() ?>