@extends('layouts.app')
@push('styles')
<style>
.tree {
    min-height:20px;
    padding:19px;
    margin-bottom:20px;
    background-color:#fbfbfb;
    border:1px solid #999;
    -webkit-border-radius:4px;
    -moz-border-radius:4px;
    border-radius:4px;
    -webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.05);
    -moz-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.05);
    box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.05)
}
.tree li {
    list-style-type:none;
    margin:0;
    padding:10px 5px 0 5px;
    position:relative
}
.tree li::before, .tree li::after {
    content:'';
    left:-20px;
    position:absolute;
    right:auto
}
.tree li::before {
    border-left:1px solid #999;
    bottom:50px;
    height:100%;
    top:0;
    width:1px
}
.tree li::after {
    border-top:1px solid #999;
    height:20px;
    top:25px;
    width:25px
}
.tree li span {
    -moz-border-radius:5px;
    -webkit-border-radius:5px;
    border:1px solid #999;
    border-radius:5px;
    display:inline-block;
    padding:3px 8px;
    text-decoration:none
}
.tree li.parent_li>span {
    cursor:pointer
}
.tree>ul>li::before, .tree>ul>li::after {
    border:0
}
.tree li:last-child::before {
    height:30px
}
.tree li.parent_li>span:hover, .tree li.parent_li>span:hover+ul li span {
    background:#eee;
    border:1px solid #94a0b4;
    color:#000
}
</style>
@endpush
@section('content')
<!-- <div class="container"> -->
    <div class="row">
        <div class="col-md-4">
          <div class="tree well">
              <?php echo $tree; ?>
          </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-body">

                </div>
            </div>
        </div>
    </div>
<!-- </div> -->

