<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UsersModel extends Model
{
    protected $_users_table = 'users';
    protected $_users_roles = 'user_has_roles';
    protected $_roles_table = 'roles';
    protected $_role_menus = 'role_has_menus';

    public function fetchUsersList($search,$start,$limit,$order,$dir,$col_search)
    {
        $response = DB::table($this->_users_table)
                    ->select($this->_users_table.'.*',$this->_roles_table.'.name AS role_name')
                    ->leftJoin($this->_users_roles,$this->_users_roles.'.user_id','=',$this->_users_table.'.id')
                    ->leftJoin($this->_roles_table,$this->_roles_table.'.id','=',$this->_users_roles.'.role_id')
                    ->where(function($query) use ($search,$col_search){
                        if($col_search=='role_name'){
                            $query->orWhere($this->_roles_table.'.name','LIKE','%'.$search.'%');
                        }else{
                            $query->orWhere($this->_users_table.'.'.$col_search,'LIKE','%'.$search.'%');
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
        $query = DB::table($this->_users_table)
                ->select($this->_users_table.'.*',$this->_roles_table.'.name AS role_name')
                ->leftJoin($this->_users_roles,$this->_users_roles.'.user_id','=',$this->_users_table.'.id')
                ->leftJoin($this->_roles_table,$this->_roles_table.'.id','=',$this->_users_roles.'.role_id')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

        return $query;
    }

    public function _count($search,$col_search)
    {
        $total_filtered_data = DB::table($this->_users_table)
                                ->select($this->_users_table.'.*',$this->_roles_table.'.name AS role_name')
                                ->leftJoin($this->_users_roles,$this->_users_roles.'.user_id','=',$this->_users_table.'.id')
                                ->leftJoin($this->_roles_table,$this->_roles_table.'.id','=',$this->_users_roles.'.role_id')
                                ->where(function($query) use ($search,$col_search){
                                    if($col_search=='role_name'){
                                        $query->orWhere($this->_roles_table.'.name','LIKE','%'.$search.'%');
                                    }else{
                                        $query->orWhere($this->_users_table.'.'.$col_search,'LIKE','%'.$search.'%');
                                    }
                                })
                                ->count();

        return $total_filtered_data;
    }

    public function countUsersRecords()
    {
        return DB::table($this->_users_table)->count();
    }

    public function checkLevel($bonus_level)
    {
        return DB::table($this->_users_table)->where('bonus_level','=',$bonus_level)->count();
    }

    public function checkBonusID($uid)
    {
        return DB::table($this->_users_table)->where('id','=',$uid)->count();
    }

    public function checkUpdateLevel($bid,$bonus_level)
    {
        $response = DB::table($this->_users_table)
                    ->where('id','<>',$bid)
                    ->where('bonus_level','=',$bonus_level)
                    ->count();

        return $response;
    }

    public function modify($request,$password)
    {
        $data = array(
            'name' => $request->input('account_name'),
            'email' => $request->input('email'),
            'password' => $password,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $request->user()->id
        );

        return DB::table($this->_users_table)
                ->where('id','=',$request->input('_uid'))
                ->update($data);
    }

    public function getDataByID($uid)
    {
        return DB::table($this->_users_table)
                ->select($this->_users_table.'.*',$this->_roles_table.'.name AS role_name',
                $this->_users_roles.'.role_id')
                ->leftJoin($this->_users_roles,$this->_users_roles.'.user_id','=',$this->_users_table.'.id')
                ->leftJoin($this->_roles_table,$this->_roles_table.'.id','=',$this->_users_roles.'.role_id')
                ->where($this->_users_table.'.id','=',$uid)->first();
    }

    public function create($account_name,$email_address,$role,$password,$created_by)
    {
        $data = array(
            'name' => $account_name,
            'email' => $email_address,
            'password' => $password,
            'created_by' => $created_by
        );

        return DB::table($this->_users_table)->insertGetId($data);
    }

    public function remove($uid)
    {
        return DB::table($this->_users_table)->where('id','=',$uid)->delete();
    }

    public function getPasswordByID($uid)
    {
        return DB::table($this->_users_table)->where('id','=',$uid)->first();
    }

    public function checkEmailAddress($email)
    {
        return DB::table($this->_users_table)->where('email','=',$email)->count();
    }

    public function insertUserIdAndRoleId($user_id,$role_id)
    {
        $data = array(
            'user_id' => $user_id,
            'role_id' => $role_id
        );

        return DB::table($this->_users_roles)->insert($data);
    }

    public function updateRoleID($user_id,$role_id)
    {
        $data = array(
            'role_id' => $role_id
        );

        return DB::table($this->_users_roles)->where('user_id','=',$user_id)->update($data);
    }

    public function checkRole($user_id)
    {
        return DB::table($this->_users_roles)
                ->where('user_id','=',$user_id)
                ->count();
    }

    public function countMenus($role_id)
    {
        return DB::table($this->_role_menus)->where('role_id','=',$role_id)->count();
    }

    public function updateProfileName($user_id,$name)
    {
        $data = array(
          'name' => $name
        );

        $response = DB::table($this->_users_table)->where('id','=',$user_id)->update($data);

        return $response;
    }

    public function modifyPassword($user_id,$password)
    {
        $data = array(
          'password' => $password
        );

        $response = DB::table($this->_users_table)->where('id','=',$user_id)->update($data);

        return $response;
    }
}
