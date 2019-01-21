<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class DownloadModel extends Model
{
      protected $purchaser = 'purchaser';
      protected $family_table = 'family_tree';
      protected $bonus_table = 'bonus';
      protected $users = 'users';

      public function getMemberMain()
      {
          $response = DB::table($this->purchaser)
                      ->select($this->purchaser.'.*')
                      ->get();

          return $response;
      }

      public function getDistributorDetails()
      {
          $response = DB::table($this->purchaser)
                      ->select($this->purchaser.'.*',$this->bonus_table.".membership")
                      ->leftJoin($this->family_table,$this->family_table.'.purchaser_id','=',$this->purchaser.'.id')
                      ->leftJoin($this->bonus_table,$this->bonus_table.'.id','=',$this->family_table.'.bonus_id')
                      ->get();

          return $response;
      }

      public function selectMemberFirstLevel($purchaser_id)
      {
          $response = DB::table($this->family_table)
                      ->select($this->family_table.'.*','purchaser_packages.package_paid_amount','purchaser_packages.package_status')
                      ->leftJoin('purchaser_packages','purchaser_packages.purchaser_id','=',$this->family_table.'.purchaser_id')
                      ->where($this->family_table.".purchaser_id_upline","=",$purchaser_id)
                      ->get();

          return $response;
      }

      public function getSelectedDistributorPackage($purchaser_id)
      {
          $response = DB::table('purchaser_packages')
                      ->where("purchaser_packages.purchaser_id","=",$purchaser_id)
                      ->get();

          return $response;
      }
}
