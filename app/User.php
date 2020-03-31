<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Backpack\CRUD\CrudTrait; // <------------------------------- this one
use Spatie\Permission\Traits\HasRoles;// <---------------------- and this one
use Illuminate\Notifications\Notifiable;


use Backpack\PermissionManager\App\Models\Role;
use Backpack\PermissionManager\App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Storage;

class User extends Authenticatable
{
    use Notifiable;
    use CrudTrait; // <----- this
    use HasRoles; // <------ and this


    static public function getCheckUserByRole($user_id, $users_array)
    {
        $roles_ = Role::all();

        $roles = array();
        foreach ($roles_ as $key => $role) {
            if(in_array($role['name'], $users_array))
            $roles[] = $role['id'];
        }

        $id_users = DB::table('model_has_roles')->where('model_id','=',$user_id)->whereIn('role_id', $roles)->pluck('model_id')->toArray();

        if(count($id_users)>0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    static public function getCheckUserByPermission($user_id, $permission)
    {
        $permissions_ = Permission::where('name', $permission)->first();

        if (!$permissions_ || is_null($permissions_))
        {
            return 0;
        }

        $roles_ = DB::table('model_has_roles')->where('model_id', $user_id)->get()->toArray();

        $roles = array();
        for($i=0;$i<sizeof($roles_);$i++) {
            $roles[$roles_[$i]->role_id] = $roles_[$i]->role_id;
        }

        $permissions = DB::table('role_has_permissions')->whereIn('role_id', $roles)->pluck('permission_id')->toArray();

        if (in_array($permissions_['id'], $permissions))
            return 1;
            
        $permissions = DB::table('model_has_permissions')->where('model_id', $user_id)->where('permission_id', $permissions_['id'])->pluck('permission_id')->toArray();
        
        if (in_array($permissions_['id'], $permissions))
            return 1;
        else
            return 0;
    }

    static public function getCheckColumnUsersid($table_name)
    {
        $check = \Schema::hasColumn($table_name, 'users_id');

        if($check)
            return 1;
        else
            return 0;
    }

    static public function getPermissionByUser($user_id, $routeName)
    {
        $roles_ = DB::table('model_has_roles')->where('model_id', $user_id)->get()->toArray();

        $roles = array();
        for($i=0;$i<sizeof($roles_);$i++) {
            $roles[$roles_[$i]->role_id] = $roles_[$i]->role_id;
        }

        $role_has_permissions = DB::table('role_has_permissions')->whereIn('role_id', $roles)->pluck('permission_id')->toArray();

                    
        $model_has_permissions = DB::table('model_has_permissions')->where('model_id', $user_id)->pluck('permission_id')->toArray();
        
        
        $permissions = array();
        for($i=0;$i<sizeof($role_has_permissions);$i++) {
            $permissions[$role_has_permissions[$i]] = $role_has_permissions[$i];
        }
        for($i=0;$i<sizeof($model_has_permissions);$i++) {
            $permissions[$model_has_permissions[$i]] = $model_has_permissions[$i];
        }

        $permissions_name = DB::table('permissions')->whereIn('id', $permissions)->pluck('name')->toArray();

        $route_name = str_replace("crud.", "", $routeName);
        $pos = strpos($route_name, ".");
        $permission__ = substr($route_name, $pos+1, strlen($route_name));
        $route_name = substr($route_name, 0, $pos);

        $permissions_names = array();
        for($i=0;$i<sizeof($permissions_name);$i++) {
            if(strpos($permissions_name[$i], $route_name) !== false)
            {
                $pos_ = strpos($permissions_name[$i], ".");
                $permission_name = substr($permissions_name[$i], $pos+1, strlen($permissions_name[$i]));
                $permissions_names[] = $permission_name;
            }
        }

        return $permissions_names;
    }

}
