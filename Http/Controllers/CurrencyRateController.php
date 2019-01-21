<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CurrencyRateModel;
use App\Models\RateModel;

class CurrencyRateController extends Controller
{
    protected $access_key = "1eefe393f49e770e0e86cf5d45a15e28";
    protected $base = "USD";
    protected $timestamp = '';
    protected $_columns = array(
        1 => 'id',
        2 => 'country_name',
    );

    public function __construct()
    {
        $this->middleware(['auth','showMethods']);
        $this->CurrencyModel = new CurrencyRateModel();
        $this->RateModel = new RateModel();
        $this->timestamp = date('Y-m-d H:i:s');
    }

    public function reloadCurrencyRate()
    {
        $codes = $this->getCurrencyCode();
        $response = $this->getLatestCurrencyCode($codes);

        if(!isset($response['result']))
        {
            $decoded = json_decode($response);
            $edcoin_rate = $this->getEDCoinCurrencyRate();

            if($decoded->success!=false)
            {
                $countries = $this->CurrencyModel->getCountries();
                foreach ($countries as $value)
                {
                    $country_id = $value->id;
                    $country_name = $value->country_name;
                    $currency_code = $value->currency_code;
                    $currency_rate = $decoded->rates->$currency_code;
                    $edcoin_value = $currency_rate * $edcoin_rate;

                    $count_currency = $this->CurrencyModel->countCurrencyRate($country_id);
                    if($count_currency>0)
                    {
                        $id = $this->CurrencyModel->getCurrencyRateID($country_id);
                        $this->CurrencyModel->modify($id,$country_id,
                        $currency_rate,$edcoin_value,$this->timestamp);
                    }
                    else
                    {
                        $id = $this->CurrencyModel->create($country_id,
                        $currency_rate,$edcoin_value,$this->timestamp);
                    }

                    $this->CurrencyModel->createLogs($id,$country_name,$currency_code,$currency_rate,$edcoin_value,$this->timestamp);
                }

                $data['result'] = 'success';
                $data['message'] = 'Currency rate was successfully registered';
            }
            else
            {
                $data['result'] = 'fail';
                $data['type'] = $decoded->error->type;
                $data['message'] = $decoded->error->info;
            }
        }
        else
        {
            $data['result'] = $response['result'];
            $data['message'] = $response['message'];
        }

        return $data;
    }

    private function getLatestCurrencyCode($currency_code)
    {
        $activity = 'latest';
        $parameters = 'access_key='.$this->access_key.'&base='.$this->base.'&symbols='.$currency_code;

        $response = $this->getCurrencyRate($activity,$parameters);

        if($response==false)
        {
            $data['result'] = 'fail';
            $data['message'] = 'Connection Lost!';
        }
        else
        {
            $data = $response;
        }

        return $data;
    }

    private function parseAPIConversionResponse()
    {
        generateCurrencyRate($currency_code);
    }

    private function generateCurrencyRate($currency_code)
    {
        $activity = 'convert';
        $edcoin_rate = $this->getEDCoinCurrencyRate();

        $parameters = 'access_key='.$this->access_key.'&from='.$this->base.
        '&to='.$currency_code.'&amount='.$edcoin_rate;

        $response = $this->getCurrencyRate($activity,$parameters);

        if($response==false)
        {
            $data['result'] = 'fail';
            $data['message'] = 'Connection Lost!';
        }
        else
        {
            $data = $response;
        }

        return $data;
    }

    private function getEDCoinCurrencyRate()
    {
        $count = $this->RateModel->countEdcoinRate();
        if($count>0)
        {
            $info = $this->RateModel->getLatestRate();
            $edcoin_rate = round($info->rate,2);
        }
        else
        {
            $edcoin_rate = 0.00;
        }

        return $edcoin_rate;
    }

