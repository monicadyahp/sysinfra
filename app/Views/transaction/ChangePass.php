<?= $this->extend("main/template") ?>
<?= $this->section("content") ?>
<!-- Selectize CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.bootstrap5.min.css">

<!-- jQuery (Wajib untuk Selectize) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Selectize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"></script>


<body class="">
<main class="main-content d-flex align-items-center justify-content-center min-vh-100">
<div class="container-fluid p-0">
    <div class="row m-0 vh-100">
        <div class="col-12 p-0">
            <div class="card z-index-0 p-4 shadow w-100 h-100">
                <div class="card-header text-center pt-3">
                    <h5>Change Password</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <form id="changePasswordForm" method="POST" autocomplete="off" class="w-100 px-4">
                        <div class="mb-3">
                            <select name="ums_userid" id="ums_userid" class="form-select w-100" data-placeholder="Select user" required>
                                <option value="" selected disabled></option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user->value ?>"><?= $user->text ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control w-100" name="old_password" id="old_password" placeholder="Old Password" required autocomplete="off" readonly 
                                onfocus="this.removeAttribute('readonly');">
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control w-100" name="new_password" id="new_password" placeholder="New Password" required autocomplete="off" readonly 
                                onfocus="this.removeAttribute('readonly');">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success w-100 submit">
                                <span class="indicator-label">Save</span>
                                <span class="loading" style="display: none">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div> <!-- End card -->
        </div> <!-- End col -->
    </div> <!-- End row -->
</div> <!-- End container-fluid -->

</main>



    <!-- Tambahkan SweetAlert2 untuk pesan yang lebih baik -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#changePasswordForm").submit(function(event) {
            event.preventDefault(); // Mencegah form submit normal

            let userId = $("#ums_userid").val();
            let oldPassword = $("#old_password").val();
            let newPassword = $("#new_password").val();

            // Validasi sederhana sebelum mengirim request
            if (!userId || !oldPassword || !newPassword) {
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan!",
                    text: "Semua kolom harus diisi.",
                });
                return;
            }

            // Tampilkan loading saat AJAX berjalan
            Swal.fire({
                title: "Sedang diproses...",
                text: "Mohon tunggu sebentar.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "<?= base_url('changePassword/update') ?>", // Pastikan sesuai dengan route
                type: "POST",
                data: {
                    ums_userid: userId,
                    old_password: oldPassword,
                    new_password: newPassword
                },
                dataType: "json",
                success: function(response) {
                    Swal.close(); // Tutup loading

                    if (response.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: response.message,
                        }).then(() => {
                            $("#changePasswordForm")[0].reset(); // Reset form setelah sukses
                            window.location.href = "<?= base_url('/') ?>"; // Auto logout
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: response.message || "Terjadi kesalahan saat mengubah password.",
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close(); // Tutup loading
                    
                    let errorMessage = "Terjadi kesalahan, coba lagi nanti.";
                    if (xhr.status === 400) {
                        errorMessage = "Request tidak valid. Periksa input Anda.";
                    } else if (xhr.status === 401) {
                        errorMessage = "Anda tidak memiliki izin untuk melakukan ini.";
                    } else if (xhr.status === 500) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: "error",
                        title: "Oops!",
                        text: errorMessage,
                    });
                }
            });
        });
    });
</script>


<script>
    $(document).ready(function () {
        $('#ums_userid').selectize({
            placeholder: "Select user", // Placeholder seperti yang ada di data-placeholder
            allowEmptyOption: true,     // Mengizinkan opsi kosong
            create: false               // Tidak bisa membuat opsi baru
        });
    });
</script>


            </div>
          </div>
        </div>
      </div>
    </div>
  </main>


  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
 

<?= $this->endSection() ?>