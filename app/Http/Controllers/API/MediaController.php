<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Exception;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    private $dataPath = './datas'; 
    public $fileDataPlaylist = './datas/media_data_playlist.json';

    private function GetDataPlaylist()
    {
        if (!file_exists($this->fileDataPlaylist)) {
            file_put_contents($this->fileDataPlaylist, json_encode([
                "playlist" => []
            ]));
        }
        $file = file_get_contents($this->fileDataPlaylist);
        $data = json_decode($file);
        return $data;
    }

    /**
     * return list playlist
     */
    public function GetList()
    {
        // return playlist
        $data = $this->GetDataPlaylist();
        return response()->json($data->playlist);
     }

    
}
