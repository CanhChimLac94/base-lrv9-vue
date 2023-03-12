<?php

namespace App\Http\Controllers\Clients;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ContactController;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Clients\ClnBaseCtrl;
// use Google_Client;
// use Google_Service_YouTube;

class ClnHomeCTRL extends ClnBaseCtrl
{
    // ClnBaseCtrl
    public function index()
    {
        return redirect('/news');
        // return view("Client.home", ["playList" => $this->GetVideosData() ]);
    }

    public function introl()
    {
        return view('Client.introduce');
    }

    public function post_AddComment(Request $request)
    {
        $itemContact = [];
        if (isset($this->user) && $this->user != null) {
            $itemContact = [
                "full_name" => $this->user->FullName,
                "email" => $this->user->Email,
                "phone" => $this->user->Phone
            ];
        }
        $itemContact["Contents"] = $request->conten;
        $result = [
            "request" => $request->all(),
            "item" => $itemContact
        ];
        $itemContact = (new ContactController())->_addNew($itemContact);
        return response()->json($result);
    }

    public function post_addContact(Request $request)
    {
        $itemContact = [
            "full_name" => $request->name,
            "email" => $request->email,
            "contents" => $request->msg,
            "phone" => $request->phone,
            "temp" => json_encode(Auth::user())
        ];
        $itemContact = (new ContactController())->_addNew($itemContact);
        return response()->json([
            "status" => "ok",
            "msg" => "success",

        ]);
    }

    public function contact()
    {
        return view("Client.contact");
    }

    public function guide()
    {
        return view('Client.guide');
    }

    public function news()
    {
        return view('Client.news');
    }

    public function test()
    {
        $user = Auth::user();
        $result = [
            "path" => getcwd()
        ];
        return response()->json($result);
    }
}
