<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Edmark Backend') }}</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/daterangepicker.min.css') }}" rel="stylesheet">
    <link rel="icon" href="{{asset('img/favicon.png')}}">

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
    @stack('styles')
</head>
<body class="cm-no-transition cm-1-navbar">
<div id="cm-menu">
    <nav class="cm-navbar cm-navbar-primary">
        <div class="cm-flex">
            <!-- <div class="cm-logo"></div> -->
        </div>
        <div class="btn btn-primary md-menu-white" data-toggle="cm-menu"></div>
    </nav>
    <div id="cm-menu-content">
        <div id="cm-menu-items-wrapper">
            <div id="cm-menu-scroller" class="sidebar-nav">
                @include('inc.sidebar')
            </div>
        </div>
    </div>
</div>
<header id="cm-header">
    <nav class="cm-navbar cm-navbar-primary">
        <div class="btn btn-primary md-menu-white hidden-md hidden-lg" data-toggle="cm-menu"></div>
        <div class="cm-flex"><h1>{!! isset($header_title ) ? $header_title  : 'Unnamed Module' !!}</h1></div>
        @auth
            <div class="pull-right open" style="width:175px;">
                <button id="show-rate" class="btn btn-primary">
                  EDCOIN RATE: {{ $edcoin_rate['rate'] }} USD
                </button>
            </div>
            <div class="dropdown pull-right">
                <button class="btn btn-primary md-account-circle-white" data-toggle="dropdown"></button>
                <ul class="dropdown-menu">
                    <li>
                        <a id="showProfileDetails" href="#">
                          <i class="fas fa-fw fa-user"></i>
                          <strong>My Profile</strong>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-fw fa-sign-out-alt"></i> Sign out</a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>
            </div>
        @endif
    </nav>
</header>
<div id="global">
    <div class="container-fluid">
        <div class="row">
            @yield('content')
        </div>
    </div>
    <footer class="cm-footer">
        <span class="pull-right"><small>&copy; ED2E Technology</small></span>
    </footer>
</div>

@include('modals.profile.profile-modal')
@include('modals.edcoin_rate.edcoin')

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/clearmin.min.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('js/jquery.mousewheel.min.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.min.js') }}"></script>
<script src="{{ asset('js/fastclick.min.js') }}"></script>
<script src="{{ asset('js/metisMenu.min.js') }}"></script>
<script src="{{ asset('js/sidebar.js') }}"></script>
<script src="{{asset('js/datatables.min.js')}}"></script>
<script src="{{asset('js/helper.js')}}"></script>
<script src="{{asset('js/profile.js')}}"></script>
<script>
  $(function(){
    var edcoin_rate_modal = $('#edcoinRateModal');
    var notification_modal = $('#notificationEDCOINRateModal');
    var message_modal = $('#mdl_edcoin_rate_message');

    $('#show-rate').on('click',function(){
        getLatestData();
        edcoin_rate_modal.modal({
          'backdrop' : 'static',
          'keyboard' : false
        });
    });

    $('#coin-rate').on("input",function(event){
       var self = $(this);
       self.val(self.val().replace(/[^0-9\.]/g, ''));
       if ((event.which != 46 || self.val().indexOf('.') != -1) && (event.which < 48 || event.which > 57))
       {
         event.preventDefault();
       }

       var array_number = self.val().split(".");
       if(!jQuery.isEmptyObject(array_number[1]))
       {
         if(array_number[1].length>3)
         {
           self.val($.trim(self.val()).slice(0, -1));
         }
       }
    });

    $('.submit-form-edcoin-rate').on('click',function(){
        var rate = $("#coin-rate").val();

        edcoinValidate(rate);
    });

    $('.modal-edcoin-rate-cancel').on('click',function(){
        notification_modal.modal('hide');
        edcoin_rate_modal.modal({
          'backdrop' : 'static',
          'keyboard' : false
        });
    });

    $('.close-edcoin-rate-modal').on('click',function(){
          $('.message-edcoin-rate-container .edcoin-rate-message').prop('hidden',true);
    });

    $('#submit-edcoin-rate').on('click',function(){
        var rate = $("#coin-rate").val();
        createEDCoinRate(rate);
    });

    function getLatestData()
    {
        $.ajax({
          type: 'post',
          url: '{{ route("rate.latest.data") }}',
          data: {
            _token : "{{ csrf_token() }}"
          },
          dataType:'json',
          success:function(data)
          {
              if(data.created_by!=="None")
              {
                  $('.editor-container').prop('hidden',false);
                  $('#coin-rate').val(data.rate);
                  $('.created-by-view').html(data.created_by);
                  $('.updated-by-view').html(data.updated_by);
                  $('.date-created-view').html(data.created_date);
                  $('.date-updated-view').html(data.updated_date);
              }
          }
        });
    }



    function createEDCoinRate($rate)
    {
        var html = '';

        $.ajax({
          type: 'POST',
          url : '{{ route("rate.create") }}',
          data: {
            _token : "{{ csrf_token() }}",
            _rate : $rate
          },
          dataType:'json',
          success:function(data) {
              html += "<div class='alert alert-success text-success'>";
              html += "<button class='close' data-dismiss='modal'>&times;</button>";
              html += "<strong>" + data.message + "</strong></div>";

              $('#show-rate').html('EDCOIN RATE: ' + $rate + ' USD');
              $('#coin-rate').val($rate);
              $('#dialog_edcoin_rate_message').html(html);

              notification_modal.modal('hide');
              message_modal.modal({
                'backdrop' : 'static',
                'keyboard' : false
              });
          }
        });
    }

    function edcoinValidate($rate)
    {
        $('.message-edcoin-rate-container .edcoin-rate-message').prop('hidden',true);
        var html = '';
        $.ajax({
          type: 'POST',
          url : '{{ route("rate.validate") }}',
          data: {
            _token : "{{ csrf_token() }}",
            _rate : $rate
          },
          dataType:'json',
          success:function(data){
              if(data.result=='success')
              {
                edcoin_rate_modal.modal('hide');
                $('.modal-edcoin-rate-notification').html('Are you sure you want to update the EDCOIN rate?');
                notification_modal.modal({
                  'backdrop' : 'static',
                  'keyboard' : false
                });
              }
              else
              {
                $('.message-edcoin-rate-container .edcoin-rate-message').prop('hidden',false);
                html += '<div class="alert alert-danger">';
                html += data.message;
                html += '</div>';
                $('.message-edcoin-rate-container .edcoin-rate-message').html(html);
              }
          }
        });
    }
  });
</script>
@stack('scripts')
</body>
</html>
