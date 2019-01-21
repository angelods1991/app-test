<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\UsersModel;
use App\Models\RoleModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Session;

class UsersController extends Controller
{
    protected $account_name = "account_name";
    protected $email = "email";
    protected $_columns = array(
        1 => 'name',
        2 => 'email',
        3 => 'role_name'
    );

    function __construct()
    {
        $this->middleware(['auth','showMethods']);
        $this->UsersModel = new UsersModel();
        $this->RoleModel = new RoleModel();
    }

    public function index()
    {
        $data['active_menu'] = 'users';
        $data['header_title'] = 'User Management';
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        $data['option_roles'] = $this->optionRoles();

        return view('pages.users.index',$data);
    }

    public function recordList(Request $request)
    {
        if($request->isMethod('post'))
        {
            if(strlen($request->input('_token'))>0)
            {
                $columns = array(0=>'id',1=>'name',2=>'email',3=>'id');

                $total_data = $this->UsersModel->countUsersRecords();

                $total_filtered_data = $total_data;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');


                if(empty($request->input('_search')))
                {
                    $post = $this->UsersModel->showAllRecords($start,$limit,$order,$dir);
                }
                else
                {
                    $search = $request->input('_search');
                    $column_search = $request->input('_radio_value');

                    $column_search = $this->selectColumnSearch($column_search);

                    $post = $this->UsersModel->fetchUsersList($search,$start,$limit,$order,$dir,$column_search);

                    $total_filtered_data = $this->UsersModel->_count($search,$column_search);
                }

                $data = array();

                if(!empty($post)):
                    $data = $this->setNestedData($post,$start);
                endif;

                $json_data = $this->setJsonData($request,$total_data,$total_filtered_data,$data);

                $data = $json_data;
            }
            else
            {
                $data['result'] = 'fail';
                $data['message'] = 'Please login before using this api!';
            }
        }
        else
        {
            $data['result'] = 'fail';
            $data['message'] = 'Invalid Method!';
        }

        return json_encode($data);
    }

    private function selectColumnSearch($column_search)
    {
        switch ($column_search) {
          case '1':
              $column_search = $this->_columns[$column_search];
            break;
          case '2':
              $column_search = $this->_columns[$column_search];
            break;
          case '3':
              $column_search = $this->_columns[$column_search];
            break;

          default:
            $column_search = 'name';
            break;
        }

      return $column_search;
    }

    private function setJsonData($request,$total_data,$total_filtered_data,$data)
    {
        $json_data = array(
                    "draw"            => intval($request->input('draw')),
                    "recordsTotal"    => intval($total_data),
                    "recordsFiltered" => intval($total_filtered_data),
                    "data"            => $data
                    );

        return $json_data;
    }

    private function setNestedData($post,$start)
    {
        $data = array();
        $iter=$start+1;

        foreach ($post as $post_data)
        {
            $nestedData['no'] = $post_data->id;
            $nestedData['name'] = $post_data->name;
            $nestedData['email'] = $post_data->email;
            $nestedData['role'] = $post_data->role_name;
            $nestedData['activity'] = "<a data-toggle='tooltip' data-placement='top' data-original-title='View " . $post_data->name . "' href='#' data-value='".$post_data->id."' class='btn btn-default view-info'><i class='fa fa-eye'></i></a>";

            $iter++;

            $data[] = $nestedData;
        }

        return $data;
    }

    public function create(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validate = Validator::make($request->all(),[
                $this->account_name => 'required',
                $this->email => 'required|email',
                'password' => 'required|alphaNum|required_with:confirm_password|same:confirm_password|min:3',
                'role' => 'required|numeric|numeric:0',
                'confirm_password' => 'required|alphaNum|min:3'
            ],[
                $this->account_name.'.required' => 'The name field is required.',
                $this->email.'.required'    => 'The email address field is required.',
                $this->email.'.email'    => 'Invalid email address.',
            ]);

            if(!$validate->fails())
            {
                $account_name = $request->input('account_name');
                $email = $request->input('email');
                $password = $request->input('password');
                $role = $request->input('role');
                $confirm_password = $request->input('confirm_password');
                $user_id = $request->user()->id;

                $response_count = $this->UsersModel->checkEmailAddress($email);
                if($response_count==0)
                {
                    $encrypt = Hash::make($password);

                    $response_id = $this->UsersModel->create($account_name,$email,$role,$encrypt,$user_id);
                    $response = $this->UsersModel->insertUserIdAndRoleId($response_id,$role);
                    if($response==1)
                    {
                        $data['result'] = 'success';
                        $data['message'] = 'Record added successfully.';
                    }
                    else
                    {
                        $data['result'] = 'fail';
                        $data['message'] = 'Unable to register the record.';
                    }
                }
                else
                {
                    $data['result'] = 'fail';
                    $data['message'] = 'Email is already used.';
                }

            }
            else
            {
                $data['result'] = 'fail';
                $data['message'] = $validate->errors()->first();
            }
        }
        else
        {
            $data['result'] = 'fail';
            $data['message'] = 'Invalid Method!';
        }

