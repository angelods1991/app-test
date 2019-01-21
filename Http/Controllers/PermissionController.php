<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermissionModel;
use Illuminate\Support\Facades\Validator;
use App\Models\UsersModel;
use Session;

class PermissionController extends Controller
{
    protected $permission_name = "permission_name";
    protected $_columns = array(
        1 => 'name',
        // 2 => 'bonus_desc'
    );

    public function __construct()
    {
        $this->middleware(['auth','showMethods']);
        $this->PermissionModel = new PermissionModel();
        $this->UsersModel = new UsersModel();
    }

    public function index()
    {
        $data['active_menu'] = 'permission';
        $data['header_title'] = 'Permission Management';
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        // echo "<pre>".json_encode(Session::all(),JSON_PRETTY_PRINT)."</pre>";

        return view('pages.permission.index',$data);
    }

    public function create(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validate = Validator::make($request->all(),[
                $this->permission_name => 'required',
            ],[
                $this->permission_name.'.required' => "The name field is required.",
            ]);

            if(!$validate->fails())
            {
                // $check_level = $this->PermissionModel->checkLevel($request->input('bonus_level'));
                // if($check_level==0)
                // {
                    $created_by = $request->user()->id;
                    $response = $this->PermissionModel->create($request->input('permission_name'),$created_by);
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
                // }
                // else
                // {
                //     $data['result'] = 'fail';
                //     $data['message'] = 'Bonus Level is already used.';
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

    public function recordList(Request $request)
    {
        if($request->isMethod('post'))
        {
            if(strlen($request->input('_token'))>0)
            {
                $columns = array(0=>'id',1=>'name');

                $total_data = $this->PermissionModel->countPermissionRecords();

                $total_filtered_data = $total_data;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');


                if(empty($request->input('_search')))
                {
                    $post = $this->PermissionModel->showAllRecords($start,$limit,$order,$dir);
                }
                else
                {
                    $search = $request->input('_search');
                    $column_search = $request->input('_radio_value');

                    $column_search = (isset($column_search)&&$column_search!=0?$this->_columns[$column_search]:'bonus_name');

                    $post = $this->PermissionModel->fetchPermissionList($search,$start,$limit,$order,$dir,$column_search);

                    $total_filtered_data = $this->PermissionModel->_count($search,$column_search);
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
            $nestedData['permission_name'] = $post_data->name;
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
                          '_pid' => 'required|numeric|numeric:0'
                      ]);

          if(!$validate->fails()){
              $bid = $request->input('_pid');

              $check_id = $this->PermissionModel->checkPermissionID($bid);
              if($check_id==1){
                  $response = $this->PermissionModel->getDataByID($bid);
                  $data['result'] = 'success';
                  $data['message'] = 'Permission updated successfully.';
                  $data['permission_id'] = $response->id;
                  $data['permission_name'] = $response->name;
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

    public function modify(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validate = Validator::make($request->all(),[
                $this->permission_name => 'required'
            ],[
                $this->permission_name.'.required' => "The name field is required.",
            ]);

            if(!$validate->fails())
            {
                $pid = $request->input('_pid');
                $pname = $request->input('permission_name');
                $updated_by = $request->user()->id;
                // $check_level = $this->PermissionModel->checkUp dateLevel($bid,$bonus_level);
                //
                // if($check_level==0)
                // {
                    $response = $this->PermissionModel->modify($pid,$pname,$updated_by);
                    if($response==1)
                    {
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
                            '_pid' => 'required|numeric|numeric:0'
                        ]);

            if(!$validate->fails())
            {
                $response = $this->PermissionModel->remove($request->input('_pid'));
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
