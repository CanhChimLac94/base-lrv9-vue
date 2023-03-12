<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\News;

use Inertia\Inertia;

class Home extends Controller
{
    public function index()
    {
        // $data = News::all();
        // return $data;
        return Inertia::render('Index', []);
    }
}
