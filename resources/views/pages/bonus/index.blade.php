@extends('layouts.app')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <table id="chartBoard" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                    <tr class="bg-primary">
                        <th style="text-align:center;">Level/Membership</th>
                        <th style="text-align:center;">1</th>
                        <th style="text-align:center;">2</th>
                        <th style="text-align:center;">3</th>
                    </tr>
                    </thead>
                    <tbody class="boardBody">
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
          <form id="form-create-bonus">
            <input type="hidden" name="_bid" class="form-control bonus-id" />
            <div class="form-group">
                <label>
                    Level
                </label>
                <div class="well bonus-level">
                </div>
            </div>
            <div class="form-group">
                <label>
                    Percentage
                </label>
                <input type="text" name="bonus_name" class="form-control bonus-percentage" />
            </div>
            <div class="form-group">
                <label>
                    Membership
                </label>
                <div class="well membership-view"></div>
            </div>
            <div class="form-group">
                <label>
                    Description
                </label>
                <textarea name="bonus_desc" class="form-control bonus-desc"></textarea>
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
        <h5 class="modal-title" id="exampleModalLabel">Bonus Information</h5>
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
                      Level
                  </label>
                  <div class="well bonus-level-view"></div>
              </div>
              <div class="form-group">
                  <label>
                      Percentage
                  </label>
                  <div class="well bonus-percentage-view"></div>
              </div>
              <div class="form-group">
                  <label>
                      Membership
                  </label>
                  <div class="well membership-view"></div>
              </div>
              <div class="form-group">
                  <label>
                      Description
                  </label>
                  <textarea name="bonus_desc" class="form-control bonus-desc-view" disabled></textarea>
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
            <div class="col-md-6">
            @if(Session::get($active_module.'modify')||Session::get($active_module.'edit'))
                <button type="button" id="modify-bonus" class="btn btn-primary btn-block"><i class="fa fa-edit"></i> Edit</button>
            @endif
            </div>
            <div class="col-md-6">
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

        reloadChart(_modal_view);

        $('.bonus-percentage').on("input",function(event){
           var self = $(this);
           self.val(self.val().replace(/[^0-9\.]/g, ''));
           if ((event.which != 46 || self.val().indexOf('.') != -1) && (event.which < 48 || event.which > 57))
           {
             event.preventDefault();
           }

           var array_number = self.val().split(".");
           if(!jQuery.isEmptyObject(array_number[1]))
           {
             if(array_number[1].length>2)
             {
               self.val($.trim(self.val()).slice(0, -1));
             }
           }
        });

        $('#create-bonus').on('click',function(){
            _activity = 'create';
            $('.modal-title').html("Create Form");
            $('.bonus-id').val('');
            $('.bonus-level').val(0);
            $('.bonus-percentage').val('');
            $('.bonus-desc').val('');

            activeButtons();

            _loading.prop('hidden',true);
            _message.prop('hidden',true);

            _modal_create.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });
        });

        $('#modify-bonus').on('click',function(){
            var _data = $(this).attr('data-value');
            _activity = "modify";
            $('.modal-title').html("Modify Bonus");

            activeButtons();

            $('.bonus-id').val('');
            $('.bonus-level').val(0);
            $('.bonus-percentage').val('');
            $('.bonus-desc').val('');

            _modal_view.modal('hide');

            _modal_create.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });

            getData(_data,_activity);
        });

        $('#delete-bonus').on('click',function(){
            var _data = $(this).attr('data-value');
            _serialized_array = _data;
            _activity = "delete";

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
                      'backdrop' : 'static',
                      'keyboard' : false
                  });
                  $('.modal-title').html("Modify Bonus");
                  break;
              default:
                  _modal_create.modal('hide');
                  _modal_view.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });

                  $('.modal-title').html("Bonus Information");

                  break;
            }
        });

        $('.close-modal').on('click',function(){
            _loading.prop('hidden',true);
            _message.prop('hidden',true);

            switch(_activity){
              case "modify" :
                  var bid = $('.bonus-id').val();
                  _modal_create.modal('hide');
                  _modal_view.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });
                  $('.modal-title').html("Bonus Information");

                  getData(bid);
                  break;
              case "delete" :
                  _modal_create.modal('hide');
                  _modal_view.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });

                  $('.modal-title').html("Bonus Information");
                  break;
            }

        });

        $('#submit-form').on('click',function(){
            var bid = '';
            switch (_activity) {
              case "create":
                  var _url = '{{ route("bonus.create") }}';
                  _serialized_array = $('#form-create-bonus').serializeArray();

                break;
              case "modify":
                  var _url = '{{ route("bonus.modify") }}';
                  _serialized_array = $('#form-create-bonus').serializeArray();
                  bid = $('.bonus-id').val();

                  _modal_notification.modal('hide');

                  _modal_create.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });

                  selectModalTitle(_activity);
                break;
              case "delete":
                  var _url = '{{ route("bonus.remove") }}';
                  bid = _serialized_array;

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
                fdata.append('_bid',bid);
            }else{
                $.each(_serialized_array,function(i,fields){
                    fdata.append(fields.name,fields.value);
                });
            }

            connectToServer(fdata,_url,_loading,_message,table,_activity,_modal_view);
        });

        var table = $('#bonusTable').DataTable({
          processing: true,
          searching:false,
          bLengthChange: false,
          serverSide: true,
          searchDelay: 500,
          pagingType: "input",
          "columnDefs": [ {
          "targets": [5],
          "orderable": false
          } ],
          ajax:{
              type : "POST",
              url : "{{ route('bonus.list') }}",
              dataType : "json",
              data : function(d){
                return $.extend({}, d, {
                   _token:"{{ csrf_token() }}",
                   _search:$('#search').val(),
                   _radio_value : $('input[name=column_search]:checked').val(),
                   _category : $('.category').val()
                });
              }
          },
          columns: [
              { data : 'no', sClass: 'dt-body-right'},
              { data : 'bonus_level',sClass: 'dt-body-right'},
              { data : 'bonus_name',sClass: 'dt-body-right'},
              { data : 'membership_type'},
              { data : 'bonus_desc'},
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

    function reloadChart(_modal_view)
    {
        $.ajax({
          type : 'GET',
          url : '{{ route("board.record") }}',
          data : {
            _token : "{{ csrf_token() }}"
          },
          success:function(data){
            $('#chartBoard .boardBody').html(data);
            callViewModal(_modal_view);
          }
        });
    }

    function callViewModal(_modal_view)
    {
      $(".view-info").on("click",function(){
            var _data = $(this).attr('data-value');
            $('.message').prop('hidden',true);
            $('.loading').prop('hidden',true);
            $('.modal-title').html("Bonus Information");

            _modal_view.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });

            $('#modify-bonus').attr('data-value',_data);
            $('#delete-bonus').attr('data-value',_data);
            $('#modify-bonus').prop('disabled',false);
            $('#delete-bonus').prop('disabled',false);

            getData(_data,'');

            return false;
      });
    }

    function connectToServer($data,$url,$loading,$message,$table,$activity,_modal_view)
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

                    reloadChart(_modal_view);
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

    function getData($bid,$activity)
    {
        $.ajax({
            type:'POST',
            url:'{{ route("bonus.data") }}',
            dataType:'json',
            data:{
              _token : '{{ csrf_token() }}',
              _bid : $bid
            },
            success:function(data)
            {
                if($activity==="modify")
                {
                  $('.bonus-id').val(data.bonus_id);
                  $('.bonus-level').text(data.bonus_level);
                  $('.bonus-percentage').val(data.bonus_name);
                  $('.bonus-desc').val(data.bonus_desc);
                  $('.membership-view').text(data.membership);
                }
                else
                {
                  $('.bonus-level-view').text(data.bonus_level);
                  $('.bonus-percentage-view').text(data.bonus_name);
                  $('.bonus-desc-view').val(data.bonus_desc);
                  $('.membership-view').text(data.membership);
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
            $('#modify-bonus').prop('disabled',true);
            $('#delete-bonus').prop('disabled',true);
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
            $(".modal-title").html("Create Bonus");
            break;
          case "modify":
            $(".modal-title").html("Modify Bonus");
            break;
          case "delete":
            $(".modal-title").html("Bonus Information");
            break;
          default:

        }
    }

    function disableButtons()
    {
        var disabled_button = 'disabled';
        $('.submit-form').prop(disabled_button,true);
        $('#modify-bonus').prop(disabled_button,true);
        $('#delete-bonus').prop(disabled_button,true);
    }

    function activeButtons()
    {
        var disabled_button = 'disabled';
        $('.submit-form').prop(disabled_button,false);
        $('#modify-bonus').prop(disabled_button,false);
        $('#delete-bonus').prop(disabled_button,false);
    }
  </script>
@endpush
