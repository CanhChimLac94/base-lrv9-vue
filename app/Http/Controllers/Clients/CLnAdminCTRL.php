<?php

namespace App\Http\Controllers\Clients;


use App\InforCompany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ContactController;

class ClnAdminCTRL extends ClnBaseCtrl
{
    
    public function goods()
    {
        $this->__construct();
        return view("Admin.goods");
    }

    public function getorder(){
        $this->__construct();
        return view("Admin.order");
    }
}

