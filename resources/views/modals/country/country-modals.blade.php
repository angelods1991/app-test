<!-- Create Modal -->
<div id="mdl_create" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_create" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Create Country</div>
            </div>
            <div class="modal-body">
                <form id="frm_create" action="#">
                    <div class="row">
                        <div id="alert_create" class="alert-message col-md-12"></div>
                        <!-- Form -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Code: *</label>
                                <input type="text" name="country_code" class="form-control" placeholder="Code" maxlength="2" onkeyup="this.value = this.value.toUpperCase()">
                            </div>
                            <div class="form-group">
                                <label>Name: *</label>
                                <input type="text" name="country_name" class="form-control" placeholder="Name" maxlength="191">
                            </div>
                            <div class="form-group">
                                <label>Currency Code: *</label>
                                <input type="text" name="currency_code" class="form-control" placeholder="Currency Code" maxlength="191">
                            </div>
                            <div class="form-group">
                                <label> Status: *</label>
                                <select name="country_status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="col-md-6 pt-2">
                            <button type="submit" id="btn_create" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Save
                            </button>
                        </div>
                        <div class="col-md-6 pt-2">
                            <button id="btn_create_cancel" type="button" class="btn btn-gray btn-block" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="dialog_create_confirm" class="modal-dialog" style="display: none">
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
                            <button id="btn_create_confirm_yes" type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Yes
                            </button>
                        </div>
                        <div class="col-md-6 pt-2">
                            <button id="btn_create_confirm_no" type="submit" class="btn btn-gray btn-block">
                                <i class="fas fa-times"></i> No
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->
<div id="mdl_edit" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_edit" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Edit Country</div>
            </div>
            <div class="modal-body">
                <form id="frm_edit" action="#">
                    <div class="row">
                        <div id="alert_edit" class="alert-message col-md-12"></div>
                        <!-- Form -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>ID No.:</label>
                                <input type="text" name="id" class="form-control" placeholder="Id" readonly>
                            </div>
                            <div class="form-group">
                                <label>Code:</label>
                                <input type="text" name="country_code" class="form-control" placeholder="Code" readonly>
                            </div>
                            <div class="form-group">
                                <label>Name: *</label>
                                <input type="text" name="country_name" class="form-control" placeholder="Name"  maxlength="191">
                            </div>
                            <div class="form-group">
                                <label>Currency Code: *</label>
                                <input type="text" name="currency_code" class="form-control" placeholder="Currency Code" maxlength="191">
                            </div>
                            <div class="form-group">
                                <label>Status: *</label>
                                <select name="country_status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="col-md-6 pt-2">
                            <button type="submit" id="btn_edit" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Save
                            </button>
                        </div>
                        <div class="col-md-6 pt-2">
                            <button type="button" id="btn_edit_cancel" class="btn btn-gray btn-block">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="dialog_edit_confirm" class="modal-dialog" style="display: none">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Confirm to Save</div>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Are you sure that you want to save?</p>
                        </div>
                        <div class="col-md-6">
                            <button id="btn_edit_confirm_yes" type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Yes
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button id="btn_edit_confirm_no" type="submit" class="btn btn-gray btn-block">
                                <i class="fas fa-times"></i> No
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="dialog_edit_cancel_confirm" class="modal-dialog" style="display: none">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Confirm to Discard Changes</div>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <p>You have made changes. Do you want to discard it?</p>
                        </div>
                        <div class="col-md-6">
                            <button id="btn_edit_cancel_confirm_yes" type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Yes
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button id="btn_edit_cancel_confirm_no" type="submit" class="btn btn-gray btn-block">
                                <i class="fas fa-times"></i> No
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete Modal -->
<div id="mdl_delete" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_delete" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Delete Country</div>
            </div>
            <div class="modal-body">
                <form id="frm_delete" action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Are you sure that you want to delete this Country?</p>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" id="btn_delete_confirm_yes" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Yes
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" id="btn_delete_confirm_no" class="btn btn-gray btn-block">
                                <i class="fas fa-times"></i> No
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- View Modal -->
<div id="mdl_view" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_view" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">View Country</div>
            </div>
            <div class="modal-body">
                <form id="frm_view" action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>ID No.:</label>
                                <input type="text" name="id" class="form-control" placeholder="Id" readonly>
                            </div>
                            <div class="form-group">
                                <label>Code:</label>
                                <input type="text" name="country_code" class="form-control" placeholder="Code" readonly>
                            </div>
                            <div class="form-group">
                                <label>Name:</label>
                                <input type="text" name="country_name" class="form-control" placeholder="Name" readonly>
                            </div>
                            <div class="form-group">
                                <label>Currency Code: *</label>
                                <input type="text" name="currency_code" class="form-control" placeholder="Currency Code" maxlength="191" readonly>
                            </div>
                            <div class="form-group">
                                <label> Status:</label>
                                <input type="text" name="country_status" class="form-control" placeholder="Status" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <span style="display: block;"><strong>Created by:</strong><br><span class="created_by"></span></span>
                            <span class="pt-2" style="display: block;"><strong>Last Modified by:</strong><br><span class="modified_by"></span></span>
                        </div>
                        <div class="col-md-6 text-right">
                            <span style="display: block;"><strong>Created Date:</strong><br><span class="created_date"></span></span>
                            <span class="pt-2" style="display: block;"><strong>Last Modified Date:</strong><br><span class="modified_date"></span></span>
                        </div>
                        <div class="col-md-4 pt-2">
                            @if(Session::get($active_module.'delete'))
                                <button class="btn btn-danger btn-block" id="btn_view_delete">
                                    <i class="far fa-trash-alt"></i> Delete
                                </button>
                            @endif
                        </div>

                        <div class="col-md-4 pt-2">
                            @if(Session::get($active_module.'modify'))
                                <button class="btn btn-primary btn-block" id="btn_view_edit">
                                    <i class="far fa-edit"></i> Edit
                                </button>
                            @endif
                        </div>

                        <div class="col-md-4 pt-2">
                            <button class="btn btn-gray btn-block" id="btn_view_close">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Message Modal -->
<div id="mdl_message" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_message" class="modal-dialog text-center">
        <div class="modal-content">
            <div class='modal-body'><i class='fas fa-pulse fa-spinner'></i> Please Wait</div>
        </div>
    </div>
</div>
<!-- Session Timeout Modal -->
<div id="mdl_session_timeout" class="modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Warning!</div>
            </div>
            <div class="modal-body">
                <p>Your Session has Timed Out, please login again.</p>
                <small>You will be logout shortly.</small>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="{{route("login")}}">Login Again</a>
            </div>
        </div>
    </div>
</div>
