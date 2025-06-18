const Toast = Swal.mixin({
    toast: true,
    position: "top",
    showConfirmButton: false,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
});

$(document).ready(function () {
    init_tooltip();

    $("[selectize]").each(function () {
        $(this).selectize({
            create: false,
            sortField: "text",
        });
    });

    $(document).ready(function () {
        $("#logout").click(function (e) {
            e.preventDefault();
    
            Swal.fire({
                text: "Are you sure you want to sign out?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, sign me out!",
                cancelButtonText: "Cancel",
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary",
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url('logout') ?>", // Sesuaikan dengan route logout
                        type: "GET",
                        dataType: "json",
                        success: function (response) {
                            if (response.status === "success") {
                                Swal.fire({
                                    icon: "success",
                                    title: "Signed out successfully",
                                    showConfirmButton: false,
                                    timer: 1500,
                                }).then(() => {
                                    window.location.href = response.redirect; // Gunakan redirect dari server
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Logout Failed",
                                    text: response.message || "Something went wrong!",
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: "error",
                                title: "Logout Failed",
                                text: "Something went wrong!",
                            });
                        }
                    });
                }
            });
        });
    });
    
    
    

    $("#app").on("change", function () {
        refresh_group();
    });

    $("#group_code").on("change", function () {
        refresh_menu();
    });

    $("#ums_userid, #ums_menuid").on("change", function () {
        let user = $("#ums_userid").val();
        let menu = $("#ums_menuid").val();
        $("#privilege").find(".form-check-input").prop("checked", false);

        if (!user || !menu) return false;
        wait_screen(true);
        $.ajax({
            type: "POST",
            url: base_url + "C_MenuAccess/get_useraccess",
            data: {
                user: user,
                menu: menu,
            },
        })
            .done(function (response) {
                if (response) {
                    $("#ums_add").prop("checked", response.ums_add == 1);
                    $("#ums_edit").prop("checked", response.ums_edit == 1);
                    $("#ums_delete").prop("checked", response.ums_delete == 1);
                    $("#ums_generate").prop(
                        "checked",
                        response.ums_generate == 1
                    );
                    $("#ums_post").prop("checked", response.ums_post == 1);
                    $("#ums_print").prop("checked", response.ums_print == 1);
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                let message = JSON.parse(jqXHR.responseText);
                swal_error(message.message);
            })
            .always(function () {
                wait_screen(false);
            });
    });

    $("#ums_userid, #app, #group_code, #ums_menuid").on("change", function () {
        let changed_input = $(this).attr("id");
        let val = $(this).val();

        const btn_map = {
            ums_userid: {
                add: "#btn_adduser",
                edit: "#btn_edituser",
            },
            app: {
                add: "#btn_addapp",
                edit: "#btn_editapp",
            },
            group_code: {
                add: "#btn_addgroup",
                edit: "#btn_editgroup",
            },
            ums_menuid: {
                add: "#btn_addmenu",
                edit: "#btn_editmenu",
            },
        };

        if (btn_map[changed_input]) {
            if (val) {
                $(btn_map[changed_input].add).addClass("d-none");
                $(btn_map[changed_input].edit).removeClass("d-none");
            } else {
                $(btn_map[changed_input].add).removeClass("d-none");
                $(btn_map[changed_input].edit).addClass("d-none");
            }
        }
    });

    $("#btn_checkall").on("click", function () {
        // Select all checkboxes within the form
        $("#privilege").find(".form-check-input").prop("checked", true);
    });
    $("#btn_uncheckall").on("click", function () {
        // Select all checkboxes within the form
        $("#privilege").find(".form-check-input").prop("checked", false);
    });

    $("#btn_saveaccess").on("click", function () {
        $(".input-warning").text("");
        let checkFields = true;
        $("#f_addaccess")
            .find("input[required], select[required], textarea[required]")
            .each(function () {
                if (!$(this).val()) {
                    checkFields = false;
                    let id = $(this).attr("id");
                    $("#err_" + id).text("This field is required.");
                    $("#" + id).focus();
                    return false;
                }
            });
        if (!checkFields) return false;

        wait_screen(true, "Saving...");
        $.ajax({
            type: "POST",
            url: base_url + "C_MenuAccess/save_access",
            data: $("#f_addaccess").serialize(),
        })
            .done(function (response) {
                if (response.status == "success") {
                    Toast.fire({
                        icon: "success",
                        text: "Data saved!",
                        timer: 1500,
                    });
                } else {
                    swal_error(response.message);
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                let message = JSON.parse(jqXHR.responseText);
                swal_error(message.message);
            })
            .always(function () {
                wait_screen(false);
            });
    });

    // user data event
    $("#btn_adduser").on("click", function () {
        $("#m_user").data("action", "add");
        $("#m_user").modal("show");
    });
    $("#btn_edituser").on("click", function () {
        let user_id = $("#ums_userid").val();
        if (!user_id) {
            swal_warning("Select User first.");
            return false;
        }

        $("#m_user").data("action", "edit");
        $("#m_user").modal("show");
    });
    $("#m_user").on("shown.bs.modal", function (event) {
        $("#ua_emplcode").selectize({
            valueField: "em_emplcode",
            labelField: "em_emplname",
            searchField: [
                "em_emplcode",
                "em_emplname",
                "sec_department",
                "sec_section",
                "sec_team",
            ],
            create: false,
            load: function (query, callback) {
                if (!query.length) return callback();

                $.ajax({
                    type: "GET",
                    url: base_url + "C_MenuAccess/get_employee",
                    data: { q: query },
                    success: function (response) {
                        callback(response);
                    },
                    error: function (xhr, status, error) {
                        callback();
                    },
                });
            },
            render: {
                option: function (data, escape) {
                    // Custom HTML for each option
                    return `
                       <div class="p-2 d-flex flex-column">
                           <strong>${data.em_emplname}</strong>
                           <small>Empl Code : ${data.em_emplcode}</small>
                           <small>Department : ${data.sec_department}</small>
                           <small>Section/Team : ${data.sec_section}/${data.sec_team}</small>
                       </div>
                       `;
                },
            },
        });
    });
    $("#m_user").on("show.bs.modal", function (event) {
        let action = $(this).data("action");

        if (action === "add") {
            $("#m_user").find(".modal-title").text("Add User");
        } else {
            $("#m_user").find(".modal-title").text("Edit User");
            // edit record
            wait_screen(true);
            $.ajax({
                type: "GET",
                url: base_url + "C_MenuAccess/get_user",
                data: { id: $("#ums_userid").val() },
            })
                .done(function (response) {
                    $.each(response, function (key, value) {
                        if ($("#" + key).length) {
                            if (key === "ua_isactive") {
                                $("#" + key).prop("checked", Number(value));
                            } else {
                                $("#" + key).val(value);
                            }
                        }
                    });
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    let message = JSON.parse(jqXHR.responseText);
                    swal_error(message.message);
                })
                .always(function () {
                    wait_screen(false);
                });
        }
    });
    $("#m_user").on("hide.bs.modal", function (event) {
        // reset form group
        $("#f_user")
            .find("input")
            .each(function () {
                if (this.type === "checkbox" || this.type === "radio") {
                    this.checked = false;
                } else {
                    $(this).val("");
                }
            });
    });

    $("#save_user").on("click", function () {
        $(".input-warning").text("");
        let checkFields = true;
        $("#f_user")
            .find("input[required], select[required], textarea[required]")
            .each(function () {
                if (!$(this).val()) {
                    checkFields = false;
                    let id = $(this).attr("id");
                    $("#err_" + id).text("This field is required.");
                    $("#" + id).focus();
                    return false;
                }
            });
        if (!checkFields) return false;

        wait_screen(true);
        $.ajax({
            type: "POST",
            url: base_url + "C_MenuAccess/save_user",
            data: $("#f_user").serialize(),
        })
            .done(function (response) {
                if (response.status == "success") {
                    Toast.fire({
                        icon: "success",
                        text: "Application saved!",
                        timer: 1500,
                    });
                    $("#m_user").modal("hide");
                    refresh_user();
                } else {
                    swal_error(response.message);
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                let message = JSON.parse(jqXHR.responseText);
                swal_error(message.message);
            })
            .always(function () {
                wait_screen(false);
            });
    });

    // application data event
    $("#btn_addapp").on("click", function () {
        $("#m_app").data("action", "add");
        $("#m_app").modal("show");
    });
    $("#btn_editapp").on("click", function () {
        let app_id = $("#app").val();
        if (!app_id) {
            swal_warning("Select Application first.");
            return false;
        }

        $("#m_app").data("action", "edit");
        $("#m_app").modal("show");
    });
    $("#m_app").on("show.bs.modal", function (event) {
        let action = $(this).data("action");

        if (action === "add") {
            $("#m_app").find(".modal-title").text("Add Application");
        } else {
            $("#m_app").find(".modal-title").text("Edit Application");
            // edit record
            wait_screen(true);
            $.ajax({
                type: "GET",
                url: base_url + "C_MenuAccess/get_app",
                data: { id: $("#app").val() },
            })
                .done(function (response) {
                    $.each(response, function (key, value) {
                        if ($("#" + key).length) {
                            if (key === "appisactive") {
                                $("#" + key).prop("checked", Number(value));
                            } else {
                                $("#" + key).val(value);
                            }
                        }
                    });
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    let message = JSON.parse(jqXHR.responseText);
                    swal_error(message.message);
                })
                .always(function () {
                    wait_screen(false);
                });
        }
    });
    $("#m_app").on("hide.bs.modal", function (event) {
        // reset form group
        $("#f_app")
            .find("input")
            .each(function () {
                if (this.type === "checkbox" || this.type === "radio") {
                    this.checked = false;
                } else {
                    $(this).val("");
                }
            });
    });

    $("#save_app").on("click", function () {
        $(".input-warning").text("");
        let checkFields = true;
        $("#f_app")
            .find("input[required], select[required], textarea[required]")
            .each(function () {
                if (!$(this).val()) {
                    checkFields = false;
                    let id = $(this).attr("id");
                    $("#err_" + id).text("This field is required.");
                    $("#" + id).focus();
                    return false;
                }
            });
        if (!checkFields) return false;

        wait_screen(true);
        $.ajax({
            type: "POST",
            url: base_url + "C_MenuAccess/save_app",
            data: $("#f_app").serialize(),
        })
            .done(function (response) {
                if (response.status == "success") {
                    Toast.fire({
                        icon: "success",
                        text: "Application saved!",
                        timer: 1500,
                    });
                    $("#m_app").modal("hide");
                    refresh_app();
                } else {
                    swal_error(response.message);
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                let message = JSON.parse(jqXHR.responseText);
                swal_error(message.message);
            })
            .always(function () {
                wait_screen(false);
            });
    });

    // group data event
    $("#btn_addgroup").on("click", function () {
        let app_id = $("#app").val();
        if (!app_id) {
            swal_warning("Select Application first.");
            return false;
        }

        $("#m_group").data("action", "add");
        $("#m_group").modal("show");
    });
    $("#btn_editgroup").on("click", function () {
        let app_id = $("#app").val();
        if (!app_id) {
            swal_warning("Select Application first.");
            return false;
        }
        let group_id = $("#group_code").val();
        if (!group_id) {
            swal_warning("Select Group Menu first.");
            return false;
        }

        $("#m_group").data("action", "edit");
        $("#m_group").modal("show");
    });
    $("#m_group").on("show.bs.modal", function (event) {
        let action = $(this).data("action");

        if (action === "add") {
            $("#m_group").find(".modal-title").text("Add Group");
            // add new record
            let selectizeInstance = $("#app")[0].selectize;
            let selectedText = selectizeInstance
                .getItem(selectizeInstance.getValue())
                .text();
            $("#groupappcode").val($("#app").val());
            $("#add_appname").val(selectedText);
        } else {
            $("#m_group").find(".modal-title").text("Edit Group");
            // edit record
            wait_screen(true);
            $.ajax({
                type: "GET",
                url: base_url + "C_MenuAccess/get_group",
                data: { id: $("#group_code").val() },
            })
                .done(function (response) {
                    $.each(response, function (key, value) {
                        if ($("#" + key).length) {
                            $("#" + key).val(value);
                        }
                    });
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    let message = JSON.parse(jqXHR.responseText);
                    swal_error(message.message);
                })
                .always(function () {
                    wait_screen(false);
                });
        }
    });
    $("#m_group").on("hide.bs.modal", function (event) {
        // reset form group
        $("#f_group")
            .find("input")
            .each(function () {
                if (this.type === "checkbox" || this.type === "radio") {
                    this.checked = false;
                } else {
                    $(this).val("");
                }
            });
    });

    $("#save_group").on("click", function () {
        $(".input-warning").text("");
        let checkFields = true;
        $("#f_group")
            .find("input[required], select[required], textarea[required]")
            .each(function () {
                if (!$(this).val()) {
                    checkFields = false;
                    let id = $(this).attr("id");
                    $("#err_" + id).text("This field is required.");
                    $("#" + id).focus();
                    return false;
                }
            });
        if (!checkFields) return false;

        wait_screen(true);
        $.ajax({
            type: "POST",
            url: base_url + "C_MenuAccess/save_group",
            data: $("#f_group").serialize(),
        })
            .done(function (response) {
                if (response.status == "success") {
                    Toast.fire({
                        icon: "success",
                        text: "Group saved!",
                        timer: 1500,
                    });
                    $("#m_group").modal("hide");
                    refresh_group();
                } else {
                    swal_error(response.message);
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                let message = JSON.parse(jqXHR.responseText);
                swal_error(message.message);
            })
            .always(function () {
                wait_screen(false);
            });
    });

    // menu data event
    $("#btn_addmenu").on("click", function () {
        let group_id = $("#group_code").val();
        if (!group_id) {
            swal_warning("Select Group Menu first.");
            return false;
        }

        $("#m_menu").data("action", "add");
        $("#m_menu").modal("show");
    });
    $("#btn_editmenu").on("click", function () {
        let group_id = $("#group_code").val();
        if (!group_id) {
            swal_warning("Select Group Menu first.");
            return false;
        }
        let menu_id = $("#ums_menuid").val();
        if (!menu_id) {
            swal_warning("Select Menu first.");
            return false;
        }

        $("#m_menu").data("action", "edit");
        $("#m_menu").modal("show");
    });
    $("#m_menu").on("show.bs.modal", function (event) {
        let action = $(this).data("action");

        if (action === "add") {
            $("#m_menu").find(".modal-title").text("Add Menu");
            // add new record
            let selectizeInstance = $("#group_code")[0].selectize;
            let selectedText = selectizeInstance
                .getItem(selectizeInstance.getValue())
                .text();
            $("#mn_groupcode").val($("#group_code").val());
            $("#add_groupname").val(selectedText);
            $("#mn_isactive").prop("checked", true);
        } else {
            $("#m_menu").find(".modal-title").text("Edit Menu");
            // edit record
            wait_screen(true);
            $.ajax({
                type: "GET",
                url: base_url + "C_MenuAccess/get_menu",
                data: { id: $("#ums_menuid").val() },
            })
                .done(function (response) {
                    $.each(response, function (key, value) {
                        if ($("#" + key).length) {
                            if (key === "mn_isactive") {
                                $("#" + key).prop("checked", Number(value));
                            } else {
                                $("#" + key).val(value);
                            }
                        }
                    });
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    let message = JSON.parse(jqXHR.responseText);
                    swal_error(message.message);
                })
                .always(function () {
                    wait_screen(false);
                });
        }
    });
    $("#m_menu").on("hide.bs.modal", function (event) {
        // reset form menu
        $("#f_menu")
            .find("input")
            .each(function () {
                if (this.type === "checkbox" || this.type === "radio") {
                    this.checked = false;
                } else {
                    $(this).val("");
                }
            });
    });

    $("#save_menu").on("click", function () {
        $(".input-warning").text("");
        let checkFields = true;
        $("#f_menu")
            .find("input[required], select[required], textarea[required]")
            .each(function () {
                if (!$(this).val()) {
                    checkFields = false;
                    let id = $(this).attr("id");
                    $("#err_" + id).text("This field is required.");
                    $("#" + id).focus();
                    return false;
                }
            });
        if (!checkFields) return false;

        wait_screen(true);
        $.ajax({
            type: "POST",
            url: base_url + "C_MenuAccess/save_menu",
            data: $("#f_menu").serialize(),
        })
            .done(function (response) {
                if (response.status == "success") {
                    Toast.fire({
                        icon: "success",
                        text: "Menu saved!",
                        timer: 1500,
                    });
                    $("#m_menu").modal("hide");
                    refresh_menu();
                } else {
                    swal_error(response.message);
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                let message = JSON.parse(jqXHR.responseText);
                swal_error(message.message);
            })
            .always(function () {
                wait_screen(false);
            });
    });
});

function wait_screen(opt, text = "Loading...") {
    $("#load-text").text(text);
    if (opt) $("#loader").show();
    else $("#loader").hide();
}

function swal_error(text) {
    Swal.fire({
        icon: "error",
        title: "Error!",
        html: text,
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-primary",
        },
    });
}

function swal_warning(text) {
    Swal.fire({
        icon: "warning",
        title: "Warning!",
        html: text,
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-primary",
        },
    });
}

function init_tooltip() {
    const tooltipTriggerList = document.querySelectorAll(
        '[data-bs-toggle="tooltip"]'
    );
    const tooltipList = [...tooltipTriggerList].map(
        (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
    );
}

function refresh_user() {
    $.ajax({
        url: base_url + "get_users",
        method: "GET",
        dataType: "json",
        success: function (data) {
            let selectize = $("#ums_userid")[0].selectize;
            if (selectize) {
                selectize.clear();
                selectize.clearOptions();
                if (Array.isArray(data)) {
                    selectize.addOption(data);
                    selectize.refreshOptions();
                } else {
                    swal_error("Expected an array of options.");
                }
            } else {
                swal_error("Selectize is not initialized.");
            }
        },
        error: function (xhr, status, error) {
            swal_error("AJAX Error:<br>" + status + "<br>" + error);
        },
    });
}

function refresh_app() {
    $.ajax({
        url: base_url + "get_apps",
        method: "GET",
        dataType: "json",
        success: function (data) {
            let selectize = $("#app")[0].selectize;
            if (selectize) {
                selectize.clear();
                selectize.clearOptions();
                if (Array.isArray(data)) {
                    selectize.addOption(data);
                    selectize.refreshOptions();
                    let selectize_group = $("#group_code")[0].selectize;
                    selectize_group.clear();
                    selectize_group.clearOptions();
                    let selectize_menu = $("#ums_menuid")[0].selectize;
                    selectize_menu.clear();
                    selectize_menu.clearOptions();
                } else {
                    swal_error("Expected an array of options.");
                }
            } else {
                swal_error("Selectize is not initialized.");
            }
        },
        error: function (xhr, status, error) {
            swal_error("AJAX Error:<br>" + status + "<br>" + error);
        },
    });
}

function refresh_group() {
    if (!$("#app").val()) return false;

    $.ajax({
        url: base_url + "get_groupnames",
        method: "GET",
        dataType: "json",
        data: {
            app: $("#app").val(),
        },
        success: function (data) {
            let selectize = $("#group_code")[0].selectize;
            if (selectize) {
                selectize.clear();
                selectize.clearOptions();
                if (Array.isArray(data)) {
                    selectize.addOption(data);
                    selectize.refreshOptions();
                    let selectize_menu = $("#ums_menuid")[0].selectize;
                    selectize_menu.clear();
                    selectize_menu.clearOptions();
                } else {
                    swal_error("Expected an array of options.");
                }
            } else {
                swal_error("Selectize is not initialized.");
            }
        },
        error: function (xhr, status, error) {
            swal_error("AJAX Error:<br>" + status + "<br>" + error);
        },
    });
}

function refresh_menu() {
    if (!$("#group_code").val()) return false;

    $.ajax({
        url: base_url + "get_menus",
        method: "GET",
        dataType: "json",
        data: {
            group: $("#group_code").val(),
        },
        success: function (data) {
            let selectize = $("#ums_menuid")[0].selectize;
            if (selectize) {
                selectize.clear();
                selectize.clearOptions();
                if (Array.isArray(data)) {
                    selectize.addOption(data);
                    selectize.refreshOptions();
                } else {
                    swal_error("Expected an array of options.");
                }
            } else {
                swal_error("Selectize is not initialized.");
            }
        },
        error: function (xhr, status, error) {
            swal_error("AJAX Error:<br>" + status + "<br>" + error);
        },
    });
}
