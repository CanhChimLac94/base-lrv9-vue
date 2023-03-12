<?php

namespace App\Http\Controllers\API;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct(Contact::class);
    }

    public function change_checked(Request $request){
        $id = $request->id;
        $is_checked = $request->is_checked;
        // trace($id);
        Contact::where([
            "id" => $id
        ])->update([
            "is_checked" => $is_checked == "true"?1:0
        ]);
        return response()->json([
            "status" => "ok",
            "msg" => "success"
        ]);
    }
}
