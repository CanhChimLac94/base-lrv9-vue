<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "tbl_products";
    protected $guarded = [];

    public static function getProductById($pro_id, $select = ["*"]){
        $item = Product::select($select)
        ->where([
            ["id", "=", $pro_id]
        ])->get()->first();
        if(null != $item){
            $item->imgs = json_decode(base64_decode($item->imgs));
            if (
                $item->imgs == null
                || count($item->imgs) == 0
            ) {
                $item->imgs = (object) [
                    "url" => "#",
                ];
            }
        }
        return $item;
    }

    public static function getProductByName($p_name, $select = ["*"])
    {
        $item = Product::select($select)
            ->where([
                ["name", "like", "%$p_name%"]
            ])->get()->first();
        if($item == null){
            $names = explode(" ", $p_name);
            foreach($names as $key=>$name){
                $item = Product::select($select)
                    ->where([
                        ["name", "like", "%$name%"]
                    ])->get()->first();
                if($item != null) break;
            }
        }
        if (null != $item) {
            $item->imgs = json_decode(base64_decode($item->imgs));
            if (
                $item->imgs == null
                || count($item->imgs) == 0
            ) {
                $item->imgs = (object) [
                    "url" => "#",
                ];
            }
        }
        return $item;
    }
};

