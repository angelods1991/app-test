<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BonusModel;
use App\Models\UsersModel;
use Illuminate\Support\Facades\Validator;
use Session;

class BonusController extends Controller
{
    protected $bonus_level = "bonus_level";
    protected $bonus_name = "bonus_name";

    protected $_columns = array(
        1 => 'bonus_level',
        2 => 'bonus_name',
        3 => 'bonus_desc'
    );

    public function __construct()
    {
        $this->middleware(['auth','showMethods']);
        $this->BonusModel = new BonusModel();
        $this->UsersModel = new UsersModel();
    }

    public function index()
    {
        $data['active_menu'] = 'bonus';
        $data['header_title'] = 'Bonus Management';
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        return view('pages.bonus.index',$data);
    }

    public function activity()
    {
        $data['active_menu'] = 'bonus_activity';
        $data['header_title'] = 'Referral Bonus Logs';
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        return view('pages.bonus.activity',$data);
    }

    public function create(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validate = Validator::make($request->all(),[
                $this->bonus_level => 'required',
                $this->bonus_name => 'required'
            ],[
                $this->bonus_level.".required" => 'The level field is required.',
                $this->bonus_name.".required" => 'The percentage field is required.'
            ]);

            if(!$validate->fails())
            {

                    $response = $this->BonusModel->create($request->input('bonus_level'),$request->input('bonus_name'),
                    $request->input('bonus_desc'),$request->user()->id);
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

    public function setMemberLine()
    {
        $data = $this->tableShowView();
        $html = '';
        foreach ($data['header'] as $value) {
            $html .= '<tr>';
            $html .= '<td>'.$value.'</td>';
            foreach ($data['body'][$value] as $percentage) {
                $html .= '<td>'.$percentage[2].' <a data-toggle="tooltip" data-placement="top" data-original-title="View ' . $percentage[2] . '" href="#" data-value="'.$percentage[0].'" class="btn btn-default view-info pull-right"><i class="fa fa-eye"></i></a></td>';
            }
            $html .= '</tr>';
        }

        return $html;
    }

    private function tableShowView()
    {
        $response = $this->BonusModel->getBonusData();
        $table_continer = array();
        $data_container = array();

        foreach ($response as $key => $value) {
            $table_continer = $this->collectHeader($table_continer,$value);
            $data_container = $this->collectData($table_continer,$data_container,$value);
        }

        $data['header'] = $table_continer;
        $data['body'] = $data_container;

        return $data;
    }

    private function collectHeader($table_continer,$value)
    {
      if(!in_array($value->membership,$table_continer)){
          $table_continer[] = $value->membership;
      }

      return $table_continer;
    }

    private function collectData($table_continer,$data_container,$value)
    {
      if(in_array($value->membership,$table_continer)){
          $data_container[$value->membership][] = array(
            $value->id,
            $value->bonus_level,
            $value->bonus_name,
            $value->bonus_desc
          );
      }

      return $data_container;
    }

    public function recordList(Request $request)
    {
        if($request->isMethod('post'))
        {
            if(strlen($request->input('_token'))>0)
            {
                $columns = array(0=>'id',1=>'bonus_level',2=>'bonus_name',3=>'membership',4=>'bonus_desc');

                $total_data = $this->BonusModel->countUsersRecords();

                $total_filtered_data = $total_data;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');

                if(empty($request->input('_search')) && empty($request->input('_category')))
                {
                    $post = $this->BonusModel->showAllRecords($start,$limit,$order,$dir);
                }
                else
                {
                    $search = $request->input('_search');
                    $column_search = $request->input('_radio_value');
                    $category = $request->input('_category');

                    $column_search = $this->selectColumnSearch($column_search);

                    $post = $this->BonusModel->fetchUsersList($search,$start,$limit,$order,$dir,$column_search,$category);

                    $total_filtered_data = $this->BonusModel->_count($search,$column_search,$category);
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
            $column_search = 'bonus_level';
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
            $nestedData['bonus_level'] = $post_data->bonus_level;
            $nestedData['bonus_name'] = $post_data->bonus_name;
            $nestedData['bonus_desc'] = $post_data->bonus_desc;
            $nestedData['membership_type'] = $post_data->membership;
            $nestedData['activity'] = "<a data-toggle='tooltip' data-placement='top' data-original-title='View " . $post_data->bonus_name . "' href='#' data-value='".$post_data->id."' class='btn btn-default view-info'><i class='fa fa-eye'></i></a>";

            $iter++;

            $data[] = $nestedData;
        }

        return $data;
    }

    public function getData(Request $request)
    {
        if($request->isMethod('post')){
          $validate = Validator::make($request->all(),[
                          '_bid' => 'required|numeric|numeric:0'
                      ]);

          if(!$validate->fails()){
              $bid = $request->input('_bid');

              $check_id = $this->BonusModel->checkBonusID($bid);
              if($check_id==1){
                  $response = $this->BonusModel->getDataByID($bid);
                  $data['result'] = 'success';
                  $data['message'] = 'Bonus updated successfully.';
                  $data['bonus_id'] = $response->id;
                  $data['bonus_level'] = $response->bonus_level;
                  $data['bonus_name'] = $response->bonus_name;
                  $data['bonus_desc'] = $response->bonus_desc;
                  $data['membership'] = $response->membership;
                  $data['created_by'] = $this->userDataName($response->created_by);
                  $data['updated_by'] = $this->userDataName($response->modified_by);
                  $data['created_date'] = $this->checkDate($response->created_date);
                  $data['updated_date'] = $this->checkDate($response->modified_date);
              }else{
                  $data['result'] = 'fail';
                  $data['message'] = 'Bonus is not registered.';
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
                'bonus_name' => 'required'
            ],[
                $this->bonus_name.".required" => 'The percentage field is required.'
            ]);

            if(!$validate->fails())
            {
                $bid = $request->input('_bid');
                $bonus_level = $request->input('bonus_level');
                $bonus_name = $request->input('bonus_name');
                $bonus_desc = $request->input('bonus_desc');

                    $response = $this->BonusModel->modify($bid,$bonus_level,$bonus_name,$bonus_desc,
                    date('Y-m-d H:i:s'),$request->user()->id);
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
                            '_bid' => 'required|numeric|numeric:0'
                        ]);

            if(!$validate->fails())
            {
                $response = $this->BonusModel->remove($request->input('_bid'));
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

    public function membershipList()
    {
        $container = array();
        $html = '';
        $response = $this->BonusModel->getAllMembership();

        if(count($response)>0)
        {
            foreach ($response as $key => $value) {
                if(!in_array($value->membership,$container)){
                    $container[] = $value->membership;
                    $html .= "<option value='".$value->id."'>".$value->membership."</option>";
                }
            }
        }

        return $html;
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
