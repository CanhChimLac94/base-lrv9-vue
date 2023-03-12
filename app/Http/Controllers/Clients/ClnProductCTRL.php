<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\OrderTemp;
use App\Models\OrderProductTemp;

class ClnProductCTRL extends ClnBaseCtrl
{
    public function index()
    {
        $spNoiBat = [];
        $spNoiBat = Product::take(10)->get();
        $all_product = $this->get_all_product();
        return view('Client.home', [
            "spNoiBat" => $spNoiBat,
            "all_product" => $all_product,
        ]);
    }

    public function order_view()
    {
        return view("Client.product.order");
    }

    public function detail($p_name, $product_id = null)
    {
        $product = null;
        if(null != $product_id)
            $product = Product::getProductById($product_id);
        else if(null != $p_name){
            $p_name = str_replace("-", " ", $p_name);
            $p_name = str_replace("_", " ", $p_name);
            $product = Product::getProductByName($p_name);
        }
        // return response()->json($product);
        if(null == $product) 
            return redirect("/");
        return view('Client.product.goods_detail', [
            "product" => $product
        ]);
    }

    public function get_all_product()
    {
        $res = [];
        $all_product = DB::table('tbl_products as p')
            ->leftJoin('tbl_categories as ct', "p.category_id", "=", "ct.id")
            ->select("p.*", "ct.name as category_name")
            ->get();
        foreach ($all_product as $pro) {
            $pro->imgs = json_decode($pro->imgs);
            // $pro->detail = json_decode($pro->detail);
            $res[$pro->category_name][] = $pro;
        }
        // dd($res);
        return $res;
    }

    public function add_to_cart(Request $request)
    {
        $cart = Session::get("cart");
        // $request->session()->forget("cart");
        $product_id = $request->product_id;
        $product_name = $request->product_name;
        $count = $request->count;

        $is_exists = false;
        if ($cart != null) {
            foreach ($cart as $index => $product) {
                if ($product->id  == $product_id) {
                    $product->count += $count;
                    $cart[$index] = $product;
                    $is_exists = true;
                    break;
                }
            }
        }
        if (!$is_exists) {
            $cart[] = (object)[
                "id" => $product_id,
                "name" => $product_name,
                "count" => $count
            ];
        }
        $request->session()->put('cart',  $cart);
        return response()->json([
            "status" => "ok",
            "event" => "UPDATE_CART"
        ]);
    }

    /**
     * Update a product in cart
     *
     * @param Request $request
     * @return void
     */
    public function post_update_cart(Request $request){
        $cart = Session::get("cart");
        $product_id = $request->product_id;
        $count = $request->count;
        foreach ($cart as $index => $product) {
            if ($product->id  == $product_id) {
                $product->count = $count;
                $cart[$index] = $product;
                break;
            }
        }
        $request->session()->put('cart',  $cart);
        return response()->json([
            "status" => "ok",
            "event" => "UPDATE_CART"
        ]);
    }

    /**
     * remove a product in cart by id
     *
     * @param Request $request
     * @return void
     */
    public function post_remove_cart_product(Request $request){
        $cart = Session::get("cart");
        $product_id = $request->product_id;
        foreach ($cart as $index => $product) {
            if ($product->id  == $product_id) {
                unset($cart[$index]);
                break;
            }
        }
        $request->session()->put('cart',  $cart);
        return response()->json([
            "status" => "ok",
            "event" => "UPDATE_CART"
        ]);
    }

    public function get_detailcart(Request $request)
    {
        $cart_detail = $this->detailcart();
        return response()->json($cart_detail);
    }

    public function get_header_cart(Request $request){
        $html = view("Client.Share.header_cart");
        $html = base64_encode($html);
        return $html;
    }

    public function get_test_cart(Request $request){
        $cart = Session::get("cart");
        // dd($cart);
        return response()->json($cart);
    }

    public function get_set_cart(Request $request){
        $request->session()->forget("cart");
        // $request->session()->push('cart',  $cart);
        return redirect("product/test_cart");
    }

    public function post_submitCart(Request $request){
        $IP = $_SERVER['REMOTE_ADDR']; 
        $data = $request->post();
        $cart = $this->detailcart();
        $detail_order = $cart->cart_detail;
        $data["status"] = 0;
        $data["processed"] = 0;
        $data["ctm_code"] = time();
        $data['status'] = "new";
        // $data["ship_price"] = 0;
        $data["temp"] = json_encode([
            "client_ip" => $IP
        ]);
        // return response()->json($IP);
        // validate
        $msg = "";
        if(!isset($data["ctm_name"])) $msg = "Hasn't Name\n";
        if (!isset($data["ctm_phone"])) $msg .= "Hasn't Phone\n";
        if (!isset($data["ctm_address"])) $msg .= "Hasn't Address\n";
        if($msg != ""
        || 0 == $cart->total_count){
            return response()->json([
                "status" => "fail",
                "msg" => $msg
            ]);
        }
        $order = OrderTemp::create($data);
        //-----------
        // return response()->json($order);
        //------------
        foreach ($detail_order as $key => $pro) {
            $data_pro = [
                "order_id" => $order->id,
                "product_id" => $pro->id,
                "product_code" => $pro->code,
                "product_name" => $pro->name,
                "count" => $pro->count,
                "in_price" => $pro->in_price,
                "promote_price" => $pro->promote_price,
                "promote_rate" => $pro->promote_rate,
                "imgs" => json_encode($pro->imgs),

            ];
            $pro_order = OrderProductTemp::create($data_pro);
        }
        $request->session()->forget("cart");
        return response()->json([
            "status" => "ok",
            "msg" => "success",
            "datas" => [
                "ctm_code" => $data["ctm_code"],
                "oder_detail" => $detail_order
            ],
            "event" => "UPDATE_CART_SUCCESS"
        ]);
    }

    public function get_watching_order($code, Request $request){
        $order = OrderTemp::where([
            "ctm_code" => $code
        ])->get()->first();
        $order_detail = null;
        if(null != $order){
            // check stattus
            switch($order->status){
                case 'new':
                    $order->status = "Đang chờ xử lý...";
                    $order->class = "btn-shop-primary";
                break;
                case 'excuted':
                    $order->status = "Đang giao hàng";
                    $order->class = "btn-shop-success";
                break;
                case 'rejected':
                    $order->status = "Đã bị hủy (không hợp lệ)";
                    $order->class = "btn-shop-danger";
                break;
            }
            // get details
            $order_detail = OrderProductTemp::where([
                "order_id" => $order->id
            ])->get();
            if(count($order_detail) > 0){
                foreach($order_detail as $key=>$o){
                    $o->imgs = json_decode($o->imgs);
                    $order_detail[$key] = $o;
                }
            }
        }   
        return view("Client.product.watch_order", [
            "order" => $order,
            "order_detail" => $order_detail
        ]);
    }

}