<!-- Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Create Form</h4>
      </div>
      <div class="modal-body">
          <div class="message-container" align="center">
              <div class="message" hidden></div>
              <div class="loading" hidden>
                  <div class="loader"></div>
                  Please wait. Loading like crazy. . .<hr>
              </div>
          </div>
          <form id="form-create-bonus">
            <input type="hidden" name="_bid" class="form-control bonus-id" />
            <div class="form-group">
                <label>
                    Bonus Level
                </label>
                <input type="text" name="bonus_level" class="form-control bonus-level" />
            </div>
            <div class="form-group">
                <label>
                    Bonus Name
                </label>
                <input type="text" name="bonus_name" class="form-control bonus-name" />
            </div>
            <div class="form-group">
                <label>
                    Bonus Description
                </label>
                <textarea name="bonus_desc" class="form-control bonus-desc"></textarea>
            </div>
          </form>
      </div>
      <div class="modal-footer">
          <div class="row">
              <div class="col-md-6">
                  <button type="button" class="btn btn-secondary btn-block close-modal" data-dismiss="modal">Close</button>
              </div>
              <div class="col-md-6">
                  <button type="button" class="btn btn-primary btn-block submit-form">Submit</button>
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
          <h4>Are you sure you want to create this record?</h4>
      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-md-6">
              <button type="button" class="btn btn-secondary modal-cancel btn-block" data-dismiss="modal">Cancel</button>
          </div>
          <div class="col-md-6">
              <button type="button" id="submit-form" class="btn btn-primary btn-block">Submit</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">View Details</h5>
      </div>
      <div class="modal-body">
          <div class="message-container" align="center">
              <div class="message" hidden></div>
              <div class="loading" hidden>
                  <div class="loader"></div>
                  Please wait. Loading like crazy. . .<hr>
              </div>
          </div>
        <div class="form-group">
            <label>
                Bonus Level
            </label>
            <input type="text" name="bonus_level" class="form-control bonus-level-view" disabled/>
        </div>
        <div class="form-group">
            <label>
                Bonus Name
            </label>
            <input type="text" name="bonus_name" class="form-control bonus-name-view" disabled/>
        </div>
        <div class="form-group">
            <label>
                Bonus Description
            </label>
            <textarea name="bonus_desc" class="form-control bonus-desc-view" disabled></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <div class="row">
            <div class="col-md-4">
                <button type="button" id="delete-bonus" class="btn btn-danger btn-block">Delete</button>
            </div>
            <div class="col-md-4">
                <button type="button" id="modify-bonus" class="btn btn-primary btn-block">Modify</button>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-secondary view-cancel btn-block" data-dismiss="modal">Cancel</button>
            </div>
        </div>
      </div>
    </div>
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

        $('#create-bonus').on('click',function(){
            _activity = 'create';
            $('.modal-title').html("Create Form");
            $('.bonus-id').val('');
            $('.bonus-level').val('');
            $('.bonus-name').val('');
            $('.bonus-desc').val('');

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
            $('.modal-title').html("Modify Form");

            $('.bonus-id').val('');
            $('.bonus-level').val('');
            $('.bonus-name').val('');
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

            $('.modal-notification').html("<h4>Are you sure you want to remove this record?</h4>");
            _modal_view.modal('hide');

            _modal_notification.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });
        });

        $('.submit-form').on('click',function(){
            _modal_create.modal('hide');

            _modal_notification.modal({
                'backdrop' : 'static',
                'keyboard' : false
            });

            if(_activity=="modify"){
              $('.modal-notification').html("<h4>Are you sure you want to update this record?</h4>");
            }else if(_activity=="create"){
              $('.modal-notification').html("<h4>Are you sure you want to create this record?</h4>");
            }else{
              alert("Invalid Call");
            }
        });

        $('.modal-cancel').on('click',function(){
            _loading.prop('hidden',true);
            _message.prop('hidden',true);

            if((_activity=="delete")){
                _modal_notification.modal('hide');
                _modal_view.modal({
                    'backdrop' : 'static',
                    'keyboard' : false
                });
            }else{
                _modal_notification.modal('hide');
                _modal_create.modal({
                    'backdrop' : 'static',
                    'keyboard' : false
                });
            }

        });

        $('.close-modal').on('click',function(){
            _loading.prop('hidden',true);
            _message.prop('hidden',true);

            if(_activity=="modify"){
                _modal_create.modal('hide');
                _modal_view.modal({
                    'backdrop' : 'static',
                    'keyboard' : false
                });
                $('.modal-title').html("<h4>View Details</h4>");
            }else if(_activity=="create"){
                _modal_notification.modal('hide');
                _modal_create.modal({
                    'backdrop' : 'static',
                    'keyboard' : false
                });
                $('.modal-title').html("<h4>Create Form</h4>");
            }
        });

        $('#submit-form').on('click',function(){
            var bid = '';
            switch (_activity) {
              case "create":
                  var _url = '{{ route("bonus.create") }}';
                  _serialized_array = $('#form-create-bonus').serializeArray();
                  _modal_notification.modal('hide');

                  _modal_create.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });

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
                break;
              case "delete":
                  var _url = '{{ route("bonus.remove") }}';
                  bid = _serialized_array;

                  _modal_notification.modal('hide');

                  _modal_view.modal({
                      'backdrop' : 'static',
                      'keyboard' : false
                  });
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

            connectToServer(fdata,_url,_loading,_message,table,_activity);
        });

        $("#bonusTable").on("click",".view-info",function(){
              var _data = $(this).attr('data-value');

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

        var table = $('#bonusTable').DataTable({
          processing: true,
          searching:false,
          bLengthChange: false,
          serverSide: true,
          searchDelay: 500,
          ajax:{
              type : "POST",
              url : "{{ route('bonus.list') }}",
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
              { data : 'no', sClass: 'text-right'},
              { data : 'bonus_level'},
              { data : 'bonus_name'},
              { data : 'bonus_desc'},
              { data : 'activity', sClass: 'text-center'},
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
                    html += '<div class="alert alert-success">';
                    html += data.message;
                    html += '</div>';
                    if($activity=="delete"){
                        $('#modify-bonus').prop('disabled',true);
                        $('#delete-bonus').prop('disabled',true);
                    }
                    $table.draw();
                }
                else
                {
                    html += '<div class="alert alert-danger">';
                    html += data.message;
                    html += '</div>';
                }

                $loading.prop('hidden',true);
                $message.prop('hidden',false);
                $message.html(html);
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
                  $('.bonus-level').val(data.bonus_level);
                  $('.bonus-name').val(data.bonus_name);
                  $('.bonus-desc').val(data.bonus_desc);
                }
                else
                {
                  $('.bonus-level-view').val(data.bonus_level);
                  $('.bonus-name-view').val(data.bonus_name);
                  $('.bonus-desc-view').val(data.bonus_desc);
                }
            },
            error:function(ajaxHrs, status, error)
            {
                alert(error);
            }
        });
    }
  </script>
  <script>
    $(function () {
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
    });
  </script>
@endpush
