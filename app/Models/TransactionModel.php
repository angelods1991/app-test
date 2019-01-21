<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class TransactionModel extends Model
{
    protected $transaction_logs = "transaction_logs";
    protected $purchaser = "purchaser";

    public function create($reference_no,$purchaser_id,$edc_amount,$edp_amount,$description)
    {
        $data = array(
            'reference_no' => $reference_no,
            'purchaser_id' => $purchaser_id,
            'edc_amount' => $edc_amount,
            'edp_amount' => $edp_amount,
            'description' => $description
        );

        $response = DB::table($this->transaction_logs)->insert($data);

        return $response;
    }

    public function checkTransactionNo($reference_no)
    {
        $response = DB::table($this->transaction_logs)->where('reference_no','=',$reference_no)->count();

        return $response;
    }

    public function countTransactionNo()
    {
        $response = DB::table($this->transaction_logs)->count();

        return $response;
    }

    public function getTransactionRecord()
    {
        $response = DB::table($this->transaction_logs)->get($data);

        return $response;
    }

    public function getTransactionRecordByID($purchaser_id)
    {
        $response = DB::table($this->transaction_logs)
                    ->where('purchaser_id','=',$purchaser_id)
                    ->get();

        return $response;
    }

    public function fetchTransactionList($search,$start,$limit,$order,$dir,$col_search,$date_from,$date_to)
    {
        $response = DB::table($this->transaction_logs)
                    ->select($this->transaction_logs.".*",$this->purchaser.".purchaser_name")
                    ->leftJoin($this->purchaser,$this->purchaser.'.id','=',$this->transaction_logs.'.purchaser_id')
                    ->whereBetween($this->transaction_logs.".created_date",[$date_from,$date_to])
                    ->where(function($query) use ($search,$col_search){
                        $this->selectTableSearch($query,$search,$col_search);
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

        return $response;
    }

    private function selectTableSearch($query,$search,$col_search)
    {
        switch ($col_search) {
          case 'reference_no':
            $query->orWhere($this->transaction_logs.'.'.$col_search,'LIKE','%'.$search.'%');
            break;
          case 'purchaser_name':
            $query->orWhere($this->purchaser.'.'.$col_search,'LIKE','%'.$search.'%');
            break;

          default:
            $query->orWhere($this->transaction_logs.'.'.$col_search,'LIKE','%'.$search.'%');
            break;
        }
    }

    public function showAllRecords($start,$limit,$order,$dir,$date_from,$date_to)
    {
        $query = DB::table($this->transaction_logs)
                ->select($this->transaction_logs.".*",$this->purchaser.".purchaser_name")
                ->leftJoin($this->purchaser,$this->purchaser.'.id','=',$this->transaction_logs.'.purchaser_id')
                ->whereBetween($this->transaction_logs.".created_date",[$date_from,$date_to])
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

        return $query;
    }

    public function _count($search,$col_search,$date_from,$date_to)
    {
        $total_filtered_data = DB::table($this->transaction_logs)
                                ->select($this->transaction_logs.".*",$this->purchaser.".purchaser_name")
                                ->leftJoin($this->purchaser,$this->purchaser.'.id','=',$this->transaction_logs.'.purchaser_id')
                                ->whereBetween($this->transaction_logs.".created_date",[$date_from,$date_to])
                                ->where(function($query) use ($search,$col_search){
                                        $this->selectTableSearch($query,$search,$col_search);
                                })
                                ->count();

        return $total_filtered_data;
    }

    public function countTransactionRecords()
    {
        return DB::table($this->transaction_logs)->count();
    }
}
