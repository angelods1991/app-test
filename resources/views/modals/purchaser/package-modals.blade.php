<div id="mdl_package_view" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_package_view" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Viewing the Packages of
                    <span class="purchaser-name" style="font-weight: bold"></span></div>
            </div>
            <div class="modal-body">
                <form id="frm_package_view" action="#">
                    <div class="row">
                        @if(Session::get($active_module.'create'))
                          <div class="col-md-12 text-right">
                              <button class="btn btn-primary" id="btn_package_create">
                                  <i class="fas fa-plus"></i> Add Package To Purchaser
                              </button>
                          </div>
                        @endif
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="dt_purchaser_packages" class="table table-responsive table-bordered table-hover" style="width: 100%">
                                    <thead>
                                    <tr class="bg-primary">
                                        <th>Id</th>
                                        <th>Amount Paid</th>
                                        <th>Token Price</th>
                                        <th>Incentive %</th>
                                        <th>Token Earned</th>
                                        <th>Created By</th>
                                        <th>Date Created</th>
                                        <th>Updated By</th>
                                        <th>Date Updated</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center">Please Wait...</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12 pt-2">
                            <button class="btn btn-gray btn-block" id="btn_package_view_close">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="mdl_package_create" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_package_create" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Creating Package for
                    <span class="purchaser-name" style="font-weight: bold"></span></div>
            </div>
            <div class="modal-body">
                <form id="frm_package_create" action="#">
                    <div class="row">
                        <div id="alert_package_create" class="col-md-12 alert-message"></div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Token Price: *</label>
                                <div class="input-group">
                                    <input name="package_token_price" type="text" class="form-control" placeholder="Token Price">
                                    <span class="input-group-addon">USD</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Paid Amount: *</label>
                                <div class="input-group">
                                    <input name="package_paid_amount" type="text" class="form-control" placeholder="Paid Amount">
                                    <span class="input-group-addon">USD</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Incentive Percentage:</label>
                                <div class="input-group">
                                    <input name="package_incentive_percentage" type="text" class="form-control" placeholder="Incentive">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Package Tokens:</label>
                                <input name="package_tokens" type="text" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Breakdown:</label>
                                <div class="well well-sm mt-1">
                                    <div class="form-group">
                                        <label>Purchase Tokens:</label>
                                        <input name="package_locked_tokens" type="text" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Incentive Tokens:</label>
                                        <input name="package_incentive_tokens" type="text" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Payment Method: *</label>
                                <select name="package_method" class="form-control">
                                    <option value=>-- Select One</option>
                                    <option value="CASH">Cash</option>
                                    <option value="BANK-IN">Bank-In</option>
                                    <option value="CRYPTO">Crypto</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Remarks: *</label>
                                <textarea name="package_remarks" class="form-control" placeholder="Remarks"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 pt-2">
                            <button type="submit" id="btn_package_create_submit" class="btn btn-primary btn-block">
                                Save
                            </button>
                        </div>
                        <div class="col-md-6 pt-2">
                            <button type="button" id="btn_package_create_cancel" class="btn btn-gray btn-block">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="dialog_package_create_confirm" class="modal-dialog" style="display: none">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Confirm</div>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Are you sure that you want to save?</p>
                        </div>
                        <div class="col-md-6 pt-2">
                            <button id="btn_package_create_confirm_yes" type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Yes
                            </button>
                        </div>
                        <div class="col-md-6 pt-2">
                            <button id="btn_package_create_confirm_no" type="submit" class="btn btn-gray btn-block">
                                <i class="fas fa-times"></i> No
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="mdl_package_verify" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_package_verify" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Verifying Package for
                    <span class="purchaser-name" style="font-weight: bold"></span></div>
            </div>
            <div class="modal-body">
                <form id="frm_package_verify" action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Token Price:</label>
                                <div class="input-group">
                                    <input name="package_token_price" type="text" class="form-control" placeholder="Token Price" readonly>
                                    <span class="input-group-addon">USD</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Paid Amount:</label>
                                <div class="input-group">
                                    <input name="package_paid_amount" type="text" class="form-control" placeholder="Paid Amount" readonly>
                                    <span class="input-group-addon">USD</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Incentive Percentage:</label>
                                <div class="input-group">
                                    <input name="package_incentive_percentage" type="text" class="form-control" placeholder="Incentive" readonly>
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Package Tokens:</label>
                                <input name="package_tokens" type="text" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Breakdown:</label>
                                <div class="well well-sm mt-1">
                                    <div class="form-group">
                                        <label>Purchase Tokens:</label>
                                        <input name="package_locked_tokens" type="text" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Incentive Tokens:</label>
                                        <input name="package_incentive_tokens" type="text" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Payment Method: *</label>
                                <input type="text" name="package_method" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Remarks: *</label>
                                <textarea name="package_remarks" class="form-control" placeholder="Remarks" readonly></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upline #1</label>
                                        <input name="upline_name_1" class="form-control" readonly placeholder="Upline #1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Bonus #1</label>
                                        <input name="upline_bonus_1" class="form-control" readonly placeholder="Bonus #1">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upline #2</label>
                                        <input name="upline_name_2" class="form-control" readonly placeholder="Upline #2">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Bonus #2</label>
                                        <input name="upline_bonus_2" class="form-control" readonly placeholder="Bonus #2">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upline #3</label>
                                        <input name="upline_name_3" class="form-control" readonly placeholder="Upline #3">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Bonus #3</label>
                                        <input name="upline_bonus_3" class="form-control" readonly placeholder="Bonus #3">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 pt-2">
                          @if(Session::get($active_module.'reject'))
                            <button type="submit" id="btn_package_verify_reject" class="btn btn-block btn-danger">
                                Reject
                            </button>
                          @endif
                        </div>
                        <div class="col-md-4 pt-2">
                          @if(Session::get($active_module.'post'))
                            <button type="submit" id="btn_package_verify_post" class="btn btn-primary btn-block">
                                Post
                            </button>
                          @endif
                        </div>
                        <div class="col-md-4 pt-2">
                            <button type="button" id="btn_package_verify_cancel" class="btn btn-gray btn-block">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="dialog_package_verify_post_confirm" class="modal-dialog" style="display: none">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Confirm</div>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Are you sure that you want to post?</p>
                        </div>
                        <div class="col-md-6 pt-2">
                            <button id="btn_package_verify_post_confirm_yes" data-value="1" type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Yes
                            </button>
                        </div>
                        <div class="col-md-6 pt-2">
                            <button id="btn_package_verify_post_confirm_no" type="submit" class="btn btn-gray btn-block">
                                <i class="fas fa-times"></i> No
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="dialog_package_verify_reject_confirm" class="modal-dialog" style="display: none">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Confirm</div>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Are you sure that you want to reject?</p>
                        </div>
                        <div class="col-md-6 pt-2">
                            <button id="btn_package_verify_reject_confirm_yes" data-value="0" type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Yes
                            </button>
                        </div>
                        <div class="col-md-6 pt-2">
                            <button id="btn_package_verify_reject_confirm_no" type="submit" class="btn btn-gray btn-block">
                                <i class="fas fa-times"></i> No
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
