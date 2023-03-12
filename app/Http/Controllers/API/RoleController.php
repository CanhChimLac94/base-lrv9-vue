<?php

namespace App\Http\Controllers\API;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct(Role::class);
    }

    public function get_permissions(Request $request){
        $role_id = $request->role_id;
        // get permissions in role
        // $sql = "SELECT * 
        //         FROM tbl_permission 
        //         WHERE id in (SELECT peermission_id 
        //                 FROM tbl_role_permissions 
        //                 WHERE role_id = $role_id)";
        $permission_in = Permission::get_list_by_role($role_id);
        $permission_out = Permission::get_list_out_role($role_id);

        return response()->json([
            "permission_in" => $permission_in,
            "permission_out" => $permission_out,
        ]);
    }

    public function add_permissions(Request $request){
        $role_id = $request->role_id;
        $permission_ids = $request->permission_ids;
        Role::add_permission($role_id, $permission_ids);
    }

    public function update_enable(Request $request){
        $role_id = $request->role_id;
        $permission_id = $request->permission_id;
        $is_enable = $request->is_enable;
        Role::updated_enable_permission($role_id, $permission_id, $is_enable);
    }

}
