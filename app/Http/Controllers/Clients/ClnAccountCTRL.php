<?php

/**
 * Created by CanhChimLac.94.
 * User: CanhChimLac.94
 * Date: 29/10/2018
 * Time: 22:00
 */

namespace App\Http\Controllers\Clients;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ClnAccountCTRL extends ClnBaseCtrl
{
    // protected $user;

    public function __construct()
    {
        parent::__construct();
        $this->libs = [
            '<base href="' . url('/') . '/">',
            '<script style="text/script" src="./dist/js/app.lib.js"></script>',
            '<link href="/favicon.png" rel="icon" type="image/x-icon" />',
        ];
    }

    public function index()
    {
        return view();
    }

    public function get_Signin(Request $request)
    {
        $data = [
            "libs" => $this->libs,
            "type" => "signin",
            "r" => $request->r
        ];
        if (Auth::user()) {
            return redirect("/");
        }
        return view('Common.login', $data);
    }
    
    public function get_SignUp()
    {
        $data = [
            "libs" => $this->libs,
            "type" => "signup",
        ];
        return view('Common.signup', $data);
    }

    public function post_Login(Request $request)
    {
        $r = Session::get("redirect_url");
        if($r == null) $r = "/";
        else{
            Session::forget('redirect_url');
        }

        $key = $request->txtUserName;
        if (filter_var($key, FILTER_VALIDATE_EMAIL)) {
            $field = "email";
        } else {
            $field = "user_name";
        }
        $user = [
            $field => $key,
            "password" => $request->txtPassWord,
        ];
        Auth::guard('web')->attempt($user);
        $result = [
            "status" => "ok",
            "msg" => "loged",
            "next" => $r
        ];
        if (Auth::check()) {
            // return response()->json($request);

            if (isset($request->r)) {
                $result['next'] = base64_decode($request->r);
            }
            return response()->json($result);
        } else {
            $result = [
                "status" => "fail",
                "msg" => "login info not true, please enter again!",
                "userName" => $key,
                "pass" => $request->txtPassWord,
            ];
            return response()->json($result);
        }
    }

    public function get_Login()
    {
        $result = [
            'guest' => Auth::guest(),
            'user' => $this->user, // Auth::user(),
        ];
        return response()->json($result);
    }

    public function get_user(){
        return Auth::user();
    }

    public function get_Guard()
    {
        return response()->json(Auth::guard('web'));
    }
    public function get_Logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();
        return redirect("/");
    }

    public function post_logout(Request $request){
        Auth::logout();
        $request->session()->flush();
        return null;
    }

    public function setRules()
    {
        $this->rules = [
            'txtFullName' => 'required|max:255',
            "txtEmail" => 'required',
            "txtName" => "required",
            // "txtPhone" => "required",
            // "txtAddress" => "required",
            "txtPass" => "required",
        ];
        $this->messages = [
            "txtFullName.required" => "Mising full name, please enter full name",
            "txtEmail.required" => "Mising email, please enter your email",
            "txtName.required" => "Mising user name, please enter user name",
            // "txtPhone.required" => "Mising phone number, please enter your phone number",
            // "txtAddress.required" => "Mising address, please enter your address",
            "txtPass.required" => "Mising pass word, please enter your pass word",
        ];
    }

    public function valid(Request $request)
    {
        $this->setRules();
        if (count($this->rules) > 0 && count($this->messages) > 0) {
            $this->validator = Validator::make($request->all(), $this->rules, $this->messages);
            return $this->validator->fails();
        }
        return false;
    }

    public function post_Register(Request $request)
    {
        $result = [
            "status" => "ok",
            "msg" => "Tài khoản của bạn đã được đăng ký",
            "next" => "/",
        ];

        if ($this->valid($request)) {
            $result = [
                "status" => "fail",
                "msg" => $this->validator,
            ];
            return response()->json($result);
        }
        $user = User::create([
            "user_name" => $request->txtName,
            "email" => $request->txtEmail,
            "passWord" => bcrypt($request->txtPass),
            "full_name" => $request->txtFullName,
            "avatar_path" => DEFAULT_AVATAR_PATH,
            "role_id" => DEFAULT_ROLE_ID
        ]);
        
        if(null != $user){
            $user1 = [
                "email" => $user->email,
                "password" => $request->txtPass,
            ];
            Auth::guard('web')->attempt($user1);
        }

        return response()->json($result);
    }

    public function post_CheckDuplicate(Request $request)
    {
        $user = User::where('email', '=', $request->key)
            ->orwhere('user_name', $request->key)
            ->get();
        $result = [
            "status" => "ok",
            "msg" => "no duplicate",
        ];
        if (count($user) > 0) {
            $result = [
                "status" => "fail",
                "msg" => "duplicated",
            ];
        }

        return response()->json($result);
    }

    public function get_Profile(Request $request)
    {
        if ($this->user == null)
            return redirect('/login' . "?r=".base64_encode(("account/profile")));
        return view("Client.Account.profile");
    }

    public function post_Profile(Request $request)
    {
        return response()->json($this->user);
    }

    public function post_ChangeAvarta(Request $request)
    {
        $file = $request->file;
        $result = [
            "file name" => $file->getClientOriginalName(),
            "extention" => $file->getClientOriginalExtension(),
            "temp path" => $file->getRealPath(),
            "size" => $file->getSize(),
            "type" => $file->getMimeType(),
            "filePath" => ""
        ];
        $folderPath = './img/avatar/';
        $fileName = $this->user->UserName;
        $fullPath = $folderPath . $fileName;
        if ($this->user != null) {
            $file->move($folderPath, $fileName);
            $result['filePath'] = $fullPath;
        }
        User::where('id', $this->user->id)
            ->update([
                "AvataPath" => $fullPath
            ]);
        return response()->json($result);
    }

    public function post_ChangePass(Request $request)
    { }

    public function get_check($param = null){
        // trace(1);
        // return response()->json($param);
        trace(isPermission([$param]));
    }

    public function get_test($param = null){
        trace(base_permission_globe()[$param]);
    }

    public function get_cooki(){
        return csrf_token();
    }

    public function get_sanctum(){
        return $this->get_cooki();
    }

}
