<!-- Create Modal -->
<div id="mdl_create" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_create" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Create Purchaser</div>
            </div>
            <div class="modal-body">
                <form id="frm_create" action="#">
                    <div class="row">
                        <div id="alert_create" class="alert-message col-md-12"></div>
                        <!-- Form -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Country: *</label>
                                <select name="purchaser_country" class="form-control"></select>
                            </div>
                            <div class="form-group">
                                <label>Photo:</label>
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Select file</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" name="purchaser_image">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Name: *</label>
                                <input type="text" name="purchaser_name" class="form-control" placeholder="Name" maxlength="191">
                            </div>
                            <div class="form-group">
                                <label>Email Address: *</label>
                                <input type="text" name="purchaser_email" class="form-control" placeholder="Email Address" onkeyup="this.value = this.value.toLowerCase()" maxlength="191">
                            </div>
                            <div class="form-group">
                                <label>Contact No:</label>
                                <input type="text" name="purchaser_contact" class="form-control" placeholder="Contact No." maxlength="191">
                            </div>
                            <div class="form-group">
                                <label> Member Type: *</label>
                                <select name="purchaser_type" class="form-control">
                                    <option value="1">Existing Member</option>
                                    <option value="0">Public Member</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>EDA Number:</label>
                                <input type="text" name="purchaser_eda" class="form-control" placeholder="EDA Number" onkeyup="this.value = this.value.toUpperCase()" maxlength="191">
                                <small>* Required only if <b>Purchaser Type</b> is <b>Existing Member</b></small>
                            </div>
                            <div class="form-group">
                                <label>Membership: *</label>
                                <select name="purchaser_membership" class="form-control">
                                    <option value=>-- Select One --</option>
                                    {!! $membership_options !!}
                                </select>
                            </div>
                            <!-- Referral Group - Start -->
                            <div class="form-group">
                                <div class="checkbox">
                                    <label style="font-weight: bold">
                                        <input name="referred" type="checkbox"> Referred?
                                    </label>
                                </div>
                            </div>
                            <div class="referral-group well well-sm" style="display:none">
                                <div class="form-group">
                                    <label>Referral Name:</label>
                                    <select name="referral" class="form-control" style="width: 100%"></select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Status: *</label>
                                <select name="purchaser_status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="col-md-6">
                            <button type="submit" {{--id="btn_create"--}} class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Save
                            </button>
                        </div>
                        <div class="col-md-6">
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
                            <p>Are you sure you want to save?</p>
                        </div>
                        <div class="col-md-6">
                            <button id="btn_create_confirm_yes" type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Yes
                            </button>
                        </div>
                        <div class="col-md-6">
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
                <div class="modal-title">Edit Purchaser</div>
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
                                <label>Country:</label>
                                <input type="text" name="purchaser_country" class="form-control" placeholder="Country" readonly>
                                {{--<select name="purchaser_country" class="form-control"></select>--}}
                            </div>
                            <div class="form-group">
                                <label>Photo:</label>
                                <div class="purchaser-image"></div>
                                <div class="checkbox image-delete-checkbox">
                                    <label>
                                        <input type="checkbox" name="purchaser_delete_image"> Delete existing Photo?
                                    </label>
                                </div>
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Select file</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" name="purchaser_image">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Name:</label>
                                {{--<input type="text" name="purchaser_name" class="form-control" placeholder="Purchaser Name" readonly>--}}
                                <input type="text" name="purchaser_name" class="form-control" placeholder="Purchaser Name" maxlength="191">
                            </div>
                            <div class="form-group">
                                <label>Email: *</label>
                                <input type="text" name="purchaser_email" class="form-control" placeholder="Purchaser Email" onkeyup="this.value = this.value.toLowerCase()" maxlength="191">
                            </div>
                            <div class="form-group">
                                <label>Contact No:</label>
                                <input type="text" name="purchaser_contact" class="form-control" placeholder="Contact No." maxlength="191">
                            </div>
                            <div class="form-group">
                                <label>Member Type: *</label>
                                {{--<input type="text" name="purchaser_type" class="form-control" placeholder="Member Type" readonly>--}}
                                <select name="purchaser_type" class="form-control">
                                     <option value="1">Existing Member</option>
                                     <option value="0">Public Member</option>
                                 </select>
                            </div>
                            <div class="form-group">
                                <label>EDA Number:</label>
                                {{--<input type="text" name="purchaser_eda" class="form-control" placeholder="EDA Number" readonly>--}}
                                <input type="text" name="purchaser_eda" class="form-control" placeholder="EDA Number" onkeyup="this.value = this.value.toUpperCase()" maxlength="191">
                                <small>* Required only if <b>Type</b> is <b>Existing Member</b></small>
                            </div>
                            <div class="form-group">
                                <label>Membership:</label>
                                {{--<input type="text" name="purchaser_membership" class="form-control" placeholder="Membership" readonly>--}}
                                <select name="purchaser_membership" class="form-control">
                                     <option value=>-- Select One --</option>
                                     {!! $membership_options !!}
                                 </select>
                            </div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <label style="font-weight: bold">
                                        <input name="referred" type="checkbox"> Referred?
                                    </label>
                                </div>
                            </div>
                           {{-- <div class="referral-group well well-sm" >
                                <div class="form-group">
                                    <label>Referral Name:</label>
                                    <select name="referral" class="form-control">
                                    </select>
                                    --}}{{--<input type="text" name="referral_name" class="form-control" readonly placeholder="Referral Name">--}}{{--
                                </div>
                            </div>--}}
                            <div class="form-group">
                                <label>Status:</label>
                                {{--<input type="text" name="purchaser_status" class="form-control" placeholder="Status" readonly>--}}
                                <select name="purchaser_status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="col-md-6">
                            <button type="submit" id="btn_edit" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i> Save
                            </button>
                        </div>
                        <div class="col-md-6">
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
                            <p>Are you sure you want to save?</p>
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
                <div class="modal-title">Delete Purchaser</div>
            </div>
            <div class="modal-body">
                <form id="frm_delete" action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Are you sure you want to delete this Purchaser?</p>
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
                <div class="modal-title">View Purchaser</div>
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
                                <label>Country:</label>
                                <input type="text" name="purchaser_country" class="form-control" placeholder="Country" readonly>
                            </div>
                            <div class="form-group">
                                <label>Photo:</label>
                                <div class="purchaser-image"></div>
                            </div>
                            <div class="form-group">
                                <label>Name:</label>
                                <input type="text" name="purchaser_name" class="form-control" placeholder="Purchaser Name" readonly>
                            </div>
                            <div class="form-group">
                                <label>Email Address:</label>
                                <input type="text" name="purchaser_email" class="form-control" placeholder="Purchaser Email" readonly>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" id="btn_resend_wallet_code" class="btn btn-success">
                                    <i class="fas fa-paper-plane"></i> Resend Wallet Code
                                </button>
                            </div>
                            <div class="form-group">
                                <label>Contact No:</label>
                                <input type="text" name="purchaser_contact" class="form-control" placeholder="Contact No." readonly>
                            </div>
                            <div class="form-group">
                                <label>Member Type:</label>
                                <input type="text" name="purchaser_type" class="form-control" placeholder="Member Type" readonly>
                            </div>
                            <div class="form-group">
                                <label>EDA Number:</label>
                                <input type="text" name="purchaser_eda" class="form-control" placeholder="EDA Number" readonly>
                            </div>
                            <div class="form-group">
                                <label>Membership:</label>
                                <input type="text" name="purchaser_membership" class="form-control" placeholder="Membership" readonly>
                            </div>
                            <div class="form-group">
                                <label>Referral Name:</label>
                                <input type="text" name="referral_name" class="form-control" placeholder="Referral Name" readonly>
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <input type="text" name="purchaser_status" class="form-control" placeholder="Status" readonly>
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
