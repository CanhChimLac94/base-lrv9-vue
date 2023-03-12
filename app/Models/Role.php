<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    protected $table = "tbl_roles";
    protected $guarded = [];

    public static function add_permission($role_id, $permission_ids){
        $permissions = [];
        foreach ($permission_ids as $k =>$p_id) {
            $permissions[] = [
                "role_id" => $role_id,
                "permission_id" => $p_id,
                "is_enable" => 1
            ];
        }
        DB::table('tbl_role_permissions')->insert($permissions);
    }

    public static function updated_enable_permission($role_id, $per_id, $is_enable){
        DB::table('tbl_role_permissions')->where([
            "role_id" => $role_id,
            "permission_id" => $per_id
        ])->update([
            "is_enable" => $is_enable
        ]);
    }

};