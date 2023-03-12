<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TopList;

class TopListController extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct(TopList::class);
    }

    public function editData(&$data)
    {
        $u = Auth::user();
        $data["create_by_id"] = $u->id;
        if(!isset($data["key_words"]) 
        || $data["key_words"] == null)
            $data["key_words"] = "";
        // $data["is_pin"] = $data["is_pin"] == "false" ? 0 : ($data["is_pin"] == "true" ? 1 : $data["is_pin"]);
        // $data["is_hot"] = $data["is_hot"] == "false" ? 0 : ($data["is_hot"] == "true" ? 1 : $data["is_hot"]);
        // $data["is_new"] = $data["is_new"] == "false" ? 0 : ($data["is_new"] == "true" ? 1 : $data["is_new"]);
        $data['type'] = json_encode($data['type']);
        $data['content'] = json_encode($data['content']);
        
        unset($data['topic_name']);
        // parent::editData($data);
        return $data;
    }

    public function GetList(Request $request)
    {
        $page = $request->page;
        $size = $request->size;
        $total = $this->mdl::count();
        $raw_list = $this->mdl::select('*')
                        ->orderBy("updated_at", "DESC")
                        ->skip(($page - 1) * $size)
                        ->take($size)
                        ->get()->toArray();
        $res_list = [];
        foreach ($raw_list as $key => $item) {
            $item['type'] = json_decode($item['type']);
            $item['content'] = json_decode($item['content']);
            $res_list[] = $item;
        }
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

}
