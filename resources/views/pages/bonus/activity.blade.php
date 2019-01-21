  @extends('layouts.app')

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
                            <input type="text" name="search" class="form-control"  id="search"  placeholder="Search">
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn-search btn btn-default">
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
                                <input type="radio" name="column_search" value="2" checked /> Candidate Name
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="radio-inline form-control-static pr-3">
                                <input type="radio" name="column_search" value="1" /> Purchaser Name
                            </label>
                        </div>
                    </div>
                </div>
            </div>
      </div>
      <div class="panel-body">
          <div class="col-md-12">
              <table id="usersTable" class="table table-bordered table-striped" style="width: 100%">
                  <thead>
                  <tr class="bg-primary">
                      <th>ID No.</th>
                      <th>Candidate Name</th>
                      <th>Purchaser Name</th>
                      <th>Bonus Amount</th>
                      <th>Status</th>
                      <th>Verified By</th>
                      <th>Date Created</th>
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

  @endsection

  @push('scripts')
  <script>
      $(function(){

        $("body").tooltip({selector: "[data-toggle='tooltip']"});

        var table = $('#usersTable').DataTable({
          processing: true,
          searching:false,
          bLengthChange: false,
          serverSide: true,
          searchDelay: 500,
          order:[0,'desc'],
          pagingType: "input",
          "columnDefs": [ {
          "targets": [3,4,5],
          "orderable": false
          } ],
          ajax:{
              type : "POST",
              url : "{{ route('bonus.activity.list') }}",
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
              { data : 'purchaser_name'},
              { data : 'candidate_name'},
              { data : 'amount',sClass: 'dt-body-right'},
              { data : 'status'},
              { data : 'created_by'},
              { data : 'date'},
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
