<?php

namespace App\Http\Controllers\Clients;


use App\InforCompany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ContactController;
use Google_Client;
use Google_Service_YouTube;

// use App\Http\Controllers\Clients\ClnBaseCtrl;

class ClnYoutubeCTRL extends ClnBaseCtrl
{
    public $fileName = "./public/youtube_data.json";

    public function index()
    {
        return "error: 404";
    }

}
