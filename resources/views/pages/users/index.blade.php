@extends('layouts.app')

@section('content')

    <div class="panel panel-default">

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-inline">
                        <div class="form-group">
                            <input type="text" class="form-control" name="search" id="search" placeholder="Search"/>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-default btn-search"><i class="fa fa-search"></i></button>
                        </div>
                        <div class="form-inline pt-3">
                            <div class="form-group">
                                <label class="form-control-static pr-3">Search Type:</label>
                            </div>
                            <div class="form-group">
                                <label class="radio-inline form-control-static pr-3">
                                    <input type="radio" name="column_search" value="1" checked/> Name
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="radio-inline form-control-static pr-3">
                                    <input type="radio" name="column_search" value="2"/> Email Address
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="radio-inline form-control-static pr-3">
                                    <input type="radio" name="column_search" value="3"/> Role
                                </label>
                            </div>
                        </div>
                        @if(Session::get($active_module.'create'))
                            <div class="form-inline pt-3 text-right">
                                <button class="btn btn-primary" id="create-account">
                                    <i class="fa fa-plus"></i> Create Account
                                </button>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table id="usersTable" class="table table-bordered table-striped" style="width: 100%">
                        <thead>
                        <tr class="bg-primary">
                            <th>ID No.</th>
                            <th>Name</th>
                            <th>Email Address</th>
                            <th>Role</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="5" align="center">No data found!</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width:450px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Create User</h4>
                </div>
                <div class="modal-body">
                    <div class="message-container" align="center">
                        <div class="message" hidden></div>
                        <div class="loading" hidden>
                            <div class="loader"></div>
                            Please wait. . .
                            <hr>
                        </div>
                    </div>
                    <form id="form-create-account">
                        <input type="hidden" name="_uid" class="form-control account-id"/>
                        <div class="form-group">
                            <label>
                                Name
                            </label>
                            <input type="text" name="account_name" class="form-control account-name"/>
                        </div>
                        <div class="form-group">
                            <label>
                                Email Address
                            </label>
                            <input type="text" name="email" class="form-control email-address"/>
                        </div>
                        <div class="form-group">
                            <label>
                                Select a Role
                            </label>
                            <select class="form-control role" name="role">
                                <?php echo $option_roles; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>
                                Password
                            </label>
                            <input type="password" name="password" class="form-control password"/>
                        </div>
                        <div class="form-group">
                            <label>
                                Confirm Password
                            </label>
                            <input type="password" name="confirm_password" class="form-control confirm-password"/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary btn-block submit-form">
                                <i class="fa fa-check"></i> Submit
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-gray btn-block close-modal" data-dismiss="modal">
                                <i class="fa fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Validation Message</h5>
                </div>
                <div class="modal-body modal-notification" align="center">
                    Are you sure you want to create this record?
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" id="submit-form" class="btn btn-primary btn-block">
                                <i class="fa fa-check"></i> Submit
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-gray modal-cancel btn-block" data-dismiss="modal">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width:450px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">User Information</h5>
                </div>
                <div class="modal-body">
                    <div class="message-container" align="center">
                        <div class="message" hidden></div>
                        <div class="loading" hidden>
                            <div class="loader"></div>
                            Please wait. . .
                            <hr>
                        </div>
                    </div>
                  <div class="">
                      <div class="form-group">
                          <label>
                              Name
                          </label>
                          <input type="text" name="account_name" class="form-control account-name-view" disabled/>
                      </div>
                      <div class="form-group">
                          <label>
                              Email Address
                          </label>
                          <input type="text" name="email" class="form-control email-view" disabled/>
                      </div>
                      <div class="form-group">
                          <label>
                              Role
                          </label>
                          <div class="well role-view"></div>
                      </div>
                  </div>
                  <div class="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    Created By:
                                </label>
                                <div class="created-by-view"></div>
                            </div>
                            <div class="form-group">
                                <label>
                                    Last Modified By:
                                </label>
                                <div class="updated-by-view"></div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="form-group">
                                <label>
                                    Created Date:
                                </label>
                                <div class="date-created-view"></div>
                            </div>
                            <div class="form-group">
                                <label>
                                    Last Modified Date:
                                </label>
                                <div class="date-updated-view"></div>
                            </div>
                        </div>
                    </div>
                  </div>

                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-4">
                            @if(Session::get($active_module.'delete'))
                                <button type="button" id="delete-user" class="btn btn-danger btn-block">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            @endif
                        </div>
                        <div class="col-md-4">
                            @if(Session::get($active_module.'modify')||Session::get($active_module.'edit'))
                                <button type="button" id="modify-user" class="btn btn-primary btn-block">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-gray view-cancel btn-block" data-dismiss="modal">
                                <i class="fa fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div id="mdl_message" class="modal" data-backdrop="static" data-keyboard="false">
        <div id="dialog_message" class="modal-dialog text-center">
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            var _serialized_array = '';
            var _activity = '';
            var _modal_create = $('#createModal');
            var _modal_notification = $('#notificationModal');
            var _modal_view = $('#viewModal');
            var _loading = $('.loading');
            var _message = $('.message');

            $("body").tooltip({selector: "[data-toggle='tooltip']"});

            $('#create-account').on('click', function () {
                _activity = 'create';
                $('.modal-title').html("Create User");
                $('.account-id').val('');
                $('.account-name').val('');
                $('.email-address').val('');
                $('.role option:selected').prop('selected', false);
                $('.password').val('');
                $('.confirm-password').val('');

                activeButtons();

                _loading.prop('hidden', true);
                _message.prop('hidden', true);

                _modal_create.modal({
                    'backdrop': 'static',
                    'keyboard': false
                });
            });

            $('#modify-user').on('click', function () {
                var _data = $(this).attr('data-value');
                _activity = "modify";
                $('.modal-title').html("Modify User");

                activeButtons();

                $('.account-id').val('');
                $('.account-name').val('');
                $('.email-address').val('');
                $('.role option:selected').prop('selected', false);
                $('.password').val('');
                $('.confirm-password').val('');

                _modal_view.modal('hide');

                _modal_create.modal({
                    'backdrop': 'static',
                    'keyboard': false
                });

                getData(_data, _activity);
            });

            $('#delete-user').on('click', function () {
                var _data = $(this).attr('data-value');
                _serialized_array = _data;
                _activity = "delete";

                $('.modal-title').html('Validation Message');
                $('.modal-notification').html("Are you sure you want to remove this record?");
                _modal_view.modal('hide');

                _modal_notification.modal({
                    'backdrop': 'static',
                    'keyboard': false
                });
            });

            $('.submit-form').on('click', function () {
                _modal_create.modal('hide');
                $('.modal-title').html('Validation Message');

                disableButtons();

                _modal_notification.modal({
                    'backdrop': 'static',
                    'keyboard': false
                });

                if (_activity == "modify") {
                    $('.modal-notification').html("Are you sure you want to update this record?");
                } else if (_activity == "create") {
                    $('.modal-notification').html("Are you sure you want to create this record?");
                } else {
                    alert("Invalid Call");
                }
            });

            $('.modal-cancel').on('click', function () {
                _loading.prop('hidden', true);
                _message.prop('hidden', true);

                switch(_activity){
                    case "modify" :
                        _modal_notification.modal('hide');
                        _modal_create.modal({
                            'backdrop': 'static',
                            'keyboard': false
                        });
                        selectModalTitle(_activity);

                        getData(uid, '');

                        break;
                    case "create" :
                        _modal_notification.modal('hide');
                        _modal_create.modal({
                            'backdrop': 'static',
                            'keyboard': false
                        });
                        selectModalTitle(_activity);

                        break;
                    case "delete" :
                        _modal_notification.modal('hide');
                        _modal_view.modal({
                            'backdrop': 'static',
                            'keyboard': false
                        });

                        $('.modal-title').html("User Information");
                        break;
                }
            });

            $('.close-modal').on('click', function () {
                _loading.prop('hidden', true);
                _message.prop('hidden', true);
                var uid = $('.account-id').val();

                switch(_activity){
                    case "modify" :
                      _modal_notification.modal('hide');
                      _modal_view.modal({
                          'backdrop': 'static',
                          'keyboard': false
                      });
                      $('.modal-title').html("User Information");
                      getData(uid);
                      break;
                    case "delete" :
                      _modal_create.modal('hide');
                      _modal_view.modal({
                          'backdrop': 'static',
                          'keyboard': false
                      });
                      $('.modal-title').html("User Information");
                      break;
                    case "create" :
                      $('.modal-title').html("Create User");
                      break;
                }

            });

              $('#submit-form').on('click', function () {
                var uid = '';
                switch (_activity) {
                    case "create":
                        var _url = '{{ route("users.create") }}';
                        _serialized_array = $('#form-create-account').serializeArray();

                        break;
                    case "modify":
                        var _url = '{{ route("users.modify") }}';
                        _serialized_array = $('#form-create-account').serializeArray();
                        uid = $('.account-id').val();

                        _modal_notification.modal('hide');

                        _modal_create.modal({
                            'backdrop': 'static',
                            'keyboard': false
                        });

                        selectModalTitle(_activity);
                        getData(uid);
                        break;
                    case "delete":
                        var _url = '{{ route("users.remove") }}';
                        uid = _serialized_array;

                        disableButtons();

                        _modal_notification.modal('hide');

                        _modal_view.modal({
                            'backdrop': 'static',
                            'keyboard': false
                        });

                        selectModalTitle(_activity);
                        break;
                    default:
                        _serialized_array = "No Data";
                        break;
                }

                var fdata = new FormData();

                fdata.append('_token', '{{ csrf_token() }}');
                if (_activity == "delete") {
                    fdata.append('_uid', uid);
                } else {
                    $.each(_serialized_array, function (i, fields) {
                        fdata.append(fields.name, fields.value);
                    });
                }

                connectToServer(fdata, _url, _loading, _message, table, _activity);
            });

            $("#usersTable").on("click", ".view-info", function () {
                var _data = $(this).attr('data-value');
                $('.message').prop('hidden', true);
                $('.loading').prop('hidden', true);
                $('.modal-title').html("User Information");

                _modal_view.modal({
                    'backdrop': 'static',
                    'keyboard': false
                });

                $('#modify-user').attr('data-value', _data);
                $('#delete-user').attr('data-value', _data);
                $('#modify-user').prop('disabled', false);
                $('#delete-user').prop('disabled', false);

                getData(_data, '');

                return false;
            });

            var table = $('#usersTable').DataTable({
                processing: true,
                searching: false,
                bLengthChange: false,
                serverSide: true,
                searchDelay: 500,
                pagingType: "input",
                "columnDefs": [{
                    "targets": [4],
                    "orderable": false
                }],
                ajax: {
                    type: "POST",
                    url: "{{ route('users.list') }}",
                    dataType: "json",
                    data: function (d) {
                        return $.extend({}, d, {
                            _token: "{{ csrf_token() }}",
                            _search: $('#search').val(),
                            _radio_value: $('input[name=column_search]:checked').val()
                        });
                    }
                },
                columns: [
                    {data: 'no', sClass: 'dt-body-right'},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'role'},
                    {data: 'activity', sClass: 'dt-btn-cell'},
                ]
            });

            $('.btn-search').on('click', function () {
                table.draw();
            });

            $('#search').keypress(function (event) {
                if (event.keyCode == 13) {
                    table.draw();
                }
            });
        });

        function connectToServer($data, $url, $loading, $message, $table, $activity) {
            $loading.prop('hidden', false);
            $message.prop('hidden', true);

            $.ajax({
                type: 'POST',
                url: $url,
                dataType: 'json',
                processData: false,
                contentType: false,
                data: $data,
                success: function (data) {
                    var html = '';

                    if (data.result === "success") {
                        if ($activity == "create") {
                            var message = "<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal'>&times;</button>" +
                                "<strong>" + data.message + "</strong></div>";

                            modalMessage($('#notificationModal'), message);

                        } else {
                            if($activity=="modify"){
                                activeButtons();
                            }
                            html += '<div class="alert alert-success">';
                            html += data.message;
                            html += '</div>';

                            callModal($loading, $message, $activity, html);
                        }

                        $table.draw();
                    } else {
                        html += '<div class="alert alert-danger">';
                        html += data.message;
                        html += '</div>';

                        activeButtons();

                        callModal($loading, $message, $activity, html);
                    }

                }
            });
        }

        function callModal($loading, $message, $activity, $html) {
            $('#notificationModal').modal('hide');

            if ($activity == "delete") {
                $('#modify-user').prop('disabled', true);
                $('#delete-user').prop('disabled', true);
            } else {
                $('#createModal').modal({
                    'backdrop': 'static',
                    'keyboard': false
                });
            }

            $loading.prop('hidden', true);
            $message.prop('hidden', false);
            $message.html($html);
        }

        function getData($uid, $activity) {
            $.ajax({
                type: 'POST',
                url: '{{ route("users.data") }}',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                    _uid: $uid
                },
                success: function (data) {
                    if ($activity === "modify") {
                        $('.account-id').val(data.account_id);
                        $('.account-name').val(data.account_name);
                        $('.email-address').val(data.email);
                        $('.password').val(data.account_password);
                        $('.role').val(data.role_id);
                        $('.confirm-password').val(data.account_password);
                    }
                    else {
                        $('.account-name-view').val(data.account_name);
                        $('.email-view').val(data.email);
                        $('.role-view').html(data.role);
                        $('.created-by-view').html(data.created_by);
                        $('.updated-by-view').html(data.updated_by);
                        $('.date-created-view').html(data.created_date);
                        $('.date-updated-view').html(data.updated_date);
                    }
                },
                error: function (ajaxHrs, status, error) {
                    alert(error);
                }
            });
        }

        function selectModalTitle($activity)
        {
            switch ($activity) {
              case "create":
                $(".modal-title").html("Create User");
                break;
              case "modify":
                $(".modal-title").html("Modify User");
                break;
              case "delete":
                $(".modal-title").html("User Information");
                break;
              default:

            }
        }

        function modalMessage($modal = '', $message = '') {

            if ($modal != '') {
                $modal.modal('hide');
            }

            if ($message != '') {
                $('#mdl_message').modal('show');
                $('#dialog_message').html($message);
            } else {
                $('#mdl_message').modal('hide');
            }
        }

        function disableButtons()
        {
            var disabled_button = 'disabled';
            $('.submit-form').prop(disabled_button,true);
            $('#modify-user').prop(disabled_button,true);
            $('#delete-user').prop(disabled_button,true);
        }

        function activeButtons()
        {
            var disabled_button = 'disabled';
            $('.submit-form').prop(disabled_button,false);
            $('#modify-user').prop(disabled_button,false);
            $('#delete-user').prop(disabled_button,false);
        }
    </script>
@endpush
