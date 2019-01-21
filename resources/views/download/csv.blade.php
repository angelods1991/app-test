@extends('layouts.app')
@push('styles')

@endpush
@section('content')
<div class="panel panel-default">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-5" style="font-size:18px;">
            Accumulative contributed (1st level)
        </div>
        <div class="col-md-4">
            <a class="btn btn-default" href="../../../download/package/member?access_code=de93e494ea514f24e86611af8b283a2d"><i class="fa fa-download"></i> Download</a>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-5" style="font-size:18px;">
            Contributor contributed and EDCoin Balance
        </div>
        <div class="col-md-4">
            <a class="btn btn-default" href="../../../download/distributor/details?access_code=de93e494ea514f24e86611af8b283a2d"><i class="fa fa-download"></i> Download</a>
        </div>
      </div>

    </div>
</div>
@endsection
