<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class PermissionModel extends Model
{
    protected $permission_table = 'permissions';

    public function checkName($pname)
    {
        return DB::table($this->permission_table)->where('name','=',$pname)->count();
    }

    public function checkPermissionID($pid)
    {
        return DB::table($this->permission_table)->where('id','=',$pid)->count();
    }

    public function checkUpdatePermission($pid,$pname)
    {
        $response = DB::table($this->permission_table)
                    ->where('id','<>',$pid)
                    ->where('name','=',$pname)
                    ->count();

        return $response;
    }

    public function modify($pid,$pname,$updated_by)
    {
        $data = array(
            'name' => $pname,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $updated_by
        );

        return DB::table($this->permission_table)->where('id','=',$pid)->update($data);
    }

    public function getDataByID($pid)
    {
        return DB::table($this->permission_table)->where('id','=',$pid)->first();
    }

    public function create($pname,$created_by)
    {
        $data = array(
            'name' => $pname,
            'created_by' => $created_by
        );

        return DB::table($this->permission_table)->insert($data);
    }

    public function fetchPermissionList($search,$start,$limit,$order,$dir,$col_search)
    {
        $response = DB::table($this->permission_table)
                    ->where(function($query) use ($search,$col_search){
                            $query->orWhere($this->permission_table.'.'.$col_search,'LIKE','%'.$search.'%');
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

        return $response;
    }

    public function showAllRecords($start,$limit,$order,$dir)
    {
        $query = DB::table($this->permission_table)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

        return $query;
    }

    public function _count($search,$col_search)
    {
        $total_filtered_data = DB::table($this->permission_table)
                                ->where(function($query) use ($search,$col_search){
                                        $query->orWhere($this->permission_table.'.'.$col_search,'LIKE','%'.$search.'%');
                                })
                                ->count();

        return $total_filtered_data;
    }

    public function countPermissionRecords()
    {
        return DB::table($this->permission_table)->count();
    }

    public function remove($pid)
    {
        return DB::table($this->permission_table)->where('id','=',$pid)->delete();
    }

    public function getPermissionRecords()
    {
        return DB::table($this->permission_table)->get();
    }

    public function getPermissionNameByID($permission_id)
    {
        return DB::table($this->permission_table)->where('id','=',$permission_id)->first();
    }
}
