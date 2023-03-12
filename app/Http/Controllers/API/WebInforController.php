<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebInforController extends Controller
{
    public $fileName = "./web_infor.json";

    public function GetInfo()
    {
        if (!file_exists($this->fileName)) {
            file_put_contents($this->fileName, "{}");
        }
        $file = file_get_contents($this->fileName);
        $webInfor = json_decode($file);
        return response()->json($webInfor);
    }

    public function GetList()
    {
        return $this->GetInfo();
    }

    public function Update(Request $request)
    {
        $data = json_encode($request->all());
        file_put_contents($this->fileName, $data);
        return response()->json("ok");
    }
}
