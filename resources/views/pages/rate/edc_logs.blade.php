@extends('layouts.app')
@push('styles')

@endpush
@section('content')

<div class="panel panel-default">
    <div class="panel-body">
          <div class="row">
              <div class="col-md-4">
                  <div class="form-group">
                    <div class='input-group date'>
                        <span class="input-group-addon">
                            Date From
                        </span>
                        <input type='text' name='from_date' id="from_date" class="form-control single-date" />
                        <span class="input-group-addon">
                            <span class="fa fa-calendar"></span>
                        </span>
                    </div>
                  </div>
              </div>

              <div class="col-md-4">
                  <div class="form-group">
                    <div class='input-group date'>
                        <span class="input-group-addon">
                            Date To
                        </span>
                        <input type='text' name='to_date' id="to_date" class="form-control single-date" />
                        <span class="input-group-addon">
                            <span class="fa fa-calendar"></span>
                        </span>
                    </div>
                  </div>
              </div>
          </div>
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
                              <input type="radio" name="column_search" value="1" checked /> ID No.
                        </label>
                        <label class="radio-inline form-control-static pr-3">
                              <input type="radio" name="column_search" value="2" /> Updated By
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table id="currencyRateLogsTable" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                      <tr class="bg-primary">
                          <th>ID No.</th>
                          <th>EDCOIN Rate</th>
                          <!-- <th>Created By</th>
                          <th>Date Created</th> -->
                          <th>Updated By</th>
                          <th>Date Updated</th>
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

@endsection

@push('scripts')
  <script>
    $(function(){
        var table = $('#currencyRateLogsTable').DataTable({
          processing: true,
          searching:false,
          bLengthChange: false,
            pagingType: "input",
          serverSide: true,
          searchDelay: 500,
          "columnDefs": [ {
          // "targets": [1,2,3],
          "orderable": false
          } ],
          'ordering' : false,
          ajax:{
              type : "POST",
              url : "{{ route('edcoin.activity.list') }}",
              dataType : "json",
              data : function(d){
                return $.extend({}, d, {
                   _token:"{{ csrf_token() }}",
                   _search:$('#search').val(),
                   _date_from:$('#from_date').val(),
                   _date_to:$('#to_date').val(),
                   _radio_value : $('input[name=column_search]:checked').val()
                });
              }
          },
          columns: [
              { data : 'no', sClass: 'dt-body-right'},
              { data : 'rate', sClass: 'dt-body-right'},
              // { data : 'created_by'},
              // { data : 'created_date'},
              { data : 'updated_by'},
              { data : 'updated_date'}
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


  </script>
  <script>
  $(function(){
    var date = new Date();

    $('#from_date').daterangepicker({
      opens: 'right',
      singleDatePicker: true,
      showDropdowns: true,
      startDate: getLastWeek(30)
    });

    $('#to_date').daterangepicker({
      opens: 'right',
      singleDatePicker: true,
      showDropdowns: true,
      startDate: getLastWeek(0),
    });

    function getLastWeek($past) {
      var today = new Date();
      var previousDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - $past);
      return (previousDate.getMonth()+1) +"/"+ previousDate.getDate() +"/"+ previousDate.getFullYear();
    }
  });
  </script>
@endpush
