<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Constraint\Exception;

class BaseAPIController extends Controller
{
    public $validator;
    public $rules;
    public $messages;
    public $wheres = [1];

    public function __construct($model)
    {
        parent::__construct();
        $this->mdl = $model;
    }

    public function index(Request $request)
    {
        try {
            $mode = $request->mode;
            $type = strtolower(gettype($mode));
            if ($type == "string") {
                switch (strtolower($mode)) {
                    case 'getlist':
                    case 'get':
                    case 'list':
                        return response()->json($this->_getList($request->data));
                        break;
                    case 'getitem':
                    case 'item':
                        return response()->json($this->_getItem($request->data));
                        break;
                    case 'listall':
                    case 'getall':
                        return response()->json($this->_getListAll($request->data));
                        break;
                    case 'create':
                    case 'add':
                    case 'addnew':
                        return response()->json($this->_addNew($request->data));
                        break;
                    case 'update':
                    case 'edit':
                    case 'save':
                        return response()->json($this->_update($request->data));
                        break;
                    case 'delete':
                    case 'deletes':
                        return response()->json($this->_deletes($request->data));
                        break;
                    case 'truncate':
                    case 'trun':
                        return response()->json($this->truncate());
                        break;
                }
            } else if ($type == "integer") {
                switch ($mode) {
                    case 0: // 'getlist'
                        return response()->json($this->_getList($request->data));
                        break;
                    case 1: // 'create'
                        return response()->json($this->_addNew($request->data));
                        break;
                    case 2: // 'update'
                        return response()->json($this->_update($request->data));
                        break;
                    case 3: // 'delete'
                        return response()->json($this->_deletes($request->data));
                        break;
                }
            }
        } catch (Exception $ex) {
            $result = [
                "status" => 'fail',
                "msg" => "false",
                "ex" => $ex,
            ];
            return response()->json($ex);
        }
    }

    public function setRules()
    {
        $this->rules = [];
        $this->messages = [];
        /**
         * Eg
         */
        /*
            $this->rules = [
                'txtFullName' => 'required|max:255',
                "txtEmail" => 'required',
                "txtName" => "required",
                "txtPass" => "required",
            ];
            $this->messages = [
                "txtFullName.required" => "Mising full name, please enter full name",
                "txtEmail.required" => "Mising email, please enter your email",
                "txtName.required" => "Mising user name, please enter user name",
                "txtPass.required" => "Mising pass word, please enter your pass word",
            ];
         */    
        
    }

    public function valid($data)
    {
        $this->setRules();
        if (count($this->rules) > 0 && count($this->messages) > 0) {
            $this->validator = Validator::make($data, $this->rules, $this->messages);
            return $this->validator->fails();
        }
        return false;
    }

    /**
     * Add, Edit data befor insert
     *
     * @param [type] $data
     * @return void
     */
    public function editData(&$data)
    {
        return $data;
    }

    /**
     * Check and edit data affter create
     *
     * @param [type] $data
     * @return void
     */
    public function confirmData($data)
    {
        return $data;
    }

    public function GetList(Request $request)
    {
        return response()->json($this->_getList($request));
    }

    public function GetListAll(Request $request){
         return response()->json($this->_getListAll($request));
    }

    public function Create(Request $request)
    {
        $data = $request->all();
        return response()->json($this->_addNew($data));
    }

    public function Update(Request $request)
    {
        $item = $request->all();
        return response()->json($this->_update($item));
    }

    public function Deletes(Request $request)
    {
        $ids = explode(";", $request->ids);
        return response()->json($this->_deletes($ids));
    }

    public function beforeDelate($ids){}

