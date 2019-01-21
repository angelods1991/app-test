<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class FamilyTreeModel extends Model
{
    protected $family_table = 'family_tree';
    protected $purchasers = 'purchaser';

    public function getpurchaserInformation($purchaser_id)
    {
        $response = DB::table($this->purchasers)
                    ->where('id','=',$purchaser_id)
                    ->get();

        return $response;
    }

    public function getpurchaserInformationByID($purchaser_id)
    {
        $response = DB::table($this->purchasers)
                    ->where('id','=',$purchaser_id)
                    ->first();

        return $response;
    }

    public function getCategory($child_id)
    {
        $response = DB::table($this->family_table)
                    ->select($this->purchasers.'.*',$this->family_table.'.purchaser_id_upline',$this->family_table.'.purchaser_id')
                    ->leftJoin($this->purchasers,$this->purchasers.'.id','=',$this->family_table.'.purchaser_id')
                    ->where($this->family_table.'.purchaser_id_upline','=',$child_id)
                    ->get();

        return $response;
    }

    public function checkChildRecord($purchaser_id_upline)
    {
        return DB::table($this->family_table)->where('purchaser_id_upline','=',$purchaser_id_upline)->count();
    }

    public function getChildRecord($purchaser_id_upline)
    {
        return DB::table($this->family_table)->where('purchaser_id_upline','=',$purchaser_id_upline)->get();
    }

    public function getChildToParentRecord($purchaser_id)
    {
        return DB::table($this->family_table)->where('purchaser_id','=',$purchaser_id)->first();
    }

    public function getParentID($id)
    {
        return DB::table($this->family_table)->where('purchaser_id','=',$id)->first();
    }

    public function checkParentID($id)
    {
        return DB::table($this->purchasers)->where('id','=',$id)->count();
    }

    public function insertFamilyTree($user_id,$id,$referral_id,$bonus_id,$level)
    {
        $data = array(
            'purchaser_id' => $id,
            'purchaser_id_upline' => $referral_id,
            'bonus_id' => $bonus_id,
            'purchaser_level' => $level,
            'created_by' => $user_id,
            'modified_by' => $user_id,
            'modified_date' => date('Y-m-d H:i:s')
        );

        return DB::table($this->family_table)->insert($data);
    }

    public function modify($request,$id,$level)
    {
        $data = array(
            //'purchaser_id_upline' => $request->input('referral'),
            'bonus_id' => $request->input('purchaser_membership'),
            'purchaser_level' => $level,
            'modified_by' => $request->user()->id,
            'modified_date' => date('Y-m-d H:i:s'),
        );

        return DB::table($this->family_table)->where('purchaser_id','=',$id)->update($data);
    }

    public function getFamilyInformation($purchaser_id)
    {
        $response = DB::table($this->family_table)->where('purchaser_id','=',$purchaser_id)->first();

        return $response;
    }

    public function updateFamilyTreeLevel($purchaser_id,$purchaser_level)
    {
        $data = array(
          'purchaser_level' => $purchaser_level
        );

        return DB::table($this->family_table)->where('purchaser_id','=',$purchaser_id)->update($data);
    }
}
