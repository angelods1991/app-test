@extends('layouts.app')

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
                                <select name="status" class="form-control">
                                    <option value="all">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
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
                                    <input type="radio" name="col_filter" value="2"> Code
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="radio-inline form-control-static pr-3">
                                    <input type="radio" name="col_filter" value="3"> Name
                                </label>
                            </div>
                        </div>
                        {{--@if(Session::get($active_module.'create'))--}}
                        <div class="form-inline pt-3">
                            <button  class="btn btn-default" data-toggle="tooltip" title="Download CSV" id="btn_download_csv">
                                <i class="fas fa-file-download"></i>
                            </button>
                            @if(Session::get($active_module.'create'))
                                <button id="btn_create" type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#mdl_create">
                                    <i class="fas fa-plus"></i> Create Country
                                </button>
                            @endif
                        </div>
                        {{--@endif--}}
                    </form>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="dt_country" class="table table-bordered table-hover" style="width: 100%">
                            <thead>
                            <tr class="bg-primary">
                                <th>ID No.</th>
                                <th>Country Code</th>
                                <th>Name</th>
                                <th>Currency Code</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-center" colspan="5">Please Wait...</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modals.country.country-modals')
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var id = 0, search = "", col_filter = 1, status = "all", original_data,
                token = $("meta[name='csrf-token']").attr("content"), url = "{{url('') . '/pages/'}}";

            $("body").tooltip({selector: "[data-toggle='tooltip']", container: "body", trigger: "hover"});

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                beforeSend: function () {
                    $("button[type='submit']").attr({disabled: true});
                    $('.alert_message').html("");
                },
                complete: function () {
                    $("button[type='submit']").attr({disabled: false});
                    $('.modal').animate({scrollTop: 0}, "fast");
                },
                error: function (respond) {
                    if (respond.status == 401 || respond.status == 419) {
                        $('.modal').modal('hide');
                        $("#mdl_session_timeout").modal("show");

                        setTimeout(function () {
                            location.reload(true);
                        }, 10000);
                    } else {
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
                search = $("#frm_search input[name='search']").val();
             dt_country.ajax.reload(null, false);
            });

            $("#mdl_message").on('hidden.bs.modal', function () {
                $("#dialog_message").html("<div class='modal-content'><div class='modal-body'><i class='fas fa-spinner fa-pulse'></i> Please Wait</div></div>")
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

            function message_modal_open(message = 0, modal_id = 0) {
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

            function get_country_data(id) {
                $.ajax({
                    url: url + "country/" + id,
                    method: "get",
                    success: function (respond) {

                        original_data = respond.data;

                        $("#frm_view input[name='id']").val(respond.data.id);
                        $("#frm_view input[name='country_code']").val(respond.data.country_code);
                        $("#frm_view input[name='country_name']").val(respond.data.country_name);
                        $("#frm_view input[name='currency_code']").val(respond.data.currency_code);
                        $("#frm_view input[name='country_status']").val((respond.data.country_status == 1) ? "Active" : "Inactive");

                        $(".created_date").html(respond.data.created_date);
                        $(".modified_date").html((respond.data.modified_date != null) ? respond.data.modified_date : "None");

                        $(".created_by").html(respond.data.created_name);
                        $(".modified_by").html((respond.data.modified_name != null) ? respond.data.modified_name : "None");

                        $("#frm_edit input[name='id']").val(respond.data.id);
                        $("#frm_edit input[name='country_code']").val(respond.data.country_code);
                        $("#frm_edit input[name='country_name']").val(respond.data.country_name);
                        $("#frm_edit input[name='currency_code']").val(respond.data.currency_code);
                        $("#frm_edit select[name='country_status']").val(respond.data.country_status);
                    }
                })
            }

            var dt_country = $("#dt_country").DataTable({
                processing: true,
                serverSide: true,
                pagingType: "input",
                dom: "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-6'i><'col-sm-6'p>>",
                ajax: {
                    url: url + "country/table",
                    method: "post",
                    data: function (d) {
                        d._token = token;
                        d.search = search;
                        d.col_filter = col_filter;
                        d.status = status;
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
                        className: "dt-body-right"
                    },
                    {
                        data: "country_code",
                        name: "country_code"
                    },
                    {
                        data: "country_name",
                        name: "country_name"
                    },
                    {
                        data: "currency_code",
                        name: "currency_code"
                    },
                    {
                        data: "country_status",
                        name: "country_status",
                        orderable: false,
                        searchable: false,
                        className: "dt-btn-cell",
                        render: function (data) {
                            if (data == 1) {
                                return "<label class='label label-success mr-5 ml-5'>Active</label>";
                            } else {
                                return "<label class='label label-danger mr-5 ml-5'>Inactive</label>";
                            }
                        }
                    },
                    {
                        data: "id",
                        name: "id",
                        orderable: false,
                        searchable: false,
                        className: "dt-btn-cell",
                        render: function (data) {
                            return "<button class='btn btn-default btn-country-view btn-sm' data-id='" + data + "' data-title='View' data-placement='top' data-toggle='tooltip'><i class='far fa-fw fa-eye'></i></button>";
                        }
                    },
                ]
            });

            /** Form View - Start **/

            $("#dt_country").on("click", ".btn-country-view", function () {
                id = $(this).attr("data-id");
                get_country_data(id);
                $("#mdl_view").modal("show");
            });

            $("#btn_view_edit").on("click", function () {
                modal_switch("#mdl_view","#mdl_edit");
            });

            $("#btn_view_close").on("click", function () {
                $("#mdl_view").modal("hide");
            });

            $("#btn_view_delete").on("click", function () {
                modal_switch("#mdl_view","#mdl_delete");
            });

            /** Form View - End **/

            /** Form Create - Start **/

            $("#btn_create_cancel").on("click", function () {
                $("#frm_create")[0].reset();
                clear_messages();
            });

            $("#frm_create").on("submit", function () {
                var data = $("#frm_create").serialize();

                $.ajax({
                    url: url + "country/validate/store",
                    method: "post",
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

            $("#btn_create_confirm_yes").on("click", function () {
                var data = $("#frm_create").serialize();

                modal_switch("#mdl_create", "#mdl_message");
                confirm_dialog_close("#dialog_create");

                $.ajax({
                    url: url + "country",
                    method: "post",
                    data: data,
                    success: function (respond) {
                        $("#frm_create")[0].reset();

                        if (respond.status == 'OK') {
                            message_modal_open("<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>", "#mdl_create");

                            dt_country.ajax.reload(null, false);

                        } else {
                            message_modal_open("<div class='alert alert-danger text-danger'>" +
                                "<button class='close' data-dismiss='modal'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>", "#mdl_create");
                        }
                    }
                });
            });

            $("#btn_create_confirm_no").on("click", function () {
                confirm_dialog_close("#dialog_create");
            });

            /** Form Create - End **/

            /** Form Edit - Start **/

            $("#frm_edit").on("submit", function () {
                var data = $("#frm_edit").serialize();

                $.ajax({
                    url: url + "country/validate/update/" + id,
                    method: "post",
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

            $("#btn_edit_cancel").on("click", function () {
                var unchanged_form = true, ignore_list = [
                    'country_code',
                    'id'
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
                } else {
                    cancel_confirm_dialog_open("#dialog_edit");
                }
            });

            $("#btn_edit_confirm_yes").on("click", function () {
                var data = $("#frm_edit").serialize();

                modal_switch("#mdl_edit", "#mdl_message");
                confirm_dialog_close("#dialog_edit");

                $.ajax({
                    url: url + "country/" + id,
                    method: "put",
                    data: data,
                    success: function (respond) {
                        $("#frm_edit")[0].reset();

                        if (respond.status == 'OK') {

                            get_country_data(id);

                            message_modal_open("<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal' data-toggle='modal' data-target='#mdl_view'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>", "#mdl_edit");

                         dt_country.ajax.reload(null, false);
                        } else {
                            message_modal_open("<div class='alert alert-danger text-danger'>" +
                                "<button class='close' data-dismiss='modal'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>", "#mdl_edit");
                        }
                    }
                });
            });

            $("#btn_edit_confirm_no").on("click", function () {
                confirm_dialog_close("#dialog_edit");
            });

            $("#btn_edit_cancel_confirm_yes").on("click", function () {
                get_country_data(id);
                cancel_confirm_dialog_close("#dialog_edit");
                modal_switch("#mdl_edit", "#mdl_view");
            });

            $("#btn_edit_cancel_confirm_no").on("click", function () {
                cancel_confirm_dialog_close("#dialog_edit");
            });

            /** Form Edit - End **/

            /** Form Delete - Start **/

            $("#btn_delete_confirm_yes").on("click", function () {
                modal_switch("#mdl_delete", "#mdl_message");

                $.ajax({
                    url: url + "country/" + id,
                    method: "delete",
                    success: function (respond) {
                        if (respond.status == 'OK') {
                            message_modal_open("<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>", "#mdl_edit");

                         dt_country.ajax.reload(null, false);
                        } else {
                            message_modal_open("<div class='alert alert-danger text-danger'>" +
                                "<button class='close' data-dismiss='modal'>&times;</button>" +
                                "<strong>" + respond.message + "</strong></div>", "#mdl_edit");
                        }
                    }
                });
            });

            $("#btn_delete_confirm_no").on("click", function () {
                modal_switch("#mdl_delete","#mdl_view");
            });

            $("#btn_download_csv").on("click", function () {
                window.location = url + "country/download/csv?" + $("#frm_search").serialize();
            });

            /** Form Delete - End **/
        });
    </script>
@endpush