    public function _getList($data)
    {
        $list = [];
        $page = 0;
        $size = 10;
        try {
            $page = $data->page;
            $size = $data->size;
            if(null == $page || null == $size){
                if(null == $data->where) $list = $this->mdl::orderBy('created_at', 'desc')->get();
                else $list = $this->mdl::where($data->where)->orderBy('created_at', 'desc')->get();
            }else{
                if(null == $data->where)
                    $list = $this->mdl::skip(($page - 1) * $size)->take($size)->orderBy('created_at', 'desc')->get();
                else
                    $list = $this->mdl::where($data->where)
                    ->skip(($page - 1) * $size)
                    ->take($size)->orderBy('created_at', 'desc')
                    ->get();
            }
        } catch (\Exception $ex) {
            trace([
                $ex,
                $data->page,
                $data->size
            ]);
        }
        $total = $this->mdl::count();

        if ($list == null) {
            $list = [];
        }
        $result = [
            'list' => $list,
            // 'mdl' => $this->mdl,
            'param' => $data,
            'total' => $total,
            'page' => $page,
            'size' => $size,
        ];
        return $result;
    }

    public function _getListAll($data){
        // $arrWhere = $data->where;
        if(isset($data->where))
            $list = $this->mdl::where($data->where)->get();
        else {
            $list = $this->mdl::get();
        }
        $total = $this->mdl::count();
        if($list == null) $list = [];
        $result = [
            'list' => $list,
            'total' => $total
        ];
        return $result;
    }

    public function _addNew($data)
    {
        if ($this->valid($data)) {
            $msgs = $this->validator->customMessages;
            if(count($msgs) > 0){
                $msg = "";
                foreach($msgs as $k=>$m){
                    $msg .= $m . "\n";
                }
                return [
                    "status" => "fail",
                    "msg" => $msg
                ];
            }
            return redirect()->back()->withErrors($this->validator)->withInput();
        }
        $item = null;
        $result = [
            "status" => "ok",
            "request" => $data,
            "item" => $item,
            "msg" => "Create new item complete",
        ];
        try {
            $item = $this->mdl::create($this->editData($data));

            if (!$this->confirmData($item)) {
                $this->deleteItem($item);
                $result["msg"] = "error edit data";
            }
            $result['item'] = $item;
            return $result;
        } catch (Exception $e) {
            if ($item) {
                $this->deleteItem($item);
            }
            $result["status"] = "fail";
            $result['msg'] = "false";
            $result['er'] = $e;
            return $result;
        }
    }

    private function _update($data)
    {
        try {
            $this->editData($data);
        } catch (Exception $ex) {}
        if ($this->valid($data)) {
            $msgs = $this->validator->customMessages;
            if (count($msgs) > 0) {
                $msg = "";
                foreach ($msgs as $k => $m) {
                    $msg .= $m . "\n";
                }
                return [
                    "status" => "fail",
                    "msg" => $msg
                ];
            }
            return redirect()->back()->withErrors($this->validator)->withInput();
        }
        $rawData = $data;
        $id = $data['id'];
        $data = $this->mdl::where('id', $id)->update($data);
        $item = null;
        if ($data) {
            $item = $this->mdl::where('id', $id)->get()->first();
        }
        $result = [
            "status" => "ok",
            "request" => $data,
            "item" => $item,
            "msg" => "Update item complete",
            "rawData" => $rawData
        ];
        return $result;
    }

    public function _deletes($ids)
    {
        $this->beforeDelate($ids);
        $listD = $this->mdl::whereIn('id', $ids)->delete();
        $list = []; // $this->mdl::get();
        $result = [
            "request" => $ids,
            "list" => $list,
            "msg" => "deletes item complete",
        ];
        return $result;
    }

    public function _getItem($data)
    {
        $item = null;
        try {
            $id = $data['id'];
            $item = $this->mdl::where('id', $id)
                ->first();
            // ->get();
        } catch (\Exception $ex) {

        }
        $result = [
            "status" => "ok",
            "item" => $item,
            "param" => $data,
        ];
        return $result;
    }

    public function updateItem($item)
    {
        $item->save();
        return $item;
    }

    public function deleteItem($item)
    {
        $item->delete();
    }

    public function truncate(){
        try{
            $this->mdl::truncate();
            return [
                "status" => "ok",
                "msg" => "Truncate table compeleted!"
            ];
        }catch(\Exception $ex){
            return [
                "status" => "fail",
                "msg" => $ex->getMessage()
            ];
        }
    }
}
