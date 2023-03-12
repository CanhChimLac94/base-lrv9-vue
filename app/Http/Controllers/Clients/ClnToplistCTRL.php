<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\NewTopic;
use App\Models\TopList;

class ClnToplistCTRL extends ClnBaseCtrl
{
    // -------------get view-----------
    public function get_view_detail($top_title = '', $top_id = '', Request $request){
        $top = TopList::findById($top_id);
        $contentHtlm = $this->getHtmlContent();
        $resData = [
            "top" => $top,
            "url" => $contentHtlm['url'],
            "head" => $contentHtlm['head'],
            "body" => $contentHtlm['body'],
        ];
        // return response()->json($resData);
        return view('client/toplist/detail', $resData);
    }

    //---------------------------------
    public function index(Request $request)
    {
        $page = $request->page;
        $topListData = $this->getTop(10, $page);
        return response()->json($topListData);
    }

    private function getTop($size, $page = 1){
        return TopList::getTop($size, $page);
    }

    public function get_detail(Request $request){
        $id = $request->top;
        $top = TopList::findById($id);
        return response()->json($top);
    }


}
