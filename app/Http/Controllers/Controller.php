<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

use Exception;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public $user = null;

    public function __construct()
    {
        if (Auth::check()) {
            $this->user = Auth::user();
        }
    }
    //-----
    public function IsAdmin(){
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }
        if($user != null && $user->IdRoleGroup != null){
            $roleAdmin = Role::where("name", "Admin")
                        ->orWhere("name", "Administrator")
                        ->get();
            foreach($roleAdmin as $a){
                if($user->IdRoleGroup == $a->id)
                    return true;
            }
        }
        return false;
    }

    protected function IsAdministrator(){
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }
        if($user != null && $user->IdRoleGroup != null){
            $roleAdmin = Role::where("name", "Administrator")
                        ->get();
            foreach($roleAdmin as $a){
                if($user->IdRoleGroup == $a->id)
                    return true;
            }
        }
        return false;
    }

    public function get_url($url)
    {
    	$opts = array(
    		'http'=>array(
    			'method'=>"GET",
    			'header'=>"Accept-Language: en-US,en;q=0.8rn" .
    						"Accept-Encoding: gzip,deflate,sdchrn" .
    						"Accept-Charset:UTF-8,*;q=0.5rn" .
    						"User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:19.0) Gecko/20100101 Firefox/19.0 FirePHP/0.4rn"
    		)
    	);
    	$context = stream_context_create($opts);
    	$content = file_get_contents($url ,false,$context); 
    	foreach($http_response_header as $c => $h)
    	{
    		if(stristr($h, 'content-encoding') and stristr($h, 'gzip'))
    		{
    			$content = gzinflate( substr($content,10,-8) );
    		}
    	}
        $content = html_entity_decode($content, ENT_HTML5, 'UTF-8');
        $content = html_entity_decode($content, ENT_QUOTES, "UTF-8");
        // $content = htmlspecialchars_decode($content);
        return $content;
    }

    public function createThumbnail($image_name, $new_width, $new_height, $imgResourcePath, $imgTargetPath, $new_img_name = "")
    {
        try{
            if(!file_exists($imgTargetPath)){
                mkdir($imgTargetPath, 7777, true);
            }
            if($new_img_name == ""){
                $new_img_name = $image_name;
            }
            $imgResourcePath = getcwd()."\\".$imgResourcePath."\\";
            $imgTargetPath = getcwd()."\\".$imgTargetPath."\\";
            $path = $imgResourcePath . '\\' . $image_name;
            //$image_name = str_replace(".png", ".jpg", $image_name);
            
            //----------------------------------------------
            $mime = getimagesize($path);
            if($mime['mime']=='image/png') { 
                $src_img = imagecreatefrompng($path);
            }
            if($mime['mime']=='image/jpg' 
                || $mime['mime']=='image/jpeg' 
                || $mime['mime']=='image/pjpeg') {
                $src_img = imagecreatefromjpeg($path);
            }  
            if($mime['mime']=='image/webp'){
                $src_img = imagecreatefromwebp($path);
            }
            $old_w = imageSX($src_img);
            $old_h = imageSY($src_img);
            //-----------------------------------------------------------
            $thumb_h = $new_height;
            $thumb_w = $new_width;
            if($new_height == 0 && $new_width == 0){
                $thumb_h = $old_h;
                $thumb_w = $old_w;
            }else if($new_height == 0){
                $thumb_h = ($new_width/$old_w)*$old_h;
            }else if($new_width == 0){
                $thumb_w = ($new_height/$old_h)*$old_w;
            }else if($old_w < $old_h){
                $thumb_h = ($new_width/$old_w)*$old_h;
            }else if($old_w > $old_h){
                $thumb_w = ($new_height/$old_h)*$old_w;
            }
            // trace([$thumb_w, $thumb_h]);
            //-----------------------------------------------------------
            $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_w, $old_h); 
            // New save location
            $new_thumb_loc = $imgTargetPath . $new_img_name;
            if($mime['mime']=='image/png') {
                $result = imagepng($dst_img,$new_thumb_loc,8);
            }
            if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
                $result = imagejpeg($dst_img,$new_thumb_loc,80);
            }
            if($mime['mime']=='image/webp'){
                $result = imagewebp($dst_img, $new_thumb_loc, 80);
            }
            imagedestroy($dst_img); 
            imagedestroy($src_img);
            return $result;
        }catch(Exception $e){
            
        }
    }

    public function trace($data){
        header('Content-type: application/json');
        echo json_encode($data);
        die('');
    }
}
