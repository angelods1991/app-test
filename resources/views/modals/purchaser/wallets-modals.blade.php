<div id="mdl_wallet_view" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_wallet" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Viewing Wallet of <span class="purchaser-name" style="font-weight: bold"></span></div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form action="#" id="frm_wallet_view">
                        <div class="col-md-12 text-right">
                        </div>
                        <div class="col-md-12" id="group_wallet_token">
                            <div class="form-group">
                                <label>Total Balance:</label>
                                <input type="text" name="wallet_balance" class="form-control" style="text-align: right !important;" readonly>
                            </div>
                            <div class="form-group">
                                <label>Locked Balance:</label>
                                <input type="text" name="wallet_lock_balance" class="form-control" style="text-align: right !important;" readonly>
                            </div>
                            <div class="form-group">
                                <label>Referral Bonus:</label>
                                <input type="text" name="referral_bonus" class="form-control" style="text-align: right !important;" readonly>
                            </div>
                            <div class="form-group">
                                <label>Available Balance:</label>
                                <input type="text" name="wallet_available_balance" class="form-control" style="text-align: right !important;" readonly>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-gray btn-block" data-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
