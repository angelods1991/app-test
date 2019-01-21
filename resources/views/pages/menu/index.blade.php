@extends('layouts.app')

@section('content')

<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control" name="search" id="search" placeholder="Search"  />
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-search"><i class="fa fa-search"></i></button>
                    </div>
                </div>
                <div class="form-inline pt-3">
                    <div class="form-group">
                        <label class="form-control-static pr-3">Search Type:</label>
                    </div>
                    <div class="form-group">
                        <label class="radio-inline form-control-static pr-3">
                            <input type="radio" name="column_search" value="1" checked /> Name
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="radio-inline form-control-static pr-3">
                            <input type="radio" name="column_search" value="2" /> Link
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="radio-inline form-control-static pr-3">
                            <input type="radio" name="column_search" value="3" /> Type
                        </label>
                    </div>
                </div>
                @if(Session::get($active_module.'create'))
                <div class="form-inline pt-3 text-right">
                    <button class="btn btn-primary" id="create-menu"><i class="fa fa-plus"></i> Register Menu</button>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table id="menuTable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                    <tr class="bg-primary">
                        <th>ID No.</th>
                        <th>Name</th>
                        <th>Link</th>
                        <th>Type</th>
                        <th>Permissions</th>
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
  <div class="modal-dialog" style="max-width:450px; width:100%" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Create Form</h4>
      </div>
      <div class="modal-body">
          <div class="message-container" align="center">
              <div class="message" hidden></div>
              <div class="loading" hidden>
                  <div class="loader"></div>
                  Please wait. . .<hr>
              </div>
          </div>
          <form id="form-create-menu">
            <input type="hidden" name="_mid" class="form-control menu-id" />
            <div class="form-group">
                <label>
                    Name
                </label>

                <select class="form-control menu-name" name="menu_name">
                    <option value="Dashboard">Dashboard</option>
                    <option value="Purchaser Management">Purchaser Management</option>
                    <option value="Bonus Management">Bonus Management</option>
                    <option value="EDCOIN Rate">EDCOIN Rate</option>
                    <option value="Referral Bonus Logs">Referral Bonus Logs</option>
                    <option value="Transaction Logs">Transaction Logs</option>
                    <option value="EDCOIN Rate Logs">EDCOIN Rate Logs</option>
                    <option value="Download Reports">Download Reports</option>
                    <option value="User Management">User Management</option>
                    <option value="Role Management">Role Management</option>
                    <option value="Menu Management">Menu Management</option>
                    <option value="Permission Management">Permission Management</option>
                    <option value="Country Management">Country Management</option>
                </select>
            </div>
            <div class="form-group">
                  <label>
                      Link
                  </label>
                  <div class="input-group">
                      <div class="input-group-addon">
                          <span>{{ url('/') }}</span>
                      </div>
                      <input type="text" name="menu_link" class="form-control menu-link" />
                  </div>
            </div>

            <div class="form-group">
                <label>
                    Type
                </label>
                <input type="text" name="menu_type" class="form-control menu-type" />
            </div>
            <div class="form-group">
                <label>
                    Permissions
                </label>
                <select class="form-control permissions" name="permissions" multiple>
                </select>
            </div>
          </form>
      </div>
      <div class="modal-footer">
          <div class="row">
              <div class="col-md-6">
                  <button type="button" class="btn btn-primary btn-block submit-form"><i class="fa fa-check"></i> Submit</button>
              </div>
              <div class="col-md-6">
                  <button type="button" class="btn btn-gray btn-block close-modal" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
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
        <h5 class="modal-title" id="exampleModalLabel">Message Box</h5>
      </div>
      <div class="modal-body modal-notification" align="center">
          Are you sure you want to create this record?
      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-md-6">
              <button type="button" id="submit-form" class="btn btn-primary btn-block"><i class="fa fa-check"></i> Submit</button>
          </div>
          <div class="col-md-6">
              <button type="button" class="btn btn-gray modal-cancel btn-block" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
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
        <h5 class="modal-title" id="exampleModalLabel">Menu Information</h5>
      </div>
      <div class="modal-body">
          <div class="message-container" align="center">
              <div class="message" hidden></div>
              <div class="loading" hidden>
                  <div class="loader"></div>
                  Please wait. . .<hr>
              </div>
          </div>
          <div>
            <div class="form-group">
                <label>
                    Name
                </label>
                <input type="text" name="menu_name" class="form-control menu-name-view" disabled/>
            </div>
            <div class="form-group">
                <label>
                    Link
                </label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <span>{{ url('/') }}</span>
                    </div>
                    <input type="text" name="menu_url" class="form-control menu-url-view" disabled/>
                </div>
            </div>
            <div class="form-group">
                <label>
                    Type
                </label>
                <input type="text" name="menu_type" class="form-control menu-type-view" disabled/>
            </div>
            <div class="form-group">
                <label>
                    Permissions
                </label>
                <div class="well permissions-view">

                </div>
            </div>
          </div>
          <div>
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
                <button type="button" id="delete-menu" class="btn btn-danger btn-block"><i class="fa fa-trash"></i> Delete</button>
            @endif
            </div>
            <div class="col-md-4">
            @if(Session::get($active_module.'modify')||Session::get($active_module.'edit'))
                <button type="button" id="modify-menu" class="btn btn-primary btn-block"><i class="fa fa-edit"></i> Edit</button>
            @endif
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-gray view-cancel btn-block" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
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
    $(function(){
        var _serialized_array = '';
        var _activity = '';
        var _modal_create = $('#createModal');
        var _modal_notification = $('#notificationModal');
        var _modal_view = $('#viewModal');
        var _loading = $('.loading');
        var _message = $('.message');

        $("body").tooltip({selector: "[data-toggle='tooltip']"});

        $('#create-menu').on('click',function(){
            _activity = 'create';
            $('.modal-title').html("Create Menu");
            $('.menu-id').val('');
            $('.menu-name').val('Dashboard');
            $('.menu-link').val('');

            activeButtons();

            _loading.prop('hidden',true);
            _message.prop('hidden',true);

            _modal_create.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });

            permissionRecords();
        });

        $('#modify-menu').on('click',function(){
            var _data = $(this).attr('data-value');
            _activity = "modify";
            $('.modal-title').html("Modify Menu");

            activeButtons();

            $('.menu-id').val('');
            $('.menu-name').val('');
            $('.menu-link').val('');
            $('.menu-type').val('');
            $(".permissions option:selected").prop("selected", false);

            _modal_view.modal('hide');

            _modal_create.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });

            getData(_data,_activity);
        });

        $('#delete-menu').on('click',function(){
            var _data = $(this).attr('data-value');
            _serialized_array = _data;
            _activity = "delete";
            $('.modal-title').html("Validation Message");
            $('.modal-notification').html("Are you sure you want to remove this record?");
            _modal_view.modal('hide');

            _modal_notification.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });
        });

        $('.submit-form').on('click',function(){
            _modal_create.modal('hide');
            $('.modal-title').html('Validation Message');

            disableButtons();

            _modal_notification.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });

            if(_activity=="modify"){
              $('.modal-notification').html("Are you sure you want to update this record?");
            }else if(_activity=="create"){
              $('.modal-notification').html("Are you sure you want to create this record?");
            }else{
              alert("Invalid Call");
            }
        });

        $('.modal-cancel').on('click',function(){
            _loading.prop('hidden',true);
            _message.prop('hidden',true);

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

                    $('.modal-title').html("Menu Information");
                    break;
            }

        });

        $('.close-modal').on('click',function(){
            _loading.prop('hidden',true);
            _message.prop('hidden',true);

            switch(_activity){
                case "modify" :
                  var mid = $('.menu-id').val();
                  _modal_create.modal('hide');
                  _modal_view.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });
                  $('.modal-title').html("Menu Information");
                  getData(mid);
                  break;
                case "delete" :
                  _modal_create.modal('hide');
                  _modal_view.modal({
                      'backdrop': 'static',
                      'keyboard': false
                  });
                  $('.modal-title').html("Menu Information");
                  break;
                case "create" :
                  _modal_notification.modal('hide');
                  _modal_create.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });
                  $('.modal-title').html("Create Role");
                  break;
            }
        });

        $('#submit-form').on('click',function(){
            var mid = '';
            switch (_activity) {
              case "create":
                  var _url = '{{ route("menu.create") }}';
                  _serialized_array = $('#form-create-menu').serializeArray();

                break;
              case "modify":
                  var _url = '{{ route("menu.modify") }}';
                  _serialized_array = $('#form-create-menu').serializeArray();
                  mid = $('.menu-id').val();

                  _modal_notification.modal('hide');

                  _modal_create.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });

                  selectModalTitle(_activity);
                  getData(mid);
                break;
              case "delete":
                  var _url = '{{ route("menu.remove") }}';
                  mid = _serialized_array;

                  disableButtons();

                  _modal_notification.modal('hide');

                  _modal_view.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });

                  selectModalTitle(_activity);
                break;
              default:
                _serialized_array = "No Data";
                break;
            }

            var fdata = new FormData();

            fdata.append('_token','{{ csrf_token() }}');
            if(_activity=="delete"){
                fdata.append('_mid',mid);
            }else{
                fdata.append('_mid',$('.menu-id').val());
                fdata.append('menu_name',$('.menu-name').val());
                fdata.append('menu_type',$('.menu-type').val());
                fdata.append('menu_link',$('.menu-link').val());
                fdata.append('permissions',$('.permissions').val());
            }

            connectToServer(fdata,_url,_loading,_message,table,_activity);
        });

        $("#menuTable").on("click",".view-info",function(){
              var _data = $(this).attr('data-value');
              $('.message').prop('hidden',true);
              $('.loading').prop('hidden',true);
              $('.modal-title').html("Menu Information");

              _modal_view.modal({
                  'backdrop' : 'static',
                  'keyboard' : false
              });

              $('#modify-menu').attr('data-value',_data);
              $('#delete-menu').attr('data-value',_data);
              $('#modify-menu').prop('disabled',false);
              $('#delete-menu').prop('disabled',false);

              getData(_data,'');

              return false;
        });

        var table = $('#menuTable').DataTable({
          processing: true,
          searching:false,
          bLengthChange: false,
            pagingType: "input",
          serverSide: true,
          searchDelay: 500,
          "columnDefs": [ {
          "targets": [2,3,4,5],
          "orderable": false
          } ],
          ajax:{
              type : "POST",
              url : "{{ route('menu.list') }}",
              dataType : "json",
              data : function(d){
                return $.extend({}, d, {
                   _token:"{{ csrf_token() }}",
                   _search:$('#search').val(),
                   _radio_value : $('input[name=column_search]:checked').val()
                });
              }
          },
          columns: [
              { data : 'no', sClass: 'dt-body-right'},
              { data : 'menu_name'},
              { data : 'menu_link'},
              { data : 'menu_type'},
              { data : 'menu_permissions'},
              { data : 'activity', sClass: 'dt-btn-cell'},
          ]
        });

        $('.btn-search').on('click',function(){
            table.draw();
        });

        $('#search').keypress(function(event){
            if(event.keyCode==13){
                table.draw();
            }
        });
    });

    function permissionRecords(mid='')
    {
      $.ajax({
          type : 'POST',
          url : "{{ route('menu.permission.record') }}",
          dataType : 'json',
          data : {
              _token : '{{ csrf_token() }}',
              _mid : mid
          },
          success : function(data)
          {
              $('.permissions').html(data.html);
          }
      });
    }

    function connectToServer($data,$url,$loading,$message,$table,$activity)
    {
        $loading.prop('hidden',false);
        $message.prop('hidden',true);

        $.ajax({
            type : 'POST',
            url : $url,
            dataType : 'json',
            processData: false,
            contentType: false,
            data : $data,
            success : function(data)
            {
                var html = '';

                if(data.result==="success")
                {
                    if($activity=="create"){
                        var message = "<div class='alert alert-success text-success'>" +
                                "<button class='close' data-dismiss='modal'>&times;</button>" +
                                "<strong>" + data.message + "</strong></div>";

                        modalMessage($('#notificationModal'),message);
                    }else{
                        if($activity=="modify"){
                            activeButtons();
                        }
                        html += '<div class="alert alert-success">';
                        html += data.message;
                        html += '</div>';

                        callModal($loading,$message,$activity,html);
                    }

                    $table.draw();
                }
                else
                {
                    html += '<div class="alert alert-danger">';
                    html += data.message;
                    html += '</div>';

                    activeButtons();

                    callModal($loading,$message,$activity,html);
                }
            }
        });
    }

    function getData($mid,$activity,_table)
    {
        $.ajax({
            type:'POST',
            url:'{{ route("menu.data") }}',
            dataType:'json',
            data:{
              _token : '{{ csrf_token() }}',
              _mid : $mid
            },
            success:function(data)
            {
                if($activity==="modify")
                {
                    $('.menu-id').val(data.menu_id);
                    $('.menu-name').val(data.menu_name);
                    $('.menu-link').val(data.menu_link);
                    $('.menu-type').val(data.menu_type);
                    permissionRecords(data.menu_id)
                }
                else
                {
                    $('.menu-name-view').val(data.menu_name);
                    $('.menu-type-view').val(data.menu_type);
                    $('.menu-url-view').val(data.menu_link);
                    $('.permissions-view').html(data.permissions);
                    $('.created-by-view').html(data.created_by);
                    $('.updated-by-view').html(data.updated_by);
                    $('.date-created-view').html(data.created_date);
                    $('.date-updated-view').html(data.updated_date);
                }
            },
            error:function(ajaxHrs, status, error)
            {
                alert(error);
            }
        });
    }

    function callModal($loading,$message,$activity,$html)
    {
        $('#notificationModal').modal('hide');

        if($activity=="delete"){
            $('#modify-menu').prop('disabled',true);
            $('#delete-menu').prop('disabled',true);
        }else{
            $('#createModal').modal({
                'backdrop' : 'static',
                'keyboard' : false
            });
        }

        $loading.prop('hidden',true);
        $message.prop('hidden',false);
        $message.html($html);
    }

    function modalMessage($modal='',$message=''){

        if($modal!=''){
            $modal.modal('hide');
        }

        if($message!=''){
            $('#mdl_message').modal('show');
            $('#dialog_message').html($message);
        }else{
            $('#mdl_message').modal('hide');
        }
    }

    function selectModalTitle($activity)
    {
        switch ($activity) {
          case "create":
            $(".modal-title").html("Create Menu");
            break;
          case "modify":
            $(".modal-title").html("Modify Menu");
            break;
          case "delete":
            $(".modal-title").html("Menu Information");
            break;
          default:

        }
    }

    function disableButtons()
    {
        var disabled_button = 'disabled';
        $('.submit-form').prop(disabled_button,true);
        $('#modify-menu').prop(disabled_button,true);
        $('#delete-menu').prop(disabled_button,true);
    }

    function activeButtons()
    {
        var disabled_button = 'disabled';
        $('.submit-form').prop(disabled_button,false);
        $('#modify-menu').prop(disabled_button,false);
        $('#delete-menu').prop(disabled_button,false);
    }
  </script>
@endpush
