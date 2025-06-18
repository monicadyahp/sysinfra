<!DOCTYPE html>

<html lang="en" data-assets-path="<?= base_url() ?>assets/">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Login - MOM | Manufacturing Operations Management</title>

    <!-- Icon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?>assets/img/jstlogo.ico">

    <!-- Fonts -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/fonts/fontsgoogleapis.css">

    <!-- Icons -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/fonts/fontawesome.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/fonts/tabler-icons.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/fonts/flag-icons.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/css/rtl/core.css" class="template-customizer-core-css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/demo.css">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/node-waves/node-waves.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/typeahead-js/typeahead.css">
    <!-- Vendor -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/formvalidation/dist/css/formValidation.min.css">

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/css/pages/page-auth.css">
    <!-- Helpers -->
    <script src="<?= base_url() ?>assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <!-- <script src="<?= base_url() ?>assets/vendor/js/template-customizer.js"></script> -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?= base_url() ?>assets/js/config.js"></script>
    <script src="<?= base_url() ?>assets/vendor/js/custom.js"></script>
</head>

<body>
    <!-- Content -->

    <div class="d-flex flex-column flex-sm-row vh-100">
    <div class="d-flex w-100 w-sm-50 h-100 bg-primary" 
         style="background-image: url('<?= base_url() ?>assets/img/3.jpg') !important; 
                background-size: cover; 
                background-position: center; 
                background-repeat: no-repeat;">
    </div>
    <div class="d-flex justify-content-center align-items-center w-100 w-sm-50 vh-100 bg-white">

            <!-- Login -->
            <div class="card shadow-none w-50" style="border-radius: 0px;">
                <div class="card-body">
                    <div class="row" style="height: 400px;"> <!-- Set a fixed height for centering -->
                        <!-- Logo Column -->
                        <div class="col-md-6 d-flex w-100 align-items-center justify-content-center">
                            <img src="<?= base_url() ?>assets/img/logobig.png" alt="Logo" class="img-fluid" style="max-width: 200px; height: 100px; margin-bottom: -10px;">
                        </div>

                        <!-- Text Below the Logo -->
                        <div class="text-center">
                            <h3 class="mb-1 pt-2">Welcome to Admin Access</h3>
                            <p style="margin-bottom: 20px">To Make Our Work Easier</p>
                        </div>

                        <!-- Form Column -->
                        <div class="col-md-12 d-flex align-items-center justify-content-center"> <!-- Center content -->
                            <form id="login" class="mb-3" enctype="multipart/form-data" method="POST" style="width: 100%">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" autofocus>
                                </div>
                                <div class="mb-4 form-password-toggle">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label" for="password">Password</label>
                                    </div>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password">
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-reddit d-grid w-100 submit">
                                        <span class="indicator-label">Login</span>
                                        <span class="loading" style="display: none">Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                                </div>
                                <div class="text-center" style="margin-top: 40px;">
                                    <p>Created by Jinsystem <span id="currentYear"></span>.</p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Login -->
        </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="<?= base_url() ?>assets/vendor/libs/jquery/jquery.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/popper/popper.js"></script>
    <script src="<?= base_url() ?>assets/vendor/js/bootstrap.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/node-waves/node-waves.js"></script>

    <script src="<?= base_url() ?>assets/vendor/libs/hammer/hammer.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/i18n/i18n.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="<?= base_url() ?>assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="<?= base_url() ?>assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>

    <!-- Main JS -->
    <script src="<?= base_url() ?>assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="<?= base_url() ?>assets/js/pages-auth.js"></script>

    <script>
        $(document).ready(function() {
            document.getElementById("currentYear").textContent = new Date().getFullYear();
            let base_url = "<?= base_url() ?>";

            // Handle the eye icon toggle
            $(".input-group-text").on("click", function() {
                let passwordInput = $("#password");
                let icon = $(this).find("i");

                // Toggle the password field type between password and text
                if (passwordInput.attr("type") === "password") {
                    passwordInput.attr("type", "text");
                    icon.removeClass("ti-eye-off").addClass("ti-eye"); // Change icon
                } else {
                    passwordInput.attr("type", "password");
                    icon.removeClass("ti-eye").addClass("ti-eye-off"); // Revert icon
                }
            });

            $("#login").submit(function(e) {
                e.preventDefault();
                let username = $("#username").val();
                let password = $("#password").val();
                if (!username) {
                    $("#username").focus();
                    return false;
                }
                if (!password) {
                    $("#password").focus();
                    return false;
                }

                showLoading(); // Show loading indicator

                $.ajax({
                    url: base_url + "C_Auth/login",
                    type: "POST",
                    data: {
                        "username": username,
                        "password": password
                    }
                }).done(function(response) {
                    if (response.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            customClass: {
                                popup: 'swal2-popup-custom'
                            },
                            showConfirmButton: false, // No confirm button
                            timer: 2000 // Close after 2 seconds
                        }).then(function() {
                            // Redirect to admin dashboard
                            // window.location.assign(base_url + "Main");
                            window.location.href = response.redirect_url;
                        });
                        hideLoading(); // Hide loading indicator
                        $('#password').val('');
                        // window.location.assign(base_url + "Main");
                    } else {
                        // Display error message if it exists, otherwise display a generic error
                        let errorMessage = response.error || "An unknown error occurred.";
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: errorMessage, // Display the error message from the response
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                        });
                        hideLoading(); // Hide loading indicator
                        $('#password').val('');
                    }
                }).fail(function(xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Incorrect password. Please try again.",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    });
                    hideLoading(); // Hide loading indicator
                    $('#password').val('');
                });
            });
        });
    </script>
    <!--end::Global Javascript Bundle-->
    <script src="<?= base_url('/assets/vendor/sweetalert2/sweetalert2.all.min.js') ?>"></script>
</body>

</html>

<!-- <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4" style="max-width: 800px;">
                <div class="card" style="border-radius: 20px">
                    <div class="card-body">
                        <div class="text-center">
                            <h4 class="mb-1 pt-2">Welcome to MOM!</h4>
                            <p style="margin-bottom: 0px">Manufacturing Operations Management</p>
                        </div>

                        <div class="row" style="height: 400px;">
                            <div class="col-md-6 d-flex align-items-center justify-content-center">
                                <img src="<?= base_url() ?>assets/img/logoungu.png" alt="Logo" class="img-fluid" style="max-width: 100%; height: auto;">
                            </div>

                            <div class="col-md-6 d-flex align-items-center justify-content-center"> 
                                <form id="login" class="mb-3" enctype="multipart/form-data" method="POST" style="width: 100%">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" autofocus>
                                    </div>
                                    <div class="mb-3 form-password-toggle">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="password">Password</label>
                                        </div>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password">
                                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary d-grid w-100 submit">
                                            <span class="indicator-label">Login</span>
                                            <span class="loading" style="display: none">Please wait...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->