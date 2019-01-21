<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoleModel;
use App\Models\MenuModel;
use App\Models\UsersModel;
use Illuminate\Support\Facades\Validator;
use Session;

class RoleController extends Controller
{
    protected $role_name = "role_name";
    protected $menu_type = "menu_type";
    protected $menus = "menus";
    protected $_columns = array(
        1 => 'name',
    );

    public function __construct()
    {
        $this->middleware(['auth','showMethods']);
        $this->RoleModel = new RoleModel();
        $this->UsersModel = new UsersModel();
        $this->MenuModel = new MenuModel();
    }

    public function index()
    {
        $data['active_menu'] = 'role';
        $data['header_title'] = 'Role Management';
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        $data['menu_type'] = $this->menuType();

        return view('pages.role.index',$data);
    }

    public function menuRecords(Request $request)
    {
        $response = $this->MenuModel->getMenuRecords($request);
        if(count($response)>0)
        {
            $html = '';
            $data['result'] = 'success';
            foreach ($response as $menus) {
                $count = $this->RoleModel->checkRoleMenuID($request->input('_mid'),$menus->id);
                $html .= '<option value="'.$menus->id.'" '.($count==1?"selected":"").'>';
                $html .= $menus->name;
                $html .= '</option>';
            }
        }
        else
        {
          $data['result'] = 'fail';
          $html = 'No data found!';
        }

        $data['html'] = $html;

        echo json_encode($data);
    }

    private function menuType()
    {
        $response = $this->MenuModel->getMenuType();
        $menu_container = array();
        if(count($response)>0)
        {
            $html = '';
            $html .= '<option value="0">Please select a type</option>';
            foreach ($response as $menu) {
              if(!in_array($menu->type,$menu_container)){
                  $html .= '<option>';
                  $html .= $menu->type;
                  $html .= '</option>';

                  $menu_container[] = $menu->type;
              }
            }
        }
        else
        {
          $html = 'No data found!';
        }

        return $html;
    }

    public function create(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validate = Validator::make($request->all(),[
                $this->role_name => 'required',
                $this->menu_type => 'required',
                $this->menus => 'required'
            ],[
              $this->role_name.".required" => 'The name field is required.',
              $this->menu_type.".required" => 'The type field is required.',
              $this->menus.".required" => 'The menu field is required.'
            ]);

            if(!$validate->fails())
            {
                $response_id = $this->RoleModel->create($request->input('role_name'),$request->user()->id);
                if(!empty($response_id))
                {
                    $this->insertRolesMenus($response_id,$request->input('menus'));
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

    private function insertRolesMenus($role_id,$menus)
    {
        $menu_array = explode(',',$menus);

        for ($i=0; $i < count($menu_array) ; $i++) {
            $this->RoleModel->insertRole($role_id,$menu_array[$i]);
        }
    }

    public function recordList(Request $request)
    {
        if($request->isMethod('post'))
        {
            if(strlen($request->input('_token'))>0)
            {
                $columns = array(0=>'id',1=>'name');

                $total_data = $this->RoleModel->countRoleRecords();

                $total_filtered_data = $total_data;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');


                if(empty($request->input('_search')))
                {
                    $post = $this->RoleModel->showAllRecords($start,$limit,$order,$dir);
                }
                else
                {
                    $search = $request->input('_search');
                    $column_search = $request->input('_radio_value');

                    $column_search = (isset($column_search)&&$column_search!=0?$this->_columns[$column_search]:'bonus_name');

                    $post = $this->RoleModel->fetchRoleList($search,$start,$limit,$order,$dir,$column_search);

                    $total_filtered_data = $this->RoleModel->_count($search,$column_search);
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
            $nestedData['role_name'] = $post_data->name;
            $nestedData['role_menus'] = $this->parsePermissionID($post_data->id);
            $nestedData['activity'] = "<a data-toggle='tooltip' data-placement='top' data-original-title='View " . $post_data->name . "' href='#' data-value='".$post_data->id."' class='btn btn-default view-info'><i class='fa fa-eye'></i></a>";

            $iter++;

            $data[] = $nestedData;
        }

        return $data;
    }

    public function getData(Request $request)
    {
        if($request->isMethod('post')){
          $validate = Validator::make($request->all(),[
                          '_rid' => 'required|numeric|numeric:0'
                      ]);

          if(!$validate->fails()){
              $rid = $request->input('_rid');

              $check_id = $this->RoleModel->checkRoleID($rid);
              if($check_id==1){
                  $response = $this->RoleModel->getDataByID($rid);
                  $data['result'] = 'success';
                  $data['message'] = 'Record get successfully.';
                  $data['role_id'] = $response->id;
                  $data['role_name'] = $response->name;
                  $data['menu_type'] = $this->parseMenuType($response->id);
                  $data['menus'] = $this->parsePermissionID($response->id);
                  $data['created_by'] = $this->userDataName($response->created_by);
                  $data['updated_by'] = $this->userDataName($response->updated_by);
                  $data['created_date'] = $this->checkDate($response->created_at);
                  $data['updated_date'] = $this->checkDate($response->updated_at);
              }else{
                  $data['result'] = 'fail';
                  $data['message'] = 'Record is not registered.';
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

    private function parseMenuType($role_id)
    {
        $type = '';
        $type_container = array();
        $response = $this->RoleModel->getRolePermissions($role_id);
        foreach ($response as $key => $value) {
            if(!in_array($value->type,$type_container)):
                $type = $value->type;
                $type_container[] = $value->type;
            endif;
        }

        return $type;
    }

    private function parsePermissionID($role_id)
    {
        $menus = '';
        $response = $this->RoleModel->getRolePermissions($role_id);
        $i=1;

        foreach ($response as $info) {
            if($i<count($response)){
              $comma = ', ';
            }else{
              $comma = '';
            }

            $menus .= $info->name.$comma;
            $i++;
        }

        return $menus;
    }

    public function modify(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validate = Validator::make($request->all(),[
                $this->role_name => 'required',
                $this->menu_type => 'required',
                $this->menus => 'required'
            ],[
              $this->role_name.".required" => 'The name field is required.',
              $this->menu_type.".required" => 'The type field is required.',
              $this->menus.".required" => 'The menu field is required.'
            ]);

            if(!$validate->fails())
            {
                $rid = $request->input('_rid');
                $rname = $request->input('role_name');
                $menus = $request->input('menus');

                // $check_level = $this->RoleModel->checkUpdateLevel($bid,$bonus_level);
                //
                // if($check_level==0)
                // {
                    $response = $this->RoleModel->modify($rid,$rname,$request->user()->id);
                    if($response==1)
                    {
                        $this->RoleModel->removeRolesPermissions($rid);
                        $this->insertRolesMenus($rid,$menus);

                        $data['result'] = 'success';
                        $data['message'] = 'Record updated successfully.';
                    }
                    else
                    {
                        $data['result'] = 'fail';
                        $data['message'] = 'Record is on update.';
                    }
                // }
                // else
                // {
                //     $data['result'] = 'fail';
                //     $data['message'] = 'Bonus Level is already used. '.$check_level;
                // }
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
                            '_rid' => 'required|numeric|numeric:0'
                        ]);

            if(!$validate->fails())
            {
                $response = $this->RoleModel->remove($request->input('_rid'));
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

    private function checkDate($date)
    {
        if($date=='' || $date==null){
            $date = "None";
        }

        return $date;
    }

    private function userDataName($uid)
    {
        if(strlen($uid)>0){
            $response = $this->UsersModel->getPasswordByID($uid);
            $name = $response->name;
        }else{
          $name = "None";
        }

        return $name;
    }
}
