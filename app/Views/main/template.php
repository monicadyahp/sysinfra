<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?= base_url() ?>assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>MOM | Manufacturing Operations Management</title>

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
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/css/rtl/theme-default-dark.css" class="template-customizer-theme-css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/demo.css">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/node-waves/node-waves.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/typeahead-js/typeahead.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/apex-charts/apex-charts.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/swiper/swiper.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/flatpickr/flatpickr.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/select2/select2.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/libs/bootstrap-select/bootstrap-select.css">

    <!-- Page CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/css/pages/cards-advance.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/vendor/css/custom.css">

    <!-- Helpers -->
    <script src="<?= base_url() ?>assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <!-- <script src="<?= base_url() ?>assets/vendor/js/template-customizer.js"></script> -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?= base_url() ?>assets/js/config.js"></script>
    <script src="<?= base_url() ?>assets/vendor/js/custom.js"></script>
    <script src="<?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script>

    <style>
    #layout-navbar {
        transition: transform 0.3s ease-in-out;
    }

    .hide-navbar {
        transform: translateY(-150%);
    }
</style>

</head>

<body style="background: #0F172A;">
    <!-- Layout wrapper -->
    <div class="layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo" style="padding-left: 0px">
                    <span class="app-brand-logo demo" style="width: 180px; height: 54px">
                        <!-- Logo -->
                        <img alt="Logo" src="<?= base_url() ?>assets/img/logomedium.png">
                    </span>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
                        <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <?php
                    $usermenu = session()->get('usermenu');
                    $groupedMenus = [];

                    // Group menus by their group name
                    foreach ($usermenu as $menu) {
                        $groupedMenus[$menu->umg_groupname][] = $menu;
                    }

                    // Loop through each group and create menu items
                    foreach ($groupedMenus as $groupName => $groupMenus) :
                        // Determine if this group is active
                        $isGroupActive = ($groupName === $active_menu_group);
                        $groupIcon = htmlspecialchars($groupMenus[0]->groupicon);

                        // Retrieve the group icon dynamically from the first menu item
                    ?>
                        <li class="menu-item <?= $isGroupActive ? 'active open' : '' ?>">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons <?= $groupIcon ?>"></i>
                                <div data-i18n="<?= htmlspecialchars($groupName) ?>"><?= htmlspecialchars($groupName) ?></div>
                                <!-- <div class="badge bg-label-primary rounded-pill ms-auto"><?= count($groupMenus) ?></div> -->
                            </a>
                            <ul class="menu-sub">
                                <?php foreach ($groupMenus as $menu) :
                                    $isActive = ($menu->umn_menuname === $active_menu_name);
                                    $menuPath = htmlspecialchars(basename($menu->umn_path));
                                    $menuName = htmlspecialchars($menu->umn_menuname);
                                ?>
                                    <li class="menu-item <?= $isActive ? 'active' : '' ?>">
                                        <a href="<?= $menuPath ?>" class="menu-link" onclick="sessionStorage.clear()">
                                            <div data-i18n="<?= $menuName ?>"><?= $menuName ?></div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center shadow-none bg-navbar-theme fixed-top" id="layout-navbar" >
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="ti ti-menu-2 ti-sm"></i>
                        </a>
                    </div>
                    <div class="navbar-nav-left d-flex w-100 justify-content-between align-items-center">
                        <!-- Title on the Left -->
                        <div class="d-flex mt-3 gap-1">
                            <p class="fw-light"><?= htmlspecialchars($active_menu_group) ?></p>
                            <p class="fw-light">/</p>
                            <p class="fw-bold"><?= htmlspecialchars($active_menu_name) ?></p>
                        </div>
                        <div class="d-none d-xl-flex mt-3 gap-1 fw-bold">
                            <p class="mx-1 fw-light">
                                <?= htmlspecialchars(isset(session()->get('user_info')['EM_EmplCode']) ? session()->get('user_info')['EM_EmplCode'] : '-') ?>
                            </p>
                            <p class="mx-1 fw-light">-</p>
                            <p class="mx-1 fw-light">
                                <?= htmlspecialchars(isset(session()->get('user_info')['sec_sectionnaming']) ? session()->get('user_info')['sec_sectionnaming'] : '-') ?>
                            </p>
                            <p class="mx-1 fw-light">/</p>
                            <p class="mx-1 fw-light">
                                <?= htmlspecialchars(isset(session()->get('user_info')['sec_teamnaming']) ? session()->get('user_info')['sec_teamnaming'] : '-') ?>
                            </p>
                        </div>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Style Switcher -->
                            <li class="nav-item me-2 me-xl-0">
                                <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
                                    <i class="ti ti-md"></i>
                                </a>
                            </li>
                            <!--/ Style Switcher -->
                            <li class="d-none d-xl-block">
                                <p class="mt-3  d-block"><?= htmlspecialchars(isset(session()->get('user_info')['EM_EmplName']) ? session()->get('user_info')['EM_EmplName'] : '') ?></p>
                            </li>
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item btnSignOUt" href="#">
                                            <i class="ti ti-logout me-2 ti-sm"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>

                    <!-- Search Small Screens -->
                    <div class="navbar-search-wrapper search-input-wrapper d-none">
                        <input type="text" class="form-control search-input container-fluid border-0" placeholder="Search..." aria-label="Search...">
                        <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <div id="wait_screen" class="loader-container" style="display: none;">
                            <div class="loader">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>
                        <?= $this->renderSection('content') ?>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
<!--                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-fluid">
                            <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
                                <div>
                                     Copyright &copy; System Dept. JST Indonesia <?= date('Y') ?> 
                                    Created with <i class="fa fa-heart" style="color: #F43F5E"></i> by jinsystem <?= date('Y') ?>
                                </div>
                            </div>
                        </div>
                    </footer>-->
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

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
    <script src="<?= base_url() ?>assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/swiper/swiper.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/select2/select2.js"></script>
    <script src="<?= base_url() ?>assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
    <script src="<?= base_url('/assets/vendor/sweetalert2/sweetalert2.all.min.js') ?>"></script>

    <!-- Main JS -->
    <script src="<?= base_url() ?>assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="<?= base_url() ?>assets/js/dashboards-analytics.js"></script>

    <script>
        $(document).ready(function() {
            
        let lastScrollY = window.scrollY;
        const navbar = document.getElementById('layout-navbar');

        window.addEventListener('scroll', () => {
            if (window.scrollY > lastScrollY) {
                // Scroll down
                navbar.classList.add('hide-navbar');
            } else {
                // Scroll up
                navbar.classList.remove('hide-navbar');
            }
            lastScrollY = window.scrollY;
        });
    
    
            // Handle Deactivate button click
            $(document).on("click", ".btnSignOUt", function() {
                event.preventDefault(); // Prevent the default action

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to log out?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, log out!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to the logout URL
                        window.location.href = "<?= base_url('/') ?>";
                    }
                });
            });
        });
    </script>
</body>

</html>
