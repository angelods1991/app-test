<!-- Modal -->
<div class="modal fade" id="edcoinRateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:400px; width:100%;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">EDCOIN Rate Setup</h4>
        @if(Session::get('edcoinratelogs')=='active')
        <div class="pull-right" style="margin-top: -22px;">
            <a href="{{route('edcoin.activity')}}">View EDCOIN Logs > ></a>
        </div>
        @endif
      </div>
      <div class="modal-body">
          <!-- <div class="message-edcoin-rate-container" align="center">
              <div class="edcoin-rate-message" hidden></div>
              <div class="loading" hidden>
                  <div class="loader"></div>
                  Please wait. . .<hr>
              </div>
          </div> -->
          <form id="edcoin-rate-form">
              <input type="text" style="text-align:right;" name="coin_rate" id="coin-rate" class="form-control" placeholder="0.00" value="{{ $edcoin_rate['rate'] }}" {{ (!Session::get('edcoinratemodify')? print 'readonly' : print '') }} />
          </form>
          <div class="editor-container" hidden>
              <br>
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
          @if(Session::get('edcoinratemodify')||Session::get('edcoinrateedit'))
              <div class="col-md-6">
                  <button type="button" class="btn btn-primary btn-block submit-form-edcoin-rate"><i class="fa fa-check"></i> Submit</button>
              </div>
              <div class="col-md-6">
          @else
              <div class="col-md-12">
          @endif
                  <button type="button" class="btn btn-gray btn-block close-edcoin-rate-modal" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="notificationEDCOINRateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Message Box</h5>
      </div>
      <div class="modal-body modal-edcoin-rate-notification" align="center">
          Are you sure you want to update the EDCOIN rate?
      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-md-6">
                <button type="button" id="submit-edcoin-rate" class="btn btn-primary btn-block"><i class="fa fa-check"></i> Submit</button>
          </div>
          <div class="col-md-6">
              <button type="button" class="btn btn-gray modal-edcoin-rate-cancel btn-block" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Message Modal -->
<div id="mdl_edcoin_rate_message" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_edcoin_rate_message" class="modal-dialog text-center">
    </div>
</div>
