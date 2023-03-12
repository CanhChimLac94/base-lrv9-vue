<?php
/**
 * Created by CanhChimLac.94.
 * User: CanhChimLac.94
 * Date: 27/10/2018
 * Time: 11:22 AM
 */

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\UrlGenerator;

use App\Models\News;
use App\Models\NewTopic;
use App\Models\Product;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PHPUnit\Runner\Exception;

use URL;
use Cache;

class ClnBaseCtrl extends Controller
{
    protected $time_limited = 1800;
    public $youtubeDataFileName = "./public/youtube_data.json";
    public $webInfor = null;

    public function __construct()
    {
        parent::__construct();
        // $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        // $file = str_replace("public", "", getcwd())."./public/web_infor.json";
        // if (env("DEV_PC") && strrpos($actual_link, "://localhost")) {
        //     $file = "./public/web_infor.json";
        // }
        // if (!file_exists($file)) {
        //     file_put_contents($file, "{}");
        // }
        // $file = file_get_contents($file);
        // $webInfor = json_decode($file);
        // $topics = $this->getTopics();
        // $new_menus = News::getNewsMenu();
        // $carts = $this->detailcart();
        // trace($webInfor);

        view()->share([
            // 'user' => Auth::user(),
            // 'webInfor' => $webInfor,
            // "topics" => $topics,
            // "banners" => $this->getBanners(),
            // "carts" => $carts->cart_detail,
            // "cart_detail" => (object)[
            //     "total_price" => $carts->total_price,
            //     "total_count" => $carts->total_count
            // ],
            // "new_menus" => $new_menus,
        ]);
        // $this->webInfor = $webInfor;
    }

    public function checkLogin()
    {
        if ($this->user == null) {
            return  redirect("/login");
        }
    }

    public function webinfo()
    {
        $this->__construct();
        return response()->json($this->webInfor);
    }

    public function detailcart()
    {
        $cart = Session::get("cart");
        $cart_detail = [];
        $total_price = 0;
        $total_count = 0;
        if ($cart != null) {
            foreach ($cart as $c) {
                $pro = Product::getProductById($c->id);
                if(null == $pro) continue;
                $pro["count"] = $pro["number"] = (int)$c->count;
                $price = $pro->promote_price;
                $total_price += ($price * $c->count);
                $total_count += $c->count;
                $pro["price"] = $price;
                $cart_detail[] = $pro;
            }
        }
        $data = (object)[
            "cart_detail" => $cart_detail,
            "total_price" => $total_price,
            "total_count" => $total_count
        ];
        return $data;
    }

    public function get_truncate_cart(Request $request){
        // $request->session()->put('cart',  null);
    }

    public function getBanners(){
        $banneres = Banner::take(1)->get();
        return $banneres;
    }

    public function GetVideosData()
    {
        if (!file_exists($this->youtubeDataFileName)) {
            file_put_contents($this->youtubeDataFileName, "{}");
        }
        $file = file_get_contents($this->youtubeDataFileName);
        $data = json_decode($file);
        $playlists = $data->playlists;
        if(empty($playlists)) $playlists = [];
        usort($playlists, function($p1, $p2){
            return $p1->total_videos < $p2->total_videos?1:-1;
        });
        return $playlists;
    }

    protected function getTopics(){
        $data = Cache::get('topics', null);
        if($data == null){
            $data = NewTopic::getAll();
            Cache::put('topics', $data, $this->time_limited);
        }
        return json_encode($data);
    }

    protected function gettags(){
        $data = Cache::get('tags', null);
        if($data == null){
            $data = NewTopic::getChildrenOnly();
            Cache::put('tags', $data, $this->time_limited);
        }
        return json_encode($data);
    }

    public function getUrl(){
        $url = config('nuxt.page');
        return $url;
    }

    public function getFullLink($link)
    {
        if (!isset($link) || strpos($link, 'https:') !== false || strpos($link, 'http:') !== false) {
            return $link;
        }
        $link = preg_replace(['/\/public/', '/public/'], '', $link);
        return $this->getUrl() . $link;
    }

    public function getHtmlContent(){
        return '';
        $url = $this->getUrl();
        $content = file_get_contents($url);
        // echo $content; die; // debug
        $content = preg_replace([
          '/<meta(.*?)\>/is',
          '/<title>(.*?)<\/title>/',
          '/data\-n\-head=\"1\"/is'
        ], '', $content);
        preg_match("/<head(.*?)>(.*?)<\/head>/is", $content, $matches);
        $head = $matches[2];
        preg_match("/<body(.*?)>(.*?)<\/body>/is", $content, $matches);
        $body = $matches[0];
        
        $resData = [
            "url" => $url,
            "head" => $head,
            "body" => $body,
        ];
        return $resData;
    }

    public function get_reset_cache(){

    }

}
