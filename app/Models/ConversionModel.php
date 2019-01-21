<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ConversionModel extends Model
{
    protected $purchaser = "purchaser";
    protected $purchaser_wallets = "purchaser_wallets";
    protected $company_code = "company_code";
    protected $conversion_setup = "conversion_setup";

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

    public function insertSetup($code,$currency_value,$edpoint_value,$user_id)
    {
        $data = array(
          'company_code' => $code,
          'currency_value' => $currency_value,
          'edpoint_value' => $edpoint_value,
          'created_by' => $user_id
        );

        $response = DB::table($this->conversion_setup)->insert($data);

        return $response;
    }

    public function modifySetup($setup_id,$code,$currency_value,$edpoint_value,$user_id)
    {
        $data = array(
          'company_code' => $code,
          'currency_value' => $currency_value,
          'edpoint_value' => $edpoint_value,
          'updated_by' => $user_id,
          'updated_date' => date('Y-m-d H:i:s')
        );

        $response = DB::table($this->conversion_setup)
                    ->where('id','=',$setup_id)
                    ->update($data);

        return $response;
    }

    public function checkCompanyCode($code)
    {
        $response = DB::table($this->conversion_setup)->where('company_code','=',$code)->count();

        return $response;
    }

    public function fetchConverterList($search,$start,$limit,$order,$dir,$col_search)
    {
        $response = DB::table($this->conversion_setup)
                    ->select($this->conversion_setup.".*",$this->company_code.".description")
                    ->leftJoin($this->company_code,$this->company_code.'.currency_code','=',$this->conversion_setup.'.company_code')
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
        $query = DB::table($this->conversion_setup)
                ->select($this->conversion_setup.".*",$this->company_code.".description",$this->company_code.".currency_code")
                ->leftJoin($this->company_code,$this->company_code.'.code','=',$this->conversion_setup.'.company_code')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

        return $query;
    }

    public function _count($search,$col_search)
    {
        $total_filtered_data = DB::table($this->conversion_setup)
                              ->select($this->conversion_setup.".*",$this->company_code.".description")
                              ->where(function($query) use ($search,$col_search){
                                      $query->orWhere($this->company_code.'.'.$col_search,'LIKE','%'.$search.'%');
                              })
                              ->leftJoin($this->company_code,$this->company_code.'.currency_code','=',$this->conversion_setup.'.company_code')
                              ->count();

        return $total_filtered_data;
    }

    public function countConverterRecords()
    {
        return DB::table($this->conversion_setup)->count();
    }

    public function getConversionRecord($sid)
    {
        $response = DB::table($this->conversion_setup)
                    ->select($this->conversion_setup.".*",$this->company_code.".currency_code")
                    ->leftJoin($this->company_code,$this->company_code.'.code','=',$this->conversion_setup.'.company_code')
                    ->where($this->conversion_setup.'.id','=',$sid)
                    ->first();

        return $response;
    }

    public function remove($sid)
    {
        return DB::table($this->conversion_setup)
                ->where('id','=',$sid)
                ->delete();
    }

    public function checkWalletCode($wallet_code)
    {
        $response = DB::table($this->purchaser_wallets)
                    ->where('wallet_code','=',$wallet_code)
                    ->count();

        return $response;
    }

    public function getEDANumber($wallet_code)
    {
        $response = DB::table($this->purchaser_wallets)
                    ->select($this->purchaser_wallets.'.wallet_code',$this->purchaser.'.purchaser_eda')
                    ->leftJoin($this->purchaser,$this->purchaser.'.id','=',
                      $this->purchaser_wallets.'.purchaser_id')
                    ->where($this->purchaser_wallets.'.wallet_code','=',$wallet_code)
                    ->first();

        return $response;
    }

    public function deductWalletAvailable($purchaser_id,$edcoin_value)
    {
        $data = array(
            'wallet_available_balance' => DB::raw('wallet_available_balance - ' . $edcoin_value)
        );

        $response = DB::table($this->purchaser_wallets)
                    ->where('purchaser_id','=',$purchaser_id)
                    ->update($data);

        return $response;
    }

    public function updateReferralBonus($purchaser_id,$referral_bonus)
    {
        $data = array(
            'referral_bonus' => $referral_bonus
        );

        $response = DB::table($this->purchaser_wallets)
                    ->where('purchaser_id','=',$purchaser_id)
                    ->update($data);

        return $response;
    }

    public function getReferralBonus($purchaser_id)
    {
        $response = DB::table($this->purchaser_wallets)
                    ->where('purchaser_id','=',$purchaser_id)
                    ->first();

        return $response;
    }

    public function getWalletCodeDistributor($wallet_code)
    {
        return DB::table($this->purchaser_wallets)->where('wallet_code','=',$wallet_code)->first();
    }
}
