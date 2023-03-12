<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopList extends Model
{
    protected $table = "tbl_toplist";
    protected $guarded = [];
    protected $casts = [
        // 'is_new' => 'boolean',
    ];

    private static function getStandard($top){
        $top['type'] = json_decode($top['type']);
        $top['content'] = json_decode($top['content']);
        return $top;
    }

    public static function getTop($size, $page = 1){
        return $toplist = TopList::where([
         
        ])
        ->select(['id', 'title', 'top', 'thumb', 'description', 'updated_at', 'type', 'key_words'])
        ->orderBy('created_at', 'desc')
        ->skip(($page - 1) * $size)
        ->take($size)->get();
     }

     public static function findById($id){
        $toplist = TopList::where([
            "id" => $id
        ])->first();
        // return $toplist;
        return TopList::getStandard($toplist);
     }


};
