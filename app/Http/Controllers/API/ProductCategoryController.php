<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;

class productCategoryController extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct(Category::class);
    }
}
