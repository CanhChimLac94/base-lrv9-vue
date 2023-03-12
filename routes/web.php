<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return Inertia::render('Dashboard');
//     })->name('dashboard');
// });

use App\Models\UserModel;
use App\Http\Controllers\Clients\ClnNewsCTRL;
use App\Http\Controllers\Home;

// Route::get('/', function(){
//     $data = UserModel::all();
//     return $data;
//     // return "tesst router";
// });
Route::get('/', [Home::class, 'index']);


// // custom get method
Route::get("/{ctrl?}/{action?}/{param?}", function (Request $request, $ctrl = null, $action = null, $param = null) {
    if (!isset($action) || $action == null || $action == "") {
        $action = "index";
    } else {
        $action = "get_" . $action;
    }
    try {
        return App::call("App\Http\Controllers\Clients\Cln" . ucfirst($ctrl) . "CTRL@$action", ["request" => $request, "param" => $param]);
    } catch (Exception $ex) {
        // return $ex;
    }
});

// custom post method
Route::post('/{ctrl}/{action}', function (Request $request, $ctrl = null, $action = null) {
    if (!$request->isMethod('post')) {
        return redirect('/Home');
    }
    if (!isset($action) || $action == null || $action == "") {
        $action = "index";
    } else {
        $action = "post_" . $action;
    }
    return App::call("App\Http\Controllers\Clients\Cln" . ucfirst($ctrl) . "CTRL@$action", ["request" => $request]);
    return redirect('/Home');
});
