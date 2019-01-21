<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CurrencyModel extends Model
{
    protected $purchaser = "purchaser";
    protected $purchaser_wallets = "purchaser_wallets";
    protected $company_code = "company_code";
    protected $currency_rate = "currency_rate";

    public function getWalletToken($id)
    {
        $response = DB::table($this->purchaser_wallets)
                    ->where('purchaser_id','=',$id)
                    ->first();

        return $response->wallet_available_balance;
    }

    public function checkEDANumber($eda_number)
    {
        $response = DB::table($this->purchaser)
                    ->where('purchaser_eda','=',$eda_number)
                    ->count();

        return $response;
    }

    public function getID($eda_number)
    {
        $response = DB::table($this->purchaser)
                    ->where('purchaser_eda','=',$eda_number)
                    ->first();

        return $response->id;
    }

    public function getCompanyCode()
    {
        return DB::table($this->company_code)->get();
    }

    public function insertSetup($currency_id,$value,$usd,$user_id)
    {
        $data = array(
          'country_id' => $currency_id,
          'value' => $value,
          'usd' => $usd,
          'created_by' => $user_id
        );

        $response = DB::table($this->currency_rate)->insert($data);

        return $response;
    }

    public function modifySetup($rate_id,$currency_id,$value,$usd,$user_id)
    {
        $data = array(
          'country_id' => $currency_id,
          'value' => $value,
          'usd' => $usd,
          'updated_by' => $user_id,
          'updated_date' => date('Y-m-d H:i:s')
        );

        $response = DB::table($this->currency_rate)
                    ->where('id','=',$rate_id)
                    ->update($data);

        return $response;
    }

    public function checkCompanyCode($country_id)
    {
        $response = DB::table($this->currency_rate)->where('id','=',$country_id)->count();

        return $response;
    }

    public function fetchConverterList($search,$start,$limit,$order,$dir,$col_search)
    {
        $response = DB::table($this->currency_rate)
                    ->select($this->currency_rate.".*",$this->company_code.".description",$this->company_code.".currency_code")
                    ->leftJoin($this->company_code,$this->company_code.'.id','=',$this->currency_rate.'.country_id')
                    ->where(function($query) use ($search,$col_search){
                            $query->orWhere($this->company_code.'.'.$col_search,'LIKE','%'.$search.'%');
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

        return $response;
    }

    public function showAllRecords($start,$limit,$order,$dir)
    {
        $query = DB::table($this->currency_rate)
                ->select($this->currency_rate.".*",$this->company_code.".description",$this->company_code.".currency_code")
                ->leftJoin($this->company_code,$this->company_code.'.id','=',$this->currency_rate.'.country_id')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

        return $query;
    }

    public function _count($search,$col_search)
    {
        $total_filtered_data = DB::table($this->currency_rate)
                              ->select($this->currency_rate.".*",$this->company_code.".description")
                              ->where(function($query) use ($search,$col_search){
                                      $query->orWhere($this->company_code.'.'.$col_search,'LIKE','%'.$search.'%');
                              })
                              ->leftJoin($this->company_code,$this->company_code.'.id','=',$this->currency_rate.'.country_id')
                              ->count();

        return $total_filtered_data;
    }

    public function countConverterRecords()
    {
        return DB::table($this->currency_rate)->count();
    }

    public function getRateRecord($sid)
    {
        $response = DB::table($this->currency_rate)
                    ->select($this->currency_rate.".*",$this->company_code.".currency_code")
                    ->leftJoin($this->company_code,$this->company_code.'.id','=',$this->currency_rate.'.country_id')
                    ->where($this->currency_rate.'.id','=',$sid)
                    ->first();

        return $response;
    }

    public function remove($sid)
    {
        return DB::table($this->currency_rate)
                ->where('id','=',$sid)
                ->delete();
    }
}
