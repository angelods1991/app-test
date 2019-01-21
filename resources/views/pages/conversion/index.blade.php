@extends('layouts.app')
@push('styles')
<style>
    .loader {
      border: 8px solid #f3f3f3;
      border-radius: 50%;
      border-top: 8px solid rgb(52, 152, 219);
      border-bottom: 8px solid rgb(52, 152, 219);
      width: 15px;
      height: 15px;
      -webkit-animation: spin 2s linear infinite;
      animation: spin 2s linear infinite;
    }

    @-webkit-keyframes spin {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
</style>
@endpush
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
                              <input type="radio" name="column_search" value="1" checked /> Country Name
                        </label>
                    </div>
                </div>
                @if(Session::get($active_module.'create'))
                  <div class="form-inline pt-3 text-right">
                      <button class="btn btn-primary" id="create-conversion"><i class="fa fa-plus"></i> Create Conversion</button>
                  </div>
                @endif
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table id="conversionTable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                      <tr class="bg-primary">
                          <th>ID No.</th>
                          <th>Country Name</th>
                          <th>Currency</th>
                          <th>Value</th>
                          <th>EDPoint Rate</th>
                          <th></th>
                      </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="6" align="center">No data found!</td>
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
        <h4 class="modal-title" id="exampleModalLabel">Create Conversion</h4>
      </div>
      <div class="modal-body">
          <div class="message-container" align="center">
              <div class="message" hidden></div>
              <div class="loading" hidden>
                  <div class="loader"></div>
                  Please wait. . .<hr>
              </div>
          </div>
          <form id="form-create-conversion-setup">
            <input type="hidden" name="_sid" class="form-control setup-id" />
            <div class="form-group">
                <label>
                    Country Name
                </label>

                <select class="form-control country-name" name="country_name">
                  <?php echo $options; ?>
                </select>
            </div>
            <div class="form-group">
                  <label>
                      Currency
                  </label>
                  <div class="input-group">
                      <div class="input-group-addon">
                          <span class="currency-level">PHP</span>
                      </div>
                      <input type="text" min="0" name="currency_value" class="form-control currency-value" />
                      <div class="input-group-addon">
                          <span>EDPoint</span>
                      </div>
                      <input type="text" min="0" name="edpoint_value" class="form-control edpoint-value" />
                  </div>
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
        <h5 class="modal-title" id="exampleModalLabel">Conversion Information</h5>
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
            <input type="hidden" name="_sid" class="form-control setup-id" />
            <div class="form-group">
                <label>
                    Country Name
                </label>

                <select disabled class="form-control country-name-view" name="country_name">
                  <?php echo $options; ?>
                </select>
            </div>
            <div class="form-group">
                  <label>
                      Currency
                  </label>
                  <div class="input-group">
                      <div class="input-group-addon">
                          <span class="currency-level-view">PHP</span>
                      </div>
                      <input type="text" min="0" disabled name="currency_value" class="form-control currency-value-view" />
                      <div class="input-group-addon">
                          <span>EDPoint</span>
                      </div>
                      <input type="text" disabled min="0" name="edpoint_value" class="form-control edpoint-value-view" />
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
                <button type="button" id="delete-conversion" class="btn btn-danger btn-block"><i class="fa fa-trash"></i> Delete</button>
              @endif
            </div>
            <div class="col-md-4">
              @if(Session::get($active_module.'modify')||Session::get($active_module.'edit'))
                <button type="button" id="modify-conversion" class="btn btn-primary btn-block"><i class="fa fa-edit"></i> Edit</button>
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

        $('#create-conversion').on('click',function(){
            _activity = 'create';
            $('.modal-title').html("Create Conversion");
            $('.setup-id').val('');
            $('.country-name').val('Dashboard');
            $('.currency-code').val('');
            $('.country-name').val('PHP');
            $('.currency-value').val(0);
            $('.edpoint-value').val(0);

            var currency_value = $('.currency-value').val();

            activeButtons();

            _loading.prop('hidden',true);
            _message.prop('hidden',true);

            _modal_create.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });
        });

        $('.edpoint-value').on("input",function(event){
            checkTextInput($(this),event);
        });

        $('.currency-value').on("input",function(event){
            var currency_code = $('.country-name').val();
            var currency_value = $(this).val();

            $('.currency-value-label').text(currency_value);

            checkTextInput($(this),event);
        });

        $('#modify-conversion').on('click',function(){
            var _data = $(this).attr('data-value');
            _activity = "modify";
            $('.modal-title').html("Modify Conversion");

            activeButtons();

            $('.setup-id').val('');
            $('.country-name').val('');
            $('.currency-code').val('');
            $('.edpoint-value').val('');
            _modal_view.modal('hide');

            _modal_create.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });

            getData(_data,_activity);
        });

        $('#delete-conversion').on('click',function(){
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
                    activeButtons();
                    break;
                case "delete" :
                    _modal_notification.modal('hide');
                    _modal_view.modal({
                        'backdrop': 'static',
                        'keyboard': false
                    });

                    $('.modal-title').html("Conversion Information");
                    break;
            }

        });

        $('.close-modal').on('click',function(){
            _loading.prop('hidden',true);
            _message.prop('hidden',true);

            switch(_activity){
                case "modify" :
                  var sid = $('.setup-id').val();
                  _modal_create.modal('hide');
                  _modal_view.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });
                  $('.modal-title').html("Conversion Information");
                  getData(sid);
                  break;
                case "delete" :
                  _modal_create.modal('hide');
                  _modal_view.modal({
                      'backdrop': 'static',
                      'keyboard': false
                  });
                  $('.modal-title').html("Conversion Information");
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
            var sid = '';
            switch (_activity) {
              case "create":
                  var _url = '{{ route("conversion.create") }}';
                  _serialized_array = $('#form-create-conversion-setup').serializeArray();

                break;
              case "modify":
                  var _url = '{{ route("conversion.modify") }}';
                  _serialized_array = $('#form-create-conversion-setup').serializeArray();
                  sid = $('.setup-id').val();

                  _modal_notification.modal('hide');

                  _modal_create.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });

                  selectModalTitle(_activity);

                  getData(sid);
                break;
              case "delete":
                  var _url = '{{ route("conversion.remove") }}';
                  sid = _serialized_array;

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
                fdata.append('_sid',sid);
            }else{
                fdata.append('_sid',$('.setup-id').val());
                fdata.append('country_name',$('.country-name :selected').attr('data-value'));
                fdata.append('currency_value',$('.currency-value').val());
                fdata.append('edpoint_value',$('.edpoint-value').val());
                fdata.append('convert_rate',$('.convert-rate').val());
            }

            connectToServer(fdata,_url,_loading,_message,table,_activity);
        });

        $("#conversionTable").on("click",".view-info",function(){
              var _data = $(this).attr('data-value');
              $('.message').prop('hidden',true);
              $('.loading').prop('hidden',true);
              $('.modal-title').html("Conversion Information");

              _modal_view.modal({
                  'backdrop' : 'static',
                  'keyboard' : false
              });

              $('#modify-conversion').attr('data-value',_data);
              $('#delete-conversion').attr('data-value',_data);
              $('#modify-conversion').prop('disabled',false);
              $('#delete-conversion').prop('disabled',false);

              getData(_data,'');

              return false;
        });

        var table = $('#conversionTable').DataTable({
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
              url : "{{ route('conversion.activity.list') }}",
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
              { data : 'company_name'},
              { data : 'company_code'},
              { data : 'currency_value', sClass: 'dt-body-right'},
              { data : 'edpoint_value', sClass: 'dt-body-right'},
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

        $('.country-name').on('change',function(){
            var currency_code = $(this).val();
            var currency_value = $('.currency-value').val();

            $('.currency-level').text(currency_code);
        });
    });

    function checkTextInput($this,$event)
    {
        var self = $this;
        self.val(self.val().replace(/[^0-9\.]/g, ''));
        if (($event.which != 46 || self.val().indexOf('.') != -1) && ($event.which < 48 || $event.which > 57))
        {
          $event.preventDefault();
        }

        var array_number = self.val().split(".");
        if(!jQuery.isEmptyObject(array_number[1]))
        {
          if(array_number[1].length>2)
          {
            self.val($.trim(self.val()).slice(0, -1));
          }
        }
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

    function getData($sid,$activity,_table)
    {
        $.ajax({
            type:'POST',
            url:'{{ route("conversion.edpoint") }}',
            dataType:'json',
            data:{
              _token : '{{ csrf_token() }}',
              _sid : $sid
            },
            success:function(data)
            {
                $('.setup-id').val(data.setup_id);
                if($activity==="modify")
                {
                    $('.country-name').val(data.company_code);
                    $('.currency-value').val(data.currency_value);
                    $('.edpoint-value').val(data.edpoint_value);
                }
                else
                {
                  $('.currency-level-view').text(data.currency_code);
                  $('.country-name-view').val(data.company_code);
                  $('.currency-value-view').val(data.currency_value);
                  $('.edpoint-value-view').val(data.edpoint_value);
                  $('.created-by-view').text(data.created_by);
                  $('.date-created-view').text(data.created_date);
                  $('.updated-by-view').text(data.updated_by);
                  $('.date-updated-view').text(data.updated_date);
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
            $('#modify-conversion').prop('disabled',true);
            $('#delete-conversion').prop('disabled',true);
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
            $(".modal-title").html("Create Conversion");
            break;
          case "modify":
            $(".modal-title").html("Modify Conversion");
            break;
          case "delete":
            $(".modal-title").html("Conversion Information");
            break;
          default:

        }
    }

    function disableButtons()
    {
        var disabled_button = 'disabled';
        $('.submit-form').prop(disabled_button,true);
        $('#modify-conversion').prop(disabled_button,true);
        $('#delete-conversion').prop(disabled_button,true);
    }

    function activeButtons()
    {
        var disabled_button = 'disabled';
        $('.submit-form').prop(disabled_button,false);
        $('#modify-conversion').prop(disabled_button,false);
        $('#delete-conversion').prop(disabled_button,false);
    }
  </script>
@endpush
