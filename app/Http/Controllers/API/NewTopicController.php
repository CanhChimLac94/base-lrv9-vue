<?php

namespace App\Http\Controllers\API;
use PHPUnit\Framework\Constraint\Exception;
use Illuminate\Http\Request;
use App\Models\NewTopic;

class NewTopicController extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct(NewTopic::class);
    }

    public function confirmData($item)
    {
        try {
            if($item['parent_id'] > 0){
                $this->mdl::where([
                    "id" => $item['parent_id']
                ])->update([
                    "has_sub" => 1
                ]);
            }
            if (!isset($item['id']) || $item->id == null) {
                return false;
            } else if ($item->parent_id <= 0 || $item->parent_id == null) {
                // $item->parent_id = (int) $item->id;
                $item->save();
            }
            return true;
        } catch (Exception $ex) {
            return false;
        }
        return false;
    }

    public function GetList(Request $request)
    {
        $page = $request->page;
        $size = $request->size;
        $data = $this->mdl::getList($page, $size);
        $result = [
            'list' => $data['list'], // $res_list,
            'mdl' => $this->mdl,
            'param' => $request->all(),
            'total' => $data['total'],
            'page' => $page,
            'size' => $size,
        ];
        return response()->json($result);
    }

    public function Deletes(Request $request){
        $ids = explode(";", $request->ids);
        NewTopic::whereIn('parent_id', $ids)->update([
            "parent_id" => -1
        ]);
        return response()->json($this->_deletes($ids));
    }

}