        echo json_encode($data);
    }

    public function getData(Request $request)
    {
        if($request->isMethod('post')){
          $validate = Validator::make($request->all(),[
                          '_uid' => 'required|numeric|numeric:0'
                      ]);

          if(!$validate->fails()){
              $uid = $request->input('_uid');

              $check_id = $this->UsersModel->checkBonusID($uid);
              if($check_id==1)
              {
                  $response = $this->UsersModel->getDataByID($uid);
                  $data['result'] = 'success';
                  $data['account_id'] = $response->id;
                  $data['account_name'] = $response->name;
                  $data['email'] = $response->email;
                  $data['role'] = $response->role_name;
                  $data['role_id'] = $response->role_id;
                  $data['created_by'] = $this->userDataName($response->created_by);
                  $data['updated_by'] = $this->userDataName($response->updated_by);
                  $data['created_date'] = $this->checkDate($response->created_at);
                  $data['updated_date'] = $this->checkDate($response->updated_at);
                  $data['account_password'] = "1234567891011";
              }
              else
              {
                  $data['result'] = 'fail';
                  $data['message'] = 'Account is not registered.';
              }

          }else{
              $data['result'] = 'fail';
              $data['message'] = $validate->errors()->first();
          }
        }else{
          $data['result'] = 'fail';
          $data['message'] = 'Invalid Method!';
        }

        echo json_encode($data);
    }

    private function checkDate($date)
    {
        if($date=='' || $date==null){
            $date = "None";
        }

        return $date;
    }

    private function userDataName($uid)
    {
        $count = $this->UsersModel->checkBonusID($uid);
        if($count>0)
        {
          $response = $this->UsersModel->getPasswordByID($uid);
          $name = $response->name;
        }
        else
        {
          $name = "None";
        }
        return $name;
    }

    public function modify(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validate = Validator::make($request->all(),[
                $this->account_name => 'required',
                $this->email => 'required',
                'password' => 'required|alphaNum|min:3|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'required|alphaNum|min:3'
            ],[
                $this->account_name.'.required' => 'The name field is required.',
                $this->email.'.required'    => 'The email address field is required.',
                $this->email.'.email'    => 'Invalid email address.',
            ]);

            if(!$validate->fails())
            {
                $user_id = $request->input('_uid');
                $password = $request->input('password');
                $role_id = $request->input('role');

                if($password!="1234567891011"):
                  $password = Hash::make($password);
                else:
                  $response = $this->UsersModel->getPasswordByID($user_id);
                  $password = $response->password;
                endif;

                $response = $this->UsersModel->modify($request,$password);

                if($response==1)
                {
                    $check_roles = $this->UsersModel->checkRole($user_id);

                    if($check_roles==0){
                        $this->UsersModel->insertUserIdAndRoleId($user_id,$role_id);
                    }else{
                        $this->UsersModel->updateRoleID($user_id,$role_id);
                    }

                    $data['result'] = 'success';
                    $data['message'] = 'Record updated successfully.';
                }
                else
                {
                    $data['result'] = 'fail';
                    $data['message'] = 'Record is on update.';
                }
            }
            else
            {
                $data['result'] = 'fail';
                $data['message'] = $validate->errors()->first();
            }
        }
        else
        {
            $data['result'] = 'fail';
            $data['message'] = 'Invalid Method!';
        }

        echo json_encode($data);
    }

    public function remove(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validate = Validator::make($request->all(),[
                            '_uid' => 'required|numeric|numeric:0'
                        ]);

            if(!$validate->fails())
            {
                $response = $this->UsersModel->remove($request->input('_uid'));
                if($response==1)
                {
                    $data['result'] = 'success';
                    $data['message'] = 'Record deleted successfully.';
                }
                else
                {
                    $data['result'] = 'fail';
                    $data['message'] = 'Record is already deleted.';
                }
            }
            else
            {
                $data['result'] = 'fail';
                $data['message'] = $validate->errors()->first();
            }
        }
        else
        {
            $data['result'] = 'fail';
            $data['message'] = 'Invalid Method!';
        }

        echo json_encode($data);
    }

    private function optionRoles()
    {
        $response = $this->RoleModel->getRoleRecords();
        if(count($response)>0)
        {
            $html = '';
            foreach ($response as $info) {
                $html .= '<option value="'.$info->id.'">';
                $html .= $info->name;
                $html .= '</option>';
            }
        }
        else
        {
            $html = 'No Data Found!';
        }

        return $html;
    }

    public function updateProfileInformation(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validate = Validator::make($request->all(),[
                          '_name' => 'required'
                        ]);

            if(!$validate->fails())
            {
                $user_id = Auth::user()->id;
                $name = $request->input('_name');

                $response = $this->UsersModel->updateProfileName($user_id,$name);
                if($response)
                {
                    $data['result'] = 'success';
                    $data['message'] = 'Your profile name was updated successfully.';
                }
                else
                {
                    $data['result'] = 'fail';
                    $data['message'] = 'Your profile name is on update.';
                }
            }
            else
            {
                $data['result'] = 'fail';
                $data['message'] = $validate->errors()->first();
            }
        }
        else
        {
            $data['result'] = 'fail';
            $data['message'] = 'Invalid Method!';
        }

        return $data;
    }

    public function updateProfilePassword(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validation = Validator::make($request->all(),[
              '_password' => 'required|alphaNum|min:3|required_with:_confirm_password|same:_confirm_password',
              '_confirm_password' => 'required|alphaNum|min:3'
            ]);

            if(!$validation->fails())
            {
                $user_id = Auth::user()->id;
                $password = $request->input('_password');
                $password = Hash::make($password);

                $response = $this->UsersModel->modifyPassword($user_id,$password);

                if($response==1)
                {
                    $data['result'] = 'success';
                    $data['message'] = 'Your password was updated successfully.';
                }
                else
                {
                    $data['result'] = 'fail';
                    $data['message'] = 'Unable to update your password.';
                }
            }
            else
            {
                $data['result'] = 'fail';
                $data['message'] = $validation->errors()->first();
            }
        }
        else
        {
            $data['result'] = 'fail';
            $data['message'] = 'Invalid Method!';
        }

        return $data;
    }
}
