<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RateModel;
use Illuminate\Support\Facades\Validator;

class EdcoinRateController extends Controller
{
    protected $_columns = array(
        1 => 'id',
        2 => 'updated_by'
    );

    public function __construct()
    {
        $this->middleware(['auth','showMethods']);
        $this->RateModel = new RateModel();
    }

    public function index()
    {
        $data['active_menu'] = 'edcoin_rate';
        $data['header_title'] = 'EDCOIN Rate';
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        $data['edcoin_data'] = $this->edcoinData();

        return view('pages.rate.index')->with($data);
    }

    public function activity()
    {
        $data['active_menu'] = 'edcoin_rate_logs';
        $data['header_title'] = 'EDCOIN Rate Logs';
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        return view('pages.rate.edc_logs')->with($data);
    }

    public function getLatestRecordData(Request $request)
    {
        if($request->isMethod('post'))
        {
            $data = $this->edcoinData();
        }
        else
        {
            $data['result'] = 'fail';
            $data['message'] = 'Invalid Method';
        }

        echo json_encode($data);
    }

    private function edcoinData()
    {
        $count = $this->RateModel->countEdcoinRate();

        if($count>0)
        {
            $response = $this->RateModel->getLatestRate();
            $data = array(
              'rate' => number_format(round($response->rate,2),2),
              'created_by' => $this->getUserName($response->created_by),
              'created_date' => $response->created_date,
              'updated_by' => ($response->updated_by==NULL?'None':$this->getUserName($response->updated_by)),
              'updated_date' => ($response->updated_date==NULL?'None':$response->updated_date)
            );
        }
        else
        {
            $data = array(
              'rate' => '0.00',
              'created_by' => 'None',
              'created_date' => 'None',
              'updated_by' => 'None',
              'updated_date' => 'None',
            );
        }

        return $data;
    }

    private function getUserName($user_id)
    {
        $name = $this->RateModel->getUserName($user_id);

        return $name;
    }

    public function registerRate(Request $request)
    {
      if($request->isMethod('POST'))
      {
          $validate = Validator::make($request->all(),[
              '_rate' => 'required|numeric'
          ]);

          $data['result'] = 'fail';

          if(!$validate->fails())
          {
              $information = $this->getRecordInfo();

              $response = $this->RateModel->create($request->user()->id,$request->input('_rate'),$information['created_by'],$information['created_date']);
              $data = $this->createResponse($response);
          }
          else
          {
              $data['message'] = $validate->errors()->first();
          }
      }
      else
      {
        $data['message'] = 'Invalid Method';
      }

      return json_encode($data);
    }

    private function getRecordInfo()
    {
        $count = $this->RateModel->countEdcoinRate();
        if($count>0)
        {
          $information = $this->RateModel->getFirstRecord();
          $data['created_by'] = $information->created_by;
          $data['created_date'] = $information->created_date;
        }
        else
        {
          $data['created_by'] = NULL;
          $data['created_date'] = NULL;
        }

        return $data;
    }

    public function validateEDCoinRate(Request $request)
    {
      if($request->isMethod('POST'))
      {
          $validate = Validator::make($request->all(),[
              '_rate' => 'required|numeric'
          ]);

          $data['result'] = 'fail';

          if(!$validate->fails())
          {
              $data['result'] = 'success';
          }
          else
          {
              $data['message'] = $validate->errors()->first();
          }
        }
        else
        {
            $data['message'] = 'Invalid Method';
        }

        return json_encode($data);
    }

    private function createResponse($response)
    {
        if($response==1)
        {
            $data['result'] = 'success';
            $data['message'] = 'EDCoin rate was updated successfully.';
        }
        else
        {
            $data['result'] = 'fail';
            $data['message'] = 'Unable to register the edcoin rate. Please try again.';
        }

        return $data;
    }

    public function recordList(Request $request)
    {
        if($request->isMethod('post'))
        {
            if(strlen($request->input('_token'))>0)
            {
                $columns = array(0=>'id',1=>'currency_name');

                $total_data = $this->RateModel->countEDCoinRateLogs();

                $total_filtered_data = $total_data;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = '';
                $dir = '';

                $date = $this->setDateTime($request->input('_date_from'),$request->input('_date_to'));

                if(empty($request->input('_search')) && empty($request->input('_category')))
                {
                    $post = $this->RateModel->showAllRecordLogs($start,$limit,$order,$dir,$date['from'],$date['to']);
                }
                else
                {
                    $search = $request->input('_search');
                    $column_search = $request->input('_radio_value');

                    $column_search = (isset($column_search)&&$column_search!=0?$this->_columns[$column_search]:'id');

                    $post = $this->RateModel->getEDCoinRateLogs($search,$start,$limit,$order,$dir,$column_search,$date['from'],$date['to']);

                    $total_filtered_data = $this->RateModel->_countLogs($search,$column_search,$date['from'],$date['to']);
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
            $nestedData['rate'] = $post_data->rate;
            // $nestedData['created_by'] = $this->getUserName($post_data->created_by);
            // $nestedData['created_date'] = $post_data->created_date;
            $nestedData['updated_by'] = ($post_data->updated_by==NULL?"None":$this->getUserName($post_data->updated_by));
            $nestedData['updated_date'] = ($post_data->updated_date==NULL?"None":$post_data->updated_date);

            $iter++;

            $data[] = $nestedData;
        }

        return $data;
    }

    private function setDateTime($date_from,$date_to)
    {
        if(empty($date_from))
        {
            $date_from = date('Y-m-d H:i:s', strtotime(date('Y-m-d').'-30 days'));
            $date_to = date('Y-m-d H:i:s',strtotime("23:59:59"));
        }
        else
        {
            $date_from = date('Y-m-d H:i:s',strtotime($date_from));
            $date_to = date('Y-m-d H:i:s',strtotime($date_to . " 23:59:59"));
        }

        return array('from'=>$date_from,'to'=>$date_to);
    }
}
