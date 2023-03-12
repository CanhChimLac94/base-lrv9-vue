<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Permission extends Model
{
    protected $table = "tbl_Permissions";
    protected $guarded = [];

    public static function get_list_by_role($role_id){
        $sql = "SELECT p.*, rp.is_enable FROM 
                (SELECT * 
                    FROM tbl_permissions 
                    WHERE id IN (SELECT permission_id 
                            FROM tbl_role_permissions 
                            WHERE role_id = $role_id)) as p
                join tbl_role_permissions as rp ON rp.permission_id = p.id";
        $permission = DB::select($sql);
        return $permission;
    }

    public static function get_list_out_role($role_id){
        $sql = "SELECT * 
                FROM tbl_permissions 
                WHERE id NOT IN (SELECT permission_id 
                        FROM tbl_role_permissions 
                        WHERE role_id = $role_id)";
        $permission = DB::select($sql);
        return $permission;
    }
};
