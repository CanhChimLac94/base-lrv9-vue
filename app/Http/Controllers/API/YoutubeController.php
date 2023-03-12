<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Exception;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Http\Request;

class YoutubeController extends Controller
{
    public $fileName = "./public/youtube_data.json";

    private function GetData()
    {
        if (!file_exists($this->fileName)) {
            file_put_contents($this->fileName, "{}");
        }
        $file = file_get_contents($this->fileName);
        $data = json_decode($file);
        return $data;
    }

    /**
     * return list playlist
     */
    public function GetList()
    {
        $data = $this->GetData();
        return response()->json($data->playlists);
     }

    /**
     * return object detail of all playlist videos
     */
    public function getVideos(){
        $data = $this->GetData();
        $res_datas = [
            "total_videos" => $data->total_videos,
            "playlist" => $data->playlists
        ];
        return response()->json($res_datas);
    }

    public function updateVideos(Request $request)
    {
        try {
            $data = $request->all();
            $api_key = empty($data) || empty($data['api_key']) ? config('google.api_key') : $data['api_key'];
            $channel_id = empty($data) || empty($data['channel_id']) ? "UCJI1XVgYbytqqtXkjap0gXA" : $data['channel_id'];
            $playlist_raw = $this->getPlayListOfChannel($api_key, $channel_id);
            $playlist_res = [];
            $gb_total_videos = 0;
            $total_pl = 0;
            foreach ($playlist_raw as $key => $pl) {
                $videos_raw = $this->getVideosOfPlayList($api_key, $pl->id);
                $videos_res = [];
                $total_vieo = 0;
                foreach ($videos_raw as $key => $vieo) {
                    if ($vieo->snippet->resourceId->videoId == "TaDz-9OsV-8") {
                        continue;
                    }
                    $thumbnail = $vieo->snippet->thumbnails['default'];
                    $thumbnail = empty($thumbnail->url) ? "" : $thumbnail->url;
                    $thumbnail = str_replace("default.jpg", "maxresdefault.jpg", $thumbnail);
                    try {
                        $videos_res[] = [
                            "id" => $vieo->snippet->resourceId->videoId,
                            "title" => $vieo->snippet->title,
                            "description" => $vieo->snippet->description,
                            "thumbnail" => $thumbnail,
                            "publishedAt" => $vieo->snippet->publishedAt,
                            "playlistId" => $vieo->snippet->playlistId,
                            "position" => $vieo->snippet->position,
                        ];
                    } catch (Exception $ee) {
                        dump(["ERROR:", $ee, $vieo]);
                    }
                    $total_vieo++;
                }

                usort($videos_res, function ($v1, $v2) {
                    return $v1['position'] < $v2['position'] ? 1 : -1;
                });

                $playlist_res[] = [
                    "id" => $pl->id,
                    "title" => $pl->snippet->title,
                    "description" => $pl->snippet->description,
                    "thumbnail" => $pl->snippet->thumbnails->maxres->url,
                    "publishedAt" => $pl->snippet->publishedAt,
                    "videos" => $videos_res,
                    "total_videos" => $total_vieo
                ];
                $gb_total_videos += $total_vieo;
                $total_pl++;
            }
            usort($playlist_res, function ($p1, $p2) {
                return $p1['total_videos'] < $p2['total_videos'] ? 1 : -1;
            });
            //-------------- save data to file -------------------
            $raw_data = [
                "total_videos" => $gb_total_videos,
                "total_playlist" => $total_pl,
                "playlists" => $playlist_res
            ];
            $data = json_encode($raw_data);
            file_put_contents($this->fileName, $data);
            //====================================================
            return response()->json($raw_data);
        } catch (Exception $ee) {
            return response()->json($ee);
        }
    }

    public function getPlayListOfChannel($api_key,  $channel_id = "UCJI1XVgYbytqqtXkjap0gXA")
    {
        if (empty($api_key)) $api_key = config('google.api_key');
        $client = new Google_Client();
        $client->setApplicationName('DuongTamKhoi');
        $client->setDeveloperKey($api_key);
        $client->setAccessType('offline');
        $service = new Google_Service_YouTube($client);
        $queryParams = [
            'channelId' => $channel_id,
            'maxResults' => 50
        ];
        $response = $service->playlists->listPlaylists(['snippet'], $queryParams)->toSimpleObject();
        // dump($response->items); die;
        return $response->items;
    }

    public function getVideosOfPlayList($api_key,  $playlist_id = "PLgKqFLieGoy7q8wnFzxdoyrAaKtmBD9D1")
    {
        if (empty($api_key)) $api_key = config('google.api_key');
        $client = new Google_Client();
        $client->setApplicationName('DuongTamKhoi');
        $client->setDeveloperKey($api_key);
        $client->setAccessType('offline');
        $service = new Google_Service_YouTube($client);
        $queryParams = [
            'playlistId' => $playlist_id,
            'maxResults' => 50
        ];
        return $service->playlistItems->listPlaylistItems('snippet', $queryParams)->items;;
    }
}
