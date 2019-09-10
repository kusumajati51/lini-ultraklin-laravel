<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Permission;

class PermissionController extends Controller
{
    public function getJson()
    {
        $permissionGroups = Permission::all()
            ->groupBy('group');

        return $permissionGroups;
    }
}
