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
                            <input type="radio" name="column_search" value="1" checked /> Role
                        </label>
                    </div>
                </div>
                @if(Session::get($active_module.'create'))
                <div class="form-inline pt-3 text-right">
                    <button class="btn btn-primary" id="create-role"><i class="fa fa-plus"></i> Create Role</button>
                </div>
                @endif

            </div>
        </div>

    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <table id="roleTable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                    <tr class="bg-primary">
                        <th>ID No.</th>
                        <th>Role</th>
                        <th>Menu</th>
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
          <form id="form-create-role">
            <input type="hidden" name="_rid" class="form-control role-id" />
            <div class="form-group">
                <label>
                    Name
                </label>
                <input type="text" name="role_name" class="form-control role-name" />
            </div>
            <div class="form-group">
                <label>
                    Type
                </label>
                <select class="form-control menu-type" name="menu_type">

                    <?php echo $menu_type; ?>
                </select>
            </div>
            <div class="form-group select-menus" hidden>
                <label>
                    Menu
                </label>
                <select class="form-control menus" name="menus" multiple>
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
        <h5 class="modal-title" id="notification-header">Message Box</h5>
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
        <h5 class="modal-title" id="exampleModalLabel">Role Information</h5>
      </div>
      <div class="modal-body">
          <div class="message-container" align="center">
              <div class="message" hidden></div>
              <div class="loading" hidden>
                  <div class="loader"></div>
                  Please wait. . .<hr>
              </div>
          </div>
          <div class="">
            <div class="form-group">
                <label>
                    Name
                </label>
                <input type="text" name="role_name" class="form-control role-name-view" disabled/>
            </div>
            <div class="form-group">
                <label>
                    Type
                </label>
                <div class="well menu-type-view">

                </div>
            </div>
            <div class="form-group">
                <label>
                    Menu
                </label>
                <div class="well menus-view">

                </div>
            </div>
          </div>
          <div >
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
                <button type="button" id="delete-role" class="btn btn-danger btn-block"><i class="fa fa-trash"></i> Delete</button>
            @endif
            </div>
            <div class="col-md-4">
            @if(Session::get($active_module.'modify')||Session::get($active_module.'edit'))
                <button type="button" id="modify-role" class="btn btn-primary btn-block"><i class="fa fa-edit"></i> Edit</button>
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

        $('#create-role').on('click',function(){
            _activity = 'create';
            $('.modal-title').html("Create Role");
            $('.role-id').val('');
            $('.role-name').val('');
            $('.menus').val(0);

            activeButtons();

            _loading.prop('hidden',true);
            _message.prop('hidden',true);

            _modal_create.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });
        });

        $('.menu-type').on('change',function(e){
          e.preventDefault();
            var _text = $('.menu-type option:selected').text();

            showMenus(_text);
        });

        $('#modify-role').on('click',function(){
            var _data = $(this).attr('data-value');
            _activity = "modify";
            $('.modal-title').html("Modify Role");

            activeButtons();

            $('.role-id').val('');
            $('.role-name').val('');
            $(".menus option:selected").prop("selected", false);

            _modal_view.modal('hide');

            _modal_create.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });

            getData(_data,_activity);
        });

        $('#delete-role').on('click',function(){
            var _data = $(this).attr('data-value');
            _serialized_array = _data;
            _activity = "delete";

            $('.modal-notification').html("Are you sure you want to remove this record?");
            _modal_view.modal('hide');

            _modal_notification.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });

            $('#notification-header').html("Validation Message");
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

                    $('.modal-title').html("Role Information");
                    break;
            }

        });

        $('.close-modal').on('click',function(){
            _loading.prop('hidden',true);
            _message.prop('hidden',true);

            switch(_activity){
                case "modify" :
                  var rid = $('.role-id').val();
                  _modal_create.modal('hide');
                  _modal_view.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });
                  $('.modal-title').html("Role Information");
                  getData(rid);
                  break;
                case "delete" :
                  _modal_create.modal('hide');
                  _modal_view.modal({
                      'backdrop': 'static',
                      'keyboard': false
                  });
                  $('.modal-title').html("Role Information");
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
            var rid = '';
            switch (_activity) {
              case "create":
                  var _url = '{{ route("role.create") }}';
                  _serialized_array = $('#form-create-role').serializeArray();

                break;
              case "modify":
                  var _url = '{{ route("role.modify") }}';
                  _serialized_array = $('#form-create-role').serializeArray();
                  rid = $('.role-id').val();

                  _modal_notification.modal('hide');

                  _modal_create.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });

                  selectModalTitle(_activity);
                  getData(rid);
                break;
              case "delete":
                  var _url = '{{ route("role.remove") }}';
                  rid = _serialized_array;

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
                fdata.append('_rid',rid);
            }else{
                fdata.append('_rid',$('.role-id').val());
                fdata.append('role_name',$('.role-name').val());
                fdata.append('menu_type',$('.menu-type').val());

                if($('.menus').val()!==null){
                    fdata.append('menus',$('.menus').val());
                }else{
                    fdata.append('menus',"");
                }

            }

            connectToServer(fdata,_url,_loading,_message,table,_activity);
        });

        $("#roleTable").on("click",".view-info",function(){
              var _data = $(this).attr('data-value');
              $('.message').prop('hidden',true);
              $('.loading').prop('hidden',true);
              $('.modal-title').html("Role Information");

              _modal_view.modal({
                  'backdrop' : 'static',
                  'keyboard' : false
              });

              $('#modify-role').attr('data-value',_data);
              $('#delete-role').attr('data-value',_data);
              $('#modify-role').prop('disabled',false);
              $('#delete-role').prop('disabled',false);

              getData(_data,'');

              return false;
        });

        var table = $('#roleTable').DataTable({
          processing: true,
          searching:false,
          bLengthChange: false,
          serverSide: true,
          searchDelay: 500,
            pagingType: "input",
          "columnDefs": [ {
          "targets": [2,3],
          "orderable": false
          } ],
          ajax:{
              type : "POST",
              url : "{{ route('role.list') }}",
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
              { data : 'no', sClass: 'dt-body-right dt-head-nowrap'},
              { data : 'role_name', sClass: 'dt-head-nowrap'},
              { data : 'role_menus'},
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

    function callModal($loading,$message,$activity,$html)
    {
        $('#notificationModal').modal('hide');

        if($activity=="delete"){
            $('#modify-role').prop('disabled',true);
            $('#delete-role').prop('disabled',true);
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

    function getData($rid,$activity)
    {
        $.ajax({
            type:'POST',
            url:'{{ route("role.data") }}',
            dataType:'json',
            data:{
              _token : '{{ csrf_token() }}',
              _rid : $rid
            },
            success:function(data)
            {
                if($activity==="modify")
                {
                  $('.role-id').val(data.role_id);
                  $('.role-name').val(data.role_name);
                  $('.menu-type').val(data.menu_type);
                  showMenus(data.menu_type,data.role_id);
                }
                else
                {
                  $('.role-name-view').val(data.role_name);
                  $('.menu-type-view').html(data.menu_type);
                  $('.menus-view').html(data.menus);
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

    function showMenus(type,mid='')
    {
        var response = false;
        $.ajax({
            type:'POST',
            url :'{{ route("menus.active.list") }}',
            dataType:'json',
            data:{
                _token : '{{ csrf_token() }}',
                _type : type,
                _mid : mid
            },
            success:function(data)
            {
                if(data.result==="success"){
                    $('.select-menus').prop('hidden',false);
                }else{
                    $('.select-menus').prop('hidden',true);
                }
                $('.menus').html(data.html);
            },
            error:function(ajaxHrs,status,error)
            {
                alert(error);
            }
        });

        return response;
    }

    function selectModalTitle($activity)
    {
        switch ($activity) {
          case "create":
            $(".modal-title").html("Create Role");
            break;
          case "modify":
            $(".modal-title").html("Modify Role");
            break;
          case "delete":
            $(".modal-title").html("Role Information");
            break;
          default:

        }
    }

    function disableButtons()
    {
        var disabled_button = 'disabled';
        $('.submit-form').prop(disabled_button,true);
        $('#modify-role').prop(disabled_button,true);
        $('#delete-role').prop(disabled_button,true);
    }

    function activeButtons()
    {
        var disabled_button = 'disabled';
        $('.submit-form').prop(disabled_button,false);
        $('#modify-role').prop(disabled_button,false);
        $('#delete-role').prop(disabled_button,false);
    }
  </script>
@endpush
