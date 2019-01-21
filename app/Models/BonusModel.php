<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class BonusModel extends Model
{
    protected $bonus_table = 'bonus';
    protected $bonus_activity = 'bonus_activity';
    protected $wallet_table = 'purchaser_wallets';
    protected $package_table = 'packages';
    protected $purchaser_table = 'purchaser';
    protected $users_table = 'users';

    public function checkLevel($bonus_level)
    {
        return DB::table($this->bonus_table)->where('bonus_level','=',$bonus_level)->count();
    }

    public function getBonusData()
    {
        return DB::table($this->bonus_table)->get();
    }

    public function checkBonusID($bid)
    {
        return DB::table($this->bonus_table)->where('id','=',$bid)->count();
    }

    public function checkUpdateLevel($bid,$bonus_level)
    {
        $response = DB::table($this->bonus_table)
                    ->where('id','<>',$bid)
                    ->where('bonus_level','=',$bonus_level)
                    ->count();

        return $response;
    }

    public function modify($bid,$bonus_level,$bonus_name,$bonus_desc,$modified_date,$modified_by)
    {
        $data = array(
            'bonus_name' => $bonus_name,
            'bonus_desc' => $bonus_desc,
            'modified_by' => $modified_by,
            'modified_date' => $modified_date
        );

        return DB::table($this->bonus_table)->where('id','=',$bid)->update($data);
    }

    public function getDataByID($bid)
    {
        return DB::table($this->bonus_table)->where('id','=',$bid)->first();
    }

    public function create($bonus_level,$bonus_name,$bonus_desc,$created_by)
    {
        $data = array(
            'bonus_level' => $bonus_level,
            'bonus_name' => $bonus_name,
            'bonus_desc' => $bonus_desc,
            'created_by' => $created_by
        );

        return DB::table($this->bonus_table)->insert($data);
    }

    public function fetchUsersList($search,$start,$limit,$order,$dir,$col_search,$category)
    {
        $response = DB::table($this->bonus_table)
                    ->where('membership','LIKE','%'.$category.'%')
                    ->where(function($query) use ($search,$col_search,$category){
                            $query->orWhere($this->bonus_table.'.'.$col_search,'LIKE','%'.$search.'%');
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

        return $response;
    }

    public function showAllRecords($start,$limit,$order,$dir)
    {
        $query = DB::table($this->bonus_table)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

        return $query;
    }

    public function _count($search,$col_search,$category)
    {
        $total_filtered_data = DB::table($this->bonus_table)
                                ->where('membership','LIKE','%'.$category.'%')
                                ->where(function($query) use ($search,$col_search,$category){
                                        $query->orWhere($this->bonus_table.'.'.$col_search,'LIKE','%'.$search.'%');
                                })
                                ->count();

        return $total_filtered_data;
    }

    public function countUsersRecords()
    {
        return DB::table($this->bonus_table)->count();
    }

    public function remove($bid)
    {
        return DB::table($this->bonus_table)->where('id','=',$bid)->delete();
    }

    public function getBonusRecords()
    {
        return DB::table($this->bonus_table)->get();
    }

    public function checkBonusWallet($purchaser_id)
    {
        return DB::table($this->wallet_table)->where('purchaser_id','=',$purchaser_id)->count();
    }

    public function insertBonusWallet($purchaser_id,$amount)
    {
        $data = array(
            'purchaser_id' => $purchaser_id,
            'wallet_balance' => $amount,
            'wallet_lock_balance' => $amount - (0.20 * $amount),
            'wallet_available_balance' => $amount - (0.80 * $amount)
        );

        return DB::table($this->wallet_table)->insert($data);
    }

    public function updateBonusWallet($purchaser_id,$amount)
    {
        $data = array(
            'wallet_available_balance' => DB::raw('wallet_available_balance + ' . $amount),
            'referral_bonus' => DB::raw('referral_bonus + ' . $amount)
        );

        $response = DB::table($this->wallet_table)
                    ->where('purchaser_id','=',$purchaser_id)
                    ->update($data);

        return $response;
    }

    public function getPackage($package_id)
    {
        return DB::table($this->package_table)->where('id','=',$package_id)->first();
    }

    public function getBonusByLevel($tier)
    {
        $response = DB::table($this->bonus_table)->where('bonus_level','=',$tier)->first();

        return $response->bonus_name;
    }

    public function getAllBonus()
    {
        $response = DB::table($this->bonus_table)->pluck('bonus_name');

        return $response;
    }

    public function getAllMembership()
    {
        $response = DB::table($this->bonus_table)->get();

        return $response;
    }

    public function insertBonusActivity($purchaser_id,$candidate_id,$amount,$status,$created_by)
    {
        $data = array(
            'purchaser_id' => $purchaser_id,
            'candidate_id' => $candidate_id,
            'amount' => $amount,
            'status' => $status,
            'created_by' => $created_by
        );

        return DB::table($this->bonus_activity)->insert($data);
    }


    public function fetchActivityList($search,$start,$limit,$order,$dir,$col_search,
                                      $date_from,$date_to)
    {
        $response = DB::table($this->bonus_activity)
                    ->select($this->bonus_activity.'.*',$this->purchaser_table.'.purchaser_name',
                    'candidate_table.purchaser_name as candidate_name',$this->users_table.'.name')
                    ->leftJoin($this->users_table,$this->users_table.'.id','=',$this->bonus_activity.'.created_by')
                    ->leftJoin($this->purchaser_table,$this->purchaser_table.'.id','=',$this->bonus_activity.'.purchaser_id')
                    ->leftJoin($this->purchaser_table." as candidate_table",'candidate_table.id','=', $this->bonus_activity.'.candidate_id')
                    ->whereBetween($this->bonus_activity.'.created_at',[$date_from,$date_to])
                    ->where(function($query) use ($search,$col_search){
                      if($col_search=="candidate_name"){
                          $query->orWhere('candidate_table.purchaser_name','LIKE','%'.$search.'%');
                      }else{
                          $query->orWhere($this->purchaser_table.'.'.$col_search,'LIKE','%'.$search.'%');
                      }
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

        return $response;
    }

    public function showAllActivityRecords($start,$limit,$order,$dir,$date_from,$date_to)
    {
        $query = DB::table($this->bonus_activity)
                ->select($this->bonus_activity.'.*',$this->purchaser_table.'.purchaser_name',
                'candidate_table.purchaser_name as candidate_name',$this->users_table.'.name')
                ->leftJoin($this->users_table,$this->users_table.'.id','=',$this->bonus_activity.'.created_by')
                ->leftJoin($this->purchaser_table,$this->purchaser_table.'.id','=',$this->bonus_activity.'.purchaser_id')
                ->leftJoin($this->purchaser_table." as candidate_table",'candidate_table.id','=', $this->bonus_activity.'.candidate_id')
                ->whereBetween($this->bonus_activity.'.created_at',[$date_from,$date_to])
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

        return $query;
    }

    public function _countActivity($search,$col_search,$date_from,$date_to)
    {
        $total_filtered_data = DB::table($this->bonus_activity)
                                ->select($this->bonus_activity.'.*',$this->purchaser_table.'.purchaser_name',
                                'candidate_table.purchaser_name as candidate_name',$this->users_table.'.name')
                                ->leftJoin($this->users_table,$this->users_table.'.id','=',$this->bonus_activity.'.created_by')
                                ->leftJoin($this->purchaser_table,$this->purchaser_table.'.id','=',$this->bonus_activity.'.purchaser_id')
                                ->leftJoin($this->purchaser_table." as candidate_table",'candidate_table.id','=', $this->bonus_activity.'.candidate_id')
                                ->whereBetween($this->bonus_activity.'.created_at',[$date_from,$date_to])
                                ->where(function($query) use ($search,$col_search){
                                  if($col_search=="candidate_name"){
                                      $query->orWhere('candidate_table.purchaser_name','LIKE','%'.$search.'%');
                                  }else{
                                      $query->orWhere($this->purchaser_table.'.'.$col_search,'LIKE','%'.$search.'%');
                                  }
                                })
                                ->count();

        return $total_filtered_data;
    }

    public function countActivityRecords()
    {
        return DB::table($this->bonus_activity)->count();
    }

    public function getMembershipType($bonus_id)
    {
        return DB::table($this->bonus_table)->where('id','=',$bonus_id)->first();
    }

    public function getPercentageByMembershipType($membership)
    {
        return DB::table($this->bonus_table)->where('membership','=',$membership)->get();
    }

    public function getBonusActivity($referral_id,$status)
    {
        $response = DB::table($this->bonus_activity)
                    ->where('purchaser_id','=',$referral_id)
                    ->where('status','=',$status)
                    ->get();

        return $response;
    }

    public function updateBonusActivityStatus($referral_id,$status)
    {
        $data = array(
            'status' => 1
        );

        $response = DB::table($this->bonus_activity)
                    ->where('purchaser_id','=',$referral_id)
                    ->where('status','=',$status)
                    ->update($data);

        return $response;
    }

    public function getPurchaserName($purchaser_id)
    {
        return DB::table($this->purchaser_table)->where('id','=',$purchaser_id)->first();
    }
}
