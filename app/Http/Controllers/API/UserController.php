<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function update_role(Request $request){
        $uid = $request->user_id;
        $role_id = $request->role_id;

        User::where([
            "id"=>$uid,
            "is_edit" => 1
        ])->update([
            "role_id"=> $role_id
        ]);
    }

}
