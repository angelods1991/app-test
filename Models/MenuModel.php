<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class MenuModel extends Model
{
    protected $web_menus = 'web_menus';
    protected $menu_permisions = 'menu_has_permissions';
    protected $permissions_table = 'permissions';

    public function checkName($menu_name)
    {
        return DB::table($this->web_menus)->where('name','=',$menu_name)->count();
    }

    public function checkUpdateMenu($mid,$menu_name)
    {
        $response = DB::table($this->web_menus)
                    ->where('id','<>',$mid)
                    ->where('name','=',$menu_name)
                    ->count();

        return $response;
    }

    public function modify($mid,$menu_name,$menu_link,$menu_type,$updated_by)
    {
        $data = array(
            'name' => $menu_name,
            'link' => $menu_link,
            'type' => $menu_type,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $updated_by
        );

        return DB::table($this->web_menus)->where('id','=',$mid)->update($data);
    }

    public function getDataByID($mid)
    {
        return DB::table($this->web_menus)->where('id','=',$mid)->first();
    }

    public function getMenuPermissions($menu_id)
    {
        $response = DB::table($this->menu_permisions)
                    ->leftJoin($this->permissions_table,$this->permissions_table.'.id','=',$this->menu_permisions.'.permission_id')
                    ->where($this->menu_permisions.'.menu_id','=',$menu_id)
                    ->get();

        return $response;
    }

    public function create($request)
    {
        $data = array(
            'name' => $request->input('menu_name'),
            'link' => $request->input('menu_link'),
            'type' => $request->input('menu_type'),
            'created_by' => $request->user()->id
        );

        return DB::table($this->web_menus)->insertGetId($data);
    }

    public function checkMenuType($request)
    {
        $response = DB::table($this->web_menus)
                    ->where('name','=',$request->input('menu_name'))
                    ->where('type','=',$request->input('menu_type'))
                    ->count();

        return $response;
    }

    public function checkMenuTypeUpdate($request)
    {
        $response = DB::table($this->web_menus)
                    ->where('id','!=',$request->input('_mid'))
                    ->where('name','=',$request->input('menu_name'))
                    ->where('type','=',$request->input('menu_type'))
                    ->count();

        return $response;
    }

    public function fetchMenuList($search,$start,$limit,$order,$dir,$col_search)
    {
        $response = DB::table($this->web_menus)
                    ->where(function($query) use ($search,$col_search){
                            $query->orWhere($this->web_menus.'.'.$col_search,'LIKE','%'.$search.'%');
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

        return $response;
    }

    public function showAllRecords($start,$limit,$order,$dir)
    {
        $query = DB::table($this->web_menus)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

        return $query;
    }

    public function _count($search,$col_search)
    {
        $total_filtered_data = DB::table($this->web_menus)
                                ->where(function($query) use ($search,$col_search){
                                        $query->orWhere($this->web_menus.'.'.$col_search,'LIKE','%'.$search.'%');
                                })
                                ->count();

        return $total_filtered_data;
    }

    public function countMenuRecords()
    {
        return DB::table($this->web_menus)->count();
    }

    public function remove($mid)
    {
        return DB::table($this->web_menus)->where('id','=',$mid)->delete();
    }

    public function getMenuType()
    {
        return DB::table($this->web_menus)->get();
    }

    public function getMenuRecords($request)
    {
        return DB::table($this->web_menus)
        ->where('type','=',$request->input('_type'))
        ->get();
    }

    public function checkMenuID($menu_id)
    {
        return DB::table($this->web_menus)->where('id','=',$menu_id)->count();
    }

    public function insertMenu($menu_id,$permission_id)
    {
        $data = array(
          'menu_id' => $menu_id,
          'permission_id' => $permission_id
        );

        return DB::table($this->menu_permisions)->insert($data);
    }

    public function removeMenuPermissions($menu_id)
    {
        return DB::table($this->menu_permisions)->where('menu_id','=',$menu_id)->delete();
    }

    public function getPermissionNameByID($menu_id)
    {
        return DB::table($this->web_menus)->where('id','=',$permission_id)->first();
    }

    public function checkMenuPermissionID($menu_id,$permission_id)
    {
        $response = DB::table($this->menu_permisions)
                    ->where('menu_id','=',$menu_id)
                    ->where('permission_id','=',$permission_id)
                    ->count();

        return $response;
    }
}
