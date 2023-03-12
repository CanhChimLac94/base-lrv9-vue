<?php

namespace App\Http\Controllers\API;

use App\Models\Menu;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\Exception;
use Illuminate\Support\Facades\DB;

class MenuController extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct(Menu::class);
    }

    public function confirmData($item)
    {
        try {
            if (!isset($item['id']) || $item->id == null) {
                return false;
            } else if ($item->parent_id == 0 || $item->parent_id == null) {
                $item->parent_id = (int) $item->id;
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
        $total = $this->mdl::count();
        $list_parent = $this->mdl::whereColumn("id", "=", "parent_id")
                        ->orWhere('id', 'null')
                        ->orWhere('id', 0)
                        ->orderBy("type", "ASC")
                        ->orderBy('order', 'ASC')
                        ->skip(($page - 1) * $size)
                        ->take($size)
                        ->get()->toArray();
        if ($list_parent == null || count($list_parent) == 0) {
            $list_parent = [];
        }
        $res_list = [];
        foreach($list_parent as $item){
            if($item["has_sub"] == 1)
                $item["subs"] = $this->GetListSub($item['id']);
            else {
                $item["subs"] = [];
            }
            $type = $item["type"];
            $res_list["$type"][] = $item;
        }
        $result = [
            'list' => $list_parent,
            'mdl' => $this->mdl,
            'param' => $request->all(),
            'total' => $total,
            'page' => $page,
            'size' => $size,
        ];
        return response()->json($result);
    }

    public function GetListSub($parent_id){
        $list_subs = $this->mdl::where( 'parent_id', $parent_id)
                    ->get()->toArray();
        if($list_subs == null)
            $list_subs = [];
        foreach($list_subs as $menu){
            if($menu["has_sub"] == 1)
                $menu["subs"] = $this->GetListSub($menu['id']);
            else $item["subs"] = [];
        }
        return $list_subs;
    }

    private function _viewMenu($to, $title, $icon, $subs = null){
        return [
            "to" => $to, 
            "title"=>$title, 
            "icon" => $icon, 
            "subs" => $subs
        ];
    }

    public function ListView(Request $request)
    {
        $result = [];
        $root_menu = $this->_viewMenu("#", "Root", "fa-globe",
            [
                $this->_viewMenu("/admin/Menus", "Menus", "fa-bars"),
                $this->_viewMenu('/admin/User', 'Thành viên', 'fa-users'),
                $this->_viewMenu("/admin/Roles", "Vai trò (role)", "fa-users"),
                $this->_viewMenu("/admin/Permissions", "Quyền hạn (permission)", "fa-users"),
            ]
        );
        $admin_menu = $this->_viewMenu("#", "System", "fa-globe",
            [
                $this->_viewMenu('/admin/Webinfo', 'Thông tin website', 'fa-info-circle'),
                $this->_viewMenu('/admin/Banner', 'Banner', 'fa-image'),
            ]
        );
        if(isRole("administrator")){
            $result[] = $root_menu;
            $result[] = $admin_menu;
        }
        if(isRole("admin")){
            $result[] = $admin_menu;
        }
        $result[] = $this->_viewMenu("/admin/home", "Dashboard", "fa-info-circle");
        $type = $request->type;
        $raw_menu = $this->mdl::where([
            ['enable', '=', 1],
            ['type', '=', $type],
        ])
        ->whereColumn('id', '=', 'parent_id')
        ->orderBy('order', 'ASC')
        ->get();
        $more_menu = [];
        foreach ($raw_menu as $key => $menu) {
            $more_menu[] = $this->_viewMenu($menu->url, $menu->name, $menu->icon);
        }
        $result = array_merge($result, $more_menu);
        return response()->json($result);
    }

    public function ParentView(Request $request){
        
        return response()->json("");
    }

    public function LstParent(Request $request)
    {
        $result = $this->mdl::whereColumn('id', '=', 'parent_id')
            ->orderby('id', 'ASC')
            ->get();

        return response()->json($result);
    }

    // public function GetListAll(Request $request){
    //     $result = $this->mdl::where("Enable", 1)
    //             ->get();
    //     return response()->json($result);
    // }
}
