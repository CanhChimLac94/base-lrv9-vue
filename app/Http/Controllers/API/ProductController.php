<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\OrderTemp;
use App\Models\OrderProductTemp;
use Exception;

class ProductController extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct(Product::class);
    }

    public function detail_order(Request $request){
        $order_id = $request->order_id;
        $products = OrderProductTemp::where("order_id", $order_id)->get();
        foreach($products as $key=> $p){
            $p->imgs = json_decode($p->imgs);
            $products[$key] = $p;
        }
        $data = [
            "order" => OrderTemp::where("id", $order_id)->get()->first(),
            "order_detail" => $products
        ];

        return response()->json($data);
    }

    public function list_order(){

        $new_orders = OrderTemp::orderByDesc("created_at")->where([
            "status" => "new"
        ])->get();
        $excuted_orders = OrderTemp::orderByDesc("created_at")->where([
            "status" => "excuted"
        ])->get();
        $rejected_orders = OrderTemp::orderByDesc("created_at")->where([
            "status" => "rejected"
        ])->get();

        $data = [
            "list_order" => $new_orders,
            "new_orders" => $new_orders,
            "excuted_orders" => $excuted_orders,
            "rejected_orders" => $rejected_orders
        ];

        return response()->json($data);
    }

    public function processed_order(Request $request){
        $order_id = $request->order_id;
        OrderTemp::where("id", $order_id)->update([
            "processed" => 1,
            "status" => "excuted"
        ]);

    }

    public function reject_order(Request $request){
        $order_id = $request->order_id;
        OrderTemp::where("id", $order_id)->update([
            "processed" => 1,
            "status" => "rejected"
        ]);
    }

    public function editData(&$data){
        // //--------------debug----------
        // unset($data["imgs"]);
        // trace($data);
        // //-------------------------------
        if(isset($data["properties"]))
        {
            $data["properties"] = base64_encode(json_encode($data["properties"]));
        }
        $imgs = [];
        if(isset($data["imgs"]))
        {
            $product_path = "public/upload/product/";
            foreach($data["imgs"] as $k=>$img){
                $img["url"] = str_replace($product_path, "", $img["url"]);
                $img_name = $img["url"];
                $url = $product_path.$img_name;

                if(isset($img["data"])){
                    // check file 
                    if(file_exists($url) != true){
                        $img_name = $this->GUID().".png";
                        $url = $product_path.$img_name;
                    }
                    try{
                        $img["data"] = str_replace("data:image/jpeg;base64,", "", $img["data"]);
                        file_put_contents($url, base64_decode($img["data"]));

                        $this->createThumbnail($img_name, 0, 0,$product_path, $product_path."standard");
                        $this->createThumbnail($img_name, 45, 45, $product_path, $product_path."imgs", $img_name."_45x45");
                        $this->createThumbnail($img_name, 75, 75, $product_path, $product_path."imgs", $img_name."_75x75");
                        $this->createThumbnail($img_name, 255, 255, $product_path, $product_path."imgs", $img_name."_255x255");
                        $this->createThumbnail($img_name, 425, 425, $product_path, $product_path."imgs", $img_name."_425x425");

                        unlink($url);
                    }catch(Exception $e){};
                }
                $imgs[] = [
                    "url" => $img_name
                ];
            }
            $data["imgs"] = base64_encode(json_encode($imgs));
        }
        $data["promote_price"] = round($data["out_price"] - $data["out_price"] * ($data["promote_rate"] / 100), 2);
        if (isset($data["id"])) {
            $p = $this->_getItem($data)["item"];
            $old_imgs = json_decode(base64_decode($p["imgs"]));
            $new_imgs = $imgs;
            if(null == $old_imgs) $old_imgs = [];
            foreach($old_imgs as $ko=>$img_o){
                $check = false;
                foreach($new_imgs as $kn=>$img_n){
                    if($img_o->url == $img_n["url"]){
                        unset($new_imgs[$kn]);
                        unset($old_imgs[$ko]);
                        $check = true;
                        break;
                    }
                }
                if(!$check){
                    // delete file $img_o
                    try{
                        unlink($img_o->url);
                    }catch(Exception $e){}
                }
            }
            
        }
        // $data['notes'] = base64_encode($data['notes']);
        // trace($data);
        parent::editData($data);
        return $data;
    }

    public function beforeDelete($ids){

    }

    public function GetList(Request $request)
    {
        if(!\isRole(["admin", "administrator"])){
            $u = getUser();
            $request["where"] = [
                ["uid", "=", $u->id]
            ];
        }
        return response()->json($this->_getList($request));
    }

    private function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function test(){
        return response()->json(file_exists("public/upload/product/20D10E40-A865-4EFD-B4C1-C903EABD875D"));
    }

}