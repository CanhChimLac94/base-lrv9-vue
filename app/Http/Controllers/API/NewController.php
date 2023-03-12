<?php

namespace App\Http\Controllers\API;

use App\Models\News;
use App\Models\NewTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cache;

class NewController extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct(News::class);
    }

    public function setRules(){
        $this->rules = [
            'title' => 'required',
            "description" => 'required',
            "content" => "required",
        ];
        $this->messages = [
            "title.required" => "<p>Mising title, please enter title</p>",
            "description.required" => "<p>Mising description, please enter description</p>",
            "content.required" => "<p>Mising content, please enter content</p>",
        ];
    }

    public function editData(&$data){
        $u = Auth::user();
        $data["create_by_id"] = $u->id;
        if(!isset($data["key_words"]) 
        || $data["key_words"] == null)
            $data["key_words"] = "";
        $data["is_pin"] = $data["is_pin"] == "false" ? 0 : ($data["is_pin"] == "true" ? 1 : $data["is_pin"]);
        $data["is_hot"] = $data["is_hot"] == "false" ? 0 : ($data["is_hot"] == "true" ? 1 : $data["is_hot"]);
        $data["is_new"] = $data["is_new"] == "false" ? 0 : ($data["is_new"] == "true" ? 1 : $data["is_new"]);
        unset($data['topic_name']);
        // parent::editData($data);
        return $data;
    }

    public function GetList(Request $request){
        $page = $request->page;
        $size = $request->size;
        $total = $this->mdl::count();
        $res_list = $this->mdl::select('tbl_news.*', 'tp.name as topic_name')
                        ->join('tbl_new_topics as tp', 'tp.id', '=', 'topic_id')
                        ->orderBy("tbl_news.updated_at", "DESC")
                        ->skip(($page - 1) * $size)
                        ->take($size)
                        ->get()->toArray();
        $result = [
            'list' => $res_list,
            // 'mdl' => $this->mdl,
            'param' => $request->all(),
            'total' => $total,
            'page' => $page,
            'size' => $size,
        ];
        return response()->json($result);
    }

    public function getAllTopics(Request $request){
        return response()->json([
            "status" => "ok",
            "msg" => "success",
            "datas" => [
                "topics" => NewTopic::getAll()
            ]
        ]);
    }

    public function addNewTopic(Request $request){
        $cate = $request->cate;
        $msg = "";
        // check validate
        if(!isset($cate["name"]) || $cate["name"] == null || $cate["name"] == "") 
            $msg = "Tên chủ đề chưa được nhập";
        // if (!isset($cate["code"]) || $cate["code"] == null || $cate["code"] == "")
        //     $msg = "Mã chủ đề chưa được nhập";
        // if (!isset($cate["notes"]) || $cate["notes"] == null || $cate["notes"] == "")
        //     $msg = "Ghi chú cho chủ đề chưa có";
        // check cate is exists
        $has_cate = NewTopic::getTopicByName($cate["name"]);
        if($has_cate != null){
            $msg = "Chủ đề đã tồn tại";
        }
        if($msg != ""){
            return response()->json([
                "status" => "fail",
                "msg" => $msg
            ]);
        }
        // add new categories
        $cate = NewTopic::create($cate);
        return response()->json([
            "status" => "ok",
            "msg" => $msg,
            "datas" => [

            ]
        ]);
    }

    public function resetCache(Request $request){
        $time_limited = 1800;
        //------
        $newsData = News::getTop(10, 1);
        Cache::put('newData', $newsData, $time_limited);
        //-------
        $rawDatas = News::news();
        Cache::put('newss', $rawDatas, $time_limited);
        //-------
        $rawDatas = News::higlight();
        $resData = $this->_formatData($rawDatas);
        Cache::put('higlight', $resData, $time_limited);
        //--------
        $data = NewTopic::getAll();
        Cache::put('topics', $data, $time_limited);
        //--------
        $data = NewTopic::getChildrenOnly();
        Cache::put('tags', $data, $time_limited);
        return response()->json([
            "msg" => 'success'
        ]);
    }

    private function _formatData($rawDatas = []){
        $resData = [];
        foreach ($rawDatas as $key => $item) {
            $item['title'] =  preg_replace('/   |\//is', '', $item['title']);
            $item["to"] = "/tin/".$item['title'].".".$item['id'].".html";
            $resData[] = $item;
        }
        return $resData;
    }

    //-----Channel type------------
    private $VNEXPRESS = 'vnexpress';
    private $VTV = 'vtv-news';
    private $CAFEF = 'cafef';
    private $GENK = 'genk';
    private $VIETNAMBIZ = 'vietnambiz';
    private $CAFEBIZ = 'cafebiz';
    private $SUCKHOEDOISONG = 'suckhoedoisong';
    //=======================

    public function getFromChannel(Request $request){
        $channel = $request->channel;
        $page = $request->page;
        switch ($channel) {
            case $this->VTV:
               return $this->getVTVNews($page);
            break;
            case $this->VNEXPRESS:
                return $this->getVNExpressHome($page);
             break;
             case $this->CAFEF:
                return $this->getCafeF($page);
            case $this->GENK:
                return $this->getGenK($page);
            case $this->VIETNAMBIZ:
                return $this->getVietNamBiz($page);
            case $this->CAFEBIZ:
                return $this->getCafeBiz($page);
            case $this->SUCKHOEDOISONG:
                return $this->getSucKhoeDoiSong($page);
                
             break;
        }
        return response()->json([]);
    }

    public function getChannelContentItem(Request $request){
        $channel = $request->channel;
        $link = $request->link;
        switch ($channel) {
            case $this->VTV:
                return $this->getContentVTV($link);
            break;
            case $this->VNEXPRESS:
                return $this->getVNExpressContent($link);
            break;
            case $this->CAFEF:
                return $this->getContentCafeF($link);
            case $this->GENK:
                return $this->getContentGenK($link);
            case $this->VIETNAMBIZ:
                return $this->getContentVietNamBiz($link);
            case $this->CAFEBIZ:
                return $this->getContentCafeBiz($link);
            case $this->SUCKHOEDOISONG:
                return $this->getContentSucKhoeDoiSong($link);
             break;
        }
    }

    private function getBodySite($url){
        $site = $this->get_url($url);
        $xml = $this->getBodyFromHtmlText($site);
        return $xml;
    }

    private function getBodyFromHtmlText ($html_text) {
        preg_match("/<body[^>]*>(.*?)<\/body>/is", $html_text, $matches);
        $xml = $matches[1];
        $pattern = [
            '/\<!--(.*?)--\>/i',
            '/<header[\d | \w]*<\/header>/i',
            '/<noscript>(.*?)<\/noscript>/i',
            '/<iframe[^>]*>(.*?)<\/iframe>/i',
            '/<style[^>]*>(.*?)<\/style>/i',
            '/<footer[^>]*>(.*?)<\/footer>/i',
            '/<script[^>]*>(.*?)<\/script>/i',
            '/<script[^>]*>(.*?)\/>/i',

            '/\\r/', '/\\n/',
            // '/;/',
        ];
        $replacement = '';
        $xml = preg_replace($pattern, $replacement, $xml);
        return $xml;
    }

    private function getVNExpressDataFromHtml($xml){
        preg_match_all("/<article(.*?)<\/article>/is", $xml, $matches2, PREG_PATTERN_ORDER);
        $resData = [];
        foreach ($matches2[0] as $key => $item) {
            preg_match("/data-id=\"(.*?)\"/is", $item, $matchesi);
            if(count($matchesi) <= 0) $id = '000000000000000000';
            else $id = $matchesi[1];
            preg_match("/title=\"(.*?)\"/is", $item, $matchesi);
            if(count($matchesi) <= 0) continue;
            $title = $matchesi[1];
            preg_match("/href=\"(.*?)\"/is", $item, $matchesi);
            $link = $matchesi[1];
            preg_match("/class=\"thumb-art\"(.*?)<\/div>/is", $item, $matchesi);
            if(count($matchesi) <= 0) continue;
            $img = $matchesi[0];
            preg_match("/1x,(.*?)2x\"/is", $img, $matchesi);
            if(count($matchesi) <= 0) continue;
            $thumb = $matchesi[1];
            $thumb = preg_replace('/ /', '', $thumb);
            preg_match("/<p(.*?)<\/p>/is", $item, $matchesi);
            preg_match("/<a(.*?)<\/a>/is", $matchesi[0], $matchesi);
            preg_match("/>(.*?)<\/a>/is", $matchesi[0], $matchesi);
            $description = $matchesi[1];
            $description = preg_replace('/<span(.*?)<\/span>/', '', $description);
            
            $resData[] = [
                "id" => $id,
                "title" => $title,
                "link" => $link,
                "thumb" => $thumb,
                "description" => $description,
                "content" => '',
                "key_words" => '',
            ];
        }
        return $resData;
    }

    private function getVNExpressHome($page = ''){
        $resData = [];
        $links = [
            "https://vnexpress.net/{$page}",

        ];
        foreach ($links as $key => $link) {
            $xml = $this->getBodySite($link);
            $resData = array_merge($resData, $this->getVNExpressDataFromHtml($xml));
        }
        return response()->json($resData);
    }

    private function getVNExpressContent($link){
        $site = $this->get_url($link);
        preg_match("/<meta name=\"news_keywords(.*?)>/is", $site, $matches);
        $key_words = $matches[1];
        preg_match("/content=\"(.*?)\"/is", $key_words, $matches);
        $key_words = $matches[1];
        preg_match("/<body[^>]*>(.*?)<\/body>/is", $site, $matches);
        $pattern = [
            '/\<!--(.*?)--\>/i',
            '/<header[\d | \w]*<\/header>/i',
            '/<noscript>(.*?)<\/noscript>/i',
            '/<iframe[^>]*>(.*?)<\/iframe>/i',
            '/<style[^>]*>(.*?)<\/style>/i',
            '/<footer[^>]*>(.*?)<\/footer>/i',
            '/<script[^>]*>(.*?)<\/script>/is',
            '/<script[^>]*>(.*?)\/>/is',
            '/<figcaption[^>]*>(.*?)<\/figcaption>/is',
            '/<p(.*?)class=\"description(.*?)<\/p>/is',
            '/<h1(.*?)class=\"title-detail\"(.*?)<\/h1>/i',
            '/\\n/i',
            // '/;/',
        ];
        $replacement = '';
        $xml = preg_replace($pattern, $replacement, $matches[0]);
        preg_match("/<section class=\"section page-detail top-detail(.*?)\">(.*?)<\/section>/is", $xml, $matches);
        if(count($matches)<=0) return [
            "key_words" => $key_words,
            "content" => '',
        ];
        preg_match("/<div class=\"sidebar-1(.*?)<div(.*?)class=\"(.*?)popup-zoom/is", $matches[0], $matches);
        if(count($matches)<=0) return [
            "key_words" => $key_words,
            "content" => '',
        ];
        $pattern = [
            '/<div class=\"popup-zoom(.*?)/i',
            '/<div class=\"header-content(.*?)<\/div>/si',
            '/<ul class=\"list-news(.*?)<\/ul>/is',
            '/<div class=\"myvne_save_for_later(.*?)<\/div>/is',
            '/<div class=\"social(.*?)<\/div>/is',
            '/<div class=\"footer-content(.*?)<\/div>/is',
            '/<div id=\"_detail_topic\"(.*?)<\/div>/'
        ];
        $xml = preg_replace($pattern, $replacement, $matches[0]);
        $xml = preg_replace('/data-src/i', 'src', $xml);
        $startIndex = strrpos($xml, '<p');
        $endIndex = strrpos($xml, '</p');
        $xml = substr($xml, 0, $startIndex);

        return [
            "key_words" => $key_words,
            "content" => $xml,
        ];
    }

    public function getVTVNews($page = ''){
        $site = $this->getBodySite('https://vtv.vn/'.$page);

        preg_match("/<div id=\"admWrapsite\">(.*?)<a id=\"scrollToTop\"/is", $site, $matches);
        $site = preg_replace('/<a id=\"scrollToTop\"/i', '', $matches[0]);
        preg_match_all("/<li class=\"tlitem(.*?)<\/li>/is", $matches[0], $matches2, PREG_PATTERN_ORDER);
        $site = $matches2[0];
        $resData = [];
        foreach ($matches2[0] as $key => $item) {
            preg_match("/data-newsid=\"(.*?)\"/is", $item, $matchesi);
            if(count($matchesi) <= 0 ) continue;
            $id = $matchesi[1];
            preg_match("/title=\"(.*?)\"/is", $item, $matchesi);
            $title = $matchesi[1];
            preg_match("/<a href=\"(.*?)\"/is", $item, $matchesi);
            $link = $matchesi[1];
            preg_match("/<img src=\"(.*?)\"/is", $item, $matchesi);
            $thumb = $matchesi[1];
            $thumb = preg_replace('/zoom\/256_159/', 'thumb_w/650', $thumb);
            preg_match("/<p class=\"sapo\">(.*?)<\/p>/is", $item, $matchesi);
            $description = $matchesi[1];
            $description = preg_replace('/VTV.VN - /i', '', $description);
            $resData[] = [
                "id" => $id,
                "title" => $title,
                "link" => 'https://vtv.vn'.$link,
                "thumb" => $thumb,
                "description" => $description,
                "key_words" => '',
                "content" => '',
            ];
        }

        return response()->json($resData);
    }

    private function getContentVTV($link = ''){
        $site = $this->get_url($link);
        preg_match("/name=\"news_keywords(.*?)>/is", $site, $matches);
        $key_words = $matches[1];
        preg_match("/content=\"(.*?)\"/is", $key_words, $matches);
        if(count($matches) > 1)
            $key_words = $matches[1];
        else $key_words = '';
        $site = $this->getBodyFromHtmlText($site);
        preg_match("/id=\"entry-body\">(.*?)<div class=\"clearfix\"/is", $site, $matches);
        if(count($matches) <= 0){
            return [
                "key_words" => $key_words,
                "content" => '',
            ];    
        }
        return [
            "key_words" => $key_words,
            "content" => $matches[1],
        ];
    }

    private function getCafeF($page = ''){
        $site = $this->getBodySite("https://cafef.vn/".$page);
        preg_match_all("/<li class=\"tlitem(.*?)<\/li>/is", $site, $matches, PREG_PATTERN_ORDER);
        $resData = [];
        foreach ($matches[0] as $key => $item) {
            preg_match("/data-newsid=\"(.*?)\"/is", $item, $matchesi);
            if(count($matchesi) <= 0 ) continue;
            $id = $matchesi[1];
            preg_match("/title=\"(.*?)\"/is", $item, $matchesi);
            $title = $matchesi[1];
            preg_match("/<a href=\"(.*?)\"/is", $item, $matchesi);
            $link = $matchesi[1];
            preg_match("/<img src=\"(.*?)\"/is", $item, $matchesi);
            $thumb = $matchesi[1];
            $thumb = preg_replace('/zoom\/250_156/', 'thumb_w/650', $thumb);
            preg_match("/<p class=\"sapo\">(.*?)<\/p>/is", $item, $matchesi);
            $description = $matchesi[1];
            $description = preg_replace('/VTV.VN - /i', '', $description);
            $resData[] = [
                "id" => $id,
                "title" => $title,
                "link" => 'https://cafef.vn'.$link,
                "thumb" => $thumb,
                "description" => $description,
                "key_words" => '',
                "content" => '',
            ];
        }
        return $resData;
    }

    private function getContentCafeF($link){
        $site = $this->get_url($link);
        preg_match("/name=\"news_keywords(.*?)>/is", $site, $matches);
        $key_words = $matches[1];
        preg_match("/content=\"(.*?)\"/is", $key_words, $matches);
        if(count($matches) > 1)
            $key_words = $matches[1];
        else $key_words = '';
        $site = $this->getBodyFromHtmlText($site);
        preg_match("/id=\"mainContent\">(.*?)<p class=\"author\"/is", $site, $matches);
        
        if(count($matches) <= 0){
            return [
                "key_words" => $key_words,
                "content" => '',
            ];    
        }
        $site = preg_replace('/  /is', '', $matches[1]);
        return [
            "key_words" => $key_words,
            "content" => $site,
        ];
    }

    private function getGenK($page = ''){
        $site = $this->getBodySite("https://genk.vn/".$page);
        preg_match_all("/<li class=\"shownews(.*?)<\/li>/is", $site, $matches, PREG_PATTERN_ORDER);
        $resData = [];
        foreach ($matches[0] as $key => $item) {
            preg_match("/id=\"(.*?)\"/is", $item, $matchesi);
            if(count($matchesi) <= 0 ) continue;
            $id = $matchesi[1];
            preg_match("/title=\"(.*?)\"/is", $item, $matchesi);
            $title = $matchesi[1];
            preg_match("/href=\"(.*?)\"/is", $item, $matchesi);
            $link = $matchesi[1];
            preg_match("/<img src=\"(.*?)\"/is", $item, $matchesi);
            $thumb = $matchesi[1];
            $thumb = preg_replace('/zoom\/260_162/', 'thumb_w/660', $thumb);
            preg_match("/<span class=\"knswli-sapo\">(.*?)<\/span>/is", $item, $matchesi);
            $description = $matchesi[1];
            // $description = preg_replace('/VTV.VN - /i', '', $description);
            $resData[] = [
                "id" => $id,
                "title" => $title,
                "link" => 'https://genk.vn'.$link,
                "thumb" => $thumb,
                "description" => $description,
                "key_words" => '',
                "content" => '',
            ];
        }
        return $resData;
    }

    private function getContentGenK($link){
        $site = $this->get_url($link);
        preg_match("/name=\"news_keywords(.*?)>/is", $site, $matches);
        $key_words = $matches[1];
        preg_match("/content=\"(.*?)\"/is", $key_words, $matches);
        if(count($matches) > 1)
            $key_words = $matches[1];
        else $key_words = '';
        $site = $this->getBodyFromHtmlText($site);
        preg_match("/id=\"ContentDetail\">(.*?)<zone/is", $site, $matches);
        
        if(count($matches) <= 0){
            return [
                "key_words" => $key_words,
                "content" => '',
            ];    
        }
        $site = preg_replace('/  /is', '', $matches[1]);
        return [
            "key_words" => $key_words,
            "content" => $site,
        ];
    }

    private function getVietNamBiz($page = ''){
        $site = $this->getBodySite("https://vietnambiz.vn/".$page);
        $resData = [];
        if($page == '' || $page == '/' || !isset($page)){
            preg_match_all("/<li class=\"newsread(.*?)<\/li>/is", $site, $matches, PREG_PATTERN_ORDER);
            foreach ($matches[0] as $key => $item) {
                $id = '1111111111111';
                preg_match("/data-nocheck=\"1\">(.*?)<\/a/is", $item, $matchesi);
                $title = $matchesi[1];
                preg_match("/href=\"(.*?)\"/is", $item, $matchesi);
                $link = $matchesi[1];
                preg_match("/src=\"(.*?)\"/is", $item, $matchesi);
                $thumb = $matchesi[1];
                $thumb = preg_replace(['/zoom\/120_80/', '/zoom\/180_120/'], '', $thumb);
                preg_match("/<span class=\"knswli-sapo\">(.*?)<\/span>/is", $item, $matchesi);
                $description = $title;
                $resData[] = [
                    "id" => $id,
                    "title" => $title,
                    "link" => 'https://vietnambiz.vn/'.$link,
                    "thumb" => $thumb,
                    "description" => $description,
                    "key_words" => '',
                    "content" => '',
                ];
            }
        }else{
            preg_match_all("/<li data-zoneid=\"(.*?)<\/li>/is", $site, $matches, PREG_PATTERN_ORDER);
            foreach ($matches[0] as $key => $item) {
                $id = '2222222222222';
                preg_match("/title=\"(.*?)\"/is", $item, $matchesi);
                $title = $matchesi[1];
                preg_match("/href=\"(.*?)\"/is", $item, $matchesi);
                $link = $matchesi[1];
                preg_match("/src=\"(.*?)\"/is", $item, $matchesi);
                $thumb = $matchesi[1];
                $thumb = preg_replace(['/zoom\/120_80/', '/zoom\/180_120/'], '', $thumb);
                preg_match("/<span class=\"sapo\">(.*?)<\/span>/is", $item, $matchesi);
                // $description = $matchesi[1];
                $description = $title;
                $resData[] = [
                    "id" => $id,
                    "title" => $title,
                    "link" => 'https://vietnambiz.vn/'.$link,
                    "thumb" => $thumb,
                    "description" => $description,
                    "key_words" => '',
                    "content" => '',
                ];
            }
        }
        return $resData;   
    }

    private function getContentVietNamBiz($link){
        $site = $this->get_url($link);
        preg_match("/name=\"news_keywords(.*?)>/is", $site, $matches);
        $key_words = $matches[1];
        preg_match("/content=\"(.*?)\"/is", $key_words, $matches);
        if(count($matches) > 1)
            $key_words = $matches[1];
        else $key_words = '';
        $site = $this->getBodyFromHtmlText($site);
        preg_match("/data-role=\"sapo\">(.*?)<\/div>/is", $site, $matches);
        $description = $matches[1];
        preg_match("/id=\"vnb-detail-content\">(.*?)<div data-check-position/is", $site, $matches);
        if(count($matches) <= 0){
            return [
                "key_words" => $key_words,
                "content" => '',
            ];    
        }
        $site = preg_replace('/  /is', '', $matches[1]);
        return [
            "key_words" => $key_words,
            "description" => $description,
            "content" => $description.$site,
        ];
    }
    
    private function getCafeBiz($page = ''){
        $site = $this->getBodySite('https://cafebiz.vn/'.$page);
        preg_match_all("/<li class=\"item\"(.*?)<\/li>/is", $site, $matches, PREG_PATTERN_ORDER);
        $resData = [];
        foreach ($matches[0] as $key => $item) {
            preg_match("/data-newsid=\"(.*?)\"/is", $item, $matchesi);
            if(count($matchesi) <= 0 ) continue;
            $id = $matchesi[1];
            preg_match('/title=\"(.*?)\"/is', $item, $matchesi);
            if(count($matchesi) <= 0 ) continue;
            $title = $matchesi[1];
            preg_match("/href=\"(.*?)\"/is", $item, $matchesi);
            $link = $matchesi[1];
            preg_match("/url\('(.*?)'\)/is", $item, $matchesi);
            $thumb = $matchesi[1];
            $thumb = preg_replace('/zoom\/270_170/', 'thumb_w/600', $thumb);
            $resData[] = [
                "id" => $id,
                "title" => $title,
                "link" => 'https://cafebiz.vn'.$link,
                "thumb" => $thumb,
                "description" => '',
                "key_words" => '',
                "content" => '',
                // "matchesi" => $matchesi,
            ];

            // $resData[] = $item;
        }
        return $resData;   
    }

    private function getContentCafeBiz($link){
        $site = $this->get_url($link);
        preg_match("/name=\"news_keywords(.*?)>/is", $site, $matches);
        $key_words = $matches[1];
        preg_match("/content=\"(.*?)\"/is", $key_words, $matches);
        if(count($matches) > 1)
            $key_words = $matches[1];
        else $key_words = '';
        $site = $this->getBodyFromHtmlText($site);
        preg_match("/class=\"sapo\">(.*?)<\/span>/is", $site, $matches);
        $description = $matches[1];
        preg_match("/class=\"detail-content\">(.*?)<div data-check-position/is", $site, $matches);
        if(count($matches) <= 0){
            return [
                "key_words" => $key_words,
                "content" => '',
            ];    
        }
        $site = preg_replace('/  /is', '', $matches[1]);
        return [
            "key_words" => $key_words,
            "description" => $description,
            "content" => $description.$site,
        ];
    }

    private function sucKhoeDoiSongGetDataFromHtml($html){
        preg_match_all("/class=\"box-category-item\"(.*?)<\/div>/is", $html, $matches, PREG_PATTERN_ORDER);
        $resData = [];
        foreach ($matches[0] as $key => $item) {
            preg_match("/data-id=\"(.*?)\"/is", $item, $matchesi);
            if(count($matchesi) <= 0 ) continue;
            $id = $matchesi[1];
            preg_match('/title=\"(.*?)\"/is', $item, $matchesi);
            if(count($matchesi) <= 0 ) continue;
            $title = $matchesi[1];
            preg_match("/href=\"(.*?)\"/is", $item, $matchesi);
            $link = $matchesi[1];
            preg_match("/src=\"(.*?)\"/is", $item, $matchesi);
            if(count($matchesi) <= 0) continue;
            $thumb = $matchesi[1];
            $thumb = preg_replace(['/zoom\/180_110/', '/zoom\/260_155/'], '/thumb_w/640/', $thumb);
            preg_match("/sapo\">(.*?)</is", $item, $matchesi);
            if(count($matchesi) <= 0) $description = '';
            else $description = $matchesi[1];

            $resData[] = [
                "id" => $id,
                "title" => $title,
                "link" => 'https://suckhoedoisong.vn'.$link,
                "thumb" => $thumb,
                "description" => $description,
                "key_words" => '',
                "content" => '',
                // "matchesi" => $matchesi,
            ];
        }
        return $resData;
    }

    private function getSucKhoeDoiSong($page = ''){
        $url = 'https://suckhoedoisong.vn/'.$page;
        $site = $this->getBodySite($url);
        $resData = $this->sucKhoeDoiSongGetDataFromHtml($site);
        if($page == '/' || $page == '' || !isset($page)){
            $site = $this->get_url('https://suckhoedoisong.vn/AjaxLoadHome.htm');
            $resData2 = $this->sucKhoeDoiSongGetDataFromHtml($site);
            $resData = array_merge($resData, $resData2);
        }
        return $resData;
    }

    private function getContentSucKhoeDoiSong($link){
        $site = $this->get_url($link);
        // return $site;
        preg_match("/name=\"news_keywords(.*?)>/is", $site, $matches);
        preg_match("/content=\"(.*?)\"/is", $matches[1], $matches);
        if(count($matches) > 1)
            $key_words = $matches[1];
            else $key_words = '';
        preg_match("/name=\"description\"(.*?)>/is", $site, $matches);
        preg_match("/content=\"(.*?)\"/is", $matches[1], $matches);
        $description = $matches[1];
        preg_match("/data-role=\"content\">(.*?)<\/div>(.*?)<style/is", $site, $matches);
        $content = $description;

        $content = strip_tags($content);

        if(count($matches) > 1){
            $content = preg_replace([
               '/  /is', 
                '/data-role=\"content\">/',
                '/\r/', '/\n/',
                '/<\/div><style/',
                '/<!--(.*?)-->/',
            ], '', $matches[0]);
            $content = $content;
        }
        return [
            "key_words" => $key_words,
            "description" => $description,
            "content" => $content,
        ];
    }

    public function test(){
        $url = "https://suckhoedoisong.vn/nha-trang-khong-con-xa-phuong-la-vung-do-169210917095458276.htm";
        $content = $this->getContentSucKhoeDoiSong($url);
        return response()->json($content);
   
        return response()->json($this->getVNExpressHome());
    }

}
