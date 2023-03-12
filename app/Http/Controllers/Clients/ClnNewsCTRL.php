<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\NewTopic;
use Cache;

class ClnNewsCTRL extends ClnBaseCtrl
{

    //-------------View-------------------
    public function get_view_detail($news_title = '', $news_id = '1', Request $request){
        $news = News::getById($news_id);
        if(empty($news)) return redirect('/');
        $contentHtlm = $this->getHtmlContent();
        $resData = [
            "news_title" => $news_title,
            "news_id" => $news_id,
            "news" => $news,
            "head" => $contentHtlm['head'],
            "body" => $contentHtlm['body'],
        ];
        return view('client/news/detail', $resData);
    }

    public function get_view_home(Request $request){
        // $contentHtlm = $this->getHtmlContent();
        $newsData = $this->getCacheNewsData();
        $resData = [
            "newsData" => $newsData,
            "topics" => $this->getTopics(),
            "tags" => $this->gettags(),
            // "head" => $contentHtlm['head'],
            // "body" => $contentHtlm['body'],
        ];
        return view('client/news/index', $resData);
    }
    //-------------API--------------------
    public function index(Request $request)
    {
        $page = $request->page;
        $newsData = [];
        $newsData = $this->getTop(10, $page);
        // if($page > 1){
        // }else{
        //     $newsData = $this->getCacheNewsData();
        // }
        return response()->json([
            "msg" => 'ok',
            "data" => $newsData,
            "page" => $page,
        ]);
    }

    private function getCacheNewsData(){
        $newsData = Cache::get('newData', null);
        if($newsData == null){
            $newsData = $this->getTop(10, $page = 1);
            Cache::put('newData', $newsData, $this->time_limited);
        }
        return $newsData;
    }

    public function get_list(Request $request){
        return response()->json([
            "msg" => 'ok',
        ]);
    }

    public function get_topic($topic_name = '', $topic_id = null, Request $request){
        $page = $request->page;
        $news = News::getByTopic($topic_id, $page);
        return response()->json([
            "data"=>$news,
        ]);
    }

    public function get_topics(){
        return response()->json([
            'msg' => 'topics ok',
            "data" => json_decode($this->getTopics()),
        ]);
    }

    public function get_tags(){
        return response()->json([
            'msg' => 'topics ok',
            "data" => json_decode($this->gettags()),
        ]);
    }

    public function get_detail($title = "", $newid = null){
        $newItem =  News::where([
                "id" => $newid
            ])->get()->first();
        return view("Client.news.detail_news",[
                "topic_name" => '',
                "news" => $newItem,
            ]);
    }

    public function post_bytopic(Request $request){
        $topic_id = $request->topic_id;
        $resData = News::getNewsByTopicId($topic_id, 15);
        return response()->json($resData);
    }

    public function post_info($newid = null){
        $newItem = News::getById($newid);
        $other_news = News::getByTopic($newItem->topic_id);
        return response()->json([
            "news" => $newItem,
            "other_news" => $other_news,
        ]);
    }

    public function get_higlight(Request $request){
        $resData = Cache::get('higlight', null);
        if($resData == null){
            $rawDatas = News::higlight();
            $resData = $this->_formatData($rawDatas);
            Cache::put('higlight', $resData, $this->time_limited);
        }
        return response()->json($resData);
    }

    public function get_news(Request $request){
        $rawDatas = Cache::get('newss', null);
        if($rawDatas == null){
            $rawDatas = News::news();
            Cache::put('newss', $rawDatas, $this->time_limited);
        }
        return response()->json($this->_formatData($rawDatas));
    }

    public function post_filter(Request $request){
        $keyword = $request->keyword;
        if($keyword == ""){
            return response()->json([]);
        }
        $rawNews = News::filter($keyword);
        $news = $this->_formatData($rawNews);
        return response()->json($news);
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

    private function getTop($size, $page = 1){
        return News::getTop($size, $page);
    }

    public function get_resetCache(Request $request){
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
}
