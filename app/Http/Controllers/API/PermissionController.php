<?php

namespace App\Http\Controllers\API;

use App\Models\Permission;

class PermissionController extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct(Permission::class);
    }
}
