
<div id="modalShowProfileDetails" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" style="max-width:450px; width:100%" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">User Profile</h5>
      </div>
      <div class="modal-body">
              <div class="form-group">
                    <div class="form-inline" style="margin-bottom:16px;">
                        <label>Name</label>
                        <span class="pull-right" style="margin-top:-5px;">
                            <button id="change-name" class="btn btn-default">Change Name</button>
                        </span>
                    </div>
                    <div class="well well-name">
                        {{Auth::user()->name}}
                    </div>
              </div>
              <div class="form-group">
                    <label>Email Address</label>
                    <div class="well well-email">
                      {{Auth::user()->email}}
                    </div>
              </div>
              <div class="form-group">
                    <label>Role</label>
                    <div class="well well-role">
                        {{ Session::get('role_name') }}
                    </div>
              </div>
              <div class="form-group">
                    <div class="form-inline" style="margin-bottom:16px;">
                        <label class="label-password">Password</label>
                        <span class="pull-right" style="margin-top:-5px;">
                          <button id="change-password" class="btn btn-default">Change Password</button>
                        </span>
                    </div>
                    <div class="well display-password">
                          ********
                    </div>
              </div>
      </div>
      <div class="modal-footer">
          <div class="row">
              <div class="col-md-12">
                  <button type="button" class="btn btn-gray btn-block" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                  </button>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>

<div id="modalProfileName" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" style="max-width:450px; width:100%" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Profile Name</h5>
      </div>
      <div class="modal-body">
          <form class="profile-form">
              <div class="form-group">
                    <div class="form-inline">
                        <label>Name</label>
                    </div>
                    <input type="text" id="profile-name" name="profile_name" class="form-control" value="{{Auth::user()->name}}" />
              </div>
          </form>
      </div>
      <div class="modal-footer">
          <div class="row">
              <div class="col-md-6">
                  <button type="button" class="btn btn-primary btn-block btn-submit-profile-data">
                    <i class="fa fa-check"></i> Submit
                  </button>
              </div>
              <div class="col-md-6">
                  <button type="button" class="btn btn-gray btn-block modal-information-close" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                  </button>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>

<div id="modalChangePassword" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" style="max-width:450px; width:100%" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Password</h5>
      </div>
      <div class="modal-body">
          <form class="profile-change-password-form">
              <div class="form-group">
                  <label>New Password</label>
                  <input type="password" id="profile-password" name="new_password" class="form-control" value="" />
              </div>
              <div class="form-group">
                  <label>Confirm Password</label>
                  <input type="password" id="profile-confirm-password" name="confirm_password" class="form-control" value="" />
              </div>
          </form>
      </div>
      <div class="modal-footer">
          <div class="row">
              <div class="col-md-6">
                  <button type="button" class="btn btn-primary btn-block btn-modal-notification">
                    <i class="fa fa-check"></i> Submit
                  </button>
              </div>
              <div class="col-md-6">
                  <button type="button" class="btn btn-gray btn-block modal-password-close" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                  </button>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="notificationProfilelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Message Box</h5>
      </div>
      <div class="modal-body modal-profile-notification" align="center">
          Are you sure you want to create this record?
      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-md-6">
              <button type="button" id="submit-profile-form" data-value="{{ csrf_token() }}" class="btn btn-primary btn-block"><i class="fa fa-check"></i> Submit</button>
          </div>
          <div class="col-md-6 modal-password-notification-cancel" hidden>
              <button type="button" class="btn btn-gray btn-block" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
          </div>
          <div class="col-md-6 modal-profile-notification-cancel" hidden>
              <button type="button" class="btn btn-gray btn-block" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Message Modal -->
<div id="mdl_profile_message" class="modal" data-backdrop="static" data-keyboard="false">
    <div id="dialog_profile_message" class="modal-dialog text-center">
    </div>
</div>
