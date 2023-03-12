<?php

namespace App\Http\Controllers\Clients;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ContactController;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Clients\ClnBaseCtrl;

class ClnDuongtamkhoiCTRL extends ClnBaseCtrl
{
    // ClnBaseCtrl
    public function index()
    {
        return "ahihi";
        // return view("Client.duongtamkhoi.index");
    }

    public function get_theme01(){
        return response()->json([
            "theme dung tv"
        ]);
        
    }
    
}