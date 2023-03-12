<?php

namespace App\Http\Controllers\Clients;


use App\InforCompany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ContactController;
// use App\Http\Controllers\Clients\ClnBaseCtrl;

class ClnTestCTRL extends ClnBaseCtrl
{
    public function index()
    {
        return view('Client.home');
    }

    public function get_copyimg_done(){
        $this->copy_img_to_standard("45x45");
        $this->copy_img_to_standard("75x75");
        $this->copy_img_to_standard("255x255");
        $this->copy_img_to_standard("425x425");
    }

    private function copy_img_to_standard($folder){
        $product_path = "public/upload/product/";
        $directory = $product_path.$folder;
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
        foreach($scanned_directory as $key=> $file){
            $file_name = str_replace(".jpg", "_".$folder.".jpg", $file);
            $file_name = str_replace(".png", "_".$folder.".png", $file_name);
            copy($directory."/".$file, getcwd()."\\".$product_path."standard_temp\\".$file_name);
        }

    }

}