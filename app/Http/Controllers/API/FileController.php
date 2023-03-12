<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Constraint\Exception;

class FileController extends Controller
{
  public function upload(Request $request){
    if (!$request->hasFile('file')) {
      return response()->json([
        'msg' => 'fail',
        'localtion' => ''
      ]);
    }
    $file = $request->file;
    $file_name = $file->getClientOriginalName();
    $file_extention = $file->getClientOriginalExtension();
    $file_real_path = $file->getRealPath();
    $file_size = $file->getSize();
    $file_type = $file->getMimeType();
    $sub_folder = date('Y/m/d', time());
    $file_path_full = "upload\\".$sub_folder."\\".$file_name;
    if(file_exists($file_path_full)){
      $file_name = str_replace(".".$file_extention, '', $file_name);
      $file_name = $file_name.time().".".$file_extention;
    }
    // save file
    $res = $file->move("upload\\".$sub_folder, $file_name);
    $this->createThumbnail($file_name, 120, 120, "upload\\".$sub_folder, "upload\_thumbs\\".$sub_folder, $file_name);

    return response()->json([
      'msg' => "ok",
      "file_name" => $file_name,
      "location" => "/upload/$sub_folder/$file_name",
    ]);
  }

  private function asyncThumb($sub_folder = ''){
    if('' == $sub_folder){
      return [];
    }
    try{
      $root_folder = 'upload\\'.$sub_folder;
      if(!is_dir($root_folder)){
        return [];
      }
      $files = scandir($root_folder, 1);
      $res_files = [];
      foreach ($files as $key => $file_name) {
          if($file_name == '.' || '..' == $file_name){
            continue;
          }
          $this->createThumbnail($file_name, 120, 120, "upload\\".$sub_folder, "upload\_thumbs\\".$sub_folder, $file_name);
          $res_files[] = $file_name;
      }
      return $res_files;
    }catch(Exception $e){ return []; }
  }

  public function asyncAllThumb(Request $request){
    // $all_sub_folders = scandir('upload\\', 1);
    $all_sub_folders = [
      '2021-09-10'
    ];
    $data = [];
    foreach ($all_sub_folders as $key => $folder) {
        if($folder == '.' || '..' == $folder || '_thumbs' == $folder){
          continue;
        }
        $data[] = $this->asyncThumb($folder);
    }
    return response()->json($data);
  }

}