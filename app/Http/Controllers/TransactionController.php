<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionModel;

class TransactionController extends Controller
{
    protected $_columns = array(
        1 => 'reference_no',
        2 => 'purchaser_name'
    );

    public function __construct()
    {
        $this->middleware(['auth','showMethods']);
        $this->TransactionModel = new TransactionModel();
    }

    public function index()
    {
        $data['active_menu'] = 'transaction_logs';
        $data['header_title'] = 'Transaction Logs';
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        return view('pages.transaction.index',$data);
    }

    public function recordList(Request $request)
    {
        if($request->isMethod('post'))
        {
            if(strlen($request->input('_token'))>0)
            {
                $columns = array(0=>'reference_no',1=>'purchaser_name',2=>'edc_amount',5=>'created_date');

                $total_data = $this->TransactionModel->countTransactionRecords();

                $total_filtered_data = $total_data;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');

                $date = $this->setDateTime($request->input('_date_from'),$request->input('_date_to'));

                if(empty($request->input('_search')) && empty($request->input('_category')))
                {
                    $post = $this->TransactionModel->showAllRecords($start,$limit,$order,$dir,$date['from'],$date['to']);
                }
                else
                {
                    $search = $request->input('_search');
                    $column_search = $request->input('_radio_value');

                    $column_search = $this->selectColumnSearch($column_search);

                    $post = $this->TransactionModel->fetchTransactionList($search,$start,$limit,$order,$dir,$column_search,$date['from'],$date['to']);

                    $total_filtered_data = $this->TransactionModel->_count($search,$column_search,$date['from'],$date['to']);
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
            $date_to = date('Y-m-d H:i:s',strtotime("23:59:59"));
        }
        else
        {
            $date_from = date('Y-m-d H:i:s',strtotime($date_from));
            $date_to = date('Y-m-d H:i:s',strtotime($date_to . " 23:59:59"));
        }

        return array('from'=>$date_from,'to'=>$date_to);
    }

    private function selectColumnSearch($column_search)
    {
        switch ($column_search) {
          case '1':
              $column_search = "reference_no";
            break;
          case '2':
              $column_search = $this->_columns[$column_search];
            break;

          default:
            $column_search = 'reference_no';
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
            $nestedData['reference_no'] = $post_data->reference_no;
            $nestedData['purchaser_name'] = $post_data->purchaser_name;
            $nestedData['edc_amount'] = number_format($post_data->edc_amount,2);
            $nestedData['edp_amount'] = number_format($post_data->edp_amount,2);
            $nestedData['description'] = $post_data->description;
            $nestedData['created_date'] = $post_data->created_date;

            $iter++;

            $data[] = $nestedData;
        }

        return $data;
    }
}
