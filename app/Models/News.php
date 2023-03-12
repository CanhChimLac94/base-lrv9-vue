<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class News extends Model
{
    protected $table = "tbl_news";
    protected $guarded = [];
    protected $casts = [
        // 'is_new' => 'boolean',
        // 'is_hot' => 'boolean',
        // 'is_pin' => 'boolean',
    ];

    public static function selectSortField(){
        return ['tbl_news.id', 'title', 'img_path', 'tbl_news.updated_at', 'topic_id', 'tp.name as topic_name'];
    }
    
    public static function selectField(){
        return [
            'tbl_news.id', 'title', 'img_path', 'tbl_news.updated_at', 'topic_id', 'content', 'description'
        ];
    }
    
    public static function selectTopicField(){
        return array_merge(self::selectField(), [
            'tp.name as topic_name',
        ]);
    }


    public static function getById($newid){
        $select_field = self::selectTopicField();
        $select_field[] = "tbl_news.*";
        return News::where([
            "tbl_news.id" => $newid
        ])
        ->leftJoin('tbl_new_topics as tp', 'tp.id', '=', 'topic_id')
        ->select($select_field)
        ->get()->first();
    }

    /**
     * return list news topics and top 5 news in side each topic
     *
     * @return array
     */
    public static function getNewsMenu()
    {
        $topics = NewTopic::get();
        foreach ($topics as $k => $t) {
            $t["news"] = News::getNewsByTopicId($t->id, 5);
            $topics[$k] = $t;
        }
        return $topics == null ? [] : $topics;
    }

    /**
     * return list news by topic id max count = top_n
     *
     * @param [type] $topic_id
     * @param [type] $top_n
     * @return array
     */
    public static function getNewsByTopicId($topic_id, $top_n)
    {
        $news = News::where([
            "topic_id" => $topic_id
        ])
        ->join('tbl_new_topics as tp', 'tp.id', '=', 'topic_id')
        ->select(self::selectTopicField())
        ->take($top_n)->get();
        return $news == null ? [] : $news;
    }

    public function getIsPublishedAttribute($value)
    {
        return $value == 1 ? true : false;
    }

    public static function getTop($size, $page = 1){
        return $news = News::where([ ])
        ->select(self::selectField())
        ->orderBy('tbl_news.updated_at', 'DESC')
        ->skip(($page - 1) * $size)
        ->take($size)->get();
     }

     public static function getByTopic($topic_id, $page = 1, $size = 10){
         $topic = NewTopic::where([
             "id" => $topic_id
         ])->first();
         if(empty($topic)){
            return [];
         }
         $select_field = self::selectField();
         $select_field[] = DB::raw("'".$topic['name']."' as topic_name");
        return $news = News::select($select_field)
        ->where([
            "topic_id" => $topic_id
        ])
        ->orderBy("id", "desc")
        ->skip(($page - 1) * $size)
        ->take($size)
        ->get();
     }

     public static function higlight($size = 15){
        return News::where([
           "is_hot" => 1
        ])
        ->join('tbl_new_topics as tp', 'tp.id', '=', 'topic_id')
        ->select(self::selectSortField())
        ->orderBy('tbl_news.updated_at', 'DESC')
        ->take($size)
        ->get()
        ->toArray();
    }

    /**
     * Tin mới
     */
    public static function news($size = 15){
        return News::where([
           "is_new" => 1
        ])
        ->join('tbl_new_topics as tp', 'tp.id', '=', 'topic_id')
        ->select(self::selectTopicField())
        ->orderBy('tbl_news.id', 'DESC')
        ->take($size)
        ->get()
        ->toArray();
    }

    public static function filter($keyword, $size = 10){
        return News::where([["title", 'LIKE', "%$keyword%"]])
        ->join('tbl_new_topics as tp', 'tp.id', '=', 'topic_id')
        ->orWhere([["description", 'LIKE', "%$keyword%"]])
        ->orWhere([["key_words", 'LIKE', "%$keyword%"]])
        // ->orWhere([["content", 'LIKE', "%$keyword%"]])
        ->orderBy('tbl_news.created_at', 'DESC')
        ->select(self::selectTopicField())
        ->take($size)
        ->get()->toArray();
    }

};
