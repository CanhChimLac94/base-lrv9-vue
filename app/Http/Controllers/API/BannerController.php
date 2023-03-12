<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner;

class BannerController extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct(Banner::class);
    }
}
