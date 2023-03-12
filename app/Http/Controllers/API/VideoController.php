<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Constraint\Exception;

class VideoController extends Controller
{
  const API_BASE = "https://www.tiktok.com/node/";


  public function tiktok_trending(Request $request){
    $maxCursor = 1;
    $param = [
      "type"      => 5,
      "secUid"    => "",
      "id"        => 1,
      "count"     => 100,
      "minCursor" => 0,
      "maxCursor" => $maxCursor > 0 ? 1 : 0,
      "shareUid"  => "",
      "lang"      => "en",
      "verifyFp"  => "",
    ];
    $link = self::API_BASE . "video/feed?" . http_build_query($param);
    $resData = file_get_contents($link);
    $resData = json_decode($resData);
    return response()->json($resData);
  }

  public function tiktok_user_feed(Request $request){
    $username = $request->username; 
    $maxCursor = $request->maxCursor;
    
    if(empty($maxCursor)) $maxCursor = 0;
    // $user = $this->tiktok_get_user($username);

    // return response()->json($user);

    $param = [
        "type"      => 1,
        "secUid"    => "",
        "id"        =>  '6971608218732659713',
        "count"     => 100,
        "minCursor" => "0",
        "maxCursor" => $maxCursor,
        "shareUid"  => "",
        "lang"      => "",
        "verifyFp"  => "",
    ];
    $link = self::API_BASE . "video/feed?" . http_build_query($param);
    $resData = file_get_contents($link);
    $resData = json_decode($resData);

    return response()->json($resData);

    return response()->json([
       "link" => $link,
      "data" => $resData
    ]);
  }

  private function tiktok_get_user($username = "")
  {
    if(empty($username)) $username = 'tiktok';
    $username = urlencode($username);
    $link = "https://www.tiktok.com/@slow_mm?is_copy_url=1&is_from_webapp=v1&lang=en";
    $result = file_get_contents($link, false);
    // return $result; // debug
    if (preg_match('/<script id="__NEXT_DATA__"([^>]+)>([^<]+)<\/script>/', $result, $matches)) {
      $result = json_decode($matches[2], false);
      if (isset($result->props->pageProps->userInfo)) {
          $result = $result->props->pageProps->userInfo;
          if ($this->cacheEnabled) {
              $this->cacheEngine->set($cacheKey, $result, $this->_config['cache-timeout']);
          }
          return $result;
      }
    }
    return null;
  }

  private function remote_call($url = "", $isJson = true, $headers = ['Referer: https://www.tiktok.com/foryou?lang=en'])
        {
            $ch      = curl_init();
            $options = [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT      => $this->_config['user-agent'],
                CURLOPT_ENCODING       => "utf-8",
                CURLOPT_AUTOREFERER    => true,
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_HTTPHEADER     => array_merge([], $headers),
                CURLOPT_COOKIEJAR      => $this->_config['cookie_file'],
                CURLOPT_COOKIEFILE => $this->_config['cookie_file'],
            ];

            curl_setopt_array($ch, $options);
            if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            }
            if ($this->_config['proxy-host'] && $this->_config['proxy-port']) {
                curl_setopt($ch, CURLOPT_PROXY, $this->_config['proxy-host'] . ":" . $this->_config['proxy-port']);
                if ($this->_config['proxy-username'] && $this->_config['proxy-password']) {
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->_config['proxy-username'] . ":" . $this->_config['proxy-password']);
                }
            }
            $data = curl_exec($ch);
            curl_close($ch);
            if ($isJson) {
                $data = json_decode($data);
            }
            return $data;
        }

}