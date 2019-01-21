<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuModel;
use App\Models\PermissionModel;
use App\Models\UsersModel;
use Illuminate\Support\Facades\Validator;
use Session;

class MenuController extends Controller
{
    protected $menu_name = 'menu_name';
    protected $menu_link = 'menu_link';
    protected $menu_type = 'menu_type';
    protected $permissions = 'permissions';
    protected $_columns = array(
        1 => 'name',
        2 => 'link',
        3 => 'type'
    );

    public function __construct()
    {
        $this->middleware(['auth','showMethods']);
        $this->MenuModel = new MenuModel();
        $this->UsersModel = new UsersModel();
        $this->PermissionModel = new PermissionModel();
    }

    public function index(Request $request)
    {
        $data['active_menu'] = 'menu';
        $data['header_title'] = 'Menu Management';
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        return view('pages.menu.index',$data);
    }

    public function permissionRecords(Request $request)
    {
        $response = $this->PermissionModel->getPermissionRecords();
        if(count($response)>0)
        {
            $html = '';

            foreach ($response as $permission) {
                $count = $this->MenuModel->checkMenuPermissionID($request->input('_mid'),$permission->id);
                $html .= '<option value="'.$permission->id.'" '.($count==1?"selected":"").'>';
                $html .= $permission->name;
                $html .= '</option>';
            }
        }
        else
        {
          $html = '<option>No Data Found!</option>';
        }

        $data['html'] = $html;

        echo json_encode($data);
    }

    public function create(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validate = Validator::make($request->all(),[
                $this->menu_name => 'required|string|min:1',
                $this->menu_link => 'required|string',
                $this->menu_type => 'required|string',
                $this->permissions => 'required'
            ],[
                $this->menu_name.'.required' => 'The name field is required.',
                $this->menu_name.'.min' => 'The name field is required.',
                $this->menu_link.'.required' => 'The link field is required.',
                $this->menu_type.'.required' => 'The type field is required.',
                $this->permissions.'.required' => 'The permissions field is required.'
            ]);

            if(!$validate->fails())
            {
                $check_type = $this->MenuModel->checkMenuType($request);
                if($check_type==0)
                {
                    if(!is_null($request->input('permissions')))
                    {
                        $response_id = $this->MenuModel->create($request);
                        if(!empty($response_id))
                        {
                            $this->insertMenuPermissions($response_id,$request->input('permissions'));
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
                        $data['message'] = 'Please select a permission.';
                    }

                }
                else
                {
                    $data['result'] = 'fail';
                    $data['message'] = 'Menu Name and Menu Type is already used.';
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

    private function insertMenuPermissions($menu_id,$permissions)
    {
        $permission_array = explode(',',$permissions);

        for ($i=0; $i < count($permission_array) ; $i++) {
            $this->MenuModel->insertMenu($menu_id,$permission_array[$i]);
        }
    }

    public function recordList(Request $request)
    {
        if($request->isMethod('post'))
        {
            if(strlen($request->input('_token'))>0)
            {
                $columns = array(0=>'id',1=>'name');

                $total_data = $this->MenuModel->countMenuRecords();

                $total_filtered_data = $total_data;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');


                if(empty($request->input('_search')))
                {
                    $post = $this->MenuModel->showAllRecords($start,$limit,$order,$dir);
                }
                else
                {
                    $search = $request->input('_search');
                    $column_search = $request->input('_radio_value');

                    $column_search = (isset($column_search)&&$column_search!=0?$this->_columns[$column_search]:'bonus_name');

                    $post = $this->MenuModel->fetchMenuList($search,$start,$limit,$order,$dir,$column_search);

                    $total_filtered_data = $this->MenuModel->_count($search,$column_search);
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
            $nestedData['menu_name'] = $post_data->name;
            $nestedData['menu_link'] = ($post_data->link==""?"N/A":url('/').$post_data->link);
            $nestedData['menu_type'] = $post_data->type;
            $nestedData['menu_permissions'] = $this->parsePermissionID($post_data->id);
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
                          '_mid' => 'required|numeric|numeric:0'
                      ]);

          if(!$validate->fails()){
              $mid = $request->input('_mid');

              $check_id = $this->MenuModel->checkMenuID($mid);
              if($check_id==1){
                  $response = $this->MenuModel->getDataByID($mid);
                  $data['result'] = 'success';
                  $data['message'] = 'Permission updated successfully.';
                  $data['menu_id'] = $response->id;
                  $data['menu_name'] = $response->name;
                  $data['menu_link'] = $response->link;
                  $data['menu_type'] = $response->type;
                  $data['permissions'] = $this->parsePermissionID($response->id);
                  $data['created_by'] = $this->userDataName($response->created_by);
                  $data['updated_by'] = $this->userDataName($response->updated_by);
                  $data['created_date'] = $this->checkDate($response->created_at);
                  $data['updated_date'] = $this->checkDate($response->updated_at);
              }else{
                  $data['result'] = 'fail';
                  $data['message'] = 'Permission is not registered.';
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

    private function parsePermissionID($menu_id)
    {
        $permissions = '';
        $response = $this->MenuModel->getMenuPermissions($menu_id);
        $i=1;

        foreach ($response as $info) {
            if($i<count($response)){
              $comma = ', ';
            }else{
              $comma = '';
            }

            $permissions .= $info->name.$comma;
            $i++;
        }

        return $permissions;
    }

    public function modify(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validate = Validator::make($request->all(),[
                $this->menu_name => 'required|string|min:1',
                $this->menu_link => 'required|string',
                $this->menu_type => 'required|string',
                $this->permissions => 'required'
            ],[
                $this->menu_name.'.required' => 'The name field is required.',
                $this->menu_name.'.min' => 'The name field is required.',
                $this->menu_link.'.required' => 'The link field is required.',
                $this->menu_type.'.required' => 'The type field is required.',
                $this->permissions.'.required' => 'The permissions field is required.'
            ]);

            if(!$validate->fails())
            {
                $mid = $request->input('_mid');
                $menu_name = $request->input('menu_name');
                $menu_link = $request->input('menu_link');
                $menu_type = $request->input('menu_type');
                $permissions = $request->input('permissions');
                $created_by = $request->user()->id;

                if($request->input('permissions')!=null||$request->input('permissions')!="")
                {
                    $check_type = $this->MenuModel->checkMenuTypeUpdate($request);
                    if($check_type==0)
                    {
                        $response = $this->MenuModel->modify($mid,$menu_name,$menu_link,$menu_type,$created_by);
                        if($response==1)
                        {
                            $this->MenuModel->removeMenuPermissions($mid);
                            $this->insertMenuPermissions($mid,$permissions);

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
                        $data['message'] = 'Menu Name and Menu Type is already used.';
                    }

                }
                else
                {
                    $data['result'] = 'fail';
                    $data['message'] = 'Please select a permission.';
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
                            '_mid' => 'required|numeric|numeric:0'
                        ]);

            if(!$validate->fails())
            {
                $response = $this->MenuModel->remove($request->input('_mid'));
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
