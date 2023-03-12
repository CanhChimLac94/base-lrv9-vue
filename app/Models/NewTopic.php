<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewTopic extends Model
{
    protected $table = "tbl_new_topics";
    protected $guarded = [];

    public static function getTopicByName($cate_name){
        $cate = NewTopic::where([
            "name" => $cate_name
        ])->get()->first();
        return $cate;
    }

    public static function getAll(){
        $root_topics = NewTopic::where([
            "parent_id" => -1
        ])->get();
        if ($root_topics == null || count($root_topics) == 0) {
            $root_topics = [];
        }
        $res_list = [];
        foreach($root_topics as $key => $item){
            if($item["has_sub"] == 1)
                $item["subs"] = NewTopic::_getSubtopics($item['id']);
            else {
                $item["subs"] = [];
            }
            $res_list[] = $item;
        }
        return $res_list;
    }

    public static function getList($page, $size){
        $list_parent = NewTopic::where([
                            "parent_id" => -1
                        ])
                        // ->orWhere('id', 'null')
                        // ->orWhere('id', 0)
                        ->orderBy("id", "DESC")
                        ->orderBy('order', 'ASC')
                        ->skip(($page - 1) * $size)
                        ->take($size)
                        ->get()->toArray();
        if ($list_parent == null || count($list_parent) == 0) {
            $list_parent = [];
        }
        $total = count($list_parent);
        $res_list = [];
        foreach($list_parent as $key => $item){
            if($item["has_sub"] == 1)
                $item["subs"] = NewTopic::_getSubtopics($item['id']);
            else {
                $item["subs"] = [];
            }
            $res_list[] = $item;
        }
        return [
            'list' => $res_list,
            'total' => $total
        ];
    }

    public static function getChildrenOnly(){
        return NewTopic::where([
            ["has_sub", "in", [0, -1]]
        ])
        ->select(['id', 'name'])
        ->get();
    }

    public static function _getSubtopics($parent_id){
        $list_subs = NewTopic::where( 'parent_id', $parent_id)
                    ->get()->toArray();
        if($list_subs == null)
            $list_subs = [];
        $result_list = [];
        foreach($list_subs as $topic){
            if($topic["has_sub"] == 1){
                $topic["subs"] = NewTopic::_getSubtopics($topic['id']);
            }
            else $topic["subs"] = [];
            $result_list[] = $topic;
        }
        return $result_list;
    }

    public static function fillter($keyword){
        
    }
    
};
