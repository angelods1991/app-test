<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class RateModel extends Model
{
    protected $edcoin_rate = 'edc_edcoin_rate_logs';
    protected $users = 'users';

    public function getLatestRate()
    {
        $response = DB::table($this->edcoin_rate)
                    ->orderBy('id','DESC')
                    ->first();

        return $response;
    }

    public function countEdcoinRate()
    {
        $response = DB::table($this->edcoin_rate)
                    ->count();

        return $response;
    }

    public function create($user_id,$rate,$created_by,$created_date)
    {
        $data = $this->createData($user_id,$rate,$created_by,$created_date);
        $response = DB::table($this->edcoin_rate)->insert($data);

        return $response;
    }

    private function createData($user_id,$rate,$created_by,$created_date)
    {
      if(is_null($created_by))
      {
          $data = array(
            'rate' => $rate,
            'created_by' => $user_id,
            'updated_by' => $user_id,
            'updated_date' => date('Y-m-d H:i:s')
          );
      }
      else
      {
          $data = array(
            'rate' => $rate,
            'created_by' => $created_by,
            'created_date' => $created_date,
            'updated_by' => $user_id,
            'updated_date' => date('Y-m-d H:i:s')
          );
      }

      return $data;
    }

    public function getFirstRecord()
    {
        return DB::table($this->edcoin_rate)->first();
    }

    public function getUserName($user_id)
    {
        return DB::table($this->users)->where('id','=',$user_id)->pluck('name')->first();
    }

    public function getEDCoinRateLogs($search,$start,$limit,$order,$dir,$col_search,$date_from,$date_to)
    {
        $response = DB::table($this->edcoin_rate)
                    ->select($this->edcoin_rate.'.*',$this->users.'.name')
                    ->leftJoin($this->users,$this->users.'.id','=',$this->edcoin_rate.'.updated_by')
                    ->where(function($query) use ($search,$col_search){
                        if($col_search=="updated_by"){
                          $query->orWhere($this->users.'.name','LIKE','%'.$search.'%');
                        }else{
                          $query->orWhere($this->edcoin_rate.'.'.$col_search,'LIKE','%'.$search.'%');
                        }
                    })
                    ->whereBetween($this->edcoin_rate.".updated_date",[$date_from,$date_to])
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('id','DESC')
                    ->get();

        return $response;
    }

    public function showAllRecordLogs($start,$limit,$order,$dir,$date_from,$date_to)
    {
        $query = DB::table($this->edcoin_rate)
                ->select($this->edcoin_rate.'.*',$this->users.'.name')
                ->leftJoin($this->users,$this->users.'.id','=',$this->edcoin_rate.'.updated_by')
                ->whereBetween($this->edcoin_rate.".updated_date",[$date_from,$date_to])
                ->offset($start)
                ->limit($limit)
                ->orderBy('id','DESC')
                ->get();

        return $query;
    }

    public function _countLogs($search,$col_search,$date_from,$date_to)
    {
        $total_filtered_data = DB::table($this->edcoin_rate)
                                ->select($this->edcoin_rate.'.*',$this->users.'.name')
                                ->leftJoin($this->users,$this->users.'.id','=',$this->edcoin_rate.'.updated_by')
                                ->where(function($query) use ($search,$col_search){
                                    if($col_search=="updated_by"){
                                      $query->orWhere($this->users.'.name','LIKE','%'.$search.'%');
                                    }else{
                                      $query->orWhere($this->edcoin_rate.'.'.$col_search,'LIKE','%'.$search.'%');
                                    }
                                })
                                ->whereBetween($this->edcoin_rate.".updated_date",[$date_from,$date_to])
                                ->count();

        return $total_filtered_data;
    }

    public function countEDCoinRateLogs()
    {
        return DB::table($this->edcoin_rate)->count();
    }


}
