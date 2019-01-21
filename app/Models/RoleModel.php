<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class RoleModel extends Model
{
    protected $role_table = 'roles';
    protected $role_menus = 'role_has_menus';
    protected $menu_table = 'web_menus';

    public function checkName($rname)
    {
        return DB::table($this->role_table)->where('name','=',$rname)->count();
    }

    public function checkUpdateRole($pid,$rname)
    {
        $response = DB::table($this->role_table)
                    ->where('id','<>',$pid)
                    ->where('name','=',$rname)
                    ->count();

        return $response;
    }

    public function modify($pid,$rname,$updated_by)
    {
        $data = array(
            'name' => $rname,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $updated_by
        );

        return DB::table($this->role_table)->where('id','=',$pid)->update($data);
    }

    public function getDataByID($pid)
    {
        return DB::table($this->role_table)->where('id','=',$pid)->first();
    }

    public function getRolePermissions($role_id)
    {
        $response = DB::table($this->role_menus)
                    ->leftJoin($this->menu_table,$this->menu_table.'.id','=',$this->role_menus.'.menu_id')
                    ->where($this->role_menus.'.role_id','=',$role_id)
                    ->get();

        return $response;
    }

    public function checkRoleMenuID($role_id,$menu_id)
    {
        $response = DB::table($this->role_menus)
                    ->where($this->role_menus.'.role_id','=',$role_id)
                    ->where($this->role_menus.'.menu_id','=',$menu_id)
                    ->count();

        return $response;
    }

    public function create($rname,$created_by)
    {
        $data = array(
            'name' => $rname,
            'created_by' => $created_by
        );

        return DB::table($this->role_table)->insertGetId($data);
    }

    public function fetchRoleList($search,$start,$limit,$order,$dir,$col_search)
    {
        $response = DB::table($this->role_table)
                    ->where(function($query) use ($search,$col_search){
                            $query->orWhere($this->role_table.'.'.$col_search,'LIKE','%'.$search.'%');
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

        return $response;
    }

    public function showAllRecords($start,$limit,$order,$dir)
    {
        $query = DB::table($this->role_table)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

        return $query;
    }

    public function _count($search,$col_search)
    {
        $total_filtered_data = DB::table($this->role_table)
                                ->where(function($query) use ($search,$col_search){
                                        $query->orWhere($this->role_table.'.'.$col_search,'LIKE','%'.$search.'%');
                                })
                                ->count();

        return $total_filtered_data;
    }

    public function countRoleRecords()
    {
        return DB::table($this->role_table)->count();
    }

    public function remove($pid)
    {
        return DB::table($this->role_table)->where('id','=',$pid)->delete();
    }

    public function getRoleRecords()
    {
        return DB::table($this->role_table)->get();
    }

    public function checkRoleID($role_id)
    {
        return DB::table($this->role_table)->where('id','=',$role_id)->count();
    }

    public function insertRole($role_id,$menu_id)
    {
        $data = array(
          'role_id' => $role_id,
          'menu_id' => $menu_id
        );

        return DB::table($this->role_menus)->insert($data);
    }

    public function removeRolesPermissions($role_id)
    {
        return DB::table($this->role_menus)->where('role_id','=',$role_id)->delete();
    }

    public function getActiveMenus($role_id)
    {
        $response = DB::table($this->role_menus)
                    ->leftJoin($this->menu_table,$this->menu_table.'.id','=',$this->role_menus.'.menu_id')
                    ->where('role_id','=',$role_id)
                    ->get();

        return $response;
    }
}