    private function getCurrencyCode()
    {
        $code_container = array();
        $currency_code = '';
        $iter = 1;

        $count = $this->CurrencyModel->countCountries();

        if($count>0)
        {
            $countries = $this->CurrencyModel->getCountries();
            foreach ($countries as $value)
            {

                if($iter<$count)
                {
                  $concat = ',';
                }
                else
                {
                  $concat = '';
                }

                if(!in_array($value->currency_code,$code_container)):
                  $currency_code .= $value->currency_code.$concat;
                  $code_container[] = $value->currency_code;
                endif;

                $iter++;
            }
        }
        else
        {
            $currency_code = false;
        }

        return $currency_code;
    }

    private function getCurrencyRate($activity,$parameters)
    {
        $init = curl_init();

        curl_setopt_array($init, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => 'http://data.fixer.io/api/'.$activity.'?'.$parameters,
        ));

        $response = curl_exec($init);

        curl_close($init);

        return $response;
    }

    public function recordList(Request $request)
    {
        if($request->isMethod('post'))
        {
            if(strlen($request->input('_token'))>0)
            {
                $columns = array(0=>'id',1=>'currency_name');

                $total_data = $this->CurrencyModel->countCurrencyRateRecords();

                $total_filtered_data = $total_data;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');

                if(empty($request->input('_search')))
                {
                    $post = $this->CurrencyModel->showAllRecords($start,$limit,$order,$dir);
                }
                else
                {
                    $search = $request->input('_search');
                    $column_search = $request->input('_radio_value');

                    $column_search = (isset($column_search)&&$column_search!=0?$this->_columns[$column_search]:'country_name');

                    $post = $this->CurrencyModel->fetchCurrencyRateList($search,$start,$limit,$order,$dir,$column_search);

                    $total_filtered_data = $this->CurrencyModel->_count($search,$column_search);
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
            $nestedData['country_name'] = $post_data->country_name;
            $nestedData['currency_rate'] = round($post_data->currency_rate,6);
            $nestedData['edcoin_value'] = round($post_data->edcoin_value,6);
            $nestedData['updated_date'] = $post_data->created_date;

            $iter++;

            $data[] = $nestedData;
        }

        return $data;
    }

    public function activity()
    {
        $data['active_menu'] = 'currency_rate_logs';
        $data['header_title'] = 'Currency Rate Logs';
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        return view('pages.rate.currency_logs')->with($data);
    }

    public function recordLogs(Request $request)
    {
        if($request->isMethod('post'))
        {
            if(strlen($request->input('_token'))>0)
            {
                $columns = array(0=>'id',1=>'country_name',4=>'created_date');

                $total_data = $this->CurrencyModel->countCurrencyRateLogs();

                $total_filtered_data = $total_data;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');

                $date = $this->setDateTime($request->input('_date_from'),$request->input('_date_to'));

                if(empty($request->input('_search')) && empty($request->input('_category')))
                {
                    $post = $this->CurrencyModel->showAllRecordLogs($start,$limit,$order,$dir,$date['from'],$date['to']);
                }
                else
                {
                    $search = $request->input('_search');
                    $column_search = $request->input('_radio_value');

                    $column_search = (isset($column_search)&&$column_search!=0?$this->_columns[$column_search]:'country_name');

                    $post = $this->CurrencyModel->getCurrencyRateLogs($search,$start,$limit,$order,$dir,$column_search,$date['from'],$date['to']);

                    $total_filtered_data = $this->CurrencyModel->_countLogs($search,$column_search,$date['from'],$date['to']);
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

    private function setDateTime($date_from,$date_to)
    {
        if(empty($date_from))
        {
            $date_from = date('Y-m-d H:i:s', strtotime(date('Y-m-d').'-30 days'));
            $date_to = date('Y-m-d H:i:s');
        }
        else
        {
            $date_from = date('Y-m-d H:i:s',strtotime($date_from));
            $date_to = date('Y-m-d H:i:s',strtotime($date_to . " 23:59:59"));
        }

        return array('from'=>$date_from,'to'=>$date_to);
    }
}
