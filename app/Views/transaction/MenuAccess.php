<!DOCTYPE html>
<html lang="en">
<?= $this->extend("main/template") ?>
<?= $this->section("content") ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Access Management</title>
    <link rel="shortcut icon" href="<?= base_url('/assets/jstlogo.ico') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('/assets/login_form/fonts/icomoon/style.css') ?>">

    <link rel="stylesheet" href="<?= base_url('/assets/vendors/bootstrap-icons/font/bootstrap-icons.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/vendors/sweetalert2/sweetalert2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/vendors/selectize/selectize.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/vendors/selectize/selectize.bootstrap5.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/css/style.css') ?>">
</head>

<body>
    
    <div class="d-flex flex-column gap-2 align-items-center justify-content-center">
        <form id="f_addaccess" class="card p-5">
            <div class="my-2">
                <label for="ums_userid" class="form-label required m-0">User</label>
                <div class="d-flex gap-1">
                    <select name="ums_userid" id="ums_userid" class="form-select" selectize
                        data-placeholder="Select user" required>
                        <option value="" selected disabled></option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user->value ?>"><?= $user->text ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" id="btn_adduser" class="btn btn-light" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="New user">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                    <button type="button" id="btn_edituser" class="btn btn-light d-none" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="Edit user">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                </div>
                <small id="err_ums_userid" class="input-warning text-danger"></small>
            </div>
            <div class="my-2">
                <label for="app" class="form-label required m-0">Application Name</label>
                <div class="d-flex gap-1">
                    <select id="app" class="form-select" selectize data-placeholder="Select application" required>
                        <option value="" selected disabled></option>
                        <?php foreach ($apps as $app): ?>
                            <option value="<?= $app->value ?>"><?= $app->text ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" id="btn_addapp" class="btn btn-light" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="New application">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                    <button type="button" id="btn_editapp" class="btn btn-light d-none" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="Edit application">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                </div>
                <small id="err_app" class="input-warning text-danger"></small>
            </div>
            <div class="my-2">
                <label for="group_code" class="form-label required m-0">Group Name</label>
                <div class="d-flex gap-1">
                    <select id="group_code" class="form-select" selectize data-placeholder="Select group" required>
                    </select>
                    <button type="button" id="btn_addgroup" class="btn btn-light" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="New group">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                    <button type="button" id="btn_editgroup" class="btn btn-light d-none" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="Edit group">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                </div>
                <small id="err_group_code" class="input-warning text-danger"></small>
            </div>
            <div class="my-2">
                <label for="ums_menuid" class="form-label required m-0">Menu Name</label>
                <div class="d-flex gap-1">
                    <select name="ums_menuid" id="ums_menuid" class="form-select" selectize
                        data-placeholder="Select menu" required>
                    </select>
                    <button type="button" id="btn_addmenu" class="btn btn-light" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="New menu">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                    <button type="button" id="btn_editmenu" class="btn btn-light d-none" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="Edit menu">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                </div>
                <small id="err_ums_menuid" class="input-warning text-danger"></small>
            </div>
            <div class="my-2">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label m-0">Access</label>
                    <button type="button"
                        style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .3rem; --bs-btn-font-size: .75rem;"
                        id="btn_checkall" class="btn btn-secondary ms-auto">Check all</button>
                    <button type="button"
                        style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .3rem; --bs-btn-font-size: .75rem;"
                        id="btn_uncheckall" class="btn btn-secondary">Uncheck all</button>
                </div>
                <div id="privilege" class="row m-0 mt-2">
                    <div class="form-check col-6 col-sm-4">
                        <input class="form-check-input mt-0" type="checkbox" value="" name="ums_add" id="ums_add">
                        <label class="form-check-label ms-1" for="ums_add">Add</label>
                    </div>
                    <div class="form-check col-6 col-sm-4">
                        <input class="form-check-input mt-0" type="checkbox" value="" name="ums_edit" id="ums_edit">
                        <label class="form-check-label ms-1" for="ums_edit">Edit</label>
                    </div>
                    <div class="form-check col-6 col-sm-4">
                        <input class="form-check-input mt-0" type="checkbox" value="" name="ums_delete" id="ums_delete">
                        <label class="form-check-label ms-1" for="ums_delete">Delete</label>
                    </div>
                    <div class="form-check col-6 col-sm-4">
                        <input class="form-check-input mt-0" type="checkbox" value="" name="ums_generate"
                            id="ums_generate">
                        <label class="form-check-label ms-1" for="ums_generate">Generate</label>
                    </div>
                    <div class="form-check col-6 col-sm-4">
                        <input class="form-check-input mt-0" type="checkbox" value="" name="ums_post" id="ums_post">
                        <label class="form-check-label ms-1" for="ums_post">Post</label>
                    </div>
                    <div class="form-check col-6 col-sm-4">
                        <input class="form-check-input mt-0" type="checkbox" value="" name="ums_print" id="ums_print">
                        <label class="form-check-label ms-1" for="ums_print">Print</label>
                    </div>
                </div>
            </div>
            <div class="text-center">
                               <button type="submit" class="btn btn-success d-grid w-100 submit">
                                        <span class="indicator-label">Save</span>
                                        <span class="loading" style="display: none">Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                            </div>
        </form>
        


    </div>
    

    <div class="modal fade" id="m_user" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="f_user">
                        <input type="hidden" name="appcode" id="appcode">
                        <div class="m-2">
                            <label for="ua_emplcode" class="form-label required">Employee</label>
                            <select id="ua_emplcode" class="form-select" data-placeholder="Select employee" required>
                            </select>
                            <!-- <input type="text" name="ua_emplcode" id="ua_emplcode" placeholder="Employee"
                                class="form-control form-control-solid" required> -->
                            <small id="err_ua_emplcode" class="input-warning text-danger"></small>
                        </div>
                        <div class="m-2">
                            <label for="ua_username" class="form-label required">Username</label>
                            <input type="text" name="ua_username" id="ua_username" placeholder="Username"
                                class="form-control form-control-solid" required>
                            <small id="err_ua_username" class="input-warning text-danger"></small>
                        </div>
                        <div class="m-2 form-check gap-2">
                            <input type="checkbox" name="ua_isactive" id="ua_isactive" class="form-check-input">
                            <label class="form-check-label" for="ua_isactive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="save_user" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="m_app" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="f_app">
                        <input type="hidden" name="appcode" id="appcode">
                        <div class="m-2">
                            <label for="appname" class="form-label required">Application Name</label>
                            <input type="text" name="appname" id="appname" placeholder="Application Name"
                                class="form-control form-control-solid" required>
                            <small id="err_appname" class="input-warning text-danger"></small>
                        </div>
                        <div class="m-2">
                            <label for="appdescription" class="form-label required">Application Description</label>
                            <input type="text" name="appdescription" id="appdescription" placeholder="Application Description"
                                class="form-control form-control-solid" required>
                            <small id="err_appdescription" class="input-warning text-danger"></small>
                        </div>
                        <div class="m-2 form-check gap-2">
                            <input type="checkbox" name="appisactive" id="appisactive" class="form-check-input">
                            <label class="form-check-label" for="appisactive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="save_app" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="m_group" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="f_group">
                        <div class="m-2">
                            <input type="hidden" name="groupcode" id="groupcode">
                            <input type="hidden" name="groupappcode" id="groupappcode" required>
                            <label for="add_appname" class="form-label">Application Name</label>
                            <input type="text" id="add_appname" class="form-control form-control-solid" disabled>
                        </div>
                        <div class="m-2">
                            <label for="groupname" class="form-label required">Group Name</label>
                            <input type="text" name="groupname" id="groupname" placeholder="Group Name"
                                class="form-control form-control-solid" required>
                            <small id="err_groupname" class="input-warning text-danger"></small>
                        </div>
                        <div class="m-2">
                            <label for="groupclass" class="form-label required">Group Class</label>
                            <input type="text" name="groupclass" id="groupclass" placeholder="groupclass"
                                class="form-control form-control-solid" required>
                            <small id="err_groupclass" class="input-warning text-danger"></small>
                        </div>
                        <div class="m-2">
                            <label for="groupicon" class="form-label required">Group Icon</label>
                            <input type="text" name="groupicon" id="groupicon" placeholder="fa fa-icon"
                                class="form-control form-control-solid" required>
                            <small id="err_groupicon" class="input-warning text-danger"></small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="save_group" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="m_menu" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="f_menu">
                        <div class="m-2">
                            <input type="hidden" name="mn_menucode" id="mn_menucode">
                            <input type="hidden" name="mn_groupcode" id="mn_groupcode" required>
                            <label for="add_groupname" class="form-label">Group Name</label>
                            <input type="text" id="add_groupname" class="form-control form-control-solid" disabled>
                        </div>
                        <div class="m-2">
                            <label for="mn_menuname" class="form-label required">Menu Name</label>
                            <input type="text" name="mn_menuname" id="mn_menuname" placeholder="Menu Name"
                                class="form-control form-control-solid" required>
                            <small id="err_mn_menuname" class="input-warning text-danger"></small>
                        </div>
                        <div class="m-2">
                            <label for="mn_path" class="form-label required">Menu Path</label>
                            <input type="text" name="mn_path" id="mn_path" placeholder="group/menupath"
                                class="form-control form-control-solid" required>
                            <small id="err_mn_path" class="input-warning text-danger"></small>
                        </div>
                        <div class="m-2">
                            <label for="mn_shortpath" class="form-label">Menu Short Path</label>
                            <input type="text" name="mn_shortpath" id="mn_shortpath" placeholder="menupath"
                                class="form-control form-control-solid">
                            <small id="err_mn_shortpath" class="input-warning text-danger"></small>
                        </div>
                        <div class="m-2 form-check gap-2">
                            <input type="checkbox" name="mn_isactive" id="mn_isactive" class="form-check-input">
                            <label class="form-check-label" for="mn_isactive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="save_menu" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loader">
        <div class="load-content">
            <div class="load-animation">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            <div id="load-text">
                Loading...
            </div>
        </div>
    </div>

    <script>
        let base_url = "<?= base_url() ?>";
    </script>
 <?= $this->endSection() ?>
</body>

</html>
