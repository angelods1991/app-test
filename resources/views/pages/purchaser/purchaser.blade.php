@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{asset('css/family-tree.css')}}">
    <link rel="stylesheet" href="{{asset('css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/select2-bootstrap.min.css')}}">
@endpush
@section('content')
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <!-- Search -->
                <div class="col-md-12">
                    <form action="#" id="frm_search">
                        <div class="form-inline">
                            <div class="form-group">
                                <input type="text" name="search" class="form-control" placeholder="Search">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-inline pt-3">
                            <div class="form-group">
                                <label class="form-control-static pr-3">Search Type:</label>
                            </div>
                            <div class="form-group">
                                <label class="radio-inline form-control-static pr-3">
                                    <input type="radio" name="col_filter" value="1" checked> ID No.
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="radio-inline form-control-static pr-3">
                                    <input type="radio" name="col_filter" value="2"> Name
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="radio-inline form-control-static pr-3">
                                    <input type="radio" name="col_filter" value="3"> Email Address
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="radio-inline form-control-static pr-3">
                                    <input type="radio" name="col_filter" value="4"> EDA Number
                                </label>
                            </div>
                        </div>
                        <div class="form-inline">
                            <div class="form-group">
                                <button type="button" id="btn_advance_search" style="text-decoration: none !important;" class="btn btn-default btn-link pl-0">Advance Search
                                    <i class="fas fa-angle-down"></i></button>
                            </div>
                        </div>
                        <div class="well well-sm mt-3" id="advance_search" data-toggle="false" style="display: none">
                            <div class="form-inline">
                                <div class="form-group">
                                    <label style="display: block">Status:</label>
                                    <select name="status" class="form-control">
                                        <option value="all">All</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label style="display: block">Type:</label>
                                    <select name="type" class="form-control">
                                        <option value="all">All</option>
                                        <option value="1">Existing Member</option>
                                        <option value="0">Public Member</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label style="display: block">Country:</label>
                                    <select name="country" class="form-control">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label style="display: block">Package Status:</label>
                                    <select name="package" class="form-control">
                                        <option value="all">All</option>
                                        <option value="NP">No Package</option>
                                        <option value="P">Pending</option>
                                        <option value="PA">Partially Approved</option>
                                        <option value="FP">Fully Approved</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label style="display: block">&nbsp;</label>
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="form-group">
                                    <label style="display: block">&nbsp;</label>
                                    <button id="btn_advance_search_reset" type="button" class="btn btn-link" style="text-decoration: none !important; ">
                                        <i class="fas fa-eraser"></i> Reset Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-inline pt-3">
                            <button class="btn btn-default" data-toggle="tooltip" title="Download CSV" id="btn_download_csv">
                                <i class="fas fa-file-download"></i>
                            </button>
                            @if(Session::get($active_module.'create'))
                                <button id="btn_create" type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#mdl_create">
                                    <i class="fas fa-plus"></i> Create Purchaser
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="dt_purchaser" class="table table-bordered table-hover" style="width: 100%">
                            <thead>
                            <tr class="bg-primary">
                                <th>ID No.</th>
                                <th>Country Code</th>
                                <th>Name</th>
                                <th>Email Address</th>
                                <th>EDA Number</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-center" colspan="8">Please Wait...</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('modals.purchaser.purchaser-modals')
    @include('modals.purchaser.wallets-modals')
    @include('modals.purchaser.family-modals')
    @include('modals.purchaser.package-modals')
