@extends('layouts.app')
@push('styles')
@endpush
@section('content')

<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
          <!-- <div class="form-inline">
              <div class="form-group">
                  <input type="text" class="form-control" name="search" id="search" placeholder="Search"  />
              </div>
              <div class="form-group">
                  <button class="btn btn-default btn-search"><i class="fa fa-search"></i></button>
              </div>
          </div> -->

          <div>
            <button id="show-rate" class="btn btn-default">
              EDCOIN RATE: {{ $edcoin_data['rate'] }} USD
            </button>
          </div>

          <!-- <div class="form-inline pt-3">
              <div class="form-group">
                  <label class="form-control-static pr-3">Search Type:</label>
              </div>
              <div class="form-group">
                  <label class="radio-inline form-control-static pr-3">
                      <input type="radio" name="column_search" value="1" checked /> ID No.
                  </label>
                  <label class="radio-inline form-control-static pr-3">
                      <input type="radio" name="column_search" value="2" checked /> Country Name
                  </label>
              </div>
          </div> -->
        </div>
    </div>
    <!-- <div class="panel-body">
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table id="currencyRateTable" class="table table-bordered table-striped">
                    <thead>
                        <tr class="bg-primary">
                            <th>ID No.</th>
                            <th>Country Name</th>
                            <th>USD to Currency</th>
                            <th>EDCoin Value</th>
                            <th>Date Updated</th>
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
    </div> -->
</div>


@endsection

@push('scripts')

@endpush
