<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CurrencyRateModel extends Model
{
    protected $currency_rate = 'edc_currency_rate';
    protected $currency_rate_logs = 'edc_currency_rate_logs';
    protected $countries = 'countries';
    protected $purchaser = 'purchaser';

    public function getCountries()
    {
        $response = DB::table($this->countries)
                    ->get();

        return $response;
    }

    public function countCountries()
    {
        $response = DB::table($this->countries)
                    ->count();

        return $response;
    }

    public function create($country_id,$currency_rate,$edcoin_value,$created_date)
    {
        $data = array(
          'country_id' => $country_id,
          'currency_rate' => $currency_rate,
          'edcoin_value' => $edcoin_value,
          'created_date' => $created_date
        );

        return DB::table($this->currency_rate)->insertGetId($data);
    }

    public function countCurrencyRate($country_id)
    {
        return DB::table($this->currency_rate)->where('country_id','=',$country_id)->count();
    }

    public function modify($id,$country_id,$currency_rate,$edcoin_value,$created_date)
    {
        $data = array(
          'id' => $id,
          'country_id' => $country_id,
          'currency_rate' => $currency_rate,
          'edcoin_value' => $edcoin_value,
          'created_date' => $created_date
        );

        $response =  DB::table($this->currency_rate)
                     ->where('id','=',$id)
                     ->update($data);

        return $response;
    }

    public function getCurrencyRateID($country_id)
    {
        $response = DB::table($this->currency_rate)
                    ->where('country_id','=',$country_id)
                    ->first();

        return $response->id;
    }

    public function createLogs($id,$country_name,$currency_code,$currency_rate,$edcoin_value,$created_date)
    {
        $data = array(
          'id' => $id,
          'country_name' => $country_name,
          'currency_code' => $currency_code,
          'currency_rate' => $currency_rate,
          'edcoin_value' => $edcoin_value,
          'created_date' => $created_date
        );

        return DB::table($this->currency_rate_logs)->insert($data);
    }

    public function getCurrencyData($country_code)
    {
        $response = DB::table($this->countries)
                    // ->leftJoin($this->countries,$this->countries.'.country_code','=',
                    // $this->purchaser.'.purchaser_country')
                    // ->leftJoin($this->currency_rate,$this->currency_rate.'.country_id','=',
                    // $this->countries.'.id')
                    ->where('country_code','=',$country_code)
                    ->first();

        return $response;
    }

    public function fetchCurrencyRateList($search,$start,$limit,$order,$dir,$col_search)
    {
        $response = DB::table($this->currency_rate)
                    ->select($this->currency_rate.'.*',$this->countries.'.country_name',$this->countries.'.currency_code')
                    ->leftJoin($this->countries,$this->countries.'.id','=',$this->currency_rate.'.country_id')
                    ->where(function($query) use ($search,$col_search){
                            if($col_search!="country_name"){
                                $query->orWhere($this->currency_rate.'.'.$col_search,'LIKE','%'.$search.'%');
                            }else{
                                $query->orWhere($this->countries.'.'.$col_search,'LIKE','%'.$search.'%');
                            }
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
                ->select($this->currency_rate.'.*',$this->countries.'.country_name',$this->countries.'.currency_code')
                ->leftJoin($this->countries,$this->countries.'.id','=',$this->currency_rate.'.country_id')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

        return $query;
    }

    public function _count($search,$col_search)
    {
        $total_filtered_data = DB::table($this->currency_rate)
                                ->select($this->currency_rate.'.*',$this->countries.'.country_name',$this->countries.'.currency_code')
                                ->leftJoin($this->countries,$this->countries.'.id','=',$this->currency_rate.'.country_id')
                                ->where(function($query) use ($search,$col_search){
                                        if($col_search!="country_name"){
                                            $query->orWhere($this->currency_rate.'.'.$col_search,'LIKE','%'.$search.'%');
                                        }else{
                                            $query->orWhere($this->countries.'.'.$col_search,'LIKE','%'.$search.'%');
                                        }
                                })
                                ->count();

        return $total_filtered_data;
    }

    public function countCurrencyRateRecords()
    {
        return DB::table($this->currency_rate)->count();
    }

    public function getCurrencyRateLogs($search,$start,$limit,$order,$dir,$col_search,$date_from,$date_to)
    {
        $response = DB::table($this->currency_rate_logs)
                    ->where(function($query) use ($search,$col_search){
                        $query->orWhere($this->currency_rate_logs.'.'.$col_search,'LIKE','%'.$search.'%');
                    })
                    ->whereBetween($this->currency_rate_logs.".created_date",[$date_from,$date_to])
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

        return $response;
    }

    public function showAllRecordLogs($start,$limit,$order,$dir,$date_from,$date_to)
    {
        $query = DB::table($this->currency_rate_logs)
                ->whereBetween($this->currency_rate_logs.".created_date",[$date_from,$date_to])
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

        return $query;
    }

    public function _countLogs($search,$col_search,$date_from,$date_to)
    {
        $total_filtered_data = DB::table($this->currency_rate_logs)
                                ->where(function($query) use ($search,$col_search){
                                    $query->orWhere($this->currency_rate_logs.'.'.$col_search,'LIKE','%'.$search.'%');
                                })
                                ->whereBetween($this->currency_rate_logs.".created_date",[$date_from,$date_to])
                                ->count();

        return $total_filtered_data;
    }

    public function countCurrencyRateLogs()
    {
        return DB::table($this->currency_rate_logs)->count();
    }
}