@endsection
@push('scripts')
    <script src="{{asset('js/select2.min.js')}}"></script>
    <script src="{{asset('js/jasny.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var id = 0, package_id = 0, search = "", col_filter = 1, status = "all", type = "all", country = "all",
                package_status = "all",
                original_data,
                _selected_referral = '', package_token_total = 0,
                token = $("meta[name='csrf-token']").attr("content"), url = "{{url('') . '/pages/'}}",
                url_public = "{{url('')}}";

            $("body").tooltip({selector: "[data-toggle='tooltip']", container: "body", trigger: "hover"});

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                beforeSend: function () {
                    $("button[type='submit']").attr({disabled: true});
                    $(".table button").attr({disabled: true}).tooltip('hide');
                    $('.alert_message').html("");
                },
                complete: function () {
                    $("button[type='submit']").attr({disabled: false});
                    //$('.modal').animate({scrollTop: 0}, "fast");
                    $(".table button").attr({disabled: false});
                },
                error: function (respond) {
                    if (respond.status == 401 || respond.status == 419) {
                        $('.modal').modal('hide');
                        $("#mdl_session_timeout").modal("show");
                        setTimeout(function () {
                            location.reload(true);
                        }, 10000);
                    } else if (respond.getAllResponseHeaders()) {
                        alert("Something Went Wrong");
                        if (respond.responseJSON.message) {
                            alert("Error Message: \n" + respond.responseJSON.message);
                        }
                    }
                }
            });

            $("form").submit(function (e) {
                e.preventDefault();
            });

            $("#frm_search input[name='col_filter']").on("change", function () {
                col_filter = $(this).val();
            });

            $("#frm_search").on("submit", function () {
                status = $("#frm_search select[name='status']").val();
                type = $("#frm_search select[name='type']").val();
                search = $("#frm_search input[name='search']").val();
                country = $("#frm_search select[name='country']").val();
                package_status = $("#frm_search select[name='package']").val();
                dt_purchaser.ajax.reload();
            });

            $("#btn_advance_search").on("click", function () {
                advance_search_toggle();
            });

            $("#btn_advance_search_reset").on("click", function () {
                advance_search_reset();
            });

            function confirm_dialog_open(dialog_id) {
                clear_messages();

                $(dialog_id).slideUp("fast");
                $(dialog_id + "_confirm").slideDown("fast");
            }

            function confirm_dialog_close(dialog_id) {
                clear_messages();

                $(dialog_id + "_confirm").slideUp("fast");
                $(dialog_id).slideDown("fast");
            }

            function cancel_confirm_dialog_open(dialog_id) {
                $(dialog_id).slideUp("fast");
                $(dialog_id + "_cancel_confirm").slideDown("fast");
            }

            function cancel_confirm_dialog_close(dialog_id) {
                $(dialog_id + "_cancel_confirm").slideUp("fast");
                $(dialog_id).slideDown("fast");
            }

            function message_modal_open(modal_id = 0, message = 0) {
                if (modal_id != 0) {
                    $(modal_id).modal("hide");
                }

                if (message != 0) {
                    $("#mdl_message").modal("show");
                    $("#dialog_message").html(message);
                } else {
                    $("#mdl_message").modal("show");
                }

            }

            function clear_messages() {
                $.each($(".alert-message"), function () {
                    $(this).html("");
                });
            }

            function modal_switch(closing_modal, opening_modal) {
                $(closing_modal).modal("hide");
                $(opening_modal).modal("show");
            }

            function get_purchaser_data(id, modal_id = 0, form = true) {
                $.ajax({
                    url: url + "purchaser/" + id,
                    method: "get",
                    success: function (respond) {
                        if (form == true) {
                            if (respond.status == "OK") {

                                original_data = respond.data;

                                $("#frm_edit")[0].reset();

                                $("#frm_view input[name='id']").val(respond.data.id);
                                $("#frm_view input[name='purchaser_name']").val(respond.data.purchaser_name);
                                $("#frm_view input[name='purchaser_eda']").val(respond.data.purchaser_eda);
                                $("#frm_view input[name='purchaser_email']").val(respond.data.purchaser_email);
                                $("#frm_view input[name='purchaser_contact']").val(respond.data.purchaser_contact);
                                $("#frm_view input[name='purchaser_type']").val((respond.data.purchaser_type == 1) ? "Existing Member" : "Public Member");
                                $("#frm_view input[name='purchaser_status']").val((respond.data.purchaser_status == 1) ? "Active" : "Inactive");
                                $("#frm_view input[name='purchaser_membership']").val(respond.data.membership);
                                $("#frm_view input[name='purchaser_country']").val(respond.data.country_name);

                                $(".created_by").html(respond.data.created_name);
                                $(".created_date").html(respond.data.created_date);
                                $(".modified_by").html(((respond.data.modified_name) ? respond.data.modified_name : 'None'));
                                $(".modified_date").html(((respond.data.modified_date) ? respond.data.modified_date : 'None'));

                                if (respond.data.purchaser_id_upline == 0) {
                                    $("#frm_view input[name='referral_name']").parent().hide();

                                    $("#frm_edit .referral-group").hide();
                                    $("#frm_edit input[name='referred']").prop('checked', false).parent().hide();

                                } else {
                                    $("#frm_view input[name='referral_name']").val(respond.data.referral_name).parent().show();
                                    $("#frm_edit .referral-group").show();
                                    $("#frm_edit input[name='referred']").prop('checked', true).parent().hide();

                                    $("#frm_edit input[name='referral_name']").val(respond.data.referral_name);
                                    $("#frm_edit select[name='referral']").val(respond.data.purchaser_id_upline).hide().attr({readonly: true});
                                    _selected_referral = respond.data.purchaser_id_upline;

                                }

                                //$("#frm_edit input[name='id']").val(respond.data.id);
                                //$("#frm_edit select[name='purchaser_country']").val(respond.data.purchaser_country);
                                //$("#frm_edit input[name='purchaser_name']").val(respond.data.purchaser_name);
                                //$("#frm_edit input[name='purchaser_eda']").val(respond.data.purchaser_eda);
                                //$("#frm_edit input[name='purchaser_email']").val(respond.data.purchaser_email);
                                //$("#frm_edit input[name='purchaser_contact']").val(respond.data.purchaser_contact);
                                $("#frm_edit select[name='purchaser_type']").val(respond.data.purchaser_type);
                                //$("#frm_edit select[name='purchaser_status']").val(respond.data.purchaser_status);
                                $("#frm_edit select[name='purchaser_membership']").val(respond.data.bonus_id);

                                $("#frm_edit input[name='id']").val(respond.data.id);
                                $("#frm_edit input[name='purchaser_name']").val(respond.data.purchaser_name);
                                $("#frm_edit input[name='purchaser_eda']").val(respond.data.purchaser_eda);
                                $("#frm_edit input[name='purchaser_email']").val(respond.data.purchaser_email);
                                $("#frm_edit input[name='purchaser_contact']").val(respond.data.purchaser_contact);
                                //$("#frm_edit input[name='purchaser_type']").val((respond.data.purchaser_type == 1) ? "Existing Member" : "Public Member");
                                //$("#frm_edit input[name='purchaser_status']").val((respond.data.purchaser_status == 1) ? "Active" : "Inactive");
                                //$("#frm_edit input[name='purchaser_membership']").val(respond.data.membership);
                                $("#frm_edit input[name='purchaser_country']").val(respond.data.country_name);
                                $("#frm_edit select[name='purchaser_status']").val(respond.data.purchaser_status);

                                if (respond.data.purchaser_image != null) {
                                    $(".purchaser-image").html(
                                        "<img style='border: #ccc 1px solid; width: 100%' src='" + url_public + "/storage/img/purchaser/" + respond.data.purchaser_image + "' class='img-rounded img-responsive'>"
                                    );
                                    $("#frm_edit input[name='purchaser_delete_image']").parent().show();
                                } else {
                                    $(".purchaser-image").html(
                                        "<div class='well well-sm text-center'><strong>No Photo Found</strong></div>"
                                    );
                                    $("#frm_edit input[name='purchaser_delete_image']").parent().hide();
                                }

                            } else {
                                message_modal_open(0, "<div class='alert alert-danger text-danger'><button class='close' data-dismiss='modal'>&times;</button><strong>" + respond.message + "</strong></div>");
                            }
                        }

                        $(".purchaser-name").html(respond.data.purchaser_name);

                        if (modal_id != 0) {
                            $(modal_id).modal("show");
                        }
                    }
                });
            }

            function get_wallet_data(id) {
                $.ajax({
                    url: url + "purchaser-wallet/" + id,
                    method: "get",
                    success: function (respond) {

                        $("#frm_wallet_view input[name='wallet_balance']").val(respond.data.wallet_balance);
                        $("#frm_wallet_view input[name='wallet_lock_balance']").val(format_number(respond.data.wallet_lock_balance));
                        $("#frm_wallet_view input[name='wallet_available_balance']").val(format_number(respond.data.wallet_available_balance));
                        $("#frm_wallet_view input[name='referral_bonus']").val(format_number(respond.data.referral_bonus));

                        // var digit = respond.data.wallet_balance;
                        // digit = digit.split(".");
                        //
                        // $("#frm_wallet_view input[name='wallet_balance']").val(format_number(digit[0]) + "." + digit[1]);
                        //
                        // digit = respond.data.wallet_lock_balance;
                        // digit = digit.split(".");
                        //
                        // $("#frm_wallet_view input[name='wallet_lock_balance']").val(format_number(digit[0]) + "." + digit[1]);
                        //
                        // digit = respond.data.wallet_available_balance;
                        // digit = digit.split(".");
                        //
                        // $("#frm_wallet_view input[name='wallet_available_balance']").val(format_number(digit[0]) + "." + digit[1]);
                        //
                        // digit = respond.data.wallet_lock_balance;
                        // digit = digit.split(".");
                        //
                        // $("#frm_wallet_unlock input[name='wallet_lock_balance']").val(format_number(digit[0]) + "." + digit[1]);
                        //
                        // digit = respond.data.referral_bonus;
                        // digit = digit.split(".");
                        //
                        // $("#frm_wallet_view input[name='referral_bonus']").val(format_number(digit[0]) + "." + digit[1]);
                    }
                });
            }

            function get_packages_data(id) {
                $("#mdl_package_view").modal("show");
                get_purchaser_data(id, 0, false);
                dt_purchaser_packages.ajax.reload();
            }

            function show_referral_group(element) {
                if (element.is(':checked')) {
                    $(".referral-group").slideDown("fast");
                } else {
                    $(".referral-group").slideUp("fast");
                }
            }

            function resend_wallet_code() {
                modal_switch("#mdl_view", "#mdl_message");

                $.ajax({
                    url: url + 'purchaser/email/wallet-code/' + id,
                    method: "post",
                    success: function (respond) {
                        if (respond.status == "OK") {
                            message_modal_open("#mdl_view", "<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal' data-toggle='modal' data-target='#mdl_view'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>");
                        }
                    }
                });
            }

            function get_country_code() {
                $.ajax({
                    url: url + "purchaser/list/country",
                    method: "post",
                    success: function (respond) {
                        var country_list = "<option value=> -- Select Country --</option>";
                        $.each(respond.data, function (key, data) {
                            country_list += "<option value='" + data.country_code + "'>" + data.country_name + "</option>";
                        });

                        $("#frm_create select[name='purchaser_country']").html(country_list);
                        $("#frm_edit select[name='purchaser_country']").html(country_list);

                        country_list = "<option value='all'>All</option>";

                        $.each(respond.data, function (key, data) {
                            country_list += "<option value='" + data.country_code + "'>" + data.country_name + "</option>";
                        });

                        $("#frm_search select[name='country']").html(country_list);
                    }
                })
            }

            function compute_package_token() {
                var token_price = $("#frm_package_create input[name='package_token_price']").val(),
                    paid_amount = $("#frm_package_create input[name='package_paid_amount']").val(),
                    incentive_percentage = $("#frm_package_create input[name='package_incentive_percentage']").val(),
                    values_numeric = true;

                if (isNaN(token_price)) {
                    clean_package_breakdown();
                    values_numeric = false;
                }

                if (isNaN(paid_amount)) {
                    clean_package_breakdown();
                    values_numeric = false;
                }

                if (isNaN(incentive_percentage)) {
                    clean_package_breakdown();
                    values_numeric = false;
                }

                if (token_price <= 0) {
                    clean_package_breakdown();
                    values_numeric = false;
                }

                if (values_numeric) {

                    $("#frm_package_create input[name='package_tokens']").val("Computing...");
                    $("#frm_package_create input[name='package_locked_tokens']").val("Computing...");
                    $("#frm_package_create input[name='package_incentive_tokens']").val("Computing...");

                    $.ajax({
                        url: url + "purchaser-package/compute/token",
                        method: "post",
                        data: {
                            package_token_price: token_price,
                            package_paid_amount: paid_amount,
                            package_incentive_percentage: incentive_percentage
                        },
                        beforeSend: function () {
                            $("button[type='submit']").attr({disabled: true});
                        },
                        complete: function () {
                            $("button[type='submit']").attr({disabled: false});
                        },
                        success: function (respond) {
                            $("#frm_package_create input[name='package_tokens']").val(format_number(respond.data.package_tokens));
                            $("#frm_package_create input[name='package_locked_tokens']").val(format_number(respond.data.package_locked_tokens));
                            $("#frm_package_create input[name='package_incentive_tokens']").val(format_number(respond.data.package_incentive_tokens));
                        }
                    });
                }
            }

            function clean_package_breakdown() {
                $("#frm_package_create input[name='package_tokens]").val("");
                $("#frm_package_create input[name='package_locked_tokens']").val("");
                $("#frm_package_create input[name='package_incentive_tokens']").val("");
            }

            function get_package_verify_data(package_id) {
                $.ajax({
                    url: url + "purchaser-package/" + package_id,
                    method: "get",
                    asycn: false,
                    success: function (respond) {
                        $("#frm_package_verify input[name='package_token_price']").val(respond.data.package_token_price);
                        $("#frm_package_verify input[name='package_paid_amount']").val(respond.data.package_paid_amount);
                        $("#frm_package_verify input[name='package_incentive_percentage']").val(respond.data.package_incentive_percentage);
                        $("#frm_package_verify input[name='package_tokens']").val(format_number(respond.data.package_token_total));
                        $("#frm_package_verify input[name='package_locked_tokens']").val(format_number(respond.data.package_token_locked));
                        $("#frm_package_verify input[name='package_incentive_tokens']").val(format_number(respond.data.package_token_incentive));
                        $("#frm_package_verify input[name='package_method']").val(respond.data.package_method);
                        $("#frm_package_verify textarea[name='package_remarks']").html(respond.data.package_remarks);

                        $("#frm_package_verify input[name='upline_name_1']").val(respond.upline.upline_name_1);
                        $("#frm_package_verify input[name='upline_name_2']").val(respond.upline.upline_name_2);
                        $("#frm_package_verify input[name='upline_name_3']").val(respond.upline.upline_name_3);
                        $("#frm_package_verify input[name='upline_bonus_1']").val(respond.upline.upline_token_1);
                        $("#frm_package_verify input[name='upline_bonus_2']").val(respond.upline.upline_token_2);
                        $("#frm_package_verify input[name='upline_bonus_3']").val(respond.upline.upline_token_3);
                    }
                });
            }

            function format_number(num) {
                var digit = num;

                if (digit.indexOf(".") > -1) {
                    digit = digit.split(".");

                    var
                        whole_number = digit[0],
                        decimal_number = digit[1];

                    return whole_number.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + "." + decimal_number;

                } else {
                    return digit.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + ".00";
                }

                //return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
            }

            function advance_search_toggle() {
                var advance_search_state = $("#advance_search").attr("data-toggle");

                advance_search_reset();

                if (advance_search_state == "false") {
                    $("#advance_search").attr({"data-toggle": true}).slideDown("fast");
                    $("#btn_advance_search i").removeClass("fa-angle-down").addClass("fa-angle-up");
                } else {
                    $("#advance_search").attr({"data-toggle": false}).slideUp("fast");
                    $("#btn_advance_search i").removeClass("fa-angle-up").addClass("fa-angle-down");
                }
            }

            function advance_search_reset() {
                status = type = country = package_status = "all";

                $("#frm_search select[name='status']").val("all");
                $("#frm_search select[name='type']").val("all");
                $("#frm_search select[name='country']").val("all");
                $("#frm_search select[name='package']").val("all");
            }

            get_country_code();

            var dt_purchaser = $("#dt_purchaser").DataTable({
                processing: true,
                serverSide: true,
                pagingType: "input",
                dom: "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-6'i><'col-sm-6'p>>",
                ajax: {
                    url: url + "purchaser/table",
                    method: "post",
                    data: function (d) {
                        d._token = token;
                        d.search = search;
                        d.col_filter = col_filter;
                        d.type = type;
                        d.status = status;
                        d.country = country;
                        d.package = package_status;
                    },
                    error: function (respond) {
                        if (respond.status == 401 || respond.status == 419) {
                            $('.modal').modal('hide');
                            $("#mdl_session_timeout").modal("show");
                            setTimeout(function () {
                                location.reload(true);
                            }, 10000);
                        }
                    }
                },
                columns: [
                    {
                        data: "id",
                        name: "id",
                        className: "dt-body-right"
                    },
                    {
                        data: "purchaser_country",
                        name: "purchaser_country",
                    },
                    {
                        data: "purchaser_name",
                        name: "purchaser_name"
                    },
                    {
                        data: "purchaser_email",
                        name: "purchaser_email"
                    },

                    {
                        data: "purchaser_eda",
                        name: "purchaser_eda"
                    },
                    {
                        data: "purchaser_type",
                        name: "purchaser_type",
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            if (data == "1") {
                                return "Existing Member"
                            } else if (data == "0") {
                                return "Public Member"
                            }
                        }
                    },
                    {
                        data: "purchaser_status",
                        name: "purchaser_status",
                        className: "dt-btn-cell",
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            if (data == "1") {
                                return "<span class='label label-success'>Active</span>"
                            } else if (data == "0") {
                                return "<span class='label label-danger'>Inactive</span>"
                            }
                        }
                    },
                    {
                        data: "id",
                        name: "id",
                        className: "dt-btn-cell",
                        orderable: false,
                        searchable: false,
                        render: function (data, type, pp) {
                            return "<button class='btn btn-view-purchaser btn-default m-1' data-id='" + data + "' data-toggle='tooltip' data-placement='top' title='View Purchaser'><i class='far fa-fw fa-eye'></i></button>" +
                                "<button class='btn btn-view-purchaser-packages btn-default m-1 btn-notif' data-id='" + data + "' data-toggle='tooltip' data-placement='top' title='View Packages'><i class='fas fa-fw fa-layer-group'></i><span class='label label-default' style='font-size: small; position: absolute; top: -15%; right: -15%;display: inline;'>" + pp.pending_packages + "</span></button><br>" +
                                "<button class='btn btn-view-purchaser-wallets btn-default m-1' data-id='" + data + "' data-toggle='tooltip' data-placement='top' title='View Wallet'><i class='fas fa-fw fa-wallet'></i></button>" +
                                "<button class='btn btn-view-tree btn-default m-1' data-id='" + data + "' data-toggle='tooltip' data-placement='top' title='View Family'><i class='fas fa-fw fa-tree'></i></button>";
                        }
                    }
                ]
            });

            var dt_purchaser_packages = $("#dt_purchaser_packages").DataTable({
                processing: true,
                serverSide: true,
                pagingType: "input",
                dom: "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-6'i><'col-sm-6'p>>",
                ajax: {
                    url: url + "purchaser-package/table",
                    method: "post",
                    data: function (d) {
                        d._token = token;
                        d.id = id;
                    },
                    error: function (respond) {
                        if (respond.status == 401 || respond.status == 419) {
                            $('.modal').modal('hide');
                            $("#mdl_session_timeout").modal("show");

                            setTimeout(function () {
                                window.location = "{{route("login")}}";
                            }, 10000);
                        }
                    }
                },
                columns: [
                    {
                        data: "id",
                        name: "id",
                        className: "dt-body-right",
                        searchable: false,
                    },
                    {
                        data: "package_paid_amount",
                        name: "package_paid_amount",
                        className: "dt-body-right",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, '$')
                    },
                    {
                        data: "package_token_price",
                        name: "package_token_price",
                        className: "dt-body-right",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2)
                    },
                    {
                        data: "package_incentive_percentage",
                        name: "package_incentive_percentage",
                        className: "dt-body-right",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, "", "%")
                    },
                    {
                        data: "package_token_total",
                        name: "package_token_total",
                        className: "dt-body-right",
                        searchable: false,
                        render: function (data) {
                            return format_number(data)
                        }
                    },
                    {
                        data: "name",
                        name: "name",
                        className: "dt-body-right",
                        searchable: false
                    },
                    {
                        data: "created_date",
                        name: "created_date",
                        className: "dt-body-right",
                        searchable: false
                    },
                    {
                        data: "modified_by",
                        name: "modified_by",
                        searchable: false,
                        render: function (data) {
                            if (data == null) {
                                return "None"
                            } else {
                                return data
                            }
                        }
                    },
                    {
                        data: "modified_date",
                        name: "modified_date",
                        searchable: false,
                        render: function (data) {
                            if (data == null) {
                                return "None"
                            } else {
                                return data
                            }
                        }
                    },
                    {
                        data: "package_status",
                        name: "package_status",
                        className: "dt-btn-cell",
                        searchable: false,
                        render: function (data) {
                            if (data == 0) {
                                return "<span class='label label-default'>Pending</span>"
                            } else if (data == 1) {
                                return "<span class='label label-success'>Approved</span>"
                            }
                            else if (data == 2) {
                                return "<span class='label label-danger'>Rejected</span>"
                            }
                        }
                    },
                    {
                        data: "package_status",
                        name: "package_status",
                        className: "dt-btn-cell",
                        searchable: false,
                        orderable: false,
                        render: function (data, type, row) {
                            if (data == 0) {
                                @if(Session::get($active_module.'verify')||Session::get($active_module.'validate'))
                                    return "<button data-id='" + row.id + "' class='btn btn-default btn-package-verify' data-toggle='tooltip' data-placement='top' " +
                                    "data-title='Verify Transaction'><i class='fas fa-paperclip'></i></buton>";
                                @else
                                    return "";
                                @endif
                            } else {
                                return "";
                            }
                        }
                    }
                ]
            });

            /** Purchaser Stuff - START**/

            /** Form Create **/

            $("#frm_create").on("submit", function () {

                var data = new FormData($("#frm_create")[0]);

                $.ajax({
                    url: url + "purchaser/validate/store",
                    method: "post",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: data,
                    success: function (respond) {
                        if (respond.status == "OK") {
                            confirm_dialog_open("#dialog_create");
                        } else {
                            var message = "";
                            $.each(respond.message, function (index, value) {
                                message = message + "<div class='alert alert-danger text-danger'><strong>" + value + "</strong></div>"
                            });
                            $("#alert_create").html(message);
                        }
                    }
                });

            });

            $("#frm_create input[name='referred']").on("change", function () {
                show_referral_group($(this))
            });

            $("#btn_create_confirm_yes").on("click", function () {
                var data = new FormData($("#frm_create")[0]);

                $.ajax({
                    url: url + "purchaser",
                    method: "post",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: data,
                    success: function (respond) {
                        confirm_dialog_close("#dialog_create");

                        $("#frm_create")[0].reset();

                        $("#frm_create select[name='referral']").val(null).trigger("change");

                        show_referral_group($("#frm_create input[name='referred']"));
                        $("#package_details").hide();

                        dt_purchaser.ajax.reload();

                        if (respond.status == "OK") {
                            message_modal_open("#mdl_create", "<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>");
                        } else {
                            message_modal_open("#mdl_create", "<div class='alert alert-danger text-danger'>" +
                                "<button class='close' data-dismiss='modal'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>");
                        }
                    }
                });
            });

            $("#btn_create_confirm_no").on("click", function () {
                confirm_dialog_close("#dialog_create");
            });

            $("#btn_create_cancel").on("click", function () {
                clear_messages();
                $("#frm_create input[name='referred']").prop('checked', false);
                $("#frm_create .referral-group").hide();
                $("#frm_create")[0].reset();
                $("#frm_create select[name='referral']").val(null).trigger("change");

            });

            $("#frm_create select[name='referral']").select2({
                dropdownParent: $("#dialog_create"),
                placeholder: "-- Find One --",
                theme: "bootstrap",
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: url + "purchaser/list/purchaser",
                    method: "post",
                    delay: 500,
                    async: false,
                    beforeSend: function () {
                        $("button[type='submit']").attr({disabled: true});
                        $('.alert_message').html("");
                    },
                    complete: function () {
                        $("button[type='submit']").attr({disabled: false});
                    },
                    data: function (params) {
                        return {
                            search_value: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data.data, function (item) {
                                return {
                                    text: item.purchaser_name + " [" + ((item.purchaser_eda == null) ? "NO EDA" : item.purchaser_eda) + "]",
                                    id: item.id
                                }
                            })
                        }
                    }
                }
            });

            /** Form Edit **/

            $("#frm_edit").on("submit", function () {
                var data = new FormData($("#frm_edit")[0]);

                $.ajax({
                    url: url + "purchaser/validate/update/" + id,
                    method: "post",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: data,
                    success: function (respond) {
                        if (respond.status == "OK") {
                            confirm_dialog_open("#dialog_edit");
                        } else {
                            var message = "";
                            $.each(respond.message, function (index, value) {
                                message = message + "<div class='alert alert-danger text-danger'><strong>" + value + "</strong></div>"
                            });
                            $("#alert_edit").html(message);
                        }
                    }
                });
            });

            $("#btn_edit_confirm_yes").on("click", function () {
                var data = new FormData($("#frm_edit")[0]);

                data.append("_method", "put");

                $.ajax({
                    url: url + "purchaser/" + id,
                    method: "post",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: data,
                    success: function (respond) {
                        confirm_dialog_close("#dialog_edit");
                        dt_purchaser.ajax.reload();

                        if (respond.status == "OK") {
                            get_purchaser_data(id);
                            message_modal_open("#mdl_edit", "<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal' data-toggle='modal' data-target='#mdl_view'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>");
                        } else {
                            message_modal_open("#mdl_edit", "<div class='alert alert-danger text-danger'>" +
                                "<button class='close' data-dismiss='modal' data-toggle='modal' data-target='#mdl_view'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>");
                        }
                    }
                });
            });

            $("#btn_edit_confirm_no").on("click", function () {
                confirm_dialog_close("#dialog_edit");
            });

            $("#btn_edit_cancel").on("click", function () {
                var unchanged_form = true, ignore_list = [
                    'referral_name',
                    //'purchaser_name',
                    'purchaser_country',
                    'purchaser_image',
                    //'purchaser_eda',
                    //'purchaser_type',
                    ///'purchaser_status',
                    //'purchaser_membership'
                ];

                $.each(original_data, function (og_key, og_data) {
                    if (jQuery.inArray(og_key, ignore_list) == -1) {
                        $.each($("#frm_edit").serializeArray(), function (al_key, al_data) {
                            if (og_key == al_data.name) {
                                if (og_data != ((al_data.value == "") ? null : al_data.value)) {
                                    unchanged_form = false;
                                    return false;
                                }
                            }
                        });
                    }
                });

                if (unchanged_form) {
                    modal_switch("#mdl_edit", "#mdl_view");
                    $(".fileinput").fileinput("clear");
                } else {
                    cancel_confirm_dialog_open("#dialog_edit");
                }
            });

            /** Form Delete **/

            $("#btn_delete_confirm_yes").on("click", function () {
                $.ajax({
                    url: url + "purchaser/" + id,
                    method: "delete",
                    success: function (respond) {

                        dt_purchaser.ajax.reload();

                        if (respond.status == "OK") {
                            message_modal_open("#mdl_delete", "<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>");
                        } else {
                            message_modal_open("#mdl_delete", "<div class='alert alert-danger text-danger'>" +
                                "<button class='close' data-dismiss='modal'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>");
                        }
                    }
                })
            });

            $("#btn_delete_confirm_no").on("click", function () {
                modal_switch("#mdl_delete", "#mdl_view");
            });

            /** From View **/

            $("#btn_view_edit").on("click", function () {
                modal_switch("#mdl_view", "#mdl_edit");
            });

            $("#btn_view_delete").on("click", function () {
                modal_switch("#mdl_view", "#mdl_delete");
            });

            $("#btn_view_close").on("click", function () {
                $("#mdl_view").modal("hide");
            });

            $("#dt_purchaser").on("click", ".btn-view-purchaser", function () {
                id = $(this).attr("data-id");
                get_purchaser_data(id, "#mdl_view");
            });

            $("#dt_purchaser").on("click", ".btn-view-activity", function () {
                id = $(this).attr("data-id");
                $("#mdl_activity_view").modal("show");
            });

            /** Purchaser Stuff  - END **/

            /** Wallet Events */

            $("#dt_purchaser").on("click", ".btn-view-purchaser-wallets", function () {
                id = $(this).attr("data-id");
                get_purchaser_data(id, 0, false);
                get_wallet_data(id);
                $("#mdl_wallet_view").modal("show");
            });

            /** Family Tree Events*/

            $('#dt_purchaser').on('click', '.btn-view-tree', function () {

                id = $(this).attr("data-id");
                get_purchaser_data(id, "#mdl_family_view", false);

                var tid = $(this).attr('data-id');

                $.ajax({
                    type: 'POST',
                    url: '{{ route("tree.member.view") }}',
                    data: {
                        _fid: tid
                    },
                    success: function (data) {
                        $('.tree').html(data);
                        $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
                        $('.tree li.parent_li > span').on('click', function (e) {
                            var children = $(this).parent('li.parent_li').find(' > ul > li');
                            if (children.is(":visible")) {
                                children.hide('fast');
                                $(this).attr('title', 'Expand this branch').find(' > i').addClass('fa-plus').removeClass('fa-minus');
                            } else {
                                children.show('fast');
                                $(this).attr('title', 'Collapse this branch').find(' > i').addClass('fa-minus').removeClass('fa-plus-circle');
                            }
                            e.stopPropagation();
                        });
                    }
                });
            });

            $("#dt_purchaser").on("click", ".btn-view-purchaser-packages", function () {
                id = $(this).attr("data-id");
                get_packages_data(id);
            });

            $("#btn_package_view_close").on("click", function () {
                $("#mdl_package_view").modal("hide");
            });

            $("#btn_package_create").on("click", function () {
                modal_switch("#mdl_package_view", "#mdl_package_create");
            });

            $("#btn_package_create_cancel").on("click", function () {
                $("#frm_package_create")[0].reset();
                clear_messages();
                modal_switch("#mdl_package_create", "#mdl_package_view");
            });

            $("#frm_package_create").on("submit", function () {
                var data = $(this).serialize() + "&" + $.param({purchaser_id: id});

                $.ajax({
                    url: url + "purchaser-package/validate/store",
                    method: "post",
                    data: data,
                    success: function (respond) {
                        if (respond.status == "OK") {
                            confirm_dialog_open("#dialog_package_create");
                        } else {
                            var message = "";
                            $.each(respond.message, function (index, value) {
                                message = message + "<div class='alert alert-danger text-danger'><strong>" + value + "</strong></div>"
                            });
                            $("#alert_package_create").html(message);
                        }
                    }
                });
            });

            $("#btn_package_create_confirm_no").on("click", function () {
                confirm_dialog_close("#dialog_package_create");
            });

            $("#btn_package_create_confirm_yes").on("click", function () {
                var data = $("#frm_package_create").serialize() + "&" + $.param({purchaser_id: id});

                $.ajax({
                    url: url + "purchaser-package",
                    method: "post",
                    data: data,
                    success: function (respond) {
                        $("#frm_package_create")[0].reset();
                        dt_purchaser_packages.ajax.reload();
                        dt_purchaser.ajax.reload(null, false);
                        confirm_dialog_close("#dialog_package_create");
                        $("#package_details").slideUp("fast");
                        if (respond.status == "OK") {
                            message_modal_open("#mdl_package_create", "<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal' data-toggle='modal' data-target='#mdl_package_view'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>");
                        } else {
                            message_modal_open("#mdl_package_create", "<div class='alert alert-danger text-danger'>" +
                                "<button class='close' data-dismiss='modal' data-toggle='modal' data-target='#mdl_package_view'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>");
                        }
                    }
                });
            });

            /** Key Press on Digit Only Fields **/

            $("#frm_package_create input[name='package_token']").ForceNumericOnly();

            $("#frm_package_create input[name='package_incentive']").ForceNumericOnly();

            $("#frm_package_create input[name='package_price']").ForceNumericOnly();

            $("#frm_edit input[name='purchaser_contact']").ForceDigitsOnly();

            $("#frm_create input[name='purchaser_contact']").ForceDigitsOnly();

            /** Cancel to Confirm on Edit Form **/

            $("#btn_edit_cancel_confirm_yes").on("click", function () {
                get_purchaser_data(id, 0, true);
                cancel_confirm_dialog_close("#dialog_edit");
                modal_switch("#mdl_edit", "#mdl_view");
            });

            $("#btn_edit_cancel_confirm_no").on("click", function () {
                cancel_confirm_dialog_close("#dialog_edit");
            });

            $("#btn_resend_wallet_code").on("click", function () {
                resend_wallet_code();
            });

            $("#mdl_message").on('hidden.bs.modal', function () {
                $("#dialog_message").html("<div class='modal-content'><div class='modal-body'><i class='fas fa-spinner fa-pulse'></i> Please Wait</div></div>")
            });

            $("#frm_package_create input[name='package_token_price']").on("change", function (e) {
                compute_package_token(e);
            });

            $("#frm_package_create input[name='package_paid_amount']").on("change", function (e) {
                compute_package_token(e);
            });

            $("#frm_package_create input[name='package_incentive_percentage']").on("change", function (e) {
                compute_package_token(e);
            });

            $("#dt_purchaser_packages").on("click", ".btn-package-verify", function () {
                package_id = $(this).attr("data-id");
                modal_switch("#mdl_package_view", "#mdl_package_verify");
                get_package_verify_data(package_id);
            });

            $("#btn_package_verify_cancel").on("click", function () {

                modal_switch("#mdl_package_verify", "#mdl_package_view");
            });

            $("#btn_package_verify_reject").on("click", function () {
                $("#dialog_package_verify").slideUp("fast");
                $("#dialog_package_verify_reject_confirm").slideDown("fast");
            });

            $("#btn_package_verify_reject_confirm_no").on("click", function () {
                $("#dialog_package_verify_reject_confirm").slideUp("fast");
                $("#dialog_package_verify").slideDown("fast");
            });

            $("#btn_package_verify_post").on("click", function () {
                $("#dialog_package_verify").slideUp("fast");
                $("#dialog_package_verify_post_confirm").slideDown("fast");
            });

            $("#btn_package_verify_post_confirm_no").on("click", function () {
                $("#dialog_package_verify_post_confirm").slideUp("fast");
                $("#dialog_package_verify").slideDown("fast");
            });

            $("#btn_package_verify_reject_confirm_yes").on("click", function () {

                //modal_switch("#mdl_package_verify", "#mdl_message");

                var reject_value = $(this).attr('data-value');
                bonusActivityLogs(id, package_token_total, reject_value, package_id);

                $.ajax({
                    url: url + "purchaser-package/transact/reject",
                    method: "post",
                    data: {
                        package_id: package_id
                    },
                    success: function (respond) {
                        if (respond.status == "OK") {
                            message_modal_open("#mdl_package_verify", "<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal' data-toggle='modal' data-target='#mdl_package_view'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>");

                            dt_purchaser_packages.ajax.reload();

                            $("#dialog_package_verify_reject_confirm").slideUp("fast");
                            $("#dialog_package_verify").slideDown("fast");
                        }
                    }
                });

                dt_purchaser.ajax.reload(null, false);

            });

            $("#btn_package_verify_post_confirm_yes").on("click", function () {

                //modal_switch("#mdl_package_verify", "#mdl_message");

                var post_value = $(this).attr('data-value');
                bonusActivityLogs(id, package_token_total, post_value, package_id);

                $.ajax({
                    url: url + "purchaser-package/transact/post",
                    method: "post",
                    data: {
                        package_id: package_id
                    }
                });

                $.ajax({
                    url: url + "purchaser-wallet",
                    method: "post",
                    data: {
                        package_id: package_id
                    },
                    success: function (respond) {
                        if (respond.status == "OK") {
                            message_modal_open("#mdl_package_verify", "<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal' data-toggle='modal' data-target='#mdl_package_view'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>");

                            dt_purchaser_packages.ajax.reload();

                            $("#dialog_package_verify_post_confirm").slideUp("fast");
                            $("#dialog_package_verify").slideDown("fast");
                        }
                    }
                });

                dt_purchaser.ajax.reload(null, false);
            });

            $("#btn_download_csv").on("click", function () {
                $(this).attr({disabled: true});
                window.location = url + "purchaser/download/csv?" +
                    "search=" + search +
                    "&col_filter=" + col_filter +
                    "&type=" + type +
                    "&status=" + status +
                    "&country=" + country +
                    "&package=" + package_status;
                $(this).attr({disabled: false});
            });

            function bonusActivityLogs(purchaser_id, package_token_total, bonus_status, package_id) {
                $.ajax({
                    method: "post",
                    url: "{{route('compute.bonus')}}",
                    data: {
                        _token: token,
                        package_id: package_id,
                        purchaser_id: purchaser_id,
                        package_token: package_token_total,
                        status: bonus_status,
                    }
                });
            }
        });
    </script>
@endpush
